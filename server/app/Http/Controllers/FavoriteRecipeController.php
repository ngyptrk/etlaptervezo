<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFavoriteRecipeRequest;
use App\Models\FavoriteRecipe;
use Illuminate\Http\Request;

class FavoriteRecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->apiResponse(function () use ($request) {
            $user = $request->user();
            return FavoriteRecipe::where('user_id', $user->id)
                ->pluck('recipe_id')
                ->values();
        });
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFavoriteRecipeRequest $request)
    {
        return $this->apiResponse(function () use ($request) {
            $user = $request->user();
            $validated = $request->validated();

            return FavoriteRecipe::firstOrCreate([
                'user_id' => $user->id,
                'recipe_id' => $validated['recipe_id'],
            ]);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, int $recipeId)
    {
        return $this->apiResponse(function () use ($request, $recipeId) {
            $user = $request->user();
            $favorite = FavoriteRecipe::where('user_id', $user->id)
                ->where('recipe_id', $recipeId)
                ->firstOrFail();

            $favorite->delete();
            return ['recipe_id' => $recipeId];
        });
    }
}
