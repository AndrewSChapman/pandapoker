<?php


namespace App\Domains\ChangeLog\Service\ChangeLogRetriever;

use App\Domains\ChangeLog\Collection\ChangeLogList;
use App\Domains\ChangeLog\Type\ChangeLogId;
use App\Domains\User\Type\UserId;

interface ChangeLogRetrieverInterface
{
    /**
     * Returns the change log, filtering out change log items that belong to the current user
     * and optionally starting at a specific id (for use cases such as "give me all the changes since id 5"
     */
    public function getChangeLog(UserId $userId, ?ChangeLogId $startId = null): ChangeLogList;

    /**
     * Gets the current maximum change log id that is in use.
     */
    public function getMaxId(): ?ChangeLogId;
}
