<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFilenameToDownloads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('downloads', function(Blueprint $table) {
            $table->string('filename')->nullable()->after('hash');
            $table->string('bucket')->nullable()->after('filename');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('downloads', function(Blueprint $table) {
            $table->dropColumn('filename');
            $table->dropColumn('bucket');
        });
    }
}
