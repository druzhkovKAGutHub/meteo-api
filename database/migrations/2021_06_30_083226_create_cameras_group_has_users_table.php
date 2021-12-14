<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCamerasGroupHasUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cameras_group_has_users', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('group_id')->unsigned();
        });

        Schema::table('cameras_group_has_users', function($table){
            $table->foreign('user_id')->references('id')->on('users') // устанавливаем зависимости полей
            ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('cameras_groups')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->primary(['user_id', 'group_id']); // ключи
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cameras_group_has_users');
    }
}
