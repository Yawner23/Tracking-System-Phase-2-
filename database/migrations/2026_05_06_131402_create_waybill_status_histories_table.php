<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waybill_status_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('waybill_record_id')
                ->constrained('waybill_records')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('status');
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index(['waybill_record_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waybill_status_histories');
    }
};