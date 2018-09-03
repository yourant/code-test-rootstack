<?php

namespace App\Repositories;

use App\Models\Region;
use App\Models\State;
use App\Models\Town;
use App\Models\AdminLevel1;
use App\Models\AdminLevel2;
use App\Models\AdminLevel3;
use App\Models\ZipCode;

class ZipCodeRepository extends AbstractRepository
{
    /**
     * @var PostalOfficeTypeRepository
     */
    protected $postalOfficeTypeRepository;

    function __construct(ZipCode $model, PostalOfficeTypeRepository $postalOfficeTypeRepository)
    {
        $this->model = $model;
        $this->postalOfficeTypeRepository = $postalOfficeTypeRepository;
    }

    public function getByCode($code)
    {
        return $this->model->whereCode($code)->first();
    }

    public function getByCodeAndCountryCode($code, $countryCode)
    {
        return $this->search(['code' => $code, 'country_code' => $countryCode])->first();
    }

    public function getFirstPostalOfficeByZipCode(ZipCode $zipCode)
    {
        return $zipCode->postalOffices()->first();
    }

    /**
     * @param array $filters
     *
     * @return mixed
     */
    public function search(array $filters = [])
    {
        $query = $this->model
            ->select('zip_codes.*')
            ->distinct();

        if (isset($filters['code']) && $filters['code']) {
            $query->ofCode($filters['code']);
        }

        if (isset($filters['country_code']) && $filters['country_code']) {
            $query
                ->join('admin_level_3', 'zip_codes.admin_level_3_id', '=', 'admin_level_3.id')
                ->join('admin_level_2', 'admin_level_3.admin_level_2_id', '=', 'admin_level_2.id')
                ->join('admin_level_1', 'admin_level_2.admin_level_1_id', '=', 'admin_level_1.id')
                ->join('countries', 'admin_level_1.country_id', '=', 'countries.id')
                ->ofCountryCode($filters['country_code']);

        }

        if (isset($filters['postal_office_type']) && $filters['postal_office_type']) {
            if ($filters['postal_office_type'] == 'Mexpost') {
                $query
                    ->join('postal_office_zip_code', 'postal_office_zip_code.zip_code_id', '=', 'zip_codes.id')
                    ->join('postal_offices', 'postal_offices.id', '=', 'postal_office_zip_code.postal_office_id')
                    ->join('postal_office_types', 'postal_office_types.id', '=', 'postal_offices.postal_office_type_id')
                    ->whereIn('postal_office_types.name', $this->postalOfficeTypeRepository->getMexpostPostalOfficeTypeNames());
            }
        }

        if(isset($filters['region_id']) && $filters['region_id']){
            $query->join('admin_level_3', 'zip_codes.admin_level_3_id', '=', 'admin_level_3.id')
                ->join('admin_level_2', 'admin_level_3.admin_level_2_id', '=', 'admin_level_2.id')
                ->join('admin_level_1', 'admin_level_2.admin_level_1_id', '=', 'admin_level_1.id')
                ->join('regions', 'admin_level_1.region_id', '=', 'regions.id')
                ->where('regions.id', $filters['region_id']);

        }

        if(isset($filters['admin_level_1_id']) && $filters['admin_level_1_id']){
            $query->join('admin_level_3', 'zip_codes.admin_level_3_id', '=', 'admin_level_3.id')
                ->join('admin_level_2', 'admin_level_3.admin_level_2_id', '=', 'admin_level_2.id')
                ->join('admin_level_1', 'admin_level_2.admin_level_1_id', '=', 'admin_level_1.id')
                ->where('admin_level_1.id', $filters['admin_level_1_id']);

        }

        if(isset($filters['admin_level_2_id']) && $filters['admin_level_2_id']){
            $query->join('admin_level_3', 'zip_codes.admin_level_3_id', '=', 'admin_level_3.id')
                ->join('admin_level_2', 'admin_level_3.admin_level_2_id', '=', 'admin_level_2.id')
                ->where('admin_level_2.id', $filters['admin_level_2_id']);
        }

        return $query->orderBy('zip_codes.id', 'asc');
    }

    /**
     * @param array $filters
     *
     * @return mixed
     */
    public function searchByAdminLevel3($adminLevel3Id)
    {
        $query = $this->model
            ->select('zip_codes.*')
            ->distinct()->where('admin_level_3_id', $adminLevel3Id);

        return $query;
    }

    /**
     * @param array $filters
     *
     * @return mixed
     */
    public function getByAdminLevel1(AdminLevel1 $adminLevel1)
    {
        $query = $this->model
            ->select('zip_codes.*')
            ->distinct()
            ->join('admin_level_3', 'zip_codes.admin_level_3_id', '=', 'admin_level_3.id')
            ->join('admin_level_2', 'admin_level_3.admin_level_2_id', '=', 'admin_level_2.id')
            ->join('admin_level_1', 'admin_level_2.admin_level_1_id', '=', 'admin_level_1.id')
            ->where('admin_level_1.id', $adminLevel1->id);

        return $query->get();
    }

    /**
     * @param array $filters
     *
     * @return mixed
     */
    public function getByAdminLevel2(AdminLevel2 $adminLevel2)
    {
        $query = $this->model
            ->select('zip_codes.*')
            ->distinct()
            ->join('admin_level_3', 'zip_codes.admin_level_3_id', '=', 'admin_level_3.id')
            ->join('admin_level_2', 'admin_level_3.admin_level_2_id', '=', 'admin_level_2.id')
            ->where('admin_level_2.id', $adminLevel2->id);

        return $query->get();
    }

    /**
     * @param array $filters
     *
     * @return mixed
     */
    public function getByRegion(Region $region)
    {
        $query = $this->model
            ->select('zip_codes.*')
            ->distinct()
            ->join('admin_level_3', 'zip_codes.admin_level_3_id', '=', 'admin_level_3.id')
            ->join('admin_level_2', 'admin_level_3.admin_level_2_id', '=', 'admin_level_2.id')
            ->join('admin_level_1', 'admin_level_2.admin_level_1_id', '=', 'admin_level_1.id')
            ->join('regions', 'admin_level_1.region_id', '=', 'regions.id')
            ->where('regions.id', $region->id);

        return $query->get();
    }
}
