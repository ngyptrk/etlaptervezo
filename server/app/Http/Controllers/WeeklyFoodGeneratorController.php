<?php

namespace App\Http\Controllers;

use App\Models\Day;
use App\Models\Recipe;
use App\Models\Weekday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class WeeklyFoodGeneratorController extends Controller
{
    public function generate(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Nincs bejelentkezve'], 401);
        }

        $validated = $request->validate([
            'mode' => ['nullable', 'in:days,weeks'],
            'amount' => ['nullable', 'integer', 'min:1', 'max:84'],
        ]);

        $mode = $validated['mode'] ?? 'weeks';
        $amount = (int) ($validated['amount'] ?? 1);

        $totalDays = $mode === 'days' ? $amount : $amount * 7;
        $weekCount = (int) ceil($totalDays / 7);
        $hasPlanWeek = Schema::hasColumn('days', 'plan_week');

        DB::beginTransaction();

        try {
            Day::where('user_id', $user->id)->delete();

            $weekdays = Weekday::orderBy('id')->get();
            $createdRows = 0;

            for ($week = 1; $week <= $weekCount; $week++) {
                if (!$hasPlanWeek && $week > 1) {
                    break;
                }

                $daysLeft = $totalDays - (($week - 1) * 7);
                $daysForThisWeek = min(7, $daysLeft);
                $selectedWeekdays = $weekdays->take($daysForThisWeek);

                foreach ($selectedWeekdays as $weekday) {
                    $createdRows += $this->createDayMealsForWeekday(
                        $user->id,
                        $weekday->id,
                        $week,
                        $hasPlanWeek
                    );
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Étrend sikeresen generálva.',
                'data' => [
                    'mode' => $mode,
                    'amount' => $amount,
                    'total_days' => $totalDays,
                    'generated_weeks' => $hasPlanWeek ? $weekCount : 1,
                    'generated_rows' => $createdRows,
                    'multi_week_enabled' => $hasPlanWeek,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Hiba történt',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function myPlan(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Nincs bejelentkezve'], 401);
        }

        $hasPlanWeek = Schema::hasColumn('days', 'plan_week');
        $week = (int) $request->query('week', 0);

        $query = Day::query()
            ->where('user_id', $user->id)
            ->with([
                'weekday:id,day',
                'recipe:id,name,description,picture,person,meal_id',
                'recipe.meal:id,meal',
                'mealRequirement:id,meal_of_day_id,meal_id',
                'mealRequirement.mealOfDay:id,meal_of_day',
                'mealRequirement.meal:id,meal',
            ])
            ->orderBy('weekday_id')
            ->orderBy('meal_requirement_id');

        if ($hasPlanWeek) {
            $query->orderBy('plan_week');
            if ($week > 0) {
                $query->where('plan_week', $week);
            }
        }

        $rows = $query->get()->map(function ($row) use ($hasPlanWeek) {
            if (!$hasPlanWeek) {
                $row->plan_week = 1;
            }
            return $row;
        });

        $weeks = $hasPlanWeek
            ? (Day::where('user_id', $user->id)->max('plan_week') ?? 0)
            : ($rows->isEmpty() ? 0 : 1);

        return response()->json([
            'message' => 'OK',
            'data' => $rows,
            'meta' => [
                'weeks' => $weeks,
                'selected_week' => $week,
                'multi_week_enabled' => $hasPlanWeek,
            ],
        ]);
    }

    private function createDayMealsForWeekday(int $userId, int $weekdayId, int $week, bool $hasPlanWeek): int
    {
        $pairs = [
            1 => [1, 10],
            2 => [11, 20],
            3 => [21, 30],
            4 => [31, 40],
            5 => [41, 50],
            6 => [31, 40],
            7 => [41, 50],
        ];

        $created = 0;

        foreach ($pairs as $mealRequirementId => [$minId, $maxId]) {
            $recipe = Recipe::whereBetween('id', [$minId, $maxId])
                ->inRandomOrder()
                ->first();

            if (!$recipe) {
                continue;
            }

            $payload = [
                'user_id' => $userId,
                'weekday_id' => $weekdayId,
                'recipe_id' => $recipe->id,
                'meal_requirement_id' => $mealRequirementId,
            ];

            if ($hasPlanWeek) {
                $payload['plan_week'] = $week;
            }

            Day::create($payload);
            $created++;
        }

        return $created;
    }
}
