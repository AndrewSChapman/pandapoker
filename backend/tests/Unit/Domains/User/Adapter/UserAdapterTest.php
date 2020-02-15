<?php

namespace Testing\Unit\Domains\Room\Adapter;

use App\Domains\Shared\Exception\AdapterException;
use App\Domains\User\Adapter\UserAdapter;
use Faker\Provider\Uuid;
use PHPUnit\Framework\TestCase;

/**
 * @group UserAdapter
 */
class UserAdapterTest extends TestCase
{
    public function testUserAdapterCorrectlyInstantiatesUserFromArray(): void
    {
        $user = UserAdapter::getUserFromArray([
            'id' => Uuid::uuid(),
            'username' => 'Test User',
            'totem_animal' => 'dog',
            'created_at' => 12345,
            'updated_at' => 123456
        ]);

        $this->assertEquals('Test User', $user->getUsername()->toString());
    }

    /**
     * @param array $invalidData
     * @dataProvider invalidUserDataProvider
     */
    public function testUserAdapterThrowsAdapterExceptionIfDataInvalid(array $invalidData): void
    {
        $this->expectException(AdapterException::class);

        UserAdapter::getUserFromArray($invalidData);
    }

    public function invalidUserDataProvider(): array
    {
        return [
            [
                [
                    'username' => 'Test User',
                    'totem_animal' => 'dog',
                    'created_at' => 12345,
                    'updated_at' => 123456
                ]
            ],
            [
                [
                    'id' => Uuid::uuid(),
                    'totem_animal' => 'dog',
                    'created_at' => 12345,
                    'updated_at' => 123456
                ]
            ],
            [
                [
                    'id' => Uuid::uuid(),
                    'username' => 'Test User',
                    'totem_animal' => 'dog',
                    'updated_at' => 123456
                ]
            ],
            [
                [
                    'id' => Uuid::uuid(),
                    'username' => 'Test User',
                    'totem_animal' => 'dog',
                    'created_at' => 12345,
                ]
            ],
            [
                [
                    'id' => Uuid::uuid(),
                    'username' => 'Test User',
                    'created_at' => 12345,
                    'updated_at' => 123456
                ]
            ],
        ];
    }
}
