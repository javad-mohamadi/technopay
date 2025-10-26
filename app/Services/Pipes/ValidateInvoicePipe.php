<?php

namespace App\Services\Pipes;

use App\Models\Invoice;
use App\Models\User;
use App\Specifications\Invoice\InvoiceBelongsToUser;
use App\Specifications\Invoice\InvoiceIsNotExpired;
use App\Specifications\Invoice\InvoiceIsPending;
use App\Specifications\Validator\SpecificationValidator;
use Closure;

class ValidateInvoicePipe extends AbstractPaymentCheckerPipe
{
    public function handle($request, Closure $next)
    {
        /** @var User $user */
        $user = $request['user'];
        /** @var Invoice $invoice */
        $invoice = $request['invoice'];

        SpecificationValidator::validate($invoice, [
            new InvoiceBelongsToUser($user),
            new InvoiceIsPending,
            new InvoiceIsNotExpired,
        ]);

        return $next($request);
    }
}
