<?php

namespace App\Domains\Shared\Security\Service\TokenService\Type;

use PhpTypes\Type\ConstrainedString;

class TokenSecret extends ConstrainedString
{
    public function __construct(string $secret)
    {
        parent::__construct($secret, 8);
    }
}
