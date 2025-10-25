<?php

namespace App\Services;

use App\Repositories\TwoFactor\TwoFactorRepositoryInterface;
use App\Services\Interfaces\TwoFactorServiceInterface;
use Illuminate\Support\Facades\Log;

class TwoFactorService implements TwoFactorServiceInterface
{
    public function __construct(protected TwoFactorRepositoryInterface $twoFactorRepository) {}

    public function sendOtp(int $userId): string
    {
        $otp = rand(100000, 999999);
        $data = [
            'user_id' => $userId,
            'otp_code' => $otp,
            'expires_at' => now()->addMinutes(5),
        ];

        $this->twoFactorRepository->create($data);

        Log::info("OTP for user {$userId}: {$otp}");

        return 'OTP sent successfully.'; // mock response
    }

    public function verifyOtp(int $userId, string $otp): bool
    {
        $verification = $this->twoFactorRepository->findValidOtp($userId, $otp);

        if ($verification) {
            $verification->update(['is_verified' => true]);

            return true;
        }

        return false;
    }
}
