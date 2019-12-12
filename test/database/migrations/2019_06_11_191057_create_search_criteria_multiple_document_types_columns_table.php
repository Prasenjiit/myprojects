<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSearchCriteriaMultipleDocumentTypesColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_search_criteria_multiple_document_types_columns', function (Blueprint $table){
            $table->increments('id');
            $table->integer('search_criteria_id');
            $table->integer('document_type_id');
            $table->integer('document_type_column_id');
            $table->string('document_type_column_name',255);
            $table->string('document_type_column_value',255);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbl_search_criteria_multiple_document_types_columns');
    }
}
