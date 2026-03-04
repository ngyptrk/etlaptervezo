<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'meal_of_day_id',
        'meal_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function mealOfDay()
    {
        return $this->belongsTo(MealOfDay::class);
    }

    public function meal()
    {
        return $this->belongsTo(Meal::class);
    }
}
