<?php

namespace App\Repositories;

use App\Models\ExchangeRate;
use Carbon\Carbon;

class ExchangeRateRepository extends AbstractRepository
{

    function __construct(ExchangeRate $model)
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
            ->select('exchange_rates.*')
            ->distinct();

        if (isset($filters['max_created_at']) && $filters['max_created_at']) {
            $date = Carbon::parse($filters['max_created_at'])->format('Y-m-d');

            $query->where(function ($query2) use ($date) {
                return $query2->whereRaw("created_at = (SELECT MAX(created_at)
					                                    FROM exchange_rates
					                                    WHERE exchange_rates.created_at >= '{$date} 00:00:00'
								                              AND exchange_rates.created_at <= '{$date} 23:59:59')");
            });
        }

        return $query;
    }
}