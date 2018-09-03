<?php

use App\Repositories\EventCodeCategoryRepository;
use App\Repositories\EventCodeRepository;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventCodeCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_code_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('event_codes', function (Blueprint $table) {
            $table->integer('event_code_category_id')->unsigned()->nullable();
            $table->foreign('event_code_category_id')->references('id')->on('event_code_categories')->onUpdate('cascade')->onDelete('cascade');
            $table->index('event_code_category_id', 'event_codes_event_code_category_id_foreign');
        });

        /** @var EventCodeCategoryRepository $eventCodeCategoryRepository */
        $eventCodeCategoryRepository = app(EventCodeCategoryRepository::class);

        $sellerDropOff = $eventCodeCategoryRepository->create(['name' => 'Seller Drop Off']);
        $warehouse = $eventCodeCategoryRepository->create(['name' => 'Warehouse']);
        $transit = $eventCodeCategoryRepository->create(['name' => 'Transit']);
        $customs = $eventCodeCategoryRepository->create(['name' => 'Customs']);
        $distribution = $eventCodeCategoryRepository->create(['name' => 'Distribution']);
        $returns = $eventCodeCategoryRepository->create(['name' => 'Returns']);
        $others = $eventCodeCategoryRepository->create(['name' => 'Others']);


        /** @var EventCodeRepository $eventCodeRepository */
        $eventCodeRepository = app(EventCodeRepository::class);

        foreach ($eventCodeRepository->search(['key' => 'ML-100'])->get() as $eventCode) {
            $eventCodeRepository->update($eventCode, ['event_code_category_id' => $sellerDropOff->id]);
        }

        foreach ($eventCodeRepository->search(['key' => ['ML-200', 'ML-201', 'ML-202']])->get() as $eventCode) {
            $eventCodeRepository->update($eventCode, ['event_code_category_id' => $warehouse->id]);
        }

        foreach ($eventCodeRepository->search(['key' => ['ML-300', 'ML-301', 'ML-302', 'ML-303', 'ML-304']])->get() as $eventCode) {
            $eventCodeRepository->update($eventCode, ['event_code_category_id' => $transit->id]);
        }

        foreach ($eventCodeRepository->search(['key' => ['ML-400', 'ML-401', 'ML-402']])->get() as $eventCode) {
            $eventCodeRepository->update($eventCode, ['event_code_category_id' => $customs->id]);
        }

        foreach ($eventCodeRepository->search(['key' => ['ML-500', 'ML-501', 'ML-502', 'ML-503', 'ML-504', 'ML-505']])->get() as $eventCode) {
            $eventCodeRepository->update($eventCode, ['event_code_category_id' => $distribution->id]);
        }

        foreach ($eventCodeRepository->search(['key' => ['ML-700', 'ML-701']])->get() as $eventCode) {
            $eventCodeRepository->update($eventCode, ['event_code_category_id' => $returns->id]);
        }

        foreach ($eventCodeRepository->search(['key' => ['ML-600', 'ML-601', 'ML-602', 'ML-603', 'ML-604', 'ML-605', 'ML-606']])->get() as $eventCode) {
            $eventCodeRepository->update($eventCode, ['event_code_category_id' => $others->id]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_codes', function (Blueprint $table) {
            $table->dropIndex('event_codes_event_code_category_id_foreign');
            $table->dropForeign('event_codes_event_code_category_id_foreign');
            $table->dropColumn('event_code_category_id');
        });

        Schema::drop('event_code_categories');
    }
}
