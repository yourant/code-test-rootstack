<?php
/**
 * Created by PhpStorm.
 * User: plabin
 * Date: 7/5/2018
 * Time: 10:05 AM
 */

namespace App\Repositories;


use App\Models\ServiceType;

class ServiceTypeRepository extends AbstractRepository
{
    function __construct(ServiceType $model)
    {
        $this->model = $model;
    }

    public function search(array $filters = [])
    {
        $query = $this->model;

        if (isset($filters['with_services']) && $filters['with_services']) {
            $query->join('services', 'services.service_type_id', '=', 'service_types.id');
            $query->whereNotNull('services.service_type_id');
        }

        return $query->orderBy('service_types.description', 'asc');
    }
}