<?php

namespace App\Models;

use App\Presenters\AdminLevel3Presenter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class AdminLevel3
 *
 * @package App
 * @property AdminLevel2 $adminLevel2
 * @property Collection $zipCodes
 * @mixin \Eloquent
 */

class AdminLevel3 extends Model implements HasPresenter
{
    protected $table = 'admin_level_3';
    
    public $timestamps = false;

    protected $fillable = [
        'admin_level_2_id',
        'name',
        'name_alt',
        'territorial_code',
        'abbreviation_code',
        'subzone_code'
    ];

    protected $hidden = ['id', 'admin_level_2_id'];

    protected $with = ['adminLevel2'];

    public function adminLevel2()
    {
        return $this->belongsTo(AdminLevel2::class, 'admin_level_2_id');
    }

    public function zipCodes()
    {
        return $this->hasMany(ZipCode::class, 'admin_level_3_id');
    }

    public function scopeOfName($query, $name)
    {
        return !$name ? $query : $query->where('admin_level_3.name', 'ilike', $name);
    }

    public function scopeOfNameAlt($query, $name_alt)
    {
        return !$name_alt ? $query : $query->where('admin_level_3.name_alt', 'ilike', $name_alt);
    }

    public function scopeOfTerritorialCode($query, $code)
    {
        return !$code ? $query : $query->where('admin_level_3.territorial_code', $code);
    }

    public function scopeOfAbbreviationCode($query, $code)
    {
        return !$code ? $query : $query->where('admin_level_3.abbreviation_code', 'ilike', $code);
    }

    public function scopeOfCountryCode($query, $code)
    {
        return !$code ? $query : $query->where('countries.code', 'ilike', $code);
    }

    public function getAdminLevel2Name()
    {
        return $this->adminLevel2 ? $this->adminLevel2->name : null;
    }

    public function getAdminLevel2AdminLevel1Name()
    {
        return $this->adminLevel2 ? $this->adminLevel2->getAdminLevel1Name() : null;
    }

    public function getAdminLevel2AdminLevel1CountryName()
    {
        return $this->adminLevel2 ? $this->adminLevel2->getAdminLevel1CountryName() : null;
    }

    public function getAdminLevel2AdminLevel1CountryCode()
    {
        return $this->adminLevel2 ? $this->adminLevel2->getAdminLevel1CountryCode() : null;
    }

    public function getAdminLevel2AdminLevel1RegionCode()
    {
        return $this->adminLevel2 ? $this->adminLevel2->getAdminLevel1RegionCode() : null;
    }

    public function getAdminLevel2AdminLevel1RegionName()
    {
        return $this->adminLevel2 ? $this->adminLevel2->getAdminLevel1RegionName() : null;
    }

    public function getAdminLevel2RegionName()
    {
        return $this->adminLevel2 ? $this->adminLevel2->getRegionName() : null;
    }

    public function getPresenterClass()
    {
        return AdminLevel3Presenter::class;
    }
}
