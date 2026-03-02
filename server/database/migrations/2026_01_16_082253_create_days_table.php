<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('days', function (Blueprint $table) {
            $table->id();

            $table->foreignId('weekday_id')
                ->constrained('weekdays')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('recipe_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('meal_requirement_id')
                ->constrained('meal_requirements')
                ->cascadeOnDelete();

            $table->timestamps();

            // Egyedi kombináció
            $table->unique(
                ['user_id', 'weekday_id', 'meal_requirement_id'],
                'days_user_weekday_mealreq_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('days');
    }
};
