<?php

namespace App\Models;

use App\Presenters\TownshipTypePresenter;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * App\Models\TownshipType
 *
 * @property-read \App\Models\ZipCode $zipCode
 * @mixin \Eloquent
 */
class TownshipType extends Model implements HasPresenter
{

    public $timestamps = false;

    protected $fillable = ['name'];

    public function zipCode()
    {
        return $this->belongsTo(ZipCode::class);
    }

    public function getPresenterClass()
    {
        return TownshipTypePresenter::class;
    }
}
