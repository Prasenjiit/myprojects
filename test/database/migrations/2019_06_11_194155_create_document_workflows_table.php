<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentWorkflowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_document_workflows', function (Blueprint $table){
                $table->increments('document_workflow_id');
                $table->string('document_workflow_object_id',20);
                $table->string('document_workflow_object_type',250);
                $table->integer('workflow_stage_id');
                $table->integer('activity_id');
                $table->integer('activity_order');
                $table->string('document_workflow_responsible_user',250);
                $table->tinyInteger('document_workflow_notifcation_to_status');
                $table->string('document_workflow_activity_by_user',250);
                $table->string('document_workflow_activity_date',50);
                $table->string('document_workflow_activity_due_date',50);
                $table->text('document_workflow_activity_notes');
                $table->string('document_workflow_created_by',250);
                $table->string('document_workflow_updated_by',250);
                $table->integer('action_activity');
                $table->string('action_activity_name',50);
                $table->string('action_activity_note',255);
                $table->integer('action_activity_by');
                $table->dateTime('action_activity_date');
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
        Schema::drop('tbl_document_workflows');
    }
}
