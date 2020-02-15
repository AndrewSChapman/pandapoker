<?php

namespace App\Domains\Room\Exception;

use App\Domains\Room\Type\RoomId;
use App\Domains\User\Type\UserId;
use DomainException;

class RoomCannotBeDeletedException extends DomainException
{
    public function __construct(RoomId $roomId, UserId $userId, string $reason)
    {
        parent::__construct("Room {$roomId} cannot be deleted by user {$roomId} - Reason: $reason");
    }
}
