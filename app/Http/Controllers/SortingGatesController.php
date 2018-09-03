<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Transformers\SortingGateTransformer;
use App\Http\Requests\CreateSortingGateRequest;
use App\Http\Requests\EditSortingGateRequest;
use App\Repositories\CountryRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\SortingGateCriteriaRepository;
use App\Repositories\SortingGateRepository;
use App\Repositories\SortingRepository;
use App\Services\Sortings\SortingGateCreationService;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;

/**
 * Class SortingGatesController
 * @package App\Http\Controllers
 */
class SortingGatesController extends Controller
{
    /** @var \App\Repositories\SortingRepository */
    protected $sortingRepository;

    /** @var \App\Repositories\SortingGateRepository */
    protected $sortingGateRepository;

    /** @var \App\Repositories\SortingGateCriteriaRepository */
    protected $sortingGateCriteriaRepository;

    /** @var \App\Services\Sortings\SortingGateCreationService */
    protected $sortingGateCreationService;

    /** @var \App\Repositories\CountryRepository */
    protected $countryRepository;

    /** @var ServiceRepository */
    protected $serviceRepository;

    /** @var SortingGateTransformer */
    protected $sortingGateTransformer;

    /**
     * SortingGatesController constructor.
     * @param SortingRepository $sortingRepository
     * @param ServiceRepository $serviceRepository
     * @param SortingGateCreationService $sortingGateCreationService
     * @param CountryRepository $countryRepository
     * @param SortingGateRepository $sortingGateRepository
     * @param SortingGateCriteriaRepository $sortingGateCriteriaRepository
     * @param SortingGateTransformer $sortingGateTransformer
     */
    public function __construct(
        SortingRepository $sortingRepository,
        ServiceRepository $serviceRepository,
        SortingGateCreationService $sortingGateCreationService,
        CountryRepository $countryRepository,
        SortingGateRepository $sortingGateRepository,
        SortingGateCriteriaRepository $sortingGateCriteriaRepository,
        SortingGateTransformer $sortingGateTransformer
    ) {
        $this->sortingRepository = $sortingRepository;
        $this->serviceRepository = $serviceRepository;
        $this->countryRepository = $countryRepository;
        $this->sortingGateRepository = $sortingGateRepository;
        $this->sortingGateCriteriaRepository = $sortingGateCriteriaRepository;
        $this->sortingGateCreationService = $sortingGateCreationService;
        $this->sortingGateTransformer = $sortingGateTransformer;
    }

    /**
     * @param Request $request
     * @param $sorting_id
     * @return $this|\Illuminate\View\View
     */
    public function index(Request $request, $sorting_id)
    {
        $params = $request->all();
        $sorting = $this->sortingRepository->getById($sorting_id);
        $sorting_gates = $this->sortingGateRepository
            ->filter(['sorting_id' => $sorting_id])
            ->with(['sortingGateCriterias'])
            ->orderBy('number')
            ->get();

        $sorting_gates = $this->sortingGateTransformer->transform($sorting_gates);

        return view('sortings.gates.index', compact('sorting', 'sorting_gates', 'params'));
    }

    /**
     * @param $sorting_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create($sorting_id)
    {
        $sorting = $this->sortingRepository->getById($sorting_id);
        $sorting->load(['sortingTypes']);
        $countries = $this->countryRepository->all();

        return view('sortings.gates.create', compact('sorting', 'countries'));
    }

    /**
     * @param $sorting_id
     * @param $gate_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($sorting_id, $gate_id)
    {
        $sorting = $this->sortingRepository->getById($sorting_id);
        $sortingGate = $this->sortingGateRepository->getById($gate_id);

        return view('sortings.gates.edit', compact('sorting', 'sortingGate'));
    }

    /**
     * @param $sorting_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateSortingGateRequest $request, $sorting_id)
    {
        $input = $request->all();
        $sorting = $this->sortingRepository->getById($sorting_id);
        $gate = $this->sortingGateCreationService->insert($sorting, $input);

        return redirect()->route('sortings.gates.index', ['sorting' => $sorting->id])
            ->with('alert_success', 'Item created successfully!');
    }

    /**
     * @param $sorting_id
     * @param $gate_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function update(EditSortingGateRequest $request, $sorting_id, $gate_id)
    {
        $sorting = $this->sortingRepository->getById($sorting_id);

        $sortingGate = $this->sortingGateRepository->getById($gate_id);

        $this->sortingGateRepository->update($sortingGate, [
            'gate_code'   => $request->input('code'),
            'gate_number' => $request->input('number')
        ]);

        return redirect()->route('sortings.gates.index', ['sorting' => $sorting->id])
            ->with('alert_success', 'Gate update successfully!');
    }

    /**
     * @param $sorting_id
     * @param $gate_id
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function destroy($sorting_id, $gate_id)
    {
        $this->sortingRepository->getById($sorting_id);
        $gate = $this->sortingGateRepository->getById($gate_id);
        $gate->load(['sortingGateCriterias']);

        $deleted_at = Carbon::now();
        $user = current_user();
        try {
            DB::beginTransaction();
            $gate->sortingGateCriterias->each(function ($criteria) use ($deleted_at, $user) {
                $this->sortingGateCriteriaRepository->update($criteria, ['deleted_at' => $deleted_at, 'modified_by' => $user->id]);
            });

            $this->sortingGateRepository->update($gate, ['deleted_at' => $deleted_at, 'modified_by' => $user->id]);

            DB::commit();

            return redirect()->back()->with('alert_success', 'Item deleted.');
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->back()->withErrors($e->getMessage());
        }
    }
}
