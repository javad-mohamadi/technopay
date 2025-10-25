<?php

namespace Tests\Feature;

use App\DTOs\Auth\RegisterDTO;
use App\Enums\InvoiceStatus;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Invoice;
use App\Models\TwoFactorVerification;
use App\Models\User;
use App\Services\Interfaces\RegistrationServiceInterface;
use Illuminate\Support\Facades\Config;
use Laravel\Passport\Passport;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    protected User $user;

    protected Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        $registrationService = app(RegistrationServiceInterface::class);
        $registerDto = new RegisterDTO('technopay', 'technopay@gmail.com', '1234');
        $this->user = $registrationService->register($registerDto);
        Passport::actingAs($this->user);
    }

    public function test_payment_invoice_successful(): void
    {
        $invoice = Invoice::factory()->create([
            'user_id' => $this->user->id,
            'status' => InvoiceStatus::PENDING,
            'amount' => 4000,
        ]);

        Passport::actingAs($this->user);
        $response = $this->postJson("/api/v1/invoice/{$invoice->id}/request-payment");

        $this->assertDatabaseCount('two_factor_verifications', 1);
        $this->assertEquals($response['message'], 'OTP has been sent successfully. It is valid for 5 minutes.');
        $otp = TwoFactorVerification::query()->where('invoice_id', $invoice->id)->first();

        $response = $this->postJson('/api/v1/invoice/pay', [
            'invoice_id' => $invoice->id,
            'otp' => $otp->otp_code,
        ]);

        $invoice->refresh();

        $this->assertEquals($response['message'], 'Payment successful!');
        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => InvoiceStatus::PAID,
        ]);
        $this->assertNotNull($invoice->paid_at);

        $this->assertDatabaseHas('transactions', [
            'invoice_id' => $invoice->id,
            'type' => TransactionType::DEBIT,
            'status' => TransactionStatus::SUCCESSFUL,
            'amount' => 4000,
        ]);

        $otp->refresh();

        $this->assertDatabaseHas('two_factor_verifications', [
            'id' => $otp->id,
            'user_id' => $this->user->id,
            'invoice_id' => $invoice->id,
            'is_verified' => true,
        ]);
    }

    public function test_pay_invoice_with_more_daily_spending_limit()
    {
        Config::set('wallet.max_global_daily_spend', 150_000);

        $invoice = Invoice::factory()->create([
            'user_id' => $this->user->id,
            'amount' => 200_000,
        ]);
        $response = $this->postJson("/api/v1/invoice/{$invoice->id}/request-payment");

        $this->assertDatabaseCount('two_factor_verifications', 0);
        $this->assertEquals($response['message'], 'Could not process payment request.');
    }

    public function test_payment_fails_with_invalid_otp(): void
    {
        $invoice = Invoice::factory()->create([
            'user_id' => $this->user->id,
            'status' => InvoiceStatus::PENDING,
            'amount' => 4000,
        ]);

        $this->postJson("/api/v1/invoice/{$invoice->id}/request-payment")->assertOk();

        $this->assertDatabaseCount('two_factor_verifications', 1);

        $payResponse = $this->postJson('/api/v1/invoice/pay', [
            'invoice_id' => $invoice->id,
            'otp' => '000000',
        ]);

        $payResponse->assertStatus(422);
        $payResponse->assertJsonFragment(['message' => 'The provided OTP is invalid or has expired.']);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => InvoiceStatus::PENDING,
        ]);
        $this->assertDatabaseMissing('transactions', [
            'invoice_id' => $invoice->id,
        ]);
    }
}
