<?php

namespace App\Domains\ChangeLog\Collection;

use App\Domains\ChangeLog\Entity\ChangeLogItem;
use PhpTypes\Collection\AbstractList;

class ChangeLogList extends AbstractList
{
    public function add(ChangeLogItem $changeLogItem): void
    {
        $this->values[] = $changeLogItem;
    }

    public function current(): ChangeLogItem
    {
        return $this->offsetGet($this->iteratorPointer);
    }

    public function offsetGet($offset): ChangeLogItem
    {
        return $this->values[$offset];
    }

    public function sortById(): void
    {
        usort($this->values, function(ChangeLogItem $a, ChangeLogItem $b) {
            return $a->getId()->getValue() <=> $b->getId()->getValue();
        });
    }
}
