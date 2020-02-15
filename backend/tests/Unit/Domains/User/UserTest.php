<?php

namespace Testing\Unit\Domains\User;

use App\Domains\Shared\Type\CreatedAt;
use App\Domains\Shared\Type\UpdatedAt;
use App\Domains\User\Entity\User;
use App\Domains\User\Type\AnimalType;
use App\Domains\User\Type\UserId;
use App\Domains\User\Type\Username;
use Testing\Unit\UnitTest;

class UserTest extends UnitTest
{
    private const USERNAME = 'TestyMcTesterson';
    private const TOTEM_ANIMAL = 'cat';
    private const CREATED_AT = 12345;
    private const UPDATED_AT = 123456;

    public function testUserInstantiatesAndValuesAreCorrect(): void
    {
        $userId = new UserId('', true);
        $userName = new Username(self::USERNAME);
        $totemAnimal = new AnimalType(self::TOTEM_ANIMAL);
        $createdAt = new CreatedAt(self::CREATED_AT);
        $updatedAt = new UpdatedAt(self::UPDATED_AT);

        $user = new User($userId, $userName, $totemAnimal, $createdAt, $updatedAt);

        $this->assertEquals((string)$userId, (string)$user->getId());
        $this->assertEquals(self::USERNAME, $user->getUsername()->getValue());
        $this->assertEquals(self::TOTEM_ANIMAL, (string)$user->getTotemAnimal());
        $this->assertEquals(self::CREATED_AT, $user->getCreatedAt()->getTimestamp());
        $this->assertEquals(self::UPDATED_AT, $user->getUpdatedAt()->getTimestamp());

        $expectedArray = [
            'id' => $userId->getUuid()->toString(),
            'username' => $userName->toString(),
            'totem_animal' => (string)$totemAnimal,
            'created_at' => $createdAt->getTimestamp(),
            'updated_at' => $updatedAt->getTimestamp()
        ];

        $this->assertEquals($expectedArray, $user->toArray());
    }

    public function testUserInstantiatesWithoutTimestamps(): void
    {
        $userId = new UserId('', true);
        $userName = new Username(self::USERNAME);
        $totemAnimal = new AnimalType(self::TOTEM_ANIMAL);

        $user = new User($userId, $userName, $totemAnimal);

        $this->assertEquals((string)$userId, (string)$user->getId());
        $this->assertEquals(self::USERNAME, $user->getUsername()->getValue());
        $this->assertEquals(self::TOTEM_ANIMAL, (string)$user->getTotemAnimal());
        $this->assertGreaterThan(time() - 10, $user->getCreatedAt()->getTimestamp());
        $this->assertGreaterThan(time() - 10, $user->getUpdatedAt()->getTimestamp());
    }
}
