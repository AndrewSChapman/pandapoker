<?php

namespace App\Util\Logger;

use App\Util\Logger\Type\LogLevel;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class MonologLogger implements LoggerInterface
{
    /** @var Logger */
    private $logger;

    public function __construct(string $chanelName, StreamHandler $streamHandler)
    {
        $this->logger = new Logger($chanelName);
        $this->logger->pushHandler($streamHandler);
    }

    public function log(LogLevel $logLevel, string $message): void
    {
        $this->logger->log(intval((string)$logLevel), $message);
    }
}
