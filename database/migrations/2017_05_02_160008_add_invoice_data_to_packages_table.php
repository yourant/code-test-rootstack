<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInvoiceDataToPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function(Blueprint $table) {
            $table->string('invoice_number', 20)->nullable()->after('classification');
            $table->decimal('invoice_amount')->nullable()->after('invoice_number');
            $table->string('invoice_currency', 3)->nullable()->after('invoice_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packages', function(Blueprint $table) {
            $table->dropColumn('invoice_number', 20);
            $table->dropColumn('invoice_amount');
            $table->dropColumn('invoice_currency', 3);
        });
    }
}
