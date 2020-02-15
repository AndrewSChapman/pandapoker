<?php

namespace Testing\Unit;

use PHPUnit\Framework\TestCase;
use Testing\DataHelper\DataHelper;

abstract class UnitTest extends TestCase
{
    /** @var DataHelper */
    private $dataHelper;

    public function getDataHelper(): DataHelper
    {
        if (!$this->dataHelper) {
            $this->dataHelper = new DataHelper($this);
        }

        return $this->dataHelper;
    }
}
