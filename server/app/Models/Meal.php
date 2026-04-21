<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    /** @use HasFactory<\Database\Factories\MealFactory> */
    use HasFactory;

    protected $fillable = [
        'meal',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    public function mealRequirements()
    {
        return $this->hasMany(MealRequirement::class);
    }
}
