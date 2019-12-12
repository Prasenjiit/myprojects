<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationRecipientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_notification_recipients', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('notification_id');
            $table->integer('notification_recipient');
            $table->tinyInteger('notification_viewed')->default('0')->comment('1=Read');
            $table->dateTime('created_at');
            $table->dateTime('viewd_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbl_notification_recipients');
    }
}
