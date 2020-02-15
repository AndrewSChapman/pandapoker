<?php

namespace Testing\Unit\Domains\ChangeLog\Service;

use App\Domains\ChangeLog\Collection\ChangeLogList;
use App\Domains\ChangeLog\Service\ChangeLogRetriever\ChangeLogRetriever;
use Testing\Unit\UnitTest;

class ChangeLogRetrieverTest extends UnitTest
{
    public function testChangeLogRetrieverWillReturnAllItemsIfUserIsDifferentToWhoCreatedTheItems(): void
    {
        $changeLogRepo = $this->getDataHelper()->changeLog()->makeChangeLogRepository();

        // Make some change log items all created by the same user
        $user = $this->getDataHelper()->user()->makeUser();
        $changeLogList = new ChangeLogList();
        $changeLogList->add($this->getDataHelper()->changeLog()->makeChangeLogItem($user));
        $changeLogList->add($this->getDataHelper()->changeLog()->makeChangeLogItem($user));
        $changeLogList->add($this->getDataHelper()->changeLog()->makeChangeLogItem($user));

        // Make sure the repo returns all 3 items
        $changeLogRepo->expects($this->once())->method('getChangeLogItems')->willReturn($changeLogList);


        $changeLogRetriever = new ChangeLogRetriever($changeLogRepo);

        // Ensure that when we call the retriever we still get 3 items back, because the user id is different.
        $anotherUser = $this->getDataHelper()->user()->makeUser();
        $returnedItems = $changeLogRetriever->getChangeLog($anotherUser->getId());
        $this->assertCount(3, $returnedItems);
    }

    public function testChangeLogRetrieverWillFilterItemsCreatedBySameUser(): void
    {
        $changeLogRepo = $this->getDataHelper()->changeLog()->makeChangeLogRepository();

        // Make some change log items all created by the same user
        $user = $this->getDataHelper()->user()->makeUser();
        $changeLogList = new ChangeLogList();
        $changeLogList->add($this->getDataHelper()->changeLog()->makeChangeLogItem($user));
        $changeLogList->add($this->getDataHelper()->changeLog()->makeChangeLogItem($user));
        $changeLogList->add($this->getDataHelper()->changeLog()->makeChangeLogItem($user));

        // Make sure the repo returns all 3 items
        $changeLogRepo->expects($this->once())->method('getChangeLogItems')->willReturn($changeLogList);

        $changeLogRetriever = new ChangeLogRetriever($changeLogRepo);

        // Ensure that if we call the retriever with the same user id who created the change log items in the first place that
        // none of them are returned
        $returnedItems = $changeLogRetriever->getChangeLog($user->getId());
        $this->assertCount(0, $returnedItems);
    }
}
