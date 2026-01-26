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
        (2, 'Szerda'), 
        (2, 'Csütörtök'), 
        (2, 'Péntek'), 
        (3, 'Szombat'), 
        (3, 'Vasárnap')
        
        ";
        DB::statement($sql);
    }
}
