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
        Schema::create('number_sequences', function (Blueprint $table) {

            $table->id();

            $table->string('sequence_type', 50)->unique();

            $table->string('prefix', 20);

            $table->unsignedBigInteger('current_number')->default(0);

            $table->integer('number_length')->default(5);

            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('number_sequences');
    }
};
