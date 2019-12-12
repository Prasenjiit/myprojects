<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRoleActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_user_role_activities', function (Blueprint $table) {
            $table->increments('user_role_activity_id');
            $table->integer('user_role_id');
            $table->string('user_role_activity_name',255);
            $table->tinyInteger('user_role_activity_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbl_user_role_activities');
    }
}
