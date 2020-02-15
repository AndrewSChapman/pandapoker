<?php

namespace App\Domains\Room\Action;

use App\Domains\Room\Service\RoomVotingService\RoomVotingServiceInterface;
use App\Domains\Room\Type\RoomId;
use App\Domains\Shared\Http\AbstractAction;
use App\Domains\Shared\Security\Service\TokenService\ValueObject\TokenInfo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OpenVotingAction extends AbstractAction
{
    /** @var RoomVotingServiceInterface */
    private $roomVotingService;

    public function __construct(Request $request, RoomVotingServiceInterface $roomVotingService)
    {
        parent::__construct($request);
        $this->roomVotingService = $roomVotingService;
    }

    public function openVoting(TokenInfo $tokenInfo, RoomId $roomId): JsonResponse
    {
        $room = $this->roomVotingService->openVoting(
            $roomId,
            $tokenInfo->getUserId()
        );

        $this->setResponseData($room->toArray());

        return $this->getResponse();
    }
}
