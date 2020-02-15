<?php

namespace App\Domains\EventStream\Service\EventStreamSubscriber;

use App\Domains\Shared\Persistence\DataStore\DataStoreInterface;
use App\Domains\Shared\Persistence\DataStore\Type\ChannelName;

class EventStreamSubscriber
{
    /** @var DataStoreInterface */
    private $dataStore;

    /** @var ChannelName */
    private $channelName;

    public function __construct(DataStoreInterface $dataStore, ChannelName $channelName)
    {
        $this->dataStore = $dataStore;
        $this->channelName = $channelName;
    }

    public function listen(): void
    {
        $this->dataStore->subscribe($this->channelName, function($message) {
            print "$message\n";
        });
    }
}
