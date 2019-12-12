<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_activities', function (Blueprint $table){
                $table->increments('activity_id');
                $table->string('activity_name',255);
                $table->string('activity_modules',255)->nullable($value = true);;
                $table->string('activity_type',255)->comment('approve,reject,on-hold')->nullable($value = true);;
                $table->string('activity_constant',255)->nullable($value = true);;
                $table->integer('last_activity')->nullable($value = true);;
                $table->integer('activity_added_by');
                $table->integer('activity_updated_by');
                $table->dateTime('created_at')->nullable($value = true);;
                $table->dateTime('updated_at')->nullable($value = true);;
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbl_activities');
    }
}
