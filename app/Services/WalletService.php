<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Models\User;
use App\Models\Wallet;
use App\Repositories\Wallet\WalletRepositoryInterface;
use App\Services\Interfaces\TransactionServiceInterface;
use App\Services\Interfaces\WalletServiceInterface;
use Illuminate\Database\Eloquent\Model;
use LogicException;

class WalletService implements WalletServiceInterface
{
    public function __construct(
        protected WalletRepositoryInterface $walletRepository,
        protected TransactionServiceInterface $transactionService
    ) {}

    public function createForUser(User $user, float $initialBalance = 0): Wallet
    {
        $data = [
            'user_id' => $user->id,
            'balance' => $initialBalance,
        ];
        $wallet = $this->walletRepository->create($data);
        if ($initialBalance > 0) {
            $this->transactionService->log(
                $wallet->id,
                TransactionType::CREDIT,
                $initialBalance,
                'Initial wallet balance'
            );
        }

        return $wallet;
    }

    public function credit(Wallet $wallet, float $amount): Wallet
    {
        if (! $wallet->is_active) {
            throw new LogicException('Wallet not found.');
        }

        $wallet->balance += $amount;
        $wallet->save();

        return $wallet;
    }

    public function debit(Wallet $wallet, float $amount): Wallet
    {
        if ($wallet->balance < $amount) {
            throw new LogicException('Insufficient balance.');
        }

        $wallet->balance -= $amount;
        $wallet->save();

        return $wallet;
    }

    public function getActiveWallet(int $userId): ?Wallet
    {
        return $this->walletRepository->findActiveByUserId($userId);
    }

    public function findAndLock(int $id): Model
    {
        return $this->walletRepository->findAndLock($id);
    }
}
