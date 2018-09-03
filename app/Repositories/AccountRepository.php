<?php
namespace App\Repositories;

use App\Models\Client;
use App\Models\Account;

class AccountRepository extends AbstractRepository
{

    function __construct(Account $model)
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
            ->select('accounts.*');

        if (isset($filters['managed_by']) && $filters['managed_by']) {
            $query->ofManagedBy($filters['managed_by']);
        }

        if (isset($filters['owned_by']) && $filters['owned_by']) {
            $query->ofOwnedBy($filters['owned_by']);
        }

        return $query->orderBy('accounts.id', 'asc');
    }

    public function getByManager(Client $manager)
    {
        return $this->search(['managed_by' => $manager->id])->get();
    }

    public function getByOwner(Client $owner)
    {
        return $this->search(['owned_by' => $owner->id])->get();
    }
} 