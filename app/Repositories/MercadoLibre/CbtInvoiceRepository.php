<?php

namespace App\Repositories\MercadoLibre;

use App\Models\MercadoLibre\CbtInvoice;
use App\Repositories\AbstractRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CbtInvoiceRepository extends AbstractRepository
{
    public function __construct(CbtInvoice $model)
    {
        $this->model = $model;
    }

    public function search($filters = [])
    {
        $joins = collect();
        $query = $this->model
            ->select('mercadolibre_cbt_invoices.*')
            ->join('packages', 'mercadolibre_cbt_invoices.package_id', '=', 'packages.id')
            ->distinct();

        if (isset($filters['package_id']) && $filters['package_id']) {
            $query = $query->ofPackageId($filters['package_id']);
        }

        if (isset($filters['tracking']) && $filters['tracking']) {
            $tn = $filters['tracking'];
            if (is_array($tn) && !empty($tn)) {
                $query->where(function ($q2) use ($tn) {
                    collect($tn)->each(function ($item) use ($q2) {
                        $q2->orWhere('packages.tracking_number', strtoupper($item));
                    });
                });
            } else {
                $query->where('packages.tracking_number', strtoupper($tn));
            }
        }

        if ( isset($filters['shipper']) && $filters['shipper'] ) {
            $query = $query->where('packages.shipper', 'ILIKE', "%".$filters['shipper']."%");
        }

        if (isset($filters['air_waybill_code']) && $filters['air_waybill_code']) {
            $this->addJoin($joins, 'bags', 'packages.bag_id', 'bags.id');
            $this->addJoin($joins, 'dispatches', 'bags.dispatch_id', 'dispatches.id');
            $this->addJoin($joins, 'air_waybills', 'dispatches.air_waybill_id', 'air_waybills.id');
            $query = $query->where('air_waybills.code', '=', $filters['air_waybill_code']);
        }

        if (isset($filters['client_id']) && $filters['client_id']) {
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'clients', 'agreements.client_id', 'clients.id');
            $query = $query->whereIn('clients.id', $filters['client_id']);
        }

        if (isset($filters['service_id']) && $filters['service_id']) {
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'services', 'agreements.service_id', 'services.id');

            $query = $query->whereIn('services.id', $filters['service_id']);
        }

        if ( isset($filters['period_from']) && $filters['period_from']) {
            $query->OfCreatedAtNewerThan($filters['period_from']);
        }

        if ( isset($filters['period_to']) && $filters['period_to']) {
            $query->OfCreatedAtOlderThan($filters['period_to']);
        }

        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        return $query->orderBy('mercadolibre_cbt_invoices.created_at', 'desc');
    }

    private function addJoin(Collection &$joins, $table, $first, $second, $join_type = 'inner')
    {
        if (!$joins->has($table)) {
            $joins->put($table, json_encode(compact('first', 'second', 'join_type')));
        }
    }
}