<?php

namespace App\Domains\Room\Service\RoomVotingService;

use App\Domains\Room\Entity\Room;
use App\Domains\Room\Type\RoomId;
use App\Domains\Room\Type\RoomVoteOption;
use App\Domains\User\Type\UserId;

interface RoomVotingServiceInterface
{
    public function openVoting(RoomId $roomId, UserId $requestedBy): Room;
    public function closeVoting(RoomId $roomId, UserId $requestingUserId): Room;
    public function addVote(RoomId $roomId, UserId $requestingUserId, RoomVoteOption $voteOption): Room;
    public function resetVotes(RoomId $roomId, UserId $requestingUserId): Room;
}
