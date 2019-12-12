<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFtpDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ftp_details', function (Blueprint $table) {
            $table->increments('ftp_details_id');
            $table->string('ftp_details_name',255);
            $table->string('ftp_details_host',255);
            $table->string('ftp_details_port',255);
            $table->string('ftp_details_username',255);
            $table->string('ftp_details_password',255);
            $table->tinyInteger('ftp_details_test');
            $table->dateTime('ftp_details_update');
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
        Schema::drop('tbl_ftp_details');
    }
}
