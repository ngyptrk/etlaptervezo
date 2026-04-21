<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawIngredient extends Model
{
    /** @use HasFactory<\Database\Factories\RawIngredientFactory> */
    use HasFactory;
      protected $fillable = [
        'raw_ingredient',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function ingredients()
    {
        return $this->hasMany(Ingredient::class);
    }
}
