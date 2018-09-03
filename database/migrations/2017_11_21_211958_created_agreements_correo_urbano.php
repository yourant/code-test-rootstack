<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Repositories\ProviderRepository;
use App\Repositories\AgreementRepository;
use App\Repositories\CountryRepository;
use App\Repositories\ServiceTypeRepository;
use App\Repositories\LegRepository;

use App\Leg;

use Carbon\Carbon;

class CreatedAgreementsCorreoUrbano extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        /** @var ProviderRepository $provider */
        $provider = app(ProviderRepository::class);
        $correo_urbano_provider = $provider->getByName('Urbano');
        if (!$correo_urbano_provider){
            throw new Exception("Urbano Provider is not registered");
        }

        $serpost_provider = $provider->getByName('Serpost');
        if(!$serpost_provider){
            throw new Exception("Serpost Provider is not registered");
        }

        /** @var ServiceTypeRepository $service_type */
        $service_type = app(ServiceTypeRepository::class);
        $correo_urbano_service_type = $service_type->search([
            'provider_id' => $correo_urbano_provider['id']
        ])->first();
        if(!$correo_urbano_service_type){
            throw new Exception("Service Type for Urbano Provider in not registered");
        }

        $serpost_service_type = $service_type->search([
            'provider_id' => $serpost_provider['id']
        ])->first();
        if(!$serpost_service_type){
            throw new Exception("Service Type for Serpost Provider in not registered");
        }

        /** @var CountryRepository $country */
        $country = app(CountryRepository::class);
        $country_peru = $country->getByCode('PE');
        if(!$country_peru){
            throw new Exception("PERU country in not registered");
        }

        /** @var AgreementRepository $agreements */
        $agreements = app(AgreementRepository::class);
        $agreements_peru = $agreements->search([
            'country_id' => $country_peru['id']
        ])->whereNull('agreements.deleted_at')->get();
        if(!$agreements_peru){
            throw new Exception("Peru Agreements in not registered");
        }

        foreach ($agreements_peru as $agreement_peru) {
            $new_agreement_peru = $agreement_peru->replicate();
            $new_agreement_peru->name .= " (Urbano)";
            $new_agreement_peru->created_at = Carbon::now();
            $new_agreement_peru->updated_at = Carbon::now();

            $new_agreement_peru->save();

            /** @var LegRepository $legs */
            $legs = app(LegRepository::class);
            $legs_agreement_peru = $legs->search([
                'agreement_id' => $agreement_peru->id
            ])->orderBy('position', 'asc')->get();

            if(count($legs_agreement_peru) > 0){
                foreach ($legs_agreement_peru as $leg) {
                    $new_leg = $leg->replicate();
                    $new_leg->agreement_id = $new_agreement_peru->id;

                    if ($leg->service_type_id == $serpost_service_type->id){
                        $new_leg->transit_days = 2;
                    }

                    $new_leg->created_at = Carbon::now();
                    $new_leg->updated_at = Carbon::now();

                    $new_leg->save();
                }

                /** @var Leg $leg */
                $correo_urbano_leg = app(Leg::class);
                $correo_urbano_leg->agreement_id    = $new_agreement_peru->id;
                $correo_urbano_leg->service_type_id = $correo_urbano_service_type['id'];
                $correo_urbano_leg->transit_days    = 9;
                $correo_urbano_leg->position        = $new_leg->position + 1;
                $correo_urbano_leg->controlled      = 1;
                $correo_urbano_leg->created_at      = Carbon::now();
                $correo_urbano_leg->updated_at      = Carbon::now();

                $correo_urbano_leg->save();

            }

            $agreement_peru->deleted_at = Carbon::now();

            $agreement_peru->save();
        }

    }

    /**
     * Reverse the migrations.|
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
