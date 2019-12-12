<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_email_notifications', function (Blueprint $table){
                $table->increments('email_notification_id');
                $table->tinyInteger('email_notification_activity_task_notifications');
                $table->tinyInteger('email_notification_form_notifications');
                $table->tinyInteger('email_notification_document_notifications');
                $table->tinyInteger('email_notification_signin_notifications');
                $table->tinyInteger('email_notification_override_email_notifications_settings');
                $table->tinyInteger('email_notification_overwrite_preferences');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbl_email_notifications');
    }
}
