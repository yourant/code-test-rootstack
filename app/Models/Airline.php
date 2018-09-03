<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Airline extends Model
{
    protected $fillable = ['name', 'prefix'];

    public $dates = ['created_at', 'updated_at'];

    public function scopeOfCode($query, $name)
    {
        return !$name ? $query : $query->where('airlines.name', $name);
    }

    public function scopeLikeCode($query, $prefix)
    {
        return !$prefix ? $query : $query->where('airlines.prefix', $prefix);
    }
}
