<?php

namespace App\Domains\Room\Service\RoomCreator;

use App\Domains\Room\Collection\RoomVoteOptionList;
use App\Domains\Room\Entity\Room;
use App\Domains\Room\Type\RoomName;
use App\Domains\User\Type\UserId;

interface RoomCreatorInterface
{
    public function createRoom(RoomName $roomName, UserId $createdByUserId, RoomVoteOptionList $voteOptions): Room;
}
