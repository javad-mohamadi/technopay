<?php

namespace Tests\Feature;

use App\Enums\InvoiceStatus;
use App\Enums\TransactionType;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Wallet;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    public function test_user_can_successfully_pay_invoice_with_sufficient_balance(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->for($user)->create(['balance' => 100_000]);
        $invoice = Invoice::factory()->for($user)->create([
            'amount' => 50_000,
            'status' => InvoiceStatus::PENDING,
        ]);

        $response = $this->actingAs($user, 'api')->postJson('/api/invoice/pay', [
            'invoice_id' => $invoice->id,
            'otp' => '123456',
        ]);

        // Assert (Verify results)
        $response->assertStatus(200);
        $response->assertJsonStructure(['transaction_reference']);

        // Verify wallet balance
        $this->assertEquals(50000, $wallet->fresh()->balance);

        // Verify transaction was created
        $this->assertDatabaseHas('transactions', [
            'wallet_id' => $wallet->id,
            'type' => TransactionType::DEBIT,
            'amount' => 50000,
            'status' => 'SUCCESSFUL',
        ]);

        // Verify invoice was marked as paid
        $this->assertEquals(InvoiceStatus::PAID, $invoice->fresh()->status);
        $this->assertNotNull($invoice->fresh()->paid_at);
    }
}
