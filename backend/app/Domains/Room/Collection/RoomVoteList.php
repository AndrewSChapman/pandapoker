<?php

namespace App\Domains\Room\Collection;

use App\Domains\Room\Type\RoomVoteOption;
use App\Domains\Room\ValueObject\RoomVote;
use App\Domains\User\Type\UserId;
use PhpTypes\Collection\AbstractList;

class RoomVoteList extends AbstractList
{
    public function add(RoomVote $roomVote): void
    {
        $this->values[] = $roomVote;
    }

    /**
     * If there is a vote for the specified user in the list, it will be removed.
     * @param UserId $userIdToRemove#
     */
    public function removeVoteForUser(UserId $userIdToRemove): void
    {
        $newValueList = [];

        $this->rewind();

        foreach ($this as $thisVote) {
            if (!$thisVote->getUserId()->equals($userIdToRemove)) {
                $newValueList[] = $thisVote;
            }
        }

        $this->values = $newValueList;

        $this->rewind();
    }

    public function current(): RoomVote
    {
        return $this->offsetGet($this->iteratorPointer);
    }

    public function offsetGet($offset): RoomVote
    {
        return $this->values[$offset];
    }

    public function toArray(): array
    {
        $this->rewind();

        $votes = [];

        foreach ($this as $roomVote) {
            $votes[] = $roomVote->toArray();
        }

        $this->rewind();

        return $votes;
    }

    /**
     * @param array $roomVotes  An array of associative arrays of user_id and vote pairs, e.g. [['user_id' => 'uuid', 'vote' => 3]]
     * @return static
     */
    public static function fromArray(array $roomVotes): self
    {
        $roomVoteList = new RoomVoteList();

        foreach ($roomVotes as $vote) {
            $roomVoteList->add(new RoomVote(new UserId($vote['user_id'] ?? ''), new RoomVoteOption($vote['vote'])));
        }

        return $roomVoteList;
    }
}
