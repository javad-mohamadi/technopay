<?php

namespace App\Enums;

enum TransactionType: string
{
    case DEBIT = 'DEBIT';
    case CREDIT = 'CREDIT';
    case REFUND = 'REFUND';
}
