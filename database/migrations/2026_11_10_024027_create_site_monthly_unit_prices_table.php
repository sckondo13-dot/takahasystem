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
        Schema::create('site_monthly_unit_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('work_type_id')
                ->constrained()
                ->cascadeOnDelete();

            // 対象月（月初の日付で保存）
            $table->date('price_month');

            // 単価
            $table->unsignedInteger('unit_price')->default(0);
            $table->timestamps();
            $table->unique(
                ['site_id', 'work_type_id', 'price_month'],
                'site_monthly_unit_prices_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_monthly_unit_prices');
    }
};
