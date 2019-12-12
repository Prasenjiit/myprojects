<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_documents', function (Blueprint $table) {
            $table->bigInteger('document_id');
            $table->string('document_type_id',255);
            $table->integer('document_type_column_id');
            $table->string('department_id',255);
            $table->string('stack_id',255);
            $table->string('document_no',255);
            $table->string('document_name',255);
            $table->string('document_file_name',255);
            $table->string('document_path',255);
            $table->decimal('document_version_no', 5, 1);
            $table->string('document_status',255)->comment('Check out,Draft,Published,Unpublished');
            $table->string('document_pre_status',255);
            $table->dateTime('document_checkin_date');
            $table->dateTime('document_checkout_date');
            $table->string('document_checkout_path',255);
            $table->integer('documents_checkout_by');
            $table->string('document_ownership',255);
            $table->date('document_expiry_date',255);
            $table->string('document_assigned_to',255);
            $table->string('document_tagwords',255);
            $table->string('document_created_by',255);
            $table->string('document_modified_by',255)->nullable($value = true);
            $table->timestamp('created_at')->nullable($value = true);
            $table->timestamp('updated_at')->nullable($value = true);
            $table->integer('parent_id');
            $table->integer('document_size');
            $table->double('document_image_scale', 8, 2);
            $table->float('document_image_angle', 8, 2);
            $table->float('document_image_x', 8, 2);
            $table->float('document_image_y', 8, 2);
            $table->float('document_image_w', 8, 2);
            $table->float('document_image_h', 8, 2);
            $table->enum('document_is_image_values_saved', ['1', '0'])->comment('1=>Saved,0=>Not saved')->default('0');
            $table->enum('document_encrypt_status', ['1', '0'])->comment('1=>Encrypted,0=>Decrypted')->default('0');
            $table->string('document_encrypt_password',255)->nullable($value = true);
            $table->string('document_encrypted_by',255)->nullable($value = true);
            $table->dateTime('document_encrypted_on')->nullable($value = true);
            $table->string('document_decrypted_by',255);
            $table->dateTime('document_decrypted_on');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbl_documents');
    }
}
