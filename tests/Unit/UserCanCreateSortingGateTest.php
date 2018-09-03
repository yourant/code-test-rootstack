<?php

namespace Tests\Unit;

use App\Repositories\ServiceRepository;
use App\Repositories\SortingRepository;
use App\Repositories\SortingTypeRepository;
use App\Services\Sortings\SortingCreationService;
use App\Services\Sortings\SortingGateCreationService;
use App\Repositories\CountryRepository;
use App\Repositories\AdminLevel1Repository;
use App\Repositories\SortingGateRepository;
use App\Http\Controllers\SortingsController;
use App\Http\Controllers\SortingGatesController;
use Tests\TestCase;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserCanCreateSortingGateTest extends TestCase
{
    use DatabaseTransactions, SoftDeletes;
    /**
     * @var ServiceRepository
    */
    private $serviceRepository;

    /**
     * @var SortingGateRepository
    */
    private $sortingGateRepository;
    /**
     * @var SortingRepository
    */
    private $sortingRepository;
    /**
     * @var SortingTypeRepository
     */
    private $sortingTypeRepository;
    /**
     * @var SortingCreationService
     */
    private $sortingCreationService;
    /**
     * @var SortingGateCreationService
     */
    private $sortingGateCreationService;

   	/** @var CountryRepository */
    protected $countryRepository;

    /**
    * @var AdminLevel1Repository
    */
    private $adminLevel1Repository;

    /**
     * @var SortingsController
     */
    private $sortingController;

    /**
     * @var SortingGatesController
     */
    private $sortingGatesController;


	 /**
     * UserCanCreateAndDeleteSortingTest constructor.
     * @param null $name
     * @param array $data
     * @param string $dataName
     * @param SortingCreationService $sortingCreationService
     * @param SortingGateCreationService $sortingGateCreationService
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->serviceRepository = app(ServiceRepository::class);
        $this->sortingRepository = app(SortingRepository::class);
        $this->sortingTypeRepository = app(SortingTypeRepository::class);
        $this->sortingCreationService = app(SortingCreationService::class);
        $this->sortingGateCreationService = app(SortingGateCreationService::class);
        $this->countryRepository = app(CountryRepository::class);
        $this->adminLevel1Repository = app(AdminLevel1Repository::class);
        $this->sortingGateRepository = app(SortingGateRepository::class);
        $this->sortingController = app(SortingsController::class);
        $this->sortingGatesController = app(SortingGatesController::class);
    }



    /**
     * A basic test example.
     *@test
     * @return void
     */
    public function UserCanCreateSortingGate()
    {
        $sortingTypeState = $this->sortingTypeRepository->filter(['is_like' => 'geographical_state'])->first();

        $input_sorting = [
    		'name' => 'SortingUnitGeographicalState',
    		'sorting_types' => $sortingTypeState->id
    	];

   		$sorting = $this->sortingCreationService->insert($input_sorting);

   		$countryMexico = $this->countryRepository->getByCode('MX');

   		$stateMexicoIds = $this->adminLevel1Repository->search(['country_id' => $countryMexico->id])->take(6)->pluck('id');

   		$input_gates = [
   			'code' => 'NA{$countryMexico->code}EX02',
   			'state_id' => $stateMexicoIds->toArray()
   		];

   		$gate = $this->sortingGateCreationService->insert($sorting, $input_gates);


   		$this->assertDatabaseHas('sortings', [
   			'name' => 'SortingUnitGeographicalState'
   		]);

   		$this->assertDatabaseHas('sorting_gates', [
   			'sorting_id' => $sorting->id,
   			'gate_code' => $gate->gate_code
   		]);

    }

}
