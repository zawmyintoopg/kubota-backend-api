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
        Schema::create('service_requests', function (Blueprint $table) {

                    $table->id();

                    $table->string('request_number', 50)->unique();

                    $table->foreignId('customer_id')
                        ->constrained('customers')
                        ->cascadeOnUpdate()
                        ->restrictOnDelete();

                    $table->string('service_type', 30);

                    $table->string('machine_model', 100);

                    $table->string('machine_serial_number', 100)->nullable();

                    $table->text('description');

                    $table->text('location')->nullable();

                    $table->date('preferred_date')->nullable();

                    $table->string('status', 30)->default('PENDING');

                    $table->foreignId('assigned_to')
                        ->nullable()
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
        Schema::dropIfExists('service_requests');
    }
};
