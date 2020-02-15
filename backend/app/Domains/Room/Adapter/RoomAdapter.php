<?php

namespace App\Domains\Room\Adapter;

use App\Domains\Room\Collection\RoomParticipantList;
use App\Domains\Room\Collection\RoomVoteList;
use App\Domains\Room\Collection\RoomVoteOptionList;
use App\Domains\Room\Entity\Room;
use App\Domains\Room\Type\RoomId;
use App\Domains\Room\Type\RoomName;
use App\Domains\Shared\Exception\AdapterException;
use App\Domains\Shared\Type\CreatedAt;
use App\Domains\Shared\Type\UpdatedAt;
use App\Domains\User\Type\UserId;

class RoomAdapter
{
    public static function getRoomFromArray(array $roomData): Room
    {
        try {
            $createdAt = $roomData['created_at'] ?? null;
            $updatedAt = $roomData['updated_at'] ?? null;

            if (!is_numeric($createdAt)) {
                throw new AdapterException(RoomAdapter::class, 'created_at attribute invalid or missing');
            }

            if (!is_numeric($updatedAt)) {
                throw new AdapterException(RoomAdapter::class, 'updated_at attribute invalid or missing');
            }

            $id = new RoomId($roomData['id'] ?? '');
            $name = new RoomName($roomData['name'] ?? '');
            $createdByUserId = new UserId($roomData['created_by_user_id'] ?? '');
            $roomVoteOptions = RoomVoteOptionList::fromArray($roomData['vote_options'] ?? []);
            $roomVotes = RoomVoteList::fromArray($roomData['votes'] ?? []);
            $roomParticipants = RoomParticipantList::fromArray($roomData['participants'] ?? []);
            $votingOpen = filter_var($roomData['voting_open'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $createdAt = new CreatedAt($createdAt);
            $updatedAt = new UpdatedAt($updatedAt);

            return new Room(
                $id,
                $name,
                $createdByUserId,
                $roomVoteOptions,
                $roomVotes,
                $roomParticipants,
                $votingOpen,
                $createdAt,
                $updatedAt
            );
        } catch (\Exception $exception) {
            throw new AdapterException(RoomAdapter::class, $exception->getMessage());
        } catch (\TypeError $typeError) {
            throw new AdapterException(RoomAdapter::class, $typeError->getMessage());
        }
    }
}
