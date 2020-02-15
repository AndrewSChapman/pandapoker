<?php

namespace App\Domains\Room\Exception;
use App\Domains\Room\Type\RoomId;
use DomainException;

class VotingNotOpenException extends DomainException
{
    public function __construct(RoomId $roomId)
    {
        parent::__construct("Voting is not open for room: {$roomId}");
    }
}
