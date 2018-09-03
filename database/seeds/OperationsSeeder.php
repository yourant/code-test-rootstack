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

class OperationsSeeder extends Seeder
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
        $countryMexico = $this->countryRepository->getByCode('MX');

        try {
            DB::beginTransaction();

            // Mexico Registered Panel
            $panel = $this->panelRepository->create([
                'country_id'   => $countryMexico->id,
                'service_type' => 'registered',
                'name'         => 'Correos de México'
            ]);

            // Segment Types
            $segmentTypePickPack = $this->segmentTypeRepository->firstOrCreate(['key' => 'pick_pack', 'name' => 'Pick & Pack']);
            $segmentTypeWarehouse = $this->segmentTypeRepository->firstOrCreate(['key' => 'warehouse', 'name' => 'Warehouse']);
            $segmentTypeAirFreight = $this->segmentTypeRepository->firstOrCreate(['key' => 'air_freight', 'name' => 'Air Freight']);
            $segmentTypeDistribution = $this->segmentTypeRepository->firstOrCreate(['key' => 'distribution', 'name' => 'Distribution']);

            // Segment 1
            $segment1 = $this->panelRepository->addSegment($panel, [
                'segment_type_id' => $segmentTypePickPack->id,
                'name'            => 'Segment #1',
                'position'        => 1
            ]);

            // Segment 2
            $segment2 = $this->panelRepository->addSegment($panel, [
                'segment_type_id' => $segmentTypeWarehouse->id,
                'name'            => 'Segment #2',
                'position'        => 2
            ]);

            // Segment 3
            $segment3 = $this->panelRepository->addSegment($panel, [
                'segment_type_id' => $segmentTypeAirFreight->id,
                'name'            => 'Segment #3',
                'position'        => 3
            ]);

            // Segment 4
            $segment4 = $this->panelRepository->addSegment($panel, [
                'segment_type_id' => $segmentTypeDistribution->id,
                'name'            => 'Segment #4',
                'position'        => 4
            ]);

            // Milestone 1 - Label Generated
            $m11 = $this->segmentRepository->addMilestone($segment1, [
                'name'      => 'Label generated',
                'days'      => 10,
                'warning1'  => 3,
                'warning2'  => 1,
                'critical1' => 1,
                'critical2' => 3,
                'critical3' => 5,
                'critical4' => 10,
                'position'  => 1
            ]);
            foreach ($this->checkpointCodeRepository->search(['classification_id' => 23])->get() as $cc) {
                $this->milestoneRepository->addCheckpointCode($m11, $cc);
            }

            // Milestone 2 - Posted at origin
            $m21 = $this->segmentRepository->addMilestone($segment2, [
                'name'      => 'Posted at origin',
                'days'      => 2,
                'warning1'  => 2,
                'warning2'  => 1,
                'critical1' => 1,
                'critical2' => 3,
                'critical3' => 5,
                'critical4' => 10,
                'position'  => 1
            ]);
            foreach ($this->checkpointCodeRepository->search(['id' => [4727, 4902]])->get() as $cc) {
                $this->milestoneRepository->addCheckpointCode($m21, $cc);
            }

            // Milestone 2 - Dispatch assigned
            $m22 = $this->segmentRepository->addMilestone($segment2, [
                'name'      => 'Dispatch assigned',
                'days'      => 2,
                'warning1'  => 2,
                'warning2'  => 1,
                'critical1' => 1,
                'critical2' => 3,
                'critical3' => 5,
                'critical4' => 10,
                'position'  => 2
            ]);
            foreach ($this->checkpointCodeRepository->search(['id' => [4728, 4729, 4730, 4903]])->get() as $cc) {
                $this->milestoneRepository->addCheckpointCode($m22, $cc);
            }

            // Milestone 2 - In transit to airport
            $m23 = $this->segmentRepository->addMilestone($segment2, [
                'name'      => 'In transit to airport',
                'days'      => 2,
                'warning1'  => 2,
                'warning2'  => 1,
                'critical1' => 1,
                'critical2' => 3,
                'critical3' => 5,
                'critical4' => 10,
                'position'  => 3
            ]);
            foreach ($this->checkpointCodeRepository->search(['id' => [4381, 4502, 4598, 4632, 4633, 4644]])->get() as $cc) {
                $this->milestoneRepository->addCheckpointCode($m23, $cc);
            }

            // Milestone 3 - Booking confirmed
            $m31 = $this->segmentRepository->addMilestone($segment3, [
                'name'      => 'Booking confirmed',
                'days'      => 3,
                'warning1'  => 2,
                'warning2'  => 1,
                'critical1' => 1,
                'critical2' => 3,
                'critical3' => 5,
                'critical4' => 10,
                'position'  => 1
            ]);

            // Milestone 3 - Received at the airline
            $m32 = $this->segmentRepository->addMilestone($segment3, [
                'name'      => 'Received at the airline',
                'days'      => 2,
                'warning1'  => 2,
                'warning2'  => 1,
                'critical1' => 1,
                'critical2' => 3,
                'critical3' => 5,
                'critical4' => 10,
                'position'  => 2
            ]);

            // Milestone 3 - Departed to destination country
            $m33 = $this->segmentRepository->addMilestone($segment3, [
                'name'      => 'Departed to destination country',
                'days'      => 2,
                'warning1'  => 2,
                'warning2'  => 1,
                'critical1' => 1,
                'critical2' => 3,
                'critical3' => 5,
                'critical4' => 10,
                'position'  => 3
            ]);

            // Milestone 3 - In transit to destination country
            $m34 = $this->segmentRepository->addMilestone($segment3, [
                'name'      => 'In transit to destination country',
                'days'      => 2,
                'warning1'  => 2,
                'warning2'  => 1,
                'critical1' => 1,
                'critical2' => 3,
                'critical3' => 5,
                'critical4' => 10,
                'position'  => 4
            ]);
            foreach ($this->checkpointCodeRepository->search(['classification_id' => [2, 4]])->get() as $cc) {
                $this->milestoneRepository->addCheckpointCode($m34, $cc);
            }

            // Milestone 4 - Arrived at destination country
            $m41 = $this->segmentRepository->addMilestone($segment4, [
                'name'      => 'Arrived at destination country',
                'days'      => 2,
                'warning1'  => 2,
                'warning2'  => 1,
                'critical1' => 1,
                'critical2' => 3,
                'critical3' => 5,
                'critical4' => 10,
                'position'  => 1
            ]);
            foreach ($this->checkpointCodeRepository->search(['classification_id' => 6])->get() as $cc) {
                $this->milestoneRepository->addCheckpointCode($m41, $cc);
            }

            // Milestone 4 - Customs
            $m42 = $this->segmentRepository->addMilestone($segment4, [
                'name'      => 'Customs',
                'days'      => 2,
                'warning1'  => 2,
                'warning2'  => 1,
                'critical1' => 1,
                'critical2' => 3,
                'critical3' => 5,
                'critical4' => 10,
                'position'  => 2
            ]);
            foreach ($this->checkpointCodeRepository->search(['classification_id' => [9, 10, 11, 12]])->get() as $cc) {
                $this->milestoneRepository->addCheckpointCode($m42, $cc);
            }

            // Milestone 4 - In transit to delivery center
            $m43 = $this->segmentRepository->addMilestone($segment4, [
                'name'      => 'In transit to delivery center',
                'days'      => 3,
                'warning1'  => 2,
                'warning2'  => 1,
                'critical1' => 1,
                'critical2' => 3,
                'critical3' => 5,
                'critical4' => 10,
                'position'  => 3
            ]);
            foreach ($this->checkpointCodeRepository->search(['classification_id' => [14, 21]])->get() as $cc) {
                $this->milestoneRepository->addCheckpointCode($m43, $cc);
            }

            // Milestone 4 - Last mile
            $m44 = $this->segmentRepository->addMilestone($segment4, [
                'name'      => 'Last mile',
                'days'      => 3,
                'warning1'  => 2,
                'warning2'  => 1,
                'critical1' => 1,
                'critical2' => 3,
                'critical3' => 5,
                'critical4' => 10,
                'position'  => 4
            ]);
            foreach ($this->checkpointCodeRepository->search(['classification_id' => [15, 17, 18, 19, 20, 25]])->get() as $cc) {
                $this->milestoneRepository->addCheckpointCode($m44, $cc);
            }

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
            foreach ($states as $k => $v) {
                if (!$s = $this->stateRepository->getByNameAndCountryId($v['name'], $countryMexico->id)) {
                    throw new Exception($v['name']);
                }
                $this->segmentRepository->addStateMilestone($segment4, [
                    'state_id'     => $s->id,
                    'transit'      => $v['transit'],
                    'distribution' => $v['distribution'],
                    'warning1'     => 2,
                    'warning2'     => 1,
                    'critical1'    => 1,
                    'critical2'    => 3,
                    'critical3'    => 5,
                    'critical4'    => 10,
                ]);
            }

            // Frequencies
            $this->frequencyRepository->firstOrCreate(['key' => 'hourly', 'value' => 'Hourly']);
            $this->frequencyRepository->firstOrCreate(['key' => 'daily', 'value' => 'Daily']);
            $this->frequencyRepository->firstOrCreate(['key' => 'weekly', 'value' => 'Weekly']);
            $this->frequencyRepository->firstOrCreate(['key' => 'monthly', 'value' => 'Monthly']);
            $this->frequencyRepository->firstOrCreate(['key' => 'quarterly', 'value' => 'Quarterly']);
            $this->frequencyRepository->firstOrCreate(['key' => 'yearly', 'value' => 'Yearly']);

            // Holidays
            $this->holidayRepository->create(['country_id' => $countryMexico->id, 'date' => '2017-01-01', 'description' => 'Año Nuevo - día festivo oficial en México']);
            $this->holidayRepository->create(['country_id' => $countryMexico->id, 'date' => '2017-02-06', 'description' => 'Día de la Constitución Mexicana - día festivo oficial en México']);
            $this->holidayRepository->create(['country_id' => $countryMexico->id, 'date' => '2017-03-20', 'description' => 'Natalicio de Benito Juárez - día festivo oficial en México']);
            $this->holidayRepository->create(['country_id' => $countryMexico->id, 'date' => '2017-04-13', 'description' => 'Jueves Santo - día festivo regional']);
            $this->holidayRepository->create(['country_id' => $countryMexico->id, 'date' => '2017-04-14', 'description' => 'Viernes Santo - día festivo regional']);
            $this->holidayRepository->create(['country_id' => $countryMexico->id, 'date' => '2017-04-16', 'description' => 'Domingo de Resurrección - día festivo oficial en México']);
            $this->holidayRepository->create(['country_id' => $countryMexico->id, 'date' => '2017-05-01', 'description' => 'Día del Trabajo - día festivo oficial en México']);
            $this->holidayRepository->create(['country_id' => $countryMexico->id, 'date' => '2017-05-05', 'description' => 'Batalla de Puebla - Viernes usual']);
            $this->holidayRepository->create(['country_id' => $countryMexico->id, 'date' => '2017-05-10', 'description' => 'Día de las Madres - Miércoles usual']);
            $this->holidayRepository->create(['country_id' => $countryMexico->id, 'date' => '2017-09-16', 'description' => 'Día de la Independencia - día festivo oficial en México']);
            $this->holidayRepository->create(['country_id' => $countryMexico->id, 'date' => '2017-11-02', 'description' => 'Día de los Muertos - Jueves usual']);
            $this->holidayRepository->create(['country_id' => $countryMexico->id, 'date' => '2017-11-20', 'description' => 'Revolución Mexicana - día festivo oficial en México']);
            $this->holidayRepository->create(['country_id' => $countryMexico->id, 'date' => '2017-12-12', 'description' => 'Día de la Virgen de Guadalupe - Martes usual']);
            $this->holidayRepository->create(['country_id' => $countryMexico->id, 'date' => '2017-12-24', 'description' => 'Noche buena - Domingo usual']);
            $this->holidayRepository->create(['country_id' => $countryMexico->id, 'date' => '2017-12-25', 'description' => 'Día de Navidad - día festivo oficial en México']);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getTraceAsString() . PHP_EOL;
            throw $e;
        }
    }
}
