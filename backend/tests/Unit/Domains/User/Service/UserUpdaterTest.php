<?php

namespace Testing\Unit\Domains\User\Service;

use App\Domains\Shared\Exception\PermissionException;
use App\Domains\User\Event\UserUpdatedEvent;
use App\Domains\User\Exception\UserAlreadyExistsException;
use App\Domains\User\Service\UserUpdater\UserUpdater;
use App\Domains\User\Type\AnimalType;
use App\Domains\User\Type\UserId;
use App\Domains\User\Type\Username;
use Testing\Unit\UnitTest;

/**
 * @group UserUpdater
 */
class UserUpdaterTest extends UnitTest
{
    private const USERNAME = 'Testy McTest';
    private const TOTEM_ANIMAL = 'dog';

    public function testUserUpdaterThrowsExceptionIfLoggedInUserNotSameAsUserBeingUpdated(): void
    {
        $this->expectException(PermissionException::class);

        $loggedInUserId = new UserId('', true);
        $user = $this->getDataHelper()->user()->makeUser();
        $userRepository = $this->getDataHelper()->user()->makeUserRepository();
        $eventDispatcher = $this->getDataHelper()->getEventDispatcher();

        $newUsername = new Username(self::USERNAME);
        $userUpdater = new UserUpdater($userRepository, $eventDispatcher);

        $totemAnimal = new AnimalType(self::TOTEM_ANIMAL);
        $userUpdater->updateUser($loggedInUserId, $user->getId(), $newUsername, $totemAnimal);
    }

    public function testUserUpdaterDoesNotCallSaveMethodsIfUsernameAndTotemAnimalNotChanged(): void
    {
        $user = $this->getDataHelper()->user()->makeUser();
        $userRepository = $this->getDataHelper()->user()->makeUserRepository();
        $eventDispatcher = $this->getDataHelper()->getEventDispatcher();

        $userRepository->expects($this->once())->method('getUser')->willReturnCallback(
            function(UserId $userId) use ($user) {
            $this->assertTrue($userId->equals($user->getId()));

            return $user;
        });

        $userRepository->expects($this->never())->method('saveUser');
        $eventDispatcher->expects($this->never())->method('dispatch');

        $userUpdater = new UserUpdater($userRepository, $eventDispatcher);

        // Username AND totem animal has NOT changed
        $userUpdater->updateUser($user->getId(), $user->getId(), $user->getUsername(), $user->getTotemAnimal());
    }

    public function testUserUpdaterThrowsExceptionIfNewUsernameAlreadyExists(): void
    {
        $this->expectException(UserAlreadyExistsException::class);

        $user = $this->getDataHelper()->user()->makeUser();

        $userRepository = $this->getDataHelper()->user()->makeUserRepository();
        $eventDispatcher = $this->getDataHelper()->getEventDispatcher();

        $userRepository->expects($this->once())->method('getUser')->willReturnCallback(
            function(UserId $userId) use ($user) {
                $this->assertTrue($userId->equals($user->getId()));

                return $user;
            });

        $userRepository->expects($this->never())->method('saveUser');

        $newUsername = new Username(self::USERNAME);
        $anotherUser = $this->getDataHelper()->user()->makeUser(
            new UserId('', true),
            $newUsername
        );

        $userRepository->expects($this->once())->method('getUserByUsername')->willReturnCallback(
            function(Username $username) use ($newUsername, $anotherUser) {
                $this->assertTrue($newUsername->equals($username));

                return $anotherUser;
            }
        );

        $userUpdater = new UserUpdater($userRepository, $eventDispatcher);

        // Username has changed
        $userUpdater->updateUser($user->getId(), $user->getId(), $newUsername, $user->getTotemAnimal());
    }

    public function testUserUpdaterCallSaveMethodIfUsernameHasChanged(): void
    {
        $user = $this->getDataHelper()->user()->makeUser();
        $userRepository = $this->getDataHelper()->user()->makeUserRepository();
        $eventDispatcher = $this->getDataHelper()->getEventDispatcher();

        $userRepository->expects($this->once())->method('getUser')->willReturnCallback(
            function(UserId $userId) use ($user) {
                $this->assertTrue($userId->equals($user->getId()));

                return $user;
            });

        $newUsername = new Username(self::USERNAME);

        $userRepository->expects($this->once())->method('getUserByUsername')->willReturnCallback(
            function(Username $username) use ($newUsername, $user) {
                $this->assertTrue($newUsername->equals($username));

                return null;
            }
        );

        $userRepository->expects($this->once())->method('saveUser');
        $eventDispatcher->expects($this->once())->method('dispatch')->willReturnCallback(
            function(UserUpdatedEvent $event) use ($user, $newUsername) {
                $eventData = $event->toArray();
                $this->assertTrue($user->getId()->equals($event->getEventCreatedBy()));
                $this->assertEquals($newUsername->toString(), $eventData['user']['username']);
            }
        );

        $userUpdater = new UserUpdater($userRepository, $eventDispatcher);

        // Username has changed
        $userUpdater->updateUser($user->getId(), $user->getId(), $newUsername, $user->getTotemAnimal());
    }

    public function testUserUpdaterCallSaveMethodIfAnimalTypeHasChanged(): void
    {
        $user = $this->getDataHelper()->user()->makeUser(
            new UserId('', true),
            new Username(self::USERNAME),
            new AnimalType(self::TOTEM_ANIMAL)
        );

        $newTotemAnimal = new AnimalType(AnimalType::OWL);

        $userRepository = $this->getDataHelper()->user()->makeUserRepository();
        $eventDispatcher = $this->getDataHelper()->getEventDispatcher();

        $userRepository->expects($this->once())->method('getUser')->willReturnCallback(
            function(UserId $userId) use ($user) {
                $this->assertTrue($userId->equals($user->getId()));

                return $user;
            });

        $userRepository->expects($this->once())->method('getUserByUsername')->willReturnCallback(
            function(Username $username) use ($user) {
                $this->assertTrue($user->getUsername()->equals($username));

                return null;
            }
        );

        $userRepository->expects($this->once())->method('saveUser');
        $eventDispatcher->expects($this->once())->method('dispatch')->willReturnCallback(
            function(UserUpdatedEvent $event) use ($user, $newTotemAnimal) {
                $eventData = $event->toArray();
                $this->assertTrue($user->getId()->equals($event->getEventCreatedBy()));
                $this->assertEquals((string)$newTotemAnimal, $eventData['user']['totem_animal']);
            }
        );

        $userUpdater = new UserUpdater($userRepository, $eventDispatcher);

        // Username has changed
        $userUpdater->updateUser($user->getId(), $user->getId(), $user->getUsername(), $newTotemAnimal);
    }
}
