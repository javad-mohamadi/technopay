<?php

namespace App\Repositories\Transaction;

use App\Models\Transaction;
use App\Repositories\BaseRepository;

class TransactionRepository extends BaseRepository implements TransactionRepositoryInterface
{
    public function __construct(Transaction $model)
    {
        parent::__construct($model);
    }

    public function findByInvoiceId(int $invoiceId)
    {
        return $this->model->where('invoice_id', $invoiceId)->first();
    }
}
