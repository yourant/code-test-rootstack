<?php

namespace App\Models;

use App\Presenters\BagPresenter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class Bag
 *
 * @property Dispatch dispatch
 * @property Collection $packages
 * @package App
 * @property int $id
 * @property int|null $dispatch_id
 * @property string $tracking_number
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read mixed $cn38
 * @property-read mixed $package_count
 * @property-read mixed $weight
 * @mixin \Eloquent
 */
class Bag extends Model implements HasPresenter
{

    protected $fillable = ['tracking_number', 'dispatch_id'];
    
//    protected $with = ['packages'];

//    protected $touches = ['dispatch'];

    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    public function dispatch()
    {
        return $this->belongsTo(Dispatch::class);
    }

    public function setTrackingNumberAttribute($value)
    {
        $this->attributes['tracking_number'] = strtoupper($value);
    }

    public function scopeOfId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('bags.id', $id);
        } else {
            return !$id ? $query : $query->where('bags.id', $id);
        }
    }

    public function scopeOfExcludeIds($query, $id)
    {
        if (is_array($id) && $id) {
            return $query->whereNotIn('bags.id', $id);
        } else {
            return !$id ? $query : $query->where('bags.id', '!=', $id);
        }
    }

    public function scopeOfTrackingNumber($query, $input)
    {
        if (is_array($input) && !empty($input)) {
            $query->where(function ($q2) use ($input) {
                collect($input)->each(function ($item) use($q2){
                    $q2->orWhere('bags.tracking_number', strtoupper($item));
                });
            });
            return $query;
        } else {
            return !$input ? $query : $query->where('bags.tracking_number', strtoupper($input));
        }
    }

    public function scopeOfCN38($query, $input)
    {
        if (is_array($input) && !empty($input)) {
            return $query->whereIn('dispatches.code', $input);
        } else {
            return !$input ? $query : $query->where('dispatches.code', $input);
        }
    }

    public function scopeOfAirWaybillCode($query, $input)
    {
        if (is_array($input) && !empty($input)) {
            return $query->whereIn('air_waybills.code', $input);
        } else {
            return !$input ? $query : $query->where('air_waybills.code', $input);
        }
    }

    public function scopeOfDispatchId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('bags.dispatch_id', $id);
        } else {
            return !$id ? $query : $query->where('bags.dispatch_id', $id);
        }
    }

//    public function scopeOfServiceTypeId($query, $id)
//    {
//        return !$id ? $query : $query->where('agreements.service_type_id', $id);
//    }

    public function scopeOfAgreementId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('packages.agreement_id', $id);
        } else {
            return !$id ? $query : $query->where('packages.agreement_id', $id);
        }
    }

    public function scopeOfClientId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('agreements.client_id', $id);
        } else {
            return !$id ? $query : $query->where('agreements.client_id', $id);
        }
    }

//    public function scopeOfCountryId($query, $id)
//    {
//        if (is_array($id) && !empty($id)) {
//            return $query->whereIn('agreements.country_id', $id);
//        } else {
//            return !$id ? $query : $query->where('agreements.country_id', $id);
//        }
//    }

    public function scopeOfDestinationCountryId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('destination_location.country_id', $id);
        } else {
            return !$id ? $query : $query->where('destination_location.country_id', $id);
        }
    }

    public function scopeOfYear($query, $year)
    {
        return $query->where('dispatches.year', $year);
    }

    public function getPackageCountAttribute()
    {
        return $this->packages ? $this->packages->count() : 0;
    }

    public function getWeightAttribute()
    {
        $weight = 0;
        foreach ($this->packages as $p) {
            $weight += $p->weight;
        }
        return $weight;
    }

    public function getCn38Attribute()
    {
        return $this->dispatch ? $this->dispatch->code : null;
    }

    public function detectCn38FromCn35()
    {
        return substr($this->tracking_number, 16, 4);
    }

    public function getFirstControlledCheckpoint()
    {
        $lps = Collection::make();
        /** @var Package $p */
        foreach ($this->packages as $p) {
            if ($ic = $p->firstControlledCheckpoint) {
                $lps->add($ic);
            }
        }

        return $lps ? $lps->sortBy('checkpoint_at')->first() : null;
    }

    public function getLastCheckpoint()
    {
        $lps = Collection::make();
        /** @var Package $p */
        foreach ($this->packages as $p) {
            $lps->add($p->lastCheckpoint);
        }

        return $lps ? $lps->sortByDesc('checkpoint_at')->first() : null;
    }

    public function getFirstCheckpointOfCheckpointCode(CheckpointCode $checkpointCode)
    {
        $lps = Collection::make();
        /** @var Package $p */
        foreach ($this->packages as $p) {
            $lps->add($p->getFirstCheckpointOfCheckpointCode($checkpointCode));
        }

        return $lps ? $lps->sortBy('checkpoint_at')->first() : null;
    }

    public function getDispatchAirWaybill()
    {
        return $this->dispatch ? $this->dispatch->airWaybill : null;
    }

    public function getDispatchAirWaybillCode()
    {
        return $this->dispatch ? $this->dispatch->getAirWaybillCode() : null;
    }
    
//    public function getFirstPackageAgreementCountryName()
//    {
//        $first_package = $this->packages->first();
//
//        return $first_package ? $first_package->getAgreementCountryName() : null;
//    }

    public function getFirstPackageAgreementServiceDestinationLocationCountryName()
    {
        /** @var Package $first_package */
        $first_package = $this->packages->first();

        return $first_package ? $first_package->getAgreementServiceDestinationLocationCountryName() : null;
    }

//    public function getFirstPackageAgreementCountryCode()
//    {
//        $first_package = $this->packages->first();
//
//        return $first_package ? $first_package->getAgreementCountryCode() : null;
//    }

    public function getFirstPackageAgreementServiceDestinationLocationCountryCode()
    {
        /** @var Package $first_package */
        $first_package = $this->packages->first();

        return $first_package ? $first_package->getAgreementServiceDestinationLocationCountryCode() : null;
    }

//    public function getFirstPackageAgreementName()
//    {
//        $first_package = $this->packages->first();
//
//        return $first_package ? $first_package->getAgreementName() : null;
//    }
    
//    public function getFirstPackageAgreementTransitDays()
//    {
//        $first_package = $this->packages->first();
//
//        return $first_package ? $first_package->getAgreementTransitDays() : null;
//    }

    public function getFirstPackageAgreementServiceTransitDays()
    {
        /** @var Package $first_package */
        $first_package = $this->packages->first();

        return $first_package ? $first_package->getAgreementServiceTransitDays() : null;
    }

//    public function getFirstPackageAgreementFirstControlledCheckpointCode()
//    {
//        $first_package = $this->packages->first();
//
//        return $first_package ? $first_package->getAgreementFirstControlledCheckpointCode() : null;
//    }

    public function getFirstPackageDeliveryRouteFirstControlledCheckpointCode()
    {
        /** @var Package $first_package */
        $first_package = $this->packages->first();

        return $first_package ? $first_package->getDeliveryRouteFirstControlledCheckpointCode() : null;
    }

//    public function getFirstPackageAgreementLastUncontrolledCheckpointCode()
//    {
//        $first_package = $this->packages->first();
//
//        return $first_package ? $first_package->getAgreementLastUncontrolledCheckpointCode() : null;
//    }

    public function getFirstPackageDeliveryRouteLastUncontrolledCheckpointCode()
    {
        /** @var Package $first_package */
        $first_package = $this->packages->first();

        return $first_package ? $first_package->getDeliveryRouteLastUncontrolledCheckpointCode() : null;
    }

    public function getPresenterClass()
    {
        return BagPresenter::class;
    }
}