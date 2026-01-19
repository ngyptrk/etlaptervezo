<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MealOfDaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $sql = "INSERT INTO `meal_of_days` (`meal_of_day`) VALUES
        ('Reggeli'),
        ('Ebéd'),
        ('Vacsora')
        ";
        DB::statement($sql);
    }
}
