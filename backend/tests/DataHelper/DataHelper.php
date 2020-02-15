<?php

namespace Testing\DataHelper;

use App\Domains\Shared\Concurrency\Service\LockManager\LockManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Testing\DataHelper\Modules\ChangeLogDataHelper;
use Testing\DataHelper\Modules\HttpDataHelper;
use Testing\DataHelper\Modules\RoomDataHelper;
use Testing\DataHelper\Modules\SecurityDataHelper;
use Testing\DataHelper\Modules\UserDataHelper;

class DataHelper
{
    /** @var TestCase */
    private $testCase;

    /** @var ChangeLogDataHelper */
    private $changeLogDataHelper;

    /** @var HttpDataHelper */
    private $httpDataHelper;

    /** @var UserDataHelper */
    private $userDataHelper;

    /** @var RoomDataHelper */
    private $roomDataHelper;

    /** @var SecurityDataHelper */
    private $securityDataHelper;

    /**
     * DataHelper constructor.
     * @param TestCase $testCase
     */
    public function __construct(TestCase $testCase)
    {
        $this->testCase = $testCase;
    }

    public function changeLog(): ChangeLogDataHelper
    {
        if (!$this->changeLogDataHelper) {
            $this->changeLogDataHelper = new ChangeLogDataHelper($this->testCase);
        }

        return $this->changeLogDataHelper;
    }

    public function http(): HttpDataHelper
    {
        if (!$this->httpDataHelper) {
            $this->httpDataHelper = new HttpDataHelper($this->testCase);
        }

        return $this->httpDataHelper;
    }

    public function room(): RoomDataHelper
    {
        if (!$this->roomDataHelper) {
            $this->roomDataHelper = new RoomDataHelper($this->testCase);
        }

        return $this->roomDataHelper;
    }

    public function security(): SecurityDataHelper
    {
        if (!$this->securityDataHelper) {
            $this->securityDataHelper = new SecurityDataHelper($this->testCase);
        }

        return $this->securityDataHelper;
    }

    public function user(): UserDataHelper
    {
        if (!$this->userDataHelper) {
            $this->userDataHelper = new UserDataHelper($this->testCase);
        }

        return $this->userDataHelper;
    }

    /**
     * @return LockManagerInterface|MockObject
     */
    public function lockManager(): LockManagerInterface
    {
        return $this->testCase->getMockBuilder(LockManagerInterface::class)
            ->getMock();
    }

    /**
     * @return EventDispatcherInterface|MockObject
     */
    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->testCase->getMockBuilder(EventDispatcherInterface::class)
            ->getMock();
    }
}
