<?php

namespace App\Domains\Shared\Type;

use PhpTypes\Type\ConstrainedString;

class Varchar50Required extends ConstrainedString
{
    public function __construct(string $value)
    {
        parent::__construct($value, 1, 50);
    }
}
