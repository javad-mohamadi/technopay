<?php

namespace App\Services\Interfaces;

use App\Enums\TransactionType;
use App\Models\Transaction;

interface TransactionServiceInterface
{
    public function log(int $walletId, TransactionType $type, float $amount, ?string $description = null): Transaction;
}
