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
        Schema::create('orders', function (Blueprint $table) {

                $table->id();

                $table->string('order_number', 50)->unique();

                $table->foreignId('customer_id')
                    ->constrained('customers')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();

                $table->dateTime('order_date');

                $table->string('status', 30)->default('PENDING');

                $table->decimal('subtotal', 15, 2)->default(0);

                $table->decimal('discount_amount', 15, 2)->default(0);

                $table->decimal('total_amount', 15, 2)->default(0);

                $table->text('note')->nullable();

                $table->timestamps();

                });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products_orders');
    }
};
