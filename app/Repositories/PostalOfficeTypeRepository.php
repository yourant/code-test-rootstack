<?php
namespace App\Repositories;

use App\Models\PostalOfficeType;

class PostalOfficeTypeRepository extends AbstractRepository
{
    function __construct(PostalOfficeType $model)
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

        return $query->orderBy('postal_office_types.name', 'asc');
    }

    public function getMexpostPostalOfficeTypeNames()
    {
        return [
            "Administración Postal Mexpost (AP)",
            "Centro Operativo Mexpost (COM)",
            "Oficina Operativa Mexpost (OOP)",
            "Coordinación Mexpost"
        ];
    }
} 