<?php

namespace Testing\Unit\Domains\Room\Action;

use App\Domains\Room\Action\CreateRoomAction;
use App\Domains\Room\Collection\RoomVoteOptionList;
use App\Domains\Room\Type\RoomName;
use App\Domains\User\Type\UserId;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Testing\Unit\UnitTest;

class CreateRoomActionTest extends UnitTest
{
    private const ROOM_NAME = 'Test Room';
    private const VOTE_OPTIONS = [1, 2, 4, 8];

    /**
     * @param string $roomName
     * @param mixed $voteOptions
     * @dataProvider invalidPostDataProvider
     */
    public function testCreateRoomActionWillReturn422IfInputDataInvalid(string $roomName, $voteOptions): void
    {
        $this->expectException(ValidationException::class);

        $request = $this->getDataHelper()->http()->makeRequest();
        $roomCreator = $this->getDataHelper()->room()->makeRoomCreator();
        $user = $this->getDataHelper()->user()->makeUser();
        $tokenInfo = $this->getDataHelper()->security()->getTokenInfo($user);

        $request->expects($this->once())->method('all')->willReturn([
            'name' => $roomName,
            'vote_options' => $voteOptions
        ]);

        $action = new CreateRoomAction($request, $roomCreator);
        $action->createRoom($tokenInfo);
    }

    public function testCreateRoomActionWillCallCorrectServiceIfPostDataCorrect(): void
    {
        $roomCreator = $this->getDataHelper()->room()->makeRoomCreator();
        $user = $this->getDataHelper()->user()->makeUser();
        $tokenInfo = $this->getDataHelper()->security()->getTokenInfo($user);
        $room = $this->getDataHelper()->room()->makeRoom($user, self::VOTE_OPTIONS);

        $postData = [
            'name' => self::ROOM_NAME,
            'vote_options' => self::VOTE_OPTIONS
        ];

        $request = $this->getDataHelper()->http()->makeRequest($postData);

        $roomCreator->expects($this->once())->method('createRoom')->willReturnCallback(
            function(RoomName $roomName, UserId $userId, RoomVoteOptionList $voteOptions) use ($user, $room) {
                $this->assertTrue($user->getId()->equals($userId));
                $this->assertCount(4, $voteOptions);
                return $room;
            }
        );

        $action = new CreateRoomAction($request, $roomCreator);
        $response = $action->createRoom($tokenInfo);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function invalidPostDataProvider(): array
    {
        return [
            ['', []],
            ['My Room', []],
            ['My Room', 'Not an array'],
            ['My Room', ['Not an integer']],
        ];
    }
}
