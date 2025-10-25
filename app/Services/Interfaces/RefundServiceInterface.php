<?php

namespace App\Services\Interfaces;

use App\Models\Transaction;

interface RefundServiceInterface
{
    public function processRefund(int $originalTransactionId): Transaction;
}
