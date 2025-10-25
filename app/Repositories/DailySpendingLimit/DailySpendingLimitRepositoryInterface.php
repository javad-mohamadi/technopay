<?php

namespace App\Repositories\DailySpendingLimit;

use App\Models\DailySpendingLimit;

interface DailySpendingLimitRepositoryInterface
{
    public function findTodaySpendAndLock(): ?DailySpendingLimit;

    public function incrementTodaySpend(float $amount): void;
}
