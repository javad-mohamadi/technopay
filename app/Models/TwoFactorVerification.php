<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwoFactorVerification extends Model
{
    protected $fillable = [
        'user_id',
        'invoice_id',
        'otp_code',
        'expires_at',
        'is_verified',
        'attempts',
    ];

    protected $casts = [
        'expires_at' => 'timestamp',
        'is_verified' => 'boolean',
    ];
}
