<?php
use database\seeds;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::table('tbl_departments')->insert(['department_id'=>1,'department_order'=>'1','department_name'=>'AGREEMENT','department_description'=>'agrement dept']);

        // seed tbl_settings
        $data= array(
            'settings_company_name' => 'Mathenson',
            'settings_address' => '106,Onkar Industrial Estate,
Opp Kanjurmarg Rly Stn, (W)
Mumbai - 400078.
Tel. : 022-25778888',
            'settings_email' => 'mathenson@gmail.com',
            'settings_document_no' => 'Document Number',
            'settings_document_name' => 'Document Name',
            'settings_department_name' => 'Department',
            'settings_user_folder' => 'fileeazy',
            'settings_login_attempts' => '5',
            'settings_login_attempt_time' => '5',
            'settings_pasword_expiry' => '90',
            'settings_document_expiry' => '30',
            'settings_alphabets' => '1',
            'settings_numerics' => '1',
            'settings_special_characters' => '0',
            'settings_capital_and_small' => '0',
            'settings_password_length_from' => '6',
            'settings_password_length_to' => '10',
            'settings_file_extensions' =>  '.pdf, .doc, .docx, .xls, .xlsx, .flv, .mp4, .ogv, .mp3, .ogg, .wav, .gif, .jpg, .jpeg, .png, .tif,
 .dwg, .zip (.tif will be converted in to pdf before uploading).',
            'settings_rows_per_page' => '30',
            'settings_timezone' => 'Asia/Kolkata',
            'settings_dateformat' => 'd-m-Y',
            'settings_timeformat' => 'h:i A',
            'settings_datetimeformat' => 'd-m-Y h:i A',
            'settings_ftp' => '0',
            'settings_install' => '0'            
        );
        Db::table('tbl_settings')->insert($data);

        //seed tbl_user
        $data= array(
            'username' => 'adminuser',
            'email' => 'adminuser@fileeazy.com',
            'password' => '$2y$10$vIJP.eX8fBLnM3D8rO0mUui5q2yawDRPl3yUqTNEgIEu5oD5yZ5xq',
            'user_full_name' => 'Administrator',
            'user_role' => '1',
            'user_permission'=> 'add,edit,view,delete,checkout,import,export,workflow,decrypt',
            'user_form_permission'=> 'add,edit,view,delete,export',
            'user_workflow_permission'=> 'add,edit,view,delete',
            'user_status'=> '1',
            'login_status'=> '1',
            'user_lock_status'=> '0'
        );
        Db::table('tbl_users')->insert($data);

        //seed tbl_activities
        $data = array(
            'activity_name' => 'Email',
            'activity_modules' => 'workflows',
            'activity_added_by'=> '1',
            'activity_updated_by'=> '1',
            'created_at'=> date('Y-m-d H:i:s'),
            'updated_at'=> date('Y-m-d H:i:s')
        );
        Db::table('tbl_activities')->insert($data);

        $data = array(
            'activity_name' => 'Assign',
            'activity_modules' => 'workflows',
            'activity_added_by'=> '1',
            'activity_updated_by'=> '1',
            'created_at'=> date('Y-m-d H:i:s'),
            'updated_at'=> date('Y-m-d H:i:s')
        );
        Db::table('tbl_activities')->insert($data);

        $data = array(
            'activity_name' => 'Phone',
            'activity_modules' => 'workflows',
            'activity_added_by'=> '1',
            'activity_updated_by'=> '1',
            'created_at'=> date('Y-m-d H:i:s'),
            'updated_at'=> date('Y-m-d H:i:s')
        );
        Db::table('tbl_activities')->insert($data);

        $data = array(
            'activity_name' => 'Follow Up',
            'activity_modules' => 'workflows',
            'activity_added_by'=> '1',
            'activity_updated_by'=> '1',
            'created_at'=> date('Y-m-d H:i:s'),
            'updated_at'=> date('Y-m-d H:i:s')
        );
        Db::table('tbl_activities')->insert($data);

        $data = array(
            'activity_name' => 'Approve',
            'activity_modules' => 'form_action',
            'activity_constant' => 'approve',
            'activity_added_by'=> '1',
            'activity_updated_by'=> '1',
            'created_at'=> date('Y-m-d H:i:s'),
            'updated_at'=> date('Y-m-d H:i:s')
        );
        Db::table('tbl_activities')->insert($data);

        $data = array(
            'activity_name' => 'Completed',
            'activity_modules' => 'workflows',
            'activity_added_by'=> '1',
            'activity_updated_by'=> '1',
            'created_at'=> date('Y-m-d H:i:s'),
            'updated_at'=> date('Y-m-d H:i:s')
        );
        Db::table('tbl_activities')->insert($data);

        $data = array(
            'activity_name' => 'Decline',
            'activity_modules' => 'workflows',
            'activity_added_by'=> '1',
            'activity_updated_by'=> '1',
            'created_at'=> date('Y-m-d H:i:s'),
            'updated_at'=> date('Y-m-d H:i:s')
        );
        Db::table('tbl_activities')->insert($data);

        $data = array(
            'activity_name' => 'On-Hold',
            'activity_modules' => 'form_action',
            'activity_type' => 'on-hold',
            'activity_constant' => 'on-hold',
            'activity_added_by'=> '1',
            'activity_updated_by'=> '1',
            'created_at'=> date('Y-m-d H:i:s'),
            'updated_at'=> date('Y-m-d H:i:s')
        );
        Db::table('tbl_activities')->insert($data);

        $data = array(
            'activity_name' => 'Review',
            'activity_modules' => 'workflows',
            'activity_added_by'=> '1',
            'activity_updated_by'=> '1',
            'created_at'=> date('Y-m-d H:i:s'),
            'updated_at'=> date('Y-m-d H:i:s')
        );
        Db::table('tbl_activities')->insert($data);

        $data = array(
            'activity_name' => 'Reject',
            'activity_modules' => 'form_action',
            'activity_type' => 'reject',
            'activity_constant' => 'reject',
            'activity_added_by'=> '1',
            'activity_updated_by'=> '1',
            'created_at'=> date('Y-m-d H:i:s'),
            'updated_at'=> date('Y-m-d H:i:s')
        );
        Db::table('tbl_activities')->insert($data);

        $data = array(
            'activity_name' => 'Cancel',
            'activity_modules' => 'workflows',
            'activity_added_by'=> '1',
            'activity_updated_by'=> '1',
            'created_at'=> date('Y-m-d H:i:s'),
            'updated_at'=> date('Y-m-d H:i:s')
        );
        Db::table('tbl_activities')->insert($data);
    }
}
