<?php

namespace Testing\Integration\Domains\Room\Repository;

use App\Domains\Room\Collection\RoomVoteOptionList;
use App\Domains\Room\Entity\Room;
use App\Domains\Room\Repository\RoomRepository\RoomRepository;
use App\Domains\Room\Type\RoomId;
use App\Domains\Room\Type\RoomName;
use App\Domains\Room\Type\RoomVoteOption;
use App\Domains\Room\ValueObject\RoomParticipant;
use App\Domains\Room\ValueObject\RoomVote;
use App\Domains\Shared\Type\CreatedAt;
use App\Domains\Shared\Type\UpdatedAt;
use App\Domains\User\Type\UserId;
use Testing\Integration\IntegrationTest;

class RoomRepositoryTest extends IntegrationTest
{
    private const ROOM_NAME = 'Test Room';
    private const VOTING_OPEN = true;
    private const CREATED_AT = 12345;
    private const UPDATED_AT = 123456;
    private const ROOM_VOTE_OPTIONS = [1, 2, 3, 5, 8, 13];

    public function testRoomRepositoryCanSaveAndRetrieveRoom(): void
    {
        $dataStore = $this->dataStore();
        $dataStore->flush();

        $roomId = new RoomId('', true);
        $roomName = new RoomName(self::ROOM_NAME);
        $createdByUserId = new UserId('', true);
        $createdAt = new CreatedAt(self::CREATED_AT);
        $updatedAt = new UpdatedAt(self::UPDATED_AT);
        $roomVoteOptionList = new RoomVoteOptionList();

        foreach(self::ROOM_VOTE_OPTIONS as $voteOption) {
            $roomVoteOptionList->add(new RoomVoteOption($voteOption));
        }

        $room = new Room(
            $roomId,
            $roomName,
            $createdByUserId,
            $roomVoteOptionList,
            null,
            null,
            self::VOTING_OPEN,
            $createdAt,
            $updatedAt
        );

        // Add a participant
        $room->addParticipant(new RoomParticipant(new UserId('237e57f7-b5f8-4de2-96f8-3d49e408473d'), true));

        // Add a vote for good measure
        $room->addVote(new RoomVote(new UserId('237e57f7-b5f8-4de2-96f8-3d49e408473d'), new RoomVoteOption(2)));

        $repository = new RoomRepository($dataStore);
        $repository->saveRoom($room);

        $savedRoom = $repository->getRoom($roomId);

        $this->assertEquals($room->getId()->getUuid(), $savedRoom->getId()->getUuid());
        $this->assertCount(6, $savedRoom->getRoomVoteOptions());
        $this->assertCount(1, $savedRoom->getRoomVotes());
        $this->assertCount(1, $savedRoom->getRoomParticipants());

        $rooms = $repository->getRooms();
        $this->assertCount(1, $rooms);
    }
}
