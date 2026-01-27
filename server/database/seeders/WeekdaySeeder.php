<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WeekdaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //ű
         $sql = "INSERT INTO `weekdays` (`id`,`day`) VALUES
        (1, 'Hétfő'), 
        (2, 'Kedd'), 
        (3, 'Szerda'), 
        (4, 'Csütörtök'), 
        (5, 'Péntek'), 
        (6, 'Szombat'), 
        (7, 'Vasárnap')
        
        ";
        DB::statement($sql);
    }
}
