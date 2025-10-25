<?php

namespace App\Services\Interfaces;

use App\Models\DailySpendingLimit;

interface DailySpendingLimitServiceInterface
{
    public function canSpend(float $amount): array;

    public function findTodaySpend(): ?DailySpendingLimit;

    public function incrementTodaySpend(float $amount): void;

    public function findTodaySpendAndLock(): ?DailySpendingLimit;
}
