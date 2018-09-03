<?php

namespace App\Models;

use App\Presenters\AgreementPresenter;
use App\Traits\Cacheable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class Agreement
 *
 * @package App
 * @property Client $client
 * @property Collection $packages
 * @property Service $service
 * @property int $id
 * @property int $client_id
 * @property int $service_id
 * @property int|null $tariff_id
 * @property int $ddp
 * @property int $enabled
 * @mixin \Eloquent
 */
class Agreement extends Model implements HasPresenter
{
    use Cacheable;

    protected $fillable = [
        'service_id',
        'client_id',
        'tariff_id',
        'enabled',
        'ddp',
//        'controlled_transit_days',
//        'uncontrolled_transit_days',
//        'total_transit_days',
    ];

    protected $with = ['client', 'service'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    public function scopeOfClientId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('agreements.client_id', $id);
        } else {
            return !$id ? $query : $query->where('agreements.client_id', $id);
        }
    }

    public function scopeOfDestinationCountryId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('destination_locations.country_id', $id);
        } else {
            return !$id ? $query : $query->where('destination_locations.country_id', $id);
        }
    }

    public function scopeOfServiceId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('agreements.service_id', $id);
        } else {
            return !$id ? $query : $query->where('agreements.service_id', $id);
        }
    }

    public function scopeOfServiceTransitDays($query, $transit_days)
    {
        return !$transit_days ? $query : $query->where('services.transit_days', $transit_days);
    }

    public function scopeOfServiceCode($query, $code)
    {
        return !$code ? $query : $query->where('services.code', $code);
    }

    public function scopeOfProviderServiceId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('provider_services.id', $id);
        } else {
            return !$id ? $query : $query->where('provider_services.id', $id);
        }
    }

    public function getClientUser()
    {
        return $this->client->user ? $this->client->user : null;
    }

    public function getClientCode()
    {
        return $this->client ? $this->client->code : null;
    }

    public function getClientName()
    {
        return $this->client ? $this->client->name : null;
    }

    public function getClientAcronym()
    {
        return $this->client ? $this->client->acronym : null;
    }

    public function getClientCountryName()
    {
        return $this->client ? $this->client->getCountryName() : null;
    }

    public function getClientTimezone()
    {
        return $this->client ? $this->client->timezone : null;
    }

    public function getServiceOriginLocationId()
    {
        return $this->service ? $this->service->origin_location_id : null;
    }

    public function getServiceDestinationLocationId()
    {
        return $this->service ? $this->service->destination_location_id : null;
    }

    public function getServiceDestinationLocationCountryId()
    {
        return $this->service ? $this->service->getDestinationLocationCountryId() : null;
    }

    public function getServiceOriginLocationCountryName()
    {
        return $this->service ? $this->service->getOriginLocationCountryName() : null;
    }

    public function getServiceDestinationLocationCountryName()
    {
        return $this->service ? $this->service->getDestinationLocationCountryName() : null;
    }

    public function getServiceDestinationLocationCountryCode()
    {
        return $this->service ? $this->service->getDestinationLocationCountryCode() : null;
    }

    public function getClientMarketplaceName()
    {
        return $this->client ? $this->client->getMarketplaceName() : null;
    }

    public function getServiceTransitDays()
    {
        return $this->service ? $this->service->transit_days : null;
    }

    public function getServiceName()
    {
        return $this->service ? $this->service->name : null;
    }

    public function getServiceCode()
    {
        return $this->service ? $this->service->code : null;
    }

    public function getServiceDeliveryRoutes()
    {
        return $this->service ? $this->service->deliveryRoutes : null;
    }

    public function getServiceDefaultDeliveryRoute()
    {
        return $this->service ? $this->service->getDefaultDeliveryRoute() : null;
    }

    public function getServiceServiceTypeKey()
    {
        return $this->service ? $this->service->getServiceTypeKey() : null;
    }

    public function isEnabled()
    {
        return ($this->enabled);
    }

    public function isServiceDestinationLocationCountryMexico()
    {
        return $this->service ? $this->service->isDestinationLocationCountryMexico() : false;
    }

    public function isServiceDestinationLocationCountryChile()
    {
        return $this->service ? $this->service->isDestinationLocationCountryChile() : false;
    }

    public function isServiceBillingModeVolumetric()
    {
        return $this->service ? $this->service->isBillingModeVolumetric() : false;
    }

    public function hasServiceAlternativeDeliveryRoutes()
    {
        return $this->service ? $this->service->hasAlternativeDeliveryRoutes() : false;
    }

    public function isDeliveryDutiesPaid()
    {
        return $this->ddp;
    }

    public function getPresenterClass()
    {
        return AgreementPresenter::class;
    }
}