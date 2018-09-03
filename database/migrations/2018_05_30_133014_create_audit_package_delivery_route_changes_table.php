<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuditPackageDeliveryRouteChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_package_delivery_route_changes', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer('package_id')->unsigned();
            $table->string('old_delivery_route');
            $table->string('new_delivery_route');
            $table->integer('user_id')->unsigned();
            $table->string('user_email');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('package_id')->references('id')->on('packages')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('audit_package_delivery_route_changes');
    }
}
