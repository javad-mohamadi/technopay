<?php

namespace App\Services;

use App\Models\DailySpendingLimit;
use App\Repositories\DailySpendingLimit\DailySpendingLimitRepositoryInterface;
use App\Services\Interfaces\DailySpendingLimitServiceInterface;

class DailySpendingLimitService implements DailySpendingLimitServiceInterface
{
    public function __construct(protected DailySpendingLimitRepositoryInterface $repository) {}

    public function canSpend(float $amount): array
    {
        $limit = config('wallet.max_global_daily_spend');

        $log = $this->findTodaySpendAndLock();
        $todaySpending = $log ? $log->total_spent : 0;

        if (($todaySpending + $amount) > $limit) {
            return [
                'can_spend' => false,
                'reason' => 'Global daily spending limit would be exceeded.',
            ];
        }

        return ['can_spend' => true];
    }

    public function incrementTodaySpend(float $amount): void
    {
        $this->repository->incrementTodaySpend($amount);
    }

    public function findTodaySpendAndLock(): DailySpendingLimit
    {
        return $this->repository->findTodaySpendAndLock();
    }
}
