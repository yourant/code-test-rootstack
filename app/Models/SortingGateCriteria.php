<?php

namespace App\Models;

use App\Presenters\SortingGateCriteriaPresenter;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class SortingGateCriteria
 * @package App
 *
 * @property SortingGate $sortingGate
 * @property SortingType $sortingType
 */
class SortingGateCriteria extends Model implements HasPresenter
{
    /**
     * @var array
     */
    protected $fillable = ['sorting_gate_id', 'sorting_type_id', 'after_than', 'before_than', 'criteria_code', 'deleted_at', 'modified_by'];

    /**
     * Get the presenter class.
     *
     * @return string
     */

    public function sortingGate()
    {
        return $this->belongsTo(SortingGate::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sortingType()
    {
        return $this->belongsTo(SortingType::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function zipCodes()
    {
        return $this
            ->belongsToMany(ZipCode::class, 'sorting_gate_criteria_zip_code', 'sorting_gate_criteria_id', 'zip_code_id')
            ->withTimestamps()
            ->withPivot(['modified_by']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function modifier()
    {
        return $this->belongsTo(User::class, 'modified_by');
    }

    public function getFirstZipCode()
    {
        return $this->zipCodes ? $this->zipCodes->first() : null;
    }

    public function getSortingTypeNameAttribute()
    {
        return $this->sortingType ? $this->sortingType->name : null;
    }

    public function getSortingTypeDescriptionAttribute()
    {
        return $this->sortingType ? $this->sortingType->description : null;
    }

    /**
     * @return array
     */
    public function getStatesNames()
    {
        $states = collect();
        $zipCodes = $this->zipCodes;
        foreach ($zipCodes as $zip) {
            $state = $zip->getAdminLevel3AdminLevel2AdminLevel1Name();
            $states->contains($state) ? null : $states->push($state);
        }

        return $states->toArray();
    }

    /**
     * @return array
     */
    public function getRegionsNames()
    {
        $regions = collect();
        $zipCodes = $this->zipCodes;
        foreach ($zipCodes as $zip) {
            $region = $zip->getAdminLevel3AdminLevel2AdminLevel1RegionName();
            $regions->contains($region) ? null : $regions->push($region);
        }

        return $regions->toArray();
    }

    public function getTownsNames()
    {
        $towns = collect();
        $zipCodes = $this->zipCodes;
        foreach ($zipCodes as $zip) {
            $town = $zip->getAdminLevel3AdminLevel2Name();
            $towns->contains($town) ? null : $towns->push($town);
        }

        return $towns->toArray();
    }

    public function getCitiesNames()
    {
        $cities = collect();
        $zipCodes = $this->zipCodes;
        foreach ($zipCodes as $zip) {
            $city = $zip->getAdminLevel3AdminLevel2Name();
            $cities->contains($city) ? null : $cities->push($city);
        }

        return $cities->toArray();
    }

    public function getTownshipsNames()
    {
        $townships = collect();
        $zipCodes = $this->zipCodes;
        foreach ($zipCodes as $zip) {
            $township = $zip->getAdminLevel3Name();
            $townships->contains($township) ? null : $townships->push($township);
        }

        return $townships->toArray();
    }

    public function getRegions()
    {
        $regions = collect();
        $zipCodes = $this->zipCodes;
        foreach ($zipCodes as $zip) {
            $city = $zip->getAdminLevel3AdminLevel2();
            $region = $city->region;
            $regions->push($region);
        }

        return $regions->unique()->toArray();
    }

    public function getStates()
    {
        $states = collect();
        $zipCodes = $this->zipCodes;
        foreach ($zipCodes as $zip) {
            $city = $zip->getAdminLevel3AdminLevel2();
            $state = $city->state;
            $states->push($state);
        }

        return $states->unique()->toArray();
    }

    public function getCities()
    {
        $cities = collect();
        $zipCodes = $this->zipCodes;
        foreach ($zipCodes as $zip) {
            $city = $zip->getAdminLevel3AdminLevel2();
            $cities->push($city);
        }

        return $cities->unique()->toArray();
    }

    public function getPostalOffices()
    {
        $postalOffices = collect();
        $zipCodes = $this->zipCodes;
        foreach ($zipCodes as $zip) {
            foreach ($zip->postalOffices as $office) {
                $postalOffices->push($office);
            }
        }

        return $postalOffices->unique()->toArray();
    }

    public function getPostalOfficeNames()
    {
        $postalOffices = collect();
        $zipCodes = $this->zipCodes;
        foreach ($zipCodes as $zip) {
            foreach ($zip->postalOffices as $office) {
                $postalOffice = $zip->getAdminLevel3AdminLevel2Name() . ', ' . $office->name;
                $postalOffices->contains($postalOffice) ? null : $postalOffices->push($postalOffice);
            }
        }

        return $postalOffices->toArray();
    }

    public function getZipCode($value = '')
    {
        # code...
    }

    /**
     * @param $sorting_type_name
     * @return null|string
     */
    public function isByValue($sorting_type_name = null)
    {
        $sorting_type_name = $sorting_type_name ? $sorting_type_name : $this->sortingType->name;

        return preg_match('/^by.+value/i', $sorting_type_name) ? true : false;
    }

    /**
     * @param
     * @return null|string
     */
    public function isByRegion($sorting_type_name = null)
    {
        $sorting_type_name = $sorting_type_name ? $sorting_type_name : $this->sortingType->name;

        return preg_match('/^geographical.+region/i', $sorting_type_name) ? true : false;
    }

    /**
     * @param $sorting_type_name
     * @return null|string
     */
    public function isByCity($sorting_type_name = null)
    {
        $sorting_type_name = $sorting_type_name ? $sorting_type_name : $this->sortingType->name;

        return preg_match('/^geographical.+town/i', $sorting_type_name) ? true : false;
    }

    /**
     * @param $sorting_type_name
     * @return null|string
     */

    public function isByTown($sorting_type_name = null)
    {
        $sorting_type_name = $sorting_type_name ? $sorting_type_name : $this->sortingType->name;

        return preg_match('/^geographical.+town/i', $sorting_type_name) ? true : false;
    }

    /**
     * @param $sorting_type_name
     * @return null|string
     */
    public function isByState($sorting_type_name = null)
    {
        $sorting_type_name = $sorting_type_name ? $sorting_type_name : $this->sortingType->name;

        return preg_match('/^geographical.+state/i', $sorting_type_name) ? true : false;
    }

    /**
     * @param $sorting_type_name
     * @return null|string
     */
    public function isByWeight($sorting_type_name = null)
    {
        $sorting_type_name = $sorting_type_name ? $sorting_type_name : $this->sortingType->name;

        return preg_match('/^particular.+weight/i', $sorting_type_name) ? true : false;
    }

    /**
     * @param $sorting_type_name
     * @return null|string
     */
    public function isByPostalOffice($sorting_type_name = null)
    {
        $sorting_type_name = $sorting_type_name ? $sorting_type_name : $this->sortingType->name;

        return preg_match('/^geographical.+postal.+office/i', $sorting_type_name) ? true : false;
    }

    /**
     * @param $sorting_type_name
     * @return null|string
     */
    public function isByCriteria($sorting_type_name = null)
    {
        $sorting_type_name = $sorting_type_name ? $sorting_type_name : $this->sortingType->name;

        return preg_match('/^particular.+criteria/i', $sorting_type_name) ? true : false;
    }

    public function getPresenterClass()
    {
        return SortingGateCriteriaPresenter::class;
    }
}
