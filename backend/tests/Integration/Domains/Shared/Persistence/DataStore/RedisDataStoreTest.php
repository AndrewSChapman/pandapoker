<?php

namespace Testing\Integration\Domains\Shared\Persistence\DataStore;

use App\Domains\Shared\Persistence\DataStore\RedisDataStore;
use App\Domains\Shared\Persistence\DataStore\Type\DataStoreKey;
use PhpTypes\Type\Timestamp;
use PHPUnit\Framework\TestCase;

/**
 * @group RedisDataStore
 */
class RedisDataStoreTest extends TestCase
{
    public function testCanSetAndGetValuesAndGetMatchingKeys(): void
    {
        $dataStore = new RedisDataStore();

        // Flush the db to ensure a clean slate for the tests
        $dataStore->flush();;

        $key1 = new DataStoreKey("test:" . rand(999, 99999));

        // There should be no value for this key in the store right now
        $this->assertNull($dataStore->getValueForKey($key1));

        // Test storing a value for the key
        $value = [
            'Hello' => 'World'
        ];

        $expiresIn = new Timestamp(time() + 5);
        $dataStore->setValueForKey($key1, $value, $expiresIn);
        $retrieved = $dataStore->getValueForKey($key1);

        $this->assertEquals($value, $retrieved);

        // Test storing another value and make sure there are now two keys in the store
        $key2 = new DataStoreKey("test:" . rand(999, 99999));
        $dataStore->setValueForKey($key2, $value, $expiresIn);

        $keys = $dataStore->getKeys("*test:*");
        $this->assertCount(2, $keys);

        // Ensure that the keys returned are in fact the ones we added
        $foundKey1 = false;
        $foundKey2 = false;

        foreach ($keys as $thisKey) {
            if ((string)$thisKey === (string)$key1) {
                $foundKey1 = true;
            }

            if ((string)$thisKey === (string)$key2) {
                $foundKey2 = true;
            }
        }

        $this->assertTrue($foundKey1);
        $this->assertTrue($foundKey2);

        // Ensure if we ask for keys that have not been added, nothing is returned
        $this->assertCount(0, $dataStore->getKeys("*nope*"));

        // Ensure we can remove keys
        $dataStore->removeKey($key1);
        $keys = $dataStore->getKeys("*test:*");
        $this->assertCount(1, $keys);

        $dataStore->removeKey($key2);
        $keys = $dataStore->getKeys("*test:*");
        $this->assertCount(0, $keys);
    }
}
