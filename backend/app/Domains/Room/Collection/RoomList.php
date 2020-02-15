<?php

namespace App\Domains\Room\Collection;

use App\Domains\Room\Entity\Room;
use PhpTypes\Collection\AbstractList;

class RoomList extends AbstractList
{
    public function add(Room $room): void
    {
        $this->values[] = $room;
    }

    public function current(): Room
    {
        return $this->offsetGet($this->iteratorPointer);
    }

    public function offsetGet($offset): Room
    {
        return $this->values[$offset];
    }

    public function sortByRoomName(bool $caseInsensitive = true): void
    {
        usort($this->values, function(Room $a, Room $b) use ($caseInsensitive) {
            $nameA = $a->getName()->toString();
            $nameB = $b->getName()->toString();

            if ($caseInsensitive) {
                $nameA = strtolower($nameA);
                $nameB = strtolower($nameB);
            }

            return $nameA <=> $nameB;
        });
    }
}
