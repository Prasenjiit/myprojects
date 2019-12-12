<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFaqTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_faq', function (Blueprint $table){
            $table->increments('faq_id');
            $table->string('faq_title',100);
            $table->text('faq_description');
            $table->integer('faq_added_by');
            $table->integer('faq_updated_by');
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
        Schema::drop('tbl_faq');
    }
}
