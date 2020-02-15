<?php

namespace App\Domains\Room\Event;

use App\Domains\Room\Type\RoomId;
use App\Domains\Room\Type\RoomVoteOption;
use App\Domains\Shared\Event\AbstractEvent;
use App\Domains\Shared\Event\Type\EventCreatedAt;
use App\Domains\Shared\Event\Type\EventId;
use App\Domains\Shared\Event\Type\EventName;
use App\Domains\User\Type\UserId;

class RoomVoteEvent extends AbstractEvent
{
    /** @var RoomId */
    private $roomId;

    /** @var RoomVoteOption */
    private $roomVoteOption;

    public function __construct(
        RoomId $roomId,
        UserId $eventCreatedBy,
        RoomVoteOption $roomVoteOption,
        ?EventId $id = null,
        ?EventCreatedAt $createdAt = null
    ) {
        parent::__construct(new EventName('ROOM_VOTE'), $eventCreatedBy, $id, $createdAt);

        $this->roomId = $roomId;
        $this->roomVoteOption = $roomVoteOption;
    }

    public function toArray(): array
    {
        $eventData = parent::toArray();
        $eventData['room_id'] = (string)$this->roomId;
        $eventData['vote'] = (string)$this->roomVoteOption->getValue();

        return $eventData;
    }
}
