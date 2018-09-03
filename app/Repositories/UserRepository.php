<?php

namespace App\Repositories;

use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Database\Eloquent\Model;
use Hash;
use Illuminate\Support\Collection;

class UserRepository extends AbstractRepository
{

    function __construct(User $model)
    {
        $this->model = $model;
    }

    public function search(array $params = [])
    {
        $query = $this->model->select('users.*')->distinct();

        if (isset($params['email']) && $params['email']) {
            $query->ofEmailLike($params['email']);
        }

        return $query;
    }

    public function create(array $credentials, $activate = true)
    {
        return Sentinel::register($credentials, $activate);
    }

    public function update(Model $model, array $attributes)
    {
        if (isset($attributes['password']) && $attributes['password']) {
            $attributes['password'] = Hash::make($attributes['password']);
        } else {
            unset($attributes['password']);
        }

        return parent::update($model, $attributes);
    }

    public function syncRoles(User $user, Collection $roles)
    {
        return $user->roles()->sync($roles->toArray());
    }

}