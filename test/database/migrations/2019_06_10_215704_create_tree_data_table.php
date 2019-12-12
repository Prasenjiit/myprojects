<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTreeDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tree_data', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nm',255);
            $table->integer('doc_count');
            $table->integer('temp_doc_count');
            $table->integer('chk_doc_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tree_data');
    }
}
