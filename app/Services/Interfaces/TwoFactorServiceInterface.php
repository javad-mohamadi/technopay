<?php

namespace App\Services\Interfaces;

use App\DTOs\Payment\PayInvoiceDTO;

interface TwoFactorServiceInterface
{
    public function sendOtp(int $userId, int $invoiceId): string;

    public function verifyOtp(PayInvoiceDTO $dto): bool;

    public function markOtpAsUsed(int $userId, string $invoiceId, string $otp): void;
}
