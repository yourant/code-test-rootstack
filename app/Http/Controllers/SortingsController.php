<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSortingFormRequest;
use App\Models\Service;
use App\Models\Sorting;
use App\Repositories\ServiceRepository;
use App\Repositories\SortingGateRepository;
use App\Repositories\SortingRepository;
use App\Repositories\SortingTypeRepository;
use App\Services\Sortings\SortingCreationService;
use App\Services\Sortings\SortingGateCreationService;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;

/**
 * Class SortingsController
 * @package App\Http\Controllers
 */
class SortingsController extends Controller
{
    /** @var SortingRepository */
    protected $sortingRepository;

    /** @var SortingGateRepository */
    protected $sortingGateRepository;

    /** @var ServiceRepository */
    protected $serviceRepository;

    /** @var SortingCreationService */
    protected $sortingCreationService;

    /** @var SortingGateCreationService */
    protected $sortingGateCreationService;

    /** @var SortingTypeRepository */
    protected $sortingTypeRepository;

    public function __construct(
        SortingRepository $sortingRepository,
        SortingGateRepository $sortingGateRepository,
        ServiceRepository $serviceRepository,
        SortingCreationService $sortingCreationService,
        SortingGateCreationService $sortingGateCreationService,
        SortingTypeRepository $sortingTypeRepository
    ) {
        $this->sortingRepository = $sortingRepository;
        $this->sortingGateRepository = $sortingGateRepository;
        $this->serviceRepository = $serviceRepository;
        $this->sortingCreationService = $sortingCreationService;
        $this->sortingGateCreationService = $sortingGateCreationService;
        $this->sortingTypeRepository = $sortingTypeRepository;
    }

    public function index(Request $request)
    {
        $params = $request->all();
        $items = $this->sortingRepository->filter($params)
            ->with(['sortingTypes', 'service'])->paginate(20);

        return view('sortings.index', compact('params', 'items'))
            ->nest('filters', 'sortings.filters', compact('params', 'items'))
            ->nest('table', 'sortings.table', compact('params', 'items'));
    }

    public function create(Request $request)
    {
        $params = $request->all();

        $sorting = $this->sortingRepository->newInstance();

        $services = $this->serviceRepository->search()->get()->filter(function (Service $service) {
            return !$service->sorting;
        });

        $sortingTypes = $this->sortingTypeRepository->all();

        return view('sortings.create', compact('params', 'sorting', 'services', 'sortingTypes'));
    }

    public function store(CreateSortingFormRequest $request)
    {
        try {
            /** @var Service $service */
            $service = $this->serviceRepository->getById($request->get('service_id'));

            /** @var Sorting $sorting */
            $sorting = $this->sortingCreationService->create($request->get('name'), $service, array_keys($request->get('sorting_type_id')));

            return redirect()->route('sortings.index')->with('alert_success', 'Item created successfully!');
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function edit(Request $request, $sorting_id)
    {
        $params = $request->all();

        $sorting = $this->sortingRepository->getById($sorting_id);
        $sorting->load(['service', 'sortingTypes']);

        $sortingTypes = $this->sortingTypeRepository->filter()->get();

        return view('sortings.edit', compact('params', 'sorting', 'sortingTypes'));
    }

    public function update(CreateSortingFormRequest $request, $sorting_id)
    {
        /** @var Sorting $sorting */
        $sorting = $this->sortingRepository->getById($sorting_id);

        try {
            DB::beginTransaction();

            $this->sortingCreationService->update($sorting, $request->get('name'), array_keys($request->get('sorting_type_id')));

            DB::commit();

            return redirect()->route('sortings.index')->with('alert_success', 'Item updated successfully!');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('sortings.index')->withErrors($e->getMessage())->withInput();
        }
    }

    public function destroy($sorting_id)
    {
        $sorting = $this->sortingRepository->getById($sorting_id);
        $sorting->load(['sortingGates', 'service', 'sortingTypes']);

        $deleted_at = Carbon::now();
        $modified_by = current_user_id();

        try {
            DB::beginTransaction();

            $sorting->sortingGates->each(function ($gate) use ($deleted_at, $modified_by) {
                $this->sortingGateRepository->update($gate, ['deleted_at' => $deleted_at, 'modified_by' => $modified_by]);
            });

            $sorting->sortingTypes->each(function ($sortingType) use ($sorting, $deleted_at, $modified_by) {
                $this->sortingRepository->updateSortingType($sorting, $sortingType, ['deleted_at' => $deleted_at, 'modified_by' => $modified_by]);
            });

            if ($sorting->service) {
                $this->serviceRepository->update($sorting->service, ['sorting_id' => null]);
            }

            $this->sortingRepository->update($sorting, ['deleted_at' => $deleted_at, 'modified_by' => $modified_by]);

            DB::commit();

            return redirect()->back()->with('alert_success', 'Item deleted.');
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }
}
