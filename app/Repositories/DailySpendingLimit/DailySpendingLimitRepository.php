<?php

namespace App\Repositories\DailySpendingLimit;

use App\Models\DailySpendingLimit;
use App\Repositories\BaseRepository;
use Illuminate\Support\Carbon;

class DailySpendingLimitRepository extends BaseRepository implements DailySpendingLimitRepositoryInterface
{
    public function __construct(DailySpendingLimit $model)
    {
        parent::__construct($model);
    }

    public function findTodaySpend(): ?DailySpendingLimit
    {
        return $this->model
            ->where('date', Carbon::today())
            ->first();
    }

    public function findTodaySpendAndLock(): ?DailySpendingLimit
    {
        return $this->model
            ->where('date', Carbon::today())
            ->lockForUpdate()
            ->first();
    }

    public function incrementTodaySpend(float $amount): void
    {
        $record = $this->model->firstOrCreate(
            ['date' => Carbon::today()],
            ['total_spent' => 0]
        );

        $record->increment('total_spent', $amount);
    }
}
