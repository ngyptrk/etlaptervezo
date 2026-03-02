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
        'meal_id',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'meal_of_days_id' => null, // default null
    ];
    // Kapcsolat a recepthez
    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    // Kapcsolat a hét naphoz
    public function weekday()
    {
        return $this->belongsTo(Weekday::class, 'day_id');
    }
}
