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
        Schema::create('purchase_items', function (Blueprint $table) {

            $table->id();
            $table->integer('purchase_id');
            $table->integer('product_id');
            $table->string('product_name', 200);
            $table->string('part_number', 100);
            $table->integer('quantity');
            $table->decimal('unit_cost', 15, 2);
            $table->decimal('amount', 15, 2);
            $table->timestamps();

            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
