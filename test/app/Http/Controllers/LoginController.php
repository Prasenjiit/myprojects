<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ApplicationLogController;
use App\Http\Requests;
use App\Users as Users;
use Request as ReqIp;
use Auth;
use View;
use Validator;
use Input;
use Session;
use DateTime;
use Hash;
use DB;
use App\ActivityModel as ActivityModel;
use App\StacksModel as StacksModel;
use App\DepartmentsModel as DepartmentsModel;
use App\DocumentTypesModel as DocumentTypesModel;
use App\DocumentsModel as DocumentsModel;
use App\ModulesModel as ModulesModel;
use App\Mylibs\Common;
use Lang;
use Config;

class LoginController extends Controller
{
	public function __construct() {

	}
	public function index(){
        //echo dec_enc('encrypt','DRDL5-G9NUF-P3NB9-8PNM7-78XC2-0'); AG
        //echo dec_enc('encrypt','ARWL5-GYNUS-T24B9-9PNM1-72XH2-0');
        
        $tbl_settings = DB::table('tbl_settings')->first();
        $lic_key = $tbl_settings->settings_license_key;
        $instl_date = $tbl_settings->settings_installation_date;
        $active_stat = $tbl_settings->settings_active;
        $premisevalue = (env('ON_PREMISE'))?env('ON_PREMISE'):0;
        //Session::put('onpremise',$premisevalue);
        if($premisevalue=="1"){
            $volume_label = $this->getDiskAddress();
            Session::put('settings_volume_label',$volume_label);
        }else{
            $volume_label = $_SERVER['SERVER_ADDR']; 
            Session::put('settings_volume_label',$volume_label);
        }
       

        if((file_exists(config('app.license_folder').'license.txt')) && (file_exists(config('app.license_folder').'license.key'))){
            $file = file_get_contents(config('app.license_folder').'license.txt');
            if($lic_key==$file){
                return view('auth.login');
            }else{
				
				//delete installation controller files
                /*$controllerPath = config('app.controller_path');
                $files = array('/WelcomeController.php', '/RequirementsController.php', '/PermissionsController.php', '/EnvironmentController.php', '/DatabaseController.php', '/ImportTableController.php', '/FinalController.php');
                foreach ($files as $value) {
                    if(file_exists($controllerPath.$value)){
                        unlink($controllerPath.$value);
                    }
                   
                }
                // delete helpers file
                $helpersPath = config('app.helpers_path');
                $helperfiles = array('/DatabaseManager.php', '/EnvironmentManager.php', '/FinalInstallManager.php', '/RequirementsChecker.php', '/PermissionsChecker.php', '/MigrationsHelper.php', '/InstalledFileManager.php');
                foreach ($helperfiles as $val) {
                    if(file_exists($helpersPath.$val)){
                        unlink($helpersPath.$val);
                    }
                   
                }

                // delete resource file
                $resourcePath = config('app.resource_path');
                if(File::isDirectory($resourcePath.'/installer')){
                    File::deleteDirectory($resourcePath.'/installer');
                }*/
				
                Session::put('lic_mesage','This seems to be a new installation of FileEazy! or the license key is missing. Please input the license key to activate FileEazy!');
                return view('auth.activate');
            }
        }else{
            if($active_stat){
                if($active_stat==1){
                    Session::put('lic_mesage','Oops! license key missing, You must reactivate your license with your license key.');
                    return view('auth.activate');
                }else{
                    Session::put('lic_mesage','Oops! Something wrong, You must reactivate your license with your license key.');
                    return view('auth.activate');
                }
            }else{
                return view('auth.activate');
            }            
        }       
    }

    function dec_enc($action, $string) {
        $output = false;
     
        $encrypt_method = "AES-256-CBC";
        $secret_key = '12dasdq3g5b2434b';
        $secret_iv = '35dasqq3t5b9431q';
     
        // hash
        $key = hash('sha256', $secret_key);
        
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
     
        if( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        }
        else if( $action == 'decrypt' ){
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
     
        return $output;
    }

    public function getDiskAddress() {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // $operatingsys = "windows";
            // ob_start(); // Turn on output buffering 
            // system('ipconfig /all'); //Execute external program to display output 
            // $mycom=ob_get_contents(); // Capture the output into a variable 
            // ob_clean(); // Clean (erase) the output buffer 
            // $findme = "Physical"; 
            // $pmac = strpos($mycom, $findme); // Find the position of Physical text 
            // $mac = substr($mycom,($pmac+36),17); // Get Physical Address 
            $mac = '';
            $mac = shell_exec('wmic DISKDRIVE GET SerialNumber 2>&1');
            // $macarray  = explode(' ',$mac);
            // print_r($macarray);
            // $hddserialkey = $macarray[1];
            return $mac;
        }else if(PHP_OS=="Linux"){
            $mac = '';
            $mac = shell_exec('udevadm info --query=all --name=/dev/sda | grep ID_SERIAL_SHORT');            
            return $mac;
			// exec('netstat -ie', $result);
			// if(is_array($result)) {
			// 	$iface = array();
			// 	foreach($result as $key => $line) {
			// 		if($key > 0) {
			// 			$tmp = str_replace(" ", "", substr($line, 0, 10));
			// 			if($tmp <> "") {
			// 				$macpos = strpos($line, "HWaddr");
			// 				if($macpos !== false) {
			// 					$iface[] = array('iface' => $tmp, 'mac' => strtolower(substr($line, $macpos+7, 17)));
			// 				}
			// 			}
			// 		}
			// 	}
			// 	return $iface[0]['mac'];
			// } else {
			// 	return "notfound";
			// }
        }
    }
	
    public function activateKeyRequest(){
        $serialkey     =   trim(Input::get('serialkey'));
        $modulesModel = new ModulesModel;
		$mac = Session::get('settings_volume_label');
      
        $serialarr = explode('-', $serialkey);
        // if the sysytem is offline 
        if($serialarr[5]==0){
            $curdate = date('d-m-Y');
            $liddate = dec_enc('encrypt', $curdate);
            $leddate = dec_enc('encrypt','No Expiry');
            $lnu = dec_enc('encrypt',-1);
            $lvou = dec_enc('encrypt',-1);
            // $llk = dec_enc('encrypt',$serialarr[0].'-'.$serialarr[1].'-'.$serialarr[2].'-'.$serialarr[3].'-'.$serialarr[4]);
            $llk = dec_enc('encrypt',$serialarr[0].'-'.$serialarr[1].'-'.$serialarr[2].'-'.$serialarr[3].'-'.$serialarr[4].'-'.$serialarr[5]);
            $active = dec_enc('encrypt',1);
            $ldk = dec_enc('encrypt',$mac);

            if(file_exists(config('app.license_folder').'serialkey.txt')){

                $file = trim(file_get_contents(config('app.license_folder').'serialkey.txt'));
                $file = dec_enc('decrypt',$file);
                //echo $file;
                //echo $llk;
                if($serialkey==$file){
                    $record = DB::table('tbl_settings')->select('*')->first();
                    $dataToUpdate = array(
                        'settings_installation_date'=> $liddate,
                        'settings_expiry_date'      => $leddate,
                        'settings_no_of_users'      => $lnu,
                        'settings_view_only_users'  => $lvou,
                        'settings_license_key'      => $llk,
                        'settings_active'           => $active,
                        'settings_volume_label'     => $ldk);
                    DB::table('tbl_settings')->where('settings_id',$record->settings_id)->update($dataToUpdate);

                    //write license key file
                    $content = $llk;
                    $fp = fopen(config('app.license_folder').'license.key',"wb");
                    fwrite($fp,$content);
                    fclose($fp);
                    //write license txt file
                    $content1 = $llk;
                    $fp1 = fopen(config('app.license_folder').'license.txt',"wb");
                    fwrite($fp1,$content1);
                    fclose($fp1);

                   
                    $m1k    = dec_enc('encrypt','1,Export/Import');
                    $m1cnt  = dec_enc('encrypt','0');
                    $m1ad   = dec_enc('encrypt',$curdate);
                    $lexpirydate1 = dec_enc('encrypt','No Expiry');           

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


                    $m2k    = dec_enc('encrypt','0,Document Annotation');
                    $m2cnt  = dec_enc('encrypt','0');
                    $m2ad   = dec_enc('encrypt',$curdate);
                    $lexpirydate2 = dec_enc('encrypt',"No Expiry");            

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

                    $m3k    = dec_enc('encrypt','0,OCR');
                    $m3cnt  = dec_enc('encrypt','0');
                    $m3ad   = dec_enc('encrypt',$curdate);
                    $lexpirydate3 = dec_enc('encrypt',"No Expiry");
                    
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


                    $m4k    = dec_enc('encrypt','0,Forms & Work Flow');
                    $m4cnt  = dec_enc('encrypt','0');
                    $m4ad   = dec_enc('encrypt',$curdate);           
                    $lexpirydate4 = dec_enc('encrypt',"No Expiry");
                 
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


                    $m5k    = dec_enc('encrypt','1,Audit Trail');
                    $m5cnt  = dec_enc('encrypt','0');
                    $m5ad   = dec_enc('encrypt',$curdate);
                    $lexpirydate5 = dec_enc('encrypt',"No Expiry");

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

                                  
                    $m6k    = dec_enc('encrypt','0,Encrypt/Decrypt');
                    $m6cnt  = dec_enc('encrypt','0');
                    $m6ad   = dec_enc('encrypt',$curdate);
                    $lexpirydate6 = dec_enc('encrypt',"No Expiry");
                   
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

                    $m7k    = dec_enc('encrypt','0,Apps');
                    $m7cnt  = dec_enc('encrypt','0');
                    $m7ad   = dec_enc('encrypt',$curdate);
                    $lexpirydate7 = dec_enc('encrypt',"No Expiry");
                   
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


                    $m8k    = dec_enc('encrypt','0,Email Documents');
                    $m8cnt  = dec_enc('encrypt','0');
                    $m8ad   = dec_enc('encrypt',$curdate);
                    $lexpirydate8 = dec_enc('encrypt',"No Expiry");
                      
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


                    $m9k    = dec_enc('encrypt','0,Website Publishing');
                    $m9cnt  = dec_enc('encrypt','0');
                    $m9ad   = dec_enc('encrypt',$curdate);
                    $lexpirydate9 = dec_enc('encrypt',"No Expiry");
                   
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


                    $m10k    = dec_enc('encrypt','0,Task Manager');
                    $m10cnt  = dec_enc('encrypt','0');
                    $m10ad   = dec_enc('encrypt',$curdate);
                    $lexpirydate10 = dec_enc('encrypt',"No Expiry");
                     
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

                    $m11k    = dec_enc('encrypt','0,Scanner');
                    $m11cnt  = dec_enc('encrypt','0');
                    $m11ad   = dec_enc('encrypt',$curdate);
                    $lexpirydate11 = dec_enc('encrypt',"No Expiry");
                      
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
                }else{
                    echo "mismatch";
                }
            }else{
                echo "not found";
            }

        }else{
            if( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ) )
            {
                $method=$_GET['method'];
                if($method == 'online')
                {
                    
                    $url=$_GET['url'];
                    $ch = curl_init(); 
                    $params = array(
                        'serial' => $serialkey,
                        'diskkey' => $mac);
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
                            //print_r($details_exp);
                            if($details_exp[0]=="lid"){
                                $liddate = $details_exp[1];
                            }
                            if($details_exp[0]=="led"){
                                if($details_exp[1]=="noexpiry"){
                                    $leddate = dec_enc('encrypt',"No Expiry");
                                }else{
                                    $leddate = $details_exp[1];
                                }
                            }
                            if($details_exp[0]=="lnu"){
                                $lnu = $details_exp[1];
                            }
                            if($details_exp[0]=="lvou"){
                                $lvou = $details_exp[1];
                            }
                            if($details_exp[0]=="llk"){
                                $llk = $details_exp[1];
                            }

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

                            if($details_exp[0]=="dk"){//scanner
                                $ldk   = dec_enc('encrypt',$details_exp[1]);                           
                            }
                        }
                        
                        // checking wether data already exists or not
                        $record = DB::table('tbl_settings')->select('*')->first();
                        $dataToUpdate = array(
                            'settings_installation_date'=> $liddate,
                            'settings_expiry_date'      => $leddate,
                            'settings_no_of_users'      => $lnu,
                            'settings_view_only_users'  => $lvou,
                            'settings_license_key'      => $llk,
                            'settings_active'           => $active,
                            'settings_volume_label'     => $ldk);

                        DB::table('tbl_settings')->where('settings_id',$record->settings_id)->update($dataToUpdate);
                        //write license key file
                        $content = $llk;
                        $fp = fopen(config('app.license_folder').'license.key',"wb");
                        fwrite($fp,$content);
                        fclose($fp);
                        //write license txt file
                        $content1 = $llk;
                        $fp1 = fopen(config('app.license_folder').'license.txt',"wb");
                        fwrite($fp1,$content1);
                        fclose($fp1);
                    }              
                }             
            } else {
                exit('No direct access allowed.');
            }
        }
    }
	
	public function loginProcess(Request $request) { 
		$this->validateLogin($request);

        //get loginusername
        $userName =$request->input($this->loginUsername()); 
        /*<-- Captcha -->*/
        if( (Input::get('is_captcha_exists') == 'yes' && Session::get('is_limit_exceed') == 'yes') || (Input::get('is_captcha_exists') == 'yes' && Input::get('check_cap') == 'yes') ):
            $captcha = null;
            if(isset($_POST['g-recaptcha-response'])){
                $captcha=$_POST['g-recaptcha-response'];
                if(!$captcha){
                    Session::put('captcha_validation_error','error');
                    Session::put('is_limit_exceed','yes');
                    return redirect()->back()->withInput();
                }else{
                    Session::forget('captcha_validation_error');
                }
                $response=json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LdUxSsUAAAAAJq_lLhngH2Rzo5QKqDzYhWV3WEt&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
                if($response['success'] == false)
                {
                    echo '<h2>Google Captcha Error.Please Go to back and try again.</h2>';exit;
                }
            }
        endif;
        /*<-- Captcha -->*/

        /*<--Updated by developer-->*/                
        //get current datetime and make it lessthan 5 minute
        date_default_timezone_set('Asia/Kolkata');
        $date = new DateTime();
        $date->modify('-5 minutes');
        $formatted_date = $date->format('Y-m-d H:i:s');

        //check user login count date within (login attempt)  minute or not
        $getTime = DB::table('tbl_users')->where('username',$userName)->where('user_login_count_date','<',$formatted_date)->where('user_login_count_date','!=','0000-00-00 00:00:00')->where('user_lock_status','=',0)->get();

        //if attempt is greater than 5 minute
        if(!empty($getTime)){ //make the login count date and login count is set to 0
            Users::where('username','=',$userName)->update(['user_login_count_date' => '0000-00-00 00:00:00','user_login_count' => 0]);
            echo "updated";
        }
        //get no of users from the tbl_settings
        $data['tbl_settings'] = DB::table('tbl_settings')->first();
        $no_of_users = dec_enc('decrypt',$data['tbl_settings']->settings_no_of_users);
        $count_login_user = DB::table('tbl_users')->where('login_status','1')->count();

        //get userlock status based on login username
        $atmptUsr = DB::table('tbl_users')->select('id','user_lock_status','login_status','password')->where('username',$userName)->get();

        //check the array is not empty
        if (!empty($atmptUsr)) {  
            //get the lock status 0->unlocked 1->locked
            $lockStatus      = @$atmptUsr[0]->user_lock_status;

            if($lockStatus==1){ //login user is locked display lockout error message
                $seconds = 60;
                return redirect()->back()
                ->withInput($request->only($this->loginUsername(), 'remember'))
                ->withErrors([
                    $this->loginUsername() => $this->getLockoutErrorMessage($seconds),
                ]);
            }else{ // if the login user is uncloked 
                 
                $date = new DateTime();
                $nowDate = $date->format('Y-m-d H:i:s');
                //get user login count and login count date
                $atmptUsr = DB::table('tbl_users')->select('user_login_count','user_login_count_date')->where('username','=',$userName)->get();
                $loginCnt      = @$atmptUsr[0]->user_login_count;
                $loginCntDate  = @$atmptUsr[0]->user_login_count_date;
                // if maximum login attempt is zero then no need to unlock the user
                if($this->maxLoginAttempts() != '0'){
                  
                    // Checking password correct or not
                    $UsrDetails = DB::table('tbl_users')->select('id','password')->where('username',$userName)->get();
                    if(Hash::check(Input::get('password'), @$UsrDetails[0]->password)){
                        Users::where('username', '=', $userName)->update(['user_login_count_date' => $nowDate,'user_login_count' => '0','user_lock_status'=>'0']);
                    }else{
                        //if the count is equals 0, set the date and login count is 1
                        if($loginCnt==0){ 
                            $count = 1;
                            Users::where('username', '=', $userName)->update(['user_login_count_date' => $nowDate,'user_login_count' => $count]);
                        }
                        if(($loginCnt>0) && ($loginCnt<$this->maxLoginAttempts())){ 
                            $count = $loginCnt+1;
                            Users::where('username', '=', $userName)->update(['user_login_count_date' => $nowDate,'user_login_count' => $count]);
                        }

                        /*<--To set captcha-->*/
                        if( ($this->maxLoginAttempts() != '4') && ($this->maxLoginAttempts() > '4')){
                            
                            if($loginCnt+1 == '4' || $loginCnt+1 > '4'){
                                Session::put('is_limit_exceed','yes');

                                if($loginCnt+1 == '4')
                                    return redirect()->back()->withInput();
                            }
                        }

                        if($loginCnt+1 < '4'):
                            Session::put('is_limit_exceed','no');
                        endif;
                        /*<--To set captcha-->*/

                        // checking login attempt
                        if(($loginCnt+1 ==$this->maxLoginAttempts())){ 
                            // unlock the user
                            Users::where('username', '=', $userName)->update(['user_lock_status' => 1,'user_login_count' => 0]); 
                            // Distroy session
                            Session::forget('userName');
                            Session::forget('is_limit_exceed');
                            Session::forget('captcha_validation_error');
                            $seconds = 60;
                            return redirect()->back()
                            ->withInput($request->only($this->loginUsername(), 'remember'))
                            ->withErrors([
                                $this->loginUsername() => $this->getLockoutErrorMessage($seconds),
                            ]);
                        }
                    }
                }


                //get userlock status based on login username
                $atmptUsr1 = DB::table('tbl_users')->select('login_status','user_view_only')->where('username',$userName)->get();
                //get no of users from the tbl_settings
                $data['tbl_settings'] = DB::table('tbl_settings')->first();
                $no_of_users = dec_enc('decrypt',$data['tbl_settings']->settings_no_of_users);
                $view_only_users = dec_enc('decrypt',$data['tbl_settings']->settings_view_only_users);

                $count_login_user = DB::table('tbl_users')->where('login_status','1')->where('user_view_only','0')->count();
                $count_view_only_user = DB::table('tbl_users')->where('login_status','1')->where('user_view_only','1')->count();

                if(@$atmptUsr1[0]->login_status!=1){
                    if(@$atmptUsr1[0]->user_view_only==1){
                        if(($view_only_users!="-1")&&($count_view_only_user>=$view_only_users)){
                            return redirect()->back()
                                ->withInput($request->only($this->loginUsername(), 'remember'))
                                ->withErrors([
                                    $this->loginUsername() => $this->getvouExceedsErrorMessage(),
                                ]);
                        }
                    }else{
                        if(($count_login_user>=$no_of_users)&&($no_of_users!="-1")){
                            return redirect()->back()
                                ->withInput($request->only($this->loginUsername(), 'remember'))
                                ->withErrors([
                                    $this->loginUsername() => $this->getUserExceedsErrorMessage(),
                                ]);
                        }   
                    }                                     
                }
                
                /*$volume_label = $this->getDiskAddress();
                if(dec_enc('decrypt',$data['tbl_settings']->settings_volume_label)!=$volume_label){
                    return redirect()->back()
                        ->withInput($request->only($this->loginUsername(), 'remember'))
                        ->withErrors([
                            $this->loginUsername() => $this->getMACErrorMessage(),
                        ]);
                    
                }*/

                //check license expiry
				if(dec_enc('decrypt',$data['tbl_settings']->settings_expiry_date)!="No Expiry"){
                    if(strtotime(date("d-m-Y")) > strtotime(dec_enc('decrypt',$data['tbl_settings']->settings_expiry_date))){
                        return redirect()->back()
                                    ->withInput($request->only($this->loginUsername(), 'remember'))
                                    ->withErrors([
                                        $this->loginUsername() => $this->getUserLicenseExpiredErrorMessage(),
                                    ]);
                    }
                }
              
                // If the class is using the ThrottlesLogins trait, we can automatically throttle
                // the login attempts for this application. We'll key this by the username and
                // the IP address of the client making these requests into this application.
                $throttles = $this->isUsingThrottlesLoginsTrait();
                if ($throttles && $lockedOut = $this->hasTooManyLoginAttempts($request)) {
                    $this->fireLockoutEvent($request);
                    return $this->sendLockoutResponse($request);
                }
                $credentials = $this->getCredentials($request);
                if (Auth::guard($this->getGuard())->attempt($credentials, $request->has('remember'))) {
                    Users::where('username','=',Auth::user()->username)->where('password',Auth::user()->password)->update(['user_login_count_date' => '0000-00-00 00:00:00','user_login_count' => -1]);
                    return $this->handleUserWasAuthenticated($request, $throttles);
                }

                // If the login attempt was unsuccessful we will increment the number of attempts
                // to login and redirect the user back to the login form. Of course, when this
                // user surpasses their maximum number of attempts they will get locked out.
                /*if ($throttles && ! $lockedOut) {
                    $this->incrementLoginAttempts($request);
                }*/
            }
        }
        /*<--Updated by developer-->*/
        return $this->sendFailedLoginResponse($request);
	}

    

    protected function getMACErrorMessage()
    {
        return Lang::has('auth.macerror') ? Lang::get('auth.macerror') : 'System identifier validation failed.';
    }

    protected function getvouExceedsErrorMessage()
    {
        return Lang::has('auth.viewuserexceed') ? Lang::get('auth.viewuserexceed') : 'User login exceeds the View User License.';
    }

     protected function getUserExceedsErrorMessage()
    {
        return Lang::has('auth.exceed') ? Lang::get('auth.exceed') : 'User login exceeds the User License.';
    }

    protected function getUserLicenseExpiredErrorMessage()
    {
        return Lang::has('auth.exceed') ? Lang::get('auth.expired') : 'License has been expired.';
    }

    protected function getLockoutErrorMessage($seconds)
    {
        return Lang::has('auth.throttle')
            ? Lang::get('auth.throttle', ['seconds' => $seconds])
            : 'Too many login attempts. Please try again in '.$seconds.' seconds.';
    }
	protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->loginUsername() => 'required', 'password' => 'required',
        ]);
    }
    public function loginUsername()
    {
        return property_exists($this, 'username') ? $this->username : 'username';
    }
    protected function sendFailedLoginResponse(Request $request)
    {   
        $userName =$request->input($this->loginUsername());
        Session::put('failed_login_user_name',$userName);// To get geolocation if login failed
        $atmptTime = DB::table('tbl_settings')->select('settings_login_attempt_time','settings_login_attempts')->get();
        $logAtmptTme   = $atmptTime[0]->settings_login_attempt_time ;
        $loginAttempts   = $atmptTime[0]->settings_login_attempts ;

        $atmptUsr = DB::table('tbl_users')->select('user_login_count','user_login_count_date')->where('username',$userName)->get();
        
        if($loginAttempts == '0'){
           
            return redirect()->back()
            ->withInput($request->only($this->loginUsername(), 'remember'))
            ->withErrors([
                $this->loginUsername() => $this->getFailedLogin(),
            ]);
        }else{

            return redirect()->back()
            ->withInput($request->only($this->loginUsername(), 'remember'))
            ->withErrors([
                $this->loginUsername() => $this->getFailedLoginMessage($logAtmptTme,$this->maxLoginAttempts()),
            ]);
        }            
    }

    protected function maxLoginAttempts()
    {
        return property_exists($this, 'maxLoginAttempts') ? $this->maxLoginAttempts : 5;
    }
    protected function getFailedLoginMessage($timeLimit,$logAtmpt)
    { 
        return 'Sign In failed. Either the username or password is wrong. If you are an existing user your account will be locked out after '.$logAtmpt.' attempts in '.$timeLimit.' minutes. If you forgot the password, please click on Forgot Password link to reset the password.';
        //return 'These credentials do not match our records. You have made '.$atmptCnt.' unsuccessful attempt(s). If the number of unsuccessful attempts exceed '.$logAtmpt.', your account will be locked for login. In case you forgot the password, please click on Forgot Password to reset the password';
    }
    protected function isUsingThrottlesLoginsTrait()
    {
        return in_array(
            ThrottlesLogins::class, class_uses_recursive(static::class)
        );
    }

    protected function getCredentials(Request $request)
    {
        return $request->only($this->loginUsername(), 'password');
    }
    protected function getGuard()
    {
        return property_exists($this, 'guard') ? $this->guard : null;
    }
    protected function handleUserWasAuthenticated(Request $request, $throttles)
    {
        if ($throttles) {
            $this->clearLoginAttempts($request);
        }
       
        if (method_exists($this, 'authenticated')) {
            return $this->authenticated($request, Auth::guard($this->getGuard())->user());
        }

        //last login date
        date_default_timezone_set('Asia/Kolkata');
        $date = new DateTime();
        $date->format('Y-m-d H:i:s');

        $usrId = Auth::user()->id;
        $ipaddress = ReqIp::ip();
        Users::where('id', '=', $usrId)->update(['user_lastlogin_date' => $date,'login_status' => 1]);

        //getting the password expiry days from settings page 
        $paswdExp = DB::table('tbl_settings')->select('settings_pasword_expiry')->get();
        $expryNo   = $paswdExp[0]->settings_pasword_expiry;
        //if the expiry is set greater than 0
        if($expryNo>0){
            $usrPasswd = DB::table('tbl_users')->select('password_date')->where('username',Auth::user()->username)->get();
            $paswdDate   = $usrPasswd[0]->password_date;
            $todate = $date->format('Y-m-d'); 
            $startTimeStamp = strtotime($todate);
            $endTimeStamp = strtotime($paswdDate);
            $timeDiff = abs($endTimeStamp - $startTimeStamp);
            $numberDays = $timeDiff/86400;  // 86400 seconds in one day
            // and you might want to convert to integer
            $numberDays = intval($numberDays);
            if($numberDays>$expryNo){ //if the password expires redirect to reset password page
                return redirect('/reset');
            }else{ //success redirects to home page
                Users::where('username','=',Auth::user()->username)->update(['user_login_count_date' => '0000-00-00 00:00:00','user_login_count' => 0]);
                $this->auth_location_details();
                return redirect('/home');
            }
        }else{ //if the expiry is set equals 0   (there is no check needed redirect to home page)
            Users::where('username','=',Auth::user()->username)->update(['user_login_count_date' => '0000-00-00 00:00:00','user_login_count' => 0]);
            return redirect('/home');
        }
        
    }

    // Save geolocation if login failed
    public function auth_location_details(){
            if(config('app.on_premise')== true)
            {
                $ip = Users::getUserIpAddr();
                $location = '';
            }
            else
            {
                $ip=Input::get('hidd_ip');
                if(!$ip)
                {
                    $ip=Input::get('ip');
                }
                $location=Input::get('hidd_location');
                if(!$location)
                {
                    $location=Input::get('location');
                }
            }
            if(Input::get('user')!= "" || Input::get('user')!= null)
            {
                if (Users::where('username', '=', Input::get('user'))->exists()) 
                {
                   // user name exist
                    $user = Input::get('user');
                    $actionDes = "$user Sign In failure near $ip, $location.";
                    (new AuditsController)->loginLog($user, 'Sign In Failure', 'Sign In Failure', $actionDes, $ip, $location);
                    echo "login failed, existing user";
                }
                else
                {
                    $user = 'Unknown User';
                    $actionDes = 'Unknown User - '.Input::get('user')." Sign In failure near $ip, $location.";
                    (new AuditsController)->loginLog($user, 'Sign In Failure', 'Sign In Failure', $actionDes, $ip, $location);
                    echo "login failed, unknown user";
                }
            }
            if (Auth::user()) {
            $user = Auth::user()->username;
            $actionDes = "$user Signed In $ip $location";
            if(($ip == null) && ($location == null))
            {
                $actionDes = "$user Signed In";
            }
            (new AuditsController)->loginLog($user, 'Sign In', 'Sign In', $actionDes, $ip, $location);
            echo "login success";
            }
            else
            {
                echo "login failed";
            }
        
    }


    public function auth_location_details_no_conn(){
        $ip = Users::getUserIpAddr();
        $location = '';
        if(Input::get('user')!= "" || Input::get('user')!= null)
        {
            if (Users::where('username', '=', Input::get('user'))->exists()) {
               // user name exist
                $user = Input::get('user');
                $actionDes = "$user Sign In failure near $ip, $location.";
                (new AuditsController)->loginLog(@$user, 'Sign In Failure', 'Sign In Failure', @$actionDes, @$ip, @$location);
                echo "login failed, existing user";
            }
            else
            {
                $user = 'Unknown User';
                $actionDes = 'Unknown User - '.Input::get('user')." Sign In failure near $ip, $location.";
                (new AuditsController)->loginLog($user, 'Sign In Failure', 'Sign In Failure', @$actionDes, @$ip, @$location);
                echo "login failed, unknown user";
            }
        }
        if (Auth::user()) {
        $user = Auth::user()->username;
        $actionDes = "$user Signed In $ip $location";
        if(($ip == null) && ($location == null))
        {
            $actionDes = "$user Signed In";
        }
        (new AuditsController)->loginLog($user, 'Sign In', 'Sign In', $actionDes, @$ip, @$location);
        echo "login success";
        }
        else
        {
            echo "login failed";
        }
        
    }
}