<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username',255);
            $table->string('email',255);
            $table->string('password',255);
            $table->date('password_date');
            $table->string('user_full_name',255);
            $table->string('department_id',100);
            $table->enum('user_role', ['1', '2', '3', '4']);
            $table->string('user_permission',255);
            $table->string('user_form_permission',255);
            $table->string('user_workflow_permission',255);
            $table->tinyInteger('user_status');
            $table->tinyInteger('login_status');
            $table->enum('user_lock_status',['0', '1']);
            $table->string('user_ip',255);
            $table->string('user_location',255);
            $table->tinyInteger('user_activity_task_notifications');
            $table->tinyInteger('user_form_notifications');
            $table->tinyInteger('user_document_notifications');
            $table->tinyInteger('user_signin_notifications');
            $table->date('user_login_expiry');
            $table->integer('user_login_count');
            $table->dateTime('user_login_count_date');
            $table->string('user_created_by',255);
            $table->string('user_modified_by',255);
            $table->string('remember_token',100);
            $table->dateTime('user_lastlogin_date');
            $table->text('dashboard_widgets');
            $table->string('user_skin',255);
            $table->string('user_documents_default_view',20);
            $table->integer('report_to');
            $table->integer('delegate_user');
            $table->date('delegate_from_date');
            $table->date('delegate_to_date');
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
        Schema::drop('tbl_users');
    }
}
