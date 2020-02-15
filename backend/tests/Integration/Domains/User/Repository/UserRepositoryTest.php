<?php

namespace Testing\Integration\Domains\Room\Repository;

use App\Domains\Shared\Type\CreatedAt;
use App\Domains\Shared\Type\UpdatedAt;
use App\Domains\User\Entity\User;
use App\Domains\User\Repository\UserRepository;
use App\Domains\User\Type\AnimalType;
use App\Domains\User\Type\UserId;
use App\Domains\User\Type\Username;
use Testing\Integration\IntegrationTest;

class UserRepositoryTest extends IntegrationTest
{
    private const USER_NAME = 'Testy McTest';
    private const CREATED_AT = 12345;
    private const UPDATED_AT = 123456;
    private const TOTEM_ANIMAL = AnimalType::ELEPHANT;

    public function testUserRepositoryCanSaveAndRetrieveUser(): void
    {
        $dataStore = $this->dataStore();
        $dataStore->flush();

        $userId = new UserId('', true);
        $username = new Username(self::USER_NAME);
        $totemAnimal = new AnimalType(self::TOTEM_ANIMAL);
        $createdAt = new CreatedAt(self::CREATED_AT);
        $updatedAt = new UpdatedAt(self::UPDATED_AT);

        $user = new User($userId, $username, $totemAnimal, $createdAt, $updatedAt);

        $repository = new UserRepository($dataStore);
        $repository->saveUser($user);

        $savedUser = $repository->getUser($userId);
        $this->assertTrue($user->getId()->equals($savedUser->getId()));

        // Create another user
        $userId2 = new UserId('', true);
        $username2 = new Username(self::USER_NAME . '2');
        $user2 = new User($userId2, $username2, $totemAnimal);
        $repository->saveUser($user2);

        // Test getting a list of users
        $userList = $repository->getUsers();
        $this->assertCount(2, $userList);

        $foundId1 = false;
        $foundId2 = false;

        foreach ($userList as $thisUser) {
            if ($thisUser->getId()->equals($userId)) {
                $foundId1 = true;
            } else if ($thisUser->getId()->equals($userId2)) {
                $foundId2 = true;
            }
        }

        $this->assertTrue($foundId1);
        $this->assertTrue($foundId2);
    }
}
