<?php

namespace App\Domains\User\Service\UserLister;

use App\Domains\User\Collection\UserList;
use App\Domains\User\Repository\UserRepositoryInterface;

class UserLister implements UserListerInterface
{
    /** @var UserRepositoryInterface */
    private $userRepository;

    /**
     * UserLister constructor.
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUserList(): UserList
    {
        $userList = $this->userRepository->getUsers();
        $userList->sortByUsername();

        return $userList;
    }
}
