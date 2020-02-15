<?php

namespace App\Domains\User\Event;

use App\Domains\Shared\Event\AbstractEvent;
use App\Domains\Shared\Event\Type\EventCreatedAt;
use App\Domains\Shared\Event\Type\EventId;
use App\Domains\Shared\Event\Type\EventName;
use App\Domains\User\Entity\User;
use App\Domains\User\Type\UserId;

class UserUpdatedEvent extends AbstractEvent
{
    /** @var User */
    private $user;

    public function __construct(
        User $user,
        UserId $eventCreatedBy,
        ?EventId $id = null,
        ?EventCreatedAt $createdAt = null
    ) {
        parent::__construct(new EventName('USER_UPDATED'), $eventCreatedBy, $id, $createdAt);

        $this->user = $user;
    }

    public function toArray(): array
    {
        $eventData = parent::toArray();
        $eventData['user'] = $this->user->toArray();

        return $eventData;
    }
}
