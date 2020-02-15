<?php

namespace App\Domains\Room\ValueObject;

use App\Domains\User\Type\UserId;

class RoomParticipant
{
    /** @var UserId */
    private $userId;

    /** @var bool */
    private $isVoting;

    /**
     * RoomParticipant constructor.
     * @param UserId $userId
     * @param bool $isVoting
     */
    public function __construct(UserId $userId, bool $isVoting)
    {
        $this->userId = $userId;
        $this->isVoting = $isVoting;
    }

    /**
     * @return UserId
     */
    public function getUserId(): UserId
    {
        return $this->userId;
    }

    /**
     * @return bool
     */
    public function isVoting(): bool
    {
        return $this->isVoting;
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->getUserId()->getUuid()->toString(),
            'is_voting' => $this->isVoting()
        ];
    }
}
