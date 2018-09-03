<?php
namespace App\Repositories;

use App\Models\Zone;

class ZoneRepository extends AbstractRepository {

    function __construct(Zone $model)
    {
        $this->model = $model;
    }

    public function getByName($name) {
        return $this->model->whereName($name)->first();
    }

    /**
     * @param array $filters
     *
     * @return mixed
     */
    public function search(array $filters = [])
    {
        $query = $this->model;

        return $query->orderBy('zones.name', 'asc');
    }
} 