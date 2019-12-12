<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSearchCriteriaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_search_criteria', function (Blueprint $table) {
            $table->bigInteger('search_criteria_id');
            $table->string('criteria_name',255);
            $table->enum('search_option', ['AND','OR']);
            $table->integer('user_id');
            $table->string('document_type_id',255);
            $table->string('department_id',255);
            $table->string('document_name',255);
            $table->string('docno',255);
            $table->string('stack_id',255);
            $table->string('ownership',255);
            $table->string('tagwords_category_id',255);
            $table->string('tbl_tagwords',255);
            $table->string('created_date_from',255);
            $table->string('created_date_to',255);
            $table->string('last_modified_from',255);
            $table->string('last_modified_to',255);          
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbl_search_criteria');
    }
}
