<?php

namespace App\Domains\Shared\Event;

use App\Domains\ChangeLog\Event\ChangeLogSubscriber;
use App\Domains\ChangeLog\Repository\ChangeLogRepositoryInterface;
use App\Util\Logger\Logger;
use App\Util\Logger\Type\LogLevel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventSubscriber
{
    /** @var ChangeLogRepositoryInterface */
    private $changeLogRepository;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    private $subscribersAttached = false;

    public function __construct(ChangeLogRepositoryInterface $changeLogRepository)
    {
        $this->eventDispatcher = new EventDispatcher();
        $this->changeLogRepository = $changeLogRepository;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher(): EventDispatcherInterface
    {
        if (!$this->subscribersAttached) {
            $this->attachSubscribers();
        }

        return $this->eventDispatcher;
    }

    private function attachSubscribers(): void
    {
        // ATTACH SUBSCRIBERS HERE
        Logger::log(LogLevel::DEBUG, "EventSubscriber - Subscribing services");

        $this->eventDispatcher->addSubscriber(new ChangeLogSubscriber($this->changeLogRepository));

        $this->subscribersAttached = true;
    }
}
