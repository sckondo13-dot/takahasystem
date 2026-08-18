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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('site_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('invoice_no')->unique();

            $table->enum('invoice_type', [
                'normal',
                'combined',
            ])->default('normal');

            $table->string('title');

            $table->date('invoice_date');

            $table->date('payment_due');

            $table->decimal('subtotal', 12, 2)->default(0);

            $table->decimal('tax', 12, 2)->default(0);

            $table->decimal('total', 12, 2)->default(0);

            $table->text('remarks')->nullable();

            $table->enum('status', [
                'draft',
                'issued',
                'paid'
            ])->default('draft');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
