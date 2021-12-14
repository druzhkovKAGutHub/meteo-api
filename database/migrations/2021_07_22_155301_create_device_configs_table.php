<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeviceConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('device_id');

            $table->string('ver', 16);
            $table->bigInteger('ap_interval');
            $table->bigInteger('send_interval');
            $table->string('ssid_sta', 64);
            $table->string('password_sta', 64);
            $table->string('ssid_ap', 64);
            $table->string('password_ap', 64);
            $table->bigInteger('max_count_connect_sta');
            $table->string('key', 64);
            
            $table->string('host', 128);
            $table->bigInteger('port');
            $table->string('login', 128);
            $table->string('password', 128);

            $table->timestamps();
        });

        Schema::table('device_configs', function($table){
            $table->foreign('device_id')->references('id')->on('devices') // устанавливаем зависимости полей
            ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('device_configs');
    }
}
