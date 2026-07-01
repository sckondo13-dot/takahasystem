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
        Schema::create('daily_report_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_id')
                ->constrained()
                ->cascadeOnDelete();

            // 機材・資材・その他
            $table->string('category')->default('貸出');

            // 名称
            $table->string('name');

            // 数量
            $table->decimal('quantity', 8, 2)->default(1);

            // 単位
            $table->string('unit')->nullable();

            // 単価（将来の請求用）
            $table->integer('unit_price')->default(0);

            // 金額
            $table->integer('amount')->default(0);

            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_report_items');
    }
};
