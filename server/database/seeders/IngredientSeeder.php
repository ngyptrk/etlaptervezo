<?php

namespace Database\Seeders;

use App\Helpers\CsvReader;
use App\Models\Ingredient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $fileName = 'csv/ingredients.csv';
        $delimeter = ';';
        $data = CsvReader::csvToArray($fileName,$delimeter);
        Ingredient::factory()->createMany($data);
    }
}