<?php

namespace App\Domains\User\Repository;

use App\Domains\User\Collection\UserList;
use App\Domains\User\Entity\User;
use App\Domains\User\Type\UserId;
use App\Domains\User\Type\Username;

interface UserRepositoryInterface
{
    public function saveUser(User $user): void;
    public function getUser(UserId $userId): User;
    public function getUserByUsername(Username $username): ?User;
    public function getUsers(): UserList;
}
