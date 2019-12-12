<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request as CookieRequest;
use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;
use View;
use Validator;
use Input;
use URL;
use Response;
use Session;
use File;
use Cookie;
Use DateTime;
Use Storage;
use Cache;
use Artisan;
use Config;
use App\Mylibs\Filetotext;
use App\Mylibs\Common;
use App\DocumentsCheckoutModel as DocumentsCheckoutModel;
use App\DocumentsColumnCheckoutModel as DocumentsColumnCheckoutModel;
use App\TempDocumentsModel as TempDocumentsModel;
use App\TagWordsModel as TagWordsModel;
use App\DocumentsModel as DocumentsModel;
use App\DocumentTypesModel as DocumentTypesModel;
use App\DocumentTypeColumnModel as DocumentTypeColumnModel;
use App\DocumentsColumnModel as DocumentsColumnModel;
use App\TempDocumentsColumnModel as TempDocumentsColumnModel;
use App\DepartmentsModel as DepartmentsModel;
use App\KeywordsModel as KeywordsModel;
use App\TagWordsCategoryModel as TagWordsCategoryModel;
use App\StacksModel as StacksModel;
use App\DocumentNoteModel as DocumentNoteModel;
use App\TempDocumentNoteModel as TempDocumentNoteModel;
use App\DocumentHistoryModel as DocumentHistoryModel;
use App\DocumentHistoryColumnModel as DocumentHistoryColumnModel;
use App\AuditsModel as AuditsModel;
use App\Users as Users;
use App\SettingsModel as SettingsModel;
use App\TreeDataModel as TreeDataModel;
use App\TreeStructModel as TreeStructModel;
use App\WorkflowsModel as WorkflowsModel;
use App\FormModel as FormModel;
use App\DocumentBookmarkModel as DocumentBookmarkModel;
use DB;
use \stdClass;
use Lang;
use ZipArchive;

class DocumentsController extends Controller
{
    public function __construct()
    {
        Session::put('menuid', '1');
        $this->middleware(['auth', 'user.status']);

        // Define common variable
        $this->actionName1 = 'Document';
        $this->actionName2 = 'Import Document';
        $this->module      = 'File Viewer';
        $this->decs        = 'Image Annotation';
        $this->docObj      = new Common(); // class defined in app/mylibs

    }

    
    public function saveBookmark(){   
        if (Auth::user()) {
            //$data['docmntid']=Input::get('docmntid');
            $documentBkmrkModl                  =   new DocumentBookmarkModel;
            $documentBkmrkModl->document_id     =   Input::get('docmntid');
            $documentBkmrkModl->document_bookmark   =   Input::get('bokmrktxt');
            $documentBkmrkModl->document_bookmark_pageno   =   Input::get('pageno');
            $documentBkmrkModl->document_bookmark_created_by  =   Auth::user()->username;
            if ($documentBkmrkModl->save()) {
                $lastId = ($documentBkmrkModl->document_bookmark_id);
                echo $lastId;
            }else{
                echo 'failed';
                exit;
            }
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    public function deleteBookmark(){   
        if (Auth::user()) {
            $document_id            =   Input::get('docmntid');
            $document_bookmark_id   =   Input::get('selectdid');
            if((isset($document_bookmark_id)) && (isset($document_id))){
                echo $res = DB::table('tbl_document_bookmarks')->where('document_id', $document_id)->where('document_bookmark_id', $document_bookmark_id)->delete();         
            }else{
                echo 'failed';
                exit;
            }        
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    public function getBookmark(){   
        if (Auth::user()) {
            $document_id            =   Input::get('docmntid');
            $document_bookmark_id   =   Input::get('selectdid');
            if($document_bookmark_id==0){
                $bookmarkpage = 0;
            }else{
                $bookMarkList = DB::table('tbl_document_bookmarks')->select('document_bookmark_pageno')->where('document_id', $document_id)->where('document_bookmark_id', $document_bookmark_id)->get();  
                foreach($bookMarkList as $val):
                    $bookmarkpage = $val->document_bookmark_pageno;
                endforeach;                
            }
            echo $bookmarkpage;
            
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }


    public function session_destroy_all()
    {
        Session::forget('search_documentsIds');
        Session::forget('search_list_exists');
        Session::forget('serach_doc_no');
        Session::forget('search_docname');
        Session::forget('coltypecnt');
        Session::forget('search_keywrd_srchtxt');
        Session::forget('search_option');
        Session::forget('search_ownership');
        Session::forget('search_created_by');
        Session::forget('search_updated_by');
        Session::forget('updated_by_owner_ids');
        Session::forget('created_by_owner_ids');
        Session::forget('owner_ids');
        Session::forget('search_departments');
        Session::forget('departments');
        Session::forget('search_document_type_name');
        Session::forget('doctypeids');
        Session::forget('doctypeid');
        Session::forget('search_stack');
        Session::forget('stackids');
        Session::forget('search_created_date_from');
        Session::forget('search_created_date_to');
        Session::forget('search_last_modified_from');
        Session::forget('search_last_modified_to');
        Session::forget('search_criteria_id'); 
        Session::forget('search_criteria_name');
    }
    public function index()
    {    

        if (Auth::user()) {
            Session::put('menuid', '1');
            $saved_search = (Input::get('saved_search'))?1:0;
            $data['status'] = 0;         
            Session::forget('sess_lastInsertedIDD');
            Session::forget('sess_lastInsertedFiles');
            Session::forget('sess_lastInsertedID');
            Session::forget('sess_settype');
            // Distroy session in search list
            $this->session_destroy_all();
            $this->docObj->document_assign_notification();
            $this->docObj->document_reject_notification();
            $this->docObj->document_accept_notification();

            if(Session::get('sess_countcol')>0){
                $count=Session::get('sess_countcol');
                for($i=1;$i<=$count;$i++)
                {
                Session::forget('sess_doctypecol'.$i);
                }
            }
            Session::forget('sess_countcol');

            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name','settings_document_expiry','settings_ftp')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;
            $data['settings_document_expiry'] = $settings[0]->settings_document_expiry;
            
            Session::put('settings_document_expiry',$settings[0]->settings_document_expiry);

            Session::put('settings_ftp_upload',$settings[0]->settings_ftp);
            // Get department id of the user
            $depIds = explode(',',Auth::user()->department_id);
            
            if(!empty(Session::get('SESS_parentIdd'))){
    
                if(Auth::user()->user_role != Session::get("user_role_super_admin")):
                                    
                  $query = DB::table('tbl_documents')->select('*');
                //check user = private user, fetch only the docs of that user
                if(Auth::user()->user_role == Session::get("user_role_private_user"))
                {
                    $query->where('document_ownership',Auth::user()->username);
                }
                  $count = count($depIds);
                  if($count == 1):
                    $x = 0;
                  else:
                    $x = 1;
                  endif;
                  /*foreach($depIds as $ids): 
                    if($x==1){
                        $query->orWhereRaw('('.'FIND_IN_SET('.$ids.',department_id)');
                    }elseif($x==$count){
                        $query->orWhereRaw('FIND_IN_SET('.$ids.',department_id)'.')');
                    }else{
                        $query->orWhereRaw('FIND_IN_SET('.$ids.',department_id)');
                    }
                    
                    $x++;
                    endforeach;*/
                    $query->whereIn('department_id',$depIds); 
                    $query->where('parent_id',Session::get('SESS_parentIdd'));
                    $query->orderBy('document_id', 'desc')->paginate(10);
                    $data['dglist'] = $query->get();
                    //echo '<pre>'; print_r($data); echo '</pre>';
                else: 
                    $data['dglist'] = DB::table('tbl_documents')->select('*')->where('parent_id',Session::get('SESS_parentIdd'))->orderBy('document_id', 'desc')->paginate(10);
                endif;
                
            }else{

                if(Auth::user()->user_role != Session::get("user_role_super_admin")):
                                    
                  $query = DB::table('tbl_documents')->select('*')->where('document_expiry_date','>',date('Y-m-d'));
                //check user = private user, fetch only the docs of that user
                if(Auth::user()->user_role == Session::get("user_role_private_user"))
                {
                    $query->where('document_ownership',Auth::user()->username);
                }
                  $count = count($depIds);
                  if($count == 1):
                    $x = 0;
                  else:
                    $x = 1;
                  endif;
                  foreach($depIds as $ids):
                    if($x==1){
                        $query->orWhereRaw('('.'FIND_IN_SET('.$ids.',department_id)');
                    }elseif($x==$count){
                        $query->orWhereRaw('FIND_IN_SET('.$ids.',department_id)'.')');
                    }else{
                        $query->orWhereRaw('FIND_IN_SET('.$ids.',department_id)');
                    }
                    
                    $x++;
                    endforeach;
                    //$query->whereIn('department_id',$depIds);
                    $query->where('parent_id',1);
                    $query->orderBy('document_id', 'desc')->paginate(10);
                    $data['dglist'] = $query->get();

                else:
                    $data['dglist'] = DB::table('tbl_documents')->select('*')->where('parent_id',Session::get('SESS_parentIdd'))->orderBy('document_id', 'desc')->paginate(10);
                endif;
                $root=DB::table('tree_data')->select('nm')->where('id',1)->get();
                Session::put('SESS_path',$root[0]->nm);
                Session::put('SESS_parentIdd',1);
            }

            // Expanding dglits with required datas
            foreach($data['dglist'] as $val):

                $val->document_type_columns = DB::table('tbl_documents_columns')->select('document_column_name','document_column_value','document_column_type')->where('document_id',$val->document_id)->get();
    
                // Get documentTypes
                //$val->documentTypes = DB::table('tbl_document_types')->select(DB::raw('GROUP_CONCAT(document_type_name) AS document_type_names','document_type_column_no','document_type_column_name'))->whereIn('document_type_id',explode(',',$val->document_type_id))->get();

                $val->documentTypes = DocumentTypesModel::select('document_type_column_no','document_type_column_name')->where('document_type_id', $val->document_type_id)->get();
                
                // Get stack
                $val->stacks = DB::table('tbl_stacks')->select(DB::raw('GROUP_CONCAT(stack_name) AS stack_name'))->whereIn('stack_id',explode(',',$val->stack_id))->get();
                // Get Tag words
                $val->tagwords = DB::table('tbl_tagwords')->select(DB::raw('GROUP_CONCAT(tagwords_title) AS tagwords_title'))->whereIn('tagwords_id',explode(',',$val->document_tagwords))->get();
                // Get department
                $val->department = DB::table('tbl_departments')->select(DB::raw('GROUP_CONCAT(department_name) AS department_name'))->whereIn('department_id',explode(',',$val->department_id))->get();
                
            endforeach;

                $data['docType'] = DocumentTypesModel::where('is_app',0)->get();
                
                $data['stckApp'] = $this->docObj->common_stack();
                $data['deptApp'] = $this->docObj->common_dept();
                $data['doctypeApp'] = $this->docObj->common_type();
                $data['records'] = $this->docObj->common_records();
            // View page for multiple upload files
            if(@Input::get('uploadFile')){
                
                $data['stack'] = StacksModel::all();
                $data['tagsCateg'] = TagWordsCategoryModel::all();
                $data['docType'] = DocumentTypesModel::where('is_app',0)->get();
                return View('pages/documents/upload_file')->with($data);
            }
            $data['page'] = (Session::get('SESS_page_grid') && $saved_search)?Session::get('SESS_page_grid'):1;
            $data['searhtext'] = (Session::get('SESS_searhtext') && $saved_search)?Session::get('SESS_searhtext'):'';
            $data['path'] = (Session::get('SESS_path') && $saved_search)?Session::get('SESS_path'):'DMS ROOT';
            return View::make('pages/documents/index')->with($data);
            
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
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
            Session::put('serach_length',$length);
            Session::put('serach_start',$start);
            Session::put('serach_doc_type',$doclistid);
            Session::put('serach_filter',$filter);
            Session::put('serach_view',$view);

            $order = (isset($_POST['order'][0]['column']))?$_POST['order'][0]['column']:1;
            $direct = (isset($_POST['order'][0]['dir']))?$_POST['order'][0]['dir']:'DESC';
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
            $search       =   (isset($_POST['search']['value']))?trim($_POST['search']['value']):'';
            Session::put('search_text',$search);
            $footer_search1 =  (isset($_POST['columns'][1]['search']['value']))?trim($_POST['columns'][1]['search']['value']):'';
            $footer_search2 =  (isset($_POST['columns'][2]['search']['value']))?trim($_POST['columns'][2]['search']['value']):'';
            $footer_search3 =  (isset($_POST['columns'][3]['search']['value']))?trim($_POST['columns'][3]['search']['value']):'';
            $footer_search4 =  (isset($_POST['columns'][4]['search']['value']))?trim($_POST['columns'][4]['search']['value']):'';
            $footer_search5 =  (isset($_POST['columns'][5]['search']['value']))?trim($_POST['columns'][5]['search']['value']):'';
            $footer_search6 =  (isset($_POST['columns'][8]['search']['value']))?trim($_POST['columns'][8]['search']['value']):'';
            $footer_search7 =  (isset($_POST['columns'][9]['search']['value']))?trim($_POST['columns'][9]['search']['value']):'';
            /* Check out date filtering [ Only in case of checkout view ] */
            $footer_search8 =  (isset($_POST['columns'][10]['search']['value']))?trim($_POST['columns'][10]['search']['value']):'';

            $loggedUsersdepIds = explode(',',Auth::user()->department_id);
            if(Input::get('docid'))
            {
                $docid = Input::get('docid');//particular document show on list
            }
            else
            {
                $docid = '';
            }
            switch($view)
            {
                case Lang::get('language.list_view')://list view
                case Lang::get('language.document_type_view'): //type
                case Lang::get('language.stack_view')://stack
                case Lang::get('language.recent_document') : //recent document
                case Lang::get('language.department_view')://dept wise
                    $table = 'tbl_documents';
                   $data = $this->filter($table,$doclistid,$curr_id,$loggedUsersdepIds,$view,$filter,$docid,$search,$footer_search1,$footer_search2,$footer_search3,$footer_search4,$footer_search5,$footer_search6,$footer_search7,$footer_search8,$length);
                break; 
                case Lang::get('language.import_view'): //import view
                    $table = 'tbl_temp_documents';
                   $data = $this->filter($table,$doclistid,$curr_id,$loggedUsersdepIds,$view,$filter,$docid,$search,$footer_search1,$footer_search2,$footer_search3,$footer_search4,$footer_search5,$footer_search6,$footer_search7,$footer_search8,$length);
                break;
                case Lang::get('language.checkout_view'): //checkout view
                    $table = 'tbl_documents_checkout';
                   $data = $this->filter($table,$doclistid,$curr_id,$loggedUsersdepIds,$view,$filter,$docid,$search,$footer_search1,$footer_search2,$footer_search3,$footer_search4,$footer_search5,$footer_search6,$footer_search7,$footer_search8,$length);
                break;
            }
              
            $count_all = ($data['dglist'])?$data['dglist']->total():0;
            $i = $start;
            $data_table = array();
            foreach ($data['dglist'] as $value) {
            $ext = pathinfo($value->document_file_name, PATHINFO_EXTENSION);
            $current_view =Input::get('view');
            $user_permission=Auth::user()->user_permission;
            $i++;
            $row_d = array();
            $action =''; 
            if(($current_view == Lang::get('language.list_view')) || ($current_view == Lang::get('language.stack_view')) || ($current_view == Lang::get('language.document_type_view')) || ($current_view == Lang::get('language.department_view'))
                || ($current_view == Lang::get('language.recent_document')))
            {
                if($value->document_status!='Checkout'){
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
                    elseif ($ext=='png'||$ext=='jpg'||$ext=='jpeg'||$ext=='tiff'||$ext=='tif'||$ext=='TIFF'||$ext=='TIF'||$ext=='gif') {
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
                        
                            $action .='<i class="fa fa-trash" id="faClose'.$value->document_id.'" onclick="del(\''.$value->document_id.'\',\''.$value->document_name.'\',\''.$value->document_file_name.'\')" style="color: red; cursor:pointer;" title="Delete"></i>';
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
                        $action.='<a href="download?file='.$value->document_file_name.'" title="Download"><i class="fa fa-download"></i></a>'; 
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
                                 $action.='<i title="Delete" class="fa fa-trash" onclick="del( \''.$value->document_id.'\',\''.$value->document_name.'\')" style="color: red; cursor:pointer;"></i>';
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
                    $action.='';
                   
                    
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

                if($value->document_type->document_type_name)
                {$row_d['document_type_id'] = ucfirst(@$value->document_type->document_type_name);}
                else
                {if($current_view == Lang::get('language.import_view'))
                    {$row_d['document_type_id'] = $missing;}
                    else
                    {$row_d['document_type_id'] = '';}
                }
                if($value->document_no)
                {$row_d['document_no'] = $value->document_no;}
                else
                {if($current_view == Lang::get('language.import_view'))
                    {$row_d['document_no'] = $missing;}
                    else
                    {$row_d['document_no'] = '';}
                }
                if($value->document_name)
                {$row_d['document_name'] = ucfirst($value->document_name).', Ver : '.$value->document_version_no;}
                else
                {if($current_view == Lang::get('language.import_view'))
                    {$row_d['document_name'] = $missing;}
                    else
                    {$row_d['document_name'] = '';}
                }

                if($value->department->department_name)
                {$row_d['department_id'] = ucfirst($value->department->department_name);}
                else
                {if($current_view == Lang::get('language.import_view'))
                    {$row_d['department_id'] = $missing;}
                    else
                    {$row_d['department_id'] = '';}
                }

                if($value->stacks->stack_name)
                {$row_d['stack_id'] = ucfirst($value->stacks->stack_name);}
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
                            $value_column = custom_date_Format($value2->document_column_value);
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
              "data" => $data_table
            );
                
            echo json_encode($output);
        }
        else 
        {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    
    //filter datas according to input
    public function filter($table,$doclistid,$curr_id,$loggedUsersdepIds,$view,$filter,$docid,$search,$footer_search1,$footer_search2,$footer_search3,$footer_search4,$footer_search5,$footer_search6,$footer_search7,$footer_search8,$length)
    {
        if($view == Lang::get('language.recent_document')) {
            $query = DB::table($table)  
            //old query                         
            /*->leftjoin('tbl_audits',$table.'.'.'document_id','=','tbl_audits.document_id')
            ->leftjoin('tbl_departments',$table.'.'.'department_id','=','tbl_departments.department_id')
            ->leftjoin('tbl_stacks',$table.'.'.'stack_id','=','tbl_stacks.stack_id')
            ->leftjoin('tbl_document_types',$table.'.'.'document_type_id','=','tbl_document_types.document_type_id')
            ->where('tbl_audits.audit_action_type','=','Open')
            ->where('tbl_audits.audit_user_name',Auth::user()->username)
            ->distinct();*/
            //new query
            ->leftjoin('tbl_documents_columns',$table.'.'.'document_id','=','tbl_documents_columns.document_id')
            ->leftjoin('tbl_audits',$table.'.'.'document_id','=','tbl_audits.document_id')
            ->leftjoin('tbl_departments',$table.'.'.'department_id','=','tbl_departments.department_id')
            ->leftjoin('tbl_stacks',$table.'.'.'stack_id','=','tbl_stacks.stack_id')
            ->leftjoin('tbl_users_departments','tbl_departments.department_id','=','tbl_users_departments.department_id')
            ->leftjoin('tbl_document_types',$table.'.'.'document_type_id','=','tbl_document_types.document_type_id')
            ->where('tbl_audits.audit_action_type','=','Open')
            ->where('tbl_audits.audit_user_name',Auth::user()->username)
            ->distinct();

            $select ="
            tbl_departments.department_id,
            tbl_stacks.stack_id,
            $table.document_type_id,
            tbl_document_types.document_type_name as document_type_name,
            $table.document_id,
            $table.document_no as document_no,
            $table.document_name as document_name,
            $table.department_id,
            tbl_departments.department_name as department_name,
            $table.stack_id,
            tbl_stacks.stack_name as stack_name,
            $table.document_ownership,
            $table.document_assigned_to,
            $table.document_path,
            $table.created_at,
            $table.updated_at,
            $table.document_expiry_date,
            $table.document_status,
            $table.parent_id,
            $table.document_version_no,
            $table.document_file_name,
            $table.document_modified_by";
        }
        else{ 
            //listview,dept,stack,type views use this
            $query = DB::table($table)
             ->leftjoin('tbl_documents_columns',$table.'.'.'document_id','=','tbl_documents_columns.document_id')
           ->leftjoin('tbl_departments',$table.'.'.'department_id','=','tbl_departments.department_id')
           ->leftjoin('tbl_stacks',$table.'.'.'stack_id','=','tbl_stacks.stack_id')
           ->leftjoin('tbl_document_types',$table.'.'.'document_type_id','=','tbl_document_types.document_type_id');
          
            $select ="
            tbl_departments.department_id,
            tbl_stacks.stack_id,
            $table.document_type_id,
            tbl_document_types.document_type_name as document_type_name,
            $table.document_id,
            $table.document_no as document_no,
            $table.document_name as document_name,
            $table.department_id,
            tbl_departments.department_name as department_name,
            $table.stack_id,
            tbl_stacks.stack_name as stack_name,
            $table.document_ownership,
            $table.document_assigned_to,
            $table.document_path,
            $table.created_at,
            $table.updated_at,
            $table.document_expiry_date,
            $table.document_status,
            $table.parent_id,
            $table.document_version_no,
            $table.document_file_name,
            $table.document_modified_by,
            $table.document_encrypt_status,
            $table.document_encrypted_by";
            //if checkout view apend additional fields from checkout table
        }

        if($view == Lang::get('language.import_view'))//import view
        {
            $query = DB::table($table)
            ->leftjoin('tbl_documents_columns',$table.'.'.'document_id','=','tbl_documents_columns.document_id')
           ->leftjoin('tbl_departments',$table.'.'.'department_id','=','tbl_departments.department_id')
           ->leftjoin('tbl_stacks',$table.'.'.'stack_id','=','tbl_stacks.stack_id')
           ->leftjoin('tbl_document_types',$table.'.'.'document_type_id','=','tbl_document_types.document_type_id');
          
            $select ="
            tbl_departments.department_id,
            tbl_stacks.stack_id,
            $table.document_type_id,
            tbl_document_types.document_type_name as document_type_name,
            $table.document_id,
            $table.document_no as document_no,
            $table.document_name as document_name,
            $table.department_id,
            tbl_departments.department_name as department_name,
            $table.stack_id,
            tbl_stacks.stack_name as stack_name,
            $table.document_ownership,
            $table.document_assigned_to,
            $table.document_path,
            $table.created_at,
            $table.updated_at,
            $table.document_expiry_date,
            $table.document_status,
            $table.parent_id,
            $table.document_version_no,
            $table.document_file_name,
            $table.document_modified_by";
        }

        if($view == Lang::get('language.checkout_view'))//checkout view
        {
            $query->leftjoin('tbl_users',$table.'.'.'documents_checkout_by','=','tbl_users.id');
            $select.=",$table.document_checkout_date,
            $table.document_pre_status,
            tbl_users.username";
        }
        else if($view == Lang::get('language.stack_view'))
        {
            $query->Where("$table.stack_id",$curr_id);
        }
        else if($view == Lang::get('language.department_view'))
        {
            $query->Where("$table.department_id",$curr_id);
        }
        
        //ajax search
        switch($view)
        {
            case Lang::get('language.list_view')://list view
            case Lang::get('language.document_type_view'): //type
            case Lang::get('language.recent_document'):// recent document
            case Lang::get('language.stack_view')://stack
            case Lang::get('language.department_view')://dept wise
            case Lang::get('language.import_view'): //import view
            $column = array('document_type_name',$table.'.'.'document_no',$table.'.'.'document_name','tbl_departments.department_name','tbl_stacks.stack_name',$table.'.'.'document_ownership',$table.'.'.'document_path',$table.'.'.'created_at',$table.'.'.'updated_at',$table.'.'.'document_expiry_date',$table.'.'.'document_status','tbl_documents_columns.document_column_value');
            break;
            case Lang::get('language.checkout_view'): //checkout view
            $column = array('document_type_name',$table.'.'.'document_no',$table.'.'.'document_name','tbl_departments.department_name','tbl_stacks.stack_name',$table.'.'.'document_ownership',$table.'.'.'document_path',$table.'.'.'created_at',$table.'.'.'updated_at',$table.'.'.'document_expiry_date',$table.'.'.'document_status',$table.'.'.'document_checkout_date','tbl_users.username','tbl_documents_columns.document_column_value');
            break;
        }
        if($search){
            $query->Where(function($query1) use($column,$search) {
                foreach ($column as $key => $value) {
                    if($value=="tbl_documents.updated_at"){
      //                $time_input = strtotime($search);  
                        // $date_input = getDate($time_input);  
                        // print_r($date_input); 

                        // $stringlen = strlen($search);
                        // if($stringlen<10){
                            // $srcharr = explode('-', $search);
                            // $format = Config::get('app.settings_timeformat');
                            // $formatarr = explode('-', $format);
                            // $formatarr[0] = 
                            // foreach ($srcharr as $key => $value) {
                            //  # code...
                            // }
                        //  $datesearch = date("d", strtotime($search));    
                        // }else if($stringlen==5){
                        //  $datesearch = date("m-d", strtotime($search));  
                        //}else{
                            $datesearch = date("Y-m-d", strtotime($search));    
                        //}
                        //print_r($datesearch);
                        $query1->orWhere($value,'LIKE','%'.$datesearch.'%');
                    }else{
                        //print_r("else");
                        $query1->orWhere($value,'LIKE','%'.$search.'%');        
                    }
                  
                }
            });
        }
        //tfoot column search
        //column1
        if($footer_search1){
            $tfoot_column1 = array('document_type_name');
            $query->where(function($query1) use($tfoot_column1,$footer_search1) {
              foreach ($tfoot_column1 as $key => $value) {
                  $query1->orWhere($value,'LIKE','%'.$footer_search1.'%');
                }
            });
        }
        //column2
        if($footer_search2){
            $tfoot_column2 = array('document_no');
            $query->where(function($query2) use($tfoot_column2,$footer_search2,$table) {
              foreach ($tfoot_column2 as $key => $value) {
                  $query2->orWhere($table.'.'.$value,'LIKE','%'.$footer_search2.'%');
                }
            });
        } 
        //column3
        if($footer_search3){
            $tfoot_column3 = array('document_name');
            $query->where(function($query3) use($tfoot_column3,$footer_search3,$table) {
              foreach ($tfoot_column3 as $key => $value) {
                  $query3->orWhere($table.'.'.$value,'LIKE','%'.$footer_search3.'%');
                }
            });
        }
        //column4
        if($footer_search4){
            $tfoot_column4 = array('department_name');
            $query->where(function($query4) use($tfoot_column4,$footer_search4) {
              foreach ($tfoot_column4 as $key => $value) {
                  $query4->orWhere($value,'LIKE','%'.$footer_search4.'%');
                }
            });
        } 
        //column5
        if($footer_search5){
            $tfoot_column5 = array('stack_name');
            $query->where(function($query5) use($tfoot_column5,$footer_search5) {
              foreach ($tfoot_column5 as $key => $value) {
                  $query5->orWhere($value,'LIKE','%'.$footer_search5.'%');
                }
            });
        }

        /*Footer search with date range start*/
       if($footer_search6) {
        $dates1 = explode("_",$footer_search6);
        $date1 = date('Y-m-d H:i:s', strtotime(current($dates1)));
        $date2 = date('Y-m-d H:i:s', strtotime(end($dates1)));
        $query->whereBetween($table.'.'.'created_at',array($date1,$date2));
       }
       if($footer_search7) {
        $dates2 = explode("_",$footer_search7);
        $d1 = date('Y-m-d H:i:s', strtotime(current($dates2)));
        $d2 = date('Y-m-d H:i:s', strtotime(end($dates2)));
        $query->whereBetween($table.'.'.'updated_at',array($d1,$d2));
       }

       /*Check out date sort [ Only in case of check out view ]*/
       if($footer_search8) {
         $dates3 = explode("_",$footer_search8);
         $checkoutDt1 = date('Y-m-d H:i:s', strtotime(current($dates3)));
         $checkoutDt2 = date('Y-m-d H:i:s', strtotime(end($dates3)));
         $query->whereBetween($table.'.'.'document_checkout_date',array($checkoutDt1,$checkoutDt2));
       }
       /*Footer search with date range ends*/
        // Ajax order by works
        $order = (isset($_POST['order'][0]['column']))?$_POST['order'][0]['column']:1;
        $direct = (isset($_POST['order'][0]['dir']))?$_POST['order'][0]['dir']:'DESC';
        $data_item = (isset($_POST['columns'][$order]['data']))?$_POST['columns'][$order]['data']:'';
        switch($data_item)
        {
          case 'document_type_id':
          $table_column = $table.'.'.'document_type_id';
          break;
          case 'document_no':
          $table_column = $table.'.'.'document_no';
          break;
          case 'document_name':
          $table_column = $table.'.'.'document_name';
          break;
          case 'department_id':
          $table_column = 'tbl_departments'.'.'.'department_name';
          break;
          case 'stack_id':
          $table_column = 'tbl_stacks'.'.'.'stack_name';
          break;
          case 'document_ownership':
          $table_column = $table.'.'.'document_ownership';
          break;
          case 'document_path':
          $table_column = $table.'.'.'document_path';
          break;
          case 'created_at':
          $table_column = $table.'.'.'created_at';
          break;
          case 'updated_at':
          $table_column = $table.'.'.'updated_at';
          break;
          case 'document_expiry_date':
          $table_column = $table.'.'.'document_expiry_date';
          break;
          case 'document_status':
          $table_column = $table.'.'.'document_status';
          break;
          case 'document_checkout_date':
          $table_column = $table.'.'.'document_checkout_date';
          break;
          case 'document_modified_by':
          $table_column = $table.'.'.'document_modified_by';
          break;
          default:
          $table_column = 'tbl_departments'.'.'.'department_name';
          break;
        }
        
            // Get data by department wise
            if(Auth::user()->user_role == Session::get("user_role_group_admin") || Auth::user()->user_role == Session::get("user_role_regular_user")) 
            {
                /*$count = count($loggedUsersdepIds);
                if($count == 1):
                $x=0;
                else:
                $x=1;
                endif; */
                /*
        
                foreach($loggedUsersdepIds as $depid):

                if($x == 1):
                    $query->WhereRaw('('.'FIND_IN_SET('.$depid.','.$table.'.department_id)');
                elseif($x == $count):
                    $query->WhereRaw('FIND_IN_SET('.$depid.','.$table.'.department_id)'.')');
                else:
                    $query->WhereRaw('FIND_IN_SET('.$depid.','.$table.'.department_id)');
                endif;
                $x++;
                endforeach;*/ 
                $query->whereIn($table.'.department_id',$loggedUsersdepIds);
            }
            //check user = private user, fetch only the docs of that user
            if(Auth::user()->user_role == Session::get("user_role_private_user"))
            {
                $query->where($table.'.'.'document_ownership',Auth::user()->username);
            }
            else//super admin
            {
                $query;
            }
            if($doclistid == '0')//stack and dept cases
            {
                switch($view)
                    {
                        case Lang::get('language.document_type_view'): //type
                        {
                        $query;
                        }
                        break;
                        case Lang::get('language.stack_view')://stack
                        {
                        $query->where($table.'.'.'stack_id',$curr_id);
                        }
                        break;
                        case Lang::get('language.department_view')://dept wise
                        {
                        $query->where($table.'.'.'department_id',$curr_id);
                        }
                        break;
                    }
                    //stack dept slecetd and also filter using radio button expire
                switch($filter)
                    {
                        
                        case Lang::get('language.exclude'): //docs not expired
                        {
                        $query->where($table.'.'.'document_expiry_date','>',date('Y-m-d'))->orWhere($table.'.'.'document_expiry_date','=',null);
                        }
                        break;
                        case Lang::get('language.expired')://expired docs only fetch
                        {
                        $query->where($table.'.'.'document_expiry_date','<=',date('Y-m-d'));
                        }
                        break;
                        case Lang::get('language.all')://all docs
                        {
                        $query;
                        //if a particular document selected(frm home page recent,not access)
                            if($docid != '' || $docid != null)
                            {
                                $query->where($table.'.'.'document_id',$docid);
                            }
                        }
                        break;
                        case Lang::get('language.expire_soon')://expire soon docs
                        {
                        $datetomarrow = new DateTime('tomorrow');
                        $query->where($table.'.'.'document_expiry_date','!=','null')->whereBetween($table.'.'.'document_expiry_date',[$datetomarrow->format('Y-m-d'),Session::get('expiry_date_from_settings')]);
                        }
                        break;
                        case Lang::get('language.assigned')://assigned docs only
                        {
                        $query->where($table.'.'.'document_status','Review')->where($table.'.'.'document_assigned_to',Auth::user()->username);
                        }
                        break;
                        case Lang::get('language.rejected')://rejected docs only
                        {
                        $query->where($table.'.'.'document_status','Rejected')->where($table.'.'.'document_created_by',Auth::user()->username);
                        }
                        break;
                        case Lang::get('language.accepted')://accepted docs only
                        {
                        $query->where($table.'.'.'document_status','Published')->where($table.'.'.'document_created_by',Auth::user()->username)->where($table.'.'.'document_assigned_to','!=',"");
                        }
                        break;
                        case Lang::get('language.assigned_to_whom')://assignedt to docs only
                        {
                        $query->where($table.'.'.'document_status','Review')
                        ->where($table.'.'.'document_created_by',Auth::user()->username)
                        ->where($table.'.'.'document_assigned_to','!=',"");
                        }
                        break;
                        case Lang::get('Level 1')://assignedt to docs only
                        {                      
                            $query->where('tbl_documents_columns.document_column_name','=',$filter)->where('tbl_documents_columns.document_column_value','=','yes');
                        }
                        break;
                        case Lang::get('Level 2')://assignedt to docs only
                        {                      
                            $query->where('tbl_documents_columns.document_column_name','=',$filter)->where('tbl_documents_columns.document_column_value','=','yes');
                        }
                        break;
                        case Lang::get('Level 3')://assignedt to docs only
                        {                      
                            $query->where('tbl_documents_columns.document_column_name','=',$filter)->where('tbl_documents_columns.document_column_value','=','yes');
                        }
                        break;
                        
                    }
            }
            else if($doclistid != '0')
            {
            //Filter according to view
                //filter according to radio button in listview(expiry)
                switch($filter)
                {
                    
                    case Lang::get('language.exclude'): //docs not expired
                    {
                    $query->where($table.'.'.'document_type_id',$doclistid)->where($table.'.'.'document_expiry_date','>',date('Y-m-d'))->orWhere($table.'.'.'document_expiry_date','=',null);
                    }
                    break;
                    case Lang::get('language.expired')://expired docs only fetch
                    {
                    $query->where($table.'.'.'document_expiry_date','<=',date('Y-m-d'));
                    }
                    break;
                    case Lang::get('language.all')://all docs
                    {
                    $query;
                    }
                    break;
                    case Lang::get('language.expire_soon')://expire soon docs
                    {
                    $datetomarrow = new DateTime('tomorrow');
                    $query->where($table.'.'.'document_type_id',$doclistid)->where($table.'.'.'document_expiry_date','!=','null')->whereBetween($table.'.'.'document_expiry_date',[$datetomarrow->format('Y-m-d'),Session::get('expiry_date_from_settings')]);
                    }
                    break;
                    case Lang::get('language.assigned')://assigned docs only
                    {
                    $query->where($table.'.'.'document_status','Review')->where($table.'.'.'document_assigned_to',Auth::user()->username);
                    }
                    break;
                    case Lang::get('language.rejected')://rejected docs only
                    {
                    $query->where($table.'.'.'document_status','Rejected')->where($table.'.'.'document_created_by',Auth::user()->username);
                    }
                    break;
                    case Lang::get('language.accepted')://accepted docs only
                    {
                    $query->where($table.'.'.'document_status','Published')->where($table.'.'.'document_created_by',Auth::user()->username)->where($table.'.'.'document_assigned_to','!=',"");
                    }
                    break;
                    case Lang::get('language.assigned_to_whom')://assignedt to docs only
                    {
                    $query->where($table.'.'.'document_status','Review')
                    ->where($table.'.'.'document_created_by',Auth::user()->username)
                    ->where($table.'.'.'document_assigned_to','!=',"");
                    }
                    break;
                    case Lang::get('Level 1')://assignedt to docs only
                        {                      
                            $query->where('tbl_documents_columns.document_column_name','=',$filter)->where('tbl_documents_columns.document_column_value','=','yes');
                        }
                        break;
                        case Lang::get('Level 2')://assignedt to docs only
                        {                      
                            $query->where('tbl_documents_columns.document_column_name','=',$filter)->where('tbl_documents_columns.document_column_value','=','yes');
                        }
                        break;
                        case Lang::get('Level 3')://assignedt to docs only
                        {                      
                            $query->where('tbl_documents_columns.document_column_name','=',$filter)->where('tbl_documents_columns.document_column_value','=','yes');
                        }
                        break;
                }
                //$table_type = 'tbl_document_types';
                $query->whereRaw('FIND_IN_SET('.$doclistid.','.$table.'.document_type_id)');
                
            }
            
            $query->orderBy("$table_column","$direct");
            $query->orderBy("$table.document_id",'DESC');
            $data['dglist'] = $query->selectRaw($select)->groupBy("$table.document_id")->paginate($length);

            //Expand columns
            foreach($data['dglist'] as $val)
            {   
                //To check document has workfowhistory
                @$val->hasWorkfowHistory = DB::table('tbl_workflow_histories')->where('document_workflow_object_id',$val->document_id)->exists();
                // Get documentTypes
                @$val->document_type = DB::table('tbl_document_types')->select(DB::raw('GROUP_CONCAT(tbl_document_types.document_type_name) AS document_type_name','document_type_column_no','document_type_column_name'))->whereIn('tbl_document_types.document_type_id',explode(',',$val->document_type_id))->first();
                // Get stack
                @$val->stacks = DB::table('tbl_stacks')->select(DB::raw('GROUP_CONCAT(tbl_stacks.stack_name) AS stack_name'))->whereIn('tbl_stacks.stack_id',explode(',',$val->stack_id))->first();
                // Get department
                @$val->department = DB::table('tbl_departments')->select(DB::raw('GROUP_CONCAT(tbl_departments.department_name) AS department_name'))->whereIn('tbl_departments.department_id',explode(',',$val->department_id))->first();  
            }
            $parent=array();
            Session::put('child','');
            foreach ($data['dglist'] as $key => $value) 
            {
            $parent=$value->parent_id;
            $data['level'][]=TreeStructModel::select('lvl')->where('id',$parent)->get();
            $this->printChild($parent);
            }
            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            $data['current_view'] = $view;
            return $data;
    }
    public function printChild($parentid) {
        $folder_name=TreeDataModel::select('nm')->where('id',$parentid)->get();
        Session::put('child',$folder_name->last()->nm);
        $result=TreeStructModel::select('pid')->where('id',$parentid)->get();
        $x = 0;
        if(($result[$x]->pid)!= 0) {
           $this->printChild($result[$x]->pid);
        }
    }
    // Get Doc Type Column names when click on document type
    public function getDocTypeColumn(){
        
        $DocumentTypesColumns = DB::table('tbl_document_types_columns')
        ->select('tbl_document_types_columns.document_type_column_id','tbl_document_types_columns.document_type_column_name','tbl_document_types_columns.document_type_column_type','tbl_advance_serach_demo.document_column_value')
        ->leftJoin('tbl_advance_serach_demo','tbl_advance_serach_demo.document_type_column_id','=','tbl_document_types_columns.document_type_column_id')
        ->whereIn('tbl_document_types_columns.document_type_id',explode(',',Input::get('ids')))
        ->orderBy('tbl_document_types_columns.document_type_id','ASC')
        ->orderBy('tbl_document_types_columns.document_type_column_order','ASC')
        ->get();
        
        $x=1;
        foreach($DocumentTypesColumns as $val):
            $previousValue = '';
            if($val->document_column_value):
                $previousValue = $val->document_column_value;
            else:
                $previousValue = '';
            endif;

            if($val->document_type_column_type == 'Date'){

                echo '<div class="col-sm-12"><label class="control-label">'.$val->document_type_column_name.':</label><div class="input-group"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="hidden" name="document_type_column_id[]" value="'.$val->document_type_column_id.'"><input class="form-control" id="'.$val->document_type_column_id.'"  title="'.$val->document_type_column_name.'" name="document_column_value[]" value="'.$previousValue.'" placeholder="YYYY-MM-DD" type="text"></div></div>';

            }else{
                echo '<div class="col-sm-12"><label class="control-label">'.$val->document_type_column_name.':</label><input type="hidden" name="document_type_column_id[]" value="'.$val->document_type_column_id.'"><input class="form-control" id="'.$val->document_type_column_id.'"  title="'.$val->document_type_column_name.'" placeholder="'.$val->document_type_column_name.'" name="document_column_value[]" value="'.$previousValue.'" type="text"></div>';
            }
            $x++;
            endforeach;
        exit;
    }

    //document history
    public function history($docid)
    { 
        if (Auth::user()) {
            if ($docid) {
                Session::put('selected_doc_list',$docid);
                $data['stckApp'] = $this->docObj->common_stack();
                $data['deptApp'] = $this->docObj->common_dept();
                $data['doctypeApp'] = $this->docObj->common_type();
                $data['records'] = $this->docObj->common_records();

                $dcmntname = DocumentsModel::select('document_name')->where('document_id', $docid)->get();
                $data['document_name']   = $dcmntname[0]->document_name;
                $data['docsList'] = DB::table('tbl_audits')->where('document_id',$docid)->orderBy('created_at', 'DESC')->get();
                return View::make('pages/documents/history')->with($data);
            } else {
                return redirect('users');
            }
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }


    public function getNote(){
        if (Auth::user()) {
            $docid   =     Input::get('docid');
            $data['noteList']= DocumentNoteModel::where('document_id', '=', $docid )->orderBy('created_at', 'desc')->get();
            return View::make('pages/documents/notelist')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }


    public function moreDetails()
    {
        if (Auth::user()) 
        {
            $docid = Input::get('docid');
            $view = Input::get('view');
            switch($view)
            {
                case Lang::get('language.list_view')://list view
                case Lang::get('language.document_type_view'): //type
                case Lang::get('language.stack_view')://stack
                case Lang::get('language.department_view')://dept wise                   
                   $table = 'tbl_documents';//tbl_documents
                   $column_table = 'tbl_documents_columns';//tbl_documents_columns
                   $note_table = 'tbl_document_notes';//tbl_temp_document_notes
                   $data = $this->more_details($docid,$table,$column_table,$note_table);
                break; 
                case Lang::get('language.import_view'): //import view
        
                   $table = 'tbl_temp_documents';//tbl_temp_documents
                   $column_table = 'tbl_temp_documents_columns';//tbl_temp_documents_columns
                   $note_table = 'tbl_temp_document_notes';//tbl_temp_document_notes
                   $data = $this->more_details($docid,$table,$column_table,$note_table);
                break;
                case Lang::get('language.checkout_view'): //check view        
                   $table = 'tbl_documents_checkout';//tbl_temp_documents
                   $column_table = 'tbl_documents_columns_checkout';//tbl_temp_documents_columns
                   $note_table = 'tbl_document_notes';//tbl_temp_document_notes
                   $data = $this->more_details($docid,$table,$column_table,$note_table);
                break;
            }
            
            return View::make('pages/documents/moredetailslist')->with($data);
        } 
        else
        {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function more_details($docid,$table,$column_table,$note_table)
    {
        $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
        $data['settings_document_no']   = $settings[0]->settings_document_no;
        $data['settings_document_name'] = $settings[0]->settings_document_name;

        $data['noteList']= DB::table($note_table)->where('document_id', '=', $docid )->orderBy('created_at', 'desc')->get();
        $data['dglist'] = DB::table($table)->select('*')->where('document_id','=',$docid)->get();
        // Expanding dglits with required datas
        foreach($data['dglist'] as $val):
            $val->document_type_columns = DB::table($column_table)->select('document_column_name','document_column_value','document_column_type')->where('document_id',$val->document_id)->get();
            // Get documentTypes
            $val->documentTypes_name = DB::table('tbl_document_types')->select(DB::raw('GROUP_CONCAT(document_type_name) AS document_type_names'))->whereIn('document_type_id',explode(',',$val->document_type_id))->get();

            $val->documentTypes = DocumentTypesModel::select('document_type_column_no','document_type_column_name')->where('document_type_id', $val->document_type_id)->get();


            // Get stack
            $val->stacks = DB::table('tbl_stacks')->select(DB::raw('GROUP_CONCAT(stack_name) AS stack_name'))->whereIn('stack_id',explode(',',$val->stack_id))->get();
            // Get Tag words
            $val->tagwords = DB::table('tbl_tagwords')->select(DB::raw('GROUP_CONCAT(tagwords_title) AS tagwords_title'))->whereIn('tagwords_id',explode(',',$val->document_tagwords))->get();
            // Get department
            $val->department = DB::table('tbl_departments')->select(DB::raw('GROUP_CONCAT(department_name) AS department_name'))->whereIn('department_id',explode(',',$val->department_id))->get();
            if(file_exists(config('app.base_path').$val->document_file_name)){

            $val->size = File::size(config('app.base_path').$val->document_file_name);
        	}
        endforeach;
        return $data;
    }
    public function moreDetailsPrevious(){
        if (Auth::user()) {
            $docid   =     Input::get('docid');
            $docversion = Input::get('version');
            $doc_history_id = Input::get('doc_history_id');
            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;

            $data['noteList']= DocumentNoteModel::where('document_id', '=', $docid )->orderBy('created_at', 'desc')->get();
            $data['dglist']   = DB::table('tbl_documents_history')->where('document_id','=',$docid)->where('document_history_id',$doc_history_id)->where('document_status','=','Checkin')->where('document_version_no',$docversion)->get();
            // Expanding dglist
            foreach($data['dglist'] as $val){
                $val->document_type_columns = DB::table('tbl_documents_history_columns')
                    ->Join('tbl_documents_history','tbl_documents_history.document_history_id','=','tbl_documents_history_columns.document_history_id')->select('tbl_documents_history_columns.document_column_name','tbl_documents_history_columns.document_column_value','tbl_documents_history_columns.document_column_type')->where('tbl_documents_history_columns.document_id',$docid)->where('document_version_no',$docversion)->where('tbl_documents_history.document_history_id',$doc_history_id)->get();
            
                // Get documentTypes
                $val->documentTypes_name = DB::table('tbl_document_types')->select(DB::raw('GROUP_CONCAT(document_type_name) AS document_type_names'))->whereIn('document_type_id',explode(',',$val->document_type_id))->get();

                $val->documentTypes = DocumentTypesModel::select('document_type_column_no','document_type_column_name')->where('document_type_id', $val->document_type_id)->get();

                // Get stack
                $val->stacks = DB::table('tbl_stacks')->select(DB::raw('GROUP_CONCAT(stack_name) AS stack_name'))->whereIn('stack_id',explode(',',$val->stack_id))->get();
                
                // Get department
                $val->department = DB::table('tbl_departments')->select(DB::raw('GROUP_CONCAT(department_name) AS department_name'))->whereIn('department_id',explode(',',$val->department_id))->get();
                if(file_exists(config('app.backup_path').$val->document_file_name)){

                $val->size = File::size(config('app.backup_path').$val->document_file_name);
            	}
            }
            return View::make('pages/documents/moredetailslist')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function moreDetails_relate(){
        if (Auth::user()) {
            $docid   =     Input::get('docid');

            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;

            $data['noteList']= DocumentNoteModel::where('document_id', '=', $docid )->orderBy('created_at', 'desc')->get();
            
            $data['dglist'] = DB::table('tbl_documents')->select('*')->where('document_id','=',$docid)->get();
            // Expanding dglits with required datas
            foreach($data['dglist'] as $val):
                $val->document_type_columns = DB::table('tbl_documents_columns')->select('document_column_name','document_column_value','document_column_type')->where('document_id',$val->document_id)->get();
            
                // Get documentTypes
                $val->documentTypes_name = DB::table('tbl_document_types')->select(DB::raw('GROUP_CONCAT(document_type_name) AS document_type_names'))->whereIn('document_type_id',explode(',',$val->document_type_id))->get();

                $val->documentTypes = DocumentTypesModel::select('document_type_column_no','document_type_column_name')->where('document_type_id', $val->document_type_id)->get();

                // Get stack
                $val->stacks = DB::table('tbl_stacks')->select(DB::raw('GROUP_CONCAT(stack_name) AS stack_name'))->whereIn('stack_id',explode(',',$val->stack_id))->get();
                // Get Tag words
                $val->tagwords = DB::table('tbl_tagwords')->select(DB::raw('GROUP_CONCAT(tagwords_title) AS tagwords_title'))->whereIn('tagwords_id',explode(',',$val->document_tagwords))->get();
                // Get department
                $val->department = DB::table('tbl_departments')->select(DB::raw('GROUP_CONCAT(department_name) AS department_name'))->whereIn('department_id',explode(',',$val->department_id))->get();
                if(file_exists(config('app.base_path').$val->document_file_name)){

                $val->size = File::size(config('app.base_path').$val->document_file_name);
            		}
            endforeach;

            return View::make('pages/documents/moredetailslist')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function getKeywords(){
        if (Auth::user()) {
            $keycatid   =     Input::get('keycatid');
            $keyid = "";
            if($keycatid>0){
                for($i=0;$i<count($keycatid);$i++){
                    $kid = $keycatid[$i];
                    if($i==0){
                        $keyid = $kid;
                    }else if($i==count($keycatid)-1){
                        $keyid = $keyid.','.$kid;
                    }else{
                        $keyid = $keyid.','.$kid;
                    }
                }
                $keycatid[] = $keyid;
                $data['keyWords']= KeywordsModel::whereIn('keywords_category_id', $keycatid)->get();
            }else{
                $data['keyWords']= KeywordsModel::whereIn('keywords_category_id', [0])->get();
            }
            return View::make('pages/documents/keywrdlist')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }


    public function documentsSubListSrch()
    { 
        if (Auth::user()) {
            $doctypeid =     Input::get('doctypeid');
            $data['documentTypeData'] = DocumentTypeColumnModel::where('document_type_id', $doctypeid)->orderby('document_type_id','ASC')->orderby('document_type_column_order','ASC')->get();
            return View::make('pages/documents/sublistsrch')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    public function getDocumentIndexFields()
    { 
        if (Auth::user()) {
            $doctypeid =     Input::get('doctypeid');
            $data = DocumentTypesModel::select('document_type_column_no','document_type_column_name')->where('document_type_id', $doctypeid)->get();
            $dataname['document_type_column_no'] = $data[0]->document_type_column_no;
            $dataname['document_type_column_name'] = $data[0]->document_type_column_name;
            return $dataname;
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }


    public function documentsSubList()
    {
        if (Auth::user()) {
            $doctypeid =     Input::get('doctypeid');
            $data['documentTypeData'] = DocumentTypeColumnModel::where('document_type_id',$doctypeid)->orderby('document_type_id','ASC')->orderby('document_type_column_order','ASC')->get();
            return View::make('pages/documents/sublist')->with($data);
        }
         else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function documentsSubListEdit()
    {
        if (Auth::user()) {
            $doctypeid =     Input::get('doctypeid');
            $dctpeid = "";
            $doc=Input::get('doc_id');
            $doctypeid = $dctpeid;
                $data['documentTypeData'] = DocumentTypeColumnModel::where('document_type_id', $doctypeid)->orderby('document_type_id','ASC')->orderby('document_type_column_order','ASC')->get();
                $data['fetch_doc_col'] =DB::table('tbl_temp_documents_columns')
                ->join('tbl_document_types_columns', 'tbl_temp_documents_columns.document_type_column_id', '=', 'tbl_document_types_columns.document_type_column_id')->select('tbl_temp_documents_columns.*', 'tbl_document_types_columns.*')->where('tbl_temp_documents_columns.document_id', $doc)
                ->orderby('tbl_document_types_columns.document_type_column_order')->get();
                //$data['fetch_doc_col']=TempDocumentsColumnModel::where('document_id', $doc)->get();
                $count_fetch=count($data['fetch_doc_col']);
                if($count_fetch==0)
                {
                    return $this->documentsSubList();
                }
            return View::make('pages/documents/sublist_edit')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function documentfileSubListEdit()
    {
        if (Auth::user()) {
            $doctypeid =     Input::get('doctypeid');
            $dctpeid = "";
            $doc=Input::get('doc_id');
            if($doctypeid>0){
                for($i=0;$i<count($doctypeid);$i++){
                    $cid = $doctypeid[$i];
                    if($i==0){
                        $dctpeid = $cid;
                    }else if($i==count($doctypeid)-1){
                        $dctpeid = $dctpeid.','.$cid;
                    }else{
                        $dctpeid = $dctpeid.','.$cid;
                    }
                }
                $doctypeid[] = $dctpeid;
                $data['documentTypeData'] = DocumentTypeColumnModel::whereIn('document_type_id', $doctypeid)->orderby('document_type_column_order')->get();
                //$data['fetch_doc_col']=DocumentsColumnModel::where('document_id', $doc)->get();
                $data['fetch_doc_col'] =DB::table('tbl_documents_columns')
                ->join('tbl_document_types_columns', 'tbl_documents_columns.document_type_column_id', '=', 'tbl_document_types_columns.document_type_column_id')->select('tbl_documents_columns.*', 'tbl_document_types_columns.*')->where('tbl_documents_columns.document_id', $doc)
                ->orderby('tbl_document_types_columns.document_type_column_order')->get();
                $count_fetch=count($data['fetch_doc_col']);
                if($count_fetch==0)
                {
                    return $this->documentsSubList();
                }
            }else{
                $data['documentTypeData'] = DocumentTypeColumnModel::whereIn('document_type_id', [0])->orderby('document_type_column_order')->get();
                
            }
            return View::make('pages/documents/sublist_edit')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
        
    public function saveNote(){   
        if (Auth::user()) {
            $data['view']=Input::get('action');
            $documentNteModl                  =   new DocumentNoteModel;
            $documentNteModl->document_id     =   Input::get('docmntid');
            $documentNteModl->document_note   =   Input::get('noteval');
            $documentNteModl->document_note_created_by  =   Auth::user()->username;
            if ($documentNteModl->save()) {
                $data['dglist']   = DB::table('tbl_documents')->where('document_id',Input::get('docmntid'))->get();
                $data['noteList']= DocumentNoteModel::where('document_id', '=', Input::get('docmntid'))->orderBy('created_at', 'DESC')->get();
            }else{
                echo 'Sorry,something went wrong. note saving failed.';exit;
            }
            return View::make('pages/documents/note_refresh_view')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    public function saveNotePopup(){   
        if (Auth::user()) {
            $data['view']=Input::get('action');
            $documentNteModl                  =   new DocumentNoteModel;
            $documentNteModl->document_id     =   Input::get('docmntid');
            $documentNteModl->document_note   =   Input::get('noteval');
            $documentNteModl->document_note_created_by  =   Auth::user()->username;
            if ($documentNteModl->save()) {
                echo "success";
            }else{
                echo 'Sorry,something went wrong. note saving failed.';exit;
            }
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    public function tempsaveNote(){   
        if (Auth::user()) {
            $data['view']=Input::get('action');
            $documentNteModl                  =   new TempDocumentNoteModel;
            $documentNteModl->document_id     =   Input::get('docmntid');
            $documentNteModl->document_note   =   Input::get('noteval');
            $documentNteModl->document_note_created_by  =   Auth::user()->username;
            if ($documentNteModl->save()) {
                $data['dglist']   = DB::table('tbl_temp_documents')->where('document_id',Input::get('docmntid'))->get();
                $data['noteList']= TempDocumentNoteModel::where('document_id', '=', Input::get('docmntid'))->orderBy('created_at', 'DESC')->get();
            }else{
                echo 'Sorry,something went wrong. note saving failed.';exit;
            }
            return View::make('pages/documents/note_refresh_view')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    /*<--Rezise image-->*/
    public function resizeImage($imageName){ 

        $filename  = config('app.base_path')."$imageName";// Image from path
        
        $saveto    = public_path('images/test/'.$imageName); // Same path
        // Check file already has
        if(file_exists($saveto) == NULL){  
            // Create image
            // Get new sizes
            list($width, $height) = getimagesize($filename);

            switch(true){
                case $width >= 2000:
                        $percent = 0.2;// width = 3808
                        break;
                case $width >= 1920:
                        $percent = 0.6;// width = 1920
                        break;
                default :
                        $percent = 0.7;
                        break;
            }
           
            $newwidth = $width * $percent;
            $newheight = $height * $percent;

            // If width more tha 850 then resize it
            if(($width > 850) || ($height > 1000) ):

                $info = getimagesize($filename);
                $mime = $info['mime'];

                 switch ($mime) {
                        case 'image/jpeg':
                                $image_create_func = 'imagecreatefromjpeg';
                                $image_save_func = 'imagejpeg';
                                $new_image_ext = 'jpg';
                                break;

                        case 'image/png':
                                $image_create_func = 'imagecreatefrompng';
                                $image_save_func = 'imagepng';
                                $new_image_ext = 'png';
                                break;

                        case 'image/gif':
                                $image_create_func = 'imagecreatefromgif';
                                $image_save_func = 'imagegif';
                                $new_image_ext = 'gif';
                                break;

                        default: 
                                $image_create_func = NULL;
                }


               if($image_create_func):
                    // Load
                    $thumb = imagecreatetruecolor($newwidth, $newheight);
                    $source = $image_create_func($filename);
                    // Resize
                    imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                    // Output
                    imagejpeg($thumb,$saveto);
                endif;

            endif;
           
        }
                
    }/*<--//Rezise image-->*/
    public function cadView()
    {
        $data['file'] = Input::get('file');
        // echo $data['file'];
        // exit();
        return View::make('pages/documents/cad_fileviewer')->with($data);
    }

    public function fileView()
    {
        if (Auth::user()) {
            //check the view page
            $page = Input::get('page');
            $id=Input::get('dcno');
            //session for highlight the opendoc on list page view
            Session::put('selected_doc_list',$id);
            $open_doc = Input::get('id');
            if($open_doc)
            {
                Session::put('open_doc_no',$open_doc);
            }
            else
            {
                Session::put('open_doc_no',1);
            }
            $data['retrnid'] = Input::get('id');
            if(Input::get('id')){
                Session::put('dsd_id',Input::get('id'));
            }
            $data['id']=$id;
            $view=Input::get('view');
            $data['view']=$view;
            Session::put('menuid', '0');
            // For set side bar menu as active in Document Type,Stack And Department.
            if(@$_GET['dtl']):
                Session::put('menuid','2');
            elseif(@$_GET['sl']):
                Session::put('menuid','7');
            elseif(@$_GET['dl']):
                Session::put('menuid','3');
            endif;
            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            $data['page'] = $page;
        if($page == 'list' || $page == 'document' || $page == 'chkoutlist' || $page == 'content' || $page == 'department' || $page == 'stack' || $page == 'documentType' || $page == 'checkout')
        {
           if((Input::get('dcno'))==0 && (Input::get('id')==0) )
            {
                $name=Input::get('name');
                $rowid=0;
                $id=DB::table('tbl_documents')->select('document_id')->where('document_file_name',$name)->get();
                foreach ($id as $key) {
                    $id= $key->document_id;
                }
            }
            else
            {
                $id = Input::get('dcno');
                $rowid = Input::get('id');
                //check the file is encrypted
                $encrypt_details = DB::table('tbl_documents')
                ->select('document_encrypt_status',
                    'document_encrypted_by',
                    'document_encrypted_on',
                    'document_name',
                    'document_file_name',
                    'document_id')
                ->where('document_id',$id)->first();
            }
            //annotation part
            $file_be_to_note =DB::table('tbl_documents')->select('document_file_name')->where('document_id',$id)->get();
            if($file_be_to_note){
                foreach ($file_be_to_note as $key) {
                    $file_noted = $key->document_file_name;
                }
            //file name save in session for annotate on fileviewer view page    
            Session::put('sess_file_to_annotate',$file_noted);
            }
            $data['fileName'] = DocumentsModel::select('document_path')->where('document_id', '=', $id)->first();
            
            $data['dglist']   = DB::table('tbl_documents')->where('document_id',$id)->get();
            // print_r($data['dglist']);
            // exit();
            if(!$data['dglist'])
            {
                return response()->view('404_error',$data,404);
            }
            //<!--Resize image for annotation and reduce large size-->
            $ext = pathinfo(@$data['dglist'][0]->document_file_name, PATHINFO_EXTENSION);
            $ext = strtolower($ext);
            if($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif'){
                $this->resizeImage(@$data['dglist'][0]->document_file_name);// Passing image name to resize
            }
            //<!--//Resize image -->

            // Expanding dglist
            foreach($data['dglist'] as $val):
                $val->document_type_columns = DB::table('tbl_documents_columns')->select('document_column_name','document_column_value')->where('document_id',$val->document_id)->get();
                // Get documentTypes
                //$val->documentTypes = DB::table('tbl_document_types')->select(DB::raw('GROUP_CONCAT(document_type_name) AS document_type_names','document_type_column_no','document_type_column_name'))->whereIn('document_type_id',explode(',',$val->document_type_id))->get();
                // Get documentTypes
                $val->documentTypes_name = DB::table('tbl_document_types')->select(DB::raw('GROUP_CONCAT(document_type_name) AS document_type_names'))->whereIn('document_type_id',explode(',',$val->document_type_id))->get();

                $val->documentTypes = DocumentTypesModel::select('document_type_column_no','document_type_column_name')->where('document_type_id', $val->document_type_id)->get();

                // Get stack
                $val->stacks = DB::table('tbl_stacks')->select(DB::raw('GROUP_CONCAT(stack_name) AS stack_name'))->whereIn('stack_id',explode(',',$val->stack_id))->get();
                // Get Tag words
                $val->tagwords = DB::table('tbl_tagwords')->select(DB::raw('GROUP_CONCAT(tagwords_title) AS tagwords_title'))->whereIn('tagwords_id',explode(',',$val->document_tagwords))->get();
                // Get department
                $val->department = DB::table('tbl_departments')->select(DB::raw('GROUP_CONCAT(department_name) AS department_name'))->whereIn('department_id',explode(',',$val->department_id))->get();
                if(file_exists(config('app.base_path').$val->document_file_name)){

                $val->size = File::size(config('app.base_path').$val->document_file_name);
                    }
            endforeach;

            Session::put('sess_docview_id',$id);
            Session::put('sess_row_id',$rowid);
            //get stack
            $stack = DocumentsModel::select('stack_id')->where('document_id', '=', $id)->first();
            $stackstr = @$stack->stack_id;
            $stckarray = explode(',' , $stackstr);
            $data['stcklist'] = StacksModel::whereIn('stack_id', $stckarray)->get();

            $branch = DocumentsModel::select('document_type_id')
            ->where('document_id', '=', $id)
            ->first();
            $str = @$branch->document_type_id;
            $array = explode(',' , $str);
            $data['documentType'] = DocumentTypesModel::whereIn('document_type_id', $array)->where('is_app',0)->get();
            
            $data['noteList']= DocumentNoteModel::where('document_id', '=', $id )
            ->orderBy('created_at', 'desc')->get();
            $data['preVer']= DocumentHistoryModel::where('document_id', '=', $id )->orderBy('created_at', 'DESC')->get();
            $data['evntLog']= AuditsModel::where('document_id', '=', $id )->orderBy('created_at','DESC')->get();
            //action open save to audits 
            $user = Auth::user()->username;
            
            $action_desc = "Document $val->document_file_name opened by $user";
            DB::table('tbl_audits')->insert(['document_id'=>$val->document_id,'document_name'=>$val->document_name,'document_no'=>$val->document_no,'audit_owner'=>'Document','audit_user_name'=>Auth::user()->username,'audit_action_type'=>'Open','audit_action_desc'=>$action_desc,'created_at'=>date('Y-m-d h:i:s')]);

            if(@$encrypt_details->document_encrypt_status == 1)
                {
                    $data['document_id'] = @$encrypt_details->document_id;
                    $data['file_name'] = @$encrypt_details->document_file_name;
                    $data['document_name'] = @$encrypt_details->document_name;
                    $data['encrypted_by'] = @$encrypt_details->document_encrypted_by;
                    $data['encrypted_on'] = @$encrypt_details->document_encrypted_on;
                    return View::make('pages/documents/fileview_intermediate')->with(@$data);
                }
            else
                {
                    return View::make('pages/documents/fileviewer')->with($data);
                }
        }
        else if($page == 'import')
        {
            $id = Input::get('dcno');
            //$rowid = Input::get('id');
            $data['fileName'] = TempDocumentsModel::select('document_path')->where('document_id', '=', $id)->first();
            //annotation part
            $upload_file_be_to_note =DB::table('tbl_temp_documents')->select('document_file_name')->where('document_id',$id)->get();
                foreach ($upload_file_be_to_note as $key) {
                    $upload_file_noted = $key->document_file_name;
                }
            //file name save in session for annotate on fileviewer_upload view page    
            Session::put('sess_file_to_annotate',$upload_file_noted);
            $data['dglist']   = DB::table('tbl_temp_documents')->where('document_id',$id)->get();
            if(!$data['dglist'])
            {
                return response()->view('404_error',$data,404);
            }
            // Expanding dglist
            foreach($data['dglist'] as $val):
                $val->document_type_columns = DB::table('tbl_temp_documents_columns')->select('document_column_name','document_column_value')->where('document_id',$val->document_id)->get();
                // Get documentTypes
                //$val->documentTypes = DB::table('tbl_document_types')->select(DB::raw('GROUP_CONCAT(document_type_name) AS document_type_names','document_type_column_no','document_type_column_name'))->whereIn('document_type_id',explode(',',$val->document_type_id))->get();

                $val->documentTypes = DocumentTypesModel::select('document_type_column_no','document_type_column_name')->where('document_type_id', $val->document_type_id)->get();

                // Get stack
                $val->stacks = DB::table('tbl_stacks')->select(DB::raw('GROUP_CONCAT(stack_name) AS stack_name'))->whereIn('stack_id',explode(',',$val->stack_id))->get();
                // Get Tag words
                $val->tagwords = DB::table('tbl_tagwords')->select(DB::raw('GROUP_CONCAT(tagwords_title) AS tagwords_title'))->whereIn('tagwords_id',explode(',',$val->document_tagwords))->get();
                // Get department
                $val->department = DB::table('tbl_departments')->select(DB::raw('GROUP_CONCAT(department_name) AS department_name'))->whereIn('department_id',explode(',',$val->department_id))->get();
                if(file_exists(config('app.base_path').$val->document_file_name)){

                $val->size = File::size(config('app.base_path').$val->document_file_name);
                }
            endforeach;
        
            Session::put('sess_docview_id',$id);
            //get stack
            $stack = TempDocumentsModel::select('stack_id')->where('document_id', '=', $id)->first();
            $stackstr = $stack->stack_id;
            $stckarray = explode(',' , $stackstr);
            $data['stcklist'] = StacksModel::whereIn('stack_id', $stckarray)->get();

            $branch = TempDocumentsModel::select('document_type_id')
            ->where('document_id', '=', $id)
            ->first();
            $str = $branch->document_type_id;
            $array = explode(',' , $str);
            $data['documentType'] = DocumentTypesModel::whereIn('document_type_id', $array)->where('is_app',0)->get();
            
            $data['noteList']= DocumentNoteModel::where('document_id', '=', $id )
            ->orderBy('created_at', 'desc')->get();
            $data['preVer']= DocumentHistoryModel::where('document_id', '=', $id )->orderBy('created_at', 'DESC')->get();
            $data['evntLog']= AuditsModel::where('document_id', '=', $id )->get();
        }
        else if($page == 'history')
        {
            $id = Input::get('dcno');
            $rowid = Input::get('id');
            $history_id = Input::get('hist');
            $docversion = Input::get('ver');
            $data['fileName'] = DocumentHistoryModel::select('document_path')->where('document_id', '=', $id)->first();
            //annotation part
            $history_file_be_to_note =DB::table('tbl_documents_history')->where('document_id','=',$id)->where('document_history_id',$history_id)->where('document_status','=','Checkin')->get();
                foreach ($history_file_be_to_note as $key) {
                    @$history_file_noted = @$key->document_file_name;
                }
            //file name save in session for annotate on fileviewer_upload view page    
            Session::put('sess_file_to_annotate',@$history_file_noted);
            $data['dglist']   = DB::table('tbl_documents_history')->where('document_id','=',$id)->where('document_history_id',$history_id)->where('document_status','=','Checkin')->get();
            if(!$data['dglist'])
            {
                return response()->view('404_error',$data,404);
            }
            // Expanding dglist
            foreach($data['dglist'] as $val):
                $val->document_type_columns = DB::table('tbl_documents_history_columns')
                    ->Join('tbl_documents_history','tbl_documents_history.document_history_id','=','tbl_documents_history_columns.document_history_id')->select('tbl_documents_history_columns.document_column_name','tbl_documents_history_columns.document_column_value')->where('tbl_documents_history_columns.document_id',$id)->where('document_version_no',$docversion)->where('tbl_documents_history.document_history_id',$history_id)->get();
                // Get documentTypes
                //$val->documentTypes = DB::table('tbl_document_types')->select(DB::raw('GROUP_CONCAT(document_type_name) AS document_type_names','document_type_column_no','document_type_column_name'))->whereIn('document_type_id',explode(',',$val->document_type_id))->get();

                $val->documentTypes = DocumentTypesModel::select('document_type_column_no','document_type_column_name')->where('document_type_id', $val->document_type_id)->get();

                // Get stack
                $val->stacks = DB::table('tbl_stacks')->select(DB::raw('GROUP_CONCAT(stack_name) AS stack_name'))->whereIn('stack_id',explode(',',$val->stack_id))->get();
                // Get department
                $val->department = DB::table('tbl_departments')->select(DB::raw('GROUP_CONCAT(department_name) AS department_name'))->whereIn('department_id',explode(',',$val->department_id))->get();
                if(file_exists(config('app.backup_path').$val->document_file_name)){

                $val->size = File::size(config('app.backup_path').$val->document_file_name);
                }
            endforeach;
            Session::put('sess_docview_id',$id);
            Session::put('sess_row_id',$rowid);
            //get stack
            $stack = DocumentHistoryModel::select('stack_id')->where('document_id', '=', $id)->first();
            $stackstr = $stack->stack_id;
            $stckarray = explode(',' , $stackstr);
            $data['stcklist'] = StacksModel::whereIn('stack_id', $stckarray)->get();

            $branch = DocumentHistoryModel::select('document_type_id')
            ->where('document_id', '=', $id)
            ->first();
            $str = $branch->document_type_id;
            $array = explode(',' , $str);
            $data['documentType'] = DocumentTypesModel::whereIn('document_type_id', $array)->where('is_app',0)->get();
            
            $data['noteList']= DocumentNoteModel::where('document_id', '=', $id )
            ->orderBy('created_at', 'desc')->get();
            $data['preVer']= DocumentHistoryModel::where('document_id', '=', $id)->where('document_version_no','<',$docversion)->orderBy('created_at', 'DESC')->get();
            $data['evntLog']= AuditsModel::where('document_id', '=', $id )->get();
            //action open save to audits 
            $name = $val->document_file_name;
            $user = Auth::user()->username;
            $actionDes = "Document $name opened by $user";
            DB::table('tbl_audits')->insert(['document_id'=>$val->document_id,'document_name'=>$val->document_name,'document_no'=>$val->document_no,'audit_owner'=>'Document','audit_user_name'=>Auth::user()->username,'audit_action_type'=>'Open','audit_action_desc'=>$actionDes,'created_at'=>date('Y-m-d h:i:s')]);
        }
        else if($page == 'forms')
        {
            $name = Input::get('dcno');
            $data['fileName'] = $name;
            $data['dglist'] = array();
            $clasa   = array('document_file_name'=>$name,'document_status'=>'published');
            $data['dglists'] = json_decode(json_encode($clasa));
            $data['noteList'] = array();
            $data['preVer']= array();
            $data['evntLog']= array();
            array_push($data['dglist'], $data['dglists']);

        }
        return View::make('pages/documents/fileviewer')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function fileView1()
    {
        if (Auth::user()) 
        {   
            $data['docid'] = Input::get('dcno');
            if(Input::get('dcno')){
                $data['bookmarklist'] = DB::table('tbl_document_bookmarks')->select('document_id','document_bookmark_id','document_bookmark')->where('document_id',Input::get('dcno'))->get();
            }
            //print($data['bookmarklist']);
            return View::make('pages/documents/viewer')->with($data);
        } 
        else 
        {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function add()
    {   
        if (Auth::user()) {
            $this->docObj->document_assign_notification();
            $this->docObj->document_reject_notification();
            $this->docObj->document_accept_notification();
            Session::put('menuid', '0');
            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name','settings_ftp')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name; 
            $data['stack']      = StacksModel::all();
            $data['tagsCateg']  = TagWordsCategoryModel::all();
            $data['docType']    = $this->docObj->common_type();
            
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            $data['noteList']= DocumentNoteModel::where('document_id', '=', 0 )
            ->orderBy('created_at', 'desc')->get();
            Session::put('settings_ftp_upload',$settings[0]->settings_ftp);
            // list users
            switch(Auth::user()->user_role)
            {
            //superadmin -> all users are list
            case Session::get("user_role_super_admin"):
            $users = DB::table('tbl_users')->select('*')->where('id','!=',Auth::user()->id)->get();
            foreach($users as $val):
                $department_ids =  $val->department_id;
                $department_ids =  explode(',',$department_ids);
                $departments = DB::table('tbl_departments')->whereIn('department_id',$department_ids)->select(DB::raw('group_concat(department_name) as department_name'))->get(); 
                $val->departments = $departments;
            endforeach; 
            $data['users'] = $users;
            break;
            //group admin or regular user or private user-> deptvise
            case Session::get("user_role_group_admin"):
            case Session::get("user_role_regular_user"):
            case Session::get("user_role_private_user"):
            $data['users']=DB::table('tbl_users')->leftJoin('tbl_users_departments', 'tbl_users.id', '=', 'tbl_users_departments.users_id')->whereIn('tbl_users_departments.department_id',Session::get('auth_user_dep_ids'))->select('tbl_users.username as username')->where('tbl_users.id','!=',Auth::user()->id)->groupBy('tbl_users.id')->get();
            break;
            }

            return View::make('pages/documents/add')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function edituploadfile()
    {
        $id=Input::get('id');
        Session::forget('ancestor_import');
        if (Auth::user()) {
            
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();

            switch(Auth::user()->user_role)
            {
            //superadmin -> all users are list
            case Session::get("user_role_super_admin"):
            $users = DB::table('tbl_users')->select('*')->where('id','!=',Auth::user()->id)->get();
            foreach($users as $val):
                $department_ids =  $val->department_id;
                $department_ids =  explode(',',$department_ids);
                $departments = DB::table('tbl_departments')->whereIn('department_id',$department_ids)->select(DB::raw('group_concat(department_name) as department_name'))->get(); 
                $val->departments = $departments;
            endforeach; 
            $data['users'] = $users;
            break;
            //group admin or regular user or private user-> deptvise
            case Session::get("user_role_group_admin"):
            case Session::get("user_role_regular_user"):
            case Session::get("user_role_private_user"):
            $data['users']=DB::table('tbl_users')->leftJoin('tbl_users_departments', 'tbl_users.id', '=', 'tbl_users_departments.users_id')->whereIn('tbl_users_departments.department_id',Session::get('auth_user_dep_ids'))->select('tbl_users.username as username')->where('tbl_users.id','!=',Auth::user()->id)->groupBy('tbl_users.id')->get();
            break;
            }
        
            $data['stacks'] = StacksModel::all();
            $data['tagsCateg'] = TagWordsCategoryModel::all();
            $data['docType'] = DocumentTypesModel::where('is_app',0)->get();
            $data['dglist'] = TempDocumentsModel::where('document_id',$id)->get();
            $this->ancestor_import($data['dglist'][0]->parent_id);
            $tag = TempDocumentsModel::select('document_tagwords')->where('document_id', '=', $id)->first();
            $tagstr = $tag->document_tagwords;
            $tagarray = explode(',' , $tagstr);
            $data['taglist'] = TagWordsModel::select('tagwords_category_id')->whereIn('tagWords_id', $tagarray)->get();
            $data['raw_count']=1;
            $data['id']=$id;

            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;
            $data['noteList']= TempDocumentNoteModel::where('document_id', '=', $id )
            ->orderBy('created_at', 'desc')->get();
            $path_array=Session::get('ancestor_import');
            $path_array_rev=array_reverse($path_array);
            $path=implode('/',$path_array_rev);
            foreach ($data['dglist'] as $key => $value) {
                $value->document_path=$path;
            }
            return View::make('pages/documents/editdocument')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function ancestor_import($parentid) {
        $folder_name=TreeDataModel::select('nm')->where('id',$parentid)->get();
        Session::push('ancestor_import',$folder_name->last()->nm);
        $result=TreeStructModel::select('pid')->where('id',$parentid)->get();
        $x = 0;
        if(($result[$x]->pid)!= 0) {
           $this->ancestor_import($result[$x]->pid);
        }
    }
    public function editDocumentfile()
    {
        $id=Input::get('id');
        if (Auth::user()) {
            
                $data['stckApp'] = $this->docObj->common_stack();
                $data['deptApp'] = $this->docObj->common_dept();
                $data['doctypeApp'] = $this->docObj->common_type();
                $data['records'] = $this->docObj->common_records();
            $data['stack'] = StacksModel::all();
            $data['tagsCateg'] = TagWordsCategoryModel::all();
            $data['docType'] = DocumentTypesModel::where('is_app',0)->get();
            $data['dglist'] = DocumentsModel::where('document_id',$id)->get();
            $tag = DocumentsModel::select('document_tagwords')->where('document_id', '=', $id)->first();
            $tagstr = $tag->document_tagwords;
            $tagarray = explode(',' , $tagstr);
            $data['taglist'] = TagWordsModel::select('tagwords_category_id')->whereIn('tagWords_id', $tagarray)->get();
            $data['raw_count']=1;
            $data['id']=$id;
            return View::make('pages/documents/edit')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function edituploadfilesall(){
        $values = Input::get('checkbox');
        $count_raw=Input::get('hidd_count');
        $files=Input::get('hidd_type');
        $view=Input::get('hidd_view');
        if (Auth::user()) {
            $data['stack'] = StacksModel::all();
            $data['tagsCateg'] = TagWordsCategoryModel::all();
            $data['docType'] = DocumentTypesModel::where('is_app',0)->get();
            $data['raw_count']=$count_raw;
            $data['type']=$files;
            $data['view']=$view;
            
                $data['stckApp'] = $this->docObj->common_stack();
                $data['deptApp'] = $this->docObj->common_dept();
                $data['doctypeApp'] = $this->docObj->common_type();
                $data['records'] = $this->docObj->common_records();        // save in aduites
            //(new AuditsController)->dcmntslog(Auth::user()->username, 'Import Document', 'Edit', 'Document:'.$documenttypeid);

            return View::make('pages/documents/editallupload')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    //advance search onload 
    public function advancesearch($name='')
    {  
        if (Auth::user()) {
            $data['name'] = $name;
            if($name == null){
                // Distroy session in search list
                Session::forget('search_documentsIds');
                Session::forget('search_list_exists');
                Session::forget('serach_doc_no');
                Session::forget('search_docname');
                Session::forget('coltypecnt');
                Session::forget('search_keywrd_srchtxt');
                Session::forget('search_option');
                Session::forget('search_ownership');
                Session::forget('search_created_by');
                Session::forget('search_updated_by');
                Session::forget('updated_by_owner_ids');
                Session::forget('created_by_owner_ids');
                Session::forget('owner_ids');
                Session::forget('search_departments');
                Session::forget('departments');
                Session::forget('search_document_type_name');
                Session::forget('doctypeids');
                Session::forget('doctypeid');
                Session::forget('search_stack');
                Session::forget('stackids');
                Session::forget('search_created_date_from');
                Session::forget('search_created_date_to');
                Session::forget('search_last_modified_from');
                Session::forget('search_last_modified_to');
                Session::forget('document_column_name');
                Session::forget('document_column_value');
                Session::forget('document_type_column_id');    
                Session::forget('documentColNam');
                Session::forget('search_criteria_id'); 
                Session::forget('search_criteria_name');

                Session::forget('form_content_search');
                Session::forget('form_content_search_comb');
                Session::forget('form_content_search_attach');
                Session::forget('form_search_form_name');
                Session::forget('form_search_assigned_to');
                Session::forget('form_search_workflow_id');
                Session::forget('form_search_activity_id');
                Session::forget('form_search_created_by');
                Session::forget('form_search_submitted_date_from');
                Session::forget('form_search_submitted_date_to');
                Session::forget('form_search_submitted_date_from');
                Session::forget('form_search_option');
            }else{
                // Select tags
                if(session('tagscate')){
                    $tbl_tagwords = DB::table('tbl_tagwords')->select('tagwords_id','tagwords_title')->whereIn('tagwords_category_id',session('tagscate'))->get();
                    if($tbl_tagwords)
                        $data['catTags'] = $tbl_tagwords;
                }
            }
            
            if(@$_GET['page'] == 'documents'):
                Session::put('page_doc','documents');
                Session::forget('page_doc_view');

                // empty the demo table
                DB::table('tbl_advance_serach_demo')->truncate();

            elseif(@$_GET['page'] == 'documentsListview'):
                Session::put('page_doc_view','documentsList');
                Session::forget('page_doc');
            endif;
            
            if(@$_GET['page'] == 'documentsAdvSearch')
                DB::table('tbl_advance_serach_demo')->truncate();

            
                $data['stckApp'] = $this->docObj->common_stack();
                $data['deptApp'] = $this->docObj->common_dept();
                $data['doctypeApp'] = $this->docObj->common_type();
                $data['records'] = $this->docObj->common_records();            $data['serach_criteria'] = DB::table('tbl_search_criteria')->select('search_criteria_id','criteria_name')->where('user_id',Auth::user()->id)->get();// search criteria
            $data['records']         = DB::table('tbl_settings')->first();
            Session::put('menuid', '0');
            $data['tagsCateg']       =      TagWordsCategoryModel::all();
            $data['stacks']          =      StacksModel::all();
            $data['docType']         =      DocumentTypesModel::where('is_app',0)->get();
            $data['tagCat']          =      TagWordsCategoryModel::all();
            $data['settingsDetails'] =      SettingsModel::getSettingsDetails();//Settings details
            $data['sess_dept']       =      Session::get('sess_dept');
            $data['sess_doctypeid']  =      Session::get('sess_doctypeid');
            $data['sess_owner']      =      Session::get('sess_owner');
            $data['sess_stacks']     =      Session::get('sess_stacks');
            $data['sess_doctypecol'] =      Session::get('sess_doctypecol');
            $user                    =      Users::select('username','id')->get();
            $doctypeid               =      Session::get('sess_settype');

            // Listing department according to the users department ids list
            $loggedUsersdepIds = explode(',',Auth::user()->department_id);
            if(Auth::user()->user_role == Session::get('user_role_super_admin')){
                $result = DepartmentsModel::select('department_name','department_id')->get();
            }elseif(Auth::user()->user_role == Session::get('user_role_group_admin')){
                $result = DepartmentsModel::select('department_name','department_id')->whereIn('department_id',$loggedUsersdepIds)->get();
            }elseif(Auth::user()->user_role == Session::get('user_role_regular_user')){
                $result = DepartmentsModel::select('department_name','department_id')->whereIn('department_id',$loggedUsersdepIds)->get();
            }elseif(Auth::user()->user_role == Session::get('user_role_private_user')){
                $result = DepartmentsModel::select('department_name','department_id')->whereIn('department_id',$loggedUsersdepIds)->get();
            }


            // Previousily searched criteria
            if(Input::get('searchCriteriaId')){
                $value = DB::table('tbl_search_criteria')->select('tbl_search_criteria.*',DB::RAW('GROUP_CONCAT(tbl_search_criteria_multiple_document_types_columns.document_type_column_name) AS document_type_column_name'),DB::RAW('GROUP_CONCAT(tbl_search_criteria_multiple_document_types_columns.document_type_column_value) AS document_type_column_value'))->where('tbl_search_criteria.search_criteria_id',Input::get('searchCriteriaId'))->leftJoin('tbl_search_criteria_multiple_document_types_columns','tbl_search_criteria_multiple_document_types_columns.search_criteria_id','=','tbl_search_criteria.search_criteria_id')->get(); 
                
                // Change search criteria values according to the id               
                Session::put('search_criteria_id', Input::get('searchCriteriaId'));
                Session::put('serach_doc_no', $value[0]->docno);
                Session::put('departments', explode(',',$value[0]->department_id));  
                Session::put('search_docname', $value[0]->document_name);  
                Session::put('search_option', $value[0]->search_option); 
                Session::put('stackids', explode(',',$value[0]->stack_id));  
                Session::put('tagscate', explode(',',$value[0]->tagwords_category_id));  
                Session::put('keywords', explode(',',$value[0]->tbl_tagwords));  
                Session::put('search_created_date_from', $value[0]->created_date_from);
                Session::put('search_created_date_to', $value[0]->created_date_to);
                Session::put('search_last_modified_from', $value[0]->last_modified_from);
                Session::put('search_last_modified_to', $value[0]->last_modified_to);
                Session::put('owner_ids', explode(',',$value[0]->ownership));
                Session::put('created_by_owner_ids', explode(',',$value[0]->document_created_by));
                Session::put('updated_by_owner_ids', explode(',',$value[0]->document_modified_by));
                Session::put('doctypeids', $value[0]->document_type_id);
                // For document sublist
                if($value[0]->document_type_column_name){
                    Session::put('document_column_value', explode(',',$value[0]->document_type_column_value));
                    Session::put('search_document_type_name','yes');
                }else{
                    Session::forget('document_column_value');
                    Session::forget('search_document_type_name');
                }
                $tbl_tagwords = DB::table('tbl_tagwords')->select('tagwords_id','tagwords_title')->whereIn('tagwords_category_id',Session::get('tagscate'))->get();
                if($tbl_tagwords)
                    $data['catTags'] = $tbl_tagwords;
            }
            $tbl_tagwords = DB::table('tbl_tagwords')->select('tagwords_id','tagwords_title')->get();
                
                if($tbl_tagwords)
                    $data['catTags'] = $tbl_tagwords;
            /*if(!empty($doctypeid)){
                $datases = DocumentTypesModel::find($doctypeid)->chlids;
                return View::make('pages/documents/advancesearch')->with($data)->with(['datas'=>$datases])->with(['results'=>$result])->with(['users'=>$user]);
            }else{*/
                $data['user'] = WorkflowsModel::users_list();
                $data['workflows'] = WorkflowsModel::get_workflows();
                $data['activities'] = WorkflowsModel::get_activities();
                $data['forms'] = FormModel::forms_list();
                return View::make('pages/documents/advancesearch')->with($data)->with(['results'=>$result])->with(['users'=>$user]);   
           // }
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    // Delete saved search criteria
    public function deleteSavedSearch(){
        $search_criteria = Input::get('search_criteria');
        // Delete 
        DB::table('tbl_search_criteria')->where('search_criteria_id',$search_criteria)->delete();
        DB::table('tbl_search_criteria_multiple_document_types_columns')->where('search_criteria_id',$search_criteria)->delete();
        // Destroy sessions that created for saved search
        echo "true";exit;// Ajax response
    } 

    public function getDocumentTypeSublist(){
        $document_type_id   = explode(',',$_GET['id']); 
        $search_criteria_id = @$_GET['criteriaId'];
        if(@$search_criteria_id){
            $data = DB::table('tbl_search_criteria_multiple_document_types_columns')
                    ->join('tbl_document_types_columns','tbl_search_criteria_multiple_document_types_columns.document_type_column_id','=','tbl_document_types_columns.document_type_column_id')
                    ->select('tbl_search_criteria_multiple_document_types_columns.*','tbl_document_types_columns.document_type_column_type','tbl_document_types_columns.document_type_options')
                    ->where('search_criteria_id',$search_criteria_id)
                    ->orderBy('id')
                    ->get();

            
        }else{
            $data = DB::table('tbl_document_types_columns')->select('document_type_column_id','document_type_id','document_type_column_name','document_type_column_type','document_type_options')->whereIn('document_type_id',$document_type_id)->orderBy('document_type_id')->orderBy('document_type_column_order')->get();
        }
        
        $i=1;
        $j=0;
        echo '<input type="hidden" name="coltypecnt" value="'.count($data).'">';
        foreach($data as $val):

            if($val->document_type_column_type == "Yes/No"):

                // For set values
                if(Session::get('document_column_value')[$j] == 'yes')
                {
                    $radioValYes[$j] = 'checked';
                }
                elseif(Session::get('document_column_value')[$j] == 'no')
                {
                    $radioValNo[$j] = 'checked';
                }
                elseif(Session::get('document_column_value')[$j] == '')
                {
                    $radioValNo[$j] = '';
                }
                echo '<div class="col-sm-12"><label class="control-label"> '.$val->document_type_column_name.' :</label><div class="fstControls">
                      <p><input type="hidden" name="docid'.$i.'" value="'.$val->document_type_column_id.'"><input '.@$radioValYes[$j].' name="doccol'.$i.'" id="newsletter" type="radio" value="yes"> Yes 
                          <input '.@$radioValNo[$j].' name="doccol'.$i.'" id="newsletter" type="radio" value="no"> No</p> 
                          <input type="hidden" name="doclabl'.$i.'" value="'.$val->document_type_column_name.'"></div></div>';
            elseif($val->document_type_column_type == "Date"):
                echo '<div class="col-sm-12">
                      <label for="DocTypes" class="control-label">'.$val->document_type_column_name.' :</label>
                      <div class="fstControls">
                      <input type="hidden" name="docid'.$i.'" value="'.$val->document_type_column_id.'">
                      <input type="hidden" name="doctype'.$i.'" value="'.$val->document_type_column_type.'">
                      <input type="text" name="doccol'.$i.'" class="form-control" id="exp_date'.$i.'" value="'.Session::get('document_column_value')[$j].'" placeholder="YYYY-MM-DD"><input type="hidden" name="doclabl'.$i.'" value="'.$val->document_type_column_name.'"></div></div>';
            elseif($val->document_type_column_type == "Piclist"):
                echo    '<div class="col-sm-12"><label for="DocTypes" class="control-label">'.$val->document_type_column_name.' : </label>
                        <div class="fstControls">
                        <input type="hidden" name="docid'.$i.'" value="'.$val->document_type_column_id.'">
                        <input type="hidden" name="doctype'.$i.'" value="'.$val->document_type_column_type.'">
                        <input type="hidden" name="doclabl'.$i.'" value="'.$val->document_type_column_name.'">
                        <select id="List" name="doccol'.$i.'" class="form-control">';
                        foreach(explode(',',$val->document_type_options) as $opt):
                            if($opt == Session::get('document_column_value')[$j]){
                                $value = 'selected';
                            }
                            echo '<option '.@$value.' value="'.$opt.'">'.$opt.'</option>';
                        endforeach;
                echo    '</select>
                        </div></div>';
            else:
                echo '<div class="col-sm-12"><label for="DocTypes" class="control-label">'.$val->document_type_column_name.' :</label>
                      <div class="fstControls">
                      <input type="hidden" name="docid'.$i.'" value="'.$val->document_type_column_id.'">
                      <input type="hidden" name="doctype'.$i.'" value="'.$val->document_type_column_type.'">
                      <input type="text" name="doccol'.$i.'" value="'.Session::get('document_column_value')[$j].'" class="form-control">
                      <input type="hidden" name="doclabl'.$i.'" value="'.$val->document_type_column_name.'">
                      </div></div>';
            endif;      
            $i++;
            $j++;
            endforeach;
            exit;
    }
    
    public function getDownload(Request $request,$filname)
    {
        $file = config('app.base_path').$filname;
        $array = explode("/", $file);
        $newUrl = $array[0]."/".$array[1]."/".$array[2]."/".$array[3]."/".$array[5]."/".$array[6]."/".$array[7];
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=".$filname);
        header("Content-Type: application/pdf");
        header("Content-Transfer-Encoding: binary");
        readfile($newUrl);
    }

    public function editFile(Request $request, $id)
    {
        if (Auth::user()) {
    $path = url('/');
    $tokens = explode('/', $path);
    $basepath = $tokens[0]."/".$tokens[1]."/".$tokens[2]."/".$tokens[3]."/";

        return redirect()->to($basepath.'php/simple_document.php?subfolder=&doc='.$id);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }



    public function documentsList()
    {   
        if (Auth::user()) {

            // Distroy session in search list
            $this->session_destroy_all();
            Session::forget('document_column_name');
            Session::forget('document_column_value');
            Session::forget('document_type_column_id'); 
            Session::forget('dglist');
            Session::forget('documentColNam');
            

            DB::enableQueryLog();
            // Get department id of the user
            //$depIds = explode(',',Auth::user()->department_id);

            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;
            $data['docType'] = DocumentTypesModel::where('is_app',0)->get();
            $data['stacks'] = StacksModel::all();
            $data['depts'] = DepartmentsModel::all();

            

            $queries = DB::getQueryLog();
            $last_query = end($queries);

            
                $data['stckApp'] = $this->docObj->common_stack();
                $data['deptApp'] = $this->docObj->common_dept();
                $data['doctypeApp'] = $this->docObj->common_type();
                $data['records'] = $this->docObj->common_records();
            return View::make('pages/documents/listview_united')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    

    //Show Documents in dept
    public function showdep(Request $request,$id,$name)
    {
        if(Auth::user()){

            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;

            $result= DocumentsModel::select('document_no','document_name')->whereIn('department_id',[$id])->get();
            $data['name']    = $name;
            $data['results'] = $result;

            return View::make('pages/departments/showdocuments')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();

        }
    }

    public function searchdoc()
    {
        if (Auth::user()) {

            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;

            $name=Input::get('q');
            $data['searchres'] = $name;
            $data['dglist'] = DB::table('tbl_documents')->where('document_name','LIKE','%'.$name.'%')->orWhere('document_no','LIKE','%'.$name.'%')->paginate(10);
            // Expanding dglits with required datas
            foreach($data['dglist'] as $val):
                $val->document_type_columns = DB::table('tbl_documents_columns')->select('document_column_name','document_column_value','document_column_type')->where('document_id',$val->document_id)->get();
                // Get documentTypes
                //$val->documentTypes = DB::table('tbl_document_types')->select(DB::raw('GROUP_CONCAT(document_type_name) AS document_type_names','document_type_column_no','document_type_column_name'))->whereIn('document_type_id',explode(',',$val->document_type_id))->get();

                $val->documentTypes = DocumentTypesModel::select('document_type_column_no','document_type_column_name')->where('document_type_id', $val->document_type_id)->get();

                // Get stack
                $val->stacks = DB::table('tbl_stacks')->select(DB::raw('GROUP_CONCAT(stack_name) AS stack_name'))->whereIn('stack_id',explode(',',$val->stack_id))->get();
                // Get Tag words
                $val->tagwords = DB::table('tbl_tagwords')->select(DB::raw('GROUP_CONCAT(tagwords_title) AS tagwords_title'))->whereIn('tagwords_id',explode(',',$val->document_tagwords))->get();

                // Get department
                $val->department = DB::table('tbl_departments')->select(DB::raw('GROUP_CONCAT(department_name) AS department_name'))->whereIn('department_id',explode(',',$val->department_id))->get();
                //To check document has workfowhistory
                $val->hasWorkfowHistory = DB::table('tbl_workflow_histories')->where('document_workflow_object_id',$val->document_id)->exists();
                endforeach;
            
            $data['docType'] = DocumentTypesModel::where('is_app',0)->get();
            
                $data['stckApp'] = $this->docObj->common_stack();
                $data['deptApp'] = $this->docObj->common_dept();
                $data['doctypeApp'] = $this->docObj->common_type();
                $data['records'] = $this->docObj->common_records();
            return View::make('pages/documents/datas')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    public function docClear()
    {
        if (Auth::user()) {

            // Distroy session in search list
            $this->session_destroy_all();
            Session::forget('document_column_name');
            Session::forget('document_column_value');
            Session::forget('document_type_column_id'); 
            Session::forget('documentColNam');
            
            /*Session::forget('sess_docno');
            Session::forget('sess_docname');
            Session::forget('sess_doctypeid');
            Session::forget('sess_owner');
            Session::forget('sess_modified_to');
            Session::forget('sess_modified_from');
            Session::forget('sess_created_from');
            Session::forget('sess_created_to');
            Session::forget('sess_dept');
            Session::forget('sess_settype');

            if(Session::get('sess_countcol')>0){
                $count=Session::get('sess_countcol');
                for($i=1;$i<=$count;$i++)
                {
                Session::forget('sess_doctypecol'.$i);
                }}
            Session::forget('sess_countcol');*/  
        }
        else{
           return redirect('')->withErrors("Please login")->withInput(); 
        }
    }
    
    public function contentSearch(Request $request)
    {
        $section = (Input::get('section'))?Input::get('section'):'documents';//
        $department = (Input::get('department'))?Input::get('department'):0;//
        $stacks = (Input::get('stacks'))?Input::get('stacks'):0;//
        $doctypeid = (Input::get('doctypeid'))?Input::get('doctypeid'):0;//
        $ownership = (Input::get('ownership'))?Input::get('ownership'):0;//
        $created_date_from = (Input::get('created_date_from'))?Input::get('created_date_from'):0;//
        $created_date_to = (Input::get('created_date_to'))?Input::get('created_date_to'):0;//
        $last_modified_from = (Input::get('last_modified_from'))?Input::get('last_modified_from'):0;//
        $last_modified_to = (Input::get('last_modified_to'))?Input::get('last_modified_to'):0;//
        $combination = Input::get('searchformat');
        $keyword = Input::get('content_srchtxt');
        $start = Input::get('start');
        $chunkSize = Input::get('chunkSize');
        $dir = config('app.base_path');

        $query = $this->optimise_content($section,$department,$stacks,$doctypeid,$ownership,$created_date_from,$created_date_to,$last_modified_from,$last_modified_to);
        
        $result_files = $query->offset($start)->limit($chunkSize)->get();
        if(($combination != "") && ($keyword!= ""))
        {   
            $keyword = ltrim($keyword);
            $keyword = rtrim($keyword);  
            $needle =  $keyword;
        }
        if(Auth::user()){
        $data['docType'] = DocumentTypesModel::where('is_app',0)->orderBy('document_type_order', 'ASC')->get();
        $data['stacks'] = StacksModel::all();
        $data['depts'] = DepartmentsModel::all();
        
        $data['stckApp'] = $this->docObj->common_stack();
        $data['deptApp'] = $this->docObj->common_dept();
        $data['doctypeApp'] = $this->docObj->common_type();
        $data['records'] = $this->docObj->common_records();
        
        $keyword = strtolower($needle);
        $pattern = preg_quote($keyword, '/');
        $pattern = "/^.*$pattern.*\$/m";
        $keyword_alter = '~'.$keyword.'~';
        $keyword_alter_array = array($keyword_alter);
        //global arrays
        $GLOBALS['content']=array();$GLOBALS['docs']=array();$GLOBALS['document_no']=array();
        $GLOBALS['document_name']=array();
        $GLOBALS['document_ownership']=array();
        $GLOBALS['created_at']=array();
        $GLOBALS['updated_at']=array();
        $GLOBALS['document_size']=array();
        $GLOBALS['document_expiry']=array();
        $data['needle'] = $keyword;
        $data['combination'] = $combination;
        //OR
        switch($combination)
        {
            case 'or':
            {
                $split = explode(" ", $keyword);
                foreach ($split as $key) {
                    
                    $pattern = preg_quote($key, '/');
                    $pattern_array_or[] = "/^.*$pattern.*\$/m";
                }
                foreach ($result_files as $file) 
                {
                    foreach ($pattern_array_or as $pattern) 
                    {    
                         $this->curl($file,$dir,$pattern,$combination,$keyword,$section);
                    }                    
                }                
            }
            break;
        //AND
            case 'and':
            {
                $split = explode(" ", $keyword);               
                foreach ($result_files as $file) 
                {
                    $this->curl_and($file,$dir,$split,$combination,$keyword,$section);  
                }
            }
            break;
        //Exact
            case 'ex':
            {
                foreach ($result_files as $file) 
                {
                    $this->curl($file,$dir,$pattern,$combination,$keyword,$section);
                }
            }
            break;
        }
            $unique_docs=array_unique($GLOBALS['docs']);
            $doc_unique_keys=array_keys($unique_docs);
            $doc_keys=array_keys($GLOBALS['docs']);
            if(count($doc_keys)!=0)
            {
            $missing = array_diff(range(0, max($doc_keys)), $doc_unique_keys);
            $order=array_values($missing);

            foreach($order as $val):
                unset($GLOBALS['document_no'][$val]);
                unset($GLOBALS['document_name'][$val]);
                unset($GLOBALS['document_ownership'][$val]);
                unset($GLOBALS['created_at'][$val]);
                unset($GLOBALS['updated_at'][$val]);
                unset($GLOBALS['content'][$val]);
                unset($GLOBALS['document_expiry'][$val]);
            endforeach;
            }
            $content['dglist'] = array(
                'file'=>array_values($unique_docs),
                'number'=>array_values($GLOBALS['document_no']),
                'name'=>array_values($GLOBALS['document_name']),
                'owner'=>array_values($GLOBALS['document_ownership']),
                'created'=>array_values($GLOBALS['created_at']),
                'updated'=>array_values($GLOBALS['updated_at']),
                'size'=>array_values($GLOBALS['document_size']),
                'content'=>array_values($GLOBALS['content']),
                'expiry'=>array_values($GLOBALS['document_expiry']));
            $content['dglist'] = (object) $content['dglist'];
            $content['section'] = $section;
            // echo '<pre>';
            // print_r($content);
            return $content;
            exit();
    }
        else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function curl($file,$dir,$pattern,$combination,$keyword,$section)
    {
        foreach(glob("$dir/$file->document_file_name") as $filename)
        {
            // Doc/docx documents search
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $ext = strtolower($ext);
            if($ext=='docx' || $ext == 'doc')
            {
                $this->docObj = new Filetotext($filename); // class defined in app/mylibs
                $content = $this->docObj->convertToText();
                $this->check_combination($content,$pattern,$file,$keyword,$combination,$section);
            }
            //Pdf search
            if($ext == 'pdf')
            {
                $content    = addslashes(shell_exec('/usr/bin/pdftotext \''.$filename.'\' -'));
                $this->check_combination($content,$pattern,$file,$keyword,$combination,$section);
            }
            // txt files searchs
            if($ext == 'txt')
            {
                $content = file_get_contents("$filename");
                $this->check_combination($content,$pattern,$file,$keyword,$combination,$section);
            }    
        }
    }
    public function curl_and($file,$dir,$pattern,$combination,$keyword,$section)
    {
        foreach(glob("$dir/$file->document_file_name") as $filename)
        {
            // Doc/docx documents search
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $ext = strtolower($ext);
            if($ext=='docx' || $ext == 'doc')
            {
                $this->docObj = new Filetotext($filename); // class defined in app/mylibs
                $content = $this->docObj->convertToText();
                $this->combination_and($pattern,$content,$file,$section);
            }
            //Pdf search
            if($ext == 'pdf')
            {
                $content    = addslashes(shell_exec('/usr/bin/pdftotext \''.$filename.'\' -'));
                $this->combination_and($pattern,$content,$file,$section);
            }
            // txt files searchs
            if($ext == 'txt')
            {
                $content = file_get_contents("$filename");
                $this->combination_and($pattern,$content,$file,$section);
            }    
        }
    }
    public function push($content,$file,$section){
        if($section == 'documents')
        {
            array_push($GLOBALS['content'], strtolower($content));
            array_push($GLOBALS['docs'], $file->document_file_name);
            array_push($GLOBALS['document_no'], $file->document_no);
            array_push($GLOBALS['document_name'], $file->document_name);
            array_push($GLOBALS['document_ownership'], $file->document_ownership);
            array_push($GLOBALS['created_at'], $file->created_at);
            array_push($GLOBALS['updated_at'], $file->updated_at);
            array_push($GLOBALS['document_expiry'], $file->document_expiry_date);
        }
        else if($section == 'forms')
        {
            array_push($GLOBALS['content'], strtolower($content));
            array_push($GLOBALS['docs'], $file->document_file_name);
            array_push($GLOBALS['document_no'], $file->form_response_id);
            array_push($GLOBALS['document_name'], $file->form_response_value);
            array_push($GLOBALS['document_ownership'], $file->user_id);
            array_push($GLOBALS['created_at'], $file->created_at);
            array_push($GLOBALS['updated_at'], $file->updated_at);
            array_push($GLOBALS['document_expiry'], $file->form_response_file_size);
        }
    }
    public function check_combination($content,$pattern,$file,$keyword,$combination,$section)
    {
        if($combination == 'ex')
        {
            if(preg_match_all('/'.$keyword.'/', strtolower($content), $matches))
            {
                $this->push($content,$file,$section);
            }
        }
        else
        {
            if(preg_match_all($pattern, strtolower($content), $matches))
            {
                $this->push($content,$file,$section);
            }
        }
    }
    public function combination_and($pattern,$content,$file,$section)
    {
        $match_word_count=0;
        for($i=0;$i<count($pattern);$i++){
            if (strpos(strtolower($content), $pattern[$i]) !== false) {
                $match_word_count++;
            }
        }
        if($match_word_count==count($pattern))
        {
            $this->push($content,$file,$section);
        }
    }
    public function searchadv(Request $request)
    {
        if (Auth::user())
        {  
            Session::put('search_list_exists','yes');
            // Fetching settings_no and name label
            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;
            // search
            
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['doctypeApp_selected'] = DocumentTypesModel::select('document_type_id','document_type_name')->where('document_type_id',Input::get('doctypeid'))->get();
            //document type columns headers listing
            $heads = array();  
            $data['type_id'] = (Input::get('doctypeid'))?Input::get('doctypeid'):0;
            
            if(!$data['type_id']) //Default doc type
            {
                $data['type_id'] = (isset($data['doctypeApp'][0]->document_type_id))?$data['doctypeApp'][0]->document_type_id:0;
            }
            
            foreach($data['doctypeApp'] as $val)
            {
                $res = DB::table('tbl_document_types_columns as tc')->select('tc.document_type_column_name','tc.document_type_column_id','tc.document_type_column_type','tc.document_type_options')->where('tc.document_type_id',$val->document_type_id)->orderBy('tc.document_type_column_order','ASC')->get();
                $heads[$val->document_type_id] = (count($res))?$res:array();
            }
            $data['heads'] = $heads;
            $data['records'] = $this->docObj->common_records();
            //For save search criteria
            if(@Input::get('search_criteria_id')){
                $criteria_name = DB::table('tbl_search_criteria')->select('criteria_name')->where('search_criteria_id',Input::get('search_criteria_id'))->get();
                Session::put('search_criteria_id',Input::get('search_criteria_id'));
                Session::put('search_criteria_name',$criteria_name[0]->criteria_name);
            }else{
                Session::forget('search_criteria_id');
                Session::forget('search_criteria_name');
            }
            // CHECK WETHER WHERE IS "AND" or "OR".
            if(Input::get('search_option') == 'AND'){
                Session::put('search_option',Input::get('search_option'));
            }else{
                // search_option="OR"
                Session::put('search_option',Input::get('search_option'));
            }
            // If document no exists
            if(Input::get('docno')):
                Session::put('serach_doc_no', Input::get('docno'));
            else:
                Session::forget('serach_doc_no');
            endif;
            // If document name exists
            if(Input::get('docname')):
                Session::put('search_docname',Input::get('docname'));
            else:
                Session::forget('search_docname');
            endif;
            // If ownership exists
            if(Input::get('ownership')):
                foreach(Input::get('ownership') as $owe):
                    $oweIds = DB::table('tbl_users')->select('id')->where('username',$owe)->get();
                    $ownerIds[] = $oweIds['0']->id;
                    $owes[]  = $owe;
                    endforeach;
                Session::put('search_ownership',$owes);
                Session::put('owner_ids',$ownerIds);
            else:
                Session::forget('search_ownership');
                Session::forget('owner_ids');
            endif;
            // If created by exists
            if(Input::get('created_by')):
                foreach(Input::get('created_by') as $created_by_owe):
                    $created_by_oweIds = DB::table('tbl_users')->select('id')->where('username',$created_by_owe)->get();
                    $created_by_ownerIds[] = $created_by_oweIds['0']->id;
                    $created_by_owes[]  = $created_by_owe;
                    endforeach;
                Session::put('search_created_by',$created_by_owes);
                Session::put('created_by_owner_ids',$created_by_ownerIds);
            else:
                Session::forget('search_created_by');
                Session::forget('created_by_owner_ids');
            endif;
            // If updated by exists
            if(Input::get('updated_by')):
                foreach(Input::get('updated_by') as $updated_by_owe):
                    $updated_by_oweIds = DB::table('tbl_users')->select('id')->where('username',$updated_by_owe)->get();
                    $updated_by_ownerIds[] = $updated_by_oweIds['0']->id;
                    $updated_by_owes[]  = $updated_by_owe;
                    endforeach;
                Session::put('search_updated_by',$updated_by_owes);
                Session::put('updated_by_owner_ids',$updated_by_ownerIds);
            else:
                Session::forget('search_updated_by');
                Session::forget('updated_by_owner_ids');
            endif;
            // If created_date_from exists 
            if(Input::get('created_date_from')):
                Session::put('search_created_date_from',Input::get('created_date_from'));
                Session::put('search_created_date_to',Input::get('created_date_to'));
            else:
                Session::forget('search_created_date_from');
            endif;
            // If created_date_to exists 
            if(Input::get('created_date_to')):
                Session::put('search_created_date_from',Input::get('created_date_from'));
                Session::put('search_created_date_to',Input::get('created_date_to'));
            else:
                Session::forget('search_created_date_to');
            endif;
            // If last_modified_from exists 
            if(Input::get('last_modified_from')):
                Session::put('search_last_modified_from',Input::get('last_modified_from'));
                Session::put('search_last_modified_to',Input::get('last_modified_to'));
            else:
                Session::forget('search_last_modified_from');
            endif;
            // If last_modified_to exists 
            if(Input::get('last_modified_to')):
                Session::put('search_last_modified_from',Input::get('last_modified_from'));
                Session::put('search_last_modified_to',Input::get('last_modified_to'));
            else:
                Session::forget('search_last_modified_to');
            endif;
            //departments
            if(Input::get('department')):
                foreach(Input::get('department') as $depid):
                    $departments[] = DB::table('tbl_departments')->select('department_name')->where('department_id',$depid)->get();
                endforeach;
            
                if(isset($departments)):
                    foreach($departments as $dep):
                        $deptMnts[] = $dep[0]->department_name;
                    endforeach;
                    Session::put('search_departments',$deptMnts);
                    Session::put('departments',Input::get('department'));
                else:
                    Session::forget('search_departments');
                    Session::forget('departments');
                endif;
            else:
                Session::forget('search_departments');
                Session::forget('departments');
            endif;
            //stacks
            if(Input::get('stacks')):
                foreach(Input::get('stacks') as $satckIds):
                    $stackNames[] = DB::table('tbl_stacks')->select('stack_name')->where('stack_id',$satckIds)->get();
                endforeach;
                if(isset($stackNames)):
                    foreach($stackNames as $stknam):
                        $stacknames[] = $stknam[0]->stack_name;
                    endforeach;
                    Session::put('search_stack',$stacknames);
                    Session::put('stackids',Input::get('stacks'));
                else:
                    Session::forget('search_stack');
                    Session::forget('stackids');
                endif;
            else:
                Session::forget('search_stack');
                Session::forget('stackids');
            endif;
            //doctype
            if(Input::get('doctypeid')):
            //column names fetches
            $data['col_names'] = DocumentTypeColumnModel::select('document_type_column_name')->where('document_type_id',Input::get('doctypeid'))->orderBy('document_type_column_order','ASC')->get();
            $documentTypeNames = DB::table('tbl_document_types')->select('document_type_name')->where('document_type_id',Input::get('doctypeid'))->get();
            if(isset($documentTypeNames)):
                Session::put('search_document_type_name',$documentTypeNames[0]->document_type_name);
                Session::put('doctypeids',Input::get('doctypeid'));
                else:
                    Session::forget('search_document_type_name');
                    Session::forget('doctypeids');
                endif;
            else:
                Session::forget('search_document_type_name');
                Session::forget('doctypeids');
            endif;
            //keyword
            if(Input::get('keywrd_srchtxt')):
                Session::put('search_keywrd_srchtxt',Input::get('keywrd_srchtxt'));
            else:
                Session::forget('search_keywrd_srchtxt');
            endif;

            //For save search criteria
            if(@Input::get('search_criteria_id')):
                Session::put('search_criteria_id',Input::get('search_criteria_id'));
            else:
                Session::forget('search_criteria_id');
            endif;
            //document type columns
            $coltypcnt  = Input::get('coltypecnt');
            
            if($coltypcnt)
            {
                Session::put('coltypecnt',Input::get('coltypecnt'));
                               
                for($i=1;$i<=$coltypcnt;$i++)
                {
                    //if(Input::get('doccol'.$i)): 
                        $document_column_name[] = Input::get('doclabl'.$i);
                        $document_column_value[] = (Input::get('doccol'.$i))?Input::get('doccol'.$i):'';
                        $document_type_column_id[] = Input::get('docid'.$i);
                        $document_type_column_type[] = Input::get('doctype'.$i);
                    //endif;
                } 
                
                if(@$document_column_value):
                    Session::put('document_column_name',$document_column_name);
                    Session::put('document_column_value',$document_column_value);
                    Session::put('document_type_column_id',$document_type_column_id);
                    Session::put('document_type_column_type',$document_type_column_type);
                else:
                    Session::forget('document_column_name');
                    Session::forget('document_column_value');
                    Session::forget('document_type_column_id');
                    Session::forget('document_type_column_type');
                endif;
            }   
            else
            {
                Session::forget('coltypecnt');
            }
            $data['tcolumn_imp'] =array();
            array_push($data['tcolumn_imp'],
                ['title'=>trans('language.document name'),'data'=>'document_name'],
                ['title'=>trans('documents.ownership'),'data'=>'document_ownership'],
                ['title'=>trans('documents.created date'),'data'=>'created_at'],
                ['title'=>trans('documents.updated date'),'data'=>'updated_at']
            );
            $data['tcolumn'] = array();
            array_push($data['tcolumn'],
                ['title'=>trans('language.department'),'data'=>'department_id'],
                ['title'=>trans('language.Stack'),'data'=>'stack_id'],
                ['title'=>trans('language.document type'),'data'=>'document_type_id'],
                ['title'=>trans('language.document no'),'data'=>'document_no'],
                ['title'=>trans('documents.expir_date'),'data'=>'document_expiry_date'],
                ['title'=>trans('language.status'),'data'=>'document_status']
                );
            $data['tcolumn_dynamic'] =array();
            foreach($data['doctypeApp_selected'] as $key => $row)
            { 
                $head_footr = (isset($heads[$row->document_type_id]))?$heads[$row->document_type_id]:array();

                if(count($head_footr))
                {
                    foreach($head_footr as $value)
                    { 
                        array_push($data['tcolumn_dynamic'],['column_id'=>$value->document_type_column_id,'title'=>$value->document_type_column_name,'data'=>$value->document_type_column_name]);
                    } 
                } 
            } 
            // echo '<pre>';
            // print_r($data['tcolumn_imp']);
            // exit();
            return View::make('pages/documents/advance_search_list')->with($data);
        }
        else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

     //find the duplicates records
     public function fileDuplicates(){
        if (Auth::user()) {  
            // Fetching settings_no and name label
            $datetime = date("Y-m-d_H-i-s");
            $mismatch_name = array();
			$dir = config('app.base_path');

			$myarray = glob($dir."*.pdf");

			$file_err= config('app.export_path')."mismatch_files_list".$datetime.".csv";
            $headers = array('Content-Type: text/plain',);
            $out = fopen($file_err, 'w');

           	$err=array('List of mismatched pdf files');
        	fputcsv($out,$err);
        	//blank row insert
        	$a=array('File Name');
        	fputcsv($out, $a);

            foreach($myarray as $file) 
            {   
            	//remove path from name
                $file = basename($file);
            	$row = array();
               	$query = DB::table('tbl_documents')->select('tbl_documents.*')->where('document_file_name',$file);
            	$dglist = $query->count(); 

            	if($dglist==0){
            		fputcsv($out, array($file));
            	}
            }

            $a=array('* These files are not available in our database');
        	fputcsv($out, $a);

            fclose($out);
            return Response::download($file_err,'mismatch_files_list'.$datetime.'.csv', $headers); 
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    //listing duplicate file for agm
    public function listDuplicates(){
        if (Auth::user()) 
        {  
            $user_permission=Auth::user()->user_permission;
            $data['docType'] = DocumentTypesModel::orderBy('document_type_order', 'ASC')->get();   
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            $data['dglist'] = DB::select( DB::raw("SELECT table1.document_id,table1.document_file_name,table1.document_no as 'name',table1.document_name  as  'department_name',tbl_stacks.stack_name,table1.created_at,table1.document_created_by,table1.document_modified_by,table1.document_size FROM tbl_documents as table1 LEFT JOIN tbl_stacks ON table1.stack_id = tbl_stacks.stack_id WHERE (table1.document_file_name IN (SELECT tbl_documents.document_file_name FROM tbl_documents GROUP BY document_file_name having count(*) > 1 ORDER BY tbl_documents.document_id ASC) AND table1.document_file_name != '') ORDER BY table1.document_file_name ASC"));

           return View::make('pages/documents/duplicateList')->with($data);
            // echo '<pre>';
            // print_r($data['dglist']);
            // exit();
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
     //find the duplicates records
     public function findDuplicates(){
        if (Auth::user()) {  
            // Fetching settings_no and name label
            $mismatch_name = array();

            $query = DB::table('tbl_documents')->select('tbl_documents.*','tbl_stacks.stack_name as stack_name')->leftjoin('tbl_stacks','tbl_documents.stack_id','=','tbl_stacks.stack_id')->where('document_status','Published')->groupBy('document_file_name');
            $data['dglist'] = $query->get(); 
            $incr = 1;
            $datetime = date("Y-m-d_H-i-s");

            $file_err= config('app.export_path')."mismatch_documents_list".$datetime.".csv";
            $headers = array('Content-Type: text/plain',);
            $out = fopen($file_err, 'w');
             $err=array('List of files missing in the server');
            fputcsv($out,$err);
            //blank row insert
            $a=array('File Name','Pensioners Name','ID/Department Name','Stack Name');
            fputcsv($out, $a);
            //print to csv
            
            foreach($data['dglist'] as $val){
                $file_name = $val->document_file_name;
                if(file_exists(config('app.base_path').$file_name) && $file_name)
                {  
                }else{       
                $row = array();
                //foreach ($fields_export as $key) {
                   $row[] = $val->document_file_name;
                   
                  // }   
                   fputcsv($out, array($val->document_file_name,$val->document_no,$val->document_name,$val->stack_name));
                    // $res[$incr]['doc_name'] = $val->document_name;
                    // $res[$incr]['doc_no'] = $val->document_no;
                    // $res[$incr]['doc_file_name'] = $val->document_file_name;
                    //error file
                    
                   
                    //convert to column printing format
                    //foreach ($arr_dup_name as $key) {
                       //fputcsv($out, $val->document_file_name);
                    //}
                    
                    $incr++;  
                }     

            }
            fclose($out);
            //fputcsv($out, $a);
            return Response::download($file_err,'mismatch_documents_list'.$datetime.'.csv', $headers);
            // echo $incr;
            // $data['datalist'] = $res;
            
            //return View::make('pages/documents/find_duplicates')->with($data);   
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    
    //Advance search document submit 
    /*public function searchadvncdoc(Request $request)
    {   
        if (Auth::user()) {  
            // Fetching settings_no and name label
            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;
            
            //For save search criteria
            if(@Input::get('search_criteria_id')){
                $criteria_name = DB::table('tbl_search_criteria')->select('criteria_name')->where('search_criteria_id',Input::get('search_criteria_id'))->get();
                Session::put('search_criteria_name',$criteria_name[0]->criteria_name);
            }else{
                Session::forget('search_criteria_name');
            }

            DB::enableQueryLog();
            $table = 'tbl_documents';
            //old query
            //$query = DB::table('tbl_documents')->select('tbl_documents.*','tbl_documents_columns.document_id as doc_val_document_id','tbl_documents_columns.document_column_value',DB::RAW('GROUP_CONCAT(document_column_name) AS document_column_names'),DB::RAW('GROUP_CONCAT(document_column_value) AS document_column_values'))->leftJoin('tbl_documents_columns','tbl_documents_columns.document_id','=','tbl_documents.document_id');

            $query = DB::table($table)
             ->leftjoin('tbl_documents_columns',$table.'.'.'document_id','=','tbl_documents_columns.document_id')
           ->leftjoin('tbl_departments',$table.'.'.'department_id','=','tbl_departments.department_id')
           ->leftjoin('tbl_stacks',$table.'.'.'stack_id','=','tbl_stacks.stack_id')
           ->leftjoin('tbl_document_types',$table.'.'.'document_type_id','=','tbl_document_types.document_type_id');
           $select ="
            tbl_departments.department_id,
            tbl_stacks.stack_id,
            $table.document_type_id,
            tbl_document_types.document_type_name as document_type_name,
            $table.document_id,
            $table.document_no as document_no,
            $table.document_name as document_name,
            $table.department_id,
            tbl_departments.department_name as department_name,
            $table.stack_id,
            tbl_stacks.stack_name as stack_name,
            $table.document_ownership,
            $table.created_at,
            $table.updated_at,
            $table.document_expiry_date,
            $table.document_status,
            $table.parent_id,
            $table.document_version_no,
            $table.document_file_name,
            $table.document_modified_by,
            $table.document_encrypt_status,
            $table.document_encrypted_by";
            //check user = private user, fetch only the docs of that user
                if(Auth::user()->user_role == Session::get("user_role_private_user"))
                {
                    $query->where('tbl_documents.document_ownership',Auth::user()->username);
                }
            // CHECK WETHER WHERE IS "AND" or "OR".
            if(Input::get('search_option') == 'AND')
            {
                $queryWhere    = 'where';
                $queryWhereIn  = 'whereIn';
                $queryWhereRaw = 'whereRaw';
            }
            else
            {
                // search_option="OR"
                $queryWhere    = 'orWhere';
                $queryWhereIn  = 'orWhereIn';
                $queryWhereRaw = 'orWhereRaw';
            }
            
            //////////////// KEYWORD SEARCH ///////////////////////
            if(Input::get('keywrd_srchtxt')):   
                // Search by users
                if(Auth::user()->user_role != Session::get('user_role_super_admin'))
                {
                    $department_id = explode(',',Auth::user()->department_id);
                    $count         = count($department_id);
                    if($count == 1):
                        $x=0;
                    else:
                        $x=1;
                    endif;                    
                    foreach($department_id as $depid):
                       if($x == 1):
                            $query->$queryWhereRaw('('.'FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        elseif($x == $count):
                            $query->orWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)'.')');
                        else:
                            $query->$queryWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        endif;
                        $x++;
                    endforeach;
                    $query->where('tbl_documents.document_no','LIKE','%'.Input::get('keywrd_srchtxt').'%');
                    $query->orWhere('tbl_documents.document_name','LIKE','%'.Input::get('keywrd_srchtxt').'%');
                    $query->orWhere('tbl_documents.document_no','=',Input::get('keywrd_srchtxt'));
                }
                else//search by super admin
                {
                    $query->where('tbl_documents_columns.document_column_value','LIKE','%'.Input::get('keywrd_srchtxt').'%');
                    $query->orWhere('tbl_documents.document_name','LIKE','%'.Input::get('keywrd_srchtxt').'%');
                    $query->orWhere('tbl_documents.document_no','=',Input::get('keywrd_srchtxt'));
                }
            
            endif;
            //////////////// END KEYWORD SEARCH ///////////////////////

            // If document no exists
            if(Input::get('docno')):

                // Search by users department list
                if(Auth::user()->user_role != Session::get('user_role_super_admin')){
                    $department_id = explode(',',Auth::user()->department_id);
                    $count         = count($department_id);
                    if($count == 1):
                        $x=0;
                    else:
                        $x=1;
                    endif;
                    
                    foreach($department_id as $depid):
                       if($x == 1):
                            $query->$queryWhereRaw('('.'FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        elseif($x == $count):
                            $query->orWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)'.')');
                        else:
                            $query->$queryWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        endif;
                        $x++;
                    endforeach;
                    $query->where('tbl_documents.document_no','LIKE','%'.Input::get('docno').'%');
                }else{
                    $query->$queryWhere('tbl_documents.document_no','LIKE','%'.Input::get('docno').'%');
                }
            endif;

            // If document name exists
            if(Input::get('docname')):

                // Search by users department list
                if(Auth::user()->user_role != Session::get('user_role_super_admin')){
                    $department_id = explode(',',Auth::user()->department_id);
                    $count         = count($department_id);
                    if($count == 1):
                        $x=0;
                    else:
                        $x=1;
                    endif;
                    
                    foreach($department_id as $depid):
                       if($x == 1):
                            $query->$queryWhereRaw('('.'FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        elseif($x == $count):
                            $query->orWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)'.')');
                        else:
                            $query->$queryWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        endif;
                        $x++;
                    endforeach;

                    $query->where('tbl_documents.document_name','LIKE','%'.Input::get('docname').'%');
                }else{
                    $query->$queryWhere('tbl_documents.document_name','LIKE','%'.Input::get('docname').'%');
                }
            endif;
     
            // If ownership exists
            if(Input::get('ownership')):

                // Search by users department list
                if(Auth::user()->user_role != Session::get('user_role_super_admin')){
                    $department_id = explode(',',Auth::user()->department_id);
                    $count         = count($department_id);
                    if($count == 1):
                        $x=0;
                    else:
                        $x=1;
                    endif;
                    
                    foreach($department_id as $depid):
                       if($x == 1):
                            $query->$queryWhereRaw('('.'FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        elseif($x == $count):
                            $query->orWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)'.')');
                        else:
                            $query->$queryWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        endif;
                        $x++;
                    endforeach;

                    $query->whereIn('tbl_documents.document_ownership',Input::get('ownership'));

                }else{
                    $query->$queryWhereIn('tbl_documents.document_ownership',Input::get('ownership'));
                }

                
                foreach(Input::get('ownership') as $owe):
                    $oweIds = DB::table('tbl_users')->select('id')->where('username',$owe)->get();
                    $ownerIds[] = $oweIds['0']->id;
                    $owes[]  = $owe;
                    endforeach;
                
            endif;

            // If created by exists
            if(Input::get('created_by')):
                // print_r(Input::get('created_by'));
                // exit();
                // Search by users department list
                if(Auth::user()->user_role != Session::get('user_role_super_admin')){
                    $department_id = explode(',',Auth::user()->department_id);
                    $count         = count($department_id);
                    if($count == 1):
                        $x=0;
                    else:
                        $x=1;
                    endif;
                    
                    foreach($department_id as $depid):
                       if($x == 1):
                            $query->$queryWhereRaw('('.'FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        elseif($x == $count):
                            $query->orWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)'.')');
                        else:
                            $query->$queryWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        endif;
                        $x++;
                    endforeach;

                    $query->whereIn('tbl_documents.document_created_by',Input::get('created_by'));

                }else{
                    $query->$queryWhereIn('tbl_documents.document_created_by',Input::get('created_by'));
                }

                
                foreach(Input::get('created_by') as $created_by_owe):
                    $created_by_oweIds = DB::table('tbl_users')->select('id')->where('username',$created_by_owe)->get();
                    $created_by_ownerIds[] = $created_by_oweIds['0']->id;
                    $created_by_owes[]  = $created_by_owe;
                    endforeach;
                
            endif;

            // If updated by exists
            if(Input::get('updated_by')):

                // Search by users department list
                if(Auth::user()->user_role != Session::get('user_role_super_admin')){
                    $department_id = explode(',',Auth::user()->department_id);
                    $count         = count($department_id);
                    if($count == 1):
                        $x=0;
                    else:
                        $x=1;
                    endif;
                    
                    foreach($department_id as $depid):
                       if($x == 1):
                            $query->$queryWhereRaw('('.'FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        elseif($x == $count):
                            $query->orWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)'.')');
                        else:
                            $query->$queryWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        endif;
                        $x++;
                    endforeach;

                    $query->whereIn('tbl_documents.document_modified_by',Input::get('updated_by'));

                }else{
                    $query->$queryWhereIn('tbl_documents.document_modified_by',Input::get('updated_by'));
                }

                
                foreach(Input::get('updated_by') as $updated_by_owe):
                    $updated_by_oweIds = DB::table('tbl_users')->select('id')->where('username',$updated_by_owe)->get();
                    $updated_by_ownerIds[] = $updated_by_oweIds['0']->id;
                    $updated_by_owes[]  = $updated_by_owe;
                    endforeach;
                
            endif;

            // If department exists
            if(Input::get('department')):

                foreach(Input::get('department') as $depid):
                    $query->$queryWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                    $departments[] = DB::table('tbl_departments')->select('department_name')->where('department_id',$depid)->get();
                    endforeach;
            endif;

            // If document type id exists
            if(Input::get('doctypeid')):

                // Search by users department list
                if(Auth::user()->user_role != Session::get('user_role_super_admin')){
                    $department_id = explode(',',Auth::user()->department_id);
                    $count         = count($department_id);
                    if($count == 1):
                        $x=0;
                    else:
                        $x=1;
                    endif;
                    
                    foreach($department_id as $depid):
                       if($x == 1):
                            $query->$queryWhereRaw('('.'FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        elseif($x == $count):
                            $query->orWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)'.')');
                        else:
                            $query->$queryWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        endif;
                        $x++;
                    endforeach;
                    // search with document type
                    $query->whereRaw('FIND_IN_SET('.Input::get('doctypeid').',tbl_documents.document_type_id)');
                }else{
                    // search with document type
                    $query->$queryWhereRaw('FIND_IN_SET('.Input::get('doctypeid').',tbl_documents.document_type_id)');
                }
                $documentTypeNames = DB::table('tbl_document_types')->select('document_type_name','document_type_column_no','document_type_column_name')->where('document_type_id',Input::get('doctypeid'))->get();
                //column names fetches
            $data['col_names'] = DocumentTypeColumnModel::select('document_type_column_name')->where('document_type_id',Input::get('doctypeid'))->orderBy('document_type_column_order','ASC')->get();
            else:
                // Distory session
                Session::forget('search_document_type_name');
                Session::forget('doctypeids');
                Session::forget('doctypeid');
            endif;

            $coltypcnt  = Input::get('coltypecnt');   
            if($coltypcnt){
                // Geting input document value count
                for($a=1;$a<=$coltypcnt;$a++){
                    $no_of_document_column_value[] = Input::get('doccol'.$a);
                }
                

                for($i=1;$i<=$coltypcnt;$i++){
                    if(Input::get('doccol'.$i)): 
                        // To make And,Or query in a different way.
                        if(count(array_filter($no_of_document_column_value)) > 1):

                            $query->orWhere('tbl_documents_columns.document_column_name','like','%'.Input::get('doclabl'.$i).'%');
                            $query->where('tbl_documents_columns.document_column_value','like','%'.Input::get('doccol'.$i).'%');
                        else:
                            $query->where('tbl_documents_columns.document_column_name','like','%'.Input::get('doclabl'.$i).'%');
                            $query->where('tbl_documents_columns.document_column_value','like','%'.Input::get('doccol'.$i).'%');
                        endif;
                    endif;
                    $document_column_name[]  = Input::get('doclabl'.$i);
                    $document_column_value[] = Input::get('doccol'.$i);
                    $document_type_column_id[] = Input::get('docid'.$i);
                    
                } 
                if(@$document_column_value):

                    Session::put('document_column_name',$document_column_name);
                    Session::put('document_column_value',$document_column_value);
                    Session::put('document_type_column_id',$document_type_column_id);
                endif;
            }
           
            // If statcks exists
            if(Input::get('stacks')):

                if(Auth::user()->user_role != Session::get('user_role_super_admin')){
                    $department_id = explode(',',Auth::user()->department_id);
                    $count         = count($department_id);
                    if($count == 1):
                        $x=0;
                    else:
                        $x=1;
                    endif;
                    
                    foreach($department_id as $depid):
                       if($x == 1):
                            $query->$queryWhereRaw('('.'FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        elseif($x == $count):
                            $query->orWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)'.')');
                        else:
                            $query->$queryWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)');
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
                            $query->$queryWhereRaw('FIND_IN_SET('.$satckIds.',tbl_documents.stack_id)'.')');
                        else:
                            $query->whereRaw('FIND_IN_SET('.$satckIds.',tbl_documents.stack_id)');
                        endif;
                        $stackNames[] = DB::table('tbl_stacks')->select('stack_name')->where('stack_id',$satckIds)->get();
                        $y++;
                    endforeach;

                }else{
                    foreach(Input::get('stacks') as $satckIds):
                    $query->$queryWhereRaw('FIND_IN_SET('.$satckIds.',tbl_documents.stack_id)');
                    $stackNames[] = DB::table('tbl_stacks')->select('stack_name')->where('stack_id',$satckIds)->get();
                    endforeach;
                }

                
            endif;

            // If created_date_from exists 
            if(Input::get('created_date_from')):

                // Search by users department list
                if(Auth::user()->user_role != Session::get('user_role_super_admin')){
                    $department_id = explode(',',Auth::user()->department_id);
                    $count         = count($department_id);
                    if($count == 1):
                        $x=0;
                    else:
                        $x=1;
                    endif;
                    
                    foreach($department_id as $depid):
                       if($x == 1):
                            $query->$queryWhereRaw('('.'FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        elseif($x == $count):
                            $query->orWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)'.')');
                        else:
                            $query->$queryWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        endif;
                        $x++;
                    endforeach;

                    $query->where('tbl_documents.created_at','>=',Input::get('created_date_from').' 00:00:00');
                }else{
                    $query->orWhere('tbl_documents.created_at','>=',Input::get('created_date_from').' 00:00:00');
                }

                
            endif;

            // If created_date_to exists 
            if(Input::get('created_date_to')):

                // Search by users department list
                if(Auth::user()->user_role != Session::get('user_role_super_admin')){
                    $department_id = explode(',',Auth::user()->department_id);
                    $count         = count($department_id);
                    if($count == 1):
                        $x=0;
                    else:
                        $x=1;
                    endif;
                    
                    foreach($department_id as $depid):
                       if($x == 1):
                            $query->$queryWhereRaw('('.'FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        elseif($x == $count):
                            $query->orWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)'.')');
                        else:
                            $query->$queryWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        endif;
                        $x++;
                    endforeach;

                    $query->where('tbl_documents.created_at','<=',Input::get('created_date_to').' 23:59:59');
                }else{
                    $query->$queryWhere('tbl_documents.created_at','<=',Input::get('created_date_to').' 23:59:59');
                }

                
            endif;

            // If last_modified_from exists 
            if(Input::get('last_modified_from')):

                // Search by users department list
                if(Auth::user()->user_role != Session::get('user_role_super_admin')){
                    $department_id = explode(',',Auth::user()->department_id);
                    $count         = count($department_id);
                    if($count == 1):
                        $x=0;
                    else:
                        $x=1;
                    endif;
                    
                    foreach($department_id as $depid):
                       if($x == 1):
                            $query->$queryWhereRaw('('.'FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        elseif($x == $count):
                            $query->orWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)'.')');
                        else:
                            $query->$queryWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        endif;
                        $x++;
                    endforeach;

                    $query->where('tbl_documents.updated_at','>=',Input::get('last_modified_from').' 00:00:00');
                }else{
                    $query->$queryWhere('tbl_documents.updated_at','>=',Input::get('last_modified_from').' 00:00:00');
                }
                
                
            endif;

            // If last_modified_to exists 
            if(Input::get('last_modified_to')):

                // Search by users department list
                if(Auth::user()->user_role != Session::get('user_role_super_admin')){
                    $department_id = explode(',',Auth::user()->department_id);
                    $count         = count($department_id);
                    if($count == 1):
                        $x=0;
                    else:
                        $x=1;
                    endif;
                    
                    foreach($department_id as $depid):
                       if($x == 1):
                            $query->$queryWhereRaw('('.'FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        elseif($x == $count):
                            $query->orWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)'.')');
                        else:
                            $query->$queryWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        endif;
                        $x++;
                    endforeach;

                    $query->where('tbl_documents.updated_at','<=',Input::get('last_modified_to').' 23:59:59');
                }else{
                    $query->$queryWhere('tbl_documents.updated_at','<=',Input::get('last_modified_to').' 23:59:59');
                }
                
                
            endif;

            // Expanding dglits with required datas

            $length         =     Input::get("length");
            $start          =     Input::get("start");
            $currentPage = ($start)?($start/$length)+1:1;

            \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($currentPage) {
                return $currentPage;
            });
            //ajax search
            $column = array('document_type_name',$table.'.'.'document_no',$table.'.'.'document_name','tbl_departments.department_name','tbl_stacks.stack_name',$table.'.'.'document_ownership',$table.'.'.'document_path',$table.'.'.'created_at',$table.'.'.'updated_at',$table.'.'.'document_expiry_date',$table.'.'.'document_status','tbl_documents_columns.document_column_value');
            $search       =   (isset($_POST['search']['value']))?trim($_POST['search']['value']):'';
            Session::put('adv_dt_search_text',$search);
            if($search)
            {
                $query->Where(function($query1) use($column,$search) 
                {
                    foreach ($column as $key => $value) 
                    {
                        if($value=="tbl_documents.updated_at"){
                                $datesearch = date("Y-m-d", strtotime($search));    
                                $query1->orWhere($value,'LIKE','%'.$datesearch.'%');
                        }
                        else
                        {
                            $query1->orWhere($value,'LIKE','%'.$search.'%');        
                        }
                    }
                });
            }
            // Ajax order by works
            $order = (isset($_POST['order'][0]['column']))?$_POST['order'][0]['column']:1;
            $direct = (isset($_POST['order'][0]['dir']))?$_POST['order'][0]['dir']:'DESC';
            $data_item = (isset($_POST['columns'][$order]['data']))?$_POST['columns'][$order]['data']:'';
            switch($data_item)
        {
          case 'document_type_id':
          $table_column = $table.'.'.'document_type_id';
          break;
          case 'document_no':
          $table_column = $table.'.'.'document_no';
          break;
          case 'document_name':
          $table_column = $table.'.'.'document_name';
          break;
          case 'department_id':
          $table_column = 'tbl_departments'.'.'.'department_name';
          break;
          case 'stack_id':
          $table_column = 'tbl_stacks'.'.'.'stack_name';
          break;
          case 'document_ownership':
          $table_column = $table.'.'.'document_ownership';
          break;
          case 'created_at':
          $table_column = $table.'.'.'created_at';
          break;
          case 'updated_at':
          $table_column = $table.'.'.'updated_at';
          break;
          case 'document_expiry_date':
          $table_column = $table.'.'.'document_expiry_date';
          break;
          case 'document_status':
          $table_column = $table.'.'.'document_status';
          break;
          case 'document_modified_by':
          $table_column = $table.'.'.'document_modified_by';
          break;
          default:
          $table_column = 'tbl_departments'.'.'.'department_name';
          break;
        }
            // Run the query
            $query->orderBy("$table_column","$direct");
            $query->orderBy("$table.document_id",'DESC');
            $query->selectRaw($select)->groupBy('tbl_documents.document_id');
            $data['dglist'] = $query->paginate($length); 
            $i = $start;
            $data_table = array();
            foreach($data['dglist'] as $value): 
            $ext = pathinfo($value->document_file_name, PATHINFO_EXTENSION);
            $user_permission=Auth::user()->user_permission;
            $current_view =Lang::get('language.list_view');
            $i++;
            $row_d = array();
            $action =''; 
            //column values
            @$value->document_type_columns = DB::table('tbl_documents_columns')->select('document_column_name','document_column_value','document_column_type')->where('document_id',$value->document_id)->get();
            //To check document has workfowhistory
            @$value->hasWorkfowHistory = DB::table('tbl_workflow_histories')->where('document_workflow_object_id',$value->document_id)->exists();
            if($value->document_status!='Checkout')
            {
                $action.='<input name="checkbox[]" type="checkbox" value="'.$value->document_id.'" id="chk'.$value->document_id.'" class="checkBoxClass">';
            }
            else
            {
                $action.='<input name="checkbox_disabled[]" type="checkbox" onclick="return swal(You have no permission)" value="'.$value->document_id.'" disabled=true>';
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
                 
                    $action.='<a title="Open Document" href="documentManagementView?dcno='.$value->document_id.'&page='.Lang::get('language.list_view').'">';
                
                    if($ext=="pdf"){
                        $action.='<i class="fa fa-file-pdf-o"></i>';
                    }
                    elseif ($ext=='png'||$ext=='jpg'||$ext=='jpeg'||$ext=='tiff'||$ext=='tif'||$ext=='TIFF'||$ext=='TIF') {
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
                                 $action.='<i title="Delete" class="fa fa-trash" onclick="del( \''.$value->document_id.'\',\''.$value->document_name.'\')" style="color: red; cursor:pointer;"></i>';
                            }
                            else{
                                $action .='&nbsp;';
                                 $action.='<i title="Delete" class="fa fa-trash" onclick="return swal(\''.ucfirst($value->document_name).' is curently Checked Out by '.ucfirst($value->document_modified_by).'. It must be Checked In first before you can perform this operation.'.'\')" style="color: red; cursor:pointer;"></i>';
                            }
                            
                        }
                    $action.='
                    <!-- Related document -->';
                        $action .='&nbsp;';
                        $action.='<a title="Related documents" href="relatedsearch/'.$value->document_id.'/'.$value->document_no.'/'.$value->document_name.'"><i class="fa fa-files-o" ></i></a>';
                    $action.='
                    <!-- Workflow history -->';
                        if(stristr($user_permission,"view")){
                        if(@$value->hasWorkfowHistory){
                        $action .='&nbsp;';
                        $action.='<a title="Add/View Workflow" href="showWorkflowHistory/document/'.$value->document_id.'><i class="fa fa-fw fa-line-chart" style="cursor:pointer;"></i></a>';                
                        }
                        }
                    if (Session::get('module_activation_key4')==1){
                        if(date("Y-m-d") > Session::get('module_expiry_date4')){ 
                        }else{
                            $action.='
                            <!-- add view workflow -->';
                            if(stristr($user_permission,"workflow")){
                            $action .='&nbsp;';
                            $action.='<a count="'.$value->document_id.'" data-toggle="modal" data-target="#addview_workflow" title="add/view workflow" id="workflow" style="cursor:pointer;"><i class="fa fa-map-o" ></i></a>';
                           }
                        }
                    }
                    
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
                    $action .='</ul></div>';
            $row_d['actions'] = $action;
            if(@$value->document_type_name)
                {$row_d['document_type_id'] = ucfirst(@$value->document_type_name);}
           if(@$value->document_no)
                {$row_d['document_no'] = @$value->document_no;}
            if(@$value->document_name)
                {$row_d['document_name'] = ucfirst(@$value->document_name).', Ver : '.$value->document_version_no;}
            if(@$value->department_name)
                {$row_d['department_id'] = ucfirst(@$value->department_name);}
            if(@$value->stack_name)
                {$row_d['stack_id'] = ucfirst(@$value->stack_name);}
            $document_id = $value->document_id;

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
            ->where('tc.document_type_id',Session::get('serach_doc_type'))
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
                
                $row_d["$value2->document_type_column_id"] = ($value_column)?$value_column:'-';
            }
            $row_d['document_ownership'] = ucfirst($value->document_ownership);
            $row_d['created_at'] = dtFormat($value->created_at);
            $row_d['updated_at'] = dtFormat($value->updated_at);
            $row_d['document_encrypt_status'] = @$value->document_encrypt_status;
            $row_d['document_id'] = @$value->document_id;
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
            endforeach;
            // echo'<pre>';
            // print_r($data['dglist']);
            // exit();
            $count_all = ($data['dglist'])?$data['dglist']->total():0;
            $output = array(
              "draw" =>  Input::get('draw'),
              "recordsTotal" => @$count_all,
              "recordsFiltered" => @$count_all,
              "data" => $data_table
            );
                
            echo json_encode($output);
            // Set session for change criteria
            if(Input::get('doctypeid')){
                Session::put('dtid',Input::get('doctypeid'));
            }else{
                Session::forget('dtid');
            }
        
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }*/

    // Save criteria 
    public function saveCriteria(){

        $criteriaNameExists = DB::table('tbl_search_criteria')->where('criteria_name',@Input::get('criteria_name'))->where('user_id',Auth::user()->id)->exists();
        if(@$criteriaNameExists){
            // show error
            echo "0";exit;
        }else{

            // Prepare data
            $searchCriterias = array('criteria_name'=>Input::get('criteria_name'),
                                        'user_id'=> Auth::user()->id,
                                        'document_type_id'=>@Session::get('dtid'),
                                        'department_id'=>@implode(',',Session::get('departments')),
                                        'document_name'=>@Session::get('search_docname'),
                                        'search_option'=>Session::get('search_option'),
                                        'docno'=>@Session::get('serach_doc_no'),
                                        'stack_id'=>@implode(',',Session::get('stackids')),
                                        'ownership'=>@implode(',',Session::get('owner_ids')),
                                        'document_created_by'=>@implode(',',Session::get('created_by_owner_ids')),
                                        'document_modified_by'=>@implode(',',Session::get('updated_by_owner_ids')),
                                        'tagwords_category_id'=>@implode(',',Session::get('tagscate')),
                                        'tbl_tagwords'=>@implode(',',Session::get('keywords')),
                                        'created_date_from'=>@Session::get('search_created_date_from'),
                                        'created_date_to'=>@Session::get('search_created_date_to'),
                                        'last_modified_from'=>@Session::get('search_last_modified_from'),
                                        'last_modified_to'=>@Session::get('search_last_modified_to'));

            if(input::get('searchCriteriaId')){
                // Update
                unset($searchCriterias['criteria_name']);
                DB::table('tbl_search_criteria')->where('search_criteria_id',input::get('searchCriteriaId'))->update($searchCriterias);
                // create new doc types values
                if(Session::get('document_column_name')){
                    // Refresh doc types values by deleting old
                    DB::table('tbl_search_criteria_multiple_document_types_columns')->where('search_criteria_id',input::get('searchCriteriaId'))->delete();
                    // Creating new entry
                    $count = count(Session::get('document_column_name'));
                    for($x = 0; $x < $count; $x++){
                        $docTypeColmnValues = array('search_criteria_id'=>input::get('searchCriteriaId'),
                                                    'document_type_id'=>@Session::get('dtid'),
                                                    'document_type_column_id'=>@Session::get('document_type_column_id')[$x],
                                                    'document_type_column_name'=>@Session::get('document_column_name')[$x],
                                                    'document_type_column_value'=>@Session::get('document_column_value')[$x]);
                        DB::table('tbl_search_criteria_multiple_document_types_columns')->insert($docTypeColmnValues);
                    }
                    
                }
                echo "1";exit;// successfully updated for ajax response
            }else{
                // insert
                $searchCriterias_last_insertedId = DB::table('tbl_search_criteria')->insertGetId($searchCriterias);

                if(Session::get('document_column_name')){
                    // Insert doc types values
                    if($searchCriterias_last_insertedId){
                        $count = count(Session::get('document_column_name'));
                        for($x = 0; $x < $count; $x++){
                            $docTypeColmnValues = array('search_criteria_id'=>$searchCriterias_last_insertedId,
                                                        'document_type_id'=>@Session::get('dtid'),
                                                        'document_type_column_id'=>@Session::get('document_type_column_id')[$x],
                                                        'document_type_column_name'=>@Session::get('document_column_name')[$x],
                                                        'document_type_column_value'=>@Session::get('document_column_value')[$x]);
                            DB::table('tbl_search_criteria_multiple_document_types_columns')->insert($docTypeColmnValues);
                        }
                        
                    }
                }
            echo "2";exit;// successfully inserted for ajax response
            }
       }

    }

    // Get criteria
    public function getCriteria(){
        $data = DB::table('tbl_search_criteria')->select('tbl_search_criteria.*',DB::RAW('GROUP_CONCAT(tbl_search_criteria_multiple_document_types_columns.document_type_column_name) AS document_type_column_name'),DB::RAW('GROUP_CONCAT(tbl_search_criteria_multiple_document_types_columns.document_type_column_value) AS document_type_column_value'))->where('tbl_search_criteria.search_criteria_id',Input::get('id'))->leftJoin('tbl_search_criteria_multiple_document_types_columns','tbl_search_criteria_multiple_document_types_columns.search_criteria_id','=','tbl_search_criteria.search_criteria_id')->get(); 
        echo json_encode($data[0]);exit; // Ajax response
    }

public function advcsrchstatus(){
    if (Auth::user()) {
     $id= Input::get('id');
     $res = CookieRequest::cookie('toggleSrchStatus');
     if($res==0){
        $value = 1;
    }else{
        $value = 0;
    }
    Cookie::queue('toggleSrchStatus', $value, 43200);
    $res = CookieRequest::cookie('toggleSrchStatus');
    echo json_encode($res);
} else {
    return redirect('')->withErrors("Please login")->withInput();
}
}

public function showcookies(){
    echo Cookie::get('toggleSrchStatus');
}


public function columnsList()
{
    if (Auth::user()) {
       $doctypeid =     Input::get('doctypeid');
       Session::put('sess_settype',$doctypeid);
       Session::forget('doctypeid');
       $data['documentTypeData'] = DocumentTypesModel::find($doctypeid)->chlids;
       $count=DocumentTypesModel::find($doctypeid)->chlids->count();
       Session::put('sess_countcol',$count);
       //echo Session::get('sess_countcol');
       for($i=1;$i<=$count;$i++)
                {
                Session::forget('sess_doctypecol'.$i);
                }
       
       return View::make('pages/documents/collist')->with($data);
   } else {
    return redirect('')->withErrors("Please login")->withInput();
}
}

public function documentsSrchSubList()
{
    if (Auth::user()) {
       $doctypeid =     Input::get('doctypeid');
       $data['documentTypeData'] = DocumentTypesModel::find($doctypeid)->chlids;
       return View::make('pages/documents/srchsublist')->with($data);
   } else {
    return redirect('')->withErrors("Please login")->withInput();
}
}
public function getTagwords(){
        if (Auth::user()) {
            $keycatid   =     Input::get('tagcatid');
            $keyid = "";
            $key=Input::get('tagword');
            if($keycatid>0){
                for($i=0;$i<count($keycatid);$i++){
                    $kid = $keycatid[$i];
                    if($i==0){
                        $keyid = $kid;
                    }else if($i==count($keycatid)-1){
                        $keyid = $keyid.','.$kid;
                    }else{
                        $keyid = $keyid.','.$kid;
                    }
                }
                $keycatid[] = $keyid;
                $data['tagWords']= TagWordsModel::whereIn('tagwords_category_id', $keycatid)->get();
                $data['key']=$key;
            }else{
                $data['tagWords']= TagWordsModel::whereIn('tagwords_category_id', [0])->get();
                $data['key']=$key;
            }
            return View::make('pages/documents/keywrdlist')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
public function editdoc(Request $request,$id)
{
    if (Auth::user()) 
    {
        $document = TempDocumentsModel::select('document_name','document_file_name')->where('document_id',$id)->first();
        $change_folder_id=Input::get('hidd_folder_id');
        $change_folder_path=Input::get('up_folder');
        $selctdkeywrds = "";
        $documenttypeid = "";
        $deparmentid="";
        $stack = "";
        
        $keywrds = (Input::get('keywords'))?Input::get('keywords'):[''];
        $coltypcnt  = Input::get('coltypecnt');
        if($keywrds!="Please select tag category"){
            $keywrdsCnt = count($keywrds);
            for($i=0; $i < $keywrdsCnt; $i++){
                if($i == $keywrdsCnt-1){
                    $selctdkeywrds = $selctdkeywrds.($keywrds[$i]);
                }else{
                    $selctdkeywrds = $selctdkeywrds.($keywrds[$i] . ",");
                }
            }
        }else{
             $selctdkeywrds = [''];
        }


            // $coltypcnt  = Input::get('coltypecnt');
            // $keywrds = Input::get('keywords');
            //         if($keywrds!=null){
            //             $keywrdsCnt = count(Input::get('keywords'));
            //             for($i=0; $i < $keywrdsCnt; $i++){
            //                 if($i == $keywrdsCnt-1){
            //                     $selctdkeywrds = $selctdkeywrds.($keywrds[$i]);
            //                 }else{
            //                     $selctdkeywrds = $selctdkeywrds.($keywrds[$i] . ",");
            //                 }
            //             }
            //         }
            //         else{
            //             $selctdkeywrds="";
            //         }
        //---Doc types---
            $dctyp = Input::get('doctypeid');
            $stack = Input::get('stack');
            // if($stack)
            // {
            // $stackCnt = count($stack);
            // for($i=0; $i < $stackCnt; $i++){
            //     if($i == $stackCnt-1){
            //         $stackid = $stackid.($stack[$i]);
            //     }else{
            //         $stackid = $stackid.($stack[$i] . ",");
            //     }
            // }
            // }
            // else{
            //     $stackid="";
            // }
            $departments = Input::get('departmentid');
            $departmentsCnt = count(Input::get('departmentid'));
            for($i=0; $i < $departmentsCnt; $i++){
                if($i == $departmentsCnt-1){
                    $deparmentid = $deparmentid.($departments[$i]);
                }else{
                    $deparmentid = $deparmentid.($departments[$i] . ",");
                }
            }
            $documentMgmtTemp =   new TempDocumentsModel;
            $documentMgmtTemp=TempDocumentsModel::find($id);
            if(Input::get('save')) {
                $documentMgmtTemp->document_status  =   "Unpublished";
            } elseif(Input::get('draft')) {
                $documentMgmtTemp->document_status  =   "Draft";
            } elseif(Input::get('savepublish')) {
                $documentMgmtTemp->document_status  =   "Published";
            }
            $documentMgmtTemp->department_id        =   $deparmentid;
            $documentMgmtTemp->document_type_id     =   $dctyp;
            $documentMgmtTemp->document_no          =   Input::get('docno');
            $documentMgmtTemp->document_name        =   Input::get('docname');
            $documentMgmtTemp->stack_id             =   $stack;
            $documentMgmtTemp->document_version_no  =   "1.0";
            $documentMgmtTemp->document_path        =   trim(preg_replace('/\s*\([^)]*\)/', '', $change_folder_path));
            $documentMgmtTemp->document_ownership   =   Auth::user()->username;
            $documentMgmtTemp->document_created_by  =   Auth::user()->username;
            $documentMgmtTemp->document_tagwords    =   $selctdkeywrds;
            $documentMgmtTemp->parent_id            =   $change_folder_id;
            $documentMgmtTemp->document_expiry_date  =   @Input::get('document_expiry_date');
            $documentMgmtTemp->document_assigned_to  =   @Input::get('assign_users');
            $documentMgmtTemp->save();
            $lastId = $documentMgmtTemp->document_id;
            $duplicate_col=TempDocumentsColumnModel::where('document_id','=',$lastId)->get();
            if(count($duplicate_col)>0)
            {
                TempDocumentsColumnModel::where('document_id','=',$lastId)->delete();    
            }
            if($coltypcnt>0)
            {
                for($i=1;$i<=$coltypcnt;$i++){
                $tempdocumenttypecolModl   =   new TempDocumentsColumnModel;
                $tempdocumenttypecolModl->document_id =   $lastId;
                $tempdocumenttypecolModl->document_type_column_id    =   Input::get('docid'.$i);
                $tempdocumenttypecolModl->document_column_name   =   Input::get('doclabl'.$i);
                $tempdocumenttypecolModl->document_column_type   =   Input::get('doctype'.$i);
                /*if type = date then change document column value to date(y-m-d) format*/

                if(Input::get('doctype'.$i) == 'Date')
                {
                
                    $cal_date = Input::get('doccol'.$i);
                    $date = ($cal_date)?date('Y-m-d',strtotime($cal_date)):'';
                    $tempdocumenttypecolModl->document_column_value   =   $date;
                }
                else
                {

                    $tempdocumenttypecolModl->document_column_value   =   Input::get('doccol'.$i);
                }

                
                $tempdocumenttypecolModl->document_column_mandatory   =   Input::get('docmandatory'.$i);
                $tempdocumenttypecolModl->document_column_created_by=Auth::user()->username;
                $tempdocumenttypecolModl->document_column_modified_by=Auth::user()->username;
                $tempdocumenttypecolModl->save();
                }
            }
            // Save in audits
            $user = Auth::user()->username;
            // Get update action message
            $actionMsg = Lang::get('language.update_action_msg');
            $actionDes = $this->docObj->stringReplace($this->actionName2,$document->document_file_name,$user,$actionMsg);
            $result = (new AuditsController)->log(Auth::user()->username, 'Import Document', 'Edit', $actionDes);
            
            // Redirect if btn Save clicked
            if(Input::get('uniqueId')){
                return redirect()->route('documentEdit', ['id' => Input::get('uniqueId')])->with('data', '  '.@Input::get('docname').' updated successfully');
            }else{
                return redirect('listview?view=import&saved_search=1')->with('data', '  '.@Input::get('docname').' updated successfully ');
            }          
            //return redirect('tempAllView')->with('data', '  '.@Input::get('docname').' updated successfully ');               
    
        }else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    
}
public function editdocfilesave(Request $request,$id)
{echo $id;
    if (Auth::user()) 
    {
        
            $change_folder_id=Input::get('hidd_folder_id');
            $change_folder_path=Input::get('up_folder');
            $selctdkeywrds = "";
            $documenttypeid = "";
            $deparmentid="";
            $stack = "";
            $keywrds = (Input::get('keywords'))?Input::get('keywords'):[''];
            $coltypcnt  = Input::get('coltypecnt');
            if($keywrds!="Please select tag category"){
                $keywrdsCnt = count($keywrds);
                for($i=0; $i < $keywrdsCnt; $i++){
                    if($i == $keywrdsCnt-1){
                        $selctdkeywrds = $selctdkeywrds.($keywrds[$i]);
                    }else{
                        $selctdkeywrds = $selctdkeywrds.($keywrds[$i] . ",");
                    }
                }
            }else{
                 $selctdkeywrds = [''];
            }

                // $keywrds = Input::get('keywords');
                // $coltypcnt  = Input::get('coltypecnt');
                //             $keywrdsCnt = count(Input::get('keywords'));
                //             for($i=0; $i < $keywrdsCnt; $i++){
                //                 if($i == $keywrdsCnt-1){
                //                     $selctdkeywrds = $selctdkeywrds.($keywrds[$i]);
                //                 }else{
                //                     $selctdkeywrds = $selctdkeywrds.($keywrds[$i] . ",");
                //                 }
                //             }
                        //---Doc types---
                            $dctyp = Input::get('doctypeid');
                            $dctypCnt = count(Input::get('doctypeid'));
                            for($i=0; $i < $dctypCnt; $i++){
                                if($i == $dctypCnt-1){
                                    $documenttypeid = $documenttypeid.($dctyp[$i]);
                                    }else{
                                        $documenttypeid = $documenttypeid.($dctyp[$i] . ",");
                                        }
                                    }
                            $stack = Input::get('stack');
                            $stackCnt = count(Input::get('stack'));
                            // for($i=0; $i < $stackCnt; $i++){
                            //     if($i == $stackCnt-1){
                            //         $stackid = $stackid.($stack[$i]);
                            //     }else{
                            //         $stackid = $stackid.($stack[$i] . ",");
                            //     }
                            // }
                            $departments = Input::get('departmentid');
                            $departmentsCnt = count(Input::get('departmentid'));
                            for($i=0; $i < $departmentsCnt; $i++){
                                if($i == $departmentsCnt-1){
                                    $deparmentid = $deparmentid.($departments[$i]);
                                }else{
                                    $deparmentid = $deparmentid.($departments[$i] . ",");
                                }
                            }
                            $documentMgmtTemp =   new DocumentsModel;
                            $documentMgmtTemp=DocumentsModel::find($id);
                            if(Input::get('save')) {
                                $documentMgmtTemp->document_status  =   "Published";
                            } elseif(Input::get('draft')) {
                                $documentMgmtTemp->document_status  =   "Draft";
                            } elseif(Input::get('savepublish')) {
                                $documentMgmtTemp->document_status  =   "Published";
                            }
                            $documentMgmtTemp->department_id        =   $deparmentid;
                            $documentMgmtTemp->document_type_id     =   $documenttypeid;
                            $documentMgmtTemp->document_no          =   Input::get('docno');
                            $documentMgmtTemp->document_name        =   Input::get('docname');
                            $documentMgmtTemp->stack_id             =   $stack;
                            $documentMgmtTemp->document_version_no  =   "1.0";
                            $documentMgmtTemp->document_path        =   $change_folder_path;
                            $documentMgmtTemp->document_ownership   =   Auth::user()->username;
                            $documentMgmtTemp->document_created_by  =   Auth::user()->username;
                            $documentMgmtTemp->document_tagwords    =   $selctdkeywrds;
                            $documentMgmtTemp->parent_id            =   $change_folder_id;
                            $documentMgmtTemp->save();
                            $lastId = $documentMgmtTemp->document_id;
                            $duplicate_col=DocumentsColumnModel::where('document_id','=',$lastId)->get();
                            if(count($duplicate_col)>0)
                            {
                                DocumentsColumnModel::where('document_id','=',$lastId)->delete();    
                            }
                                if($coltypcnt>0){
                                    for($i=1;$i<=$coltypcnt;$i++){
                                    $tempdocumenttypecolModl   =   new DocumentsColumnModel;
                                    $tempdocumenttypecolModl->document_id =   $lastId;
                                    $tempdocumenttypecolModl->document_type_column_id    =   Input::get('docid'.$i);
                                    $tempdocumenttypecolModl->document_column_name   =   Input::get('doclabl'.$i);
                                    $tempdocumenttypecolModl->document_column_type   =   Input::get('doctype'.$i);
                                    
                                    /*if type = date then change document column value to date(y-m-d) format*/

                                    if(Input::get('doctype'.$i) == 'Date')
                                    {
                                        $cal_date = Input::get('doccol'.$i);
                                        $date = ($cal_date)?date('Y-m-d',strtotime($cal_date)):'';
                                        $tempdocumenttypecolModl->document_column_value   =   $date;
                                    }
                                    else
                                    {

                                        $tempdocumenttypecolModl->document_column_value   =   Input::get('doccol'.$i);
                                    }
                                    
                                    $tempdocumenttypecolModl->document_column_mandatory   =   Input::get('docmandatory'.$i);
                                    $tempdocumenttypecolModl->document_column_created_by=Auth::user()->username;
                                    $tempdocumenttypecolModl->document_column_modified_by=Auth::user()->username;
                                    $tempdocumenttypecolModl->save();
                                    }
                                }
                                // Save in audits
                                $user = Auth::user()->username;
                                
                                // Get update action message
                                $actionMsg = Lang::get('language.update_action_msg');
                                $actionDes = $this->docObj->stringReplace($this->actionName2,$documentMgmtTemp->document_name,$user,$actionMsg);
                                $result = (new AuditsController)->log(Auth::user()->username, 'Document', 'Edit',$actionDes);
                        
         return redirect('listview?view=list&saved_search=1')->with('data', 'Edited successfully.');               
    
    }//auth close
    else 
    {
        return redirect('')->withErrors("Please login")->withInput();
    }
    
}
public function saveAll(Request $request,$status,$ids=null)
{ 
    $array_id=explode(',', $ids);
    //from bulkimport edit
    if($ids == null)
    {
        $ids = Input::get('hidd_last_inserted');
        $array_id = explode(',', $ids);
        $array_id = array_values(array_filter($array_id));
    }
   if (Auth::user())
   {
        //$coltypcnt  = Input::get('coltypecnt');
        $change_folder_id=Input::get('hidd_folder_id');
        $change_folder_path=Input::get('up_folder');
        //$keywrds = Input::get('keywords');
        $expiry_date_doc = Input::get('document_expiry_date');
        $selctdkeywrds = "";
        $documenttypeid = "";
        $stackid = "";
        $deparmentid="";
    //Tags    
        $keywrds = (Input::get('keywords'))?Input::get('keywords'):[''];
        $coltypcnt  = Input::get('coltypecnt');
        if($keywrds!="Please select tag category"){
            $keywrdsCnt = count($keywrds);
            for($i=0; $i < $keywrdsCnt; $i++){
                if($i == $keywrdsCnt-1){
                    $selctdkeywrds = $selctdkeywrds.($keywrds[$i]);
                }else{
                    $selctdkeywrds = $selctdkeywrds.($keywrds[$i] . ",");
                }
            }
        }else{
             $selctdkeywrds = [''];
        }

        // if($keywrds!=null){
        //     $keywrdsCnt = count(Input::get('keywords'));
        //     for($i=0; $i < $keywrdsCnt; $i++){
        //         if($i == $keywrdsCnt-1){
        //             $selctdkeywrds = $selctdkeywrds.($keywrds[$i]);
        //         }else{
        //             $selctdkeywrds = $selctdkeywrds.($keywrds[$i] . ",");
        //         }
        //     }
        // }
        // else{
        //     $selctdkeywrds="";
        // }
    //Document types
        $documenttypeid = Input::get('doctypeid');
        
    //Stacks
        $stack = Input::get('stack');
        if($stack!=null)
        {
        $stackCnt = count(Input::get('stack'));
        for($i=0; $i < $stackCnt; $i++){
            if($i == $stackCnt-1){
                $stackid = $stackid.($stack[$i]);
            }else{
                $stackid = $stackid.($stack[$i] . ",");
            }
        }
        }
        else{
            $stackid="";
        }
    //Departments
        $departments = Input::get('departments');
        $departmentsCnt = count(Input::get('departments'));
        for($i=0; $i < $departmentsCnt; $i++){
            if($i == $departmentsCnt-1){
                $deparmentid = $deparmentid.($departments[$i]);
            }else{
                $deparmentid = $deparmentid.($departments[$i] . ",");
            }
        }

        if(($documenttypeid=="")&&($deparmentid=="")&&($stackid=="")&&($selctdkeywrds=="")&&($change_folder_path=="")&&($expiry_date_doc==""))
        {

            if($status=='unpublished')
                {
                    //from bulkimport edit
                    if (isset($_POST['save'])) {
                    # Publish-button was clicked
                        return redirect('importFile')->with('data', 'Edited successfully.');
                    }
                    else{
                        # Save-button was clicked
                        return redirect('listview?view=import')->with('data', 'Edited successfully.');
                    }
                }
            else
                {
                    return redirect('listview?view=list')->with('data', 'Edited successfully.');
                }

        }
        else
        {
    //Document columns fetch
        $array_doctype = $documenttypeid;
        $documentTypes = DB::table('tbl_document_types_columns')->select('document_type_column_name','document_type_column_id','document_type_id','document_type_column_type','document_type_column_mandatory')->where('document_type_id',$array_doctype)->get();
    //Check temp and documents doctypes are identical or not
        $document_types=DocumentsModel::select('document_type_id')->whereIn('document_id',$array_id)->get()->toArray();
        $temp_document_types=TempDocumentsModel::select('document_type_id')->whereIn('document_id',$array_id)->get()->toArray();
        $oneDimensionalArray = array_map('current', $document_types);
        $oneDimensionalArray_temp = array_map('current', $temp_document_types);

        //--------------Temp Documents-----------Unpublished-------------//   


        //print_r(Input::get('document_expiry_date'));exit;
        if(count(array_unique($oneDimensionalArray_temp)) == 1)//identical document types
        {
            
            if($oneDimensionalArray_temp[0]==$documenttypeid)//selecte doctypes and edit doctype are same
            {
                

                if($status=='unpublished')
                {
                    foreach ($array_id as $key => $value)
                    {
                        $document = TempDocumentsModel::select('document_name','document_file_name')->where('document_id',$value)->first();
                        $documentTemp                       =   new TempDocumentsModel;
                        $documentTemp= TempDocumentsModel::find($value);
                        if(Input::get('save')) {
                            $documentTemp->document_status  =   "Unpublished";
                        } elseif(Input::get('draft')) {
                            $documentTemp->document_status  =   "Draft";
                        }
                        if($deparmentid != null || $deparmentid != "")
                        {
                            $documentTemp->department_id        =   $deparmentid;
                        }  
                        if($documenttypeid != null || $documenttypeid != "")
                        {
                            $documentTemp->document_type_id     =   $documenttypeid;
                        }
                        if($stackid != null || $stackid != "")
                        {
                            $documentTemp->stack_id         =   $stackid;
                        }
                        $documentTemp->document_version_no  =   "1.0";
                        $documentTemp->document_modified_by =   Auth::user()->username;
                        $documentTemp->updated_at           =  date('Y-m-d h:i:s');
                        $documentTemp->document_expiry_date =  @Input::get('document_expiry_date');
                        if($selctdkeywrds != null || $selctdkeywrds != "")
                        {
                            $documentTemp->document_tagwords=   $selctdkeywrds;
                        }
                        if($change_folder_id != null || $change_folder_id !="")
                        {
                            $documentTemp->parent_id            =   $change_folder_id;
                        }
                        if($change_folder_path != null || $change_folder_path != "")
                        {
                            $documentTemp->document_path        =   $change_folder_path;
                        }
                        $documentTemp->save();
                        //doc expire notification update
                        if(Input::get('document_expiry_date')){
                            //call common function for doc expiry notification
                            
                            $this->docObj->commom_expiry_documents_check(null);
                        }
                        $lastId = $documentTemp->document_id;
                        // Save in audits
                        $user = Auth::user()->username;

                        // Get update action message
                        $actionMsg = Lang::get('language.update_action_msg');
                        $actionDes = $this->docObj->stringReplace($this->actionName2,$document->document_file_name,$user,$actionMsg);

                        $result = (new AuditsController)->log(Auth::user()->username, 'Import Document', 'Edit',$actionDes);

                        ///////updated from agm////////
                        if($documenttypeid != null || $documenttypeid != "")
                        {
                            //if no document columns then insert them
                            $exist_column_count = DB::table('tbl_temp_documents_columns')->where('document_id',$value)->count();
                            if(!$exist_column_count)
                            {
                                foreach ($documentTypes as $val) {
                                DB::table('tbl_temp_documents_columns')->insert(['document_id'=>$value,'document_type_column_id'=>$val->document_type_column_id,'document_column_name'=>$val->document_type_column_name,'document_column_type'=>$val->document_type_column_type,'document_column_mandatory'=>$val->document_type_column_mandatory,'document_column_value'=>null]);
                                }
                            }
                        }
                        ///////end updated from agm////////
                    }
                    //from bulkimport edit
                    if (isset($_POST['save'])) {
                    # Publish-button was clicked
                        return redirect('importFile')->with('data', 'Edited successfully.');
                    }
                    else{
                        # Save-button was clicked
                        return redirect('listview?view=import')->with('data', 'Edited successfully.');
                    }
                }
            }
            else
            {
                

                if($status=='unpublished')
                {  
                    if($documenttypeid != null || $documenttypeid != "")
                    {
                    //Duplicate columns delete
                    DB::table('tbl_temp_documents_columns')->whereIn('document_id',$array_id)->delete();
                    }
                    foreach ($array_id as $key => $value)
                    {
                        $document = TempDocumentsModel::select('document_name','document_file_name')->where('document_id',$value)->first();
                        $documentTemp                       =   new TempDocumentsModel;
                        $documentTemp= TempDocumentsModel::find($value);
                        if(Input::get('save')) {
                            $documentTemp->document_status  =   "Unpublished";
                        } elseif(Input::get('draft')) {
                            $documentTemp->document_status  =   "Draft";
                        } 
                        if($deparmentid != null || $deparmentid != "")
                        {
                            $documentTemp->department_id        =   $deparmentid;
                        }  
                        if($documenttypeid != null || $documenttypeid != "")
                        {
                            $documentTemp->document_type_id     =   $documenttypeid;
                        }
                        if($stackid != null || $stackid != "")
                        {
                            $documentTemp->stack_id         =   $stackid;
                        }
                        $documentTemp->document_version_no  =   "1.0";
                        $documentTemp->document_modified_by =   Auth::user()->username;
                        $documentTemp->updated_at           =  date('Y-m-d h:i:s');
                        $documentTemp->document_expiry_date =  @Input::get('document_expiry_date');
                        if($selctdkeywrds != null || $selctdkeywrds != "")
                        {
                            $documentTemp->document_tagwords=   $selctdkeywrds;
                        }
                        if($change_folder_id != null || $change_folder_id !="")
                        {
                            $documentTemp->parent_id            =   $change_folder_id;
                        }
                        if($change_folder_path != null || $change_folder_path != "")
                        {
                            $documentTemp->document_path        =   $change_folder_path;
                        }
                        $documentTemp->save();
                        //doc expire notification update
                        if(Input::get('document_expiry_date')){
                            //call common function for doc expiry notification
                            
                            $this->docObj->commom_expiry_documents_check(null);
                        }
                        $lastId = $documentTemp->document_id;

                        // Save in audits
                        $user = Auth::user()->username;
                        

                        // Get update action message
                        $actionMsg = Lang::get('language.update_action_msg');
                        $actionDes = $this->docObj->stringReplace($this->actionName2,$document->document_file_name,$user,$actionMsg);

                        $result = (new AuditsController)->log(Auth::user()->username, 'Import Document', 'Edit',$actionDes);
                        if($documenttypeid != null || $documenttypeid != "")
                        {
                            foreach ($documentTypes as $val) {
                            DB::table('tbl_temp_documents_columns')->insert(['document_id'=>$value,'document_type_column_id'=>$val->document_type_column_id,'document_column_name'=>$val->document_type_column_name,'document_column_type'=>$val->document_type_column_type,'document_column_mandatory'=>$val->document_type_column_mandatory,'document_column_value'=>null]);
                            }
                        }
                    }
                    //from bulkimport edit
                    if (isset($_POST['save'])) {
                    # Publish-button was clicked
                        return redirect('importFile')->with('data', 'Edited successfully.');
                    }
                    else{
                        # Save-button was clicked
                        return redirect('listview?view=import')->with('data', 'Edited successfully.');
                    }
                }
            }        
        }
        else
        {
           if($status=='unpublished')
            {
                

                //Duplicate columns delete
                if($documenttypeid != null || $documenttypeid != "")
                {
                    DB::table('tbl_temp_documents_columns')->whereIn('document_id',$array_id)->delete();
                }
                foreach ($array_id as $key => $value)
                {
                    $document = TempDocumentsModel::select('document_name','document_file_name')->where('document_id',$value)->first();
                    $documentTemp                       =   new TempDocumentsModel;
                    $documentTemp= TempDocumentsModel::find($value);
                    if(Input::get('save')) {
                        $documentTemp->document_status  =   "Unpublished";
                    } elseif(Input::get('draft')) {
                        $documentTemp->document_status  =   "Draft";
                    } 
                    if($deparmentid != null || $deparmentid != "")
                    {
                        $documentTemp->department_id        =   $deparmentid;
                    }  
                    if($documenttypeid != null || $documenttypeid != "")
                    {
                        $documentTemp->document_type_id     =   $documenttypeid;
                    }
                    if($stackid != null || $stackid != "")
                    {
                        $documentTemp->stack_id         =   $stackid;
                    }
                    $documentTemp->document_version_no  =   "1.0";
                    $documentTemp->document_modified_by =   Auth::user()->username;
                    $documentTemp->updated_at           =  date('Y-m-d h:i:s');
                    $documentTemp->document_expiry_date =  @Input::get('document_expiry_date');
                    if($selctdkeywrds != null || $selctdkeywrds != "")
                    {
                        $documentTemp->document_tagwords=   $selctdkeywrds;
                    }
                    if($change_folder_id != null || $change_folder_id !="")
                    {
                        $documentTemp->parent_id            =   $change_folder_id;
                    }
                    if($change_folder_path != null || $change_folder_path != "")
                    {
                        $documentTemp->document_path        =   $change_folder_path;
                    }
                    $documentTemp->save();
                    //doc expire notification update
                    if(Input::get('document_expiry_date')){
                        //call common function for doc expiry notification
                        
                        $this->docObj->commom_expiry_documents_check(null);
                    }
                    $lastId = $documentTemp->document_id;

                    // Save in audits
                    $user = Auth::user()->username;

                    // Get update action message
                    $actionMsg = Lang::get('language.update_action_msg');
                    $actionDes = $this->docObj->stringReplace($this->actionName2,$document->document_file_name,$user,$actionMsg);

                    $result = (new AuditsController)->log(Auth::user()->username, 'Import Document', 'Edit',$actionDes);
                    if($documenttypeid != null || $documenttypeid != "")
                    {
                        foreach ($documentTypes as $val) {
                        DB::table('tbl_temp_documents_columns')->insert(['document_id'=>$value,'document_type_column_id'=>$val->document_type_column_id,'document_column_name'=>$val->document_type_column_name,'document_column_type'=>$val->document_type_column_type,'document_column_mandatory'=>$val->document_type_column_mandatory,'document_column_value'=>null]);
                        }
                    }
                }
                //from bulkimport edit
                if (isset($_POST['save'])) {
                # Publish-button was clicked
                    return redirect('importFile')->with('data', 'Edited successfully.');
                }
                else {
                    # Save-button was clicked
                    return redirect('listview?view=import')->with('data', 'Edited successfully.');
                }
            } 
        }

        //------------Documents-----------------Published-----------//

        if(count(array_unique($oneDimensionalArray)) == 1)//identical document types
        {
            if($oneDimensionalArray[0]==$documenttypeid)//selecte doctypes and edit doctype are same
            {
                if($status=='published')
                {
                    foreach ($array_id as $key => $value)
                    {
                        $document = DocumentsModel::select('document_name','document_file_name')->where('document_id',$value)->first();
                        $documentTemp                       =   new DocumentsModel;
                        $documentTemp= DocumentsModel::find($value);
                        
                        if($deparmentid != null || $deparmentid != "")
                        {
                            $documentTemp->department_id        =   $deparmentid;
                        }  
                        if($documenttypeid != null || $documenttypeid != "")
                        {
                            $documentTemp->document_type_id     =   $documenttypeid;
                        }
                        if($stackid != null || $stackid != "")
                        {
                            $documentTemp->stack_id         =   $stackid;
                        }
                        $documentTemp->document_modified_by =   Auth::user()->username;
                        $documentTemp->updated_at           =  date('Y-m-d h:i:s');
                        $documentTemp->document_expiry_date =  @Input::get('document_expiry_date');
                        if($selctdkeywrds != null || $selctdkeywrds != "")
                        {
                            $documentTemp->document_tagwords=   $selctdkeywrds;
                        }
                        if($change_folder_id != null || $change_folder_id !="")
                        {
                            $documentTemp->parent_id            =   $change_folder_id;
                        }
                        if($change_folder_path != null || $change_folder_path != "")
                        {
                            $documentTemp->document_path        =   $change_folder_path;
                        }
                        $documentTemp->save();
                        //doc expire notification update
                        if(Input::get('document_expiry_date')){
                            //call common function for doc expiry notification
                            
                            $this->docObj->commom_expiry_documents_check(null);
                        }
                        $lastId = $documentTemp->document_id;
                        // Save in audits
                        $user = Auth::user()->username;
                       

                        // Get update action message
                        $actionMsg = Lang::get('language.update_action_msg');
                        $actionDes = $this->docObj->stringReplace($this->actionName1,$document->document_file_name,$user,$actionMsg);


                        $result = (new AuditsController)->log(Auth::user()->username, 'Document', 'Edit',$actionDes);
                    }
                    return redirect('listview?view=list')->with('data', 'Edited successfully.');
                }
            }
            else
            {
                if($status=='published')
                {
                    //Duplicate columns delete
                    if($documenttypeid != null || $documenttypeid != "")
                    {
                    DB::table('tbl_documents_columns')->whereIn('document_id',$array_id)->delete();
                    }
                    foreach ($array_id as $key => $value)
                    {
                        $document = DocumentsModel::select('document_name','document_file_name')->where('document_id',$value)->first();
                        $documentTemp                       =   new DocumentsModel;
                        $documentTemp= DocumentsModel::find($value);
                        
                        if($deparmentid != null || $deparmentid != "")
                        {
                            $documentTemp->department_id        =   $deparmentid;
                        }  
                        if($documenttypeid != null || $documenttypeid != "")
                        {
                            $documentTemp->document_type_id     =   $documenttypeid;
                        }
                        if($stackid != null || $stackid != "")
                        {
                            $documentTemp->stack_id         =   $stackid;
                        }
                        $documentTemp->document_modified_by =   Auth::user()->username;
                        $documentTemp->updated_at           =  date('Y-m-d h:i:s');
                        $documentTemp->document_expiry_date =  @Input::get('document_expiry_date');
                        if($selctdkeywrds != null || $selctdkeywrds != "")
                        {
                            $documentTemp->document_tagwords=   $selctdkeywrds;
                        }
                        if($change_folder_id != null || $change_folder_id !="")
                        {
                            $documentTemp->parent_id            =   $change_folder_id;
                        }
                        if($change_folder_path != null || $change_folder_path != "")
                        {
                            $documentTemp->document_path        =   $change_folder_path;
                        }
                        $documentTemp->save();
                        //doc expire notification update
                        if(Input::get('document_expiry_date')){
                            //call common function for doc expiry notification
                            
                            $this->docObj->commom_expiry_documents_check(null);
                        }
                        $lastId = $documentTemp->document_id;
                        // Save in audits
                        $user = Auth::user()->username;
                       
                        // Get update action message
                        $actionMsg = Lang::get('language.update_action_msg');
                        $actionDes = $this->docObj->stringReplace($this->actionName1,$document->document_file_name,$user,$actionMsg);
                        $result = (new AuditsController)->log(Auth::user()->username, 'Document', 'Edit',$actionDes);
                        if($documenttypeid != null || $documenttypeid != "")
                        {
                            foreach ($documentTypes as $val) {
                            DB::table('tbl_documents_columns')->insert(['document_id'=>$value,'document_type_column_id'=>$val->document_type_column_id,'document_column_name'=>$val->document_type_column_name,'document_column_type'=>$val->document_type_column_type,'document_column_mandatory'=>$val->document_type_column_mandatory,'document_column_value'=>null]);
                            }
                        }
                    }
                    return redirect('listview?view=list')->with('data', 'Edited successfully.');
                }
            }        
        }
        else
        {
            if($status=='published')
            {
                //Duplicate columns delete
                if($documenttypeid != null || $documenttypeid != "")
                {
                DB::table('tbl_documents_columns')->whereIn('document_id',$array_id)->delete();
                }
                foreach ($array_id as $key => $value)
                {
                    $document = DocumentsModel::select('document_name','document_file_name')->where('document_id',$value)->first();
                    $documentTemp                       =   new DocumentsModel;
                    $documentTemp= DocumentsModel::find($value);
                    
                    if($deparmentid != null || $deparmentid != "")
                    {
                        $documentTemp->department_id        =   $deparmentid;
                    }  
                    if($documenttypeid != null || $documenttypeid != "")
                    {
                        $documentTemp->document_type_id     =   $documenttypeid;
                    }
                    if($stackid != null || $stackid != "")
                    {
                        $documentTemp->stack_id         =   $stackid;
                    }
                    $documentTemp->document_modified_by =   Auth::user()->username;
                    $documentTemp->updated_at           =  date('Y-m-d h:i:s');
                    $documentTemp->document_expiry_date =  @Input::get('document_expiry_date');
                    if($selctdkeywrds != null || $selctdkeywrds != "")
                    {
                        $documentTemp->document_tagwords=   $selctdkeywrds;
                    }
                    if($change_folder_id != null || $change_folder_id !="")
                    {
                        $documentTemp->parent_id            =   $change_folder_id;
                    }
                    if($change_folder_path != null || $change_folder_path != "")
                    {
                        $documentTemp->document_path        =   $change_folder_path;
                    }
                    $documentTemp->save();
                    //doc expire notification update
                    if(Input::get('document_expiry_date')){
                        //call common function for doc expiry notification
                        
                        $this->docObj->commom_expiry_documents_check(null);
                    }
                    $lastId = $documentTemp->document_id;
                    // Save in audits
                    $user = Auth::user()->username;
                    
                    // Get update action message
                    $actionMsg = Lang::get('language.update_action_msg');
                    $actionDes = $this->docObj->stringReplace($this->actionName1,$document->document_file_name,$user,$actionMsg);
                    $result = (new AuditsController)->log(Auth::user()->username, 'Document', 'Edit',$actionDes);
                    if($documenttypeid != null || $documenttypeid != "")
                    {
                        foreach ($documentTypes as $val) {
                        DB::table('tbl_documents_columns')->insert(['document_id'=>$value,'document_type_column_id'=>$val->document_type_column_id,'document_column_name'=>$val->document_type_column_name,'document_column_type'=>$val->document_type_column_type,'document_column_mandatory'=>$val->document_type_column_mandatory,'document_column_value'=>null]);
                        }
                    }
                }
                return redirect('listview?view=list')->with('data', 'Edited successfully.');
            }
        }
    }
    }
    else
    {
        return redirect('')->withErrors("Please login")->withInput();
    }    
}
//bulk checkout from list views
public function blkCheckout()
{
    if (Auth::user()) 
    {
        $arr = Input::get('selected');
        $coments = Input::get('comments');
        $view = Input::get('view');
        $table = 'tbl_documents';//tbl_documents
        $data = $this->blk_chkout($table,$arr,$view,$coments);              
        echo json_encode("Bulk Checkout successfully.");
    }
    else 
    {
        return redirect('')->withErrors("Please login")->withInput();
    }
}

//delete all from list views
public function deleteAll()
{
    if (Auth::user()) 
    {
        $arr=Input::get('selected');
        $view = Input::get('view');
        switch($view)
        {
            case Lang::get('language.list_view')://list view
            case Lang::get('language.document_type_view'): //type
            case Lang::get('language.stack_view')://stack
            case Lang::get('language.department_view')://dept wise
               $heading = 'Documents';
               $table = 'tbl_documents';//tbl_documents
               $column_table = 'tbl_documents_columns';//tbl_documents_columns
               $note_table = 'tbl_temp_document_notes';//tbl_temp_document_notes
               $data = $this->delete_all($arr,$view,$table,$column_table,$note_table,$heading);
            break; 
            case Lang::get('language.import_view'): //import view
               $heading = 'Temp-Documents'; 
               $table = 'tbl_temp_documents';//tbl_temp_documents
               $column_table = 'tbl_temp_documents_columns';//tbl_temp_documents_columns
               $note_table = 'tbl_document_notes';//tbl_temp_document_notes
               $data = $this->delete_all($arr,$view,$table,$column_table,$note_table,$heading);
            break;
        }
        echo json_encode("Documents deleted successfully.");
    }
    else 
    {
        return redirect('')->withErrors("Please login")->withInput();
    }
}
public function delete_all($arr,$view,$table,$column_table,$note_table,$heading)
{
    foreach ($arr as $key => $value) 
    {
        $documents = DB::table($table)->where('document_id',$value)->get();
        foreach ($documents as $data) 
        {
            DB::table($table)->where('document_id','=',$value)->delete();
            DB::table($column_table)->where('document_id','=',$value)->delete();
            DB::table($note_table)->where('document_id','=',$value)->delete();
            
            if($view==Lang::get('language.import_view')){
                $destinationPath = config('app.temp_document_path'); // upload path
            }else{
                $destinationPath = config('app.base_path'); // upload path
            }
            $document_file_name = ($data->document_file_name)?$data->document_file_name:0;
            /* Prevent File deletion - Same file exists in more document result*/
            $multiple = DocumentsModel::check_file_owners($table,$value,$document_file_name); 
            if(file_exists($destinationPath."/".$document_file_name) && ($document_file_name) && ($multiple == 0)){
            unlink($destinationPath."/".$document_file_name);
            }
            // Save audits
            $name = $data->document_file_name;
            $user = Auth::user()->username;
            // Get delete action message
            $actionMsg = Lang::get('language.delete_action_msg');
            $actionDes = $this->docObj->stringReplace($this->actionName2,$name,$user,$actionMsg);
            $result = (new AuditsController)->log(Auth::user()->username, $heading, 'Delete',$actionDes);
        }
    }
}

public function blk_chkout($table,$arr,$view,$coments)
{
    $files = array();
    foreach ($arr as $key => $value) 
    {
        $documents = DB::table($table)->where('document_id',$value)->get();
        foreach ($documents as $data) 
        {
            // Update check out model
            $docid = $data->document_id;
            $name = $data->document_file_name;
            $document_name = $data->document_name;
            // Update check out model
            DocumentsModel::where('document_id',$docid)->update(['document_status' => "Checkout",'document_pre_status' => 'Checkout','documents_checkout_by'=>Auth::user()->id,'document_checkout_date'=>date('Y-m-d h:i:s'),'document_modified_by'=>Auth::user()->username]);

            $duplicate_doc=DocumentsCheckoutModel::where('document_id','=',$docid)->get();
            if(count($duplicate_doc)>=0)
            {
                DocumentsCheckoutModel::where('document_id','=',$docid)->delete();    
            } 
            $doc_items = DocumentsModel::where('document_id', '=', $docid )->get()->toArray();
            foreach ($doc_items as $data1) { 
                $data1['documents_checkout_by'] = Auth::user()->id;
                $checkmodl= new DocumentsCheckoutModel;
                $checkmodl->insert($data1);
            }
            $duplicate_col=DocumentsColumnCheckoutModel::where('document_id','=',$docid)->get();
            if(count($duplicate_col)>=0)
            {
                DocumentsColumnCheckoutModel::where('document_id','=',$docid)->delete();    
            } 
            $doc_columns=DocumentsColumnModel::where('document_id', '=', $docid )->get()->toArray(); 
            foreach ($doc_columns as $columns) { 
                $col_checkmodl= new DocumentsColumnCheckoutModel;
                $col_checkmodl->insert($columns);
            }// Update check out model

            $documentNteModl                  =   new DocumentNoteModel;
            $documentNteModl->document_id     =   $docid;
            $documentNteModl->document_note   =   $coments;
            $documentNteModl->document_note_created_by  =   Auth::user()->username;
            $documentNteModl->save();

            DocumentsModel::where('document_id',$docid)->update(['document_status' => "Checkout",'document_pre_status' => 'Checkout','documents_checkout_by'=>Auth::user()->id,'document_checkout_date'=>date('Y-m-d h:i:s'),'document_modified_by'=>Auth::user()->username]);

            array_push($files, $name);
            
            //delete zip file in first case only for avoid previous results
            // For audits
            $documentsData = DB::table('tbl_documents')->select('document_no','document_name','document_path')->where('document_id',$docid)->get();        
            $user = Auth::user()->username;            
            $actionMsg = Lang::get('language.checkOut_action_msg');
            $actionDes = $this->docObj->stringReplace($this->actionName1,$name,$user,$actionMsg);
            $result = (new AuditsController)->dcmntslog(Auth::user()->username, $docid, 'Document', 'Check Out',$actionDes,$documentsData[0]->document_no,$documentsData[0]->document_name,$documentsData[0]->document_path);
            //echo $name;
        }
    }

    //file unique for different users
    $datetime = date("Y-m-d_H-i-s");
    $filename_checkout = Config::get('constants.blkcheckout_file').$datetime.'.zip';
    $filename_checkout = str_replace("username",Auth::user()->username,$filename_checkout);
    Session::put('checkout_zip_file',$filename_checkout);
    $destinationPath  = config('app.checkout_path'); // checkout path
        if(!file_exists($destinationPath))
        {
            //create directory checkout
            File::makeDirectory($destinationPath, $mode = 0777, true, true);
        }
    $filename = config('app.checkout_path').$filename_checkout;
    //zip file create here
    $zip = new ZipArchive;
    if($zip->open($filename, ZipArchive::CREATE)=== TRUE){
        foreach ($files as $file){
            //if file exists
            if(file_exists(config('app.base_path').$file)){
                $zip->addFile(config('app.base_path').$file, $file);
            }
        }
    }
    else
    {
        echo "zip not created";
    }
    $zip->close();

}

public function downloadCheckout()
{  
    if (Auth::user()) 
    {

        $file= config('app.checkout_path').Session::get('checkout_zip_file');
        if(!file_exists($file))
        {
            return redirect()->back()->with('data', 'Zip does not exist');
            exit();
        }
        else
        {
            return Response::download($file,Session::get('checkout_zip_file'));  
        }
        //delete aftr download enble this code
        //return Response::download($file,Session::get('checkout_zip_file'))->deleteFileAfterSend(true);  
    } else {
        return redirect('')->withErrors("Please login")->withInput();
    }
}

public function deleteAllPrevious()
{
$arr=Input::get('selected');
if (Auth::user()) {
            foreach ($arr as $key => $value) {
            $documents = DocumentHistoryModel::where('document_history_id',$value)->get();
            foreach ($documents as $data) {
            DocumentHistoryModel::where('document_history_id','=',$value)->delete();
            DocumentHistoryColumnModel::where('document_history_id','=',$value)->delete();
            $destinationPath = config('app.backup_path'); // upload path
            

            if(file_exists($destinationPath."/".$data->document_file_name)){
            unlink($destinationPath."/".$data->document_file_name);
        	}

            // Save in audits
            $name = $data->document_file_name;
            $user = Auth::user()->username;
            
            
            // Get delete action message
            $actionMsg = Lang::get('language.delete_action_msg');
            $actionDes = $this->docObj->stringReplace($this->actionName1,$name,$user,$actionMsg);

            $result = (new AuditsController)->dcmntslog(Auth::user()->username,$value,'Document', 'Delete',$actionDes,$data->document_no,$data->document_file_name,$data->document_path);
            }
            }
            echo json_encode("Documents deleted successfully.");
            }
        else {
            return redirect('')->withErrors("Please login")->withInput();
        }
}

/************** MOVE ALL START *************/
public function moveAll()
{
    //selected docs for move
    $arr=Input::get('selected')?:array();
    $doctype=Input::get('doctype');
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
                $file_data_exists = DocumentsModel::check_file_data_exists($data->document_file_name);
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
                        $tbl_documents_modl= DocumentsModel::find($dup_doc_id);
                        if(!$tbl_documents_modl)
                        {
                           $tbl_documents_modl=new DocumentsModel;     
                        }
;    
                    }
                    else
                    {
                        $tbl_documents_modl=new DocumentsModel;
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
            $destinationPath    =   config('app.base_path');
            if($oldFileName)
            {
                $source         =   config('app.temp_document_path').$oldFileName;
                $dest           =   $destinationPath.$oldFileName;
                if(file_exists($source))
                { //copy the file source to destination
                    $copy = copy($source, $dest);
                    unlink($source);//reomve original file
                }           
            }        
            
            $data_from_temp_col = DB::table('tbl_temp_documents_columns')->where('document_id',$value )->get();
            $affectedRows = DocumentsColumnModel::where('document_id', '=', $last_ins_id)->delete();
            foreach ($data_from_temp_col as $data)
            {         
                $tbl_col_documents_modl=new DocumentsColumnModel;      
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
                $tbl_note_modl=new DocumentNoteModel;
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
        $doc_type_name = DocumentTypesModel::select('document_type_name')->where('document_type_id',$doctype)->first();
        if($checkin_action == 'discard')
        {
                   
            $message = "Document discarded successfully";
        }   
        else if($published_count)
        {
           

           if($checkin_type == 'draft')
            {
                 $message .= $published_count." ".trans('documents.Documents_published_draft'); 
                 $result = (new AuditsController)->dcmntslog(Auth::user()->username,$data->document_id,'Document','Import And Draft','Document Type:\''.$doc_type_name->document_type_name.'\' '.$published_count.' Documents Drafted','','','','','');
            }
            else
            {
                $message .= $published_count." ".trans('documents.Documents_published'); 
                $result = (new AuditsController)->dcmntslog(Auth::user()->username,$data->document_id,'Document','Import And Publish','Document Type:\''.$doc_type_name->document_type_name.'\' '.$published_count.' Documents Published','','','','','');
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
public function moveCheckedAll()
{
    $arr=Input::get('selected');
    $response_format=Input::get('response_format');
    if (Auth::user()) 
    {
        $dataSet = [];
        $dataCol=[];
        
        $data_from_checked=DocumentsCheckoutModel::whereIn('document_id',$arr)->get(); 
        // $duplicate_doc=DocumentsCheckoutModel::whereIn('document_id',$arr)->get();
        // if(count($duplicate_doc)>=0)
        //     {
        //         DocumentsCheckoutModel::whereIn('document_id',$arr)->delete();    
        //     } 
        foreach ($data_from_checked as $data) 
        {
            if(($data->document_type_id != 0) && ($data->document_name != null))
            { 
            $tbl_documents_modl=new DocumentsModel;
            $tbl_documents_modl= DocumentsModel::find($data->document_id);//update documents
            $tbl_documents_modl->document_type_id      = $data->document_type_id;
            $tbl_documents_modl->document_name         = $data->document_name;
            $tbl_documents_modl->document_file_name    = $data->document_file_name;
            $tbl_documents_modl->parent_id             = $data->parent_id;
            $tbl_documents_modl->department_id         = $data->department_id;
            $tbl_documents_modl->stack_id              = $data->stack_id;
            $tbl_documents_modl->document_version_no   = $data->document_version_no+0.1;
            $tbl_documents_modl->document_ownership    = $data->document_ownership;
            $tbl_documents_modl->document_created_by   = $data->document_created_by;
            $tbl_documents_modl->document_modified_by  = Auth::user()->username;
            $tbl_documents_modl->document_tagwords     = $data->document_tagwords;
            $tbl_documents_modl->document_no           = $data->document_no;
            $tbl_documents_modl->document_path         = trim(preg_replace('/\s*\([^)]*\)/', '', $data->document_path));
            $tbl_documents_modl->document_status       = "Published";
            $tbl_documents_modl->created_at            = $data->created_at;
            $tbl_documents_modl->updated_at            = date('Y-m-d H:i:s');
            $tbl_documents_modl->document_checkin_date = date('Y-m-d H:i:s');
            $tbl_documents_modl->document_checkout_path= $data->document_checkout_path;
            $tbl_documents_modl->document_size         = $data->document_size;
            $tbl_documents_modl->document_expiry_date  =$data->document_expiry_date;
            $tbl_documents_modl->save();

            

            // save in audits
            $name = $data->document_file_name;
            $user = Auth::user()->username;
    

            // Get checkInAndPublish_action_msg action message
            $actionMsg = Lang::get('language.checkInAndPublish_action_msg');
            $actionDes = $this->docObj->stringReplace($this->actionName1,$name,$user,$actionMsg);

            $result = (new AuditsController)->dcmntslog(Auth::user()->username,$data->document_id,'Document', 'Check In And Publish',$actionDes,$data->document_no,$data->document_file_name,trim(preg_replace('/\s*\([^)]*\)/', '', $data->document_path)));

            }
            
            else if(($data->document_type_id == 0) || ($data->document_name == null))
            {
                echo "Some required information is missing. Please correct your entries and try again.";
                exit();
            }
        }
            $data_from_check_col = DocumentsColumnCheckoutModel::whereIn('document_id',$arr )->get();

            $duplicate_col=DocumentsColumnModel::whereIn('document_id',$arr)->get();
            if(count($duplicate_col)>=0)
                {
                    DocumentsColumnModel::whereIn('document_id',$arr)->delete();    
                } 
            foreach ($data_from_check_col as $data) { 
                $tbl_documents_col=new DocumentsColumnModel;
                $tbl_documents_col->document_id=$data->document_id;
                $tbl_documents_col->document_type_column_id=$data->document_type_column_id;
                $tbl_documents_col->document_column_name=$data->document_column_name;
                $tbl_documents_col->document_column_value=$data->document_column_value;
                $tbl_documents_col->document_column_type=$data->document_column_type;
                $tbl_documents_col->document_column_mandatory=$data->document_column_mandatory;
                $tbl_documents_col->document_column_created_by=$data->document_column_created_by;
                $tbl_documents_col->document_column_modified_by=Auth::user()->username;
                $tbl_documents_col->created_at=$data->created_at;
                $tbl_documents_col->updated_at=date('Y-m-d h:i:s');
                $tbl_documents_col->save();
            }

            foreach ($arr as $value){
            DB::table('tbl_documents_checkout')->where('document_id', $value)->delete();
            DB::table('tbl_documents_columns_checkout')->where('document_id', $value)->delete();
            }
                    if($response_format == 'json')
                    {
                        $message = "Documents published successfully.";
                        $response = array('status' => 2);
                        $response['message'] = trim($message);
                        echo json_encode($response);
                    }
                    else
                    {
                        echo json_encode("Documents published successfully.");
                    }
                    
            }
        else {
            return redirect('')->withErrors("Please login")->withInput();
        }
}

public function moveAsDraft()
{

    //selected docs for move
    $arr=Input::get('selected');

    if (Auth::user()) 
    {

        //check document type columns
        $data_from_temp_columns_count = TempDocumentsColumnModel::whereIn('document_id',$arr)->exists();
        //if no entries in documents temp columns alert
        /*if(!$data_from_temp_columns_count)
        {
            echo "1";
            //echo json_encode('Cannot publish documents, Please fill document type columns.');
            exit();  
        }*/

        $dataSet = [];
        $dataCol=[];
        $last_ins_id =array();
        
        //Temp documents table to Main Documents table
        foreach ($arr as $value) 
        {
            $data = DB::table('tbl_temp_documents')->where('document_id',$value)->first();
            if($data)
            { 
                $tbl_documents_modl=new DocumentsModel;
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
                $tbl_documents_modl->document_status       = "Draft";
                $tbl_documents_modl->document_assigned_to  = 'NULL';
                $tbl_documents_modl->created_at            = date('Y-m-d h:i:s');
                $tbl_documents_modl->updated_at            = date('Y-m-d h:i:s');
                $tbl_documents_modl->document_size         = $data->document_size;
                $tbl_documents_modl->document_expiry_date  = $data->document_expiry_date;
                $tbl_documents_modl->save();
                $last_ins_id= $tbl_documents_modl->document_id;

                //move the file from source to destination
                $oldFileName        =   $data->document_file_name;
                $destinationPath    =   config('app.base_path');
                if($oldFileName){
                    $source         =   config('app.temp_document_path').$oldFileName;
                    $dest           =   $destinationPath.$oldFileName;
                    if(file_exists($source) && $oldFileName)
                    { //copy the file source to destination
                        $copy = copy($source, $dest);
                        unlink($source);//reomve original file
                    }           
                }
            }
            //columns table
            $data_from_temp_col = DB::table('tbl_temp_documents_columns')->where('document_id',$value )->get();
       
            foreach ($data_from_temp_col as $data)
            {         
            $tbl_col_documents_modl=new DocumentsColumnModel;      
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
                $tbl_note_modl=new DocumentNoteModel;
                $tbl_note_modl->document_notes_id       =   '';
                $tbl_note_modl->document_id             =   $last_ins_id;
                $tbl_note_modl->document_note           =   $note->document_note;
                $tbl_note_modl->document_note_created_by=   $note->document_note_created_by;
                $tbl_note_modl->document_note_modified_by=   $note->document_note_modified_by;
                $tbl_note_modl->created_at              =   $note->created_at;
                $tbl_note_modl->updated_at              =   $note->updated_at;
                $tbl_note_modl->save();
            }
             

        }
            //delete all moved files from temp documents
            foreach ($arr as $value)
            {
                DB::table('tbl_temp_documents')->where('document_id', $value)->delete();
                DB::table('tbl_temp_documents_columns')->where('document_id', $value)->delete();
                DB::table('tbl_temp_document_notes')->where('document_id', $value)->delete();
            }
        
        //Audiit table entry
            $published_count = count($arr);
            if($published_count>0)
            {
                $result = (new AuditsController)->dcmntslog(Auth::user()->username,$data->document_id,'Document','Import And Draft','Document Type:\''.$doc_type_name->document_type_name.'\' '.$published_count.' Documents Drafted','','','','','');
            }
        echo "2";exit();
        //echo json_encode("Documents published successfully.");
    }
    else 
    {
        return redirect('')->withErrors("Please login")->withInput();
    } 
}

/* To delete START*/
public function moveAll_bkup()
{
    //selected docs for move
    $arr=Input::get('selected');

    if (Auth::user()) 
    {

        //check document type columns
        $data_from_temp_columns_count = TempDocumentsColumnModel::whereIn('document_id',$arr)->exists();
        //if no entries in documents temp columns alert
        /*if(!$data_from_temp_columns_count)
        {
            echo "1";
            //echo json_encode('Cannot publish documents, Please fill document type columns.');
            exit();  
        }*/

        $dataSet = [];
        $dataCol=[];
        $last_ins_id =array();
        
        //Temp documents table to Main Documents table
        foreach ($arr as $value) 
        {
            $data = DB::table('tbl_temp_documents')->where('document_id',$value)->first();
            if($data)
            { 
                $tbl_documents_modl=new DocumentsModel;
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
                    $tbl_documents_modl->created_at            = date('Y-m-d h:i:s');
                    $tbl_documents_modl->updated_at            = date('Y-m-d h:i:s');
                    $tbl_documents_modl->document_size         = $data->document_size;
                    $tbl_documents_modl->document_expiry_date  = $data->document_expiry_date;
                    $tbl_documents_modl->save();
                    $last_ins_id= $tbl_documents_modl->document_id;
            }
            //columns table
            $data_from_temp_col = DB::table('tbl_temp_documents_columns')->where('document_id',$value )->get();
       
            foreach ($data_from_temp_col as $data)
            {         
            $tbl_col_documents_modl=new DocumentsColumnModel;      
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
                $tbl_note_modl=new DocumentNoteModel;
                $tbl_note_modl->document_notes_id       =   '';
                $tbl_note_modl->document_id             =   $last_ins_id;
                $tbl_note_modl->document_note           =   $note->document_note;
                $tbl_note_modl->document_note_created_by=   $note->document_note_created_by;
                $tbl_note_modl->document_note_modified_by=   $note->document_note_modified_by;
                $tbl_note_modl->created_at              =   $note->created_at;
                $tbl_note_modl->updated_at              =   $note->updated_at;
                $tbl_note_modl->save();
            }
             

        }
            //delete all moved files from temp documents
            foreach ($arr as $value)
            {
                DB::table('tbl_temp_documents')->where('document_id', $value)->delete();
                DB::table('tbl_temp_documents_columns')->where('document_id', $value)->delete();
                DB::table('tbl_temp_document_notes')->where('document_id', $value)->delete();
            }
        
            //Audiit table entry
            $published_count = count($arr);
            if($published_count>0)
            {
                $result = (new AuditsController)->dcmntslog(Auth::user()->username,$data->document_id,'Document','Import And Publish','Document Type:\''.$doc_type_name->document_type_name.'\' '.$published_count.' Documents Published','','','','','');
            }
        echo "2";exit();
        //echo json_encode("Documents published successfully.");
    }
    else 
    {
        return redirect('')->withErrors("Please login")->withInput();
    }
}
public function moveCheckedAll_bkup()
{
    $arr=Input::get('selected');
    if (Auth::user()) 
    {
        $dataSet = [];
        $dataCol=[];
        
        $data_from_checked=DocumentsCheckoutModel::whereIn('document_id',$arr)->get(); 
        // $duplicate_doc=DocumentsCheckoutModel::whereIn('document_id',$arr)->get();
        // if(count($duplicate_doc)>=0)
        //     {
        //         DocumentsCheckoutModel::whereIn('document_id',$arr)->delete();    
        //     } 
        foreach ($data_from_checked as $data) 
        {
            if(($data->document_type_id != 0) && ($data->document_name != null))
            { 
            $tbl_documents_modl=new DocumentsModel;
            $tbl_documents_modl= DocumentsModel::find($data->document_id);//update documents
            $tbl_documents_modl->document_type_id      = $data->document_type_id;
            $tbl_documents_modl->document_name         = $data->document_name;
            $tbl_documents_modl->document_file_name    = $data->document_file_name;
            $tbl_documents_modl->parent_id             = $data->parent_id;
            $tbl_documents_modl->department_id         = $data->department_id;
            $tbl_documents_modl->stack_id              = $data->stack_id;
            $tbl_documents_modl->document_version_no   = $data->document_version_no+0.1;
            $tbl_documents_modl->document_ownership    = $data->document_ownership;
            $tbl_documents_modl->document_created_by   = $data->document_created_by;
            $tbl_documents_modl->document_modified_by  = Auth::user()->username;
            $tbl_documents_modl->document_tagwords     = $data->document_tagwords;
            $tbl_documents_modl->document_no           = $data->document_no;
            $tbl_documents_modl->document_path         = trim(preg_replace('/\s*\([^)]*\)/', '', $data->document_path));
            $tbl_documents_modl->document_status       = "Published";
            $tbl_documents_modl->created_at            = $data->created_at;
            $tbl_documents_modl->updated_at            = date('Y-m-d h:i:s');
            $tbl_documents_modl->document_checkin_date = date('Y-m-d h:i:s');
            $tbl_documents_modl->document_checkout_path= $data->document_checkout_path;
            $tbl_documents_modl->document_size         = $data->document_size;
            $tbl_documents_modl->document_expiry_date  =$data->document_expiry_date;
            $tbl_documents_modl->save();

            // save in audits
            $name = $data->document_file_name;
            $user = Auth::user()->username;
    

            // Get checkInAndPublish_action_msg action message
            $actionMsg = Lang::get('language.checkInAndPublish_action_msg');
            $actionDes = $this->docObj->stringReplace($this->actionName1,$name,$user,$actionMsg);

            $result = (new AuditsController)->dcmntslog(Auth::user()->username,$data->document_id,'Document', 'Check In And Publish',$actionDes,$data->document_no,$data->document_file_name,trim(preg_replace('/\s*\([^)]*\)/', '', $data->document_path)));

            }
            
            else if(($data->document_type_id == 0) || ($data->document_name == null))
            {
                echo "Some required information is missing. Please correct your entries and try again.";
                exit();
            }
        }
            $data_from_check_col = DocumentsColumnCheckoutModel::whereIn('document_id',$arr )->get();

            $duplicate_col=DocumentsColumnModel::whereIn('document_id',$arr)->get();
            if(count($duplicate_col)>=0)
                {
                    DocumentsColumnModel::whereIn('document_id',$arr)->delete();    
                } 
            foreach ($data_from_check_col as $data) { 
                $tbl_documents_col=new DocumentsColumnModel;
                $tbl_documents_col->document_id=$data->document_id;
                $tbl_documents_col->document_type_column_id=$data->document_type_column_id;
                $tbl_documents_col->document_column_name=$data->document_column_name;
                $tbl_documents_col->document_column_value=$data->document_column_value;
                $tbl_documents_col->document_column_type=$data->document_column_type;
                $tbl_documents_col->document_column_mandatory=$data->document_column_mandatory;
                $tbl_documents_col->document_column_created_by=$data->document_column_created_by;
                $tbl_documents_col->document_column_modified_by=Auth::user()->username;
                $tbl_documents_col->created_at=$data->created_at;
                $tbl_documents_col->updated_at=date('Y-m-d h:i:s');
                $tbl_documents_col->save();
            }

            foreach ($arr as $value){
            DB::table('tbl_documents_checkout')->where('document_id', $value)->delete();
            DB::table('tbl_documents_columns_checkout')->where('document_id', $value)->delete();
            }
            echo json_encode("Documents published successfully.");
            }
        else {
            return redirect('')->withErrors("Please login")->withInput();
        }
}
/* To delete END*/



public function savefile(Request $request, $id)
{
    if (Auth::user()) {
        $change_folder_id=Input::get('hidd_folder_id');
        $change_folder_path=Input::get('up_folder');
        $selctdkeywrds = "";
        $documenttypeid = Input::get('doctypeid');
        $stackid="";
        $deparmentid="";
        // $coltypcnt  = Input::get('coltypecnt');
        // $keywrds = Input::get('keywords');
        $note_add = Input::get('note_assign');//note
    // if($keywrds!=null){
    //     $keywrdsCnt = count(Input::get('keywords'));
    //     for($i=0; $i < $keywrdsCnt; $i++){
    //         if($i == $keywrdsCnt-1){
    //             $selctdkeywrds = $selctdkeywrds.($keywrds[$i]);
    //         }else{
    //             $selctdkeywrds = $selctdkeywrds.($keywrds[$i] . ",");
    //         }
    //     }
    // }
    // else{
    //     $selctdkeywrds="";
    // }

        $keywrds = (Input::get('keywords'))?Input::get('keywords'):[''];
        $coltypcnt  = Input::get('coltypecnt');
        if($keywrds!="Please select tag category"){
            $keywrdsCnt = count($keywrds);
            for($i=0; $i < $keywrdsCnt; $i++){
                if($i == $keywrdsCnt-1){
                    $selctdkeywrds = $selctdkeywrds.($keywrds[$i]);
                }else{
                    $selctdkeywrds = $selctdkeywrds.($keywrds[$i] . ",");
                }
            }
        }else{
             $selctdkeywrds = [''];
        }

        $stack = Input::get('stack');
      
        // if($stack){
        // $stackCnt = count(Input::get('stack'));
        //     for($i=0; $i < $stackCnt; $i++){
        //         if($i == $stackCnt-1){
        //             $stackid = $stackid.($stack[$i]);
        //         }else{
        //             $stackid = $stackid.($stack[$i] . ",");
        //         }
        //     }
        // }else{
        //     $stackid = 0;
        // }
        $departments = Input::get('departmentid');
        $departmentsCnt = count(Input::get('departmentid'));
        for($i=0; $i < $departmentsCnt; $i++){
            if($i == $departmentsCnt-1){
                $deparmentid = $deparmentid.($departments[$i]);
            }else{
                $deparmentid = $deparmentid.($departments[$i] . ",");
            }
        }

        $documentMgmtModl                       =   new DocumentsModel;
        if(Input::get('save')) 
        {
            $documentMgmtModl->document_status  =   "Published";
        } 
        elseif(Input::get('draft')) 
        {
            $documentMgmtModl->document_status  =   "Draft";
        } 
        elseif(Input::get('savepublish')) 
        {
            if((Input::get('assign_users'))!="")
            {
            $documentMgmtModl->document_status  =   "Review";
            }
            else
            {
            $documentMgmtModl->document_status  =   "Published";
            }
        }

        //move the file from source to destination
        $oldFileName        =   Input::get('hidd_file');
        $destinationPath    =   config('app.base_path');
        if($oldFileName){
            $source         =   config('app.temp_document_path').$oldFileName;
            $dest           =   $destinationPath.$oldFileName;
            if(file_exists($source)){ //copy the file source to destination
                $copy = copy($source, $dest);
                unlink($source);//reomve original file
            }           
        }

        
        $documentMgmtModl->department_id        =   $deparmentid;
        $documentMgmtModl->document_type_id     =   $documenttypeid;
        $documentMgmtModl->document_no          =   Input::get('docno');
        $DocName=$documentMgmtModl->document_name =   Input::get('docname');
        $documentMgmtModl->stack_id             =   $stack;
        $documentMgmtModl->document_version_no  =   "1.0";
        $documentMgmtModl->document_file_name   =   Input::get('hidd_file');
        $documentMgmtModl->document_size        =   Input::get('hidd_file_size');
        $documentMgmtModl->document_ownership   =   Auth::user()->username;
        $documentMgmtModl->document_created_by  =   Auth::user()->username;
        $documentMgmtModl->document_tagwords    =   $selctdkeywrds;
        $documentMgmtModl->document_path        =   trim(preg_replace('/\s*\([^)]*\)/', '', $change_folder_path));
        $documentMgmtModl->parent_id            =   $change_folder_id;
        $documentMgmtModl->document_checkin_date=   date('Y-m-d h:i:s');
        $documentMgmtModl->document_expiry_date    =  @Input::get('document_expiry_date');
        $documentMgmtModl->document_assigned_to    =  @Input::get('assign_users');
        if ($documentMgmtModl->save()) {
            $lastId = $documentMgmtModl->document_id;
            //------Document id update in documents note table-----//
            DB::table('tbl_document_notes')
            ->where('document_id', 0)
            ->update(['document_id' => $lastId]);
            //add note by without click of addnote button
            if($note_add != "")
            {
                DB::table('tbl_document_notes')->insert(['document_id'=>$lastId,'document_note'=>$note_add,'document_note_created_by'=>Auth::user()->username]);
            }
            //-----------------------------------------------------//
            if($coltypcnt>0){
                for($i=1;$i<=$coltypcnt;$i++){
                    $documenttypecolModl   =   new DocumentsColumnModel;
                    $documenttypecolModl->document_id =   $lastId;
                    
                    $documenttypecolModl->document_type_column_id    =   Input::get('docid'.$i);
                    $documenttypecolModl->document_column_name   =   Input::get('doclabl'.$i);
                    $documenttypecolModl->document_column_type  =   Input::get('doctype'.$i);

                    /*if type = date then change document column value to date(y-m-d) format*/

                        if(Input::get('doctype'.$i) == 'Date')
                        {
                            $cal_date = Input::get('doccol'.$i);
                            $date = ($cal_date)?date('Y-m-d',strtotime($cal_date)):'';
                            $documenttypecolModl->document_column_value   =   $date;
                        }
                        else
                        {

                            $documenttypecolModl->document_column_value   =   Input::get('doccol'.$i);
                        }

                    $documenttypecolModl->document_column_mandatory =   Input::get('docmandatory'.$i);
                    $documenttypecolModl->save();
                }
            }
            //doc expire notification update
            if(Input::get('document_expiry_date')){
                //call common function for doc expiry notification
                
                $this->docObj->commom_expiry_documents_check(null);
            }
            // Save in audits
            $user = Auth::user()->username;
            // Assign details add to audits
            if(@Input::get('assign_users'))
            {
                $actionMsg = Lang::get('language.assign_action_msg');
                $actionDes = $this->docObj->stringReplace($DocName,@Input::get('assign_users'),Auth::user()->username,$actionMsg);

                $result = (new AuditsController)->dcmntslog(Auth::user()->username,$lastId,'Document', 'Assigned',$actionDes,@Input::get('docno'),@Input::get('docname'),trim(preg_replace('/\s*\([^)]*\)/', '', $change_folder_path)));
            }
            // Get save action message
            $actionMsg = Lang::get('language.save_action_msg');
            $actionDes = $this->docObj->stringReplace($this->actionName1,$DocName,$user,$actionMsg);

            $result = (new AuditsController)->dcmntslog(Auth::user()->username,$lastId,'Document', 'Add',$actionDes,Input::get('docno'),Input::get('docname'),trim(preg_replace('/\s*\([^)]*\)/', '', $change_folder_path)));
            $page = Input::get('page')?Input::get('page'):'';    
            if($page == 'viewappdata')
            {
                $app_doc_id = Input::get('app_doc_id')?Input::get('app_doc_id'):0; 
                $app_id = Input::get('app_id')?Input::get('app_id'):0; 
                $app_links= \App\AppLinksModel::where('fk_app_id','=',$app_id)->where('document_type_id','=',$documenttypeid)->first();
                if($app_links)
                {
                     $msg=$DocName. ' added successfully.';
                }
                else
                {
                    $msg = "You have added a document to a Document Type that is not linked to this App. Documents will be shown here only after this App is linked to the Document Type";
                }
                
               
                return redirect('viewappdata/'.$app_id.'/'.$app_doc_id)->with('data',$msg);
                exit();
            }
            if($result > 0) {
                return redirect()->back()->with('data', $DocName. ' added successfully.');
                exit();
            } else {
                return redirect()->back()->with('data', 'Some issues in log file,contact admin.');
                exit;
            }
            
        } else {
            return redirect()->back()->with('data', 'Sorry you cant add document.');
            exit();
        }
       } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
public function save(Request $request, $id)
{
    if (Auth::user()) 
    {
        if ($id) 
        {} 
        else 
        {
            // getting all of the post data
                $change_folder_id=Input::get('hidd_folder_id');
                $change_folder_path=Input::get('up_folder');
                $selctdkeywrds = "";
                $documenttypeid = "";
                $stack ="";
                $deparmentid="";
                $file = array('image' => Input::file('image'));
                if(is_array($file))
                { 
                    //array is not empty
                    //$coltypcnt  = Input::get('coltypecnt');
                    $mime = Input::file('image')->getMimeType();
                    if($mime=="application/pdf")
                    {
                        $destinationPath = config('app.base_path'); // upload path
                        $extension = Input::file('image')->getClientOriginalExtension();
                        $extension = strtolower($extension); // getting file extension
                        $fileName = $_FILES['file']['name'];
                        //$fileName = "dms_".uniqid();
                        //$time = microtime() ;
                        //$fileRandName = $fileName.'.' .$extension;
                        $fileRandName = $fileName;
                        $upload_success = Input::file('image')->move($destinationPath, $fileRandName); // uploading file to given path
                            // $keywrds = Input::get('keywords');
                            // $keywrdsCnt = count(Input::get('keywords'));
                            // for($i=0; $i < $keywrdsCnt; $i++){
                            //     if($i == $keywrdsCnt-1){
                            //         $selctdkeywrds = $selctdkeywrds.($keywrds[$i]);
                            //     }else{
                            //         $selctdkeywrds = $selctdkeywrds.($keywrds[$i] . ",");
                            //     }
                            // }

                            $keywrds = (Input::get('keywords'))?Input::get('keywords'):[''];
                            $coltypcnt  = Input::get('coltypecnt');
                            if($keywrds!="Please select tag category"){
                                $keywrdsCnt = count($keywrds);
                                for($i=0; $i < $keywrdsCnt; $i++){
                                    if($i == $keywrdsCnt-1){
                                        $selctdkeywrds = $selctdkeywrds.($keywrds[$i]);
                                    }else{
                                        $selctdkeywrds = $selctdkeywrds.($keywrds[$i] . ",");
                                    }
                                }
                            }else{
                                 $selctdkeywrds = [''];
                            }


                            $dctyp = Input::get('doctypeid');
                            $dctypCnt = count(Input::get('doctypeid'));
                            for($i=0; $i < $dctypCnt; $i++){
                                if($i == $dctypCnt-1){
                                    $documenttypeid = $documenttypeid.($dctyp[$i]);
                                }else{
                                    $documenttypeid = $documenttypeid.($dctyp[$i] . ",");
                                }
                            }
                            $stack = Input::get('stack');
                            $stackCnt = count(Input::get('stack'));
                            for($i=0; $i < $stackCnt; $i++){
                                if($i == $stackCnt-1){
                                    $stackid = $stackid.($stack[$i]);
                                }else{
                                    $stackid = $stackid.($stack[$i] . ",");
                                }
                            }
                            $departments = Input::get('departmentid');
                            $departmentsCnt = count(Input::get('departmentid'));
                            for($i=0; $i < $departmentsCnt; $i++){
                                if($i == $departmentsCnt-1){
                                    $deparmentid = $deparmentid.($departments[$i]);
                                }else{
                                    $deparmentid = $deparmentid.($departments[$i] . ",");
                                }
                            }

                            $documentMgmtModl                       =   new DocumentsModel;
                            if(Input::get('save')) {
                                $documentMgmtModl->document_status  =   "Published";
                            } elseif(Input::get('draft')) {
                                $documentMgmtModl->document_status  =   "Draft";
                            } elseif(Input::get('savepublish')) {
                                $documentMgmtModl->document_status  =   "Published";
                            }
                            $documentMgmtModl->department_id        =   $deparmentid;
                            $documentMgmtModl->document_type_id     =   $documenttypeid;
                            $documentMgmtModl->document_no          =   Input::get('docno');
                            $DocName=$documentMgmtModl->document_name        =   Input::get('docname');
                            $documentMgmtModl->stack_id             =   $stack;
                            $documentMgmtModl->document_version_no  =   "1.0";
                            $documentMgmtModl->document_file_name   =   $fileRandName;
                            $documentMgmtModl->document_ownership   =   Auth::user()->username;
                            $documentMgmtModl->document_created_by  =   Auth::user()->username;
                            $documentMgmtModl->document_tagwords    =   $selctdkeywrds;
                            $documentMgmtModl->document_path        =   $change_folder_path;
                            $documentMgmtModl->parent_id            =   $change_folder_id;
                            $documentMgmtModl->document_checkin_date=   date('Y-m-d h:i:s');
                            if ($documentMgmtModl->save()) 
                            {
                                $lastId = $documentMgmtModl->document_id;
                                if($coltypcnt>0){
                                    for($i=1;$i<=$coltypcnt;$i++){
                                        $documenttypecolModl   =   new DocumentsColumnModel;
                                        $documenttypecolModl->document_id =   $lastId;
                                        
                                        $documenttypecolModl->document_type_column_id    =   Input::get('docid'.$i);
                                        $documenttypecolModl->document_column_name   =   Input::get('doclabl'.$i);
                                        
                                        $documenttypecolModl->document_column_type  =   Input::get('doctype'.$i);
                                        /*if type = date then change document column value to date(y-m-d) format*/

                                        if(Input::get('doctype'.$i) == 'Date')
                                        {
                                            $cal_date = Input::get('doccol'.$i);
                                            $date = ($cal_date)?date('Y-m-d',strtotime($cal_date)):'';
                                            $documenttypecolModl->document_column_value   =   $date;
                                        }
                                        else
                                        {

                                            $documenttypecolModl->document_column_value   =   Input::get('doccol'.$i);
                                        }

                                        $documenttypecolModl->document_column_mandatory =   Input::get('docmandatory'.$i);
                                        $documenttypecolModl->save();
                                    }
                                }
                                // Save in audits
                                $name = Input::get('docname');
                                $user = Auth::user()->username;
                                
                                // Get save action message
                                $actionMsg = Lang::get('language.save_action_msg');
                                $actionDes = $this->docObj->stringReplace($this->actionName1,$name,$user,$actionMsg);
                                $result = (new AuditsController)->dcmntslog(Auth::user()->username,@$lastId ,'Document', 'Add',$actionDes,Input::get('docno'),Input::get('docname'),$change_folder_path);
                                if($result > 0) 
                                {
                                    return redirect()->back()->with('data', $DocName. ' added successfully.');
                                    exit();
                                } 
                                else 
                                {
                                    return redirect()->back()->with('data', 'Some issues in log file,contact admin.');
                                    exit;
                                }
                                
                            } 
                            else 
                            {
                                return redirect()->back()->with('data', 'Sorry you cant add document.');
                                exit();
                            }
                            
                        }
                        else
                        {
                            return redirect()->back()->with('data', 'You have tried to upload an invalid file extension.');
                            exit();
                        }
                    }
                    else
                    {
                        //array is empty
                        return redirect()->back()->with('data', 'Document file is missing, please upload the proper file.');
                        exit();
                    }                                       
                
            }//else close
            
        }//auth close
        else 
        {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function edit(Request $request, $id)
    {
        if (Auth::user()) {
        
                $data['stckApp'] = $this->docObj->common_stack();
                $data['deptApp'] = $this->docObj->common_dept();
                $data['doctypeApp'] = $this->docObj->common_type();
                $data['records'] = $this->docObj->common_records();
            $documentTypeData['datas']= DocumentsModel:: find($id);
            return View::make('pages/document_type/edit')->with($documentTypeData);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    //delete single document   
    public function deleteDocument()
    {
        if (Auth::user()) 
        {
            $id = Input::get('id');
            $name = Input::get('docname');
            $view = Input::get('view');
            switch($view)
            {
                case Lang::get('language.list_view')://list view
                case Lang::get('language.document_type_view'): //type
                case Lang::get('language.stack_view')://stack
                case Lang::get('language.department_view')://dept wise
                   
                   $table = 'tbl_documents';//tbl_documents
                   $column_table = 'tbl_documents_columns';//tbl_documents_columns
                   $note_table = 'tbl_document_notes';//tbl_temp_document_notes
                   $this->delete_document($id,$view,$name,$table,$column_table,$note_table);
                break; 
                case Lang::get('language.import_view'): //import view

                   $table = 'tbl_temp_documents';//tbl_temp_documents
                   $column_table = 'tbl_temp_documents_columns';//tbl_temp_documents_columns
                   $note_table = 'tbl_temp_document_notes';//tbl_temp_document_notes
                   $this->delete_document($id,$view,$name,$table,$column_table,$note_table);
                break;
            }
        }
        else
        {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

     public function delete_document($id,$view,$name,$table,$column_table,$note_table)
    {
        if($view==Lang::get('language.import_view')){
            $destinationPath = config('app.temp_document_path'); // upload path
        }else{
            $destinationPath = config('app.base_path'); // upload path
        }
        $record = DB::table($table)->where('document_id',$id)->first();           
        $usrname = Auth::user()->username;

        if($record){

       /* Prevent File deletion - Same file exists in more document result*/
        $document_file_name = $record->document_file_name;
        $multiple = DocumentsModel::check_file_owners($table,$id,$document_file_name); 
       

        $res = DB::table('tbl_documents_history')->insert(
                        array('document_id' => $record->document_id, 'document_no' => $record->document_no, 'document_name'=>$record->document_name, 'document_path'=>$record->document_path, 'document_version_no'=>$record->document_version_no, 'document_status'=>'Delete','document_history_created_by'=>$usrname));
        if($res){
                if($table == 'tbl_documents')
                {
                    $encrypt_details = DB::table($table)->select('document_encrypt_status')->where('document_id',$id)->first();
                    if(@$encrypt_details->document_encrypt_status == 1)
                    {
                        if(file_exists($destinationPath."/".$record->document_file_name.Lang::get('language.encrypt_extension')))
                        {
                            //delete encrypted file 
                            unlink($destinationPath."/".$record->document_file_name.Lang::get('language.encrypt_extension'));
                        }
                    }
                }
                DB::table($table)->where('document_id',$id)->delete();
                DB::table($note_table)->where('document_id',$id)->delete();//Delete notes of the document
                DB::table($column_table)->where('document_id',$id)->delete();
                /*echo "multiple".$multiple."<br>";*/
                if(file_exists($destinationPath."/".$document_file_name) && ($document_file_name) && ($multiple == 0))
                {
                    //delete original
                    unlink($destinationPath."/".$document_file_name);
                   /* echo "file deleted".$document_file_name."<br>";*/
                }
                else
                {
                   /* echo ",document_file_name=".$document_file_name;
                    echo ",multiple=".$multiple;*/
                   /* echo "file not deleted".$document_file_name."<br>";*/
                }

                // Save in audits
                $name = $record->document_file_name;
                $user = Auth::user()->username;

                // Get delete action message
                $actionMsg = Lang::get('language.delete_action_msg');
                $actionDes = $this->docObj->stringReplace($this->actionName1,$name,$user,$actionMsg);

                $result = (new AuditsController)->dcmntslog(Auth::user()->username,$id,'Documents', 'Delete',$actionDes,$record->document_no,$record->document_name,$record->document_path);
                if($result) {
                    echo "1";
                    //echo json_encode("Document '". $record->document_name ."' deleted successfully.");
                    exit();
                } else {
                    echo "0";
                    //echo json_encode("Some issues in log file,contact admin");
                    exit;
                }
            }
        }
    }

    public function deleteDocumentHistory()
    {
        if (Auth::user()) {
            $id = Input::get('id');
            $name = Input::get('docname');
            DocumentHistoryModel::where('document_history_id',$id)->delete();
            DocumentHistoryColumnModel::where('document_history_id',$id)->delete(); 
            echo json_encode("Document '". $name ."' deleted successfully.");
            exit();          
            
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function duplication()
    {
        if (Auth::user()) {
            $name= Input::get('name');
            $duplicateEntry= DocumentsModel::where('department_name', '=', $name )->get();

            if(count($duplicateEntry) > 0 )
            {                
                echo json_encode('<div class="parsley-errors-list filled" id="dp-inner">'. $name.' is already in our database. </div>');
                exit();
            }            

        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    public function uploadFiles(Request $request) {
    if (Auth::user()) {  
        $module = Input::get('module');
        $retrnstatus = 0;
        $settings = DB::table('tbl_settings')->select('settings_ftp')->get();
        $settings_ftp   = $settings[0]->settings_ftp;
        if($settings_ftp==1){ // checking the ftp value is 1
            // get the ftp credentials
            $ftpsettings = DB::table('tbl_ftp_details')->select('ftp_details_host','ftp_details_port','ftp_details_username','ftp_details_password')->get();
            $domainhost   = $ftpsettings[0]->ftp_details_host;
            $port         = $ftpsettings[0]->ftp_details_port;
            $ftp_username   = $ftpsettings[0]->ftp_details_username;
            $ftp_userpass   = $ftpsettings[0]->ftp_details_password;

            $domainpass=($ftp_userpass)?decrypt($ftp_userpass):'';  
            //$basedirectory="/storage/";

            if($domainhost || $port || $ftp_username || $domainpass)
            { //checkig the ftp is connect or not
                $ftp_conn = @ftp_connect($domainhost,$port);
                if (false === $ftp_conn) {
                    $retrnstatus = 0;
                }
                if($ftp_conn)
                {
                   // login
                    if (@ftp_login($ftp_conn, $ftp_username, $domainpass))
                      {
                      $retrnstatus = 1;
                      }
                    else
                      {
                      $retrnstatus = 0;
                      }
                }
                // close connection
                ftp_close($ftp_conn); 
            }
        }else if($settings_ftp==0){ // if the ftp is not checked the default mode http is active
            $retrnstatus = 1;
        }

        if($retrnstatus == 1){ // checking the status is 1 either ftp or http connections are ok
            $file_name = Input::file('file')->getClientOriginalName();
            $check = check_valid_file_name($file_name);
            if(!$check)
            {
                return 'invalidname';
                exit();
            }    
            $input = Input::all();
            $mime  = Input::file('file')->getMimeType();
            // echo $mime;
            // exit();
            $size  = filesize(Input::file('file'));
            if($mime=="application/vnd.ms-office" || $mime== "application/octet-stream" || $mime=="application/pdf" || $mime=="image/jpeg" || $mime=="image/png" || $mime=="application/msword" || $mime=="application/vnd.openxmlformats-officedocument.wordprocessingml.document" || $mime=="application/vnd.ms-excel" || $mime=="application/zip" || $mime=="application/vnd.ms-office" || $mime=="text/plain" || $mime=="image/tiff" || $mime=="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" || $mime=="audio/mp3" || $mime=="audio/ogg" || $mime=="audio/wav" || $mime=="audio/x-wav" || $mime=="video/mp4" || $mime=="application/ogg" || $mime=="video/ogg" || $mime=="video/webm" || $mime=="video/flv" || $mime=="video/x-flv" || $mime=="application/x-rar" || $mime=="video/flv" || $mime=="audio/mpeg" || $mime=="image/vnd.dwg" || $mime=="text/html" || $mime=="image/gif"){
                if($module == 'documentEdit')
                {
                    $destinationPath  = config('app.temp_document_path'); // upload file to temp folder path
                }
                else
                {
                    $destinationPath  = config('app.temp_document_path'); // upload file to temp folder path
                }
                
                if(!file_exists($destinationPath))
                {
                    //create directory import
                    File::makeDirectory($destinationPath, $mode = 0777, true, true);
                }
                $extension        = Input::file('file')->getClientOriginalExtension();
                $extension = strtolower($extension); // getting file extension
                $originalfileName = $_FILES['file']['name'];
                $upload_filename  = pathinfo($originalfileName, PATHINFO_FILENAME);
                // $duplicateEntry= DocumentsModel::where('document_name', '=', $upload_filename)->get();
                // $duplicateEntryTemp= TempDocumentsModel::where('document_name', '=', $upload_filename)->get();
                //     if(count($duplicateEntry) > 0 || count($duplicateEntryTemp) > 0)
                //     { 
                //         return 'exists';
                //         exit();
                //     }  
                //$fileName     = "dms_".uniqid();
                //$fileRandName = $originalfileName.'.' .$extension;
                $fileRandName = $originalfileName;

                // If the file is tif then convert in to pdf
                if($mime == 'image/tiff'){
                    // By calling Imagic functuion 
                    $fileRandName = $this->docObj->tiffToPdf(Input::file('file'),@$fileName,$destinationPath);// app/Mylibs/Common.php 
                    if(Input::get('module') == 'documentEdit'):
                        $extension = 'pdf';
                        endif;

                }else{
                    // Normal upload
                    /*$upload_success = Input::file('file')->move($destinationPath, $fileRandName);*/
                    $ftp=0;
                    if(Session::get("settings_ftp_upload") == 1)
                    {
                        $upload = array();
                        //$upload['destinationPath'] = 'website/public/test';
                        $upload['destinationPath'] = '/storage/app/'.config('app.user_folder_name').'/documents/temp/';
                        //$upload['destinationPath'] =  $destinationPath;
                        $upload['sourceFile'] = $_FILES["file"]["tmp_name"];
                        $upload['destinationFile'] = $fileRandName;
                        $return = $this->docObj->ftpUpload($upload); 
                        $ftp=(isset($upload['status']))?$upload['status']:0;
                    }

                    if(!$ftp)
                    {
                        $upload_success = Input::file('file')->move($destinationPath, $fileRandName);
                    }
                    
                }
                
                if(Input::get('module') == 'documentAdd' || Input::get('module') == 'documentEdit'):
                    // Document Edit module
                    if(Input::get('module') == 'documentEdit'):
                        //Set image name in session
                        Session::put('new_file_name',$originalfileName);
                        Session::put('extension',$extension);
                    endif;

                    $data['fileRandName']=$fileRandName;
                    $data['size']=$size;
                    return $data;

                elseif(Input::get('module') == 'bulkImport'):
                   // Document Bulk Import module
                    $documenttypeid = (Input::get('doctypeid'))?Input::get('doctypeid'):0;
                    Session::put('sess_lastInsertedFiles',$upload_filename);
                    $par_id= Session::get('SESS_parentIdd');
                    $where = array('document_type_id'=>$documenttypeid,'document_file_name'=>$fileRandName);
                    $duplicate = TempDocumentsModel::where($where)->get();
                    foreach ($duplicate as $key => $value) {
                        DB::table('tbl_temp_documents_columns')->where('document_id',$value->document_id)->delete();
                        DB::table('tbl_temp_documents')->where('document_id',$value->document_id)->delete();
                    }
                    $where = array('document_type_id'=>0);
                    
                    $duplicate = TempDocumentsModel::where($where)->get();
                    foreach ($duplicate as $key => $value) {
                        DB::table('tbl_temp_documents_columns')->where('document_id',$value->document_id)->delete();
                        DB::table('tbl_temp_documents')->where('document_id',$value->document_id)->delete();
                    }
                    
                    $documentMgmtModlTemp = new TempDocumentsModel;

                        $documentMgmtModlTemp->document_file_name   =   $fileRandName;
                        $documentMgmtModlTemp->document_name        =   $upload_filename;
                        $documentMgmtModlTemp->department_id        =   Auth::user()->department_id;
                        $documentMgmtModlTemp->document_type_id     =   $documenttypeid;
                        $documentMgmtModlTemp->document_version_no  =   "1.0";
                        $documentMgmtModlTemp->document_path        =   Session::get('SESS_path');
                        $documentMgmtModlTemp->document_ownership   =   Auth::user()->username;
                        $documentMgmtModlTemp->document_created_by  =   Auth::user()->username;
                        $documentMgmtModlTemp->parent_id            =   $par_id;
                        $documentMgmtModlTemp->document_status      =   "Unpublished";
                        $documentMgmtModlTemp->document_size        =   $size;

                    $documentMgmtModlTemp->save();
                    if($documentMgmtModlTemp->save())
                        {
                            $lastInsertedID = $documentMgmtModlTemp->document_id;
                            Session::put('sess_lastInsertedIDD',$lastInsertedID);
                            $sql_upload=DB::table('tbl_temp_documents')
                            ->where('document_id',$lastInsertedID)
                            ->update(['parent_id' => $par_id]);
                        }
                    $data['fileRandName']=$fileRandName;
                    $data['size']=$size;
                    $data['last_inserted'] = $lastInsertedID;
                    return $data;
                endif;
            }else{
                return 'invalid';
                exit();
            }
        }else{ //if its failed to connect return error
            return 'ftpinvalid';
            exit();
        }
    } else {
        return redirect('')->withErrors("Please login")->withInput();
    }

    }

    public function testUpload() 
    { echo 'hi'; 

        if(isset($_FILES["firstname"]["name"]) && $_FILES["firstname"]["name"])
        {
            $upload = array();
            $upload['destinationPath'] = 'website/public/test';
            $upload['sourceFile'] = $_FILES["firstname"]["tmp_name"];
            $upload['destinationFile'] = $_FILES["firstname"]["name"];
            $return = $this->docObj->ftpUpload($upload);
            print_r($return);  
        }
        else
        {
            return View::make('upload');
        }
    }

    public function wrkspacesrchdoc(Request $request) 
    {
        Session::put('open_doc_no',1);
        $page = Input::get('page');
        Session::put('SESS_page_grid',@$page);
        //$page_per_length = Session::get('settings_rows_per_page');
        $page_per_length = 5;
        $name=Input::get('searhtext');
        Session::put('SESS_searhtext',@$name);
        $data['searchres'] = $name;
        Session::put('SESS_parentIdd',@$_GET['par_id']);
        $path=Input::get('path'); 
        Session::put('SESS_path',$path);
        $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
        $data['settings_document_no']   = $settings[0]->settings_document_no;
        $data['settings_document_name'] = $settings[0]->settings_document_name;
        
        $query = DB::table('tbl_documents')->leftjoin('tbl_document_types','tbl_documents.document_type_id','=','tbl_document_types.document_type_id')->where('tbl_document_types.is_app',0);

        // Get data by department wise
        $loggedUsersdepIds = explode(',',Auth::user()->department_id);
        if(Auth::user()->user_role == Session::get("user_role_group_admin") || Auth::user()->user_role == Session::get("user_role_regular_user") || Auth::user()->user_role == Session::get("user_role_private_user")){
            $count = count($loggedUsersdepIds);
            if($count == 1):
                $x=0;
            else:
                $x=1;
            endif;
            
            foreach($loggedUsersdepIds as $depid):

                if($x == 1):
                    $query->orWhereRaw('('.'FIND_IN_SET('.$depid.',department_id)');
                elseif($x == $count):
                    $query->orWhereRaw('FIND_IN_SET('.$depid.',department_id)'.')');
                else:
                    $query->orWhereRaw('FIND_IN_SET('.$depid.',department_id)');
                endif;
                $x++;
                endforeach;
        }
        if(isset($name))
        {
        $query->where('tbl_documents.document_name','LIKE','%'.$name.'%')->orWhere('tbl_documents.document_no','LIKE','%'.$name.'%');
        $countdocs = $query->count();
        $dglist = $query->paginate($page_per_length);
        }
        else
        {
        $query->select('*')->where('tbl_documents.parent_id',Session::get('SESS_parentIdd'))->where('tbl_documents.document_type_id','!=',0)->orderBy('tbl_documents.document_id', 'desc');
        //check user = private user, fetch only the docs of that user
        if(Auth::user()->user_role == Session::get("user_role_private_user"))
        {
            $query->where('tbl_documents.document_ownership',Auth::user()->username);
        }
        $countdocs = $query->count();
        $dglist = $query->paginate($page_per_length);
        }
        // Expanding dglits with required datas
            foreach($dglist as $val):
                $val->document_type_columns = DB::table('tbl_documents_columns')->select('document_column_name','document_column_value')->where('document_id',$val->document_id)->get();
                // Get documentTypes
                //$val->documentTypes = DB::table('tbl_document_types')->select('document_type_name as document_type_names','document_type_column_no','document_type_column_name')->whereIn('document_type_id',explode(',',$val->document_type_id))->get();

                $val->documentTypes = DocumentTypesModel::select('document_type_column_no','document_type_column_name')->where('document_type_id', $val->document_type_id)->get();

                // Get stack
                $val->stacks = DB::table('tbl_stacks')->select(DB::raw('GROUP_CONCAT(stack_name) AS stack_name'))->whereIn('stack_id',explode(',',$val->stack_id))->get();
                // Get Tag words
                $val->tagwords = DB::table('tbl_tagwords')->select(DB::raw('GROUP_CONCAT(tagwords_title) AS tagwords_title'))->whereIn('tagwords_id',explode(',',$val->document_tagwords))->get();
                // Get department
                $val->department = DB::table('tbl_departments')->select(DB::raw('GROUP_CONCAT(department_name) AS department_name'))->whereIn('department_id',explode(',',$val->department_id))->get();
                //To check document has workfowhistory
                $val->hasWorkfowHistory = DB::table('tbl_workflow_histories')->where('document_workflow_object_id',$val->document_id)->exists();
                //file existance checking
                if(file_exists(config('app.base_path').$val->document_file_name))
                {
                    $val->isfileexist = 'exist';
                }
                else
                {
                    $val->isfileexist = 'notexist';
                }
            endforeach;

            if ($request->ajax()) 
            {
                return view('pages/documents/datas', ['dglist' => $dglist,'countdocs' => $countdocs])->render();  
            }
                return view('pages/documents/datas',compact('dglist','countdocs'))->with($data);
        
    }
    public function uploadEdit()//View of recently uploaded files
    {   
        $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
        $data['settings_document_no']   = $settings[0]->settings_document_no;
        $data['settings_document_name'] = $settings[0]->settings_document_name;

        if (Auth::user()) {
            if(Session::has('sess_lastInsertedIDD')){
            $output = array();
            foreach(Session::get('sess_lastInsertedIDD') as $term){
            $output[] = $term;
            }
            
                $data['stckApp'] = $this->docObj->common_stack();
                $data['deptApp'] = $this->docObj->common_dept();
                $data['doctypeApp'] = $this->docObj->common_type();
                $data['records'] = $this->docObj->common_records();
            $data['stacks'] = StacksModel::all();
            $data['tags'] = TagWordsModel::all();
            $data['depts'] = DepartmentsModel::all();
            $data['doctypes'] = DocumentTypesModel::where('is_app',0)->get();
            $data['type_id'] = '0';//default select 'All document type'
            $data['dglist'] =DB::table('tbl_temp_documents')->whereIn('tbl_temp_documents.document_id',$output)->get();
            return View::make('pages/documents/listview_united')->with($data);
        }
        else
        {
            /*$data['stckApp'] = StacksModel::select('stack_id','stack_name')->orderBy('created_at', 'DESC')->get();
            if(Auth::user()->user_role == Session::get("user_role_group_admin") || Auth::user()->user_role == Session::get("user_role_regular_user") || Auth::user()->user_role == Session::get("user_role_private_user")){
                    $loggedUsersdepIds = explode(',',Auth::user()->department_id);
                    $data['deptApp'] = DepartmentsModel::select('department_id','department_name')->whereIn('department_id',$loggedUsersdepIds)->orderBy('created_at', 'DESC')->get();
                }else{
                    $data['deptApp'] = DepartmentsModel::select('department_id','department_name')->orderBy('created_at', 'DESC')->get();
                }

            $data['doctypeApp'] = DocumentTypesModel::select('document_type_id','document_type_name')->orderBy('created_at', 'DESC')->get();
            $data['stacks'] = StacksModel::all();
            $data['tags'] = TagWordsModel::all();
            $data['depts'] = DepartmentsModel::all();
            $data['doctypes'] = DocumentTypesModel::all();
            $data['dglist'] = TempDocumentsModel::all();
            return View::make('pages/documents/uploadedit')->with($data);*/
            return redirect()->back()->with('error', 'Upload file error, no documents for edit.');
            exit(); 
        }
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    //List view united
    public function uploadAllView()
    {  
        if (Auth::user()) {
            $this->docObj->document_assign_notification();
            $this->docObj->document_reject_notification();
            $this->docObj->document_accept_notification();
            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            $data['stacks'] = StacksModel::all();
            $data['tags'] = TagWordsModel::all();
            $data['depts'] = DepartmentsModel::all();
            $data['doctypes'] = DocumentTypesModel::where('is_app',0)->get();
            if(Input::get('id'))
            {
                $id=Input::get('id');//from url 'listview'
            }
            $saved_search = (Input::get('saved_search'))?1:0;    
            Session::put('menuid', '1');
        switch(Input::get('view') )
        {
            //import    listview?view=import
            case Lang::get('language.import_view'):
                Session::put('menuid', '16');
                $data['type_id'] = '0';//default select 'All document type'
            break;

            //import    listview?view=import
            case Lang::get('language.stack_view'):
                Session::put('menuid', '7');
                $data['type_id'] = '0';//default select 'All document type'
            break;
            //documentsCheckoutListview 
            case Lang::get('language.checkout_view'):
                $data['type_id'] = '0';//default select 'All document type'
                Session::put('menuid', '18');
                // Unlink file
                if(Input::get('val') == 'cancel'):
                    if(Session::get('new_file_name')){
                        $source = config('app.base_path').Session::get('new_file_name').'.'.Session::get('extension');
                        if(file_exists($source)){
                            unlink($source);
                        }
                        Session::forget('new_file_name');
                        Session::forget('extension');
                    }
                endif;
            break;
            //documentList
            case Lang::get('language.list_view'):
            //particular document in list view
                if(Input::get('docid'))
                {
                    $data['doc_id'] = Input::get('docid');
                    $data['type_id'] = '0';//all type show
                }   
            //if notification occures
                if(Input::get('notification') == 1)
                {
                    $data['type_id'] = '0';//all type show
                    $data['notification_expiry'] = 1;//expired docs soon only show
                }
                elseif(Input::get('notification') == 2)
                {
                    $data['type_id'] = '0';//all type show
                    $data['notification_expiry'] = 2;//assigned docs only show
                }
                elseif(Input::get('notification') == 3)
                {
                    $data['type_id'] = '0';//all type show
                    $data['notification_expiry'] = 3;//rejected docs only show
                }
                elseif(Input::get('notification') == 4)
                {
                    $data['type_id'] = '0';//all type show
                    $data['notification_expiry'] = 4;//accepted docs only show
                }
                else
                {
                $data['type_id'] = '0';//default select 'All document type'
                // Distroy session in search list
                $this->session_destroy_all();
                Session::forget('document_column_name');
                Session::forget('document_column_value');
                Session::forget('document_type_column_id'); 
                Session::forget('dglist');
                Session::forget('documentColNam');
                }
            break;
            //sidebar
            case Lang::get('language.document_type_view'):
                // For Document type
                $data['sel_item_id'] = $id;
                $data['type_id'] = $id;
                Session::put('menuid', '2');//sidebar highlight
                if(!$saved_search)
                {
                   Session::put('serach_doc_type',$id);     
                }
            break;
            case Lang::get('language.stack_view'):
                // For stack
                $data['sel_item_id'] = $id;
                $data['type_id'] = '0';
                Session::put('menuid', '7');//sidebar highlight
            break;
            case Lang::get('language.department_view'):
                // For department
                $data['sel_item_id'] = $id;
                $data['type_id'] = '0';
                Session::put('menuid', '3');//sidebar highlight
            break;

        }
            $heads = array();  
            $type_id = (isset($data['type_id']) && $data['type_id'])?$data['type_id']:0;
            
            $data['type_id'] = (Session::get('serach_doc_type'))?Session::get('serach_doc_type'):$type_id;
            
            if(!$data['type_id']) //Default doc type
            {
                $data['type_id'] = (isset($data['doctypeApp'][0]))?$data['doctypeApp'][0]->document_type_id:0;
            }
            
            foreach($data['doctypeApp'] as $val)
            {
                $res = DB::table('tbl_document_types_columns as tc')->select('tc.document_type_column_name','tc.document_type_column_id','tc.document_type_column_type','tc.document_type_options')->where('tc.document_type_id',$val->document_type_id)->orderBy('tc.document_type_column_order','ASC')->get();
                $heads[$val->document_type_id] = (count($res))?$res:array();
            }
            $data['heads'] = $heads;
            $data['displayStart'] = (Session::get('serach_start') && $saved_search)?Session::get('serach_start'):0;
            $data['rows_per_page'] = (Session::get('serach_length'))?Session::get('serach_length'):Session::get('settings_rows_per_page');
            $data['serach_filter'] = (Session::get('serach_filter') && $saved_search)?Session::get('serach_filter'):trans('documents.radio_all');
            $data['search_text'] = (Session::get('search_text') && $saved_search)?Session::get('search_text'):'';
            $data['search_column'] = (Session::get('search_column') && $saved_search)?Session::get('search_column'):'';
            
            return View::make('pages/documents/listview_united')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function session_set()
    {
        $path=Input::get('lang'); 
        Session::put('SESS_path',$path);
    }
    public function docnoDuplication()
    {   
        if (Auth::user()) {
            $name= Input::get('name');
            $id  = Input::get('doc_id');
            
            $duplicateEntry= DB::table('tbl_documents')->where('document_no', '=', $name )->get();
            $duplicateEntryTemp= DB::table('tbl_temp_documents')->where('document_no', '=', $name )->get();

            if($duplicateEntry || $duplicateEntryTemp){

                //Edit doc
                if($id){
    
                    if(@$duplicateEntry[0]->document_id == $id){
                        $duplicateEntry = '';
                    }else{
                        $duplicateEntry = $duplicateEntry;
                    }
                    // temp
                    //temp doc
                    if(@$duplicateEntryTemp[0]->document_id == $id){
                        $duplicateEntryTemp = '';
                    }else{
                        $duplicateEntryTemp = $duplicateEntryTemp;
                    }
                }else{
                
                    // Add
                    $duplicateEntry = $duplicateEntry;
                    $duplicateEntryTemp = $duplicateEntryTemp;
                }

            }

            if( $duplicateEntry  || $duplicateEntryTemp){ 
                
                echo json_encode('<div class="parsley-errors-list filled" id="dp-inner">'. $name.' is already in our database. </div>');
                exit();
            }        

        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function docnameDuplication()
    {
        if (Auth::user()) {
            $name= Input::get('name');
            $id  = Input::get('doc_id');
            $duplicateEntry= DB::table('tbl_documents')->where('document_name', '=', $name )->get();
            $duplicateEntryTemp= DB::table('tbl_temp_documents')->where('document_name', '=', $name )->get();

            if($duplicateEntry || $duplicateEntryTemp){
                //Edit doc
                if($id){
    
                    if(@$duplicateEntry[0]->document_id == $id){
                        $duplicateEntry = '';
                    }else{
                        $duplicateEntry = $duplicateEntry;
                    }
                    // temp
                    //temp doc
                    if(@$duplicateEntryTemp[0]->document_id == $id){
                        $duplicateEntryTemp = '';
                    }else{
                        $duplicateEntryTemp = $duplicateEntryTemp;
                    }
                }else{
                    // Add
                    $duplicateEntry = $duplicateEntry;
                    $duplicateEntryTemp = $duplicateEntryTemp;
                }

            }

            if( $duplicateEntry  || $duplicateEntryTemp )
            { 
                echo json_encode('<div class="parsley-errors-list filled" id="dp-inner">'. $name.' is already in our database. </div>');
                exit();
            }            

        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function removeDocument()
    {   
        if (Auth::user()) { 

            if(@Input::get('file_name')){
                // Bulk import
                $file = Input::get('file_name');
                // Delete row
                $query = DB::table('tbl_temp_documents');
                $data = $query->select('document_id','document_file_name','document_size')->where('document_file_name',$file)->first();
                if($data)
                {
                    DB::table('tbl_temp_documents')->where('document_id',$data->document_id)->delete();
                    $dat['fileRandName'] = $data->document_file_name;
                    $dat['last_inserted'] = $data->document_id;
                    $dat['size'] = $data->document_size;
                    $dat['success'] = 'yes';
                    
                }
                else
                {
                    $dat['success'] = 'no';
                }
                $multiple = DB::table('tbl_temp_documents')->where('document_file_name',$file)->first();
                if($multiple == 0)
                {
                    $destinationPath = config('app.temp_document_path');
                    $source         =   $destinationPath.$file;
                    if(file_exists($source) && $file)
                    { 
                        unlink($source);//reomve original file
                    }   
                }
                return $dat;
            }else{
                // Document add/update
                $file = Input::get('file');
                $file = json_decode($file);
                $file = $file->fileRandName;
            }
            // Delete
            $destinationPath = config('app.temp_document_path');
            $source         =   $destinationPath.$file;
            if(file_exists($source) && $file)
            { 
                        unlink($source);//reomve original file
            }
            /*$destinationPath = config('app.base_path'); // upload path
            unlink($destinationPath."/".$file);*/
            echo "removed"; 
            
        }
        else{
            echo json_encode("Some issues in log file,contact admin");
            exit;
        }   
    }
    public function uploadFiles2(Request $request) {
        if (Auth::user()) {

        $input = Input::all();
        $mime = Input::file('file')->getMimeType();
        if($mime== "text/plain" || $mime== "text/csv" || $mime== "text/x-Algol68" || $mime== "text/x-algol68"){
        $datetime = date("Y-m-d_H-i-s");
        $destinationPath = config('app.import_path'); // upload path
        $filename = Config::get('constants.import_file').$datetime.'.csv';
        $filename = str_replace("username",Auth::user()->username,$filename);
        $path_upload_csv = config('app.import_path').$filename;
        if(!file_exists($destinationPath))
        {
            //create directory import
            File::makeDirectory(config('app.import_path'), $mode = 0777, true, true);
        }
        move_uploaded_file( $_FILES["file"]["tmp_name"], $path_upload_csv);
        Session::put('csv_import_filename',$filename);
        echo Session::get('csv_import_filename');
        
        }
        else{
                $a=1;
                return $a;
                exit();
            }    
        } 
        else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    //for upload the back/restore files. 
    public function bckpUploadFiles(Request $request) {
        if (Auth::user()) {   
            $retrnstatus = 0;
            $settings = DB::table('tbl_settings')->select('settings_ftp')->get();
            $settings_ftp   = $settings[0]->settings_ftp;
            if($settings_ftp==1){ // checking the ftp value is 1
                // get the ftp credentials
                $ftpsettings = DB::table('tbl_ftp_details')->select('ftp_details_host','ftp_details_port','ftp_details_username','ftp_details_password')->get();
                $domainhost   = $ftpsettings[0]->ftp_details_host;
                $port         = $ftpsettings[0]->ftp_details_port;
                $ftp_username   = $ftpsettings[0]->ftp_details_username;
                $ftp_userpass   = $ftpsettings[0]->ftp_details_password;

                $domainpass=($ftp_userpass)?decrypt($ftp_userpass):'';  
                //$basedirectory="/storage/";

                if($domainhost || $port || $ftp_username || $domainpass)
                { //checkig the ftp is connect or not
                    $ftp_conn = @ftp_connect($domainhost,$port);
                    if (false === $ftp_conn) {
                        $retrnstatus = 0;
                    }
                    if($ftp_conn)
                    {
                       // login
                        if (@ftp_login($ftp_conn, $ftp_username, $domainpass))
                          {
                          $retrnstatus = 1;
                          }
                        else
                          {
                          $retrnstatus = 0;
                          }
                    }
                    // close connection
                    ftp_close($ftp_conn); 
                }
            }else if($settings_ftp==0){ // if the ftp is not checked the default mode http is active
                $retrnstatus = 1;
            }

            if($retrnstatus == 1){ // checking the status is 1 either ftp or http connections are ok
                $input = Input::all();
                $mime  = Input::file('file')->getMimeType();
                $size  = filesize(Input::file('file'));
                $check_specific = array("application/msword",
                    "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
                    "application/vnd.ms-office",
                    "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                    "application/octet-stream");
                $allow_files = array("text/plain",                    
                    "application/sql",
                    "application/zip",
                    "text/x-Algol68",
                    "application/octet-stream");
                //check internet conn
                $premisevalue = (env('ON_PREMISE'))?env('ON_PREMISE'):0;
                if(($premisevalue==0)&&(in_array($mime, $check_specific)))
                {
                    return 'invalid';
                }
                else
                {
                    if(in_array($mime, $allow_files))
                    {
                        $destinationPath  = config('app.base_path'); // upload path
                        $restredestinationPath  = config('app.zip_backup_path'); // upload path
                        if(!file_exists($destinationPath))
                        {
                            //create directory import
                            File::makeDirectory(config('app.base_path'), $mode = 0777, true, true);
                        }
                        if(!file_exists($restredestinationPath))
                        {
                            //create directory backup
                            File::makeDirectory(config('app.zip_backup_path'), $mode = 0777, true, true);
                        }
                        $extension        = Input::file('file')->getClientOriginalExtension();
                        $extension = strtolower($extension); // getting file extension
                        $originalfileName = $_FILES['file']['name'];
                        $upload_filename  = pathinfo($originalfileName, PATHINFO_FILENAME);
                        $fileRandName = $originalfileName;

                        // If the file is tif then convert in to pdf
                        if(($mime == 'text/x-Algol68') || ($mime == 'application/zip') || ($mime == 'application/sql') || ($mime == 'text/plain')){
                            $ftp=0;
                            if(Session::get("settings_ftp_upload") == 1)
                            {
                                $upload = array();
                                $upload['destinationPath'] = '/storage/app/'.config('app.zip_backup_path');
                                $upload['sourceFile'] = $_FILES["file"]["tmp_name"];
                                $upload['destinationFile'] = $fileRandName;
                                $return = $this->docObj->ftpUpload($upload); 
                                $ftp=(isset($upload['status']))?$upload['status']:0;
                            }
                            if(!$ftp)
                            {
                                $upload_success = Input::file('file')->move($restredestinationPath, $fileRandName);
                            }
                        }
                        if(Input::get('module') == 'restoredb'):
                            Session::put('restoreFileName',$fileRandName);                       
                            $data['fileRandName']=$fileRandName;
                            $data['size']=$size;
                            return $data;
                        endif;
                    }
                    else
                    {
                        return 'invalid';
                        exit();
                    }
                }

            }else{ //if its failed to connect return error
                return 'ftpinvalid';
                exit();
            }
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }

    }

    public function removeDocumentTemp(Request $request)
    {
        if (Auth::user()) {
            $uploadfile = Input::get('upload_file');
            if(file_exists(config('app.import_path').$uploadfile)){
                unlink(config('app.import_path').$uploadfile);
                echo "removed";
            }
            else
            {
                echo "file not exist";
            }
        }
        else{
            echo json_encode("Some issues in log file,contact admin");
            exit;
        }   
    }
    public function removeDocumentOnNavigation(Request $request)
    {
        if (Auth::user()) {
            $uploadfile = Input::get('upload_file');
            if(file_exists(config('app.import_path').$uploadfile)){
                unlink(config('app.import_path').$uploadfile);
                echo "removed";
            }
            else
            {
                echo "file not exist";
            }
        }
        else{
            echo json_encode("Some issues in log file,contact admin");
            exit;
        }   
    }
    // Get Decument More Details 
    public function docMoreDetails($id){

        if(@$_GET['dtl']):
            Session::put('menuid','2');
        elseif(@$_GET['sl']):
            Session::put('menuid','7');
        elseif(@$_GET['dl']):
            Session::put('menuid','3');
        endif;

        $docDetails = DB::table('tbl_documents')->where('document_id',$id)->get();
        $data['xtraDetails'] = DB::table('tbl_documents_columns')->select('tbl_documents_columns.document_column_name','tbl_documents_columns.document_column_value','tbl_documents_columns.document_column_id','tbl_documents_columns.document_type_column_id','tbl_document_types_columns.document_type_column_id as 2nd_document_type_column_id')->leftJoin('tbl_document_types_columns','tbl_document_types_columns.document_type_column_id','=','tbl_documents_columns.document_type_column_id')->where('tbl_documents_columns.document_id',$id)->orderby('tbl_document_types_columns.document_type_column_order','ASC')->get();

        foreach($docDetails as $val):           

            // tbl_document_types
            //$val->document_type_names = DB::table('tbl_document_types')->select(DB::raw('GROUP_CONCAT(document_type_name) AS document_type_names','document_type_column_no','document_type_column_name'))->whereIn('document_type_id',explode(',',$val->document_type_id))->get(); 

            $val->documentTypes = DocumentTypesModel::select('document_type_column_no','document_type_column_name')->where('document_type_id', $val->document_type_id)->get();

            // tbl_departments
            $val->departments = DB::table('tbl_departments')->select(DB::raw('GROUP_CONCAT(department_name) AS department_names'))->whereIn('department_id',explode(',',$val->department_id))->get();
            // tbl_stacks
            $val->stacks = DB::table('tbl_stacks')->select(DB::raw('GROUP_CONCAT(stack_name) AS stack_names'))->whereIn('stack_id',explode(',',$val->stack_id))->get();
            // tbl_tagwords
            $val->tagwords = DB::table('tbl_tagwords')->select(DB::raw('GROUP_CONCAT(tagwords_title) AS tagwords_titles'))->whereIn('tagwords_id',explode(',',$val->document_tagwords))->get();
            endforeach;
        $data['moreDetails'] = $docDetails;
        // Datas for app.blade.php
        
                $data['stckApp'] = $this->docObj->common_stack();
                $data['deptApp'] = $this->docObj->common_dept();
                $data['doctypeApp'] = $this->docObj->common_type();
                $data['records'] = $this->docObj->common_records();
        
        return View::make('pages/documents/documentmoredetails')->with($data);
    }
    
    public function saveComment()
    {  
        $response = array('status' => 0);
        if (Auth::user()) 
        {
            $id=Input::get('hidd_doc_id');
            $comments= Input::get('comments');
            $username = Auth::user()->username;

            $document = DB::table('tbl_documents')->select('document_file_name','document_version_no','document_no','document_name','document_path','document_status')->where('document_id',$id)->first();
            if($document)
            {
              $document_name = $document->document_name;
              $document_file_name = $document->document_file_name;
              $document_pre_status = $document->document_status;
              Session::put('sess_download_file',$document_file_name);
                // Update check out model
              DocumentsModel::where('document_id',$id)->update(['document_status' => "Checkout",'document_pre_status' => $document_pre_status,'documents_checkout_by'=>Auth::user()->id,'document_checkout_date'=>date('Y-m-d H:i:s'),'document_modified_by'=>$username]);
               $download_file= '';
              if($document_file_name)
            {
                $splitVersions = pathinfo($document_file_name);
                $extension = $splitVersions['extension'];
                $partfilename = $splitVersions['filename'];
                $version_no = $document->document_version_no;
                $source = config('app.base_path').$document_file_name;

                $dest = config('app.backup_path').$partfilename.'_'.$version_no.'.'.$extension;
                if(file_exists($source))
                {
                    $download_file= config('app.doc_url').$document_file_name;
                    /*$copy = copy($source, $dest);*/
                }
            }
                DocumentsCheckoutModel::where('document_id','=',$id)->delete();  
                DocumentsColumnCheckoutModel::where('document_id','=',$id)->delete();   

                $doc_items = DocumentsModel::where('document_id', '=', $id )->get()->toArray();
                foreach ($doc_items as $data) 
                { 
                    $data['documents_checkout_by'] = Auth::user()->id;
                    $checkmodl= new DocumentsCheckoutModel;
                    $checkmodl->insert($data);
                }

                $doc_columns=DocumentsColumnModel::where('document_id', '=', $id )->get()->toArray(); 
                foreach ($doc_columns as $columns) 
                { 
                    $col_checkmodl= new DocumentsColumnCheckoutModel;
                    $col_checkmodl->insert($columns);
                }

                if($comments)
                {
                    $documentNteModl                  =   new DocumentNoteModel;
                    $documentNteModl->document_id     =   $id;
                    $documentNteModl->document_note   =   $comments;
                    $documentNteModl->document_note_created_by  =   $username;
                    $documentNteModl->save();
                }

                DocumentsModel::where('document_id',$id)->update(['document_status'=>'Checkout','document_pre_status'=>'Published','document_checkout_date'=>date('Y-m-d h:i:s')]);

                
        
                $actionMsg = Lang::get('language.checkOut_action_msg');
                $actionDes = $this->docObj->stringReplace($this->actionName1,$document_file_name,$username,$actionMsg);

                $result = (new AuditsController)->dcmntslog($username, $id, 'Document', 'Check Out',$actionDes,$document->document_no,$document->document_file_name,$document->document_path);
                $response['status'] = 1;
                $response['download_file'] = $download_file;
                $response['file_name'] = $document_file_name;
            }
            else
            {
                $response['status'] = 2;
            } 

            echo json_encode($response);  
        } 
        else 
        {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    public function download(){  
        if (Auth::user()) {

            $name = '';
            if(Input::get('file')){
                $name = Input::get('file');
            }
            if(Session::get('sess_download_file')){
                $name=Session::get('sess_download_file');
            } 
            $file= config('app.base_path').$name;
            Session::forget('sess_download_file');
        return Response::download($file);    
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }


    public function relatedsearch($id)
    {
        if (Auth::user()) {
            Session::put('selected_doc_list',$id);
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            $data['settingsDetails'] =      SettingsModel::getSettingsDetails();//Settings details
            $data['doc_columns']=DocumentsColumnModel::select('document_column_name')->where('document_id',$id)->get();
            $data['doc_values']=DocumentsColumnModel::select('document_column_value')->where('document_id',$id)->get();
            $doc_details = DocumentsModel::select('document_id','document_type_id','document_expiry_date','document_tagwords','document_no','document_name')->where('document_id',$id)->first();
            $data['doc_type_id']=DocumentsModel::select('document_type_id')->where('document_id',$id)->get();
            $data['doc_expiry']=DocumentsModel::select('document_expiry_date')->where('document_id',$id)->get();
            
            $doc_tag_words = $doc_details->document_tagwords;
            $tag_array = explode(',', $doc_details->document_tagwords);
            $data['tagwords'] = TagWordsModel::select('tagwords_title','tagwords_id')->whereIn('tagWords_id',$tag_array)->get();
            $data['no']=$doc_details->document_no;
            $data['name']=$doc_details->document_name;
            $data['id']=$doc_details->document_id;
            $doctypeid = $doc_details->document_type_id;
            $data['dglist'] = DocumentsModel::where('document_id',$id)->get();
            
            $settings = DocumentTypesModel::select('document_type_column_no','document_type_column_name')->where('document_type_id', $doctypeid)->get();
            $data['settings_document_no'] = $settings[0]->document_type_column_no;
            $data['settings_document_name'] = $settings[0]->document_type_column_name;
            
            $data['docType'] = DocumentTypesModel::all();
            $data['stacks'] = StacksModel::all();
            $data['depts'] = DepartmentsModel::all();
            return View::make('pages/documents/relateSearch')->with($data);
        }
        else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function relateResultTag()
    {
        if (Auth::user()) 
        {
            @$document_id =Input::get('document_id');
            $tag = Input::get('tag');
            $data['dglist'] = DocumentsModel::where('document_tagwords',$tag)->where('document_id','!=',$document_id)->get();
            $doctype = DocumentsModel::select('document_type_id')->where('document_id',$document_id)->first();
            $settings = DocumentTypesModel::select('document_type_column_no','document_type_column_name')->where('document_type_id', $doctype->document_type_id)->get();
            $data['settings_document_no'] = $settings[0]->document_type_column_no;
            $data['settings_document_name'] = $settings[0]->document_type_column_name;
            $data['docType'] = DocumentTypesModel::all();
            $data['stacks'] = StacksModel::all();
            $data['depts'] = DepartmentsModel::all();
            return View::make('pages/documents/relate_type_base')->with($data);
        }
        else{
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function relateResult()
    {
        if (Auth::user()) 
        {
            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;
            $doclistid =(Input::get('doctype_id'))?Input::get('doctype_id'):0;
            $doc_col_name=(Input::get('sel_col'))?Input::get('sel_col'):0;
            $doc_col_value=(Input::get('sel_value'))?Input::get('sel_value'):0;
            $selected_type = (Input::get('selected_type'))?Input::get('selected_type'):0;
            @$document_id =(Input::get('document_id'))?Input::get('document_id'):0;
            $query = DB::table('tbl_documents_columns')
            ->join('tbl_documents','tbl_documents.document_id','=','tbl_documents_columns.document_id')
            ->where('tbl_documents.document_id','!=',$document_id);
        if($selected_type != 0)// if document type selected
        {
            $settings = DocumentTypesModel::select('document_type_column_no','document_type_column_name')->where('document_type_id', $selected_type)->get();
            $data['settings_document_no'] = $settings[0]->document_type_column_no;
            $data['settings_document_name'] = $settings[0]->document_type_column_name;
            
            //column names fetches
            $data['col_names'] = DocumentTypeColumnModel::select('document_type_column_name')->where('document_type_id',$selected_type)->orderBy('document_type_column_order','ASC')->get();
            //fetch selected doctype data
            $query->where('tbl_documents.document_type_id',$selected_type);
            if($doc_col_name==$settings[0]->settings_document_no)
            {
                $query->where('tbl_documents.document_no',$doc_col_value);
                $query->groupBy('tbl_documents.updated_at');
                $data['dglist'] = $query->get();
            }
            elseif($doc_col_name==$settings[0]->settings_document_name)
            {
                $match_name = $doc_col_value;//exact matching

                // Partial matching enable this

                /*$match_name = substr($doc_col_value, 0, 5);//first 5 characters matching 
                $query->where(function ($query1) use($doc_col_value,$match_name,$document_id) {
                    $query1->orWhere('tbl_documents.document_name', 'like', '%' . $doc_col_value . '%')
                    ->where('tbl_documents.document_id','!=',$document_id);
                })->orWhere(function($query1) use($doc_col_value,$match_name,$document_id) {
                    $query1->orWhere('tbl_documents.document_name', 'like', '%' . $match_name . '%')
                    ->where('tbl_documents.document_id','!=',$document_id);
                });*/
                $query->Where('tbl_documents.document_name',$doc_col_value)
                    ->where('tbl_documents.document_id','!=',$document_id);
                $query->groupBy('tbl_documents.updated_at');
                $data['dglist'] = $query->get();
            }
            elseif(isset($doc_col_value))
            {
                //$query->where('tbl_documents_columns.document_column_name',$doc_col_name);
                $query->where('tbl_documents_columns.document_column_value',$doc_col_value);
                $query->groupBy('tbl_documents.updated_at');
                $data['dglist'] = $query->get();
            }
            //expand columns
            foreach($data['dglist'] as $val):
                $val->document_type_columns = DB::table('tbl_documents_columns')
                ->leftJoin('tbl_document_types_columns','tbl_document_types_columns.document_type_column_id','=','tbl_documents_columns.document_type_column_id')
                ->select('tbl_documents_columns.*','tbl_document_types_columns.*')
                ->where('tbl_documents_columns.document_id',$val->document_id)
                ->where('tbl_document_types_columns.document_type_id',$selected_type)
                 ->orderby('tbl_document_types_columns.document_type_column_order','ASC')
                ->get();                      
            endforeach;    
        }
        else
        {
            if($doc_col_name==$settings[0]->settings_document_no)
            {
                $query->where('tbl_documents.document_no',$doc_col_value);
                $query->groupBy('tbl_documents.updated_at');
                $data['dglist'] = $query->get();
            }
            elseif($doc_col_name==$settings[0]->settings_document_name)
            {
                $match_name = $doc_col_value;//exact matching

                // partial matching enable this code

                /*$match_name = substr($doc_col_value, 0, 5);//first 5 characters matching 
                $query->where(function ($query1) use($doc_col_value,$match_name,$document_id) {
                    $query1->orWhere('tbl_documents.document_name', 'like', '%' . $doc_col_value . '%')
                    ->where('tbl_documents.document_id','!=',$document_id);
                })->orWhere(function($query1) use($doc_col_value,$match_name,$document_id) {
                    $query1->orWhere('tbl_documents.document_name', 'like', '%' . $match_name . '%')
                    ->where('tbl_documents.document_id','!=',$document_id);
                });*/
                $query->Where('tbl_documents.document_name',$doc_col_value)
                    ->where('tbl_documents.document_id','!=',$document_id);
                $query->groupBy('tbl_documents.updated_at');
                $data['dglist'] = $query->get();
            }
            elseif(isset($doc_col_value))
            {
                //$query->where('tbl_documents_columns.document_column_name',$doc_col_name);
                $query->where('tbl_documents_columns.document_column_value',$doc_col_value);
                $query->groupBy('tbl_documents.updated_at');
                $data['dglist'] = $query->get();
            }
                  
        }
                // Expanding dglits with required datas
                foreach($data['dglist'] as $val):
                    // Get documentTypes
                    //$val->documentTypes = DB::table('tbl_document_types')->select(DB::raw('GROUP_CONCAT(document_type_name) AS document_type_names','document_type_column_no','document_type_column_name'))->whereIn('document_type_id',explode(',',$val->document_type_id))->get();

                    $val->documentTypes = DocumentTypesModel::select('document_type_column_no','document_type_column_name')->where('document_type_id', $val->document_type_id)->get();

                    // Get stack
                    $val->stacks = DB::table('tbl_stacks')->select(DB::raw('GROUP_CONCAT(stack_name) AS stack_name'))->whereIn('stack_id',explode(',',$val->stack_id))->get();
                    // Get Tag words
                    $val->tagwords = DB::table('tbl_tagwords')->select(DB::raw('GROUP_CONCAT(tagwords_title) AS tagwords_title'))->whereIn('tagwords_id',explode(',',$val->document_tagwords))->get();
                    // Get department
                    $val->department = DB::table('tbl_departments')->select(DB::raw('GROUP_CONCAT(department_name) AS department_name'))->whereIn('department_id',explode(',',$val->department_id))->get();  
                endforeach; 
                
                $data['docType'] = DocumentTypesModel::all();
                $data['stacks'] = StacksModel::all();
                $data['depts'] = DepartmentsModel::all();
                $data['stckApp'] = $this->docObj->common_stack();
                $data['deptApp'] = $this->docObj->common_dept();
                $data['doctypeApp'] = $this->docObj->common_type();
                $data['records'] = $this->docObj->common_records();
            return View::make('pages/documents/relate_type_base')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function relateResult_previous()
    {
        if (Auth::user()) {
        
            //values from view
            
            @$document_id =(Input::get('document_id'))?Input::get('document_id'):0;
            
            $doctype = DocumentsModel::select('document_type_id')->where('document_id',$document_id)->first();

            $settings = DocumentTypesModel::select('document_type_column_no','document_type_column_name')->where('document_type_id', $doctype->document_type_id)->get();
            $data['settings_document_no'] = $settings[0]->document_type_column_no;
            $data['settings_document_name'] = $settings[0]->document_type_column_name;

            @$doc_col_name = (Input::get('sel_col'))?Input::get('sel_col'):0;
            
            @$doc_col_value = (Input::get('sel_value'))?Input::get('sel_value'):0;
            // echo $document_id;
            // echo $doc_col_name;
            // echo $doc_col_value;
            // exit();
            //data from history table
            $query = DB::table('tbl_documents_history')
            ->leftjoin('tbl_documents_history_columns','tbl_documents_history.document_id','=','tbl_documents_history_columns.document_id')->select('tbl_documents_history.*');
            
            //->where('tbl_documents_history.document_id',$document_id);
            if(($doc_col_name==0) && ($doc_col_value==0))
            {  
                $query->where('tbl_documents_history.document_id',$document_id)       
                ->groupBy('tbl_documents_history.document_version_no');
                $data['dglist'] = $query->get();
            }

            
            else
            {
                if($doc_col_name==$settings[0]->settings_document_no)
                {
                    $query->where('tbl_documents_history.document_no',$doc_col_value);
                    $query->groupBy('tbl_documents_history.document_version_no');
                    $data['dglist'] = $query->get();
                }
                elseif($doc_col_name==$settings[0]->settings_document_name)
                {
                    $match_name = $doc_col_value;//exact matching
                    /*$match_name = substr($doc_col_value, 0, 5);//first 5 characters matching 
                    $query->where(function ($query1) use($doc_col_value,$match_name) {
                        $query1->orWhere('tbl_documents_history.document_name', 'like', '%' . $doc_col_value . '%')
                        ->where('tbl_documents_history.document_name',$doc_col_value);
                    })->orWhere(function($query1) use($doc_col_value,$match_name) {
                        $query1->orWhere('tbl_documents_history.document_name', 'like', '%' . $match_name . '%')
                        ->where('tbl_documents_history.document_name',$doc_col_value);
                    });*/
                    $query->Where('tbl_documents_history.document_name',$doc_col_value);
                    $query->groupBy('tbl_documents_history.document_version_no');
                    $data['dglist'] = $query->get();
                }
                elseif(isset($doc_col_value))
                {
                    // $query = DB::table('tbl_documents_history_columns');
                    //     ->Join('tbl_documents_history','tbl_documents_history.document_history_id','=','tbl_documents_history_columns.document_history_id');
                    //$query->where('tbl_documents_history_columns.document_id',$document_id);
                    //$query->where('tbl_documents_history_columns.document_column_name',$doc_col_name);
                    $query->where('tbl_documents_history_columns.document_column_value',$doc_col_value);
                    $query->groupBy('tbl_documents_history.document_version_no');
                    $data['dglist'] = $query->get();
                }
            }
            // echo '<pre>';
            // print_r($data['dglist']);
            // exit();
               foreach($data['dglist'] as $val){
                     // $val->document_type_columns = DB::table('tbl_documents_history')
                     // ->leftjoin('tbl_documents_history_columns','tbl_documents_history.document_history_id','=','tbl_documents_history_columns.document_history_id')
                     // ->where('tbl_documents_history_columns.document_id',@$val->document_id)
                     // ->where('tbl_documents_history_columns.document_history_id',@$val->document_history_id)
                     // ->where('tbl_documents_history.document_version_no',@$val->document_version_no)
                     // ->get();                     

                    // Get documentTypes
                    //$val->documentTypes = DB::table('tbl_document_types')->select(DB::raw('GROUP_CONCAT(document_type_name) AS document_type_names','document_type_column_no','document_type_column_name'))->whereIn('document_type_id',explode(',',$val->document_type_id))->get();

                    $val->documentTypes = DocumentTypesModel::select('document_type_column_no','document_type_column_name')->where('document_type_id', $val->document_type_id)->get();

                    // Get stack
                    $val->stacks = DB::table('tbl_stacks')->select(DB::raw('GROUP_CONCAT(stack_name) AS stack_name'))->whereIn('stack_id',explode(',',$val->stack_id))->get();
                    
                    // Get department
                    $val->department = DB::table('tbl_departments')->select(DB::raw('GROUP_CONCAT(department_name) AS department_name'))->whereIn('department_id',explode(',',$val->department_id))->get();   
                }
            
            $data['docType'] = DocumentTypesModel::all();
            $data['stacks'] = StacksModel::all();
            $data['depts'] = DepartmentsModel::all();
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            // echo'<pre>';
            // print_r($data);
            // exit();
            return View::make('pages/documents/relate_type_base_previous')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function annotehandler()
    {
        if (Auth::user()) {
            $data['usr_logged'] = Auth::user()->id;
            return View::make('pages/documents/annotationHandler')->with($data);
        }
        else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    // Get image annotations
    public function getImgAnnotations()
    {  
        $records = DB::table('tbl_image_annotation')->select('image_annotation_top as top','image_annotation_left as left','image_annotation_width as width','image_annotation_height as height','image_annotation_message as text','image_annotation_id as id')->where('document_Id',Input::get('dId'))->get();
        foreach($records as $val):
            $val->editable = 'true';
            endforeach;
        return json_encode($records);
    }

    // Save image annotation
    public function saveImgAnnotation(){
        // Prepare values
        $values = Input::get();
        $values['username'] = Auth::user()->username;
        
        if(Input::get('image_annotation_id') == 'new'){
            // Add
            $id = DB::table('tbl_image_annotation')->insertGetId($values);

            // Save in audits
            $actionMsg = Lang::get('language.save_action_msg');
            $actionDes = $this->docObj->stringReplace($this->decs,$values['image_annotation_message'],Auth::user()->username,$actionMsg);
            $result    = (new AuditsController)->log(Auth::user()->username, $this->module,Lang::get('language.insert'),$actionDes);

            return $id;
        }else{
            // Update
            DB::table('tbl_image_annotation')->where('image_annotation_id',Input::get('image_annotation_id'))->update($values);

            // Save in audits
            $actionMsg = Lang::get('language.update_action_msg');
            $actionDes = $this->docObj->stringReplace($this->decs,$values['image_annotation_message'],Auth::user()->username,$actionMsg);
            $result    = (new AuditsController)->log(Auth::user()->username, $this->module,Lang::get('language.update'),$actionDes);

            return '0';
        }
    
    } 

    // Delete image annotation
    public function deleteImgAnnotation()
    {
        DB::table('tbl_image_annotation')->where('image_annotation_id',Input::get('image_annotation_id'))->delete();
        // Save in audits
        $actionMsg = Lang::get('language.delete_action_msg');
        $actionDes = $this->docObj->stringReplace($this->decs,Input::get('title'),Auth::user()->username,$actionMsg);
        $result    = (new AuditsController)->log(Auth::user()->username, $this->module,Lang::get('language.deleted'),$actionDes);
        return Lang::get('language.deleted_successfully');
    }

    // Save and update image rotation
    public function saveImgRotations()
    {
        if(Input::get('update') == 'yes'):
            $values['document_is_image_values_saved'] = Input::get('document_is_image_values_saved');
        else:
            $values = array('document_image_scale'=>Input::get('scale'),
                            'document_image_angle'=>Input::get('angle'),
                            'document_image_x'=>Input::get('x'),
                            'document_image_y'=>Input::get('y'),
                            'document_image_w'=>Input::get('w'),
                            'document_image_h'=>Input::get('h'),
                            'document_is_image_values_saved'=>Input::get('document_is_image_values_saved'));
        endif;
    
        // Update 
        if(Input::get('page') == 'import'){
            $table = 'tbl_temp_documents';
        }elseif(Input::get('page') == 'checkout'){
            $table = 'tbl_documents_checkout';
        }else{
            $table = 'tbl_documents';
        }

        DB::table($table)->where('document_id',Input::get('documentId'))->update($values);

        return 'successfully document table updated';// Nothing but just for show message in console.log
    }
    public function RecordsCountContentSearch(Request $request)
    {

        $section = (Input::get('section'))?Input::get('section'):'documents';//
        $department = (Input::get('department'))?Input::get('department'):0;//
        $stacks = (Input::get('stacks'))?Input::get('stacks'):0;//
        $doctypeid = (Input::get('doctypeid'))?Input::get('doctypeid'):0;//
        $ownership = (Input::get('ownership'))?Input::get('ownership'):0;//
        $created_date_from = (Input::get('created_date_from'))?Input::get('created_date_from'):0;//
        $created_date_to = (Input::get('created_date_to'))?Input::get('created_date_to'):0;//
        $last_modified_from = (Input::get('last_modified_from'))?Input::get('last_modified_from'):0;//
        $last_modified_to = (Input::get('last_modified_to'))?Input::get('last_modified_to'):0;//

        $query = $this->optimise_content($section,$department,$stacks,$doctypeid,$ownership,$created_date_from,$created_date_to,$last_modified_from,$last_modified_to);
            
        $file_count = $query->count();
        echo $file_count;
        exit();
    }
    public function optimise_content($section,$department,$stacks,$doctypeid,$ownership,$created_date_from,$created_date_to,$last_modified_from,$last_modified_to)
    {
        if($section == 'documents')
        {
            //get count of files
            //DB::enableQueryLog();
            $query = DB::table('tbl_documents');
            // If ownership exists
            if($ownership)
            {
                $query->where('document_ownership','=',$ownership);
            }
            // If department exists
             if($department)
            {
                $query->where('department_id','=',$department);
            }
            // If document type id exists
             if($doctypeid)
            {
                $query->where('document_type_id','=',$doctypeid);
            }
            // If statcks exists
             if($stacks)
            {
                $query->where('stack_id','=',$stacks);
            }
            // If created_date_from exists 
             if($created_date_from)
            {
                $query->where('created_at','>=',$created_date_from.' 00:00:00');
            }
            // If created_date_to exists 
             if($created_date_to)
            {
                $query->where('created_at','<=',$created_date_to.' 23:59:59');
            }
            // If last_modified_from exists 
             if($last_modified_from)
            {
                $query->where('updated_at','>=',$last_modified_from.' 00:00:00');
            }
            // If last_modified_to exists 
             if($last_modified_to)
            {
                $query->where('updated_at','<=',$last_modified_to.' 23:59:59');
            }
        }
        else
        {
            //get count of forms
            $query = DB::table('tbl_form_responses');
            
            // If created_date_from exists 
            if(Input::get('created_date_from'))
            {
                $query->where('created_at','>=',Input::get('created_date_from').' 00:00:00');
            }
            // If created_date_to exists 
            if(Input::get('created_date_to'))
            {
                $query->where('created_at','<=',Input::get('created_date_to').' 23:59:59');
            }
            // If last_modified_from exists 
            if(Input::get('last_modified_from'))
            {
                $query->where('updated_at','>=',Input::get('last_modified_from').' 00:00:00');
            }
            // If last_modified_to exists 
            if(Input::get('last_modified_to'))
            {
                $query->where('updated_at','<=',Input::get('last_modified_to').' 23:59:59');
            }
        }
        $query->where(function ($query1) {
            $query1->where('document_file_name', 'like', '%' . '.pdf' . '%')
            ->orwhere('document_file_name', 'like', '%' . '.doc' . '%')
            ->orwhere('document_file_name', 'like', '%' . '.docx' . '%')
            ->orwhere('document_file_name', 'like', '%' . '.txt' . '%');
                });

        //$queries = DB::getQueryLog();
        //print_r($queries);
        //echo $file_count;
        return $query;
    }
    public function contentSearchview()
    {
        $data['docType'] = DocumentTypesModel::where('is_app',0)->orderBy('document_type_order', 'ASC')->get();
        $data['stacks'] = StacksModel::all();
        $data['depts'] = DepartmentsModel::orderBy('department_order', 'ASC')->get();
        $data['users'] = Users::all();
        $data['stckApp'] = $this->docObj->common_stack();
        $data['deptApp'] = $this->docObj->common_dept();
        $data['doctypeApp'] = $this->docObj->common_type();
        $data['records'] = $this->docObj->common_records();
        return View::make('pages/documents/contentsearch')->with($data);
    }
    public function setDefaultview(Request $request)
    {
        if (Auth::user()) 
        {
            $view = Input::get('defaultview');
            //set default view
            DB::table('tbl_users')->where('id',Auth::user()->id)->update(['user_documents_default_view'=>$view]);
            return $view; 
        }
    }

    public function encrypt(Request $request)
    {
        if (Auth::user()) 
        {
            $ALGORITHM = 'AES-128-CBC';
            $IV    = '12dasdq3g5b2434b';
            $error = '';

            if (isset($_POST) && isset($_POST['action'])) {
                //password for the encryption take from settings table
                $Encryption_password = DB::table('tbl_settings')->select('settings_encryption_pwd')->first();
                if($Encryption_password)
                {
                    $encrypted_pwd = $Encryption_password->settings_encryption_pwd;
                    $password = decrypt($encrypted_pwd);
                    // echo $password;
                    // exit();
                }
                else
                {
                    $password = null;
                }
                //action 'c'=> 'Encrypt'; 'd'=> 'Decrypt';

                $action = isset($_POST['action']) && in_array($_POST['action'],array('c','d')) ? $_POST['action'] : null;

                $file_name = (Input::get('file'))?Input::get('file'): null;
                $docname = (Input::get('docname'))?Input::get('docname'): 'Document';

                if($file_name!=null)
                {
                    if(file_exists(config('app.base_path').$file_name)){
                        $file = config('app.base_path').$file_name;
                    }
                    else
                    {
                        $file = null;
                    }
                }
                // echo $file;
                // exit();
              
              if ($password === null) {
                $error .= 'Invalid Password<br>';
              }
              if ($action === null) {
                $error .= 'Invalid Action(Encryption)<br>';
              }
              if ($file === null) {
                $error .= 'File not exist<br>';
              }
              
              if ($error === '') {
              
                $contenuto = '';
              
                $contenuto = file_get_contents($file);
                $filename  = $file_name;
              
                switch ($action) {
                  case 'c':
                    $contenuto = openssl_encrypt($contenuto, $ALGORITHM, $password, 0, $IV);
                    $filename  = $filename . Lang::get('language.encrypt_extension');
                    break;
                }
                
                if ($contenuto === false) {
                  $error .= 'Errors occurred while encrypting the file ';
                }
                
                if ($error === '') 
                {
                
                    $size = strlen($contenuto);
                      $headers = array(
                              "Pragma: public",
                              "Pragma: no-cache",
                              "Cache-Control: no-store, no-cache, must-revalidate, max-age=0",
                              "Cache-Control: post-check=0, pre-check=0", false,
                              "Expires: 0",
                              "Content-Type: application/octet-stream",
                              "Content-Length: " . $size
                            );
                //create a new file with '.crypto' extension
                $encrypted = File::put(config('app.base_path').$filename,$contenuto);
                //delete the original file(which is encrypted) or it will cause to duplication when decrypt them.
                if(file_exists(config('app.base_path').$file_name))
                {
                    $delete_file = unlink(config('app.base_path').$file_name);
                }
                //save the encrypted password in document table for further operations
                if($encrypted)
                {
                    DB::table('tbl_documents')->where('document_id',Input::get('docid'))->update(['document_encrypt_password'=>$encrypted_pwd,
                                'document_encrypted_by'=>Auth::user()->user_full_name,
                                'document_encrypted_on'=>date('Y-m-d H:i:s'),
                                'document_encrypt_status'=>1]);
                    //encrypt action added to audit
                    $action_desc = "Document $docname encryted by ".Auth::user()->username;
                    DB::table('tbl_audits')->insert(['document_id'=>Input::get('docid'),'document_name'=>Input::get('docname'),'audit_owner'=>'Document','audit_user_name'=>Auth::user()->username,'audit_action_type'=>'Encrypt','audit_action_desc'=>$action_desc,'created_at'=>date('Y-m-d h:i:s')]);
                }
                echo "1";
                exit();
                }
                else
                {
                    echo "$error";
                    exit();
                }
              
              }
              
            }

        }
    }
    public function decrypt(Request $request)
    {
        //check decrypt permission
        $user_permission=Auth::user()->user_permission;
        if(stristr($user_permission,"decrypt"))
        {
            $ALGORITHM = 'AES-128-CBC';
            $IV    = '12dasdq3g5b2434b';

            $error = '';

            if (isset($_POST) && isset($_POST['action'])) {
                //password for the decryption take from document table
                $Decryption_password = DB::table('tbl_documents')->select('document_encrypt_password')->where('document_id',Input::get('docid'))->first();
                if($Decryption_password)
                {
                    $Decrypted_pwd = $Decryption_password->document_encrypt_password;
                    $password = decrypt($Decrypted_pwd);
                    // echo $password;
                    // exit();
                }
                else
                {
                    $password = null;
                }
                //action 'c'=> 'Encrypt'; 'd'=> 'Decrypt';

                $action = isset($_POST['action']) && in_array($_POST['action'],array('c','d')) ? $_POST['action'] : null;

                $file_name = (Input::get('file'))?Input::get('file'): null;
                $docname = (Input::get('docname'))?Input::get('docname'): 'Document';

                if($file_name!=null)
                {
                    if(file_exists(config('app.base_path').$file_name.Lang::get('language.encrypt_extension'))){
                        $file = config('app.base_path').$file_name.Lang::get('language.encrypt_extension');
                    }
                    else
                    {
                        $file = null;
                    }
                }
                // echo $file;
                // exit();
              
              if ($password === null) {
                $error .= 'Invalid Password<br>';
              }
              if ($action === null) {
                $error .= 'Invalid Action(Encryption)<br>';
              }
              if ($file === null) {
                $error .= 'File not exist<br>';
              }
              
              if ($error === '') {
              
                $contenuto = '';
              
                $contenuto = file_get_contents($file);
                $filename  = $file_name;
              
                switch ($action) {
                  case 'd':
                    $contenuto = openssl_decrypt($contenuto, $ALGORITHM, $password, 0, $IV);
                    $filename  = preg_replace('#\.crypto$#','',$filename);
                    break;
                }
                
                if ($contenuto === false) {
                  $error .= 'Errors occurred while decrypting the file ';
                }
                
                if ($error === '') {
                
                    $size = strlen($contenuto);
                      $headers = array(
                              "Pragma: public",
                              "Pragma: no-cache",
                              "Cache-Control: no-store, no-cache, must-revalidate, max-age=0",
                              "Cache-Control: post-check=0, pre-check=0", false,
                              "Expires: 0",
                              "Content-Type: application/octet-stream",
                              "Content-Length: " . $size
                            );
                //create a new file with '.crypto' extension
                $decrypted = File::put(config('app.base_path').$filename,$contenuto);
                //delete the original file(which is encrypted) or it will cause to duplication when decrypt them.
                if(file_exists(config('app.base_path').$file_name.Lang::get('language.encrypt_extension')))
                {
                    $delete_file = unlink(config('app.base_path').$file_name.Lang::get('language.encrypt_extension'));
                }
                //save the encrypted password in document table for further operations
                if($decrypted)
                {
                    DB::table('tbl_documents')->where('document_id',Input::get('docid'))->update(['document_encrypt_password'=>null,
                                'document_decrypted_by'=>Auth::user()->user_full_name,
                                'document_decrypted_on'=>date('Y-m-d H:i:s'),
                                'document_encrypt_status'=>0]);
                    //decrypt action added to audit
                    $action_desc = "Document $docname decrypted by ".Auth::user()->username;
                    DB::table('tbl_audits')->insert(['document_id'=>Input::get('docid'),'document_name'=>Input::get('docname'),'audit_owner'=>'Document','audit_user_name'=>Auth::user()->username,'audit_action_type'=>'Decrypt','audit_action_desc'=>$action_desc,'created_at'=>date('Y-m-d h:i:s')]);
                }
                echo "1";
                exit();
                }
                else
                {
                    echo "$error";
                    exit();
                }
              
              }
              
            }
        }
        else
        {
            echo "You have no permission";
            exit();
        }
    }
    //encrypt all from list views
    public function bulkEncrypt()
    {
        if (Auth::user()) 
        {
            $error = '';
            $ALGORITHM = 'AES-128-CBC';
            $IV    = '12dasdq3g5b2434b';
            $view = Input::get('view');
            $table = 'tbl_documents';//tbl_documents
            $action = 'encrypt';
            $Encryption_password = DB::table('tbl_settings')->select('settings_encryption_pwd')->first();
                if($Encryption_password)
                {
                    $encrypted_pwd = $Encryption_password->settings_encryption_pwd;
                    $password = decrypt($encrypted_pwd);
                }
                else
                {
                    echo "Please enter the password for encryption";
                    exit();
                }
            $arr=Input::get('selected');
            //fetch filenames and status, not fetch the already encrypted files
            $files_array = DB::table('tbl_documents')->select('document_id','document_file_name','document_name','document_encrypt_status')->whereIn('document_id',$arr)->where('document_encrypt_status','!=',1)->get();
            if($files_array)
            {
                foreach ($files_array as $value) 
                {
                    $file_name = (@$value->document_file_name)?@$value->document_file_name: null;
                    $enc_document_name = (@$value->document_name)?@$value->document_name: 'Document';
                    if($file_name!=null)
                    {
                        if(file_exists(config('app.base_path').$file_name)){
                            $file = config('app.base_path').$file_name;
                        }
                        else
                        {
                            echo 'Errors occured while encrypting document: \''.$enc_document_name.'\' </br>File: \''.$file_name.'\' does not exist';
                            exit();
                        }
                    }
                    //encryption start
                    $contenuto = '';
                    $contenuto = file_get_contents($file);
                    $filename  = $file_name;
                    $contenuto = openssl_encrypt($contenuto, $ALGORITHM, $password, 0, $IV);
                    $filename  = $filename . Lang::get('language.encrypt_extension');
                    
                    if ($contenuto === false) {
                      $error .= 'Errors occured while encrypting document: \''.$enc_document_name.'\' file: \''.$file_name.'\'</br>';
                    }
                    
                    if ($error === '') 
                    {
                    
                        $size = strlen($contenuto);
                        $headers = array(
                                "Pragma: public",
                                "Pragma: no-cache",
                                "Cache-Control: no-store, no-cache, must-revalidate, max-age=0",
                                  "Cache-Control: post-check=0, pre-check=0", false,
                                  "Expires: 0",
                                  "Content-Type: application/octet-stream",
                                  "Content-Length: " . $size
                                );
                    //create a new file with '.crypto' extension
                    $encrypted = File::put(config('app.base_path').$filename,$contenuto);
                    //delete the original file(which is encrypted) or it will cause to duplication when decrypt them.
                    if(file_exists(config('app.base_path').$file_name))
                    {
                        $delete_file = unlink(config('app.base_path').$file_name);
                    }
                    //save the encrypted password in document table for further operations
                    if($encrypted)
                    {
                        DB::table('tbl_documents')->where('document_id',@$value->document_id)->update(['document_encrypt_password'=>$encrypted_pwd,
                                    'document_encrypted_by'=>Auth::user()->user_full_name,
                                    'document_encrypted_on'=>date('Y-m-d H:i:s'),
                                    'document_encrypt_status'=>1]);
                        //encrypt action added to audit
                        $action_desc = "Document $enc_document_name encryted by ".Auth::user()->username;
                        DB::table('tbl_audits')->insert(['document_id'=>@$value->document_id,'document_name'=>$enc_document_name,'audit_owner'=>'Document','audit_user_name'=>Auth::user()->username,'audit_action_type'=>'Encrypt','audit_action_desc'=>$action_desc,'created_at'=>date('Y-m-d h:i:s')]);
                    }
                    }
                }//for loop
                echo "Files are encrypted successfully";
                exit();
                if($error)
                {
                    echo $error;
                    exit();
                }
            }
            else
            {
                echo "Selected files are already encrypted";
                exit();
            }
            // print_r($files_array);
            // exit();
            
            // $data = $this->encrypt_all($arr,$view,$table);
            // echo json_encode("Documents deleted successfully");
        }
        else 
        {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
}/*<--END-->*/
