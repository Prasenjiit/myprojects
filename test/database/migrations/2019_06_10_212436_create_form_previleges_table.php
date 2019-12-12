<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormPrevilegesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_form_privileges', function (Blueprint $table){
                $table->increments('form_privileges_id');
                $table->integer('form_id');
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
        Schema::drop('tbl_form_privileges');
    }
}
