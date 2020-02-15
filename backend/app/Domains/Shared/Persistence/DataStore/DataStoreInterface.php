<?php

namespace App\Domains\Shared\Persistence\DataStore;

use App\Domains\Shared\Persistence\DataStore\Collection\DataStoreKeyList;
use App\Domains\Shared\Persistence\DataStore\Type\ChannelMessage;
use App\Domains\Shared\Persistence\DataStore\Type\ChannelName;
use App\Domains\Shared\Persistence\DataStore\Type\DataStoreKey;
use PhpTypes\Type\Timestamp;

interface DataStoreInterface
{
    public function getValueForKey(DataStoreKey $key): ?array;
    public function setValueForKey(DataStoreKey $key, array $value, Timestamp $expiry): void;
    public function removeKey(DataStoreKey $key): void;
    public function getKeys(string $match = ''): DataStoreKeyList;
    public function publish(ChannelName $channelName, ChannelMessage $message): void;
    public function subscribe(ChannelName $channelName, \Closure $callback): void;
    public function flush(): void;
}
