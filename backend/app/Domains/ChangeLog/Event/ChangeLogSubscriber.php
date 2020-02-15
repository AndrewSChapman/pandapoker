<?php

namespace App\Domains\ChangeLog\Event;

use App\Domains\ChangeLog\Entity\ChangeLogItem;
use App\Domains\ChangeLog\Repository\ChangeLogRepositoryInterface;
use App\Domains\Room\Event\RoomCreatedEvent;
use App\Domains\Room\Event\RoomDeletedEvent;
use App\Domains\Room\Event\RoomUpdatedEvent;
use App\Domains\Room\Event\RoomVoteEvent;
use App\Domains\Shared\Event\AbstractEvent;
use App\Domains\User\Event\UserCreatedEvent;
use App\Domains\User\Event\UserUpdatedEvent;
use App\Util\Logger\Logger;
use App\Util\Logger\Type\LogLevel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ChangeLogSubscriber implements EventSubscriberInterface
{
    /** @var ChangeLogRepositoryInterface */
    private $changeLogRepository;

    public function __construct(ChangeLogRepositoryInterface $changeLogRepository)
    {
        $this->changeLogRepository = $changeLogRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            // ROOM EVENTS
            RoomCreatedEvent::class => 'handleRoomCreatedEvent',
            RoomUpdatedEvent::class => 'handleRoomUpdatedEvent',
            RoomDeletedEvent::class => 'handleRoomDeletedEvent',
            RoomVoteEvent::class => 'handleRoomVoteEvent',

            // USER EVENTS
            UserCreatedEvent::class => 'handleUserCreatedEvent',
            UserUpdatedEvent::class => 'handleUserUpdatedEvent'
        ];
    }

    public function handleUserCreatedEvent(UserCreatedEvent $event): void
    {
        Logger::log(LogLevel::DEBUG, 'ChangeLogSubscriber - Handle user created');
        $this->saveEvent($event);
    }

    public function handleUserUpdatedEvent(UserUpdatedEvent $event): void
    {
        Logger::log(LogLevel::DEBUG, 'ChangeLogSubscriber - Handle user updated');
        $this->saveEvent($event);
    }

    public function handleRoomCreatedEvent(RoomCreatedEvent $event): void
    {
        Logger::log(LogLevel::DEBUG, 'ChangeLogSubscriber - Handle room created');
        $this->saveEvent($event);
    }

    public function handleRoomUpdatedEvent(RoomUpdatedEvent $event): void
    {
        Logger::log(LogLevel::DEBUG, 'Handle room updated');
        $this->saveEvent($event);
    }

    public function handleRoomDeletedEvent(RoomDeletedEvent $event): void
    {
        Logger::log(LogLevel::DEBUG, 'Handle room deleted');
        $this->saveEvent($event);
    }

    public function handleRoomVoteEvent(RoomVoteEvent $event): void
    {
        Logger::log(LogLevel::DEBUG, 'Handle room vote');
        $this->saveEvent($event);
    }

    private function saveEvent(AbstractEvent $event): void
    {
        $changeLogId = $this->changeLogRepository->getNextId();
        $changeLogItem = new ChangeLogItem($changeLogId, $event->toArray());
        $this->changeLogRepository->saveChangeLogItem($changeLogItem);
    }
}
