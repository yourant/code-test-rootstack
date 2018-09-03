<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSortingGatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sorting_gates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('sorting_id');
            $table->integer('gate_number')->unsigned()->nullable();
            $table->integer('default')->unsigned()->default(0);
            $table->string('gate_code');
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedInteger('modified_by')->nullable();

            $table->foreign('sorting_id')->references('id')->on('sortings')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('modified_by')->references('id')->on('users')->onDelete('set null');

            $table->index('sorting_id', 'sorting_gates_sorting_id_foreign');
            $table->index('modified_by', 'sorting_gates_modified_by_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sorting_gates');
    }
}
