<?php

namespace App\Specifications\Invoice;

use App\Models\Invoice;
use App\Models\User;
use App\Specifications\Contracts\Specification;
use LogicException;

class InvoiceBelongsToUser implements Specification
{
    public function __construct(private User $user) {}

    public function isSatisfiedBy(mixed $object): bool
    {
        if (! $object instanceof Invoice) {
            throw new LogicException('This specification can only be used with Invoice objects.');
        }

        return $object->user_id === $this->user->id;
    }

    public function getErrorMessage(): string
    {
        return 'Invoice does not belong to the user.';
    }
}
