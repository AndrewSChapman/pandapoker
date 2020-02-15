<?php

use App\Domains\Shared\Persistence\DataStore\Type\ChannelName;
use App\Domains\EventStream\Service\EventStreamSubscriber\EventStreamSubscriber;
use App\Domains\Shared\Persistence\DataStore\RedisDataStore;

// Ensure PHP doesn't timeout sockets - we need a persistent connection to Redis
ini_set('default_socket_timeout', -1);

require_once('../vendor/autoload.php');

// Load the .env file into environment variables.
$dotenv = \Dotenv\Dotenv::create(__DIR__ . '/../');
$dotenv->load();

$channelName = new ChannelName(env('REDIS_EVENT_CHANNEL_NAME'));

$redisDataStore = new RedisDataStore();
$eventStreamSubscriber = new EventStreamSubscriber($redisDataStore, $channelName);
$eventStreamSubscriber->listen();
