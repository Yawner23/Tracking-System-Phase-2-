<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('returned_backjob_waybills', function (Blueprint $table) {
            $table->id();

            $table->foreignId('waybill_record_id')
                ->unique()
                ->constrained('waybill_records')
                ->cascadeOnDelete();

            $table->foreignId('backjob_waybill_id')
                ->constrained('backjob_waybills')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->index('backjob_waybill_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('returned_backjob_waybills');
    }
};