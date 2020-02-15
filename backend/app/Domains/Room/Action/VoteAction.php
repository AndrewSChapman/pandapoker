<?php

namespace App\Domains\Room\Action;

use App\Domains\Room\Service\RoomVotingService\RoomVotingServiceInterface;
use App\Domains\Room\Type\RoomId;
use App\Domains\Room\Type\RoomVoteOption;
use App\Domains\Shared\Http\AbstractAction;
use App\Domains\Shared\Security\Service\TokenService\ValueObject\TokenInfo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VoteAction extends AbstractAction
{
    /** @var RoomVotingServiceInterface */
    private $roomVotingService;

    public function __construct(Request $request, RoomVotingServiceInterface $roomVotingService)
    {
        parent::__construct($request);
        $this->roomVotingService = $roomVotingService;
    }

    public function vote(TokenInfo $tokenInfo, RoomId $roomId): JsonResponse
    {
        $roomVoteOption = new RoomVoteOption(intval($this->getRequest()->post('vote')));

        $room = $this->roomVotingService->addVote(
            $roomId,
            $tokenInfo->getUserId(),
            $roomVoteOption
        );

        $this->setResponseData($room->toArray(true));

        return $this->getResponse();
    }
}
