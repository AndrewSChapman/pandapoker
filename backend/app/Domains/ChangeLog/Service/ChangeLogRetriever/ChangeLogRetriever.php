<?php

namespace App\Domains\ChangeLog\Service\ChangeLogRetriever;

use App\Domains\ChangeLog\Collection\ChangeLogList;
use App\Domains\ChangeLog\Repository\ChangeLogRepositoryInterface;
use App\Domains\ChangeLog\Type\ChangeLogId;
use App\Domains\User\Type\UserId;

class ChangeLogRetriever implements ChangeLogRetrieverInterface
{
    /** @var ChangeLogRepositoryInterface */
    private $changeLogRepository;

    public function __construct(ChangeLogRepositoryInterface $changeLogRepository)
    {
        $this->changeLogRepository = $changeLogRepository;
    }

    /**
     * @inheritDoc
     */
    public function getChangeLog(UserId $userId, ?ChangeLogId $startId = null): ChangeLogList
    {
        $changeLogItems = $this->changeLogRepository->getChangeLogItems($startId);
        if ($changeLogItems->isEmpty()) {
            return $changeLogItems;
        }

        $filteredItems = new ChangeLogList();
        foreach ($changeLogItems as $changeLogItem) {
            if (!$changeLogItem->getCreatedBy()->equals($userId)) {
                $filteredItems->add($changeLogItem);
            }
        }

        return $filteredItems;
    }

    /**
     * @inheritDoc
     */
    public function getMaxId(): ?ChangeLogId
    {
        $changeLogItems = $this->changeLogRepository->getChangeLogItems();
        if ($changeLogItems->isEmpty()) {
            return null;
        }

        $maxId = 0;

        foreach ($changeLogItems as $changeLogItem) {
            if ($changeLogItem->getId()->getValue() > $maxId) {
                $maxId = $changeLogItem->getId()->getValue();
            }
        }

        return new ChangeLogId($maxId);
    }
}
