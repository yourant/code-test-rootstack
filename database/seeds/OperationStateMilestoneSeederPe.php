<?php

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

class OperationStateMilestoneSeederPe extends Seeder
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
    public function run()
    {
        $country = $this->countryRepository->getByCode('PE');

        // State Milestone
        $states = [
            ['name' => 'AMAZONAS', 'transit' => 4, 'distribution' => 4],
            ['name' => 'ANCASH', 'transit' => 3, 'distribution' => 4],
            ['name' => 'APURIMAC', 'transit' => 4, 'distribution' => 3],
            ['name' => 'AREQUIPA', 'transit' => 4, 'distribution' => 5],
            ['name' => 'AYACUCHO', 'transit' => 3, 'distribution' => 5],
            ['name' => 'CAJAMARCA', 'transit' => 4, 'distribution' => 4],
            ['name' => 'CALLAO', 'transit' => 2, 'distribution' => 2],
            ['name' => 'CUSCO', 'transit' => 4, 'distribution' => 5],
            ['name' => 'HUANCAVELICA', 'transit' => 3, 'distribution' => 3],
            ['name' => 'HUANUCO', 'transit' => 3, 'distribution' => 4],
            ['name' => 'ICA', 'transit' => 3, 'distribution' => 3],
            ['name' => 'JUNIN', 'transit' => 3, 'distribution' => 3],
            ['name' => 'LA LIBERTAD', 'transit' => 4, 'distribution' => 3],
            ['name' => 'LAMBAYEQUE', 'transit' => 4, 'distribution' => 3],
            ['name' => 'LIMA', 'transit' => 2, 'distribution' => 3],
            ['name' => 'LORETO', 'transit' => 5, 'distribution' => 6],
            ['name' => 'MADRE DE DIOS', 'transit' => 5, 'distribution' => 6],
            ['name' => 'MOQUEGUA', 'transit' => 5, 'distribution' => 3],
            ['name' => 'PASCO', 'transit' => 3, 'distribution' => 3],
            ['name' => 'PIURA', 'transit' => 5, 'distribution' => 3],
            ['name' => 'PUNO', 'transit' => 5, 'distribution' => 4],
            ['name' => 'SAN MARTIN', 'transit' => 4, 'distribution' => 4],
            ['name' => 'TACNA', 'transit' => 5, 'distribution' => 3],
            ['name' => 'TUMBES', 'transit' => 5, 'distribution' => 3],
            ['name' => 'UCAYALI', 'transit' => 4, 'distribution' => 6]
        ];

        $transit_milestone_ids = [83];
        $last_mile_milestone_ids = [84];

        foreach ($states as $k => $v) {
            if (!$s = $this->stateRepository->getByNameAndCountryId($v['name'], $country->id)) {
                throw new Exception($v['name']);
            }

            foreach ($transit_milestone_ids as $id) {
                $this->stateMilestoneRepository->create([
                    'state_id'     => $s->id,
                    'milestone_id' => $id,
                    'days'         => $v['transit'],
                    'warning1'     => 2,
                    'warning2'     => 1,
                    'critical1'    => 1,
                    'critical2'    => 3,
                    'critical3'    => 5,
                    'critical4'    => 10,
                ]);
            }

            foreach ($last_mile_milestone_ids as $id) {
                $this->stateMilestoneRepository->create([
                    'state_id'     => $s->id,
                    'milestone_id' => $id,
                    'days'         => $v['distribution'],
                    'warning1'     => 2,
                    'warning2'     => 1,
                    'critical1'    => 1,
                    'critical2'    => 3,
                    'critical3'    => 5,
                    'critical4'    => 10,
                ]);
            }
        }

        foreach ($transit_milestone_ids as $id) {
            $this->stateMilestoneRepository->create([
                'state_id'     => null,
                'milestone_id' => $id,
                'days'         => 8,
                'warning1'     => 2,
                'warning2'     => 1,
                'critical1'    => 1,
                'critical2'    => 3,
                'critical3'    => 5,
                'critical4'    => 10,
            ]);
        }

        foreach ($last_mile_milestone_ids as $id) {
            $this->stateMilestoneRepository->create([
                'state_id'     => null,
                'milestone_id' => $id,
                'days'         => 8,
                'warning1'     => 2,
                'warning2'     => 1,
                'critical1'    => 1,
                'critical2'    => 3,
                'critical3'    => 5,
                'critical4'    => 10,
            ]);
        }
    }
}
