<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Events\RefundProcessed;
use App\Models\Refund;
use App\Models\Transaction;
use App\Services\Interfaces\RefundServiceInterface;
use App\Services\Interfaces\TransactionServiceInterface;
use App\Services\Interfaces\WalletServiceInterface;
use Illuminate\Support\Facades\DB;

class RefundService implements RefundServiceInterface
{
    public function __construct(
        protected WalletServiceInterface $walletService,
        protected TransactionServiceInterface $transactionService
    ) {}

    public function processRefund(int $originalTransactionId): Transaction
    {
        return DB::transaction(function () use ($originalTransactionId) {
            $originalTransaction = Transaction::query()->lockForUpdate()->findOrFail($originalTransactionId);

            if ($originalTransaction->type !== TransactionType::DEBIT) {
                throw new \LogicException('Only debit transactions can be refunded.');
            }
            if (Refund::query()->where('original_transaction_id', $originalTransaction->id)->exists()) {
                throw new \LogicException('This transaction has already been refunded.');
            }

            $wallet = $this->walletService->findAndLock($originalTransaction->wallet_id);

            $this->walletService->credit($wallet, $originalTransaction->amount);

            $refundTransaction = $this->transactionService->log(
                walletId: $wallet->id,
                type: TransactionType::REFUND,
                amount: $originalTransaction->amount,
                description: "refunded_transaction_id:$originalTransaction->id",
                invoiceId: $originalTransaction->invoice_id,
            );

            Refund::query()->create([
                'original_transaction_id' => $originalTransaction->id,
                'refund_transaction_id' => $refundTransaction->id,
                'amount' => $originalTransaction->amount,
                'reason' => 'User requested refund.',
            ]);

            DB::afterCommit(fn () => event(new RefundProcessed($refundTransaction)));

            return $refundTransaction;
        });
    }
}
