<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'weekday_id',
        'recipe_id',
        'meal_requirement_id',
        'plan_week',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function weekday()
    {
        return $this->belongsTo(Weekday::class);
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function mealRequirement()
    {
        return $this->belongsTo(MealRequirement::class);
    }
}
