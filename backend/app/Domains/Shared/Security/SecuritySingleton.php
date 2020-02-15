<?php


namespace App\Domains\Shared\Security;

use App\Domains\Shared\Security\Service\TokenService\ValueObject\TokenInfo;

class SecuritySingleton
{
    /** @var TokenInfo|null */
    private static $tokenInfo;

    public static function setTokenInfo(TokenInfo $tokenInfo): void
    {
        if (static::$tokenInfo) {
            throw new \Exception('SecuritySingleton::setTokenInfo - TokenInfo already set!');
        }

        static::$tokenInfo = $tokenInfo;
    }

    public static function getTokenInfo(): TokenInfo
    {
        if (!static::$tokenInfo) {
            throw new \Exception('SecuritySingleton::getTokenInfo - TokenInfo not set!');
        }

        return static::$tokenInfo;
    }

    public static function hasTokenInfo(): bool
    {
        return !is_null(static::$tokenInfo);
    }
}
