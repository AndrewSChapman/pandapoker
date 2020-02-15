<?php

namespace App\Domains\Room\Service\RoomDeleter;

use App\Domains\Room\Event\RoomDeletedEvent;
use App\Domains\Room\Exception\RoomCannotBeDeletedException;
use App\Domains\Room\Repository\RoomRepository\RoomRepositoryInterface;
use App\Domains\Room\Type\RoomId;
use App\Domains\Shared\Exception\PermissionException;
use App\Domains\User\Repository\UserRepositoryInterface;
use App\Domains\User\Type\UserId;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RoomDeleter implements RoomDeleterInterface
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

    public function deleteRoom(UserId $loggedInUserId, RoomId $roomId): void
    {
        $user = $this->userRepository->getUser($loggedInUserId);

        $room = $this->roomRepository->getRoom($roomId);
        if (!$room->getCreatedByUserId()->equals($loggedInUserId)) {
            throw new PermissionException($loggedInUserId, "Delete room: {$roomId}");
        }

        if ($room->isVotingOpen()) {
            throw new RoomCannotBeDeletedException(
                $roomId,
                $loggedInUserId,
                'A room cannot be deleted whilst voting is open'
            );
        }

        $this->roomRepository->deleteRoom($room);

        $this->eventDispatcher->dispatch(new RoomDeletedEvent($room, $loggedInUserId));
    }
}
