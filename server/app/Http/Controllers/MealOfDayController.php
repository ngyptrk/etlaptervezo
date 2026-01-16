<?php

namespace App\Http\Controllers;

use App\Models\MealOfDay;
use App\Http\Requests\StoreMealOfDayRequest;
use App\Http\Requests\UpdateMealOfDayRequest;

class MealOfDayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMealOfDayRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(MealOfDay $mealOfDay)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMealOfDayRequest $request, MealOfDay $mealOfDay)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MealOfDay $mealOfDay)
    {
        //
    }
}
