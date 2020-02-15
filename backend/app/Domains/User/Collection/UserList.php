<?php

namespace App\Domains\User\Collection;

use App\Domains\User\Entity\User;
use PhpTypes\Collection\AbstractList;

class UserList extends AbstractList
{
    public function add(User $user): void
    {
        $this->values[] = $user;
    }

    public function current(): User
    {
        return $this->offsetGet($this->iteratorPointer);
    }

    public function offsetGet($offset): User
    {
        return $this->values[$offset];
    }

    public function sortByUsername(bool $caseInsensitive = true): void
    {
        usort($this->values, function(User $a, User $b) use ($caseInsensitive) {
            $usernameA = $a->getUsername()->toString();
            $usernameB = $b->getUsername()->toString();

            if ($caseInsensitive) {
                $usernameA = strtolower($usernameA);
                $usernameB = strtolower($usernameB);
            }

            return $usernameA <=> $usernameB;
        });
    }
}
