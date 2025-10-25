<?php

namespace App\Listeners;

use App\Events\PaymentFailed;
use App\Events\PaymentSuccessful;
use App\Events\RefundProcessed;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPaymentNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(protected NotificationService $notificationService) {}

    public function handle(PaymentSuccessful|PaymentFailed|RefundProcessed $event): void
    {
        match (true) {
            $event instanceof PaymentSuccessful => $this->handlePaymentSuccess($event),
            $event instanceof PaymentFailed => $this->handlePaymentFailure($event),
            $event instanceof RefundProcessed => $this->handleRefundSuccess($event),
        };
    }

    // can be using pattern for handle sent
    private function handlePaymentSuccess(PaymentSuccessful $event): void
    {
        $user = $event->transaction->wallet->user;
        $message = "Your payment of {$event->transaction->amount} for invoice #{$event->transaction->invoice_id} was successful.";

        $this->notificationService->send($user->id, $message, 'SUCCESS');
    }

    private function handlePaymentFailure(PaymentFailed $event): void
    {
        $message = "Your payment for invoice #{$event->invoice->id} failed: {$event->reason}";

        $this->notificationService->send($event->user->id, $message, 'FAILURE');
    }

    private function handleRefundSuccess(RefundProcessed $event): void
    {
        $user = $event->refundTransaction->wallet->user;
        $message = "A refund of {$event->refundTransaction->amount} has been processed for your wallet.";

        $this->notificationService->send($user->id, $message, 'INFO');
    }
}
