<?php

namespace Testing\DataHelper\Modules;

use App\Domains\Shared\Security\Service\TokenService\TokenServiceInterface;
use App\Domains\Shared\Security\Service\TokenService\Type\Token;
use App\Domains\Shared\Security\Service\TokenService\Type\TokenExpiresAt;
use App\Domains\Shared\Security\Service\TokenService\ValueObject\TokenInfo;
use App\Domains\User\Entity\User;
use PHPUnit\Framework\MockObject\MockObject;

class SecurityDataHelper extends AbstractDataHelperModule
{
    /**
     * @return TokenServiceInterface|MockObject
     */
    public function makeTokenService(): TokenServiceInterface
    {
        return $this->getTestCase()->getMockBuilder(TokenServiceInterface::class)
            ->getMock();
    }

    public function getTokenInfo(User $user): TokenInfo
    {
        return new TokenInfo($user->getId(), new TokenExpiresAt(time() + 86400), new Token('SOME_TOKEN'));
    }
}
