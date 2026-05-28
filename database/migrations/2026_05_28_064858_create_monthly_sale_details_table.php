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
        Schema::create('monthly_sale_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monthly_sale_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('site_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('work_type_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->decimal('quantity', 6, 2);
            $table->integer('unit_price');
            $table->integer('subtotal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_sale_details');
    }
};
