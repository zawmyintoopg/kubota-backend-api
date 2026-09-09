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
        Schema::create('suppliers', function (Blueprint $table) {

                $table->id();

                $table->string('supplier_code', 50)->unique();

                $table->string('supplier_name', 200);

                $table->string('contact_person', 150)->nullable();

                $table->string('phone', 30)->nullable();

                $table->string('email', 150)->nullable();

                $table->text('address')->nullable();

                $table->string('status', 20)->default('ACTIVE');

                $table->timestamps();

                });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
