<?php

namespace App\Models;

use App\Presenters\TownPresenter;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class Country
 *
 * @package App
 * @property State $state
 * @property Region $region
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Township[] $townships
 * @mixin \Eloquent
 */
class Town extends Model implements HasPresenter
{

    public $timestamps = false;

    protected $fillable = ['name', 'state_id', 'region_id'];

    protected $hidden = ['id', 'state_id'];

    protected $with = ['state'];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function townships()
    {
        return $this->hasMany(Township::class);
    }

    public function getStateName()
    {
        return $this->state ? $this->state->name : null;
    }

    public function getStateCountryName()
    {
        return $this->state ? $this->state->getCountryName() : null;
    }

    public function getStateCountryCode()
    {
        return $this->state ? $this->state->getCountryCode() : null;
    }
    
    public function getStateRegionCode()
    {
        return $this->state ? $this->state->getRegionCode() : null;
    }

    public function getStateRegionName()
    {
        return $this->state ? $this->state->getRegionName() : null;
    }

    public function getRegionName()
    {
        return $this->region ? $this->region->name : null;
    }

    public function getPresenterClass()
    {
        return TownPresenter::class;
    }
}