<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTempCoumentsNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_temp_document_notes', function (Blueprint $table) {
            $table->increments('document_notes_id');
            $table->integer('document_id');
            $table->text('document_note');
            $table->string('document_note_created_by',255);
            $table->string('document_note_modified_by',255);
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
        Schema::drop('tbl_temp_document_notes');
    }
}
