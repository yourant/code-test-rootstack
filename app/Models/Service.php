<?php

namespace App\Models;

use App\Presenters\ServicePresenter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class Service
 *
 * @property Location $originLocation
 * @property Location destinationLocation
 * @property Collection $deliveryRoutes
 * @property TariffTemplate $tariffTemplate
 * @property Collection $agreements
 * @property BillingMode $billingMode
 * @property ServiceType $serviceType
 * @property int $id
 * @property int $origin_location_id
 * @property int $destination_location_id
 * @property int|null $default_delivery_route_id
 * @property string $code
 * @property string $name
 * @property string $description
 * @property int $transit_days
 * @mixin \Eloquent
 */
class Service extends Model implements HasPresenter
{
    protected $fillable = [
        'sorting_id',
        'origin_location_id',
        'destination_location_id',
        'service_type_id',
        'billing_mode_id',
        'code',
        'name',
        'description',
        'transit_days',
        'enabled'
    ];

    protected $with = ['originLocation', 'destinationLocation'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sorting()
    {
        return $this->belongsTo(Sorting::class);
    }

    public function originLocation()
    {
        return $this->belongsTo(Location::class);
    }

    public function destinationLocation()
    {
        return $this->belongsTo(Location::class);
    }

//    public function defaultDeliveryRoute()
//    {
//        return $this->belongsTo(DeliveryRoute::class);
//    }

    public function deliveryRoutes()
    {
        return $this->belongsToMany(DeliveryRoute::class)->withPivot('default');
    }

    public function tariffTemplate()
    {
        return $this->belongsTo(TariffTemplate::class);
    }

    public function agreements()
    {
        return $this->hasMany(Agreement::class);
    }

    public function billingMode()
    {
        return $this->belongsTo(BillingMode::class);
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function scopeOfCode($query, $code)
    {
        return !$code ? $query : $query->where('services.code', $code);
    }

    public function scopeOfTransitDays($query, $code)
    {
        return !$code ? $query : $query->where('services.transit_days', $code);
    }

    public function scopeOfOriginLocationId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('origin_location.id', $id);
        } else {
            return !$id ? $query : $query->where('origin_location.id', $id);
        }
    }

    public function scopeOfDestinationLocationId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('destination_location.id', $id);
        } else {
            return !$id ? $query : $query->where('destination_location.id', $id);
        }
    }

    public function scopeOfSortingId($query, $sorting_id)
    {
        if (!$sorting_id) {
            return $query->whereNull('services.sorting_id');
        }

        return $query->where('services.sorting_id', $sorting_id);
    }

    public function scopeOfClientId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('clients.id', $id);
        } else {
            return !$id ? $query : $query->where('clients.id', $id);
        }
    }

    public function scopeOfExcludeServiceId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereNotIn('services.id', $id);
        } else {
            return !$id ? $query : $query->where('services.id', '!=', $id);
        }
    }

//    public function getDeliveryRoute()
//    {
//        $service = $this->defaultDeliveryRoute;
//
//        $delivery_routes = $this->defaultDeliveryRoute->all();
//
//        $filtered = $delivery_routes->filter(function ($value) use ($service){
//            return $value['origin_location_id'] == $service['origin_location_id'] && $value['destination_location_id'] == $service['destination_location_id'];
//        });
//
//        return $filtered;
//    }

    public function getDefaultDeliveryRoute()
    {
        /** @var DeliveryRoute $defaultDeliveryRoute */
        $defaultDeliveryRoute = $this->deliveryRoutes->filter(function (DeliveryRoute $d) {
            return $d->pivot->default;
        })->first();

        return $defaultDeliveryRoute ? $defaultDeliveryRoute : null;
    }

    public function getDefaultDeliveryRouteLegs()
    {
        /** @var DeliveryRoute $defaultDeliveryRoute */
        $defaultDeliveryRoute = $this->deliveryRoutes->filter(function (DeliveryRoute $d) {
            return $d->pivot->default;
        })->first();

        return $defaultDeliveryRoute ? $defaultDeliveryRoute->legs : null;
    }

    public function getOriginLocationCountryName()
    {
        return $this->originLocation ? $this->originLocation->getCountryName() : null;
    }

    public function getOriginLocationCountryCode()
    {
        return $this->originLocation ? $this->originLocation->getCountryCode() : null;
    }

    public function getDestinationLocationCountryId()
    {
        return $this->destinationLocation ? $this->destinationLocation->country_id : null;
    }

    public function getDestinationLocationCountryName()
    {
        return $this->destinationLocation ? $this->destinationLocation->getCountryName() : null;
    }

    public function getDestinationLocationCountryCode()
    {
        return $this->destinationLocation ? $this->destinationLocation->getCountryCode() : null;
    }

    public function getDestinationLocationCountryContinentAbbreviation()
    {
        return $this->destinationLocation ? $this->destinationLocation->getCountryContinentAbbreviation() : null;
    }

    public function getServiceTypeDescription()
    {
        return $this->serviceType ? $this->serviceType->description : null;
    }

    public function getServiceTypeKey()
    {
        return $this->serviceType ? $this->serviceType->key : null;
    }

    public function isDestinationLocationCountryMexico()
    {
        return $this->destinationLocation ? $this->destinationLocation->isCountryMexico() : false;
    }

    public function isDestinationLocationCountryChile()
    {
        return $this->destinationLocation ? $this->destinationLocation->isCountryChile() : false;
    }

    public function isBillingModeVolumetric()
    {
        return $this->billingMode ? $this->billingMode->isVolumetric() : false;
    }

    public function isEnabled()
    {
        return ($this->enabled);
    }

    public function isServiceTypePriority()
    {
        return $this->serviceType ? $this->serviceType->isPriority() : false;
    }

    public function isServiceTypeRegistered()
    {
        return $this->serviceType ? $this->serviceType->isRegistered() : false;
    }

    public function isServiceTypeStandard()
    {
        return $this->serviceType ? $this->serviceType->isStandard() : false;
    }

    public function hasAlternativeDeliveryRoutes()
    {
        return $this->deliveryRoutes->count() > 1;
    }

    public function getPresenterClass()
    {
        return ServicePresenter::class;
    }
}
