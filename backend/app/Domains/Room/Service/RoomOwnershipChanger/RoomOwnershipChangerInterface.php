<?php

namespace App\Domains\Room\Service\RoomOwnershipChanger;

use App\Domains\Room\Entity\Room;
use App\Domains\Room\Type\RoomId;
use App\Domains\User\Type\UserId;

interface RoomOwnershipChangerInterface
{
    public function changeRoomOwnership(RoomId $roomId, UserId $userId): Room;
}
