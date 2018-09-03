<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMercadolibreApiTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mercadolibre_cbt_api_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->string('access_token');
            $table->string('refresh_token');
            $table->timestamp('acquired_at');
            $table->timestamp('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('mercadolibre_cbt_api_tokens');
    }
}
