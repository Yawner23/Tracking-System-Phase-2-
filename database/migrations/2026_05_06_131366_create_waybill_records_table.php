<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waybill_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('branch_id')
                ->constrained('branches')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('waybill_number')->unique();
            $table->string('reference_number')->nullable()->unique();

            $table->string('client_name');
            $table->string('client_contact_number')->nullable();

            $table->string('pos_receipt_number')->nullable()->index();
            $table->string('pos_tracking_number')->nullable()->index();

            $table->text('additional_information')->nullable();

            $table->string('current_status')
                ->default('Ready for Shipment')
                ->index();

            $table->enum('mode_of_payment', [
                'G-Cash',
                'Bank Transfer',
                'Cash',
            ])->nullable();

            $table->enum('payment_status', [
                'Unpaid',
                'Paid',
                'Partial',
                'Refund',
                'Cancelled',
            ])->default('Unpaid')->index();

            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('total_amount_paid', 10, 2)->default(0);
            $table->decimal('balance', 10, 2)->default(0);

            $table->timestamps();

            $table->index('branch_id');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waybill_records');
    }
};