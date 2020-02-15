<?php

namespace App\Domains\User\Service\UserUpdater;

use App\Domains\Shared\Exception\PermissionException;
use App\Domains\User\Entity\User;
use App\Domains\User\Event\UserUpdatedEvent;
use App\Domains\User\Exception\UserAlreadyExistsException;
use App\Domains\User\Repository\UserRepositoryInterface;
use App\Domains\User\Type\AnimalType;
use App\Domains\User\Type\UserId;
use App\Domains\User\Type\Username;
use App\Util\Logger\Logger;
use App\Util\Logger\Type\LogLevel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UserUpdater implements UserUpdaterInterface
{
    /** @var UserRepositoryInterface */
    private $userRepository;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        UserRepositoryInterface $userRepositoryInterface,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->userRepository = $userRepositoryInterface;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function updateUser(
        UserId $loggedInUserId,
        UserId $userToUpdateId,
        Username $username,
        AnimalType $totemAnimal
    ): User {
        if (!$loggedInUserId->equals($userToUpdateId)) {
            throw new PermissionException($loggedInUserId, "You may not update userId $userToUpdateId");
        }

        $user = $this->userRepository->getUser($userToUpdateId);

        // Only update the user if the display/user name OR the totem animal has actually changed
        if (($user->getUsername()->equals($username)) &&
            ($user->getTotemAnimal()->is($totemAnimal))) {
            return $user;
        }

        $existingUser = $this->userRepository->getUserByUsername($username);
        if (($existingUser) && (!$existingUser->getId()->equals($loggedInUserId))) {
            Logger::log(LogLevel::DEBUG, "UserUpdater - A user already exists with name: {$username}");
            throw new UserAlreadyExistsException($username);
        }

        $user->setUsername($username);
        $user->setTotemAnimal($totemAnimal);
        $this->userRepository->saveUser($user);

        $this->eventDispatcher->dispatch(new UserUpdatedEvent($user, $user->getId()));

        return $user;
    }
}
