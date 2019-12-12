<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDatatableStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('datatables_states', function (Blueprint $table){
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('type');
            $table->string('view',200);
            $table->text('state');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('datatables_states');
    }
}
