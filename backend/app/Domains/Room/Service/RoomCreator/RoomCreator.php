<?php

namespace App\Domains\Room\Service\RoomCreator;

use App\Domains\Room\Collection\RoomVoteOptionList;
use App\Domains\Room\Entity\Room;
use App\Domains\Room\Event\RoomCreatedEvent;
use App\Domains\Room\Exception\RoomAlreadyExistsException;
use App\Domains\Room\Repository\RoomRepository\RoomRepositoryInterface;
use App\Domains\Room\Type\RoomId;
use App\Domains\Room\Type\RoomName;
use App\Domains\User\Repository\UserRepositoryInterface;
use App\Domains\User\Type\UserId;
use App\Util\Logger\Logger;
use App\Util\Logger\Type\LogLevel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RoomCreator implements RoomCreatorInterface
{
    /** @var RoomRepositoryInterface */
    private $roomRepository;

    /** @var UserRepositoryInterface */
    private $userRepository;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        RoomRepositoryInterface $roomRepository,
        UserRepositoryInterface $userRepository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->roomRepository = $roomRepository;
        $this->userRepository = $userRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function createRoom(RoomName $roomName, UserId $createdByUserId, RoomVoteOptionList $voteOptions): Room
    {
        // Make sure the user actually exists.
        $user = $this->userRepository->getUser($createdByUserId);

        if ($this->roomRepository->getRoomByName($roomName)) {
            throw new RoomAlreadyExistsException($roomName);
        }

        $room = new Room(
            new RoomId('', true),
            $roomName,
            $user->getId(),
            $voteOptions
        );

        Logger::log(LogLevel::DEBUG, "RoomCreator - Creating room with name: {$room->getName()}");

        $this->roomRepository->saveRoom($room);
        $this->eventDispatcher->dispatch(new RoomCreatedEvent($room, $user->getId()));

        Logger::log(LogLevel::DEBUG, "RoomCreator - room created with id: {$room->getId()->getUuid()}");

        return $room;
    }
}
