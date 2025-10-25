<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case PAID = 'PAID';
    case EXPIRED = 'EXPIRED';
    case FAILED = 'FAILED';
    case PENDING = 'PENDING';
}
