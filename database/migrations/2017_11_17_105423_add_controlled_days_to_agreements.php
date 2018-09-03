<?php

use App\Agreement;
use App\Repositories\AgreementRepository;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddControlledDaysToAgreements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agreements', function(Blueprint $table) {
            $table->integer('controlled_transit_days')->unsigned()->nullable()->after('transit_days');
            $table->integer('uncontrolled_transit_days')->unsigned()->nullable()->after('controlled_transit_days');
            $table->integer('total_transit_days')->unsigned()->nullable()->after('uncontrolled_transit_days');

            $table->index(['type', 'transit_days', 'controlled_transit_days'], 'agreements_type_transit_index');
        });

        /** @var AgreementRepository $agreementRepository */
        $agreementRepository = app(AgreementRepository::class);

        /** @var Agreement $agreement */
        foreach ($agreementRepository->all() as $agreement) {
            $controlled_transit_days = $agreement->calculateControlledTransitDays();
            $uncontrolled_transit_days = $agreement->calculateUncontrolledTransitDays();
            $total_transit_days = $agreement->calculateTotalTransitDays();

            $agreementRepository->update($agreement, compact('controlled_transit_days', 'uncontrolled_transit_days', 'total_transit_days'));
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agreements', function(Blueprint $table) {
            $table->dropColumn('controlled_transit_days');
            $table->dropColumn('uncontrolled_transit_days');
            $table->dropColumn('total_transit_days');
        });
    }
}
