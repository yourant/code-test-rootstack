<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSortingGateCriteriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sorting_gate_criterias', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('sorting_gate_id');
            $table->unsignedInteger('sorting_type_id');
            $table->decimal('after_than')->nullable();
            $table->decimal('before_than')->nullable();
            $table->string('criteria_code')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedInteger('modified_by')->nullable();

            $table->foreign('sorting_gate_id')->references('id')->on('sorting_gates')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('sorting_type_id')->references('id')->on('sorting_types')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('modified_by')->references('id')->on('users')->onDelete('set null');

            $table->index('sorting_gate_id', 'sorting_gate_criterias_sorting_gate_id_foreign');
            $table->index('sorting_type_id', 'sorting_gate_criterias_sorting_type_id_foreign');
            $table->index('modified_by', 'sorting_gate_criterias_modified_by_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sorting_gate_criterias');
    }
}
