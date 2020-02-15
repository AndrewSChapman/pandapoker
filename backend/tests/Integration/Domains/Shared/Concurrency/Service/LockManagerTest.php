<?php


namespace Testing\Integration\Domains\Shared\Concurrency\Service;

use App\Domains\Room\Type\RoomId;
use App\Domains\Shared\Concurrency\Service\LockManager\LockManager;
use App\Domains\Shared\Concurrency\Service\LockManager\Type\LockKey;
use App\Domains\Shared\Concurrency\Service\LockManager\Type\LockType;
use App\Domains\Shared\Persistence\DataStore\RedisDataStore;
use Testing\Integration\IntegrationTest;

class LockManagerTest extends IntegrationTest
{
    public function testCanGetAndReleaseLock(): void
    {
        $dataStore = new RedisDataStore();
        $lockManager = new LockManager($dataStore);

        $key = new LockKey(new LockType(LockType::ROOM), new RoomId('', true));

        $startTime = microtime(true);
        $lockManager->getLock($key);

        $timeToGetLock = microtime(true) - $startTime;

        $this->assertLessThan(1, $timeToGetLock);

        $lockManager->releaseLock($key);
    }
}
