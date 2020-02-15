<?php

namespace App\Domains\Room\Repository\RoomRepository;

use App\Domains\Room\Collection\RoomList;
use App\Domains\Room\Entity\Room;
use App\Domains\Room\Type\RoomId;
use App\Domains\Room\Type\RoomName;

interface RoomRepositoryInterface
{
    public function saveRoom(Room $room): void;
    public function getRoom(RoomId $roomId): Room;
    public function getRooms(): RoomList;
    public function getRoomByName(RoomName $roomName): ?Room;
    public function deleteRoom(Room $room): void;
}
