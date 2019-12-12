<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWfGroupOperationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_wf_operation_details', function (Blueprint $table){
                $table->increments('id');
                $table->integer('wf_operation_id');
                $table->integer('wf_stage');
                $table->string('wf_stage_name',50);
                $table->integer('activity_id');                
                $table->integer('completed');
                $table->longText('notified_users');
                $table->integer('delegate_user');
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
        Schema::drop('tbl_wf_operation_details');
    }
}
