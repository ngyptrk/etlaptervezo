<?php

namespace App\Http\Controllers;

use App\Models\MealRequirement;
use App\Http\Requests\StoreMealRequirementRequest;
use App\Http\Requests\UpdateMealRequirementRequest;

class MealRequirementController extends Controller
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
    public function store(StoreMealRequirementRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(MealRequirement $mealRequirement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMealRequirementRequest $request, MealRequirement $mealRequirement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MealRequirement $mealRequirement)
    {
        //
    }
}
