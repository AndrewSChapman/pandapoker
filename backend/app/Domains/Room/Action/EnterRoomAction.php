<?php

namespace App\Domains\Room\Action;

use App\Domains\Room\Service\RoomParticipantManager\RoomParticipantManagerInterface;
use App\Domains\Room\Type\RoomId;
use App\Domains\Shared\Http\AbstractAction;
use App\Domains\Shared\Security\Service\TokenService\ValueObject\TokenInfo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnterRoomAction extends AbstractAction
{
    /** @var RoomParticipantManagerInterface */
    private $roomParticipantManager;

    public function __construct(Request $request, RoomParticipantManagerInterface $roomParticipantManager)
    {
        parent::__construct($request);
        $this->roomParticipantManager = $roomParticipantManager;
    }

    public function enterRoom(TokenInfo $tokenInfo, RoomId $roomId): JsonResponse
    {
        $room = $this->roomParticipantManager->addUserToRoom(
            $tokenInfo->getUserId(),
            $roomId,
            $tokenInfo->getUserId(),
            true
        );

        $this->setResponseData($room->toArray(true));

        return $this->getResponse();
    }
}
