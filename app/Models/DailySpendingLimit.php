<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailySpendingLimit extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'total_spent',
    ];

    protected $casts = [
        'total_spent' => 'decimal:2',
        'date' => 'date',
    ];
}
