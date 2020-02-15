<?php

namespace App\Domains\User\Type;

use PhpTypes\Type\ConstrainedString;

class Username extends ConstrainedString
{
    public function __construct(string $username)
    {
        parent::__construct($username, 2, 50);
    }
}
