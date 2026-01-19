<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        //Mielőtt seedelünk, minden táblát töröljünk le.
        DB::statement('DELETE FROM users');
        DB::statement('DELETE FROM units');
        DB::statement('DELETE FROM raw_ingredients');
        DB::statement('DELETE FROM meals');
        DB::statement('DELETE FROM meal_of_days');
        DB::statement('DELETE FROM meal_requirements');



        //Ami Seeder osztály itt fel van sorolva, annak lefut a run() metódusa
        $this->call([
            UserSeeder::class,
            UnitSeeder::class,
            RawIngredientSeeder::class,
            MealSeeder::class,
            MealOfDaySeeder::class,
            MealRequirementSeeder::class,
        ]);
    }
}
