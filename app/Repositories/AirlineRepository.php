<?php
namespace App\Repositories;

use App\Models\Airline;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;

class AirlineRepository extends AbstractRepository
{

    function __construct(Airline $model)
    {
        $this->model = $model;
    }

    public function getByPrefix($prefix)
    {
        return $this->search(compact('prefix'))->first();
    }

    /**
     * @param array $filters
     *
     * @return mixed
     */
    public function search(array $filters = [], $distinct = true)
    {
        $query = $this->model->select('airlines.*');


        if($distinct)
            $query->distinct();

        if (isset($filters['name']) && $filters['name']) {
            $query->ofName($filters['name']);
        }

        if (isset($filters['prefix']) && $filters['prefix']) {
            $query->ofPrefix($filters['prefix']);
        }

        return $query;
    }
}
