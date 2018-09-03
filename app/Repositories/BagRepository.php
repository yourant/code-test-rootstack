<?php
namespace App\Repositories;

use App\Models\Bag;
use App\Models\Dispatch;
use DB;
use Illuminate\Support\Collection;

class BagRepository extends AbstractRepository
{
    function __construct(Bag $model)
    {
        $this->model = $model;
    }

    private function addJoin(Collection &$joins, $table, $first, $second, $join_type = 'inner')
    {
        if (!$joins->has($table)) {
            $joins->put($table, json_encode(compact('first', 'second', 'join_type')));
        }
    }
    
    public function search(array $params = [])
    {
        $joins = collect();

        $query = $this->model
            ->distinct()
            ->select('bags.*');
        
        if (isset($params['id'])) {
            $query->ofId($params['id']);
        }

        if (isset($params['exclude_ids'])) {
            $query->ofExcludeIds($params['exclude_ids']);
        }
        
        if (isset($params['cn35']) && $params['cn35']) {
            $query = $query->OfTrackingNumber($params['cn35']);
        }

        if (isset($params['cn38']) && $params['cn38']) {
            $this->addJoin($joins, 'dispatches', 'bags.dispatch_id', 'dispatches.id');
            $query = $query->ofCN38($params['cn38']);
        }

        if (isset($params['air_waybill_code']) && $params['air_waybill_code']) {
            $this->addJoin($joins, 'dispatches', 'bags.dispatch_id', 'dispatches.id');
            $this->addJoin($joins, 'air_waybills', 'dispatches.air_waybill_id', 'air_waybills.id', 'left outer');
            $query = $query->ofAirWaybillCode($params['air_waybill_code']);
        }

        if (isset($params['dispatch_id'])) {
            $query = $query->ofDispatchId($params['dispatch_id']);
        }

//        if (isset($params['service_type_id'])) {
//            $this->addJoin($joins, 'packages', 'bags.id', 'packages.bag_id');
//            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
//            $query->ofServiceTypeId($params['service_type_id']);
//        }

        if (isset($params['agreement_id'])) {
            $this->addJoin($joins, 'packages', 'bags.id', 'packages.bag_id');
            $query->ofAgreementId($params['agreement_id']);
        }

        if (isset($params['client_id'])) {
            $this->addJoin($joins, 'packages', 'bags.id', 'packages.bag_id');
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $query->ofClientId($params['client_id']);
        }

        if (isset($params['country_id'])) {
            $this->addJoin($joins, 'packages', 'bags.id', 'packages.bag_id');
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'services', 'agreements.service_id', 'services.id');
            $this->addJoin($joins, 'locations as destination_location', 'services.destination_location_id', 'destination_location.id');
            $query->ofDestinationCountryId($params['country_id']);
        }

        if (isset($params['year']) && $params['year']) {
            $this->addJoin($joins, 'dispatches', 'bags.dispatch_id', 'dispatches.id');
            $query->ofYear($params['year']);
        }

        if (isset($params['provider_invoices_id']) && $params['provider_invoices_id']) {

            $this->addJoin($joins, 'packages', 'packages.bag_id', 'bags.id');
            $this->addJoin($joins, 'package_provider_invoice', 'packages.id', 'package_provider_invoice.package_id');

            $query->where('package_provider_invoice.provider_invoice_id', $params['provider_invoices_id']);
        }

        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        return $query;
    }

    public function getPackages(Bag $bag)
    {
        return $bag->packages;
    }

    public function getInTransitToAirportDate($cn38)
    {
        $bag = $this->search(compact('cn38'))->get()->first();

        return $bag->getInTransitToAirportDate();
    }

    public function getTrackingNumbers()
    {
        return $this->model->distinct()->select('bags.tracking_number')->orderBy('bags.tracking_number')->get();
    }
}