<?php

namespace App\Models;

use App\Presenters\AliasPresenter;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class Alias
 * @package App\Models
 *
 * @property string $code
 * @property Package $package
 * @property Provider $provider
 */
class Alias extends Model implements HasPresenter
{
    protected $fillable = ['package_id', 'provider_id', 'code'];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function scopeOfCode($query, $code)
    {
        if (is_array($code) && !empty($code)) {
            $query->where(function ($q2) use ($code) {
                collect($code)->each(function ($item) use ($q2) {
                    $q2->orWhere('aliases.code', strtoupper($item));
                });
            });

            return $query;
        } else {
            return !$code ? $query : $query->where('aliases.code', strtoupper($code));
        }
    }

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper($value);
    }

    public function getPackageTrackingNumber()
    {
        return $this->package ? $this->package->tracking_number : null;
    }

    public function getProviderCode()
    {
        return $this->provider ? $this->provider->code : null;
    }

    public function getProviderName()
    {
        return $this->provider ? $this->provider->name : null;
    }

    public function getPresenterClass()
    {
        return AliasPresenter::class;
    }
}
