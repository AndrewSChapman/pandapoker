<?php

namespace App\Domains\User\Type;

use PhpTypes\Type\AbstractEnum;

class AnimalType extends AbstractEnum
{
    public const BIRD = 'bird';
    public const CAT = 'cat';
    public const DOG = 'dog';
    public const DUCK = 'duck';
    public const ELEPHANT = 'elephant';
    public const MONKEY = 'monkey';
    public const OWL = 'owl';
}
