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
    ->middleware(['auth:sanctum', 'ability:units:post']);
Route::patch('units/{id}', [UnitController::class, 'update'])
    ->middleware(['auth:sanctum', 'ability:units:patch']);
Route::delete('units/{id}', [UnitController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'ability:units:delete']);