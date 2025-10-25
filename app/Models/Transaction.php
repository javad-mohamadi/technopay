<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'wallet_id',
        'type',
        'amount',
        'status',
        'reference_id',
        'metadata',
        'rollback_reason',
    ];

    protected $casts = [
        'metadata' => 'array',
        'amount' => 'decimal:2',
        'type' => TransactionType::class,
        'status' => TransactionStatus::class,
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->reference_id)) {
                $transaction->reference_id = (string) Str::uuid();
            }
        });
    }
}
