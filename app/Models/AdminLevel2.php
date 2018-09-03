<?php

namespace App\Models;

use App\Presenters\AdminLevel2Presenter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class AdminLevel2
 *
 * @package App
 * @property AdminLevel1 $adminLevel1
 * @property Collection $adminLevels3
 * @property Region $region
 * @mixin \Eloquent
 */

class AdminLevel2 extends Model implements HasPresenter
{
    protected $table = 'admin_level_2';
    
    public $timestamps = false;

    protected $fillable = ['name', 'admin_level_1_id'];

    protected $hidden = ['id', 'admin_level_1_id'];

    protected $with = ['adminLevel1'];

    public function adminLevel1()
    {
        return $this->belongsTo(AdminLevel1::class, 'admin_level_1_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function adminLevels3()
    {
        return $this->hasMany(AdminLevel3::class, 'admin_level_2_id');
    }

    public function getAdminLevel1Name()
    {
        return $this->adminLevel1 ? $this->adminLevel1->name : null;
    }

    public function getAdminLevel1CountryName()
    {
        return $this->adminLevel1 ? $this->adminLevel1->getCountryName() : null;
    }

    public function getAdminLevel1CountryCode()
    {
        return $this->adminLevel1 ? $this->adminLevel1->getCountryCode() : null;
    }

    public function getAdminLevel1RegionCode()
    {
        return $this->adminLevel1 ? $this->adminLevel1->getRegionCode() : null;
    }

    public function getAdminLevel1RegionName()
    {
        return $this->adminLevel1 ? $this->adminLevel1->getRegionName() : null;
    }

    public function getRegionName()
    {
        return $this->region ? $this->region->name : null;
    }

    public function getPresenterClass()
    {
        return AdminLevel2Presenter::class;
    }
}
