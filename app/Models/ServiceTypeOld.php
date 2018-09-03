<?php

namespace App\Models;

use App\Presenters\ServiceTypePresenter;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class ServiceType
 *
 * @package App
 * @property Provider $provider
 * @property ProviderServiceType $providerServiceType
 * @property-read \App\Models\CheckpointCode $firstCheckpointCode
 * @property-read mixed $provider_name
 * @property-read \App\Models\CheckpointCode $lastCheckpointCode
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceType ofProviderId($id)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceType ofProviderServiceTypeKey($type)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceType ofService($service)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceType ofType($type)
 * @mixin \Eloquent
 */
class ServiceTypeOld extends Model implements HasPresenter
{
    protected $fillable = [
        'provider_id',
        'provider_service_type_id',
        'first_checkpoint_code_id',
        'last_checkpoint_code_id',
        'code',
        'name',
        'provider_code',
        'transit_days',
        'type',
        'service',
        'details'
    ];

    protected $with = ['provider', 'providerServiceType'];

    protected $touches = ['provider', 'firstCheckpointCode', 'lastCheckpointCode'];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function providerServiceType()
    {
        return $this->belongsTo(ProviderServiceType::class);
    }

    public function firstCheckpointCode()
    {
        return $this->belongsTo(CheckpointCode::class);
    }

    public function lastCheckpointCode()
    {
        return $this->belongsTo(CheckpointCode::class);
    }

    public function getProviderCheckpointCodes()
    {
        return $this->provider ? $this->provider->checkpointCodes : null;
    }

    public function scopeOfProviderId($query, $id)
    {
        return !$id ? $query : $query->where('service_types.provider_id', $id);
    }

    public function scopeOfCode($query, $code)
    {
        return !$code ? $query : $query->where('service_types.code', $code);
    }

    public function scopeOfProviderCode($query, $provider_code)
    {
        return !$provider_code ? $query : $query->where('service_types.provider_code', $provider_code);
    }

    public function scopeOfType($query, $type)
    {
        return !$type ? $query : $query->where('service_types.type', $type);
    }

    public function scopeOfProviderServiceTypeKey($query, $type)
    {
        return !$type ? $query : $query->where('provider_service_types.key', $type);
    }

    public function scopeOfService($query, $service)
    {
        return !$service ? $query : $query->where('service_types.service', $service);
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
        return ServiceTypePresenter::class;
    }
}