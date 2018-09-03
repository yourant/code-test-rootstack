<?php
/**
 * Created by PhpStorm.
 * User: plabin
 * Date: 4/5/2018
 * Time: 5:21 PM
 */

namespace App\Repositories;


use App\Models\BillingMode;

class BillingModeRepository extends AbstractRepository
{
    function __construct(BillingMode $model)
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
            ->select('billing_modes.*');

        return $query->orderBy('billing_modes.description', 'asc');
    }
}