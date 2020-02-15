<?php

namespace Testing\DataHelper\Modules;

use PHPUnit\Framework\TestCase;

class AbstractDataHelperModule
{
    /** @var TestCase */
    private $testCase;

    /**
     * AbstractDataHelperModule constructor.
     * @param TestCase $testCase
     */
    public function __construct(TestCase $testCase)
    {
        $this->testCase = $testCase;
    }

    protected function getTestCase(): TestCase
    {
        return $this->testCase;
    }
}
