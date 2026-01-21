<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealOfDay extends Model
{
    /** @use HasFactory<\Database\Factories\MealOfDayFactory> */
    use HasFactory;

      protected $fillable = [
        'meal_of_day',
    ];
        protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
