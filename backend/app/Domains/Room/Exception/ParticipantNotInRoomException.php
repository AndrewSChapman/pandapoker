<?php

namespace App\Domains\Room\Exception;

use App\Domains\Room\Type\RoomId;
use App\Domains\User\Type\UserId;
use DomainException;

class ParticipantNotInRoomException extends DomainException
{
    public function __construct(RoomId $roomId, UserId $userId)
    {
        parent::__construct("Participant {$userId} is not in room {$roomId}");
    }
}
