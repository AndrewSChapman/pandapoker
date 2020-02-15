<?php

namespace App\Domains\ChangeLog\Repository;

use App\Domains\ChangeLog\Adapter\ChangeLogItemAdapter;
use App\Domains\ChangeLog\Collection\ChangeLogList;
use App\Domains\ChangeLog\Entity\ChangeLogItem;
use App\Domains\ChangeLog\Type\ChangeLogId;
use App\Domains\ChangeLog\Type\ChangeLogLockId;
use App\Domains\Shared\Concurrency\Service\LockManager\LockManagerInterface;
use App\Domains\Shared\Concurrency\Service\LockManager\Type\LockKey;
use App\Domains\Shared\Concurrency\Service\LockManager\Type\LockType;
use App\Domains\Shared\Exception\EntityNotFoundException;
use App\Domains\Shared\Persistence\DataStore\DataStoreInterface;
use App\Domains\Shared\Persistence\DataStore\Type\ChannelMessage;
use App\Domains\Shared\Persistence\DataStore\Type\ChannelName;
use App\Domains\Shared\Persistence\DataStore\Type\DataStoreKey;
use PhpTypes\Type\Timestamp;

class ChangeLogRepository implements ChangeLogRepositoryInterface
{
    /** @var DataStoreInterface */
    private $dataStore;

    /** @var LockManagerInterface */
    public $lockManager;

    /** @var ChannelName */
    private $channelName;

    public function __construct(
        DataStoreInterface $dataStore,
        LockManagerInterface $lockManager,
        ChannelName $channelName
    ) {
        $this->dataStore = $dataStore;
        $this->lockManager = $lockManager;
        $this->channelName = $channelName;
    }

    /**
     * Implements an autonumber / sequence in redis.
     * A lock is used to ensure no concurrency issues (we lock, get the value, set the next value, release lock).
     * @return ChangeLogId
     */
    public function getNextId(): ChangeLogId
    {
        $this->lockManager->getLock($this->getLockKey());

        $idCacheKey = $this->getIdCacheKey();

        $idArray = $this->dataStore->getValueForKey($idCacheKey);
        if (is_null($idArray)) {
            $idArray = ['id' => 0];
        }

        $nextId = new ChangeLogId(intval($idArray['id'] ?? 0) + 1);

        $this->dataStore->setValueForKey(
            $idCacheKey,
            ['id' => $nextId->getValue()],
            $this->getIdExpiryTimestamp()
        );

        $this->lockManager->releaseLock($this->getLockKey());

        return $nextId;
    }

    public function saveChangeLogItem(ChangeLogItem $changeLogItem): void
    {
        $changeLogItemData = $changeLogItem->toArray();

        $this->dataStore->setValueForKey(
            $this->getCacheKey($changeLogItem->getId()),
            $changeLogItemData,
            $this->getExpiryTimestamp()
        );

        $channelMessage = ChannelMessage::fromArray($changeLogItemData);
        $this->dataStore->publish($this->channelName, $channelMessage);
    }

    public function getChangeLogItem(ChangeLogId $changeLogId): ChangeLogItem
    {
        $key = $this->getCacheKey($changeLogId);
        $data = $this->dataStore->getValueForKey($key);

        if (!$data) {
            throw new EntityNotFoundException(ChangeLogItem::class);
        }

        return ChangeLogItemAdapter::fromArray($data);
    }

    public function getChangeLogItems(?ChangeLogId $startId = null): ChangeLogList
    {
        $changeLogList = new ChangeLogList();

        $keys = $this->dataStore->getKeys("*{$this->changeLogKeyPrefix()}*");

        foreach ($keys as $thisKey) {
            $idAsInt = intval($thisKey->getIdFromKey());

            if ($idAsInt === 0) {
                continue;
            }

            $changeLogId = new ChangeLogId($idAsInt);

            if (!is_null($startId)) {
                if ($changeLogId->getValue() < $startId->getValue()) {
                    continue;
                }
            }

            $changeLogList->add($this->getChangeLogItem($changeLogId));
        }

        $changeLogList->sortById();

        return $changeLogList;
    }

    private function getLockKey(): LockKey
    {
        return new LockKey(
            new LockType(LockType::CHANGE_LOG_ID),
            new ChangeLogLockId('cd79fa25-26ed-4e37-8303-9335b3a2a551', false)
        );
    }

    private function changeLogKeyPrefix(): string
    {
        return 'changelog:';
    }

    private function getCacheKey(ChangeLogId $id): DataStoreKey
    {
        return new DataStoreKey("{$this->changeLogKeyPrefix()}{$id->toString()}");
    }

    private function getIdCacheKey(): DataStoreKey
    {
        return new DataStoreKey("{$this->changeLogKeyPrefix()}id");
    }

    private function getExpiryTimestamp(): Timestamp
    {
        // Change log keys will expire after 30 minutes
        return new Timestamp(time() + (60 * 30));
    }

    private function getIdExpiryTimestamp(): Timestamp
    {
        // 10 years :-)
        return new Timestamp(time() + (86400 * 365 * 10));
    }
}
