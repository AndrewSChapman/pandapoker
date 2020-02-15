<?php

namespace App\Domains\Shared\Exception;

class JsonEncodingException extends \Exception
{
    public function __construct($message = "Failed to encode data to JSON", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
