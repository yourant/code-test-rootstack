<?php
namespace App\Repositories;

use App\Models\Leg;

class LegRepository extends AbstractRepository
{

    function __construct(Leg $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $filters
     *
     * @return mixed
     */
    public function search(array $filters = [])
    {
        $table = $this->model->getTable();
        $query = $this->model->distinct();

//        if (!is_null($filters['agreement_id'])) {
//            $query->ofAgreementId($filters['agreement_id']);
//        }

        if (isset($filters['delivery_route_id'])) {
            $query->ofDeliveryRouteId($filters['delivery_route_id']);
        }

        if (isset($filters['position'])) {
            $query->ofPosition($filters['position']);
        }

        return $query->select("{$table}.*")
                     ->orderBy("{$table}.position", 'asc');
    }
} 