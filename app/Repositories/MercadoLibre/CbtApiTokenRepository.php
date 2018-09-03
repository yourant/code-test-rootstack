<?php

namespace App\Repositories\MercadoLibre;

use App\Models\MercadoLibre\CbtApiToken;
use App\Repositories\AbstractRepository;

class CbtApiTokenRepository extends AbstractRepository
{

    function __construct(CbtApiToken $model)
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
            ->select('mercadolibre_cbt_api_tokens.*');

        return $query;
    }

    public function getNewest()
    {
        return $this->search()->orderBy('mercadolibre_cbt_api_tokens.acquired_at', 'desc')->first();
    }

}