<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkflowsHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_workflow_histories', function (Blueprint $table){
                $table->increments('workflow_history_id');
                $table->integer('workflow_id');
                $table->string('workflow_name',250);
                $table->string('workflow_color',100);
                $table->string('document_workflow_object_id',20);
                $table->string('document_workflow_object_type',255);
                $table->integer('workflow_stage_id');
                $table->string('workflow_stage_name',250);
                $table->integer('activity_id');
                $table->string('activity_name',250);
                $table->string('document_workflow_responsible_user',250);
                $table->string('document_workflow_activity_by_user',250);
                $table->string('document_workflow_activity_date',50);
                $table->string('document_workflow_activity_due_date',50);
                $table->text('document_workflow_activity_notes');
                $table->string('document_workflow_created_by',250);
                $table->string('document_workflow_updated_by',250);
                $table->dateTime('document_workflow_created_at');
                $table->dateTime('document_workflow_updated_at');
                $table->string('action_activity_name',100);
                $table->text('action_activity_note');
                $table->integer('action_activity_by');
                $table->dateTime('action_activity_date');
                $table->dateTime('created_at');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbl_workflow_histories');
    }
}