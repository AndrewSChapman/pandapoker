<?php

namespace App\Domains\Room\Service\RoomParticipantManager;

use App\Domains\Room\Entity\Room;
use App\Domains\Room\Type\RoomId;
use App\Domains\User\Type\UserId;

interface RoomParticipantManagerInterface
{
    public function addUserToRoom(
        UserId $loggedInUserId,
        RoomId $roomId,
        UserId $userIdToAddToRoom,
        bool $isVoting
    ): Room;

    public function removeUserFromRoom(
        UserId $loggedInUserId,
        RoomId $roomId,
        UserId $userIdToRemoveFromRoom
    ): Room;
}
