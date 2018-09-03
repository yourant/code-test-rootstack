<?php

namespace App\Models;

use App\Presenters\ProviderServicePresenter;
use McCool\LaravelAutoPresenter\HasPresenter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ProviderService
 *
 * @package App
 * @property Provider $provider
 * @property ProviderServiceType $providerServiceType
 * @property CheckpointCode $firstCheckpointCode
 * @property CheckpointCode $lastCheckpointCode
 * @property Collection $legs
 * @property int $id
 * @property int $provider_id
 * @property int|null $provider_service_type_id
 * @property string|null $code
 * @property int $transit_days
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string|null $deleted_at
 * @mixin \Eloquent
 */
class ProviderService extends Model implements HasPresenter
{
    use SoftDeletes;

    protected $fillable = [
        'provider_id',
        'provider_service_type_id',
        'first_checkpoint_code_id',
        'last_checkpoint_code_id',
        'code',
        'name',
        'transit_days',
        'provider_code'
    ];

    protected $with = ['provider', 'providerServiceType'];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function providerServiceType()
    {
        return $this->belongsTo(ProviderServiceType::class);
    }

    public function legs()
    {
        return $this->hasMany(Leg::class);
    }

    public function firstCheckpointCode()
    {
        return $this->belongsTo(CheckpointCode::class);
    }

    public function lastCheckpointCode()
    {
        return $this->belongsTo(CheckpointCode::class);
    }

    public function scopeOfProviderId($query, $id)
    {
        return !$id ? $query : $query->where('provider_services.provider_id', $id);
    }

    public function scopeOfProviderCode($query, $code)
    {
        return !$code ? $query : $query->where('provider_services.provider_code', $code);
    }

    public function scopeOfCode($query, $code)
    {
        return !$code ? $query : $query->where('provider_services.code', $code);
    }

    public function scopeOfName($query, $code)
    {
        return !$code ? $query : $query->where('provider_services.name', $code);
    }

    public function scopeOfProviderServiceTypeKey($query, $type)
    {
        return !$type ? $query : $query->where('provider_service_types.key', $type);
    }

    public function getFirstCheckpointCodeDescription()
    {
        return $this->firstCheckpointCode ? $this->firstCheckpointCode->description : null;
    }

    public function getFirstCheckpointCodeKey()
    {
        return $this->firstCheckpointCode ? $this->firstCheckpointCode->key : null;
    }

    public function getLastCheckpointCodeDescription()
    {
        return $this->lastCheckpointCode ? $this->lastCheckpointCode->description : null;
    }

    public function getLastCheckpointCodeKey()
    {
        return $this->lastCheckpointCode ? $this->lastCheckpointCode->key : null;
    }

    public function getProviderId()
    {
        return $this->provider ? $this->provider->id : null;
    }

    public function getProviderCheckpointCodes()
    {
        return $this->provider ? $this->provider->checkpointCodes : null;
    }

    public function getProviderName()
    {
        return $this->provider ? $this->provider->name : null;
    }

    public function getProviderNameAttribute()
    {
        return $this->provider ? $this->provider->name : null;
    }

    public function getProviderTimezone()
    {
        return $this->provider ? $this->provider->timezone : null;
    }

    public function getProviderCountryName()
    {
        return $this->provider ? $this->provider->getCountryName() : null;
    }

    public function getProviderCountryCode()
    {
        return $this->provider ? $this->provider->getCountryCode() : null;
    }

    public function getProviderCountryId()
    {
        return $this->provider ? $this->provider->country_id : null;
    }

    public function getProviderServiceTypeName()
    {
        return $this->providerServiceType ? $this->providerServiceType->name : null;
    }

    public function isProviderSepomex()
    {
        return $this->provider ? $this->provider->isSepomex() : false;
    }

    public function isProviderQuality()
    {
        return $this->provider ? $this->provider->isQuality() : false;
    }

    public function isProviderMexpost()
    {
        return $this->provider ? $this->provider->isMexpost() : false;
    }

    public function isProvider472()
    {
        return $this->provider ? $this->provider->is472() : false;
    }

    public function isProviderCorreios()
    {
        return $this->provider ? $this->provider->isCorreios() : false;
    }

    public function isProviderSerpost()
    {
        return $this->provider ? $this->provider->isSerpost() : false;
    }

    public function isProviderSinotrans()
    {
        return $this->provider ? $this->provider->isSinotrans() : false;
    }

    public function isProviderGlobalMatch()
    {
        return $this->provider ? $this->provider->isGlobalMatch() : false;
    }

    public function isProviderChile()
    {
        return $this->provider ? $this->provider->isChile() : false;
    }

    public function isProviderBlueExpress()
    {
        return $this->provider ? $this->provider->isBlueExpress() : false;
    }

    public function isProviderSellerDropOff()
    {
        return $this->provider ? $this->provider->isSellerDropOff() : false;
    }

    public function isProviderServiceTypeDistribution()
    {
        return $this->providerServiceType ? $this->providerServiceType->isDistribution() : false;
    }

    public function isProviderUrbano()
    {
        return $this->provider ? $this->provider->isUrbano() : false;
    }

    public function isProviderTCC()
    {
        return $this->provider ? $this->provider->isTCC() : false;
    }

    public function getPresenterClass()
    {
        return ProviderServicePresenter::class;
    }
}
