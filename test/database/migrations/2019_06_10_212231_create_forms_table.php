<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_forms', function (Blueprint $table) {
            $table->increments('form_id');
            $table->string('form_name',255);
            $table->text('form_description');
            $table->string('form_created_by',255);
            $table->string('form_updated_by',255);
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
         Schema::drop('tbl_forms');
    }
}
