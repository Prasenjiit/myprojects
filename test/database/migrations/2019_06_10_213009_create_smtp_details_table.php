<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmtpDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_smtp_details', function (Blueprint $table) {
            $table->increments('smtp_details_id');
            $table->enum('smtp_details_unique_key',['other','gmail','outlook','exchange','yahoo'])->comment('This account is active and it is the unique key.');
            $table->enum('smtp_details_active_account',['active','inactive']);
            $table->string('smtp_details_user_authentication',255)->nullable($value = true);
            $table->string('smtp_details_username',255)->nullable($value = true);
            $table->string('smtp_details_password',255)->nullable($value = true);
            $table->string('smtp_details_mailserver',255)->nullable($value = true);
            $table->string('smtp_details_port',11);
            $table->enum('smtp_details_tls_ssl',['none','ssl','tls']);
            $table->string('smtp_details_fromname',255);
            $table->string('smtp_details_fromaddress',255);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbl_smtp_details');
    }
}
