<?php

namespace App\Domains\Shared\Concurrency\Service\LockManager\Type;

use App\Domains\Shared\Persistence\DataStore\Type\DataStoreKey;
use PhpTypes\Type\Id;

class LockKey extends DataStoreKey
{
    public function __construct(LockType $lockType, Id $id)
    {
        $key = "LOCK:{$lockType}:{$id->getUuid()}";

        parent::__construct($key);
    }
}
