<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWfTransitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_wf_transitions', function (Blueprint $table){
                $table->increments('id');
                $table->integer('workflow_id');
                $table->string('name',200);
                $table->integer('activity_id');
                $table->integer('to_state');
                $table->integer('edit');
                $table->integer('tr_order');
                $table->tinyInteger('with_rule');
                $table->text('rule_area');
                $table->longText('rule_array');
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
        Schema::drop('tbl_wf_transitions');
    }
}
