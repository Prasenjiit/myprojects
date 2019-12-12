<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_departments', function (Blueprint $table) {
            $table->increments('department_id');
            $table->tinyInteger('department_order');
            $table->string('department_name',255);
            $table->text('department_description');
            $table->string('department_created_by',255);
            $table->string('department_modified_by',255);
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
        Schema::drop('tbl_departments');
    }
}
