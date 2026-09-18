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
        Schema::table('invoice_details', function (Blueprint $table) {
            $table->foreignId('site_id')
                ->nullable()
                ->after('invoice_id')
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('work_type_id')
                ->nullable()
                ->after('site_id')
                ->constrained()
                ->nullOnDelete();

            $table->string('source_type')
                ->default('manual')
                ->after('work_type_id');

            $table->decimal('progress_rate', 5, 2)
                ->nullable()
                ->after('source_type');

            $table->decimal('remaining_rate', 5, 2)
                ->nullable()
                ->after('progress_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_details', function (Blueprint $table) {
            $table->dropForeign(['site_id']);
            $table->dropForeign(['work_type_id']);

            $table->dropColumn([
                'site_id',
                'work_type_id',
                'source_type',
                'progress_rate',
                'remaining_rate',
            ]);
        });
    }
};
