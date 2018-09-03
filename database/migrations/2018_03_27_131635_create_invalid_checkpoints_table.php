<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvalidCheckpointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invalid_checkpoints', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('package_id')->unsigned()->nullable();
            $table->string('key')->nullable();
            $table->string('description')->nullable();
            $table->datetime('checkpoint_at')->nullable();
            $table->datetime('sent_at')->nullable();
            $table->timestamps();

            $table->foreign('package_id')->references('id')->on('packages')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invalid_checkpoints');
    }
}
