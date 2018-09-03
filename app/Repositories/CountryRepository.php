<?php

namespace App\Repositories;

use App\Models\Continent;
use App\Models\Country;

class CountryRepository extends AbstractRepository
{
    function __construct(Country $model)
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
        $query = $this->model
            ->distinct('countries.id')
//            ->join('agreements', 'agreements.country_id', '=', 'countries.id', 'left outer');
            ->join('locations as destination_locations', 'destination_locations.country_id', '=', 'countries.id', 'left outer');

        if (isset($filters['code']) && $filters['code']) {
            $query = $query->ofCode($filters['code']);
        }

        if (isset($filters['name']) && $filters['name']) {
            $query = $query->ofName($filters['name']);
        }

        if (isset($filters['with_agreements']) && $filters['with_agreements']) {
            $query->join('services', 'services.destination_location_id', '=', 'destination_locations.id');
            $query->whereNotNull('services.destination_location_id');
        }

        if (isset($filters['with_clients']) && $filters['with_clients']) {
            $query->join('clients', 'clients.country_id', '=', 'countries.id', 'left outer');
            $query->whereNotNull('clients.country_id');
        }

        return $query->select("{$table}.*")->orderBy("{$table}.name", 'asc');
    }

    public function getByCode($code)
    {
        return $this->search(compact('code'))->first();
    }

    public function setContinent(Country $country, Continent $continent)
    {
        $country->continent()->associate($continent);

        return $country->save();
    }
} 