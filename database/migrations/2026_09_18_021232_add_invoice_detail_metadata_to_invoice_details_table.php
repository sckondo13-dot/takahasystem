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
            $table->string('source_type')
                ->default('manual')
                ->after('amount');

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
            $table->dropColumn([
                'source_type',
                'progress_rate',
                'remaining_rate',
            ]);
        });
    }
};
