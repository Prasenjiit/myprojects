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
use ZipArchive;
use Config;


class BckpRestreController extends Controller
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
            $data['emailNotif']  = DB::table('tbl_email_notifications')->get();             
            //getting all backup files
            $dir = config('app.zip_backup_path');
            $row_file = array();
            //Session::forget('status');
            $noOfRecords=0;
            if(is_dir($dir))
            {
                //descending order by date
                $myarray = glob($dir."*fileeazy*");
                sort($myarray);
                $cntarr = count($myarray);
                $incr =1;
                foreach(array_reverse($myarray) as $file) 
                {   
                    $noOfRecords++;
                    //remove path from name
                    $file = basename($file);
                    //conver bytes to mb,kb
                    $bytes = filesize($dir.$file);
                    if ($bytes >= 1073741824)
                    {
                        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
                    }
                    elseif ($bytes >= 1048576)
                    {
                        $bytes = number_format($bytes / 1048576, 2) . ' MB';
                    }
                    elseif ($bytes >= 1024)
                    {
                        $bytes = number_format($bytes / 1024, 2) . ' KB';
                    }
                    elseif ($bytes > 1)
                    {
                        $bytes = $bytes . ' bytes';
                    }
                    elseif ($bytes == 1)
                    {
                        $bytes = $bytes . ' byte';
                    }
                    else
                    {
                        $bytes = '0 bytes';
                    }
                    $row_file[] = array(
                        'incr' => $incr,
                        'filename' => $file,
                        'size' => $bytes,
                        'date' => date ("Y-m-d H:i:s", filemtime($dir.$file)),
                        'actions' => '<a href="javascript:void(0);"><i class="fa fa-hdd-o" title="Restore file" onclick="restorefile(\''.$file.'\')" cursor:pointer;" id='.$file.'></i></a>&nbsp;&nbsp;<a href='.URL('/DownloadBckup').'/'.$file.' title="Download"><i class="fa fa-fw fa-download" id='.$file.'></i></a>&nbsp;&nbsp;<i class="fa fa-fw fa-trash" title="Delete" onclick="del(\''.$file.'\')" style="color: red; cursor:pointer;" id='.$file.'></i>'
                    ); 
                    $incr++;
                }
            }
            $data['data'] = $row_file;            
            $data['recordsTotal'] = $noOfRecords;

            return View::make('pages/backup/index')->with($data);
       } else {
            return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }

    public function dobackup(){
        $backupmode = Input::get('bckup');
        $datetime = date("Y-m-d_H-i-s");
        $file_size = 0;
        $totalfile_size = 0;
        $zip = new ZipArchive;
        if(($backupmode==2) || ($backupmode==3)){ // backup documents as zip
            
            $files = array();
            $data = DB::table('tbl_documents')->where('document_file_name','!=','')->get();
            $pack = 1;
            $totalpack = 1;

            foreach ($data as $key => $val) 
            {   
                $totalfile_size += $val->document_size;
                //if(($file_size>943718400)&&($file_size<1258291200)){ // check the filesize is greater than 900mb and lessthan 1200 mb                    
                if(($totalfile_size>943718400)&&($totalfile_size<1258291200)){
                    $totalpack++;
                    $totalfile_size = 0;
                }                
            }
            
            $datacnt = count($data);
            $totcnt = 1;
            foreach ($data as $key => $value) 
            {   
                $file_size += $value->document_size;
                array_push($files, $value->document_file_name);               

                if(($file_size>943718400)&&($file_size<1258291200)){ // check the filesize is greater than 900mb and lessthan 1200 mb 
                    $export_zip_name = $datetime."_".'fileeazy_doc_'.Auth::user()->username."_part".$pack."_of_".$totalpack.".zip";
                    $filename = config('app.zip_backup_path').$export_zip_name;                    
                    if($zip->open($filename, ZipArchive::CREATE)=== TRUE)
                    {
                        foreach ($files as $file) 
                        {
                            //if file exists
                            if(file_exists(config('app.base_path').$file))
                            {
                                $zip->addFile(config('app.base_path').$file, $file);                                
                            }
                        }
                        $zip->close();
                        $files = array();
                        // Get update action message
                        $actionMsg = Lang::get('language.backup_action_msg');
                        $actionname = "Document";
                        $actionDes = $this->docObj->stringReplace($actionname,$export_zip_name,Auth::user()->username,$actionMsg);
                        $result = (new AuditsController)->backuplog(Auth::user()->username,'Backup/Restore', 'Backup',$actionDes);
                        
                    }
                    $files = array();
                    $file_size = 0;
                    $pack++;
                }else{ // incase of the total file size is lesser than 100mb 
                	$export_zip_name = $datetime."_".'fileeazy_doc_'.Auth::user()->username.".zip";
                    $filename = config('app.zip_backup_path').$export_zip_name;                    
                    if($zip->open($filename, ZipArchive::CREATE)=== TRUE)
                    {
                        foreach ($files as $file) 
                        {
                            //if file exists
                            if(file_exists(config('app.base_path').$file))
                            {
                                $zip->addFile(config('app.base_path').$file, $file);                                
                            }
                        }
                        $zip->close();
                    }
                    if($datacnt==$totcnt){
                        // Get update action message
                        $actionMsg = Lang::get('language.backup_action_msg');
                        $actionname = "Document";
                        $actionDes = $this->docObj->stringReplace($actionname,$export_zip_name,Auth::user()->username,$actionMsg);
                        $result = (new AuditsController)->backuplog(Auth::user()->username,'Backup/Restore', 'Backup',$actionDes);
                    }
                }    
                $totcnt++;            
            }          
            $res = 1; 
        }

        if(($backupmode==1) || ($backupmode==3)){ //backup database as sql  
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=".$datetime."_"."fileeazy_db_".Auth::user()->username.".sql");
            header("Pragma: no-cache");
            header("Expires: 0");

            //MySQL connection parameters
            $dbhost = config()->get('database.connections.mysql.host');
            $dbuser = config()->get('database.connections.mysql.username');
            $dbpsw = config()->get('database.connections.mysql.password');
            $dbname = config()->get('database.connections.mysql.database');

            $connection = mysqli_connect($dbhost,$dbuser,$dbpsw,$dbname);
            $tables = array();
            $result = mysqli_query($connection,"SHOW TABLES");
          
            while($row = mysqli_fetch_row($result)){
                $tables[] = $row[0];
            }
            $return = '';
            foreach($tables as $table){
                $result = mysqli_query($connection,"SELECT * FROM ".$table);
                $num_fields = mysqli_num_fields($result);                
                if(($table!="tbl_settings")&&($table!="tbl_users")&&($table!="tbl_users_departments")){
                    $return .= 'DROP TABLE IF EXISTS '.$table.';';
                    $row2 = mysqli_fetch_row(mysqli_query($connection,"SHOW CREATE TABLE ".$table));
                    $return .= "\n\n".$row2[1].";\n\n";              
                    for($i=0;$i<$num_fields;$i++){
                        while($row = mysqli_fetch_row($result)){
                            $return .= "INSERT INTO ".$table." VALUES(";
                            for($j=0;$j<$num_fields;$j++){
                                $row[$j] = addslashes($row[$j]);
                                if(isset($row[$j])){ $return .= '"'.$row[$j].'"';}
                                else{ $return .= '""';}
                                if($j<$num_fields-1){ $return .= ',';}
                            }
                            $return .= ");\n";
                        }
                    }
                    $return .= "\n\n\n";
                }                
            }
            $export_name = $datetime."_"."fileeazy_db_".Auth::user()->username.".sql";
            $file = config('app.zip_backup_path').$export_name;
            // Get update action message
            $actionMsg = Lang::get('language.backup_action_msg');
            $actionname = "Database";
            $actionDes = $this->docObj->stringReplace($actionname,$export_name,Auth::user()->username,$actionMsg);
            $result = (new AuditsController)->backuplog(Auth::user()->username,'Backup/Restore', 'Backup',$actionDes);

            //save file
            $handle = fopen($file,"w+");
            fwrite($handle,$return);
            fclose($handle);
            $res = 1;
        } 
        //Session::put('status', 'Backup created successfully.'); 
       	echo $res;
    }

    //download zip file
    public function getZip($filename='')
    {
        $data = array();
        if(Auth::user())
        {
            $headers = array(
            'Content-Type' => 'application/octet-stream',
            );
            $fullpath = config('app.zip_backup_path')."{$filename}";
                       //check file exist
            if($filename && file_exists($fullpath))
            {
                //download file automatically
                return response()->download($fullpath, $filename,$headers);
            }
            else
            {
               return response()->view('404_error',$data,404); 
            }

            // Get update action message
            $actionMsg = Lang::get('language.backup_action_msg');
            $actionname = "File";
            $actionDes = $this->docObj->stringReplace($actionname,$export_name,Auth::user()->username,$actionMsg);
            $result = (new AuditsController)->backuplog(Auth::user()->username,'Backup/Restore', 'Backup',$actionDes);
        }
        else
        {
           return response()->view('404_error',$data,404); 
        }
    }

    public function doRestore(Request $request){
        $zip = new ZipArchive;
        $res = '';
        $restrfname = Input::get('restrfname');
        $extnsion = Input::get('extnsion');
        if($extnsion=="zip"){
            $zipFileName = config('app.zip_backup_path').$restrfname;
            $opened = $zip->open( $zipFileName, ZIPARCHIVE::CREATE );
            if(($opened === true)){
                // Extract file
                $zip->extractTo(config('app.base_path'));
                $zip->close();
                $res = 1;
                // Get update action message
                $actionMsg = Lang::get('language.restr_action_msg');
                $actionname = "File";
                $actionDes = $this->docObj->stringReplace($actionname,$restrfname,Auth::user()->username,$actionMsg);
                $result = (new AuditsController)->backuplog(Auth::user()->username,'Backup/Restore', 'Restore',$actionDes);
            }else{
                $res = 0;
                //die("cannot open {$zipFileName} for writing.");                
            }
        }else if($extnsion=="sql"){
            $restredestinationPath  = config('app.zip_backup_path'); // upload path
            if(file_exists($restredestinationPath.$restrfname)){
                $filename = config('app.zip_backup_path').$restrfname;
                //MySQL connection parameters
                $dbhost = config()->get('database.connections.mysql.host');
                $dbuser = config()->get('database.connections.mysql.username');
                $dbpsw = config()->get('database.connections.mysql.password');
                $dbname = config()->get('database.connections.mysql.database');

                $connection = mysqli_connect($dbhost,$dbuser,$dbpsw,$dbname);

                $handle = fopen($filename,"r+");
                $contents = fread($handle,filesize($filename));
                $sql = explode(';',$contents);

                $tables = array();
                $result = mysqli_query($connection,"SHOW TABLES");
                while($row = mysqli_fetch_row($result)){
                    $tables[] = $row[0];
                }
                //print_r($tables);
                $fail = 0;
                $success = 0;
                foreach($sql as $query){
                    if(strstr($query,'DROP')){
                        //get table name from the sql
                        $split = explode(" ", $query);
                        $tablename = $split[count($split)-1];
                        if (in_array($tablename, $tables)) { 
                           $success++;
                        } else { 
                            $fail++;
                        } 
                    }            
                }

                if($fail==0){
                    foreach($sql as $query1){
                        try {
                            $result = mysqli_query($connection,$query1);
                            if($result){
                                $res = 1;
                            }else{
                                $res = 0;
                            }
                        } catch(Exception $e) {
                            $res = 0;
                        }
                    }
                }     
                
                fclose($handle);     
                // Get update action message
                $actionMsg = Lang::get('language.restr_action_msg');
                $actionname = "File";
                $actionDes = $this->docObj->stringReplace($actionname,$restrfname,Auth::user()->username,$actionMsg);
                $result = (new AuditsController)->backuplog(Auth::user()->username,'Backup/Restore', 'Restore',$actionDes);           
            }    
        }  
        echo $res;      
    }

    //remove uploaded files from dropzone
    public function removeDocument()
    {   
        if (Auth::user()) { 

            if(@Input::get('file')){
                // Delete
                $file = Input::get('file');
                $destinationPath = config('app.zip_backup_path'); // upload path
                unlink($destinationPath.$file);
                echo "removed";             
            }else{
                // Delete
                echo "File not found!"; 
            }
        }
        else{
            echo json_encode("Some issues in log file,contact admin");
            exit;
        }   
    }

    //delete all from list views
    public function deleteAll()
    {
        if (Auth::user()) 
        {
            $arr=Input::get('selected');
            foreach($arr as $val){
                $destinationPath = config('app.zip_backup_path'); // upload path
                unlink($destinationPath.$val);
                // Get update action message
                $actionMsg = Lang::get('language.delete_backup_action_msg');
                $actionname = "File";
                $actionDes = $this->docObj->stringReplace($actionname,$val,Auth::user()->username,$actionMsg);
                $result = (new AuditsController)->backuplog(Auth::user()->username,'Backup/Restore', 'Delete',$actionDes);
            }
            echo "success";
        }
        else 
        {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    //delete already uploaded sql & zip file
    public function DeleteZip(Request $request)
    {
        $data = array();
        if(Auth::user())
        {
            $filename = Input::get('filename');
            $fullpath = config('app.zip_backup_path')."{$filename}";
            //check file exist
            if($filename && file_exists($fullpath))
            {
                //delete file
                unlink($fullpath);
                // Get update action message
                $actionMsg = Lang::get('language.delete_backup_action_msg');
                $actionname = "File";
                $actionDes = $this->docObj->stringReplace($actionname,$filename,Auth::user()->username,$actionMsg);
                $result = (new AuditsController)->backuplog(Auth::user()->username,'Backup/Restore', 'Delete',$actionDes);
                echo "1";
                exit();
            }
            else
            {
                //file not found
                echo "0";
                exit();
            }
        }
    }
   

}/*<--END-->*/
