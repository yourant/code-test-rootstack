<?php

namespace App\Repositories;

use App\Models\PostalOffice;
use App\Models\ZipCode;

class PostalOfficeRepository extends AbstractRepository
{
    /**
     * @var PostalOfficeTypeRepository
     */
    protected $postalOfficeTypeRepository;

    function __construct(PostalOffice $model, PostalOfficeTypeRepository $postalOfficeTypeRepository)
    {
        $this->model = $model;
        $this->postalOfficeTypeRepository = $postalOfficeTypeRepository;
    }

    public function getByCode($code)
    {
        return $this->model->ofCode($code)->first();
    }

    public function hasZipCode(PostalOffice $postalOffice, ZipCode $zipCode)
    {
        return $postalOffice->zipCodes()->where('zip_code_id', $zipCode->id)->count() > 0;
    }

    public function addZipCode(PostalOffice $postalOffice, ZipCode $zipCode)
    {
        return $postalOffice->zipCodes()->attach($zipCode->id);
    }

    public function syncZipCodes(PostalOffice $postalOffice, array $ids = [])
    {
        return $postalOffice->zipCodes()->sync($ids);
    }

    /**
     * @param array $filters
     *
     * @return mixed
     */
    public function search(array $filters = [], $distinct = true)
    {
        $query = $this->model
            ->select('postal_offices.*')
            ->join('postal_office_types', 'postal_office_types.id', '=', 'postal_offices.postal_office_type_id', 'left outer');

        if($distinct) $query->distinct();

        if (isset($filters['zip_code_id']) && $filters['zip_code_id']) {
            $query
                ->join('postal_office_zip_code', 'postal_office_zip_code.postal_office_id', '=', 'postal_offices.id', 'left outer')
                ->where('postal_office_zip_code.zip_code_id', $filters['zip_code_id']);
        }

        if (isset($filters['code']) && $filters['code']) {
            $query->ofCode($filters['code']);
        }

        if (isset($filters['name']) && $filters['name']) {
            $query->ofName($filters['name']);
        }

        if (isset($filters['provider_id']) && $filters['provider_id']) {
            $query->ofProviderId($filters['provider_id']);
        }

        if (isset($filters['q']) && $filters['q']) {
            $query = $query->ofKeywords($filters['q']);
        }

        if (isset($filters['typename'])) {
            $query->ofTypeName($filters['typename']);
        }

        if (isset($filters['country_id'])) {
            $query->join('postal_office_zip_code', 'postal_office_zip_code.postal_office_id', '=', 'postal_offices.id')
                ->join('zip_codes', 'zip_codes.id', '=', 'postal_office_zip_code.zip_code_id')
                ->join('admin_level_3', 'admin_level_3.id', '=', 'zip_codes.admin_level_3_id')
                ->join('admin_level_2', 'admin_level_3.admin_level_2_id', '=', 'admin_level_2.id')
                ->join('admin_level_1', 'admin_level_2.admin_level_1_id', '=', 'admin_level_1.id')
                ->where('admin_level_1.country_id', $filters['country_id']);
        }

        if (isset($filters['admin_level_2_id'])) {
            $query->join('postal_office_zip_code', 'postal_office_zip_code.postal_office_id', '=', 'postal_offices.id')
                ->join('zip_codes', 'zip_codes.id', '=', 'postal_office_zip_code.zip_code_id')
                ->join('admin_level_3', 'admin_level_3.id', '=', 'zip_codes.admin_level_3_id')
                ->where('admin_level_3.admin_level_2_id', $filters['admin_level_2_id']);
        }

        if (isset($filters['postal_office_type'])) {
            if ($filters['postal_office_type'] == 'Mexpost') {
                $query->whereIn('postal_office_types.name', $this->postalOfficeTypeRepository->getMexpostPostalOfficeTypeNames());
            }
        }

        if (isset($filters['sorting_gate_criteria_id']) && $filters['sorting_gate_criteria_id']) {
            $query->join('postal_office_zip_code', 'postal_offices.id', '=', 'postal_office_zip_code.postal_office_id')
                ->join('zip_codes', 'postal_office_zip_code.zip_code_id', '=', 'zip_codes.id')
                ->join('sorting_gate_criteria_zip_code', 'zip_codes.id', '=', 'sorting_gate_criteria_zip_code.zip_code_id');
            if(is_array($filters['sorting_gate_criteria_id'])){
                $query->whereIn('sorting_gate_criteria_zip_code.sorting_gate_criteria_id', $filters['sorting_gate_criteria_id']);
            } else {
                $query->where('sorting_gate_criteria_zip_code.sorting_gate_criteria_id', $filters['sorting_gate_criteria_id']);
            }
        }

        if (isset($filters['sort_by']) && $filters['sort_by']) {
            $column = $filters['sort_by'];
            $direction = 'asc';
            if (isset($filters['sort_direction']) && $filters['sort_direction']) {
                $direction = $filters['sort_direction'];
            }
            return $query->orderBy($column, $direction);
        } else {
            return $query->orderBy('postal_offices.code', 'asc')->orderBy('postal_offices.name', 'asc');
        }

    }
} 