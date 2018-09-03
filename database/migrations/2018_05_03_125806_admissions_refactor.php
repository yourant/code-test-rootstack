<?php

use App\Models\Client;
use App\Repositories\CheckpointCodeRepository;
use App\Repositories\ClientRepository;
use App\Repositories\CountryRepository;
use App\Repositories\ProviderRepository;
use App\Repositories\ProviderServiceRepository;
use App\Repositories\TimezoneRepository;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Output\ConsoleOutput;

class AdmissionsRefactor extends Migration
{
    protected $consoleOutput;

    public function __construct()
    {
        $this->consoleOutput = new ConsoleOutput();
    }

    public function up()
    {
        try {
            DB::beginTransaction();

            logger("Start main migration");

            Schema::create('locations', function (Blueprint $table) {
                $table->increments('id');
                $table->string('code');
                $table->string('description')->nullable();
                $table->string('type')->nullable();
                $table->integer('country_id')->unsigned();
                $table->timestamps();

                $table->foreign('country_id')->references('id')->on('countries')->onUpdate('cascade')->onDelete('cascade');

                $table->unique('code');
            });

            Schema::create('delivery_routes', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('origin_location_id')->unsigned();
                $table->integer('destination_location_id')->unsigned();
                $table->integer('controlled_transit_days')->unsigned()->nullable();
                $table->integer('uncontrolled_transit_days')->unsigned()->nullable();
                $table->integer('total_transit_days')->unsigned()->nullable();
                $table->boolean('enabled')->default(true);
                $table->string('label')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('origin_location_id')->references('id')->on('locations')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('destination_location_id')->references('id')->on('locations')->onUpdate('cascade')->onDelete('cascade');
            });

            Schema::create('billing_modes', function (Blueprint $table) {
                $table->increments('id');
                $table->string('key')->unique();
                $table->string('description')->nullable();

                $table->timestamps();
            });

            Schema::table('service_types', function (Blueprint $table) {
                $table->rename('provider_services');
            });

            Schema::create('service_types', function (Blueprint $table) {
                $table->increments('id');
                $table->string('key')->unique();
                $table->string('description')->nullable();

                $table->timestamps();
            });

            Schema::create('services', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('origin_location_id')->unsigned();
                $table->integer('destination_location_id')->unsigned();
                $table->integer('service_type_id')->unsigned();
                $table->string('code')->unique();
                $table->string('name')->nullable();
                $table->integer('billing_mode_id')->unsigned();
                $table->integer('transit_days')->unsigned()->default(1);
                $table->boolean('enabled')->default(true);
                $table->timestamps();

                $table->foreign('origin_location_id')->references('id')->on('locations')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('destination_location_id')->references('id')->on('locations')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('service_type_id')->references('id')->on('service_types')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('billing_mode_id')->references('id')->on('billing_modes')->onUpdate('cascade')->onDelete('cascade');
            });

            Schema::create('delivery_route_service', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('delivery_route_id')->unsigned()->nullable();
                $table->integer('service_id')->unsigned();
                $table->boolean('default')->default(false);
                $table->timestamps();
            });

            Schema::create('tariffs', function (Blueprint $table) {
                $table->increments('id');
                $table->date('valid_from')->nullable();
                $table->date('valid_to')->nullable();
                $table->boolean('enabled')->default(true);
                $table->decimal('tier');
                $table->timestamps();
            });

            Schema::create('tariff_templates', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('service_id')->unsigned();
                $table->date('valid_from')->nullable();
                $table->date('valid_to')->nullable();
                $table->boolean('enabled')->default(true);
                $table->timestamps();

                $table->foreign('service_id')->references('id')->on('services')->onUpdate('cascade')->onDelete('cascade');
            });

            Schema::table('agreements', function (Blueprint $table) {
                $table->string('type')->change();
            });

//            DB::statement("ALTER TABLE agreements CHANGE COLUMN type type VARCHAR(255)");

            Schema::create('new_agreements', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('client_id')->unsigned();
                $table->integer('service_id')->unsigned();
                $table->integer('tariff_id')->unsigned()->nullable();
                $table->boolean('enabled')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('service_id')->references('id')->on('services')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('client_id')->references('id')->on('clients')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('tariff_id')->references('id')->on('tariffs')->onUpdate('cascade')->onDelete('cascade');
            });

            Schema::create('new_legs', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('delivery_route_id')->unsigned();
                $table->integer('provider_service_id')->unsigned();
                $table->integer('position')->unsigned()->default(1);
                $table->boolean('controlled')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('delivery_route_id')->references('id')->on('delivery_routes')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('provider_service_id')->references('id')->on('provider_services')->onUpdate('cascade')->onDelete('cascade');
            });

            Schema::table('legs', function (Blueprint $table) {
                $table->renameColumn('service_type_id', 'provider_service_id');
                $table->dropForeign('legs_service_type_id_foreign');
                $table->foreign('provider_service_id')->references('id')->on('provider_services')->onUpdate('cascade')->onDelete('cascade');
//                $table->dropForeign('legs_agreement_id_foreign');
//
                $table->dropIndex('legs_agreement_id_service_type_id_unique');
            });

            Schema::table('packages', function (Blueprint $table) {
                $table->integer('delivery_route_id')->unsigned()->nullable()->after('agreement_id');
                $table->integer('new_leg_id')->unsigned()->nullable()->after('bag_id');
                $table->integer('new_agreement_id')->unsigned()->nullable()->after('agreement_id');

                $table->foreign('delivery_route_id')->references('id')->on('delivery_routes')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('new_leg_id')->references('id')->on('new_legs')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('new_agreement_id')->references('id')->on('new_agreements')->onUpdate('cascade')->onDelete('cascade');
            });

            Schema::create('event_codes', function (Blueprint $table) {
                $table->increments('id');
                $table->string('key', 20);
                $table->string('description');
                $table->integer('position')->unsigned()->default(1);
                $table->timestamps();
            });

            Schema::create('events', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('package_id')->unsigned();
                $table->integer('event_code_id')->unsigned();
                $table->integer('last_checkpoint_id')->unsigned();
                $table->timestamps();

                $table->foreign('package_id')->references('id')->on('packages')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('event_code_id')->references('id')->on('event_codes')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('last_checkpoint_id')->references('id')->on('checkpoints')->onUpdate('cascade')->onDelete('cascade');
            });

            Schema::create('checkpoint_code_event_code', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('checkpoint_code_id')->unsigned();
                $table->integer('event_code_id')->unsigned();

                $table->foreign('checkpoint_code_id')->references('id')->on('checkpoint_codes')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('event_code_id')->references('id')->on('event_codes')->onUpdate('cascade')->onDelete('cascade');
            });

            Schema::table('checkpoint_codes', function (Blueprint $table) {
                $table->boolean('exceptional')->default(false)->after('virtual');
            });


            // created_at and updated_at
            $now = Carbon::now()->toDateTimeString();

            // Add Service Types
            // ------------------------------------------------------------------------------------------------

            logger("Create service types");

            $service_types = [
                ['id' => 1, 'key' => 'standard', 'description' => 'Standard'],
                ['id' => 2, 'key' => 'registered', 'description' => 'Registered'],
                ['id' => 3, 'key' => 'priority', 'description' => 'Priority']
            ];

            foreach ($service_types as $service_type) {
                DB::table('service_types')->insert([
                    'id'       => $service_type['id'],
                    'key'       => $service_type['key'],
                    'description' => $service_type['description'],
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ]);
            }


            // Format Agreements to new services
            // ------------------------------------------------------------------------------------------------

            $formatted_agreements = [
                ['country' => 'CL', 'country_id' => 44, 'service_type' => 'registered', 'transit_days' => 30],
                ['country' => 'CO', 'country_id' => 48, 'service_type' => 'registered', 'transit_days' => 30],
                ['country' => 'CO', 'country_id' => 48, 'service_type' => 'priority', 'transit_days' => 15],
                ['country' => 'PE', 'country_id' => 172, 'service_type' => 'registered', 'transit_days' => 30],
                ['country' => 'MX', 'country_id' => 142, 'service_type' => 'registered', 'transit_days' => 30],
                ['country' => 'MX', 'country_id' => 142, 'service_type' => 'priority', 'transit_days' => 15],
                ['country' => 'BR', 'country_id' => 31, 'service_type' => 'standard', 'transit_days' => 40],
                ['country' => 'BR', 'country_id' => 31, 'service_type' => 'registered', 'transit_days' => 30],
                ['country' => 'BR', 'country_id' => 31, 'service_type' => 'priority', 'transit_days' => 15],
                ['country' => 'EC', 'country_id' => 63, 'service_type' => 'registered', 'transit_days' => 30]
            ];

            DB::table('agreements')
                ->where('type', 'spregistered')
                ->update(['type' => 'registered']);

            foreach ($formatted_agreements as $formatted_agreement) {
                DB::table('agreements')
                    ->where('type', $formatted_agreement['service_type'])
                    ->where('country_id', $formatted_agreement['country_id'])
                    ->update(['transit_days' => $formatted_agreement['transit_days']]);
            }

            // Generate Billing Modes
            // ------------------------------------------------------------------------------------------------

            logger("Create billing modes");

            $billing_mode_real_weight_id = DB::table('billing_modes')->insertGetId([
                'key'         => 'real_weight',
                'description' => 'Real Weight',
                'created_at'  => $now,
                'updated_at'  => $now
            ]);
            $billing_mode_volumetric_weight_id = DB::table('billing_modes')->insertGetId([
                'key'         => 'volumetric_weight',
                'description' => 'Volumetric Weight',
                'created_at'  => $now,
                'updated_at'  => $now
            ]);

            //      Create MEXPOST Provider
            //      ------------------------------------------------------------------------------------------------

            logger("Create Mexpost provider");
            
            /** @var CountryRepository $countryRepository */
            $countryRepository = app(CountryRepository::class);
            $mexico_country = $countryRepository->getByCode('MX');

            /** @var TimezoneRepository $timezoneRepository */
            $timezoneRepository = app(TimezoneRepository::class);
            $mexico_timezone = $timezoneRepository->getByNameAndDescription('America/Mexico_City', '(UTC-06:00) Mexico City');

            /** @var ProviderRepository $providerRepository */
            $providerRepository = app(ProviderRepository::class);

            /** @var ProviderServiceRepository $providerServiceRepository */
            $providerServiceRepository = app(ProviderServiceRepository::class);

            /** @var CheckpointCodeRepository $checkpointCodeRepository */
            $checkpointCodeRepository = app(CheckpointCodeRepository::class);

            $mexpost_provider = $providerRepository->create([
                'country_id'  => $mexico_country->id,
                'name'        => 'Mexpost',
                'code'        => 'PR4938',
                'timezone_id' => $mexico_timezone->id
            ]);

            $mexpost_checkpoint_codes = DB::table('checkpoint_codes')
                ->join('providers', 'providers.id', '=', 'checkpoint_codes.provider_id')
                ->select([
                    'checkpoint_codes.provider_id',
                    'checkpoint_codes.classification_id',
                    'checkpoint_codes.key as checkpoint_code_key',
                    'checkpoint_codes.type as checkpoint_code_type',
                    'checkpoint_codes.code as checkpoint_code_code',
                    'checkpoint_codes.description',
                    'checkpoint_codes.category',
                    'checkpoint_codes.description_en',
                    'checkpoint_codes.delivered',
                    'checkpoint_codes.returned',
                    'checkpoint_codes.canceled',
                    'checkpoint_codes.stalled',
                    'checkpoint_codes.returning as checkpoint_code_returning',
                    'checkpoint_codes.clockstop as checkpoint_code_clockstop',
                    'checkpoint_codes.virtual as checkpoint_code_virtual',
                ])
                ->where('providers.code', 'PR4937')
                ->get()
                ->toArray();

            foreach ($mexpost_checkpoint_codes as $mexpost_checkpoint_code) {
                $mexpost_checkpoint_code = (array)$mexpost_checkpoint_code;

                $mexpost_checkpoint_code['provider_id'] = $mexpost_provider->id;
                $mexpost_checkpoint_code['checkpoint_code_key'] = 'MP-' . $mexpost_checkpoint_code['checkpoint_code_key'];

                DB::table('checkpoint_codes')->insert([
                    'provider_id'       => $mexpost_checkpoint_code['provider_id'],
                    'classification_id' => $mexpost_checkpoint_code['classification_id'],
                    'key'               => $mexpost_checkpoint_code['checkpoint_code_key'],
                    'type'              => $mexpost_checkpoint_code['checkpoint_code_type'],
                    'code'              => $mexpost_checkpoint_code['checkpoint_code_code'],
                    'description'       => $mexpost_checkpoint_code['description'],
                    'category'          => $mexpost_checkpoint_code['category'],
                    'description_en'    => $mexpost_checkpoint_code['description_en'],
                    'delivered'         => $mexpost_checkpoint_code['delivered'],
                    'returned'          => $mexpost_checkpoint_code['returned'],
                    'canceled'          => $mexpost_checkpoint_code['canceled'],
                    'stalled'           => $mexpost_checkpoint_code['stalled'],
                    'returning'         => $mexpost_checkpoint_code['checkpoint_code_returning'],
                    'clockstop'         => $mexpost_checkpoint_code['checkpoint_code_clockstop'],
                    'virtual'           => $mexpost_checkpoint_code['checkpoint_code_virtual'],
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ]);
            }

            $mexpost_provider_service = $providerServiceRepository->getByName('MEXPost');
            $correos_mexico_first_checkpoint = $checkpointCodeRepository->getById($mexpost_provider_service->first_checkpoint_code_id);
            $mexpost_first_checkpoint = $checkpointCodeRepository->search([
                'provider_id' => $mexpost_provider->id,
                'key'         => "MP-" . $correos_mexico_first_checkpoint->key,
                'description' => $correos_mexico_first_checkpoint->description
            ])->first();

            $new_mexpost_service_type_id = DB::table('provider_services')->insertGetId([
                'provider_id'              => $mexpost_provider->id,
                'provider_service_type_id' => $mexpost_provider_service->provider_service_type_id,
                'name'                     => 'Mexpost Distribution',
                'transit_days'             => $mexpost_provider_service->transit_days,
                'type'                     => $mexpost_provider_service->type,
                'service'                  => $mexpost_provider_service->service,
                'first_checkpoint_code_id' => $mexpost_first_checkpoint->id,
                'created_at'               => $now,
                'updated_at'               => $now,
            ]);

            $correos_mexico_provider = $providerRepository->getByCode('PR4937');

            $mexpost_legs = DB::table('legs')
                ->select('legs.id')
                ->join('agreements', 'agreements.id', '=', 'legs.agreement_id')
                ->join('provider_services', 'provider_services.id', '=', 'legs.provider_service_id')
                ->where('agreements.country_id', $mexico_country->id)
                ->where('agreements.type', 'priority')
                ->where('provider_services.provider_id', $correos_mexico_provider->id)
                ->get()
                ->toArray();

            foreach ($mexpost_legs as $mexpost_leg) {
                $mexpost_leg = (array)$mexpost_leg;

                DB::table('legs')->where('id', $mexpost_leg['id'])->update(['provider_service_id' => $new_mexpost_service_type_id]);
            }

            // Generate Services
            // ------------------------------------------------------------------------------------------------

            logger("Starting to create services");
            
            $agreements = DB::table('agreements')
                ->select([
                    'agreements.type',
                    'agreements.id',
                    'agreements.client_id as client_id',
                    'clients.country_id as origin_country_id',
                    'agreements.country_id as destination_country_id',
                    'providers.name as provider_name',
                    'agreements.transit_days as transit_days',
                    'origin_country.code as origin_country_code',
                    'destination_country.code as destination_country_code',
                    'agreements.type as service_type'
                ])
                ->join('legs', 'legs.agreement_id', '=', 'agreements.id')
                ->join('provider_services', 'provider_services.id', '=', 'legs.provider_service_id')
                ->join('providers', 'providers.id', '=', 'provider_services.provider_id')
                ->join('clients', 'clients.id', '=', 'agreements.client_id')
                ->join('countries as origin_country', 'origin_country.id', '=', 'clients.country_id')
                ->join('countries as destination_country', 'destination_country.id', '=', 'agreements.country_id')
                ->where('provider_services.type', 'last_mile')
                ->get()
                ->toArray();

            foreach ($agreements as $agreement) {
                $agreement = (array)$agreement;

                // Search or create origin location
                if ($origin_location = DB::table('locations')->select('id')->where('country_id', $agreement['origin_country_id'])->first()) {
                    $origin_location_id = $origin_location->id;
                } else {
                    $origin_location_id = DB::table('locations')->insertGetId([
                        'country_id' => $agreement['origin_country_id'],
                        'type'       => 'country',
                        'code'       => $agreement['origin_country_code'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                };

                // Search or create destination location
                if ($destination_location = DB::table('locations')->select('id')->where('country_id', $agreement['destination_country_id'])->first()) {
                    $destination_location_id = $destination_location->id;
                } else {
                    $destination_location_id = DB::table('locations')->insertGetId([
                        'country_id' => $agreement['destination_country_id'],
                        'type'       => 'country',
                        'code'       => $agreement['destination_country_code'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                };

                // Search or create service with agreement values.
                $services = DB::table('services')
                    ->select('id')
                    ->where('origin_location_id', $origin_location_id)
                    ->where('destination_location_id', $destination_location_id)
                    ->where('transit_days', $agreement['transit_days'])
                    ->get();

                if ($services->isEmpty()) {
                    // Create service
                    $formatted_transit_days = sprintf("%04d", $agreement['transit_days']);
                    $service_code = "{$agreement['origin_country_code']}{$formatted_transit_days}{$agreement['destination_country_code']}";
                    if ($agreement['service_type'] == 'priority') {
                        $billing_mode_id = $billing_mode_volumetric_weight_id;
                    } else {
                        $billing_mode_id = $billing_mode_real_weight_id;
                    }

                    $service_type = DB::table('service_types')
                        ->select('id')
                        ->where('key', $agreement['service_type'])
                        ->first();
                    
                    $service_type_id = null;
                    if ($service_type) {
                        $service_type_id = $service_type->id;
                    }

                    $service_id = DB::table('services')->insertGetId([
                        'origin_location_id'      => $origin_location_id,
                        'destination_location_id' => $destination_location_id,
                        'service_type_id'         => $service_type_id,
                        'transit_days'            => $agreement['transit_days'],
                        'code'                    => $service_code,
                        'billing_mode_id'         => $billing_mode_id,
                        'created_at'              => $now,
                        'updated_at'              => $now,
                    ]);

                    DB::table('delivery_route_service')->insertGetId([
                        'delivery_route_id' => null,
                        'service_id'        => $service_id,
                        'default'           => true,
                        'created_at'        => $now,
                        'updated_at'        => $now,
                    ]);
                } else {
                    $service_id = $services->first()->id;
                }

                $new_agreements = DB::table('new_agreements')
                    ->select('id')
                    ->where('service_id', $service_id)
                    ->where('client_id', $agreement['client_id'])
                    ->get();

                if ($new_agreements->isEmpty()) {
                    // Create new agreement
                    $new_agreement_id = DB::table('new_agreements')->insertGetId([
                        'service_id' => $service_id,
                        'client_id'  => $agreement['client_id'],
                        'created_at' => $now,
                        'updated_at' => $now
                    ]);
                } else {
                    $new_agreement_id = $new_agreements->first()->id;
                }

                // Prepare Update packages with new agreement sentences
                DB::table('packages')->where('agreement_id', $agreement['id'])->update(['new_agreement_id' => $new_agreement_id]);
//                $update_agreement_id_in_packages[] = "UPDATE packages SET packages.new_agreement_id = {$new_agreement_id} WHERE packages.agreement_id = {$agreement['id']}";
            }


            // Create Delivery Routes
            // ------------------------------------------------------------------------------------------------

            logger("Starting to create delivery routes");

            $agreements = DB::table('agreements')
                ->select([
                    'agreements.id',
                    'clients.country_id as origin_country_id',
                    'agreements.country_id as destination_country_id',
                    'providers.name as provider_name',
                    'agreements.transit_days as transit_days'
                ])
                ->join('legs', 'legs.agreement_id', '=', 'agreements.id')
                ->join('provider_services', 'provider_services.id', '=', 'legs.provider_service_id')
                ->join('providers', 'providers.id', '=', 'provider_services.provider_id')
                ->join('clients', 'clients.id', '=', 'agreements.client_id')
                ->where('provider_services.type', 'last_mile')
                ->get()
                ->toArray();

            foreach ($agreements as $agreement) {
                $agreement = (array)$agreement;
                $delivery_routes_found = [];
                $first = true;
                $new_new_legs = [];
                $exists = true;

                // Search or create origin location
                if ($origin_location = DB::table('locations')->select('id')->where('country_id', $agreement['origin_country_id'])->first()) {
                    $origin_location_id = $origin_location->id;
                } else {
                    $origin_location_id = DB::table('locations')->insertGetId([
                        'country_id' => $agreement['origin_country_id'],
                        'type'       => 'country',
                        'code'       => $agreement['origin_country_code'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                };

                // Search or create destination location
                if ($destination_location = DB::table('locations')->select('id')->where('country_id', $agreement['destination_country_id'])->first()) {
                    $destination_location_id = $destination_location->id;
                } else {
                    $destination_location_id = DB::table('locations')->insertGetId([
                        'country_id' => $agreement['destination_country_id'],
                        'type'       => 'country',
                        'code'       => $agreement['destination_country_code'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                };

                $legs = DB::table('legs')->select(['id', 'provider_service_id', 'transit_days', 'position', 'controlled'])
                    ->where('legs.agreement_id', $agreement['id'])
                    ->whereNull('legs.deleted_at')
                    ->get()
                    ->toArray();

//                logger("legs to agreement");
//                logger($legs);
//
                foreach ($legs as $leg) {
                    $leg = (array)$leg;
                    if ($exists) {
                        // Search if delivery route exists
                        $delivery_routes = DB::table('new_legs')
                            ->select('new_legs.delivery_route_id')
                            ->join('delivery_routes', 'delivery_routes.id', '=', 'new_legs.delivery_route_id')
                            ->where('new_legs.provider_service_id', $leg['provider_service_id'])
                            ->where('new_legs.position', $leg['position'])
                            ->where('new_legs.controlled', $leg['controlled'])
                            ->where('delivery_routes.origin_location_id', $origin_location_id)
                            ->where('delivery_routes.destination_location_id', $destination_location_id)
                            ->get()
                            ->pluck('delivery_route_id')
                            ->toArray();

                        if (empty($delivery_routes)) {
                            $exists = false;
                        } else {
                            if ($first) {
                                $delivery_routes_found = $delivery_routes;
                                $first = false;
                            } else {
                                $new_found = [];
                                foreach ($delivery_routes as $dl) {
                                    if (in_array($dl, $delivery_routes_found)) {
                                        $new_found[] = $dl;
                                    }
                                }
                                $delivery_routes_found = $new_found;
                                if (empty($delivery_routes_found)) {
                                    $exists = false;
                                }
                            }
                        }
                    }

                    $new_new_legs[] = [
                        'provider_service_id' => $leg['provider_service_id'],
                        'position'            => $leg['position'],
                        'controlled'          => $leg['controlled'],
                        'leg_id'              => $leg['id']
                    ];
                }

                if (!$exists) {
                    // Creo delivery route
                    $delivery_route_new_id = DB::table('delivery_routes')->insertGetId([
                        'origin_location_id'      => $origin_location_id,
                        'destination_location_id' => $destination_location_id,
                        'label'                   => $agreement['provider_name'],
                        'created_at'              => $now,
                        'updated_at'              => $now,
                    ]);

                    $new_delivery_route_id = $delivery_route_new_id;
                    foreach ($new_new_legs as $new_leg) {
                        $new_new_leg_id = DB::table('new_legs')->insertGetId([
                            'delivery_route_id'   => $delivery_route_new_id,
                            'provider_service_id' => $new_leg['provider_service_id'],
                            'position'            => $new_leg['position'],
                            'controlled'          => $new_leg['controlled'],
                            'created_at'          => $now,
                            'updated_at'          => $now,
                        ]);

                        DB::table('packages')
                            ->where('leg_id', $new_leg['leg_id'])
                            ->update(['new_leg_id' => $new_new_leg_id]);
                    }
                } else {
//                    logger("existe ruta");
                    // Si encontro mas de una ruta, pregunto por la cantidad de piernas
                    if (count($delivery_routes_found) > 1) {
//                        logger("mas de una ruta");
                        foreach ($delivery_routes_found as $dl) {
                            $count = DB::table('new_legs')->where('delivery_route_id', $dl)->count();
                            if ($count == count($new_new_legs)) {
                                $new_delivery_route_id = $dl;
                                break;
                            }
                        }
                    } else {
//                        logger("una ruta");
                        $new_delivery_route_id = $delivery_routes_found[0];
                    }

                    // Busco las piernas de esa ruta y las actualizo en packages
                    foreach ($legs as $leg) {
                        $leg = (array)$leg;

                        $dl_provider_services = DB::table('new_legs')
                            ->select('id')
                            ->where('provider_service_id', $leg['provider_service_id'])
                            ->where('position', $leg['position'])
                            ->where('controlled', $leg['controlled'])
                            ->where('delivery_route_id', $new_delivery_route_id)
                            ->get();

                        if ($dl_provider_services->isNotEmpty()) {
                            $dl_provider_service_id = $dl_provider_services->first()->id;

                            DB::table('packages')->where('leg_id', $leg['id'])->update(['new_leg_id' => $dl_provider_service_id]);
                        }
                    }
                }

                DB::table('packages')->where('agreement_id', $agreement['id'])->update(['delivery_route_id' => $new_delivery_route_id]);

                // CARGAR RUTA DEFAULT EN LOS SERVICIOS CORRESPONDIENTES
                $services = DB::table('services')
                    ->select('id')
                    ->where('origin_location_id', $origin_location_id)
                    ->where('destination_location_id', $destination_location_id)
                    ->where('transit_days', $agreement['transit_days'])
                    ->get();

                foreach ($services as $service) {
                    $service = (array)$service;

                    DB::table('delivery_route_service')
                        ->where('service_id', $service['id'])
                        ->update(['delivery_route_id' => $new_delivery_route_id]);
                }
            }

            // Create Event codes
            // ------------------------------------------------------------------------------------------------

            logger("Starting to create event codes");

            $events = [
                ['id' => 1, 'description' => 'Label Generated', 'key' => 'ML-100', 'position' => 1],
                ['id' => 2, 'description' => 'Received at origin warehouse', 'key' => 'ML-200', 'position' => 2],
                ['id' => 3, 'description' => 'Processing in origin warehouse', 'key' => 'ML-201', 'position' => 3],
                ['id' => 4, 'description' => 'Package ready to be shipped', 'key' => 'ML-202', 'position' => 4],
                ['id' => 5, 'description' => 'In transit to the airport', 'key' => 'ML-300', 'position' => 5],
                ['id' => 6, 'description' => 'Origin customs', 'key' => 'ML-301', 'position' => 6],
                ['id' => 7, 'description' => 'In flight', 'key' => 'ML-302', 'position' => 7],
                ['id' => 8, 'description' => 'Transfer airport', 'key' => 'ML-303', 'position' => 8],
                ['id' => 9, 'description' => 'Arrived to destination Country', 'key' => 'ML-304', 'position' => 9],
                ['id' => 10, 'description' => 'Received in customs', 'key' => 'ML-400', 'position' => 10],
                ['id' => 11, 'description' => 'Held by customs', 'key' => 'ML-401', 'position' => 11],
                ['id' => 12, 'description' => 'Released by customs', 'key' => 'ML-402', 'position' => 12],
                ['id' => 13, 'description' => 'Classification and dispatch to destination office', 'key' => 'ML-500', 'position' => 13],
                ['id' => 14, 'description' => 'In destination office', 'key' => 'ML-501', 'position' => 14],
                ['id' => 15, 'description' => 'In distribution with postman', 'key' => 'ML-502', 'position' => 15],
                ['id' => 16, 'description' => 'Delivery attempt', 'key' => 'ML-503', 'position' => 16],
                ['id' => 17, 'description' => 'Delivered', 'key' => 'ML-504', 'position' => 17],
                ['id' => 18, 'description' => 'Ready for pick up', 'key' => 'ML-505', 'position' => 18],
                ['id' => 19, 'description' => 'Returning to local warehouse', 'key' => 'ML-700', 'position' => 19],
                ['id' => 20, 'description' => 'Returned to local warehouse', 'key' => 'ML-701', 'position' => 20],
                ['id' => 21, 'description' => 'Stolen package', 'key' => 'ML-600', 'position' => 21],
                ['id' => 22, 'description' => 'Address rectification', 'key' => 'ML-601', 'position' => 22],
                ['id' => 23, 'description' => 'Customs taxes prepayment required', 'key' => 'ML-602', 'position' => 23],
                ['id' => 24, 'description' => 'Held until customs taxes are payed', 'key' => 'ML-603', 'position' => 24],
                ['id' => 25, 'description' => 'Taxes payed', 'key' => 'ML-604', 'position' => 25],
                ['id' => 26, 'description' => 'Cancelled', 'key' => 'ML-605', 'position' => 26],
                ['id' => 27, 'description' => 'Held / Delayed', 'key' => 'ML-606', 'position' => 27],

            ];

            $checkpoint_code_events = [
                ['checkpoint_code_id' => 1, 'event_code_id' => 10],
                ['checkpoint_code_id' => 10, 'event_code_id' => 19],
                ['checkpoint_code_id' => 100, 'event_code_id' => 16],
                ['checkpoint_code_id' => 11, 'event_code_id' => 19],
                ['checkpoint_code_id' => 12, 'event_code_id' => 19],
                ['checkpoint_code_id' => 13, 'event_code_id' => 19],
                ['checkpoint_code_id' => 14, 'event_code_id' => 17],
                ['checkpoint_code_id' => 15, 'event_code_id' => 18],
                ['checkpoint_code_id' => 18, 'event_code_id' => 16],
                ['checkpoint_code_id' => 19, 'event_code_id' => 14],
                ['checkpoint_code_id' => 2, 'event_code_id' => 10],
                ['checkpoint_code_id' => 20, 'event_code_id' => 15],
                ['checkpoint_code_id' => 21, 'event_code_id' => 15],
                ['checkpoint_code_id' => 22, 'event_code_id' => 13],
                ['checkpoint_code_id' => 23, 'event_code_id' => 16],
                ['checkpoint_code_id' => 24, 'event_code_id' => 13],
                ['checkpoint_code_id' => 25, 'event_code_id' => 13],
                ['checkpoint_code_id' => 26, 'event_code_id' => 10],
                ['checkpoint_code_id' => 27, 'event_code_id' => 27],
                ['checkpoint_code_id' => 28, 'event_code_id' => 11],
                ['checkpoint_code_id' => 29, 'event_code_id' => 18],
                ['checkpoint_code_id' => 3, 'event_code_id' => 10],
                ['checkpoint_code_id' => 30, 'event_code_id' => 10],
                ['checkpoint_code_id' => 31, 'event_code_id' => 14],
                ['checkpoint_code_id' => 32, 'event_code_id' => 14],
                ['checkpoint_code_id' => 33, 'event_code_id' => 10],
                ['checkpoint_code_id' => 34, 'event_code_id' => 26],
                ['checkpoint_code_id' => 35, 'event_code_id' => 26],
                ['checkpoint_code_id' => 36, 'event_code_id' => 13],
                ['checkpoint_code_id' => 37, 'event_code_id' => 26],
                ['checkpoint_code_id' => 38, 'event_code_id' => 26],
                ['checkpoint_code_id' => 39, 'event_code_id' => 26],
                ['checkpoint_code_id' => 4, 'event_code_id' => 14],
                ['checkpoint_code_id' => 40, 'event_code_id' => 17],
                ['checkpoint_code_id' => 41, 'event_code_id' => 21],
                ['checkpoint_code_id' => 42, 'event_code_id' => 26],
                ['checkpoint_code_id' => 4345, 'event_code_id' => 10],
                ['checkpoint_code_id' => 4346, 'event_code_id' => 12],
                ['checkpoint_code_id' => 4348, 'event_code_id' => 26],
                ['checkpoint_code_id' => 4352, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4353, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4354, 'event_code_id' => 19],
                ['checkpoint_code_id' => 4355, 'event_code_id' => 18],
                ['checkpoint_code_id' => 4358, 'event_code_id' => 15],
                ['checkpoint_code_id' => 4359, 'event_code_id' => 19],
                ['checkpoint_code_id' => 4360, 'event_code_id' => 19],
                ['checkpoint_code_id' => 4361, 'event_code_id' => 17],
                ['checkpoint_code_id' => 4368, 'event_code_id' => 26],
                ['checkpoint_code_id' => 4370, 'event_code_id' => 26],
                ['checkpoint_code_id' => 4372, 'event_code_id' => 19],
                ['checkpoint_code_id' => 4373, 'event_code_id' => 17],
                ['checkpoint_code_id' => 4374, 'event_code_id' => 21],
                ['checkpoint_code_id' => 4375, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4376, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4377, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4378, 'event_code_id' => 9],
                ['checkpoint_code_id' => 4379, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4380, 'event_code_id' => 9],
                ['checkpoint_code_id' => 4381, 'event_code_id'  => 4],
                ['checkpoint_code_id' => 4382, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4383, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4384, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4385, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4386, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4387, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4388, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4389, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 4390, 'event_code_id' => 10],
                ['checkpoint_code_id' => 4393, 'event_code_id' => 20],
                ['checkpoint_code_id' => 4394, 'event_code_id' => 21],
                ['checkpoint_code_id' => 4395, 'event_code_id' => 21],
                ['checkpoint_code_id' => 4396, 'event_code_id' => 26],
                ['checkpoint_code_id' => 4397, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4398, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4399, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4400, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4401, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4402, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4403, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4404, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4405, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4406, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4407, 'event_code_id' => 9],
                ['checkpoint_code_id' => 4408, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4409, 'event_code_id' => 9],
                ['checkpoint_code_id' => 4410, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4411, 'event_code_id' => 9],
                ['checkpoint_code_id' => 4412, 'event_code_id' => 20],
                ['checkpoint_code_id' => 4413, 'event_code_id' => 13],
                ['checkpoint_code_id' => 4414, 'event_code_id' => 26],
                ['checkpoint_code_id' => 4427, 'event_code_id' => 18],
                ['checkpoint_code_id' => 4432, 'event_code_id' => 19],
                ['checkpoint_code_id' => 4433, 'event_code_id' => 19],
                ['checkpoint_code_id' => 4434, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4435, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4436, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4437, 'event_code_id' => 9],
                ['checkpoint_code_id' => 4438, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4439, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4444, 'event_code_id' => 17],
                ['checkpoint_code_id' => 4445, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4446, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4447, 'event_code_id' => 19],
                ['checkpoint_code_id' => 4448, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4449, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4450, 'event_code_id' => 19],
                ['checkpoint_code_id' => 4451, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4452, 'event_code_id' => 19],
                ['checkpoint_code_id' => 4453, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4454, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4455, 'event_code_id' => 21],
                ['checkpoint_code_id' => 4456, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4457, 'event_code_id' => 19],
                ['checkpoint_code_id' => 4458, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4459, 'event_code_id' => 19],
                ['checkpoint_code_id' => 4460, 'event_code_id' => 19],
                ['checkpoint_code_id' => 4461, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4462, 'event_code_id' => 21],
                ['checkpoint_code_id' => 4463, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4464, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4465, 'event_code_id' => 19],
                ['checkpoint_code_id' => 4466, 'event_code_id' => 19],
                ['checkpoint_code_id' => 4467, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4468, 'event_code_id' => 19],
                ['checkpoint_code_id' => 4469, 'event_code_id' => 17],
                ['checkpoint_code_id' => 4470, 'event_code_id' => 19],
                ['checkpoint_code_id' => 4473, 'event_code_id' => 19],
                ['checkpoint_code_id' => 4475, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4480, 'event_code_id' => 26],
                ['checkpoint_code_id' => 4481, 'event_code_id' => 27],
                ['checkpoint_code_id' => 4482, 'event_code_id' => 27],
                ['checkpoint_code_id' => 4487, 'event_code_id' => 20],
                ['checkpoint_code_id' => 4489, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4490, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4491, 'event_code_id' => 19],
                ['checkpoint_code_id' => 4492, 'event_code_id' => 26],
                ['checkpoint_code_id' => 4493, 'event_code_id' => 14],
                ['checkpoint_code_id' => 4494, 'event_code_id' => 15],
                ['checkpoint_code_id' => 4495, 'event_code_id' => 15],
                ['checkpoint_code_id' => 4496, 'event_code_id' => 14],
                ['checkpoint_code_id' => 4498, 'event_code_id' => 17],
                ['checkpoint_code_id' => 4499, 'event_code_id' => 17],
                ['checkpoint_code_id' => 4500, 'event_code_id' => 20],
                ['checkpoint_code_id' => 4502, 'event_code_id'  => 5],
                ['checkpoint_code_id' => 4503, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4504, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4505, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4506, 'event_code_id'  => 9],
                ['checkpoint_code_id' => 4511, 'event_code_id' => 15],
                ['checkpoint_code_id' => 4512, 'event_code_id' => 17],
                ['checkpoint_code_id' => 4557, 'event_code_id' => 11],
                ['checkpoint_code_id' => 4570, 'event_code_id' => 15],
                ['checkpoint_code_id' => 4571, 'event_code_id' => 15],
                ['checkpoint_code_id' => 4572, 'event_code_id' => 14],
                ['checkpoint_code_id' => 4573, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4574, 'event_code_id' => 21],
                ['checkpoint_code_id' => 4575, 'event_code_id' => 26],
                ['checkpoint_code_id' => 4576, 'event_code_id' => 18],
                ['checkpoint_code_id' => 4577, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4578, 'event_code_id' => 26],
                ['checkpoint_code_id' => 4579, 'event_code_id' => 25],
                ['checkpoint_code_id' => 4580, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4581, 'event_code_id' => 21],
                ['checkpoint_code_id' => 4583, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4584, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4588, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4593, 'event_code_id' => 14],
                ['checkpoint_code_id' => 4594, 'event_code_id' => 11],
                ['checkpoint_code_id' => 4595, 'event_code_id' => 14],
                ['checkpoint_code_id' => 4596, 'event_code_id' => 14],
                ['checkpoint_code_id' => 4597, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4598, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4599, 'event_code_id' => 18],
                ['checkpoint_code_id' => 4609, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4612, 'event_code_id' => 21],
                ['checkpoint_code_id' => 4613, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4614, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4618, 'event_code_id' => 10],
                ['checkpoint_code_id' => 4619, 'event_code_id' => 10],
                ['checkpoint_code_id' => 4621, 'event_code_id' => 12],
                ['checkpoint_code_id' => 4622, 'event_code_id' => 26],
                ['checkpoint_code_id' => 4624, 'event_code_id' => 11],
                ['checkpoint_code_id' => 4625, 'event_code_id' => 11],
                ['checkpoint_code_id' => 4627, 'event_code_id' => 11],
                ['checkpoint_code_id' => 4629, 'event_code_id' => 11],
                ['checkpoint_code_id' => 4630, 'event_code_id' => 13],
                ['checkpoint_code_id' => 4631, 'event_code_id' => 17],
                ['checkpoint_code_id' => 4632, 'event_code_id'  => 5],
                ['checkpoint_code_id' => 4633, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4635, 'event_code_id'  => 9],
                ['checkpoint_code_id' => 4636, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4642, 'event_code_id' => 11],
                ['checkpoint_code_id' => 4644, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4645, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4646, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4647, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4648, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4649, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4650, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4651, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4652, 'event_code_id' => 12],
                ['checkpoint_code_id' => 4653, 'event_code_id' => 20],
                ['checkpoint_code_id' => 4654, 'event_code_id' => 11],
                ['checkpoint_code_id' => 4655, 'event_code_id' => 26],
                ['checkpoint_code_id' => 4660, 'event_code_id' => 14],
                ['checkpoint_code_id' => 4663, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4664, 'event_code_id' => 14],
                ['checkpoint_code_id' => 4665, 'event_code_id' => 21],
                ['checkpoint_code_id' => 4667, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4668, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4672, 'event_code_id' => 21],
                ['checkpoint_code_id' => 4676, 'event_code_id' => 14],
                ['checkpoint_code_id' => 4677, 'event_code_id' => 21],
                ['checkpoint_code_id' => 4678, 'event_code_id' => 18],
                ['checkpoint_code_id' => 4679, 'event_code_id' => 14],
                ['checkpoint_code_id' => 4680, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4681, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4682, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4683, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4684, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4685, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4686, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4687, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4691, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4692, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4697, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4698, 'event_code_id' => 26],
                ['checkpoint_code_id' => 4700, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4701, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4702, 'event_code_id' => 17],
                ['checkpoint_code_id' => 4703, 'event_code_id' => 15],
                ['checkpoint_code_id' => 4704, 'event_code_id' => 9],
                ['checkpoint_code_id' => 4705, 'event_code_id' => 14],
                ['checkpoint_code_id' => 4706, 'event_code_id' => 14],
                ['checkpoint_code_id' => 4707, 'event_code_id' => 11],
                ['checkpoint_code_id' => 4708, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4709, 'event_code_id' => 20],
                ['checkpoint_code_id' => 4710, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4711, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4712, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4713, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4714, 'event_code_id' => 14],
                ['checkpoint_code_id' => 4718, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4719, 'event_code_id' => 11],
                ['checkpoint_code_id' => 4721, 'event_code_id' => 1],
                ['checkpoint_code_id' => 4722, 'event_code_id' => 1],
                ['checkpoint_code_id' => 4724, 'event_code_id' => 14],
                ['checkpoint_code_id' => 4725, 'event_code_id' => 18],
                ['checkpoint_code_id' => 4726, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4727, 'event_code_id' => 2],
                ['checkpoint_code_id' => 4728, 'event_code_id' => 3],
                ['checkpoint_code_id' => 4729, 'event_code_id' => 3],
                ['checkpoint_code_id' => 4730, 'event_code_id' => 4],
                ['checkpoint_code_id' => 4733, 'event_code_id' => 19],
                ['checkpoint_code_id' => 4735, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4736, 'event_code_id' => 24],
                ['checkpoint_code_id' => 4738, 'event_code_id' => 13],
                ['checkpoint_code_id' => 4740, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4741, 'event_code_id' => 1],
                ['checkpoint_code_id' => 4743, 'event_code_id' => 21],
                ['checkpoint_code_id' => 4746, 'event_code_id' => 18],
                ['checkpoint_code_id' => 4750, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4751, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4752, 'event_code_id' => 21],
                ['checkpoint_code_id' => 4753, 'event_code_id' => 10],
                ['checkpoint_code_id' => 4754, 'event_code_id' => 12],
                ['checkpoint_code_id' => 4755, 'event_code_id' => 11],
                ['checkpoint_code_id' => 4756, 'event_code_id' => 10],
                ['checkpoint_code_id' => 4757, 'event_code_id' => 26],
                ['checkpoint_code_id' => 4758, 'event_code_id' => 11],
                ['checkpoint_code_id' => 4759, 'event_code_id' => 11],
                ['checkpoint_code_id' => 4760, 'event_code_id' => 11],
                ['checkpoint_code_id' => 4768, 'event_code_id' => 12],
                ['checkpoint_code_id' => 4769, 'event_code_id' => 14],
                ['checkpoint_code_id' => 4771, 'event_code_id' => 21],
                ['checkpoint_code_id' => 4772, 'event_code_id' => 21],
                ['checkpoint_code_id' => 4773, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4774, 'event_code_id' => 19],
                ['checkpoint_code_id' => 4775, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4777, 'event_code_id' => 18],
                ['checkpoint_code_id' => 4778, 'event_code_id'  => 5],
                ['checkpoint_code_id' => 4780, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4781, 'event_code_id' => 18],
                ['checkpoint_code_id' => 4782, 'event_code_id' => 21],
                ['checkpoint_code_id' => 4783, 'event_code_id'  => 11],
                ['checkpoint_code_id' => 4785, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4786, 'event_code_id' => 18],
                ['checkpoint_code_id' => 4787, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4791, 'event_code_id' => 13],
                ['checkpoint_code_id' => 4796, 'event_code_id' => 21],
                ['checkpoint_code_id' => 4797, 'event_code_id' => 14],
                ['checkpoint_code_id' => 4799, 'event_code_id' => 14],
                ['checkpoint_code_id' => 48, 'event_code_id' => 26],
                ['checkpoint_code_id' => 4800, 'event_code_id' => 1],
                ['checkpoint_code_id' => 4802, 'event_code_id' => 19],
                ['checkpoint_code_id' => 4807, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4811, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4812, 'event_code_id' => 26],
                ['checkpoint_code_id' => 4813, 'event_code_id' => 18],
                ['checkpoint_code_id' => 4814, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4816, 'event_code_id' => 22],
                ['checkpoint_code_id' => 4817, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4824, 'event_code_id' => 19],
                ['checkpoint_code_id' => 4826, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4827, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4828, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4829, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4830, 'event_code_id' => 9],
                ['checkpoint_code_id' => 4831, 'event_code_id' => 9],
                ['checkpoint_code_id' => 4835, 'event_code_id' => 20],
                ['checkpoint_code_id' => 4837, 'event_code_id' => 27],
                ['checkpoint_code_id' => 4838, 'event_code_id' => 27],
                ['checkpoint_code_id' => 4839, 'event_code_id' => 18],
                ['checkpoint_code_id' => 4841, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4842, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4843, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4844, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4845, 'event_code_id' => 17],
                ['checkpoint_code_id' => 4846, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4848, 'event_code_id' => 19],
                ['checkpoint_code_id' => 4852, 'event_code_id'  => 17],
                ['checkpoint_code_id' => 4862, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4863, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4865, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4866, 'event_code_id' => 26],
                ['checkpoint_code_id' => 4867, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4871, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4872, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4873, 'event_code_id' => 10],
                ['checkpoint_code_id' => 4874, 'event_code_id' => 26],
                ['checkpoint_code_id' => 4875, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4876, 'event_code_id' => 13],
                ['checkpoint_code_id' => 4879, 'event_code_id' => 21],
                ['checkpoint_code_id' => 4886, 'event_code_id' => 11],
                ['checkpoint_code_id' => 4887, 'event_code_id' => 14],
                ['checkpoint_code_id' => 4888, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4891, 'event_code_id' => 20],
                ['checkpoint_code_id' => 4892, 'event_code_id' => 21],
                ['checkpoint_code_id' => 4893, 'event_code_id' => 21],
                ['checkpoint_code_id' => 4899, 'event_code_id' => 27],
                ['checkpoint_code_id' => 49, 'event_code_id' => 26],
                ['checkpoint_code_id' => 4902, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4903, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4904, 'event_code_id' => 20],
                ['checkpoint_code_id' => 4905, 'event_code_id' => 19],
                ['checkpoint_code_id' => 4906, 'event_code_id'  => 5],
                ['checkpoint_code_id' => 4907, 'event_code_id' => 18],
                ['checkpoint_code_id' => 4918, 'event_code_id' => 26],
                ['checkpoint_code_id' => 4919, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4931, 'event_code_id' => 1],
                ['checkpoint_code_id' => 4932, 'event_code_id' => 11],
                ['checkpoint_code_id' => 4933, 'event_code_id' => 18],
                ['checkpoint_code_id' => 4938, 'event_code_id' => 20],
                ['checkpoint_code_id' => 4941, 'event_code_id' => 27],
                ['checkpoint_code_id' => 4945, 'event_code_id' => 21],
                ['checkpoint_code_id' => 4946, 'event_code_id' => 23],
                ['checkpoint_code_id' => 4947, 'event_code_id' => 24],
                ['checkpoint_code_id' => 4948, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4949, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4950, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4951, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4952, 'event_code_id' => 23],
                ['checkpoint_code_id' => 4953, 'event_code_id' => 12],
                ['checkpoint_code_id' => 4954, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4955, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4956, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4957, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4958, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4959, 'event_code_id' => 19],
                ['checkpoint_code_id' => 4960, 'event_code_id' => 18],
                ['checkpoint_code_id' => 4961, 'event_code_id' => 9],
                ['checkpoint_code_id' => 4962, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4963, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4964, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 4965, 'event_code_id' => 27],
                ['checkpoint_code_id' => 4966, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4967, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4968, 'event_code_id' => 26],
                ['checkpoint_code_id' => 4969, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4970, 'event_code_id' => 9],
                ['checkpoint_code_id' => 4971, 'event_code_id' => 9],
                ['checkpoint_code_id' => 4972, 'event_code_id' => 20],
                ['checkpoint_code_id' => 4973, 'event_code_id' => 19],
                ['checkpoint_code_id' => 4974, 'event_code_id' => 11],
                ['checkpoint_code_id' => 4975, 'event_code_id' => 18],
                ['checkpoint_code_id' => 4976, 'event_code_id' => 19],
                ['checkpoint_code_id' => 4977, 'event_code_id' => 10],
                ['checkpoint_code_id' => 4978, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4979, 'event_code_id' => 11],
                ['checkpoint_code_id' => 4980, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 4981, 'event_code_id' => 27],
                ['checkpoint_code_id' => 4982, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4983, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 4984, 'event_code_id' => 7],
                ['checkpoint_code_id' => 4985, 'event_code_id'  => 17],
                ['checkpoint_code_id' => 4986, 'event_code_id' => 18],
                ['checkpoint_code_id' => 4987, 'event_code_id' => 17],
                ['checkpoint_code_id' => 4988, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4989, 'event_code_id' => 18],
                ['checkpoint_code_id' => 4990, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4991, 'event_code_id' => 16],
                ['checkpoint_code_id' => 4992, 'event_code_id' => 27],
                ['checkpoint_code_id' => 4993, 'event_code_id' => 19],
                ['checkpoint_code_id' => 4994, 'event_code_id' => 14],
                ['checkpoint_code_id' => 4995, 'event_code_id' => 27],
                ['checkpoint_code_id' => 4996, 'event_code_id' => 26],
                ['checkpoint_code_id' => 4997, 'event_code_id' => 18],
                ['checkpoint_code_id' => 4998, 'event_code_id' => 17],
                ['checkpoint_code_id' => 4999, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 5, 'event_code_id' => 14],
                ['checkpoint_code_id' => 50, 'event_code_id' => 7],
                ['checkpoint_code_id' => 5000, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 5001, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5002, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5003, 'event_code_id' => 14],
                ['checkpoint_code_id' => 5004, 'event_code_id' => 17],
                ['checkpoint_code_id' => 5005, 'event_code_id' => 15],
                ['checkpoint_code_id' => 5006, 'event_code_id' => 17],
                ['checkpoint_code_id' => 5007, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5008, 'event_code_id' => 16],
                ['checkpoint_code_id' => 5009, 'event_code_id' => 16],
                ['checkpoint_code_id' => 5010, 'event_code_id' => 16],
                ['checkpoint_code_id' => 5011, 'event_code_id' => 16],
                ['checkpoint_code_id' => 5012, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5013, 'event_code_id' => 16],
                ['checkpoint_code_id' => 5014, 'event_code_id' => 16],
                ['checkpoint_code_id' => 5015, 'event_code_id' => 12],
                ['checkpoint_code_id' => 5016, 'event_code_id' => 17],
                ['checkpoint_code_id' => 5017, 'event_code_id' => 27],
                ['checkpoint_code_id' => 5018, 'event_code_id' => 27],
                ['checkpoint_code_id' => 5019, 'event_code_id' => 27],
                ['checkpoint_code_id' => 5020, 'event_code_id' => 21],
                ['checkpoint_code_id' => 5021, 'event_code_id' => 20],
                ['checkpoint_code_id' => 5022, 'event_code_id' => 16],
                ['checkpoint_code_id' => 5023, 'event_code_id' => 16],
                ['checkpoint_code_id' => 5024, 'event_code_id' => 14],
                ['checkpoint_code_id' => 5025, 'event_code_id' => 27],
                ['checkpoint_code_id' => 5026, 'event_code_id' => 17],
                ['checkpoint_code_id' => 5027, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5028, 'event_code_id' => 11],
                ['checkpoint_code_id' => 5029, 'event_code_id' => 19],
                ['checkpoint_code_id' => 5030, 'event_code_id' => 16],
                ['checkpoint_code_id' => 5031, 'event_code_id' => 27],
                ['checkpoint_code_id' => 5033, 'event_code_id' => 11],
                ['checkpoint_code_id' => 5034, 'event_code_id' => 1],
                ['checkpoint_code_id' => 5035, 'event_code_id' => 14],
                ['checkpoint_code_id' => 5036, 'event_code_id' => 27],
                ['checkpoint_code_id' => 5037, 'event_code_id' => 19],
                ['checkpoint_code_id' => 5038, 'event_code_id' => 12],
                ['checkpoint_code_id' => 5039, 'event_code_id' => 27],
                ['checkpoint_code_id' => 5040, 'event_code_id' => 24],
                ['checkpoint_code_id' => 5041, 'event_code_id' => 16],
                ['checkpoint_code_id' => 5042, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 5043, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 5044, 'event_code_id' => 19],
                ['checkpoint_code_id' => 5045, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5046, 'event_code_id' => 16],
                ['checkpoint_code_id' => 5047, 'event_code_id' => 16],
                ['checkpoint_code_id' => 5048, 'event_code_id' => 19],
                ['checkpoint_code_id' => 5049, 'event_code_id' => 19],
                ['checkpoint_code_id' => 5050, 'event_code_id' => 16],
                ['checkpoint_code_id' => 5051, 'event_code_id' => 17],
                ['checkpoint_code_id' => 5052, 'event_code_id' => 19],
                ['checkpoint_code_id' => 5053, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 5054, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 5055, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 5056, 'event_code_id' => 21],
                ['checkpoint_code_id' => 5057, 'event_code_id' => 27],
                ['checkpoint_code_id' => 5058, 'event_code_id' => 19],
                ['checkpoint_code_id' => 5059, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5060, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5061, 'event_code_id' => 7],
                ['checkpoint_code_id' => 5062, 'event_code_id' => 9],
                ['checkpoint_code_id' => 5063, 'event_code_id' => 11],
                ['checkpoint_code_id' => 5064, 'event_code_id' => 9],
                ['checkpoint_code_id' => 5065, 'event_code_id' => 17],
                ['checkpoint_code_id' => 5066, 'event_code_id' => 17],
                ['checkpoint_code_id' => 5067, 'event_code_id' => 17],
                ['checkpoint_code_id' => 5068, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5069, 'event_code_id' => 9],
                ['checkpoint_code_id' => 5070, 'event_code_id' => 9],
                ['checkpoint_code_id' => 5071, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5072, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 5073, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 5074, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5075, 'event_code_id' => 27],
                ['checkpoint_code_id' => 5076, 'event_code_id' => 26],
                ['checkpoint_code_id' => 5077, 'event_code_id' => 19],
                ['checkpoint_code_id' => 5078, 'event_code_id' => 14],
                ['checkpoint_code_id' => 5079, 'event_code_id' => 14],
                ['checkpoint_code_id' => 5080, 'event_code_id' => 1],
                ['checkpoint_code_id' => 5081, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 5082, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 5083, 'event_code_id' => 27],
                ['checkpoint_code_id' => 5084, 'event_code_id' => 11],
                ['checkpoint_code_id' => 5085, 'event_code_id' => 7],
                ['checkpoint_code_id' => 5086, 'event_code_id' => 21],
                ['checkpoint_code_id' => 5087, 'event_code_id' => 21],
                ['checkpoint_code_id' => 5088, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5091, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5092, 'event_code_id' => 17],
                ['checkpoint_code_id' => 5093, 'event_code_id' => 19],
                ['checkpoint_code_id' => 5094, 'event_code_id' => 19],
                ['checkpoint_code_id' => 5095, 'event_code_id' => 26],
                ['checkpoint_code_id' => 5096, 'event_code_id' => 19],
                ['checkpoint_code_id' => 5097, 'event_code_id' => 24],
                ['checkpoint_code_id' => 5098, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 5099, 'event_code_id' => 27],
                ['checkpoint_code_id' => 5121, 'event_code_id' => 26],
                ['checkpoint_code_id' => 5122, 'event_code_id' => 21],
                ['checkpoint_code_id' => 5123, 'event_code_id' => 21],
                ['checkpoint_code_id' => 5125, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5127, 'event_code_id' => 19],
                ['checkpoint_code_id' => 5128, 'event_code_id' => 21],
                ['checkpoint_code_id' => 5129, 'event_code_id' => 4],
                ['checkpoint_code_id' => 5130, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 5131, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 5132, 'event_code_id' => 4],
                ['checkpoint_code_id' => 5133, 'event_code_id' => 4],
                ['checkpoint_code_id' => 5134, 'event_code_id' => 25],
                ['checkpoint_code_id' => 5135, 'event_code_id' => 26],
                ['checkpoint_code_id' => 5136, 'event_code_id' => 21],
                ['checkpoint_code_id' => 5137, 'event_code_id' => 21],
                ['checkpoint_code_id' => 5138, 'event_code_id' => 26],
                ['checkpoint_code_id' => 5139, 'event_code_id' => 27],
                ['checkpoint_code_id' => 5140, 'event_code_id' => 7],
                ['checkpoint_code_id' => 5141, 'event_code_id' => 9],
                ['checkpoint_code_id' => 5142, 'event_code_id' => 7],
                ['checkpoint_code_id' => 5143, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 5144, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5145, 'event_code_id' => 7],
                ['checkpoint_code_id' => 5146, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 5147, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 5148, 'event_code_id'  => 4],
                ['checkpoint_code_id' => 5149, 'event_code_id' => 19],
                ['checkpoint_code_id' => 5150, 'event_code_id' => 19],
                ['checkpoint_code_id' => 5151, 'event_code_id' => 19],
                ['checkpoint_code_id' => 5152, 'event_code_id' => 19],
                ['checkpoint_code_id' => 5153, 'event_code_id'  => 4],
                ['checkpoint_code_id' => 5154, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 5155, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 5156, 'event_code_id' => 14],
                ['checkpoint_code_id' => 5157, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5158, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5159, 'event_code_id' => 14],
                ['checkpoint_code_id' => 5160, 'event_code_id' => 15],
                ['checkpoint_code_id' => 5161, 'event_code_id' => 16],
                ['checkpoint_code_id' => 5162, 'event_code_id' => 16],
                ['checkpoint_code_id' => 5163, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5164, 'event_code_id' => 14],
                ['checkpoint_code_id' => 5165, 'event_code_id' => 17],
                ['checkpoint_code_id' => 5167, 'event_code_id' => 17],
                ['checkpoint_code_id' => 5168, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5169, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5170, 'event_code_id' => 14],
                ['checkpoint_code_id' => 5171, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5172, 'event_code_id' => 16],
                ['checkpoint_code_id' => 5173, 'event_code_id' => 14],
                ['checkpoint_code_id' => 5174, 'event_code_id' => 10],
                ['checkpoint_code_id' => 5175, 'event_code_id'  => 7],
                ['checkpoint_code_id' => 5176, 'event_code_id' => 7],
                ['checkpoint_code_id' => 5177, 'event_code_id'  => 9],
                ['checkpoint_code_id' => 5178, 'event_code_id'  => 10],
                ['checkpoint_code_id' => 5179, 'event_code_id'  => 12],
                ['checkpoint_code_id' => 5180, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 5181, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 5182, 'event_code_id' => 17],
                ['checkpoint_code_id' => 5183, 'event_code_id' => 26],
                ['checkpoint_code_id' => 5184, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5185, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5186, 'event_code_id' => 12],
                ['checkpoint_code_id' => 5187, 'event_code_id' => 26],
                ['checkpoint_code_id' => 5188, 'event_code_id'  => 14],
                ['checkpoint_code_id' => 5189, 'event_code_id'  => 15],
                ['checkpoint_code_id' => 5190, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 5191, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 5192, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 5193, 'event_code_id'  => 15],
                ['checkpoint_code_id' => 5194, 'event_code_id' => 17],
                ['checkpoint_code_id' => 5195, 'event_code_id' => 2],
                ['checkpoint_code_id' => 5196, 'event_code_id' => 21],
                ['checkpoint_code_id' => 5197, 'event_code_id' => 16],
                ['checkpoint_code_id' => 5198, 'event_code_id' => 20],
                ['checkpoint_code_id' => 5199, 'event_code_id'  => 2],
                ['checkpoint_code_id' => 5200, 'event_code_id' => 27],
                ['checkpoint_code_id' => 5201, 'event_code_id' => 26],
                ['checkpoint_code_id' => 5202, 'event_code_id' => 27],
                ['checkpoint_code_id' => 5204, 'event_code_id' => 26],
                ['checkpoint_code_id' => 5206, 'event_code_id'  => 27],
                ['checkpoint_code_id' => 5207, 'event_code_id' => 26],
                ['checkpoint_code_id' => 5208, 'event_code_id' => 26],
                ['checkpoint_code_id' => 5209, 'event_code_id' => 11],
                ['checkpoint_code_id' => 5210, 'event_code_id' => 25],
                ['checkpoint_code_id' => 5211, 'event_code_id'  => 15],
                ['checkpoint_code_id' => 5212, 'event_code_id' => 17],
                ['checkpoint_code_id' => 5213, 'event_code_id'  => 3],
                ['checkpoint_code_id' => 5214, 'event_code_id' => 16],
                ['checkpoint_code_id' => 5215, 'event_code_id' => 26],
                ['checkpoint_code_id' => 5216, 'event_code_id' => 26],
                ['checkpoint_code_id' => 5217, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 5218, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 5219, 'event_code_id' => 10],
                ['checkpoint_code_id' => 5220, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 5221, 'event_code_id'  => 4],
                ['checkpoint_code_id' => 5222, 'event_code_id'  => 4],
                ['checkpoint_code_id' => 5223, 'event_code_id'  => 4],
                ['checkpoint_code_id' => 5224, 'event_code_id' => 11],
                ['checkpoint_code_id' => 5225, 'event_code_id'  => 10],
                ['checkpoint_code_id' => 5226, 'event_code_id' => 10],
                ['checkpoint_code_id' => 5227, 'event_code_id'  => 14],
                ['checkpoint_code_id' => 5228, 'event_code_id' => 16],
                ['checkpoint_code_id' => 5229, 'event_code_id'  => 17],
                ['checkpoint_code_id' => 5230, 'event_code_id' => 15],
                ['checkpoint_code_id' => 5231, 'event_code_id'  => 14],
                ['checkpoint_code_id' => 5232, 'event_code_id'  => 10],
                ['checkpoint_code_id' => 5233, 'event_code_id' => 17],
                ['checkpoint_code_id' => 5234, 'event_code_id' => 20],
                ['checkpoint_code_id' => 5235, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5236, 'event_code_id' => 19],
                ['checkpoint_code_id' => 5237, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5238, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5239, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5240, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5241, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 5242, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 5243, 'event_code_id'  => 18],
                ['checkpoint_code_id' => 5244, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 5245, 'event_code_id' => 26],
                ['checkpoint_code_id' => 5246, 'event_code_id' => 21],
                ['checkpoint_code_id' => 5247, 'event_code_id' => 26],
                ['checkpoint_code_id' => 5248, 'event_code_id' => 21],
                ['checkpoint_code_id' => 5249, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5250, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 5251, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 5252, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 5253, 'event_code_id' => 19],
                ['checkpoint_code_id' => 5254, 'event_code_id' => 26],
                ['checkpoint_code_id' => 5255, 'event_code_id' => 26],
                ['checkpoint_code_id' => 5256, 'event_code_id' => 11],
                ['checkpoint_code_id' => 5257, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 5258, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 5259, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 5260, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 5261, 'event_code_id'  => 8],
                ['checkpoint_code_id' => 5262, 'event_code_id' => 12],
                ['checkpoint_code_id' => 5263, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 5264, 'event_code_id' => 26],
                ['checkpoint_code_id' => 5265, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 5266, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5267, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5268, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5269, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5270, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5271, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5272, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5273, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5274, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5275, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5276, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5277, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5278, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5279, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5280, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5281, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5282, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5283, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5284, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5285, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5286, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5287, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5288, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5289, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5290, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5291, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5292, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5293, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5294, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5295, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5296, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5297, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5298, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5299, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5300, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5301, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5302, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5303, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5304, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5305, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5306, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5307, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5308, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5309, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5310, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5311, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5312, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5313, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5314, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5315, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5316, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5317, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5318, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5319, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5320, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5321, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5322, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5323, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5324, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5325, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5326, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5327, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5328, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5329, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5330, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5331, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5332, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5333, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5334, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5335, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5336, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5337, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5338, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5339, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5340, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5341, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5342, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5343, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5344, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5345, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5346, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5347, 'event_code_id'  => 14],
                ['checkpoint_code_id' => 5348, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5349, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5350, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5351, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5352, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5353, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5354, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5355, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5356, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5357, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5358, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5359, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5360, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5361, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5362, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5363, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5364, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5365, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5366, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5367, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5368, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5369, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5370, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5371, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5372, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5373, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5374, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5375, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5376, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5377, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5378, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5379, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5380, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5381, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5382, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5383, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5384, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5385, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5386, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5387, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5388, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5389, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5390, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5391, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5392, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5393, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5394, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5395, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5396, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5397, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5398, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5399, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5400, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5401, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5402, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5403, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5404, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5405, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5406, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5407, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5408, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5409, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5410, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5411, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5412, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5413, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5414, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5415, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5416, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5417, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5418, 'event_code_id' => 18],
                ['checkpoint_code_id' => 5419, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5420, 'event_code_id' => 13],
                ['checkpoint_code_id' => 5421, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 5422, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 5423, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 5424, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 5425, 'event_code_id'  => 21],
                ['checkpoint_code_id' => 5426, 'event_code_id'  => 21],
                ['checkpoint_code_id' => 5428, 'event_code_id'  => 14],
                ['checkpoint_code_id' => 5429, 'event_code_id'  => 15],
                ['checkpoint_code_id' => 55, 'event_code_id' => 13],
                ['checkpoint_code_id' => 56, 'event_code_id' => 13],
                ['checkpoint_code_id' => 57, 'event_code_id' => 13],
                ['checkpoint_code_id' => 58, 'event_code_id' => 13],
                ['checkpoint_code_id' => 59, 'event_code_id' => 15],
                ['checkpoint_code_id' => 6, 'event_code_id' => 19],
                ['checkpoint_code_id' => 60, 'event_code_id' => 14],
                ['checkpoint_code_id' => 61, 'event_code_id' => 14],
                ['checkpoint_code_id' => 62, 'event_code_id' => 14],
                ['checkpoint_code_id' => 63, 'event_code_id' => 17],
                ['checkpoint_code_id' => 64, 'event_code_id' => 17],
                ['checkpoint_code_id' => 65, 'event_code_id' => 26],
                ['checkpoint_code_id' => 66, 'event_code_id' => 13],
                ['checkpoint_code_id' => 67, 'event_code_id' => 13],
                ['checkpoint_code_id' => 69, 'event_code_id' => 22],
                ['checkpoint_code_id' => 7, 'event_code_id' => 19],
                ['checkpoint_code_id' => 70, 'event_code_id' => 17],
                ['checkpoint_code_id' => 71, 'event_code_id' => 14],
                ['checkpoint_code_id' => 74, 'event_code_id' => 17],
                ['checkpoint_code_id' => 75, 'event_code_id' => 24],
                ['checkpoint_code_id' => 8, 'event_code_id' => 19],
                ['checkpoint_code_id' => 81, 'event_code_id' => 15],
                ['checkpoint_code_id' => 83, 'event_code_id' => 16],
                ['checkpoint_code_id' => 84, 'event_code_id' => 16],
                ['checkpoint_code_id' => 89, 'event_code_id' => 16],
                ['checkpoint_code_id' => 9, 'event_code_id' => 19],
                ['checkpoint_code_id' => 90, 'event_code_id' => 19],
                ['checkpoint_code_id' => 94, 'event_code_id' => 22],
                ['checkpoint_code_id' => 9469, 'event_code_id'  => 17],
                ['checkpoint_code_id' => 9470, 'event_code_id'  => 9],
                ['checkpoint_code_id' => 9471, 'event_code_id'  => 19],
                ['checkpoint_code_id' => 9472, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9473, 'event_code_id'  => 18],
                ['checkpoint_code_id' => 9474, 'event_code_id'  => 21],
                ['checkpoint_code_id' => 9475, 'event_code_id'  => 11],
                ['checkpoint_code_id' => 9476, 'event_code_id'  => 21],
                ['checkpoint_code_id' => 9477, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9478, 'event_code_id'  => 11],
                ['checkpoint_code_id' => 9479, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 9480, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 9481, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 9482, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 9483, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 9484, 'event_code_id'  => 18],
                ['checkpoint_code_id' => 9485, 'event_code_id'  => 14],
                ['checkpoint_code_id' => 9486, 'event_code_id'  => 7],
                ['checkpoint_code_id' => 9487, 'event_code_id'  => 7],
                ['checkpoint_code_id' => 9488, 'event_code_id'  => 9],
                ['checkpoint_code_id' => 9489, 'event_code_id'  => 10],
                ['checkpoint_code_id' => 9490, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 9491, 'event_code_id'  => 12],
                ['checkpoint_code_id' => 9492, 'event_code_id'  => 14],
                ['checkpoint_code_id' => 9493, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 9494, 'event_code_id'  => 15],
                ['checkpoint_code_id' => 9495, 'event_code_id'  => 17],
                ['checkpoint_code_id' => 9496, 'event_code_id'  => 18],
                ['checkpoint_code_id' => 9497, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9498, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9499, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 9500, 'event_code_id'  => 15],
                ['checkpoint_code_id' => 9501, 'event_code_id'  => 17],
                ['checkpoint_code_id' => 9502, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9503, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9504, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 9505, 'event_code_id'  => 14],
                ['checkpoint_code_id' => 9506, 'event_code_id'  => 15],
                ['checkpoint_code_id' => 9507, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9508, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 9509, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9510, 'event_code_id'  => 10],
                ['checkpoint_code_id' => 9511, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 9512, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 9513, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9515, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 9516, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9518, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9519, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9522, 'event_code_id'  => 14],
                ['checkpoint_code_id' => 9524, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9529, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9531, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 9532, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9534, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9536, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9538, 'event_code_id'  => 7],
                ['checkpoint_code_id' => 9539, 'event_code_id'  => 9],
                ['checkpoint_code_id' => 9540, 'event_code_id'  => 10],
                ['checkpoint_code_id' => 9541, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 9542, 'event_code_id'  => 12],
                ['checkpoint_code_id' => 9543, 'event_code_id'  => 14],
                ['checkpoint_code_id' => 9544, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 9545, 'event_code_id'  => 15],
                ['checkpoint_code_id' => 9546, 'event_code_id'  => 17],
                ['checkpoint_code_id' => 9547, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9548, 'event_code_id'  => 14],
                ['checkpoint_code_id' => 9549, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9550, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9551, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9552, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9553, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9554, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9555, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9556, 'event_code_id'  => 18],
                ['checkpoint_code_id' => 9557, 'event_code_id'  => 14],
                ['checkpoint_code_id' => 9558, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 9559, 'event_code_id'  => 10],
                ['checkpoint_code_id' => 9560, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 9561, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 9562, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 9564, 'event_code_id'  => 20],
                ['checkpoint_code_id' => 9565, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9566, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9567, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9568, 'event_code_id'  => 21],
                ['checkpoint_code_id' => 9569, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 9570, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9571, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9572, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9573, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 9574, 'event_code_id'  => 15],
                ['checkpoint_code_id' => 9575, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9576, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9577, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9578, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 9579, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9580, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 9581, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 99, 'event_code_id' => 17],
                ['checkpoint_code_id' => 9987, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9988, 'event_code_id'  => 21],
                ['checkpoint_code_id' => 9989, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 9990, 'event_code_id'  => 14],
                ['checkpoint_code_id' => 9991, 'event_code_id'  => 27]
            ];

            foreach ($events as $event) {
                DB::table('event_codes')->insert([
                    'id'          => $event['id'],
                    'description' => $event['description'],
                    'key'         => $event['key'],
                    'position'    => $event['position'],
                    'created_at'  => $now,
                    'updated_at'  => $now
                ]);
            }

            foreach ($checkpoint_code_events as $checkpoint_code_event) {
                DB::table('checkpoint_code_event_code')->insert([
                    'checkpoint_code_id' => $checkpoint_code_event['checkpoint_code_id'],
                    'event_code_id'      => $checkpoint_code_event['event_code_id']
                ]);
            }

            // Generate Code Clients
            // ------------------------------------------------------------------------------------------------
            
            logger("Generate clients code");
            
            Schema::table('clients', function (Blueprint $table) {
                $table->string('code', 6)->nullable()->after('name');
            });

            /** @var ClientRepository $clientRepository */
            $clientRepository = app(ClientRepository::class);
            $clients = $clientRepository->search()->get();
            $codesClient = collect();
            if (count($clients) > 0) {
                /** @var Client $client */
                foreach ($clients as $client) {
                    do {
                        $code = 'CL' . random_int(1001, 9001);
                        $exist_code = $codesClient->contains('key', $code);
                        if (!$exist_code) {
                            $codesClient[] = [
                                'key'  => $code,
                                'code' => $code
                            ];
                            $client->code = $code;
                            $client->save();

                            $exist_code = false;
                        }
                    } while ($exist_code);
                }
            }

            Schema::table('clients', function (Blueprint $table) {
                $table->unique(['code']);
            });

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            logger($e->getMessage());
            logger($e->getTraceAsString());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
