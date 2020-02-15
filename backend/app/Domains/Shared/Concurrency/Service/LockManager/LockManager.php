<?php

namespace App\Domains\Shared\Concurrency\Service\LockManager;

use App\Domains\Shared\Concurrency\Service\LockManager\Type\LockKey;
use App\Domains\Shared\Persistence\DataStore\DataStoreInterface;
use PhpTypes\Type\Timestamp;

class LockManager implements LockManagerInterface
{
    /** @var DataStoreInterface */
    private $dataStore;

    /**
     * LockManager constructor.
     * @param DataStoreInterface $dataStore
     */
    public function __construct(DataStoreInterface $dataStore)
    {
        $this->dataStore = $dataStore;
    }

    /**
     * If there is an existing lock with the same key, we wait for the lock to be removed.
     * A lock is then created with a short 3 second expiry.
     * @param LockKey $key
     */
    public function getLock(LockKey $key): void
    {
        // If the key already exists - wait for it to be cleared.
        $item = $this->dataStore->getValueForKey($key);
        while ($item) {
            usleep(100000);
            $item = $this->dataStore->getValueForKey($key);
        }

        // Add the key into Redis with a very quick timeout
        srand();
        $myLockValue = intval(rand(1, 99999999));
        $this->dataStore->setValueForKey($key, ['lock_value' => $myLockValue], $this->getKeyExpiryTimestamp());
        $item = $this->dataStore->getValueForKey($key);

        $storedLockValue = intval($item['lock_value'] ?? 0);

        if ($myLockValue !== $storedLockValue) {
            $this->getLock($key);
        }
    }

    /**
     * Releases the lock for the given key
     * @param LockKey $key
     */
    public function releaseLock(LockKey $key): void
    {
        // If an item exists for the key, remove it.
        $item = $this->dataStore->getValueForKey($key);
        if ($item) {
            $this->dataStore->removeKey($key);
        }
    }

    private function getKeyExpiryTimestamp(): Timestamp
    {
        return new Timestamp(time() + 3);
    }
}
