<?php

namespace Testing\Unit\Domains\Room\Service;

use App\Domains\Room\Entity\Room;
use App\Domains\Room\Event\RoomUpdatedEvent;
use App\Domains\Room\Service\RoomOwnershipChanger\RoomOwnershipChanger;
use Testing\Unit\UnitTest;

class RoomOwnershipChangerTest extends UnitTest
{
    public function testRoomOwnershipChangerDoesNothingIfNewUserIdIsTheSame(): void
    {
        $roomRepo = $this->getDataHelper()->room()->makeRoomRepository();
        $userRepo = $this->getDataHelper()->user()->makeUserRepository();
        $eventDispatcher = $this->getDataHelper()->getEventDispatcher();

        $user = $this->getDataHelper()->user()->makeUser();
        $room = $this->getDataHelper()->room()->makeRoom($user);

        $roomRepo->expects($this->once())->method('getRoom')
            ->willReturn($room);

        $userRepo->expects($this->never())->method('getUser');

        $roomRepo->expects($this->never())->method('saveRoom');

        $eventDispatcher->expects($this->never())->method('dispatch');

        $roomOwnershipChanger = new RoomOwnershipChanger($roomRepo, $userRepo, $eventDispatcher);
        $roomOwnershipChanger->changeRoomOwnership($room->getId(), $room->getCreatedByUserId());
    }

    public function testRoomOwnershipChangerCallsCorrectMethodsAndDispatchesEvent(): void
    {
        $roomRepo = $this->getDataHelper()->room()->makeRoomRepository();
        $userRepo = $this->getDataHelper()->user()->makeUserRepository();
        $eventDispatcher = $this->getDataHelper()->getEventDispatcher();

        $user1 = $this->getDataHelper()->user()->makeUser();
        $user2 = $this->getDataHelper()->user()->makeUser();
        $room = $this->getDataHelper()->room()->makeRoom($user1);

        $roomRepo->expects($this->once())->method('getRoom')
            ->willReturn($room);

        $userRepo->expects($this->once())->method('getUser')->willReturn($user2);

        // Make sure the id of the room's "created_by_user_id" has been updated and saved
        $roomRepo->expects($this->once())->method('saveRoom')->willReturnCallback(
            function(Room $room) use ($user2) {
                $this->assertTrue($user2->getId()->equals($room->getCreatedByUserId()));
                return $room;
            }
        );

        // Make sure the id of the room's "created_by_user_id" has been updated to the new user in the dispatched event
        $eventDispatcher->expects($this->once())->method('dispatch')->willReturnCallback(
            function(RoomUpdatedEvent $roomUpdatedEvent) use ($user2) {
                $eventData = $roomUpdatedEvent->toArray();
                $this->assertEquals($user2->getId()->getUuid()->toString(), $eventData['room']['created_by_user_id']);
            }
        );

        $roomOwnershipChanger = new RoomOwnershipChanger($roomRepo, $userRepo, $eventDispatcher);
        $roomOwnershipChanger->changeRoomOwnership($room->getId(), $user2->getId());
    }
}
