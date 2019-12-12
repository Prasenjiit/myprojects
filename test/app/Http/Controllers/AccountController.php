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
use App\ModulesModel as ModulesModel;
use Lang;
use Mail;

class AccountController extends Controller
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
            // $data['docType'] = DocumentTypesModel::orderBy('document_type_order', 'ASC')->get();
            $data['tbl_settings'] = DB::table('tbl_settings')->first();
            //echo dec_enc('encrypt','1');
            // echo dec_enc('decrypt','UXlyV3pyS0xXaU16M3RnTWUybVVvUT09');
            //account details
            //echo $data['tbl_settings']->settings_installation_date;
            $installation_date = dec_enc('decrypt',$data['tbl_settings']->settings_installation_date);
            $expiry_date = dec_enc('decrypt',$data['tbl_settings']->settings_expiry_date);
            $tmp_no_of_users = dec_enc('decrypt',$data['tbl_settings']->settings_no_of_users);
            $tmp_view_only_users = dec_enc('decrypt',$data['tbl_settings']->settings_view_only_users);
            $license_key = dec_enc('decrypt',$data['tbl_settings']->settings_license_key); 
            $volume_label = dec_enc('decrypt',$data['tbl_settings']->settings_volume_label);  

            $no_of_users = 0;
            $view_only_users = 0;
            if($tmp_no_of_users==-1){
                $no_of_users = "Unlimited";
            }
            if($tmp_view_only_users=='-1'){
                $view_only_users = "Unlimited";
            }else{
                $view_only_users = $tmp_view_only_users;
            }
            Session::put('settings_installation_date',$installation_date);
            Session::put('settings_expiry_date',$expiry_date);
            Session::put('settings_no_of_users',$no_of_users);
            Session::put('settings_view_only_users',$view_only_users);
            Session::put('settings_license_key',$license_key);
            Session::put('settings_volume_label',$volume_label);

            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();

            $data['modules'] = $this->docObj->get_modules();
            
            $premisevalue = (env('ON_PREMISE'))?env('ON_PREMISE'):0;
            if($premisevalue=="1"){
                $volume_label = $this->getDiskAddress();
                Session::put('settings_volume_label',$volume_label);
            }else{
                $volume_label = $_SERVER['SERVER_ADDR']; 
                Session::put('settings_volume_label',$volume_label);
            }  
  
           return View::make('pages/account/index')->with($data);
       } else {
           return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }   

    public function getDiskAddress() {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $mac = '';
            $diskkey = '';
            $mac = shell_exec('wmic DISKDRIVE GET SerialNumber 2>&1');
            $diskkey = trim($mac);
            return $diskkey;
        }else if(PHP_OS=="Linux"){
            $mac = '';
            $diskkey = '';
            $mac = shell_exec('udevadm info --query=all --name=/dev/sda | grep ID_SERIAL_SHORT'); 
            $diskkey = trim($mac);           
            return $diskkey;        
        }
    } 

    public function mod_update_req(){
        $lickey     =   Input::get('lickey');
        $diskkey     =   Input::get('diskkey');
        $modulesModel = new ModulesModel;
        if( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ) )
        {
            $method=$_GET['method'];
            if($method == 'online')
            {                
                $url=$_GET['url'];
                $ch = curl_init(); 
                $params = array(
                    'lickey' => $lickey,
                    'diskkey' => $diskkey
                );
                $query = http_build_query($params);
                curl_setopt($ch,CURLOPT_URL,"$url?$query");

                curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
                $output=curl_exec($ch);
                curl_close($ch);
                                  
                //echo $output; 
                if($output=="mismatch"){
                    echo $output;
                }else if($output == 'error'){
                    echo $output;
                }else if($output == 'not found'){
                    echo $output;
                }else{
                    $licdetails = explode("|",$output);                      

                    foreach($licdetails as $val){
                        $details_exp = explode("_",$val);
                        
                        $active = dec_enc('encrypt',1);
                        if($details_exp[0]=="m1"){ //import/export
                            $m1k    = dec_enc('encrypt',$details_exp[1].',Export/Import');
                            $m1cnt  = dec_enc('encrypt',$details_exp[2]);
                            $m1ad   = dec_enc('encrypt',$details_exp[3]);
                            $m1ed   = $details_exp[4];
                            $m1ne   = $details_exp[5];    

                            if($m1ne==1){
                                $lexpirydate1 = dec_enc('encrypt','No Expiry');
                            }else{
                                $lexpirydate1 = dec_enc('encrypt',$m1ed);
                            }

                            $ieExists1 = $modulesModel->where('module_id','1')->get()->count();
                            $moduleRecords['module_name']              =   'Export/Import';
                            $moduleRecords['module_activation_key']    =   $m1k;
                            $moduleRecords['module_activation_count']  =   $m1cnt;
                            $moduleRecords['module_activation_date']   =   $m1ad;
                            $moduleRecords['module_expiry_date']       =   $lexpirydate1;                
                            if($ieExists1):                    
                                $modulesModel->where('module_id','1')->update($moduleRecords);
                            else:
                                // Insert
                                $modulesModel->insert($moduleRecords);
                            endif;                       
                        }
                        if($details_exp[0]=="m2"){ //doc annot
                            $m2k    = dec_enc('encrypt',$details_exp[1].',Document Annotation');
                            $m2cnt  = dec_enc('encrypt',$details_exp[2]);
                            $m2ad   = dec_enc('encrypt',$details_exp[3]);
                            $m2ed   = $details_exp[4];  
                            $m2ne   = $details_exp[5];  
                            if($m2ne==1){
                                $lexpirydate2 = dec_enc('encrypt',"No Expiry");
                            }else{
                                $lexpirydate2 = dec_enc('encrypt',$m2ed);
                            }   

                            $ieExists2 = $modulesModel->where('module_id','2')->get()->count();
                            $moduleRecords['module_name']              =   'Document Annotation';
                            $moduleRecords['module_activation_key']    =   $m2k;
                            $moduleRecords['module_activation_count']  =   $m2cnt;
                            $moduleRecords['module_activation_date']   =   $m2ad;
                            $moduleRecords['module_expiry_date']       =   $lexpirydate2;                
                            if($ieExists2):                    
                                $modulesModel->where('module_id','2')->update($moduleRecords);
                            else:
                                // Insert
                                $modulesModel->insert($moduleRecords);
                            endif;                       
                        }
                        if($details_exp[0]=="m3"){ //ocr
                            $m3k    = dec_enc('encrypt',$details_exp[1].',OCR');
                            $m3cnt  = dec_enc('encrypt',$details_exp[2]);
                            $m3ad  = dec_enc('encrypt',$details_exp[3]);
                            $m3ed   = $details_exp[4];  
                            $m3ne   = $details_exp[5];    
                            if($m3ne==1){
                                $lexpirydate3 = dec_enc('encrypt',"No Expiry");
                            }else{
                                $lexpirydate3 = dec_enc('encrypt',$m3ed);
                            }  

                            $ieExists3 = $modulesModel->where('module_id','3')->get()->count();
                            $moduleRecords['module_name']              =   'OCR';
                            $moduleRecords['module_activation_key']    =   $m3k;
                            $moduleRecords['module_activation_count']  =   $m3cnt;
                            $moduleRecords['module_activation_date']   =   $m3ad;
                            $moduleRecords['module_expiry_date']       =   $lexpirydate3;                
                            if($ieExists3):                    
                                $modulesModel->where('module_id','3')->update($moduleRecords);
                            else:
                                // Insert
                                $modulesModel->insert($moduleRecords);
                            endif;                        
                        }
                        if($details_exp[0]=="m4"){ //forms/wf
                            $m4k    = dec_enc('encrypt',$details_exp[1].',Forms & Work Flow');
                            $m4cnt  = dec_enc('encrypt',$details_exp[2]);
                            $m4ad   = dec_enc('encrypt',$details_exp[3]);
                            $m4ed   = $details_exp[4]; 
                            $m4ne   = $details_exp[5];   
                            if($m4ne==1){
                                $lexpirydate4 = dec_enc('encrypt',"No Expiry");
                            }else{
                                $lexpirydate4 = dec_enc('encrypt',$m4ed);
                            }  
                            $ieExists4 = $modulesModel->where('module_id','4')->get()->count();
                            $moduleRecords['module_name']              =   'Forms & Work Flow';
                            $moduleRecords['module_activation_key']    =   $m4k;
                            $moduleRecords['module_activation_count']  =   $m4cnt;
                            $moduleRecords['module_activation_date']   =   $m4ad;
                            $moduleRecords['module_expiry_date']       =   $lexpirydate4;                
                            if($ieExists4):                    
                                $modulesModel->where('module_id','4')->update($moduleRecords);
                            else:
                                // Insert
                                $modulesModel->insert($moduleRecords);
                            endif;                          
                        }
                        if($details_exp[0]=="m5"){//audit
                            $m5k    = dec_enc('encrypt',$details_exp[1].',Audit Trail');
                            $m5cnt  = dec_enc('encrypt',$details_exp[2]);
                            $m5ad   = dec_enc('encrypt',$details_exp[3]);
                            $m5ed   = $details_exp[4];  
                            $m5ne   = $details_exp[5];   
                            if($m5ne==1){
                                $lexpirydate5 = dec_enc('encrypt',"No Expiry");
                            }else{
                                $lexpirydate5 = dec_enc('encrypt',$m5ed);
                            }  

                            $ieExists5 = $modulesModel->where('module_id','5')->get()->count();
                            $moduleRecords['module_name']              =   'Audit Trail';
                            $moduleRecords['module_activation_key']    =   $m5k;
                            $moduleRecords['module_activation_count']  =   $m5cnt;
                            $moduleRecords['module_activation_date']   =   $m5ad;
                            $moduleRecords['module_expiry_date']       =   $lexpirydate5;                
                            if($ieExists5):                    
                                $modulesModel->where('module_id','5')->update($moduleRecords);
                            else:
                                // Insert
                                $modulesModel->insert($moduleRecords);
                            endif;                         
                        }
                        if($details_exp[0]=="m6"){//encrypt/decrypt
                            $m6k    = dec_enc('encrypt',$details_exp[1].',Encrypt/Decrypt');
                            $m6cnt  = dec_enc('encrypt',$details_exp[2]);
                            $m6ad   = dec_enc('encrypt',$details_exp[3]);
                            $m6ed   = $details_exp[4]; 
                            $m6ne   = $details_exp[5];   
                            if($m6ne==1){
                                $lexpirydate6 = dec_enc('encrypt',"No Expiry");
                            }else{
                                $lexpirydate6 = dec_enc('encrypt',$m6ed);
                            }  

                            $ieExists6 = $modulesModel->where('module_id','6')->get()->count();
                            $moduleRecords['module_name']              =   'Encrypt/Decrypt';
                            $moduleRecords['module_activation_key']    =   $m6k;
                            $moduleRecords['module_activation_count']  =   $m6cnt;
                            $moduleRecords['module_activation_date']   =   $m6ad;
                            $moduleRecords['module_expiry_date']       =   $lexpirydate6;                
                            if($ieExists6):                    
                                $modulesModel->where('module_id','6')->update($moduleRecords);
                            else:
                                // Insert
                                $modulesModel->insert($moduleRecords);
                            endif;                          
                        }
                        if($details_exp[0]=="m7"){//apps
                            $m7k    = dec_enc('encrypt',$details_exp[1].',Apps');
                            $m7cnt  = dec_enc('encrypt',$details_exp[2]);
                            $m7ad   = dec_enc('encrypt',$details_exp[3]);
                            $m7ed   = $details_exp[4];   
                            $m7ne   = $details_exp[5];    
                            if($m7ne==1){
                                $lexpirydate7 = dec_enc('encrypt',"No Expiry");
                            }else{
                                $lexpirydate7 = dec_enc('encrypt',$m7ed);
                            }  
                            $ieExists7 = $modulesModel->where('module_id','7')->get()->count();
                            $moduleRecords['module_name']              =   'Apps';
                            $moduleRecords['module_activation_key']    =   $m7k;
                            $moduleRecords['module_activation_count']  =   $m7cnt;
                            $moduleRecords['module_activation_date']   =   $m7ad;
                            $moduleRecords['module_expiry_date']       =   $lexpirydate7;                
                            if($ieExists7):                    
                                $modulesModel->where('module_id','7')->update($moduleRecords);
                            else:
                                // Insert
                                $modulesModel->insert($moduleRecords);
                            endif;                       
                        }
                        if($details_exp[0]=="m8"){//email doc
                            $m8k    = dec_enc('encrypt',$details_exp[1].',Email Documents');
                            $m8cnt  = dec_enc('encrypt',$details_exp[2]);
                            $m8ad   = dec_enc('encrypt',$details_exp[3]);
                            $m8ed   = $details_exp[4];   
                            $m8ne   = $details_exp[5];  
                            if($m8ne==1){
                                $lexpirydate8 = dec_enc('encrypt',"No Expiry");
                            }else{
                                $lexpirydate8 = dec_enc('encrypt',$m8ed);
                            }  
                            $ieExists8 = $modulesModel->where('module_id','8')->get()->count();
                            $moduleRecords['module_name']              =   'Email Documents';
                            $moduleRecords['module_activation_key']    =   $m8k;
                            $moduleRecords['module_activation_count']  =   $m8cnt;
                            $moduleRecords['module_activation_date']   =   $m8ad;
                            $moduleRecords['module_expiry_date']       =   $lexpirydate8;                
                            if($ieExists8):                    
                                $modulesModel->where('module_id','8')->update($moduleRecords);
                            else:
                                // Insert
                                $modulesModel->insert($moduleRecords);
                            endif;                         
                        }
                        if($details_exp[0]=="m9"){//web publishing
                            $m9k    = dec_enc('encrypt',$details_exp[1].',Website Publishing');
                            $m9cnt  = dec_enc('encrypt',$details_exp[2]);
                            $m9ad   = dec_enc('encrypt',$details_exp[3]);
                            $m9ed   = $details_exp[4];   
                            $m9ne   = $details_exp[5];  
                            if($m9ne==1){
                                $lexpirydate9 = dec_enc('encrypt',"No Expiry");
                            }else{
                                $lexpirydate9 = dec_enc('encrypt',$m9ed);
                            }  
                            $ieExists9 = $modulesModel->where('module_id','9')->get()->count();
                            $moduleRecords['module_name']              =   'Website Publishing';
                            $moduleRecords['module_activation_key']    =   $m9k;
                            $moduleRecords['module_activation_count']  =   $m9cnt;
                            $moduleRecords['module_activation_date']   =   $m9ad;
                            $moduleRecords['module_expiry_date']       =   $lexpirydate9;                
                            if($ieExists9):                    
                                $modulesModel->where('module_id','9')->update($moduleRecords);
                            else:
                                // Insert
                                $modulesModel->insert($moduleRecords);
                            endif;                         
                        }
                        if($details_exp[0]=="m10"){//task manager
                            $m10k   = dec_enc('encrypt',$details_exp[1].',Task Manager');
                            $m10cnt = dec_enc('encrypt',$details_exp[2]);
                            $m10ad  = dec_enc('encrypt',$details_exp[3]);
                            $m10ed  = $details_exp[4];
                            $m10ne   = $details_exp[5];    
                            if($m10ne==1){
                                $lexpirydate10 = dec_enc('encrypt',"No Expiry");
                            }else{
                                $lexpirydate10 = dec_enc('encrypt',$m10ed);
                            }  
                            $ieExists10 = $modulesModel->where('module_id','10')->get()->count();
                            $moduleRecords['module_name']              =   'Task Manager';
                            $moduleRecords['module_activation_key']    =   $m10k;
                            $moduleRecords['module_activation_count']  =   $m10cnt;
                            $moduleRecords['module_activation_date']   =   $m10ad;
                            $moduleRecords['module_expiry_date']       =   $lexpirydate10;                
                            if($ieExists10):                    
                                $modulesModel->where('module_id','10')->update($moduleRecords);
                            else:
                                // Insert
                                $modulesModel->insert($moduleRecords);
                            endif;                          
                        }
                        if($details_exp[0]=="m11"){//scanner
                            $m11k   = dec_enc('encrypt',$details_exp[1].',Scanner');
                            $m11cnt = dec_enc('encrypt',$details_exp[2]);
                            $m11ad  = dec_enc('encrypt',$details_exp[3]);
                            $m11ed  = $details_exp[4];  
                            $m11ne   = $details_exp[5];   
                            if($m11ne==1){
                                $lexpirydate11 = dec_enc('encrypt',"No Expiry");
                            }else{
                                $lexpirydate11 = dec_enc('encrypt',$m11ed);
                            }  
                            $ieExists11 = $modulesModel->where('module_id','11')->get()->count();
                            $moduleRecords['module_name']              =   'Scanner';
                            $moduleRecords['module_activation_key']    =   $m11k;
                            $moduleRecords['module_activation_count']  =   $m11cnt;
                            $moduleRecords['module_activation_date']   =   $m11ad;
                            $moduleRecords['module_expiry_date']       =   $lexpirydate11;                
                            if($ieExists11):                    
                                $modulesModel->where('module_id','11')->update($moduleRecords);
                            else:
                                // Insert
                                $modulesModel->insert($moduleRecords);
                            endif;                         
                        }
                    }
                }              
            }             
        } else {
            exit('No direct access allowed.');
        }
    }

    public function modules()
    {   
        $moddata = $this->docObj->get_modules();
        //$incr =1;
        foreach ($moddata as $key => $value) {
            
            if($value->module_id==1){
                $data['data_ie_activ_date'] = dec_enc('decrypt',$value->module_activation_date);
                //$data['data_ie_cnt'] = dec_enc('decrypt',$value->module_activation_count);
                $data['data_ie_exp_date'] = dec_enc('decrypt',$value->module_expiry_date);
                $iestatus = dec_enc('decrypt',$value->module_activation_key);
                $iestatarr = explode(',', $iestatus);
                if(($iestatarr[0])&&$iestatarr[1]=="Export/Import"){
                    $data['data_ie_status'] = $iestatarr[0];
                }else{
                     $data['data_ie_status'] = 0;
                }
            }

            if($value->module_id==2){
                $data['data_docanno_activ_date'] = dec_enc('decrypt',$value->module_activation_date);
                //$data['data_docanno_cnt'] = dec_enc('decrypt',$value->module_activation_count);
                $data['data_docanno_exp_date'] = dec_enc('decrypt',$value->module_expiry_date);
                $docanno_status = dec_enc('decrypt',$value->module_activation_key);
                $docannoarr = explode(',', $docanno_status);
                if(($docannoarr[0])&&$docannoarr[1]=="Document Annotation"){
                    $data['data_docanno_status'] = $docannoarr[0];
                }else{
                     $data['data_docanno_status'] = 0;
                }

            }
            if($value->module_id==3){
                $data['data_ocr_activ_date'] = dec_enc('decrypt',$value->module_activation_date);
                //$data['data_ocr_cnt'] = dec_enc('decrypt',$value->module_activation_count);
                $data['data_ocr_exp_date'] = dec_enc('decrypt',$value->module_expiry_date);
                $data_ocr_status = dec_enc('decrypt',$value->module_activation_key);
                $ocrstatarr = explode(',', $data_ocr_status);
                if(($ocrstatarr[0])&&$ocrstatarr[1]=="OCR"){
                    $data['data_ie_status'] = $ocrstatarr[0];
                }else{
                     $data['data_ie_status'] = 0;
                }
            }
            if($value->module_id==4){
                $data['data_fw_activ_date'] = dec_enc('decrypt',$value->module_activation_date);
                //$data['data_fw_cnt'] = dec_enc('decrypt',$value->module_activation_count);
                $data['data_fw_exp_date'] = dec_enc('decrypt',$value->module_expiry_date);
                $data_fw_status = dec_enc('decrypt',$value->module_activation_key);
                $fwstatarr = explode(',', $data_fw_status);
                if(($fwstatarr[0])&&$fwstatarr[1]=="Forms & Work Flow"){
                    $data['data_ie_status'] = $fwstatarr[0];
                }else{
                     $data['data_ie_status'] = 0;
                }
            }
            if($value->module_id==5){
                $data['data_audt_activ_date'] = dec_enc('decrypt',$value->module_activation_date);
                //$data['data_audt_cnt'] = dec_enc('decrypt',$value->module_activation_count);
                $data['data_audt_exp_date'] = dec_enc('decrypt',$value->module_expiry_date);
                $data_audt_status = dec_enc('decrypt',$value->module_activation_key);
                $auditstatarr = explode(',', $data_audt_status);
                if(($auditstatarr[0])&&$auditstatarr[1]=="Audit Trail"){
                    $data['data_ie_status'] = $auditstatarr[0];
                }else{
                     $data['data_ie_status'] = 0;
                }
            }
            if($value->module_id==6){
                $data['data_endec_activ_date'] = dec_enc('decrypt',$value->module_activation_date);
                //$data['data_endec_cnt'] = dec_enc('decrypt',$value->module_activation_count);
                $data['data_endec_exp_date'] = dec_enc('decrypt',$value->module_expiry_date);
                $data_endec_status = dec_enc('decrypt',$value->module_activation_key);
                $endecstatarr = explode(',', $data_endec_status);
                if(($endecstatarr[0])&&$endecstatarr[1]=="Encrypt/Decrypt"){
                    $data['data_ie_status'] = $endecstatarr[0];
                }else{
                     $data['data_ie_status'] = 0;
                }
            }
            if($value->module_id==7){
                $data['data_app_activ_date'] = dec_enc('decrypt',$value->module_activation_date);
                $data['data_app_cnt'] = dec_enc('decrypt',$value->module_activation_count);
                $data['data_app_exp_date'] = dec_enc('decrypt',$value->module_expiry_date);
                $data_app_status = dec_enc('decrypt',$value->module_activation_key);
                $appstatarr = explode(',', $data_app_status);
                if(($appstatarr[0])&&$appstatarr[1]=="Apps"){
                    $data['data_ie_status'] = $appstatarr[0];
                }else{
                     $data['data_ie_status'] = 0;
                }
            }
            if($value->module_id==8){
                $data['data_emaildoc_activ_date'] = dec_enc('decrypt',$value->module_activation_date);
                //$data['data_emaildoc_cnt'] = dec_enc('decrypt',$value->module_activation_count);
                $data['data_emaildoc_exp_date'] = dec_enc('decrypt',$value->module_expiry_date);
                $data_emaildoc_status = dec_enc('decrypt',$value->module_activation_key);
                $emailstatarr = explode(',', $data_emaildoc_status);
                if(($emailstatarr[0])&&$emailstatarr[1]=="Email Documents"){
                    $data['data_ie_status'] = $emailstatarr[0];
                }else{
                     $data['data_ie_status'] = 0;
                }
            }
            if($value->module_id==9){
                $data['data_webpub_activ_date'] = dec_enc('decrypt',$value->module_activation_date);
                //$data['data_webpub_cnt'] = dec_enc('decrypt',$value->module_activation_count);
                $data['data_webpub_exp_date'] = dec_enc('decrypt',$value->module_expiry_date);
                $data['data_webpub_status'] = dec_enc('decrypt',$value->module_activation_key);
                $iestatarr = explode(',', $iestatus);
                if(($iestatarr[0])&&$iestatarr[1]=="Website Publishing"){
                    $data['data_ie_status'] = $iestatarr[0];
                }else{
                     $data['data_ie_status'] = 0;
                }
            }
            if($value->module_id==10){
                $data['data_taskman_activ_date'] = dec_enc('decrypt',$value->module_activation_date);
                //$data['data_taskman_cnt'] = dec_enc('decrypt',$value->module_activation_count);
                $data['data_taskman_exp_date'] = dec_enc('decrypt',$value->module_expiry_date);
                $data_taskman_status = dec_enc('decrypt',$value->module_activation_key);
                $taskstatarr = explode(',', $data_taskman_status);
                if(($taskstatarr[0])&&$taskstatarr[1]=="Task Manager"){
                    $data['data_ie_status'] = $taskstatarr[0];
                }else{
                     $data['data_ie_status'] = 0;
                }
            }
            if($value->module_id==11){
                $data['data_scanr_activ_date'] = dec_enc('decrypt',$value->module_activation_date);
                //$data['data_scanr_cnt'] = dec_enc('decrypt',$value->module_activation_count);
                $data['data_scanr_exp_date'] = dec_enc('decrypt',$value->module_expiry_date);
                $data_scanr_status = dec_enc('decrypt',$value->module_activation_key);
                $scanrstatarr = explode(',', $data_scanr_status);
                if(($scanrstatarr[0])&&$scanrstatarr[1]=="Scanner"){
                    $data['data_scanr_status'] = $scanrstatarr[0];
                }else{
                     $data['data_scanr_status'] = 0;
                }
            }
            //$data[''] = $value->
            //$incr++;
        }

        $usrdata = $this->docObj->get_userlicense();
        //$incr =1;
        foreach ($usrdata as $key => $val) {
            $data['settings_no_of_users'] = decrypt($val->settings_no_of_users);
            $data['settings_expiry_date'] = decrypt($val->settings_expiry_date);
        }
        
        //exit();
        return View::make('pages/account/modules')->with($data);  
    }

    public function modulesSave(){

        //import/export
        $ie_status = dec_enc('encrypt',Input::get('ie_status'));
        $ie_activ_date = dec_enc('encrypt',Input::get('ie_activ_date'));
        //$ie_cnt = dec_enc('encrypt',Input::get('ie_cnt'));
        $ie_exp_date = dec_enc('encrypt',Input::get('ie_exp_date'));

        $ie_module_id = Input::get('ie_module_id');
        $ie_modleRecords = array('module_activation_key'=>$ie_status,
                                //'module_activation_count'=>$ie_cnt,
                                'module_activation_date'=>$ie_activ_date,
                                'module_expiry_date'=>$ie_exp_date);
        // Update => By this condition only one row exists 
        // Checking ftp row already exists
        DB::table('tbl_modules')->where('module_id',$ie_module_id)->update($ie_modleRecords);         

        
        //audits
        $audt_status = dec_enc('encrypt',Input::get('audt_status'));
        $audt_activ_date = dec_enc('encrypt',Input::get('audt_activ_date'));
        //$audt_cnt = dec_enc('encrypt',Input::get('audt_cnt'));
        $audt_exp_date = dec_enc('encrypt',Input::get('audt_exp_date'));

        $audt_module_id = Input::get('audt_module_id');
        $audt_modleRecords = array('module_activation_key'=>$audt_status,
                                //'module_activation_count'=>$audt_cnt,
                                'module_activation_date'=>$audt_activ_date,
                                'module_expiry_date'=>$audt_exp_date);
        // Update => By this condition only one row exists 
        // Checking ftp row already exists
        DB::table('tbl_modules')->where('module_id',$audt_module_id)->update($audt_modleRecords);     

        //forms & workflow
        $fw_status = dec_enc('encrypt',Input::get('fw_status'));
        $fw_activ_date = dec_enc('encrypt',Input::get('fw_activ_date'));
        //$fw_cnt = dec_enc('encrypt',Input::get('fw_cnt'));
        $fw_exp_date = dec_enc('encrypt',Input::get('fw_exp_date'));

        $audt_module_id = Input::get('audt_module_id');
        $audt_modleRecords = array('module_activation_key'=>$audt_status,
                                //'module_activation_count'=>$audt_cnt,
                                'module_activation_date'=>$audt_activ_date,
                                'module_expiry_date'=>$audt_exp_date);
        // Update => By this condition only one row exists 
        // Checking ftp row already exists
        DB::table('tbl_modules')->where('module_id',$audt_module_id)->update($audt_modleRecords);  


        //document annotation
        $docanno_status = dec_enc('encrypt',Input::get('docanno_status'));
        $docanno_activ_date = dec_enc('encrypt',Input::get('docanno_activ_date'));
        //$docanno_cnt = dec_enc('encrypt',Input::get('docanno_cnt'));
        $docanno_exp_date = dec_enc('encrypt',Input::get('docanno_exp_date'));

        $docanno_module_id = Input::get('docanno_module_id');
        $docanno_modleRecords = array('module_activation_key'=>$docanno_status,
                                //'module_activation_count'=>$docanno_cnt,
                                'module_activation_date'=>$docanno_activ_date,
                                'module_expiry_date'=>$docanno_exp_date);
        // Update => By this condition only one row exists 
        // Checking ftp row already exists
        DB::table('tbl_modules')->where('module_id',$docanno_module_id)->update($docanno_modleRecords);  

        //encrypt decrypt
        $endec_status = dec_enc('encrypt',Input::get('endec_status'));
        $endec_activ_date = dec_enc('encrypt',Input::get('endec_activ_date'));        
        //$endec_cnt = dec_enc('encrypt',Input::get('endec_cnt'));
        $endec_exp_date = dec_enc('encrypt',Input::get('endec_exp_date'));

        $endec_module_id = Input::get('endec_module_id');
        $endec_modleRecords = array('module_activation_key'=>$endec_status,
                                //'module_activation_count'=>$endec_cnt,
                                'module_activation_date'=>$endec_activ_date,
                                'module_expiry_date'=>$endec_exp_date);
        // Update => By this condition only one row exists 
        // Checking ftp row already exists
        DB::table('tbl_modules')->where('module_id',$endec_module_id)->update($endec_modleRecords);  
        
        //apps
        $app_status = dec_enc('encrypt',Input::get('app_status'));
        $app_activ_date = dec_enc('encrypt',Input::get('app_activ_date'));        
        $app_cnt = dec_enc('encrypt',Input::get('app_cnt'));
        $app_exp_date = dec_enc('encrypt',Input::get('app_exp_date'));

        $app_module_id = Input::get('app_module_id');
        $app_modleRecords = array('module_activation_key'=>$app_status,
                                'module_activation_count'=>$app_cnt,
                                'module_activation_date'=>$app_activ_date,
                                'module_expiry_date'=>$app_exp_date);
        // Update => By this condition only one row exists 
        // Checking ftp row already exists
        DB::table('tbl_modules')->where('module_id',$app_module_id)->update($app_modleRecords);  

        //email documents
        $emaildoc_status = dec_enc('encrypt',Input::get('emaildoc_status'));
        $emaildoc_activ_date = dec_enc('encrypt',Input::get('emaildoc_activ_date'));        
        //$emaildoc_cnt = dec_enc('encrypt',Input::get('emaildoc_cnt'));
        $emaildoc_exp_date = dec_enc('encrypt',Input::get('emaildoc_exp_date'));

        $emaildoc_module_id = Input::get('emaildoc_module_id');
        $emaildoc_modleRecords = array('module_activation_key'=>$emaildoc_status,
                                //'module_activation_count'=>$emaildoc_cnt,
                                'module_activation_date'=>$emaildoc_activ_date,
                                'module_expiry_date'=>$emaildoc_exp_date);
        // Update => By this condition only one row exists 
        // Checking ftp row already exists
        DB::table('tbl_modules')->where('module_id',$emaildoc_module_id)->update($emaildoc_modleRecords);  

        //web publish
        $webpub_status = dec_enc('encrypt',Input::get('webpub_status'));
        $webpub_activ_date = dec_enc('encrypt',Input::get('webpub_activ_date'));        
        //$webpub_cnt = dec_enc('encrypt',Input::get('webpub_cnt'));
        $webpub_exp_date = dec_enc('encrypt',Input::get('webpub_exp_date'));

        $webpub_module_id = Input::get('webpub_module_id');
        $webpub_modleRecords = array('module_activation_key'=>$webpub_status,
                                //'module_activation_count'=>$webpub_cnt,
                                'module_activation_date'=>$webpub_activ_date,
                                'module_expiry_date'=>$webpub_exp_date);
        // Update => By this condition only one row exists 
        // Checking ftp row already exists
        DB::table('tbl_modules')->where('module_id',$webpub_module_id)->update($webpub_modleRecords);  

        //task manager
        $taskman_status = dec_enc('encrypt',Input::get('taskman_status'));
        $taskman_activ_date = dec_enc('encrypt',Input::get('taskman_activ_date'));        
        //$taskman_cnt = dec_enc('encrypt',Input::get('taskman_cnt'));
        $taskman_exp_date = dec_enc('encrypt',Input::get('taskman_exp_date'));

        $taskman_module_id = Input::get('taskman_module_id');
        $taskman_modleRecords = array('module_activation_key'=>$taskman_status,
                                //'module_activation_count'=>$taskman_cnt,
                                'module_activation_date'=>$taskman_activ_date,
                                'module_expiry_date'=>$taskman_exp_date);
        // Update => By this condition only one row exists 
        // Checking ftp row already exists
        DB::table('tbl_modules')->where('module_id',$taskman_module_id)->update($taskman_modleRecords);  

        //scanner
        $scanr_status = dec_enc('encrypt',Input::get('scanr_status'));
        $scanr_activ_date = dec_enc('encrypt',Input::get('scanr_activ_date'));        
        //$scanr_cnt = dec_enc('encrypt',Input::get('scanr_cnt'));
        $scanr_exp_date = dec_enc('encrypt',Input::get('scanr_exp_date'));

        $scanr_module_id = Input::get('scanr_module_id');
        $scanr_modleRecords = array('module_activation_key'=>$scanr_status,
                                //'module_activation_count'=>$scanr_cnt,
                                'module_activation_date'=>$scanr_activ_date,
                                'module_expiry_date'=>$scanr_exp_date);
        // Update => By this condition only one row exists 
        // Checking ftp row already exists
        DB::table('tbl_modules')->where('module_id',$scanr_module_id)->update($scanr_modleRecords);  

        //ocr
        $ocr_status = dec_enc('encrypt',Input::get('ocr_status'));
        $ocr_activ_date = dec_enc('encrypt',Input::get('ocr_activ_date'));        
        //$ocr_cnt = dec_enc('encrypt',Input::get('ocr_cnt'));
        $ocr_exp_date = dec_enc('encrypt',Input::get('ocr_exp_date'));

        $ocr_module_id = Input::get('ocr_module_id');
        $ocr_modleRecords = array('module_activation_key'=>$ocr_status,
                                //'module_activation_count'=>$ocr_cnt,
                                'module_activation_date'=>$ocr_activ_date,
                                'module_expiry_date'=>$ocr_exp_date);
        // Update => By this condition only one row exists 
        // Checking ftp row already exists
        DB::table('tbl_modules')->where('module_id',$ocr_module_id)->update($ocr_modleRecords);  


        // settings license detials
        $user_lic = encrypt(Input::get('user_lic'));
        $user_lic_exp = encrypt(Input::get('user_lic_exp'));
        $usr_Records = array('settings_no_of_users'=>$user_lic,
                                'settings_expiry_date'=>$user_lic_exp);
        // Update => By this condition only one row exists 
        // Checking ftp row already exists
        DB::table('tbl_settings')->where('settings_id',1)->update($usr_Records);  



        $data['success'] = "Updated Successfully.";

        $usrdata = $this->docObj->get_userlicense();
        //$incr =1;
        foreach ($usrdata as $key => $val) {
            $data['settings_no_of_users'] = decrypt($val->settings_no_of_users);
            $data['settings_expiry_date'] = decrypt($val->settings_expiry_date);
        }

        $moddata = $this->docObj->get_modules();
        //$incr =1;
        foreach ($moddata as $key => $value) {
            
            if($value->module_id==1){
                $data['data_ie_activ_date'] = dec_enc('decrypt',$value->module_activation_date);
                //$data['data_ie_cnt'] = dec_enc('decrypt',$value->module_activation_count);
                $data['data_ie_exp_date'] = dec_enc('decrypt',$value->module_expiry_date);
                $data['data_ie_status'] = dec_enc('decrypt',$value->module_activation_key);
            }

            if($value->module_id==2){
                $data['data_docanno_activ_date'] = dec_enc('decrypt',$value->module_activation_date);
                //$data['data_docanno_cnt'] = dec_enc('decrypt',$value->module_activation_count);
                $data['data_docanno_exp_date'] = dec_enc('decrypt',$value->module_expiry_date);
                $data['data_docanno_status'] = dec_enc('decrypt',$value->module_activation_key);

            }
            if($value->module_id==3){
                $data['data_ocr_activ_date'] = dec_enc('decrypt',$value->module_activation_date);
                //$data['data_ocr_cnt'] = dec_enc('decrypt',$value->module_activation_count);
                $data['data_ocr_exp_date'] = dec_enc('decrypt',$value->module_expiry_date);
                $data['data_ocr_status'] = dec_enc('decrypt',$value->module_activation_key);

            }
            if($value->module_id==4){
                $data['data_fw_activ_date'] = dec_enc('decrypt',$value->module_activation_date);
                //$data['data_fw_cnt'] = dec_enc('decrypt',$value->module_activation_count);
                $data['data_fw_exp_date'] = dec_enc('decrypt',$value->module_expiry_date);
                $data['data_fw_status'] = dec_enc('decrypt',$value->module_activation_key);

            }
            if($value->module_id==5){
                $data['data_audt_activ_date'] = dec_enc('decrypt',$value->module_activation_date);
                //$data['data_audt_cnt'] = dec_enc('decrypt',$value->module_activation_count);
                $data['data_audt_exp_date'] = dec_enc('decrypt',$value->module_expiry_date);
                $data['data_audt_status'] = dec_enc('decrypt',$value->module_activation_key);

            }
            if($value->module_id==6){
                $data['data_endec_activ_date'] = dec_enc('decrypt',$value->module_activation_date);
                //$data['data_endec_cnt'] = dec_enc('decrypt',$value->module_activation_count);
                $data['data_endec_exp_date'] = dec_enc('decrypt',$value->module_expiry_date);
                $data['data_endec_status'] = dec_enc('decrypt',$value->module_activation_key);

            }
            if($value->module_id==7){
                $data['data_app_activ_date'] = dec_enc('decrypt',$value->module_activation_date);
                $data['data_app_cnt'] = dec_enc('decrypt',$value->module_activation_count);
                $data['data_app_exp_date'] = dec_enc('decrypt',$value->module_expiry_date);
                $data['data_app_status'] = dec_enc('decrypt',$value->module_activation_key);

            }
            if($value->module_id==8){
                $data['data_emaildoc_activ_date'] = dec_enc('decrypt',$value->module_activation_date);
                //$data['data_emaildoc_cnt'] = dec_enc('decrypt',$value->module_activation_count);
                $data['data_emaildoc_exp_date'] = dec_enc('decrypt',$value->module_expiry_date);
                $data['data_emaildoc_status'] = dec_enc('decrypt',$value->module_activation_key);

            }
            if($value->module_id==9){
                $data['data_webpub_activ_date'] = dec_enc('decrypt',$value->module_activation_date);
                //$data['data_webpub_cnt'] = dec_enc('decrypt',$value->module_activation_count);
                $data['data_webpub_exp_date'] = dec_enc('decrypt',$value->module_expiry_date);
                $data['data_webpub_status'] = dec_enc('decrypt',$value->module_activation_key);

            }
            if($value->module_id==10){
                $data['data_taskman_activ_date'] = dec_enc('decrypt',$value->module_activation_date);
                //$data['data_taskman_cnt'] = dec_enc('decrypt',$value->module_activation_count);
                $data['data_taskman_exp_date'] = dec_enc('decrypt',$value->module_expiry_date);
                $data['data_taskman_status'] = dec_enc('decrypt',$value->module_activation_key);

            }
            if($value->module_id==11){
                $data['data_scanr_activ_date'] = dec_enc('decrypt',$value->module_activation_date);
                //$data['data_scanr_cnt'] = dec_enc('decrypt',$value->module_activation_count);
                $data['data_scanr_exp_date'] = dec_enc('decrypt',$value->module_expiry_date);
                $data['data_scanr_status'] = dec_enc('decrypt',$value->module_activation_key);

            }
            //$data[''] = $value->
            //$incr++;
        }
        
        return View::make('pages/account/modules')->with($data); 
    }
    
    public function updatedata()
    {   
            //<--module records update-->
            $module_id = Input::get('module_id');
            $status = dec_enc('encrypt',Input::get('status'));
            $count = dec_enc('encrypt',Input::get('count'));
            $activdate = dec_enc('encrypt',Input::get('activation_date'));
            $expdate = dec_enc('encrypt',Input::get('expiry_date'));
            $modleRecords = array('module_activation_key'=>$status,
                                    'module_activation_count'=>$count,
                                    'module_activation_date'=>$activdate,
                                    'module_expiry_date'=>$expdate);
            // Update => By this condition only one row exists 
            // Checking ftp row already exists
            DB::table('tbl_modules')->where('module_id',$module_id)->update($modleRecords);         

           return View::make('pages/account/updatedata');

    }
   
}/*<--END-->*/
