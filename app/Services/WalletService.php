<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Services\Interfaces\WalletServiceInterface;

class WalletService implements WalletServiceInterface
{
    public function create(User $user, float $initialBalance = 0): Wallet
    {
        return Wallet::query()->create([
            'user_id' => $user->id,
            'balance' => $initialBalance,
            'is_active' => true,
        ]);
    }
}
