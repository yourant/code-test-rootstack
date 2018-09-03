<?php
/**
 * Created by PhpStorm.
 * User: plabin
 * Date: 16/11/2017
 * Time: 4:37 PM
 */

namespace App\Repositories;


use App\Models\ProviderServiceType;

class ProviderServiceTypeRepository extends AbstractRepository
{
    function __construct(ProviderServiceType $model)
    {
        $this->model = $model;
    }

    public function getByName($name)
    {
        return $this->model->where('name', $name)->first();
    }

    public function getByKey($key)
    {
        return $this->model->where('key', $key)->first();
    }

}