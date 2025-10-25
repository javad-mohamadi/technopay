<?php

namespace App\Repositories\Wallet;

use App\Models\Wallet;
use App\Repositories\BaseRepository;

class WalletRepository extends BaseRepository implements WalletRepositoryInterface
{
    public function __construct(Wallet $model)
    {
        parent::__construct($model);
    }

    public function findActiveByUserId(int $userId): ?Wallet
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->first();
    }

    public function getActiveWalletByUserIdAndLock(int $userId): ?Wallet
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->lockForUpdate()
            ->first();
    }
}
