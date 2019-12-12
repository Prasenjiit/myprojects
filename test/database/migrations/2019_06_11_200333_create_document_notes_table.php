<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_document_notes', function (Blueprint $table) {
            $table->bigInteger('document_notes_id');
            $table->integer('document_id');
            $table->text('document_note');
            $table->string('document_note_created_by',255);
            $table->timestamp('created_at');
            $table->timestamp('updated_at');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbl_document_notes');
    }
}
