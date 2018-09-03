<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Airport extends Model
{
    use SoftDeletes;

    protected $fillable = ['code', 'name', 'country_id'];

    public function scopeOfCode($query, $code)
    {
        return !$code ? $query : $query->where('airports.code', $code);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
