<?php

namespace Testing\Unit\Domains\Room\Adapter;

use App\Domains\Room\Adapter\RoomAdapter;
use App\Domains\Shared\Exception\AdapterException;
use Faker\Provider\Uuid;
use PHPUnit\Framework\TestCase;

class RoomAdapterTest extends TestCase
{
    private const ROOM_VOTES = [
        [
            'user_id' => '237e57f7-b5f8-4de2-96f8-3d49e408473d',
            'vote' => 3
        ],
        [
            'user_id' => 'da508b74-ba87-4cff-99e9-153957581b49',
            'vote' => 2
        ],
    ];

    private const ROOM_PARTICIPANTS = [
        [
            'user_id' => '237e57f7-b5f8-4de2-96f8-3d49e408473d',
            'is_voting' => true
        ],
        [
            'user_id' => 'da508b74-ba87-4cff-99e9-153957581b49',
            'is_voting' => false
        ],
    ];

    public function testRoomAdapterCorrectlyInstantiatesRoomFromArrayWithoutVotes(): void
    {
        $room = RoomAdapter::getRoomFromArray([
            'id' => Uuid::uuid(),
            'name' => 'Test Room',
            'created_by_user_id' => Uuid::uuid(),
            'vote_options' => [1, 2, 3, 8],
            'created_at' => 12345,
            'updated_at' => 123456
        ]);

        $this->assertEquals('Test Room', $room->getName()->toString());
        $this->assertCount(4, $room->getRoomVoteOptions());
    }

    public function testRoomAdapterCorrectlyInstantiatesRoomFromArrayWithVotes(): void
    {
        $room = RoomAdapter::getRoomFromArray([
            'id' => Uuid::uuid(),
            'name' => 'Test Room',
            'created_by_user_id' => Uuid::uuid(),
            'vote_options' => [1, 2, 3, 8],
            'votes' => self::ROOM_VOTES,
            'created_at' => 12345,
            'updated_at' => 123456
        ]);

        $this->assertEquals('Test Room', $room->getName()->toString());
        $this->assertCount(2, $room->getRoomVotes());
    }

    public function testRoomAdapterCorrectlyInstantiatesRoomFromArrayWithParticipants(): void
    {
        $room = RoomAdapter::getRoomFromArray([
            'id' => Uuid::uuid(),
            'name' => 'Test Room',
            'created_by_user_id' => Uuid::uuid(),
            'vote_options' => [1, 2, 3, 8],
            'participants' => self::ROOM_PARTICIPANTS,
            'voting_open' => true,
            'created_at' => 12345,
            'updated_at' => 123456
        ]);

        $this->assertEquals('Test Room', $room->getName()->toString());
        $this->assertCount(2, $room->getRoomParticipants());
        $this->assertTrue($room->isVotingOpen());
    }

    /**
     * @param array $invalidData
     * @dataProvider invalidRoomDataProvider
     */
    public function testRoomAdapterThrowsAdapterExceptionIfDataInvalid(array $invalidData): void
    {
        $this->expectException(AdapterException::class);

        RoomAdapter::getRoomFromArray($invalidData);
    }

    public function invalidRoomDataProvider(): array
    {
        return [
            [
                [
                    'name' => 'Test Room',
                    'created_by_user_id' => Uuid::uuid(),
                    'vote_options' => [1, 2, 4, 8],
                    'votes' => self::ROOM_VOTES,
                    'participants' => self::ROOM_PARTICIPANTS,
                    'voting_open' => true,
                    'created_at' => 12345,
                    'updated_at' => 123456
                ]
            ],
            [
                [
                    'id' => Uuid::uuid(),
                    'created_by_user_id' => Uuid::uuid(),
                    'vote_options' => [1, 2, 4, 8],
                    'votes' => self::ROOM_VOTES,
                    'participants' => self::ROOM_PARTICIPANTS,
                    'voting_open' => true,
                    'created_at' => 12345,
                    'updated_at' => 123456
                ]
            ],
            [
                [
                    'id' => Uuid::uuid(),
                    'name' => 'Test Room',
                    'vote_options' => [1, 2, 4, 8],
                    'votes' => self::ROOM_VOTES,
                    'participants' => self::ROOM_PARTICIPANTS,
                    'voting_open' => true,
                    'created_at' => 12345,
                    'updated_at' => 123456
                ]
            ],
            [
                [
                    'id' => Uuid::uuid(),
                    'created_by_user_id' => Uuid::uuid(),
                    'vote_options' => [1, 2, 4, 8],
                    'votes' => self::ROOM_VOTES,
                    'participants' => self::ROOM_PARTICIPANTS,
                    'voting_open' => true,
                    'name' => 'Test Room',
                    'updated_at' => 123456
                ]
            ],
            [
                [
                    'id' => Uuid::uuid(),
                    'created_by_user_id' => Uuid::uuid(),
                    'vote_options' => [1, 2, 4, 8],
                    'votes' => self::ROOM_VOTES,
                    'participants' => self::ROOM_PARTICIPANTS,
                    'voting_open' => true,
                    'name' => 'Test Room',
                    'created_at' => 12345,
                ]
            ],
        ];
    }
}
