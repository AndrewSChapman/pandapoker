<?php


namespace App\Domains\Shared\Persistence\DataStore\Type;


use PhpTypes\Type\ConstrainedString;

class DataStoreKey extends ConstrainedString
{
    public function __construct(string $keyName)
    {
        parent::__construct($keyName, 1);
    }

    public function getIdFromKey(): string
    {
        $parts = explode(':', $this->getValue());
        return $parts[count($parts) - 1];
    }
}
