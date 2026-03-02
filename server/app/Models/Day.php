<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    /** @use HasFactory<\Database\Factories\DayFactory> */
    use HasFactory;
  protected $fillable = [
        'user_id',
        'weekday_id',          // ← EZ KELL
        'recipe_id',
        'meal_requirement_id', // ← EZ IS KELL
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
