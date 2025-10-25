<?php

namespace App\Repositories\TwoFactor;

use App\Models\TwoFactorVerification;

interface TwoFactorRepositoryInterface
{
    public function findValidOtp(int $userId, int $invoiceId, string $otp): ?TwoFactorVerification;
}
