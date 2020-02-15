<?php

namespace App\Domains\Shared\Persistence\DataStore;

use App\Domains\Shared\Persistence\DataStore\Collection\DataStoreKeyList;
use App\Domains\Shared\Persistence\DataStore\Type\ChannelMessage;
use App\Domains\Shared\Persistence\DataStore\Type\ChannelName;
use App\Util\Logger\Logger;
use App\Util\Logger\Type\LogLevel;
use Exception;
use App\Domains\Shared\Persistence\DataStore\Exception\DataStoreException;
use App\Domains\Shared\Persistence\DataStore\Type\DataStoreKey;
use App\Domains\Shared\Exception\JsonDecodingException;
use App\Domains\Shared\Exception\JsonEncodingException;
use App\Domains\Shared\Helper\JsonHelper;
use Illuminate\Redis\Connections\PhpRedisConnection;
use PhpTypes\Type\Timestamp;

class RedisDataStore implements DataStoreInterface
{
    /** @var PhpRedisConnection */
    private static $connection;

    /**
     * @param DataStoreKey $key
     * @return array|null
     * @throws JsonDecodingException
     * @throws Exception
     */
    public function getValueForKey(DataStoreKey $key): ?array
    {
        $this->connect();

        if (!static::$connection->exists((string)$key)) {
            return null;
        }

        $value = static::$connection->get((string)$key);

        if (empty($value)) {
            return null;
        }

        return JsonHelper::jsonStringToArray($value);
    }

    /**
     * @param DataStoreKey $key
     * @param array $value
     * @param Timestamp $expiry
     * @throws Exception
     * @throws JsonEncodingException
     */
    public function setValueForKey(DataStoreKey $key, array $value, Timestamp $expiry): void
    {
        $this->connect();

        $expiresInSeconds = $expiry->getTimestamp() - time();
        if ($expiresInSeconds <= 0) {
            throw new DataStoreException('Cannot set a value with an expired key!');
        }

        $jsonValue = JsonHelper::arrayToJsonString($value);

        static::$connection->setex((string)$key, $expiresInSeconds, $jsonValue);
    }

    /**
     * @param DataStoreKey $key
     * @throws Exception
     */
    public function removeKey(DataStoreKey $key): void
    {
        $this->connect();

        if (!static::$connection->exists((string)$key)) {
            return;
        }

        static::$connection->del((string)$key);
    }

    /**
     * @param string $match
     * @return DataStoreKeyList
     * @throws Exception
     */
    public function getKeys(string $match = ''): DataStoreKeyList
    {
        $this->connect();

        $keyList = new DataStoreKeyList();

        $keys = static::$connection->keys($match);

        foreach ($keys as $key) {
            $keyList->add(new DataStoreKey($key));
        }

        return $keyList;
    }

    /**
     * @throws Exception
     */
    public function flush(): void
    {
        $this->connect();
        static::$connection->flushAll();
    }

    /**
     * @param ChannelName $channelName
     * @param ChannelMessage $message
     * @throws Exception
     */
    public function publish(ChannelName $channelName, ChannelMessage $message): void
    {
        $this->connect();
        static::$connection->publish((string)$channelName, (string)$message);
        Logger::log(LogLevel::INFO, 'Published message: ' . $message . ' to: ' . (string)$channelName);
    }

    public function subscribe(ChannelName $channelName, \Closure $callback): void
    {
        $this->connect(true);
        static::$connection->subscribe([(string)$channelName], $callback);
    }

    /**
     * @throws Exception
     */
    private function connect(bool $persistant = false): void
    {
        if (!static::$connection) {
            $redis = new \Redis();
            $redisHost = env('REDIS_HOST', '');
            $redisPort = intval(env('REDIS_PORT', 6379));
            $redisDbIndex = intval(env('REDIS_DBINDEX', 0));

            if (empty($redisHost)) {
                throw new \Exception('Redis Host not defined in .env file!');
            }

            if ($persistant) {
                if (!$redis->pconnect($redisHost, $redisPort, 0)) {
                    throw new DataStoreException('Redis connection failure.  Please check host and port settings in the .env file');
                }
            } else {
                if (!$redis->connect($redisHost, $redisPort)) {
                    throw new DataStoreException('Redis connection failure.  Please check host and port settings in the .env file');
                }
            }

            if (!$redis->select($redisDbIndex)) {
                throw new DataStoreException('Redis selection failure!');
            }

            static::$connection = new PhpRedisConnection($redis);
        }
    }
}
