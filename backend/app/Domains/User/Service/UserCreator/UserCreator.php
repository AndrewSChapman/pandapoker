<?php

namespace App\Domains\User\Service\UserCreator;

use App\Domains\Shared\Security\Service\TokenService\TokenServiceInterface;
use App\Domains\Shared\Security\Service\TokenService\Type\TokenExpiresAt;
use App\Domains\Shared\Security\Service\TokenService\Type\TokenExpiryDuration;
use App\Domains\Shared\Security\Service\TokenService\ValueObject\TokenInfo;
use App\Domains\User\Entity\User;
use App\Domains\User\Event\UserCreatedEvent;
use App\Domains\User\Exception\UserAlreadyExistsException;
use App\Domains\User\Repository\UserRepositoryInterface;
use App\Domains\User\Type\AnimalType;
use App\Domains\User\Type\UserId;
use App\Domains\User\Type\Username;
use App\Util\Logger\Logger;
use App\Util\Logger\Type\LogLevel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UserCreator implements UserCreatorInterface
{
    /** @var UserRepositoryInterface */
    private $userRepository;

    /** @var TokenServiceInterface */
    private $tokenService;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var TokenExpiryDuration */
    private $tokenExpiryDuration;

    public function __construct(
        UserRepositoryInterface $userRepository,
        TokenServiceInterface $tokenService,
        TokenExpiryDuration $tokenExpiryDuration,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->userRepository = $userRepository;
        $this->tokenService = $tokenService;
        $this->tokenExpiryDuration = $tokenExpiryDuration;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function createUser(Username $username, AnimalType $totemAnimal): TokenInfo
    {
        Logger::log(LogLevel::DEBUG, "UserCreator - Creating user with name: {$username}");

        $existingUser = $this->userRepository->getUserByUsername($username);
        if ($existingUser) {
            Logger::log(LogLevel::DEBUG, "UserCreator - User already exists with name: {$username}");
            throw new UserAlreadyExistsException($username);
        }

        $user = new User(
            new UserId('', true),
            $username,
            $totemAnimal
        );

        $this->userRepository->saveUser($user);
        Logger::log(LogLevel::DEBUG, "UserCreator - User created with name: {$username}");

        $tokenExpiresAt = new TokenExpiresAt(time() + $this->tokenExpiryDuration->getValue());

        $token = $this->tokenService->createToken(new TokenInfo($user->getId(), $tokenExpiresAt));

        Logger::log(LogLevel::DEBUG, "UserCreator - User token created");

        $this->eventDispatcher->dispatch(new UserCreatedEvent($user, $user->getId()));

        return new TokenInfo(
            $user->getId(),
            $tokenExpiresAt,
            $token
        );
    }
}
