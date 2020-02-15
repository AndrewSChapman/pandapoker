<?php

namespace App\Domains\Shared\Security\Service\TokenService\Type;

use PhpTypes\Type\ConstrainedString;

class TokenIssuer extends ConstrainedString
{
    public function __construct(string $secret)
    {
        parent::__construct($secret, 2);
    }
}
