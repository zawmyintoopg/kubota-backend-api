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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');      // products.id (FK မချိတ // optional            
            $table->string('variant_name');
            $table->integer('unit_id');    // units.id
            $table->decimal('last_purchase_price',10,2)->default(0);
            $table->decimal('avg_purchase_price',10,2)->default(0);
            $table->date('last_purchase_date');
            $table->decimal('sell_price',10,2)->default(0);
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
