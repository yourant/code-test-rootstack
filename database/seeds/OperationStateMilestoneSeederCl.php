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

class OperationStateMilestoneSeederCl extends Seeder
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
        $country = $this->countryRepository->getByCode('CL');

        // State Milestone
        $states = [
            ['name' => 'Antofagasta', 'transit' => 3, 'distribution' => 4],
            ['name' => 'Antártica Chilena', 'transit' => 4, 'distribution' => 7],
            ['name' => 'Arauco', 'transit' => 2, 'distribution' => 2],
            ['name' => 'Arica', 'transit' => 3, 'distribution' => 3],
            ['name' => 'Aysén', 'transit' => 3, 'distribution' => 4],
            ['name' => 'Biobío', 'transit' => 2, 'distribution' => 3],
            ['name' => 'Cachapoal', 'transit' => 2, 'distribution' => 3],
            ['name' => 'Capitán Prat', 'transit' => 3, 'distribution' => 4],
            ['name' => 'Cardenal Caro', 'transit' => 2, 'distribution' => 3],
            ['name' => 'Cauquenes', 'transit' => 2, 'distribution' => 2],
            ['name' => 'Cautín', 'transit' => 2, 'distribution' => 3],
            ['name' => 'Chacabuco', 'transit' => 2, 'distribution' => 2],
            ['name' => 'Chañaral', 'transit' => 3, 'distribution' => 3],
            ['name' => 'Chiloé', 'transit' => 3, 'distribution' => 3],
            ['name' => 'Choapa', 'transit' => 2, 'distribution' => 3],
            ['name' => 'Colchagua', 'transit' => 2, 'distribution' => 3],
            ['name' => 'Concepción', 'transit' => 2, 'distribution' => 3],
            ['name' => 'Copiapó', 'transit' => 3, 'distribution' => 3],
            ['name' => 'Cordillera', 'transit' => 2, 'distribution' => 3],
            ['name' => 'Coyhaique', 'transit' => 3, 'distribution' => 2],
            ['name' => 'Curicó', 'transit' => 2, 'distribution' => 3],
            ['name' => 'El Loa', 'transit' => 3, 'distribution' => 3],
            ['name' => 'Elqui', 'transit' => 2, 'distribution' => 3],
            ['name' => 'General Carrera', 'transit' => 3, 'distribution' => 3],
            ['name' => 'Huasco', 'transit' => 3, 'distribution' => 3],
            ['name' => 'Iquique', 'transit' => 3, 'distribution' => 3],
            ['name' => 'Isla de Pascua', 'transit' => 3, 'distribution' => 3],
            ['name' => 'Limarí', 'transit' => 2, 'distribution' => 3],
            ['name' => 'Linares', 'transit' => 2, 'distribution' => 3],
            ['name' => 'Llanquihue', 'transit' => 3, 'distribution' => 3],
            ['name' => 'Los Andes', 'transit' => 3, 'distribution' => 3],
            ['name' => 'Magallanes', 'transit' => 4, 'distribution' => 3],
            ['name' => 'Maipo', 'transit' => 2, 'distribution' => 2],
            ['name' => 'Malleco', 'transit' => 2, 'distribution' => 3],
            ['name' => 'Marga Marga', 'transit' => 3, 'distribution' => 2],
            ['name' => 'Melipilla', 'transit' => 2, 'distribution' => 3],
            ['name' => 'Osorno', 'transit' => 3, 'distribution' => 3],
            ['name' => 'Palena', 'transit' => 3, 'distribution' => 3],
            ['name' => 'Parinacota', 'transit' => 3, 'distribution' => 3],
            ['name' => 'Petorca', 'transit' => 3, 'distribution' => 3],
            ['name' => 'Quillota', 'transit' => 3, 'distribution' => 2],
            ['name' => 'Ranco', 'transit' => 3, 'distribution' => 3],
            ['name' => 'San Antonio', 'transit' => 3, 'distribution' => 2],
            ['name' => 'San Felipe de Aconcagua', 'transit' => 3, 'distribution' => 2],
            ['name' => 'Santiago', 'transit' => 2, 'distribution' => 2],
            ['name' => 'Talagante', 'transit' => 2, 'distribution' => 2],
            ['name' => 'Talca', 'transit' => 2, 'distribution' => 3],
            ['name' => 'Tamarugal', 'transit' => 3, 'distribution' => 4],
            ['name' => 'Tierra del Fuego', 'transit' => 4, 'distribution' => 3],
            ['name' => 'Tocopilla', 'transit' => 3, 'distribution' => 3],
            ['name' => 'Valdivia', 'transit' => 3, 'distribution' => 3],
            ['name' => 'Valparaíso', 'transit' => 3, 'distribution' => 3],
            ['name' => 'Ñuble', 'transit' => 2, 'distribution' => 3],
            ['name' => 'Última Esperanza', 'transit' => 4, 'distribution' => 6]
        ];

        $transit_milestone_ids = [59, 71];
        $last_mile_milestone_ids = [60, 72];

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
