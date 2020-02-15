<?php

namespace App\Domains\Room\Service\RoomLister;

use App\Domains\Room\Collection\RoomList;

interface RoomListerInterface
{
    public function getRoomList(): RoomList;
}
