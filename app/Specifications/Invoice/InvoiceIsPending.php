<?php

namespace App\Specifications\Invoice;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Specifications\Contracts\Specification;
use LogicException;

class InvoiceIsPending implements Specification
{
    public function isSatisfiedBy(mixed $object): bool
    {
        if (! $object instanceof Invoice) {
            throw new LogicException('This specification can only be used with Invoice objects.');
        }

        return $object->status === InvoiceStatus::PENDING;
    }

    public function getErrorMessage(): string
    {
        return 'Invoice is not pending';
    }
}
