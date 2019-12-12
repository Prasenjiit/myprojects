<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCsvDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_csv_data', function (Blueprint $table){
                $table->increments('csv_data_id');
                $table->longText('csv_data_filename');
                $table->integer('document_type_id');
                $table->longText('csv_data');
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
        Schema::drop('tbl_csv_data');
    }
}
