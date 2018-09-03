<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer('bag_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('tracking_number');
            $table->string('customer_tracking_number');
            $table->string('sales_order_number')->nullable();
            $table->string('buyer');
            $table->string('company');
            $table->string('address1');
            $table->string('address2')->nullable();
            $table->string('address3')->nullable();
            $table->string('city');
            $table->string('zip');
            $table->string('country');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->decimal('value');
            $table->decimal('net_weight')->default(0);
            $table->decimal('weight')->default(0);
            $table->decimal('width')->default(0);
            $table->decimal('height')->default(0);
            $table->decimal('length')->default(0);
            $table->string('service_type');
            $table->string('classification');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('bag_id')->references('id')->on('bags')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('packages');
    }
}
