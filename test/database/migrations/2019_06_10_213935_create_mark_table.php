<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mark', function (Blueprint $table){
                $table->increments('id');
                $table->string('document_filename', 255)->nullable($value = true);
                $table->string('document_relative_path',255)->nullable($value = true);
                $table->text('selection_text')->nullable($value = true);
                $table->integer('has_selection')->nullable($value = true);
                $table->string('color',10)->nullable($value = true);
                $table->string('selection_info',30)->nullable($value = true);
                $table->tinyInteger('readonly')->nullable($value = true);
                $table->string('type',30)->nullable($value = true);
                $table->string('displayFormat',30)->nullable($value = true);
                $table->text('note')->nullable($value = true);
                $table->integer('pageIndex')->nullable($value = true);
                $table->float('positionX', 8, 2)->nullable($value = true);
                $table->float('positionY', 8, 2)->nullable($value = true);
                $table->integer('width')->nullable($value = true);
                $table->integer('height')->nullable($value = true);
                $table->tinyInteger('collapsed')->nullable($value = true);
                $table->text('points')->nullable($value = true);
                $table->dateTime('datecreated')->nullable($value = true);
                $table->dateTime('datechanged')->nullable($value = true);
                $table->string('author',30)->nullable($value = true);
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('mark');
    }
}
