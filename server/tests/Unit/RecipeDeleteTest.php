<?php

namespace Tests\Unit;

use App\Http\Controllers\RecipeController;
use App\Models\Ingredient;
use App\Models\Meal;
use App\Models\RawIngredient;
use App\Models\Recipe;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_recipe_delete_removes_only_recipe_ingredients(): void
    {
        $meal = Meal::create(['meal' => 'Teszt etkezes']);
        $unit = Unit::create(['unit' => 'kg']);
        $rawIngredient = RawIngredient::create(['raw_ingredient' => 'Teszt alapanyag']);
        $recipe = Recipe::create([
            'name' => 'Torolheto recept',
            'description' => 'Teszt leiras',
            'picture' => 'Pictures/torolheto-recept.png',
            'person' => 2,
            'meal_id' => $meal->id,
        ]);

        $ingredient = Ingredient::create([
            'recipe_id' => $recipe->id,
            'raw_ingredient_id' => $rawIngredient->id,
            'amount' => 1,
            'unit_id' => $unit->id,
        ]);

        $response = app(RecipeController::class)->destroy($recipe->id);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertDatabaseMissing('ingredients', ['id' => $ingredient->id]);
        $this->assertDatabaseMissing('recipes', ['id' => $recipe->id]);
        $this->assertDatabaseHas('raw_ingredients', ['id' => $rawIngredient->id]);
    }
}
