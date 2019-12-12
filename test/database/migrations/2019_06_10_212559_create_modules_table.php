<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_modules', function (Blueprint $table){
                $table->increments('module_id');
                $table->longText('module_name');
                $table->longText('module_activation_key');
                $table->longText('module_activation_count');
                $table->longText('module_activation_date');
                $table->longText('module_expiry_date');
                $table->longText('module_no_expiry');
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
        Schema::drop('tbl_modules');
    }
}
