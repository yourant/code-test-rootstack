<?php

namespace App\Models;

use App\Presenters\DispatchPresenter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class Dispatch
 *
 * @package App
 * @property Agreement $agreement
 * @property AirWaybill $airWaybill
 * @property Collection $bags
 * @property int $id
 * @property int|null $air_waybill_id
 * @property int $code
 * @property int|null $year
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string|null $deleted_at
 * @mixin \Eloquent
 */
class Dispatch extends Model implements HasPresenter
{
    protected $fillable = [
        'air_waybill_id', 
        'code', 
        'year'
    ];
    
//    protected $with = ['bags'];

    public function bags()
    {
        return $this->hasMany(Bag::class);
    }

    public function airWaybill()
    {
        return $this->belongsTo(AirWaybill::class);
    }

    public function scopeOfClientId($query, $client_id)
    {
        if (is_array($client_id)) {
            return $query->whereIn('agreements.client_id', $client_id);
        } else {
            return !$client_id ? $query : $query->where('agreements.client_id', $client_id);
        }
    }

    public function scopeOfMarketplaceId($query, $id)
    {
        if (is_array($id)) {
            return $query->whereIn('client_marketplace.marketplace_id', $id);
        } else {
            return !$id ? $query : $query->where('client_marketplace.marketplace_id', $id);
        }
    }

    public function scopeOfDestinationCountryId($query, $id)
    {
        if (is_array($id)) {
            return $query->whereIn('destination_location.country_id', $id);
        } else {
            return !$id ? $query : $query->where('destination_location.country_id', $id);
        }
    }

    public function scopeOfYear($query, $year)
    {
        return !$year ? $query : $query->where('dispatches.year', $year);
    }

    public function scopeOfCode($query, $input, $operator = '=')
    {
        return $query->where('dispatches.code', $operator, $input);
    }

    public function scopeOfCountryCode($query, $code)
    {
        if (is_array($code)) {
            return $query->whereIn('countries.code', $code);
        } else {
            return !$code ? $query : $query->where('countries.code', $code);
        }
    }

    public function scopeOfProviderCode($query, $code) {
        return !$code ? $query : $query->where('providers.code', $code);
    }

    public function scopeOfProviderKey($query, $key) {
        return !$key ? $query : $query->where('provider_service_types.key', $key);
    }

    public function scopeOfAirWaybillId($query, $id) {
        if (is_array($id) && $id) {
            return $query->whereIn('dispatches.air_waybill_id', $id);
        } else {
            if ($id) {
                return $query->where('dispatches.air_waybill_id', $id);
            } else {
                return $query->whereNull('dispatches.air_waybill_id');
            }
        }
    }

    public function scopeOfId($query, $id)
    {
        if (is_array($id) && $id) {
            return $query->whereIn('dispatches.id', $id);
        } else {
            return !$id ? $query : $query->where('dispatches.id', $id);
        }
    }

    public function scopeOfExcludeIds($query, $id)
    {
        if (is_array($id) && $id) {
            return $query->whereNotIn('dispatches.id', $id);
        } else {
            return !$id ? $query : $query->where('dispatches.id', '!=', $id);
        }
    }

    public function scopeOfLastMileProviderCode($query, $code) {
        return !$code ? $query : $query->where('providers.code', $code)->where('service_types.type','like', 'last_mile');
    }

    public function scopeOfCn38($query, $cn38)
    {
        if (is_array($cn38) && !empty($cn38)) {
            $query->where(function ($query2) use ($cn38) {
                // Process each element
                collect($cn38)->each(function ($item) use (&$query2) {
                    // Split
                    if ($parts = preg_split('/\//', $item)) {
                        $code = intval($parts[0]);
                        $year = isset($parts[1]) ? intval($parts[1]) : null;

                        $query2->orWhere(function ($query3) use ($code, $year) {
                            $query3->where('dispatches.code', $code);
                            if ($year) {
                                $query3->where('dispatches.year', $year);
                            }
                        });
                    }
                });
            });
        } elseif ($cn38) {
            $parts = preg_split('/\//', $cn38);
            $code = intval($parts[0]);
            $year = isset($parts[1]) ? intval($parts[1]) : null;
            $query->where('dispatches.code', $code);
            if ($year) {
                $query->where('dispatches.year', $year);
            }
        }
        return $query;
    }

//    public function scopeOfCreateAt($query, $created_at) {
//        return !$created_at ? $query : $query->where('dispatches.created_at', '>=', $created_at);
//    }
    
//    public function getFirstBagFirstPackageAgreementName()
//    {
//        /** @var Bag $first_bag */
//        $first_bag = $this->bags->first();
//        
//        return $first_bag ? $first_bag->getFirstPackageAgreementName() : null;
//    }

    public function getFirstBagFirstPackageAgreementServiceName()
    {
        /** @var Bag $first_bag */
        $first_bag = $this->bags->first();

        return $first_bag ? $first_bag->getFirstPackageAgreementServiceDestinationLocationCountryName() : null;
    }

//    public function getFirstBagFirstPackageAgreementTransitDays()
//    {
//        $first_bag = $this->bags->first();
//
//        return $first_bag ? $first_bag->getFirstPackageAgreementTransitDays() : null;
//    }

    public function getFirstBagFirstPackageAgreementServiceTransitDays()
    {
        /** @var Bag $first_bag */
        $first_bag = $this->bags->first();

        return $first_bag ? $first_bag->getFirstPackageAgreementServiceTransitDays() : null;
    }
    
    public function getAirWaybillId()
    {
        return $this->airWaybill ? $this->airWaybill->id : null;
    }

    public function getAirWaybillCode()
    {
        return $this->airWaybill ? $this->airWaybill->code : null;
    }

//    public function getFirstBagFirstPackageAgreementFirstControlledCheckpointCode()
//    {
//        $first_bag = $this->bags->first();
//
//        return $first_bag ? $first_bag->getFirstPackageAgreementFirstControlledCheckpointCode() : null;
//    }

    public function getFirstBagFirstPackageDeliveryRouteFirstControlledCheckpointCode()
    {
        /** @var Bag $first_bag */
        $first_bag = $this->bags->first();

        return $first_bag ? $first_bag->getFirstPackageDeliveryRouteFirstControlledCheckpointCode() : null;
    }

//    public function getFirstBagFirstPackageAgreementLastUncontrolledCheckpointCode()
//    {
//        $first_bag = $this->bags->first();
//
//        return $first_bag ? $first_bag->getFirstPackageAgreementLastUncontrolledCheckpointCode() : null;
//    }

    public function getFirstBagFirstPackageDeliveryRouteLastUncontrolledCheckpointCode()
    {
        /** @var Bag $first_bag */
        $first_bag = $this->bags->first();

        return $first_bag ? $first_bag->getFirstPackageDeliveryRouteLastUncontrolledCheckpointCode() : null;
    }

//    public function getFirstBagFirstPackageAgreementCountryCode()
//    {
//        $first_bag = $this->bags->first();
//
//        return $first_bag ? $first_bag->getFirstPackageAgreementCountryCode() : null;
//    }

    public function getFirstBagFirstPackageAgreementServiceDestinationLocationCountryCode()
    {
        /** @var Bag $first_bag */
        $first_bag = $this->bags->first();

        return $first_bag ? $first_bag->getFirstPackageAgreementServiceDestinationLocationCountryCode() : null;
    }

    public function getFirstControlledCheckpoint()
    {
        $c = Collection::make();

        /** @var CheckpointCode $fcc */
        if (!$fcc = $this->getFirstBagFirstPackageDeliveryRouteFirstControlledCheckpointCode()) {
            return null;
        }

        /** @var Bag $bag */
        foreach ($this->bags as $bag) {
            if ($fc = $bag->getFirstCheckpointOfCheckpointCode($fcc)) {
                $c->add($fc);
            }
        }

        return $c ? $c->sortBy('checkpoint_at')->first() : null;
    }

    public function getLastCheckpoint()
    {
        $c = Collection::make();
        /** @var Bag $bag */
        foreach ($this->bags as $bag) {
            if ($lc = $bag->getLastCheckpoint()) {
                $c->add($lc);
            }
        }

        return $c ? $c->sortByDesc('checkpoint_at')->first() : null;
    }

    public function isFinished()
    {
        $finished = $total = 0;
        foreach ($this->bags as $bag) {
            /** @var Package $package */
            foreach ($bag->packages as $package) {
                $finished += ($package->isFinished());
                ++$total;
            }
        }

        return ($finished == $total);
    }

    public function getElapsedDays()
    {
        $ic = $this->getFirstControlledCheckpoint();
        $lc = $this->getLastCheckpoint();

        if ($this->isFinished()) {
            return ($ic && $lc) ? $ic->checkpoint_at_carbon->diffInDays($lc->checkpoint_at_carbon) : null;
        } else {
            return $ic ? $ic->checkpoint_at_carbon->diffInDays(Carbon::now()) : null;
        }
    }

    public function getBagCount()
    {
        return $this->bags ? $this->bags->count() : 0;
    }

    public function getTotalWeight()
    {
        return $this->bags ? $this->bags->sum('weight') : 0;
    }

    public function getTotalPackages()
    {
        return $this->bags->sum('package_count');
    }

    public function getPresenterClass()
    {
        return DispatchPresenter::class;
    }
}