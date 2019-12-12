<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagwordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_tagwords', function (Blueprint $table) {
            $table->increments('tagwords_id');
            $table->integer('tagwords_category_id');
            $table->string('tagwords_title',255);
            $table->string('tagwords_created_by',255);
            $table->string('tagwords_modified_by',255);
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
        Schema::drop('tbl_tagwords');
    }
}
