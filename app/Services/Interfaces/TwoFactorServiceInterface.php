<?php

namespace App\Services\Interfaces;

interface TwoFactorServiceInterface
{
    public function sendOtp(int $userId): string;

    public function verifyOtp(int $userId, string $otp): bool;
}
