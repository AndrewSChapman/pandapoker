<?php

namespace Testing\Unit\Domains\Shared\Security\Service;

use App\Domains\Shared\Security\Service\TokenService\JwtTokenService;
use App\Domains\Shared\Security\Service\TokenService\Type\Token;
use App\Domains\Shared\Security\Service\TokenService\Type\TokenExpiresAt;
use App\Domains\Shared\Security\Service\TokenService\Type\TokenExpiryDuration;
use App\Domains\Shared\Security\Service\TokenService\Type\TokenIssuer;
use App\Domains\Shared\Security\Service\TokenService\Type\TokenSecret;
use App\Domains\Shared\Security\Service\TokenService\ValueObject\TokenInfo;
use Testing\Unit\UnitTest;

class JwtTokenServiceTest extends UnitTest
{
    public function testJwtTokenServiceCanCreateAndVerifyToken(): void
    {
        $user = $this->getDataHelper()->user()->makeUser();

        $expiryDuration = new TokenExpiryDuration(intval(env('TOKEN_EXPIRY_DURATION')));

        $tokenInfo = new TokenInfo(
            $user->getId(),
            new TokenExpiresAt(time() + $expiryDuration->getValue())
        );

        $tokenService = $this->getTokenService();

        $token = $tokenService->createToken($tokenInfo);

        $this->assertInstanceOf(Token::class, $token);

        $tokenInfo = $tokenService->verifyToken($token);

        $this->assertTrue($user->getId()->equals($tokenInfo->getUserId()));
        $this->assertTrue($tokenInfo->getExpires()->getTimestamp() > time());
    }

    private function getTokenService(): JwtTokenService
    {
        $secret = new TokenSecret(env('JWT_SECRET'));
        $issuer = new TokenIssuer(env('JWT_ISSUER'));

        return new JwtTokenService($secret, $issuer);
    }
}
