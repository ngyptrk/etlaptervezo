<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // FK kikapcsolás
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Child táblák törlése először
        DB::table('days')->truncate();
        DB::table('ingredients')->truncate();
        DB::table('recipes')->truncate();
        DB::table('meal_requirements')->truncate();
        DB::table('meal_of_days')->truncate();
        DB::table('meals')->truncate();
        DB::table('raw_ingredients')->truncate();
        DB::table('units')->truncate();
        DB::table('weekdays')->truncate();
        DB::table('users')->truncate();

        // FK visszakapcsolás
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Seederek helyes sorrendben
        $this->call([
            UserSeeder::class,
            UnitSeeder::class,
            RawIngredientSeeder::class,
            MealSeeder::class,
            WeekdaySeeder::class,
            MealOfDaySeeder::class,
            MealRequirementSeeder::class,
            RecipeSeeder::class,
            IngredientSeeder::class,
            DaySeeder::class,
        ]);
    }
}