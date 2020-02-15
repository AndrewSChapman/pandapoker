<?php

namespace App\Util\Logger\Type;

use PhpTypes\Type\AbstractEnum;

class LogLevel extends AbstractEnum
{
    public const DEBUG = 100;
    public const INFO = 200;
    public const ERROR = 400;
}
