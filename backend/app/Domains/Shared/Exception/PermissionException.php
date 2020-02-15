<?php


namespace App\Domains\Shared\Exception;

use App\Domains\User\Type\UserId;

class PermissionException extends \DomainException
{
    public function __construct(UserId $userId, string $action)
    {
        parent::__construct("User with id '{$userId} is not allowed to perform action: $action'");
    }
}
