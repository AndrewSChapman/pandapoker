<?php

namespace App\Util\Logger;

use App\Util\Logger\Type\LogLevel;

/**
 * Used during unit tests to ensure the logger remains silent.
 */
class NullLogger implements LoggerInterface
{
    public function log(LogLevel $logLevel, string $message): void
    {
       // DO NOTHING
    }
}
