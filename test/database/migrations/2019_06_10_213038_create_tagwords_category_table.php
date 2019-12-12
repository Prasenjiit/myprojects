<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagwordsCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_tagwords_category', function (Blueprint $table) {
            $table->increments('tagwords_category_id');
            $table->string('tagwords_category_name',255);
            $table->string('tagwords_category_created_by',255);
            $table->string('tagwords_category_modified_by',255);
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
        Schema::drop('tbl_tagwords_category');
    }
}
