<?php

use App\Repositories\AdminLevel3Repository;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubzoneCodeToAdminLevel3Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admin_level_3', function (Blueprint $table) {
            $table->string('subzone_code', 10)->nullable();
        });
        
        /** @var AdminLevel3Repository $adminLevel3Repository */
        $adminLevel3Repository = app(AdminLevel3Repository::class);

        $subzone_codes = [
            ['territorial_code' => '1101' , 'subzone_code' => '0'],
            ['territorial_code' => '1107' , 'subzone_code' => '1'],
            ['territorial_code' => '1401' , 'subzone_code' => '2'],
            ['territorial_code' => '1402' , 'subzone_code' => '4'],
            ['territorial_code' => '1403' , 'subzone_code' => '4'],
            ['territorial_code' => '1404' , 'subzone_code' => '3'],
            ['territorial_code' => '1405' , 'subzone_code' => '2'],
            ['territorial_code' => '2101' , 'subzone_code' => '0'],
            ['territorial_code' => '2102' , 'subzone_code' => '3'],
            ['territorial_code' => '2103' , 'subzone_code' => '3'],
            ['territorial_code' => '2104' , 'subzone_code' => '3'],
            ['territorial_code' => '2201' , 'subzone_code' => '0'],
            ['territorial_code' => '2202' , 'subzone_code' => '3'],
            ['territorial_code' => '2203' , 'subzone_code' => '2'],
            ['territorial_code' => '2301' , 'subzone_code' => '2'],
            ['territorial_code' => '2302' , 'subzone_code' => '2'],
            ['territorial_code' => '3101' , 'subzone_code' => '0'],
            ['territorial_code' => '3102' , 'subzone_code' => '3'],
            ['territorial_code' => '3103' , 'subzone_code' => '1'],
            ['territorial_code' => '3201' , 'subzone_code' => '3'],
            ['territorial_code' => '3202' , 'subzone_code' => '2'],
            ['territorial_code' => '3301' , 'subzone_code' => '4'],
            ['territorial_code' => '3302' , 'subzone_code' => '4'],
            ['territorial_code' => '3303' , 'subzone_code' => '4'],
            ['territorial_code' => '3304' , 'subzone_code' => '4'],
            ['territorial_code' => '4101' , 'subzone_code' => '0'],
            ['territorial_code' => '4102' , 'subzone_code' => '2'],
            ['territorial_code' => '4103' , 'subzone_code' => '3'],
            ['territorial_code' => '4104' , 'subzone_code' => '3'],
            ['territorial_code' => '4105' , 'subzone_code' => '3'],
            ['territorial_code' => '4106' , 'subzone_code' => '3'],
            ['territorial_code' => '4201' , 'subzone_code' => '4'],
            ['territorial_code' => '4202' , 'subzone_code' => '4'],
            ['territorial_code' => '4203' , 'subzone_code' => '4'],
            ['territorial_code' => '4204' , 'subzone_code' => '4'],
            ['territorial_code' => '4301' , 'subzone_code' => '4'],
            ['territorial_code' => '4302' , 'subzone_code' => '4'],
            ['territorial_code' => '4303' , 'subzone_code' => '4'],
            ['territorial_code' => '4304' , 'subzone_code' => '4'],
            ['territorial_code' => '4305' , 'subzone_code' => '4'],
            ['territorial_code' => '5101' , 'subzone_code' => '0'],
            ['territorial_code' => '5102' , 'subzone_code' => '9'],
            ['territorial_code' => '5103' , 'subzone_code' => '5'],
            ['territorial_code' => '5104' , 'subzone_code' => '9'],
            ['territorial_code' => '5105' , 'subzone_code' => '3'],
            ['territorial_code' => '5106' , 'subzone_code' => '7'],
            ['territorial_code' => '5107' , 'subzone_code' => '5'],
            ['territorial_code' => '5108' , 'subzone_code' => '2'],
            ['territorial_code' => '5109' , 'subzone_code' => '0'],
            ['territorial_code' => '5201' , 'subzone_code' => '9'],
            ['territorial_code' => '5301' , 'subzone_code' => '0'],
            ['territorial_code' => '5302' , 'subzone_code' => '3'],
            ['territorial_code' => '5303' , 'subzone_code' => '3'],
            ['territorial_code' => '5304' , 'subzone_code' => '3'],
            ['territorial_code' => '5401' , 'subzone_code' => '3'],
            ['territorial_code' => '5402' , 'subzone_code' => '3'],
            ['territorial_code' => '5403' , 'subzone_code' => '3'],
            ['territorial_code' => '5404' , 'subzone_code' => '3'],
            ['territorial_code' => '5405' , 'subzone_code' => '3'],
            ['territorial_code' => '5501' , 'subzone_code' => '2'],
            ['territorial_code' => '5502' , 'subzone_code' => '0'],
            ['territorial_code' => '5503' , 'subzone_code' => '1'],
            ['territorial_code' => '5504' , 'subzone_code' => '2'],
            ['territorial_code' => '5505' , 'subzone_code' => '5'],
            ['territorial_code' => '5506' , 'subzone_code' => '1'],
            ['territorial_code' => '5507' , 'subzone_code' => '5'],
            ['territorial_code' => '5601' , 'subzone_code' => '3'],
            ['territorial_code' => '5602' , 'subzone_code' => '6'],
            ['territorial_code' => '5603' , 'subzone_code' => '6'],
            ['territorial_code' => '5604' , 'subzone_code' => '6'],
            ['territorial_code' => '5605' , 'subzone_code' => '6'],
            ['territorial_code' => '5606' , 'subzone_code' => '3'],
            ['territorial_code' => '5701' , 'subzone_code' => '4'],
            ['territorial_code' => '5702' , 'subzone_code' => '2'],
            ['territorial_code' => '5703' , 'subzone_code' => '2'],
            ['territorial_code' => '5704' , 'subzone_code' => '4'],
            ['territorial_code' => '5705' , 'subzone_code' => '3'],
            ['territorial_code' => '5706' , 'subzone_code' => '3'],
            ['territorial_code' => '6101' , 'subzone_code' => '0'],
            ['territorial_code' => '6102' , 'subzone_code' => '9'],
            ['territorial_code' => '6103' , 'subzone_code' => '8'],
            ['territorial_code' => '6104' , 'subzone_code' => '8'],
            ['territorial_code' => '6105' , 'subzone_code' => '8'],
            ['territorial_code' => '6106' , 'subzone_code' => '9'],
            ['territorial_code' => '6107' , 'subzone_code' => '4'],
            ['territorial_code' => '6108' , 'subzone_code' => '1'],
            ['territorial_code' => '6109' , 'subzone_code' => '6'],
            ['territorial_code' => '6110' , 'subzone_code' => '9'],
            ['territorial_code' => '6111' , 'subzone_code' => '6'],
            ['territorial_code' => '6112' , 'subzone_code' => '4'],
            ['territorial_code' => '6113' , 'subzone_code' => '4'],
            ['territorial_code' => '6114' , 'subzone_code' => '8'],
            ['territorial_code' => '6115' , 'subzone_code' => '6'],
            ['territorial_code' => '6116' , 'subzone_code' => '6'],
            ['territorial_code' => '6117' , 'subzone_code' => '4'],
            ['territorial_code' => '6201' , 'subzone_code' => '5'],
            ['territorial_code' => '6202' , 'subzone_code' => '8'],
            ['territorial_code' => '6203' , 'subzone_code' => '8'],
            ['territorial_code' => '6204' , 'subzone_code' => '5'],
            ['territorial_code' => '6205' , 'subzone_code' => '8'],
            ['territorial_code' => '6206' , 'subzone_code' => '5'],
            ['territorial_code' => '6301' , 'subzone_code' => '2'],
            ['territorial_code' => '6302' , 'subzone_code' => '3'],
            ['territorial_code' => '6303' , 'subzone_code' => '2'],
            ['territorial_code' => '6304' , 'subzone_code' => '5'],
            ['territorial_code' => '6305' , 'subzone_code' => '3'],
            ['territorial_code' => '6306' , 'subzone_code' => '3'],
            ['territorial_code' => '6307' , 'subzone_code' => '5'],
            ['territorial_code' => '6308' , 'subzone_code' => '5'],
            ['territorial_code' => '6309' , 'subzone_code' => '5'],
            ['territorial_code' => '6310' , 'subzone_code' => '3'],
            ['territorial_code' => '7101' , 'subzone_code' => '0'],
            ['territorial_code' => '7102' , 'subzone_code' => '3'],
            ['territorial_code' => '7103' , 'subzone_code' => '4'],
            ['territorial_code' => '7104' , 'subzone_code' => '3'],
            ['territorial_code' => '7105' , 'subzone_code' => '3'],
            ['territorial_code' => '7106' , 'subzone_code' => '6'],
            ['territorial_code' => '7107' , 'subzone_code' => '6'],
            ['territorial_code' => '7108' , 'subzone_code' => '6'],
            ['territorial_code' => '7109' , 'subzone_code' => '1'],
            ['territorial_code' => '7110' , 'subzone_code' => '6'],
            ['territorial_code' => '7201' , 'subzone_code' => '4'],
            ['territorial_code' => '7202' , 'subzone_code' => '4'],
            ['territorial_code' => '7203' , 'subzone_code' => '4'],
            ['territorial_code' => '7301' , 'subzone_code' => '0'],
            ['territorial_code' => '7302' , 'subzone_code' => '4'],
            ['territorial_code' => '7303' , 'subzone_code' => '4'],
            ['territorial_code' => '7304' , 'subzone_code' => '2'],
            ['territorial_code' => '7305' , 'subzone_code' => '4'],
            ['territorial_code' => '7306' , 'subzone_code' => '3'],
            ['territorial_code' => '7307' , 'subzone_code' => '2'],
            ['territorial_code' => '7308' , 'subzone_code' => '3'],
            ['territorial_code' => '7309' , 'subzone_code' => '4'],
            ['territorial_code' => '7401' , 'subzone_code' => '2'],
            ['territorial_code' => '7402' , 'subzone_code' => '2'],
            ['territorial_code' => '7403' , 'subzone_code' => '5'],
            ['territorial_code' => '7404' , 'subzone_code' => '5'],
            ['territorial_code' => '7405' , 'subzone_code' => '5'],
            ['territorial_code' => '7406' , 'subzone_code' => '3'],
            ['territorial_code' => '7407' , 'subzone_code' => '4'],
            ['territorial_code' => '7408' , 'subzone_code' => '2'],
            ['territorial_code' => '8101' , 'subzone_code' => '0'],
            ['territorial_code' => '8102' , 'subzone_code' => '5'],
            ['territorial_code' => '8103' , 'subzone_code' => '7'],
            ['territorial_code' => '8104' , 'subzone_code' => '9'],
            ['territorial_code' => '8105' , 'subzone_code' => '7'],
            ['territorial_code' => '8106' , 'subzone_code' => '4'],
            ['territorial_code' => '8107' , 'subzone_code' => '3'],
            ['territorial_code' => '8108' , 'subzone_code' => '6'],
            ['territorial_code' => '8109' , 'subzone_code' => '9'],
            ['territorial_code' => '8110' , 'subzone_code' => '2'],
            ['territorial_code' => '8111' , 'subzone_code' => '3'],
            ['territorial_code' => '8112' , 'subzone_code' => '2'],
            ['territorial_code' => '8201' , 'subzone_code' => '8'],
            ['territorial_code' => '8202' , 'subzone_code' => '4'],
            ['territorial_code' => '8203' , 'subzone_code' => '8'],
            ['territorial_code' => '8204' , 'subzone_code' => '8'],
            ['territorial_code' => '8205' , 'subzone_code' => '8'],
            ['territorial_code' => '8206' , 'subzone_code' => '8'],
            ['territorial_code' => '8207' , 'subzone_code' => '2'],
            ['territorial_code' => '8301' , 'subzone_code' => '0'],
            ['territorial_code' => '8302' , 'subzone_code' => '5'],
            ['territorial_code' => '8303' , 'subzone_code' => '3'],
            ['territorial_code' => '8304' , 'subzone_code' => '3'],
            ['territorial_code' => '8305' , 'subzone_code' => '6'],
            ['territorial_code' => '8306' , 'subzone_code' => '4'],
            ['territorial_code' => '8307' , 'subzone_code' => '4'],
            ['territorial_code' => '8308' , 'subzone_code' => '7'],
            ['territorial_code' => '8309' , 'subzone_code' => '5'],
            ['territorial_code' => '8310' , 'subzone_code' => '3'],
            ['territorial_code' => '8311' , 'subzone_code' => '7'],
            ['territorial_code' => '8312' , 'subzone_code' => '5'],
            ['territorial_code' => '8313' , 'subzone_code' => '3'],
            ['territorial_code' => '8314' , 'subzone_code' => '7'],
            ['territorial_code' => '8401' , 'subzone_code' => '0'],
            ['territorial_code' => '8402' , 'subzone_code' => '2'],
            ['territorial_code' => '8403' , 'subzone_code' => '6'],
            ['territorial_code' => '8404' , 'subzone_code' => '3'],
            ['territorial_code' => '8405' , 'subzone_code' => '4'],
            ['territorial_code' => '8406' , 'subzone_code' => '4'],
            ['territorial_code' => '8407' , 'subzone_code' => '2'],
            ['territorial_code' => '8408' , 'subzone_code' => '6'],
            ['territorial_code' => '8409' , 'subzone_code' => '3'],
            ['territorial_code' => '8410' , 'subzone_code' => '5'],
            ['territorial_code' => '8411' , 'subzone_code' => '4'],
            ['territorial_code' => '8412' , 'subzone_code' => '4'],
            ['territorial_code' => '8413' , 'subzone_code' => '2'],
            ['territorial_code' => '8414' , 'subzone_code' => '6'],
            ['territorial_code' => '8415' , 'subzone_code' => '2'],
            ['territorial_code' => '8416' , 'subzone_code' => '3'],
            ['territorial_code' => '8417' , 'subzone_code' => '3'],
            ['territorial_code' => '8418' , 'subzone_code' => '2'],
            ['territorial_code' => '8419' , 'subzone_code' => '6'],
            ['territorial_code' => '8420' , 'subzone_code' => '3'],
            ['territorial_code' => '8421' , 'subzone_code' => '5'],
            ['territorial_code' => '9101' , 'subzone_code' => '0'],
            ['territorial_code' => '9102' , 'subzone_code' => '2'],
            ['territorial_code' => '9103' , 'subzone_code' => '3'],
            ['territorial_code' => '9104' , 'subzone_code' => '5'],
            ['territorial_code' => '9105' , 'subzone_code' => '4'],
            ['territorial_code' => '9106' , 'subzone_code' => '6'],
            ['territorial_code' => '9107' , 'subzone_code' => '4'],
            ['territorial_code' => '9108' , 'subzone_code' => '3'],
            ['territorial_code' => '9109' , 'subzone_code' => '4'],
            ['territorial_code' => '9110' , 'subzone_code' => '3'],
            ['territorial_code' => '9111' , 'subzone_code' => '2'],
            ['territorial_code' => '9112' , 'subzone_code' => '0'],
            ['territorial_code' => '9113' , 'subzone_code' => '3'],
            ['territorial_code' => '9114' , 'subzone_code' => '4'],
            ['territorial_code' => '9115' , 'subzone_code' => '5'],
            ['territorial_code' => '9116' , 'subzone_code' => '2'],
            ['territorial_code' => '9117' , 'subzone_code' => '4'],
            ['territorial_code' => '9118' , 'subzone_code' => '4'],
            ['territorial_code' => '9119' , 'subzone_code' => '3'],
            ['territorial_code' => '9120' , 'subzone_code' => '5'],
            ['territorial_code' => '9121' , 'subzone_code' => '6'],
            ['territorial_code' => '9201' , 'subzone_code' => '2'],
            ['territorial_code' => '9202' , 'subzone_code' => '6'],
            ['territorial_code' => '9203' , 'subzone_code' => '2'],
            ['territorial_code' => '9204' , 'subzone_code' => '3'],
            ['territorial_code' => '9205' , 'subzone_code' => '2'],
            ['territorial_code' => '9206' , 'subzone_code' => '6'],
            ['territorial_code' => '9207' , 'subzone_code' => '6'],
            ['territorial_code' => '9208' , 'subzone_code' => '6'],
            ['territorial_code' => '9209' , 'subzone_code' => '4'],
            ['territorial_code' => '9210' , 'subzone_code' => '6'],
            ['territorial_code' => '9211' , 'subzone_code' => '3'],
            ['territorial_code' => '10101' , 'subzone_code' => '0'],
            ['territorial_code' => '10102' , 'subzone_code' => '4'],
            ['territorial_code' => '10103' , 'subzone_code' => '3'],
            ['territorial_code' => '10104' , 'subzone_code' => '3'],
            ['territorial_code' => '10105' , 'subzone_code' => '3'],
            ['territorial_code' => '10106' , 'subzone_code' => '5'],
            ['territorial_code' => '10107' , 'subzone_code' => '3'],
            ['territorial_code' => '10108' , 'subzone_code' => '4'],
            ['territorial_code' => '10109' , 'subzone_code' => '3'],
            ['territorial_code' => '10201' , 'subzone_code' => '6'],
            ['territorial_code' => '10202' , 'subzone_code' => '6'],
            ['territorial_code' => '10203' , 'subzone_code' => '6'],
            ['territorial_code' => '10204' , 'subzone_code' => '6'],
            ['territorial_code' => '10205' , 'subzone_code' => '6'],
            ['territorial_code' => '10206' , 'subzone_code' => '6'],
            ['territorial_code' => '10207' , 'subzone_code' => '6'],
            ['territorial_code' => '10208' , 'subzone_code' => '6'],
            ['territorial_code' => '10209' , 'subzone_code' => '6'],
            ['territorial_code' => '10210' , 'subzone_code' => '6'],
            ['territorial_code' => '10301' , 'subzone_code' => '0'],
            ['territorial_code' => '10302' , 'subzone_code' => '3'],
            ['territorial_code' => '10303' , 'subzone_code' => '3'],
            ['territorial_code' => '10304' , 'subzone_code' => '4'],
            ['territorial_code' => '10305' , 'subzone_code' => '3'],
            ['territorial_code' => '10306' , 'subzone_code' => '4'],
            ['territorial_code' => '10307' , 'subzone_code' => '2'],
            ['territorial_code' => '10401' , 'subzone_code' => '2'],
            ['territorial_code' => '10402' , 'subzone_code' => '2'],
            ['territorial_code' => '10403' , 'subzone_code' => '2'],
            ['territorial_code' => '10404' , 'subzone_code' => '2'],
            ['territorial_code' => '11101' , 'subzone_code' => '0'],
            ['territorial_code' => '11102' , 'subzone_code' => '5'],
            ['territorial_code' => '11201' , 'subzone_code' => '2'],
            ['territorial_code' => '11202' , 'subzone_code' => '3'],
            ['territorial_code' => '11203' , 'subzone_code' => '5'],
            ['territorial_code' => '11301' , 'subzone_code' => '4'],
            ['territorial_code' => '11302' , 'subzone_code' => '5'],
            ['territorial_code' => '11303' , 'subzone_code' => '5'],
            ['territorial_code' => '11401' , 'subzone_code' => '4'],
            ['territorial_code' => '11402' , 'subzone_code' => '5'],
            ['territorial_code' => '12101' , 'subzone_code' => '0'],
            ['territorial_code' => '12102' , 'subzone_code' => '4'],
            ['territorial_code' => '12103' , 'subzone_code' => '4'],
            ['territorial_code' => '12104' , 'subzone_code' => '4'],
            ['territorial_code' => '12201' , 'subzone_code' => '4'],
            ['territorial_code' => '12202' , 'subzone_code' => '4'],
            ['territorial_code' => '12301' , 'subzone_code' => '3'],
            ['territorial_code' => '12302' , 'subzone_code' => '4'],
            ['territorial_code' => '12303' , 'subzone_code' => '4'],
            ['territorial_code' => '12401' , 'subzone_code' => '2'],
            ['territorial_code' => '12402' , 'subzone_code' => '2'],
            ['territorial_code' => '13101' , 'subzone_code' => '0'],
            ['territorial_code' => '13102' , 'subzone_code' => '5'],
            ['territorial_code' => '13103' , 'subzone_code' => '7'],
            ['territorial_code' => '13104' , 'subzone_code' => '6'],
            ['territorial_code' => '13105' , 'subzone_code' => '2'],
            ['territorial_code' => '13106' , 'subzone_code' => '2'],
            ['territorial_code' => '13107' , 'subzone_code' => '2'],
            ['territorial_code' => '13108' , 'subzone_code' => '8'],
            ['territorial_code' => '13109' , 'subzone_code' => '4'],
            ['territorial_code' => '13110' , 'subzone_code' => '2'],
            ['territorial_code' => '13111' , 'subzone_code' => '5'],
            ['territorial_code' => '13112' , 'subzone_code' => '3'],
            ['territorial_code' => '13113' , 'subzone_code' => '6'],
            ['territorial_code' => '13114' , 'subzone_code' => '0'],
            ['territorial_code' => '13115' , 'subzone_code' => '2'],
            ['territorial_code' => '13116' , 'subzone_code' => '6'],
            ['territorial_code' => '13117' , 'subzone_code' => '8'],
            ['territorial_code' => '13118' , 'subzone_code' => '2'],
            ['territorial_code' => '13119' , 'subzone_code' => '4'],
            ['territorial_code' => '13120' , 'subzone_code' => '0'],
            ['territorial_code' => '13121' , 'subzone_code' => '6'],
            ['territorial_code' => '13122' , 'subzone_code' => '4'],
            ['territorial_code' => '13123' , 'subzone_code' => '0'],
            ['territorial_code' => '13124' , 'subzone_code' => '0'],
            ['territorial_code' => '13125' , 'subzone_code' => '0'],
            ['territorial_code' => '13126' , 'subzone_code' => '3'],
            ['territorial_code' => '13127' , 'subzone_code' => '0'],
            ['territorial_code' => '13128' , 'subzone_code' => '4'],
            ['territorial_code' => '13129' , 'subzone_code' => '7'],
            ['territorial_code' => '13130' , 'subzone_code' => '8'],
            ['territorial_code' => '13131' , 'subzone_code' => '9'],
            ['territorial_code' => '13132' , 'subzone_code' => '0'],
            ['territorial_code' => '13201' , 'subzone_code' => '6'],
            ['territorial_code' => '13202' , 'subzone_code' => '8'],
            ['territorial_code' => '13203' , 'subzone_code' => '7'],
            ['territorial_code' => '13301' , 'subzone_code' => '4'],
            ['territorial_code' => '13302' , 'subzone_code' => '6'],
            ['territorial_code' => '13303' , 'subzone_code' => '5'],
            ['territorial_code' => '13401' , 'subzone_code' => '4'],
            ['territorial_code' => '13402' , 'subzone_code' => '6'],
            ['territorial_code' => '13403' , 'subzone_code' => '5'],
            ['territorial_code' => '13404' , 'subzone_code' => '6'],
            ['territorial_code' => '13501' , 'subzone_code' => '5'],
            ['territorial_code' => '13502' , 'subzone_code' => '5'],
            ['territorial_code' => '13503' , 'subzone_code' => '6'],
            ['territorial_code' => '13504' , 'subzone_code' => '6'],
            ['territorial_code' => '13505' , 'subzone_code' => '5'],
            ['territorial_code' => '13601' , 'subzone_code' => '5'],
            ['territorial_code' => '13602' , 'subzone_code' => '6'],
            ['territorial_code' => '13603' , 'subzone_code' => '5'],
            ['territorial_code' => '13604' , 'subzone_code' => '5'],
            ['territorial_code' => '13605' , 'subzone_code' => '5'],
            ['territorial_code' => '14101' , 'subzone_code' => '0'],
            ['territorial_code' => '14102' , 'subzone_code' => '1'],
            ['territorial_code' => '14103' , 'subzone_code' => '4'],
            ['territorial_code' => '14104' , 'subzone_code' => '2'],
            ['territorial_code' => '14105' , 'subzone_code' => '2'],
            ['territorial_code' => '14106' , 'subzone_code' => '2'],
            ['territorial_code' => '14107' , 'subzone_code' => '3'],
            ['territorial_code' => '14108' , 'subzone_code' => '4'],
            ['territorial_code' => '14201' , 'subzone_code' => '2'],
            ['territorial_code' => '14202' , 'subzone_code' => '3'],
            ['territorial_code' => '14203' , 'subzone_code' => '2'],
            ['territorial_code' => '14204' , 'subzone_code' => '2'],
            ['territorial_code' => '15101' , 'subzone_code' => '0'],
            ['territorial_code' => '15102' , 'subzone_code' => '2'],
            ['territorial_code' => '15201' , 'subzone_code' => '2'],
            ['territorial_code' => '15202' , 'subzone_code' => '2']
        ];

        $count = 0;
        foreach ($subzone_codes as $subzone_code) {
            $admin_level_3 = $adminLevel3Repository->search([
                'territorial_code' => $subzone_code['territorial_code']
            ])->first();

            if (!$admin_level_3) {
                echo "\n{$subzone_code['territorial_code']} not found.\n";
                continue;
            }

            $adminLevel3Repository->update($admin_level_3, [
                'subzone_code' => $subzone_code['subzone_code']
            ]);
            
            $count++;
        }
        
        echo "{$count} codes added.\n";
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admin_level_3', function (Blueprint $table) {
            $table->dropColumn('subzone_code');
        });
    }
}