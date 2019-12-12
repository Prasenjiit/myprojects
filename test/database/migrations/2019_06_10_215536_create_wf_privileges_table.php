<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWfPrivilegesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_wf_privileges', function (Blueprint $table){
            $table->increments('wf_privileges_id');
            $table->integer('workflow_id');
            $table->string('privilege_key',100);
            $table->integer('privilege_status');
            $table->text('privilege_value_user');                
            $table->text('privilege_value_department');         
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbl_wf_privileges');
    }
}
