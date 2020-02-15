<?php

namespace App\Domains\Room\Repository\RoomRepository;

use App\Domains\Room\Adapter\RoomAdapter;
use App\Domains\Room\Collection\RoomList;
use App\Domains\Room\Type\RoomId;
use App\Domains\Room\Type\RoomName;
use App\Domains\Shared\Exception\EntityNotFoundException;
use App\Domains\Shared\Persistence\DataStore\DataStoreInterface;
use App\Domains\Shared\Persistence\DataStore\Type\DataStoreKey;
use App\Domains\Room\Entity\Room;
use PhpTypes\Type\Timestamp;

class RoomRepository implements RoomRepositoryInterface
{
    /** @var DataStoreInterface */
    private $dataStore;

    /**
     * UserRepository constructor.
     * @param DataStoreInterface $dataStore
     */
    public function __construct(DataStoreInterface $dataStore)
    {
        $this->dataStore = $dataStore;
    }

    public function saveRoom(Room $room): void
    {
        $this->dataStore->setValueForKey(
            $this->getCacheKey($room->getId()),
            $room->toArray(),
            $this->getExpiryTimestamp()
        );
    }

    public function getRoom(RoomId $roomId): Room
    {
        $key = $this->getCacheKey($roomId);
        $roomData = $this->dataStore->getValueForKey($key);

        if (!$roomData) {
            throw new EntityNotFoundException(Room::class);
        }

        return RoomAdapter::getRoomFromArray($roomData);
    }

    public function getRooms(): RoomList
    {
        $roomList = new RoomList();

        $keys = $this->dataStore->getKeys("*{$this->roomKeyPrefix()}*");

        foreach ($keys as $thisKey) {
            $roomId = new RoomId($thisKey->getIdFromKey(), false);
            $roomList->add($this->getRoom($roomId));
        }

        return $roomList;
    }

    public function getRoomByName(RoomName $roomName): ?Room
    {
        $rooms = $this->getRooms();
        foreach ($rooms as $thisRoom) {
            if ($thisRoom->getName()->equalsCaseInsensitive($roomName)) {
                return $thisRoom;
            }
        }

        return null;
    }

    public function deleteRoom(Room $room): void
    {
        $this->dataStore->removeKey($this->getCacheKey($room->getId()));
    }

    private function roomKeyPrefix(): string
    {
        return 'room:';
    }

    private function getCacheKey(RoomId $roomId): DataStoreKey
    {
        return new DataStoreKey("{$this->roomKeyPrefix()}{$roomId->getUuid()}");
    }

    private function getExpiryTimestamp(): Timestamp
    {
        return new Timestamp(time() + (86400 * 365));
    }
}
