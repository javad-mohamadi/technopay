<?php

namespace App\Specifications\Validator;

use App\Specifications\Contracts\Specification;
use LogicException;

class SpecificationValidator
{
    public static function validate(mixed $object, array $specifications): void
    {
        /** @var Specification $specification */
        foreach ($specifications as $specification) {
            if (! $specification->isSatisfiedBy($object)) {
                throw new LogicException($specification->getErrorMessage());
            }
        }
    }
}
