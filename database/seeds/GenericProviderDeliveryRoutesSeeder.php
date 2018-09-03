<?php
use App\Models\CheckpointCode;
use App\Models\DeliveryRoute;
use App\Repositories\CheckpointCodeRepository;
use App\Repositories\CountryRepository;
use App\Repositories\DeliveryRouteRepository;
use App\Repositories\LegRepository;
use App\Repositories\LocationRepository;
use App\Repositories\ProviderRepository;
use App\Repositories\ProviderServiceRepository;
use App\Repositories\ProviderServiceTypeRepository;
use App\Repositories\TimezoneRepository;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use App\Repositories\EventCodeRepository;


class GenericProviderDeliveryRoutesSeeder extends Seeder
{

	/**
     * @var LocationRepository
    */
    protected $locationRepository;

    /**
     * @var DeliveryRouteRepository
    */
    protected $deliveryRouteRepository;

    /**
     * @var ProviderRepository
    */
    protected $providerRepository;

    /**
     * @var ProviderServiceTypeRepository
    */
    protected $providerServiceTypeRepository;

    /**
     * @var ProviderServiceRepository
    */
    protected $providerServiceRepository;

    /**
     * @var LegRepository
    */
    protected $legRepository;

    /**
     * @var CountryRepository
     */
    protected $countryRepository;
	
    /**
     * @var CheckpointCodeRepository
     */
    protected $checkpointCodeRepository;

    /**
     * @var EventCodeRepository
     */
    protected $eventCodeRepository;

	/**
	 * @var TimezoneRepository
	 */
	protected $timezoneRepository;


	public function __construct(LocationRepository $locationRepository,
		DeliveryRouteRepository $deliveryRouteRepository,
		ProviderRepository $providerRepository,
		ProviderServiceTypeRepository $providerServiceTypeRepository,
		ProviderServiceRepository $providerServiceRepository,
		LegRepository $legRepository,
        CountryRepository $countryRepository,
        CheckpointCodeRepository $checkpointCodeRepository,
        EventCodeRepository $eventCodeRepository,
		TimezoneRepository $timezoneRepository
	)
	{
		$this->locationRepository = $locationRepository;
		$this->deliveryRouteRepository = $deliveryRouteRepository;
		$this->providerRepository = $providerRepository;
		$this->providerServiceTypeRepository = $providerServiceTypeRepository;
		$this->providerServiceRepository = $providerServiceRepository;
		$this->legRepository = $legRepository;
		$this->countryRepository = $countryRepository;
		$this->checkpointCodeRepository = $checkpointCodeRepository;
		$this->eventCodeRepository = $eventCodeRepository;
		$this->timezoneRepository = $timezoneRepository;
	}

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		try {

			DB::beginTransaction();

			/**
			 * COUNTRIES
			 */

			$china = $this->countryRepository->getByCode('CN');
			$mexico = $this->countryRepository->getByCode('MX');
			$unitedStates = $this->countryRepository->getByCode('US');
			$chile = $this->countryRepository->getByCode('CL');
			$colombia = $this->countryRepository->getByCode('CO');
			$ecuador = $this->countryRepository->getByCode('EC');
			$peru = $this->countryRepository->getByCode('PE');
			$unitedKingdom = $this->countryRepository->getByCode('GB');
			$brazil = $this->countryRepository->getByCode('BR');

			$houndProvider = $this->providerRepository->firstOrCreate([
				'name' => 'Hound Express',
				'code' => 'PR3874',
				'country_id' => $mexico->id,
				'timezone_id' => 15,
				'generic' => false,
				'parent_id' => null,
			]);

			$providerServiceTypeDistribution = $this->providerServiceTypeRepository
				->getByKey('distribution');

			$houndExpressProviderService = $this->providerServiceRepository->firstOrCreate([
				'provider_id' => $houndProvider->id,
				'provider_service_type_id' => $providerServiceTypeDistribution->id,
				//'first_checkpoint_code_id' => 10182,
				//'last_checkpoint_code_id' => 10183,
				'name' => 'Customs Mexico Express',
				'transit_days' => 1
			]);

			$qualityPostProvider = $this->providerRepository->firstOrCreate([
				'name' => 'Quality Post',
				'code' => 'PR3873',
				'country_id' => $mexico->id,
				'timezone_id' => 15,
				'generic' => false,
				'parent_id' => null,
			]);

			$qualityPostProviderService = $this->providerServiceRepository->firstOrCreate([
				'provider_id' => $qualityPostProvider->id,
				'provider_service_type_id' => $providerServiceTypeDistribution->id,
				//'first_checkpoint_code_id' => 10184,
				'last_checkpoint_code_id' => null,
				'name' => 'Mexico Express E-Commerce',
				'transit_days' => 6
			]);

			$tranexcoProvider = $this->providerRepository->create([
				'name' => 'Tranexco',
				'code' => 'PR8524',
				'country_id' => $colombia->id,
				'timezone_id' => 18,
				'generic' => false,
				'parent_id' => null,
			]);

			/**
			 * Add Checkpoint Codes to new Provider Tranexco
			 */

			$checkpointCodeTranexco1 = $this->checkpointCodeRepository->create([
				'key' => 'TXC-002',
				'description' => 'Salida de aduana',
				'provider_id' => $tranexcoProvider->id,
				'classification_id' => 10
			]);

			$eventCode = $this->eventCodeRepository->search(['key' => 'ML-402'])->first();

			$this->eventCodeRepository->setCheckpointCodes($eventCode, collect($checkpointCodeTranexco1->id));

			$checkpointCodeTranexco2 = $this->checkpointCodeRepository->create([
				'key' => 'TXC-001',
				'description' => 'Recivido en aduana',
				'provider_id' => $tranexcoProvider->id,
				'classification_id' => 21
			]);

			$eventCode = $this->eventCodeRepository->search(['key' => 'ML-400'])->first();

			$this->eventCodeRepository->setCheckpointCodes($eventCode, collect($checkpointCodeTranexco2->id));

			$checkpointCodeTranexco3 = $this->checkpointCodeRepository->create([
				'description' => 'Admitido para distribución',
				'provider_id' => $tranexcoProvider->id,
				'classification_id' => 14
			]);

			$eventCode = $this->eventCodeRepository->search(['key' => 'ML-500'])->first();

			$this->eventCodeRepository->setCheckpointCodes($eventCode, collect($checkpointCodeTranexco3->id));

			$providerServiceTypeDistribution = $this->providerServiceTypeRepository
				->getByKey('distribution');

			$tranexcoLiberationProviderService = $this->providerServiceRepository->create([
				'provider_id' => $tranexcoProvider->id,
				'provider_service_type_id' => $providerServiceTypeDistribution->id,
				'first_checkpoint_code_id' => $checkpointCodeTranexco2->id,
				'last_checkpoint_code_id' => $checkpointCodeTranexco1->id,
				'code' => null,
				'name' => 'Tranexco Liberation',
				'transit_days' => 1,
				'provider_code' => null
			]);

			$tranexcoDistributionProviderService = $this->providerServiceRepository->create([
				'provider_id' => $tranexcoProvider->id,
				'provider_service_type_id' => $providerServiceTypeDistribution->id,
				'first_checkpoint_code_id' => $checkpointCodeTranexco3->id,
				'last_checkpoint_code_id' => null,
				'code' => null,
				'name' => 'Tranexco Distribution',
				'transit_days' => 7,
				'provider_code' => null
			]);

			$warehouseProviderGeneric = $this->providerRepository->getByCode('PR5573');

			$this->providerRepository->update($warehouseProviderGeneric, [
				'country_id' => null,
				'timezone_id' => null,
				'generic' => true,
				'parent_id' => null,
			]);

			/**
			 * Add Checkpoint Codes for Warehouse Generic
			 */

			$firstCheckpointCodeWarehouse = $this->checkpointCodeRepository->search([
				'provider_id' => $warehouseProviderGeneric->id,
				'key' => 'WAR-01',
				'description' => 'Received at warehouse'
			])->first();

			$lastCheckpointCodeWarehouse = $this->checkpointCodeRepository->search([
				'provider_id' => $warehouseProviderGeneric->id,
				'key' => 'SIN-8',
				'description' => 'Freight delivered to forwarder'
			])->first();

			$providerServiceTypeWarehouse = $this->providerServiceTypeRepository
				->getByKey('warehouse');

			$warehouseChina1ProviderService = $this->providerServiceRepository->create([
				'provider_id' => $warehouseProviderGeneric->id,
				'provider_service_type_id' => $providerServiceTypeWarehouse->id,
				'first_checkpoint_code_id' => $firstCheckpointCodeWarehouse->id,
				'last_checkpoint_code_id' => $lastCheckpointCodeWarehouse->id,
				'code' => null,
				'name' => 'Warehouse 1',
				'transit_days' => 5,
				'provider_code' => null
			]);

			$warehouseChina2ProviderService = $this->providerServiceRepository->create([
				'provider_id' => $warehouseProviderGeneric->id,
				'provider_service_type_id' => $providerServiceTypeWarehouse->id,
				'first_checkpoint_code_id' => $firstCheckpointCodeWarehouse->id,
				'last_checkpoint_code_id' => $lastCheckpointCodeWarehouse->id,
				'code' => null,
				'name' => 'Warehouse 2',
				'transit_days' => 4,
				'provider_code' => null
			]);

			$warehouseChina3ProviderService = $this->providerServiceRepository->create([
				'provider_id' => $warehouseProviderGeneric->id,
				'provider_service_type_id' => $providerServiceTypeWarehouse->id,
				'first_checkpoint_code_id' => $firstCheckpointCodeWarehouse->id,
				'last_checkpoint_code_id' => $lastCheckpointCodeWarehouse->id,
				'code' => null,
				'name' => 'Warehouse 3',
				'transit_days' => 2,
				'provider_code' => null
			]);

			$transitProviderGeneric = $this->providerRepository->getByCode('PR0538'); // Global Match
			$this->providerRepository->update($transitProviderGeneric, [
				'name' => 'Transit',
				'country_id' => null,
				'timezone_id' => null,
				'generic' => true,
				'parent_id' => null,
			]);

			$transitProviderCheckpointCodes = [
				['checkpoint_code_id' => 4382, 'classification_id' => 2, 'description' => 'Freight checked in at departure airline (HGH)'],
				['checkpoint_code_id' => 4383, 'classification_id' => 2, 'description' => 'Freight departed to destination (TPE)'],
				['checkpoint_code_id' => 4384, 'classification_id' => 2, 'description' => 'Freight departed to destination (LHR)'],
				['checkpoint_code_id' => 4385, 'classification_id' => 2, 'description' => 'Freight arrived at destination (TPE)'],
				['checkpoint_code_id' => 4386, 'classification_id' => 2, 'description' => 'Freight arrived at destination (LHR)'],
				['checkpoint_code_id' => 4387, 'classification_id' => 2, 'description' => 'Consignment actual received after break down'],
				['checkpoint_code_id' => 4397, 'classification_id' => 2, 'description' => 'Freight checked in at departure airline (HKG)'],
				['checkpoint_code_id' => 4398, 'classification_id' => 2, 'description' => 'Freight departed to destination (ICN)'],
				['checkpoint_code_id' => 4399, 'classification_id' => 2, 'description' => 'Freight arrived at destination (ICN)'],
				['checkpoint_code_id' => 4400, 'classification_id' => 2, 'description' => 'Freight departed to destination (DWC)'],
				['checkpoint_code_id' => 4401, 'classification_id' => 2, 'description' => 'Freight arrived at destination (DWC)'],
				['checkpoint_code_id' => 4402, 'classification_id' => 2, 'description' => 'Freight departed to destination (DXB)'],
				['checkpoint_code_id' => 4403, 'classification_id' => 2, 'description' => 'Freight arrived at destination (DXB)'],
				['checkpoint_code_id' => 4404, 'classification_id' => 2, 'description' => 'Freight departed to destination (STN)'],
				['checkpoint_code_id' => 4405, 'classification_id' => 2, 'description' => 'Freight arrived at destination (STN)'],
				['checkpoint_code_id' => 4388, 'classification_id' => 2, 'description' => 'Freight delivered to forwarder'],
				['checkpoint_code_id' => 4613, 'classification_id' => 2, 'description' => 'Freight checked in at departure airline (PVG)'],
				['checkpoint_code_id' => 4647, 'classification_id' => 2, 'description' => 'Freight departed to destination (BKK)'],
				['checkpoint_code_id' => 4648, 'classification_id' => 2, 'description' => 'Freight arrived at destination (BKK)'],
				['checkpoint_code_id' => 4651, 'classification_id' => 2, 'description' => 'Freight departed to destination'],
				['checkpoint_code_id' => 4667, 'classification_id' => 2, 'description' => 'Freight departed to destination (GLA)'],
				['checkpoint_code_id' => 4668, 'classification_id' => 2, 'description' => 'Freight arrived at destination (GLA)'],
				['checkpoint_code_id' => 4681, 'classification_id' => 2, 'description' => 'Freight arrived at destination (HKT)'],
				['checkpoint_code_id' => 4683, 'classification_id' => 2, 'description' => 'Freight departed to destination (HKT)'],
				['checkpoint_code_id' => 4686, 'classification_id' => 2, 'description' => 'Freight departed to destination (DOH)'],
				['checkpoint_code_id' => 4691, 'classification_id' => 2, 'description' => 'Freight departed to destination (LGW)'],
				['checkpoint_code_id' => 4692, 'classification_id' => 2, 'description' => 'Freight arrived at destination (LGW)'],
				['checkpoint_code_id' => 4700, 'classification_id' => 2, 'description' => 'Freight departed to destination (BHX)'],
				['checkpoint_code_id' => 4701, 'classification_id' => 2, 'description' => 'Freight arrived at destination (BHX)'],
				['checkpoint_code_id' => 4750, 'classification_id' => 2, 'description' => 'Freight departed to destination (HKG)'],
				['checkpoint_code_id' => 4778, 'classification_id' => 2, 'description' => 'Freight checked in at departure airline'],
				['checkpoint_code_id' => 4780, 'classification_id' => 2, 'description' => 'Freight departed to destination (KUL)'],
				['checkpoint_code_id' => 4828, 'classification_id' => 2, 'description' => 'Freight confirmed on flight'],
				['checkpoint_code_id' => 4829, 'classification_id' => 2, 'description' => 'tracker.Embarque arribado del vuelo'],
				['checkpoint_code_id' => 4830, 'classification_id' => 2, 'description' => 'Freight arrived at destination (MEX)'],
				['checkpoint_code_id' => 4831, 'classification_id' => 6, 'description' => 'Arrived to destination country (MEX)'],
				['checkpoint_code_id' => 4867, 'classification_id' => 2, 'description' => 'Freight arrived at destination (SIN)'],
				['checkpoint_code_id' => 4906, 'classification_id' => 2, 'description' => 'Freight checked in at departure airline (CMB)'],
				['checkpoint_code_id' => 4948, 'classification_id' => 2, 'description' => 'Freight departed to destination (MEL)'],
				['checkpoint_code_id' => 4949, 'classification_id' => 2, 'description' => 'Freight arrived at destination (MEL)'],
				['checkpoint_code_id' => 4950, 'classification_id' => 2, 'description' => 'Freight departed to destination (SYD)'],
				['checkpoint_code_id' => 4951, 'classification_id' => 2, 'description' => 'Freight arrived at destination (SYD)'],
				['checkpoint_code_id' => 4954, 'classification_id' => 2, 'description' => 'Freight departed to destination (SCL)'],
				['checkpoint_code_id' => 4956, 'classification_id' => 2, 'description' => 'Freight arrived at destination (SCL)'],
				['checkpoint_code_id' => 4958, 'classification_id' => 2, 'description' => 'Freight arrived at destination (AMS)'],
				['checkpoint_code_id' => 4961, 'classification_id' => 6, 'description' => 'Arrived at destination country (SCL)'],
				['checkpoint_code_id' => 4963, 'classification_id' => 2, 'description' => 'Freight arrived at destination (KTM)'],
				['checkpoint_code_id' => 4966, 'classification_id' => 2, 'description' => 'Freight departed to destination (BOM)'],
				['checkpoint_code_id' => 4962, 'classification_id' => 2, 'description' => 'Freight departed to destination (KTM)'],
				['checkpoint_code_id' => 4978, 'classification_id' => 2, 'description' => 'Freight arrived at destination (JFK)'],
				['checkpoint_code_id' => 4982, 'classification_id' => 2, 'description' => 'Freight checked in at departure airline (CGO)'],
				['checkpoint_code_id' => 5019, 'classification_id' => 25, 'description' => 'DELAYED/ NATURAL DISASTER at DESTINATION COUNTRY'],
				['checkpoint_code_id' => 5000, 'classification_id' => 2, 'description' => 'Freight arrived at destination (ZAZ)'],
				['checkpoint_code_id' => 4999, 'classification_id' => 2, 'description' => 'Freight departed to destination (ZAZ)'],
				['checkpoint_code_id' => 5054, 'classification_id' => 2, 'description' => 'Freight arrived at destination (MAD)'],
				['checkpoint_code_id' => 5042, 'classification_id' => 2, 'description' => 'Freight departed to destination (AUH)'],
				['checkpoint_code_id' => 5053, 'classification_id' => 2, 'description' => 'Freight departed to destination (MAD)'],
				['checkpoint_code_id' => 5128, 'classification_id' => 25, 'description' => 'Lost in Transit'],
				['checkpoint_code_id' => 5142, 'classification_id' => 2, 'description' => 'Freight departed to destination (FRA)'],
				['checkpoint_code_id' => 5143, 'classification_id' => 2, 'description' => 'Freight arrived at destination (FRA)'],
				['checkpoint_code_id' => 5131, 'classification_id' => 2, 'description' => 'Freight arrived at destination (NCL)'],
				['checkpoint_code_id' => 5081, 'classification_id' => 2, 'description' => 'Freight departed to destination (BRU)'],
				['checkpoint_code_id' => 5130, 'classification_id' => 2, 'description' => 'Freight departed to destination (NCL)'],
				['checkpoint_code_id' => 5141, 'classification_id' => 2, 'description' => 'Freight arrived at destination (BOG)'],
				['checkpoint_code_id' => 5190, 'classification_id' => 2, 'description' => 'Freight departed to destination (RUH)'],
				['checkpoint_code_id' => 5191, 'classification_id' => 2, 'description' => 'Freight arrived at destination (RUH)'],
				['checkpoint_code_id' => 5192, 'classification_id' => 2, 'description' => 'Freight checked in at departure airline (RUH)'],
				['checkpoint_code_id' => 5154, 'classification_id' => 2, 'description' => 'Freight departed to destination (BNE)'],
				['checkpoint_code_id' => 5221, 'classification_id' => 2, 'description' => 'Freight booked on flight'],
				['checkpoint_code_id' => 5242, 'classification_id' => 2, 'description' => 'Freight arrived at destination (MIA)'],
				['checkpoint_code_id' => 5252, 'classification_id' => 2, 'description' => 'Freight arrived at destination'],
				['checkpoint_code_id' => 5258, 'classification_id' => 24, 'description' => 'Freight arrived at destination (LAX)'],
				['checkpoint_code_id' => 5261, 'classification_id' => 2, 'description' => 'Freight arrived at destination (DFW)'],
				['checkpoint_code_id' => 5043, 'classification_id' => 2, 'description' => 'Freight arrived at destination (AUH)'],
				['checkpoint_code_id' => 5155, 'classification_id' => 2, 'description' => 'Freight arrived at destination (BNE)'],
				['checkpoint_code_id' => 4967, 'classification_id' => 2, 'description' => 'Freight arrived at destination (BOM)'],
				['checkpoint_code_id' => 5082, 'classification_id' => 2, 'description' => 'Freight arrived at destination (BRU)'],
				['checkpoint_code_id' => 4983, 'classification_id' => 2, 'description' => 'Freight arrived at destination (HKG)'],
				['checkpoint_code_id' => 5259, 'classification_id' => 2, 'description' => 'Freight arrived at destination (YYZ)'],
				['checkpoint_code_id' => 10223, 'classification_id' => 2, 'description' => 'Freight arrived at destination (GRU)'],
				['checkpoint_code_id' => 10229, 'classification_id' => 6, 'description' => 'Arrived at destination (BOG)'],

			];

			$transit_key = 1;
			foreach ($transitProviderCheckpointCodes as $transitProviderCheckpointCode) {
				/** @var CheckpointCode $cc */
				$cc = $this->checkpointCodeRepository->getById($transitProviderCheckpointCode['checkpoint_code_id']);

				if (!$cc) {
					continue;
				}

				$key = sprintf("%03d", $transit_key);
				$key = "TRA-{$key}";

				$new_checkpoint_code = $this->checkpointCodeRepository->create([
					'provider_id' => $transitProviderGeneric->id,
					'classification_id' => $transitProviderCheckpointCode['classification_id'],
					'description' => $transitProviderCheckpointCode['description'],
					'key' => $key
				]);

				$transit_key++;

				$event_code_of_checkpoint_code = $cc->getEventCode();

				if ($event_code_of_checkpoint_code) {
					$this->eventCodeRepository->addCheckpointCodes($event_code_of_checkpoint_code, collect($new_checkpoint_code->id));
				}
			}

			$providerServiceTypeTransit = $this->providerServiceTypeRepository
				->getByKey('transit');

			// Checkpoints Codes for Transit
			$firstCheckpointCode = $this->checkpointCodeRepository->create([
				'provider_id' => $transitProviderGeneric->id,
				'classification_id' => '2', //departed_to_destination_country
				'description' => 'Freight booked on flight'
			]);
			$event_code = $this->eventCodeRepository->search(['key' => 'ML-300'])->first();
			$this->eventCodeRepository->setCheckpointCodes(
				$event_code,
				collect($firstCheckpointCode->id
				));

			$lineaAereaMexLastCheckpoint = $this->checkpointCodeRepository->search([
				'provider_id' => $transitProviderGeneric->id,
				'description' => 'Arrived to destination country (MEX)'
			])->first();

			$lineaAereaDirectMexProviderService = $this->providerServiceRepository->create([
				'provider_id' => $transitProviderGeneric->id,
				'provider_service_type_id' => $providerServiceTypeTransit->id,
				'first_checkpoint_code_id' => $firstCheckpointCode->id,
				'last_checkpoint_code_id' => $lineaAereaMexLastCheckpoint->id,
				'code' => null,
				'name' => 'Linea Aerea HKG-MEX',
				'transit_days' => 2,
				'provider_code' => null
			]);

			$lineaAereaHkgLhrLastCheckpoint = $this->checkpointCodeRepository->search([
				'provider_id' => $transitProviderGeneric->id,
				'description' => 'Freight arrived at destination (LHR)'
			])->first();

			$lineaAereaHkgLhrProviderService = $this->providerServiceRepository->create([
				'provider_id' => $transitProviderGeneric->id,
				'provider_service_type_id' => $providerServiceTypeTransit->id,
				'first_checkpoint_code_id' => $firstCheckpointCode->id,
				'last_checkpoint_code_id' => $lineaAereaHkgLhrLastCheckpoint->id,
				'code' => null,
				'name' => 'Linea Aerea HKG-LHR',
				'transit_days' => 3,
				'provider_code' => null
			]);

			$lineaAereaLhrFirstCheckpoint = $this->checkpointCodeRepository->search([
				'provider_id' => $transitProviderGeneric->id,
				'key' => 'GM-1',
				'description' => 'Picked up at airport'
			])->first();

			$lineaAereaLhrMexProviderService = $this->providerServiceRepository->create([
				'provider_id' => $transitProviderGeneric->id,
				'provider_service_type_id' => $providerServiceTypeTransit->id,
				'first_checkpoint_code_id' => $lineaAereaLhrFirstCheckpoint->id,
				'last_checkpoint_code_id' => $lineaAereaMexLastCheckpoint->id,
				'code' => null,
				'name' => 'Linea Aerea LHR-MEX',
				'transit_days' => 3,
				'provider_code' => null
			]);

			$lineaAereaUsaMexProviderService = $this->providerServiceRepository->create([
				'provider_id' => $transitProviderGeneric->id,
				'provider_service_type_id' => $providerServiceTypeTransit->id,
				'first_checkpoint_code_id' => $firstCheckpointCode->id,
				'last_checkpoint_code_id' => $lineaAereaMexLastCheckpoint->id,
				'code' => null,
				'name' => 'Linea Aerea USA-MEX',
				'transit_days' => 2,
				'provider_code' => null
			]);

			$lineaAereaGruLastCheckpoint = $this->checkpointCodeRepository->search([
				'provider_id' => $transitProviderGeneric->id,
				'description' => 'Arrived at destination country (GRU)'
			])->first();

			$lineaAereaDirectGruProviderService = $this->providerServiceRepository->create([
				'provider_id' => $transitProviderGeneric->id,
				'provider_service_type_id' => $providerServiceTypeTransit->id,
				'first_checkpoint_code_id' => $firstCheckpointCode->id,
				'last_checkpoint_code_id' => $lineaAereaGruLastCheckpoint->id,
				'code' => null,
				'name' => 'Linea Aerea HKG-GRU',
				'transit_days' => 3,
				'provider_code' => null
			]);

			$lineaAereaSclLastCheckpoint = $this->checkpointCodeRepository->search([
				'provider_id' => $transitProviderGeneric->id,
				'description' => 'Arrived at destination country (SCL)'
			])->first();

			$lineaAereaDirectSclProviderService = $this->providerServiceRepository->create([
				'provider_id' => $transitProviderGeneric->id,
				'provider_service_type_id' => $providerServiceTypeTransit->id,
				'first_checkpoint_code_id' => $firstCheckpointCode->id,
				'last_checkpoint_code_id' => $lineaAereaSclLastCheckpoint->id,
				'code' => null,
				'name' => 'Linea Aerea HKG-SCL',
				'transit_days' => 4,
				'provider_code' => null
			]);

			$lineaAereaBogLastCheckpoint = $this->checkpointCodeRepository->search([
				'provider_id' => $transitProviderGeneric->id,
				'description' => 'Arrived at destination country (BOG)'
			])->first();

			$lineaAereaDirectBogProviderService = $this->providerServiceRepository->create([
				'provider_id' => $transitProviderGeneric->id,
				'provider_service_type_id' => $providerServiceTypeTransit->id,
				'first_checkpoint_code_id' => $firstCheckpointCode->id,
				'last_checkpoint_code_id' => $lineaAereaBogLastCheckpoint->id,
				'code' => null,
				'name' => 'Linea Aerea HKG-BOG',
				'transit_days' => 4,
				'provider_code' => null
			]);

			$lineaAereaLhrBogProviderService = $this->providerServiceRepository->create([
				'provider_id' => $transitProviderGeneric->id,
				'provider_service_type_id' => $providerServiceTypeTransit->id,
				'first_checkpoint_code_id' => $lineaAereaLhrFirstCheckpoint->id,
				'last_checkpoint_code_id' => $lineaAereaBogLastCheckpoint->id,
				'code' => null,
				'name' => 'Linea Aerea LHR-BOG',
				'transit_days' => 3,
				'provider_code' => null
			]);

			$lineaAereaUioLastCheckpoint = $this->checkpointCodeRepository->search([
				'provider_id' => $transitProviderGeneric->id,
				'description' => 'Arrived at destination country (UIO)'
			])->first();

			$lineaAereaLhrUioProviderService = $this->providerServiceRepository->create([
				'provider_id' => $transitProviderGeneric->id,
				'provider_service_type_id' => $providerServiceTypeTransit->id,
				'first_checkpoint_code_id' => $lineaAereaLhrFirstCheckpoint->id,
				'last_checkpoint_code_id' => $lineaAereaUioLastCheckpoint->id,
				'code' => null,
				'name' => 'Linea Aerea LHR-UIO',
				'transit_days' => 3,
				'provider_code' => null
			]);

			$lineaAereaLimLastCheckpoint = $this->checkpointCodeRepository->search([
				'provider_id' => $transitProviderGeneric->id,
				'description' => 'Arrived at destination country (LIM)'
			])->first();

			$lineaAereaLhrLimProviderService = $this->providerServiceRepository->create([
				'provider_id' => $transitProviderGeneric->id,
				'provider_service_type_id' => $providerServiceTypeTransit->id,
				'first_checkpoint_code_id' => $lineaAereaLhrFirstCheckpoint->id,
				'last_checkpoint_code_id' => $lineaAereaLimLastCheckpoint->id,
				'code' => null,
				'name' => 'Linea Aerea LHR-LIM',
				'transit_days' => 3,
				'provider_code' => null
			]);

			$lineaAereaUsaBogProviderService = $this->providerServiceRepository->create([
				'provider_id' => $transitProviderGeneric->id,
				'provider_service_type_id' => $providerServiceTypeTransit->id,
				'first_checkpoint_code_id' => $firstCheckpointCode->id,
				'last_checkpoint_code_id' => $lineaAereaLhrBogProviderService->id,
				'code' => null,
				'name' => 'Linea Aerea USA-BOG',
				'transit_days' => 2,
				'provider_code' => null
			]);

			$correoMexRegistered = $this->providerRepository->search(['code' => 'PR4937'])->first();
			$mexPost = $this->providerRepository->search(['code' => 'PR4938'])->first();

			$sellerDropOffProvider = $this->providerRepository->getByCode('PR3278');

			$sellerDropOffFirstCheckpoint = $this->checkpointCodeRepository->search([
				'provider_id' => $sellerDropOffProvider->id,
				'description' => 'Label generated'
			])->first();

			$sellerDropOffLastCheckpoint = $this->checkpointCodeRepository->search([
				'provider_id' => $sellerDropOffProvider->id,
				'description' => 'Sent to the warehouse'
			])->first();

			$sellerDropOffProviderService = $this->providerServiceRepository->create([
				'provider_id' => $sellerDropOffProvider->id,
				'provider_service_type_id' => $providerServiceTypeTransit->id,
				'first_checkpoint_code_id' => $sellerDropOffFirstCheckpoint->id,
				'last_checkpoint_code_id' => $sellerDropOffLastCheckpoint->id,
				'code' => null,
				'name' => 'Label generation',
				'transit_days' => 2,
				'provider_code' => null
			]);

			//====================================================
			/**Mexico Registered RUTA 1**/

			$originLocation = $this->locationRepository
				->search(['country_id' => $china->id, 'code' => 'CN'])->first();

			$destinationLocation = $this->locationRepository
				->search(['country_id' => $mexico->id, 'code' => 'MX'])->first();

			$deliveryRoute1MexicoRegistered = $this->deliveryRouteRepository->create([
				'origin_location_id' => $originLocation->id,
				'destination_location_id' => $destinationLocation->id,
				'total_transit_days' => 23,
				'enabled' => true,
				'label' => 'Mexico Registered Route 1 DIRECT'
			]);

			$sellerDropOffLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1MexicoRegistered->id,
				'provider_service_id' => $sellerDropOffProviderService->id,
				'position' => 1,
				'controlled' => false,
			]);

			$warehouseChina2Leg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1MexicoRegistered->id,
				'provider_service_id' => $warehouseChina2ProviderService->id,
				'position' => 2,
				'controlled' => true,
			]);

			$lineaAereaDirectMex = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1MexicoRegistered->id,
				'provider_service_id' => $lineaAereaDirectMexProviderService->id,
				'position' => 3,
				'controlled' => true,
			]);

			$this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1MexicoRegistered->id,
				'provider_service_id' => $correoMexRegistered->id, // Correo de mexico
				'position' => 4,
				'controlled' => true,
			]);

			//====================================================
			// RUTA 2 MEXICO REGISTERED

			$deliveryRoute2MexicoRegistered = $this->deliveryRouteRepository->create([
				'origin_location_id' => $originLocation->id,
				'destination_location_id' => $destinationLocation->id,
				'total_transit_days' => 27,
				'enabled' => true,
				'label' => 'Mexico Registered Route 2 via LHR'
			]);

			// LEGS

			$sellerDropOffLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute2MexicoRegistered->id,
//	        'provider_service_id' => 28, // seller Drop Off - HKG
				'provider_service_id' => $sellerDropOffProviderService->id,
				'position' => 1,
				'controlled' => false,
			]);

			$warehouseChina2Leg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute2MexicoRegistered->id,
				'provider_service_id' => $warehouseChina2ProviderService->id,
				'position' => 2,
				'controlled' => true,
			]);

			$lineaAereaHkgLhr = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute2MexicoRegistered->id,
				'provider_service_id' => $lineaAereaHkgLhrProviderService->id,
				'position' => 3,
				'controlled' => true,
			]);

			$lineaAereaLhrMex = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute2MexicoRegistered->id,
				'provider_service_id' => $lineaAereaLhrMexProviderService->id,
				'position' => 4,
				'controlled' => true,
			]);

			$this->legRepository->create([
				'delivery_route_id' => $deliveryRoute2MexicoRegistered->id,
				'provider_service_id' => $correoMexRegistered->id, // Correo de mexico
				'position' => 5,
				'controlled' => true,
			]);


			//====================================================
			// Mexico Registered LHR

			$originLocation = $this->locationRepository
				->search(['country_id' => $unitedKingdom->id, 'code' => 'GB'])->first();


			$deliveryRoute1MexicoRegisteredLHR = $this->deliveryRouteRepository->create([
				'origin_location_id' => $originLocation->id,
				'destination_location_id' => $destinationLocation->id,
				'total_transit_days' => 20,
				'enabled' => true,
				'label' => 'Mexico Registered from LHR Route 1'
			]);

			// LEGS

			$lineaAereaHkgLhr = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1MexicoRegisteredLHR->id,
				'provider_service_id' => $lineaAereaHkgLhrProviderService->id,
				'position' => 1,
				'controlled' => false,
			]);

			$lineaAereaLhrMex = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1MexicoRegisteredLHR->id,
				'provider_service_id' => $lineaAereaLhrMexProviderService->id,
				'position' => 2,
				'controlled' => true,
			]);

			$this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1MexicoRegisteredLHR->id,
				'provider_service_id' => $correoMexRegistered->id, // Correo de mexico
				'position' => 3,
				'controlled' => true,
			]);


			//====================================================
			// Mexico Registered LM


			$deliveryRoute1MexicoRegisteredLM = $this->deliveryRouteRepository->create([
				'origin_location_id' => $destinationLocation->id,
				'destination_location_id' => $destinationLocation->id,
				'total_transit_days' => 17,
				'enabled' => true,
				'label' => 'Mexico Registered LM Last Mile'
			]);

			// LEGS

			$lineaAereaDirectMex = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1MexicoRegisteredLM->id,
				'provider_service_id' => $lineaAereaDirectMexProviderService->id,
				'position' => 1,
				'controlled' => false,
			]);

			$this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1MexicoRegisteredLM->id,
				'provider_service_id' => $correoMexRegistered->id, // Correo de mexico
				'position' => 2,
				'controlled' => true,
			]);

			//====================================================
			// Mexico Express Postal

			$originLocation = $this->locationRepository
				->search(['country_id' => 45, 'code' => 'CN'])->first();

			$deliveryRoute1MexicoExpressPostal = $this->deliveryRouteRepository->create([
				'origin_location_id' => $originLocation->id,
				'destination_location_id' => $destinationLocation->id,
				'total_transit_days' => 14,
				'enabled' => true,
				'label' => 'Mexico Express Postal DIRECTO Route 1'
			]);

			// LEGS

			$sellerDropOffLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1MexicoExpressPostal->id,
				'provider_service_id' => $sellerDropOffProviderService->id,
				'position' => 1,
				'controlled' => false,
			]);

			$warehouseChina3 = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1MexicoExpressPostal->id,
				'provider_service_id' => $warehouseChina3ProviderService->id,
				'position' => 2,
				'controlled' => true,
			]);

			$lineaAereaDirectMex = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1MexicoExpressPostal->id,
				'provider_service_id' => $lineaAereaDirectMexProviderService->id,
				'position' => 3,
				'controlled' => true,
			]);

			$this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1MexicoExpressPostal->id,
				'provider_service_id' => $mexPost->id, // Mexpost
				'position' => 4,
				'controlled' => true,
			]);


			//====================================================
			// Mexico Express Postal RUTA 2


			$deliveryRoute2MexicoExpressPostal = $this->deliveryRouteRepository->create([
				'origin_location_id' => $originLocation->id,
				'destination_location_id' => $destinationLocation->id,
				'total_transit_days' => 11,
				'enabled' => true,
				'label' => 'Mexico Express Postal DIRECTO Route 2'
			]);

			// LEGS

			$sellerDropOffLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute2MexicoExpressPostal->id,
				'provider_service_id' => $sellerDropOffProviderService->id,
				'position' => 1,
				'controlled' => false,
			]);

			$warehouseChina3 = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute2MexicoExpressPostal->id,
				'provider_service_id' => $warehouseChina3ProviderService->id,
				'position' => 2,
				'controlled' => true,
			]);

			$lineaAereaDirectMex = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute2MexicoExpressPostal->id,
				'provider_service_id' => $lineaAereaDirectMexProviderService->id,
				'position' => 3,
				'controlled' => true,
			]);

			//TODO Hound Express Liberation

			$houndExpressLiberacion = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute2MexicoExpressPostal->id,
				'provider_service_id' => $houndExpressProviderService->id,
				'position' => 4,
				'controlled' => true,
			]);

			$this->legRepository->create([
				'delivery_route_id' => $deliveryRoute2MexicoExpressPostal->id,
				'provider_service_id' => $qualityPostProviderService->id,
				'position' => 5,
				'controlled' => true,
			]);


			//====================================================
			// Mexico Express Ecommerce

			// IGUAL A: // Mexico Express Postal RUTA 2



			//====================================================
			// Mexico Express Ecommerce ROUTE 2

			// IGUAL A: Mexico Express Postal Ruta 1



			//====================================================
			// Mexico Express Postal LHR Route 1

			$originLocation = $this->locationRepository
				->search(['country_id' => $unitedKingdom->id, 'code' => 'GB'])->first();

			$deliveryRoute1MexicoExpressPostalLHR = $this->deliveryRouteRepository->create([
				'origin_location_id' => $originLocation->id,
				'destination_location_id' => $destinationLocation->id,
				'total_transit_days' => 18,
				'enabled' => true,
				'label' => 'Mexico Express Postal LHR Directo Route 1'
			]);

			// LEGS

			$warehouseLhr = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1MexicoExpressPostalLHR->id,
				'provider_service_id' => $warehouseChina1ProviderService->id,
				'position' => 1,
				'controlled' => true,
			]);

			$lineaAereaLhrMex = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1MexicoExpressPostalLHR->id,
				'provider_service_id' => $lineaAereaLhrMexProviderService->id,
				'position' => 2,
				'controlled' => true,
			]);

			$this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1MexicoExpressPostalLHR->id,
				'provider_service_id' => $mexPost->id, // Mexpost
				'position' => 3,
				'controlled' => true,
			]);


			//====================================================
			// Mexico Express Postal LHR Route 2

			$originLocation = $this->locationRepository
				->search(['country_id' => $unitedKingdom->id, 'code' => 'GB'])->first();

			$deliveryRoute2MexicoExpressPostalLHR = $this->deliveryRouteRepository->create([
				'origin_location_id' => $originLocation->id,
				'destination_location_id' => $destinationLocation->id,
				'total_transit_days' => 15,
				'enabled' => true,
				'label' => 'Mexico Express Postal LHR Directo Route 2'
			]);

			// LEGS

			$warehouseLhr = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute2MexicoExpressPostalLHR->id,
				'provider_service_id' => $warehouseChina1ProviderService->id,
				'position' => 1,
				'controlled' => true,
			]);

			$lineaAereaLhrMex = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute2MexicoExpressPostalLHR->id,
				'provider_service_id' => $lineaAereaLhrMexProviderService->id,
				'position' => 2,
				'controlled' => true,
			]);


			//TODO Hound Express Liberation

			$houndExpressLiberacion = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute2MexicoExpressPostalLHR->id,
				'provider_service_id' => $houndExpressProviderService->id,
				'position' => 3,
				'controlled' => true,
			]);

			$this->legRepository->create([
				'delivery_route_id' => $deliveryRoute2MexicoExpressPostalLHR->id,
				'provider_service_id' => $qualityPostProviderService->id,
				'position' => 4,
				'controlled' => true,
			]);


			//====================================================
			// Mexico Express Ecommerce LHR Route 1

			// IGUAL A Mexico Express Postal LHR Ruta 2



			//====================================================
			// Mexico Express Ecommerce LHR Route 2

			// IGUAL A Mexico Express Postal LHR Ruta 1



			//====================================================
			// Mexico Registrado USA Route 1

			$originLocation = $this->locationRepository
				->search(['country_id' => $unitedStates->id, 'code' => 'US'])->first();

			$deliveryRoute1MexicoRegisteredUSA = $this->deliveryRouteRepository->create([
				'origin_location_id' => $originLocation->id,
				'destination_location_id' => $destinationLocation->id,
				'total_transit_days' => 19,
				'enabled' => true,
				'label' => 'Mexico Registered USA Directo Route 1'
			]);

			// LEGS

			$sellerDropOffLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1MexicoRegisteredUSA->id,
				'provider_service_id' => $sellerDropOffProviderService->id,
				'position' => 1,
				'controlled' => false,
			]);


			$lineaAereaUsaMex = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1MexicoRegisteredUSA->id,
				'provider_service_id' => $lineaAereaUsaMexProviderService->id,
				'position' => 2,
				'controlled' => true,
			]);

			$this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1MexicoRegisteredUSA->id,
				'provider_service_id' => $correoMexRegistered->id, // Correo de mexico
				'position' => 3,
				'controlled' => true,
			]);


			//====================================================
			// Mexico USA Express Ecommerce Route 1

			$deliveryRoute1MexicoUSAEcommerce = $this->deliveryRouteRepository->create([
				'origin_location_id' => $originLocation->id,
				'destination_location_id' => $destinationLocation->id,
				'total_transit_days' => 9,
				'enabled' => true,
				'label' => 'Mexico USA Ecommerce Directo Route 1'
			]);

			// LEGS

			$sellerDropOffLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1MexicoUSAEcommerce->id,
				'provider_service_id' => $sellerDropOffProviderService->id,
				'position' => 1,
				'controlled' => false,
			]);

			$warehouseUSA = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1MexicoUSAEcommerce->id,
				'provider_service_id' => $warehouseChina3ProviderService->id,
				'position' => 2,
				'controlled' => false,
			]);

			$lineaAereaUsaMex = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1MexicoUSAEcommerce->id,
				'provider_service_id' => $lineaAereaUsaMexProviderService->id,
				'position' => 3,
				'controlled' => true,
			]);

			// TODO Hound Express Liberation

			$houndExpressLiberacion = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1MexicoUSAEcommerce->id,
				'provider_service_id' => $houndExpressProviderService->id,
				'position' => 4,
				'controlled' => true,
			]);

			$this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1MexicoUSAEcommerce->id,
				'provider_service_id' => $qualityPostProviderService->id,
				'position' => 5,
				'controlled' => true,
			]);


			//====================================================
			// Mexico USA Express Ecommerce Route 2

			$deliveryRoute2MexicoUSAEcommerce = $this->deliveryRouteRepository->create([
				'origin_location_id' => $originLocation->id,
				'destination_location_id' => $destinationLocation->id,
				'total_transit_days' => 12,
				'enabled' => true,
				'label' => 'Mexico USA Ecommerce Directo Route 2'
			]);

			// LEGS

			$sellerDropOffLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute2MexicoUSAEcommerce->id,
				'provider_service_id' => $sellerDropOffProviderService->id,
				'position' => 1,
				'controlled' => false,
			]);

			$warehouseUSA = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute2MexicoUSAEcommerce->id,
				'provider_service_id' => $warehouseChina3ProviderService->id,
				'position' => 2,
				'controlled' => false,
			]);

			$lineaAereaUsaMex = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute2MexicoUSAEcommerce->id,
				'provider_service_id' => $lineaAereaUsaMexProviderService->id,
				'position' => 3,
				'controlled' => true,
			]);

			$this->legRepository->create([
				'delivery_route_id' => $deliveryRoute2MexicoUSAEcommerce->id,
				'provider_service_id' => $mexPost->id, // Mexpost
				'position' => 4,
				'controlled' => true,
			]);


			//====================================================
			// Mexico USA Express Postal Route 1

			// IGUAL A Mexico USA Express E-commerce Ruta 2


			/**
			 * Mexico USA Express Postal ROUTE 2
			 */

			// IGUAL A: IGUAL A Mexico USA Express E-commerce Ruta 1


			/**
			 * Brasil Express E-Commerce ROUTE 1
			 */

			$originLocation = $this->locationRepository
				->search(['country_id' => $china->id, 'code' => $china->code])->first();

			$destinationLocation = $this->locationRepository
				->search(['country_id' => $brazil->id, 'code' => $brazil->code])->first();

			$deliveryRoute1BrasilExpress = $this->deliveryRouteRepository->create([
				'origin_location_id' => $originLocation->id,
				'destination_location_id' => $destinationLocation->id,
				'total_transit_days' => 18,
				'enabled' => true,
				'label' => 'Brasil Express E-Commerce ROUTE 1'
			]);

			/**
			 * Brasil Express E-Commerce ROUTE 1
			 * LEG 1 Seller Drop off
			 */


			$sellerDropOffLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1BrasilExpress->id,
				'provider_service_id' => $sellerDropOffProviderService->id,
				'position' => 1,
				'controlled' => false,
			]);

			/**
			 * Brasil Express E-Commerce ROUTE 1
			 * LEG 2 Warehouse China 1
			 */

			$warehouseChina1Leg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1BrasilExpress->id,
				'provider_service_id' => $warehouseChina1ProviderService->id,
				'position' => 2,
				'controlled' => true,
			]);

			/**
			 * Brasil Express E-Commerce ROUTE 1
			 * LEG 3 Linea Aerea Direct GRU
			 */

			$airlineDirectGruLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1BrasilExpress->id,
				'provider_service_id' => $lineaAereaDirectGruProviderService->id,
				'position' => 3,
				'controlled' => true,
			]);

			/**
			 * Brasil Express E-Commerce ROUTE 1
			 * LEG 4 PHX
			 */

			$phxCargoProvider = $this->providerRepository->search([
				'code' => 'PR8432',
				'name' => 'PHX Cargo'
			])->first();

			$providerServicePhxCargoPriority = $this->providerServiceRepository->search([
				'provider_id' => $phxCargoProvider->id,
				'name' => 'Priority'
			])->first();

			$phxLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1BrasilExpress->id,
				'provider_service_id' => $providerServicePhxCargoPriority->id,
				'position' => 4,
				'controlled' => true,
			]);

			/**
			 * Chile Registered ROUTE 1
			 */

			$originLocation = $this->locationRepository
				->search(['country_id' => $china->id, 'code' => $china->code])->first();

			$destinationLocation = $this->locationRepository
				->search(['country_id' => $chile->id, 'code' => $chile->code])->first();

			$deliveryRoute1ChileRegistered = $this->deliveryRouteRepository->create([
				'origin_location_id' => $originLocation->id,
				'destination_location_id' => $destinationLocation->id,
				'total_transit_days' => 20,
				'enabled' => true,
				'label' => 'Chile Registered ROUTE 1'
			]);

			/**
			 * Chile Registered ROUTE 1
			 * LEG 1 Seller Drop off
			 */

			$sellerDropOffLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1ChileRegistered->id,
				'provider_service_id' => $sellerDropOffProviderService->id,
				'position' => 1,
				'controlled' => false,
			]);

			/**
			 * Chile Registered ROUTE 1
			 * LEG 2 Warehouse China 2
			 */

			$warehouseChina2Leg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1ChileRegistered->id,
				'provider_service_id' => $warehouseChina2ProviderService->id,
				'position' => 2,
				'controlled' => true,
			]);

			/**
			 * Chile Registered ROUTE 1
			 * LEG 3 Linea Aerea Direct SCL
			 */

			$airlineDirectSclLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1ChileRegistered->id,
				'provider_service_id' => $lineaAereaDirectSclProviderService->id,
				'position' => 3,
				'controlled' => true,
			]);

			/**
			 * Chile Registered ROUTE 1
			 * LEG 4 Correos de Chile
			 */

			$correosDeChileProvider = $this->providerRepository->search([
				'code' => 'PR2785',
				'name' => 'Correos de Chile'
			])->first();

			$providerServiceCorreosDeChile = $this->providerServiceRepository->search([
				'provider_id' => $correosDeChileProvider->id,
				'provider_service_type_id' => $providerServiceTypeDistribution->id
			])->first();

			$correosDeChileLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1ChileRegistered->id,
				'provider_service_id' => $providerServiceCorreosDeChile->id,
				'position' => 4,
				'controlled' => true,
			]);

			/**
			 * Chile Registered ROUTE 2
			 */

			$originLocation = $this->locationRepository
				->search(['country_id' => $china->id, 'code' => $china->code])->first();

			$destinationLocation = $this->locationRepository
				->search(['country_id' => $chile->id, 'code' => $chile->code])->first();

			$deliveryRoute2ChileRegistered = $this->deliveryRouteRepository->create([
				'origin_location_id' => $originLocation->id,
				'destination_location_id' => $destinationLocation->id,
				'total_transit_days' => 15,
				'enabled' => true,
				'label' => 'Chile Registered ROUTE 2'
			]);

			/**
			 * Chile Registered ROUTE 2
			 * LEG 1 Seller Drop off
			 */

			$sellerDropOffLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute2ChileRegistered->id,
				'provider_service_id' => $sellerDropOffProviderService->id,
				'position' => 1,
				'controlled' => false,
			]);

			/**
			 * Chile Registered ROUTE 2
			 * LEG 2 Warehouse China 2
			 */

			$warehouseChina2Leg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute2ChileRegistered->id,
				'provider_service_id' => $warehouseChina2ProviderService->id,
				'position' => 2,
				'controlled' => true,
			]);

			/**
			 * Chile Registered ROUTE 2
			 * LEG 3 Linea Aerea Direct SCL
			 */

			$airlineDirectSclLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute2ChileRegistered->id,
				'provider_service_id' => $lineaAereaDirectSclProviderService->id,
				'position' => 3,
				'controlled' => true,
			]);

			/**
			 * Chile Registered ROUTE 2
			 * LEG 4 Blue Express
			 */

			$blueExpressProvider = $this->providerRepository->search([
				'code' => 'PR6548',
				'name' => 'Blue Express'
			])->first();

			$providerServiceblueExpress = $this->providerServiceRepository->search([
				'provider_id' => $blueExpressProvider->id,
				'name' => 'Priority'
			])->first();

			$blueExpressLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute2ChileRegistered->id,
				'provider_service_id' => $providerServiceblueExpress->id,
				'position' => 4,
				'controlled' => true,
			]);

			/**
			 * Chile Express E-Commerce
			 */

			// IGUAL A Chile Registered Ruta 2


			/**
			 * Colombia Registered ROUTE 1
			 */

			$originLocation = $this->locationRepository
				->search(['country_id' => $china->id, 'code' => $china->code])->first();

			$destinationLocation = $this->locationRepository
				->search(['country_id' => $colombia->id, 'code' => $colombia->code])->first();

			$deliveryRoute1ColombiaRegistered = $this->deliveryRouteRepository->create([
				'origin_location_id' => $originLocation->id,
				'destination_location_id' => $destinationLocation->id,
				'total_transit_days' => 20,
				'enabled' => true,
				'label' => 'Colombia Registered ROUTE 1'
			]);

			/**
			 * Colombia Registered ROUTE 1
			 * LEG 1 Seller Drop off
			 */

			$sellerDropOffLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1ColombiaRegistered->id,
				'provider_service_id' => $sellerDropOffProviderService->id,
				'position' => 1,
				'controlled' => false,
			]);

			/**
			 * Colombia Registered ROUTE 1
			 * LEG 2 Warehouse China 2
			 */

			$warehouseChina2Leg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1ColombiaRegistered->id,
				'provider_service_id' => $warehouseChina2ProviderService->id,
				'position' => 2,
				'controlled' => true,
			]);

			/**
			 * Colombia Registered ROUTE 1
			 * LEG 3 Linea Aerea Direct BOG
			 */

			$airlineDirectBogLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1ColombiaRegistered->id,
				'provider_service_id' => $lineaAereaDirectBogProviderService->id,
				'position' => 3,
				'controlled' => true,
			]);

			/**
			 * Colombia Registered ROUTE 1
			 * LEG 4 4-72
			 */

			$colombia472Provider = $this->providerRepository->search([
				'code' => 'PR3548',
				'name' => '4-72'
			])->first();

			$providerServiceColombia472 = $this->providerServiceRepository->search([
				'provider_id' => $colombia472Provider->id,
				'name' => 'Registrado'
			])->first();

			$colombia472Leg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1ColombiaRegistered->id,
				'provider_service_id' => $providerServiceColombia472->id,
				'position' => 4,
				'controlled' => true,
			]);

			/**
			 * Colombia Registered ROUTE 2
			 */

			$originLocation = $this->locationRepository
				->search(['country_id' => $china->id, 'code' => $china->code])->first();

			$destinationLocation = $this->locationRepository
				->search(['country_id' => $colombia->id, 'code' => $colombia->code])->first();

			$deliveryRoute2ColombiaRegistered = $this->deliveryRouteRepository->create([
				'origin_location_id' => $originLocation->id,
				'destination_location_id' => $destinationLocation->id,
				'total_transit_days' => 22,
				'enabled' => true,
				'label' => 'Colombia Registered ROUTE 2'
			]);

			/**
			 * Colombia Registered ROUTE 2
			 * LEG 1 Seller Drop off
			 */

			$sellerDropOffLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute2ColombiaRegistered->id,
				'provider_service_id' => $sellerDropOffProviderService->id,
				'position' => 1,
				'controlled' => false,
			]);

			/**
			 * Colombia Registered ROUTE 2
			 * LEG 2 Warehouse China 2
			 */

			$warehouseChina2Leg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute2ColombiaRegistered->id,
				'provider_service_id' => $warehouseChina2ProviderService->id,
				'position' => 2,
				'controlled' => true,
			]);

			/**
			 * Colombia Registered ROUTE 2
			 * LEG 3 Linea Aerea HKG-LHR
			 */

			$airlineHkgLhrLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute2ColombiaRegistered->id,
				'provider_service_id' => $lineaAereaHkgLhrProviderService->id,
				'position' => 3,
				'controlled' => true,
			]);

			/**
			 * Colombia Registered ROUTE 2
			 * LEG 4 Linea Aerea HKG-LHR
			 */

			$airlineLhrBogLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute2ColombiaRegistered->id,
				'provider_service_id' => $lineaAereaLhrBogProviderService->id,
				'position' => 4,
				'controlled' => true,
			]);

			/**
			 * Colombia Registered ROUTE 2
			 * LEG 5 4-72
			 */

			$colombia472Leg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute2ColombiaRegistered->id,
				'provider_service_id' => $providerServiceColombia472->id,
				'position' => 5,
				'controlled' => true,
			]);

			/**
			 * Colombia Express E-Commerce ROUTE 1
			 */

			$originLocation = $this->locationRepository
				->search(['country_id' => $china->id, 'code' => $china->code])->first();

			$destinationLocation = $this->locationRepository
				->search(['country_id' => $colombia->id, 'code' => $colombia->code])->first();

			$deliveryRoute1ColombiaExpress = $this->deliveryRouteRepository->create([
				'origin_location_id' => $originLocation->id,
				'destination_location_id' => $destinationLocation->id,
				'total_transit_days' => 16,
				'enabled' => true,
				'label' => 'Colombia Express E-Commerce ROUTE 1'
			]);

			/**
			 * Colombia Express E-Commerce ROUTE 1
			 * LEG 1 Seller Drop off
			 */

			$sellerDropOffLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1ColombiaExpress->id,
				'provider_service_id' => $sellerDropOffProviderService->id,
				'position' => 1,
				'controlled' => false,
			]);

			/**
			 * Colombia Express E-Commerce ROUTE 1
			 * LEG 2 Warehouse China 1
			 */

			$warehouseChina1Leg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1ColombiaExpress->id,
				'provider_service_id' => $warehouseChina1ProviderService->id,
				'position' => 2,
				'controlled' => true,
			]);

			/**
			 * Colombia Express E-Commerce ROUTE 1
			 * LEG 3 Linea Aerea Direct BOG
			 */

			$airlineDirectBogLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1ColombiaExpress->id,
				'provider_service_id' => $lineaAereaDirectBogProviderService->id,
				'position' => 3,
				'controlled' => true,
			]);

			/**
			 * Colombia Express E-Commerce ROUTE 1
			 * LEG 4 Tranexco Liberación
			 */

			$tranexcoLiberationLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1ColombiaExpress->id,
				'provider_service_id' => $tranexcoLiberationProviderService->id,
				'position' => 4,
				'controlled' => true,
			]);

			/**
			 * Colombia Express E-Commerce ROUTE 1
			 * LEG 5 TCC
			 */

			$tccProvider = $this->providerRepository->search([
				'code' => 'PR8253',
				'name' => 'TCC'
			])->first();

			$providerServiceTcc = $this->providerServiceRepository->search([
				'provider_id' => $tccProvider->id,
				'provider_service_type_id' => $providerServiceTypeDistribution->id,
			])->first();

			$tccLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute1ColombiaExpress->id,
				'provider_service_id' => $providerServiceTcc->id,
				'position' => 5,
				'controlled' => true,
			]);

			/**
			 * Colombia Express E-Commerce ROUTE 2
			 */

			$originLocation = $this->locationRepository
				->search(['country_id' => $china->id, 'code' => $china->code])->first();

			$destinationLocation = $this->locationRepository
				->search(['country_id' => $colombia->id, 'code' => $colombia->code])->first();

			$deliveryRoute2ColombiaExpress = $this->deliveryRouteRepository->create([
				'origin_location_id' => $originLocation->id,
				'destination_location_id' => $destinationLocation->id,
				'total_transit_days' => 16,
				'enabled' => true,
				'label' => 'Colombia Express E-Commerce ROUTE 2'
			]);

			/**
			 * Colombia Express E-Commerce ROUTE 2
			 * LEG 1 Seller Drop off
			 */

			$sellerDropOffLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute2ColombiaExpress->id,
				'provider_service_id' => $sellerDropOffProviderService->id,
				'position' => 1,
				'controlled' => false,
			]);

			/**
			 * Colombia Express E-Commerce ROUTE 2
			 * LEG 2 Warehouse China 1
			 */

			$warehouseChina1Leg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute2ColombiaExpress->id,
				'provider_service_id' => $warehouseChina1ProviderService->id,
				'position' => 2,
				'controlled' => true,
			]);

			/**
			 * Colombia Express E-Commerce ROUTE 2
			 * LEG 3 Linea Aerea Direct BOG
			 */

			$airlineDirectBogLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute2ColombiaExpress->id,
				'provider_service_id' => $lineaAereaDirectBogProviderService->id,
				'position' => 3,
				'controlled' => true,
			]);

			/**
			 * Colombia Express E-Commerce ROUTE 2
			 * LEG 4 Tranexco Liberación
			 */

			$tranexcoLiberationLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoute2ColombiaExpress->id,
				'provider_service_id' => $tranexcoDistributionProviderService->id,
				'position' => 4,
				'controlled' => true,
			]);

			/**
			 * Ecuador Registered
			 */

			$originLocation = $this->locationRepository
				->search(['country_id' => $china->id, 'code' => $china->code])->first();

			$destinationLocation = $this->locationRepository
				->search(['country_id' => $ecuador->id, 'code' => $ecuador->code])->first();

			$deliveryRouteEcuadorRegistered = $this->deliveryRouteRepository->create([
				'origin_location_id' => $originLocation->id,
				'destination_location_id' => $destinationLocation->id,
				'total_transit_days' => 26,
				'enabled' => true,
				'label' => 'Ecuador Registered'
			]);

			/**
			 * Ecuador Registered
			 * LEG 1 Seller Drop off
			 */

			$sellerDropOffLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRouteEcuadorRegistered->id,
				'provider_service_id' => $sellerDropOffProviderService->id,
				'position' => 1,
				'controlled' => false,
			]);

			/**
			 * Ecuador Registered
			 * LEG 2 Warehouse China 1
			 */

			$warehouseChina1Leg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRouteEcuadorRegistered->id,
				'provider_service_id' => $warehouseChina1ProviderService->id,
				'position' => 2,
				'controlled' => true,
			]);

			/**
			 * Ecuador Registered
			 * LEG 3 Linea Aerea HKG-LHR
			 */

			$airlineHkgLhrLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRouteEcuadorRegistered->id,
				'provider_service_id' => $lineaAereaHkgLhrProviderService->id,
				'position' => 3,
				'controlled' => true,
			]);

			/**
			 * Ecuador Registered
			 * LEG 4 Linea Aerea HKG-LHR
			 */

			$airlineLhrUioLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRouteEcuadorRegistered->id,
				'provider_service_id' => $lineaAereaLhrUioProviderService->id,
				'position' => 4,
				'controlled' => true,
			]);

			/**
			 * Ecuador Registered
			 * LEG 5 Correos Ecuador
			 */

			$correosDelEcuadorProvider = $this->providerRepository->search([
				'code' => 'PR7946',
				'name' => 'Correos del Ecuador'
			])->first();

			$providerServiceTypeDistribution = $this->providerServiceTypeRepository
				->getByKey('distribution');

			$correosDelEcuadorProviderService = $this->providerServiceRepository->search([
				'provider_id' => $correosDelEcuadorProvider->id,
				'provider_service_type_id' => $providerServiceTypeDistribution->id,
			])->first();

			$correosEcuadorLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRouteEcuadorRegistered->id,
				'provider_service_id' => $correosDelEcuadorProviderService->id,
				'position' => 5,
				'controlled' => true,
			]);

			/**
			 * Colombia Registrado LHR
			 */


			$originLocation = $this->locationRepository
				->search(['country_id' => $unitedKingdom->id, 'code' => 'GB'])->first();

			$destinationLocation = $this->locationRepository
				->search(['country_id' => $colombia->id, 'code' => $colombia->code])->first();

			$deliveryRouteColombiaRegisteredLhr = $this->deliveryRouteRepository->create([
				'origin_location_id' => $originLocation->id,
				'destination_location_id' => $destinationLocation->id,
				'total_transit_days' => 15,
				'enabled' => true,
				'label' => 'Colombia Registrado LHR'
			]);

			/**
			 * Colombia Registrado LHR
			 * LEG 1 Linea Aerea HKG-LHR
			 */

			$airlineHkgLhrLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRouteColombiaRegisteredLhr->id,
				'provider_service_id' => $lineaAereaHkgLhrProviderService->id,
				'position' => 1,
				'controlled' => false,
			]);

			/**
			 * Colombia Registrado LHR
			 * LEG 2 Linea Aerea LHR BOG
			 */

			$airlineLhrBogLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRouteColombiaRegisteredLhr->id,
				'provider_service_id' => $lineaAereaLhrBogProviderService->id,
				'position' => 2,
				'controlled' => true,
			]);

			/**
			 * Colombia Registrado LHR
			 * LEG 3 4-72
			 */

			$colombia472Leg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRouteColombiaRegisteredLhr->id,
				'provider_service_id' => $providerServiceColombia472->id,
				'position' => 3,
				'controlled' => true,
			]);

			/**
			 * Colombia Registrado LM
			 */

			$destinationLocation = $this->locationRepository
				->search(['country_id' => $colombia->id, 'code' => $colombia->code])->first();

			$deliveryRouteColombiaRegisteredLm = $this->deliveryRouteRepository->create([
				'origin_location_id' => $destinationLocation->id,
				'destination_location_id' => $destinationLocation->id,
				'total_transit_days' => 12,
				'enabled' => true,
				'label' => 'Colombia Registrado LM'
			]);

			/**
			 * Colombia Registrado LM
			 * LEG 1 Linea Aerea Direct BOG
			 */

			$airlineDirectBogLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRouteColombiaRegisteredLm->id,
				'provider_service_id' => $lineaAereaDirectBogProviderService->id,
				'position' => 1,
				'controlled' => false,
			]);

			/**
			 * Colombia Registrado LM
			 * LEG 2 4-72
			 */

			$colombia472Leg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRouteColombiaRegisteredLm->id,
				'provider_service_id' => $providerServiceColombia472->id,
				'position' => 2,
				'controlled' => true,
			]);

			/**
			 * Peru Hibrido
			 */

			$originLocation = $this->locationRepository
				->search(['country_id' => $china->id, 'code' => $china->code])->first();

			$destinationLocation = $this->locationRepository
				->search(['country_id' => $peru->id, 'code' => $peru->code])->first();

			$deliveryRoutePeru = $this->deliveryRouteRepository->create([
				'origin_location_id' => $originLocation->id,
				'destination_location_id' => $destinationLocation->id,
				'total_transit_days' => 19,
				'enabled' => true,
				'label' => 'Peru Hibrido'
			]);

			/**
			 * Peru Hibrido
			 * LEG 1 Seller Drop off
			 */

			$sellerDropOffLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoutePeru->id,
				'provider_service_id' => $sellerDropOffProviderService->id,
				'position' => 1,
				'controlled' => false,
			]);

			/**
			 * Peru Hibrido
			 * LEG 2 Warehouse China 2
			 */

			$warehouseChina2Leg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoutePeru->id,
				'provider_service_id' => $warehouseChina2ProviderService->id,
				'position' => 2,
				'controlled' => true,
			]);

			/**
			 * Peru Hibrido
			 * LEG 3 Linea Aerea HKG-LHR
			 */

			$airlineHkgLhrLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoutePeru->id,
				'provider_service_id' => $lineaAereaHkgLhrProviderService->id,
				'position' => 3,
				'controlled' => true,
			]);

			/**
			 * Peru Hibrido
			 * LEG 4 Linea Aerea LHR LIM
			 */

			$airlineHkgLimLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoutePeru->id,
				'provider_service_id' => $lineaAereaLhrLimProviderService->id,
				'position' => 4,
				'controlled' => true,
			]);

			/**
			 * Peru Hibrido
			 * LEG 5 Serpost Liberación
			 */

			$serpostProvider = $this->providerRepository->search([
				'code' => 'PR1985',
				'name' => 'Serpost'
			])->first();

			$providerServiceTypeDistribution = $this->providerServiceTypeRepository
				->getByKey('distribution');

			$serpostLiberationProviderService = $this->providerServiceRepository->search([
				'provider_id' => $serpostProvider->id,
				'provider_service_type_id' => $providerServiceTypeDistribution->id,
			])->first();

			$serpostLiberationLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoutePeru->id,
				'provider_service_id' => $serpostLiberationProviderService->id,
				'position' => 5,
				'controlled' => true,
			]);

			/**
			 * Peru Hibrido
			 * LEG 6 Urbano Liberación
			 */

			$urbanoProvider = $this->providerRepository->search([
				'code' => 'PR6749',
				'name' => 'Urbano'
			])->first();

			$providerServiceTypeDistribution = $this->providerServiceTypeRepository
				->getByKey('distribution');

			$urbanoPeruProviderService = $this->providerServiceRepository->search([
				'provider_id' => $urbanoProvider->id,
				'provider_service_type_id' => $providerServiceTypeDistribution->id
			])->first();

			$serpostLiberationLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoutePeru->id,
				'provider_service_id' => $urbanoPeruProviderService->id,
				'position' => 6,
				'controlled' => true,
			]);

			/**
			 * Peru Registrado
			 */

			$originLocation = $this->locationRepository
				->search(['country_id' => $china->id, 'code' => $china->code])->first();

			$destinationLocation = $this->locationRepository
				->search(['country_id' => $peru->id, 'code' => $peru->code])->first();

			$deliveryRoutePeruRegistered = $this->deliveryRouteRepository->create([
				'origin_location_id' => $originLocation->id,
				'destination_location_id' => $destinationLocation->id,
				'total_transit_days' => 25,
				'enabled' => true,
				'label' => 'Peru Registered'
			]);

			/**
			 * Peru Registrado
			 * LEG 1 Seller Drop off
			 */

			$sellerDropOffLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoutePeruRegistered->id,
				'provider_service_id' => $sellerDropOffProviderService->id,
				'position' => 1,
				'controlled' => false,
			]);

			/**
			 * Peru Registrado
			 * LEG 2 Warehouse China 2
			 */

			$warehouseChina2Leg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoutePeruRegistered->id,
				'provider_service_id' => $warehouseChina2ProviderService->id,
				'position' => 2,
				'controlled' => true,
			]);

			/**
			 * Peru Registrado
			 * LEG 3 Linea Aerea HKG-LHR
			 */

			$airlineHkgLhrLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoutePeruRegistered->id,
				'provider_service_id' => $lineaAereaHkgLhrProviderService->id,
				'position' => 3,
				'controlled' => true,
			]);

			/**
			 * Peru Registrado
			 * LEG 4 Linea Aerea LHR LIM
			 */

			$airlineHkgLimLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoutePeruRegistered->id,
				'provider_service_id' => $lineaAereaLhrLimProviderService->id,
				'position' => 4,
				'controlled' => true,
			]);

			/**
			 * Peru Registrado
			 * LEG 5 Serpost Distribución
			 */

			$serpostDistributionLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRoutePeruRegistered->id,
				'provider_service_id' => $serpostLiberationProviderService->id,
				'position' => 5,
				'controlled' => true,
			]);

			/**
			 * Colombia Registrado USA
			 */

			$originLocation = $this->locationRepository
				->search(['country_id' => $unitedStates->id, 'code' => $unitedStates->code])->first();

			$destinationLocation = $this->locationRepository
				->search(['country_id' => $mexico->id, 'code' => $mexico->code])->first();

			$deliveryRouteColombiaRegisteredUsa = $this->deliveryRouteRepository->create([
				'origin_location_id' => $originLocation->id,
				'destination_location_id' => $destinationLocation->id,
				'total_transit_days' => 2,
				'enabled' => true,
				'label' => 'Colombia Registrado USA'
			]);

			/**
			 * Colombia Registrado USA
			 * LEG 1 Seller Drop off
			 */

			$sellerDropOffLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRouteColombiaRegisteredUsa->id,
				'provider_service_id' => $sellerDropOffProviderService->id,
				'position' => 1,
				'controlled' => false,
			]);

			/**
			 * Colombia Registrado USA
			 * LEG 2 Warehouse USA
			 */

			$warehouseUsaLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRouteColombiaRegisteredUsa->id,
				'provider_service_id' => $warehouseChina3ProviderService->id,
				'position' => 2,
				'controlled' => false,
			]);

			/**
			 * Colombia Registrado USA
			 * LEG 3 Linea Aerea USA BOG
			 */

			$airlineUsaBogLeg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRouteColombiaRegisteredUsa->id,
				'provider_service_id' => $lineaAereaUsaBogProviderService->id,
				'position' => 3,
				'controlled' => true,
			]);

			/**
			 * Colombia Registrado USA
			 * LEG 4 4-72
			 */

			$colombia472Leg = $this->legRepository->create([
				'delivery_route_id' => $deliveryRouteColombiaRegisteredUsa->id,
				'provider_service_id' => $providerServiceColombia472->id,
				'position' => 4,
				'controlled' => true,
			]);


			// RECALCULAR DIAS DE TRANSITO CALCULADOS DE LAS RUTAS

			$deliveryRoutes = $this->deliveryRouteRepository->all();

			/** @var DeliveryRoute $deliveryRoute */
			foreach ($deliveryRoutes as $deliveryRoute) {
				$this->deliveryRouteRepository->update($deliveryRoute, [
					'controlled_transit_days' => $deliveryRoute->calculateControlledTransitDays(),
					'uncontrolled_transit_days' => $deliveryRoute->calculateUncontrolledTransitDays(),
					'total_transit_days' => $deliveryRoute->calculateTotalTransitDays(),
				]);
			}

			DB::commit();

		} catch (Exception $e) {
			DB::rollBack();

			logger($e->getMessage());
			logger($e->getTraceAsString());

			throw $e;
		}

    }
}
