<?php

namespace App\Domains\Shared\Event\Type;

use PhpTypes\Type\ConstrainedString;

class EventName extends ConstrainedString
{
    public function __construct(string $eventName)
    {
        parent::__construct($eventName, 1);
    }
}
