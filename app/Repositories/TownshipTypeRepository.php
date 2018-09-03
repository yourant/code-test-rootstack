<?php
namespace App\Repositories;

use App\Models\TownshipType;

class TownshipTypeRepository extends AbstractRepository
{

    function __construct(TownshipType $model)
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
        $query = $this->model;

        return $query->orderBy('township_types.name', 'asc');
    }
} 