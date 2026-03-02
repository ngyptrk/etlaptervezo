<?php

namespace App\Http\Controllers;

use App\Models\Day;
use App\Models\Recipe;
use App\Models\Weekday;
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
            // Régi heti terv törlése
            Day::where('user_id', $user->id)->delete();

            // Lekérjük a hét napjait (1 = Hétfő, 7 = Vasárnap)
            $weekdays = Weekday::all();

            foreach ($weekdays as $weekday) {

                // ===== REGGELI =====
                $breakfast = Recipe::whereBetween('id', [1, 10])
                    ->inRandomOrder()
                    ->first();

                Day::create([
                    'user_id' => $user->id,
                    'weekday_id' => $weekday->id,
                    'recipe_id' => $breakfast->id,
                    'meal_requirement_id' => 1, // reggeli
                ]);

                // ===== EBÉD =====
                $starter = Recipe::whereBetween('id', [11, 20])->inRandomOrder()->first();
                Day::create([
                    'user_id' => $user->id,
                    'weekday_id' => $weekday->id,
                    'recipe_id' => $starter->id,
                    'meal_requirement_id' => 2, // ebéd előétel
                ]);

                $soup = Recipe::whereBetween('id', [21, 30])->inRandomOrder()->first();
                Day::create([
                    'user_id' => $user->id,
                    'weekday_id' => $weekday->id,
                    'recipe_id' => $soup->id,
                    'meal_requirement_id' => 3, // ebéd leves
                ]);

                $mainLunch = Recipe::whereBetween('id', [31, 40])->inRandomOrder()->first();
                Day::create([
                    'user_id' => $user->id,
                    'weekday_id' => $weekday->id,
                    'recipe_id' => $mainLunch->id,
                    'meal_requirement_id' => 4, // ebéd főétel
                ]);

                $dessertLunch = Recipe::whereBetween('id', [41, 50])->inRandomOrder()->first();
                Day::create([
                    'user_id' => $user->id,
                    'weekday_id' => $weekday->id,
                    'recipe_id' => $dessertLunch->id,
                    'meal_requirement_id' => 5, // ebéd desszert
                ]);

                // ===== VACSORA =====
                $mainDinner = Recipe::whereBetween('id', [31, 40])->inRandomOrder()->first();
                Day::create([
                    'user_id' => $user->id,
                    'weekday_id' => $weekday->id,
                    'recipe_id' => $mainDinner->id,
                    'meal_requirement_id' => 6, // vacsora főétel
                ]);

                $dessertDinner = Recipe::whereBetween('id', [41, 50])->inRandomOrder()->first();
                Day::create([
                    'user_id' => $user->id,
                    'weekday_id' => $weekday->id,
                    'recipe_id' => $dessertDinner->id,
                    'meal_requirement_id' => 7, // vacsora desszert
                ]);
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