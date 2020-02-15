<?php

namespace Testing\DataHelper\Modules;

use App\Domains\User\Entity\User;
use App\Domains\User\Repository\UserRepositoryInterface;
use App\Domains\User\Type\AnimalType;
use App\Domains\User\Type\UserId;
use App\Domains\User\Type\Username;
use Faker\Provider\Person;
use PHPUnit\Framework\MockObject\MockObject;

class UserDataHelper extends AbstractDataHelperModule
{
    /**
     * @return UserRepositoryInterface|MockObject
     */
    public function makeUserRepository(): UserRepositoryInterface
    {
        return $this->getTestCase()->getMockBuilder(UserRepositoryInterface::class)
            ->getMock();
    }

    public function makeUser(UserId $userId = null, Username $username = null, AnimalType $animalType = null): User
    {
        $userId = $userId ? $userId : new UserId('', true);
        $username = $username ? $username : new Username(Person::firstNameFemale());

        if ($animalType) {
            $totemAnimal = $animalType;
        } else {
            $possibleAnimalTypes = [
                AnimalType::BIRD,
                AnimalType::CAT,
                AnimalType::DOG,
                AnimalType::OWL,
                AnimalType::MONKEY,
                AnimalType::ELEPHANT
            ];

            srand();
            $selectedAnimalType = rand(0, count($possibleAnimalTypes) - 1);
            $totemAnimal = new AnimalType($possibleAnimalTypes[$selectedAnimalType]);
        }

        return new User($userId, $username, $totemAnimal);
    }
}
