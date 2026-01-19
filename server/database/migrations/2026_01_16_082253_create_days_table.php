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
        Schema::create('days', function (Blueprint $table) {
            $table->id();
            $table->string('day', 15);
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('meal_of_days_id')->constrained('meal_of_days')->restrictOnDelete();
            $table->foreignId('recipe_id')->constrained('recipes')->restrictOnDelete();
            $table->foreignId('meal_id')->constrained('meals')->restrictOnDelete();

            $table->unique([
                'user_id',
                'day',
                'meal_of_days_id',
                'recipe_id'
            ]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('days');
    }
};
