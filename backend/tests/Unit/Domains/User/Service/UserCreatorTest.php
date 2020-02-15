<?php

namespace Testing\Unit\Domains\User\Service;

use App\Domains\Shared\Event\Type\EventName;
use App\Domains\Shared\Security\Service\TokenService\Type\Token;
use App\Domains\Shared\Security\Service\TokenService\Type\TokenExpiryDuration;
use App\Domains\Shared\Security\Service\TokenService\ValueObject\TokenInfo;
use App\Domains\User\Entity\User;
use App\Domains\User\Event\UserCreatedEvent;
use App\Domains\User\Exception\UserAlreadyExistsException;
use App\Domains\User\Service\UserCreator\UserCreator;
use App\Domains\User\Type\AnimalType;
use App\Domains\User\Type\Username;
use Testing\Unit\UnitTest;

/**
 * @group UserCreator
 */
class UserCreatorTest extends UnitTest
{
    private const USERNAME = 'Testy McTest';
    private const TOTEM_ANIMAL = 'dog';
    private const TOKEN_DURATION = 86400;

    public function testUserCreatorThrowsExceptionIfUserAlreadyExists(): void
    {
        $this->expectException(UserAlreadyExistsException::class);

        $user = $this->getDataHelper()->user()->makeUser();
        $totemAnimal = new AnimalType(self::TOTEM_ANIMAL);
        $userRepository = $this->getDataHelper()->user()->makeUserRepository();
        $userRepository->expects($this->once())->method('getUserByUsername')->willReturn($user);

        $tokenService = $this->getDataHelper()->security()->makeTokenService();
        $tokenExpiry = new TokenExpiryDuration(self::TOKEN_DURATION);

        $eventDispatcher = $this->getDataHelper()->getEventDispatcher();

        $userCreator = new UserCreator($userRepository, $tokenService, $tokenExpiry, $eventDispatcher);
        $userCreator->createUser(new Username(self::USERNAME), $totemAnimal);
    }

    public function testTokenCreatorCallsCorrectMethodsAndCreatesValidTokenAndDispatchesEvent(): void
    {
        $totemAnimal = new AnimalType(self::TOTEM_ANIMAL);

        $userRepository = $this->getDataHelper()->user()->makeUserRepository();
        $userRepository->expects($this->once())->method('getUserByUsername')->willReturn(null);
        $userRepository->expects($this->once())->method('saveUser')->willReturnCallback(
            function(User $user) {
                $this->assertEquals(self::USERNAME, $user->getUsername()->toString());
                $this->assertEquals(self::TOTEM_ANIMAL, (string)$user->getTotemAnimal());
            }
        );

        $tokenService = $this->getDataHelper()->security()->makeTokenService();
        $tokenService->expects($this->once())->method('createToken')->willReturnCallback(
            function(TokenInfo $tokenInfo) {
                $this->assertGreaterThan(time() + self::TOKEN_DURATION - 10, $tokenInfo->getExpires()->getTimestamp());
                return new Token('SOME_TOKEN');
            }
        );

        $tokenExpiry = new TokenExpiryDuration(self::TOKEN_DURATION);

        $eventDispatcher = $this->getDataHelper()->getEventDispatcher();
        $eventDispatcher->expects($this->once())->method('dispatch')->willReturnCallback(
            function(UserCreatedEvent $event) {
                $this->assertTrue($event->getEventName()->equals(new EventName('USER_CREATED')));
            }
        );

        $userCreator = new UserCreator($userRepository, $tokenService, $tokenExpiry, $eventDispatcher);

        $userCreator->createUser(new Username(self::USERNAME), $totemAnimal);
    }
}
