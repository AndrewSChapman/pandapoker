<?php

namespace App\Domains\User\Action;

use App\Domains\Shared\Http\AbstractAction;
use App\Domains\User\Service\UserCreator\UserCreatorInterface;
use App\Domains\User\Type\AnimalType;
use App\Domains\User\Type\Username;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class CreateUserAction extends AbstractAction
{
    /** @var UserCreatorInterface */
    private $userCreator;

    public function __construct(Request $request, UserCreatorInterface $userCreator)
    {
        parent::__construct($request);

        $this->userCreator = $userCreator;
    }

    public function createUser(): JsonResponse
    {
        $username = new Username($this->getRequest()->get('username', ''));
        $totemAnimal = new AnimalType($this->getRequest()->get('totem_animal', ''));
        $user = $this->userCreator->createUser($username, $totemAnimal);

        $this->setResponseData($user->toArray());
        return $this->getResponse();
    }
}
