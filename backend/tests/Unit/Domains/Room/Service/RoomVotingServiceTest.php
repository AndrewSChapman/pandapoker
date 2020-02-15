<?php

namespace Testing\Unit\Domains\Room\Service;

use App\Domains\Room\Entity\Room;
use App\Domains\Room\Event\RoomUpdatedEvent;
use App\Domains\Room\Service\RoomVotingService\RoomVotingService;
use App\Domains\Room\Type\RoomVoteOption;
use App\Domains\Room\ValueObject\RoomVote;
use App\Domains\Shared\Concurrency\Service\LockManager\Type\LockKey;
use App\Domains\Shared\Exception\PermissionException;
use App\Domains\User\Type\UserId;
use Testing\Unit\UnitTest;

class RoomVotingServiceTest extends UnitTest
{
    public function testRoomVotingServiceResetVotesThrowsPermissionExceptionIfUserNotRoomCreator(): void
    {
        $this->expectException(PermissionException::class);

        $userRepo = $this->getDataHelper()->user()->makeUserRepository();
        $roomRepo = $this->getDataHelper()->room()->makeRoomRepository();
        $eventDispatcher = $this->getDataHelper()->getEventDispatcher();
        $lockManager = $this->getDataHelper()->lockManager();

        $userId = new UserId('', true);
        $user = $this->getDataHelper()->user()->makeUser($userId);
        $room = $this->getDataHelper()->room()->makeRoom($user);

        $loggedInUserId = new UserId('', true);

        $roomVotingService = new RoomVotingService(
            $roomRepo,
            $userRepo,
            $eventDispatcher,
            $lockManager
        );

        $roomRepo->expects($this->once())->method('getRoom')->willReturn($room);

        // Calling reset votes with a different userId to the user who created the room.
        // This should raise an exception.
        $roomVotingService->resetVotes($room->getId(), $loggedInUserId);
    }

    public function testRoomVotingServiceResetVotesWillClearVotesSaveRoomAndDispatchesEvent(): void
    {
        $userRepo = $this->getDataHelper()->user()->makeUserRepository();
        $roomRepo = $this->getDataHelper()->room()->makeRoomRepository();
        $eventDispatcher = $this->getDataHelper()->getEventDispatcher();
        $lockManager = $this->getDataHelper()->lockManager();

        $userId = new UserId('', true);
        $user = $this->getDataHelper()->user()->makeUser($userId);
        $room = $this->getDataHelper()->room()->makeRoom($user);

        $roomVotingService = new RoomVotingService(
            $roomRepo,
            $userRepo,
            $eventDispatcher,
            $lockManager
        );

        $roomRepo->expects($this->once())->method('getRoom')->willReturn($room);

        $lockManager->expects($this->once())->method('getLock')->willReturnCallback(
            function(LockKey $roomLockKey) use ($room) {
                $this->assertEquals($room->getLockKey(), $roomLockKey);
                return;
            }
        );

        $roomRepo->expects($this->once())->method('saveRoom')->willReturnCallback(
            function(Room $room) {
                $this->assertEmpty($room->getRoomVotes());
            }
        );

        $eventDispatcher->expects($this->once())->method('dispatch')->willReturnCallback(
            function(RoomUpdatedEvent $roomUpdatedEvent) use ($room) {
                $eventData = $roomUpdatedEvent->toArray();
                $this->assertArrayHasKey('room', $eventData);
                $this->assertEquals((string)$room->getId(), $eventData['room']['id']);
                $this->assertArrayHasKey('votes', $eventData['room']);
                $this->assertEmpty($eventData['room']['votes']);
            }
        );

        // Open voting and add a vote so we can ensure votes are cleared correctly.
        $room->setVotingOpen(true);
        $room->addVote(new RoomVote($userId, new RoomVoteOption(2)));

        // Calling reset votes with a different userId to the user who created the room.
        // This should raise an exception.
        $room = $roomVotingService->resetVotes($room->getId(), $userId);
        $this->assertEmpty($room->getRoomVotes());
    }
}
