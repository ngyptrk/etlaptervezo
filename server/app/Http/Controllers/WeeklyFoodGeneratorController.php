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
        $usedRecipes = [];
        $user = Auth::user();


        if (!$user) {
            return response()->json(['message' => 'Nincs bejelentkezve'], 401);
        }

        DB::beginTransaction();

        try {

            // Régi heti terv törlése (ha csak 1 lehet)
            Day::where('user_id', $user->id)->delete();

            // Lekérjük a hét napjait (Hétfő–Vasárnap)
            $weekdays = Weekday::all();

            foreach ($weekdays as $weekday) {

                // Az 5 kötelező meal_id
                $mealTypes = [1, 2, 3, 4, 5];

                foreach ($mealTypes as $mealId) {

                    $recipe = Recipe::where('meal_id', $mealId)
                        ->inRandomOrder()
                        ->first();

                    if (!$recipe) {
                        throw new \Exception("Nincs recept a meal_id: {$mealId} kategóriában");
                    }

                    Day::create([
                        'user_id' => $user->id,
                        'day_id' => $weekday->id,
                        'recipe_id' => $recipe->id,
                        'meal_id' => $mealId,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Heti étrend sikeresen generálva'
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
