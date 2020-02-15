<?php

namespace Testing\Unit\Domains\Room\Service;

use App\Domains\Room\Entity\Room;
use App\Domains\Room\Event\RoomCreatedEvent;
use App\Domains\Room\Event\RoomDeletedEvent;
use App\Domains\Room\Exception\RoomCannotBeDeletedException;
use App\Domains\Room\Service\RoomDeleter\RoomDeleter;
use App\Domains\Room\Type\RoomId;
use App\Domains\Shared\Exception\PermissionException;
use App\Domains\User\Type\UserId;
use Testing\Unit\UnitTest;

class RoomDeleterTest extends UnitTest
{
    public function testRoomDeleterWillThrowExceptionIfLoggedInUserIdNotRoomCreator(): void
    {
        $this->expectException(PermissionException::class);

        $roomRepository = $this->getDataHelper()->room()->makeRoomRepository();
        $userRepository = $this->getDataHelper()->user()->makeUserRepository();
        $eventDispatcher = $this->getDataHelper()->getEventDispatcher();

        $user = $this->getDataHelper()->user()->makeUser();
        $room = $this->getDataHelper()->room()->makeRoom($user);

        $loggedInUserId = new UserId('', true);

        $userRepository->expects($this->once())->method('getUser')->willReturnCallback(
            function(UserId $userId) use ($loggedInUserId, $user) {
                $this->assertTrue($loggedInUserId->equals($userId));
                return $user;
            }
        );

        $roomRepository->expects($this->once())->method('getRoom')->willReturnCallback(
            function(RoomId $roomId) use ($room) {
                $this->assertTrue($room->getId()->equals($roomId));
                return $room;
            }
        );

        $roomDeleter = new RoomDeleter(
            $roomRepository,
            $userRepository,
            $eventDispatcher
        );

        $roomDeleter->deleteRoom($loggedInUserId, $room->getId());
    }

    public function testRoomDeleterWillThrowExceptionIfVotingOpen(): void
    {
        $this->expectException(RoomCannotBeDeletedException::class);

        $roomRepository = $this->getDataHelper()->room()->makeRoomRepository();
        $userRepository = $this->getDataHelper()->user()->makeUserRepository();
        $eventDispatcher = $this->getDataHelper()->getEventDispatcher();

        $user = $this->getDataHelper()->user()->makeUser();
        $room = $this->getDataHelper()->room()->makeRoom($user, [1, 2, 4, 8], true);

        $userRepository->expects($this->once())->method('getUser')->willReturnCallback(
            function(UserId $userId) use ($user) {
                $this->assertTrue($user->getId()->equals($userId));
                return $user;
            }
        );

        $roomRepository->expects($this->once())->method('getRoom')->willReturnCallback(
            function(RoomId $roomId) use ($room) {
                $this->assertTrue($room->getId()->equals($roomId));
                return $room;
            }
        );

        $roomDeleter = new RoomDeleter(
            $roomRepository,
            $userRepository,
            $eventDispatcher
        );

        $roomDeleter->deleteRoom($user->getId(), $room->getId());
    }

    public function testRoomDeleterWillCallCorrectMethodsAndDispatchEvent(): void
    {
        $roomRepository = $this->getDataHelper()->room()->makeRoomRepository();
        $userRepository = $this->getDataHelper()->user()->makeUserRepository();
        $eventDispatcher = $this->getDataHelper()->getEventDispatcher();

        $user = $this->getDataHelper()->user()->makeUser();
        $room = $this->getDataHelper()->room()->makeRoom($user);

        $userRepository->expects($this->once())->method('getUser')->willReturnCallback(
            function(UserId $userId) use ($user) {
                $this->assertTrue($user->getId()->equals($userId));
                return $user;
            }
        );

        $roomRepository->expects($this->once())->method('getRoom')->willReturnCallback(
            function(RoomId $roomId) use ($room) {
                $this->assertTrue($room->getId()->equals($roomId));
                return $room;
            }
        );

        $roomRepository->expects($this->once())->method('deleteRoom')->willReturnCallback(
            function(Room $passedRoom) use ($room) {
                $this->assertTrue($room->getId()->equals($passedRoom->getId()));
            }
        );

        $eventDispatcher->expects($this->once())->method('dispatch')->willReturnCallback(
            function(RoomDeletedEvent $event) use ($room) {
                $this->assertTrue($event->getEventName()->toString() === 'ROOM_DELETED');
                $eventData = $event->toArray();
                $this->assertEquals($room->getId()->getUuid()->toString(), $eventData['room']['id']);
            }
        );

        $roomDeleter = new RoomDeleter(
            $roomRepository,
            $userRepository,
            $eventDispatcher
        );

        $roomDeleter->deleteRoom($user->getId(), $room->getId());
    }
}
