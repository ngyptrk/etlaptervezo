<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
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

    public function meal()
    {
        return $this->belongsTo(Meal::class);
    }

    public function ingredients()
    {
        return $this->hasMany(Ingredient::class);
    }
}
