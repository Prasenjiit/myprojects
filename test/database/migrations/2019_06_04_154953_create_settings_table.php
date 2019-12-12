<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_settings', function (Blueprint $table) {
            $table->increments('settings_id');
            $table->string('settings_company_name',100);
            $table->text('settings_address');
            $table->string('settings_email',100);
            $table->string('settings_logo',100);
            $table->string('settings_document_no',255);
            $table->string('settings_document_name',255);
            $table->string('settings_department_name',255);
            $table->string('settings_user_folder',50);
            $table->integer('settings_login_attempts');
            $table->integer('settings_login_attempt_time');
            $table->integer('settings_pasword_expiry');
            $table->integer('settings_document_expiry');
            $table->enum('settings_alphabets', ['1', '0']);
            $table->enum('settings_numerics', ['1', '0']);
            $table->enum('settings_special_characters', ['1', '0']);
            $table->enum('settings_capital_and_small', ['1', '0']);
            $table->integer('settings_password_length_from');
            $table->integer('settings_password_length_to');
            $table->text('settings_file_extensions');
            $table->integer('settings_rows_per_page');
            $table->string('settings_timezone',100);
            $table->string('settings_dateformat',50);
            $table->string('settings_timeformat',50);
            $table->string('settings_datetimeformat',50);
            $table->tinyInteger('settings_ftp');
            $table->string('settings_encryption_pwd',255);
            $table->longText('settings_installation_date');
            $table->longText('settings_expiry_date');
            $table->longText('settings_no_of_users');
			$table->longText('settings_view_only_users');
            $table->longText('settings_license_key');
            $table->longText('settings_active');
            $table->longText('settings_volume_label');
			$table->tinyInteger('settings_install');
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
        Schema::drop('tbl_settings');
    }
}
