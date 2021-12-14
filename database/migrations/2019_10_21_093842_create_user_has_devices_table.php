<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserHasDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_has_devices', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned();
            $table->integer('device_id')->unsigned();
        });

        Schema::table('user_has_devices', function($table){
            $table->foreign('device_id')->references('id')->on('devices') // устанавливаем зависимости полей
            ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->primary(['device_id', 'user_id']); // ключи
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_has_devices');
    }
}
