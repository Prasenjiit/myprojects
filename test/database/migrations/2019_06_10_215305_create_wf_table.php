<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWfTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_wf', function (Blueprint $table){
                $table->increments('id');
                $table->string('workflow_name',200);
                $table->string('workflow_color',50);
                $table->integer('task_flow');
                $table->dateTime('created_at');
                $table->integer('created_by');
                $table->dateTime('updated_at');
                $table->integer('updated_by');
                $table->string('wf_object_type',20);
                $table->string('wf_object_type_id',30);
                $table->integer('deadline');
                $table->string('deadline_type',20);
                $table->integer('deadline_value');
                $table->text('departments');
                $table->longText('assigned_users');               
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbl_wf');
    }
}
