<?php

namespace App\Models;

use App\Presenters\ZipCodePresenter;
use Cartalyst\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class ZipCode
 *
 * @package App
 * @property AdminLevel3 $adminLevel3
 * @property AdminLevel3Type $adminLevel3Type
 * @property Collection $postalOffices
 * @property int $id
 * @property string $code
 * @property int $admin_level_3_id
 * @property int $admin_level_3_type_id
 * @property int|null $zone_id
 * @mixin \Eloquent
 */

class ZipCode extends Model implements HasPresenter
{
    public $timestamps = false;

    protected $fillable = ['code', 'admin_level_3_id', 'admin_level_3_types_id', 'zone_id'];

    protected $hidden = ['id'];

    public function adminLevel3()
    {
        return $this->belongsTo(AdminLevel3::class, 'admin_level_3_id');
    }

    public function adminLevel3Type()
    {
        return $this->belongsTo(AdminLevel3Type::class, 'admin_level_3_types_id');
    }

    public function postalOffices()
    {
        return $this->belongsToMany(PostalOffice::class);
    }

    public function scopeOfCode($query, $code)
    {
        if (is_array($code) && !empty($code)) {
            return $query->whereIn('zip_codes.code', $code);
        } else {
            return !$code ? $query : $query->where('zip_codes.code', $code);
        }
    }

    public function scopeOfCountryCode($query, $code)
    {
        if (is_array($code) && !empty($code)) {
            $query->where(function ($q2) use ($code) {
                collect($code)->each(function ($cod) use($q2){
                    $q2->orWhere('countries.code', 'ilike', $cod);
                });
            });
            return $query;
        } else {
            return !$code ? $query : $query->where('countries.code', 'ilike', $code);
        }
    }

    public function getAdminLevel3Name()
    {
        return $this->adminLevel3 ? $this->adminLevel3->name : null;
    }

    //
    public function getAdminLevel3NameAlt()
    {
        return $this->adminLevel3 ? $this->adminLevel3->name_alt : null;
    }

    public function getAdminLevel3TypeName()
    {
        return $this->adminLevel3Type ? $this->adminLevel3Type->name : null;
    }

    public function getAdminLevel3AdminLevel2Name()
    {
        return $this->adminLevel3 ? $this->adminLevel3->getAdminLevel2Name() : null;
    }

    public function getAdminLevel3AdminLevel2AdminLevel1Name()
    {
        return $this->adminLevel3 ? $this->adminLevel3->getAdminLevel2AdminLevel1Name() : null;
    }

    public function getAdminLevel3AdminLevel2AdminLevel1CountryName()
    {
        return $this->adminLevel3 ? $this->adminLevel3->getAdminLevel2AdminLevel1CountryName() : null;
    }

    public function getAdminLevel3AdminLevel2AdminLevel1CountryCode()
    {
        return $this->adminLevel3 ? $this->adminLevel3->getAdminLevel2AdminLevel1CountryCode() : null;
    }

    public function getAdminLevel3AdminLevel2AdminLevel1RegionName()
    {
        return $this->adminLevel3 ? $this->adminLevel3->getAdminLevel2AdminLevel1RegionName() : null;
    }

    public function getAdminLevel3AdminLevel2RegionName()
    {
        return $this->adminLevel3 ? $this->adminLevel3->getAdminLevel2RegionName() : null;
    }

    public function getFirstPostalOfficeCode()
    {
        $postalOffice = $this->postalOffices->first();

        return $postalOffice ? $postalOffice->code : null;
    }

    public function getPresenterClass()
    {
        return ZipCodePresenter::class;
    }
}