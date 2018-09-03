<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClientIdToPackages extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('packages', function (Blueprint $table) {
            $table->dropForeign('packages_user_id_foreign');
            $table->renameColumn('user_id', 'uploaded_by');
            $table->foreign('uploaded_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');

            $table->integer('client_id')->unsigned()->nullable()->after('id');

            $table->foreign('client_id')->references('id')->on('clients')->onUpdate('cascade')->onDelete('set null');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('packages', function (Blueprint $table) {
            $table->dropForeign('packages_client_id_foreign');
            $table->dropColumn('client_id');

            $table->dropForeign('packages_uploaded_by_foreign');
            $table->renameColumn('uploaded_by', 'user_id');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
        });
	}

}
