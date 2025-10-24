<?php

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')
                ->nullable()
                ->constrained('invoices')
                ->onDelete('cascade');
            $table->foreignId('wallet_id')
                ->constrained('wallets')
                ->onDelete('cascade');
            $table->enum('type', TransactionType::cases());
            $table->decimal('amount', 20, 2);
            $table->enum('status', TransactionStatus::cases())->default(TransactionStatus::PENDING);
            $table->uuid('reference_id')->unique();
            $table->json('metadata')->nullable();
            $table->text('rollback_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
