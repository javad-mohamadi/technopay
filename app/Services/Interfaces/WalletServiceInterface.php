<?php

namespace App\Services\Interfaces;

use App\Models\User;
use App\Models\Wallet;

interface WalletServiceInterface
{
    public function createForUser(User $user, float $initialBalance = 0): Wallet;

    public function credit(Wallet $wallet, float $amount): Wallet;

    public function debit(Wallet $wallet, float $amount): Wallet;

    public function getActiveWallet(int $userId): ?Wallet;

    public function findAndLock(int $id): Wallet;

    public function getActiveWalletByUserIdAndLock(int $userId): ?Wallet;
}
