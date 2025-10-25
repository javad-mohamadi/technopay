<?php

namespace App\Repositories\TwoFactor;

use App\Models\TwoFactorVerification;

interface TwoFactorRepositoryInterface
{
    public function findValidOtp(int $userId, string $otp): ?TwoFactorVerification;
}
