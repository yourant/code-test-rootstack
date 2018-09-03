<?php namespace App\Presenters;

use App\Models\Sorting;

/**
 * Class SortingPresenter
 * @property Sorting $resource
 */
class SortingPresenter extends BasePresenter
{
    /**
     * SortingPresenter constructor.
     * @param Sorting $resource
     */
    public function __construct(Sorting $resource)
    {
        $this->wrappedObject = $resource;
    }

    public function country_name()
    {
        $o = $this->wrappedObject->getServiceCountryName();

        return $o ?: '-';
    }

    public function country_code()
    {
        $o = $this->wrappedObject->getServiceCountryCode();

        return $o ?: '-';
    }

    public function service_name()
    {
        $o = $this->wrappedObject->getServiceName();

        return $o ?: '-';
    }

    public function service_code()
    {
        $o = $this->wrappedObject->getServiceCode();

        return $o ?: '-';
    }

    public function continent_abbreviation()
    {
        $o = $this->wrappedObject->getServiceCountryContinentAbbreviation();

        return $o ?: '-';
    }

    public function sorting_types()
    {
        return collect($this->wrappedObject->sortingTypes)->map(function ($sortingType) {
            return $sortingType->get_sorting_type;
        })->implode(',');
    }
}
