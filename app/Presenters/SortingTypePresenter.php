<?php namespace App\Presenters;

use App\Models\SortingType;

/**
 * Class SortingTypePresenter
 * @property SortingType $resource
 */
class SortingTypePresenter extends BasePresenter
{

    /**
     * SortingTypePresenter constructor.
     * @param SortingType $resource
     */
    public function __construct(SortingType $resource)
    {
        $this->wrappedObject = $resource;
    }

    /**
     * @return null|string
     */
    public function get_sorting_type()
    {
        $o = $this->wrappedObject->getSortinTypeNameAttribute();
        return $o;
    }

    /**
     * @param $value
     * @return null|string
     */
    public function is_by_value($value)
    {
        $o = $this->wrappedObject->isByValue($value);
        return $o;
    }

    /**
     * @param $value
     * @return null|string
     */
    public function is_by_region($value)
    {
        $o = $this->wrappedObject->isByRegion($value);
        return $o;
    }

    /**
     * @param $value
     * @return null|string
     */
    public function is_by_town($value)
    {
        $o = $this->wrappedObject->isByTown($value);
        return $o;
    }

    /**
     * @param $value
     * @return null|string
     */
    public function is_by_state($value)
    {
        $o = $this->wrappedObject->isByState($value);
        return $o;
    }

    /**
     * @param $value
     * @return null|string
     */
    public function is_by_weight($value)
    {
        $o = $this->wrappedObject->isByWeight($value);
        return $o;
    }

    /**
     * @param $value
     * @return null|string
     */
    public function is_by_postal_office($value)
    {
        $o = $this->wrappedObject->isByPostalOffice($value);
        return $o;
    }

    /**
     * @param $value
     * @return null|string
     */
    public function is_by_criteria($value)
    {
        $o = $this->wrappedObject->isByCriteria($value);
        return $o;
    }
}