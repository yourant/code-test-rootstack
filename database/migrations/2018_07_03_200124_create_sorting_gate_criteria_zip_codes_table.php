<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSortingGateCriteriaZipCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sorting_gate_criteria_zip_code', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('zip_code_id');
            $table->unsignedInteger('sorting_gate_criteria_id');
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedInteger('modified_by')->nullable();

            $table->foreign('zip_code_id')->references('id')->on('zip_codes')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('sorting_gate_criteria_id')->references('id')->on('sorting_gate_criterias')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('modified_by')->references('id')->on('users')->onDelete('set null');

            $table->index('zip_code_id', 'sorting_gate_criteria_zip_code_zip_code_id_foreign');
            $table->index('sorting_gate_criteria_id', 'sorting_gate_criteria_zip_code_sorting_gate_criteria_id_foreign');
            $table->index('modified_by', 'sorting_gate_criteria_zip_code_modified_by_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sorting_gate_criteria_zip_code');
    }
}
