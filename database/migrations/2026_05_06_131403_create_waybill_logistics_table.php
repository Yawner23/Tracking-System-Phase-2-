<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waybill_logistics', function (Blueprint $table) {
            $table->id();

            $table->foreignId('waybill_record_id')
                ->unique()
                ->constrained('waybill_records')
                ->cascadeOnDelete();

            $table->enum('logistics_type', [
                'impeccable_logistics_system',
                'third_party_logistics',
            ])->default('impeccable_logistics_system');

            $table->string('third_party_provider')->nullable();
            $table->string('third_party_waybill_number')->nullable();

            $table->foreignId('logistics_accepted_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('logistics_accepted_at')->nullable();

            $table->foreignId('main_hub_accepted_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('main_hub_accepted_at')->nullable();

            $table->timestamps();

            $table->index('logistics_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waybill_logistics');
    }
};