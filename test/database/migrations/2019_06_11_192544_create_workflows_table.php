<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkflowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_workflows', function (Blueprint $table){
                $table->increments('workflow_stage_id');
                $table->string('workflow_stage_name',100);
                $table->integer('workflow_stage_order');
                $table->integer('workflow_id');
                $table->string('workflow_name',250);
                $table->string('workflow_color',255);
                $table->string('workflow_added_by',100);
                $table->string('workflow_updated_by',100);
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
