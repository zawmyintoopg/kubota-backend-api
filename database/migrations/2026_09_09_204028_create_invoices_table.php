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

            $table->string('invoice_number', 50)->unique();

            $table->unsignedBigInteger('vr_number')->unique();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->dateTime('invoice_date');

            $table->decimal('subtotal', 15, 2)->default(0);

            $table->decimal('discount_amount', 15, 2)->default(0);

            $table->decimal('total_amount', 15, 2)->default(0);

            $table->decimal('deposit_amount', 15, 2)->default(0);

            $table->decimal('paid_amount', 15, 2)->default(0);

            $table->decimal('balance_amount', 15, 2)->default(0);

            $table->string('status', 30)->default('UNPAID');

            $table->text('note')->nullable();

            $table->foreignId('created_by')
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
        Schema::dropIfExists('invoices');
    }
};
