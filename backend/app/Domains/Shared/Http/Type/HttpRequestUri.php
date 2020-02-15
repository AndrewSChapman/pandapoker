<?php

namespace App\Domains\Shared\Http\Type;

use PhpTypes\Type\ConstrainedString;

class HttpRequestUri extends ConstrainedString
{
    public function __construct(string $uri)
    {
        parent::__construct($uri, 1);
    }
}
