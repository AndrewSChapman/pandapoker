<?php

namespace App\Domains\User\Service\UserLister;

use App\Domains\User\Collection\UserList;

interface UserListerInterface
{
    public function getUserList(): UserList;
}
