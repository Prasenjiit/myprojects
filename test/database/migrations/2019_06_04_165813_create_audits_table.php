<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_audits', function (Blueprint $table) {
            $table->bigInteger('audit_id');
            $table->integer('document_id');
            $table->integer('stack_id');
            $table->integer('department_id');
            $table->integer('document_type_id');
            $table->string('document_no',255);
            $table->string('document_name',255);
            $table->text('document_path');
            $table->string('audit_user_name',255);
            $table->string('audit_owner',255)->nullable($value = true);
            $table->string('audit_action_type',255)->comment('add, edit, delete,print');
            $table->text('audit_action_desc');
            $table->string('audit_user_ip',30);
            $table->string('audit_geo_location',255);
            $table->dateTime('created_at')->nullable($value = true);
            $table->dateTime('updated_at')->nullable($value = true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbl_audits');
    }
}
