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
        Schema::create('attendance_times', function (Blueprint $table) {
            $table->id();
            // 項目名
            $table->string('name');
            // 始業
            $table->time('start_time');
            // 終業
            $table->time('end_time');
            // 備考
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_times');
    }
};
