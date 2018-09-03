<?php

namespace App\Repositories;

use App\Models\AirWaybill;

class AirWaybillRepository extends AbstractRepository
{

    function __construct(AirWaybill $model)
    {
        $this->model = $model;
    }

    public function getByCode($code)
    {
        return $this->search(compact('code'))->first();
    }

    /**
     * @param array $filters
     *
     * @return mixed
     */
    public function search(array $filters = [], $distinct = true)
    {
        $query = $this->model->select('air_waybills.*');

        if ($distinct) {
            $query->distinct();
        }

        if (isset($filters['client_id']) && $filters['client_id']) {

            $query
                ->join('bags', 'bags.dispatch_id', '=', 'dispatches.id')
                ->join('packages', 'packages.bag_id', '=', 'bags.id')
                ->join('agreements', 'packages.agreement_id', '=', 'agreements.id');

            $query->ofClientId($filters['client_id']);
        }

        if (isset($filters['year']) && $filters['year']) {
            $query->ofYear($filters['year']);
        }

        if (isset($filters['code']) && $filters['code']) {
            $query->ofCode($filters['code']);
        }

        if (isset($filters['prefix']) && $filters['prefix']) {
            $query->ofPrefix($filters['prefix']);
        }

        if (isset($filters['created_at_newer_than']) && $filters['created_at_newer_than']) {
            $query->ofCreatedAtNewerThan($filters['created_at_newer_than']);
        }

        if (isset($filters['created_at_older_than']) && $filters['created_at_older_than']) {
            $query->ofCreatedAtOlderThan($filters['created_at_older_than']);
        }

        if (isset($filters['undelivered']) && $filters['undelivered']) {
            $query->ofUndelivered();
        }

        if (isset($filters['provider_invoices_id']) && $filters['provider_invoices_id']) {

            $query
                ->join('dispatches', 'dispatches.air_waybill_id', '=', 'air_waybills.id')
                ->join('bags', 'bags.dispatch_id', '=', 'dispatches.id')
                ->join('packages', 'packages.bag_id', '=', 'bags.id')
                ->join('package_provider_invoice', 'packages.id', '=', 'package_provider_invoice.package_id');

            $query->where('package_provider_invoice.provider_invoice_id', $filters['provider_invoices_id']);
        }

//        if ($orderDispatch) {
//            $query->join('dispatches', 'dispatches.air_waybill_id', '=', 'air_waybills.id')
//                ->addSelect('dispatches.code as dispatches_code')
//                ->orderBy('dispatches_code', 'desc');
//        }

        return $query
            ->orderBy('air_waybills.id', 'desc')
            ->orderBy('air_waybills.departed_at', 'desc');
    }

    public function getDispatchCount(AirWaybill $airWaybill)
    {
        $query = $this->model
            ->distinct()
            ->join('dispatches', 'dispatches.air_waybill_id', '=', 'air_waybills.id')
            ->where('air_waybills.id', $airWaybill->id);

        return $query->count('dispatches.id');
    }

    public function getBagCount(AirWaybill $airWaybill)
    {
        $query = $this->model
            ->distinct()
            ->join('dispatches', 'dispatches.air_waybill_id', '=', 'air_waybills.id')
            ->join('bags', 'bags.dispatch_id', '=', 'dispatches.id')
            ->where('air_waybills.id', $airWaybill->id);

        return $query->count('bags.id');
    }

    public function getPackageCount(AirWaybill $airWaybill)
    {
        $query = $this->model
            ->distinct()
            ->join('dispatches', 'dispatches.air_waybill_id', '=', 'air_waybills.id')
            ->join('bags', 'bags.dispatch_id', '=', 'dispatches.id')
            ->join('packages', 'packages.bag_id', '=', 'bags.id')
            ->where('air_waybills.id', $airWaybill->id);

        return $query->count('packages.id');
    }

    public function getTotalWeight(AirWaybill $airWaybill)
    {
        $query = $this->model
            ->distinct()
            ->join('dispatches', 'dispatches.air_waybill_id', '=', 'air_waybills.id')
            ->join('bags', 'bags.dispatch_id', '=', 'dispatches.id')
            ->join('packages', 'packages.bag_id', '=', 'bags.id')
            ->where('air_waybills.id', $airWaybill->id);

        return $query->sum('packages.weight');
    }

    public function getCheckedInCount(AirWaybill $airWaybill)
    {
        $query = $this->model
            ->distinct()
            ->join('dispatches', 'dispatches.air_waybill_id', '=', 'air_waybills.id')
            ->join('bags', 'bags.dispatch_id', '=', 'dispatches.id')
            ->join('packages', 'packages.bag_id', '=', 'bags.id')
            ->join('checkpoints', 'checkpoints.package_id', '=', 'packages.id')
            ->join('checkpoint_codes', 'checkpoints.checkpoint_code_id', '=', 'checkpoint_codes.id')
            ->join('providers', 'checkpoint_codes.provider_id', '=', 'providers.id')
            ->where('air_waybills.id', $airWaybill->id)
            ->where('providers.code', 'PR5573')
            ->where('checkpoint_codes.type', 'RCS');

        return $query->count('packages.id');
    }

    public function getCheckedInDate(AirWaybill $airWaybill)
    {
        $query = $this->model
            ->distinct()
            ->select('checkpoints.checkpoint_at')
            ->join('dispatches', 'dispatches.air_waybill_id', '=', 'air_waybills.id')
            ->join('bags', 'bags.dispatch_id', '=', 'dispatches.id')
            ->join('packages', 'packages.bag_id', '=', 'bags.id')
            ->join('checkpoints', 'checkpoints.package_id', '=', 'packages.id')
            ->join('checkpoint_codes', 'checkpoints.checkpoint_code_id', '=', 'checkpoint_codes.id')
            ->join('providers', 'checkpoint_codes.provider_id', '=', 'providers.id')
            ->where('air_waybills.id', $airWaybill->id)
            ->where('providers.code', 'PR5573')
            ->where('checkpoint_codes.type', 'RCS')
            ->orderBy('checkpoints.checkpoint_at', 'asc');

        return $query->limit(1)->get()->pluck('checkpoint_at')->first();
    }

    public function getConfirmedCount(AirWaybill $airWaybill)
    {
        $query = $this->model
            ->distinct()
            ->join('dispatches', 'dispatches.air_waybill_id', '=', 'air_waybills.id')
            ->join('bags', 'bags.dispatch_id', '=', 'dispatches.id')
            ->join('packages', 'packages.bag_id', '=', 'bags.id')
            ->join('checkpoints', 'checkpoints.package_id', '=', 'packages.id')
            ->join('checkpoint_codes', 'checkpoints.checkpoint_code_id', '=', 'checkpoint_codes.id')
            ->join('providers', 'checkpoint_codes.provider_id', '=', 'providers.id')
            ->where('air_waybills.id', $airWaybill->id)
            ->where('providers.code', 'PR5573')
            ->where('checkpoint_codes.type', 'RCF');

        return $query->count('packages.id');
    }

    public function getConfirmedDate(AirWaybill $airWaybill)
    {
        $query = $this->model
            ->distinct()
            ->select('checkpoints.checkpoint_at')
            ->join('dispatches', 'dispatches.air_waybill_id', '=', 'air_waybills.id')
            ->join('bags', 'bags.dispatch_id', '=', 'dispatches.id')
            ->join('packages', 'packages.bag_id', '=', 'bags.id')
            ->join('checkpoints', 'checkpoints.package_id', '=', 'packages.id')
            ->join('checkpoint_codes', 'checkpoints.checkpoint_code_id', '=', 'checkpoint_codes.id')
            ->join('providers', 'checkpoint_codes.provider_id', '=', 'providers.id')
            ->where('air_waybills.id', $airWaybill->id)
            ->where('providers.code', 'PR5573')
            ->where('checkpoint_codes.type', 'RCF')
            ->orderBy('checkpoints.checkpoint_at', 'asc');

        return $query->limit(1)->get()->pluck('checkpoint_at')->first();
    }

    public function getDeliveredCount(AirWaybill $airWaybill)
    {
        $query = $this->model
            ->distinct()
            ->join('dispatches', 'dispatches.air_waybill_id', '=', 'air_waybills.id')
            ->join('bags', 'bags.dispatch_id', '=', 'dispatches.id')
            ->join('packages', 'packages.bag_id', '=', 'bags.id')
            ->join('checkpoints', 'checkpoints.package_id', '=', 'packages.id')
            ->join('checkpoint_codes', 'checkpoints.checkpoint_code_id', '=', 'checkpoint_codes.id')
            ->join('providers', 'checkpoint_codes.provider_id', '=', 'providers.id')
            ->where('air_waybills.id', $airWaybill->id)
            ->where('providers.code', 'PR5573')
            ->where('checkpoint_codes.type', 'DLV');

        return $query->count('packages.id');
    }

    public function getDeliveredDate(AirWaybill $airWaybill)
    {
        $query = $this->model
            ->distinct()
            ->select('checkpoints.checkpoint_at')
            ->join('dispatches', 'dispatches.air_waybill_id', '=', 'air_waybills.id')
            ->join('bags', 'bags.dispatch_id', '=', 'dispatches.id')
            ->join('packages', 'packages.bag_id', '=', 'bags.id')
            ->join('checkpoints', 'checkpoints.package_id', '=', 'packages.id')
            ->join('checkpoint_codes', 'checkpoints.checkpoint_code_id', '=', 'checkpoint_codes.id')
            ->join('providers', 'checkpoint_codes.provider_id', '=', 'providers.id')
            ->where('air_waybills.id', $airWaybill->id)
            ->where('providers.code', 'PR5573')
            ->where('checkpoint_codes.type', 'DLV')
            ->orderBy('checkpoints.checkpoint_at', 'asc');

        return $query->limit(1)->get()->pluck('checkpoint_at')->first();
    }
}
