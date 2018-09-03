<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSortingSortingTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sorting_sorting_type', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('sorting_id');
            $table->unsignedInteger('sorting_type_id');
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedInteger('modified_by')->nullable();

            $table->foreign('sorting_id')->references('id')->on('sortings')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('sorting_type_id')->references('id')->on('sorting_types')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('modified_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');

            $table->index('sorting_id', 'sorting_sorting_type_sorting_id_foreign');
            $table->index('sorting_type_id', 'sorting_sorting_type_sorting_type_id_foreign');
            $table->index('modified_by', 'sorting_sorting_type_modified_by_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sorting_sorting_type');
    }
}
