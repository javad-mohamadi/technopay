<?php

namespace App\Specifications\Invoice;

use App\Models\Invoice;
use App\Specifications\Contracts\Specification;
use LogicException;

class InvoiceIsNotExpired implements Specification
{
    public function isSatisfiedBy(mixed $object): bool
    {
        if (! $object instanceof Invoice) {
            throw new LogicException('This specification can only be used with Invoice objects.');
        }

        return ! $object->isExpired();
    }

    public function getErrorMessage(): string
    {
        return 'Invoice has expired.';
    }
}
