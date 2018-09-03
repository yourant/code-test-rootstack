<?php
/**
 * Created by PhpStorm.
 * User: plabin
 * Date: 23/1/2018
 * Time: 11:00 AM
 */

namespace App\Repositories;

use App\Models\AdminLevel3Type;

class AdminLevel3TypeRepository extends AbstractRepository
{
    function __construct(AdminLevel3Type $model)
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
        $query = $this->model;

        return $query->orderBy('admin_level_3_types.name', 'asc');
    }
}