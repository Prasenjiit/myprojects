<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentsColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_documents_columns', function (Blueprint $table) {
            $table->bigInteger('document_column_id');
            $table->bigInteger('document_id');
            $table->integer('document_type_column_id');
            $table->string('document_column_name',255);
            $table->string('document_column_value',255);
            $table->string('document_column_type',255);
            $table->string('document_file_name',255);
            $table->string('document_file_size',255);
            $table->tinyInteger('document_column_mandatory');
            $table->string('document_column_created_by',255);
            $table->string('document_column_modified_by',255);
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
        Schema::drop('tbl_documents_columns');
    }
}
