<?php

namespace App\Services;

use App\DTOs\Payment\PayInvoiceDTO;
use App\Enums\TransactionType;
use App\Events\PaymentFailed;
use App\Events\PaymentSuccessful;
use App\Exceptions\LogicException;
use App\Exceptions\PaymentFailedException;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Interfaces\DailySpendingLimitServiceInterface;
use App\Services\Interfaces\InvoiceServiceInterface;
use App\Services\Interfaces\PaymentServiceInterface;
use App\Services\Interfaces\RefundServiceInterface;
use App\Services\Interfaces\TransactionServiceInterface;
use App\Services\Interfaces\TwoFactorServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use App\Services\Interfaces\WalletServiceInterface;
use App\Services\Pipes\CheckGlobalDailyLimitPipe;
use App\Services\Pipes\CheckSufficientBalancePipe;
use App\Services\Pipes\CheckUserIsBlockedPipe;
use App\Services\Pipes\CheckWalletIsActivePipe;
use App\Services\Pipes\ValidateInvoicePipe;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Pipeline;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PaymentService implements PaymentServiceInterface
{
    public function __construct(
        protected UserServiceInterface $userService,
        protected WalletServiceInterface $walletService,
        protected TransactionServiceInterface $transactionService,
        protected InvoiceServiceInterface $invoiceService,
        protected DailySpendingLimitServiceInterface $dailySpendingLimitService,
        protected TwoFactorServiceInterface $twoFactorService,
        protected RefundServiceInterface $refundService,
    ) {}

    public function initiatePayment(int $userId, Invoice $invoice): void
    {
        $key = "otp-request:user:{$userId}:invoice:{$invoice->id}";
        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            throw new ThrottleRequestsException("Try again in {$seconds} seconds.");
        }
        RateLimiter::hit($key, 300);

        try {
            $user = $this->userService->findOrFail($userId);
            $wallet = $this->walletService->getActiveWallet($user->id);
            $globalSpend = $this->dailySpendingLimitService->findTodaySpend();
            $payload = [
                'user' => $user,
                'wallet' => $wallet,
                'invoice' => $invoice,
                'globalSpend' => $globalSpend,
            ];
            $this->runValidationPipeline($payload);

            $this->twoFactorService->sendOtp($user->id, $invoice->id);
        } catch (Throwable) {
            throw new LogicException(Response::HTTP_BAD_REQUEST, 'could not send otp');
        }

    }

    public function pay(PayInvoiceDTO $dto): Transaction
    {
        if (! $this->twoFactorService->verifyOtp($dto)) {
            throw new ValidationException('The provided OTP is invalid or has expired.');
        }

        try {
            return DB::transaction(function () use ($dto) {
                $user = $this->userService->findAndLock($dto->userId);
                $wallet = $this->walletService->getActiveWalletByUserIdAndLock($user->id);
                $invoice = $this->invoiceService->findAndLock($dto->invoiceId);
                $globalSpend = $this->dailySpendingLimitService->findTodaySpendAndLock();

                $this->runValidationPipeline([
                    'user' => $user,
                    'wallet' => $wallet,
                    'invoice' => $invoice,
                    'globalSpend' => $globalSpend,
                ]);

                $transaction = $this->executePaymentLogic($wallet, $invoice);

                $this->twoFactorService->markOtpAsUsed(
                    $dto->userId,
                    $dto->invoiceId,
                    $dto->otp);

                DB::afterCommit(fn () => event(new PaymentSuccessful($transaction)));

                return $transaction;

            }, 5);
        } catch (ValidationException) {
            $originalTransaction = $this->transactionService->findByInvoiceId($dto->invoiceId);

            if ($originalTransaction && $originalTransaction->type === TransactionType::DEBIT) {
                $this->refundService->processRefund($originalTransaction->id);
            }
        } catch (Throwable) {
            event(new PaymentFailed(
                $this->invoiceService->find($dto->invoiceId),
                $this->userService->findOrFail($dto->userId),
                'A critical error occurred during payment.'
            ));

            throw new PaymentFailedException(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                'Payment could not be completed due to a system error.'
            );
        }
    }

    private function executePaymentLogic(Wallet $wallet, Invoice $invoice): Transaction
    {
        $this->walletService->debit($wallet, $invoice->amount);
        $this->invoiceService->markAsPaid($invoice);
        $this->dailySpendingLimitService->incrementTodaySpend($invoice->amount);

        return $this->transactionService->log(
            walletId: $wallet->id,
            type: TransactionType::DEBIT,
            amount: $invoice->amount,
            invoiceId: $invoice->id
        );
    }

    private function runValidationPipeline(array $payload): void
    {
        try {
            Pipeline::send($payload)
                ->through([
                    CheckUserIsBlockedPipe::class,
                    CheckWalletIsActivePipe::class,
                    ValidateInvoicePipe::class,
                    CheckSufficientBalancePipe::class,
                    CheckGlobalDailyLimitPipe::class,
                ])
                ->thenReturn();
        } catch (Throwable $e) {
            throw new LogicException($e->getCode(), $e->getMessage());
        }
    }
}
