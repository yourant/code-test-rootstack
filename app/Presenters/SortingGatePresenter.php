<?php namespace App\Presenters;

use App\Models\SortingGate;

/**
 * Class SortingPresenter
 * @property Sorting $resource
 */
class SortingGatePresenter extends BasePresenter
{

    /**
     * SortingGatePresenter constructor.
     * @param SortingGate $resource
     */
    public function __construct(SortingGate $resource)
    {
        $this->wrappedObject = $resource;
    }

    /**
     * @return string
     */
    public function sorting_name()
    {
        $o = $this->wrappedObject->sorting;
        return $o ? $o->name : '-';
    }

}