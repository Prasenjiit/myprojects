<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentsHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_documents_history', function (Blueprint $table) {
            $table->bigInteger('document_history_id');
            $table->bigInteger('document_id');
            $table->string('document_type_id',255);
            $table->integer('document_type_column_id');
            $table->string('department_id',255);
            $table->string('stack_id',255);
            $table->string('document_no',255);
            $table->string('document_name',255);
            $table->string('document_file_name',255);
            $table->integer('document_size');
            $table->dateTime('document_checkin_date');
            $table->dateTime('document_checkout_date');
            $table->string('document_modified_by',255);
            $table->integer('documents_checkout_by');
            $table->string('document_path',255);
            $table->decimal('document_version_no', 5, 1);
            $table->string('document_status',255);
            $table->string('document_history_created_by',255);
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
        Schema::drop('tbl_documents_history');
    }
}
