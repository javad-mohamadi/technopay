<?php

namespace App\Providers;

use App\Services\AuthenticationService;
use App\Services\DailySpendingLimitService;
use App\Services\Interfaces\AuthenticationServiceInterface;
use App\Services\Interfaces\DailySpendingLimitServiceInterface;
use App\Services\Interfaces\InvoiceServiceInterface;
use App\Services\Interfaces\PaymentServiceInterface;
use App\Services\Interfaces\RegistrationServiceInterface;
use App\Services\Interfaces\TransactionServiceInterface;
use App\Services\Interfaces\TwoFactorServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use App\Services\Interfaces\WalletServiceInterface;
use App\Services\InvoiceService;
use App\Services\PaymentService;
use App\Services\RegistrationService;
use App\Services\TransactionService;
use App\Services\TwoFactorService;
use App\Services\UserService;
use App\Services\WalletService;
use Illuminate\Support\ServiceProvider;

class ServiceLayerProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(AuthenticationServiceInterface::class, AuthenticationService::class);
        $this->app->bind(RegistrationServiceInterface::class, RegistrationService::class);
        $this->app->bind(WalletServiceInterface::class, WalletService::class);
        $this->app->bind(TransactionServiceInterface::class, TransactionService::class);
        $this->app->bind(InvoiceServiceInterface::class, InvoiceService::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(DailySpendingLimitServiceInterface::class, DailySpendingLimitService::class);
        $this->app->bind(PaymentServiceInterface::class, PaymentService::class);
        $this->app->bind(TwoFactorServiceInterface::class, TwoFactorService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
