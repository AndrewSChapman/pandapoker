<?php

namespace App\Domains\Shared\Concurrency\Service\LockManager\Type;

use PhpTypes\Type\AbstractEnum;

class LockType extends AbstractEnum
{
    public const CHANGE_LOG_ID = 'change_log_id';
    public const ROOM = 'room';
}
