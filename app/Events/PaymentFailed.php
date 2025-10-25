<?php

namespace App\Events;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentFailed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Invoice $invoice, public User $user, public string $reason) {}
}
