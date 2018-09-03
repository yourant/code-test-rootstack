<?php

namespace App\Models;

use App\Presenters\RegionPresenter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class Region
 *
 * @package App
 * @property Country $country
 * @property Collection $adminLevels1
 * @property int $id
 * @property int $country_id
 * @property string $name
 * @property string|null $code
 * @mixin \Eloquent
 */

class Region extends Model implements HasPresenter
{
    public $timestamps = false;

    protected $fillable = ['country_id', 'name', 'code'];

    protected $hidden = ['id'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function adminLevels1()
    {
        return $this->hasMany(State::class);
    }

    public function scopeOfCountryId($query, $id)
    {
        return !$id ? $query : $query->where('regions.country_id', $id);
    }

    public function getCountryName()
    {
        return $this->country ? $this->country->name : null;
    }

    public function getPresenterClass()
    {
        return RegionPresenter::class;
    }
}
