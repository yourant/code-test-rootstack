<?php

namespace App\Models;

use App\Presenters\PostalOfficePresenter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class PostalOffice
 *
 * @property AdminLevel3 $adminLevel3
 * @property Provider $provider
 * @property PostalOfficeType $postalOfficeType
 * @property Collection $zipCodes
 * @property Collection $packages
 * @package App
 * @property int $id
 * @property int|null $provider_id
 * @property int $postal_office_type_id
 * @property string $code
 * @property string|null $name
 * @property string|null $address
 * @property string|null $phone_no
 * @property string|null $email
 * @property string|null $reference
 * @mixin \Eloquent
 */
class PostalOffice extends Model implements HasPresenter
{
    public $timestamps = false;

    protected $fillable = [
        'provider_id',
        'postal_office_type_id', 
        'admin_level_3_id',
        'code',
        'name',
        'address',
        'phone_no',
        'email',
        'reference'
    ];

    protected $hidden = [
        'provider_id',
        'admin_level_3_id',
        'postal_office_type_id',
        'email',
        'reference'
    ];

    protected $with = ['adminLevel3', 'postalOfficeType'];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function adminLevel3()
    {
        return $this->belongsTo(AdminLevel3::class, 'admin_level_3_id');
    }

    public function postalOfficeType()
    {
        return $this->belongsTo(PostalOfficeType::class);
    }

    public function zipCodes()
    {
        return $this->belongsToMany(ZipCode::class);
    }

    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    public function scopeOfCode($query, $code)
    {
        return $query->where('postal_offices.code', $code);
    }

    public function scopeOfProviderId($query, $id)
    {
        return $query->where('postal_offices.provider_id', $id);
    }

    public function scopeOfName($query, $input)
    {
        if (is_array($input) && !empty($input)) {
            $query->where(function ($q2) use ($input) {
                collect($input)->each(function ($item) use($q2){
                    $q2->orWhere('postal_offices.name', $item);
                });
            });
            return $query;
        } else {
            return !$input ? $query : $query->where('postal_offices.name', $input);
        }
    }

    public function scopeOfKeywords($query, $keywords)
    {
        return $query
            ->where('postal_offices.name', 'ilike', "%{$keywords}%")
            ->orWhere('postal_offices.code', 'like', "%{$keywords}%");
    }

    public function scopeOfTypeName($query, $input)
    {
        return !$input ? $query : $query->whereRaw("concat(postal_office_types.name, ' ', postal_offices.name) = ?", [$input]);
    }

    public function isProviderSepomex()
    {
        return $this->provider ? $this->provider->isSepomex() : false;
    }

    public function getAdminLevel3Name()
    {
        return $this->adminLevel3 ? $this->adminLevel3->name : null;
    }

    public function getPostalOfficeTypeName()
    {
        return $this->postalOfficeType ? $this->postalOfficeType->name : null;
    }

    public function getAdminLevel3AdminLevel2Name()
    {
        return $this->adminLevel3 ? $this->adminLevel3->getAdminLevel2Name() : null;
    }

    public function getAdminLevel3AdminLevel2AdminLevel1Name()
    {
        return $this->adminLevel3 ? $this->adminLevel3->getAdminLevel2AdminLevel1Name() : null;
    }

    public function getPresenterClass()
    {
        return PostalOfficePresenter::class;
    }
}