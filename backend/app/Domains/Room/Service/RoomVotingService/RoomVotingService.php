<?php

namespace App\Domains\Room\Service\RoomVotingService;

use App\Domains\Room\Entity\Room;
use App\Domains\Room\Event\RoomUpdatedEvent;
use App\Domains\Room\Event\RoomVoteEvent;
use App\Domains\Room\Repository\RoomRepository\RoomRepositoryInterface;
use App\Domains\Room\Type\RoomId;
use App\Domains\Room\Type\RoomVoteOption;
use App\Domains\Room\ValueObject\RoomVote;
use App\Domains\Shared\Concurrency\Service\LockManager\LockManagerInterface;
use App\Domains\Shared\Exception\PermissionException;
use App\Domains\User\Repository\UserRepositoryInterface;
use App\Domains\User\Type\UserId;
use App\Util\Logger\Logger;
use App\Util\Logger\Type\LogLevel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class RoomVotingService implements RoomVotingServiceInterface
{
    /** @var RoomRepositoryInterface */
    private $roomRepository;

    /** @var UserRepositoryInterface */
    private $userRepository;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var LockManagerInterface */
    private $lockManager;

    public function __construct(
        RoomRepositoryInterface $roomRepository,
        UserRepositoryInterface $userRepository,
        EventDispatcherInterface $eventDispatcher,
        LockManagerInterface $lockManager
    ) {
        $this->roomRepository = $roomRepository;
        $this->userRepository = $userRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->lockManager = $lockManager;
    }

    public function openVoting(RoomId $roomId, UserId $requestingUserId): Room
    {
        $room = $this->roomRepository->getRoom($roomId);

        // Only the user who created the room can open voting
        if (!$room->getCreatedByUserId()->equals($requestingUserId)) {
            throw new PermissionException($requestingUserId, "Open voting on room {$roomId}");
        }

        // If voting is already open - do nothing.
        if ($room->isVotingOpen()) {
            return $room;
        }

        Logger::log(LogLevel::DEBUG, "Opening voting for room {$roomId}");

        $this->lockManager->getLock($room->getLockKey());

        // Open voting, save the room and dispatch and event to say the room has been updated.
        $room->setVotingOpen(true);
        $this->saveRoomAndDispatchEvents($room, $requestingUserId);

        $this->lockManager->releaseLock($room->getLockKey());

        Logger::log(LogLevel::DEBUG, "Voting now open for room {$roomId}");

        return $room;
    }

    public function closeVoting(RoomId $roomId, UserId $requestingUserId): Room
    {
        $room = $this->roomRepository->getRoom($roomId);

        // Only the user who created the room can open voting
        if (!$room->getCreatedByUserId()->equals($requestingUserId)) {
            throw new PermissionException($requestingUserId, "Close voting on room {$roomId}");
        }

        // If voting is already closed - there's nothing to do.
        if (!$room->isVotingOpen()) {
            return $room;
        }

        Logger::log(LogLevel::DEBUG, "Closing voting for room {$roomId}");

        $this->lockManager->getLock($room->getLockKey());

        // Open voting, save the room and dispatch and event to say the room has been updated.
        $room->setVotingOpen(false);
        $this->saveRoomAndDispatchEvents($room, $requestingUserId);

        $this->lockManager->releaseLock($room->getLockKey());

        Logger::log(LogLevel::DEBUG, "Voting now closed for room {$roomId}");

        return $room;
    }

    public function addVote(RoomId $roomId, UserId $requestingUserId, RoomVoteOption $voteOption): Room
    {
        $room = $this->roomRepository->getRoom($roomId);

        $this->lockManager->getLock($room->getLockKey());

        try {
            $room->addVote(new RoomVote($requestingUserId, $voteOption));
            $this->saveRoomAndDispatchEvents($room, $requestingUserId, $voteOption);
            $this->lockManager->releaseLock($room->getLockKey());

            Logger::log(
                LogLevel::DEBUG,
                "Vote of {$voteOption->getValue()} added to room {$roomId} from userId {$requestingUserId}"
            );
        } catch (\Exception $exception) {
            $this->lockManager->releaseLock($room->getLockKey());
            throw $exception;
        }

        return $room;
    }

    public function resetVotes(RoomId $roomId, UserId $requestingUserId): Room
    {
        $room = $this->roomRepository->getRoom($roomId);

        // Only the user who created the room can clear the votes
        if (!$room->getCreatedByUserId()->equals($requestingUserId)) {
            throw new PermissionException($requestingUserId, "Close voting on room {$roomId}");
        }

        Logger::log(LogLevel::DEBUG, "Room votes being reset by userId {$requestingUserId}");

        $this->lockManager->getLock($room->getLockKey());

        $room->clearVotes();

        $this->saveRoomAndDispatchEvents($room, $requestingUserId);
        $this->lockManager->releaseLock($room->getLockKey());

        Logger::log(LogLevel::DEBUG, "Room votes have been reset by userId {$requestingUserId}");

        return $room;
    }

    private function saveRoomAndDispatchEvents(Room $room, UserId $userId, RoomVoteOption $voteOption = null): void
    {
        $this->roomRepository->saveRoom($room);
        $this->eventDispatcher->dispatch(new RoomUpdatedEvent($room, $userId));

        if ($voteOption) {
            $this->eventDispatcher->dispatch(new RoomVoteEvent($room->getId(), $userId, $voteOption));
        }
    }
}
