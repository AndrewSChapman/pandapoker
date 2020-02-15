<?php

namespace App\Domains\Shared\Event;

use App\Domains\Shared\Event\Type\EventCreatedAt;
use App\Domains\Shared\Event\Type\EventId;
use App\Domains\Shared\Event\Type\EventName;
use App\Domains\User\Type\UserId;

abstract class AbstractEvent
{
    /** @var EventId */
    private $id;

    /** @var EventName */
    private $eventName;

    /** @var UserId */
    private $eventCreatedBy;

    /** @var EventCreatedAt */
    private $createdAt;

    public function __construct(
        EventName $eventName,
        UserId $eventCreatedBy,
        ?EventId $id = null,
        ?EventCreatedAt $createdAt = null
    ) {
        $this->eventName = $eventName;
        $this->eventCreatedBy = $eventCreatedBy;
        $this->id = $id ? $id : new EventId('', true);
        $this->createdAt = $createdAt ? $createdAt : new EventCreatedAt();
    }

    /**
     * @return EventId
     */
    public function getId(): EventId
    {
        return $this->id;
    }

    /**
     * @return EventName
     */
    public function getEventName(): EventName
    {
        return $this->eventName;
    }

    /**
     * @return UserId
     */
    public function getEventCreatedBy(): UserId
    {
        return $this->eventCreatedBy;
    }

    /**
     * @return EventCreatedAt
     */
    public function getCreatedAt(): EventCreatedAt
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'event_id' => $this->id->getUuid()->toString(),
            'event_name' => $this->getEventName()->toString(),
            'event_created_by' => $this->getEventCreatedBy()->getUuid()->toString(),
            'event_created_at' => $this->createdAt->getTimestamp()
        ];
    }
}
