<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCameraHasGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cameras_has_groups', function (Blueprint $table) {
            $table->bigInteger('camera_id')->unsigned();
            $table->bigInteger('group_id')->unsigned();
        });

        Schema::table('cameras_has_groups', function($table){
            $table->foreign('camera_id')->references('id')->on('cameras') // устанавливаем зависимости полей
            ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('cameras_groups')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->primary(['camera_id', 'group_id']); // ключи
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cameras_has_groups');
    }
}
