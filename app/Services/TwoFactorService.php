<?php

namespace App\Services;

use App\DTOs\Payment\PayInvoiceDTO;
use App\Repositories\TwoFactor\TwoFactorRepositoryInterface;
use App\Services\Interfaces\TwoFactorServiceInterface;
use Illuminate\Support\Facades\Log;

class TwoFactorService implements TwoFactorServiceInterface
{
    public function __construct(protected TwoFactorRepositoryInterface $twoFactorRepository) {}

    public function sendOtp(int $userId, int $invoiceId): string
    {
        $otp = rand(100000, 999999);
        $data = [
            'user_id' => $userId,
            'invoice_id' => $invoiceId,
            'otp_code' => $otp,
            'expires_at' => now()->addMinutes(5),
        ];

        $this->twoFactorRepository->create($data);

        Log::info("OTP for user {$userId} otp code: {$otp}");

        return 'OTP sent successfully.';
    }

    public function verifyOtp(PayInvoiceDTO $dto): bool
    {
        $verification = $this->twoFactorRepository->findValidOtp(
            userId: $dto->userId, invoiceId: $dto->invoiceId, otp: $dto->otp
        );

        return $verification !== null;
    }

    public function markOtpAsUsed(int $userId, string $invoiceId, string $otp): void
    {
        $verification = $this->twoFactorRepository->findValidOtp($userId, $invoiceId, $otp);

        $verification?->update(['is_verified' => true]);
    }
}
