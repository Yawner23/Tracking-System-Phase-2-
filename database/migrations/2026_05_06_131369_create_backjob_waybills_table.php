<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backjob_waybills', function (Blueprint $table) {
            $table->id();

            $table->foreignId('waybill_record_id')
                ->unique()
                ->constrained('waybill_records')
                ->cascadeOnDelete();

            $table->foreignId('return_waybill_id')
                ->constrained('return_waybills')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->index('return_waybill_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backjob_waybills');
    }
};