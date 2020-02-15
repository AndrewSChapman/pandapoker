<?php

namespace App\Domains\User\Action;

use App\Domains\Shared\Http\AbstractAction;
use App\Domains\User\Repository\UserRepositoryInterface;
use App\Domains\User\Type\UserId;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetUserAction extends AbstractAction
{
    /** @var UserRepositoryInterface */
    private $userRepository;

    public function __construct(Request $request, UserRepositoryInterface $userRepository)
    {
        parent::__construct($request);
        $this->userRepository = $userRepository;
    }

    public function getUser(UserId $userId): JsonResponse
    {
        $user = $this->userRepository->getUser($userId);

        $this->setResponseData($user->toArray());

        return $this->getResponse();
    }
}
