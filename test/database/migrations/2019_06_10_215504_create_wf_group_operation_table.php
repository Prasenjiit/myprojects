<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWfGroupOperationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_wf_operation', function (Blueprint $table){
                $table->increments('id');
                $table->string('wf_operation_name',200);
                $table->integer('wf_id');
                $table->string('wf_object_id',50);                
                $table->string('wf_object_type',50);
                $table->integer('current_stage');
                $table->integer('completed_stage');
                $table->integer('completed_activity');
                $table->integer('completed');
                $table->integer('created_by');
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
        Schema::drop('tbl_wf_group_transitions');
    }
}
