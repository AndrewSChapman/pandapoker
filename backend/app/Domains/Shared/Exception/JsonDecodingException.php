<?php

namespace App\Domains\Shared\Exception;

class JsonDecodingException extends \Exception
{
    public function __construct($message = "Failed to decode JSON string", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
