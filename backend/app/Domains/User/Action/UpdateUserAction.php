<?php

namespace App\Domains\User\Action;

use App\Domains\Shared\Http\AbstractAction;
use App\Domains\Shared\Security\Service\TokenService\ValueObject\TokenInfo;
use App\Domains\User\Service\UserUpdater\UserUpdaterInterface;
use App\Domains\User\Type\AnimalType;
use App\Domains\User\Type\UserId;
use App\Domains\User\Type\Username;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateUserAction extends AbstractAction
{
    /** @var UserUpdaterInterface */
    private $userUpdater;

    public function __construct(Request $request, UserUpdaterInterface $userUpdater)
    {
        parent::__construct($request);
        $this->userUpdater = $userUpdater;
    }

    public function updateUser(TokenInfo $tokenInfo, UserId $userId): JsonResponse
    {
        $this->validate($this->getRequest(), [
            'username' => 'required',
            'totem_animal' => 'required',
        ]);

        $newUsername = new Username($this->getRequest()->post('username'));
        $totemAnimal = new AnimalType($this->getRequest()->post('totem_animal'));

        $user = $this->userUpdater->updateUser(
            $tokenInfo->getUserId(),
            $userId,
            $newUsername,
            $totemAnimal
        );

        $this->setResponseData($user->toArray());

        return $this->getResponse();
    }
}
