<?php

namespace App\Repositories;

use App\Models\SortingType;

class SortingTypeRepository extends AbstractRepository
{
    function __construct(SortingType $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $filters
     *
     * @return mixed
     */
    public function filter(array $filters = [])
    {
        $query = $this->model->select('sorting_types.*');

        if (isset($filters['sorting_type_id']) && $filters['sorting_type_id']) {
            $query->ofId($filters['sorting_type_id']);
        }

        if (isset($filters['name_like']) && $filters['name_like']) {
            $query->ofNameLike($filters['name_like']);
        }

        return $query;
    }
}
