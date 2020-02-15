<?php

namespace App\Domains\ChangeLog\Adapter;

use App\Domains\ChangeLog\Entity\ChangeLogItem;
use App\Domains\ChangeLog\Type\ChangeLogId;
use App\Domains\Shared\Event\Type\EventId;
use App\Domains\Shared\Event\Type\EventName;
use App\Domains\Shared\Exception\AdapterException;

class ChangeLogItemAdapter
{
    public static function fromArray(array $data): ChangeLogItem
    {
        $eventData = $data['event_data'] ?? '';
        if (!is_array($eventData)) {
            throw new AdapterException(ChangeLogItemAdapter::class, 'event_data array invalid');
        }

        // Make sure we the event data contains the event id and event name and that they are valid.
        new EventId($eventData['event_id'] ?? '');
        new EventName($eventData['event_name'] ?? '');

        $eventCreatedAt = intval($eventData['event_created_at'] ?? 0);
        if ($eventCreatedAt <= 0) {
            throw new AdapterException(
                ChangeLogItemAdapter::class,
                'event_created_at timestamp is missing or invalid'
            );
        }

        // Return the change log item.
        return new ChangeLogItem(
            new ChangeLogId($data['id']),
            $eventData
        );
    }
}
