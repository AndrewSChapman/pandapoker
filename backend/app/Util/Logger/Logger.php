<?php

namespace App\Util\Logger;
use App\Util\Logger\Type\LogLevel;
use Exception;

class Logger
{
    /** @var LoggerInterface|null */
    private static $logger = null;

    /**
     * @param LoggerInterface $logger
     * @throws Exception
     */
    public static function setLogger(LoggerInterface $logger): void
    {
        if (is_null(self::$logger)) {
            self::$logger = $logger;
        } else {
            throw new Exception('Logger interface already set!');
        }
    }

    /**
     * @param int $logLevel
     * @param string $message
     * @throws Exception
     */
    public static function log(int $logLevel, string $message): void
    {
        if (is_null(self::$logger)) {
            throw new Exception('LoggerInterface not set!');
        }

        self::$logger->log(new LogLevel($logLevel), $message);
    }
}
