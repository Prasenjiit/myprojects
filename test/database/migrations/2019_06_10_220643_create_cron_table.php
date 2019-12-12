<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCronTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_cron', function (Blueprint $table){
                $table->increments('id');
                $table->integer('workflow_id')->default(0);
                $table->integer('wf_operation_id')->default(0);
                $table->integer('from_stage')->default(0);
                $table->integer('escallation_stage')->default(0);
                $table->integer('escallation_activity_id')->default(0);
                $table->dateTime('created_at')->nullable($value = true);
                $table->dateTime('updated_at')->nullable($value = true);               
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbl_cron');
    }
}
