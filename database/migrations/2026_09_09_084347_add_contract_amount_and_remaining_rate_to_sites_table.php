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
        Schema::table('sites', function (Blueprint $table) {
            $table->decimal('contract_amount', 12, 2)
                ->default(0)
                ->after('contract_end');

            $table->decimal('remaining_rate', 5, 2)
                ->default(100)
                ->after('contract_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn([
                'contract_amount',
                'remaining_rate',
            ]);
        });
    }
};
