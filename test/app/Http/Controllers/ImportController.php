<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;
use View;
use DB;
use Response;
use Input;
use \stdClass;
use Session;
use Config;
use Lang;
use File;
use Storage;
use App\CsvDataModel as CsvData;
use App\Http\Requests\CsvImportRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Mylibs\Common;
use App\DocumentsCheckoutModel as DocumentsCheckoutModel;
use App\DocumentsColumnCheckoutModel as DocumentsColumnCheckoutModel;
use App\TempDocumentsModel as TempDocumentsModel;
use App\DocumentsModel as DocumentsModel;
use App\DocumentTypesModel as DocumentTypesModel;
use App\DocumentTypeColumnModel as DocumentTypeColumnModel;
use App\TempDocumentsColumnModel as TempDocumentsColumnModel;
use App\DepartmentsModel as DepartmentsModel;
use App\StacksModel as StacksModel;
use App\DocumentNoteModel as DocumentNoteModel;
use App\TempDocumentNoteModel as TempDocumentNoteModel;
use App\TagWordsCategoryModel as TagWordsCategoryModel;
use App\AuditsModel as AuditsModel;
use App\Users as Users;
use ZipArchive;
class ImportController extends Controller
{
    public function __construct()
    {

        $this->docObj      = new Common(); // class defined in app/mylibs

    }
    public function getDownloadSample()
    {
        
        $file= config('app.export_path').Config::get('constants.sample_file');
        
        $headers = array(
                  'Content-Type: text/csv; charset=utf-8'
                );

        return Response::download($file, Config::get('constants.sample_file'), $headers);
    }
    
    //download error file
    public function get_error()
    {
        $error_name = Session::get('export_error_filename');
        $file = config('app.import_path').Session::get('export_error_filename');
        $headers = array('Content-Type: text/csv; charset=utf-8');
        return Response::download($file, $error_name, $headers);
    }
    public function session_Type()
    {
        $sele_type=Input::get('type');
        Session::put('sess_selected_type',$sele_type);
        echo Session::get('sess_selected_type');
    }
    //common function for write to the file
    public function write_to_file($data,$out,$file)
    {
        foreach($data as $line)
        {
            // Write BOM character sequence to fix UTF-8 in Excel
            //fputs( $line, $bom = chr(0xEF) . chr(0xBB) . chr(0xBF) );
            fputcsv($out, $line);
        }
    }
    
    public function get_export()
    {    
        //Master data
        //unique files for diff users
        $datetime = date("Y-m-d_H-i-s");
        $export_master_name = Config::get('constants.master_file').$datetime;
        $export_master_name = str_replace("username",Auth::user()->username,$export_master_name);
        $destinationPath  = config('app.export_path'); // export path
        if(!file_exists($destinationPath))
        {
            //create directory export
            File::makeDirectory($destinationPath, $mode = 0777, true, true);
        }
        $file = config('app.export_path').$export_master_name.".csv";

        Session::put('export_master_filename',$file);
        Session::save();

        if(!file_exists($file))
        {
            //create csv file
            File::put($file,'');
        }
        
        $headers = array('Content-Type: text/plain');
        $out = fopen($file, 'w');

        if(Auth::user()->user_role != Session::get('user_role_super_admin') ){
            $data_departments = DepartmentsModel::select('department_name')->whereIn('department_id',Session::get('auth_user_dep_ids'))->get()->toArray();
        }else{
            $data_departments = DepartmentsModel::select('department_name')->get()->toArray();
        }

        
        $first_line=array(Lang::get('language.import_first_line'));
        fputcsv($out, $first_line);//write first line
        $second_line=array(Lang::get('language.import_second_line'));
        fputcsv($out, $second_line);//write second line
        $a=array('','');//line without data
        fputcsv($out, $a);//write
        $dept_title=array(Lang::get('language.import_dept_names'));
        fputcsv($out, $dept_title);
        fputcsv($out, $a);
        $this->write_to_file($data_departments,$out,$file);
        $data_stacks = StacksModel::select('stack_name')->get()->toArray();
        fputcsv($out, $a);
        $stack_title=array(Lang::get('language.import_stack_names'));
        fputcsv($out, $stack_title);
        fputcsv($out, $a);
        $this->write_to_file($data_stacks,$out,$file);
        $data_document_types = DocumentTypesModel::select('document_type_name')->where('is_app',0)->get()->toArray();
        fputcsv($out, $a);
        $type_title=array(Lang::get('language.import_document_types'));
        fputcsv($out, $type_title);
        fputcsv($out, $a);
        $this->write_to_file($data_document_types,$out,$file);
        fclose($out);
        //delete aftr download
        return Response::download($file,$export_master_name.'.csv', $headers)->deleteFileAfterSend(true);
    }
     public function importExcel()
    {
        Session::put('menuid', '16');
        // //delete duplicate
        $where = array('document_type_id'=>0);
                    
        $duplicate = TempDocumentsModel::where($where)->get();
        foreach ($duplicate as $key => $value) {
            DB::table('tbl_temp_documents_columns')->where('document_id',$value->document_id)->delete();
            DB::table('tbl_temp_documents')->where('document_id',$value->document_id)->delete();
        }


        //error file
        $error_file_name = Config::get('constants.error_file').Session::get('csv_import_filename');
        $error_file_name = str_replace("username",Auth::user()->username,$error_file_name);
        Session::put('export_error_filename',$error_file_name);
        $file_error = config('app.import_path').$error_file_name;

        if(!file_exists($file_error))
        {
            //create csv file
            //Storage::put($file_error,'');
            File::put($file_error,'');
        }
    
        $headercnt = Input::get('headercnt'); 
        $file= config('app.import_path').Session::get('csv_import_filename');
    
        //check file exist or not?
        $check=file_exists($file);
        if(!$check){
            return redirect('importFile')->with('err', Lang::get('language.choose_import'));
            //unlink file if exist
            // if (file_exists(config('app.import_path').Session::get('csv_import_filename'))) { unlink (config('app.import_path').Session::get('csv_import_filename')); }
            exit();
        }

        $heder_data = [];
        for($i=1;$i<=$headercnt;$i++){            
            if (in_array(Input::get('header'.$i.''), $heder_data, TRUE)){
                return redirect('importFile')->with('error', Lang::get('language.column_duplicate'));
                fclose ($file);
                // if (file_exists(config('app.import_path').Session::get('csv_import_filename'))) 
                //     { 
                //         unlink (config('app.import_path').Session::get('csv_import_filename')); 
                //     }
                exit();
            }
            $heder_data[] = Input::get('header'.$i.'');
        }

       
        //master data
        $fileName = config('app.import_path').Session::get('csv_import_filename');
        $stacks = DB::table('tbl_stacks')->select('stack_name','stack_id')->pluck('stack_id','stack_name');
        $department = DB::table('tbl_departments')->select('department_name','department_id')->pluck('department_id','department_name');

        //open file in read mode
        $file = fopen(config('app.import_path').Session::get('csv_import_filename'), "r");
        //document columns details of document_type
        $documents_columns = DB::table('tbl_document_types_columns')
        ->select('document_type_column_id','document_type_column_name','document_type_column_type','document_type_column_mandatory')
        ->where('document_type_id',Session::get('sess_selected_type'))
        ->orderBy('document_type_column_id','ASC')
        ->get();

        //getting document no (column name)
        $csv_header_res_no = DB::table('tbl_document_types')->select('tbl_document_types.document_type_column_no')->where('tbl_document_types.document_type_id',Session::get('sess_selected_type'))->get();
        foreach ($csv_header_res_no[0] as $value) {           
            $docno = $value;           
        }
        if($docno==''){
            $settings = DB::table('tbl_settings')->select('settings_document_no')->get();
            $docno   = $settings[0]->settings_document_no;
        }
        //getting document name (column name)
        $csv_header_res_name = DB::table('tbl_document_types')->select('tbl_document_types.document_type_column_name')->where('tbl_document_types.document_type_id',Session::get('sess_selected_type'))->get();
        foreach ($csv_header_res_name[0] as $value) {           
            $docname = $value;           
        }
        //getting document type name
        $csv_header_res_typename = DB::table('tbl_document_types')->select('tbl_document_types.document_type_name')->where('tbl_document_types.document_type_id',Session::get('sess_selected_type'))->get();
        foreach ($csv_header_res_typename[0] as $value3) {           
            $doctypename = $value3;           
        }

        if($docname==''){
            $settings = DB::table('tbl_settings')->select('settings_document_name')->get();
            $docname = $settings[0]->settings_document_name;
        }


        $affected_rows = 0;//count of efected rows
        $array = array();
        $loop = 0;
        $arr_dup_name = array();
        $incr = 0;
        //start iteration

        while (($data = fgetcsv($file, 10000, ",")) !== FALSE){
            if($incr>0){
                
                $arr_index = 0;
                $docname_index = '';
                $docno_index = '';
                for($i=1;$i<=$headercnt;$i++){
                    $header_data = Input::get('header'.$i.'');
                    if($header_data=="document_file_name"){
                        @$filename_index = $arr_index;
                    }
                    if($header_data=="dept_name"){
                        @$dept_index = $arr_index;
                    }
                    if($header_data=="stack_name"){
                        @$stack_index = $arr_index;
                    }
                    if($header_data==$docno){
                        @$docno_index = $arr_index;
                    }
                    if($header_data==$docname){
                        @$docname_index = $arr_index;
                    }
                    $arr_index++;
                }
                // if(empty($data[@$docname_index]) || empty($data[@$dept_index]))//if doc name and dept empty: fill file in correct format as shown in sample
                // {
                //     return redirect('importFile')->with('err', Lang::get('language.fill_as_sample'));
                //     fclose ($handle);
                //     // if (file_exists(config('app.import_path').Session::get('csv_import_filename'))) 
                //     //     { 
                //     //         unlink (config('app.import_path').Session::get('csv_import_filename')); 
                //     //     }
                //     exit();
                // }

                $arr_name = (isset($data[@$filename_index]))?$data[@$filename_index]:'';  //name array
                $document = TempDocumentsModel::where('document_file_name','=',$arr_name)->select('document_id')->first();

                if(!isset($document))
                {   
                    $arr_dup_name[] = array($arr_name);
                }

                if($arr_name && $document)
                {
                    $document_id = $document->document_id;
                    $update = array();
                    $arr_dept = (isset($data[@$dept_index]))?$data[@$dept_index]:'';
                    $department_names = explode(',',$arr_dept);
                    $temp_dept = array();
                    foreach ($department_names as $value) {
                        if(isset($department[$value]))
                        {
                            $temp_dept[] = $department[$value];
                        }
                    }
                    if($temp_dept){
                        $update['department_id'] = implode(',', $temp_dept);
                    }
                    $arr_stack = (isset($data[@$stack_index]))?$data[@$stack_index]:'';
                    $stack_names = explode(',',$arr_stack);
                    $temp_stack = array();
                    foreach ($stack_names as $value) {
                        if(isset($stacks[$value]))
                        {
                            $temp_stack[] = $stacks[$value];
                        }
                    }
                    if($temp_stack){
                        $update['stack_id'] = implode(',', $temp_stack);
                    }
                   if(isset($data[$docno_index])){
                        $update['document_no'] = (isset($data[@$docno_index]))?$data[@$docno_index]:'';
                    }

                    if(isset($data[$docname_index])){
                        $update['document_name'] = (isset($data[@$docname_index]))?$data[@$docname_index]:'';
                    }
                    //Temp documents updated
                    $affected = TempDocumentsModel::where('document_id','=',$document_id)->update($update);
                    

                    foreach ($documents_columns as $key => $columns) 
                    {   
                        //document columns details of document_type
                        $check_columns = DB::table('tbl_temp_documents_columns')
                        ->select('document_column_id')
                        ->where('document_type_column_id',$columns->document_type_column_id)
                        ->where('document_id',$document_id)
                        ->get();
                        $doccolcount = count($check_columns);
                        if($doccolcount==0){                            
                            DB::table('tbl_temp_documents_columns')->insert(['document_id'=>$document_id,'document_type_column_id'=>$columns->document_type_column_id,'document_column_name'=>$columns->document_type_column_name,'document_column_type'=>$columns->document_type_column_type,'document_column_mandatory'=>$columns->document_type_column_mandatory,'document_column_value'=>null]);
                            //audit update when the column missing 
                            $actionMsg = Lang::get('language.missing_column_msg');
                            $actionDes = $this->docObj->stringReplace($doctypename,$columns->document_type_column_name,$arr_name,$actionMsg);
                            $result = (new AuditsController)->log(Auth::user()->username, 'Documents', 'Import and Publish',$actionDes);
                      
                        }
                        $update_columns = array();
                        $arr_index = 0;
                        for($i=1;$i<=$headercnt;$i++){
                            $header_data = Input::get('header'.$i.'');
                            if($header_data==$columns->document_type_column_name){
                                //$filename_index = $arr_index;
                                $update_columns['document_column_value'] = (isset($data[$arr_index]))?$data[$arr_index]:'';
                                //Tempd document columns update
                                TempDocumentsColumnModel::where('document_id','=',$document_id)->where('document_column_name','=',$columns->document_type_column_name)->update($update_columns);
                            }
                            $arr_index++;
                        }
                    }
                    $update['document_file_name'] = $arr_name;
                    $loop++;
                    $array[] = $update;
                }
            }
            $incr++;
        }//while
        fclose ($file);
        if(isset($arr_dup_name))
        {
            //error file
            $file_err= config('app.import_path').Session::get('export_error_filename');
    
            $headers = array('Content-Type: text/plain',);
            $out = fopen($file_err, 'w');
            $err=array('List of mismatched files');
            fputcsv($out,$err);
            //blank row insert
            $a=array('','');
            fputcsv($out, $a);
            //print to csv
            $arr_duplicate_name=array_unique($arr_dup_name,SORT_REGULAR);
            //convert to column printing format
            foreach ($arr_dup_name as $key) {
               fputcsv($out, $key);
            }
            
            fputcsv($out, $a);
        }
        
        // print_r($arr_dup_name); 
        // exit();
        if(file_exists($fileName)){
           unlink($fileName);
        }
        if(count(@$arr_dup_name) >0)
        {
            return redirect('importFile')->with('error',($loop). Lang::get('language.row_effected_error'));
            fclose ($file);
            // if (file_exists(config('app.import_path').Session::get('csv_import_filename'))) { unlink (config('app.import_path').Session::get('csv_import_filename')); }
            exit();
        }
        return redirect('listview?view=import')->with('data',($loop).' rows have been affected.');
        exit();
    }

    public function processImport(Request $request)
    {
        $data = CsvData::find($request->csv_data_file_id);
        $csv_data = json_decode($data->csv_data, true);
        foreach ($csv_data as $row) {
            $contact = new Contact();
            foreach (config('app.db_fields') as $index => $field) {
                if ($data->csv_header) {
                    $contact->$field = $row[$request->fields[$field]];
                } else {
                    $contact->$field = $row[$request->fields[$index]];
                }
            }
            $contact->save();
        }
        return View::make('pages/documents/import_success')->with($data);
        //return view('import_success');
    }

    public function parseImport()
    {        
        if (Auth::user()) {
            $file = config('app.import_path').Session::get('csv_import_filename');
			//print_r($file);
            $check = file_exists($file);
			$check = (Session::get('csv_import_filename'))?$check:false;
            if($check == false)
            {
                echo $file;
                echo "file not exist";
                exit();
            }
            if(Session::get('sess_selected_type')){
                if($check==true){
                    $resdata = array_map('str_getcsv', file($file));
					/*print_r($resdata);
                     if($resdata[0][0]==""){
                        return redirect('importFile')->with('err', Lang::get('language.emptyfile'));
                    }else{  */
                        $jres = json_encode($resdata);
                        if (count($resdata) > 0) {                    
                            // $csv_header = array_slice($resdata, 0, 1);
                            $csv_header_fields = [];
                            $csv_header_res_no = [];
                            $csv_header_res_name = [];
                            $csv_header_res1 = [];

                            $doctype_name = DB::table('tbl_document_types')->select('tbl_document_types.document_type_name')->where('tbl_document_types.document_type_id',Session::get('sess_selected_type'))->get();
                        
                            $csv_header_res_no = DB::table('tbl_document_types')->select('tbl_document_types.document_type_column_no')->where('tbl_document_types.document_type_id',Session::get('sess_selected_type'))->get();
                           
                            $csv_header_res_name = DB::table('tbl_document_types')->select('tbl_document_types.document_type_column_name')->where('tbl_document_types.document_type_id',Session::get('sess_selected_type'))->get();

                            $csv_header_res1 = DB::table('tbl_document_types_columns')->select('tbl_document_types_columns.document_type_column_name')->where('tbl_document_types_columns.document_type_id',Session::get('sess_selected_type'))->get();

                            $settings_no = DB::table('tbl_settings')->select('settings_document_no')->get();
                            $settings_name = DB::table('tbl_settings')->select('settings_document_name')->get();
               
                            if(count($csv_header_res_no)>0){
                                if($csv_header_res_no[0]->document_type_column_no){
                                    foreach ($csv_header_res_no as $key => $value) {
                                        if($value->document_type_column_no){
                                            $csv_header_fields[] = $value;                        
                                        }
                                    }
                                }else{
                                    foreach ($settings_no as $key => $value) {
                                        $csv_header_fields[] = $value;
                                    }
                                }
                            }else{
                                foreach ($settings_no as $key => $value) {
                                    $csv_header_fields[] = $value;
                                }
                            }

                            if(count($csv_header_res_name)>0){
                                if($csv_header_res_name[0]->document_type_column_name){
                                    foreach ($csv_header_res_name as $key => $value) {
                                        if($value->document_type_column_name){
                                            $csv_header_fields[] = $value;                        
                                        }
                                    }
                                }else{
                                    foreach ($settings_name as $key => $value) {
                                        $csv_header_fields[] = $value;
                                    }
                                }
                            }else{
                                foreach ($settings_name as $key => $value) {
                                    $csv_header_fields[] = $value;
                                }
                            }

                            if(count($csv_header_res1)>0){
                                if($csv_header_res1[0]->document_type_column_name){
                                    foreach ($csv_header_res1 as $key => $value) {
                                        if($value->document_type_column_name){
                                            $csv_header_fields[] = $value;                        
                                        }
                                    }
                                }
                            }
                            $csv_data = array_slice($resdata, 0, 5);
                            $doctypeid = Session::get('sess_selected_type');
              
                            $result = CsvData::create([
                                'csv_data_filename' => Session::get('csv_import_filename'),
                                'document_type_id' => @$doctypeid,
                                'csv_data' => $jres
                            ]);
                            $lastInsertedId= $result->id;
                            $data['result'] = DB::table('tbl_csv_data')->where('csv_data_id',$lastInsertedId)->get();
                        } else {
                            return redirect()->back();
                        }
                    //}
                }
                Session::put('menuid', '16');
                Session::put('doctype_name', @$doctype_name[0]->document_type_name);
                //Session::forget('sess_selected_type');
                $data['doc_type_id'] = @$doctypeid;
                $data['csv_data'] = @$csv_data;
                $data['csv_header_fields'] = @$csv_header_fields;
     
                $data['stack']      = StacksModel::all();
                $data['tagsCateg']  = TagWordsCategoryModel::all();
                $data['docType']    = DocumentTypesModel::where('is_app',0)->get();

                $docObj = new Common(); // class defined in app/mylibs
                $data['stckApp'] = $docObj->common_stack();
                $data['deptApp'] = $docObj->common_dept();
                $data['doctypeApp'] = $docObj->common_type();
                $data['records'] = $docObj->common_records();
                return View::make('pages/documents/import_fields')->with($data);
            }else{
                return redirect('importFile')->with('err', Lang::get('language.doctype_null'));
            }
        } else {
            return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }


    public function importView()
    {
      if (Auth::user()) {
        $file= config('app.import_path').Session::get('csv_import_filename');
        $check=file_exists($file);
        // if($check==true){
        //     unlink(config('app.import_path').Session::get('csv_import_filename'));
        // }
                Session::put('menuid', '16');
                Session::forget('sess_selected_type');
                $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
                $data['settings_document_no']   = $settings[0]->settings_document_no;
                $data['settings_document_name'] = $settings[0]->settings_document_name;
     
                $data['stack']      = StacksModel::all();
                $data['tagsCateg']  = TagWordsCategoryModel::all();
                $data['docType']    = DocumentTypesModel::where('is_app',0)->get();

                $docObj = new Common(); // class defined in app/mylibs
                $data['stckApp'] = $docObj->common_stack();
                $data['deptApp'] = $docObj->common_dept();
                $data['doctypeApp'] = $docObj->common_type();
                $data['records'] = $docObj->common_records();
                return View::make('pages/documents/import')->with($data);
            } else {
                return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
            }
    }
    public function session_destroy_all()
    {
        Session::forget('export_owner_ids');
        Session::forget('export_departments');
        Session::forget('export_doctypeids');
        Session::forget('export_stackids');
        Session::forget('export_search_created_date_from');
        Session::forget('export_search_created_date_to');
        Session::forget('export_search_last_modified_from');
        Session::forget('export_search_last_modified_to');  
    }
    public function export_clear()
    {
        if(Auth::user())
        {
             $this->session_destroy_all();
        }
        else{
            return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }
    public function exportview()
    {   
        if (Auth::user()) {
            $this->session_destroy_all();
            $docObj = new Common(); // class defined in app/mylibs
                $data['stckApp'] = $docObj->common_stack();
                $data['deptApp'] = $docObj->common_dept();
                $data['doctypeApp'] = $docObj->common_type();
                $data['records'] = $docObj->common_records();
            Session::put('menuid', '17');
            $data['tagsCateg']       =      TagWordsCategoryModel::all();
            $data['stacks']          =      StacksModel::all();
            $data['docType']         =      DocumentTypesModel::where('is_app',0)->get();
            $data['tagCat']          =      TagWordsCategoryModel::all();
            $data['sess_doctypecol'] =      Session::get('sess_doctypecol');
            $user                    =      Users::select('username','id')->get();
            $doctypeid               =      Session::get('sess_settype');

            // Listing department according to the users department ids list
            if(Auth::user()->user_role == Session::get('user_role_super_admin')){
                $result = DepartmentsModel::select('department_name','department_id')->get();
            }else{
                $result = DepartmentsModel::select('department_name','department_id')->whereIn('department_id',Session::get('auth_user_dep_ids'))->get();
            }
            return View::make('pages/documents/export')->with($data)->with(['results'=>$result])->with(['users'=>$user]);   
        } else {
            return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }
    public function fetchdata(Request $request)
    {
        $query = DB::table('tbl_documents');
        // If ownership exists
        if(Input::get('ownership')):
            $query->whereIn('document_ownership',Input::get('ownership'));
        endif;
        // If department exists
        if(Input::get('department')):
            foreach(Input::get('department') as $depid):
            $query->WhereRaw('FIND_IN_SET('.$depid.',department_id)');
            endforeach;
        endif;
        // If document type id exists
        if(Input::get('doctypeid')):
            $query->WhereRaw('FIND_IN_SET('.Input::get('doctypeid').',document_type_id)');
        endif;
        // If statcks exists
        if(Input::get('stacks')):
            foreach(Input::get('stacks') as $stackid):
            $query->WhereRaw('FIND_IN_SET('.$stackid.',stack_id)');
            endforeach;
        endif;
        // If created_date_from exists 
        if(Input::get('created_date_from')):
            $query->where('created_at','>=',Input::get('created_date_from').' 00:00:00');
        endif;
        // If created_date_to exists 
        if(Input::get('created_date_to')):
            $query->where('created_at','<=',Input::get('created_date_to').' 23:59:59');
        endif;
        // If last_modified_from exists 
        if(Input::get('last_modified_from')):
            $query->where('updated_at','>=',Input::get('last_modified_from').' 00:00:00');
        endif;
        // If last_modified_to exists 
        if(Input::get('last_modified_to')):
            $query->where('updated_at','<=',Input::get('last_modified_to').' 23:59:59');
        endif;
        return $query;
    }
    //get count of records
    public function RecordscountImport(Request $request)
    { 
        $query = DB::table('tbl_temp_documents');
        // If document type id exists
        if(Input::get('doctypeid')){
            $query->WhereRaw('FIND_IN_SET('.Input::get('doctypeid').',document_type_id)');
        }
        $count_rec = $query->count();
        return $count_rec;
        exit();
    }
    public function importadvncdoc(Request $request)
    {  
            $start = Input::get('start');
            $chunkSize = Input::get('chunkSize');
            
            $query = DB::table('tbl_temp_documents')
            ->select('tbl_temp_documents.document_id',
                    'tbl_temp_documents.document_type_id',
                    'tbl_temp_documents.department_id',
                    'tbl_temp_documents.stack_id',
                    'tbl_temp_documents.document_no',
                    'tbl_temp_documents.document_file_name',
                    'tbl_temp_documents.document_name');

            // If document type id exists
            if(Input::get('doctypeid')):
                // echo Input::get('doctypeid');
                // exit();
                // Search by users department list
                if(Auth::user()->user_role != Session::get('user_role_super_admin')){
                    $count         = count(Session::get('auth_user_dep_ids'));
                    if($count == 1):
                        $x=0;
                    else:
                        $x=1;
                    endif;
                    
                    foreach(Session::get('auth_user_dep_ids') as $depid):
                       if($x == 1):
                            $query->WhereRaw('('.'FIND_IN_SET('.$depid.',tbl_temp_documents.department_id)');
                        elseif($x == $count):
                            $query->WhereRaw('FIND_IN_SET('.$depid.',tbl_temp_documents.department_id)'.')');
                        else:
                            $query->WhereRaw('FIND_IN_SET('.$depid.',tbl_temp_documents.department_id)');
                        endif;
                        $x++;
                    endforeach;
                }

                // search with document type
                $query->WhereRaw('FIND_IN_SET('.Input::get('doctypeid').',tbl_temp_documents.document_type_id)');
                $documentTypeNames = DB::table('tbl_document_types')->select('document_type_name')->where('document_type_id',Input::get('doctypeid'))->get();

            endif;
           
            // Run the query
            $query->groupBy('tbl_temp_documents.document_id');
            $data['dglist'] = $query->offset($start)->limit($chunkSize)->get();

            // print_r($data['dglist']);
            // exit();

            $file= config('app.export_path').Config::get('constants.sample_file');
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=file.csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            $index = DB::table('tbl_document_types')->select('document_type_column_no','document_type_column_name')->where('document_type_id', Input::get('doctypeid'))->first();

            //$index_next=DB::table('tbl_document_types_columns')->select('document_type_column_name')->where('document_type_id',Input::get('doctypeid'))->orderBy('document_type_column_id','ASC')->get();
            if(Session::get('settings_department_name')) { $deptname_label = Session::get('settings_department_name'); }else{ $deptname_label = @$language['departments']; }     
            $keys_title=array('File Name',$index->document_type_column_name,$index->document_type_column_no,$deptname_label,'Stack Name');

            $datas=DocumentTypeColumnModel::select('document_type_column_name')->where('document_type_id',Input::get('doctypeid'))->get()->toArray();
            $datatype=DocumentTypeColumnModel::select('document_type_column_type')->where('document_type_id',Input::get('doctypeid'))->get()->toArray();
            $type_mandatory=DocumentTypeColumnModel::select('document_type_column_mandatory')->where('document_type_id',Input::get('doctypeid'))->get()->toArray();
            $names = array_column($datas, 'document_type_column_name');
            $types = array_column($datatype, 'document_type_column_type');
            $mandatory=array_column($type_mandatory, "document_type_column_mandatory");
            foreach ($mandatory as $key => $value) {
                if($value==0){
                    $a[]='';
                }
                else if($value==1)
                {
                    $a[]=' *';
                }
            }
            //$x[] = '';
            for($i=0;$i<count($names);$i++){
                if($types[$i]=='date'||$types[$i]=='Date'){
                    $types[$i]="Date [YYYY-MM-DD]";
                }       
                $x[] = $names[$i]. ' ('.$types[$i].')'.$a[$i];   //concatenate doc_type_name and doc_type
            }
            $keys_title = array_merge($keys_title,$x);


            // foreach ($index_next as $value) 
            // {
            //     array_push($keys_title, $value->document_type_column_name);
            // }

                if($start==0) 
                {
                    $out = fopen($file, 'w');
                    fputcsv($out, $keys_title);
                }
                else
                {
                    $out = fopen($file, 'a');
                    // echo $file;
                    // exit();
                }
            $documentTypes = DB::table('tbl_document_types')->select('document_type_id','document_type_name')->pluck('document_type_name','document_type_id');
            
            $stacks = DB::table('tbl_stacks')->select('stack_name','stack_id')->pluck('stack_name','stack_id');
            
            $department = DB::table('tbl_departments')->select('department_name','department_id')->pluck('department_name','department_id');

            foreach($data['dglist'] as $val):

                $row = array();
                $row[] = $val->document_file_name;
                $row[] = $val->document_name;
                $row[] = $val->document_no;
                //dept id to dept names             
                $department_id = explode(',',$val->department_id);
                $temp_dept = array();
                foreach ($department_id as $value) {
                    if(isset($department[$value]))
                    {
                        $temp_dept[] = $department[$value];
                    }
                }
                $row[] = implode(',', $temp_dept);
                //type id to type names
                /*$document_type_id = explode(',',$val->document_type_id);
                $temp_type = array();
                foreach ($document_type_id as $value) {
                    if(isset($documentTypes[$value]))
                    {
                        $temp_type[] = $documentTypes[$value];
                    }
                }
                $row[] = implode(',', $temp_type);*/
                //satck id to stack names
                $stack_id = explode(',',$val->stack_id);
                $temp_stack = array();
                foreach ($stack_id as $value) {
                    if(isset($stacks[$value]))
                    {
                        $temp_stack[] = $stacks[$value];
                    }
                }
                $row[] = implode(',', $temp_stack);

                $document_type_columns = DB::table('tbl_temp_documents_columns')
                ->leftJoin('tbl_document_types_columns',
                    'tbl_document_types_columns.document_type_column_id','=','tbl_temp_documents_columns.document_type_column_id')
                ->select('tbl_temp_documents_columns.document_column_name',
                        'tbl_temp_documents_columns.document_column_value')
                ->where('tbl_temp_documents_columns.document_id',$val->document_id)
                ->where('tbl_document_types_columns.document_type_id',$val->document_type_id)
                ->orderBy('tbl_document_types_columns.document_type_column_id','ASC')
                ->get();
                //column names
                foreach ($document_type_columns as $key => $value) {
                    $row[] = $value->document_column_value;
                    
                }
            //write csv
            fputcsv($out, $row);
            endforeach;
            fclose($out);
                    
                    $response = array(
                        'result'        => $data['dglist'],
                        'start'         => $start,
                        'limit'         => $chunkSize
                    );
                    echo json_encode($response);
                    exit();
    }
    //get count of records
    public function RecordscountExport(Request $request)
    { 
        $query = $this->fetchdata($request);
        $count_rec = $query->count();
        return $count_rec;
        exit();
    }
    //get count of records
    public function RecordscountExportData(Request $request)
    { 
        $encrypted = 0;
        $query = $this->fetchdata($request);
        $count_rec = $query->count();
        $data = $query->get();
        foreach ($data as $key => $value) 
            {
                if($value->document_encrypt_status == 1)
                {
                    $encrypted = 1;
                }
            }
        $response = array(
                        'count'        => $count_rec,
                        'encrypt'      => $encrypted
                    );
        echo json_encode($response);
        exit();
    }
    //Advance search document submit 
    public function exportadvncdoc(Request $request)
    {  
            $start = Input::get('start');
            $chunkSize = Input::get('chunkSize');
            $selectedText = Input::get('selectedText');
            $selectedText_change = preg_replace('/\s+/', '', $selectedText);
            $query = DB::table('tbl_documents')
            ->select('tbl_documents.document_id',
                    'tbl_documents.document_type_id',
                    'tbl_documents.department_id',
                    'tbl_documents.stack_id',
                    'tbl_documents.document_no',
                    'tbl_documents.document_file_name',
                    'tbl_documents.document_name');
            //->where('tbl_documents.document_type_id',Input::get('doctypeid'));
            
            // If ownership exists
            if(Input::get('ownership')):

                // Search by users department list
                if(Auth::user()->user_role != Session::get('user_role_super_admin')){
                    $count         = count(Session::get('auth_user_dep_ids'));
                    if($count == 1):
                        $x=0;
                    else:
                        $x=1;
                    endif;
                    
                    foreach(Session::get('auth_user_dep_ids') as $depid):
                       if($x == 1):
                            $query->WhereRaw('('.'FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        elseif($x == $count):
                            $query->WhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)'.')');
                        else:
                            $query->WhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        endif;
                        $x++;
                    endforeach;

                    $query->whereIn('tbl_documents.document_ownership',Input::get('ownership'));

                }else{
                    $query->WhereIn('tbl_documents.document_ownership',Input::get('ownership'));
                }

                
                foreach(Input::get('ownership') as $owe):
                    $oweIds = DB::table('tbl_users')->select('id')->where('username',$owe)->get();
                    $ownerIds[] = $oweIds['0']->id;
                    $owes[]  = $owe;
                    endforeach;
                Session::put('search_ownership',implode(', ',$owes));
                Session::put('owner_ids',$ownerIds);
            endif;

            // If department exists
            if(Input::get('department')):

                foreach(Input::get('department') as $depid):
                    $query->WhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                    $departments[] = DB::table('tbl_departments')->select('department_name')->where('department_id',$depid)->get();
                    endforeach;
            endif;

            // If document type id exists
            if(Input::get('doctypeid')):
                // echo Input::get('doctypeid');
                // exit();
                // Search by users department list
                if(Auth::user()->user_role != Session::get('user_role_super_admin')){
                    $count         = count(Session::get('auth_user_dep_ids'));
                    if($count == 1):
                        $x=0;
                    else:
                        $x=1;
                    endif;
                    
                    foreach(Session::get('auth_user_dep_ids') as $depid):
                       if($x == 1):
                            $query->WhereRaw('('.'FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        elseif($x == $count):
                            $query->WhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)'.')');
                        else:
                            $query->WhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        endif;
                        $x++;
                    endforeach;
                }

                // search with document type
                $query->WhereRaw('FIND_IN_SET('.Input::get('doctypeid').',tbl_documents.document_type_id)');
                $documentTypeNames = DB::table('tbl_document_types')->select('document_type_name')->where('document_type_id',Input::get('doctypeid'))->get();

                // $data = $query->get();
                // echo '<pre>';
                // print_r($data);
                // exit();

            endif;
           
            // If statcks exists
            if(Input::get('stacks')):

                if(Auth::user()->user_role != Session::get('user_role_super_admin')){
                    $count         = count(Session::get('auth_user_dep_ids'));
                    if($count == 1):
                        $x=0;
                    else:
                        $x=1;
                    endif;
                    
                    foreach(Session::get('auth_user_dep_ids') as $depid):
                       if($x == 1):
                            $query->WhereRaw('('.'FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        elseif($x == $count):
                            $query->WhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)'.')');
                        else:
                            $query->WhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        endif;
                        $x++;
                    endforeach;

                    $stackCount = count(Input::get('stacks'));
                    if($stackCount == 1):
                        $y=0;
                    else:
                        $y=1;
                    endif;
                
                    foreach(Input::get('stacks') as $satckIds):
                        if($y == 1):
                            $query->whereRaw('('.'FIND_IN_SET('.$satckIds.',tbl_documents.stack_id)');
                        elseif($y == $stackCount):
                            $query->WhereRaw('FIND_IN_SET('.$satckIds.',tbl_documents.stack_id)'.')');
                        else:
                            $query->whereRaw('FIND_IN_SET('.$satckIds.',tbl_documents.stack_id)');
                        endif;
                        $stackNames[] = DB::table('tbl_stacks')->select('stack_name')->where('stack_id',$satckIds)->get();
                        $y++;
                    endforeach;

                }else{
                    foreach(Input::get('stacks') as $satckIds):
                    $query->WhereRaw('FIND_IN_SET('.$satckIds.',tbl_documents.stack_id)');
                    $stackNames[] = DB::table('tbl_stacks')->select('stack_name')->where('stack_id',$satckIds)->get();
                    endforeach;
                }
  
            endif;
            
            // If created_date_from exists 
            if(Input::get('created_date_from')):

                // Search by users department list
                if(Auth::user()->user_role != Session::get('user_role_super_admin')){
                $this->repeat_section();

                    $query->where('tbl_documents.created_at','>=',Input::get('created_date_from').' 00:00:00');
                }else{
                    $query->Where('tbl_documents.created_at','>=',Input::get('created_date_from').' 00:00:00');
                }

            endif;

            // If created_date_to exists 
            if(Input::get('created_date_to')):

                // Search by users department list
                if(Auth::user()->user_role != Session::get('user_role_super_admin')){
                $this->repeat_section();

                    $query->where('tbl_documents.created_at','<=',Input::get('created_date_to').' 23:59:59');
                }else{
                    $query->where('tbl_documents.created_at','<=',Input::get('created_date_to').' 23:59:59');
                }
            endif;

            // If last_modified_from exists 
            if(Input::get('last_modified_from')):

                // Search by users department list
                if(Auth::user()->user_role != Session::get('user_role_super_admin')){
                $this->repeat_section();

                    $query->where('tbl_documents.updated_at','>=',Input::get('last_modified_from').' 00:00:00');
                }else{
                    $query->where('tbl_documents.updated_at','>=',Input::get('last_modified_from').' 00:00:00');
                }
            endif;

            // If last_modified_to exists 
            if(Input::get('last_modified_to')):

                // Search by users department list
                if(Auth::user()->user_role != Session::get('user_role_super_admin')){
                $this->repeat_section();

                    $query->where('tbl_documents.updated_at','<=',Input::get('last_modified_to').' 23:59:59');
                }else{
                    $query->where('tbl_documents.updated_at','<=',Input::get('last_modified_to').' 23:59:59');
                }
            endif;

            // Run the query
            $query->groupBy('tbl_documents.document_id');
            $data['dglist'] = $query->offset($start)->limit($chunkSize)->orderBy('tbl_documents.document_id','ASC')->get();

            // print_r($data['dglist']);
            // exit();

            $datetime = Input::get('dateTime');
            $export_csv_name = Config::get('constants.export_file').$selectedText_change.'_'.$datetime.".csv";
            $export_csv_name = str_replace("username",Auth::user()->username,$export_csv_name);
            $file = config('app.export_path').$export_csv_name;
            Session::put('export_csv_filename',$file);

            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=file.csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            $index = DB::table('tbl_document_types')->select('document_type_column_no','document_type_column_name')->where('document_type_id', Input::get('doctypeid'))->where('is_app',0)->first();

            $index_next=DB::table('tbl_document_types_columns')->select('document_type_column_name')->where('document_type_id',Input::get('doctypeid'))->orderBy('document_type_column_id','ASC')->get();
            if(Session::get('settings_department_name')) { $deptname_label = Session::get('settings_department_name'); }else{ $deptname_label = @$language['departments']; }     
            $keys_title=array('File Name',$index->document_type_column_name,$index->document_type_column_no,$deptname_label,'Stack Name');
            foreach ($index_next as $value) 
            {
                array_push($keys_title, $value->document_type_column_name);
            }

                if($start==0) 
                {
                    $out = fopen($file, 'w');
                    fputcsv($out, $keys_title);
                }
                else
                {
                    $out = fopen($file, 'a');
                }
            $documentTypes = DB::table('tbl_document_types')->select('document_type_id','document_type_name')->pluck('document_type_name','document_type_id');
            
            $stacks = DB::table('tbl_stacks')->select('stack_name','stack_id')->pluck('stack_name','stack_id');
            
            $department = DB::table('tbl_departments')->select('department_name','department_id')->pluck('department_name','department_id');
            $test = array();
            foreach($data['dglist'] as $val):

                $row = array();
                
                $row[] = $val->document_file_name;
                $row[] = $val->document_name;
                $row[] = $val->document_no;
                //dept id to dept names             
                $department_id = explode(',',$val->department_id);
                $temp_dept = array();
                foreach ($department_id as $value) {
                    if(isset($department[$value]))
                    {
                        $temp_dept[] = $department[$value];
                    }
                }
                $row[] = implode(',', $temp_dept);
                //type id to type names
                /*$document_type_id = explode(',',$val->document_type_id);
                $temp_type = array();
                foreach ($document_type_id as $value) {
                    if(isset($documentTypes[$value]))
                    {
                        $temp_type[] = $documentTypes[$value];
                    }
                }
                $row[] = implode(',', $temp_type);*/
                //satck id to stack names
                $stack_id = explode(',',$val->stack_id);
                $temp_stack = array();
                foreach ($stack_id as $value) {
                    if(isset($stacks[$value]))
                    {
                        $temp_stack[] = $stacks[$value];
                    }
                }
                $row[] = implode(',', $temp_stack);

                $document_type_columns = DB::table('tbl_documents_columns')
                ->leftJoin('tbl_document_types_columns',
                    'tbl_document_types_columns.document_type_column_id','=','tbl_documents_columns.document_type_column_id')
                ->select('tbl_documents_columns.document_column_name',
                        'tbl_documents_columns.document_column_value')
                ->where('tbl_documents_columns.document_id',$val->document_id)
                ->where('tbl_document_types_columns.document_type_id',$val->document_type_id)
                ->orderBy('tbl_document_types_columns.document_type_column_id','ASC')
                ->get();
                //column names
                foreach ($document_type_columns as $key => $value) {
                    $row[] = $value->document_column_value;
                    
                }
                //write csv
                fputcsv($out, $row);
            endforeach;
            fclose($out);
                    
                    $response = array(
                        'result'        => $data['dglist'],
                        'start'         => $start,
                        'limit'         => $chunkSize,
                        'filename'      => $export_csv_name
                    );
                    echo json_encode($response);
                    exit();
    }
    public function repeat_section()
    {
        $count         = count(Session::get('auth_user_dep_ids'));
        if($count == 1):
            $x=0;
        else:
            $x=1;
        endif;
        
        foreach(Session::get('auth_user_dep_ids') as $depid):
           if($x == 1):
                $query->orWhereRaw('('.'FIND_IN_SET('.$depid.',tbl_documents.department_id)');
            elseif($x == $count):
                $query->orWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)'.')');
            else:
                $query->orWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)');
            endif;
            $x++;
        endforeach;
    }
    public function getTotalRecords(Request $request)
    {
        $query = $this->fetchdata($request);
        $data = $query->count();
        return $data;
    }
    public function downloadDataZip(Request $request)
    {
        $start = Input::get('start');
        $chunkSize = Input::get('chunkSize');
        //add the document type with the filename
        $selectedText = Input::get('selectedText');
        $selectedText_change = preg_replace('/\s+/', '', $selectedText);
        //add datetime with the filename
        $datetime = Input::get('dateTime');

        $query = $this->fetchdata($request);
        
        $data = $query->offset($start)->limit($chunkSize)->get();
        $files = array();
        
        if($data)
        {
            foreach ($data as $key => $value) 
            {
                if($value->document_encrypt_status == 1)
                {
                    $encrypted = 1;
                }
                array_push($files, $value->document_file_name);
            }
        
            
            $export_zip_name = Config::get('constants.export_zip').$selectedText_change.'_'.$datetime.".zip";
            $export_zip_name = str_replace("username",Auth::user()->username,$export_zip_name);
            $filename = config('app.export_path').$export_zip_name;
            Session::put('export_zip_filename',$filename);
            Session::save();

            //delete zip file in first case only for avoid previous results
            if($start == 0)
            {
                if(file_exists($filename))
                {
                    //Delete already existing
                    unlink($filename);
                }
            }
            $zip = new ZipArchive;
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
            }

            $zip->close();
            $response = array(
                        'result'        => $files,
                        'start'         => $start,
                        'limit'         => $chunkSize,
                        'filename'      => $export_zip_name
                    );
                    echo json_encode($response);
                    exit();
        }
        else
        {//no files
            return 0;
        }
    }
    public function NumberZip()
    {
        $requestData= $_REQUEST;
        // list all filenames in given path
        $length       =   Input::get("length");
        $start        =   1+Input::get("start");
        $user_name    =   Auth::user()->username;
        $end = ($start)?($start+$length)-1:$length;
        $dir = config('app.export_path');
        $row_file = array();
        $noOfRecords=0;

            if(is_dir($dir))
            {
                //get files of particular user
                $myarray = glob($dir."fileeazy_".$user_name."_export_*");
                //descending order by date
                $callback = function($a,$b) {
                    return filemtime($b) - filemtime($a);
                };

                usort($myarray, $callback);
                foreach($myarray as $file) 
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
                        if(($noOfRecords >= $start) && ($noOfRecords <= $end))
                        {
                            $row_file[] = array(

                            'filename' => $file,
                            'size' => $bytes,
                            'date' => date ("Y-m-d H:i:s", filemtime($dir.$file)),
                            'actions' => '<a href='.URL('/ExportZip').'/'.$file.' title="Download"><i class="fa fa-fw fa-download" id='.$file.'></i></a>&nbsp;&nbsp;<i class="fa fa-fw fa-trash" title="Delete" onclick="del(\''.$file.'\')" style="color: red; cursor:pointer;" id='.$file.'></i>'
                            ); 
                        }
                }
            }
            //exit();
        $data['data'] = $row_file;
        //For ajax result
        $data['draw'] = intval( $requestData['draw'] );
        $data['recordsTotal'] = $noOfRecords;
        $data['recordsFiltered'] = $noOfRecords;
        $data['start'] = $start;
        $data['length'] = $length;
        $data['end'] = $end;
        //$data['request'] = $requestData['order'][0]['dir'];
        echo json_encode($data);
    }
}
