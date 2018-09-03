<?php

use App\Repositories\CurrencyRepository;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 3);
            $table->string('name')->default('[Assign name]');
            $table->timestamps();

            $table->unique(['code']);
        });

        $currencies = [
            ['code' => 'USD', 'name' => 'United States Dollar'],
            ['code' => 'EUR', 'name' => 'Euro'],
            ['code' => 'GBP', 'name' => 'Great Britain Pound'],
            ['code' => 'BRL', 'name' => 'Brazilian Real'],
            ['code' => 'ARS', 'name' => 'Argentine Peso'],
            ['code' => 'MXN', 'name' => 'Mexican Peso'],
            ['code' => 'COP', 'name' => 'Colombian Peso'],
            ['code' => 'MYR', 'name' => 'Malaysian Ringgit'],
            ['code' => 'CLP', 'name' => 'Chilean Peso'],
            ['code' => 'CNY', 'name' => 'Chinese Yuan Renminbi'],
            ['code' => 'XDR', 'name' => 'Special Drawing Rights'],
            ['code' => 'CZK', 'name' => 'Czech Koruna'],
            ['code' => 'IDR', 'name' => 'Indonesian Rupiah'],
            ['code' => 'THB', 'name' => '[Assign name]'],
            ['code' => 'HUF', 'name' => '[Assign name]'],
            ['code' => 'PLN', 'name' => '[Assign name]'],
            ['code' => 'VND', 'name' => '[Assign name]'],
            ['code' => 'ZAR', 'name' => '[Assign name]'],
            ['code' => 'SGD', 'name' => 'Singapore Dollar'],
            ['code' => 'HKD', 'name' => 'Hong Kong Dollar'],
            ['code' => 'INR', 'name' => 'India Rupee'],
            ['code' => 'PEN', 'name' => 'Nuevo Sol'],
        ];

        try {
            DB::beginTransaction();
            foreach ($currencies as $currency) {

                /**@var CurrencyRepository $currencyRepository */
                $currencyRepository = app(CurrencyRepository::class);
                $currencyRepository->updateOrCreate($currency, [
                    'code' => $currency['code'],
                    'name' => $currency['name']
                ]);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('currencies');
    }
}
