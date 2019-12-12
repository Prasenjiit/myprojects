<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentTypesColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_document_types_columns', function (Blueprint $table){
                $table->increments('document_type_column_id');
                $table->integer('document_type_id');
                $table->string('document_type_column_name',255);
                $table->string('document_type_column_type',255);
                $table->tinyInteger('document_type_column_mandatory');
                $table->integer('document_type_column_order');
                $table->string('document_type_options',255);
                $table->string('document_type_column_created_by',255);
                $table->string('document_type_column_modified_by',255);
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
        Schema::drop('tbl_document_types_columns');
    }
}
