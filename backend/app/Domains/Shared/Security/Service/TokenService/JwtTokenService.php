<?php

namespace App\Domains\Shared\Security\Service\TokenService;

use App\Domains\Shared\Security\Service\TokenService\Type\TokenExpiresAt;
use App\Domains\User\Type\UserId;
use ReallySimpleJWT\Token as SimpleTokenService;
use App\Domains\Shared\Security\Service\TokenService\Type\Token;
use App\Domains\Shared\Security\Service\TokenService\Type\TokenIssuer;
use App\Domains\Shared\Security\Service\TokenService\Type\TokenSecret;
use App\Domains\Shared\Security\Service\TokenService\ValueObject\TokenInfo;

class JwtTokenService implements TokenServiceInterface
{
    /** @var TokenSecret */
    private $tokenSecret;

    /** @var TokenIssuer */
    private $tokenIssuer;

    public function __construct(TokenSecret $tokenSecret, TokenIssuer $tokenIssuer)
    {
        $this->tokenSecret = $tokenSecret;
        $this->tokenIssuer = $tokenIssuer;
    }

    public function createToken(TokenInfo $tokenInfo): Token
    {
        $token = SimpleTokenService::create(
            (string)$tokenInfo->getUserId(),
            (string)$this->tokenSecret,
            $tokenInfo->getExpires()->getTimestamp(),
            (string)$this->tokenIssuer
        );

        return new Token($token);
    }

    public function verifyToken(Token $token): ?TokenInfo
    {
        if(!SimpleTokenService::validate($token->toString(), $this->tokenSecret->toString())) {
            return null;
        }

        $headers = SimpleTokenService::getHeader($token->toString(), $this->tokenSecret->toString());
        $payload = SimpleTokenService::getPayload($token->toString(), $this->tokenSecret->toString());

        $expiresAt = intval($payload['exp'] ?? 0);
        if ($expiresAt === 0) {
            return null;
        }

        $userId = new UserId($payload['user_id'] ?? '', false);
        $tokenExpiresAt = new TokenExpiresAt($expiresAt);

        return new TokenInfo($userId, $tokenExpiresAt);
    }
}
