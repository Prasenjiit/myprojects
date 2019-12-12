<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApplicationLogController;
use App\Http\Requests;
use Auth;
use View;
use URL;
use File;
use Validator;
use Input;
use Session;
use DB;
use App\Mylibs\Common;
use App\SettingsModel as SettingsModel;
use App\StacksModel as StacksModel;
use App\DepartmentsModel as DepartmentsModel;
use App\DocumentTypesModel as DocumentTypesModel;
use Lang;
use Mail;


class SettingsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        Session::put('menuid', '4');
        $this->middleware(['auth', 'user.status']);

        //Settings rows are put in session for avoid hard coding
        Session::put('settings_alphabets', '1');
        Session::put('settings_numerics', '1');
        Session::put('settings_special_characters', '1');
        Session::put('settings_capital_and_small', '1');

        // Set common variable
        $this->actionName = 'Settings';
        $this->docObj     = new Common(); // class defined in app/mylibs 
    }
    
    public function index()
    {   
        // checking wether user logged in or not
        if (Auth::user()) { 
            Session::put('menuid', '4');
            $data['docType'] = DocumentTypesModel::orderBy('document_type_order', 'ASC')->get();
            $data['tbl_settings'] = DB::table('tbl_settings')->first();
            $days = ($data['tbl_settings']->settings_document_expiry);
            $last_date = date('Y-m-d', strtotime("+".$days." days"));
            $this->docObj->commom_expiry_documents_check($last_date);
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();

            
            // Get SMTP details
            $data['otherData']   = DB::table('tbl_smtp_details')->where('smtp_details_unique_key','other')->get();// Get SMTP details
            $data['gmailData']   = DB::table('tbl_smtp_details')->where('smtp_details_unique_key','gmail')->get();// Get SMTP details
            $data['outlookData'] = DB::table('tbl_smtp_details')->where('smtp_details_unique_key','outlook')->get();// Get SMTP details
            $data['exchangeData']= DB::table('tbl_smtp_details')->where('smtp_details_unique_key','exchange')->get();// Get SMTP details
            $data['yahooData']   = DB::table('tbl_smtp_details')->where('smtp_details_unique_key','yahoo')->get();// Get SMTP details
            $data['ftpData']  = DB::table('tbl_ftp_details')->get(); 
            $data['emailNotif']  = DB::table('tbl_email_notifications')->get(); 
           return View::make('pages/settings/add')->with($data);
       } else {
           return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }

    public function save(){
        // Preparing data
        $data = new SettingsModel;
        
        // checking wether data already exists or not
        $record = DB::table('tbl_settings')->select('*')->first();

            // checking wether data already exists or not
        $record = DB::table('tbl_settings')->select('*')->first();

            //<--SMTP settings-->
            // Other
            $otherRecords   = array('smtp_details_unique_key' =>'other',
                                   'smtp_details_user_authentication'=>Input::get('smtp_details_other_user_authentication'),
                                   'smtp_details_username'  =>Input::get('smtp_details_other_username'),
                                   'smtp_details_mailserver'=>Input::get('smtp_details_other_mailserver'),
                                   'smtp_details_port'      =>Input::get('smtp_details_other_port'),
                                   'smtp_details_tls_ssl'   =>Input::get('smtp_details_other_tls_ssl'),
                                   'smtp_details_fromname'  =>Input::get('smtp_details_other_fromname'),
                                   'smtp_details_fromaddress'=>Input::get('smtp_details_other_fromaddress'));
            //Gmail
            $gmailRecords   = array('smtp_details_unique_key' =>'gmail',
                                    'smtp_details_mailserver'=>Input::get('smtp_details_gmail_server'),
                                   'smtp_details_port'       =>Input::get('smtp_details_gmail_port'),
                                   'smtp_details_tls_ssl'    =>Input::get('smtp_details_gmail_tls_ssl'),
                                   'smtp_details_user_authentication'=>Input::get('smtp_details_gmail_user_authentication'),
                                   'smtp_details_username'   =>Input::get('smtp_details_gmail_address'));
            // Outlook.com
            $outlookRecords = array('smtp_details_unique_key' =>'outlook',
                                    'smtp_details_mailserver' =>Input::get('smtp_details_outlook_server'),
                                   'smtp_details_port'        =>Input::get('smtp_details_outlook_port'),
                                   'smtp_details_tls_ssl'     =>Input::get('smtp_details_outlook_tls_ssl'),
                                   'smtp_details_user_authentication'=>Input::get('smtp_details_outlook_user_authentication'),
                                   'smtp_details_username'    =>Input::get('smtp_details_outlook_address'));
            // MS Exchange
            $exchangeRecords = array('smtp_details_unique_key' =>'exchange',
                                   'smtp_details_mailserver' =>Input::get('smtp_details_exchange_server'),
                                   'smtp_details_port'         =>Input::get('smtp_details_exchange_port'),
                                   'smtp_details_tls_ssl'      =>Input::get('smtp_details_exchange_tls_ssl'),
                                   'smtp_details_user_authentication' =>Input::get('smtp_details_exchange_user_authentication'),
                                   'smtp_details_username' =>Input::get('smtp_details_exchange_username'));
            // Yahoo
            $yahooRecords   = array('smtp_details_unique_key' =>'yahoo',
                                    'smtp_details_mailserver' =>Input::get('smtp_details_yahoo_server'),
                                    'smtp_details_port'         =>Input::get('smtp_details_yahoo_port'),
                                    'smtp_details_username'=>Input::get('smtp_details_yahoo_emailid'));
        
            // Checking allowed account
            //other
            if(@input::get('smtp_details_active_account') == 'other'){
                $otherRecords['smtp_details_active_account']   = 'active';
            }else{
                $otherRecords['smtp_details_active_account']   = 'inactive';
            }
            // Password
            if(Input::get('smtp_details_other_password')){
                    $otherRecords['smtp_details_password']     = encrypt(Input::get('smtp_details_other_password'));
                }
            //gmail
            if(@input::get('smtp_details_active_account') == 'gmail'){
                $gmailRecords['smtp_details_active_account']   = 'active';
            }else{
                $gmailRecords['smtp_details_active_account']   = 'inactive';
            }
            // Password
            if(Input::get('smtp_details_gmail_password')){
                    $gmailRecords['smtp_details_password']     = encrypt(Input::get('smtp_details_gmail_password'));
                }
            //outlook
            if(@input::get('smtp_details_active_account') == 'outlook'){
                $outlookRecords['smtp_details_active_account']   = 'active';
            }else{
                $outlookRecords['smtp_details_active_account']   = 'inactive';
            }
            // Password
            if(Input::get('smtp_details_outlook_password')){
                    $outlookRecords['smtp_details_password']     = encrypt(Input::get('smtp_details_outlook_password'));
                }
            //exchange
            if(@input::get('smtp_details_active_account') == 'exchange'){
                $exchangeRecords['smtp_details_active_account']   = 'active';
            }else{
                $exchangeRecords['smtp_details_active_account']   = 'inactive';
            }
            // Password
            if(Input::get('smtp_details_exchange_password')){
                $exchangeRecords['smtp_details_password']     = encrypt(Input::get('smtp_details_exchange_password'));
            }

            //yahoo
            if(@input::get('smtp_details_active_account') == 'yahoo'){
                $yahooRecords['smtp_details_active_account']   = 'active';
            }else{
                $yahooRecords['smtp_details_active_account']   = 'inactive';
            }
            // Password
            if(Input::get('smtp_details_yahoo_email_password')){
                $yahooRecords['smtp_details_password']     = encrypt(Input::get('smtp_details_yahoo_email_password'));
            }
            // Insertation and updation query
            // Other
            // Checking smtp row other records exists
            $otherExists = DB::table('tbl_smtp_details')->where('smtp_details_unique_key','other')->get();

            if($otherExists):
                // Update => By this condition only one row exists 
                DB::table('tbl_smtp_details')->where('smtp_details_unique_key','other')->update($otherRecords);
            else: 
                // Insert
                DB::table('tbl_smtp_details')->insert($otherRecords);
            endif;
            // gmail
            // Checking smtp row other records exists
            $gmailRecordsExists = DB::table('tbl_smtp_details')->where('smtp_details_unique_key','gmail')->get();
            if($gmailRecordsExists):
                // Update => By this condition only one row exists 
                DB::table('tbl_smtp_details')->where('smtp_details_unique_key','gmail')->update($gmailRecords);
            else:
                // Insert
                DB::table('tbl_smtp_details')->insert($gmailRecords);
            endif;
            // outlook
            // Checking smtp row other records exists
            $outlookRecordsExists = DB::table('tbl_smtp_details')->where('smtp_details_unique_key','outlook')->get();
            if($outlookRecordsExists):
                // Update => By this condition only one row exists 
                DB::table('tbl_smtp_details')->where('smtp_details_unique_key','outlook')->update($outlookRecords);
            else:
                // Insert
                DB::table('tbl_smtp_details')->insert($outlookRecords);
            endif;
            // exchange
            // Checking smtp row other records exists
            $exchangeRecordsExists = DB::table('tbl_smtp_details')->where('smtp_details_unique_key','exchange')->get();
            if($exchangeRecordsExists):
                // Update => By this condition only one row exists 
                DB::table('tbl_smtp_details')->where('smtp_details_unique_key','exchange')->update($exchangeRecords);
            else:
                // Insert
                DB::table('tbl_smtp_details')->insert($exchangeRecords);
            endif;
            // yahoo
            // Checking smtp row other records exists
            $yahooRecordsExists = DB::table('tbl_smtp_details')->where('smtp_details_unique_key','yahoo')->get();
            if($yahooRecordsExists):
                // Update => By this condition only one row exists 
                DB::table('tbl_smtp_details')->where('smtp_details_unique_key','yahoo')->update($yahooRecords);
            else:
                // Insert
                DB::table('tbl_smtp_details')->insert($yahooRecords);
            endif;
            //<--//SMTP settings-->

            //<--FTP settings-->
            $ftpRecords = array('ftp_details_host'=>Input::get('ftp_details_host'),
                                   'ftp_details_port'=>Input::get('ftp_details_port'),
                                   'ftp_details_username'=>Input::get('ftp_details_username'),
                                   'ftp_details_update'=>date('Y-m-d H:i:s'),
                                   'updated_at'=>date('Y-m-d H:i:s'));
            // Encrypt psw
            if(Input::get('ftp_details_password')){
                    $ftpRecords['ftp_details_password']     = encrypt(Input::get('ftp_details_password'));
                }

            // Checking ftp row already exists
            $ftpExists = DB::table('tbl_ftp_details')->first();
            if($ftpExists):
                // Update => By this condition only one row exists 
                DB::table('tbl_ftp_details')->where('ftp_details_id',$ftpExists->ftp_details_id)->update($ftpRecords);
            else:
                // Insert
                DB::table('tbl_ftp_details')->insert($ftpRecords);
            endif;
            //<--//FTP settings-->

             //<--Email settings-->
            if(Input::get('activity_task_notifications')):
                $activity_task_notifications = '1';
            else:
                $activity_task_notifications = '0';
            endif;
            if(Input::get('form_notifications')):
                $form_notifications = '1';
            else:
                $form_notifications = '0';
            endif;
            if(Input::get('document_notifications')):
                $document_notifications = '1';
            else:
                $document_notifications = '0';
            endif;
            if(Input::get('signin_notifications')):
                $signin_notifications = '1';
            else:
                $signin_notifications = '0';
            endif;
            if(Input::get('override_email_notifications_settings')):
                $override_email_notifications_settings = '1';
            else:
                $override_email_notifications_settings = '0';
            endif;
            if(Input::get('overwrite_preferences')):
                $overwrite_preferences = '1';
            else:
                $overwrite_preferences = '0';
            endif;

            $emailNotif = array('email_notification_activity_task_notifications'=>$activity_task_notifications,
                                    'email_notification_form_notifications'=>$form_notifications,
                                    'email_notification_document_notifications'=>$document_notifications,
                                    'email_notification_signin_notifications'=>$signin_notifications,
                                    'email_notification_overwrite_preferences'=>$overwrite_preferences,
                                    'email_notification_override_email_notifications_settings'=>$override_email_notifications_settings);
            // Checking ftp row already exists
            $emailnotifExists = DB::table('tbl_email_notifications')->first();
            if($emailnotifExists):
                // Update => By this condition only one row exists 
                DB::table('tbl_email_notifications')->where('email_notification_id',$emailnotifExists->email_notification_id)->update($emailNotif);
            else:
                // Insert
                DB::table('tbl_email_notifications')->insert($emailNotif);
            endif;

            if($overwrite_preferences==1){
                $dataToUpdate = array('updated_at'        => date('Y-m-d h:i:s'));
                $dataToUpdate['user_activity_task_notifications'] = $activity_task_notifications;
                $dataToUpdate['user_form_notifications']          = $form_notifications;
                $dataToUpdate['user_document_notifications']      = $document_notifications;
                $dataToUpdate['user_signin_notifications']        = $signin_notifications;
                // update table
                DB::table('tbl_users')->update($dataToUpdate);
            }
            
            //<--//Email settings-->

            // Password Length right section data
            $passwordLengthArray = explode(';',Input::get('password_length'));
            if(Input::get('settings_alphabets')):
                $settings_alphabets = '1';
            else:
                $settings_alphabets = '0';
            endif;

            if(Input::get('settings_numerics')):
                $settings_numerics = '1';
            else:
                $settings_numerics = '0';
            endif;

            if(Input::get('settings_special_characters')):
                $settings_special_characters = '1';
            else:
                $settings_special_characters = '0';
            endif;

            if(Input::get('settings_capital_and_small')):
                $settings_capital_and_small = '1';
                $settings_alphabets = '1';
            else:
                $settings_capital_and_small = '0';
            endif;

        
        if($record):
            // update
            if(Input::file('settings_logo')):       
                // save new file
                $logo = $this->imageUpload();

                if(@$logo):
                    // unlink oldimage if new image exists
                    $unlinkPath = public_path('images/logo/').$record->settings_logo; 
                    unlink($unlinkPath);
                endif;

            else:
                $logo= $record->settings_logo;    
            endif;
            
            $settings_ftp  = (Input::get('ftp_upload'))?1:0;

            $settings_datetimeformat = Input::get('settings_datetimeformat');

            $datetimeformat = SettingsModel::extract_date_time($settings_datetimeformat);
            $settings_dateformat = (isset($datetimeformat['date']))?$datetimeformat['date']:'';    
            $settings_timeformat = (isset($datetimeformat['time']))?$datetimeformat['time']:''; 

            // update query
            $dataToUpdate = array(
                'settings_company_name'=>Input::get('settings_company_name'),
               'settings_address'    =>Input::get('settings_address'),
               'settings_email'      =>Input::get('settings_email'),
               'settings_document_no'=>Input::get('settings_document_no'),
               'settings_document_name'=>Input::get('settings_document_name'),
               'settings_department_name'=>Input::get('settings_deptname'),
               'settings_logo'       =>$logo,
               'settings_login_attempts'  =>Input::get('range_5'),
               'settings_pasword_expiry' =>Input::get('range_1'),
               'settings_document_expiry'=>Input::get('settings_document_expiry'),
               'settings_rows_per_page'  =>Input::get('range_3'),
               'settings_timezone'       =>Input::get('settings_timezone'),
               'settings_datetimeformat'  =>$settings_datetimeformat,
               'settings_dateformat'       =>$settings_dateformat,
               'settings_timeformat'       =>$settings_timeformat,
               'settings_alphabets'      =>$settings_alphabets,
               'settings_numerics'       =>$settings_numerics,
               'settings_special_characters'=>$settings_special_characters,
               'settings_capital_and_small' =>$settings_capital_and_small,
               'settings_password_length_from'=> @$passwordLengthArray[0],
               'settings_password_length_to'  => @$passwordLengthArray[1],
               'settings_ftp'  => $settings_ftp,
               'updated_at' => date('Y-m-d h:i:s'));

            // Encrypt doc_encryption pswd
            if(Input::get('doc_encrypt_password')){
                    $dataToUpdate['settings_encryption_pwd'] = encrypt(Input::get('doc_encrypt_password'));
                }

            // Checking  row already exists
            $settingsExists = DB::table('tbl_settings')->first();
            if($settingsExists):
                // Update => By this condition only one row exists 
                DB::table('tbl_settings')->where('settings_id',$record->settings_id)->update($dataToUpdate);
            else:
                // Insert
                DB::table('tbl_settings')->insert($dataToUpdate);
            endif;

            // Update session 
            if(Input::get('range_3')){
                Session::put('settings_rows_per_page',Input::get('range_3'));
            }
            // <!--Get notifications for header-->
            if(Input::get('range_1') == '0'){
                Session::put('password_expire_date','');
            }else{                  
                //<!--Get password expiry notifiction-->   
                $this->docObj->getPasswordExpiryNotification();         
            }// <!--Notifications End-->
            // Save in audits
            $name = Input::get('settings_company_name');
            $user = Auth::user()->username;

            // Get update action message
            $actionMsg = Lang::get('language.update_action_msg');
            $actionDes = $this->docObj->stringReplace("",$this->actionName,$user,$actionMsg);
            $result = (new AuditsController)->log(Auth::user()->username, $this->actionName, Lang::get('language.update'),$actionDes);
            // redirect
            return redirect('/settings')->with('status', Lang::get('language.updated_successfully'));
        else:
            // If setting table truncated, then you nedd to insert it..  
            // saving image -->
            $data->settings_logo = $this->imageUpload();
            $data->settings_company_name = Input::get('settings_company_name');
            $data->settings_address      = Input::get('settings_address');
            $data->settings_email        = Input::get('settings_email');
            $data->settings_document_no  = Input::get('settings_document_no');
            $data->settings_document_name  = Input::get('settings_document_name');
            $data->settings_department_name = Input::get('settings_deptname');
            $data->settings_login_attempts = Input::get('range_5');
            $data->settings_pasword_expiry = Input::get('range_1');
            $data->settings_document_expiry= Input::get('settings_document_expiry');
            $data->settings_rows_per_page  = Input::get('range_3');
            $data->settings_timezone       = Input::get('settings_timezone');
            $data->settings_format       = Input::get('settings_format');
            if(Input::get('doc_encrypt_password'))
            {
            $data->settings_encryption_pwd = encrypt(Input::get('doc_encrypt_password'));
            }
            $data->settings_alphabets          = $settings_alphabets;
            $data->settings_numerics           = $settings_numerics;
            $data->settings_special_characters = $settings_special_characters;
            $data->settings_capital_and_small  = $settings_capital_and_small;
            $data->settings_password_length_from = @$passwordLengthArray[0];
            $data->settings_password_length_to   = @$passwordLengthArray[1];
            // Save data
            $data->save();

            // Save in audits
            $name = Input::get('settings_company_name');
            $user = Auth::user()->username;
    
            // Get save action message
            $actionMsg = Lang::get('language.save_action_msg');
            $actionDes = $this->docObj->stringReplace("",$this->actionName,$user,$actionMsg);
            $result = (new AuditsController)->log(Auth::user()->username, $this->actionName, Lang::get('language.insert'),$actionDes);
            // redirect
            return redirect('/settings')->with('status', Lang::get('language.saved_successfully'));
        endif;
    }

    //<!--Test SMTP email-->
    public function testEmail()
    {
       if (Auth::user()) 
       {   
           // Define variables
           $companyName = Session('settings_company_name');
            // Checking psw is new or saved one
            if(Input::get('password')):
                $password = Input::get('password');
            else:
                $password = decrypt(Input::get('savedPsw'));
            endif;

            // Tls or ssl
            if(Input::get('sslTls')):
                if(Input::get('sslTls') == 'none' || Input::get('sslTls')=='undefined'){
                        $tls_or_ssl = 'tls';
                    }else{
                        $tls_or_ssl =Input::get('sslTls');
                    }
            endif;
            
           // Update config
           config(['mail.driver' => 'smtp',
                   'mail.host' => Input::get('host'),
                   'mail.port' => Input::get('port'),
                   'mail.username' => Input::get('username'),
                   'mail.password' => $password,
                   'mail.encryption' => $tls_or_ssl
                   ]);
            
            
           $datas = array('subject'=>Lang::get('language.test_mail_subject'),'email_to'=>Input::get('emailId'),'message'=>Lang::get('language.test_mail_subject'));
           try {
               Mail::send('test_email', ['request' => $datas], function ($m) use ($datas) {//test_email is view file which contains contant
                   $m->from(config('mail.username'))
                      ->to($datas['email_to'])
                      ->subject($datas['message']);
               });
               //return "true";//Ajax response
               $return = array('status' => 1,'message' => Lang::get('language.mail_sent_success').' '.Input::get('emailId'));   
            } catch (\Exception $ex) {
                $return = array('status' => 0,'message'=>Lang::get('language.mail_not_sent'),'message2' => $ex->getMessage());   
                //return "false";
            }
            return $return;
       }
    }

    //<---------- Upload Image -------------------------->
    public function imageUpload(){
        // save image
        $image = Input::file('settings_logo');
        $input['imagename'] = time().'.'.$image->getClientOriginalExtension();
        $destinationPath = public_path('images/logo'); 
        $image->move($destinationPath, $input['imagename']);
        // Save in audits
        // Get update action message
        $actionMsg = Lang::get('language.update_action_msg');
        $actionDes = $this->docObj->stringReplace($this->actionName,Lang::get('language.logo'),Auth::user()->username,$actionMsg);

        $result = (new AuditsController)->log(Auth::user()->username, $this->actionName, Lang::get('language.logo_uploaded'),$actionDes);
        return $input['imagename'];
    }

    // Get settings details
    public function getSettings(){
        $data = DB::table('tbl_settings')->select('settings_numerics','settings_alphabets','settings_special_characters','settings_capital_and_small','settings_password_length_from','settings_password_length_to')->get();
        echo json_encode($data[0]);exit;
    }

   public function test_ftp_connectivity()
    {
        $return = array('status' => 0 , 'message' => 'Unable to connect server');
        $domainhost= Input::get('ftp_details_host');
        $port=Input::get('ftp_details_port');
        $domainuser=Input::get('ftp_details_username');
        $domainpass=Input::get('ftp_details_password');
        
        if(!$domainpass)
        {
           $ftp_details = DB::table('tbl_ftp_details')->first(); 
           $domainpass=($ftp_details)?decrypt($ftp_details->ftp_details_password):'';    
        }
        $basedirectory="/storage/";

        if($domainhost || $port || $domainuser || $domainpass)
        {
        $ftp_conn = @ftp_connect($domainhost,$port);
        if (false === $ftp_conn) {
        $return = array('status' => 0 , 'message' => 'Unable to connect server');
         return $return;
        }

        if($ftp_conn)
        {
           $login = @ftp_login($ftp_conn, $domainuser, $domainpass);
           if($login)
           {
            $contents = ftp_nlist($ftp_conn, ".");
            $contents = ($contents)?$contents:array();
             if(!in_array('storage', $contents))
             {
                $return = array('status' => 0 , 'message' => 'FTP Connected Successfully.But Upload folder not found');
                return $return;
            }
            else
            {
              $return['status'] = 1;
              $return['message'] = 'FTP Connected Successfully';
               return $return;
            }
     
             
           }
           else
            {
              $return['status'] = 0;
              $return['message'] = 'Unable to Login. Please check username and password';
            } 
           ftp_close($ftp_conn);
        }
      }
        return $return;
    }

}/*<--END-->*/
