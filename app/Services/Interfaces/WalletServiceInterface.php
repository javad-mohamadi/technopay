<?php

namespace App\Services\Interfaces;

use App\Models\User;
use App\Models\Wallet;

interface WalletServiceInterface
{
    public function create(User $user, float $initialBalance = 0): Wallet;
}
