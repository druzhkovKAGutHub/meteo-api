<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserHasCamerasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_has_cameras', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('camera_id')->unsigned();
        });

        Schema::table('user_has_cameras', function ($table) {
            $table->foreign('camera_id')->references('id')->on('cameras') // устанавливаем зависимости полей
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->primary(['camera_id', 'user_id']); // ключи
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_has_cameras');
    }
}
