<?php

use App\CheckpointCode;
use App\Models\Operation\Milestone;
use App\Models\Operation\Panel;
use App\Models\Operation\Segment;
use App\Repositories\CheckpointCodeRepository;
use App\Repositories\CountryRepository;
use App\Repositories\Operation\FrequencyRepository;
use App\Repositories\Operation\HolidayRepository;
use App\Repositories\Operation\MilestoneRepository;
use App\Repositories\Operation\PanelRepository;
use App\Repositories\Operation\SegmentRepository;
use App\Repositories\Operation\SegmentTypeRepository;
use App\Repositories\Operation\StateMilestoneRepository;
use App\Repositories\AdminLevel1Repository as StateRepository;
use Illuminate\Database\Seeder;

class OperationsSeederV2 extends Seeder
{
    /** @var PanelRepository */
    protected $panelRepository;

    /** @var CountryRepository */
    protected $countryRepository;

    /** @var SegmentRepository */
    protected $segmentRepository;

    /** @var SegmentTypeRepository */
    protected $segmentTypeRepository;

    /** @var MilestoneRepository */
    protected $milestoneRepository;

    /** @var StateMilestoneRepository */
    protected $stateMilestoneRepository;

    /** @var StateRepository */
    protected $stateRepository;

    /** @var CheckpointCodeRepository */
    protected $checkpointCodeRepository;

    /** @var FrequencyRepository */
    protected $frequencyRepository;

    /** @var HolidayRepository */
    protected $holidayRepository;

    public function __construct(
        PanelRepository $panelRepository,
        CountryRepository $countryRepository,
        SegmentRepository $segmentRepository,
        SegmentTypeRepository $segmentTypeRepository,
        MilestoneRepository $milestoneRepository,
        StateMilestoneRepository $stateMilestoneRepository,
        StateRepository $stateRepository,
        CheckpointCodeRepository $checkpointCodeRepository,
        FrequencyRepository $frequencyRepository,
        HolidayRepository $holidayRepository
    ) {
        $this->panelRepository = $panelRepository;
        $this->countryRepository = $countryRepository;
        $this->segmentRepository = $segmentRepository;
        $this->segmentTypeRepository = $segmentTypeRepository;
        $this->milestoneRepository = $milestoneRepository;
        $this->stateMilestoneRepository = $stateMilestoneRepository;
        $this->stateRepository = $stateRepository;
        $this->checkpointCodeRepository = $checkpointCodeRepository;
        $this->frequencyRepository = $frequencyRepository;
        $this->holidayRepository = $holidayRepository;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public
    function run()
    {
        try {
            DB::beginTransaction();

            // Mexico Registered Panel (reference)
            /** @var Panel $panel */
            $panel = $this->panelRepository->getById(2);
//            $panel->load('segments.milestones.checkpointCodes');

            $panels = [
                ['country_id' => 48, 'service_type' => 'registered', 'name' => '4-72'],
                ['country_id' => 48, 'service_type' => 'priority', 'name' => '4-72'],
                ['country_id' => 44, 'service_type' => 'registered', 'name' => 'Correos de Chile'],
                ['country_id' => 44, 'service_type' => 'registered', 'name' => 'Blue Express'],
                ['country_id' => 172, 'service_type' => 'registered', 'name' => 'Serpost'],
                ['country_id' => 31, 'service_type' => 'registered', 'name' => 'Correios de Brasil'],
                ['country_id' => 63, 'service_type' => 'registered', 'name' => 'Correos del Ecuador'],
            ];

            foreach ($panels as $data) {
                /** @var Panel $p */
                $p = $panel->replicate();
                $p->fill($data);
                $p->push();

                $panel->load('segments');

                /** @var Segment $segment */
                foreach ($panel->segments as $segment) {
                    /** @var Segment $s */
                    $s = $segment->replicate();
                    $s->panel_id = $p->id;
                    $s->save();

                    $segment->load('milestones');

                    /** @var Milestone $milestone */
                    foreach ($segment->milestones as $milestone) {
                        /** @var Milestone $m */
                        $m = $milestone->replicate();
                        $m->segment_id = $s->id;
                        $m->save();

                        $milestone->load('checkpointCodes');

                        if ($milestone->name != 'In transit to delivery center' and $milestone->name != 'Last mile')
                        /** @var CheckpointCode $checkpointCode */
                        foreach ($milestone->checkpointCodes as $checkpointCode) {
                            /** @var CheckpointCode $m */
                            $m->checkpointCodes()->attach($checkpointCode->id);
                        }
                    }
                }

//                $p->save();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getTraceAsString() . PHP_EOL;
            throw $e;
        }
    }
}
