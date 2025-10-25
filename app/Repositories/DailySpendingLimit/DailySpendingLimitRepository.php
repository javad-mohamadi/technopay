<?php

namespace App\Repositories\DailySpendingLimit;

use App\Models\DailySpendingLimit;
use App\Models\TwoFactorVerification;
use App\Repositories\BaseRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DailySpendingLimitRepository extends BaseRepository implements DailySpendingLimitRepositoryInterface
{
    public function __construct(TwoFactorVerification $model)
    {
        parent::__construct($model);
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
        $this->model->updateOrCreate(
            ['date' => Carbon::today()],
            ['total_spent' => DB::raw("total_spent + {$amount}")]
        );
    }
}
