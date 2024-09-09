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
        Schema::create('service_providers', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Foreign Key to users table
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade'); // Foreign Key to services table
            $table->string('hourly_rate');
            $table->string('experience')->nullable();
            $table->string('location')->nullable(); // Location where the service is offered
            $table->text('description'); // Provider’s personalized description of himself and the service
            $table->timestamps(); // created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_providers');
    }
};
