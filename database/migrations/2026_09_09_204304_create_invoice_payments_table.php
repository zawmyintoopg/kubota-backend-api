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
        Schema::create('invoice_payments', function (Blueprint $table) {

            $table->id();

            $table->foreignId('invoice_id')
                ->constrained('invoices')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('payment_number', 50)->unique();

            $table->dateTime('payment_date');

            $table->decimal('amount', 15, 2);

            $table->string('payment_method', 30);

            $table->string('reference_number', 100)->nullable();

            $table->text('note')->nullable();

            $table->foreignId('received_by')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();

            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_payments');
    }
};
