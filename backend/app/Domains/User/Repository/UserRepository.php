<?php

namespace App\Domains\User\Repository;

use App\Domains\Shared\Exception\EntityNotFoundException;
use App\Domains\Shared\Persistence\DataStore\DataStoreInterface;
use App\Domains\Shared\Persistence\DataStore\Type\DataStoreKey;
use App\Domains\User\Adapter\UserAdapter;
use App\Domains\User\Collection\UserList;
use App\Domains\User\Entity\User;
use App\Domains\User\Type\UserId;
use App\Domains\User\Type\Username;
use PhpTypes\Type\Timestamp;

class UserRepository implements UserRepositoryInterface
{
    /** @var DataStoreInterface */
    private $dataStore;

    /**
     * UserRepository constructor.
     * @param DataStoreInterface $dataStore
     */
    public function __construct(DataStoreInterface $dataStore)
    {
        $this->dataStore = $dataStore;
    }

    public function getUser(UserId $userId): User
    {
        $userData = $this->dataStore->getValueForKey($this->getCacheKey($userId));
        if (!$userData) {
            throw new EntityNotFoundException(User::class);
        }

        return UserAdapter::getUserFromArray($userData);
    }

    public function saveUser(User $user): void
    {
        $this->dataStore->setValueForKey(
            $this->getCacheKey($user->getId()),
            $user->toArray(),
            $this->getExpiryTimestamp()
        );
    }

    public function getUserByUsername(Username $username): ?User
    {
        // Do a case insensitive search of users
        $searchUsername = strtoupper(trim($username->toString()));

        $userKeyList = $this->dataStore->getKeys("*{$this->userKeyPrefix()}*");

        foreach ($userKeyList as $userKey) {
            $user = $this->getUser(new UserId($userKey->getIdFromKey()));
            if ($user) {
                $thisUsername = strtoupper(trim($user->getUsername()->toString()));
                if ($searchUsername === $thisUsername) {
                    return $user;
                }
            }
        }

        return null;
    }

    public function getUsers(): UserList
    {
        $userList = new UserList();

        $keys = $this->dataStore->getKeys("*{$this->userKeyPrefix()}*");

        foreach ($keys as $thisKey) {
            $userId = new UserId($thisKey->getIdFromKey(), false);
            $userList->add($this->getUser($userId));
        }

        return $userList;
    }

    private function userKeyPrefix(): string
    {
        return 'user:';
    }

    private function getCacheKey(UserId $userId): DataStoreKey
    {
        return new DataStoreKey("{$this->userKeyPrefix()}:{$userId->getUuid()}");
    }

    private function getExpiryTimestamp(): Timestamp
    {
        return new Timestamp(time() + (86400 * 365));
    }
}
