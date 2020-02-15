<?php

namespace App\Domains\Room\Service\RoomOwnershipChanger;

use App\Domains\Room\Entity\Room;
use App\Domains\Room\Event\RoomUpdatedEvent;
use App\Domains\Room\Repository\RoomRepository\RoomRepositoryInterface;
use App\Domains\Room\Type\RoomId;
use App\Domains\User\Repository\UserRepositoryInterface;
use App\Domains\User\Type\UserId;
use App\Util\Logger\Logger;
use App\Util\Logger\Type\LogLevel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RoomOwnershipChanger implements RoomOwnershipChangerInterface
{
    /** @var RoomRepositoryInterface */
    public $roomRepository;

    /** @var UserRepositoryInterface */
    public $userRepository;

    /** @var EventDispatcherInterface */
    public $eventDispatcher;

    /**
     * RoomOwnershipChanger constructor.
     * @param RoomRepositoryInterface $roomRepository
     * @param UserRepositoryInterface $userRepository
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        RoomRepositoryInterface $roomRepository,
        UserRepositoryInterface $userRepository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->roomRepository = $roomRepository;
        $this->userRepository = $userRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function changeRoomOwnership(RoomId $roomId, UserId $userId): Room
    {
        Logger::log(LogLevel::DEBUG, 'RoomOwnershipChanger::changeRoomOwnership invoked');

        $room = $this->roomRepository->getRoom($roomId);

        if ($room->getCreatedByUserId()->equals($userId)) {
            Logger::log(
                LogLevel::INFO,
                'RoomOwnershipChanger::changeRoomOwnership - createdByUserId is the same - nothing to do'
            );

            return $room;
        }

        $user = $this->userRepository->getUser($userId);

        $room->setCreatedByUserId($userId);

        $this->roomRepository->saveRoom($room);

        $this->eventDispatcher->dispatch(new RoomUpdatedEvent($room, $user->getId()));

        Logger::log(LogLevel::DEBUG, 'RoomOwnershipChanger::changeRoomOwnership owner changed successfully');

        return $room;
    }
}
