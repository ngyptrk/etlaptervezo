<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    /** @use HasFactory<\Database\Factories\RecipeFactory> */
    use HasFactory;
     protected $fillable = [
        'name',
        'description',
        'picture',
        'person',
        'meal_id',
    ];
        protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
