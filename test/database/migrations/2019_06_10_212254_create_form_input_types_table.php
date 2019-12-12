<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormInputTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_form_input_types', function (Blueprint $table){
                $table->increments('form_input_type');
                $table->string('form_input_type_common',30);
                $table->string('form_input_type_value',30);
				$table->string('form_input_type_name',200);
                $table->string('form_input_icon',50);
                $table->tinyInteger('view_order');
                $table->tinyInteger('is_options');
                $table->tinyInteger('is_required');
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
        Schema::drop('tbl_form_input_types');
    }
}
