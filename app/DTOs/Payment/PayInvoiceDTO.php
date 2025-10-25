<?php

namespace App\DTOs\Payment;

use Illuminate\Http\Request;

class PayInvoiceDTO
{
    public function __construct(public int $userId, public int $invoiceId, public string $otp) {}

    public static function getFromRequest(Request $request): PayInvoiceDTO
    {
        return new static(
            userId: auth()->id(),
            invoiceId: $request->validated('invoice_id'),
            otp: $request->validated('otp')
        );
    }
}
