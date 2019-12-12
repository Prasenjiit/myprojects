<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWfGroupOperationActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_wf_operation_activity', function (Blueprint $table){
                $table->increments('id');
                $table->integer('wf_operation_id');
                $table->integer('wf_stage');
                $table->integer('activity_id');                
                $table->integer('activity_order');
                $table->integer('assigned_user');
                $table->integer('assigned_by');
                $table->date('due_date');
                $table->text('activity_note');
                $table->integer('completed');
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
        Schema::drop('tbl_wf_operation_activity');
    }
}
