<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Weekday extends Model
{
    use HasFactory;

    protected $fillable = [
        'day',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function days()
    {
        return $this->hasMany(Day::class);
    }
}
