<?php

namespace App\Domains\Shared\Security\Service\TokenService\Type;

use PhpTypes\Type\ConstrainedString;

class Token extends ConstrainedString
{
    public function __construct(string $token)
    {
        parent::__construct($token, 10);
    }
}
