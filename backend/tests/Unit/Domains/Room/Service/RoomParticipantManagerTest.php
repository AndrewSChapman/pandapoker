<?php

namespace Testing\Unit\Domains\Room\Service;

use App\Domains\Room\Collection\RoomList;
use App\Domains\Room\Event\RoomUpdatedEvent;
use App\Domains\Room\Service\RoomParticipantManager\RoomParticipantManager;
use App\Domains\Shared\Exception\PermissionException;
use Testing\Unit\UnitTest;

class RoomParticipantManagerTest extends UnitTest
{
    public function testManagerWillThrowExceptionWhenAddingParticipantToRoomIfUserNotSameAsLoggedInUser(): void
    {
        $this->expectException(PermissionException::class);

        $userRepository = $this->getDataHelper()->user()->makeUserRepository();
        $roomRepository = $this->getDataHelper()->room()->makeRoomRepository();
        $lockManager = $this->getDataHelper()->lockManager();
        $eventDispatcher = $this->getDataHelper()->getEventDispatcher();

        $user1 = $this->getDataHelper()->user()->makeUser();
        $user2 = $this->getDataHelper()->user()->makeUser();
        $room = $this->getDataHelper()->room()->makeRoom($user2);

        $manager = new RoomParticipantManager($userRepository, $roomRepository, $lockManager, $eventDispatcher);
        $manager->addUserToRoom($user1->getId(), $room->getId(), $user2->getId(), true);
    }

    public function testManagerWillCallCorrectMethodsWhenAddingParticipantToRoom(): void
    {
        $userRepository = $this->getDataHelper()->user()->makeUserRepository();
        $roomRepository = $this->getDataHelper()->room()->makeRoomRepository();
        $lockManager = $this->getDataHelper()->lockManager();
        $eventDispatcher = $this->getDataHelper()->getEventDispatcher();

        $user1 = $this->getDataHelper()->user()->makeUser();

        $room1 = $this->getDataHelper()->room()->makeRoom($user1);
        $room2 = $this->getDataHelper()->room()->makeRoom($user1);

        $roomList = new RoomList();
        $roomList->add($room1);
        $roomList->add($room2);

        $roomRepository->expects($this->once())->method('getRooms')->willReturn($roomList);
        $lockManager->expects($this->exactly(2))->method('getLock');
        $lockManager->expects($this->exactly(2))->method('releaseLock');
        $roomRepository->expects($this->exactly(2))->method('saveRoom');
        $roomRepository->expects($this->exactly(3))->method('getRoom')->willReturn($room1);
        $eventDispatcher->expects($this->exactly(2))->method('dispatch')->willReturnCallback(
            function($event) {
                $this->assertInstanceOf(RoomUpdatedEvent::class, $event);
            }
        );

        $manager = new RoomParticipantManager($userRepository, $roomRepository, $lockManager, $eventDispatcher);
        $manager->addUserToRoom($user1->getId(), $room1->getId(), $user1->getId(), true);
    }

    public function testManagerWillThrowExceptionWhenRemovingParticipantFromRoomIfUserNotSameAsLoggedInUser(): void
    {
        $this->expectException(PermissionException::class);

        $userRepository = $this->getDataHelper()->user()->makeUserRepository();
        $roomRepository = $this->getDataHelper()->room()->makeRoomRepository();
        $lockManager = $this->getDataHelper()->lockManager();
        $eventDispatcher = $this->getDataHelper()->getEventDispatcher();

        $user1 = $this->getDataHelper()->user()->makeUser();
        $user2 = $this->getDataHelper()->user()->makeUser();
        $room = $this->getDataHelper()->room()->makeRoom($user2);

        $manager = new RoomParticipantManager($userRepository, $roomRepository, $lockManager, $eventDispatcher);
        $manager->removeUserFromRoom($user1->getId(), $room->getId(), $user2->getId());
    }

    public function testManagerWillCallCorrectMethodsWhenRemovingParticipantFromRoom(): void
    {
        $userRepository = $this->getDataHelper()->user()->makeUserRepository();
        $roomRepository = $this->getDataHelper()->room()->makeRoomRepository();
        $lockManager = $this->getDataHelper()->lockManager();
        $eventDispatcher = $this->getDataHelper()->getEventDispatcher();

        $user1 = $this->getDataHelper()->user()->makeUser();
        $room1 = $this->getDataHelper()->room()->makeRoom($user1);

        $lockManager->expects($this->exactly(1))->method('getLock');
        $roomRepository->expects($this->exactly(1))->method('getRoom')->willReturn($room1);
        $roomRepository->expects($this->exactly(1))->method('saveRoom');
        $eventDispatcher->expects($this->exactly(1))->method('dispatch')->willReturnCallback(
            function($event) {
                $this->assertInstanceOf(RoomUpdatedEvent::class, $event);
            }
        );
        $lockManager->expects($this->exactly(1))->method('releaseLock');

        $manager = new RoomParticipantManager($userRepository, $roomRepository, $lockManager, $eventDispatcher);
        $manager->removeUserFromRoom($user1->getId(), $room1->getId(), $user1->getId());
    }
}
