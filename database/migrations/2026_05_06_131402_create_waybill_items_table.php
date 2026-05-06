<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waybill_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('waybill_record_id')
                ->constrained('waybill_records')
                ->cascadeOnDelete();

            $table->foreignId('service_id')
                ->constrained('services')
                ->restrictOnDelete();

            $table->string('shoe_brand');
            $table->string('colorway')->nullable();
            $table->string('item_status')->nullable();

            $table->decimal('price', 10, 2)->default(0);

            $table->timestamps();

            $table->index('waybill_record_id');
            $table->index('service_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waybill_items');
    }
};