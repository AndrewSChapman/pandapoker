<?php

namespace App\Domains\Room\Action;

use App\Domains\Room\Collection\RoomVoteOptionList;
use App\Domains\Room\Service\RoomCreator\RoomCreatorInterface;
use App\Domains\Room\Type\RoomName;
use App\Domains\Room\Type\RoomVoteOption;
use App\Domains\Shared\Http\AbstractAction;
use App\Domains\Shared\Security\Service\TokenService\ValueObject\TokenInfo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreateRoomAction extends AbstractAction
{
    /** @var RoomCreatorInterface */
    private $roomCreator;

    public function __construct(Request $request, RoomCreatorInterface $roomCreator)
    {
        parent::__construct($request);
        $this->roomCreator = $roomCreator;
    }

    public function createRoom(TokenInfo $tokenInfo): JsonResponse
    {
        $this->validate($this->getRequest(), [
            'name' => 'required',
            'vote_options' => 'required|array',
            'vote_options.*' => 'integer'
        ]);

        $roomName = new RoomName($this->getRequest()->post('name'));

        $votingOptionArray = $this->getRequest()->post('vote_options');
        $votingOptionList = new RoomVoteOptionList();
        foreach ($votingOptionArray as $votingOption) {
            $votingOptionList->add(new RoomVoteOption($votingOption));
        }

        $room = $this->roomCreator->createRoom($roomName, $tokenInfo->getUserId(), $votingOptionList);

        $this->setResponseData($room->toArray());

        return $this->getResponse();
    }
}
