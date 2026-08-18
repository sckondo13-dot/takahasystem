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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->string('postal_code')->nullable();

            $table->string('address')->nullable();

            $table->string('tel')->nullable();

            $table->string('fax')->nullable();

            $table->string('email')->nullable();

            $table->string('registration_number')->nullable();

            $table->string('representative')->nullable();

            $table->string('seal_image')->nullable();

            $table->string('bank_name')->nullable();

            $table->string('branch_name')->nullable();

            $table->string('account_type')->nullable();

            $table->string('account_number')->nullable();

            $table->string('account_name')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
