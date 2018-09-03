<?php

namespace Tests\Unit;

use App\Http\Controllers\SortingsController;
use App\Repositories\ServiceRepository;
use App\Repositories\SortingRepository;
use App\Services\Sortings\SortingCreationService;
use App\Services\Sortings\SortingGateCreationService;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserCanCreateAndDeleteSortingTest extends TestCase
{
    use DatabaseTransactions, SoftDeletes;

    /**
     * @var SortingCreationService
     */
    private $sortingCreationService;
    /**
     * @var SortingGateCreationService
     */
    private $sortingGateCreationService;
    /**
     * @var ServiceRepository
     */
    private $serviceRepository;
    /**
     * @var ServiceRepository
     */
    private $sortingRepository;
    /**
     * @var SortingsController
     */
    private $sortingController;
    /**
     * @var SortingsController
     */
    private $userRepository;


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
        $this->sortingCreationService = app(SortingCreationService::class);
        $this->sortingGateCreationService = app(SortingGateCreationService::class);
        $this->serviceRepository = app(ServiceRepository::class);
        $this->sortingRepository = app(SortingRepository::class);
        $this->sortingController = app(SortingsController::class);
    }

    /**
     * Test to create a new sorting
     * @test
     */
    public function UserCanCreateSorting()
    {
        // Prepare
        $new_sorting = [
            'name' => 'sortingUnit',
            'sorting_types' => [ 0 => "1"]
        ];

        // Action
        $sorting = $this->sortingCreationService->insert($new_sorting);
        $this->sortingGateCreationService->createDefaultGate($sorting);

        // Validations
        $this->assertDatabaseHas('sortings', ['name' => 'sortingUnit']);

    }

    /**
     * Test to assing services
     * @test
     */
    public function UserCanAssingService()
    {
        // Prepare
        $service_id = 1;
        $new_sorting = [
            'name' => 'sortingUnit',
            'sorting_types' => [ 0 => "1"]
        ];
        $service = $this->serviceRepository->getById($service_id);
        $sorting = $this->sortingCreationService->insert($new_sorting);
        $this->sortingGateCreationService->createDefaultGate($sorting);

        // Action
        $this->serviceRepository->assignSorting($service, $sorting);

        //Validation
        $this->assertDatabaseHas('services', ['sorting_id' => $sorting->id]);

    }

    /**
     * Test to unassing service
     * @test
     */
    public function UserCanUnassingService()
    {
        // Prepare
        $service_id = 1;
        $new_sorting = [
            'name' => 'sortingUnit',
            'sorting_types' => [ 0 => "1"]
        ];
        $service = $this->serviceRepository->getById($service_id);
        $sorting = $this->sortingCreationService->insert($new_sorting);
        $this->sortingGateCreationService->createDefaultGate($sorting);
        $this->serviceRepository->assignSorting($service, $sorting);

        // Action
        $this->serviceRepository->unassignSorting($sorting->service);

        //Validation
        $this->assertDatabaseMissing('services', ['sorting_id' => $sorting->id]);
    }

    /**
     * Test to delete a sorting
     * @test
     */
    public function UserCanDeleteSorting()
    {
        // Prepare
        $new_sorting = [
            'name' => 'sortingUnit',
            'sorting_types' => [ 0 => "1"]
        ];
        $service = $this->serviceRepository->getById(1);
        $sorting = $this->sortingCreationService->insert($new_sorting);
        $this->sortingGateCreationService->createDefaultGate($sorting);
        $sorting = $this->sortingRepository->getByName('sortingUnit');

        // Action
        $this->sortingController->destroy($sorting->id);

        //Validation
        $this->assertSoftDeleted('sortings', ['name' => 'sortingUnit']);

    }

}
