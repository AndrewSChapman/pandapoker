<?php

namespace App\Domains\Room\ValueObject;

use App\Domains\Room\Type\RoomVoteOption;
use App\Domains\User\Type\UserId;

class RoomVote
{
    /** @var UserId */
    private $userId;

    /** @var RoomVoteOption */
    private $roomVoteOption;

    /**
     * RoomVote constructor.
     * @param UserId $userId
     * @param RoomVoteOption $roomVoteOption
     */
    public function __construct(UserId $userId, RoomVoteOption $roomVoteOption)
    {
        $this->userId = $userId;
        $this->roomVoteOption = $roomVoteOption;
    }

    /**
     * @return UserId
     */
    public function getUserId(): UserId
    {
        return $this->userId;
    }

    /**
     * @return RoomVoteOption
     */
    public function getRoomVoteOption(): RoomVoteOption
    {
        return $this->roomVoteOption;
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->getUserId()->getUuid()->toString(),
            'vote' => $this->getRoomVoteOption()->getValue()
        ];
    }
}
