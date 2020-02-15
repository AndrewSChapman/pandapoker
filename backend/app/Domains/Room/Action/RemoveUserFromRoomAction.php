<?php

namespace App\Domains\Room\Action;

use App\Domains\Room\Service\RoomParticipantManager\RoomParticipantManagerInterface;
use App\Domains\Room\Type\RoomId;
use App\Domains\Shared\Http\AbstractAction;
use App\Domains\Shared\Security\Service\TokenService\ValueObject\TokenInfo;
use App\Domains\User\Type\UserId;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RemoveUserFromRoomAction extends AbstractAction
{
    /** @var RoomParticipantManagerInterface */
    private $roomParticipantManager;

    public function __construct(Request $request, RoomParticipantManagerInterface $roomParticipantManager)
    {
        parent::__construct($request);
        $this->roomParticipantManager = $roomParticipantManager;
    }

    public function removeUser(TokenInfo $tokenInfo, RoomId $roomId, UserId $userIdToRemove): JsonResponse
    {
        $room = $this->roomParticipantManager->removeUserFromRoom(
            $tokenInfo->getUserId(),
            $roomId,
            $userIdToRemove
        );

        $this->setResponseData($room->toArray(true));

        return $this->getResponse();
    }
}
