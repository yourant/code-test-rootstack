<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrateServiceTypesOnBags extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        try {
            $bags = [];
            $count = 0;
            App\Package::chunk(100, function($packages) use (&$bags, &$count) {
                foreach ($packages as $package) {
                    $bags[$package->bag_id][$package->client_id][$package->service_type_id] = true;
                    $count ++;
                }

                echo "Queried {$count} packages." . PHP_EOL;
            });

            echo PHP_EOL;
            echo PHP_EOL;


            $count = 0;
            $bag_count = count($bags);
            foreach ($bags as $bag_id => $packages) {
                $b = App\Bag::find($bag_id);

                foreach ($packages as $client_id => $service_types) {
                    foreach ($service_types as $service_type_id => $v) {
                        $a = App\Agreement::firstOrCreate([
                          'client_id'       => $client_id,
                          'service_type_id' => $service_type_id
                        ]);

                        $cn38 = $b->detectCn38FromCn35();
                        $d = App\Dispatch::firstOrCreate([
                          'number'       => $cn38,
                          'agreement_id' => $a->id
                        ]);

                        $b->dispatch_id = $d->id;
                        $b->save();

                        echo "Processed {$count} bags of {$bag_count}." . PHP_EOL;
                        ++$count;
                    }
                }
            }
        } catch (Exception $e) {
            echo $e->getTraceAsString() . PHP_EOL;

            throw $e;
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
