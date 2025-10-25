<?php

namespace App\Services;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Repositories\Transaction\TransactionRepositoryInterface;
use App\Services\Interfaces\TransactionServiceInterface;

class TransactionService implements TransactionServiceInterface
{
    public function __construct(protected TransactionRepositoryInterface $transactionRepository) {}

    public function log(int $walletId, TransactionType $type, float $amount, ?string $description = null, ?int $invoiceId = null): Transaction
    {
        return $this->transactionRepository->create([
            'invoice_id' => $invoiceId,
            'wallet_id' => $walletId,
            'type' => $type,
            'amount' => $amount,
            'status' => TransactionStatus::SUCCESSFUL,
            'metadata' => ['description' => $description],
        ]);
    }
}
