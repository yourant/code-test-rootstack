<?php
namespace App\Repositories;

use App\Models\Timezone;

class TimezoneRepository extends AbstractRepository {

    function __construct(Timezone $model)
    {
        $this->model = $model;
    }

    public function getByName($name) {
        return $this->model->whereName($name)->first();
    }

    public function getByNameAndDescription($name, $description) {
        return $this->model->whereName($name)->whereDescription($description)->first();
    }

    public function create(array $input)
    {
        return $this->model->create($input);
    }

    /**
     * @param array $filters
     *
     * @return mixed
     */
    public function search(array $filters = [])
    {
        $query = $this->model;

        return $query->orderBy('timezones.id', 'asc');
    }
} 