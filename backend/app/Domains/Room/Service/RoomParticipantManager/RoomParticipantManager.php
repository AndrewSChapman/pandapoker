<?php

namespace App\Domains\Room\Service\RoomParticipantManager;

use App\Domains\Room\Entity\Room;
use App\Domains\Room\Event\RoomUpdatedEvent;
use App\Domains\Room\Repository\RoomRepository\RoomRepositoryInterface;
use App\Domains\Room\Type\RoomId;
use App\Domains\Room\ValueObject\RoomParticipant;
use App\Domains\Shared\Concurrency\Service\LockManager\LockManagerInterface;
use App\Domains\Shared\Concurrency\Service\LockManager\Type\LockKey;
use App\Domains\Shared\Concurrency\Service\LockManager\Type\LockType;
use App\Domains\Shared\Exception\PermissionException;
use App\Domains\User\Repository\UserRepositoryInterface;
use App\Domains\User\Type\UserId;
use App\Util\Logger\Logger;
use App\Util\Logger\Type\LogLevel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RoomParticipantManager implements RoomParticipantManagerInterface
{
    /** @var UserRepositoryInterface */
    private $userRepository;

    /** @var RoomRepositoryInterface */
    private $roomRepository;

    /** @var LockManagerInterface */
    private $lockManager;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        UserRepositoryInterface $userRepository,
        RoomRepositoryInterface $roomRepository,
        LockManagerInterface $lockManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->userRepository = $userRepository;
        $this->roomRepository = $roomRepository;
        $this->lockManager = $lockManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function addUserToRoom(
        UserId $loggedInUserId,
        RoomId $roomId,
        UserId $userIdToAddToRoom,
        bool $isVoting
    ): Room {
        // A user is only allowed to add themselves to a room (not another user)
        if (!$loggedInUserId->equals($userIdToAddToRoom)) {
            throw new PermissionException($loggedInUserId, "Add user id {$userIdToAddToRoom} to room: {$roomId}");
        }

        Logger::log(LogLevel::DEBUG, "Adding user id {$userIdToAddToRoom} to room {$roomId}");

        // Load all rooms
        $rooms = $this->roomRepository->getRooms();

        // For each room, make sure the user is *not* a participant.  If they are, remove them
        // as the user may only be in one room at time.
        foreach ($rooms as $thisRoom) {
            // Lock the room so we can be sure no on else is doing anything with it.
            $roomLockKey = $this->getRoomLockKey($thisRoom->getId());
            $this->lockManager->getLock($roomLockKey);

            // Reload the room now that we have a lock on it.
            $thisRoom = $this->roomRepository->getRoom($thisRoom->getId());

            if ($thisRoom->removeParticipant($userIdToAddToRoom)) {
                $this->roomRepository->saveRoom($thisRoom);
                $this->eventDispatcher->dispatch(new RoomUpdatedEvent($thisRoom, $loggedInUserId));
            }

            // Release room lock.
            $this->lockManager->releaseLock($roomLockKey);
        }

        // Load the room in question and add the user as a participant
        $room = $this->roomRepository->getRoom($roomId);

        // If the user is not the same user who created the room then they *must* be voting.
        if (!$room->isRoomCreator($userIdToAddToRoom)) {
            $isVoting = true;
        }

        $room->addParticipant(new RoomParticipant($userIdToAddToRoom, $isVoting));
        $this->roomRepository->saveRoom($room);
        $this->eventDispatcher->dispatch(new RoomUpdatedEvent($room, $loggedInUserId));

        Logger::log(LogLevel::DEBUG, "User id {$userIdToAddToRoom} successfully added to room {$roomId}");

        return $room;
    }

    public function removeUserFromRoom(
        UserId $loggedInUserId,
        RoomId $roomId,
        UserId $userIdToRemoveFromRoom
    ): Room {
        $roomLockKey = $this->getRoomLockKey($roomId);
        $this->lockManager->getLock($roomLockKey);

        // Load the room
        $room = $this->roomRepository->getRoom($roomId);

        // Ensure the user has permission to remove the user.
        // The room creator can remove anyone.
        // A normal  user may remove themselves
        if ((!$room->isRoomCreator($loggedInUserId)) && (!$loggedInUserId->equals($userIdToRemoveFromRoom))) {
            throw new PermissionException(
                $loggedInUserId,
                "You may not remove user id {$userIdToRemoveFromRoom} from room: {$roomId}"
            );
        }

        Logger::log(LogLevel::DEBUG, "Removing user id {$userIdToRemoveFromRoom} from room {$roomId}");

        if ($room->removeParticipant($userIdToRemoveFromRoom)) {
            $this->roomRepository->saveRoom($room);
            $this->eventDispatcher->dispatch(new RoomUpdatedEvent($room, $loggedInUserId));
        }

        $this->lockManager->releaseLock($roomLockKey);

        return $room;
    }

    private function getRoomLockKey(RoomId $roomId): LockKey
    {
        return new LockKey(new LockType(LockType::ROOM), $roomId);
    }
}
