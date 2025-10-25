<?php

namespace App\Services\Pipes;

use App\Models\Invoice;
use App\Models\User;
use App\Specifications\Invoice\InvoiceBelongsToUser;
use App\Specifications\Invoice\InvoiceIsNotExpired;
use App\Specifications\Invoice\InvoiceIsPending;
use App\Specifications\Validator\SpecificationValidator;
use Closure;

class ValidateInvoicePipe
{
    public function handle(array $payload, Closure $next)
    {
        /** @var User $user */
        $user = $payload['user'];
        /** @var Invoice $invoice */
        $invoice = $payload['invoice'];

        SpecificationValidator::validate($invoice, [
            new InvoiceBelongsToUser($user),
            new InvoiceIsPending,
            new InvoiceIsNotExpired,
        ]);

        return $next($payload);
    }
}
