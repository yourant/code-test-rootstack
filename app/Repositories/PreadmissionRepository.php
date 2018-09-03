<?php
namespace App\Repositories;

use App\Models\Preadmission;
use App\Models\Package;

class PreadmissionRepository extends AbstractRepository {

    function __construct(Preadmission $model)
    {
        $this->model = $model;
    }

    public function setPackage(Preadmission $preadmission, Package $package)
    {
        $preadmission->packages()->attach($package->id);

        return $preadmission->save();
    }
} 