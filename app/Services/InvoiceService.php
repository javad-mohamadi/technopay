<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Repositories\Invoice\InvoiceRepositoryInterface;
use App\Services\Interfaces\InvoiceServiceInterface;

class InvoiceService implements InvoiceServiceInterface
{
    public function __construct(
        protected InvoiceRepositoryInterface $invoiceRepository,
    ) {}

    public function create(int $userId, float $amount, int $expiresInMinutes = 60): Invoice
    {
        $data = [
            'user_id' => $userId,
            'amount' => $amount,
            'status' => InvoiceStatus::PENDING,
            'expires_at' => now()->addMinutes($expiresInMinutes),
        ];

        return $this->invoiceRepository->create($data);
    }

    public function find(int $id)
    {
        return $this->invoiceRepository->find($id);
    }

    public function markAsPaid(Invoice $invoice): bool
    {
        $invoice->status = InvoiceStatus::PAID;
        $invoice->paid_at = now();

        return $invoice->save();
    }

    public function findAndLock(int $id)
    {
        return $this->invoiceRepository->findAndLock($id);
    }

    public function requestInvoicePayment($dto): bool
    {
        // TODO: Implement requestInvoicePayment() method.
    }
}
