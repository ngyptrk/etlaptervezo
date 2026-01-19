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
        Schema::create('meal_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meal_of_days_id')->constrained('meal_of_days')->restrictOnDelete();
            $table->foreignId('meal_id')->constrained('meals')->restrictOnDelete();
            $table->unique(['meal_of_days_id', 'meal_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_requirements');
    }
};
