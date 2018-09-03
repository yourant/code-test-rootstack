<?php

namespace App\Models;

use App\Presenters\LegPresenter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class Leg
 *
 * @package App
 * @property ProviderService $providerService
 * @property DeliveryRoute $deliveryRoute
 * @property int $id
 * @property int $delivery_route_id
 * @property int $provider_service_id
 * @property int $position
 * @property int $controlled
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @mixin \Eloquent
 */
class Leg extends Model implements HasPresenter
{
    use SoftDeletes;

    protected $fillable = [
        'delivery_route_id', 
        'provider_service_id', 
        'position', 
        'controlled',
    ];

    protected $with = ['providerService'];

    protected $touches = [];

    public function deliveryRoute()
    {
        return $this->belongsTo(DeliveryRoute::class);
    }
    
    public function providerService()
    {
        return $this->belongsTo(ProviderService::class)->withTrashed();
    }

    /*public function scopeOfAgreementId($query, $id)
    {
        return !$id ? $query : $query->where('legs.agreement_id', $id);
    }
    */

    public function scopeOfDeliveryRouteId($query, $id)
    {
        return !$id ? $query : $query->where('legs.delivery_route_id', $id);
    }

    public function scopeOfPosition($query, $position)
    {
        return !$position ? $query : $query->where('legs.position', $position);
    }

    /*
    public function scopeOfProviderId($query, $id)
    {
        return !$id ? $query : $query->where('service_types.provider_id', $id);
    }
    */

    public function scopeOfProviderServiceId($query, $id)
    {
        return !$id ? $query : $query->where('provider_services.provider_id', $id);
    }
    
    public function getProviderServiceTransitDays()
    {
        return $this->providerService ? $this->providerService->transit_days : null;
    }

    /*
    public function getServiceTypeFirstCheckpointCode()
    {
        return $this->serviceType ? $this->serviceType->firstCheckpointCode : null;
    }
    */

    public function getProviderServiceFirstCheckpointCode()
    {
        return $this->providerService ? $this->providerService->firstCheckpointCode : null;
    }

    public function getProviderServiceFirstCheckpointCodeDescription()
    {
        return $this->providerService ? $this->providerService->getFirstCheckpointCodeDescription() : null;
    }

    /*
    public function getServiceTypeFirstCheckpointCodeDescription()
    {
        return $this->serviceType ? $this->serviceType->getFirstCheckpointCodeDescription() : null;
    }
    */

    /*
    public function getServiceTypeFirstCheckpointCodeKey()
    {
        return $this->serviceType ? $this->serviceType->getFirstCheckpointCodeKey() : null;
    }
    */

    public function getProviderServiceFirstCheckpointCodeKey()
    {
        return $this->providerService ? $this->providerService->getFirstCheckpointCodeKey() : null;
    }
    
    /*
    public function getServiceTypeLastCheckpointCode()
    {
        return $this->serviceType ? $this->serviceType->lastCheckpointCode : null;
    }
    */

    public function getProviderServiceLastCheckpointCode()
    {
        return $this->providerService ? $this->providerService->lastCheckpointCode : null;
    }
    
    /*
    public function getServiceTypeLastCheckpointCodeDescription()
    {
        return $this->serviceType ? $this->serviceType->getLastCheckpointCodeDescription() : null;
    }
    */

    public function getProviderServiceLastCheckpointCodeDescription()
    {
        return $this->providerService ? $this->providerService->getLastCheckpointCodeDescription() : null;
    }

    public function getProviderServiceLastCheckpointCodeKey()
    {
        return $this->providerService ? $this->providerService->getLastCheckpointCodeKey() : null;
    }
    
    /*
    public function getServiceTypeLastCheckpointCodeKey()
    {
        return $this->serviceType ? $this->serviceType->getLastCheckpointCodeKey() : null;
    }
    */

    /*
    public function getServiceTypeProviderTimezone()
    {
        return $this->serviceType ? $this->serviceType->getProviderTimezone() : null;
    }
    */

    public function getProviderServiceProviderTimezone()
    {
        return $this->providerService ? $this->providerService->getProviderTimezone() : null;
    }

    /*
    public function getServiceTypeType()
    {
        return $this->serviceType ? $this->serviceType->type : null;
    }
    */

    /*
    public function getServiceTypeName()
    {
        return $this->serviceType ? $this->serviceType->name : null;
    }
    */

    public function getProviderServiceName()
    {
        return $this->providerService ? $this->providerService->name : null;
    }

    /*
    public function getServiceTypeProvider()
    {
        return $this->serviceType ? $this->serviceType->provider : null;
    }
    */

    public function getProviderServiceProvider()
    {
        return $this->providerService ? $this->providerService->provider : null;
    }

    /*
    public function getProviderId()
    {
        return $this->serviceType ? $this->serviceType->provider : null;
    }
    */

    public function getProviderId()
    {
        return $this->providerService ? $this->providerService->getProviderId() : null;
    }

    /*
    public function getServiceTypeProviderName()
    {
        return $this->serviceType ? $this->serviceType->provider_name : null;
    }
    */

    public function getProviderServiceProviderName()
    {
        return $this->providerService ? $this->providerService->getProviderName() : null;
    }

    /*
    public function getServiceTypeProviderCheckpointCodes()
    {
        return $this->serviceType ? $this->serviceType->getProviderCheckpointCodes() : null;
    }
    */

    public function getProviderServiceProviderCheckpointCodes()
    {
        return $this->providerService ? $this->providerService->getProviderCheckpointCodes() : null;
    }

    public function isControlled()
    {
        return $this->controlled ? $this->controlled : false;
    }
    
    public function isDistribution()
    {
        return $this->providerService ? $this->providerService->isProviderServiceTypeDistribution() : false;
    }

    public function isProviderSepomex()
    {
        return $this->providerService ? $this->providerService->isProviderSepomex() : false;
    }

    public function isProviderQuality()
    {
        return $this->providerService ? $this->providerService->isProviderQuality() : false;
    }

    public function isProviderMexpost()
    {
        return $this->providerService ? $this->providerService->isProviderMexpost() : false;
    }

    public function isProvider472()
    {
        return $this->providerService ? $this->providerService->isProvider472() : false;
    }

    public function isProviderCorreios()
    {
        return $this->providerService ? $this->providerService->isProviderCorreios() : false;
    }

    public function isProviderSerpost()
    {
        return $this->providerService ? $this->providerService->isProviderSerpost() : false;
    }

    public function isProviderSinotrans()
    {
        return $this->providerService ? $this->providerService->isProviderSinotrans() : false;
    }

    public function isProviderGlobalMatch()
    {
        return $this->providerService ? $this->providerService->isProviderGlobalMatch() : false;
    }

    public function isProviderChile()
    {
        return $this->providerService ? $this->providerService->isProviderChile() : false;
    }

    public function isProviderBlueExpress()
    {
        return $this->providerService ? $this->providerService->isProviderBlueExpress() : false;
    }
    
    public function isProviderSellerDropOff()
    {
        return $this->providerService ? $this->providerService->isProviderSellerDropOff() : false;
    }

    public function isProviderUrbano()
    {
        return $this->providerService ? $this->providerService->isProviderUrbano() : false;
    }

    public function isProviderTCC()
    {
        return $this->providerService ? $this->providerService->isProviderTCC() : false;
    }

    public function getPresenterClass()
    {
        return LegPresenter::class;
    }
}