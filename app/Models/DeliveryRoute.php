<?php

namespace App\Models;

use App\Presenters\DeliveryRoutePresenter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class DeliveryRoute
 *
 * @package App
 * @property Location $originLocation
 * @property Location $destinationLocation
 * @property Collection $legs
 * @property int $id
 * @property int $origin_location_id
 * @property int $destination_location_id
 * @property int $enabled
 * @property string|null $label
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read mixed $leg_count
 * @mixin \Eloquent
 */
class DeliveryRoute extends Model implements HasPresenter
{
    protected $fillable = [
        'origin_location_id',
        'destination_location_id',
        'controlled_transit_days',
        'uncontrolled_transit_days',
        'total_transit_days',
        'enabled',
        'label',
    ];

    protected $with = ['originLocation', 'destinationLocation'];

    public function originLocation()
    {
        return $this->belongsTo(Location::class);
    }

    public function destinationLocation()
    {
        return $this->belongsTo(Location::class);
    }

    public function legs()
    {
        return $this->hasMany(Leg::class)->orderBy('legs.position');
    }

    public function scopeOfId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('delivery_routes.id', $id);
        } else {
            return !$id ? $query : $query->where('delivery_routes.id', $id);
        }
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

    public function scopeOfProviderServiceId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('legs.provider_service_id', $id);
        } else {
            return !$id ? $query : $query->where('legs.provider_service_id', $id);
        }
    }

    public function getControlledLegs()
    {
        return $this->legs->filter(function ($leg) {
            return $leg->controlled;
        });
    }

    public function getUncontrolledLegs()
    {
        return $this->legs->filter(function ($leg) {
            return !($leg->controlled);
        });
    }

    public function getFirstLeg()
    {
        return $this->legs->first();
    }

    public function getLegsCount()
    {
        return $this->legs->count();
    }

    public function getDistributionProviderName()
    {
        $distribution = $this->legs->filter(function($l) {
            /** @var Leg $l */
            return $l->isDistribution();
        })->first();

        return $distribution ? $distribution->getProviderServiceProviderName() : false;
    }

    public function getFirstControlledCheckpointCode()
    {
        /** @var Leg $leg */
        $leg = $this->legs->filter(function($l) {
            return $l->controlled;
        })->first();

        return $leg ? $leg->getProviderServiceFirstCheckpointCode() : null;
    }

    public function isFirstLegDistribution()
    {
        /** @var Leg $leg */
        if (!$leg = $this->legs->first()) {
            return false;
        }

        return $leg->isDistribution();
    }

    public function calculateControlledTransitDays()
    {
        return $this->legs->filter(function ($leg) {
            /** @var Leg $leg */
            return ($leg->controlled);
        })->sum(function ($leg) {
            /** @var Leg $leg */
            return $leg->getProviderServiceTransitDays();
        });
    }

    public function calculateUncontrolledTransitDays()
    {
        return $this->legs->filter(function ($leg) {
            /** @var Leg $leg */
            return !($leg->controlled);
        })->sum(function ($leg) {
            /** @var Leg $leg */
            return $leg->getProviderServiceTransitDays();
        });
    }

    public function calculateTotalTransitDays()
    {
        return $this->legs->sum(function ($leg) {
            /** @var Leg $leg */
            return $leg->getProviderServiceTransitDays();
        });
    }

    public function getOriginLocationCode()
    {
        return $this->originLocation ? $this->originLocation->code : null;
    }

    public function getDestinationLocationCode()
    {
        return $this->destinationLocation ? $this->destinationLocation->code : null;
    }
    
    public function getOriginLocationCountryName()
    {
        return $this->originLocation ? $this->originLocation->getCountryName() : null;
    }

    public function getDestinationLocationCountryName()
    {
        return $this->destinationLocation ? $this->destinationLocation->getCountryName() : null;
    }

    public function isEnabled()
    {
        return ($this->enabled);
    }

    public function isControlled(){

    }

    public function isDistribuitorBlueExpress()
    {
        $distribution = $this->legs->filter(function($l) {
            /** @var Leg $l */
            return $l->isDistribution();
        })->first();

        return $distribution ? $distribution->isProviderBlueExpress() : false;
    }

    public function isDistribuitorCorreosChile()
    {
        $distribution = $this->legs->filter(function($l) {
            /** @var Leg $l */
            return $l->isDistribution();
        })->first();

        return $distribution ? $distribution->isProviderChile() : false;
    }

    public function isDistribuitorTCC()
    {
        $distribution = $this->legs->filter(function($l) {
            /** @var Leg $l */
            return $l->isDistribution();
        })->first();

        return $distribution ? $distribution->isProviderTCC() : false;
    }

    public function isDistribuitor472()
    {
        $distribution = $this->legs->filter(function($l) {
            /** @var Leg $l */
            return $l->isDistribution();
        })->first();

        return $distribution ? $distribution->isProvider472() : false;
    }

    public function isDistribuitorQuality()
    {
        $distribuitors = $this->legs->filter(function($l) {
            /** @var Leg $l */
            return $l->isDistribution();
        });

        /** @var Leg $distribuitor */
        foreach ($distribuitors as $distribuitor) {
            if ($distribuitor->isProviderQuality()) {
                return true;
            }
        }

        return false;
    }

    public function isDistribuitorMexpost()
    {
        $distribution = $this->legs->filter(function($l) {
            /** @var Leg $l */
            return $l->isDistribution();
        })->first();

        return $distribution ? $distribution->isProviderMexpost() : false;
    }

    public function getPresenterClass()
    {
        return DeliveryRoutePresenter::class;
    }
}
