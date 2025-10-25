<?php

namespace App\Specifications\Contracts;

interface Specification
{
    public function isSatisfiedBy(mixed $object): bool;

    public function getErrorMessage(): string;
}
