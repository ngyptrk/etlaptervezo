<?php


use App\Http\Controllers\UserController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\DayController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\MealOfDayController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\RawIngredientController;
use App\Http\Controllers\MealRequirementController;
use App\Http\Controllers\WeekdayController;
use App\Http\Controllers\WeeklyFoodGeneratorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

//endpoint
Route::get('/x', function () {
    return 'API';
});


//region users
//User kezelés, login, logout
//Mindenki
Route::post('users/login', [UserController::class, 'login']);
Route::post('users/logout', [UserController::class, 'logout']);
Route::post('users', [UserController::class, 'store']);

//Admin: 
//minden user lekérdezése
Route::get('users', [UserController::class, 'index'])
    ->middleware('auth:sanctum', 'ability:admin');
//Egy user lekérése    
Route::get('users/{id}', [UserController::class, 'show'])
    ->middleware('auth:sanctum', 'ability:admin');
//User adatok módosítása      
Route::patch('users/{id}', [UserController::class, 'update'])
    ->middleware('auth:sanctum', 'ability:admin');
//User törlés
Route::delete('users/{id}', [UserController::class, 'destroy'])
    ->middleware('auth:sanctum', 'ability:admin');

//User self (Amit a user önmagával csinálhat) parancsok
Route::delete('usersme', [UserController::class, 'destroySelf'])
    ->middleware('auth:sanctum', 'ability:usersme:delete');

Route::patch('usersme', [UserController::class, 'updateSelf'])
    ->middleware('auth:sanctum', 'ability:usersme:patch');

Route::patch('usersmeupdatepassword', [UserController::class, 'updatePassword'])
    ->middleware('auth:sanctum', 'ability:usersme:updatePassword');

Route::get('usersme', [UserController::class, 'indexSelf'])
    ->middleware('auth:sanctum', 'ability:usersme:get');
//endregion

//region datas

Route::get('units', [UnitController::class, 'index']);
Route::get('units/{id}', [UnitController::class, 'show']);
Route::post('units', [UnitController::class, 'store'])
    ->middleware(['auth:sanctum', 'ability:units:post']); //Abilitity probaljuk meg h megadjuk neki az admint es a felhasznalot
Route::patch('units/{id}', [UnitController::class, 'update'])
    ->middleware(['auth:sanctum', 'ability:units:patch']);
Route::delete('units/{id}', [UnitController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'ability:units:delete']);

Route::get('days', [DayController::class, 'index']);
Route::get('days/{id}', [DayController::class, 'show']);
Route::post('days', [DayController::class, 'store'])
    ->middleware(['auth:sanctum', 'ability:days:post']);
Route::patch('days/{id}', [DayController::class, 'update'])
    ->middleware(['auth:sanctum', 'ability:days:patch']);
Route::delete('days/{id}', [DayController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'ability:days:delete']);

Route::get('meals', [MealController::class, 'index']);
Route::get('meals/{id}', [MealController::class, 'show']);
Route::post('meals', [MealController::class, 'store'])
    ->middleware(['auth:sanctum', 'ability:meals:post']);
Route::patch('meals/{id}', [MealController::class, 'update'])
    ->middleware(['auth:sanctum', 'ability:meals:patch']);
Route::delete('meals/{id}', [MealController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'ability:meals:delete']);

Route::get('ingredients', [IngredientController::class, 'index']);
Route::get('ingredients/{id}', [IngredientController::class, 'show']);
Route::post('ingredients', [IngredientController::class, 'store'])
    ->middleware(['auth:sanctum', 'ability:ingredients:post']);
Route::patch('ingredients/{id}', [IngredientController::class, 'update'])
    ->middleware(['auth:sanctum', 'ability:ingredients:patch']);
Route::delete('ingredients/{id}', [IngredientController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'ability:ingredients:delete']);

Route::get('mealofdays', [MealOfDayController::class, 'index']);
Route::get('mealofdays/{id}', [MealOfDayController::class, 'show']);
Route::post('mealofdays', [MealOfDayController::class, 'store'])
    ->middleware(['auth:sanctum', 'ability:mealofdays:post']);
Route::patch('mealofdays/{id}', [MealOfDayController::class, 'update'])
    ->middleware(['auth:sanctum', 'ability:mealofdays:patch']);
Route::delete('mealofdays/{id}', [MealOfDayController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'ability:mealofdays:delete']);

Route::get('mealrequirements', [MealRequirementController::class, 'index']);
Route::get('mealrequirements/{id}', [MealRequirementController::class, 'show']);
Route::post('mealrequirements', [MealRequirementController::class, 'store'])
    ->middleware(['auth:sanctum', 'ability:mealrequirements:post']);
Route::patch('mealrequirements/{id}', [MealRequirementController::class, 'update'])
    ->middleware(['auth:sanctum', 'ability:mealrequirements:patch']);
Route::delete('mealrequirements/{id}', [MealRequirementController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'ability:mealrequirements:delete']);

Route::get('rawingredients', [RawIngredientController::class, 'index']);
Route::get('rawingredients/{id}', [RawIngredientController::class, 'show']);
Route::post('rawingredients', [RawIngredientController::class, 'store'])
    ->middleware(['auth:sanctum', 'ability:rawingredients:post']);
Route::patch('rawingredients/{id}', [RawIngredientController::class, 'update'])
    ->middleware(['auth:sanctum', 'ability:rawingredients:patch']);
Route::delete('rawingredients/{id}', [RawIngredientController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'ability:rawingredients:delete']);

Route::get('recipes', [RecipeController::class, 'index']);
Route::get('recipes/{id}', [RecipeController::class, 'show']);
Route::post('recipes', [RecipeController::class, 'store'])
    ->middleware(['auth:sanctum', 'ability:recipes:post']);
Route::patch('recipes/{id}', [RecipeController::class, 'update'])
    ->middleware(['auth:sanctum', 'ability:recipes:patch']);
Route::delete('recipes/{id}', [RecipeController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'ability:recipes:delete']);

    Route::get('weekdays', [WeekdayController::class, 'index']);
Route::get('weekdays/{id}', [WeekdayController::class, 'show']);
Route::post('weekdays', [WeekdayController::class, 'store'])
    ->middleware(['auth:sanctum', 'ability:weekdays:post']);
Route::patch('weekdays/{id}', [WeekdayController::class, 'update'])
    ->middleware(['auth:sanctum', 'ability:weekdays:patch']);
Route::delete('weekdays/{id}', [WeekdayController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'ability:weekdays:delete']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('weeklyfood/generate', [WeeklyFoodGeneratorController::class, 'generate']);
});
