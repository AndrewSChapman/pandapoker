<?php

namespace App\Domains\Room\Service\RoomLister;

use App\Domains\Room\Collection\RoomList;
use App\Domains\Room\Repository\RoomRepository\RoomRepositoryInterface;

class RoomLister implements RoomListerInterface
{
    /** @var RoomRepositoryInterface */
    private $roomRepository;

    public function __construct(RoomRepositoryInterface $roomRepository)
    {
        $this->roomRepository = $roomRepository;
    }

    public function getRoomList(): RoomList
    {
        $roomList = $this->roomRepository->getRooms();
        $roomList->sortByRoomName();

        return $roomList;
    }
}
