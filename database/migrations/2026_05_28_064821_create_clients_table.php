<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('demolition_unit_price')->default(0)->comment('解体工単価');
            $table->integer('heavy_equipment_unit_price')->default(0)->comment('重機単価');
            $table->integer('heavy_equipment2_unit_price')->default(0)->comment('重機2単価');
            $table->integer('chipping_unit_price')->default(0)->comment('はつり単価');
            $table->integer('asbestos_unit_price')->default(0)->comment('石綿単価');
            $table->integer('truck_unit_price')->default(0)->comment('トラック単価');
            $table->integer('unic_unit_price')->default(0)->comment('ユニック単価');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
