<?php

namespace App\Domains\Room\Action;

use App\Domains\Room\Service\RoomDeleter\RoomDeleterInterface;
use App\Domains\Room\Type\RoomId;
use App\Domains\Shared\Http\AbstractAction;
use App\Domains\Shared\Security\Service\TokenService\ValueObject\TokenInfo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeleteRoomAction extends AbstractAction
{
    /** @var RoomDeleterInterface */
    private $roomDeleter;

    public function __construct(Request $request, RoomDeleterInterface $roomDeleter)
    {
        parent::__construct($request);
        $this->roomDeleter = $roomDeleter;
    }

    public function deleteRoom(TokenInfo $tokenInfo, RoomId $roomId): JsonResponse
    {
        $this->roomDeleter->deleteRoom(
            $tokenInfo->getUserId(),
            $roomId
        );

        return $this->getResponse();
    }
}
