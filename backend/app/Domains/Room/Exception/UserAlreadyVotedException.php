<?php

namespace App\Domains\Room\Exception;

use App\Domains\Room\Type\RoomId;
use App\Domains\User\Type\UserId;
use DomainException;

class UserAlreadyVotedException extends DomainException
{
    public function __construct(RoomId $roomId, UserId $userId)
    {
        parent::__construct("User {$userId} already voted in room {$roomId}");
    }
}
