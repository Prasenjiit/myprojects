<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWfTransitionsRuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_wf_transition_rule', function (Blueprint $table){
                $table->increments('id');
                $table->integer('transition_id');
                $table->string('rule_condition',50);
                $table->longText('rule_area');
                $table->integer('if_stage');
                $table->integer('else_stage');
                $table->integer('edit');
                $table->integer('sort_order');
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
        Schema::drop('tbl_wf_transition_rule');
    }
}
