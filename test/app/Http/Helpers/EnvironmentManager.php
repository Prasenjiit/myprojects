<?php
namespace App\Http\Helpers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDO;   
use Illuminate\Database\SQLiteConnection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Console\Output\BufferedOutput;

use App\Http\Controllers\Controller;
use Illuminate\Routing\Redirector;
use App\Http\Events\EnvironmentSaved;
use Validator;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Http\Requests\AccUpdateRequest;
class EnvironmentManager
{
    /**
     * @var string
     */
    private $envPath;

    /**
     * @var string
     */
    private $envExamplePath;

    /**
     * Set the .env and .env.example paths.
     */
    public function __construct()
    {
        $this->envPath = base_path('.env');
        $this->envExamplePath = base_path('.env.example');
    }

    /**
     * Get the content of the .env file.
     *
     * @return string
     */
    public function getEnvContent()
    {
        if (!file_exists($this->envPath)) {
            if (file_exists($this->envExamplePath)) {
                copy($this->envExamplePath, $this->envPath);
            } else {
                touch($this->envPath);
            }
        }

        return file_get_contents($this->envPath);
    }

    /**
     * Get the the .env file path.
     *
     * @return string
     */
    public function getEnvPath() {
        return $this->envPath;
    }

    /**
     * Get the the .env.example file path.
     *
     * @return string
     */
    public function getEnvExamplePath() {
        return $this->envExamplePath;
    }

    /**
     * Save the edited content to the .env file.
     *
     * @param Request $input
     * @return string
     */
    public function saveFileClassic(Request $input)
    {
        $message = trans('installer_messages.environment.success');
        try {
            file_put_contents($this->envPath, $input->get('envConfig'));
        }
        catch(Exception $e) {
            $message = trans('installer_messages.environment.errors');
        }

        return $message;
    }

    /**
     * create the db if not exists.
     */
    public function createDB(Request $request)
    {
        $hostName = $request->database_hostname;
        $userName = $request->database_username;
        $passwd   = $request->database_password;
        $database   = $request->database_name;
        // $hostName = env('DB_HOST');
        // $userName = env('DB_USERNAME');
        // $passwd   = env('DB_PASSWORD');
        // $database = env('DB_DATABASE');
        // Create connection
        try {
            $pdo = new PDO("mysql:host=".$hostName, $userName, $passwd);
            $output = $pdo->exec(sprintf(
                'CREATE DATABASE IF NOT EXISTS %s ;', $database
            ));   
            //echo $output; 
            return $output; 
        } catch (PDOException $exception) {
            $this->error(sprintf('Failed to create %s database, %s', $database, $exception->getMessage()));
        }         
    }

   
    /**
     * create the db if not exists.
     */
    public function checkDB(Request $request)
    {
        // $servername = env('DB_HOST');
        // echo $username = env('DB_USERNAME');
        // $password   = env('DB_PASSWORD');
        // $database = env('DB_DATABASE');

        $servername = $request->database_hostname;
        $username = $request->database_username;
        $password   = $request->database_password;
        $database   = $request->database_name;

        $link = mysqli_connect($servername, $username, $password);
        // Check connection
        if($link === false){
           die("ERROR: Could not connect. " . mysqli_connect_error());
        }
        // Attempt create database query execution
        $sql = "CREATE DATABASE IF NOT EXISTS ".$database;
        if(mysqli_query($link, $sql)){
            return "success";
        } else{
            return "error";
        }
        // Close connection
        mysqli_close($link);
    }

    /**
     * Run the migration and call the seeder.
     *
     * @param collection $outputLog
     * @return collection
     */
    public function migrateandSeed()
    {
         if(file_exists(config('app.storage_path').'database.sql')){
            $lines = config('app.storage_path').'database.sql';
            //collect contents and pass to DB::unprepared
            return DB::unprepared(file_get_contents($lines));
        }

        // $hostName = $request->database_hostname;
        // $userName = $request->database_username;
        // $passwd   = $request->database_password;
        //  $pdo = new PDO("mysql:host=".$hostName, $userName, $passwd);
        //     $output = $pdo->exec(sprintf(
        //         'CREATE DATABASE IF NOT EXISTS %s ;', $request->database_name
        //     ));     
        //     if($output==0){
        //         $migrate =  Artisan::call('migrate', array('--path' => 'database/migrations', '--force' => true));
        //         $this->seed();
        //     }    

            // try{
            // // Artisan::call('list', array(), $outputLog);
            // // return $outputLog->fetch();
            // $migrate =  Artisan::call('migrate', array('--path' => 'database/migrations', '--force' => true));
            // //return true;    
            // $this->seed();        
        // }
        // catch(Exception $e){
        //     return $e->getMessage();
        // }
        
       //return 
    }

    /**
     * Seed the database.
     *
     * @param collection $outputLog
     * @return array
     */
    // public function seed()
    // {
    //     if (!Schema::hasTable('tbl_settings')) {
    //         // Code to create table
    //         echo "AfA";
    //     }else{
    //         try{
    //             // seed tbl_settings
    //             $data= array(
    //                 'settings_company_name' => 'Mathenson',
    //                 'settings_address' => '106,Onkar Industrial Estate,
    //                 Opp Kanjurmarg Rly Stn, (W)
    //                 Mumbai - 400078.
    //                 Tel. : 022-25778888',
    //                 'settings_email' => 'mathenson@gmail.com',
    //                 'settings_document_no' => 'Document Number',
    //                 'settings_document_name' => 'Document Name',
    //                 'settings_department_name' => 'Department',
    //                 'settings_user_folder' => 'fileeazy',
    //                 'settings_login_attempts' => '5',
    //                 'settings_login_attempt_time' => '5',
    //                 'settings_pasword_expiry' => '90',
    //                 'settings_document_expiry' => '30',
    //                 'settings_alphabets' => '1',
    //                 'settings_numerics' => '1',
    //                 'settings_special_characters' => '0',
    //                 'settings_capital_and_small' => '0',
    //                 'settings_password_length_from' => '6',
    //                 'settings_password_length_to' => '10',
    //                 'settings_file_extensions' =>  '.pdf, .doc, .docx, .xls, .xlsx, .flv, .mp4, .ogv, .mp3, .ogg, .wav, .gif, .jpg, .jpeg, .png, .tif,
    //                     .dwg, .zip (.tif will be converted in to pdf before uploading).',
    //                 'settings_rows_per_page' => '30',
    //                 'settings_timezone' => 'Asia/Kolkata',
    //                 'settings_dateformat' => 'd-m-Y',
    //                 'settings_timeformat' => 'h:i A',
    //                 'settings_datetimeformat' => 'd-m-Y h:i A',
    //                 'settings_ftp' => '0',
    //                 'settings_install' => '0'            
    //             );
    //             Db::table('tbl_settings')->insert($data);

    //             //seed tbl_user
    //             $data= array(
    //                 'username' => 'adminuser',
    //                 'email' => 'adminuser@fileeazy.com',
    //                 'password' => '$2y$10$vIJP.eX8fBLnM3D8rO0mUui5q2yawDRPl3yUqTNEgIEu5oD5yZ5xq',
    //                 'user_full_name' => 'Administrator',
    //                 'user_role' => '1',
    //                 'user_permission'=> 'add,edit,view,delete,checkout,import,export,workflow,decrypt',
    //                 'user_form_permission'=> 'add,edit,view,delete,export',
    //                 'user_workflow_permission'=> 'add,edit,view,delete',
    //                 'user_status'=> '1',
    //                 'login_status'=> '1',
    //                 'user_lock_status'=> '0'
    //             );
    //             Db::table('tbl_users')->insert($data);

    //             //seed tbl_activities
    //             $data = array(
    //                 'activity_name' => 'Email',
    //                 'activity_modules' => 'workflows',
    //                 'activity_added_by'=> '1',
    //                 'activity_updated_by'=> '1',
    //                 'created_at'=> date('Y-m-d H:i:s'),
    //                 'updated_at'=> date('Y-m-d H:i:s')
    //             );
    //             Db::table('tbl_activities')->insert($data);

    //             $data = array(
    //                 'activity_name' => 'Assign',
    //                 'activity_modules' => 'workflows',
    //                 'activity_added_by'=> '1',
    //                 'activity_updated_by'=> '1',
    //                 'created_at'=> date('Y-m-d H:i:s'),
    //                 'updated_at'=> date('Y-m-d H:i:s')
    //             );
    //             Db::table('tbl_activities')->insert($data);

    //             $data = array(
    //                 'activity_name' => 'Phone',
    //                 'activity_modules' => 'workflows',
    //                 'activity_added_by'=> '1',
    //                 'activity_updated_by'=> '1',
    //                 'created_at'=> date('Y-m-d H:i:s'),
    //                 'updated_at'=> date('Y-m-d H:i:s')
    //             );
    //             Db::table('tbl_activities')->insert($data);

    //             $data = array(
    //                 'activity_name' => 'Follow Up',
    //                 'activity_modules' => 'workflows',
    //                 'activity_added_by'=> '1',
    //                 'activity_updated_by'=> '1',
    //                 'created_at'=> date('Y-m-d H:i:s'),
    //                 'updated_at'=> date('Y-m-d H:i:s')
    //             );
    //             Db::table('tbl_activities')->insert($data);

    //             $data = array(
    //                 'activity_name' => 'Approve',
    //                 'activity_modules' => 'form_action',
    //                 'activity_constant' => 'approve',
    //                 'activity_added_by'=> '1',
    //                 'activity_updated_by'=> '1',
    //                 'created_at'=> date('Y-m-d H:i:s'),
    //                 'updated_at'=> date('Y-m-d H:i:s')
    //             );
    //             Db::table('tbl_activities')->insert($data);

    //             $data = array(
    //                 'activity_name' => 'Completed',
    //                 'activity_modules' => 'workflows',
    //                 'activity_added_by'=> '1',
    //                 'activity_updated_by'=> '1',
    //                 'created_at'=> date('Y-m-d H:i:s'),
    //                 'updated_at'=> date('Y-m-d H:i:s')
    //             );
    //             Db::table('tbl_activities')->insert($data);

    //             $data = array(
    //                 'activity_name' => 'Decline',
    //                 'activity_modules' => 'workflows',
    //                 'activity_added_by'=> '1',
    //                 'activity_updated_by'=> '1',
    //                 'created_at'=> date('Y-m-d H:i:s'),
    //                 'updated_at'=> date('Y-m-d H:i:s')
    //             );
    //             Db::table('tbl_activities')->insert($data);

    //             $data = array(
    //                 'activity_name' => 'On-Hold',
    //                 'activity_modules' => 'form_action',
    //                 'activity_type' => 'on-hold',
    //                 'activity_constant' => 'on-hold',
    //                 'activity_added_by'=> '1',
    //                 'activity_updated_by'=> '1',
    //                 'created_at'=> date('Y-m-d H:i:s'),
    //                 'updated_at'=> date('Y-m-d H:i:s')
    //             );
    //             Db::table('tbl_activities')->insert($data);

    //             $data = array(
    //                 'activity_name' => 'Review',
    //                 'activity_modules' => 'workflows',
    //                 'activity_added_by'=> '1',
    //                 'activity_updated_by'=> '1',
    //                 'created_at'=> date('Y-m-d H:i:s'),
    //                 'updated_at'=> date('Y-m-d H:i:s')
    //             );
    //             Db::table('tbl_activities')->insert($data);

    //             $data = array(
    //                 'activity_name' => 'Reject',
    //                 'activity_modules' => 'form_action',
    //                 'activity_type' => 'reject',
    //                 'activity_constant' => 'reject',
    //                 'activity_added_by'=> '1',
    //                 'activity_updated_by'=> '1',
    //                 'created_at'=> date('Y-m-d H:i:s'),
    //                 'updated_at'=> date('Y-m-d H:i:s')
    //             );
    //             Db::table('tbl_activities')->insert($data);

    //             $data = array(
    //                 'activity_name' => 'Cancel',
    //                 'activity_modules' => 'workflows',
    //                 'activity_added_by'=> '1',
    //                 'activity_updated_by'=> '1',
    //                 'created_at'=> date('Y-m-d H:i:s'),
    //                 'updated_at'=> date('Y-m-d H:i:s')
    //             );
    //             Db::table('tbl_activities')->insert($data);

    //             $data = array(
    //                 'form_input_type_common' => 'text',
    //                 'form_input_type_value' => 'text',
    //                 'form_input_type_name'=> 'Single Line Text',
    //                 'form_input_icon'=> 'fa fa-fw fa-minus',
    //                 'view_order'=> '1',
    //                 'is_options'=> '0',
    //                 'is_required'=> '1'
    //             );
    //             Db::table('tbl_form_input_types')->insert($data);

    //             $data = array(
    //                 'form_input_type_common' => 'select',
    //                 'form_input_type_value' => 'select',
    //                 'form_input_type_name'=> 'Select Box',
    //                 'form_input_icon'=> 'fa fa-sort-amount-desc',
    //                 'view_order'=> '9',
    //                 'is_options'=> '1',
    //                 'is_required'=> '1'
    //             );
    //             Db::table('tbl_form_input_types')->insert($data);

    //             $data = array(
    //                 'form_input_type_common' => 'textarea',
    //                 'form_input_type_value' => 'textarea',
    //                 'form_input_type_name'=> 'Text Area',
    //                 'form_input_icon'=> 'fa fa-navicon',
    //                 'view_order'=> '10',
    //                 'is_options'=> '0',
    //                 'is_required'=> '1'
    //             );
    //             Db::table('tbl_form_input_types')->insert($data);

    //             $data = array(
    //                 'form_input_type_common' => 'checkbox',
    //                 'form_input_type_value' => 'checkbox',
    //                 'form_input_type_name'=> 'Checkbox',
    //                 'form_input_icon'=> 'fa fa-check-square-o',
    //                 'view_order'=> '6',
    //                 'is_options'=> '1',
    //                 'is_required'=> '1'
    //             );
    //             Db::table('tbl_form_input_types')->insert($data);

    //             $data = array(
    //                 'form_input_type_common' => 'checkbox',
    //                 'form_input_type_value' => 'agree',
    //                 'form_input_type_name'=> 'Agree',
    //                 'form_input_icon'=> 'fa fa-check-square-o',
    //                 'view_order'=> '7',
    //                 'is_options'=> '0',
    //                 'is_required'=> '0'
    //             );
    //             Db::table('tbl_form_input_types')->insert($data);

    //             $data = array(
    //                 'form_input_type_common' => 'radio',
    //                 'form_input_type_value' => 'radio',
    //                 'form_input_type_name'=> 'Radio',
    //                 'form_input_icon'=> 'fa fa-dot-circle-o',
    //                 'view_order'=> '8',
    //                 'is_options'=> '1',
    //                 'is_required'=> '1'
    //             );
    //             Db::table('tbl_form_input_types')->insert($data);

    //             $data = array(
    //                 'form_input_type_common' => 'email',
    //                 'form_input_type_value' => 'email',
    //                 'form_input_type_name'=> 'Email',
    //                 'form_input_icon'=> 'fa fa-envelope',
    //                 'view_order'=> '5',
    //                 'is_options'=> '0',
    //                 'is_required'=> '1'
    //             );
    //             Db::table('tbl_form_input_types')->insert($data);

    //             $data = array(
    //                 'form_input_type_common' => 'number',
    //                 'form_input_type_value' => 'number',
    //                 'form_input_type_name'=> 'Number',
    //                 'form_input_icon'=> 'fa fa-sort-numeric-asc',
    //                 'view_order'=> '2',
    //                 'is_options'=> '0',
    //                 'is_required'=> '1'
    //             );
    //             Db::table('tbl_form_input_types')->insert($data);

    //             $data = array(
    //                 'form_input_type_common' => 'date',
    //                 'form_input_type_value' => 'date',
    //                 'form_input_type_name'=> 'Date',
    //                 'form_input_icon'=> 'fa fa-calendar',
    //                 'view_order'=> '3',
    //                 'is_options'=> '0',
    //                 'is_required'=> '1'
    //             );
    //             Db::table('tbl_form_input_types')->insert($data);

    //             $data = array(
    //                 'form_input_type_common' => 'time',
    //                 'form_input_type_value' => 'time',
    //                 'form_input_type_name'=> 'Time',
    //                 'form_input_icon'=> 'fa fa-clock-o',
    //                 'view_order'=> '4',
    //                 'is_options'=> '0',
    //                 'is_required'=> '1'
    //             );
    //             Db::table('tbl_form_input_types')->insert($data);
                   
    //             $data = array(
    //                 'form_input_type_common' => 'file',
    //                 'form_input_type_value' => 'file',
    //                 'form_input_type_name'=> 'File',
    //                 'form_input_icon'=> 'fa fa-file-o',
    //                 'view_order'=> '11',
    //                 'is_options'=> '0',
    //                 'is_required'=> '1'
    //             );
    //             $result = Db::table('tbl_form_input_types')->insert($data);
    //             return $result;
    //             // $seed = Artisan::call('db:seed', array('--path' => 'database/seeds', '--force' => true));
    //         }
    //         catch(Exception $e){
    //             //return $this->response($e->getMessage(), 'error', $outputLog);
    //         }
    //     }
    //     //return $this->response(trans('installer_messages.final.finished'), 'success', $outputLog);
    // }

    /**
     * Return a formatted error messages.
     *
     * @param $message
     * @param string $status
     * @param collection $outputLog
     * @return array
     */
    // private function response($message, $status = 'danger', $outputLog)
    // {
    //     return [
    //         'status' => $status,
    //         'message' => $message,
    //         'dbOutputLog' => $outputLog->fetch()
    //     ];
    // }

    /**
     * check database type. If SQLite, then create the database file.
     *
     * @param collection $outputLog
     */
    private function sqlite($outputLog)
    {
        if(DB::connection() instanceof SQLiteConnection) {
            $database = DB::connection()->getDatabaseName();
            if(!file_exists($database)) {
                touch($database);
                DB::reconnect(Config::get('database.default'));
            }
            $outputLog->write('Using SqlLite database: ' . $database, 1);
        }
    }


    /**
     * Save the form content to the .env file.
     *
     * @param Request $request
     * @return string
     */
    public function saveFileWizard(Request $request, $insstatus)
    {
        $results = trans('installer_messages.environment.success');
        $envFileData =
        'APP_NAME=' . $request->app_name . "\n" .
        'APP_ENV=' . $request->environment . "\n" .
        'APP_KEY=' . 'base64:bODi8VtmENqnjklBmNJzQcTTSC8jNjBysfnjQN59btE=' . "\n" .
        'APP_LOG_LEVEL=' . $request->app_log_level . "\n" .
        'APP_URL=' . $request->app_url . "\n" .
        'APP_INSTALL=' . $insstatus . "\n" .
        'ON_PREMISE='. 'false' . "\n" .
        'USER_FOLDER=' . 'fileeazy' . "\n\n" .
        'DB_CONNECTION=' . $request->database_connection . "\n" .
        'DB_HOST=' . $request->database_hostname . "\n" .
        'DB_PORT=' . $request->database_port . "\n" .
        'DB_DATABASE=' . $request->database_name . "\n" .
        'DB_USERNAME=' . $request->database_username . "\n" .
        'DB_PASSWORD=' . $request->database_password . "\n" .
        'DB_COLLATION=utf8_unicode_ci'. "\n" .
        'DB_COLLATION=utf8'. "\n\n" .
        'BROADCAST_DRIVER=' . $request->broadcast_driver . "\n" .
        'CACHE_DRIVER=' . $request->cache_driver . "\n" .
        'SESSION_DRIVER=' . 'database' . "\n" .
        'QUEUE_DRIVER=' . $request->queue_driver . "\n\n" .
        'REDIS_HOST=' . $request->redis_hostname . "\n" .
        'REDIS_PASSWORD=' . $request->redis_password . "\n" .
        'REDIS_PORT=' . $request->redis_port . "\n\n" .
        'MAIL_DRIVER=' . $request->mail_driver . "\n" .
        'MAIL_HOST=' . $request->mail_host . "\n" .
        'MAIL_PORT=' . $request->mail_port . "\n" .
        'MAIL_USERNAME=' . $request->mail_username . "\n" .
        'MAIL_PASSWORD=' . $request->mail_password . "\n" .
        'MAIL_ENCRYPTION=' . $request->mail_encryption . "\n\n" .
        'PUSHER_APP_ID=' . $request->pusher_app_id . "\n" .
        'PUSHER_APP_KEY=' . $request->pusher_app_key . "\n" .
        'PUSHER_APP_SECRET=' . $request->pusher_app_secret;      
        try {
            file_put_contents($this->envPath, $envFileData);            
        }
        catch(Exception $e) {
            $results = trans('installer_messages.environment.errors');
        }       

        return $results;
    }
}
