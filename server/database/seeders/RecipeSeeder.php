<?php

namespace Database\Seeders;

use App\Helpers\CsvReader;
use App\Models\Recipe;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fileName = 'csv/recipes.csv';
        $delimeter = ';';
        $data = CsvReader::csvToArray($fileName,$delimeter);
        Recipe::factory()->createMany($data);
    }
}