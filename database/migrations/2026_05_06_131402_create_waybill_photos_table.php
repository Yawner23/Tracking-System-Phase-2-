<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waybill_photos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('waybill_record_id')
                ->constrained('waybill_records')
                ->cascadeOnDelete();

            $table->string('type')->nullable();
            $table->string('file_path');

            $table->foreignId('uploaded_by')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();

            $table->index('waybill_record_id');
            $table->index('uploaded_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waybill_photos');
    }
};