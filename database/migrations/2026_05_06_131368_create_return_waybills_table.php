<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_waybills', function (Blueprint $table) {
            $table->id();

            $table->foreignId('waybill_record_id')
                ->unique()
                ->constrained('waybill_records')
                ->cascadeOnDelete();

            $table->foreignId('waybill_id')
                ->constrained('waybills')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->index('waybill_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_waybills');
    }
};