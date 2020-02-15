<?php

namespace Testing\Functional;

use App\Domains\Room\Adapter\RoomAdapter;
use App\Domains\Room\Entity\Room;
use App\Domains\Shared\Helper\JsonHelper;
use App\Domains\Shared\Http\Type\HttpRequestType;
use App\Domains\Shared\Http\Type\HttpRequestUri;
use App\Domains\Shared\Security\Service\TokenService\ValueObject\TokenInfo;
use App\Domains\User\Adapter\UserAdapter;
use App\Domains\User\Entity\User;
use App\Domains\User\Type\AnimalType;
use App\Domains\User\Type\UserId;
use App\Domains\User\Type\Username;
use Symfony\Component\HttpFoundation\Response;

class UserJourneyTest extends FunctionalTest
{
    /**
     * SCENARIO: Create a new user
     * THEN make sure we can load that new user using the get user request
     * THEN add another user
     * THEN make sure we can load both users using the listing request
     */
    public function testCanCreateAndLoadUsers(): void
    {
        $this->clearDataStore();

        /******************************************
         * STEP 1 - CREATE NEW USER
         *****************************************/
        $jsonData = [
            'username' => 'Some username'
        ];

        // The login request will create a new user
        $tokenInfo = $this->login();

        $userId = $tokenInfo->getUserId();
        $user1 = $this->loadUser($userId);
        $user1 = $this->updateUser(
            $user1,
            new Username('Someother username'),
            new AnimalType(AnimalType::ELEPHANT)
        );

        // Test creating another user, loading them, and then assuming the user just for kicks.
        $user2TokenInfo = $this->createAnotherUser();
        $user2 = $this->loadUser($user2TokenInfo->getUserId());
        $user2TokenInfo = $this->assumeUser($user2->getUsername());

        $this->getUserListing();
        $room = $this->createRoom();
        $room = $this->enterRoom($room, $userId);
        $room = $this->openVoting($room);
        $room = $this->castVote($room, $userId);
        $room = $this->resetVotes($room, $userId);
        $room = $this->removeUserFromRoom($room, $userId);
        $room = $this->closeVoting($room);

        // Test updating room ownership to the alternative user.
        $room = $this->changeRoomOwnership($room, $user2TokenInfo->getUserId(), $user2TokenInfo);

        // Delete the room
        $this->deleteRoom($room, $user2TokenInfo);

        $this->clearDataStore();
    }

    private function loadUser(UserId $userId): User
    {
        $response = $this->makeHttpRequest(
            new HttpRequestType(HttpRequestType::GET),
            new HttpRequestUri("/user/{$userId}")
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseArray = JsonHelper::jsonStringToArray((string)$response->getBody());
        $this->assertArrayHasKey('id', $responseArray);

        return UserAdapter::getUserFromArray($responseArray);
    }

    private function updateUser(User $user, Username $newUsername, AnimalType $totemAnimal): User
    {
        $response = $this->makeJsonHttpRequest(
            new HttpRequestType(HttpRequestType::PATCH),
            new HttpRequestUri("/user/{$user->getId()}"),
            [
                'username' => (string)$newUsername,
                'totem_animal' => (string)$totemAnimal
            ]
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseArray = JsonHelper::jsonStringToArray((string)$response->getBody());
        $this->assertArrayHasKey('id', $responseArray);
        $this->assertArrayHasKey('username', $responseArray);
        $this->assertArrayHasKey('totem_animal', $responseArray);

        // The username should NOT have changed
        $this->assertEquals((string)$user->getId(), $responseArray['id']);

        // The username should have changed
        $this->assertEquals((string)$newUsername, $responseArray['username']);
        $this->assertEquals((string)$totemAnimal, $responseArray['totem_animal']);

        return UserAdapter::getUserFromArray($responseArray);
    }

    private function createAnotherUser(): TokenInfo
    {
        $jsonData = [
            'username' => 'Another username',
            'totem_animal' => AnimalType::OWL
        ];

        $response = $this->makeJsonHttpRequest(
            new HttpRequestType(HttpRequestType::POST),
            new HttpRequestUri('/user'),
            $jsonData
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $responseArray = JsonHelper::jsonStringToArray((string)$response->getBody());

        return TokenInfo::fromArray($responseArray);
    }

    private function assumeUser(Username $username): TokenInfo
    {
        $jsonData = [
            'username' => $username->toString()
        ];

        $response = $this->makeJsonHttpRequest(
            new HttpRequestType(HttpRequestType::POST),
            new HttpRequestUri('/user/assume'),
            $jsonData
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $responseArray = JsonHelper::jsonStringToArray((string)$response->getBody());

        return TokenInfo::fromArray($responseArray);
    }

    private function getUserListing(): void
    {
        $response = $this->makeHttpRequest(
            new HttpRequestType(HttpRequestType::GET),
            new HttpRequestUri("/users")
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseArray = JsonHelper::jsonStringToArray((string)$response->getBody());
        $this->assertCount(2, $responseArray);
    }

    private function createRoom(): Room
    {
        $jsonData = [
            'name' => 'Amazing Room',
            'vote_options' => [1, 2, 4, 8, 13]
        ];

        $response = $this->makeJsonHttpRequest(
            new HttpRequestType(HttpRequestType::POST),
            new HttpRequestUri('/room'),
            $jsonData
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $room = RoomAdapter::getRoomFromArray(JsonHelper::jsonStringToArray((string)$response->getBody()));
        $this->assertInstanceOf(Room::class, $room);

        return $room;
    }

    private function enterRoom(Room $room, UserId $userId): Room
    {
        $this->assertFalse($room->hasParticipant($userId));

        $jsonData = [
            'name' => 'Amazing Room',
            'vote_options' => [1, 2, 4, 8, 13]
        ];

        $response = $this->makeHttpRequest(
            new HttpRequestType(HttpRequestType::POST),
            new HttpRequestUri("/room/{$room->getId()}/enter")
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $room = RoomAdapter::getRoomFromArray(JsonHelper::jsonStringToArray((string)$response->getBody()));
        $this->assertInstanceOf(Room::class, $room);
        $this->assertTrue($room->hasParticipant($userId));

        return $room;
    }

    private function openVoting(Room $room): Room
    {
        $this->assertFalse($room->isVotingOpen());

        $response = $this->makeHttpRequest(
            new HttpRequestType(HttpRequestType::PATCH),
            new HttpRequestUri("/room/{$room->getId()}/voting/open")
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $room = RoomAdapter::getRoomFromArray(JsonHelper::jsonStringToArray((string)$response->getBody()));
        $this->assertInstanceOf(Room::class, $room);
        $this->assertTrue($room->isVotingOpen());

        return $room;
    }

    private function castVote(Room $room, UserId $userId): Room
    {
        $this->assertNull($room->getUserVote($userId));

        $jsonData = [
            'vote' => 2
        ];

        $response = $this->makeJsonHttpRequest(
            new HttpRequestType(HttpRequestType::PATCH),
            new HttpRequestUri("/room/{$room->getId()}/vote"),
            $jsonData
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $room = RoomAdapter::getRoomFromArray(JsonHelper::jsonStringToArray((string)$response->getBody()));
        $this->assertInstanceOf(Room::class, $room);

        $this->assertNotNull($room->getUserVote($userId));
        $this->assertEquals(2, $room->getUserVote($userId)->getValue());

        return $room;
    }

    private function resetVotes(Room $room, UserId $userId): Room
    {
        $this->assertNotNull($room->getUserVote($userId));

        $response = $this->makeHttpRequest(
            new HttpRequestType(HttpRequestType::PATCH),
            new HttpRequestUri("/room/{$room->getId()}/voting/reset")
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $room = RoomAdapter::getRoomFromArray(JsonHelper::jsonStringToArray((string)$response->getBody()));
        $this->assertInstanceOf(Room::class, $room);

        $this->assertEmpty($room->getRoomVotes());

        return $room;
    }

    private function removeUserFromRoom(Room $room, UserId $userId): Room
    {
        $this->assertTrue($room->hasParticipant($userId));
        $response = $this->makeHttpRequest(
            new HttpRequestType(HttpRequestType::DELETE),
            new HttpRequestUri("/room/{$room->getId()}/user/{$userId}")
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $room = RoomAdapter::getRoomFromArray(JsonHelper::jsonStringToArray((string)$response->getBody()));
        $this->assertInstanceOf(Room::class, $room);
        $this->assertFalse($room->hasParticipant($userId));

        return $room;
    }

    private function closeVoting(Room $room): Room
    {
        $this->assertTrue($room->isVotingOpen());

        $response = $this->makeHttpRequest(
            new HttpRequestType(HttpRequestType::PATCH),
            new HttpRequestUri("/room/{$room->getId()}/voting/close")
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $room = RoomAdapter::getRoomFromArray(JsonHelper::jsonStringToArray((string)$response->getBody()));
        $this->assertInstanceOf(Room::class, $room);
        $this->assertFalse($room->isVotingOpen());

        return $room;
    }

    private function changeRoomOwnership(Room $room, UserId $newUserId, TokenInfo $newUserTokenInfo): Room
    {
        $this->assertFalse($room->getCreatedByUserId()->equals($newUserId));

        $this->setForcedTokenInfo($newUserTokenInfo);

        $response = $this->makeHttpRequest(
            new HttpRequestType(HttpRequestType::PATCH),
            new HttpRequestUri("/room/{$room->getId()}/change_ownership")
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->setForcedTokenInfo(null);

        $room = RoomAdapter::getRoomFromArray(JsonHelper::jsonStringToArray((string)$response->getBody()));
        $this->assertInstanceOf(Room::class, $room);
        $this->assertTrue($room->getCreatedByUserId()->equals($newUserId));

        return $room;
    }

    private function deleteRoom(Room $room, TokenInfo $newUserTokenInfo): void
    {
        $this->setForcedTokenInfo($newUserTokenInfo);

        $response = $this->makeHttpRequest(
            new HttpRequestType(HttpRequestType::DELETE),
            new HttpRequestUri("/room/{$room->getId()}")
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}
