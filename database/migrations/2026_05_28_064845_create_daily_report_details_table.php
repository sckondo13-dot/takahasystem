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
        Schema::create('daily_report_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('employee_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('subcontractor_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('work_type_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->decimal('man_hours', 4, 2)->default(1);
            $table->decimal('overtime_hours', 4, 2)->default(0);
            $table->integer('transportation_cost')->default(0);
            $table->integer('expressway_cost')->default(0);
            $table->integer('parking_cost')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_report_details');
    }
};
