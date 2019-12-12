<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormInputsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_form_inputs', function (Blueprint $table) {
            $table->increments('form_input_id');
            $table->integer('form_id');
            $table->string('form_input_title',255);
            $table->string('form_input_type',255);
            $table->tinyInteger('form_input_require');
            $table->longText('form_input_options');
            $table->tinyInteger('form_input_repeat');
            $table->text('form_input_validation');
            $table->tinyInteger('form_input_edit');
            $table->tinyInteger('form_input_file_multiple');
            $table->integer('form_input_order');
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
        Schema::drop('tbl_form_inputs');
    }
}
