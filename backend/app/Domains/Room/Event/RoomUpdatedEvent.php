<?php

namespace App\Domains\Room\Event;

use App\Domains\Room\Entity\Room;
use App\Domains\Shared\Event\AbstractEvent;
use App\Domains\Shared\Event\Type\EventCreatedAt;
use App\Domains\Shared\Event\Type\EventId;
use App\Domains\Shared\Event\Type\EventName;
use App\Domains\User\Type\UserId;

class RoomUpdatedEvent extends AbstractEvent
{
    /** @var Room */
    private $room;

    public function __construct(
        Room $room,
        UserId $eventCreatedBy,
        ?EventId $id = null,
        ?EventCreatedAt $createdAt = null
    ) {
        parent::__construct(new EventName('ROOM_UPDATED'), $eventCreatedBy, $id, $createdAt);

        $this->room = $room;
    }

    public function toArray(): array
    {
        $eventData = parent::toArray();
        $eventData['room'] = $this->room->toArray(true);

        return $eventData;
    }
}
