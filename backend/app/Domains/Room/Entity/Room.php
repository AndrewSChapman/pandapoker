<?php

namespace App\Domains\Room\Entity;

use App\Domains\Room\Collection\RoomParticipantList;
use App\Domains\Room\Collection\RoomVoteList;
use App\Domains\Room\Collection\RoomVoteOptionList;
use App\Domains\Room\Exception\ParticipantAlreadyInRoomException;
use App\Domains\Room\Exception\ParticipantNotInRoomException;
use App\Domains\Room\Exception\ParticipantNotVotingInRoomException;
use App\Domains\Room\Exception\VotingNotOpenException;
use App\Domains\Room\Type\RoomId;
use App\Domains\Room\Type\RoomName;
use App\Domains\Room\Type\RoomVoteOption;
use App\Domains\Room\ValueObject\RoomParticipant;
use App\Domains\Room\ValueObject\RoomVote;
use App\Domains\Shared\Concurrency\Service\LockManager\Type\LockKey;
use App\Domains\Shared\Concurrency\Service\LockManager\Type\LockType;
use App\Domains\Shared\Type\CreatedAt;
use App\Domains\Shared\Type\UpdatedAt;
use App\Domains\User\Type\UserId;

class Room
{
    /** @var RoomId */
    private $id;

    /** @var RoomName */
    private $name;

    /** @var UserId */
    private $createdByUserId;

    /** @var RoomVoteOptionList */
    private $roomVoteOptions;

    /** @var RoomVoteList */
    private $roomVotes;

    /** @var bool */
    private $votingOpen;

    /** @var RoomParticipantList */
    private $roomParticipants;

    /** @var CreatedAt */
    private $createdAt;

    /** @var UpdatedAt */
    private $updatedAt;

    public function __construct(
        RoomId $id,
        RoomName $name,
        UserId $createdByUserId,
        RoomVoteOptionList $roomVoteOptionList,
        ?RoomVoteList $roomVotes = null,
        ?RoomParticipantList $roomParticipants = null,
        bool $votingOpen = false,
        CreatedAt $createdAt = null,
        UpdatedAt $updatedAt = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->createdByUserId = $createdByUserId;
        $this->roomVoteOptions = $roomVoteOptionList;
        $this->roomVotes = $roomVotes ? $roomVotes : new RoomVoteList();
        $this->roomParticipants = $roomParticipants ? $roomParticipants : new RoomParticipantList();
        $this->votingOpen = $votingOpen;
        $this->createdAt = $createdAt ? $createdAt : new CreatedAt();
        $this->updatedAt = $updatedAt ? $updatedAt : new UpdatedAt();
    }

    public function getId(): RoomId
    {
        return $this->id;
    }

    public function getName(): RoomName
    {
        return $this->name;
    }

    public function setName(RoomName $roomName): void
    {
        $this->name = $roomName;
        $this->flagUpdated();
    }

    public function getCreatedByUserId(): UserId
    {
        return $this->createdByUserId;
    }

    public function setCreatedByUserId(UserId $createdByUserId): void
    {
        $this->createdByUserId = $createdByUserId;
    }

    public function getCreatedAt(): CreatedAt
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): UpdatedAt
    {
        return $this->updatedAt;
    }

    public function getRoomVoteOptions(): RoomVoteOptionList
    {
        return $this->roomVoteOptions;
    }

    public function getRoomVotes(): RoomVoteList
    {
        return $this->roomVotes;
    }

    public function isVotingOpen(): bool
    {
        return $this->votingOpen;
    }

    public function setVotingOpen(bool $votingOpen): void
    {
        $this->votingOpen = $votingOpen;
        $this->flagUpdated();
    }

    public function addVote(RoomVote $vote): void
    {
        if (!$this->isVotingOpen()) {
            throw new VotingNotOpenException($this->getId());
        }

        // You cannot add a vote for a participant who is not in this room!
        $foundParticipant = false;
        $voteAllowed = false;
        foreach ($this->roomParticipants as $roomParticipant) {
            if ($roomParticipant->getUserId()->getUuid()->toString() === $vote->getUserId()->getUuid()->toString()) {
                $foundParticipant = true;
                $voteAllowed = $roomParticipant->isVoting();
            }
        }

        if (!$foundParticipant) {
            throw new ParticipantNotInRoomException($this->getId(), $vote->getUserId());
        }

        if (!$voteAllowed) {
            throw new ParticipantNotVotingInRoomException($this->getId(), $vote->getUserId());
        }

        // Make sure the user has already voted, remove that vote before adding the new one.
        $this->roomVotes->removeVoteForUser($vote->getUserId());
        $this->roomVotes->add($vote);
        $this->flagUpdated();
    }

    /**
     * @return RoomParticipantList
     */
    public function getRoomParticipants(): RoomParticipantList
    {
        return $this->roomParticipants;
    }

    public function addParticipant(RoomParticipant $participant): void
    {
        // Make sure the user has not already voted in this room.
        foreach ($this->roomParticipants as $roomParticipant) {
            if ($roomParticipant->getUserId()->getUuid()->toString() === $participant->getUserId()->getUuid()->toString()) {
                throw new ParticipantAlreadyInRoomException($this->getId(), $roomParticipant->getUserId());
            }
        }

        $this->roomParticipants->add($participant);
        $this->flagUpdated();
    }

    public function isRoomCreator(UserId $userId): bool
    {
        return $this->getCreatedByUserId()->equals($userId);
    }

    /**
     * Removes the passed userId from the participant AND votes list.
     * If the user was in the room and is then removed,
     * true is returned, otherwise if no change was made, false is returned.
     * @param UserId $userIdToRemove
     * @return bool
     */
    public function removeParticipant(UserId $userIdToRemove): bool
    {
        $roomParticipantList = new RoomParticipantList();
        $participantsChanged = false;

        foreach ($this->roomParticipants as $roomParticipant) {
            if ($roomParticipant->getUserId()->equals($userIdToRemove)) {
                $participantsChanged = true;
            } else {
                $roomParticipantList->add($roomParticipant);
            }
        }

        if ($participantsChanged) {
            $this->roomParticipants = $roomParticipantList;
        }

        $this->removeVote($userIdToRemove);
        $this->flagUpdated();

        return $participantsChanged;
    }

    public function hasParticipant(UserId $userId): bool
    {
        foreach ($this->roomParticipants as $roomParticipant) {
            if ($roomParticipant->getUserId()->equals($userId)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Removes any vote associated with the passed userId from the votes list.
     * If the user had a vote which was removed, true is returned, otherwise if no change was made false is returned.
     * @param UserId $userIdToRemove
     * @return bool
     */
    public function removeVote(UserId $userIdToRemove): bool
    {
        $roomVoteList = new RoomVoteList();
        $votesChanged = false;

        foreach ($this->roomVotes as $roomVote) {
            if ($roomVote->getUserId()->equals($userIdToRemove)) {
                $votesChanged = true;
            } else {
                $roomVoteList->add($roomVote);
            }
        }

        if ($votesChanged) {
            $this->roomVotes = $roomVoteList;
            $this->flagUpdated();
        }

        return $votesChanged;
    }

    public function getUserVote(UserId $userId): ?RoomVoteOption
    {
        foreach ($this->roomVotes as $roomVote) {
            if ($roomVote->getUserId()->equals($userId)) {
                return $roomVote->getRoomVoteOption();
            }
        }

        return null;
    }

    public function clearVotes(): void
    {
        $this->roomVotes = new RoomVoteList();
    }

    public function getWinningVote(): ?RoomVoteOption
    {
        if ($this->roomVotes->isEmpty()) {
            return null;
        }

        // If there's only 1 item in the list, the winning vote must be the first one :-)
        if ($this->roomVotes->count() === 1) {
            foreach ($this->roomVotes as $roomVote) {
                return $roomVote->getRoomVoteOption();
            }
        }

        // Loop through all the votes and count how many times each vote option appears
        $voteDistribution = [];

        foreach($this->roomVotes as $roomVote) {
            $vote = $roomVote->getRoomVoteOption()->getValue();
            if (isset($voteDistribution[$vote])) {
                $voteDistribution[$vote]++;
            } else {
                $voteDistribution[$vote] = 1;
            }
        }

        // Sort the votes from lowest to highest, so the last element will have the highest number of votes.
        asort($voteDistribution);

        // Get the unique vote keys
        $keys = array_keys($voteDistribution);
        $numVotes = count($keys);
        $lastKey = $keys[$numVotes - 1];

        // If there's only 1 unique vote, this is the winning vote.
        if ($numVotes === 1) {
            return new RoomVoteOption($lastKey);
        }

        // As this is an associative array, get the keys for the last and second last positions.
        $secondLastKey = $keys[$numVotes - 2];

        // If the last value is the same as the second last, we have a tie, so no winner should be returned.
        if ($voteDistribution[$lastKey] === $voteDistribution[$secondLastKey]) {
            return null;
        }

        // We have a winner!
        return new RoomVoteOption($lastKey);
    }

    public function toArray(bool $includeWinningVote = false): array
    {
        $data = [
            'id' => $this->getId()->getUuid()->toString(),
            'name' => $this->getName()->toString(),
            'created_by_user_id' => $this->getCreatedByUserId()->getUuid()->toString(),
            'vote_options' => $this->roomVoteOptions->toArray(),
            'votes' => $this->roomVotes->toArray(),
            'participants' => $this->roomParticipants->toArray(),
            'voting_open' => $this->votingOpen,
            'created_at' => $this->getCreatedAt()->getTimestamp(),
            'updated_at' => $this->getUpdatedAt()->getTimestamp()
        ];

        if ($includeWinningVote) {
            $winningVote = $this->getWinningVote();
            $data['winning_vote'] = $winningVote ? $winningVote->getValue() : null;
        }

        return $data;
    }

    public function getLockKey(): LockKey
    {
        return new LockKey(new LockType(LockType::ROOM), $this->getId());
    }

    private function flagUpdated(): void
    {
        $this->updatedAt = new UpdatedAt();
    }
}
