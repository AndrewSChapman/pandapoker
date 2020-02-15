<?php

namespace App\Domains\User\Action;

use App\Domains\Shared\Http\AbstractAction;
use App\Domains\User\Entity\User;
use App\Domains\User\Service\UserLister\UserListerInterface;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class GetUserListAction extends AbstractAction
{
    /** @var UserListerInterface */
    private $userLister;

    public function __construct(Request $request, UserListerInterface $userLister)
    {
        parent::__construct($request);

        $this->userLister = $userLister;
    }

    public function listUsers(): JsonResponse
    {
        $userList = $this->userLister->getUserList();

        $results = array_map(function(User $user) {
            return $user->toArray();
        }, $userList->toArray());

        $this->setResponseData($results);
        return $this->getResponse();
    }
}
