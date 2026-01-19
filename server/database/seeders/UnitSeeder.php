<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $sql = "INSERT INTO `units` (`unit`) VALUES
        ('g'),
        ('dkg'),
        ('kg'),
        ('ml'),
        ('dl'),
        ('l'),
        ('tk'),
        ('ek'),
        ('csipet'),
        ('db'),
        ('csésze'),
        ('bögre'),
        ('szelet'),
        ('fej'),
        ('gerezd');
        
    ";
        DB::statement($sql);

    }
}
