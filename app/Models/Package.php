<?php

namespace App\Models;

use App\Models\MercadoLibre\CbtInvoice;
use App\Presenters\PackagePresenter;
use App\Repositories\ProviderServiceRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class Package
 *
 * @package App
 * @property Bag $bag
 * @property Agreement $agreement
 * @property DeliveryRoute $deliveryRoute
 * @property Leg $leg
 * @property Collection $checkpoints
 * @property Collection $events
 * @property ZipCode $zipCode
 * @property Collection $preadmissions
 * @property Collection $aliases
 * @property Marketplace $marketplace
 * @property PostalOffice $postalOffice
 * @property Collection $prealerts
 * @property Collection $cbtInvoices
 * @property Collection $volumetricScaleMeasurements
 * @property int $id
 * @property int|null $bag_id
 * @property int|null $new_leg_id
 * @property int|null $agreement_id
 * @property int|null $delivery_route_id
 * @property int|null $marketplace_id
 * @property int|null $uploaded_by
 * @property int|null $first_checkpoint_id
 * @property string|null $first_checkpoint_at
 * @property int|null $last_checkpoint_id
 * @property string|null $last_checkpoint_at
 * @property int|null $first_controlled_checkpoint_id
 * @property string|null $first_controlled_checkpoint_at
 * @property int|null $first_clockstop_id
 * @property string|null $first_clockstop_at
 * @property int $delivered
 * @property int $returned
 * @property int $canceled
 * @property int $stalled
 * @property int $returning
 * @property int $clockstop
 * @property string $tracking_number
 * @property string|null $alias
 * @property string $customer_tracking_number
 * @property string|null $sales_order_number
 * @property string $buyer
 * @property string|null $buyer_id
 * @property string $company
 * @property string $address1
 * @property string|null $address2
 * @property string|null $address3
 * @property string|null $district
 * @property string $city
 * @property string|null $state
 * @property string|null $location
 * @property string $zip
 * @property int|null $zip_code_id
 * @property string|null $distribution_center
 * @property int|null $postal_office_id
 * @property string $country
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $shipper
 * @property string|null $shipper_address1
 * @property string|null $shipper_address2
 * @property string|null $shipper_city
 * @property string|null $shipper_state
 * @property string|null $shipper_zip
 * @property string|null $shipper_country
 * @property float $value
 * @property float $freight
 * @property float $net_weight
 * @property float $weight
 * @property float|null $verified_weight
 * @property float|null $vol_weight
 * @property float|null $verified_vol_weight
 * @property float|null $calculated_vol_weight
 * @property float $width
 * @property float|null $verified_width
 * @property float $height
 * @property float|null $verified_height
 * @property float $length
 * @property float|null $verified_length
 * @property string|null $origin_warehouse_code
 * @property string $service_type
 * @property int $returns_allowed
 * @property int $ddp
 * @property string|null $job_order
 * @property string $classification
 * @property string|null $invoice_number
 * @property float|null $invoice_amount
 * @property string|null $invoice_currency
 * @property string|null $invoiced_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @mixin \Eloquent
 */
class Package extends Model implements HasPresenter
{
    protected $fillable = [
        'bag_id',
        'agreement_id',
        'marketplace_id',
        'uploaded_by',
        'delivered',
        'returned',
        'canceled',
        'returning',
        'stalled',
        'clockstop',
        'tracking_number',
        'alias',
        'customer_tracking_number',
        'sales_order_number',
        'buyer',
        'buyer_id',
        'company',
        'address1',
        'address2',
        'address3',
        'district',
        'city',
        'state',
        'location',
        'zip',
        'distribution_center',
        'country',
        'phone',
        'email',
        'shipper',
        'shipper_address1',
        'shipper_address2',
        'shipper_city',
        'shipper_state',
        'shipper_zip',
        'shipper_country',
        'value',
        'freight',
        'net_weight',
        'weight',
        'verified_weight',
        'vol_weight',
        'verified_vol_weight',
        'calculated_vol_weight',
        'billable_weight',
        'billable_method',
        'width',
        'verified_width',
        'height',
        'verified_height',
        'length',
        'verified_length',
        'origin_warehouse_code',
        'verified_origin_warehouse_code',
        'service_type',
        'returns_allowed',
        'ddp',
        'job_order',
        'classification',
        'created_at',
        'invoice_number',
        'invoice_amount',
        'invoice_currency',
        'invoiced_at',
        'delivery_route_id',
        'm_verified_weight',
        'm_verified_vol_weight',
        'm_verified_width',
        'm_verified_height',
        'm_verified_length',
        'm_calculated_vol_weight',
        'm_billable_weight'

    ];

    protected $hidden = [
        'id',
        'bag_id',
        'agreement_id',
        'marketplace_id',
//        'delivery_route_id',
        'leg_id',
        'uploaded_by',
        'postal_office_id',
        'zip_code_id',
        'alias',
        'first_checkpoint_id',
        'first_checkpoint_at',
        'last_checkpoint_id',
        'last_checkpoint_at',
        'first_controlled_checkpoint_id',
        'first_controlled_checkpoint_at',
        'first_clockstop_id',
        'first_clockstop_at',
        'delivered',
        'returned',
        'canceled',
        'stalled',
        'returning',
        'clockstop',
        'shipper',
        'shipper_address1',
        'shipper_address2',
        'shipper_city',
        'shipper_state',
        'shipper_zip',
        'shipper_country',
        'verified_weight',
        'service_type',
        'returns_allowed',
        'job_order',
        'created_at',
        'updated_at',
        'invoice_number',
        'invoice_amount',
        'invoice_currency',
        'invoiced_at'
    ];

//    protected $touches = ['bag'];

    protected $with = ['agreement'];

    public function bag()
    {
        return $this->belongsTo(Bag::class);
    }

    public function agreement()
    {
        return $this->belongsTo(Agreement::class);
    }

    public function deliveryRoute()
    {
        return $this->belongsTo(DeliveryRoute::class);
    }

    public function leg()
    {
        return $this->belongsTo(Leg::class);
    }

    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function firstCheckpoint()
    {
        return $this->belongsTo(Checkpoint::class);
    }

    public function lastCheckpoint()
    {
        return $this->belongsTo(Checkpoint::class);
    }

    public function firstControlledCheckpoint()
    {
        return $this->belongsTo(Checkpoint::class);
    }

    public function firstClockstop()
    {
        return $this->belongsTo(Checkpoint::class);
    }

    public function postalOffice()
    {
        return $this->belongsTo(PostalOffice::class);
    }

    public function zipCode()
    {
        return $this->belongsTo(ZipCode::class);
    }

    public function packageItems()
    {
        return $this->hasMany(PackageItem::class);
    }

    public function aliases()
    {
        return $this->hasMany(Alias::class);
    }

    public function checkpoints()
    {
        return $this->hasMany(Checkpoint::class)->orderBy('checkpoint_at', 'asc');
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function preadmissions()
    {
        return $this->belongsToMany(Preadmission::class);
    }

    public function prealerts()
    {
        return $this->hasMany(Prealert::class)->orderBy('prealerts.created_at', 'desc');
    }

    public function sortingGate()
    {
        return $this->belongsTo(SortingGate::class);
    }

    public function cbtInvoices()
    {
        return $this->hasMany(CbtInvoice::class);
    }

    public function providerInvoices()
    {
        return $this->belongsToMany(ProviderInvoice::class)->withPivot('amount')->withTimestamps();
    }

    public function volumetricScaleMeasurements()
    {
        return $this->hasMany(VolumetricScaleMeasurement::class);
    }

    public function setTrackingNumberAttribute($value)
    {
        $this->attributes['tracking_number'] = strtoupper($value);
    }

    public function setCustomerTrackingNumberAttribute($value)
    {
        $this->attributes['customer_tracking_number'] = strtoupper($value);
    }

    public function scopeOfProviderServiceId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('current_legs.provider_service_id', $id);
        } else {
            return !$id ? $query : $query->where('current_legs.provider_service_id', $id);
        }
    }

    public function scopeOfAgreementServiceServiceTypeKey($query, $service_type_key)
    {
        if (is_array($service_type_key) && !empty($service_type_key)) {
            return $query->whereIn('service_types.key', $service_type_key);
        } else {
            return !$service_type_key ? $query : $query->where('service_types.key', $service_type_key);
        }
    }

    public function scopeOfAgreementServiceId($query, $service_id)
    {
        if (is_array($service_id) && !empty($service_id)) {
            return $query->whereIn('services.id', $service_id);
        } else {
            return !$service_id ? $query : $query->where('services.id', $service_id);
        }
    }

    public function scopeOfOriginWarehouseCode($query, $warehouse_code)
    {
        if (is_array($warehouse_code) && !empty($warehouse_code)) {
//            return $query->whereIn('packages.origin_warehouse_code', $warehouse_code);
            return $query->where(function ($q2) use ($warehouse_code) {
                return $q2->where(function ($q3) use ($warehouse_code) {
                    $q3->whereNotNull('packages.verified_origin_warehouse_code')
                        ->whereIn('packages.verified_origin_warehouse_code', $warehouse_code);
                })
                    ->orWhere(function ($q4) use ($warehouse_code) {
                        $q4->whereNull('packages.verified_origin_warehouse_code')
                            ->whereIn('packages.origin_warehouse_code', $warehouse_code);
                    });
            });
        } else {
            return $query->where(function ($q2) use ($warehouse_code) {
                return $q2->where(function ($q3) use ($warehouse_code) {
                    $q3->whereNotNull('packages.verified_origin_warehouse_code')
                        ->where('packages.verified_origin_warehouse_code', $warehouse_code);
                })
                    ->orWhere(function ($q4) use ($warehouse_code) {
                        $q4->whereNull('packages.verified_origin_warehouse_code')
                            ->where('packages.origin_warehouse_code', $warehouse_code);
                    });
            });
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

    public function scopeOfClientName($query, $name)
    {
        return !$name ? $query : $query->where('clients.name', 'ilike', $name);
    }

    public function scopeOfClientAccessToken($query, $access_token)
    {
        if (!$access_token) {
            return $query->whereNull('clients.access_token');
        }

        return $query->where('clients.access_token', $access_token);
    }

    public function scopeOfCheckpointCodeId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('checkpoints.checkpoint_code_id', $id);
        } else {
            return !$id ? $query : $query->where('checkpoints.checkpoint_code_id', $id);
        }
    }

    public function scopeOfTrackingNumber($query, $tn)
    {
        if (is_array($tn) && !empty($tn)) {
            $query->where(function ($q2) use ($tn) {
                collect($tn)->each(function ($item) use ($q2) {
                    $q2->orWhere('packages.tracking_number', strtoupper($item));
                });
            });

            return $query;
        } else {
            return !$tn ? $query : $query->where('packages.tracking_number', strtoupper($tn));
        }
    }

    public function scopeOfCustomerTrackingNumber($query, $ctn)
    {
        if (is_array($ctn) && !empty($ctn)) {
            $query->where(function ($q2) use ($ctn) {
                collect($ctn)->each(function ($item) use ($q2) {
                    $q2->orWhere('packages.customer_tracking_number', strtoupper($item));
                });
            });

            return $query;
        } else {
            return !$ctn ? $query : $query->where('packages.customer_tracking_number', strtoupper($ctn));
        }
    }

    public function scopeOfTrackingOrCustomerTracking($query, $tn)
    {
        if (is_array($tn) && !empty($tn)) {
            $query->where(function ($q2) use ($tn) {
                collect($tn)->each(function ($item) use ($q2) {
                    $q2->orWhere(function ($q3) use ($item) {
                        return $q3->orWhere('packages.customer_tracking_number', strtoupper($item))
                            ->orWhere('packages.tracking_number', strtoupper($item));
//                            ->orWhere('packages.alias', $item);
                    });
                });
            });

            return $query;
        } else {
            return !$tn ? $query : $query->where(function ($q2) use ($tn) {
                $q2->where('packages.customer_tracking_number', strtoupper($tn));
                $q2->orWhere('packages.tracking_number', strtoupper($tn));
//                $q2->orWhere('packages.alias', $tn);
            });
        }
    }

    public function scopeOfJobOrder($query, $job_order)
    {
        if (is_array($job_order) && !empty($job_order)) {
            return $query->where(function ($q2) use ($job_order) {
                $q2->whereIn('packages.job_order', $job_order);
            });
        } else {
            return !$job_order ? $query : $query->where(function ($q2) use ($job_order) {
                $q2->where('packages.job_order', 'like', "%{$job_order}%");
            });
        }
    }

    public function scopeOfCountry($query, $country)
    {
        return !$country ? $query : $query->where('packages.country', $country);
    }

    public function scopeOfDispatchCode($query, $cn38)
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

    public function scopeOfBagTrackingNumber($query, $cn35)
    {
        if (is_array($cn35) && !empty($cn35)) {
            $query->where(function ($q2) use ($cn35) {
                collect($cn35)->each(function ($item) use ($q2) {
                    $q2->orWhere('bags.tracking_number', strtoupper($item));
                });
            });

            return $query;
        } else {
            return !$cn35 ? $query : $query->where('bags.tracking_number', strtoupper($cn35));
        }
    }

    public function scopeOfBagId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('packages.bag_id', $id);
        } else {
            return !$id ? $query : $query->where('packages.bag_id', $id);
        }
    }

    public function scopeOfMarketplaceId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->where(function ($subquery) use ($id) {
                return $subquery->whereIn('client_marketplace.marketplace_id', $id)->orWhereIn('packages.marketplace_id', $id);
            });
        } else {
            return !$id ? $query : $query->where(function ($subquery) use ($id) {
                return $subquery->where('client_marketplace.marketplace_id', $id)->orWhere('packages.marketplace_id', $id);
            });
        }
    }

    public function scopeOfLegId($query, $id)
    {
        return !$id ? $query : $query->where('packages.leg_id', $id);
    }

    public function scopeOfDistribution($query)
    {
        return $query->where('provider_service_types.key', 'distribution');
    }

    public function scopeOfNotDistribution($query)
    {
        return $query->where('provider_service_types.key', '<>', 'distribution');
    }

    public function scopeOfDispatchId($query, $id)
    {
        return !$id ? $query : $query->where('bags.dispatch_id', $id);
    }

    public function scopeOfDestinationCountryId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('destination_locations.country_id', $id);
        } else {
            return !$id ? $query : $query->where('destination_locations.country_id', $id);
        }
    }

    public function scopeOfAirWaybillId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('dispatches.air_waybill_id', $id);
        } else {
            return !$id ? $query : $query->where('dispatches.air_waybill_id', $id);
        }
    }

    public function scopeOfAirWaybillCode($query, $code)
    {
        if (is_array($code) && !empty($code)) {
            return $query->whereIn('air_waybills.code', $code);
        } else {
            return !$code ? $query : $query->where('air_waybills.code', $code);
        }
    }

    public function scopeOfHasFirstControlledCheckpoint($query)
    {
        return $query->whereNotNull('packages.first_controlled_checkpoint_at');
    }

    public function scopeOfControlled($query)
    {
        return $query->where('current_legs.controlled', true);
    }

    public function scopeOfUncontrolled($query)
    {
        return $query->where('current_legs.controlled', false);
    }

    public function scopeOfReturnsAllowed($query)
    {
        return $query->where('packages.returns_allowed', true);
    }

    public function scopeOfReturnsNotAllowed($query)
    {
        return $query->where('packages.returns_allowed', false);
    }

    public function scopeOfCreatedBeforeThan($query, $date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');

        return !$date ? $query : $query->where('packages.created_at', '<=', $date . ' 23:59:59');
    }

    public function scopeOfCreatedAfterThan($query, $date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');

        return !$date ? $query : $query->where('packages.created_at', '>=', $date . ' 00:00:00');
    }

    public function scopeOfCheckpointFilteredNewerThan($query, $date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');

        return $query
            ->where('filtered_checkpoints.checkpoint_at', '>=', $date . ' 00:00:00');
    }

    public function scopeOfCheckpointFilteredOlderThan($query, $date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');

        return $query
            ->where('filtered_checkpoints.checkpoint_at', '<=', $date . ' 23:59:59');
    }

    public function scopeOfProviderInvoiceDateFrom($query, $date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');

        return $query
            ->where('provider_invoices.invoiced_at', '>=', $date . ' 00:00:00');
    }

    public function scopeOfProviderInvoiceDateTo($query, $date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');

        return $query
            ->where('provider_invoices.invoiced_at', '<=', $date . ' 23:59:59');
    }

    public function scopeOfInvoiced($query)
    {
        return $query->whereNotNull('packages.invoiced_at');
    }

    public function scopeOfUninvoiced($query)
    {
        return $query->whereNull('packages.invoiced_at');
    }

    public function scopeOfInvoiceNumber($query, $number)
    {
        return !$number ? $query : $query->where('packages.invoice_number', $number);
    }

    public function scopeOfInvoicedAtNewerThan($query, $date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');

        return $query
            ->where('packages.invoiced_at', '>=', $date . ' 00:00:00');
    }

    public function scopeOfInvoicedAtOlderThan($query, $date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');

        return $query
            ->where('packages.invoiced_at', '<=', $date . ' 23:59:59');
    }

    public function scopeOfUncontrolledUnbillable($query)
    {
        return $query->where('clients.name', '<>', 'LIDA E-COMMERCE (HONG KONG) LIMITED')
            ->where('clients.name', '<>', 'MercadoLibre')
            ->where('clients.name', '<>', 'Eworldhome Limited')
            ->where('clients.name', '<>', 'DealXtreme');
    }

    public function scopeOfVerifiedWeight($query)
    {
        return $query->whereNotNull('packages.verified_weight');
    }

    public function scopeOfUnverifiedWeight($query)
    {
        return $query->whereNull('packages.verified_weight');
    }

    public function scopeOfLastCheckpointOfCode($query, $event_code_id)
    {
        if (!$event_code_id) {
            return $query;
        }

        if (is_array($event_code_id)) {
            return $query->whereIn('last_checkpoints.checkpoint_code_id', $event_code_id);
        } else {
            return $query->where('last_checkpoints.checkpoint_code_id', $event_code_id);
        }
    }

    public function scopeOfLastEventCode($query, $checkpoint_code_id)
    {
        if (!$checkpoint_code_id) {
            return $query;
        }

        if (is_array($checkpoint_code_id)) {
            return $query->whereIn('last_events.event_code_id', $checkpoint_code_id);
        } else {
            return $query->where('last_events.event_code_id', $checkpoint_code_id);
        }
    }

    public function scopeOfFirstCheckpointNewerThan($query, $date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');

        return !$date ? $query : $query
            ->where('packages.first_checkpoint_at', '>=', $date . ' 00:00:00');
    }

    public function scopeOfFirstCheckpointOlderThan($query, $date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');

        return !$date ? $query : $query
            ->where('packages.first_checkpoint_at', '<=', $date . ' 23:59:59');
    }

    public function scopeOfLastCheckpointNewerThan($query, $date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');

        return !$date ? $query : $query
            ->where('packages.last_checkpoint_at', '>=', $date . ' 00:00:00');
    }

    public function scopeOfLastCheckpointOlderThan($query, $date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');

        return !$date ? $query : $query
            ->where('packages.last_checkpoint_at', '<=', $date . ' 23:59:59');
    }

    public function scopeOfFirstControlledCheckpointNewerThan($query, $date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');

        return !$date ? $query : $query
            ->where('packages.first_controlled_checkpoint_at', '>=', $date . ' 00:00:00');
    }

    public function scopeOfFirstControlledCheckpointOlderThan($query, $date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');

        return !$date ? $query : $query
            ->where('packages.first_controlled_checkpoint_at', '<=', $date . ' 23:59:59');
    }

    public function scopeOfDelivered($query)
    {
        return $query->where('packages.delivered', true);
    }

    public function scopeOfNotDelivered($query)
    {
        return $query->where('packages.delivered', false);
    }

    public function scopeOfReturned($query)
    {
        return $query->where('packages.returned', true);
    }

    public function scopeOfNotReturned($query)
    {
        return $query->where('packages.returned', false);
    }

    public function scopeOfCanceled($query)
    {
        return $query->where('packages.canceled', true);
    }

    public function scopeOfNotCanceled($query)
    {
        return $query->where('packages.canceled', false);
    }

    public function scopeOfReturning($query)
    {
        return $query->where('packages.returning', true);
    }

    public function scopeOfNotReturning($query)
    {
        return $query->where('packages.returning', false);
    }

    public function scopeOfStalled($query)
    {
        return $query->where('packages.stalled', true);
    }

    public function scopeOfNotStalled($query)
    {
        return $query->where('packages.stalled', false);
    }

    public function scopeOfFirstClockstop($query)
    {
        return $query->whereNotNull('packages.first_clockstop_id');
    }

    public function scopeOfNotFirstClockstop($query)
    {
        return $query->whereNull('packages.first_clockstop_id');
    }

    public function scopeOfFinished($query)
    {
        return $query->where(function ($query2) {
            return $query2->where('packages.delivered', true)
                ->orWhere('packages.returned', true)
                ->orWhere('packages.canceled', true);
        });
    }

    public function scopeOfUnfinished($query)
    {
        return $query->where(function ($query2) {
            return $query2->where('packages.delivered', false)
                ->where('packages.returned', false)
                ->where('packages.canceled', false);
        });
    }

    public function scopeOfAccomplished($query)
    {
        return $query->where(function ($query2) {
            return $query2->where('packages.delivered', true)
                ->orWhere('packages.returned', true)
                ->orWhere('packages.canceled', true)
                ->orWhere('packages.stalled', true)
                ->orWhere('packages.returning', true);
        });
    }

    public function scopeOfUnaccomplished($query)
    {
        return $query->where(function ($query2) {
            return $query2->where('packages.delivered', false)
                ->where('packages.returned', false)
                ->where('packages.canceled', false)
                ->where('packages.stalled', false)
                ->where('packages.returning', false);
        });
    }

    public function scopeOfFinishedOrClockstopped($query)
    {
        return $query->where(function ($query2) {
            return $query2->where(function ($query3) {
                return $query3
                    ->where('packages.delivered', true)
                    ->orWhere('packages.returned', true)
                    ->orWhere('packages.canceled', true);
            })->orWhere(function ($query3) {
                return $query3->where('packages.clockstop', '>', 0);
            });
        });
    }

    public function scopeOfDeliveredOrClockstopped($query)
    {
        return $query->where(function ($query2) {
            return $query2
                ->where('packages.delivered', true)
                ->orWhere('packages.clockstop', '>', 0);
        });
    }

    public function scopeOfPostalOfficeId($query, $postal_office_id)
    {
        return !$postal_office_id ? $query : $query->wherePostalOfficeId($postal_office_id);
    }

    public function scopeOfStateId($query, $id)
    {
        return !$id ? $query : $query->where('states.id', $id);
    }

    public function scopeOfAlertSubtype($query, Alert $alert)
    {
        if ($alert->isInteriorSubtype()) {
            return $this->scopeOfInterior($query);
        } elseif ($alert->isFederalDistrictSubtype()) {
            return $this->scopeOfFederalDistrict($query);
        } elseif ($alert->isUnclassifiedSubtype()) {
            return $this->scopeOfWithoutPostalOffice($query);
        }

        return $query;
    }

    public function scopeOfInterior($query)
    {
        return $query
            ->join('postal_offices', 'packages.postal_office_id', '=', 'postal_offices.id')
            ->join('admin_level_3', 'admin_level_3.id', '=', 'postal_offices.admin_level_3_id')
            ->join('admin_level_2', 'admin_level_2.id', '=', 'admin_level_2.admin_level_2_id')
            ->join('admin_level_1', 'admin_level_1.id', '=', 'admin_level_2.admin_level_1_id')
            ->join('regions', 'admin_level_1.region_id', '=', 'regions.id')
            ->where('regions.name', 'Metropolitana');
    }

    public function scopeOfFederalDistrict($query)
    {
        return $query
            ->join('postal_offices', 'packages.postal_office_id', '=', 'postal_offices.id')
            ->join('admin_level_3', 'admin_level_3.id', '=', 'postal_offices.admin_level_3_id')
            ->join('admin_level_2', 'admin_level_2.id', '=', 'admin_level_2.admin_level_2_id')
            ->join('admin_level_1', 'admin_level_1.id', '=', 'admin_level_2.admin_level_1_id')
            ->join('regions', 'admin_level_1.region_id', '=', 'regions.id')
            ->where('regions.name', '<>', 'Metropolitana');
    }

    public function scopeOfWithoutPostalOffice($query)
    {
        return $query->whereNull('postal_office_id');
    }

    public function scopeOfDistributionProviderId($query, $provider_id)
    {
        if (is_array($provider_id) && !empty($provider_id)) {
            return $query->whereIn('providers.id', $provider_id);
        } else {
            return $query->where('providers.id', $provider_id);
        }
    }

    public function scopeCustomPaginate($query, $perPage = 15, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);

        $results = ($total = $query->toBase()->getCountForPagination($columns))
            ? $query->forPage($page, $perPage)->get($columns)
            : $query->getModel()->newCollection();

        return new LengthAwarePaginator($results, $total, $perPage, $page, [
            'path'     => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }

    public function scopeOfAgreementId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('packages.agreement_id', $id);
        } else {
            return !$id ? $query : $query->where('packages.agreement_id', $id);
        }
    }

    public function scopeOfDeliveryRouteId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('packages.delivery_route_id', $id);
        } else {
            return !$id ? $query : $query->where('packages.delivery_route_id', $id);
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

    public function scopeOfBillableWeightFrom($query, $billable_weight_from)
    {
        return !$billable_weight_from ? $query : $query->where('packages.billable_weight', '>=', $billable_weight_from);
    }

    public function scopeOfBillableWeightTo($query, $billable_weight_to)
    {
        return !$billable_weight_to ? $query : $query->where('packages.billable_weight', '<=', $billable_weight_to);
    }

    /**
     * @param Provider|null $provider
     *
     * @return Alias|null
     */
    public function getLastAlias(Provider $provider = null)
    {
        /** @var Alias $alias */
        $alias = null;
        if ($this->aliases) {
            if ($provider) {
                $alias = $this->aliases->filter(function ($alias) use ($provider) {
                    return $alias->provider_id == $provider->id;
                })->last();
            } else {
                $alias = $this->aliases->last();
            }
        }

        return $alias;
    }

    public function getLastAliasCode(Provider $provider = null)
    {
        if ($alias = $this->getLastAlias($provider)) {
            return $alias->code;
        }

        return null;
    }

    public function getLastAliasProviderName(Provider $provider = null)
    {
        if ($alias = $this->getLastAlias($provider)) {
            return $alias->getProviderName();
        }

        return null;
    }

    public function getLegProviderServiceProvider()
    {
        return $this->leg ? $this->leg->getProviderServiceProvider() : null;
    }

    public function getProviderServiceProviderName()
    {
        return $this->leg ? $this->leg->getProviderServiceProviderName() : null;
    }

    public function getDeliveryRouteDistributionProviderName()
    {
        return $this->deliveryRoute ? $this->deliveryRoute->getDistributionProviderName() : null;
    }

    public function hasCheckpointByCheckpointCodeTimezoneAndDate(CheckpointCode $checkpointCode, Timezone $timezone, Carbon $date)
    {
        $filtered = $this->checkpoints->first(function ($checkpoint, $k) use ($checkpointCode, $timezone, $date) {
            $checkpoint_at = Carbon::parse($checkpoint->checkpoint_at);

            return ($checkpoint->checkpoint_code_id == $checkpointCode->id &&
                $checkpoint->timezone_id == $timezone->id &&
                $checkpoint_at == $date);
        });

        return $filtered;
    }

    public function getCheckpointByDateTypeAndCode($date_iso8601, $type, $code)
    {
        $filtered = $this->checkpoints->first(function ($checkpoint, $k) use ($date_iso8601, $type, $code) {
            /** @var  Checkpoint $checkpoint */
            $cp_date = $checkpoint->checkpoint_at_iso_8601;
            $cp_type = $checkpoint->getCheckpointCodeType();
            $cp_code = $checkpoint->getCheckpointCodeCode();

            return $cp_date == $date_iso8601 && $cp_type == $type && $cp_code == $code;
        });

        return $filtered;
    }

    public function getCheckpointByDateAndDescription($date_iso8601, $description)
    {
        return $this->checkpoints->first(function ($checkpoint, $k) use ($date_iso8601, $description) {
            /** @var  Checkpoint $checkpoint */
            $date = $checkpoint->checkpoint_at_iso_8601;

            return $date == $date_iso8601 && $checkpoint->getCheckpointCodeDescription() == $description;
        });
    }

    public function getItemCountAttribute()
    {
        return $this->packageItems ? $this->packageItems->count() : 0;
    }

    public function getEventCountAttribute()
    {
        return $this->events ? $this->events->count() : 0;
    }

    public function getCheckpointCountAttribute()
    {
        return $this->checkpoints ? $this->checkpoints->count() : 0;
    }

    public function getBillableWeight()
    {
        $gross = $this->getBillableGrossWeightAttribute();

        if ($this->isAgreementServiceBillingModeVolumetric() && $this->first_checkpoint_at >= '2018-01-01') {
            $volumetric = $this->getBillableVolWeightAttribute();

            return $volumetric;
        }

        return $gross;
    }

    public function getBillableGrossWeightAttribute()
    {
        // Keep previous rule for packages invoiced until May 2017
        if ($this->invoiced_at && $this->first_checkpoint_at <= '2017-04-31') {
            return max($this->weight, $this->verified_weight);
        }

        if (!$this->verified_weight) {
            return $this->weight;
        }

        $difference = abs($this->weight - $this->verified_weight);
        if ($this->weight <= 0.030) {
            // 0 - 30 grs => 3 grs
            $threshold = 0.003;
        } elseif ($this->weight > 0.030 && $this->weight <= 0.100) {
            // 31 - 100 grs => 4 grs
            $threshold = 0.004;
        } elseif ($this->weight > 0.100 && $this->weight <= 0.200) {
            // 101 - 200 grs => 8 grs
            $threshold = 0.008;
        } else {
            // > 200 grs => 3%
            $threshold = 0.03 * $this->weight;
        }

        if ($difference <= $threshold) {
            // If difference inside bounds, then seller is right
            return $this->weight;
        } else {
            // If difference outside bounds, then WH is right
            return $this->verified_weight;
        }
    }

    public function isAgreementServiceBillingModeVolumetric()
    {
        return $this->agreement ? $this->agreement->isServiceBillingModeVolumetric() : false;
    }

    public function getBillableVolWeightAttribute()
    {
        $billable_vol_weight = 0;

        // ---- Warehouse weights -----

        $verified_vol_weight = round(floatval($this->calculated_vol_weight), 3);
        if ($verified_vol_weight <= 0) {
            // If no value, then look the vol_weight field received from WH
            $verified_vol_weight = round(floatval($this->verified_vol_weight), 3);
        }
        $verified_weight = round(floatval($this->verified_weight), 3);

        // ---- Seller weights -----

        $vol_weight = round(floatval($this->vol_weight), 3);
        $weight = round(floatval($this->weight), 3);

        // -------------------------

        $max_verified_weight = max($verified_vol_weight, $verified_weight);
        $max_seller_weight = max($vol_weight, $weight);

        if ($verified_vol_weight and $verified_weight) {
            $difference = abs($max_seller_weight - $max_verified_weight);

            if ($max_seller_weight <= 0.030) {
                // 0 - 30 grs => 3 grs
                $threshold = 0.003;
            } elseif ($max_seller_weight > 0.030 && $max_seller_weight <= 0.100) {
                // 31 - 100 grs => 4 grs
                $threshold = 0.004;
            } elseif ($max_seller_weight > 0.100 && $max_seller_weight <= 0.200) {
                // 101 - 200 grs => 8 grs
                $threshold = 0.008;
            } else {
                // > 200 grs => 3%
                $threshold = 0.03 * $max_seller_weight;
            }

            if ($difference <= $threshold) {
                // If difference inside bounds, then seller is right
                $billable_vol_weight = $max_seller_weight;
            } else {
                // If difference outside bounds, then WH is right
                $billable_vol_weight = $max_verified_weight;
            }
        } else {
            $billable_vol_weight = max($max_seller_weight, $max_verified_weight);
        }

        return $billable_vol_weight;
    }

    public function getVerifiedOriginWarehouseCodeAndDescription()
    {
        if (!$this->verified_origin_warehouse_code) {
            return '-';
        }

        $description = '';

        /**@var ProviderServiceRepository $providerServiceRepository */
        $providerServiceRepository = app(ProviderServiceRepository::class);

        if ($st = $providerServiceRepository->search(['code' => $this->verified_origin_warehouse_code])->first()) {
            $description = " ({$st->name})";
        }

        return $this->verified_origin_warehouse_code . $description;
    }

    public function getOriginWarehouseCodeAndDescription()
    {
        if (!$this->origin_warehouse_code) {
            return '-';
        }

        $description = '';

        /**@var ProviderServiceRepository $providerServiceRepository */
        $providerServiceRepository = app(ProviderServiceRepository::class);

        if ($st = $providerServiceRepository->search(['code' => $this->origin_warehouse_code])->first()) {
            $description = " ({$st->name})";
        }

        return $this->origin_warehouse_code . $description;
    }

    public function getBillableWeightCalculationMethod()
    {
        $gross = $this->getBillableGrossWeightAttribute();

        if ($this->isAgreementServiceBillingModeVolumetric() && $this->first_checkpoint_at >= '2018-01-01') {
            if ($this->getBillableVolWeightAttribute() >= $gross) {
                return 'volumetric';
            }
        }

        return 'gross';
    }

    public function isAgreementTypePriority()
    {
        if ($this->agreement) {
            return $this->agreement->isTypePriority();
        }

        return false;
    }

    public function getBagTrackingNumber()
    {
        return $this->bag ? $this->bag->tracking_number : null;
    }

    public function getBagDispatchCode()
    {
        return $this->bag ? $this->bag->cn38 : null;
    }

    public function getBagDispatchId()
    {
        return $this->bag ? $this->bag->dispatch_id : null;
    }

    public function hasPreadmission()
    {
        return $this->preadmissions ? !($this->preadmissions->isEmpty()) : false;
    }

    public function hasAgreementServiceAlternativeDeliveryRoutes()
    {
        return $this->agreement ? $this->agreement->hasServiceAlternativeDeliveryRoutes() : false;
    }

    public function hasProviderInvoices()
    {
        return ($this->providerInvoices->count() > 0);
    }

    public function getBagDispatchAirWaybill()
    {
        return $this->bag ? $this->bag->getDispatchAirWaybill() : null;
    }

    public function getBagDispatchAirWaybillCode()
    {
        return $this->bag ? $this->bag->getDispatchAirWaybillCode() : null;
    }

    public function getZipCodeAdminLevel3Alt()
    {
        return $this->zipCode ? $this->zipCode->getAdminLevel3NameAlt() : null;
    }

    public function getZipCodeAdminLevel3Name()
    {
        return $this->zipCode ? $this->zipCode->getAdminLevel3Name() : null;
    }

    public function getZipCodeAdminLevel3AdminLevel2Name()
    {
        return $this->zipCode ? $this->zipCode->getAdminLevel3AdminLevel2Name() : null;
    }

    public function getZipCodeAdminLevel3AdminLevel2AdminLevel1Name()
    {
        return $this->zipCode ? $this->zipCode->getAdminLevel3AdminLevel2AdminLevel1Name() : null;
    }

    public function getZipCodeAdminLevel3AdminLevel2AdminLevel1RegionName()
    {
        return $this->zipCode ? $this->zipCode->getAdminLevel3AdminLevel2AdminLevel1RegionName() : null;
    }

    public function getZipCodeAdminLevel3AdminLevel2RegionName()
    {
        return $this->zipCode ? $this->zipCode->getAdminLevel3AdminLevel2RegionName() : null;
    }

    public function getZipCodeCode()
    {
        return $this->zipCode ? $this->zipCode->code : null;
    }

    public function getPostalOfficeName()
    {
        return $this->postalOffice ? $this->postalOffice->name : null;
    }

    public function getPostalOfficeCode()
    {
        return $this->postalOffice ? $this->postalOffice->code : null;
    }

    public function getPostalOfficePhone()
    {
        return $this->postalOffice ? $this->postalOffice->phone_no : null;
    }

    public function getFirstCheckpoint()
    {
        return $this->checkpoints->sortBy('checkpoint_at')->first();
    }

    public function getLastCheckpoint()
    {
        return $this->checkpoints->sortByDesc('checkpoint_at')->first();
    }

    public function getLastCheckpointWithOffice()
    {
        return $this->checkpoints->sortBy('checkpoint_at')->last(function ($checkpoint, $k) {
            return ($checkpoint->office && $checkpoint->office_zip);
        });
    }

    public function getInTransitToAirportCheckpoint()
    {
        return $this->checkpoints->first(function ($checkpoint, $k) {
            /** @var  Checkpoint $checkpoint */
            return $checkpoint->isInTransitToAirport();
        });
    }

    public function getDeliveredToTheCountryCheckpoint()
    {
        return $this->checkpoints->first(function ($checkpoint, $k) {
            /** @var  Checkpoint $checkpoint */
            return $checkpoint->isDeliveredToTheCountry();
        });
    }

    public function getArrivedAtAirportCheckpoint()
    {
        return $this->checkpoints->first(function ($checkpoint, $k) {
            /** @var  Checkpoint $checkpoint */
            return $checkpoint->isArrivedAtAirport();
        });
    }

    public function getFirstOnDistributionToDeliveryCenterCheckpoint()
    {
        return $this->checkpoints->first(function ($checkpoint, $k) {
            /** @var  Checkpoint $checkpoint */
            return $checkpoint->isOnDistributionToDeliveryCenter();
        });
    }

    public function getDeliveredCheckpoint()
    {
        return $this->checkpoints->first(function ($checkpoint, $k) {
            /** @var  Checkpoint $checkpoint */
            return $checkpoint->isDelivered();
        });
    }

    public function getReturnedCheckpoint()
    {
        return $this->checkpoints->first(function ($checkpoint, $k) {
            /** @var  Checkpoint $checkpoint */
            return $checkpoint->isReturned();
        });
    }

    public function getCanceledCheckpoint()
    {
        return $this->checkpoints->first(function ($checkpoint, $k) {
            /** @var  Checkpoint $checkpoint */
            return $checkpoint->isCanceled();
        });
    }

    public function getReturningCheckpoint()
    {
        return $this->checkpoints->first(function ($checkpoint, $k) {
            /** @var  Checkpoint $checkpoint */
            return $checkpoint->isReturning();
        });
    }

    public function getStalledCheckpoint()
    {
        return $this->checkpoints->first(function ($checkpoint, $k) {
            /** @var  Checkpoint $checkpoint */
            return $checkpoint->isStalled();
        });
    }

    public function getFirstCheckpointWithClockstop()
    {
        return $this->checkpoints->first(function ($checkpoint, $k) {
            /** @var  Checkpoint $checkpoint */
            return $checkpoint->isClockstop();
        });
    }

    public function hasCheckpointsWithClockstop()
    {
        return !$this->checkpoints->filter(function ($checkpoint) {
            /** @var  Checkpoint $checkpoint */
            return $checkpoint->isClockstop();
        })->isEmpty();
    }

    public function getLastCheckpointOfClockstop()
    {
        return $this->checkpoints->last(function ($checkpoint, $k) {
            /** @var  Checkpoint $checkpoint */
            return $checkpoint->isClockstop();
        });
    }

    public function getCheckpointsOfCheckpointCode(CheckpointCode $checkpointCode)
    {
        return $this->checkpoints->filter(function ($checkpoint, $k) use ($checkpointCode) {
            return ($checkpoint->checkpoint_code_id == $checkpointCode->id);
        });
    }

    public function getLastCheckpointOfCheckpointCode(CheckpointCode $checkpointCode)
    {
        return $this->checkpoints->last(function ($checkpoint, $k) use ($checkpointCode) {
            return ($checkpoint->checkpoint_code_id == $checkpointCode->id);
        });
    }

    public function getFirstCheckpointOfCheckpointCode(CheckpointCode $checkpointCode)
    {
        return $this->checkpoints->first(function ($checkpoint, $k) use ($checkpointCode) {
            return ($checkpoint->checkpoint_code_id == $checkpointCode->id);
        });
    }

    public function getPackagesClientMarketplaceName()
    {
        $name_marketplace = $this->marketplace ? $this->marketplace->name : null;

        if (empty($name_marketplace)) {
            $name_marketplace = $this->agreement->getClientMarketplaceName() ? $this->agreement->getClientMarketplaceName() : null;
        }

        return $name_marketplace;
    }

    public function getClientName()
    {
        if ($this->agreement) {
            return $this->agreement->getClientName();
        }

        return null;
    }

    public function getClientCountryId()
    {
        if ($this->agreement) {
            return $this->agreement->getClientCountryId();
        }

        return null;
    }

    public function getClientAcronym()
    {
        if ($this->agreement) {
            return $this->agreement->getClientAcronym();
        }

        return null;
    }

    public function getAgreementId()
    {
        if ($this->agreement) {
            return $this->agreement->id;
        }

        return null;
    }

    public function getAgreementFirstLegFirstCheckpointCode()
    {
        return $this->agreement ? $this->agreement->getFirstLegFirstCheckpointCode() : null;
    }

    public function getAgreementServiceCode()
    {
        return $this->agreement ? $this->agreement->getServiceCode() : null;
    }

    public function getAgreementClientTimezone()
    {
        return $this->agreement ? $this->agreement->getClientTimezone() : null;
    }

    public function getAgreementServiceOriginLocationId()
    {
        return $this->agreement ? $this->agreement->getServiceOriginLocationId() : null;
    }

    public function getAgreementServiceDestinationLocationId()
    {
        return $this->agreement ? $this->agreement->getServiceDestinationLocationId() : null;
    }

    public function getAgreementServiceLocationCountryId()
    {
        return $this->agreement ? $this->agreement->getServiceDestinationLocationCountryId() : null;
    }

    public function getAgreementServiceOriginLocationCountryName()
    {
        return $this->agreement ? $this->agreement->getServiceOriginLocationCountryName() : null;
    }

    public function getAgreementServiceDestinationLocationCountryName()
    {
        return $this->agreement ? $this->agreement->getServiceDestinationLocationCountryName() : null;
    }

    public function getAgreementServiceDestinationLocationCountryCode()
    {
        return $this->agreement ? $this->agreement->getServiceDestinationLocationCountryCode() : null;
    }

    public function getAgreementServiceDeliveryRoutes()
    {
        return $this->agreement ? $this->agreement->getServiceDeliveryRoutes() : null;
    }

    public function getAgreementServiceDefaultDeliveryRoute()
    {
        return $this->agreement ? $this->agreement->getServiceDefaultDeliveryRoute() : null;
    }

    public function getAgreementServiceTransitDays()
    {
        return $this->agreement ? $this->agreement->getServiceTransitDays() : null;
    }

    public function getAgreementServiceServiceTypeKey()
    {
        return $this->agreement ? $this->agreement->getServiceServiceTypeKey() : null;
    }

    public function getDeliveryRouteFirstLegCheckpoint()
    {
        /** @var Leg $leg */
        if (!$leg = $this->getDeliveryRouteFirstLeg()) {
            return null;
        }

        // Get First Checkpoint Code that matches criteria
        if ($fcc = $leg->getProviderServiceFirstCheckpointCode()) {
            return $this->getFirstCheckpointOfCheckpointCode($fcc);
        }

        return null;
    }

    public function getDeliveryRouteFirstLeg()
    {
        if ($legs = $this->getDeliveryRouteLegs()) {
            return $legs->first();
        }

        return null;
    }

    public function getDeliveryRouteLegs()
    {
        if ($this->deliveryRoute) {
            return $this->deliveryRoute->legs;
        }

        return null;
    }

    public function getDeliveryRouteFirstLegCheckpointCode()
    {
        /** @var Leg $leg */
        if (!$leg = $this->getDeliveryRouteFirstLeg()) {
            return null;
        }

        // Get First Checkpoint Code that matches criteria
        if ($fcc = $leg->getProviderServiceFirstCheckpointCode()) {
            return $fcc;
        }

        return null;
    }

    public function getDeliveryRouteControlledTransitDays()
    {
        return $this->deliveryRoute ? $this->deliveryRoute->calculateControlledTransitDays() : 0;
    }

    public function isAgreementServicesDestinationLocationCountryMexico()
    {
        return $this->agreement ? $this->agreement->isServiceDestinationLocationCountryMexico() : false;
    }

    public function isAgreementServicesDestinationLocationCountryChile()
    {
        return $this->agreement ? $this->agreement->isServiceDestinationLocationCountryChile() : false;
    }

    public function isDistribuitorMexpost()
    {
        return $this->deliveryRoute ? $this->deliveryRoute->isDistribuitorMexpost() : false;
    }

    public function getDeliveryRouteFirstControlledCheckpointCode()
    {
        return $this->deliveryRoute ? $this->deliveryRoute->getFirstControlledCheckpointCode() : null;
    }

    public function getCurrentLegProviderServiceProviderCheckpointCodes()
    {
        return $this->leg ? $this->leg->getProviderServiceProviderCheckpointCodes() : null;
    }

    public function getAvailableLegProviderServiceProviderCheckpointCodes()
    {
        $leg = $this->leg;

        $checkpointCodes = collect();
        if ($legs = $this->getDeliveryRouteLegs()) {
            $this->getDeliveryRouteLegs()->filter(function ($elem) use ($leg) {
                return $elem->position >= $leg->position;
            })->each(function ($elem) use (&$checkpointCodes) {
                /** @var Leg $elem */
                foreach ($elem->getProviderServiceProviderCheckpointCodes() as $cc) {
                    $checkpointCodes->push($cc);
                }
            });
        }

        return $checkpointCodes;
    }

    public function getCurrentLegProviderServiceProviderTimezone()
    {
        return $this->leg ? $this->leg->getProviderServiceProviderTimezone() : null;
    }

    public function getFirstControlledLeg()
    {
        if (!$legs = $this->getDeliveryRouteLegs()) {
            return null;
        }

        // Get first controlled leg
        return $legs->first(function ($l, $k) {
            return ($l->controlled);
        });
    }

    public function getLastUncontrolledCheckpoint()
    {
        if (!$lcc = $this->getLastUncontrolledCheckpointCode()) {
            return null;
        }

        return $this->getFirstCheckpointOfCheckpointCode($lcc);
    }

    public function getFirstControlledCheckpoint()
    {
        if (!$legs = $this->getDeliveryRouteLegs()) {
            return null;
        }

        if (!$controlledLegs = $legs->filter(function (Leg $leg) {
            return ($leg->controlled);
        })
        ) {
            return null;
        }

        /** @var Leg $leg */
        foreach ($controlledLegs as $leg) {
            $provider = $leg->getProviderServiceProvider();

            /** @var Checkpoint $checkpoint */
            foreach ($this->checkpoints as $checkpoint) {
                $p = $checkpoint->getCheckpointCodeProvider();
                if ($p && ($p->id == $provider->id)) {
                    return $checkpoint;
                }
            }
        }

        return null;
    }

    public function getFirstControlledCheckpointCode()
    {
        /** @var Checkpoint $fcc */
        if ($fcc = $this->firstControlledCheckpoint) {
            return $fcc->checkpointCode;
        }

        return null;
    }

    public function getTraceableLeg()
    {
        // Search Provider's leg in current agreement
        if ($legs = $this->getDeliveryRouteLegs()) {
            $leg = $legs->first();

            /** @var Checkpoint $checkpoint */
            foreach ($this->checkpoints->sortByDesc('checkpoint_at') as $checkpoint) {
                /** @var Provider $provider */
                $p = $checkpoint->getCheckpointCodeProvider();

                /** @var Leg $leg */
                $checkpoint_leg = $legs->first(function ($l, $k) use ($p) {
                    /** @var Leg $l */
                    $provider = $l->getProviderServiceProvider();

                    return $provider->id == $p->id;
                });

                if (!$checkpoint_leg) {
                    continue;
                }

                // If package has Leg's closing checkpoint, then move to next leg
                $lcc = $checkpoint_leg->getProviderServiceLastCheckpointCode();
                if ($lcc && $this->hasCheckpointOfCheckpointCode($lcc)) {
                    $next = $checkpoint_leg->position + 1;
                    $next_leg = $legs->filter(function ($item) use ($next) {
                        return $item->position == $next;
                    })->first();

                    if ($next_leg) {
                        $leg = $next_leg;
                    } else {
                        $leg = $checkpoint_leg;
                    }
                } else {
                    $leg = $checkpoint_leg;
                }
                break;
            }

            return !$leg ? $legs->first() : $leg;
        }

        return null;
    }

    public function hasCheckpointOfCheckpointCode(CheckpointCode $checkpointCode)
    {
        $ccs = $this->checkpoints->first(function ($checkpoint, $k) use ($checkpointCode) {
            return ($checkpoint->checkpoint_code_id == $checkpointCode->id);
        });

        return ($ccs);
    }

    public function getDistributionLegs()
    {
        if ($legs = $this->getDeliveryRouteLegs()) {
            return $legs->filter(function ($l) {
                /** @var Leg $l */
                return $l->isDistribution();
            });
        }

        return null;
    }

    public function getDistributionLeg()
    {
        if ($legs = $this->getDeliveryRouteLegs()) {
            return $legs->first(function ($l, $k) {
                /** @var Leg $l */
                return $l->isDistribution();
            });
        }

        return null;
    }

    public function isReturning()
    {
        return $this->returning;
    }

    public function isStalled()
    {
        return $this->stalled;
    }

    public function getTransitDaysFromFirstCheckpoint()
    {
        if (!$last_checkpoint = $this->lastCheckpoint) {
            return 0;
        }

        if (!$first_checkpoint = $this->firstCheckpoint) {
            return null;
        }

        if ($finished_checkpoint = $this->getFinishedCheckpoint()) {
            return count_workdays($first_checkpoint->checkpoint_at, $finished_checkpoint->checkpoint_at, $this);
        }

        return count_workdays($first_checkpoint->checkpoint_at, null, $this);
    }

    public function getFinishedCheckpoint()
    {
        return $this->checkpoints->first(function ($checkpoint, $k) {
            return $checkpoint->isFinished();
        });
    }

    public function getTransitDaysFromFirstToClockstopCheckpoint()
    {
        if (!$first_checkpoint = $this->firstCheckpoint) {
            return null;
        }

        if (!$clockstop_checkpoint = $this->firstClockstop) {
            return null;
        }

        return count_workdays($first_checkpoint->checkpoint_at, $clockstop_checkpoint->checkpoint_at, $this);
    }

    public function getTransitDaysFromFirstControlledCheckpoint()
    {
        if (!$last_checkpoint = $this->lastCheckpoint) {
            return 0;
        }

        if (!$first_checkpoint = $this->firstControlledCheckpoint) {
            return null;
        }

        if ($finished_checkpoint = $this->getFinishedCheckpoint()) {
            return count_workdays($first_checkpoint->checkpoint_at, $finished_checkpoint->checkpoint_at, $this);
        }

        return count_workdays($first_checkpoint->checkpoint_at, null, $this);
    }

    public function getTransitDaysFromFirstControlledCheckpointToClockstopCheckpoint()
    {
        if (!$first_checkpoint = $this->firstControlledCheckpoint) {
            return null;
        }

        if (!$clockstop_checkpoint = $this->firstClockstop) {
            return null;
        }

        return count_workdays($first_checkpoint->checkpoint_at, $clockstop_checkpoint->checkpoint_at, $this);
    }

    public function getTransitDaysSinceNoMovement()
    {
        if (!$last_checkpoint = $this->lastCheckpoint) {
            return 0;
        }

        if ($this->isFinished()) {
            return null;
        }

        return count_workdays($last_checkpoint->checkpoint_at, null, $this);
    }

    public function isFinished()
    {
        return $this->delivered or $this->returned or $this->canceled;
    }

    public function getPackageItemsDescriptions()
    {
        $items = $this->packageItems;

        $descriptions = '';
        foreach ($items as $item) {
            $descriptions .= "[{$item->description}]";
        }

        return $descriptions;
    }

    public function getPackageItemsQuantities()
    {
        $items = $this->packageItems;

        $quantities = '';
        foreach ($items as $item) {
            $quantities .= "[{$item->quantity}]";
        }

        return $quantities;
    }

    public function isDeliveryRouteFirstLegDistribution()
    {
        return $this->deliveryRoute ? $this->deliveryRoute->isFirstLegDistribution() : false;
    }

    public function isLegDistribution()
    {
        return $this->leg ? $this->leg->isDistribution() : false;
    }

    public function wasPrealertedSuccessfullyToAllProviders()
    {
        if (!$this->prealerts) {
            return false;
        }

        $prealerts_provider = $this->prealerts->groupBy('provider.name');
        $successful = 0;
        foreach ($prealerts_provider as $provider_name => $prealerts) {
            if ($prealerts->where('success', 1)->isNotEmpty()) {
                ++$successful;
            }
        }

        return $successful >= $prealerts_provider->count();
    }

    public function countPrealertsToProvider($provider_name)
    {
        if (!$this->prealerts) {
            return 0;
        }

        $successful = $this->prealerts->where('provider.name', $provider_name)->unique('package_id')->count();

        return $successful;
    }

    public function wasPrealertedSuccessfullyToProvider($provider)
    {
        $successful = $this->countSuccessfulPrealertsToProvider($provider->name);

        return $successful > 0;
    }

    public function countSuccessfulPrealertsToProvider($provider_name)
    {
        if (!$this->prealerts) {
            return 0;
        }

        $successful = $this->prealerts->where('provider.name', $provider_name)->where('success', 1)->unique('package_id')->count();

        return $successful;
    }

    public function countUnsuccessfulPrealertsToProvider($provider_name)
    {
        if (!$this->prealerts) {
            return 0;
        }

        $successful = $this->prealerts->where('provider.name', $provider_name)->where('success', 0)->unique('package_id')->count();

        return $successful;
    }

    public function getLastSuccessfulPrealertToProvider($provider_name)
    {
        if (!$this->prealerts) {
            return null;
        }

        return $this->prealerts->where('provider.name', $provider_name)->where('success', 1)->unique('package_id')->sortByDesc('id')->last();
    }

    public function getLastVolumetricScaleMeasurementWidth()
    {
        /** @var VolumetricScaleMeasurement $vsm */
        if ($vsm = $this->volumetricScaleMeasurements->last()) {
            return $vsm->width;
        }

        return null;
    }

    public function getLastVolumetricScaleMeasurementHeight()
    {
        /** @var VolumetricScaleMeasurement $vsm */
        if ($vsm = $this->volumetricScaleMeasurements->last()) {
            return $vsm->height;
        }

        return null;
    }

    public function getLastVolumetricScaleMeasurementLength()
    {
        /** @var VolumetricScaleMeasurement $vsm */
        if ($vsm = $this->volumetricScaleMeasurements->last()) {
            return $vsm->length;
        }

        return null;
    }

    public function getLastVolumetricScaleMeasurementWeight()
    {
        /** @var VolumetricScaleMeasurement $vsm */
        if ($vsm = $this->volumetricScaleMeasurements->last()) {
            return $vsm->weight;
        }

        return null;
    }

    public function getLastVolumetricScaleMeasurementVolWeight()
    {
        /** @var VolumetricScaleMeasurement $vsm */
        if ($vsm = $this->volumetricScaleMeasurements->last()) {
            return $vsm->vol_weight;
        }

        return null;
    }

    public function getLastVolumetricScaleMeasurementImageUrl()
    {
        /** @var VolumetricScaleMeasurement $vsm */
        if ($vsm = $this->volumetricScaleMeasurements->last()) {
            return $vsm->image_url;
        }

        return null;
    }

    public function getPresenterClass()
    {
        return PackagePresenter::class;
    }
}