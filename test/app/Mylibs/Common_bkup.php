<?php
namespace App\Mylibs;
use App\StacksModel as StacksModel;
use App\DocumentTypesModel as DocumentTypesModel;
use App\DepartmentsModel as DepartmentsModel;
use App\SettingsModel as SettingsModel;
use App\ModulesModel as ModulesModel;
use Auth;
use Session;
use DB;
use Lang;
use DateTime;
use Imagick;
use Mail;
class Common {

	public function common_stack()
	{
	    return $data['stckApp'] = StacksModel::select('stack_id','stack_name')->orderBy('created_at', 'DESC')->get();	
	}
   public function get_modules()
    {
        return $data['modules'] = ModulesModel::select('module_id','module_name','module_activation_key','module_activation_count','module_activation_date','module_expiry_date')->get();   
    }
    public function get_userlicense()
    {
        return $data['license'] = DB::table('tbl_settings')->get();
    }
    public function common_workflow()
    {
        $workflowapp = DB::table('tbl_workflows')->select('workflow_name','workflow_id')->groupBy('workflow_id')->orderBy('workflow_id','desc')->get();
        Session::put('workflowapp',$workflowapp);  
		
		 $workflowsapp = DB::table('tbl_wf')->select('workflow_name','id')->groupBy('id')->orderBy('id','desc')->get();
        Session::put('workflowsapp',$workflowsapp);
    }
    public function common_forms()
    {
        $formapp = DB::table('tbl_forms')->select('form_name','form_id')->orderBy('form_id','DESC')->get();
        Session::put('formapp',$formapp);  
    }
    public function common_dept()
    {        
        if(Auth::user()->user_role == Session::get("user_role_group_admin") || Auth::user()->user_role == Session::get("user_role_regular_user") || Auth::user()->user_role == Session::get("user_role_private_user")){
            $loggedUsersdepIds = explode(',',Auth::user()->department_id);
            return $data['deptApp'] = DepartmentsModel::select('department_id','department_name')->whereIn('department_id',$loggedUsersdepIds)->orderBy('department_order', 'ASC')->get();
        }else{
            return $data['deptApp'] = DepartmentsModel::select('department_id','department_name')->orderBy('department_order', 'ASC')->get();
        }
    }

    public function common_type()
    {
        return $data['doctypeApp']     = DocumentTypesModel::select('document_type_id','document_type_name','document_type_column_no','document_type_column_name')->where('is_app',0)->orderBy('document_type_order', 'ASC')->get();
    }
    public function common_apps()
    {
        return $data['appsApp'] = DocumentTypesModel::select('document_type_id','document_type_name')->orderBy('document_type_order', 'ASC')->where('is_app',1)->get();
    }
    public function common_records()
    {
        return $data['records'] = DB::table('tbl_settings')->first();
            
    }
    public function document_assign_notification()
    {
        $documents_assigned_count = DB::table('tbl_documents')->where('document_status','Review')->where('document_assigned_to',Auth::user()->username)->count();
        Session::put('count_doc_assigned',$documents_assigned_count);
    }
    public function document_reject_notification()
    {
        $documents_reject_count = DB::table('tbl_documents')->where('document_status','Rejected')->where('document_created_by',Auth::user()->username)->count();
        Session::put('count_doc_rejected',$documents_reject_count);
    }
    public function document_accept_notification()
    {
        $documents_accept_count = DB::table('tbl_documents')->where('document_status','Published')->where('document_created_by',Auth::user()->username)->where('document_assigned_to','!=',"")->count();
        Session::put('count_doc_accepted',$documents_accept_count);
    }
     public function commom_expiry_documents_check($expiry)
    {
        //User related activities
        $user_activitycnt = DB::table('tbl_user_role_activities')->select('user_role_activity_name','user_role_activity_status')->where('user_role_id',Auth::user()->user_role)->count();

        if($user_activitycnt>0){
            $user_activity = DB::table('tbl_user_role_activities')->select('user_role_activity_name','user_role_activity_status')->where('user_role_id',Auth::user()->user_role)->first();

            Session::put('user_activity',$user_activity->user_role_activity_name);
       
            Session::put('user_activity_doc_expire_notification','document expiry notification');
            //User activity status
            Session::put('user_activity_status',$user_activity->user_role_activity_status);
        }
        //check activities related to user 
        if(Session::get('user_activity') == Session::get('user_activity_doc_expire_notification') && Session::get('user_activity_status') == Session::get('user_status_Active'))
        {
        
        //Document Expiry Checking
         if($expiry == null)
         {
        $data['tbl_settings'] = DB::table('tbl_settings')->first();
        $days = ($data['tbl_settings']->settings_document_expiry);
        $last_date = date('Y-m-d', strtotime("+".$days." days"));
        $expiry = $last_date;
         }
        $expire_date_message = array();// array for store notifications
        $document_expiry = $expiry;
        Session::put('expiry_date_from_settings',$document_expiry);
        $today = date('Y-m-d');
        switch(Auth::user()->user_role)
        {
            case Session::get('user_role_super_admin'): //select all docs
            $tbl_documents_expiry_dates = DB::table('tbl_documents')->select('document_expiry_date','document_name')->where('document_expiry_date','!=','null')->whereBetween('document_expiry_date',[date('Y-m-d'),$document_expiry])->get();
            break;
            case Session::get('user_role_group_admin'): //select docs deptwise
            //DB::enableQueryLog();
                $query = DB::table('tbl_documents')->select('document_expiry_date','document_name')->where('document_expiry_date','!=','null')->whereBetween('document_expiry_date',[date('Y-m-d'),$document_expiry]);
                $department_id = explode(',',Auth::user()->department_id);
                   $count         = count($department_id);
                   if($count == 1):
                       $x=0;
                   else:
                       $x=1;
                   endif;
                   
                   foreach($department_id as $depid):
                      if($x == 1):
                           $query->whereRaw('('.'FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                       elseif($x == $count):
                           $query->orWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)'.')');
                       else:
                           $query->whereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                       endif;
                       $x++;
                   endforeach;
                $tbl_documents_expiry_dates = $query->get();
            break;
            case Session::get('user_role_regular_user'):
            case Session::get('user_role_private_user'):
                $tbl_documents_expiry_dates = DB::table('tbl_documents')->select('document_expiry_date','document_name')->where('document_expiry_date','!=','null')->where('document_ownership',Auth::user()->username)->whereBetween('document_expiry_date',[date('Y-m-d'),$document_expiry])->get();
            break;
        }
            foreach ($tbl_documents_expiry_dates as $value) {
                if(($day_expire_doc = date_diff(date_create($value->document_expiry_date),date_create($today))->days) <= $document_expiry)
                if($day_expire_doc > 0){
                    if($day_expire_doc == '1')://expiring today
                        $msg = Lang::get('language.doc_expire_date_message1_lang');
                        $msg = str_replace('$docname','<b>'.ucfirst($value->document_name).'</b>',$msg);
                        array_push($expire_date_message, $msg);
                    elseif($day_expire_doc == '2'):// expire tomarrow
                        $msg = Lang::get('language.doc_expire_date_message2_lang');
                        $msg = str_replace('$docname','<b>'.ucfirst($value->document_name).'</b>',$msg);
                        array_push($expire_date_message, $msg);                    
                        else:
                        //expire within n days
                        $msg = Lang::get('language.doc_expire_date_message3_lang');
                        $msg = str_replace('$docname','<b>'.ucfirst($value->document_name).'</b>',$msg);
                        $msg = str_replace('$expiryDate',@$day_expire_doc, $msg);
                        array_push($expire_date_message, $msg);
                    endif;
                }
                //today expired docs not used in new 
                /*else
                {//expired today
                    $msg = Lang::get('language.doc_expire_date_message4_lang');
                        $msg = str_replace('$docname','<b>'.ucfirst($value->document_name).'</b>',$msg);
                        array_push($expire_date_message, $msg);//Expiry date is over for this document.
                }*/    
            }
        }
        Session::put('notification_expire_date',@$expire_date_message);
    }

    //<!--Get password expiry notifiction-->   
    public function getPasswordExpiryNotification(){
        // Get security details from settings 
        $data['tbl_settings'] = DB::table('tbl_settings')->first();
        $tbl_users    = DB::table('tbl_users')->select('password_date')->where('id',Auth::user()->id)->get();
        $dateDifference = date_diff(date_create($tbl_users[0]->password_date),date_create(date('Y-m-d')))->days; 
        $expiryDate     = ($data['tbl_settings']->settings_pasword_expiry - $dateDifference);
        
        if($expiryDate <= '5' && $data['tbl_settings']->settings_pasword_expiry != 0){
            if($expiryDate > 0){
                if($expiryDate == '1'):
                    $expire_date_message = Lang::get('language.expire_date_message1_lang');
                elseif($expiryDate == '2'):
                    $expire_date_message = Lang::get('language.expire_date_message2_lang');
                else:
                    $msg = Lang::get('language.expire_date_message3_lang');
                    $msg = str_replace('$expiryDate',$expiryDate,$msg);
                    $expire_date_message = $msg;
                endif;
            }else{
                $expire_date_message = Lang::get('language.expire_date_message4_lang');//Expiry date is over for this user.
            }
        }
        //Set session message
        Session::put('password_expire_date',@$expire_date_message);
        return @$expire_date_message;// If needed,use it.
    }

    // String replace for audits actions
    public function stringReplace($var1,$var2,$var3,$actionMsg){
        $actionMsg = str_replace('$var1',$var1,$actionMsg);
        $actionMsg = str_replace('$var2',$var2,$actionMsg);
        $actionMsg = str_replace('$var3',$var3,$actionMsg); 
        return $actionMsg;
    }

    // Check password complexity
    public function checkPasswordComplexity($inputPassword){
        $setings = SettingsModel::select('settings_alphabets','settings_numerics','settings_special_characters','settings_capital_and_small')->get();
        // Checking password has atleast one alphabet
        if($setings[0]->settings_alphabets == session('settings_alphabets')){
            if(preg_match('/[a-zA-Z]/',$inputPassword) == '0')
                return 'alphabet_false';// False
        }
        // Checking password has atleast one number
        if($setings[0]->settings_numerics == session('settings_numerics')){
            if(preg_match('/\d/',$inputPassword) == '0')
                return 'numerics_false';// False
        }
        // Checking password has atleast one special character
        if($setings[0]->settings_special_characters == session('settings_special_characters')){
            if(preg_match('/[^a-zA-Z\d]/', $inputPassword) == '0')
                return 'special_character_false';// False
        }
        // Checking password has atleast one capital and small letter
        if($setings[0]->settings_capital_and_small == session('settings_capital_and_small')){
            if((preg_match('/[a-z]/', $inputPassword) == '0') || (preg_match('/[A-Z]/', $inputPassword) == '0'))
                 return 'capital_and_small_false';// False
        }
    }

    public function audit_clear_notification_check(){
        $audit_delete_notification = array();
        $delete_audit = DB::table('tbl_audits_delete_request')->select('tbl_audits_delete_request.*','tbl_users.user_full_name')->join('tbl_users','tbl_users.username','=','tbl_audits_delete_request.audits_delete_request_username')->where('audits_delete_request_approved_by',Auth::user()->username)->where('audits_delete_request_status',0)->get();
        if($delete_audit)
        {
            foreach ($delete_audit as $value) {                        
                //Avoid time
                $dateTo = $this->removeTimeFrmDate($value->delete_to_date); 
                $msg = Lang::get('language.super_admin').$value->user_full_name.Lang::get('language.request_to_purge').$dateTo;
                array_push($audit_delete_notification, $msg);
                // Get user name
                $userName[]         = $value->audits_delete_request_username;
                $user_full_name[]         = $value->user_full_name;
                $delete_from_date[] = $value->delete_from_date;
                $delete_to_date[]   = $value->delete_to_date;
            }
        }

        $audits_delete_approved_status = DB::table('tbl_audits_delete_request')->select('tbl_audits_delete_request.audits_delete_request_id','tbl_audits_delete_request.audits_delete_request_username','tbl_audits_delete_request.audits_delete_request_approved_by','tbl_audits_delete_request.delete_from_date','tbl_audits_delete_request.delete_to_date','tbl_users.user_full_name')->where('tbl_audits_delete_request.audits_delete_request_username',Auth::user()->username)->where('tbl_audits_delete_request.audits_delete_request_approved_by_who',1)->join('tbl_users','tbl_users.username','=','tbl_audits_delete_request.audits_delete_request_approved_by')->get();    
        if($audits_delete_approved_status){
            $msg2 = Lang::get('language.audits_approved_deleted_msg');
            //Avoid time
            $dateTo = $this->removeTimeFrmDate($audits_delete_approved_status[0]->delete_to_date); 
            $msg2 = $this->stringReplace($dateTo,$audits_delete_approved_status[0]->user_full_name,NULL,$msg2); 
            array_push($audit_delete_notification,$msg2);  
            Session::put('request_username',@$audits_delete_approved_status[0]->audits_delete_request_username); 
        }        
        Session::put('audit_delete_notification',@$audit_delete_notification);
        Session::put('audits_delete_request_username',@$userName);
        Session::put('purge_audits_user_full_name',@$user_full_name);
        Session::put('delete_from_date',@$delete_from_date);
        Session::put('delete_to_date',@$delete_to_date);       
    }

     public function dashboard_widget_default_postion()
     {
        $center_div1 = array('recentdoc','notaccesseddoc');
        $center_div2 = array('wi','docdepartment','doctype','docextension');
        $center_div3 = array('docusers');
        $widget = array('center_div1' => $center_div1,'center_div2' => $center_div2,'center_div3' => $center_div3);
        return $widget;
     }

    public function removeTimeFrmDate($dateTime)
    {
        $dateTime = new DateTime($dateTime);
        return $dateTime->format('Y-m-d');
    }

     public function get_workflow_notification()
    {
        $count=0;
        $results = array(); 
        if (Auth::user()) 
        {
            $username = (Auth::user()->username)?Auth::user()->username:'0';
            $where = array('tdw.document_workflow_responsible_user' => $username,'tdw.document_workflow_notifcation_to_status' => 1);
            $select ="tw.workflow_id,tw.workflow_name,COUNT(tdw.document_workflow_id) as activity";
            $query = DB::table('tbl_document_workflows as tdw');
            $query->join('tbl_workflows as tw','tdw.workflow_stage_id','=','tw.workflow_stage_id');
            $query->selectRaw($select);
            $query->where($where);
            $query->groupBy('tw.workflow_id');

            $results = $query->get();
            $count=count($results);
            if($count)
            {

            }
            else
            {
               $results = array(); 
            }
        }        
        Session::put('total_workflow_assigned_count',$count);
        Session::put('total_workflow_assigned_list',$results);
    }

    public function get_user_notification()
    {
        $count=0;
        $results = array(); 
        if (Auth::user()) 
        {
            $userid= (Auth::user()->id)?Auth::user()->id:'0';
            $where = array('tr.notification_recipient' => $userid,'tr.notification_viewed' => 0);
            $select ="tn.notification_type,tn.notification_title,tn.notification_link,tr.id";
            $query = DB::table('tbl_notifications as tn');
            $query->join('tbl_notification_recipients as tr','tn.notification_id','=','tr.notification_id');
            $query->selectRaw($select);
            $query->where($where);
            $query->groupBy('tn.notification_id');
            $query->orderBy('tn.notification_id', 'DESC');    
            $results = $query->paginate(10);
            $count=$results->count();
            if($count)
            {

            }
            else
            {
               $results = array(); 
            }
        }        
        Session::put('total_notification_count',$count);
        Session::put('total_notification_list',$results);
    }


    public function add_notification($data=array())
    {
        $time_stamp = date('Y-m-d H:i:s');
        $dbdata = array();
        //notification type
        $dbdata['notification_type'] = (isset($data['type']))?$data['type']:'';
        //recipients
        $recipients = (isset($data['recipients']))?$data['recipients']:array();

        $dbdata['notification_priority'] = (isset($data['priority']))?$data['priority']:1;
        $dbdata['notification_title'] = (isset($data['title']))?$data['title']:'';
        $dbdata['notification_details'] = (isset($data['details']))?$data['details']:'';
        $dbdata['notification_link'] = (isset($data['link']))?$data['link']:'';
        $dbdata['notification_sender'] = (isset($data['sender']))?$data['sender']:'';
        $sender = DB::table('tbl_users')->select('user_full_name')->where('id',$dbdata['notification_sender'])->first();
        $sender_name = $sender->user_full_name;
        $dbdata['created_at'] = $time_stamp;
        $dbdata['updated_at'] = $time_stamp;
        $notification_id = DB::table('tbl_notifications')->insertGetId($dbdata);
        $recipients = array_unique($recipients);
        if($recipients)
        {
            //Remove receipient from notification when sender equal receipient
            // if (($key = array_search(Auth::user()->id, $recipients)) !== false) {
            // unset($recipients[$key]);}
            // $recipients = array_unique($recipients);
            // $recipients = array_values($recipients);
            $recipient_array = array();
            foreach ($recipients as $key => $value) 
            {
                if($value)
                {
                    $recipient_array[] = array(
                        'notification_id'=>$notification_id,
                        'notification_recipient'=> $value,
                        'notification_viewed'=> 0,
                        'created_at'=> $time_stamp,
                    );
                }
            }
            // insert notifications
            DB::table('tbl_notification_recipients')->insert($recipient_array);
            //send email notifications start here
            //step 1:   check user over ride mail notifications
            $this->check_over_ride($dbdata['notification_type'],$recipient_array,$sender_name,$dbdata['notification_link'],$dbdata['notification_title']);            
        }
        return true;
    }
    //1:   check user over ride mail notifications
    public function check_over_ride($notification_type,$recipient_array,$sender,$notification_link,$notification_title)
    {
        $override = DB::table('tbl_email_notifications')->select('email_notification_override_email_notifications_settings')->first();
        if($override->email_notification_override_email_notifications_settings == 0)
        {
            //step2 : (not override) -> Use the system default settings
            $this->check_mail_notifications_settings_wise($notification_type,$recipient_array,$sender,$notification_link,$notification_title);
        }
        else if($override->email_notification_override_email_notifications_settings == 1)
        {
            //step 2 : (override) -> Use the user settings
            $this->check_mail_notifications_user_wise($notification_type,$recipient_array,$sender,$notification_link,$notification_title);
        }
        else
        {
            return;
        }

    }
    //2: Use system mail settings
    public function check_mail_notifications_settings_wise($notification_type,$recipient_array,$sender,$notification_link,$notification_title)
    {
        $check_notifications = DB::table('tbl_email_notifications')->first();
        //form notifications
        if($notification_type == 'form' && $check_notifications->email_notification_form_notifications == 1)
        {
            //step 3 : mail notifications
            $this->email_data($recipient_array,$sender,$notification_link,$notification_title);
        }
        //activity workflow notifications
        else if($notification_type == 'workflow' && $check_notifications->email_notification_activity_task_notifications == 1)
        {
            //step 3 : mail notifications
            $this->email_data($recipient_array,$sender,$notification_link,$notification_title);
        }

    }
    //2: Use user mail settings
    public function check_mail_notifications_user_wise($notification_type,$recipient_array,$sender,$notification_link,$notification_title)
    {
        if($recipient_array)
        {
            foreach ($recipient_array as $key => $value) 
            {
                $check_notifications = DB::table('tbl_users')->select('user_activity_task_notifications','user_form_notifications','user_document_notifications','user_signin_notifications')->where('id',$value['notification_recipient'])->first();
                //form
                if($notification_type == 'form' && $check_notifications->user_form_notifications == 1)
                {
                    //step 3 : mail notifications
                    $this->email_data($recipient_array,$sender,$notification_link,$notification_title);
                }
                //activity workflow notifications
                else if($notification_type == 'workflow' && $check_notifications->user_activity_task_notifications == 1)
                {
                    //step 3 : mail notifications
                    $this->email_data($recipient_array,$sender,$notification_link,$notification_title);
                }
            }
        }

    }
    public function email_data($recipient_array,$sender,$notification_link,$notification_title)
    {
        if($recipient_array)
        {
            $setings = DB::table('tbl_settings')->first();
            if($setings)
            {
                $address = $setings->settings_address;
                $logo = $setings->settings_logo;
            }
            foreach ($recipient_array as $key => $value) {
                $recipient = DB::table('tbl_users')->select('email','user_full_name')->where('id',$value['notification_recipient'])->first();
                //mail content details
                $subject = array('subject' => Lang::get('language.notification_mail_subject'),'message' => $notification_title,'by'=>$sender,'to' =>$recipient->user_full_name,'email_to' => $recipient->email,'link' => $notification_link, 'address'=>$address, 'logo'=>$logo);
                //call mail function
                $this->send_mail($subject);
            }
        }
    }
    public function send_mail($subject)
    {
        if (Auth::user()) 
        {
            try 
            {
                $smtp = DB::table('tbl_smtp_details')->where('smtp_details_active_account','active')->first();
                if($smtp)
                {
                    config(['mail.driver' => 'smtp',
                            'mail.host' => $smtp->smtp_details_mailserver,
                            'mail.port' => $smtp->smtp_details_port,
                            'mail.username' => $smtp->smtp_details_username,
                            'mail.password' => decrypt($smtp->smtp_details_password),
                            'mail.encryption' => $smtp->smtp_details_tls_ssl ]);
                }
                $subject['file'] = $file = (isset($subject['file']))?$subject['file']:'';
                $datas =$subject;
                if($file)
                {
                    Mail::send('email', ['request' => $subject], function ($m) use ($datas) {
                            $m->from(config('mail.username'));
                            $m->to($datas['email_to']);
                            $m->subject($datas['subject']);
                            $m->attach($datas['file']);
                        }); 
                    
                }
                else
                {
                Mail::send('email', ['request' => $subject], function ($m) use ($datas) {
                
                    $m->from(config('mail.username'))

                        ->to($datas['email_to'])

                        ->subject($datas['subject']);
                });
            }

                $return = array('status' => 1,'message' => 'Email sent, check your inbox.');   
            
            } 
            catch (\Exception $ex) 
            {
                // print_r($ex->getMessage());
                // exit();
                //$return = array('status' => 0,'message' => $ex->getMessage());
                $msg = Lang::get('language.notification_mail_error');     
                Session::flash('flash_message_warning',$msg);
            }
        }
    }

    // Convert Tiff in to pdf 
    public function tiffToPdf($inputFile,$fileName,$destinationPath){
        $tifFile       = $inputFile;// I/O file
        $fileToBeSaved = $destinationPath.'/'.$fileName.'.pdf';// File to be saved        
        $imagick = new Imagick();
        /*<--Solved problem:trying to handle a multi-page-tiff image with 50 (!) or more pages of 3000x2000 pixels.-->*/
        $imagick->setResourceLimit(imagick::RESOURCETYPE_MEMORY, 256);
        $imagick->setResourceLimit(imagick::RESOURCETYPE_MAP, 256);
        $imagick->setResourceLimit(imagick::RESOURCETYPE_AREA, 1512);
        $imagick->setResourceLimit(imagick::RESOURCETYPE_FILE, 768);
        $imagick->setResourceLimit(imagick::RESOURCETYPE_DISK, -1);
        /*<--Solved-->*/
        $imagick->readImage($tifFile); 
        $imagick->setImageFormat("pdf");
        $imagick->writeImages($fileToBeSaved, true);
        $imagick->clear();
        $imagick->destroy();
        return $fileName.'.pdf';
    }

    public function ftpUpload($upload)
    {
        $return = array('status' => 0 , 'message' => '');
        $destinationPath= (isset($upload['destinationPath']))?$upload['destinationPath']:'website/public/test';        
        $sourceFile=(isset($upload['sourceFile']))?$upload['sourceFile']:'';
        $destinationFile=(isset($upload['destinationFile']))?$upload['destinationFile']:'';
        $ftp_details = DB::table('tbl_ftp_details')->first();
        $domainhost=($ftp_details)?$ftp_details->ftp_details_host:'';
        $port=($ftp_details)?$ftp_details->ftp_details_port:'';
        $domainuser=($ftp_details)?$ftp_details->ftp_details_username:'';
        $domainpass=($ftp_details)?decrypt($ftp_details->ftp_details_password):'';
        $basedirectory="/public_html/";
        $basedirectory="";
        $ftp_conn = ftp_connect($domainhost,$port);
        if($ftp_conn)
        {
            $login = ftp_login($ftp_conn, $domainuser, $domainpass);
            if($login)
            {
                if($basedirectory)
                {
                    $change = ftp_chdir($ftp_conn,$basedirectory);
                    $contents = ftp_nlist($ftp_conn, ".");
                }      
                if($destinationPath)
                {
                    $change = ftp_chdir($ftp_conn,$destinationPath);
                }
                $put = ftp_put($ftp_conn, $destinationFile, $sourceFile, FTP_BINARY);
                $return['status'] = 1;
                $return['message'] = 'OK';
            } 
            ftp_close($ftp_conn);
        }
        return $return;
    }
    public function user_roles()
    {
       return DB::table('tbl_user_roles')->orderBy('user_role_id','ASC')->get();                
    }
    public function department_users_list($departments=array())
    {    
        if(!$departments)
        {
            return array();
        }
        $select ="tu.id,tu.username,tu.user_full_name,tu.user_role,tu.user_permission";
        $query = DB::table('tbl_users as tu');
        $query->join('tbl_users_departments as td','tu.id','=','td.users_id');
        $where = array();
        $query->whereIn('td.department_id', $departments);
        $query->selectRaw($select);
        $query->groupBy('tu.id');
        $query->orderBy('tu.user_full_name', 'ASC');
        return $query->get();               
    }    
}/*<--END-->*/
?>