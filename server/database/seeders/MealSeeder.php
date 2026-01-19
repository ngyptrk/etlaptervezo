<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MealSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $sql = "INSERT INTO `meals` (`id`,`meal`) VALUES
        (1,'Reggeli'),
        (2,'Előétel'),
        (3,'Leves'),
        (4,'Főétel'),
        (5,'Desszert')
        ";
        DB::statement($sql);
    }
}
