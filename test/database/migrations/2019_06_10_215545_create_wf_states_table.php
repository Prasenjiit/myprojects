<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWfStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_wf_states', function (Blueprint $table){
                $table->increments('id');
                $table->integer('workflow_id');
                $table->string('state',200);
                $table->text('description');
                $table->string('type',30);
                $table->tinyInteger('stage_action');
                $table->tinyInteger('stage_group');
                $table->float('stage_percentage', 8, 2);
                $table->string('shape',20);
                $table->integer('mark');
                $table->integer('edit');
                $table->text('departments');
                $table->longText('assigned_users');
                $table->tinyInteger('escallation_stage');
                $table->tinyInteger('escallation_activity_id');
                $table->tinyInteger('escallation_day');
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
        Schema::drop('tbl_wf_states');
    }
}
