<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Day;
use App\Models\Recipe;
use App\Models\Weekday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WeeklyFoodGeneratorController extends Controller
{
    public function generate()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Nincs bejelentkezve'], 401);
        }

        DB::beginTransaction();

        try {

            // 🔹 Régi terv törlése
            Day::where('user_id', $user->id)->delete();

            $weekdays = Weekday::all();

            // Meal names mapping
            $mealNames = [
                1 => 'Reggeli',
                2 => 'Előétel',
                3 => 'Leves',
                4 => 'Főétel',
                5 => 'Desszert',
            ];

            $response = [];

            foreach ($weekdays as $weekday) {

                $dayMeals = [];

                // 1️⃣ Reggeli
                $breakfast = Recipe::where('meal_id', 1)
                    ->inRandomOrder()
                    ->first();
                if (!$breakfast) {
                    throw new \Exception("Nincs elég Reggeli recept");
                }

                $dayMeals['Reggeli'][] = $breakfast;

                Day::create([
                    'user_id' => $user->id,
                    'day_id' => $weekday->id,
                    'recipe_id' => $breakfast->id,
                    'meal_id' => 1,
                ]);

                // 2️⃣ Ebéd (2–5 = Előétel, Leves, Főétel, Desszert)
                $lunchRecipes = [];
                for ($mealId = 2; $mealId <= 5; $mealId++) {

                    $recipe = Recipe::where('meal_id', $mealId)
                        ->inRandomOrder()
                        ->first();

                    if (!$recipe) {
                        throw new \Exception("Nincs elég recept a meal_id: {$mealId} kategóriában");
                    }

                    $lunchRecipes[$mealId] = $recipe;

                    Day::create([
                        'user_id' => $user->id,
                        'day_id' => $weekday->id,
                        'recipe_id' => $recipe->id,
                        'meal_id' => $mealId,
                    ]);
                }

                $dayMeals['Ebéd'] = $lunchRecipes;

                // 3️⃣ Vacsora = ugyanaz, mint ebéd
                $dayMeals['Vacsora'] = $lunchRecipes;
                foreach ($lunchRecipes as $mealId => $recipe) {
                    Day::create([
                        'user_id' => $user->id,
                        'day_id' => $weekday->id,
                        'recipe_id' => $recipe->id,
                        'meal_id' => $mealId,
                    ]);
                }

                // 🔹 JSON válaszhoz
                $dayResponse = [];
                foreach ($dayMeals as $mealTime => $recipes) {
                    $dayResponse[$mealTime] = [];
                    foreach ($recipes as $mealId => $recipe) {
                        $dayResponse[$mealTime][] = [
                            'meal_type' => $mealNames[$recipe->meal_id] ?? 'Ismeretlen',
                            'recipe_name' => $recipe->name ?? 'Nincs név',
                        ];
                    }
                }

                $response[$weekday->name] = $dayResponse;
            }

            DB::commit();

            return response()->json([
                'user_id' => $user->id,
                'weekly_plan' => $response,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Hiba történt',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
