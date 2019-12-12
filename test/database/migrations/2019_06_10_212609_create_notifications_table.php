<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('tbl_notifications', function (Blueprint $table){
                $table->increments('notification_id');
                $table->string('notification_type',20)->nullable($value = true);
                $table->string('notification_subtype',30)->nullable($value = true);
                $table->tinyInteger('notification_priotity')->default('1')->comment('1=Low');
                $table->text('notification_title')->nullable($value = true);
                $table->text('notification_details')->nullable($value = true);
                $table->string('notification_link',255);
                $table->integer('notification_sender')->default('0');
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
        Schema::drop('tbl_notifications');
    }
}
