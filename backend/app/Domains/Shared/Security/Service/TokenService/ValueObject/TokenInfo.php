<?php

namespace App\Domains\Shared\Security\Service\TokenService\ValueObject;

use App\Domains\Shared\Security\Service\TokenService\Type\Token;
use App\Domains\Shared\Security\Service\TokenService\Type\TokenExpiresAt;
use App\Domains\User\Type\UserId;

class TokenInfo
{
    /** @var UserId */
    private $userId;

    /** @var TokenExpiresAt */
    private $expires;

    /** @var Token|null */
    private $token;

    public function __construct(UserId $userId, TokenExpiresAt $expires, Token $token = null)
    {
        $this->userId = $userId;
        $this->expires = $expires;
        $this->token = $token;
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function getExpires(): TokenExpiresAt
    {
        return $this->expires;
    }

    public function getToken(): Token
    {
        if (is_null($this->token)) {
            throw new \Exception('TokenInfo::getToken - token not set!!');
        }

        return $this->token;
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId->getUuid()->toString(),
            'token_expires_at' => $this->getExpires()->getTimestamp(),
            'token' => $this->token ? $this->token->toString() : ''
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            new UserId($data['user_id'] ?? ''),
            new TokenExpiresAt($data['token_expires_at'] ?? 0),
            new Token($data['token'] ?? '')
        );
    }
}
