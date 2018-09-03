<?php namespace App\Presenters;

use App\Models\SortingGateCriteria;

/**
 * Class SortingGateCriteriaPresenter
 * @property SortingGateCriteria $resource
 */
class SortingGateCriteriaPresenter extends BasePresenter
{

    /**
     * SortingGateCriteriaPresenter constructor.
     * @param SortingGateCriteria $resource
     */
    public function __construct(SortingGateCriteria $resource)
    {
        $this->wrappedObject = $resource;
    }

    /**
     * @return bool
     */
    public function isByValue()
    {
        $o = $this->wrappedObject->sortingType;
        return $o ? $o->isByValue() : false;
    }

    /**
     * @return bool
     */
    public function isByRegion()
    {
        $o = $this->wrappedObject->sortingType;
        return $o ? $o->isByRegion() : false;
    }

    /**
     * @return bool
     */
    public function isByTown()
    {
        $o = $this->wrappedObject->sortingType;
        return $o ? $o->isByTown() : false;
    }

    /**
     * @return bool
     */
    public function isByState()
    {
        $o = $this->wrappedObject->sortingType;
        return $o ? $o->isByState() : false;
    }

    /**
     * @return bool
     */
    public function isByWeight()
    {
        $o = $this->wrappedObject->sortingType;
        return $o ? $o->isByWeight() : false;
    }

    /**
     * @return bool
     */
    public function isByPostalOffice()
    {
        $o = $this->wrappedObject->sortingType;
        return $o ? $o->isByPostalOffice() : false;
    }

    /**
     * @return bool
     */
    public function isByCriteria()
    {
        $o = $this->wrappedObject->sortingType;
        return $o ? $o->isByCriteria() : false;
    }

    /**
     * @return array
     */
    /*public function list_regions_names()
    {
        return $this->wrappedObject->getRegionsNames();
    }*/

    /**
     * @return array
     */
    /*public function list_states_names()
    {
        return $this->wrappedObject->getStatesNames();
    }*/

    /*public function list_towns_names()
    {
        return $this->wrappedObject->getTownsNames();
    }*/

    /*public function list_postal_offices_names()
    {
        return $this->wrappedObject->getPostalOfficeNames();
    }*/

    public function list_values_names()
    {
        return $this->wrappedObject->getValues();
    }

    public function list_weight_names()
    {
        return $this->wrappedObject->getWeights();
    }

    public function list_criterias_names()
    {
        return $this->wrappedObject->getCriterias();
    }

    public function range_weight()
    {
        $o = $this->wrappedObject->before_than . ' - '. $this->wrappedObject->after_than;
        return $o;
    }

    public function range_values()
    {
        $o = $this->wrappedObject->before_than . ' - '. $this->wrappedObject->after_than;
        return $o;
    }

}
