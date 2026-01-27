<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sql = "INSERT INTO `days` (`day_id`, `user_id`, `meal_of_days_id`, `recipe_id`,`meal_id`) VALUES
        (1, 1 , 1 , 2, 1),
        (1, 1 , 2 , 11, 2),
        (1, 1 , 2 , 23, 3),
        (1, 1 , 2 , 31, 4),
        (1, 1 , 2 , 41, 5),
        (1, 1 , 3 , 31, 5),
        (1, 1 , 3 , 41, 5)
        ";
        DB::statement($sql);
    }
}
