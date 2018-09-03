<?php

namespace App\Models;

use App\Presenters\LocationPresenter;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class Location
 *
 * @package App
 * @property Country $country
 * @property int $id
 * @property string|null $code
 * @property string|null $description
 * @property string|null $type
 * @property int $country_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @mixin \Eloquent
 */
class Location extends Model implements HasPresenter
{
    protected $fillable = [
        'code',
        'description',
        'type',
        'country_id',
    ];

    protected $with = ['country'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function scopeOfCode($query, $code)
    {
        if (is_array($code) && !empty($code)) {
            return $query->whereIn('locations.code', $code);
        } else {
            return !$code ? $query : $query->where('locations.code', $code);
        }

//        return $query->where('locations.code', $code);
    }

    public function scopeOfDescription($query, $description)
    {
        return $query->where('locations.description', $description);
    }

    public function scopeOfType($query, $type)
    {
        if (is_array($type) && !empty($type)) {
            return $query->whereIn('locations.type', $type);
        } else {
            return !$type ? $query : $query->where('locations.type', $type);
        }
//        return $query->where('locations.type', $type);
    }

    public function scopeOfCountryId($query, $country_id)
    {
        if (is_array($country_id) && !empty($country_id)) {
            return $query->whereIn('locations.country_id', $country_id);
        } else {
            return !$country_id ? $query : $query->where('locations.country_id', $country_id);
        }
    }

    public function getCountryName()
    {
        return $this->country ? $this->country->name : null;
    }

    public function getCountryCode()
    {
        return $this->country ? $this->country->code : null;
    }

    public function getCountryContinentAbbreviation()
    {
        return $this->country ? $this->country->getContinentAbbreviation() : null;
    }

    public function isCountryMexico()
    {
        return $this->country ? $this->country->isMexico() : false;
    }

    public function isCountryChile()
    {
        return $this->country ? $this->country->isChile() : false;
    }

    public function getPresenterClass()
    {
        return LocationPresenter::class;
    }
}
