<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waybill_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('waybill_transaction_id')
                ->constrained('waybill_transactions')
                ->cascadeOnDelete();

            $table->decimal('amount_paid', 10, 2)->default(0);

            $table->date('payment_date')->nullable();

            $table->enum('payment_method', [
                'G-Cash',
                'Bank Transfer',
                'Cash',
            ])->nullable();

            $table->string('reference_number')->nullable();

            $table->string('payment_proof_path')->nullable();

            $table->foreignId('received_by')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index('waybill_transaction_id');
            $table->index('received_by');
            $table->index('payment_method');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waybill_payments');
    }
};