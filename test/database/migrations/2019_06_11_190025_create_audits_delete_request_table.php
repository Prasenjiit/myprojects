<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuditsDeleteRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_audits_delete_request', function (Blueprint $table){
            $table->increments('audits_delete_request_id');
            $table->dateTime('audits_delete_request_date');
            $table->string('audits_delete_request_username',255);
            $table->string('audits_delete_request_approved_by',255);
            $table->tinyInteger('audits_delete_request_status')->default(0);
            $table->integer('audits_delete_request_approved_by_who')->comment('1=>Approved by this user');
            $table->dateTime('delete_from_date');
            $table->dateTime('delete_to_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbl_audits_delete_request');
    }
}
