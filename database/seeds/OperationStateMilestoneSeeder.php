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
use App\Repositories\AdminLevel1Repository;
use Illuminate\Database\Seeder;

class OperationStateMilestoneSeeder extends Seeder
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

    /** @var AdminLevel1Repository */
    protected $adminLevel1Repository;

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
        AdminLevel1Repository $adminLevel1Repository,
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
        $this->adminLevel1Repository = $adminLevel1Repository;
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
        $countryMexico = $this->countryRepository->getByCode('MX');

        // State Milestone
        $states = [
            ['name' => 'Ciudad de México', 'transit' => 1, 'distribution' => 2],
            ['name' => 'México', 'transit' => 2, 'distribution' => 2],
            ['name' => 'Tlaxcala', 'transit' => 4, 'distribution' => 3],
            ['name' => 'Hidalgo', 'transit' => 4, 'distribution' => 3],
            ['name' => 'Guerrero', 'transit' => 4, 'distribution' => 3],
            ['name' => 'Michoacán de Ocampo', 'transit' => 4, 'distribution' => 3],
            ['name' => 'Guanajuato', 'transit' => 4, 'distribution' => 3],
            ['name' => 'Morelos', 'transit' => 4, 'distribution' => 3],
            ['name' => 'Puebla', 'transit' => 4, 'distribution' => 3],
            ['name' => 'Querétaro', 'transit' => 4, 'distribution' => 3],
            ['name' => 'Oaxaca', 'transit' => 6, 'distribution' => 4],
            ['name' => 'Veracruz de Ignacio de la Llave', 'transit' => 6, 'distribution' => 4],
            ['name' => 'Colima', 'transit' => 6, 'distribution' => 4],
            ['name' => 'Jalisco', 'transit' => 6, 'distribution' => 4],
            ['name' => 'Nayarit', 'transit' => 6, 'distribution' => 4],
            ['name' => 'Aguascalientes', 'transit' => 6, 'distribution' => 4],
            ['name' => 'San Luis Potosí', 'transit' => 6, 'distribution' => 4],
            ['name' => 'Tamaulipas', 'transit' => 6, 'distribution' => 4],
            ['name' => 'Nuevo León', 'transit' => 6, 'distribution' => 4],
            ['name' => 'Zacatecas', 'transit' => 6, 'distribution' => 4],
            ['name' => 'Tabasco', 'transit' => 6, 'distribution' => 4],
            ['name' => 'Chiapas', 'transit' => 6, 'distribution' => 4],
            ['name' => 'Sinaloa', 'transit' => 8, 'distribution' => 6],
            ['name' => 'Durango', 'transit' => 8, 'distribution' => 6],
            ['name' => 'Coahuila de Zaragoza', 'transit' => 8, 'distribution' => 6],
            ['name' => 'Sonora', 'transit' => 8, 'distribution' => 6],
            ['name' => 'Campeche', 'transit' => 8, 'distribution' => 6],
            ['name' => 'Yucatán', 'transit' => 8, 'distribution' => 6],
            ['name' => 'Quintana Roo', 'transit' => 8, 'distribution' => 6],
            ['name' => 'Baja California', 'transit' => 1, 'distribution' => 8],
            ['name' => 'Baja California Sur', 'transit' => 11, 'distribution' => 8],
        ];

        $transit_milestone_ids = [11, 23];
        $last_mile_milestone_ids = [12, 24];

        foreach ($states as $k => $v) {
            if (!$s = $this->adminLevel1Repository->getByNameAndCountryId($v['name'], $countryMexico->id)) {
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
    }
}
