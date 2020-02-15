<?php

namespace App\Domains\Room\Exception;

use App\Domains\Room\Type\RoomName;
use DomainException;
use Throwable;

class RoomAlreadyExistsException extends DomainException
{
    public function __construct(RoomName $roomName)
    {
        parent::__construct("A room with name '{$roomName}' already exists!");
    }
}
