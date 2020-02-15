<?php

namespace App\Domains\Room\Action;

use App\Domains\Room\Service\RoomOwnershipChanger\RoomOwnershipChangerInterface;
use App\Domains\Room\Type\RoomId;
use App\Domains\Shared\Http\AbstractAction;
use App\Domains\Shared\Security\Service\TokenService\ValueObject\TokenInfo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChangeRoomOwnerAction extends AbstractAction
{
    /** @var RoomOwnershipChangerInterface */
    private $roomOwnershipChanger;

    public function __construct(Request $request, RoomOwnershipChangerInterface $roomOwnershipChanger)
    {
        parent::__construct($request);
        $this->roomOwnershipChanger = $roomOwnershipChanger;
    }

    public function changeRoomOwnership(TokenInfo $tokenInfo, RoomId $roomId): JsonResponse
    {
        $room = $this->roomOwnershipChanger->changeRoomOwnership($roomId, $tokenInfo->getUserId());

        $this->setResponseData($room->toArray());

        return $this->getResponse();
    }
}
