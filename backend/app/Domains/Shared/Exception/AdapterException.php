<?php

namespace App\Domains\Shared\Exception;

class AdapterException extends \DomainException
{
    public function __construct(string $adapterClassName, string $reason)
    {
        parent::__construct("The '{$adapterClassName}' threw an exception: {$reason}");
    }
}
