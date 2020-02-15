<?php

namespace App\Domains\User\Adapter;

use App\Domains\Shared\Exception\AdapterException;
use App\Domains\Shared\Type\CreatedAt;
use App\Domains\Shared\Type\UpdatedAt;
use App\Domains\User\Entity\User;
use App\Domains\User\Type\AnimalType;
use App\Domains\User\Type\UserId;
use App\Domains\User\Type\Username;

class UserAdapter
{
    public static function getUserFromArray(array $data): User
    {
        try {
            $createdAt = $data['created_at'] ?? null;
            $updatedAt = $data['updated_at'] ?? null;

            if (!is_numeric($createdAt)) {
                throw new AdapterException(UserAdapter::class, 'created_at attribute invalid or missing');
            }

            if (!is_numeric($updatedAt)) {
                throw new AdapterException(UserAdapter::class, 'updated_at attribute invalid or missing');
            }

            $id = new UserId($data['id'] ?? '');
            $username = new Username($data['username'] ?? '');
            $totemAnimal = new AnimalType($data['totem_animal'] ?? '');
            $createdAt = new CreatedAt($createdAt);
            $updatedAt = new UpdatedAt($updatedAt);

            return new User($id, $username, $totemAnimal, $createdAt, $updatedAt);
        } catch (\Exception $exception) {
            throw new AdapterException(UserAdapter::class, $exception->getMessage());
        } catch (\TypeError $typeError) {
            throw new AdapterException(UserAdapter::class, $typeError->getMessage());
        }
    }
}
