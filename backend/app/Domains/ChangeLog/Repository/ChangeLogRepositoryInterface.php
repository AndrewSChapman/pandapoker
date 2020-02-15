<?php

namespace App\Domains\ChangeLog\Repository;

use App\Domains\ChangeLog\Collection\ChangeLogList;
use App\Domains\ChangeLog\Entity\ChangeLogItem;
use App\Domains\ChangeLog\Type\ChangeLogId;

interface ChangeLogRepositoryInterface
{
    public function getNextId(): ChangeLogId;
    public function saveChangeLogItem(ChangeLogItem $changeLogItem): void;
    public function getChangeLogItem(ChangeLogId $changeLogId): ChangeLogItem;
    public function getChangeLogItems(?ChangeLogId $startId = null): ChangeLogList;
}
