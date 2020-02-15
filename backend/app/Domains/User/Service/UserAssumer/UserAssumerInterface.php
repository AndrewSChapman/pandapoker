<?php

namespace App\Domains\User\Service\UserAssumer;

use App\Domains\Shared\Security\Service\TokenService\ValueObject\TokenInfo;
use App\Domains\User\Type\Username;

interface UserAssumerInterface
{
    public function assumeUserByUsername(Username $username): TokenInfo;
}
