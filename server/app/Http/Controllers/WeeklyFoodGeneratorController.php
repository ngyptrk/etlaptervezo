<?php

namespace App\Http\Controllers;

use App\Mail\WeeklyPlanMail;
use App\Models\Day;
use App\Models\Weekday;
use App\Models\Recipe;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
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
                'message' => 'Etrend sikeresen generalva.',
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
                'message' => 'Hiba tortent',
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

        $rows = $this->loadPlanRows($user->id, $hasPlanWeek, $week);
        $weeks = $this->resolveWeeksCount($user->id, $hasPlanWeek, $rows);

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

    public function sendEmail(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Nincs bejelentkezve'], 401);
        }

        $validated = $request->validate([
            'email' => ['required', 'email'],
            'week' => ['nullable', 'integer', 'min:1'],
        ]);

        $hasPlanWeek = Schema::hasColumn('days', 'plan_week');
        $week = (int) ($validated['week'] ?? 0);

        $rows = $this->loadPlanRows($user->id, $hasPlanWeek, $week);
        if ($rows->isEmpty()) {
            return response()->json([
                'message' => 'Nincs kikuldheto etrended.',
            ], 422);
        }

        $mailer = config('mail.default');
        if (in_array($mailer, ['log', 'array'], true)) {
            return response()->json([
                'message' => 'Az email kuldes nincs valodi SMTP-re allitva (MAIL_MAILER=log/array). Allitsd SMTP-re a .env-ben.',
            ], 422);
        }

        $shoppingList = $this->buildShoppingList($rows);
        $pdf = Pdf::loadView('pdf.weekly-plan', [
            'user' => $user,
            'rows' => $rows,
            'shoppingList' => $shoppingList,
            'selectedWeek' => $week,
            'appUrl' => config('app.url'),
        ]);
        $pdfBinary = $pdf->output();

        try {
            Mail::to($validated['email'])->send(
                new WeeklyPlanMail($user, $rows, $shoppingList, $pdfBinary, $week)
            );
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Sikertelen email kuldes. Ellenorizd az SMTP beallitasokat.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }

        return response()->json([
            'message' => 'Email sikeresen elkuldve.',
            'data' => [
                'email' => $validated['email'],
                'rows' => $rows->count(),
            ],
        ]);
    }

    private function loadPlanRows(int $userId, bool $hasPlanWeek, int $week = 0)
    {
        $query = Day::query()
            ->where('user_id', $userId)
            ->with([
                'weekday:id,day',
                'recipe:id,name,description,picture,person,meal_id',
                'recipe.meal:id,meal',
                'recipe.ingredients:id,recipe_id,raw_ingredient_id,amount,unit_id',
                'recipe.ingredients.rawIngredient:id,raw_ingredient',
                'recipe.ingredients.unit:id,unit',
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

        return $query->get()->map(function ($row) use ($hasPlanWeek) {
            if (!$hasPlanWeek) {
                $row->plan_week = 1;
            }
            return $row;
        });
    }

    private function resolveWeeksCount(int $userId, bool $hasPlanWeek, $rows): int
    {
        return $hasPlanWeek
            ? (Day::where('user_id', $userId)->max('plan_week') ?? 0)
            : ($rows->isEmpty() ? 0 : 1);
    }

    private function buildShoppingList($rows)
    {
        $map = [];

        foreach ($rows as $row) {
            $ingredients = $row->recipe?->ingredients ?? [];
            foreach ($ingredients as $ingredient) {
                $rawName = $ingredient->rawIngredient?->raw_ingredient ?? 'Ismeretlen';
                $unitName = $ingredient->unit?->unit ?? '';
                $key = $rawName . '|' . $unitName;

                if (!isset($map[$key])) {
                    $map[$key] = [
                        'name' => $rawName,
                        'unit' => $unitName,
                        'amount' => 0,
                    ];
                }

                $map[$key]['amount'] += (int) $ingredient->amount;
            }
        }

        return collect(array_values($map))->sortBy('name')->values();
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
