<?php

namespace App\Domains\User\Exception;

use App\Domains\User\Type\Username;
use DomainException;

class UserAlreadyExistsException extends DomainException
{
    public function __construct(Username $username)
    {
        parent::__construct("A user with username '{$username}' already exists!");
    }
}
