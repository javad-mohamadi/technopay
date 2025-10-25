<?php

namespace App\Providers;

use App\Events\PaymentFailed;
use App\Events\PaymentSuccessful;
use App\Events\RefundProcessed;
use App\Listeners\SendPaymentNotification;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        PaymentSuccessful::class => [
            SendPaymentNotification::class,
        ],
        PaymentFailed::class => [
            SendPaymentNotification::class,
        ],
        RefundProcessed::class => [
            SendPaymentNotification::class,
        ],
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
