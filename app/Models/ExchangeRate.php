<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $fillable = [
        'currency_id',
        'value',
    ];

    protected $with = ['currency'];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
