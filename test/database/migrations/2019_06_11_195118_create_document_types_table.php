<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_document_types', function (Blueprint $table){
                $table->increments('document_type_id');
                $table->tinyInteger('document_type_order');
                $table->string('document_type_name',255);
                $table->text('document_type_description');
                $table->string('document_type_column_no',250);
                $table->string('document_type_column_name',250);
                $table->string('document_type_created_by',255);
                $table->string('document_type_modified_by',255);
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
        Schema::drop('tbl_document_types');
    }
}
