<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request as CookieRequest;
use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;
use Input;
use URL;
use Session;
use Config;
use DB;
use Lang;
use App\AjaxModel as AjaxModel;
class AjaxController extends Controller
{
    public function __construct()
    {
        

    }
    
   
     //list view united
    public function dtview()
    { 
        include (public_path()."/storage/includes/lang1.en.php" );
        if (Auth::user()) 
        {
            $length         =     Input::get("length");
            $start          =     Input::get("start");
            $view           =     Input::get('view');//get different view
            $doclistid      =     Input::get('type'); //current selected type id
            $filter         =     Input::get('filter'); //filter
            $curr_id        =     Input::get('id'); //id selected from side bar(stack,dept,type)
            

            $order = (isset($_POST['order'][0]['column']))?$_POST['order'][0]['column']:1;
            $direct = (isset($_POST['order'][0]['dir']))?$_POST['order'][0]['dir']:'DESC';

            $qryorder = (isset($_POST['columns'][$order]['data']))?$_POST['columns'][$order]['data']:'';
            
            /*$search       =   (isset($_POST['search']['value']))?trim($_POST['search']['value']):'';*/
            $search        =     Input::get('search_text')?trim(Input::get('search_text')):'';  
            $search_column  = (isset($_POST['search_column_data']['search_column']))?trim($_POST['search_column_data']['search_column']):'';
            Session::put('serach_length',$length);
            Session::put('serach_start',$start);
            Session::put('serach_doc_type',$doclistid);
            Session::put('serach_filter',$filter);
            Session::put('serach_view',$view);
            Session::put('search_text',$search);
            Session::put('search_column',$search_column);
            Session::put('serach_order',$order);
            Session::put('serach_direct',$direct);

            if($curr_id)
            {
                Session::put('dsd_id',$curr_id);
                Session::put('serach_id',$curr_id);

            }
            
            $currentPage = ($start)?($start/$length)+1:1;


          \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
          });
            
            
            $loggedUsersdepIds = explode(',',Auth::user()->department_id);
            $docid = Input::get('docid')?Input::get('docid'):0;
            $data = array();
            DB::enableQueryLog();
            switch($view)
            {
                case Lang::get('language.list_view')://list view
                case Lang::get('language.document_type_view'): //type
                case Lang::get('language.stack_view')://stack
                case Lang::get('language.recent_document') : //recent document
                case Lang::get('language.department_view')://dept wise
                    $table = 'tbl_documents';
                    $tbl_documents_columns = 'tbl_documents_columns';
                   $data = AjaxModel::ajax_filter($table,$tbl_documents_columns,$doclistid,$curr_id,$loggedUsersdepIds,$view,$filter,$docid,$search,$search_column,$length);
                break; 
                case Lang::get('language.import_view'): //import view
                    $table = 'tbl_temp_documents';
                    $tbl_documents_columns = 'tbl_temp_documents_columns';
                   $data = AjaxModel::ajax_filter($table,$tbl_documents_columns,$doclistid,$curr_id,$loggedUsersdepIds,$view,$filter,$docid,$search,$search_column,$length);
                break;
                case Lang::get('language.checkout_view'): //checkout view
                    $table = 'tbl_documents_checkout';
                    $tbl_documents_columns = 'tbl_documents_columns_checkout';
                   $data = AjaxModel::ajax_filter($table,$tbl_documents_columns,$doclistid,$curr_id,$loggedUsersdepIds,$view,$filter,$docid,$search,$search_column,$length);
                break;
            }
            $queries = DB::getQueryLog();  
            $count_all = ($data['dglist'])?$data['dglist']->total():0;
            $i = $start;
            $data_table = array();
            $current_view =Input::get('view');
            $user_permission=Auth::user()->user_permission;

            

            foreach ($data['dglist'] as $value) {

            $ext = pathinfo($value->document_file_name, PATHINFO_EXTENSION);
            $ext = strtolower($ext);
            $i++;
            $row_d = array();

            $action =''; 

            if(($current_view == Lang::get('language.list_view')) || ($current_view == Lang::get('language.stack_view')) || ($current_view == Lang::get('language.document_type_view')) || ($current_view == Lang::get('language.department_view'))
                || ($current_view == Lang::get('language.recent_document')))
            {
                if($value->document_status!='Checkout')
                {
                    $action.='<input name="checkbox[]" type="checkbox" value="'.$value->document_id.'" id="chk'.$value->document_id.'" class="checkBoxClass">';
                }
                else
                {
                    $action.='<input name="checkbox_disabled[]" type="checkbox" onclick="return swal(You have no permission)" value="'.$value->document_id.'" disabled=true>';
                }
            }
            else
            {
            $action.='<input name="checkbox[]" type="checkbox" value="'.$value->document_id.'" id="chk'.$value->document_id.'" class="checkBoxClass">';
            }
                $action .='&nbsp;';
                $action.='<div class="btn-group">
                      <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" title="Actions">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                      </button>
                      <ul class="dropdown-menu" role="menu" id="icons-list">
                    
                  ';
                $action .='&nbsp;';
                $action .='&nbsp;';
                 if($current_view == Lang::get('language.recent_document')) {
                    $action.='<a title="Open Document" href="documentManagementView?dcno='.$value->document_id.'&page='.Lang::get('language.list_view').'">';
                }
                else{
                    $action.='<a title="Open Document" href="documentManagementView?dcno='.$value->document_id.'&page='.$current_view.'&id='.$curr_id.'">';
                }
                    
                    
                    if($ext=="pdf"){
                        $action.='<i class="fa fa-file-pdf-o"></i>';
                    }
                    elseif ($ext=='png'||$ext=='jpg'||$ext=='jpeg'||$ext=='tiff'||$ext=='tif'||$ext=='TIFF'||$ext=='TIF'||$ext=='JPG'||$ext=='JPEG'||$ext=='gif') {
                        $action.='<i class="fa fa-file-image-o"></i>';
                    }
                    elseif ($ext=='docx'||$ext=='doc') {
                        $action.='<i class="fa fa-file-word-o"></i>';
                    }
                    elseif ($ext=='txt') {
                        $action.='<i class="fa fa-file-text-o"></i>';
                    }
                    elseif ($ext=='zip'||$ext=='rar') {
                        $action.='<i class="fa fa-file-archive-o"></i>';
                    }
                    elseif ($ext=='xls'||$ext=='xlsx') {
                        $action.='<i class="fa fa-file-excel-o"></i>';
                    }
                    elseif ($ext=='wav'||$ext=='mp3'||$ext=='ogg') {
                        $action.='<i class="icon fa fa-file-sound-o"></i>';
                    }
                    elseif ($ext=='flv'||$ext=='mp4'||$ext=='ogv'||$ext=='webm') {
                        $action.='<i class="icon fa fa-file-video-o"></i>';
                    }
                    elseif ($ext=='dwg') {
                        $action.='<i class="icon fa fa-clipboard"></i>';
                    }
                    else{
                        $action.='<i class="fa fa-file-o"></i>';
                    }
                $action .='</a>';
                
                switch($current_view)
                {
                    //import
                    case Lang::get('language.import_view'):
                        $action .='&nbsp; &nbsp;';
                        $action.='<a href="documentEdit?id='.$value->document_id.'" id="faPencil'.$value->document_id.'" title="Edit">
                        <i class="fa fa-pencil" style="cursor:pointer;"></i>
                        </a>';
                        $action .='&nbsp; &nbsp;';
                        
                        $action .='<i class="fa fa-trash delDoc" id="faClose'.$value->document_id.'"  style="color: red; cursor:pointer;" title="Delete" data-docid="'.$value->document_id.'" data-docname="'.$value->document_name.'" data-filename="'.$value->document_file_name.'"></i>';
                        $action .='&nbsp; &nbsp;'; 
                        
                            $action .='<a count="'.$value->document_id.'" id="moredet" data-toggle="modal"  style="cursor:pointer; padding-left:2px; padding-right:2px;" data-target="#viewmoreModal" title="More Details" ><i class="fa fa-ellipsis-v" aria-hidden="true"></i></a>
                            ';
                        
                    
                    break;
                    //checkout
                    case Lang::get('language.checkout_view'):
                
                        if($value->document_pre_status=="Draft"){
                        $action .='&nbsp; &nbsp;';

                        $action.='<a onclick="discard_draft(\''.$value->document_id.'\',\''.$value->document_name.'\')" title="Discard Check Out" style="cursor:pointer;"><i class="fa fa-sign-out"></i></a>
                        ';
                        }
                        else
                        {
                        $action .='&nbsp; &nbsp;';
                        $action.='<a onclick="discard_published(\''.$value->document_id.'\',\''.$value->document_name.'\')" title="Discard Check Out" style="cursor:pointer;"><i class="fa fa-sign-out"></i></a>
                        ';
                        }
                        if(stristr($user_permission,"checkout")){ 
                        $action .='&nbsp; &nbsp;';
                        $downloadurl = URL('file/documents/'.$value->document_file_name).'?download='.time();                    
                        $action.='<a href="'.$downloadurl.'" title="Download"><i class="fa fa-download"></i></a>'; 
                        }
                        $action .='&nbsp; &nbsp;';
                        $action .='<a href="editcheckoutDocument?id='.$value->document_id.'&view=checkout" id="faPencil'.$value->document_id.'"><i class="fa fa-mail-reply" style="cursor:pointer;" title="Check In"></i></a>';
                        $action .='&nbsp; &nbsp;';
                        $action .='<a count="'.$value->document_id.'" id="moredet" data-toggle="modal"  style="cursor:pointer; padding-left:2px; padding-right:2px;" data-target="#viewmoreModal" title="More Details" ><i class="fa fa-ellipsis-v" aria-hidden="true"></i></a>';
                        
                    break;
                    //list
                    case Lang::get('language.list_view'):
                    case Lang::get('language.document_type_view'): //type
                    case Lang::get('language.stack_view')://stack
                    case Lang::get('language.department_view')://dept wise
                    case Lang::get('language.recent_document')://dept wise
                    $action .='&nbsp;';
                    $action.='
                    <!-- History -->';
                    $action.='<a title="History" href="documentHistory/'.$value->document_id.'"><i class="fa fa-history" ></i></a>';
                    $action.='
                    <!-- Check Out Document -->';
                        if(stristr($user_permission,"checkout")){
                        if($value->document_status =='Published' || $value->document_status =='Rejected')
                        {
                            if(@$value->document_encrypt_status == 1){
                            $action .='&nbsp;';
                            $action.='<a href="javascript:void(0);" title="Check Out" onclick="return swal(\''.ucfirst($value->document_name).' is curently encrypted by '.ucfirst($value->document_encrypted_by).' It must be decrypt first before you can perform this operation'.'\')"><i class="fa fa-share" ></i></a>';
                            }
                            else{
                            $action .='&nbsp;';
                            $action.='<a href="javascript:void(0);"  title="Check Out" onclick="return myFunction(\''.$value->document_name.'\',\''.$value->document_file_name.'\',\''.$value->document_id.'\',\''.$i.'\')"><i class="fa fa-share"></i></a>';
                            }
                        }
                        elseif($value->document_status =='Checkout'){
                        $action .='&nbsp;';
                        $action.='<a href="javascript:void(0);" title="Check Out" onclick="return swal(\''.ucfirst($value->document_name).' is curently Checked Out by '.ucfirst($value->document_modified_by).' It must be Checked In first before you can perform this operation'.'\')"><i class="fa fa-share" ></i></a>';
                        }
                        elseif($value->document_status =='Review'){
                        $action .='&nbsp;';
                        $action.='<a href="javascript:void(0);" title="Check Out" onclick="return swal(\''.ucfirst($value->document_name).' is currently under review'.'\')"><i class="fa fa-share" ></i></a>';
                        }
                        }
                    $action.='
                    <!-- Delete -->';
                        if(stristr($user_permission,"delete")){
                            if($value->document_status!='Checkout'){
                                $action .='&nbsp;';
                                 $action.='<i title="Delete" class="fa fa-trash delDoc"  style="color: red; cursor:pointer;" data-docid="'.$value->document_id.'" data-docname="'.$value->document_name.'" data-filename="'.$value->document_file_name.'"></i>';
                            }
                            else{
                                $action .='&nbsp;';
                                 $action.='<i title="Delete" class="fa fa-trash" onclick="return swal(\''.ucfirst($value->document_name).' is curently Checked Out by '.ucfirst($value->document_modified_by).'. It must be Checked In first before you can perform this operation.'.'\')" style="color: red; cursor:pointer;"></i>';
                            }
                            
                        }
                    $action.='
                    <!-- Related document -->';
                        $action .='&nbsp;';
                        $action.='<a title="Related documents" href="relatedsearch/'.$value->document_id.'"><i class="fa fa-files-o" ></i></a>';
                   
                   
                    
                    if (Session::get('module_activation_key6')==1){
                        if(date("Y-m-d") > Session::get('module_expiry_date6')){ 
                        }else{
                            $action .='&nbsp; &nbsp;';
                            if(@$value->document_encrypt_status == 0 && @$value->document_status !='Checkout')
                            {
                                $action.='
                                <!-- encryption -->';
                                $action.='<a count="'.$value->document_id.'" doc_name="'.$value->document_name.'" doc_file_name="'.$value->document_file_name.'" title="Encrypt File" id="encrypt_doc" style="cursor:pointer;"><i class="fa fa-lock" ></i></a>';
                            }
                            elseif(@$value->document_encrypt_status == 0 && @$value->document_status =='Checkout')
                            {
                                $action.='
                                <!-- encryption -->';
                                $action.='<a title="Encrypt File" onclick="return swal(\''.ucfirst($value->document_name).' is curently Checked Out by '.ucfirst($value->document_modified_by).'. It must be Checked In first before you can perform this operation.'.'\')" style="cursor:pointer;"><i class="fa fa-lock" ></i></a>';
                            }
                            else
                            {
                                $action.='
                                <!-- decryption -->';
                                $action.='<a count="'.$value->document_id.'" doc_name="'.$value->document_name.'" doc_file_name="'.$value->document_file_name.'" title="Decrypt File" id="decrypt_doc" style="cursor:pointer;"><i class="fa fa-unlock" ></i></a>';
                            }
                        }
                    }

                    $action.='
                    <!-- more details -->';
                        $action .='&nbsp; &nbsp;';
                        $action.='<a count="'.$value->document_id.'" id="moredet" data-toggle="modal"  style="cursor:pointer; padding-left:2px; padding-right:2px;" data-target="#viewmoreModal" title="More Details" ><i class="fa fa-ellipsis-v" aria-hidden="true"></i></a>';
                    
                    break;
                }
                $action .='</ul></div>';
                $missing = '<p style="color:red;">Missing</p>';

                $row_d['actions'] = $action;

               /* if($value->document_type->document_type_name)
                {
                    $row_d['document_type_id'] = ucfirst(@$value->document_type->document_type_name);
                }
                else
                {
                    if($current_view == Lang::get('language.import_view'))
                    {$row_d['document_type_id'] = $missing;}
                    else
                    {$row_d['document_type_id'] = '';}
                }*/
                if($value->document_no)
                {
                    $row_d['document_no'] = $value->document_no;
                }
                else
                {
                    if($current_view == Lang::get('language.import_view'))
                    {
                        $row_d['document_no'] = $missing;
                    }
                    else
                    {
                        $row_d['document_no'] = '';
                    }
                }
                
                if($value->document_name)
                {
                    $row_d['document_name'] = ucfirst($value->document_name).', Ver : '.$value->document_version_no;
                }
                else
                {
                    if($current_view == Lang::get('language.import_view'))
                    {
                        $row_d['document_name'] = $missing;
                    }
                    else
                    {
                        $row_d['document_name'] = '';
                    }
                }
                 $department_name ='';
                 if($value->department_id)
                 {   
                 $department_data = DB::table('tbl_departments')->select(DB::raw('GROUP_CONCAT(tbl_departments.department_name) AS department_name'))->whereIn('tbl_departments.department_id',explode(',',$value->department_id))->first();  
                    $department_name = ($department_data)?$department_data->department_name:'';
                 }   
                if($department_name)
                {
                    $row_d['department_id'] = ucfirst($department_name);
                }
                else
                {
                    if($current_view == Lang::get('language.import_view'))
                    {
                        $row_d['department_id'] = $missing;
                    }
                    else
                    {
                        $row_d['department_id'] = '';
                    }
                }
                 $stack_name =$value->stack_name;
                 /*if($value->stack_id)
                 {   
                 $stack_data =  DB::table('tbl_stacks')->select(DB::raw('GROUP_CONCAT(tbl_stacks.stack_name) AS stack_name'))->whereIn('tbl_stacks.stack_id',explode(',',$value->stack_id))->first(); 
                 $stack_name = ($stack_data)?$stack_data->stack_name:'';
                 } */   
                if($stack_name)
                {
                    $row_d['stack_id'] = ucfirst($stack_name);
                }
                else
                {if($current_view == Lang::get('language.import_view'))
                    {$row_d['stack_id'] = $missing;}
                    else
                    {$row_d['stack_id'] = '';}
                }
                $document_id = $value->document_id;

                if($view == Lang::get('language.import_view')){
                    $query = DB::table('tbl_document_types_columns as tc')
                    ->leftJoin('tbl_temp_documents_columns as tdc', function($join) use($document_id){
                        $join->on('tc.document_type_column_id','=','tdc.document_type_column_id');
                        $join->where('tdc.document_id','=',$document_id);
                      })
                    ->select('tdc.document_column_value',
                        'tc.document_type_column_id',
                        'tdc.document_column_name',
                        'tdc.document_column_modified_by',
                        'tdc.updated_at',
                        'tdc.document_column_id',
                        'tdc.document_id',
                        'tc.document_type_id',
                        'tc.document_type_column_name',
                        'tc.document_type_column_type'
                        )
                    ->where('tc.document_type_id',$doclistid)
                    ->orderBy('tc.document_type_column_order','ASC');
                    $data2 = $query->get();                   
                }else if($view == Lang::get('language.checkout_view')){
                    $query = DB::table('tbl_document_types_columns as tc')
                    ->leftJoin('tbl_documents_columns_checkout as tdc', function($join) use($document_id){
                        $join->on('tc.document_type_column_id','=','tdc.document_type_column_id');
                        $join->where('tdc.document_id','=',$document_id);
                      })
                    ->select('tdc.document_column_value',
                        'tc.document_type_column_id',
                        'tdc.document_column_name',
                        'tdc.document_column_modified_by',
                        'tdc.updated_at',
                        'tdc.document_column_id',
                        'tdc.document_id',
                        'tc.document_type_id',
                        'tc.document_type_column_name',
                        'tc.document_type_column_type'
                        )
                    ->where('tc.document_type_id',$doclistid)
                    ->orderBy('tc.document_type_column_order','ASC');
                    $data2 = $query->get(); 
                }else{
                    $query = DB::table('tbl_document_types_columns as tc')
                    ->leftJoin('tbl_documents_columns as tdc', function($join) use($document_id){
                        $join->on('tc.document_type_column_id','=','tdc.document_type_column_id');
                        $join->where('tdc.document_id','=',$document_id);
                      })
                    ->select('tdc.document_column_value',
                        'tc.document_type_column_id',
                        'tdc.document_column_name',
                        'tdc.document_column_modified_by',
                        'tdc.updated_at',
                        'tdc.document_column_id',
                        'tdc.document_id',
                        'tc.document_type_id',
                        'tc.document_type_column_name',
                        'tc.document_type_column_type'
                        )
                    ->where('tc.document_type_id',$doclistid)
                    ->orderBy('tc.document_type_column_order','ASC');
                    $data2 = $query->get();                   
                }
                foreach ($data2 as $value2) 
                {
                    if($value2->document_type_column_type == "Date")
                    {
                        /*custom date removed due to sorting issue*/
                        /*$value_column = custom_date_Format($value2->document_column_value);*/
                        $value_column = $value2->document_column_value;
                    }
                    
                    else
                    {
                        $value_column = ucfirst($value2->document_column_value);
                    }
                    
                    $row_d["$value2->document_type_column_id"] = ($value_column)?$value_column:'-';
                }


                $row_d['document_ownership'] = ucfirst($value->document_ownership);
                $row_d['document_path'] = ucfirst($value->document_path);
                $row_d['created_at'] = dtFormat($value->created_at);
                $row_d['updated_at'] = dtFormat($value->updated_at);
                $row_d['document_encrypt_status'] = @$value->document_encrypt_status;
                $row_d['document_id'] = @$value->document_id;
                //checkout
                if($current_view == 'checkout')
                {
                    $row_d['document_checkout_date'] = dtFormat(@$value->document_checkout_date);
                    $row_d['document_modified_by'] = @$value->username;
                }  
                $row_d['document_expiry_date'] = custom_date_Format($value->document_expiry_date);
                if(($value->document_expiry_date > date('Y-m-d'))){
                    $todaydate = date('Y-m-d'); // or your date as well
                    $docexpdate = $value->document_expiry_date;
                    $datediff = abs(strtotime($docexpdate) - strtotime($todaydate));
                    $row_d['noofdays'] = round($datediff / (60 * 60 * 24));                
                }else{
                    $row_d['noofdays'] = 0;
                } 
                $row_d['document_status'] = ucfirst($value->document_status);
                $data_table[] = $row_d;
            }
                         
            $output = array(
              "draw" =>  Input::get('draw'),
              "recordsTotal" => $count_all,
              "recordsFiltered" => $count_all,
              "data" => $data_table,
              "queries" => $queries
            );
                
            echo json_encode($output);
        }
        else 
        {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

     public function link_to_doc_filter()
    { 
        include (public_path()."/storage/includes/lang1.en.php" );
        if (Auth::user()) 
        {
            $length         =     Input::get("length");
            $start          =     Input::get("start");
            $view           =     Input::get('view');//get different view
            $doclistid      =     Input::get('type'); //current selected type id
            $filter         =     Input::get('filter'); //filter
            $curr_id        =     Input::get('id'); //id selected from side bar(stack,dept,type)
            $link_doc_id        =     Input::get('link_doc_id');

            $order = (isset($_POST['order'][0]['column']))?$_POST['order'][0]['column']:1;
            $direct = (isset($_POST['order'][0]['dir']))?$_POST['order'][0]['dir']:'DESC';
            /*$search       =   (isset($_POST['search']['value']))?trim($_POST['search']['value']):'';*/
            $search        =     Input::get('search_text')?trim(Input::get('search_text')):'';  
            $search_column  = (isset($_POST['search_column_data']['search_column']))?trim($_POST['search_column_data']['search_column']):'';
            
            $currentPage = ($start)?($start/$length)+1:1;


          \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
          });
            
            
            $loggedUsersdepIds = explode(',',Auth::user()->department_id);
            $docid = Input::get('docid')?Input::get('docid'):0;
            $data = array();
            DB::enableQueryLog();
           
            $table = 'tbl_documents';
            $tbl_documents_columns = 'tbl_documents_columns';
            $data = AjaxModel::ajax_filter_new($table,$tbl_documents_columns,$doclistid,$curr_id,$loggedUsersdepIds,$view,$filter,$docid,$search,$search_column,$length);

            $queries = DB::getQueryLog();  
            $count_all = ($data['dglist'])?$data['dglist']->total():0;
            $i = $start;
            $data_table = array();
            $current_view =Input::get('view');
            $user_permission=Auth::user()->user_permission;

            $linked_array = array();    

            foreach ($data['dglist'] as $value) {

            $ext = pathinfo($value->document_file_name, PATHINFO_EXTENSION);
            $ext = strtolower($ext);
            $i++;
            $row_d = array();

            $action =''; 
            
            $document_id = $value->document_id;
            
            $check = DB::table('tbl_document_links')->where('app_document_id',$link_doc_id)->where('document_id',$document_id)->first();
            $checked="";
            if($check)
            {
               $linked_array[] =  $document_id;
               $checked='checked="checked"';
            }    
            $action.='<input name="link_checkbox[]" type="checkbox" value="'.$value->document_id.'" id="link_chk'.$value->document_id.'" class="link_checkBoxClass" '.$checked.'>';

             $row_d['actions'] = $action;
             $row_d['document_no'] = ($value->document_no)?$value->document_no:'';
             $row_d['document_no'] = ($value->document_name)?ucfirst($value->document_name).', Ver : '.$value->document_version_no:'';
              
                 $department_name ='';
                 if($value->department_id)
                 {   
                 $department_data = DB::table('tbl_departments')->select(DB::raw('GROUP_CONCAT(tbl_departments.department_name) AS department_name'))->whereIn('tbl_departments.department_id',explode(',',$value->department_id))->first();  
                    $department_name = ($department_data)?$department_data->department_name:'';
                 }
                 $row_d['department_id'] = ($department_name)?ucfirst($department_name):'';   
                 
                 $stack_name =$value->stack_name;
                 $row_d['stack_id'] = ($stack_name)?ucfirst($stack_name):''; 
               
                $document_id = $value->document_id;

                $query = DB::table('tbl_document_types_columns as tc')
                    ->leftJoin('tbl_temp_documents_columns as tdc', function($join) use($document_id){
                        $join->on('tc.document_type_column_id','=','tdc.document_type_column_id');
                        $join->where('tdc.document_id','=',$document_id);
                      })
                    ->select('tdc.document_column_value',
                        'tc.document_type_column_id',
                        'tdc.document_column_name',
                        'tdc.document_column_modified_by',
                        'tdc.updated_at',
                        'tdc.document_column_id',
                        'tdc.document_id',
                        'tc.document_type_id',
                        'tc.document_type_column_name',
                        'tc.document_type_column_type'
                        )
                    ->where('tc.document_type_id',$doclistid)
                    ->orderBy('tc.document_type_column_order','ASC');
                    $data2 = $query->get(); 

                foreach ($data2 as $value2) 
                {
                     if($value2->document_type_column_type == "Date")
                    {
                            $value_column = custom_date_Format($value2->document_column_value);
                    }
                    else
                    {
                        $value_column = ucfirst($value2->document_column_value);
                    }
                    
                   // $row_d["$value2->document_type_column_id"] = ($value_column)?$value_column:'-';
                }


               /* $row_d['document_ownership'] = ucfirst($value->document_ownership);
                $row_d['document_path'] = ucfirst($value->document_path);
                $row_d['created_at'] = dtFormat($value->created_at);
                $row_d['updated_at'] = dtFormat($value->updated_at);
                $row_d['document_encrypt_status'] = @$value->document_encrypt_status;
                $row_d['document_id'] = @$value->document_id;
                
                $row_d['document_status'] = ucfirst($value->document_status);*/
                $data_table[] = $row_d;
            }
                

            
            $output = array(
              "draw" =>  Input::get('draw'),
              "recordsTotal" => $count_all,
              "recordsFiltered" => $count_all,
              "data" => $data_table,
              "queries" => $queries,
              "linked_array" => $linked_array
            );
                
            echo json_encode($output);
        }
        else 
        {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    /************** CHECK IN AND PUBLISH  START *************/
public function moveAll()
{
    //selected docs for move
    $arr=Input::get('selected')?:array();
    $checkin_type=Input::get('checkin_type');
    $checkin_parent=Input::get('checkin_parent');
    $checkin_action=Input::get('checkin_action');
    if (Auth::user()) 
    {

        $status=0;
        $message='';
        $dataSet = [];
        $dataCol=[];
        $last_ins_id =array();
        $duplicate_list = array();
        $published_count= $duplicate_count = 0;
        //Temp documents table to Main Documents table
        foreach ($arr as $value) 
        {
            $data = DB::table('tbl_temp_documents')->where('document_id',$value)->first();

            if($data)
            { 
                $file_data_exists = \App\DocumentsModel::check_file_data_exists($data->document_file_name);
                $multiple = $file_data_exists['multiple']; 
                if($checkin_action == 'discard')
                {
                    //delete all moved files from temp documents
                    DB::table('tbl_temp_documents')->where('document_id', $value)->delete();
                    DB::table('tbl_temp_documents_columns')->where('document_id', $value)->delete();
                    DB::table('tbl_temp_document_notes')->where('document_id', $value)->delete();
                    $message = "Document discarded successfully";
                }
                else if($multiple == 0 || ($checkin_action == 'replace'))
                {
                    if($multiple)
                    {
                        $dup_data = $file_data_exists['dup_data'];
                        $dup_doc_id = ($dup_data)?$dup_data->document_id:0;
                        $tbl_documents_modl= \App\DocumentsModel::find($dup_doc_id);
                        if(!$tbl_documents_modl)
                        {
                           $tbl_documents_modl=new \App\DocumentsModel;     
                        }
;    
                    }
                    else
                    {
                        $tbl_documents_modl=new \App\DocumentsModel;
                    }    
                
                $tbl_documents_modl->document_type_id      = $data->document_type_id;
                $tbl_documents_modl->document_name         = $data->document_name;
                $tbl_documents_modl->document_file_name    = $data->document_file_name;
                $tbl_documents_modl->parent_id             = $data->parent_id;
                $tbl_documents_modl->department_id         = $data->department_id;
                $tbl_documents_modl->stack_id              = $data->stack_id;
                $tbl_documents_modl->document_version_no   = "1.0";
                $tbl_documents_modl->document_ownership    = Auth::user()->username;
                $tbl_documents_modl->document_created_by   = Auth::user()->username;
                $tbl_documents_modl->document_tagwords     = $data->document_tagwords;
                $tbl_documents_modl->document_no           = $data->document_no;
                $tbl_documents_modl->document_path         = $data->document_path;
                if($checkin_type == 'draft')
                {
                        $tbl_documents_modl->document_status       = "Draft";
                        $tbl_documents_modl->document_assigned_to  = 'NULL';
                }
                else
                {
                    if($data->document_assigned_to != "")
                    {
                        $tbl_documents_modl->document_status            = "Review";
                        $tbl_documents_modl->document_assigned_to       = $data->document_assigned_to;
                    }
                    else
                    {
                        $tbl_documents_modl->document_status       = "Published";
                        $tbl_documents_modl->document_assigned_to  = 'NULL';
                    }
                }
                    $tbl_documents_modl->created_at            = date('Y-m-d H:i:s');
                    $tbl_documents_modl->updated_at            = date('Y-m-d H:i:s');
                    $tbl_documents_modl->document_size         = $data->document_size;
                    $tbl_documents_modl->document_expiry_date  = $data->document_expiry_date;
                    $tbl_documents_modl->save();
                    $last_ins_id= $tbl_documents_modl->document_id;
           
            //move the file from source to destination
            $oldFileName        =   $data->document_file_name;
            $destinationPath    =   Session::get('base_path');
            if($oldFileName)
            {
                $source         =   Session::get('temp_document_path').$oldFileName;
                $dest           =   $destinationPath.$oldFileName;
                if(file_exists($source))
                { //copy the file source to destination
                    $copy = copy($source, $dest);
                    unlink($source);//reomve original file
                }           
            }        
            
            $data_from_temp_col = DB::table('tbl_temp_documents_columns')->where('document_id',$value )->get();
            $affectedRows = \App\DocumentsColumnModel::where('document_id', '=', $last_ins_id)->delete();
            foreach ($data_from_temp_col as $data)
            {         
                $tbl_col_documents_modl=new \App\DocumentsColumnModel;      
                $tbl_col_documents_modl->document_id=$last_ins_id;
                $tbl_col_documents_modl->document_type_column_id=$data->document_type_column_id;
                $tbl_col_documents_modl->document_column_name=$data->document_column_name;
                $tbl_col_documents_modl->document_column_value=$data->document_column_value;
                $tbl_col_documents_modl->document_column_type=$data->document_column_type;
                $tbl_col_documents_modl->document_column_mandatory=$data->document_column_mandatory;
                $tbl_col_documents_modl->document_column_created_by=$data->document_column_created_by;
                $tbl_col_documents_modl->document_column_modified_by=$data->document_column_modified_by;
                $tbl_col_documents_modl->created_at=$data->created_at;
                $tbl_col_documents_modl->updated_at=$data->updated_at;
                $tbl_col_documents_modl->save();
            }
            //notes table
            $note_from_temp = DB::table('tbl_temp_document_notes')->where('document_id',$value)->get();
            foreach ($note_from_temp as $note) 
            {
                $tbl_note_modl=new \App\DocumentNoteModel;
                $tbl_note_modl->document_notes_id       =   '';
                $tbl_note_modl->document_id             =   $last_ins_id;
                $tbl_note_modl->document_note           =   $note->document_note;
                $tbl_note_modl->document_note_created_by=   $note->document_note_created_by;
                $tbl_note_modl->document_note_modified_by=   $note->document_note_modified_by;
                $tbl_note_modl->created_at              =   $note->created_at;
                $tbl_note_modl->updated_at              =   $note->updated_at;
                $tbl_note_modl->save();
            }
             
            //delete all moved files from temp documents
            DB::table('tbl_temp_documents')->where('document_id', $value)->delete();
            DB::table('tbl_temp_documents_columns')->where('document_id', $value)->delete();
            DB::table('tbl_temp_document_notes')->where('document_id', $value)->delete();
            $status=1;
            $published_count++;
            }
            else
            {
                $dup_data = $file_data_exists['dup_data'];
                $dup_doc_id = ($dup_data)?$dup_data->document_id:0;
                $dr = array();
                $action = '<input name="checkbox[]" type="checkbox" value="'.$data->document_id.'" id="more_details" class="DupcheckBoxClass">';
                /*$action .='<a count="'.$dup_doc_id.'" id="more_details" data-toggle="modal"  style="padding-left:2px; padding-right:2px;" data-target="#viewmoreModal" title="More Details" ><i class="fa fa-ellipsis-v" aria-hidden="true"></i></a>
                            ';*/
                $dr[] = $action;            
                $department_name ='';
                 if($data->department_id)
                 {   
                 $department_data = DB::table('tbl_departments')->select(DB::raw('GROUP_CONCAT(tbl_departments.department_name) AS department_name'))->whereIn('tbl_departments.department_id',explode(',',$data->department_id))->first();  
                    $department_name = ($department_data)?$department_data->department_name:'';
                 } 
                $dr[] = $department_name; 
                $stack_name='';
                if($data->stack_id)
                 {   
                 $stack_data =  DB::table('tbl_stacks')->select(DB::raw('GROUP_CONCAT(tbl_stacks.stack_name) AS stack_name'))->whereIn('tbl_stacks.stack_id',explode(',',$data->stack_id))->first(); 
                 $stack_name = ($stack_data)?$stack_data->stack_name:'';
                 }

                $dr[] = $stack_name;
                $dr[] = $data->document_no;
                /*$dr[] = $data->document_file_name;*/
                $dr[] = $data->document_name;
                
                $document_id = $data->document_id;
                $query = DB::table('tbl_document_types_columns as tc')
                    ->leftJoin('tbl_temp_documents_columns as tdc', function($join) use($document_id){
                        $join->on('tc.document_type_column_id','=','tdc.document_type_column_id');
                        $join->where('tdc.document_id','=',$document_id);
                      })
                    ->select('tdc.document_column_value',
                        'tc.document_type_column_id',
                        'tdc.document_column_name',
                        'tdc.document_column_modified_by',
                        'tdc.updated_at',
                        'tdc.document_column_id',
                        'tdc.document_id',
                        'tc.document_type_id',
                        'tc.document_type_column_name',
                        'tc.document_type_column_type'
                        )
                    ->where('tc.document_type_id',$data->document_type_id)
                    ->orderBy('tc.document_type_column_order','ASC');
                    $data2 = $query->get(); 
                 foreach ($data2 as $value2) 
                {
                     if($value2->document_type_column_type == "Date")
                    {
                            $value_column = custom_date_Format($value2->document_column_value);
                    }
                    else
                    {
                        $value_column = ucfirst($value2->document_column_value);
                    }
                    
                    $dr[] = ($value_column)?$value_column:'-';
                }   
                
                $duplicate_list[] = $dr;
                $status=0;
                $duplicate_count++;
            }

           

            }
            //columns table

        }
           
        if(!$duplicate_count)
        {
           $status = 2;     
        }

        if($published_count)
        {
                
        }

        $response = array('status' => $status);
        $response['duplicate_list'] = $duplicate_list;
        $response['published_count'] = $published_count;
        $response['duplicate_count'] = $duplicate_count;
        $message = '';
        if($checkin_action == 'discard')
        {
                   
            $message = "Document discarded successfully";
        }   
        else if($published_count)
        {
           

           if($checkin_type == 'draft')
            {
                 $message .= $published_count." ".trans('documents.Documents_published_draft'); 
                 $result = (new AuditsController)->dcmntslog(Auth::user()->username,$data->document_id,'Documents','Import And Drafted',$published_count.' Documents Drafted Successfully','','','','','');
            }
            else
            {
                $message .= $published_count." ".trans('documents.Documents_published'); 
                $result = (new AuditsController)->dcmntslog(Auth::user()->username,$data->document_id,'Documents','Import And Published',$published_count.' Documents Published Successfully','','','','','');
            }
        }
        else
        {
            if($checkin_type == 'draft')
            {
                 
                $message .= "No documents published as draft. "; 
            }
            else
            {
                
                $message .= "No documents published. "; 
            }
        }

        if($duplicate_count)
        {
           $message .= $duplicate_count." duplicate documents were found."; 
        }
        $response['message'] = trim($message);
        echo json_encode($response);
    }
    else 
    {
        return redirect('')->withErrors("Please login")->withInput();
    }
}
/*****************Move ALL END*****************/
    

     public function link_to_doc_save()
    { 
         $link_doc_id=Input::get('link_doc_id')?:0;
         $arr=Input::get('selected')?:array();
         $uncheked_arr=Input::get('uncheked_arr')?:array();
         if($uncheked_arr)
         {
            $delete = DB::table('tbl_document_links')->where('app_document_id',$link_doc_id)->whereIn('document_id',$uncheked_arr)->delete();
         }
         $created_at            = date('Y-m-d H:i:s');
         foreach ($arr as $value) 
        {
            $data = DB::table('tbl_document_links')->where('app_document_id',$link_doc_id)->where('document_id',$value)->first();
            if(!$data)
            {
                 $values = array('app_document_id' => $link_doc_id,'document_id' => $value,'created_at' => $created_at,'created_at' => $created_at);
                 DB::table('tbl_document_links')->insert($values);   
            }
        }    
        
        $response = array('status' => 1); 
        $message = "Document linked successfully";   
        $response['message'] = trim($message);
        echo json_encode($response);
    }
    
   
   
}/*<--END-->*/
