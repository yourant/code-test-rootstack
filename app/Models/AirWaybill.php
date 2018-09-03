<?php

namespace App\Models;

use App\Presenters\AirWaybillPresenter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class AirWaybill
 *
 * @package App
 * @property string $code
 * @property int $package_count
 * @property int $weight
 * @property int $id
 * @property \Carbon\Carbon|null $checked_in_at
 * @property \Carbon\Carbon|null $departed_at
 * @property \Carbon\Carbon|null $arrived_at
 * @property \Carbon\Carbon|null $confirmed_at
 * @property \Carbon\Carbon|null $delivered_at
 * @property string|null $details
 * @property string|null $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Dispatch[] $dispatches
 * @property-read mixed $airline_name
 * @property-read mixed $bag_count
 * @mixin \Eloquent
 */
class AirWaybill extends Model implements HasPresenter
{
    use SoftDeletes;

    protected $fillable = ['code', 'checked_in_at', 'departed_at', 'arrived_at', 'confirmed_at', 'delivered_at', 'details', 'origin_airport_id', 'destination_airport_id', 'provider_id'];

    public $dates = ['checked_in_at', 'departed_at', 'arrived_at', 'confirmed_at', 'delivered_at'];

    public function dispatches()
    {
        return $this->hasMany(Dispatch::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function scopeOfCode($query, $code)
    {
        return !$code ? $query : $query->where('air_waybills.code', $code);
    }

    public function scopeOfPrefix($query, $prefix)
    {
        return !$prefix ? $query : $query->where('air_waybills.code','like', "{$prefix}-%");
    }

    public function scopeOfClientId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('agreements.client_id', $id);
        } else {
            return !$id ? $query : $query->where('agreements.client_id', $id);
        }
    }

    public function scopeOfCreatedAtNewerThan($query, $date)
    {
        return !$date ? $query : $query->where('air_waybills.created_at', '>=', $date);
    }

    public function scopeOfCreatedAtOlderThan($query, $date)
    {
        return !$date ? $query : $query->where('air_waybills.created_at', '<=', $date);
    }

    public function scopeOfUndelivered($query)
    {
        return $query->whereNull('air_waybills.delivered_at');
    }

    public function getWeightAttribute()
    {
        $total = 0;
        foreach ($this->dispatches as $dispatch) {
            $total += $dispatch->getTotalWeight();
        }

        return $total;
    }

    public function getDispatchCountAttribute()
    {
        return $this->dispatches->count();
    }

    public function getBagCountAttribute()
    {
        $total = 0;
        foreach ($this->dispatches as $dispatch) {
            $total += $dispatch->getBagCount();
        }

        return $total;
    }

    public function getPackageCountAttribute()
    {
        $total = 0;
        foreach ($this->dispatches as $dispatch) {
            $total += $dispatch->getTotalPackages();
        }

        return $total;
    }

    public function getFirstDispatchAgreementClientName()
    {
        foreach ($this->dispatches as $dispatch) {
            return $dispatch->getAgreementClientName();
        }

        return null;
    }

    public function isDelivered()
    {
        return ($this->delivered_at);
    }

    public function getProviderName()
    {
        return $this->provider ? $this->provider->name : null;
    }

    public function getAirlineNameAttribute()
    {
        if (preg_match('/^695\-/', $this->code)) {
            return 'EVA Air Cargo';
        } elseif (preg_match('/^176\-/', $this->code)) {
            return 'Emirates SkyCargo';
        } elseif (preg_match('/^217\-/', $this->code)) {
            return 'Thai Cargo';
        } elseif (preg_match('/^160\-/', $this->code)) {
            return 'Cathay Pacific Cargo';
        } elseif (preg_match('/^603\-/', $this->code)) {
            return 'SriLankan Cargo';
        } elseif (preg_match('/^125\-/', $this->code)) {
            return 'IAG Cargo';
        } elseif (preg_match('/^235\-/', $this->code)) {
            return 'Turkish Cargo';
        } elseif (preg_match('/^910\-/', $this->code)) {
            return 'Oman Air Cargo';
        } elseif (preg_match('/^232\-/', $this->code)) {
            return 'MASkargo';
        } elseif (preg_match('/^157\-/', $this->code)) {
            return 'Qatar Airways Cargo';
        } elseif (preg_match('/^618\-/', $this->code)) {
            return 'Singapore Airlines Cargo';
        } elseif (preg_match('/^139\-/', $this->code)) {
            return 'Aeromexico Cargo';
        } elseif (preg_match('/^081\-/', $this->code)) {
            return 'Qantas Freight';
        } elseif (preg_match('/^589\-/', $this->code)) {
            return 'Jet Airways';
        } elseif (preg_match('/^988\-/', $this->code)) {
            return 'Asiana Cargo';
        } elseif (preg_match('/^074\-/', $this->code)) {
            return 'KLM Cargo';
        } elseif (preg_match('/^020\-/', $this->code)) {
            return 'Lufthansa Cargo';
        } elseif (preg_match('/^045\-/', $this->code)) {
            return 'LATAM Cargo';
        }

        return null;
    }

    public function getPresenterClass()
    {
        return AirWaybillPresenter::class;
    }

}
