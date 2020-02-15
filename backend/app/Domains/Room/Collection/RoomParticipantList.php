<?php

namespace App\Domains\Room\Collection;

use App\Domains\Room\ValueObject\RoomParticipant;
use App\Domains\User\Type\UserId;
use PhpTypes\Collection\AbstractList;

class RoomParticipantList extends AbstractList
{
    public function add(RoomParticipant $participant): void
    {
        $this->values[] = $participant;
    }

    public function current(): RoomParticipant
    {
        return $this->offsetGet($this->iteratorPointer);
    }

    public function offsetGet($offset): RoomParticipant
    {
        return $this->values[$offset];
    }

    public function toArray(): array
    {
        $this->rewind();

        $votes = [];

        foreach ($this as $participant) {
            $votes[] = $participant->toArray();
        }

        $this->rewind();

        return $votes;
    }

    /**
     * @param array $roomParticipants  An array of associative arrays of user_id and is_voting pairs, e.g. [['user_id' => 'uuid', 'is_voting' => true]]
     * @return static
     */
    public static function fromArray(array $roomParticipants): self
    {
        $roomParticipantList = new RoomParticipantList();

        foreach ($roomParticipants as $roomParticipant) {
            $isVoting = filter_var($roomParticipant['is_voting'] ?? true, FILTER_VALIDATE_BOOLEAN);

            $roomParticipantList->add(new RoomParticipant(
                new UserId($roomParticipant['user_id'] ?? ''),
                $isVoting
            ));
        }

        return $roomParticipantList;
    }
}
