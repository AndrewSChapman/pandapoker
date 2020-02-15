<?php

namespace App\Domains\ChangeLog\Entity;

use App\Domains\ChangeLog\Type\ChangeLogId;
use App\Domains\User\Type\UserId;

class ChangeLogItem
{
    /** @var ChangeLogId */
    private $id;

    /** @var array */
    private $eventData;

    public function __construct(ChangeLogId $id, array $eventData)
    {
        $this->id = $id;
        $this->eventData = $eventData;
    }

    public function getId(): ChangeLogId
    {
        return $this->id;
    }

    public function getEventData(): array
    {
        return $this->eventData;
    }

    public function getCreatedBy(): UserId
    {
        return new UserId($this->getEventData()['event_created_by'] ?? '');

    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId()->getValue(),
            'event_data' => $this->getEventData()
        ];
    }
}
