<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_form_responses', function (Blueprint $table) {
            $table->increments('form_response_id');
            $table->integer('user_id');
            $table->integer('form_id');
            $table->string('form_response_unique_id',20);
            $table->integer('form_assigned_to');
            $table->string('form_name',255);
            $table->text('form_description');
            $table->integer('form_input_id');
            $table->string('form_input_title',255);
            $table->string('form_input_type',30);
            $table->longText('form_input_options');
            $table->tinyInteger('form_input_require');
            $table->tinyInteger('form_input_file_multiple');
            $table->integer('form_input_order');
            $table->longText('form_response_value');
            $table->string('form_response_selected',255);
            $table->string('document_file_name',255);
            $table->string('form_response_file_size',255);
            $table->integer('response_activity_id');
            $table->string('response_activity_name',50);
            $table->text('response_activity_note');
            $table->integer('response_activity_by');
            $table->dateTime('response_activity_date');
            $table->integer('resp_doc_workflow_id');
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
        Schema::drop('tbl_form_responses');
    }
}
