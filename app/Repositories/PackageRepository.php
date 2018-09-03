<?php

namespace App\Repositories;

use App\Models\Alert;
use App\Models\AlertDetail;
use App\Models\Bag;
use App\Models\Checkpoint;
use App\Models\Event;
use App\Models\EventCode;
use App\Models\Leg;
use App\Models\Package;
use App\Models\PostalOffice;
use App\Models\Segment;
use App\Models\ZipCode;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Collection;

class PackageRepository extends AbstractRepository
{
    /**
     * @var \App\Repositories\ZipCodeRepository
     */
    protected $zip_code;

    function __construct(Package $model, ZipCodeRepository $zip_code)
    {
        $this->model = $model;
        $this->zip_code = $zip_code;
    }

    private function addJoin(Collection &$joins, $table, $first, $second, $join_type = 'inner')
    {
        if (!$joins->has($table)) {
            $joins->put($table, json_encode(compact('first', 'second', 'join_type')));
        }
    }

    public function search($params = [], $count = false, $distinct = true)
    {
        $now = Carbon::now()->toDateTimeString();
        $joins = collect();

        $query = $this->model
            ->select('packages.*')
            ->whereNotNull('packages.bag_id');

        if ($distinct) {
            $query = $query->distinct();
        }

        if (isset($params['tracking'])) {
            $query->ofTrackingOrCustomerTracking($params['tracking']);
        }

        if (isset($params['customer_tracking_number'])) {
            $query->ofCustomerTrackingNumber($params['customer_tracking_number']);
        }

        if (isset($params['tracking_number'])) {
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

        if (isset($params['job_order'])) {
            $query->ofJobOrder($params['job_order']);
        }

        if (isset($params['period_to'])) {
            $query->ofCreatedBeforeThan($params['period_to']);
        }

        if (isset($params['period_from'])) {
            $query->ofCreatedAfterThan($params['period_from']);
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

        if (isset($params['canceled']) && $params['canceled']) {
            $query->ofCanceled();
        }

        if (isset($params['uncanceled']) && $params['uncanceled']) {
            $query->ofNotCanceled();
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

        if (isset($params['invoiced']) && $params['invoiced']) {
            $query->ofInvoiced();
        }

        if (isset($params['uninvoiced']) && $params['uninvoiced']) {
            $query->ofUninvoiced();
        }

        if (isset($params['verified_weight']) && $params['verified_weight']) {
            $query->ofVerifiedWeight();
        }

        if (isset($params['unverified_weight']) && $params['unverified_weight']) {
            $query->ofUnverifiedWeight();
        }

        if (isset($params['finished_or_clockstopped']) && $params['finished_or_clockstopped']) {
            $query->ofFinishedOrClockstopped();
        }

        if (isset($params['delivered_or_clockstopped']) && $params['delivered_or_clockstopped']) {
            $query->ofDeliveredOrClockstopped();
        }

        if (isset($params['bag_id'])) {
            $query->ofBagId($params['bag_id']);
        }

        if (isset($params['returns_allowed']) && $params['returns_allowed']) {
            $query->ofReturnsAllowed();
        }

        if (isset($params['returns_not_allowed']) && $params['returns_not_allowed']) {
            $query->ofReturnsNotAllowed();
        }

        if (isset($params['marketplace_id']) && $params['marketplace_id']) {
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'clients', 'agreements.client_id', 'clients.id');
            $this->addJoin($joins, 'client_marketplace', 'client_marketplace.client_id', 'clients.id', 'left outer');
            $query->ofMarketplaceId($params['marketplace_id']);
        }

        if (isset($params['postal_office_id'])) {
            $query->ofPostalOfficeId($params['postal_office_id']);
        }

        if (isset($params['controlled']) or isset($params['uncontrolled']) or isset($params['provider_service_id'])) {
            $this->addJoin($joins, 'legs as current_legs', 'packages.leg_id', 'current_legs.id', 'left outer');
            if (isset($params['controlled']) && $params['controlled']) {
                $query->ofControlled();
            }

            if (isset($params['uncontrolled']) && $params['uncontrolled']) {
                $query->ofUncontrolled();
            }

            if (isset($params['provider_service_id']) && $params['provider_service_id']) {
                $query->ofProviderServiceId($params['provider_service_id']);
            }
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

        //
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
        //

        if (isset($params['admin_level_1_id']) && $params['admin_level_1_id']) {
            $this->addJoin($joins, 'postal_offices', 'packages.postal_office_id', 'postal_offices.id', 'left outer');
            $this->addJoin($joins, 'admin_level_3', 'postal_offices.admin_level_3_id', 'admin_level_3.id');
            $this->addJoin($joins, 'admin_level_2', 'admin_level_3.admin_level_2_id', 'admin_level_2.id');
            $this->addJoin($joins, 'admin_level_1', 'admin_level_2.admin_level_1_id', 'admin_level_1.id');
            $query->ofAdminLevel1Id($params['admin_level_1_id']);
        }

        if (isset($params['cn38']) && $params['cn38']) {
            $this->addJoin($joins, 'bags', 'packages.bag_id', 'bags.id');
            $this->addJoin($joins, 'dispatches', 'bags.dispatch_id', 'dispatches.id');
            $query->ofDispatchCode($params['cn38']);
        }

        if (isset($params['cn35']) && $params['cn35']) {
            $this->addJoin($joins, 'bags', 'packages.bag_id', 'bags.id');
            $this->addJoin($joins, 'dispatches', 'bags.dispatch_id', 'dispatches.id');
            $query->ofBagTrackingNumber($params['cn35']);
        }

        if (isset($params['dispatch_id']) && $params['dispatch_id']) {
            $this->addJoin($joins, 'bags', 'packages.bag_id', 'bags.id');
            $query->ofDispatchId($params['dispatch_id']);
        }

        if (isset($params['air_waybill_id']) && $params['air_waybill_id']) {
            $this->addJoin($joins, 'bags', 'packages.bag_id', 'bags.id');
            $this->addJoin($joins, 'dispatches', 'bags.dispatch_id', 'dispatches.id');
            $query->ofAirWaybillId($params['air_waybill_id']);
        }

        if (isset($params['air_waybill_code']) && $params['air_waybill_code']) {
            $this->addJoin($joins, 'bags', 'packages.bag_id', 'bags.id');
            $this->addJoin($joins, 'dispatches', 'bags.dispatch_id', 'dispatches.id');
            $this->addJoin($joins, 'air_waybills', 'dispatches.air_waybill_id', 'air_waybills.id');
            $query->OfAirWaybillCode($params['air_waybill_code']);
        }

        if (isset($params['country_id']) && $params['country_id']) {
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'services', 'agreements.service_id', 'services.id');
            $this->addJoin($joins, 'locations as destination_locations', 'services.destination_location_id', 'destination_locations.id');
            $query->ofDestinationCountryId($params['country_id']);
        }

        if (isset($params['client_id']) && $params['client_id']) {
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $query->ofClientId($params['client_id']);
        }

        if (isset($params['on_time']) && $params['on_time']) {
            $this->addJoin($joins, 'delivery_routes', 'packages.delivery_route_id', 'delivery_routes.id');
            $query->whereRaw(DB::raw("(packages.first_controlled_checkpoint_at IS NULL or (date_part('day',(CASE WHEN (packages.delivered = 0 AND packages.returned = 0 AND packages.canceled = 0) THEN '" . $now . "' ELSE packages.last_checkpoint_at END) - packages.first_controlled_checkpoint_at) <= delivery_routes.controlled_transit_days))"));
        }

        if (isset($params['delayed']) && $params['delayed']) {
            $this->addJoin($joins, 'delivery_routes', 'packages.delivery_route_id', 'delivery_routes.id');
            $query->whereRaw(DB::raw("(packages.first_controlled_checkpoint_at IS NOT NULL and (date_part('day',(CASE WHEN (packages.delivered = 0 AND packages.returned = 0 AND packages.canceled = 0) THEN '" . $now . "' ELSE packages.last_checkpoint_at END) - packages.first_controlled_checkpoint_at) > delivery_routes.controlled_transit_days))"));
        }

        if (isset($params['first_checkpoint_newer_than']) && $params['first_checkpoint_newer_than']) {
            $query->ofFirstCheckpointNewerThan($params['first_checkpoint_newer_than']);
        }

        if (isset($params['first_checkpoint_older_than']) && $params['first_checkpoint_older_than']) {
            $query->ofFirstCheckpointOlderThan($params['first_checkpoint_older_than']);
        }

        if (isset($params['distribution_provider_id']) && $params['distribution_provider_id']) {
            $this->addJoin($joins, 'delivery_routes', 'packages.delivery_route_id', 'delivery_routes.id');
            $this->addJoin($joins, 'legs', 'delivery_routes.id', 'legs.delivery_route_id');
            $this->addJoin($joins, 'provider_services', 'legs.provider_service_id', 'provider_services.id');
            $this->addJoin($joins, 'providers', 'provider_services.provider_id', 'providers.id');
            $query->ofDistributionProviderId($params['distribution_provider_id']);
        }

        if (isset($params['distribution']) && $params['distribution']) {
            $this->addJoin($joins, 'legs as distribution_leg', 'packages.leg_id', 'distribution_leg.id');
            $this->addJoin($joins, 'provider_services as distribution_provider_services', 'distribution_leg.service_type_id', 'distribution_provider_services.id');
            $this->addJoin($joins, 'provider_service_types', 'provider_service_types.id', 'distribution_provider_services.provider_service_type_id');
            $query->ofDistribution();
        }

        if (isset($params['not_distribution']) && $params['not_distribution']) {
            $this->addJoin($joins, 'legs as distribution_leg', 'packages.leg_id', 'distribution_leg.id');
            $this->addJoin($joins, 'provider_services as distribution_provider_services', 'distribution_leg.service_type_id', 'distribution_provider_services.id');
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

        if (isset($params['origin_warehouse_code']) && $params['origin_warehouse_code']) {
            $query->ofOriginWarehouseCode($params['origin_warehouse_code']);
        }

        // Add transit days metrics
        $query->addSelect(DB::raw("(CASE WHEN packages.last_checkpoint_id IS NULL THEN (CASE WHEN packages.first_checkpoint_id IS NULL THEN (CASE WHEN (packages.delivered IS NOT NULL OR packages.returned IS NOT NULL OR packages.canceled IS NOT NULL) THEN  date_part('day','" . $now . "' - packages.first_checkpoint_at) ELSE  date_part('day',packages.last_checkpoint_at - packages.first_checkpoint_at) END) ELSE 0 END) ELSE 0 END) AS controlled_transit_days"));
        $query->addSelect(DB::raw("(CASE WHEN packages.first_checkpoint_id IS NULL THEN (CASE WHEN packages.first_clockstop_id IS NULL THEN  date_part('day',packages.first_clockstop_at - packages.first_checkpoint_at) ELSE NULL END) ELSE NULL END) as clockstop_transit_days"));
        $query->addSelect(DB::raw("(CASE WHEN packages.last_checkpoint_id IS NULL THEN (CASE WHEN (packages.delivered IS NOT NULL OR packages.returned IS NOT NULL OR packages.canceled IS NOT NULL) THEN  date_part('day','" . $now . "' - packages.last_checkpoint_at) ELSE NULL END) ELSE 0 END) as stalled_transit_days"));

        // provider ivoice 
        if (isset($params['package_provider_invoiced']) && $params['package_provider_invoiced']) {
            $this->addJoin($joins, 'package_provider_invoice', 'packages.id', 'package_provider_invoice.package_id');
            $this->addJoin($joins, 'provider_invoices', 'package_provider_invoice.provider_invoice_id', 'provider_invoices.id');
            $query->whereNotNull('provider_invoices.invoiced_at');
        }

        if (isset($params['package_provider_invoiced']) && !$params['package_provider_invoiced']) {
            $query->whereNotExists(function ($query) {
                $query->select('package_provider_invoice.*')
                    ->from('package_provider_invoice')
                    ->whereRaw('package_provider_invoice.package_id = packages.id');
            });
        }

        /*if (isset($params['distribution_provider_invoice_id']) && $params['distribution_provider_invoice_id'] && isset($params['package_provider_invoiced']) && $params['package_provider_invoiced']) {
            $this->addJoin($joins, 'package_provider_invoice', 'packages.id', 'package_provider_invoice.package_id');
            $this->addJoin($joins, 'provider_invoices', 'package_provider_invoice.provider_invoice_id', 'provider_invoices.id');
            $this->addJoin($joins, 'providers', 'provider_invoices.provider_id', 'providers.id');
            $query->ofDistributionProviderId($params['distribution_provider_invoice_id']);
        }*/

        //date packages
        if (isset($params['provider_invoice_controlled_date']) && $params['provider_invoice_controlled_date']) {
            if (isset($params['provider_invoice_date_from']) && $params['provider_invoice_date_from']) {
                $query->ofFirstControlledCheckpointNewerThan($params['provider_invoice_date_from']);
            }
            if (isset($params['provider_invoice_date_to']) && $params['provider_invoice_date_to']) {
                $query->ofFirstControlledCheckpointOlderThan($params['provider_invoice_date_to']);
            }
        }

        //date invoices
        if (isset($params['provider_invoice_controlled_date']) && !$params['provider_invoice_controlled_date']) {
            if (isset($params['provider_invoice_date_from']) && $params['provider_invoice_date_from']) {
                $this->addJoin($joins, 'package_provider_invoice', 'packages.id', 'package_provider_invoice.package_id');
                $this->addJoin($joins, 'provider_invoices', 'package_provider_invoice.provider_invoice_id', 'provider_invoices.id');
                $query->ofProviderInvoiceDateFrom($params['provider_invoice_date_from']);
            }

            if (isset($params['provider_invoice_date_to']) && $params['provider_invoice_date_to']) {
                $this->addJoin($joins, 'package_provider_invoice', 'packages.id', 'package_provider_invoice.package_id');
                $this->addJoin($joins, 'provider_invoices', 'package_provider_invoice.provider_invoice_id', 'provider_invoices.id');
                $query->ofProviderInvoiceDateTo($params['provider_invoice_date_to']);
            }
        }

        if (isset($params['provider_invoice_number']) && $params['provider_invoice_number']) {
            $this->addJoin($joins, 'package_provider_invoice', 'packages.id', 'package_provider_invoice.package_id');
            $this->addJoin($joins, 'provider_invoices', 'package_provider_invoice.provider_invoice_id', 'provider_invoices.id');
            $query->where('provider_invoices.number', $params['provider_invoice_number']);
        }

        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        if ($count) {
            return $query->count('packages.id');
        }

        if (isset($params['sort_by']) && $params['sort_by']) {
            $column = $params['sort_by'];
            $direction = 'asc';
            if (isset($params['sort_direction']) && $params['sort_direction']) {
                $direction = $params['sort_direction'];
            }

            $query->orderBy($column, $direction);
        } else {
            $query->orderBy('packages.first_checkpoint_at', 'desc');
        }

        return $query;
    }

    public function searchBillable($params = [], $distinct = true, $count = false)
    {
        $query = $this->model
            ->select([
                'packages.id',
                'packages.tracking_number',
                'packages.job_order',
                'packages.agreement_id',
                'packages.value',
                'packages.weight',
                'packages.verified_weight',
                'packages.billable_weight',
                'packages.billable_method',
                'packages.first_checkpoint_id',
                'packages.first_checkpoint_at',
            ]);

        if ($distinct) {
            $query->distinct();
        }

        $joins = collect();
        $filters = collect($params);

        if ($filters->has('country_id')) {
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'services', 'agreements.service_id', 'services.id');
            $this->addJoin($joins, 'locations as destination_locations', 'services.destination_location_id', 'destination_locations.id');
            $query->ofDestinationCountryId($params['country_id']);
        }

        if ($filters->has('tracking')) {
            $query->ofTrackingNumber($filters->get('tracking'));
        }

        if ($filters->has('agreement_service_type_key')) {
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'services', 'agreements.service_id', 'services.id');
            $this->addJoin($joins, 'service_types', 'service_types.id', 'services.service_type_id');

            $query->ofAgreementServiceServiceTypeKey($params['agreement_service_type_key']);
        }

        if ($filters->has('agreement_service_id')) {
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'services', 'agreements.service_id', 'services.id');

            $query->ofAgreementServiceId($params['agreement_service_id']);
        }

        if ($filters->has('billable_weight_from')) {
            $query->ofBillableWeightFrom($params['billable_weight_from']);
        }

        if ($filters->has('billable_weight_to')) {
            $query->ofBillableWeightTo($params['billable_weight_to']);
        }

        if ($filters->has('client_name') or $filters->has('client_acronym') or $filters->has('client_id')) {
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'clients', 'agreements.client_id', 'clients.id');

            if ($filters->has('client_name')) {
                $client_name = $filters->get('client_name');
                $query->where('clients.name', 'ilike', $client_name);
            }

            if ($filters->has('client_acronym')) {
                $client_acronym = $filters->get('client_acronym');
                $query->where('clients.acronym', 'ilike', $client_acronym);
            }

            if ($filters->has('client_id')) {
                $client_id = $filters->get('client_id');
                $query->ofClientId($client_id);
            }
        }

        // Invoice Number
        if ($filters->has('invoice_number')) {
            $query->ofInvoiceNumber($filters->get('invoice_number'));
        }

        // Exclude packages older than March 1st, 2017
        $query->ofFirstCheckpointNewerThan('2017-03-01');

        if ($filters->has('uninvoiced')) {
            $query->ofUninvoiced();
        }

        if ($filters->has('invoiced')) {
            $query->ofInvoiced();
        }

        // Date filters
        if ($filters->get('filter_date_by') == 'by_invoiced_at') {
            if ($filters->has('invoiced_at_newer_than')) {
                $query->ofInvoicedAtNewerThan($filters->get('invoiced_at_newer_than'));
            }

            if ($filters->has('invoiced_at_older_than')) {
                $query->ofInvoicedAtOlderThan($filters->get('invoiced_at_older_than'));
            }
        } else {
            if ($filters->has('first_controlled_checkpoint_newer_than')) {
                $query->ofFirstControlledCheckpointNewerThan($filters->get('first_controlled_checkpoint_newer_than'));
            }

            if ($filters->has('first_controlled_checkpoint_older_than')) {
                $query->ofFirstControlledCheckpointOlderThan($filters->get('first_controlled_checkpoint_older_than'));
            }
        }

        // Additional conditions
        if ($filters->get('uninvoiced')) {
            $this->addJoin($joins, 'legs as current_legs', 'packages.leg_id', 'current_legs.id');
            $query->where(function ($conditions) use ($filters, $joins) {
                return $conditions
                    ->where(function ($condition1) use ($filters) {
                        // Controlled
                        $condition1->ofControlled();

                        if ($filters->has('first_controlled_checkpoint_newer_than')) {
                            $condition1->ofFirstControlledCheckpointNewerThan($filters->get('first_controlled_checkpoint_newer_than'));
                        }

                        if ($filters->has('first_controlled_checkpoint_older_than')) {
                            $condition1->ofFirstControlledCheckpointOlderThan($filters->get('first_controlled_checkpoint_older_than'));
                        }

                        return $condition1;
                    })->orWhere(function ($condition2) use ($filters, $joins) {
                        // Not Controlled, not canceled, not stalled and older than 1 month from now (or specific date)
                        $date = Carbon::now()->subMonth()->subDay()->toDateString();
                        if ($filters->has('first_checkpoint_older_than')) {
                            $date = Carbon::parse($filters->get('first_checkpoint_older_than'))->subMonth()->subDay()->toDateString();
                        }
                        $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
                        $this->addJoin($joins, 'clients', 'agreements.client_id', 'clients.id');

                        $condition2
                            ->ofUncontrolled()
                            ->ofUncontrolledUnbillable()
                            ->ofNotCanceled()
                            ->ofNotStalled()
                            ->ofFirstCheckpointOlderThan($date);

                        if ($filters->has('first_checkpoint_newer_than') && $filters->has('first_checkpoint_newer_than') >= '2017-03-01') {
                            $condition2->ofFirstCheckpointNewerThan(Carbon::parse($filters->get('first_checkpoint_newer_than'))->subMonth()->toDateString());
                        }

                        return $condition2;
                    });
            });
        }

        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        if (isset($filters['sort_by']) && $filters['sort_by']) {
            $column = $filters['sort_by'];
            $direction = 'asc';
            if (isset($filters['sort_direction']) && $filters['sort_direction']) {
                $direction = $filters['sort_direction'];
            }

            $query->orderBy($column, $direction);
        } else {
            $query->orderBy('packages.first_checkpoint_at', 'asc')->orderBy('packages.id', 'asc');
        }

        return $count ? $query->count('packages.id') : $query;
    }

    public function searchByCheckpointDateRangeAndCode($from = null, $to = null, array $checkpoint_code_id = null)
    {
        $query = $this->model
            ->select('packages.*')
            ->distinct()
            ->join('checkpoints', 'checkpoints.package_id', '=', 'packages.id')
            ->join('checkpoint_codes', 'checkpoints.checkpoint_code_id', '=', 'checkpoint_codes.id');

        if ($from) {
            $query->where('checkpoints.checkpoint_at', '>', $from . ' 00:00:00');
        }

        if ($to) {
            $query->where('checkpoints.checkpoint_at', '<', $to . ' 23:59:59');
        }

        if ($checkpoint_code_id) {
            if (is_array($checkpoint_code_id)) {
                $query->whereIn('checkpoints.checkpoint_code_id', $checkpoint_code_id);
            } else {
                $query->where('checkpoints.checkpoint_code_id', $checkpoint_code_id);
            }
        }

        return $query->orderBy('checkpoints.checkpoint_at', 'asc');
    }

    public function searchPrealerts(array $filters = [])
    {
        $query = $this->model
            ->select('packages.*')
            ->distinct()
            ->addSelect(DB::raw('max(prealerts.created_at) as last_prealerted_at'))
            ->addSelect(DB::raw('count(prealerts.id) as prealert_count'))
            ->addSelect(DB::raw("string_agg(distinct providers.name, ',') as provider_names"))
            ->groupBy('packages.id');

        $joins = collect();
        $this->addJoin($joins, 'prealerts', 'prealerts.package_id', 'packages.id');
        $this->addJoin($joins, 'providers', 'prealerts.provider_id', 'providers.id');

        if (isset($filters['package_id'])) {
            $query->ofPackageId($filters['package_id']);
        }

        if (isset($filters['provider_id'])) {
            $query->where('prealerts.provider_id', $filters['provider_id']);
        }

        if (isset($filters['tracking']) && $filters['tracking']) {
            $query->ofTrackingNumber($filters['tracking']);
        }

        if (isset($filters['prealert_created_at_newer_than']) && $filters['prealert_created_at_newer_than']) {
            $query->where('prealerts.created_at', '>=', $filters['prealert_created_at_newer_than'] . ' 23:59:59');
        }

        if (isset($filters['prealert_created_at_older_than']) && $filters['prealert_created_at_older_than']) {
            $query->where('prealerts.created_at', '<=', $filters['prealert_created_at_older_than'] . ' 00:00:00');
        }

        if (isset($filters['successful']) && $filters['successful']) {
            $query->whereRaw("(
                select sum(pre2.success) from prealerts as pre2
                where pre2.package_id = packages.id
                group by pre2.package_id) > 0");
        }

        if (isset($filters['unsuccessful']) && $filters['unsuccessful']) {
            $query->whereRaw("(
                select sum(pre2.success) from prealerts as pre2
                where pre2.package_id = packages.id
                group by pre2.package_id) = 0");
        }

        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        if (isset($filters['sort_by']) && $filters['sort_by']) {
            $column = $filters['sort_by'];
            $direction = 'asc';
            if (isset($filters['sort_direction']) && $filters['sort_direction']) {
                $direction = $filters['sort_direction'];
            }

            return $query->orderBy($column, $direction);
        } else {
            return $query->groupBy('packages.id')->orderBy('last_prealerted_at', 'desc');
        }
    }

    public function countAll($params = [])
    {
        return $this->search($params, true);
    }

    public function getByTrackingNumber($tracking_number)
    {
        return $this->model->ofTrackingNumber($tracking_number)->first();
    }

    public function getByTrackingNumberOrAlias($code)
    {
        if (!$code) {
            return null;
        }

        $package = null;
        if (!$package = $this->model->where('packages.tracking_number', strtoupper($code))->first()) {
            // Search by alias
            $package = $this->model
                ->select('packages.*')
                ->join('aliases', 'aliases.package_id', '=', 'packages.id')
                ->where('aliases.code', strtoupper($code))
                ->first();
        }

        return $package;
    }

    public function addItem(Package $package, array $input)
    {
        return $package->packageItems()->create($input);
    }

    public function setBag(Package $package, Bag $bag)
    {
        $package->bag()->associate($bag);

        return $package->save();
    }

    public function setLeg(Package $package, Leg $leg)
    {
        $package->leg()->associate($leg);

        return $package->save();
    }

    public function syncEvents(Package $package)
    {
        $eventCodes = $package->checkpoints->map(function (Checkpoint $checkpoint) {
            return $checkpoint->getCheckpointCodeEventCode();
        })->reject(function ($ec) {
            return !$ec;
        })->unique('id');

        // Check deleted events
        $packageEventCodes = $package->events->map(function (Event $event) {
            return $event->getEventCode();
        });

        $event_codes_to_remove = $packageEventCodes->diff($eventCodes);
        $event_codes_to_add = $eventCodes->diff($packageEventCodes);

        /** @var EventCode $event_code_to_remove */
        foreach ($event_codes_to_remove as $event_code_to_remove) {
            $event_to_remove = $package->events->filter(function (Event $event) use ($event_code_to_remove) {
                return $event->getEventCodeId() == $event_code_to_remove->id;
            })->first();

            if ($event_to_remove) {
                $event_to_remove->delete();
            }
        }

        /** @var EventCode $event_code_to_add */
        foreach ($event_codes_to_add as $event_code_to_add) {
            $last_checkpoint_of_event_code = $package->checkpoints->filter(function (Checkpoint $checkpoint) use ($event_code_to_add) {
                $ec = $checkpoint->getCheckpointCodeEventCode();

                return $ec ? ($ec->id == $event_code_to_add->id) : false;
            })->sortByDesc('checkpoint_at')->first();

            $this->addEvent($package, [
                'package_id'         => $package->id,
                'event_code_id'      => $event_code_to_add->id,
                'last_checkpoint_id' => $last_checkpoint_of_event_code->id,
            ]);
        }

        /** @var Event $event */
        foreach ($package->events as $event) {
            /** @var Checkpoint $last_checkpoint_of_event_code */
            $last_checkpoint_of_event_code = $package->checkpoints->filter(function (Checkpoint $checkpoint) use ($event) {
                $ec = $checkpoint->getCheckpointCodeEventCode();

                return $ec ? ($ec->id == $event->getEventCodeId()) : false;
            })->sortByDesc('checkpoint_at')->first();

            if ($last_checkpoint_of_event_code) {
                $event->update([
                    'last_checkpoint_id' => $last_checkpoint_of_event_code->id,
                ]);

                $event->save();
            }
        }
    }

    public function addEvent(Package &$package, array $input)
    {
        /** @var Event $e */
        $e = $package->events()->create($input);

        return $e;
    }

    public function addCheckpoint(Package &$package, array $input)
    {
        /** @var Checkpoint $c */
        $c = $package->checkpoints()->create($input);

        return $c;
    }

    public function updateKeyCheckpoints(Package &$package)
    {
        $package = $package->fresh(['checkpoints']);

        // Set first & last checkpoints
        $this->setFirstCheckpoint($package);
        $this->setLastCheckpoint($package);
        $this->setFirstControlledCheckpoint($package);
        $this->setFirstClockstop($package);
        $this->recalculateCurrentLeg($package);
    }

    public function updateStatus(Package &$package)
    {
        /** @var Package $package */
        $package = $package->fresh(['checkpoints']);

        // Check if Canceled
        if ($cc = $package->getCanceledCheckpoint()) {
            $clockstop = $cc->getCheckpointCodeClockstop();
            $canceled = true;
            $delivered = $returned = $returning = $stalled = false;

            return $this->checkStatusUpdate($package, $delivered, $returned, $canceled, $stalled, $returning, $clockstop);
        }

        // Check if Returned
        if ($rc = $package->getReturnedCheckpoint()) {
            $clockstop = $rc->getCheckpointCodeClockstop();
            $returned = true;
            $delivered = $canceled = $returning = $stalled = false;

            return $this->checkStatusUpdate($package, $delivered, $returned, $canceled, $stalled, $returning, $clockstop);
        }

        // Check if Delivered
        if ($dc = $package->getDeliveredCheckpoint()) {
            $clockstop = $dc->getCheckpointCodeClockstop();
            $delivered = true;
            $returned = $canceled = $returning = $stalled = false;

            return $this->checkStatusUpdate($package, $delivered, $returned, $canceled, $stalled, $returning, $clockstop);
        }

        // Check if Returning
        if ($rtc = $package->getReturningCheckpoint()) {
            $clockstop = $rtc->getCheckpointCodeClockstop();
            $returning = true;
            $returned = $canceled = $delivered = $stalled = false;

            return $this->checkStatusUpdate($package, $delivered, $returned, $canceled, $stalled, $returning, $clockstop);
        }

        // Check if Stalled
        if ($st = $package->getStalledCheckpoint()) {
            $clockstop = $st->getCheckpointCodeClockstop();
            $stalled = true;
            $returned = $canceled = $delivered = $returning = false;

            return $this->checkStatusUpdate($package, $delivered, $returned, $canceled, $stalled, $returning, $clockstop);
        }

        // Default - Set clockstop when available
        if ($cs = $package->getLastCheckpointOfClockstop()) {
            $clockstop = $cs->getCheckpointCodeClockstop();
            $returned = $canceled = $delivered = $stalled = $returning = false;

            return $this->checkStatusUpdate($package, $delivered, $returned, $canceled, $stalled, $returning, $clockstop);
        }

        // Reset statuses if necessary
        $this->checkStatusUpdate($package);
    }

    private function checkStatusUpdate(Package &$package, $delivered = false, $returned = false, $canceled = false, $stalled = false, $returning = false, $clockstop = false)
    {
        if (($package->delivered != $delivered) or ($package->returned != $returned) or ($package->canceled != $canceled) or ($package->stalled != $stalled) or ($package->returning != $returning) or ($package->clockstop != $clockstop)) {
            return $this->update($package, compact('delivered', 'returned', 'canceled', 'stalled', 'returning', 'clockstop'));
        }

        return true;
    }

    public function setFirstCheckpoint(Package &$package)
    {
        if ($c = $package->getFirstCheckpoint()) {
            if ($fc = $package->firstCheckpoint) { // Checkpoint already set
                if ($c->id == $fc->id) { // If checkpoints are the same
                    if ($c->checkpoint_at != $package->first_checkpoint_at) { // If dates differ
                        $package->first_checkpoint_at = $c->checkpoint_at;
                    } else { // Everything fine
                        return true;
                    }
                } else { // Checkpoints differ
                    $package->firstCheckpoint()->associate($c);
                    $package->first_checkpoint_at = $c->checkpoint_at;
                }
            } else { // Associate checkpoint
                $package->firstCheckpoint()->associate($c);
                $package->first_checkpoint_at = $c->checkpoint_at;
            }
        } else { // There is no checkpoint for this relation
            $package->firstCheckpoint()->dissociate();
            $package->first_checkpoint_at = null;
        }

        return $package->save();
    }

    public function setLastCheckpoint(Package &$package)
    {
        if ($c = $package->getLastCheckpoint()) {
            if ($lc = $package->lastCheckpoint) { // Checkpoint already set
                if ($c->id == $lc->id) { // If checkpoints are the same
                    if ($c->checkpoint_at != $package->last_checkpoint_at) { // If dates differ
                        $package->last_checkpoint_at = $c->checkpoint_at;
                    } else { // Everything fine
                        return true;
                    }
                } else { // Checkpoints differ
                    $package->lastCheckpoint()->associate($c);
                    $package->last_checkpoint_at = $c->checkpoint_at;
                }
            } else { // Associate checkpoint
                $package->lastCheckpoint()->associate($c);
                $package->last_checkpoint_at = $c->checkpoint_at;
            }
        } else { // There is no checkpoint for this relation
            $package->lastCheckpoint()->dissociate();
            $package->last_checkpoint_at = null;
        }

        return $package->save();
    }

    public function setFirstControlledCheckpoint(Package &$package)
    {
        if ($c = $package->getFirstControlledCheckpoint()) {
            if ($fc = $package->firstControlledCheckpoint) { // Checkpoint already set
                if ($c->id == $fc->id) { // If checkpoints are the same
                    if ($c->checkpoint_at != $package->first_controlled_checkpoint_at) { // If dates differ
                        $package->first_controlled_checkpoint_at = $c->checkpoint_at;
                    } else { // Everything fine
                        return true;
                    }
                } else { // Checkpoints differ
                    $package->firstControlledCheckpoint()->associate($c);
                    $package->first_controlled_checkpoint_at = $c->checkpoint_at;
                }
            } else { // Associate checkpoint
                $package->firstControlledCheckpoint()->associate($c);
                $package->first_controlled_checkpoint_at = $c->checkpoint_at;
            }
        } else { // There is no checkpoint for this relation
            $package->firstControlledCheckpoint()->dissociate();
            $package->first_controlled_checkpoint_at = null;
        }

        return $package->save();
    }

    public function setFirstClockstop(Package &$package)
    {
        if ($c = $package->getFirstCheckpointWithClockstop()) {
            if ($fc = $package->firstClockstop) { // Checkpoint already set
                if ($c->id == $fc->id) { // If checkpoints are the same
                    if ($c->checkpoint_at != $package->first_clockstop_at) { // If dates differ
                        $package->first_clockstop_at = $c->checkpoint_at;
                    } else { // Everything fine
                        return true;
                    }
                } else { // Checkpoints differ
                    $package->firstClockstop()->associate($c);
                    $package->first_clockstop_at = $c->checkpoint_at;
                }
            } else { // Associate checkpoint
                $package->firstClockstop()->associate($c);
                $package->first_clockstop_at = $c->checkpoint_at;
            }
        } else { // There is no checkpoint for this relation
            $package->firstClockstop()->dissociate();
            $package->first_clockstop_at = null;
        }

        return $package->save();
    }

    public function recalculateCurrentLeg(Package &$package)
    {
        if ($l = $package->getTraceableLeg()) {
            $cl = $package->leg;
            if ($cl && $l->id == $cl->id) {
                return true;
            }
            $package->leg()->associate($l);
        } else {
            $package->leg()->dissociate();
        }

        return $package->save();
    }

    public function setUploader(Package $package, $user)
    {
        $package->uploader()->associate($user);

        return $package->save();
    }

    public function setClient(Package $package, Client $client)
    {
        $package->client()->associate($client);

        return $package->save();
    }

    public function setPostalOffice(Package $package, PostalOffice $postalOffice)
    {
        $package->postalOffice()->associate($postalOffice);

        return $package->save();
    }

    public function removePostalOffice(Package $package)
    {
        $package->postalOffice()->dissociate();

        return $package->save();
    }

    public function setZipCode(Package $package, ZipCode $zipCode)
    {
        $package->zipCode()->associate($zipCode);

        return $package->save();
    }

    public function removeZipCode(Package $package)
    {
        $package->zipCode()->dissociate();

        return $package->save();
    }

    public function updateDeliveryRoute(Package $package, $delivery_route_id)
    {
        $package->update([
            'delivery_route_id' => $delivery_route_id,
        ]);

        return $package->save();
    }

    public function getPostalOfficesPackagesByAlertQuery(Alert $alert, $params = [])
    {
        $query = $this->model
            ->distinct()
            ->select('packages.*')
            ->addSelect(DB::raw('count(distinct packages.id) as package_count'))
            ->join('checkpoints as last_checkpoints', 'packages.last_checkpoint_id', '=', 'last_checkpoints.id')
            ->join('checkpoints as first_checkpoints', 'packages.first_checkpoint_id', '=', 'first_checkpoints.id')
            ->join('agreements', 'packages.agreement_id', '=', 'agreements.id')
            ->groupBy('packages.postal_office_id')
            ->orderBy('package_count', 'desc')
            ->whereNotNull('packages.postal_office_id');

        $query->ofAlertSubtype($alert);
        $query->ofNotDelivered()->ofNotReturned();
        $query->ofFirstControlledCheckpointNewerThan(Carbon::create(2015, 7, 15, 0, 0, 0));
        if (isset($params['client_id'])) {
            $query->ofClientId($params['client_id']);
        }
        if (isset($params['first_checkpoint_newer_than'])) {
            $query->ofFirstControlledCheckpointNewerThan($params['first_checkpoint_newer_than']);
        }
        if (isset($params['first_checkpoint_older_than'])) {
            $query->ofFirstControlledCheckpointNewerThan($params['first_checkpoint_older_than']);
        }

        $query->where(function ($q) use ($alert) {
            foreach ($alert->alertDetails as $alert_detail) {
                $workdays = $alert->days;
                $classification = $alert_detail->classification;
                $checkpoint_code_ids = $classification->checkpointCodes->pluck('id')->toArray();
                foreach ($checkpoint_code_ids as $cc_id) {
                    $q->orWhere(function ($q2) use ($cc_id, $workdays) {
                        $q2->ofLastCheckpointOfCode($cc_id)->ofCreatedBeforeThan(Carbon::now()->subWeekdays($workdays));
                    });
                }
            }
        });

        return $query;
    }

    public function getPackagesByAlertAndPostalOfficeQuery(Alert $alert, PostalOffice $postalOffice)
    {
        $query = $this->search([
            'first_checkpoint_newer_than' => Carbon::create(2015, 7, 15, 0, 0, 0),
            'postal_office_id'            => $postalOffice->id,
            'federal_district'            => $alert->isFederalDistrictSubtype(),
            'interior'                    => $alert->isInteriorSubtype(),
            'undelivered'                 => true,
            'unreturned'                  => true,
        ]);

        $query = $query->where(function ($q) use ($alert) {
            foreach ($alert->alertDetails as $alert_detail) {
                $workdays = $alert_detail->days;
                $classification = $alert_detail->classification;
                $checkpoint_code_ids = $classification->checkpointCodes->pluck('id')->toArray();
                foreach ($checkpoint_code_ids as $cc_id) {
                    $q->orWhere(function ($q2) use ($cc_id, $workdays) {
                        $q2->ofLastCheckpointOfCode($cc_id)->ofCreatedBeforeThan(Carbon::now()->subWeekdays($workdays));
                    });
                }
            }
        });

        return $query;
    }

    public function getDelayedPackagesByAlertQuery(Alert $alert, AlertDetail $alertDetails, $standard_days, $params = [], $count = false)
    {
        $last_checkpoint_at_from = Carbon::now()->subWeekdays($alertDetails->days);
        $classification = $alertDetails->classification;
        $checkpoint_code_ids = $classification->checkpointCodes->pluck('id')->toArray();

        $defaults = [
            'last_checkpoint_code_id'     => $checkpoint_code_ids,
            'last_checkpoint_at_from'     => $last_checkpoint_at_from,
            'delivery_standard_days'      => $standard_days,
            'first_checkpoint_newer_than' => Carbon::create(2015, 7, 15, 0, 0, 0),
            'undelivered'                 => true,
            'unreturned'                  => true,
            'uncanceled'                  => true,
            'federal_district'            => $alert->isFederalDistrictSubtype(),
            'interior'                    => $alert->isInteriorSubtype(),
            'without_postal_office'       => $alert->isUnclassifiedSubtype()
        ];

        $params = array_merge($defaults, $params);

        return $this->search($params, $count);
    }

    public function getTotalPackagesByAlertQuery(Alert $alert, AlertDetail $alertDetails, $params = [], $count = false)
    {
        $classification = $alertDetails->classification;
        $checkpoint_code_ids = $classification->checkpointCodes->pluck('id')->toArray();

        $defaults = [
            'last_checkpoint_code_id'     => $checkpoint_code_ids,
            'first_checkpoint_newer_than' => Carbon::create(2015, 7, 15, 0, 0, 0),
            'undelivered'                 => true,
            'unreturned'                  => true,
            'uncanceled'                  => true,
            'federal_district'            => $alert->isFederalDistrictSubtype(),
            'interior'                    => $alert->isInteriorSubtype(),
            'without_postal_office'       => $alert->isUnclassifiedSubtype()
        ];

        $params = array_merge($defaults, $params);

        return $this->search($params, $count);
    }

    public function getDashboardOverallQuery($params)
    {
        // Add Selects
        $now = Carbon::now()->toDateTimeString();
        $query = $this->model
            ->select('countries.id as country_id')
            ->addSelect('countries.name as country_name')
            ->addSelect(DB::raw("count(packages.id) as package_count"))
            ->addSelect(DB::raw("sum(case when (packages.verified_weight is not null or packages.verified_weight != 0) then packages.verified_weight else packages.weight end) as total_weight"))
            ->addSelect(DB::raw("sum(case when packages.bag_id is null and packages.first_controlled_checkpoint_id is null then 1 else 0 end) as pick_and_pack"))
            ->addSelect(DB::raw("sum(case when packages.bag_id is null and packages.first_controlled_checkpoint_id is not null then 1 else 0 end) as admissions"))
            ->addSelect(DB::raw("sum(case when packages.bag_id is not null then 1 else 0 end) as dispatched"))
            ->addSelect(DB::raw("sum(case when packages.bag_id is not null and legs.controlled is false then 1 else 0 end) as uncontrolled"))
            ->addSelect(DB::raw("sum(case when packages.bag_id is not null and legs.controlled then 1 else 0 end) as controlled"))
            ->addSelect(DB::raw("sum(case when (packages.bag_id is not null and legs.controlled and (packages.delivered = 1 or packages.canceled = 1 or packages.returned = 1 or packages.stalled = 1 or packages.returning = 1)) then 1 else 0 end) as delivered"))
            ->addSelect(DB::raw("sum(case when (packages.bag_id is not null and legs.controlled and (packages.delivered = 0 and packages.canceled = 0 and packages.returned = 0 and packages.stalled = 0 and packages.returning = 0)) then 1 else 0 end) as undelivered"))
            ->addSelect(DB::raw("sum(case when (packages.bag_id is not null and legs.controlled and packages.clockstop = 1) then 1 else 0 end) as clockstop1"))
            ->addSelect(DB::raw("sum(case when (packages.bag_id is not null and legs.controlled and packages.clockstop = 2) then 1 else 0 end) as clockstop2"))
            ->addSelect(DB::raw("sum((case when (packages.bag_id is not null and legs.controlled and (packages.delivered = 0 and packages.canceled = 0 and packages.returned = 0 and packages.stalled = 0 and packages.returning = 0) and (packages.first_controlled_checkpoint_at IS NULL or (date_part('day',(CASE WHEN (packages.delivered = 0 AND packages.returned = 0 AND packages.canceled = 0) THEN '" . $now . "' ELSE packages.last_checkpoint_at END) - packages.first_controlled_checkpoint_at) <= delivery_routes.controlled_transit_days))) then 1 else 0 end)) as undelivered_on_time"))
            ->addSelect(DB::raw("sum((case when (packages.bag_id is not null and legs.controlled and (packages.delivered = 0 and packages.canceled = 0 and packages.returned = 0 and packages.stalled = 0 and packages.returning = 0) and ( date_part('day',(CASE WHEN (packages.delivered = 0 AND packages.returned = 0 AND packages.canceled = 0) THEN '" . $now . "' ELSE packages.last_checkpoint_at END) - packages.first_controlled_checkpoint_at) > delivery_routes.controlled_transit_days)) then 1 else 0 end)) as undelivered_delayed"))
            ->addSelect(DB::raw("sum((case when (packages.bag_id is not null and legs.controlled and ((packages.delivered = 1 or packages.canceled = 1 or packages.returned = 1) or packages.clockstop > 0)) then 1 else 0 end)) as performance"))
            ->addSelect(DB::raw("avg((case when (packages.bag_id is not null and legs.controlled and ((packages.delivered = 1 or packages.canceled = 1 or packages.returned = 1) or packages.clockstop > 0)) then date_part('day',packages.first_clockstop_at - packages.first_checkpoint_at) else null end)) as transit_days"));

        $joins = collect();
        $this->addJoin($joins, 'legs', 'packages.leg_id', 'legs.id');
        $this->addJoin($joins, 'delivery_routes', 'packages.delivery_route_id', 'delivery_routes.id');
        $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
        $this->addJoin($joins, 'services', 'agreements.service_id', 'services.id');
        $this->addJoin($joins, 'locations as destination_locations', 'services.destination_location_id', 'destination_locations.id');
        $this->addJoin($joins, 'countries', 'destination_locations.country_id', 'countries.id');

        if (isset($params['first_checkpoint_newer_than']) && $params['first_checkpoint_newer_than']) {
            $query->ofFirstCheckpointNewerThan($params['first_checkpoint_newer_than']);
        }

        if (isset($params['first_checkpoint_older_than']) && $params['first_checkpoint_older_than']) {
            $query->ofFirstCheckpointOlderThan($params['first_checkpoint_older_than']);
        }

        if (isset($params['marketplace_id']) && $params['marketplace_id']) {
            $this->addJoin($joins, 'clients', 'agreements.client_id', 'clients.id');
            $this->addJoin($joins, 'client_marketplace', 'client_marketplace.client_id', 'clients.id', 'left outer');
            $query->ofMarketplaceId($params['marketplace_id']);
        }

        if (isset($params['agreement_service_type_key']) && $params['agreement_service_type_key']) {
//            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'services', 'agreements.service_id', 'services.id');
            $this->addJoin($joins, 'service_types', 'service_types.id', 'services.service_type_id');

            $query->ofAgreementServiceServiceTypeKey($params['agreement_service_type_key']);
        }

        if (isset($params['country_id']) && $params['country_id']) {
            $query->ofDestinationCountryId($params['country_id']);
        }

        if (isset($params['client_id']) && $params['client_id']) {
            $query->ofClientId($params['client_id']);
        }

        // Exclude canceled packages
        $query->ofNotCanceled();

        // Perform Joins
        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query = $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        return $query->groupBy('countries.id', 'countries.name')->orderBy('package_count', 'desc')->orderBy('countries.name', 'asc');
    }

    public function getDashboardOverallByMarketplaceQuery($params)
    {
        // Add Selects
        $now = Carbon::now()->toDateTimeString();
        $query = $this->model
            ->select('marketplaces.id as marketplace_id')
            ->addSelect(DB::raw("COALESCE(marketplaces.name,'Others') as marketplace_name"))
            ->addSelect('countries.id as country_id')
            ->addSelect('countries.name as country_name')
            ->addSelect(DB::raw("sum(case when packages.canceled = 0 then 1 else 0 end) as package_count"))
            ->addSelect(DB::raw("sum(case when packages.canceled = 1 then 1 else 0 end) as canceled"))
            ->addSelect(DB::raw("sum(case when packages.canceled = 0 
                                          then case when (packages.verified_weight is not null or packages.verified_weight != 0) then packages.verified_weight else packages.weight end
                                          else 0 end ) as total_weight"));

        $joins = collect();
        $this->addJoin($joins, 'legs', 'packages.leg_id', 'legs.id');
        $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
        $this->addJoin($joins, 'services', 'agreements.service_id', 'services.id');
        $this->addJoin($joins, 'locations as destination_locations', 'services.destination_location_id', 'destination_locations.id');
        $this->addJoin($joins, 'countries', 'destination_locations.country_id', 'countries.id');
        $this->addJoin($joins, 'client_marketplace', 'agreements.client_id', 'client_marketplace.client_id', 'left outer');
        $this->addJoin($joins, 'marketplaces', 'client_marketplace.marketplace_id', 'marketplaces.id', 'left outer');

        if (isset($params['first_checkpoint_newer_than']) && $params['first_checkpoint_newer_than']) {
            $query->ofFirstCheckpointNewerThan($params['first_checkpoint_newer_than']);
        }

        if (isset($params['first_checkpoint_older_than']) && $params['first_checkpoint_older_than']) {
            $query->ofFirstCheckpointOlderThan($params['first_checkpoint_older_than']);
        }

        if (isset($params['marketplace_id']) && $params['marketplace_id']) {
            $this->addJoin($joins, 'clients', 'agreements.client_id', 'clients.id');
            $this->addJoin($joins, 'client_marketplace', 'client_marketplace.client_id', 'clients.id', 'left outer');
            $query->ofMarketplaceId($params['marketplace_id']);
        }

        if (isset($params['agreement_service_type_key']) && $params['agreement_service_type_key']) {
//            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'services', 'agreements.service_id', 'services.id');
            $this->addJoin($joins, 'service_types', 'service_types.id', 'services.service_type_id');

            $query->ofAgreementServiceServiceTypeKey($params['agreement_service_type_key']);
        }

        if (isset($params['country_id']) && $params['country_id']) {
            $query->ofDestinationCountryId($params['country_id']);
        }

        if (isset($params['client_id']) && $params['client_id']) {
            $query->ofClientId($params['client_id']);
        }

        // Perform Joins
        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query = $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        return $query
            ->groupBy('marketplaces.id', 'marketplaces.name', 'countries.id', 'countries.name')
            ->orderBy('package_count', 'desc')
            ->orderBy('marketplace_name', 'asc')
            ->orderBy('countries.name', 'asc');
    }

    public function getDashboardUndeliveredQuery($params)
    {
        // Add Selects
        $now = Carbon::now()->toDateTimeString();
        $query = $this->model
            ->select('countries.id as country_id')
            ->addSelect('countries.name as country_name')
            ->addSelect(DB::raw('count(packages.id) as undelivered'))
            ->addSelect(DB::raw("sum((case when ( date_part('day','" . $now . "' - packages.first_checkpoint_at) <= delivery_routes.controlled_transit_days and packages.first_clockstop_id is not null) then 1 else 0 end)) as with_clockstop_on_time"))
            ->addSelect(DB::raw("sum((case when ( date_part('day','" . $now . "' - packages.first_checkpoint_at) > delivery_routes.controlled_transit_days and packages.first_clockstop_id is not null) then 1 else 0 end)) as with_clockstop_delayed"))
            ->addSelect(DB::raw("sum((case when ( date_part('day','" . $now . "' - packages.first_checkpoint_at) <= delivery_routes.controlled_transit_days and packages.first_clockstop_id is null) then 1 else 0 end)) as without_clockstop_on_time"))
            ->addSelect(DB::raw("sum((case when ( date_part('day','" . $now . "' - packages.first_checkpoint_at) > delivery_routes.controlled_transit_days and packages.first_clockstop_id is null) then 1 else 0 end)) as without_clockstop_delayed"))
            ->whereNotNull('packages.bag_id')
            ->ofControlled()
            ->ofUnaccomplished()
            ->ofNotCanceled();

        $joins = collect();
        $this->addJoin($joins, 'legs as current_legs', 'packages.leg_id', 'current_legs.id');
        $this->addJoin($joins, 'delivery_routes', 'packages.delivery_route_id', 'delivery_routes.id');
        $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
        $this->addJoin($joins, 'services', 'agreements.service_id', 'services.id');
        $this->addJoin($joins, 'locations as destination_locations', 'services.destination_location_id', 'destination_locations.id');
        $this->addJoin($joins, 'countries', 'destination_locations.country_id', 'countries.id');

        if (isset($params['first_checkpoint_newer_than']) && $params['first_checkpoint_newer_than']) {
            $query->ofFirstCheckpointNewerThan($params['first_checkpoint_newer_than']);
        }

        if (isset($params['first_checkpoint_older_than']) && $params['first_checkpoint_older_than']) {
            $query->ofFirstCheckpointOlderThan($params['first_checkpoint_older_than']);
        }

        if (isset($params['marketplace_id']) && $params['marketplace_id']) {
            $this->addJoin($joins, 'clients', 'agreements.client_id', 'clients.id');
            $this->addJoin($joins, 'client_marketplace', 'client_marketplace.client_id', 'clients.id', 'left outer');
            $query->ofMarketplaceId($params['marketplace_id']);
        }

        if (isset($params['country_id']) && $params['country_id']) {
            $query->ofDestinationCountryId($params['country_id']);
        }

        if (isset($params['client_id']) && $params['client_id']) {
            $query->ofClientId($params['client_id']);
        }

        if (isset($params['agreement_service_type_key']) && $params['agreement_service_type_key']) {
//            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'services', 'agreements.service_id', 'services.id');
            $this->addJoin($joins, 'service_types', 'service_types.id', 'services.service_type_id');

            $query->ofAgreementServiceServiceTypeKey($params['agreement_service_type_key']);
        }

        // Perform Joins
        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query = $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        return $query->groupBy('countries.id', 'countries.name')
            ->orderBy('undelivered', 'desc')
            ->orderBy('countries.name', 'asc');
    }

    public function getDashboardDeliveredQuery($params)
    {
        // Add Selects
        $now = Carbon::now()->toDateTimeString();
        $query = $this->model
            ->select('countries.id as country_id')
            ->addSelect('countries.name as country_name')
            ->addSelect(DB::raw("sum(case when (packages.delivered = 1 or packages.returned = 1 or packages.canceled = 1 or packages.stalled = 1 or packages.returning = 1) then 1 else 0 end) as package_count"))
            ->addSelect(DB::raw("sum((case when ((packages.delivered = 1 OR packages.returned = 1 OR packages.canceled = 1 OR packages.stalled = 1 OR packages.returning = 1) and (packages.first_controlled_checkpoint_at IS NULL or (date_part('day',(CASE WHEN (packages.delivered = 0 AND packages.returned = 0 AND packages.canceled = 0) THEN '" . $now . "' ELSE packages.last_checkpoint_at END) - packages.first_controlled_checkpoint_at) <= delivery_routes.controlled_transit_days))) then 1 else 0 end)) as count_on_time"))
            ->addSelect(DB::raw("sum((case when ((packages.delivered = 1 OR packages.returned = 1 OR packages.canceled = 1 OR packages.stalled = 1 OR packages.returning = 1) and (packages.first_controlled_checkpoint_at IS NULL or (date_part('day',(CASE WHEN (packages.delivered = 0 AND packages.returned = 0 AND packages.canceled = 0) THEN '" . $now . "' ELSE packages.last_checkpoint_at END) - packages.first_controlled_checkpoint_at) > delivery_routes.controlled_transit_days))) then 1 else 0 end)) as count_delayed"))
            ->addSelect(DB::raw("sum(case when (packages.delivered = 1) then 1 else 0 end) as delivered"))
            ->addSelect(DB::raw("sum(case when (packages.returned = 1) then 1 else 0 end) as returned"))
            ->addSelect(DB::raw("sum(case when (packages.stalled = 1) then 1 else 0 end) as stalled"))
            ->addSelect(DB::raw('sum(case when (packages.returning = 1) then 1 else 0 end) as returning'))
            ->addSelect(DB::raw("avg((case when (packages.delivered = 1 OR packages.returned = 1 OR packages.canceled = 1 OR packages.stalled = 1 OR packages.returning = 1) then  date_part('day',packages.first_clockstop_at - packages.first_checkpoint_at) else null end)) as transit_days"))
            ->whereNotNull('packages.bag_id')
            ->ofControlled();

        $joins = collect();
        $this->addJoin($joins, 'legs as current_legs', 'packages.leg_id', 'current_legs.id');
        $this->addJoin($joins, 'delivery_routes', 'packages.delivery_route_id', 'delivery_routes.id');
        $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
        $this->addJoin($joins, 'services', 'agreements.service_id', 'services.id');
        $this->addJoin($joins, 'locations as destination_locations', 'services.destination_location_id', 'destination_locations.id');
        $this->addJoin($joins, 'countries', 'destination_locations.country_id', 'countries.id');

        if (isset($params['first_checkpoint_newer_than']) && $params['first_checkpoint_newer_than']) {
            $query->ofFirstCheckpointNewerThan($params['first_checkpoint_newer_than']);
        }

        if (isset($params['first_checkpoint_older_than']) && $params['first_checkpoint_older_than']) {
            $query->ofFirstCheckpointOlderThan($params['first_checkpoint_older_than']);
        }

        if (isset($params['marketplace_id']) && $params['marketplace_id']) {
            $this->addJoin($joins, 'clients', 'agreements.client_id', 'clients.id');
            $this->addJoin($joins, 'client_marketplace', 'client_marketplace.client_id', 'clients.id', 'left outer');
            $query->ofMarketplaceId($params['marketplace_id']);
        }

        if (isset($params['country_id']) && $params['country_id']) {
            $query->ofDestinationCountryId($params['country_id']);
        }

        if (isset($params['client_id']) && $params['client_id']) {
            $query->ofClientId($params['client_id']);
        }

        if (isset($params['agreement_service_type_key']) && $params['agreement_service_type_key']) {
//            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'services', 'agreements.service_id', 'services.id');
            $this->addJoin($joins, 'service_types', 'service_types.id', 'services.service_type_id');

            $query->ofAgreementServiceServiceTypeKey($params['agreement_service_type_key']);
        }

        // Exclude canceled packages
        $query->ofNotCanceled();

        // Perform Joins
        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query = $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        return $query->groupBy('countries.id', 'countries.name')
            ->orderBy('package_count', 'desc')
            ->orderBy('countries.name', 'asc');
    }

    public function getDashboardAdmissionsQuery($params)
    {
        // Add Selects
        $query = $this->model
            ->select('countries.id as country_id')
            ->addSelect('countries.name as country_name')
            ->addSelect(DB::raw("count(packages.id) as total"))
            ->addSelect(DB::raw("sum(case when packages.canceled = 0 then 1 else 0 end) as uncanceled"))
            ->addSelect(DB::raw("sum(case when packages.canceled = 1 then 1 else 0 end) as canceled"))
            ->addSelect(DB::raw("sum(case when provider_service_types.key = 'distribution' then 1 else 0 end) as destination"))
            ->whereNull('packages.bag_id');

        $joins = collect();
        $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
        $this->addJoin($joins, 'services', 'agreements.service_id', 'services.id');
        $this->addJoin($joins, 'locations as destination_locations', 'services.destination_location_id', 'destination_locations.id');
        $this->addJoin($joins, 'countries', 'destination_locations.country_id', 'countries.id');
        $this->addJoin($joins, 'legs', 'packages.leg_id', 'legs.id');
        $this->addJoin($joins, 'provider_services', 'legs.provider_service_id', 'provider_services.id');
        $this->addJoin($joins, 'provider_service_types', 'provider_service_types.id', 'provider_services.provider_service_type_id');

        if (isset($params['first_checkpoint_newer_than']) && $params['first_checkpoint_newer_than']) {
            $query->ofFirstCheckpointNewerThan($params['first_checkpoint_newer_than']);
        }

        if (isset($params['first_checkpoint_older_than']) && $params['first_checkpoint_older_than']) {
            $query->ofFirstCheckpointOlderThan($params['first_checkpoint_older_than']);
        }

        if (isset($params['marketplace_id']) && $params['marketplace_id']) {
            $this->addJoin($joins, 'clients', 'agreements.client_id', 'clients.id');
            $this->addJoin($joins, 'client_marketplace', 'client_marketplace.client_id', 'clients.id', 'left outer');
            $query->ofMarketplaceId($params['marketplace_id']);
        }

        if (isset($params['country_id']) && $params['country_id']) {
            $query->ofDestinationCountryId($params['country_id']);
        }

        if (isset($params['client_id']) && $params['client_id']) {
            $query->ofClientId($params['client_id']);
        }

        if (isset($params['agreement_service_type_key']) && $params['agreement_service_type_key']) {
//            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'services', 'agreements.service_id', 'services.id');
            $this->addJoin($joins, 'service_types', 'service_types.id', 'services.service_type_id');

            $query->ofAgreementServiceServiceTypeKey($params['agreement_service_type_key']);
        }

        // Exclude canceled packages
        $query->ofNotCanceled();

        // Perform Joins
        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query = $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        return $query->groupBy('countries.id', 'countries.name')
            ->orderBy('package_count', 'desc')
            ->orderBy('countries.name', 'asc');
    }

    public function getOperationsSegmentQuery(Segment $segment, $params)
    {
        // Add Selects
        $now = Carbon::now()->toDateTimeString();
        $query = $this->model
            ->select('countries.id as country_id')
            ->addSelect('countries.name as country_name')
            ->addSelect(DB::raw("count(packages.id) as total"));

        $joins = collect();
        $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
        $this->addJoin($joins, 'services', 'agreements.service_id', 'services.id');
        $this->addJoin($joins, 'locations as destination_locations', 'services.destination_location_id', 'destination_locations.id');
        $this->addJoin($joins, 'countries', 'destination_locations.country_id', 'countries.id');
        $this->addJoin($joins, 'checkpoints as last_checkpoints', 'packages.last_checkpoint_id', 'last_checkpoints.id');

        $checkpointCodes = collect();
        foreach ($segment->milestones as $milestone) {
            foreach ($milestone->checkpointCodes as $checkpointCode) {
                $checkpointCodes->push($checkpointCode->id);
            }
        }

        $query->whereIn('last_checkpoints.checkpoint_code_id', $checkpointCodes->toArray());

        foreach ($segment->boundaries as $boundary) {
            if ($boundary->upper) {
                $query->addSelect(DB::raw("sum(case when ( date_part('day','" . $now . "' - packages.last_checkpoint_at) >= {$boundary->lower}) and  date_part('day','" . $now . "' - packages.last_checkpoint_at) < {$boundary->upper} then 1 else 0 end) as boundary_{$boundary->id}"));
            } else {
                $query->addSelect(DB::raw("sum(case when ( date_part('day','" . $now . "' - packages.last_checkpoint_at) >= {$boundary->lower}) then 1 else 0 end) as boundary_{$boundary->id}"));
            }
        }

        if (isset($params['country_id']) && $params['country_id']) {
            $query->ofDestinationCountryId($params['country_id']);
        }

        // Exclude canceled packages
        $query->ofUnfinished();

        // Perform Joins
        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query = $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        return $query->groupBy('countries.id', 'countries.name', 'agreements.type')->orderBy('countries.name', 'asc');
    }

    public function getAverageTransitDays($params = [])
    {
        $query = $this->search($params, false, false);
        $result = $query->select(DB::raw('AVG( date_part(\'day\',packages.first_clockstop_at - packages.first_checkpoint_at)) as avg'))->first();

        return isset($result->avg) ? floatval($result->avg) : 0;
    }

    public function detectPostalOffice(Package &$package)
    {
        // Detect Distribution Center if Sepomex
        if ($package->isAgreementServicesDestinationLocationCountryMexico()) {
            $zip_code = str_pad($package->zip, 5, '0', STR_PAD_LEFT);
            if ($zc = $this->zip_code->getByCode($zip_code)) {
                if ($po = $this->zip_code->getFirstPostalOfficeByZipCode($zc)) {
                    $this->setPostalOffice($package, $po);
                }
            }
        }
    }

    public function normalizeZip(Package $package)
    {
        $zip = $package->zip;
        if (strlen($zip) < 5) {
            $zip = str_pad($zip, 5, '0', STR_PAD_LEFT);
            $this->update($package, ['zip' => $zip]);

            return true;
        }

        return false;
    }

    public function getPackagesByProviderAndDispatch($filters)
    {
        $query = $this->model
            ->select('packages.*')
            ->distinct()
            ->join('bags', 'packages.bag_id', '=', 'bags.id')
            ->join('dispatches', 'dispatches.id', '=', 'bags.dispatch_id')
            ->join('delivery_routes', 'delivery_routes.id', 'packages.delivery_route_id')
            ->join('legs', 'legs.delivery_route_id', '=', 'delivery_routes.id')
            ->join('provider_services', 'provider_services.id', '=', 'legs.provider_service_id')
            ->join('providers', 'providers.id', '=', 'provider_services.provider_id')
            ->leftJoin('package_preadmission', 'package_preadmission.package_id', '=', 'packages.id')
            ->where('providers.id', $filters['distribution_provider_id'])
            ->where('dispatches.id', $filters['dispatch_id'])
            ->whereNull('package_preadmission.package_id');

        return $query->orderBy('packages.tracking_number', 'desc');
    }

    public function searchNotPrealerted()
    {
        $query = $this->model
            ->select('packages.*')
            ->distinct()
            ->join('agreements', 'agreements.id', 'packages.agreement_id')
            ->join("services", "agreements.service_id", '=', "services.id")
            ->join("service_types", "services.service_type_id", '=', "service_types.id")
            ->join("locations", "services.destination_location_id", '=', "locations.id")
            ->join('countries', 'locations.country_id', 'countries.id')
            ->join('prealerts', 'prealerts.package_id', '=', 'packages.id', 'left outer')
            ->ofFirstCheckpointNewerThan('2018-01-01')
            ->whereNull('prealerts.package_id')
            ->whereNotNull('packages.bag_id')
            ->where(function ($query) {
                $query->where('countries.code', 'MX')
                    ->orWhere('countries.code', 'PE')
                    ->orWhere(function ($query) {
                        return $query->where('countries.code', 'CO')->where('service_types.key', 'priority');
                    });

                return $query;
            });

        return $query;
    }
}