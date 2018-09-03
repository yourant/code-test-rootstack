<?php

namespace App\Models;

use App\Presenters\ProviderPresenter;
use App\Traits\Cacheable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class Provider
 *
 * @package App
 * @property Collection $providerServices
 * @property Collection $checkpointCodes
 * @property Country $country
 * @property Timezone $timezone
 * @property int $id
 * @property string $name
 * @property string|null $code
 * @property string|null $prefix
 * @property int|null $country_id
 * @property int|null $timezone_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string|null $deleted_at
 * @mixin \Eloquent
 */
class Provider extends Model implements HasPresenter
{
    use Cacheable;

	protected $fillable = ['name', 'code', 'country_id', 'timezone_id', 'prefix', 'generic', 'parent_id'];

    protected $hidden = ['id'];

    public $with = ['country', 'timezone'];

    public function providerServices()
    {
        return $this->hasMany(ProviderService::class);
    }

    public function checkpointCodes()
    {
        return $this->hasMany(CheckpointCode::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function timezone()
    {
        return $this->belongsTo(Timezone::class);
    }

    public function parent()
    {
        return $this->belongsTo(Provider::class, 'parent_id');
    }

    public function scopeOfCountryId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('providers.country_id', $id);
        } else {
            return !$id ? $query : $query->where('providers.country_id', $id);
        }
    }

    public function scopeOfCode($query, $code)
    {
        if (is_array($code) && !empty($code)) {
            return $query->whereIn('providers.code', $code);
        } else {
            return !$code ? $query : $query->where('providers.code', $code);
        }
    }

    public function scopeOfName($query, $name)
    {
        if (is_array($name) && !empty($name)) {
            return $query->whereIn('providers.name', $name);
        } else {
            return !$name ? $query : $query->where('providers.name', $name);
        }
    }

    public function scopeOfPrefix($query, $prefix)
    {
        if (is_array($prefix) && !empty($prefix)) {
            return $query->whereIn('providers.prefix', $prefix);
        } else {
            return !$prefix ? $query : $query->where('providers.prefix', $prefix);
        }
    }

    public function isSepomex()
    {
        return preg_match('/PR4937/i', $this->code);
    }

    public function isQuality()
    {
        return preg_match('/PR3873/i', $this->code);
    }

    public function isMexpost()
    {
        return preg_match('/PR4938/i', $this->code);
    }

    public function is472()
    {
        return preg_match('/PR3548/i', $this->code);
    }

    public function isCorreios()
    {
        return preg_match('/PR3289/i', $this->code);
    }

    public function isSerpost()
    {
        return preg_match('/PR1985/i', $this->code);
    }

    public function isSinotrans()
    {
        return preg_match('/PR5573/i', $this->code);
    }

    public function isGlobalMatch()
    {
        return preg_match('/PR0538/i', $this->code);
    }

    public function isChile()
    {
        return preg_match('/PR2785/i', $this->code);
    }

    public function isBlueExpress()
    {
        return preg_match('/PR6548/i', $this->code);
    }

    public function isCorreosDelEcuador()
    {
        return preg_match('/PR7946/i', $this->code);
    }

    public function isSellerDropOff()
    {
        return preg_match('/PR3278/i', $this->code);
    }

    public function isUrbano()
    {
        return preg_match('/PR6749/i', $this->code);
    }

    public function isTCC()
    {
        return preg_match('/PR8253/i', $this->code);
    }

    public function isAeromexico()
    {
        return preg_match('/PR7814/i', $this->code);
    }

    public function isPhoenixCargo()
    {
        return preg_match('/PR8432/i', $this->code);
    }

    public function isWarehouse()
    {
        return preg_match('/PR4439/i', $this->code);
    }

    public function isTransit()
    {
        return preg_match('/PR8522/i', $this->code);
    }

    public function isUrbanoArgentina()
    {
        return preg_match('/PR1200/i', $this->code);
    }

    public function getCountryName()
    {
        return $this->country ? $this->country->name : null;
    }

    public function getCountryCode()
    {
        return $this->country ? $this->country->code : null;
    }

    public function getTimezoneDescription()
    {
        return $this->timezone ? $this->timezone->description : null;
    }

    public function getProviderServicesCount()
    {
        return $this->providerServices->count();
    }

    public function getParentName()
    {
        return $this->parent ? $this->parent->name : null;
    }

    public function hasParent()
    {
        return $this->parent ? $this->parent : null;
    }

    public function scopeOfGeneric($query)
    {
        return $query->where('providers.generic', true);
    }

    public function getParentNameAttribute()
    {
        return $this->parent ? $this->parent->name : null;
    }

    public function getParentCodeAttribute()
    {
        return $this->parent ? $this->parent->code : null;
    }

    public function getParentNameCodeAttribute()
    {
        return $this->parent ? $this->parent->code . ' - ' . $this->parent->name: null;
    }

    public function getPresenterClass()
    {
        return ProviderPresenter::class;
    }

}