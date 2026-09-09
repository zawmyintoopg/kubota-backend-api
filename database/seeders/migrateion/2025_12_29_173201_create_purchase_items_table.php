<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id');   // purchase.id
           // $table->unsignedBigInteger('product_id');    // product.id
            $table->unsignedBigInteger('product_variant_id');    // product_variant.id
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('quantity', 12, 2)->default(0);
            $table->decimal('unit_price', 12, 2)->default(0); // purchase price per variant
            $table->decimal('total_price', 12, 2)->default(0); // quantity * unit_price

            $table->timestamps();

            // Optional: no foreign keys as requested
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
