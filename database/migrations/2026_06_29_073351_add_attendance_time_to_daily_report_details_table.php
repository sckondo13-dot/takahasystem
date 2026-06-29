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
        Schema::table('daily_report_details', function (Blueprint $table) {
            $table->string('attendance_time_name')
                ->nullable()
                ->after('work_type_id');

            $table->time('start_time')
                ->nullable()
                ->after('attendance_time_name');

            $table->time('end_time')
                ->nullable()
                ->after('start_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_report_details', function (Blueprint $table) {
            $table->dropColumn([
                'attendance_time_name',
                'start_time',
                'end_time',
            ]);
        });
    }
};
