<?php

namespace App\Domains\Shared\Exception;

class EntityNotFoundException extends \DomainException
{
    public function __construct(string $entityClassName)
    {
        parent::__construct("The '{$entityClassName}' entity could not be found");
    }
}
