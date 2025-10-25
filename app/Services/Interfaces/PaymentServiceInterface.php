<?php

namespace App\Services\Interfaces;

use App\DTOs\Payment\PayInvoiceDTO;
use App\Models\Transaction;

interface PaymentServiceInterface
{
    public function pay(PayInvoiceDTO $dto): Transaction;
}
