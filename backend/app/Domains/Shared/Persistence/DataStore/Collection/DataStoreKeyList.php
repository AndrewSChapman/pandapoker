<?php

namespace App\Domains\Shared\Persistence\DataStore\Collection;

use App\Domains\Shared\Persistence\DataStore\Type\DataStoreKey;
use PhpTypes\Collection\AbstractList;

class DataStoreKeyList extends AbstractList
{
    public function add(DataStoreKey $key): void
    {
        $this->values[] = $key;
    }

    public function current(): DataStoreKey
    {
        return $this->offsetGet($this->iteratorPointer);
    }

    public function offsetGet($offset): DataStoreKey
    {
        return $this->values[$offset];
    }
}
