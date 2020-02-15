<?php

namespace App\Domains\Room\Action;

use App\Domains\Room\Entity\Room;
use App\Domains\Room\Service\RoomLister\RoomListerInterface;
use App\Domains\Shared\Http\AbstractAction;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class GetRoomListAction extends AbstractAction
{
    /** @var RoomListerInterface */
    private $roomLister;

    public function __construct(Request $request, RoomListerInterface $roomLister)
    {
        parent::__construct($request);

        $this->roomLister = $roomLister;
    }

    public function listRooms(): JsonResponse
    {
        $roomList = $this->roomLister->getRoomList();

        $results = array_map(function(Room $room) {
            return $room->toArray(true);
        }, $roomList->toArray());

        $this->setResponseData($results);
        return $this->getResponse();
    }
}
