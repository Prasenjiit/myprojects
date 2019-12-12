<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTreeStructTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tree_struct', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lft');
            $table->integer('rgt');
            $table->integer('lvl');
            $table->integer('pid');
            $table->integer('pos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tree_struct');
    }
}
