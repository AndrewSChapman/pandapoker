<?php

namespace Testing\Unit\Domains\Room\Entity;

use App\Domains\Room\Collection\RoomParticipantList;
use App\Domains\Room\Collection\RoomVoteList;
use App\Domains\Room\Collection\RoomVoteOptionList;
use App\Domains\Room\Entity\Room;
use App\Domains\Room\Exception\ParticipantNotInRoomException;
use App\Domains\Room\Exception\UserAlreadyVotedException;
use App\Domains\Room\Exception\VotingNotOpenException;
use App\Domains\Room\Type\RoomId;
use App\Domains\Room\Type\RoomName;
use App\Domains\Room\Type\RoomVoteOption;
use App\Domains\Room\ValueObject\RoomParticipant;
use App\Domains\Room\ValueObject\RoomVote;
use App\Domains\Shared\Type\CreatedAt;
use App\Domains\Shared\Type\UpdatedAt;
use App\Domains\User\Type\UserId;
use App\User;
use PHPUnit\Framework\TestCase;

class RoomTest extends TestCase
{
    private const ROOM_NAME = 'Test Room';
    private const VOTING_OPEN = true;
    private const CREATED_AT = 12345;
    private const UPDATED_AT = 123456;
    private const ROOM_VOTE_OPTIONS = [1, 2, 3, 5, 8, 13];
    private const VOTING_USER_ID1 = '237e57f7-b5f8-4de2-96f8-3d49e408473d';
    private const VOTING_USER_ID2 = 'da508b74-ba87-4cff-99e9-153957581b49';

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

    public function testRoomCalculatesWinningVoteCorrectly(): void
    {
        $room = $this->getRoom(false, false, false);

        $userId1 = new UserId('237e57f7-b5f8-4de2-96f8-3d49e408473d');
        $userId2 = new UserId('da508b74-ba87-4cff-99e9-153957581b49');
        $userId3 = new UserId('da508b74-ba87-4cff-99e9-153957581b41');
        $userId4 = new UserId('da508b74-ba87-4cff-99e9-153957581b42');
        $userId5 = new UserId('da508b74-ba87-4cff-99e9-153957581b43');
        $userId6 = new UserId('da508b74-ba87-4cff-99e9-153957581b44');

        // Simulate 4 participants
        $room->addParticipant(new RoomParticipant($userId1, true));
        $room->addParticipant(new RoomParticipant($userId2, true));
        $room->addParticipant(new RoomParticipant($userId3, true));
        $room->addParticipant(new RoomParticipant($userId4, true));
        $room->addParticipant(new RoomParticipant($userId5, true));
        $room->addParticipant(new RoomParticipant($userId6, true));

        $room->setVotingOpen(true);

        // As no votes have been added, there should be no winning vote
        $this->assertNull($room->getWinningVote());

        // Add a single vote and ensure that vote is now the winning vote
        $room->addVote(new RoomVote($userId1, new RoomVoteOption(2)));
        $this->assertEquals(2, $room->getWinningVote()->getValue());

        // Add another vote that is a different vote, this should cause a tie, and therefore no vote should be considered winning.
        $room->addVote(new RoomVote($userId2, new RoomVoteOption(4)));
        $this->assertNull($room->getWinningVote());

        // Add another vote that is again different, this should still be a tie.
        $room->addVote(new RoomVote($userId3, new RoomVoteOption(8)));
        $this->assertNull($room->getWinningVote());

        // Add another vote for "4", this should now be the winning vote
        $room->addVote(new RoomVote($userId4, new RoomVoteOption(4)));
        $this->assertEquals(4, $room->getWinningVote()->getValue());

        // Add another vote for "8", we should be tied again so nothing should be the winner
        $room->addVote(new RoomVote($userId5, new RoomVoteOption(8)));
        $this->assertNull($room->getWinningVote());

        // Add another vote for "8", this should now be the winner
        $room->addVote(new RoomVote($userId6, new RoomVoteOption(8)));
        $this->assertEquals(8, $room->getWinningVote()->getValue());

        // Change the 6th users vote to 4, this should now be the winner
        $room->addVote(new RoomVote($userId6, new RoomVoteOption(4)));
        $this->assertEquals(4, $room->getWinningVote()->getValue());

        // Clear the votes and simulate 4 people voting 2.  This should then be the winner.
        $room->clearVotes();

        $room->addVote(new RoomVote($userId1, new RoomVoteOption(2)));
        $room->addVote(new RoomVote($userId2, new RoomVoteOption(2)));
        $room->addVote(new RoomVote($userId3, new RoomVoteOption(2)));
        $room->addVote(new RoomVote($userId4, new RoomVoteOption(2)));
        $this->assertEquals(2, $room->getWinningVote()->getValue());

        // Add two more votes for 4, 2 should still be winning
        $room->addVote(new RoomVote($userId5, new RoomVoteOption(4)));
        $room->addVote(new RoomVote($userId6, new RoomVoteOption(4)));
        $this->assertEquals(2, $room->getWinningVote()->getValue());
    }

    public function testRoomInstantiatesWithoutVotesAndParticipantsAndValuesAreCorrect(): void
    {
        $roomVoteOptionList = new RoomVoteOptionList();

        foreach(self::ROOM_VOTE_OPTIONS as $voteOption) {
            $roomVoteOptionList->add(new RoomVoteOption($voteOption));
        }

        $roomId = new RoomId('', true);
        $roomName = new RoomName(self::ROOM_NAME);
        $createdByUserId = new UserId('', true);

        $createdAt = new CreatedAt(self::CREATED_AT);
        $updatedAt = new UpdatedAt(self::UPDATED_AT);

        $room = new Room($roomId, $roomName, $createdByUserId, $roomVoteOptionList, null, null, self::VOTING_OPEN, $createdAt, $updatedAt);

        $this->assertEquals((string)$roomId, (string)$room->getId());
        $this->assertEquals(self::ROOM_NAME, $room->getName()->getValue());
        $this->assertEquals((string)$createdByUserId, (string)$room->getCreatedByUserId());
        $this->assertEquals(self::CREATED_AT, $room->getCreatedAt()->getTimestamp());
        $this->assertEquals(self::UPDATED_AT, $room->getUpdatedAt()->getTimestamp());
        $this->assertEquals(self::ROOM_VOTE_OPTIONS, $room->getRoomVoteOptions()->toArray());
        $this->assertEquals(self::VOTING_OPEN, $room->isVotingOpen());

        $expectedArray = [
            'id' => $roomId->getUuid()->toString(),
            'name' => $roomName->toString(),
            'created_by_user_id' => $createdByUserId->getUuid()->toString(),
            'vote_options' => self::ROOM_VOTE_OPTIONS,
            'votes' => [],
            'participants' => [],
            'voting_open' => self::VOTING_OPEN,
            'created_at' => $createdAt->getTimestamp(),
            'updated_at' => $updatedAt->getTimestamp()
        ];

        $this->assertEquals($expectedArray, $room->toArray());
    }

    public function testRoomInstantiatesWithoutTimestamps(): void
    {
        $roomVoteOptionList = new RoomVoteOptionList();

        foreach(self::ROOM_VOTE_OPTIONS as $voteOption) {
            $roomVoteOptionList->add(new RoomVoteOption($voteOption));
        }

        $roomId = new RoomId('', true);
        $roomName = new RoomName(self::ROOM_NAME);
        $createdByUserId = new UserId('', true);

        $room = new Room($roomId, $roomName, $createdByUserId, $roomVoteOptionList);

        $this->assertEquals((string)$roomId, (string)$room->getId());
        $this->assertEquals(self::ROOM_NAME, $room->getName()->getValue());
        $this->assertEquals((string)$createdByUserId, (string)$room->getCreatedByUserId());
        $this->assertGreaterThan(time() - 10, $room->getCreatedAt()->getTimestamp());
        $this->assertGreaterThan(time() - 10, $room->getUpdatedAt()->getTimestamp());
    }

    public function testRoomInstantiatesWithVotesAndValuesAreCorrect(): void
    {
        $roomVoteOptionList = new RoomVoteOptionList();

        foreach(self::ROOM_VOTE_OPTIONS as $voteOption) {
            $roomVoteOptionList->add(new RoomVoteOption($voteOption));
        }

        $roomVoteList = new RoomVoteList();

        foreach(self::ROOM_VOTES as $voteData) {
            $roomVoteList->add(new RoomVote(new UserId($voteData['user_id']), new RoomVoteOption($voteData['vote'])));
        }

        $roomId = new RoomId('', true);
        $roomName = new RoomName(self::ROOM_NAME);
        $createdByUserId = new UserId('', true);

        $createdAt = new CreatedAt(self::CREATED_AT);
        $updatedAt = new UpdatedAt(self::UPDATED_AT);

        $room = new Room(
            $roomId,
            $roomName,
            $createdByUserId,
            $roomVoteOptionList,
            $roomVoteList,
            null,
            self::VOTING_OPEN,
            $createdAt,
            $updatedAt
        );

        $expectedArray = [
            'id' => $roomId->getUuid()->toString(),
            'name' => $roomName->toString(),
            'created_by_user_id' => $createdByUserId->getUuid()->toString(),
            'vote_options' => self::ROOM_VOTE_OPTIONS,
            'votes' => self::ROOM_VOTES,
            'participants' => [],
            'voting_open' => self::VOTING_OPEN,
            'created_at' => $createdAt->getTimestamp(),
            'updated_at' => $updatedAt->getTimestamp()
        ];

        $this->assertEquals($expectedArray, $room->toArray());
    }

    public function testRoomInstantiatesWithVotesAndParticipantsAndValuesAreCorrect(): void
    {
        $room = $this->getRoom();

        $roomVoteOptionList = new RoomVoteOptionList();

        foreach(self::ROOM_VOTE_OPTIONS as $voteOption) {
            $roomVoteOptionList->add(new RoomVoteOption($voteOption));
        }

        $roomVoteList = new RoomVoteList();

        foreach(self::ROOM_VOTES as $voteData) {
            $roomVoteList->add(new RoomVote(new UserId($voteData['user_id']), new RoomVoteOption($voteData['vote'])));
        }

        $roomParticipants = new RoomParticipantList();

        foreach(self::ROOM_PARTICIPANTS as $participantData) {
            $roomParticipants->add(new RoomParticipant(new UserId($participantData['user_id']), $participantData['is_voting']));
        }

        $roomId = new RoomId('', true);
        $roomName = new RoomName(self::ROOM_NAME);
        $createdByUserId = new UserId('', true);

        $createdAt = new CreatedAt(self::CREATED_AT);
        $updatedAt = new UpdatedAt(self::UPDATED_AT);

        $room = new Room(
            $roomId,
            $roomName,
            $createdByUserId,
            $roomVoteOptionList,
            $roomVoteList,
            $roomParticipants,
            self::VOTING_OPEN,
            $createdAt,
            $updatedAt
        );

        $expectedArray = [
            'id' => $roomId->getUuid()->toString(),
            'name' => $roomName->toString(),
            'created_by_user_id' => $createdByUserId->getUuid()->toString(),
            'vote_options' => self::ROOM_VOTE_OPTIONS,
            'votes' => self::ROOM_VOTES,
            'participants' => self::ROOM_PARTICIPANTS,
            'voting_open' => self::VOTING_OPEN,
            'created_at' => $createdAt->getTimestamp(),
            'updated_at' => $updatedAt->getTimestamp()
        ];

        $this->assertEquals($expectedArray, $room->toArray());
    }

    public function testRoomWillNotAllowVotingWhenVotingNotOpen(): void
    {
        $this->expectException(VotingNotOpenException::class);

        $room = $this->getRoom(true, true, false);

        // This user has already voted - exception should be raised.
        $room->addVote(new RoomVote(new UserId('237e57f7-b5f8-4de2-96f8-3d49e408473d'),new RoomVoteOption(5)));
    }

    public function testRoomWillAllowAVoteToChange(): void
    {
        $room = $this->getRoom();
        $votingUserId = new UserId(self::VOTING_USER_ID1);

        // Ensure the room starts with a vote of 3 for the voting user
        $foundUserVote = false;

        foreach ($room->getRoomVotes() as $thisVote) {
            if ($thisVote->getUserId()->equals($votingUserId)) {
                $foundUserVote = true;
                $this->assertEquals(3, $thisVote->getRoomVoteOption()->getValue());
            }
        }

        $this->assertTrue($foundUserVote);

        // Change the user vote to 5.
        $room->addVote(new RoomVote($votingUserId, new RoomVoteOption(5)));

        $foundUserVote = false;

        foreach ($room->getRoomVotes() as $thisVote) {
            if ($thisVote->getUserId()->equals($votingUserId)) {
                $foundUserVote = true;
                $this->assertEquals(5, $thisVote->getRoomVoteOption()->getValue());
            }
        }

        $this->assertTrue($foundUserVote);
    }

    public function testRoomWillNotAllowVotingIfUserNotParticipant(): void
    {
        $this->expectException(ParticipantNotInRoomException::class);

        $room = $this->getRoom(false, false);

        // This user is NOT a participant in the room, a ParticipantNotInRoomException should be raised.
        $room->addVote(new RoomVote(new UserId(self::VOTING_USER_ID1),new RoomVoteOption(5)));
    }

    public function testCanRemoveUserVotesFromRoom(): void
    {
        $room = $this->getRoom();

        $votingUserId1 = new UserId(self::VOTING_USER_ID1);
        $votingUserId2 = new UserId(self::VOTING_USER_ID2);

        $room->removeVote($votingUserId1);
        $this->assertCount(1, $room->getRoomVotes());

        $room->removeVote($votingUserId2);
        $this->assertCount(0, $room->getRoomVotes());
    }

    public function testCanRemoveParticipantsFromRoomAndRemovingParticipantsAlsoRemovesVotes(): void
    {
        $room = $this->getRoom();

        $votingUserId1 = new UserId(self::VOTING_USER_ID1);
        $votingUserId2 = new UserId(self::VOTING_USER_ID2);

        $room->removeParticipant($votingUserId1);
        $this->assertCount(1, $room->getRoomParticipants());
        $this->assertCount(1, $room->getRoomVotes());

        $room->removeParticipant($votingUserId2);
        $this->assertCount(0, $room->getRoomParticipants());
        $this->assertCount(0, $room->getRoomVotes());
    }

    private function getRoom(bool $addParticipants = true, bool $addVotes = true, bool $votingOpen = self::VOTING_OPEN): Room
    {
        $roomParticipants = new RoomParticipantList();
        $roomVoteOptionList = new RoomVoteOptionList();
        $roomVoteList = new RoomVoteList();

        foreach(self::ROOM_VOTE_OPTIONS as $voteOption) {
            $roomVoteOptionList->add(new RoomVoteOption($voteOption));
        }

        if ($addParticipants) {
            // Allow the users to vote by making them participants in the room
            $roomParticipants->add(new RoomParticipant(new UserId(self::VOTING_USER_ID1), true));
            $roomParticipants->add(new RoomParticipant(new UserId(self::VOTING_USER_ID2), true));

            if ($addVotes) {
                foreach (self::ROOM_VOTES as $voteData) {
                    $roomVoteList->add(new RoomVote(new UserId($voteData['user_id']),
                        new RoomVoteOption($voteData['vote'])));
                }
            }
        }

        $roomId = new RoomId('', true);
        $roomName = new RoomName(self::ROOM_NAME);
        $createdByUserId = new UserId('', true);
        $createdAt = new CreatedAt(self::CREATED_AT);
        $updatedAt = new UpdatedAt(self::UPDATED_AT);

        return new Room(
            $roomId,
            $roomName,
            $createdByUserId,
            $roomVoteOptionList,
            $roomVoteList,
            $roomParticipants,
            $votingOpen,
            $createdAt,
            $updatedAt
        );
    }
}
