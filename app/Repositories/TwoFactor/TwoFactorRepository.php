<?php

namespace App\Repositories\TwoFactor;

use App\Models\TwoFactorVerification;
use App\Repositories\BaseRepository;

class TwoFactorRepository extends BaseRepository implements TwoFactorRepositoryInterface
{
    public function __construct(TwoFactorVerification $model)
    {
        parent::__construct($model);
    }

    public function findValidOtp(int $userId, int $invoiceId, string $otp): ?TwoFactorVerification
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('invoice_id', $invoiceId)
            ->where('otp_code', $otp)
            ->where('is_verified', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
    }
}
