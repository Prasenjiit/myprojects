<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormWorkflowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_form_workflows', function (Blueprint $table){
                $table->increments('id');
                $table->integer('form_id');
                $table->tinyInteger('edit');
                $table->integer('form_workflow_id');
                $table->integer('form_activity_id');
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
        Schema::drop('tbl_form_workflows');
    }
}
