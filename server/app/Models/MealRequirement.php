<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealRequirement extends Model
{
    /** @use HasFactory<\Database\Factories\MealRequirementFactory> */
    use HasFactory;
     protected $fillable = [
        'meal_of_days_id',
        'meal_id',
    ];
        protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
