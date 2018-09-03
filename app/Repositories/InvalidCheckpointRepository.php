<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 27/03/18
 * Time: 09:20 AM
 */

namespace App\Repositories;


use App\Models\InvalidCheckpoint;

/**
 * Class InvalidCheckpointRepository
 * @package App\Repositories
 */
class InvalidCheckpointRepository extends AbstractRepository
{
    /**
     * InvalidChecpointRepository constructor.
     * @param InvalidCheckpoint $model
     */
    public function __construct(InvalidCheckpoint $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $params
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function search($params = [])
    {
        $query = $this->model
            ->select('invalid_checkpoints.*')
            ->distinct();

        if (isset($params['unsent']) && $params['unsent']){
            $query = $query->ofUnsent();
        }

        if (isset($params['tracking_number']) && $params['tracking_number']) {
            $query->join('packages', 'invalid_checkpoints.package_id', '=', 'packages.id');
            $query->where('packages.tracking_number', $params['tracking_number']);
        }

        return $query;
    }
}