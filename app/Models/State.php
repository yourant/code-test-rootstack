<?php

namespace App\Models;

use App\Presenters\StatePresenter;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class Country
 *
 * @package App
 * @property Region $region
 * @property Collection $towns
 * @property-read \App\Models\Country $country
 */
class State extends Model implements HasPresenter
{
    public $timestamps = false;

    protected $fillable = ['country_id', 'region_id', 'name', 'name_alt'];

    protected $hidden = ['id'];

    public function towns()
    {
        return $this->hasMany(Town::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function scopeOfName($query, $name)
    {
        return !$name ? $query : $query->where('states.name', 'ilike', $name);
    }

    public function scopeOfCountryId($query, $id)
    {
        return !$id ? $query : $query->where('states.country_id', $id);
    }

    public function scopeOfRegionId($query, $id)
    {
        return !$id ? $query : $query->where('states.region_id', $id);
    }

    public function getRegionName()
    {
       return $this->region ? $this->region->name : null;
    }

    public function getCountryName()
    {
        return $this->country ? $this->country->name : null;
    }

    public function getCountryCode()
    {
        return $this->country ? $this->country->code : null;
    }

    public function getRegionCode()
    {
        return $this->region ? $this->region->code : null;
    }

    public function getPresenterClass()
    {
        return StatePresenter::class;
    }
}
