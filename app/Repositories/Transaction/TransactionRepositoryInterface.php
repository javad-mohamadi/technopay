<?php

namespace App\Repositories\Transaction;

interface TransactionRepositoryInterface
{
    public function findByInvoiceId(int $invoiceId);
}
