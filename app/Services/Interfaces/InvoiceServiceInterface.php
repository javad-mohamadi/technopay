<?php

namespace App\Services\Interfaces;

use App\Models\Invoice;

interface InvoiceServiceInterface
{
    public function create(int $userId, float $amount, int $expiresInMinutes = 60): Invoice;

    public function find(int $id);

    public function requestInvoicePayment($dto): bool;

    public function markAsPaid(Invoice $invoice): bool;

    public function findAndLock(int $id);
}
