<?php

namespace App\Domains\User\Service\UserCreator;

use App\Domains\Shared\Security\Service\TokenService\ValueObject\TokenInfo;
use App\Domains\User\Type\AnimalType;
use App\Domains\User\Type\Username;

interface UserCreatorInterface
{
    public function createUser(Username $username, AnimalType $totemAnimal): TokenInfo;
}
