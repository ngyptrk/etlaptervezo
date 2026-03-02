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
        'day_id',
        'recipe_id',
        'meal_id'
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
