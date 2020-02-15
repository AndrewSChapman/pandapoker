<?php

namespace App\Domains\User\Entity;

use App\Domains\Shared\Type\CreatedAt;
use App\Domains\Shared\Type\UpdatedAt;
use App\Domains\User\Type\AnimalType;
use App\Domains\User\Type\UserId;
use App\Domains\User\Type\Username;

class User
{
    /** @var UserId */
    private $id;

    /** @var Username */
    private $username;

    /** @var AnimalType */
    private $totemAnimal;

    /** @var CreatedAt */
    private $createdAt;

    /** @var UpdatedAt */
    private $updatedAt;

    public function __construct(
        UserId $id,
        Username $username,
        AnimalType $totemAnimal,
        CreatedAt $createdAt = null,
        UpdatedAt $updatedAt = null
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->totemAnimal = $totemAnimal;
        $this->createdAt = $createdAt ? $createdAt : new CreatedAt();
        $this->updatedAt = $updatedAt ? $updatedAt : new UpdatedAt();
    }

    public function getId(): UserId
    {
        return $this->id;
    }

    public function getUsername(): Username
    {
        return $this->username;
    }

    public function setUsername(Username $username): void
    {
        $this->username = $username;
    }

    public function getTotemAnimal(): AnimalType
    {
        return $this->totemAnimal;
    }

    public function setTotemAnimal(AnimalType $totemAnimal): void
    {
        $this->totemAnimal = $totemAnimal;
    }

    public function getCreatedAt(): CreatedAt
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): UpdatedAt
    {
        return $this->updatedAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId()->getUuid()->toString(),
            'username' => $this->getUsername()->toString(),
            'totem_animal' => (string)$this->getTotemAnimal(),
            'created_at' => $this->getCreatedAt()->getTimestamp(),
            'updated_at' => $this->getUpdatedAt()->getTimestamp()
        ];
    }
}
