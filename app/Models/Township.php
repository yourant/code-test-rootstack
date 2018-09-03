<?php

namespace App\Models;

use App\Presenters\TownshipPresenter;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class Country
 *
 * @package App
 * @property Town $town
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ZipCode[] $zipCodes
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Township ofAbbreviationCode($code)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Township ofCountryCode($code)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Township ofName($name)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Township ofNameAlt($name_alt)
 * @mixin \Eloquent
 */
class Township extends Model implements HasPresenter
{

    public $timestamps = false;

    protected $fillable = ['town_id', 'name', 'name_alt', 'territorial_code', 'abbreviation_code'];

    protected $hidden = ['id', 'town_id'];

    protected $with = ['town'];

    public function town()
    {
        return $this->belongsTo(Town::class);
    }

    public function zipCodes()
    {
        return $this->hasMany(ZipCode::class);
    }

    public function scopeOfName($query, $name)
    {
        return !$name ? $query : $query->where('townships.name', 'ilike', $name);
    }

    public function scopeOfNameAlt($query, $name_alt)
    {
        return !$name_alt ? $query : $query->where('townships.name_alt', 'ilike', $name_alt);
    }

    public function scopeOfAbbreviationCode($query, $code)
    {
        return !$code ? $query : $query->where('townships.abbreviation_code', 'ilike', $code);
    }

    public function scopeOfCountryCode($query, $code)
    {
        return !$code ? $query : $query->where('countries.code', 'ilike', $code);
    }

    public function getTownName()
    {
        return $this->town ? $this->town->name : null;
    }

    public function getTownStateName()
    {
        return $this->town ? $this->town->getStateName() : null;
    }

    public function getTownStateCountryName()
    {
        return $this->town ? $this->town->getStateCountryName() : null;
    }

    public function getTownStateCountryCode()
    {
        return $this->town ? $this->town->getStateCountryCode() : null;
    }

    public function getTownStateRegionCode()
    {
        return $this->town ? $this->town->getStateRegionCode() : null;
    }

    public function getTownStateRegionName()
    {
        return $this->town ? $this->town->getStateRegionName() : null;
    }

    public function getTownRegionName()
    {
        return $this->town ? $this->town->getRegionName() : null;
    }

    public function getPresenterClass()
    {
        return TownshipPresenter::class;
    }
}