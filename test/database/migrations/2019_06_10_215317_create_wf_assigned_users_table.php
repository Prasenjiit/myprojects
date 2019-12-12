<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWfAssignedUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_wf_assigned_users', function (Blueprint $table){
                $table->increments('id');
                $table->integer('operation_id');
                $table->integer('stage_id');
                $table->integer('user_id');                
                $table->tinyInteger('action_taken_by');
                $table->integer('delegated_user');
                $table->integer('activity_id');
                $table->smallInteger('instant_delegation'); 
                $table->dateTime('created_at');
                $table->dateTime('updated_at');            
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbl_wf_assigned_users');
    }
}
