<?php namespace App\Http\ViewComposers;

use App\Repositories\CountryRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\SortingTypeRepository;
use Carbon\Carbon;

/**
 * Class SortingFiltersComposer
 * @package App\Http\ViewComposers
 */
class SortingFiltersComposer
{

    /**
     * @var CountryRepository
     */
    protected $countryRepository;

    /**
     * @var ServiceRepository
     */
    protected $serviceRepository;

    /**
     * @var SortingTypeRepository
     */
    protected $sortingTypeRepository;

    /**
     * SortingFiltersComposer constructor.
     * @param CountryRepository $countryRepository
     * @param ServiceRepository $serviceRepository
     * @param SortingTypeRepository $sortingTypeRepository
     */
    public function __construct(
        CountryRepository $countryRepository,
        ServiceRepository $serviceRepository,
        SortingTypeRepository $sortingTypeRepository
    )
    {
        $this->countryRepository = $countryRepository;
        $this->serviceRepository = $serviceRepository;
        $this->sortingTypeRepository = $sortingTypeRepository;
    }

    /**
     * @param $view
     */
    public function compose($view)
    {
        $params = request()->all();
        $countries = $this->countryRepository->search()->get();

        $services = $this->serviceRepository->filter()->orderBy('name')->get();

        $sortingTypes = $this->sortingTypeRepository->filter()->orderBy('name')->get();

        $view->with('params', $params);
        $view->with('countries', $countries);
        $view->with('services', $services);
        $view->with('sortingTypes', $sortingTypes);
    }
}