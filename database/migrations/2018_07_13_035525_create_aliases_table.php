<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAliasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aliases', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('package_id')->unsigned();
            $table->integer('provider_id')->unsigned()->nullable();
            $table->string('code', 100);
            $table->timestamps();

            $table->foreign('package_id')->references('id')->on('packages')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('provider_id')->references('id')->on('providers')->onUpdate('cascade')->onDelete('cascade');
            $table->index('package_id', 'aliases_package_id_foreign');
            $table->index('provider_id', 'aliases_provider_id_foreign');
            $table->index('code', 'aliases_code_index');
        });

        // Fetch successful prealerts with reference number and insert them into aliases table
        DB::table('packages')
            ->select(['packages.id as package_id', 'prealerts.provider_id', 'prealerts.reference as code', 'prealerts.created_at', 'prealerts.updated_at'])
            ->join('prealerts', 'prealerts.package_id', '=', 'packages.id')
            ->where('prealerts.success', 1)
            ->whereNotNull('prealerts.reference')
            ->orderByDesc('packages.id')
            ->chunk(10000, function ($rows) {
                // Fetch
                $data = $rows->transform(function ($row) {
                    return (array)$row;
                })->toArray();

                // Insert
                DB::table('aliases')->insert($data);
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aliases');
    }
}
