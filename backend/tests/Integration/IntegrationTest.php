<?php

namespace Testing\Integration;

use App\Domains\Shared\Persistence\DataStore\DataStoreInterface;
use App\Domains\Shared\Persistence\DataStore\RedisDataStore;
use PHPUnit\Framework\TestCase;

abstract class IntegrationTest extends TestCase
{
    protected function dataStore(): DataStoreInterface
    {
        return new RedisDataStore();
    }
}
