<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInitialCheckpointToPackages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function(Blueprint $table) {
            $table->integer('initial_checkpoint_id')->unsigned()->nullable()->after('last_checkpoint_id');
            $table->foreign('initial_checkpoint_id')->references('id')->on('checkpoints')->onUpdate('cascade')->onDelete('set null');
        });

        try {
            $count = 0;
            App\Package::chunk(100, function($packages) use (&$count) {
                foreach ($packages as $package) {
                    $package->initial_checkpoint_id = $package->first_checkpoint_id;
                    $package->save();
                    ++$count;
                }

                echo "Queried {$count} packages." . PHP_EOL;
            });
        } catch (Exception $e) {
            echo $e->getMessage() . PHP_EOL;
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
        Schema::table('packages', function (Blueprint $table) {
            $table->dropForeign('packages_initial_checkpoint_id_foreign');
            $table->dropColumn('initial_checkpoint_id');
        });
    }
}
