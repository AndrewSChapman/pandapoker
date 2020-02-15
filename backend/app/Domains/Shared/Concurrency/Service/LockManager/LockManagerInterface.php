<?php

namespace App\Domains\Shared\Concurrency\Service\LockManager;

use App\Domains\Shared\Concurrency\Service\LockManager\Type\LockKey;

interface LockManagerInterface
{
    public function getLock(LockKey $key): void;
    public function releaseLock(LockKey $key): void;
}
