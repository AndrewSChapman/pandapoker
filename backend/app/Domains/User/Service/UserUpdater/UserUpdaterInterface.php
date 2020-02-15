<?php

namespace App\Domains\User\Service\UserUpdater;

use App\Domains\User\Entity\User;
use App\Domains\User\Type\AnimalType;
use App\Domains\User\Type\UserId;
use App\Domains\User\Type\Username;

interface UserUpdaterInterface
{
    public function updateUser(
        UserId $loggedInUserId,
        UserId $userToUpdateId,
        Username $username,
        AnimalType $totemAnimal
    ): User;
}
