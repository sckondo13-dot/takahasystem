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
        Schema::create('allowance_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('allowance_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('amount');

            $table->date('start_date');

            $table->date('end_date')
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allowance_histories');
    }
};
