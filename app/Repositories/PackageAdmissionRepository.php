<?php
/**
 * Created by PhpStorm.
 * User: plabin
 * Date: 27/12/2017
 * Time: 3:32 PM
 */

namespace App\Repositories;


use App\Models\Package;
use Illuminate\Support\Collection;

class PackageAdmissionRepository extends AbstractRepository
{
    function __construct(Package $model)
    {
        $this->model = $model;
    }

    private function addJoin(Collection &$joins, $table, $first, $second, $join_type = 'inner')
    {
        if (!$joins->has($table)) {
            $joins->put($table, json_encode(compact('first', 'second', 'join_type')));
        }
    }

    public function search($params = [], $count = false, $distinct = true)
    {
        $query = $this->model
            ->select('packages.*');

        if ($distinct) {
            $query = $query->groupBy('packages.id');
        }

        $filters = collect($params);

        $joins = collect();

        if ($filters->has('tracking')) {
            $query->ofTrackingOrCustomerTracking($filters->get('tracking'));
        }

        if ($filters->has('agreement_id')) {
            $query->ofAgreementId($filters->get('agreement_id'));
        }

        if ($filters->has('delivery_route_id')) {
            $query->ofDeliveryRouteId($filters->get('delivery_route_id'));
        }

        if ($filters->has('service_id')) {
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $query->ofServiceId($filters->get('delivery_route_id'));
        }

        if ($filters->has('has_sorting')) {
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'services', 'agreements.service_id', 'services.id');
            $query->where('services.sorting_id', '!=', null);
        }

        if ($filters->has('service_code')) {
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'services', 'agreements.service_id', 'services.id');
            $query->where('services.code', '=', $filters->get('service_code'));
        }

        if ($filters->has('customer_tracking_number')) {
            $query->ofCustomerTrackingNumber($filters->get('customer_tracking_number'));
        }

        if ($filters->has('tracking_number')) {
            $query->ofTrackingOrCustomerTracking($filters->get('tracking_number'));
        }

        if (isset($params['job_order'])) {
            $query->ofJobOrder($params['job_order']);
        }

        if ($filters->get('invoice_number')) {
            $query->ofInvoiceNumber($filters->get('invoice_number'));
        }

        if (isset($params['marketplace_id']) && $params['marketplace_id']) {
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'clients', 'agreements.client_id', 'clients.id');
            $this->addJoin($joins, 'client_marketplace', 'client_marketplace.client_id', 'clients.id', 'left outer');
            $query->ofMarketplaceId($params['marketplace_id']);
        }

        if (isset($params['service_type_key']) && $params['service_type_key']) {
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'services', 'agreements.service_id', 'services.id');
            $this->addJoin($joins, 'service_types', 'services.service_type_id', 'service_types.id');
            $query->ofAgreementServiceServiceTypeKey($params['service_type_key']);
        }

        if ($filters->has('period_from') or $filters->has('period_to') or $filters->has('first_checkpoint_newer_than') or $filters->has('first_checkpoint_older_than') or $filters->has('last_checkpoint_newer_than') or $filters->has('last_checkpoint_older_than')) {
            if ($filters->has('period_from')) {
                $query->ofFirstCheckpointNewerThan($filters->get('period_from'));
            }

            if ($filters->has('period_to')) {
                $query->ofFirstCheckpointOlderThan($filters->get('period_to'));
            }

            if (isset($params['first_checkpoint_newer_than'])) {
                $query->ofFirstCheckpointNewerThan($params['first_checkpoint_newer_than']);
            }

            if (isset($params['first_checkpoint_older_than'])) {
                $query->ofFirstCheckpointOlderThan($params['first_checkpoint_older_than']);
            }

            if (isset($params['last_checkpoint_newer_than'])) {
                $query->ofLastCheckpointNewerThan($params['last_checkpoint_newer_than']);
            }

            if (isset($params['last_checkpoint_older_than'])) {
                $query->ofLastCheckpointOlderThan($params['last_checkpoint_older_than']);
            }
        }

        if ($filters->has('checkpoint_code_id')) {
            $this->addJoin($joins, 'checkpoints', 'checkpoints.package_id', 'packages.id');
            $query->ofCheckpointCodeId($filters->get('checkpoint_code_id'));
        }

        if ($filters->has('last_checkpoint_code_id')) {
            $this->addJoin($joins, 'checkpoints as last_checkpoints', 'last_checkpoints.id', 'packages.last_checkpoint_id');
            $lcc_id = $filters->get('last_checkpoint_code_id');
            if (is_array($lcc_id) && !empty($lcc_id)) {
                $query->whereIn('last_checkpoints.checkpoint_code_id', $lcc_id);
            } else {
                $query->where('last_checkpoints.checkpoint_code_id', $lcc_id);
            }
        }

        if ($filters->get('unfinished')) {
            $query->ofUnfinished();
        }

        if ($filters->get('uninvoiced')) {
            $query->ofUninvoiced();
        }

        if ($filters->get('invoiced')) {
            $query->ofInvoiced();
        }

        if ($filters->get('verified_weight')) {
            $query->ofVerifiedWeight();
        }

        if (isset($params['delivered']) && $params['delivered']) {
            $query->ofDelivered();
        }

        if (isset($params['undelivered']) && $params['undelivered']) {
            $query->ofNotDelivered();
        }

        if (isset($params['canceled']) && $params['canceled']) {
            $query->ofCanceled();
        }

        if (isset($params['has_first_controlled_checkpoint']) && $params['has_first_controlled_checkpoint']) {
            $query->ofHasFirstControlledCheckpoint();
        }

        if (isset($params['controlled']) or isset($params['uncontrolled']) or isset($params['service_type_id'])) {
            $this->addJoin($joins, 'legs as current_legs', 'packages.leg_id', 'current_legs.id', 'left outer');
            if (isset($params['controlled']) && $params['controlled']) {
                $query->ofControlled();
            }

            if (isset($params['uncontrolled']) && $params['uncontrolled']) {
                $query->ofUncontrolled();
            }

            if (isset($params['service_type_id']) && $params['service_type_id']) {
                $query->ofServiceTypeId($params['service_type_id']);
            }
        }

        if ($filters->has('client_id') or $filters->has('client_name') or $filters->has('client_acronym')) {
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'clients', 'agreements.client_id', 'clients.id');

            if ($filters->has('client_id')) {
                $client_id = $filters->get('client_id');
                if (is_array($client_id) && !empty($client_id)) {
                    $query->whereIn('agreements.client_id', $client_id);
                } else {
                    $query->where('agreements.client_id', $client_id);
                }
            }

            if ($filters->has('client_name')) {
                $client_name = $filters->get('client_name');
                $query->where('clients.name', 'ilike', $client_name);
            }

            if ($filters->has('client_acronym')) {
                $client_acronym = $filters->get('client_acronym');
                $query->where('clients.acronym', 'ilike', $client_acronym);
            }
        }

        if ($filters->has('country_id')) {
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'services', 'agreements.service_id', 'services.id');
            $this->addJoin($joins, 'locations as destination_location', 'services.destination_location_id', 'destination_location.id');
            $country_id = $filters->get('country_id');
            if (is_array($country_id) && !empty($country_id)) {
                $query->whereIn('destination_location.country_id', $country_id);
            } else {
                $query->where('destination_location.country_id', $country_id);
            }
        }

        //
        if (isset($params['checkpoint_filtered_code_id']) && $params['checkpoint_filtered_code_id']){
            $this->addJoin($joins, 'checkpoints as filtered_checkpoints', 'packages.id', 'filtered_checkpoints.package_id');
            if (isset($params['checkpoint_filtered_newer_than']) && $params['checkpoint_filtered_newer_than']){
                $query->OfCheckpointFilteredNewerThan($params['checkpoint_filtered_newer_than']);
            }

            if (isset($params['checkpoint_filtered_older_than']) && $params['checkpoint_filtered_older_than']){
                $query->OfCheckpointFilteredOlderThan($params['checkpoint_filtered_older_than']);
            }

            if (is_array($params['checkpoint_filtered_code_id'])) {
                $query->whereIn('filtered_checkpoints.checkpoint_code_id', $params['checkpoint_filtered_code_id']);
            } else {
                $query->where('filtered_checkpoints.checkpoint_code_id', $params['checkpoint_filtered_code_id']);
            }
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

        if (isset($params['air_waybill_code']) && $params['air_waybill_code']) {
            $this->addJoin($joins, 'bags', 'packages.bag_id', 'bags.id');
            $this->addJoin($joins, 'dispatches', 'bags.dispatch_id', 'dispatches.id');
            $this->addJoin($joins, 'air_waybills', 'dispatches.air_waybill_id', 'air_waybills.id');
            $query->OfAirWaybillCode($params['air_waybill_code']);
        }

        if (isset($params['distribution_provider_id']) && $params['distribution_provider_id']) {
            $this->addJoin($joins, 'delivery_routes', 'packages.delivery_route_id', 'delivery_routes.id');
            $this->addJoin($joins, 'legs', 'delivery_routes.id', 'legs.delivery_route_id');
            $this->addJoin($joins, 'provider_services', 'legs.provider_service_id', 'provider_services.id');
            $this->addJoin($joins, 'providers', 'provider_services.provider_id', 'providers.id');
            $query->ofDistributionProviderId($params['distribution_provider_id']);
        }

        if (isset($params['provider_invoices_id']) && $params['provider_invoices_id']) {

            $this->addJoin($joins, 'package_provider_invoice', 'packages.id', 'package_provider_invoice.package_id');

            $query->where('package_provider_invoice.provider_invoice_id', $params['provider_invoices_id']);
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

//        return $count ? $query->count('packages.id') : $query->orderBy('packages.first_checkpoint_at', 'desc');
    }

    public function getByTrackingNumber($tracking_number)
    {
        return $this->model->whereTrackingNumber($tracking_number)->first();
    }

}