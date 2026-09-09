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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code');
            $table->string('part_number');
            $table->string('name');
            $table->text('description');
            $table->integer('category_id');
            $table->integer('brand_id');
            $table->decimal('price');
            $table->integer('stock_quantity');
            $table->integer('low_stock_threshold');
            $table->boolean('featured');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
