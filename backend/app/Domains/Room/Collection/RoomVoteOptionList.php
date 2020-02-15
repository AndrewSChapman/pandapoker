<?php

namespace App\Domains\Room\Collection;

use App\Domains\Room\Type\RoomVoteOption;
use PhpTypes\Collection\AbstractList;

class RoomVoteOptionList extends AbstractList
{
    public function add(RoomVoteOption $roomVoteOption): void
    {
        $this->values[] = $roomVoteOption;
    }

    public function current(): RoomVoteOption
    {
        return $this->offsetGet($this->iteratorPointer);
    }

    public function offsetGet($offset): RoomVoteOption
    {
        return $this->values[$offset];
    }

    public function toArray(): array
    {
        $this->rewind();

        $options = [];

        foreach ($this as $voteOption) {
            $options[] = $voteOption->getValue();
        }

        $this->rewind();

        return $options;
    }

    /**
     * @param array $roomVoteOptions  An array of positive integers
     * @return static
     */
    public static function fromArray(array $roomVoteOptions): self
    {
        $roomVoteOptionList = new RoomVoteOptionList();

        foreach ($roomVoteOptions as $option) {
            $roomVoteOptionList->add(new RoomVoteOption($option));
        }

        return $roomVoteOptionList;
    }
}
