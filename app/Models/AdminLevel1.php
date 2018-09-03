<?php

namespace App\Models;

use App\Presenters\AdminLevel1Presenter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class AdminLevel1
 *
 * @package App
 * @property Region $region
 * @property Country $country
 * @property Collection $adminLevels2
 * @mixin \Eloquent
 */
class AdminLevel1 extends Model implements HasPresenter
{
    protected $table = 'admin_level_1';

    public $timestamps = false;

    protected $fillable = ['country_id', 'region_id', 'name', 'name_alt'];

    protected $hidden = ['id'];

    public function adminLevels2()
    {
        return $this->hasMany(AdminLevel2::class, 'admin_level_1_id');
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
        return !$name ? $query : $query->where('admin_level_1.name', 'ilike', $name);
    }

    public function scopeOfCountryId($query, $id)
    {
        return !$id ? $query : $query->where('admin_level_1.country_id', $id);
    }

    public function scopeOfRegionId($query, $id)
    {
        return !$id ? $query : $query->where('admin_level_1.region_id', $id);
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
        return AdminLevel1Presenter::class;
    }
}
