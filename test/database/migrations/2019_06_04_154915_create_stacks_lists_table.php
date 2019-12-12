<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStacksListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_stacks', function (Blueprint $table) {
            $table->increments('stack_id');
            $table->string('stack_name',255);
            $table->text('stack_description');
            $table->string('stack_created_by',255);
            $table->string('stack_modified_by',255);
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
        Schema::drop('tbl_stacks');
    }
}
