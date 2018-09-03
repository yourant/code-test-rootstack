<?php

namespace App\Repositories;

use App\Models\ObservableModel;

class ObservableModelRepository extends AbstractRepository
{
    function __construct(ObservableModel $model)
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
        $query = $this->model
            ->distinct()
            ->select('observable_models.*');

        if(isset($filters['id']) && $filters['id']){
            $query->ofId($filters['id']);
        }

        return $query;
    }
}