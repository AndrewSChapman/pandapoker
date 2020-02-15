<?php

namespace App\Domains\Shared\Http\Type;

use PhpTypes\Type\AbstractEnum;

class HttpRequestType extends AbstractEnum
{
    public const DELETE = 'DELETE';
    public const GET = 'GET';
    public const PATCH = 'PATCH';
    public const POST = 'POST';
    public const PUT = 'PUT';
}
