<?php

namespace Testing\DataHelper\Modules;

use App\Domains\Room\Collection\RoomParticipantList;
use App\Domains\Room\Collection\RoomVoteOptionList;
use App\Domains\Room\Entity\Room;
use App\Domains\Room\Repository\RoomRepository\RoomRepositoryInterface;
use App\Domains\Room\Service\RoomCreator\RoomCreatorInterface;
use App\Domains\Room\Type\RoomId;
use App\Domains\Room\Type\RoomName;
use App\Domains\Room\Type\RoomVoteOption;
use App\Domains\Room\ValueObject\RoomParticipant;
use App\Domains\User\Entity\User;
use Faker\Provider\Person;
use PHPUnit\Framework\MockObject\MockObject;

class RoomDataHelper extends AbstractDataHelperModule
{
    /**
     * @return RoomRepositoryInterface|MockObject
     */
    public function makeRoomRepository(): RoomRepositoryInterface
    {
        return $this->getTestCase()->getMockBuilder(RoomRepositoryInterface::class)
            ->getMock();
    }

    /**
     * @return RoomCreatorInterface|MockObject
     */
    public function makeRoomCreator(): RoomCreatorInterface
    {
        return $this->getTestCase()->getMockBuilder(RoomCreatorInterface::class)
            ->getMock();
    }

    public function makeRoom(User $user, array $roomVoteOptions = [1, 2, 4, 8], bool $votingOpen = false): Room
    {
        $roomVoteOptionList = new RoomVoteOptionList();
        foreach ($roomVoteOptions as $option) {
            $roomVoteOptionList->add(new RoomVoteOption($option));
        }

        $roomParticipantList = new RoomParticipantList();
        $roomParticipantList->add(new RoomParticipant($user->getId(), true));

        return new Room(
            new RoomId('', true),
            new RoomName(Person::firstNameFemale()),
            $user->getId(),
            $roomVoteOptionList,
            null,
            $roomParticipantList,
            $votingOpen
        );
    }
}
