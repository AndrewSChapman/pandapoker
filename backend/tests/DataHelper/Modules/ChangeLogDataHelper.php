<?php

namespace Testing\DataHelper\Modules;

use App\Domains\ChangeLog\Entity\ChangeLogItem;
use App\Domains\ChangeLog\Repository\ChangeLogRepositoryInterface;
use App\Domains\ChangeLog\Service\ChangeLogRetriever\ChangeLogRetrieverInterface;
use App\Domains\ChangeLog\Type\ChangeLogId;
use App\Domains\Shared\Event\AbstractEvent;
use App\Domains\Shared\Event\Type\EventCreatedAt;
use App\Domains\Shared\Event\Type\EventId;
use App\Domains\Shared\Event\Type\EventName;
use App\Domains\User\Entity\User;
use App\Domains\User\Type\UserId;
use PHPUnit\Framework\MockObject\MockObject;

class ChangeLogDataHelper extends AbstractDataHelperModule
{
    /** @var int */
    private $changeLogId = 1;

    /**
     * @return ChangeLogRepositoryInterface|MockObject
     */
    public function makeChangeLogRepository(): ChangeLogRepositoryInterface
    {
        return $this->getTestCase()->getMockBuilder(ChangeLogRepositoryInterface::class)
            ->getMock();
    }

    /**
     * @return ChangeLogRetrieverInterface|MockObject
     */
    public function makeChangeLogRetriever(): ChangeLogRetrieverInterface
    {
        return $this->getTestCase()->getMockBuilder(ChangeLogRetrieverInterface::class)
            ->getMock();
    }

    public function makeChangeLogItem(User $user, ?AbstractEvent $event = null): ChangeLogItem
    {
        $eventData = $event ? $event->toArray() : $this->getExampleEvent($user)->toArray();
        $changeLogItem = new ChangeLogItem(new ChangeLogId($this->changeLogId), $eventData);
        $this->changeLogId++;

        return $changeLogItem;
    }

    private function getExampleEvent(User $user): AbstractEvent
    {
        return new Class(new EventName('Test Event'), $user->getId()) extends AbstractEvent
        {
            public function __construct(
                EventName $eventName,
                UserId $eventCreatedBy,
                ?EventId $id = null,
                ?EventCreatedAt $createdAt = null
            ) {
                parent::__construct($eventName, $eventCreatedBy, $id, $createdAt);
            }
        };
    }
}
