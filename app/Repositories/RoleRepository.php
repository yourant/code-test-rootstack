<?php
namespace App\Repositories;

use App\Models\Role;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class RoleRepository extends AbstractRepository
{

    function __construct(Role $model)
    {
        $this->model = $model;
    }

    public function search(array $params = [])
    {
        $query = $this->model;

        return $query->orderBy('roles.name', 'asc');
    }
}