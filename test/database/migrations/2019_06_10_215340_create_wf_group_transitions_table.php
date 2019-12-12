<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWfGroupTransitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_wf_group_transitions', function (Blueprint $table){
                $table->increments('id');
                $table->integer('operation_id');
                $table->integer('transition_id');
                $table->integer('user_id');                
                $table->integer('activity_id');
                $table->float('approval_percentage', 8, 2);
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
        Schema::drop('tbl_wf_group_transitions');
    }
}
