<?php

namespace Testing\Unit\Domains\Room\Service;

use App\Domains\Room\Collection\RoomVoteOptionList;
use App\Domains\Room\Entity\Room;
use App\Domains\Room\Event\RoomCreatedEvent;
use App\Domains\Room\Service\RoomCreator\RoomCreator;
use App\Domains\Room\Type\RoomName;
use App\Domains\Room\Type\RoomVoteOption;
use App\Domains\User\Entity\User;
use App\Domains\User\Type\UserId;
use App\Domains\User\Type\Username;
use Testing\Unit\UnitTest;

/**
 * @group RoomCreator
 */
class RoomCreatorTest extends UnitTest
{
    private const ROOM_NAME = 'TEST_ROOM';
    private const ROOM_VOTE_OPTIONS = [1, 2, 3, 5, 8, 13];

    public function testRoomCreatorWillCreateRoomAndCallRepoSave(): void
    {
        $roomVoteOptionList = new RoomVoteOptionList();

        foreach(self::ROOM_VOTE_OPTIONS as $voteOption) {
            $roomVoteOptionList->add(new RoomVoteOption($voteOption));
        }

        $roomName = new RoomName(self::ROOM_NAME);
        $createdByUserId = new UserId('', true);

        $roomRepo = $this->getDataHelper()->room()->makeRoomRepository();
        $roomRepo->expects($this->once())->method('getRoomByName')->willReturn(null);

        $roomRepo->expects($this->once())->method('saveRoom')->willReturnCallback(
            function(Room $room) use ($roomName) {
                $this->assertEquals($roomName, $room->getName());
            }
        );

        $userRepo = $this->getDataHelper()->user()->makeUserRepository();
        $userRepo->expects($this->once())->method('getUser')->willReturnCallback(
            function(UserId $userId) use ($createdByUserId) {
                $this->assertEquals($createdByUserId->getUuid(), $userId->getUuid());
                $user = $this->getDataHelper()->user()->makeUser($userId, new Username('TestyMcTesterson'));
                return $user;
            }
        );

        $eventDispatcher = $this->getDataHelper()->getEventDispatcher();
        $eventDispatcher->expects($this->once())->method('dispatch')->willReturnCallback(
            function(RoomCreatedEvent $event) {
                $this->assertTrue($event->getEventName()->toString() === 'ROOM_CREATED');
            }
        );

        $roomCreator = new RoomCreator($roomRepo, $userRepo, $eventDispatcher);
        $roomCreator->createRoom($roomName, $createdByUserId, $roomVoteOptionList);
    }
}
