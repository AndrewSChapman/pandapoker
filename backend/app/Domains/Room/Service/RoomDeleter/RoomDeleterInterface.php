<?php

namespace App\Domains\Room\Service\RoomDeleter;

use App\Domains\Room\Type\RoomId;
use App\Domains\User\Type\UserId;

interface RoomDeleterInterface
{
    public function deleteRoom(UserId $loggedInUserId, RoomId $roomId): void;
}
