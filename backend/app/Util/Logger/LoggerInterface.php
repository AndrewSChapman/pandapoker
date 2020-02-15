<?php

namespace App\Util\Logger;

use App\Util\Logger\Type\LogLevel;

interface LoggerInterface
{
    public function log(LogLevel $logLevel, string $message): void;
}
