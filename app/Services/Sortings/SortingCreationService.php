<?php

namespace App\Services\Sortings;

use App\Models\Service;
use App\Models\Sorting;
use App\Repositories\ServiceRepository;
use App\Repositories\SortingRepository;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Class SortingCreationService
 * @package App\Services\Sortings
 */
class SortingCreationService
{
    /** @var SortingRepository */
    protected $sortingRepository;

    /** @var ServiceRepository */
    protected $serviceRepository;

    /** @var SortingGateCreationService */
    protected $sortingGateCreationService;

    public function __construct(SortingRepository $sortingRepository, ServiceRepository $serviceRepository, SortingGateCreationService $sortingGateCreationService)
    {
        $this->sortingRepository = $sortingRepository;
        $this->serviceRepository = $serviceRepository;
        $this->sortingGateCreationService = $sortingGateCreationService;
    }

    public function create($name, Service $service, array $sorting_type_ids)
    {
        try {
            DB::beginTransaction();

            $user = current_user();

            /** @var Sorting $sorting */
            $sorting = $this->sortingRepository->create([
                'name'        => $name,
                'modified_by' => $user ? $user->id : null
            ]);

            // Assign sorting to service
            $this->serviceRepository->assignSorting($service, $sorting);

            // Sync sorting types
            $this->sortingRepository->syncSortingTypes($sorting, $sorting_type_ids);

            // Create default gate
            $defaultGate = $this->sortingGateCreationService->createDefaultGate($sorting);

            DB::commit();

            return $sorting;
        } catch (Exception $e) {
            DB::rollback();
            logger($e->getMessage());
            logger($e->getTraceAsString());

            throw $e;
        }
    }

    public function update(Sorting $sorting, $name, array $sorting_type_ids)
    {
        try {
            DB::beginTransaction();

            $this->sortingRepository->update($sorting, [
                'name'        => $name,
                'modified_by' => current_user_id()
            ]);

            // Sync sorting types
            $this->sortingRepository->syncSortingTypes($sorting, $sorting_type_ids);

            DB::commit();

            return $sorting;
        } catch (Exception $e) {
            DB::rollback();
            logger($e->getMessage());
            logger($e->getTraceAsString());

            throw $e;
        }
    }
}