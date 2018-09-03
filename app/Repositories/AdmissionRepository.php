<?php

namespace App\Repositories;

use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AdmissionRepository extends AbstractRepository
{

    function __construct(Package $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $params
     *
     * @return mixed
     */
    public function search($params = [], $count = false, $distinct = true)
    {
        $now = Carbon::now()->toDateTimeString();
        $query = $this->model
            ->select('packages.*')
            ->whereNull('packages.bag_id')
            ->whereNotNull('packages.agreement_id');

        if ($distinct) {
            $query = $query->distinct();
        }

        $joins = collect();
        $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
        $this->addJoin($joins, 'checkpoints as first_checkpoints', 'packages.first_checkpoint_id', 'first_checkpoints.id', 'left outer');

        if (isset($params['tracking']) && $params['tracking']) {
            $query->ofTrackingOrCustomerTracking($params['tracking']);
        }

        if (isset($params['tracking_number']) && $params['tracking_number']) {
            $query->ofTrackingNumber($params['tracking_number']);
        }

        if (isset($params['alias']) && $params['alias']) {
            $alias = $params['alias'];
            $this->addJoin($joins, 'aliases', 'aliases.package_id', 'packages.id');
            if (is_array($alias) && !empty($alias)) {
                $query->where(function ($q2) use ($alias) {
                    collect($alias)->each(function ($item) use ($q2) {
                        $q2->orWhere('aliases.code', strtoupper($item));
                    });
                });
            } else {
                $query->where('aliases.code', strtoupper($alias));
            }
        }

        if (isset($params['client_id']) && $params['client_id']) {
            $query->ofClientId($params['client_id']);
        }

        if (isset($params['marketplace_id']) && $params['marketplace_id']) {
            $this->addJoin($joins, 'agreements', 'dispatches.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'clients', 'agreements.client_id', 'clients.id');
            $this->addJoin($joins, 'client_marketplace', 'client_marketplace.client_id', 'clients.id', 'left outer');
            $query->ofMarketplaceId($params['marketplace_id']);
        }

        if (isset($params['first_checkpoint_newer_than']) && $params['first_checkpoint_newer_than']) {
            $query->ofFirstCheckpointNewerThan($params['first_checkpoint_newer_than']);
        }

        if (isset($params['first_checkpoint_older_than']) && $params['first_checkpoint_older_than']) {
            $query->ofFirstCheckpointOlderThan($params['first_checkpoint_older_than']);
        }

        if (isset($params['last_checkpoint_code_id']) && $params['last_checkpoint_code_id']) {
            $this->addJoin($joins, 'checkpoints as last_checkpoints', 'packages.last_checkpoint_id', 'last_checkpoints.id', 'left outer');
            $query->ofLastCheckpointOfCode($params['last_checkpoint_code_id']);
        }

        if (isset($params['last_event_code_id']) && $params['last_event_code_id']) {
            $this->addJoin($joins, 'checkpoints as last_checkpoints', 'packages.last_checkpoint_id', 'last_checkpoints.id', 'left outer');
            $this->addJoin($joins, 'events as last_events', 'last_events.last_checkpoint_id', 'last_checkpoints.id');
            $query->ofLastEventCode($params['last_event_code_id']);
        }

        if (isset($params['country_id']) && $params['country_id']) {
            $this->addJoin($joins, 'services', 'services.id', 'agreements.service_id');
            $this->addJoin($joins, 'locations as destination_locations', 'destination_locations.id', 'services.destination_location_id', 'left outer');
            $query->ofDestinationCountryId($params['country_id']);
        }

        if (isset($params['job_order'])) {
            $query->ofJobOrder($params['job_order']);
        }

        if (isset($params['canceled']) && $params['canceled']) {
            $query->ofCanceled();
        }

        if (isset($params['uncanceled']) && $params['uncanceled']) {
            $query->ofNotCanceled();
        }

        if (isset($params['finished']) && $params['finished']) {
            $query->ofFinished();
        }

        if (isset($params['unfinished']) && $params['unfinished']) {
            $query->ofUnfinished();
        }

        if (isset($params['accomplished']) && $params['accomplished']) {
            $query->ofAccomplished();
        }

        if (isset($params['unaccomplished']) && $params['unaccomplished']) {
            $query->ofUnaccomplished();
        }

        if (isset($params['delivered']) && $params['delivered']) {
            $query->ofDelivered();
        }

        if (isset($params['undelivered']) && $params['undelivered']) {
            $query->ofNotDelivered();
        }

        if (isset($params['returned']) && $params['returned']) {
            $query->ofReturned();
        }

        if (isset($params['unreturned']) && $params['unreturned']) {
            $query->ofNotReturned();
        }

        if (isset($params['returning']) && $params['returning']) {
            $query->ofReturning();
        }

        if (isset($params['unreturning']) && $params['unreturning']) {
            $query->ofNotReturning();
        }

        if (isset($params['stalled']) && $params['stalled']) {
            $query->ofStalled();
        }

        if (isset($params['unstalled']) && $params['unstalled']) {
            $query->ofNotStalled();
        }

        if (isset($params['clockstopped']) && $params['clockstopped']) {
            $query->ofFirstClockstop();
        }

        if (isset($params['unclockstopped']) && $params['unclockstopped']) {
            $query->ofNotFirstClockstop();
        }

        if (isset($params['verified_weight']) && $params['verified_weight']) {
            $query->ofVerifiedWeight();
        }

        if (isset($params['unverified_weight']) && $params['unverified_weight']) {
            $query->ofUnverifiedWeight();
        }

        if (isset($params['controlled']) && $params['controlled']) {
            $query->whereNotNull('packages.first_controlled_checkpoint_id');
        }

        if (isset($params['uncontrolled']) && $params['uncontrolled']) {
            $query->whereNull('packages.first_controlled_checkpoint_id');
        }

        if (isset($params['invoiced']) && $params['invoiced']) {
            $query->ofInvoiced();
        }

        if (isset($params['uninvoiced']) && $params['uninvoiced']) {
            $query->ofUninvoiced();
        }

        if (isset($params['distribution']) && $params['distribution']) {
            $this->addJoin($joins, 'legs as distribution_leg', 'packages.leg_id', 'distribution_leg.id');
            $this->addJoin($joins, 'provider_services as distribution_provider_services', 'distribution_leg.provider_service_id', 'distribution_provider_services.id');
            $this->addJoin($joins, 'provider_service_types', 'provider_service_types.id', 'distribution_provider_services.provider_service_type_id');
            $query->ofDistribution();
        }

        if (isset($params['not_distribution']) && $params['not_distribution']) {
            $this->addJoin($joins, 'legs as distribution_leg', 'packages.leg_id', 'distribution_leg.id');
            $this->addJoin($joins, 'provider_services as distribution_provider_services', 'distribution_leg.provider_service_id', 'distribution_provider_services.id');
            $this->addJoin($joins, 'provider_service_types', 'provider_service_types.id', 'distribution_provider_services.provider_service_type_id');
            $query->ofNotDistribution();
        }

        if (isset($params['agreement_service_type_key']) && $params['agreement_service_type_key']) {
//            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'services', 'agreements.service_id', 'services.id');
            $this->addJoin($joins, 'service_types', 'service_types.id', 'services.service_type_id');

            $query->ofAgreementServiceServiceTypeKey($params['agreement_service_type_key']);
        }

        if (isset($params['agreement_service_id']) && $params['agreement_service_id']) {
//            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'services', 'agreements.service_id', 'services.id');

            $query->ofAgreementServiceId($params['agreement_service_id']);
        }

        if (isset($params['distribution_provider_id']) && $params['distribution_provider_id']) {
            $this->addJoin($joins, 'delivery_routes', 'delivery_routes.id', 'packages.delivery_route_id');
            $this->addJoin($joins, 'legs', 'delivery_routes.id', 'legs.delivery_route_id');
            $this->addJoin($joins, 'provider_services', 'legs.provider_service_id', 'provider_services.id');
            $this->addJoin($joins, 'providers', 'provider_services.provider_id', 'providers.id');
            $query->ofDistributionProviderId($params['distribution_provider_id']);
        }

        if (isset($params['on_time']) && $params['on_time']) {
            $this->addJoin($joins, 'delivery_routes', 'delivery_routes.id', 'packages.delivery_route_id');
            $query->whereRaw(DB::raw("date_part('day',IF(packages.delivered or packages.returned or packages.canceled, packages.last_checkpoint_at, '{$now}') - packages.first_controlled_checkpoint_at) <= delivery_routes.controlled_transit_days"));
        }

        if (isset($params['delayed']) && $params['delayed']) {
            $this->addJoin($joins, 'delivery_routes', 'delivery_routes.id', 'packages.delivery_route_id');
            $query->whereRaw(DB::raw("date_part('day',IF(packages.delivered or packages.returned or packages.canceled, packages.last_checkpoint_at, '{$now}') - packages.first_controlled_checkpoint_at) > delivery_routes.controlled_transit_days"));
        }

        if (isset($params['checkpoint_filtered_code_id']) && $params['checkpoint_filtered_code_id']) {
            $this->addJoin($joins, 'checkpoints as filtered_checkpoints', 'packages.id', 'filtered_checkpoints.package_id');
            if (isset($params['checkpoint_filtered_newer_than']) && $params['checkpoint_filtered_newer_than']) {
                $query->OfCheckpointFilteredNewerThan($params['checkpoint_filtered_newer_than']);
            }

            if (isset($params['checkpoint_filtered_older_than']) && $params['checkpoint_filtered_older_than']) {
                $query->OfCheckpointFilteredOlderThan($params['checkpoint_filtered_older_than']);
            }

            if (is_array($params['checkpoint_filtered_code_id'])) {
                $query->whereIn('filtered_checkpoints.checkpoint_code_id', $params['checkpoint_filtered_code_id']);
            } else {
                $query->where('filtered_checkpoints.checkpoint_code_id', $params['checkpoint_filtered_code_id']);
            }
        }

        if (isset($params['origin_warehouse_code']) && $params['origin_warehouse_code']) {
            $query->ofOriginWarehouseCode($params['origin_warehouse_code']);
        }

        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        if (isset($params['sort_by']) && $params['sort_by']) {
            $column = $params['sort_by'];
            $direction = 'asc';
            if (isset($params['sort_direction']) && $params['sort_direction']) {
                $direction = $params['sort_direction'];
            }

            return $query->orderBy($column, $direction);
        } else {
            return $count ? $query->count('packages.id') : $query->orderBy('packages.id', 'asc');
        }

    }

    private function addJoin(Collection &$joins, $table, $first, $second, $join_type = 'inner')
    {
        if (!$joins->has($table)) {
            $joins->put($table, json_encode(compact('first', 'second', 'join_type')));
        }
    }
} 