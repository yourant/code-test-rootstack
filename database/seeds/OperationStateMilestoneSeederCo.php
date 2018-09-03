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

class OperationStateMilestoneSeederCo extends Seeder
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
        $countryMexico = $this->countryRepository->getByCode('CO');

        // State Milestone
        $states = [
            ['name' => 'AMAZONAS', 'transit' => 2, 'distribution' => 6],
            ['name' => 'ANTIOQUIA', 'transit' => 2, 'distribution' => 6],
            ['name' => 'ARAUCA', 'transit' => 2, 'distribution' => 4],
            ['name' => 'ARCHIPIÉLAGO DE SAN ANDRÉS, PROVIDENCIA Y SANTA CATALINA', 'transit' => 2, 'distribution' => 6],
            ['name' => 'ATLÁNTICO', 'transit' => 2, 'distribution' => 3],
            ['name' => 'BOGOTÁ, D.C.', 'transit' => 2, 'distribution' => 3],
            ['name' => 'BOLÍVAR', 'transit' => 2, 'distribution' => 5],
            ['name' => 'BOYACÁ', 'transit' => 2, 'distribution' => 5],
            ['name' => 'CALDAS', 'transit' => 2, 'distribution' => 3],
            ['name' => 'CAQUETÁ', 'transit' => 2, 'distribution' => 6],
            ['name' => 'CASANARE', 'transit' => 2, 'distribution' => 5],
            ['name' => 'CAUCA', 'transit' => 2, 'distribution' => 4],
            ['name' => 'CESAR', 'transit' => 2, 'distribution' => 4],
            ['name' => 'CHOCÓ', 'transit' => 2, 'distribution' => 5],
            ['name' => 'CUNDINAMARCA', 'transit' => 2, 'distribution' => 4],
            ['name' => 'CÓRDOBA', 'transit' => 2, 'distribution' => 4],
            ['name' => 'GUAINÍA', 'transit' => 2, 'distribution' => 5],
            ['name' => 'GUAVIARE', 'transit' => 2, 'distribution' => 5],
            ['name' => 'HUILA', 'transit' => 2, 'distribution' => 4],
            ['name' => 'LA GUAJIRA', 'transit' => 2, 'distribution' => 4],
            ['name' => 'MAGDALENA', 'transit' => 2, 'distribution' => 4],
            ['name' => 'META', 'transit' => 2, 'distribution' => 6],
            ['name' => 'NARIÑO', 'transit' => 2, 'distribution' => 4],
            ['name' => 'NORTE DE SANTANDER', 'transit' => 2, 'distribution' => 4],
            ['name' => 'PUTUMAYO', 'transit' => 2, 'distribution' => 4],
            ['name' => 'QUINDIO', 'transit' => 2, 'distribution' => 3],
            ['name' => 'RISARALDA', 'transit' => 2, 'distribution' => 3],
            ['name' => 'SANTANDER', 'transit' => 2, 'distribution' => 4],
            ['name' => 'SUCRE', 'transit' => 2, 'distribution' => 3],
            ['name' => 'TOLIMA', 'transit' => 2, 'distribution' => 4],
            ['name' => 'VALLE DEL CAUCA', 'transit' => 2, 'distribution' => 4],
            ['name' => 'VAUPÉS', 'transit' => 2, 'distribution' => 5],
            ['name' => 'VICHADA', 'transit' => 2, 'distribution' => 6],
        ];

        $transit_milestone_ids = [35, 47];
        $last_mile_milestone_ids = [36, 48];

        foreach ($states as $k => $v) {
            if (!$s = $this->stateRepository->getByNameAndCountryId($v['name'], $countryMexico->id)) {
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
