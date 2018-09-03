<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMercadoLibreCbtInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mercadolibre_cbt_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('package_id');
            $table->text('invoice_url')->nullable();
            $table->timestamps();

            $table->foreign('package_id')->references('id')->on('packages')->onUpdate('cascade')->onDelete('cascade');
            $table->index('package_id', 'mercadolibre_cbt_invoices_package_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('mercadolibre_cbt_invoices');
    }
}
