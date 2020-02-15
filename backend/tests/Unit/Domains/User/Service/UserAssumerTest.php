<?php

namespace Testing\Unit\Domains\User\Service;

use App\Domains\Shared\Exception\EntityNotFoundException;
use App\Domains\Shared\Security\Service\TokenService\Type\Token;
use App\Domains\Shared\Security\Service\TokenService\Type\TokenExpiryDuration;
use App\Domains\Shared\Security\Service\TokenService\ValueObject\TokenInfo;
use App\Domains\User\Service\UserAssumer\UserAssumer;
use App\Domains\User\Type\UserId;
use App\Domains\User\Type\Username;
use Testing\Unit\UnitTest;

class UserAssumerTest extends UnitTest
{
    private const USERNAME = 'Testy McTest';
    private const TOKEN_DURATION = 86400;

    public function testUserAssumerThrowsExceptionIfUserNotFound(): void
    {
        $this->expectException(EntityNotFoundException::class);

        $userRepository = $this->getDataHelper()->user()->makeUserRepository();
        $userRepository->expects($this->once())->method('getUserByUsername')->willReturn(null);

        $tokenService = $this->getDataHelper()->security()->makeTokenService();
        $tokenExpiry = new TokenExpiryDuration(self::TOKEN_DURATION);

        $userAssumer = new UserAssumer(
            $userRepository,
            $tokenService,
            $tokenExpiry
        );

        $userAssumer->assumeUserByUsername(new Username(self::USERNAME));
    }

    public function testUserAssumerCallsCorrectMethodsIfUserFound(): void
    {
        $userRepository = $this->getDataHelper()->user()->makeUserRepository();
        $userId = new UserId('', true);
        $userName = new Username(self::USERNAME);
        $user = $this->getDataHelper()->user()->makeUser($userId, $userName);
        $userRepository->expects($this->once())->method('getUserByUsername')->willReturn($user);

        $tokenService = $this->getDataHelper()->security()->makeTokenService();
        $tokenService->expects($this->once())->method('createToken')->willReturnCallback(
            function(TokenInfo $tokenInfo) use ($userId) {
                $this->assertGreaterThan(time() + self::TOKEN_DURATION - 10, $tokenInfo->getExpires()->getTimestamp());
                $this->assertTrue($userId->equals($tokenInfo->getUserId()));
                return new Token('SOME_TOKEN');
            }
        );

        $tokenExpiry = new TokenExpiryDuration(self::TOKEN_DURATION);

        $userAssumer = new UserAssumer(
            $userRepository,
            $tokenService,
            $tokenExpiry
        );

        $tokenInfo = $userAssumer->assumeUserByUsername(new Username(self::USERNAME));
        $this->assertTrue($userId->equals($tokenInfo->getUserId()));
    }
}
