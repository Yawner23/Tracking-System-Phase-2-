<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waybill_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('waybill_record_id')
                ->constrained('waybill_records')
                ->cascadeOnDelete();

            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('adjustment', 10, 2)->default(0);
            $table->decimal('refund_amount', 10, 2)->default(0);

            $table->enum('payment_status', [
                'Unpaid',
                'Paid',
                'Partial',
                'Refund',
                'Cancelled',
            ])->default('Unpaid')->index();

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index('waybill_record_id');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waybill_transactions');
    }
};