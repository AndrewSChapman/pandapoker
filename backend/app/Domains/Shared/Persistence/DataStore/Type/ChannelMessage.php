<?php

namespace App\Domains\Shared\Persistence\DataStore\Type;

use App\Domains\Shared\Helper\JsonHelper;
use App\Domains\Shared\Exception\JsonEncodingException;

class ChannelMessage
{
    /** @var string */
    private $message;

    public function __construct(string $message = '')
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    public function __toString(): string
    {
        return $this->message;
    }

    /**
     * @param array $message
     * @return static
     * @throws JsonEncodingException
     */
    public static function fromArray(array $message): self
    {
        return new self(JsonHelper::arrayToJsonString($message));
    }
}
