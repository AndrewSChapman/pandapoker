<?php

namespace App\Domains\Shared\Security\Service\TokenService;

use App\Domains\Shared\Security\Service\TokenService\Type\Token;
use App\Domains\Shared\Security\Service\TokenService\ValueObject\TokenInfo;

interface TokenServiceInterface
{
    public function createToken(TokenInfo $tokenInfo): Token;
    public function verifyToken(Token $token): ?TokenInfo;
}
