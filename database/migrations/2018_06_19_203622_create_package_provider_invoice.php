<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageProviderInvoice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_provider_invoice', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('package_id');
            $table->unsignedInteger('provider_invoice_id');
            $table->decimal('amount', 10, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('package_id')->references('id')->on('packages')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('provider_invoice_id')->references('id')->on('provider_invoices')->onUpdate('cascade')->onDelete('cascade');

            $table->index('package_id', 'package_provider_invoice_package_id_foreign');
            $table->index('provider_invoice_id', 'package_provider_invoice_provider_invoice_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('package_provider_invoice');
    }
}
