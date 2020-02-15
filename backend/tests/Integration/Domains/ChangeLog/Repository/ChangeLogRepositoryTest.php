<?php

namespace Testing\Integration\Domains\ChangeLog\Repository;

use App\Domains\ChangeLog\Entity\ChangeLogItem;
use App\Domains\ChangeLog\Repository\ChangeLogRepository;
use App\Domains\ChangeLog\Repository\ChangeLogRepositoryInterface;
use App\Domains\Shared\Concurrency\Service\LockManager\LockManager;
use App\Domains\Shared\Event\Type\EventCreatedAt;
use App\Domains\Shared\Event\Type\EventId;
use App\Domains\Shared\Event\Type\EventName;
use App\Domains\Shared\Persistence\DataStore\Type\ChannelName;
use Testing\Integration\IntegrationTest;

class ChangeLogRepositoryTest extends IntegrationTest
{
    public function testChangeLogRepoWillIncrementNextId(): void
    {
        $this->dataStore()->flush();

        $repo = $this->getChangeLogRepository();

        $this->assertEquals(1, $repo->getNextId()->getValue());
        $this->assertEquals(2, $repo->getNextId()->getValue());
        $this->assertEquals(3, $repo->getNextId()->getValue());
    }

    public function testChangeLogRepoWillSaveAndGetItems(): void
    {
        $this->dataStore()->flush();

        $repo = $this->getChangeLogRepository();

        $eventId = new EventId('', true);
        $eventName = new EventName('ROOM_CREATED');
        $eventCreatedAt = new EventCreatedAt();

        $eventData = [
            'event_id' => $eventId->getUuid()->toString(),
            'event_name' => $eventName->toString(),
            'event_created_at' => $eventCreatedAt->getTimestamp()
        ];

        // Add a change log item to the repo
        $changeLogId1 = $repo->getNextId();

        $changeLogItem = new ChangeLogItem(
            $changeLogId1,
            $eventData
        );

        $repo->saveChangeLogItem($changeLogItem);

        $retrievedChangeLogItem = $repo->getChangeLogItem($changeLogId1);

        $this->assertEquals($retrievedChangeLogItem->getId()->getValue(), $changeLogId1->getValue());

        // Add another change log item to the repo
        $changeLogId2 = $repo->getNextId();

        $changeLogItem2 = new ChangeLogItem(
            $changeLogId2,
            $eventData
        );

        $repo->saveChangeLogItem($changeLogItem2);

        // Make sure we can get all change log items back
        $itemList1 = $repo->getChangeLogItems();
        $this->assertCount(2, $itemList1);

        // Make sure if we set a minimum id of 1, we still get 2 items back
        $itemList2 = $repo->getChangeLogItems($changeLogId1);
        $this->assertCount(2, $itemList2);

        // Make sure if we set a minimum id of 2, we only get 1 item back
        $itemList3 = $repo->getChangeLogItems($changeLogId2);
        $this->assertCount(1, $itemList3);
    }

    private function getChangeLogRepository(): ChangeLogRepositoryInterface
    {
        return new ChangeLogRepository(
            $this->dataStore(),
            new LockManager($this->dataStore()),
            new ChannelName('test_channel')
        );
    }
}
