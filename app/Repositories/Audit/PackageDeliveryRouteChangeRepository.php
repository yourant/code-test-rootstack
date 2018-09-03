<?php
namespace App\Repositories\Audit;

use App\Repositories\AbstractRepository;
use App\Models\Audit\PackageDeliveryRouteChange;

class PackageDeliveryRouteChangeRepository  extends AbstractRepository
{
    function __construct(PackageDeliveryRouteChange $model)
    {
        $this->model = $model;
    }
}