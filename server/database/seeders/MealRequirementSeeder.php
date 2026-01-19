<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MealRequirementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $sql = "INSERT INTO meal_requirements (meal_of_days_id, meal_id) VALUES
        (1, 1), 
        (2, 2), 
        (2, 3), 
        (2, 4), 
        (2, 5), 
        (3, 4), 
        (3, 5)
        
        ";
        DB::statement($sql);

    }
}
