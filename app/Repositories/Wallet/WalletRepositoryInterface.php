<?php

namespace App\Repositories\Wallet;

use App\Models\Wallet;

interface WalletRepositoryInterface
{
    public function findActiveByUserId(int $userId): ?Wallet;
}
