<?php

namespace App\Domains\User\Action;

use App\Domains\Shared\Http\AbstractAction;
use App\Domains\User\Service\UserAssumer\UserAssumerInterface;
use App\Domains\User\Type\Username;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AssumeUserAction extends AbstractAction
{
    /** @var UserAssumerInterface */
    private $userAssumer;

    public function __construct(Request $request, UserAssumerInterface $userCreator)
    {
        parent::__construct($request);
        $this->userAssumer = $userCreator;
    }

    public function assumeUser(): JsonResponse
    {
        $username = new Username($this->getRequest()->get('username', ''));
        $tokenInfo = $this->userAssumer->assumeUserByUsername($username);

        $this->setResponseData($tokenInfo->toArray());
        return $this->getResponse();
    }
}
