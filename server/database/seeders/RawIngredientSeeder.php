<?php

namespace Database\Seeders;

use App\Helpers\CsvReader;
use App\Models\RawIngredient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RawIngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
        $fileName = 'csv/raw_ingredients.csv';
        $delimeter = ';';
        $data = CsvReader::csvToArray($fileName,$delimeter);
        RawIngredient::factory()->createMany($data);
    }

    
}

