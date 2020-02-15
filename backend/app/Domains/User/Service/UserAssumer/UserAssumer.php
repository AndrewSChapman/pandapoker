<?php

namespace App\Domains\User\Service\UserAssumer;

use App\Domains\Shared\Exception\EntityNotFoundException;
use App\Domains\Shared\Security\Service\TokenService\TokenServiceInterface;
use App\Domains\Shared\Security\Service\TokenService\Type\TokenExpiresAt;
use App\Domains\Shared\Security\Service\TokenService\Type\TokenExpiryDuration;
use App\Domains\Shared\Security\Service\TokenService\ValueObject\TokenInfo;
use App\Domains\User\Entity\User;
use App\Domains\User\Repository\UserRepositoryInterface;
use App\Domains\User\Type\Username;
use App\Util\Logger\Logger;
use App\Util\Logger\Type\LogLevel;

class UserAssumer implements UserAssumerInterface
{
    /** @var UserRepositoryInterface */
    private $userRepository;

    /** @var TokenServiceInterface */
    private $tokenService;

    /** @var TokenExpiryDuration */
    private $tokenExpiryDuration;

    public function __construct(
        UserRepositoryInterface $userRepository,
        TokenServiceInterface $tokenService,
        TokenExpiryDuration $tokenExpiryDuration
    ) {
        $this->userRepository = $userRepository;
        $this->tokenService = $tokenService;
        $this->tokenExpiryDuration = $tokenExpiryDuration;
    }

    public function assumeUserByUsername(Username $username): TokenInfo
    {
        Logger::log(LogLevel::INFO, "UserAssumer - User being assumed {$username}");

        // Find the user using the username
        $user = $this->userRepository->getUserByUsername($username);
        if (!$user) {
            Logger::log(LogLevel::DEBUG, "UserAssumer - User not found");
            throw new EntityNotFoundException(User::class);
        }

        // Create a new login token and return it
        $tokenExpiresAt = new TokenExpiresAt(time() + $this->tokenExpiryDuration->getValue());

        $token = $this->tokenService->createToken(new TokenInfo($user->getId(), $tokenExpiresAt));

        Logger::log(LogLevel::DEBUG, "UserAssumer - User token created");

        return new TokenInfo(
            $user->getId(),
            $tokenExpiresAt,
            $token
        );
    }
}
