<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    /** @use HasFactory<\Database\Factories\IngredientFactory> */
    use HasFactory;
    protected $fillable = [
        'recipe_id',
        'raw_ingredient_id',
        'amount',
        'unit_id',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function rawIngredient()
    {
        return $this->belongsTo(RawIngredient::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
