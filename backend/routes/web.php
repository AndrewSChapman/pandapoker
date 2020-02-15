<?php

/***********************************
 * GENERAL IMPORTS
 **********************************/
use App\Domains\Room\Type\RoomId;
use Laravel\Lumen\Routing\Router;
use App\Domains\Shared\Security\SecuritySingleton;
use App\Domains\User\Type\UserId;

/***********************************
 * USER ACTION IMPORTS
 **********************************/
use App\Domains\User\Action\AssumeUserAction;
use App\Domains\User\Action\CreateUserAction;
use App\Domains\User\Action\GetUserAction;
use App\Domains\User\Action\GetUserListAction;
use App\Domains\User\Action\UpdateUserAction;

/***********************************
 * ROOM ACTION IMPORTS
 **********************************/
use App\Domains\Room\Action\ChangeRoomOwnerAction;
use App\Domains\Room\Action\CloseVotingAction;
use App\Domains\Room\Action\CreateRoomAction;
use App\Domains\Room\Action\DeleteRoomAction;
use App\Domains\Room\Action\EnterRoomAction;
use App\Domains\Room\Action\GetRoomListAction;
use App\Domains\Room\Action\OpenVotingAction;
use App\Domains\Room\Action\ResetRoomVotesAction;
use App\Domains\Room\Action\RemoveUserFromRoomAction;
use App\Domains\Room\Action\VoteAction;

/***********************************
 * CHANGELOG ACTION IMPORTS
 **********************************/
use App\Domains\ChangeLog\Action\GetChangeLogListAction;
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
/** @var Router $router */

/************************************************************
 * USER ACTIONS
 ***********************************************************/
$router->post('/user', function () use ($router) {
    /** @var CreateUserAction $action */
    $action = $router->app->make(CreateUserAction::class);
    return $action->createUser();
});

$router->post('/user/assume', function () use ($router) {
    /** @var AssumeUserAction $action */
    $action = $router->app->make(AssumeUserAction::class);
    return $action->assumeUser();
});

$router->get('/users', function () use ($router) {
    /** @var GetUserListAction $action */
    $action = $router->app->make(GetUserListAction::class);
    return $action->listUsers();
});

$router->get('/user/{id}', ['middleware' => 'tokenAuth', function (string $id) use ($router) {
    /** @var GetUserAction $action */
    $action = $router->app->make(GetUserAction::class);
    return $action->getUser(new UserId($id));
}]);

$router->patch('/user/{userId}', ['middleware' => 'tokenAuth', function (string $userId) use ($router) {
    /** @var UpdateUserAction $action */
    $action = $router->app->make(UpdateUserAction::class);
    return $action->updateUser(SecuritySingleton::getTokenInfo(), new UserId($userId));
}]);

/************************************************************
 * ROOM ACTIONS
 ***********************************************************/
$router->get('/rooms', function () use ($router) {
    /** @var GetRoomListAction $action */
    $action = $router->app->make(GetRoomListAction::class);
    return $action->listRooms();
});

$router->post('/room', ['middleware' => 'tokenAuth', function () use ($router) {
    /** @var CreateRoomAction $action */
    $action = $router->app->make(CreateRoomAction::class);
    return $action->createRoom(SecuritySingleton::getTokenInfo());
}]);

$router->post('/room/{roomId}/enter', ['middleware' => 'tokenAuth', function (string $roomId) use ($router) {
    /** @var EnterRoomAction $action */
    $action = $router->app->make(EnterRoomAction::class);
    return $action->enterRoom(SecuritySingleton::getTokenInfo(), new RoomId($roomId));
}]);

$router->delete('/room/{roomId}/user/{userId}', ['middleware' => 'tokenAuth', function (string $roomId, string $userId) use ($router) {
    /** @var RemoveUserFromRoomAction $action */
    $action = $router->app->make(RemoveUserFromRoomAction::class);
    return $action->removeUser(SecuritySingleton::getTokenInfo(), new RoomId($roomId), new UserId($userId));
}]);

$router->patch('/room/{roomId}/voting/open', ['middleware' => 'tokenAuth', function (string $roomId) use ($router) {
    /** @var OpenVotingAction $action */
    $action = $router->app->make(OpenVotingAction::class);
    return $action->openVoting(SecuritySingleton::getTokenInfo(), new RoomId($roomId));
}]);

$router->patch('/room/{roomId}/voting/close', ['middleware' => 'tokenAuth', function (string $roomId) use ($router) {
    /** @var CloseVotingAction $action */
    $action = $router->app->make(CloseVotingAction::class);
    return $action->closeVoting(SecuritySingleton::getTokenInfo(), new RoomId($roomId));
}]);

$router->patch('/room/{roomId}/voting/reset', ['middleware' => 'tokenAuth', function (string $roomId) use ($router) {
    /** @var ResetRoomVotesAction $action */
    $action = $router->app->make(ResetRoomVotesAction::class);
    return $action->resetVotes(SecuritySingleton::getTokenInfo(), new RoomId($roomId));
}]);

$router->patch('/room/{roomId}/vote', ['middleware' => 'tokenAuth', function (string $roomId) use ($router) {
    /** @var VoteAction $action */
    $action = $router->app->make(VoteAction::class);
    return $action->vote(SecuritySingleton::getTokenInfo(), new RoomId($roomId));
}]);

$router->patch('/room/{roomId}/change_ownership', ['middleware' => 'tokenAuth', function (string $roomId) use ($router) {
    /** @var ChangeRoomOwnerAction $action */
    $action = $router->app->make(ChangeRoomOwnerAction::class);
    return $action->changeRoomOwnership(SecuritySingleton::getTokenInfo(), new RoomId($roomId));
}]);

$router->delete('/room/{roomId}', ['middleware' => 'tokenAuth', function (string $roomId) use ($router) {
    /** @var DeleteRoomAction $action */
    $action = $router->app->make(DeleteRoomAction::class);
    return $action->deleteRoom(SecuritySingleton::getTokenInfo(), new RoomId($roomId));
}]);

/************************************************************
 * CHANGELOG ACTIONS
 ***********************************************************/
$router->get('/change_log', ['middleware' => 'tokenAuth', function () use ($router) {
    /** @var GetChangeLogListAction $action */
    $action = $router->app->make(GetChangeLogListAction::class);
    return $action->listChangeLog(SecuritySingleton::getTokenInfo());
}]);
