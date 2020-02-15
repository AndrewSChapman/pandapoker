<?php

namespace App\Providers;

use App\Domains\ChangeLog\Repository\ChangeLogRepository;
use App\Domains\ChangeLog\Repository\ChangeLogRepositoryInterface;
use App\Domains\ChangeLog\Service\ChangeLogRetriever\ChangeLogRetriever;
use App\Domains\ChangeLog\Service\ChangeLogRetriever\ChangeLogRetrieverInterface;
use App\Domains\Room\Repository\RoomRepository\RoomRepository;
use App\Domains\Room\Repository\RoomRepository\RoomRepositoryInterface;
use App\Domains\Room\Service\RoomCreator\RoomCreator;
use App\Domains\Room\Service\RoomCreator\RoomCreatorInterface;
use App\Domains\Room\Service\RoomDeleter\RoomDeleter;
use App\Domains\Room\Service\RoomDeleter\RoomDeleterInterface;
use App\Domains\Room\Service\RoomLister\RoomLister;
use App\Domains\Room\Service\RoomLister\RoomListerInterface;
use App\Domains\Room\Service\RoomOwnershipChanger\RoomOwnershipChanger;
use App\Domains\Room\Service\RoomOwnershipChanger\RoomOwnershipChangerInterface;
use App\Domains\Room\Service\RoomParticipantManager\RoomParticipantManager;
use App\Domains\Room\Service\RoomParticipantManager\RoomParticipantManagerInterface;
use App\Domains\Room\Service\RoomVotingService\RoomVotingService;
use App\Domains\Room\Service\RoomVotingService\RoomVotingServiceInterface;
use App\Domains\Shared\Concurrency\Service\LockManager\LockManager;
use App\Domains\Shared\Concurrency\Service\LockManager\LockManagerInterface;
use App\Domains\Shared\Event\EventSubscriber;
use App\Domains\Shared\Persistence\DataStore\DataStoreInterface;
use App\Domains\Shared\Persistence\DataStore\RedisDataStore;
use App\Domains\Shared\Persistence\DataStore\Type\ChannelName;
use App\Domains\Shared\Security\Service\TokenService\JwtTokenService;
use App\Domains\Shared\Security\Service\TokenService\TokenServiceInterface;
use App\Domains\Shared\Security\Service\TokenService\Type\TokenExpiryDuration;
use App\Domains\Shared\Security\Service\TokenService\Type\TokenIssuer;
use App\Domains\Shared\Security\Service\TokenService\Type\TokenSecret;
use App\Domains\User\Service\UserAssumer\UserAssumer;
use App\Domains\User\Service\UserAssumer\UserAssumerInterface;
use App\Domains\User\Service\UserLister\UserLister;
use App\Domains\User\Service\UserLister\UserListerInterface;
use App\Domains\User\Service\UserUpdater\UserUpdater;
use App\Domains\User\Service\UserUpdater\UserUpdaterInterface;
use Illuminate\Support\ServiceProvider;
use App\Domains\User\Repository\UserRepository;
use App\Domains\User\Repository\UserRepositoryInterface;
use App\Domains\User\Service\UserCreator\UserCreator;
use App\Domains\User\Service\UserCreator\UserCreatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerSharedProviders();
        $this->registerChangeLogProviders();
        $this->registerUserProviders();
        $this->registerRoomProviders();
    }

    private function registerSharedProviders(): void
    {
        $this->app->singleton(DataStoreInterface::class, function($app) {
            return new RedisDataStore();
        });

        $this->app->singleton(TokenServiceInterface::class, function($app) {
            $secret = new TokenSecret(env('JWT_SECRET'));
            $issuer = new TokenIssuer(env('JWT_ISSUER'));

            return new JwtTokenService($secret, $issuer);
        });

        $this->app->singleton(EventDispatcherInterface::class, function($app) {
            $eventSubscriber = new EventSubscriber(app()->make(ChangeLogRepositoryInterface::class));
            return $eventSubscriber->getEventDispatcher();
        });

        $this->app->singleton(LockManagerInterface::class, function($app) {
            return new LockManager(app()->make(DataStoreInterface::class));
        });
    }

    private function registerChangeLogProviders(): void
    {
        $this->app->singleton(ChangeLogRepositoryInterface::class, function($app) {
            return new ChangeLogRepository(
                app()->make(DataStoreInterface::class),
                app()->make(LockManagerInterface::class),
                new ChannelName(env('REDIS_EVENT_CHANNEL_NAME'))
            );
        });

        $this->app->singleton(ChangeLogRetrieverInterface::class, function($app) {
            return app()->make(ChangeLogRetriever::class);
        });
    }

    private function registerRoomProviders(): void
    {
        $this->app->singleton(RoomRepositoryInterface::class, function($app) {
            return new RoomRepository(app()->make(DataStoreInterface::class));
        });

        $this->app->singleton(RoomListerInterface::class, function($app) {
            return app()->make(RoomLister::class);
        });

        $this->app->singleton(RoomCreatorInterface::class, function($app) {
            return app()->make(RoomCreator::class);
        });

        $this->app->singleton(RoomParticipantManagerInterface::class, function($app) {
            return app()->make(RoomParticipantManager::class);
        });

        $this->app->singleton(RoomVotingServiceInterface::class, function($app) {
            return app()->make(RoomVotingService::class);
        });

        $this->app->singleton(RoomOwnershipChangerInterface::class, function($app) {
            return app()->make(RoomOwnershipChanger::class);
        });

        $this->app->singleton(RoomDeleterInterface::class, function($app) {
            return app()->make(RoomDeleter::class);
        });
    }

    private function registerUserProviders(): void
    {
        $this->app->singleton(UserRepositoryInterface::class, function($app) {
            return new UserRepository(app()->make(DataStoreInterface::class));
        });

        $this->app->singleton(UserCreatorInterface::class, function($app) {
            return new UserCreator(
                app()->make(UserRepositoryInterface::class),
                app()->make(TokenServiceInterface::class),
                new TokenExpiryDuration(env('TOKEN_EXPIRY_DURATION', 86400)),
                app()->make(EventDispatcherInterface::class)
            );
        });

        $this->app->singleton(UserListerInterface::class, function($app) {
            return new UserLister(app()->make(UserRepositoryInterface::class));
        });

        $this->app->singleton(UserUpdaterInterface::class, function($app) {
            return app()->make(UserUpdater::class);
        });

        $this->app->singleton(UserAssumerInterface::class, function($app) {
            return new UserAssumer(
                app()->make(UserRepositoryInterface::class),
                app()->make(TokenServiceInterface::class),
                new TokenExpiryDuration(env('TOKEN_EXPIRY_DURATION', 86400))
            );
        });
    }
}
