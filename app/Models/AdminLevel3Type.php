<?php

namespace App\Models;

use App\Presenters\AdminLevel3TypePresenter;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * App\Models\AdminLevel3Type
 *
 * @property-read \App\Models\ZipCode $zipCode
 * @mixin \Eloquent
 */
class AdminLevel3Type extends Model implements HasPresenter
{
    public $timestamps = false;

    protected $fillable = ['name'];

    public function zipCode()
    {
        return $this->belongsTo(ZipCode::class);
    }

    public function getPresenterClass()
    {
        return AdminLevel3TypePresenter::class;
    }
}
