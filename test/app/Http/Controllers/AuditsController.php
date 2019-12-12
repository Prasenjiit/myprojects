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
use App\AuditsModel as AuditsModel;
use App\StacksModel as StacksModel;
use App\DepartmentsModel as DepartmentsModel;
use App\DocumentTypesModel as DocumentTypesModel;
use App\Users as Users;
use Lang;
use Hash;
use DateTime;
use App\Support\Database\CacheQueryBuilder;
use Illuminate\Database\Eloquent\Model;

class AuditsController extends Controller
{
    /**
     * For Caching all Queries.
     */
    use CacheQueryBuilder;
    public function __construct()
    {
        Session::put('menuid', '4');
        $this->middleware(['auth', 'user.status']);
        $this->docObj     = new Common(); // class defined in app/mylibs
        $this->delete     = Lang::get('language.delete');
        $this->purgeAudits= Lang::get('language.purge_audits');
        $this->user_name  = @Auth::user()->user_full_name;
        $this->no_of_days = date("Y-m-d", strtotime("-".Session('no_of_days')." days"));
    }
    
    public function index()
    {   // checking wether user logged in or not
        if (Auth::user()) {
            Session::put('menuid', '4');
            if(input::get('is_back') == null){
                // distroy sessions in select box
                Session::forget('username');
                Session::forget('actions');
                Session::forget('items');           
                Session::forget('docno');
                Session::forget('docname');
                Session::forget('stacks');
                Session::forget('dept');     
                Session::forget('date_from');
                Session::forget('date_to');
                Session::forget('stack_names'); 
                Session::forget('departmntNames');
                Session::forget('document_type_names');
                Session::forget('document_type_ids');
                Session::forget('dctype');
            }

            $data['docType'] = DocumentTypesModel::orderBy('document_type_order', 'ASC')->get();
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();

            // audtlists depending on the users
            $departmentIds  = explode(',',Auth::user()->department_id);
            if(Auth::user()->user_role == Session::get('user_role_super_admin')):// Admin
                $data['username'] = DB::table('tbl_audits')->select('audit_user_name')->distinct()->orderBy('audit_user_name', 'asc')->get();
            elseif(Auth::user()->user_role == Session::get('user_role_group_admin')):// Gorup Admin
                $data['username'] = DB::table('tbl_audits')->join('tbl_users','tbl_users.username','=','tbl_audits.audit_user_name')->join('tbl_users_departments','tbl_users_departments.users_id','=','tbl_users.id')->whereIn('tbl_users_departments.department_id',$departmentIds)->select('tbl_audits.audit_user_name')->distinct()->orderBy('tbl_audits.audit_user_name', 'asc')->get();
            else:// Regular User
                $data['username'] = DB::table('tbl_audits')->join('tbl_users','tbl_users.username','=','tbl_audits.audit_user_name')->where('tbl_users.id',Auth::user()->id)->distinct()->select('tbl_audits.audit_user_name')->get();
		    endif;   
            $data['stacks'] = DB::table('tbl_stacks')->distinct()->select('stack_id','stack_name')->get();

			$data['depts'] = DB::table('tbl_departments')->distinct()->select('department_id','department_name')->get();

			$data['doctypes'] = DB::table('tbl_document_types')->distinct()->select('tbl_document_types.document_type_name','document_type_id')->where('tbl_document_types.is_app',0)->orderBy('tbl_document_types.document_type_order','ASC')->get();	
            
			$data['action'] = DB::table('tbl_audits')->select('audit_action_type')->distinct()->orderBy('audit_action_type', 'asc')->get();
            $data['category'] = DB::table('tbl_audits')->select('audit_owner')->distinct()->orderBy('audit_owner', 'asc')->get();
            $data['no_of_days'] = $this->no_of_days;/*180 days before date from today*/
    
            //<!--For get from date(Hidden) to delete audits record-->
            /*max date: last request date to delete audits*/
            $maxDate = DB::table('tbl_audits_delete_request')->select(DB::RAW('MAX(delete_to_date) as delete_to_date'))->get();
             
            if($maxDate[0]->delete_to_date){
                $deleteToDate = new DateTime($maxDate[0]->delete_to_date);
                // Add one more day
                $data['old_date_in_audit'] = date('Y-m-d H:i:s', strtotime($deleteToDate->format('Y-m-d') . ' +1 day'));
                $maxDate = $maxDate[0]->delete_to_date;
            }else{
                $auditsCreated_at = DB::table('tbl_audits')->select('created_at')->orderBy('created_at', 'asc')->first();
                $data['old_date_in_audit'] = ($auditsCreated_at)?$auditsCreated_at->created_at:''; 
                $maxDate = $data['old_date_in_audit'];
                /*max date : oldest entry date from audits*/
            }
            // Date must be less than current date
            if(@$maxDate >= @$data['no_of_days']):
                $data['is_old_date_valid'] = "No";
            endif;

            /*echo $maxDate;
            echo '</br>';
            echo $data['no_of_days'];
            echo '</br>';
            echo Session('no_of_days');*/

            $purge_audits_info_msg = Lang::get('language.purge_audits_info_msg');
            $data['purge_audits_info_msg'] = $this->docObj->stringReplace(Session('no_of_days'),NULL,NULL,$purge_audits_info_msg);
            //<!--//END//--For get from date(Hidden) to delete audits record-->

            $data['count_superadmin'] = Users::where('user_role',1)->count();
            return View::make('pages/audits/search')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    public function showAll()
    {
        // checking wether user logged in or not
        if (Auth::user()) {
            Session::put('menuid', '4');
            // destroy the session audits search
            Session::put('is_search_exists','no');

            // <!--contents for side bar->
            $data['docType'] = DocumentTypesModel::orderBy('document_type_order', 'ASC')->get();
                $docObj = new Common(); // class defined in app/mylibs
                $data['stckApp'] = $docObj->common_stack();
                $data['deptApp'] = $docObj->common_dept();
                $data['doctypeApp'] = $docObj->common_type();
                $data['records'] = $docObj->common_records();
 
            return View::make('pages/audits/index')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    // Get all audits details in ajax pagination
    public function getAllAudits(){
        
        $departmentIds  = explode(',',Auth::user()->department_id);
        $noOfRecords   = DB::table('tbl_audits')->count();
        // storing  request (ie, get/post) global array to a variable  

        // To set order by
        $columns = array( 
        // datatable column index  => database column name
            0 => 'audit_user_name', 
            1 => 'audit_owner',
            2=> 'audit_action_desc',
            3=> 'audit_action_type',
            4=> 'created_at'
        );

        $requestData= $_REQUEST;
        $query = DB::table('tbl_audits')->select('tbl_audits.audit_user_name','tbl_audits.audit_owner','tbl_audits.audit_action_desc','tbl_audits.audit_action_type',DB::RAW('DATE_FORMAT(tbl_audits.created_at, "%d-%m-%Y %h:%i:%s %p") AS created_at'));
        // Search
        if( !empty($requestData['search']['value']) ) {
            $query->where("tbl_audits.audit_owner","LIKE",''.$requestData['search']['value']."%");
            $query->orWhere("tbl_audits.audit_user_name","LIKE",''.$requestData['search']['value']."%");
            $query->orWhere("tbl_audits.audit_action_desc","LIKE",''.$requestData['search']['value']."%");
            $query->orWhere("tbl_audits.audit_action_type","LIKE",''.$requestData['search']['value']."%");
            $query->orWhere("tbl_audits.created_at","LIKE",''.$requestData['search']['value']."%");
            $noOfRecords = $query->count();
         }

        // New Updation:It hidden becouse Departadmin and regular user won't see audits
        // audtlists depending on the users
        /*if(Auth::user()->user_role == '1'):// Admin
            $query->orderBy('created_at','desc');
        elseif(Auth::user()->user_role == '2'):// Gorup Admin
            $query->join('tbl_users','tbl_users.username','=','tbl_audits.audit_user_name')->join('tbl_users_departments','tbl_users_departments.users_id','=','tbl_users.id');
            $query->whereIn('tbl_users_departments.department_id',$departmentIds)->groupBy('tbl_audits.audit_id');
        else:// Regular User
            $query->join('tbl_users','tbl_users.username','=','tbl_audits.audit_user_name');
            $query->where('tbl_users.id',Auth::user()->id);
        endif;*/    
        //$query->orderBy('created_at','desc');
        // Ajax order by works
        $query->orderBy($columns[$requestData['order'][0]['column']], $requestData['order'][0]['dir']);
   
        $query->offset($requestData['start'])->limit($requestData['length']);

        $data['data'] = $query->get();

        // For ajax result
        $data['draw'] = intval( $requestData['draw'] );
        $data['recordsTotal'] = $noOfRecords;
        $data['recordsFiltered'] = $noOfRecords;
        $data['request'] = $noOfRecords;

        $y = array();
       foreach( $data['data'] as $val):
        $x = (array) $val;
        $y[] = array_values($x);  
        endforeach;

        $data['data'] = $y;
        echo json_encode($data);  // send data as json format
        

    }

    //audits search 
    public function searchadvncaud()
    {
        if (Auth::user()) {
           
            $data['docType'] = DocumentTypesModel::orderBy('document_type_order', 'ASC')->get();
                $docObj = new Common(); // class defined in app/mylibs
                $data['stckApp'] = $docObj->common_stack();
                $data['deptApp'] = $docObj->common_dept();
                $data['doctypeApp'] = $docObj->common_type();
                $data['records'] = $docObj->common_records();

            // Get values for make search(Go to auditsSearchRecords)
            $data['values']  = array('category'=>Input::get('category'),
                                     'username'=>Input::get('username'),
                                     'actions'=>Input::get('actions'),
                                     'stacks'=>Input::get('stacks'),
                                     'dept'=>Input::get('dept'),
                                     'dctype'=>Input::get('dctype'),
                                     'docno'=>Input::get('docno'),
                                     'docname'=>Input::get('docname'),
                                     'createddate_from'=>Input::get('createddate_from'),
                                     'createddate_to'=>Input::get('createddate_to'));

            // Set session to show filtered by  and make ajax custom search 
            Session::put('is_search_exists','yes');
            $category = Input::get('category');
            if(!empty($category)){
                $keyid = "";
                $categoryCnt = count(Input::get('category'));
                for($i=0; $i < $categoryCnt; $i++){
                    $kid = $category[$i];
                    if($i==0){
                        $keyid = $kid;
                    }else if($i==count($category)-1){
                        $keyid = $keyid.','.$kid;
                    }else{
                        $keyid = $keyid.','.$kid;
                    }
                }
                Session::put('items',$keyid);
            }else{
                Session::forget('items');
            }

            $username = Input::get('username');
            if(!empty($username)){
                $keyid = "";
                $usrnameCnt = count(Input::get('username'));
                for($i=0; $i < $usrnameCnt; $i++){
                    $kid = $username[$i];
                    if($i==0){
                        $keyid = $kid;
                    }else if($i==count($username)-1){
                        $keyid = $keyid.','.$kid;
                    }else{
                        $keyid = $keyid.','.$kid;
                    }
                }
                Session::put('username',$keyid);
            }else{
                Session::forget('username');
            }

            $action = Input::get('actions');
            if(!empty($action)){
                $keyid = "";
                $actionCnt = count(Input::get('actions'));
                for($i=0; $i < $actionCnt; $i++){
                    $kid = $action[$i];
                    if($i==0){
                        $keyid = $kid;
                    }else if($i==count($action)-1){
                        $keyid = $keyid.','.$kid;
                    }else{
                        $keyid = $keyid.','.$kid;
                    }
                }
                Session::put('actions',$keyid);
            }else{
                Session::forget('actions');
            }
            
            $stacks = Input::get('stacks');
            if(!empty($stacks)){
                $keyid = "";
                $stacksCnt = count(Input::get('stacks'));
                for($i=0; $i < $stacksCnt; $i++){
                    $kid = $stacks[$i];
                    if($i==0){
                        $keyid = $kid;
                    }else if($i==count($stacks)-1){
                        $keyid = $keyid.','.$kid;
                    }else{
                        $keyid = $keyid.','.$kid;
                    }
                }
                
                $stacksName = DB::table('tbl_stacks')->select(DB::RAW('GROUP_CONCAT(stack_name) AS stack_names'))->whereIn('stack_id',explode(',',$keyid))->get();
                Session::put('stack_names',$stacksName[0]->stack_names);
                Session::put('stacks',$keyid);
            }else{
                Session::forget('stack_names');
                Session::forget('stacks');
            }
            
            $dept = Input::get('dept');
            if(!empty($dept)){
                $keyid = "";
                $deptCnt = count(Input::get('dept'));
                for($i=0; $i < $deptCnt; $i++){
                    $kid = $dept[$i];
                    if($i==0){
                        $keyid = $kid;
                    }else if($i==count($dept)-1){
                        $keyid = $keyid.','.$kid;
                    }else{
                        $keyid = $keyid.','.$kid;
                    }
                }
                $departmntNames = DB::table('tbl_departments')->select(DB::RAW('GROUP_CONCAT(department_name) AS department_names'))->whereIn('department_id',explode(',',$keyid))->get(); 
                Session::put('departmntNames',$departmntNames[0]->department_names);
                Session::put('dept',$keyid);
            }else{
                Session::forget('departmntNames');
                Session::forget('dept');
            }
            
            
            $dctype = Input::get('dctype');
            if(!empty($dctype)){ 
                $keyid = "";
                $dctypeCnt = count(Input::get('dctype'));
                for($i=0; $i < $dctypeCnt; $i++){
                    $kid = $dctype[$i];
                    if($i==0){
                        $keyid = $kid;
                    }else if($i==count($dctype)-1){
                        $keyid = $keyid.','.$kid;
                    }else{
                        $keyid = $keyid.','.$kid;
                    }
                }
               
                $dctypeNames = DB::table('tbl_document_types')->select(DB::RAW('GROUP_CONCAT(document_type_id) as document_type_ids'),DB::RAW('GROUP_CONCAT(document_type_name) AS document_type_names'))->whereIn('document_type_id',$dctype)->get();
                Session::put('document_type_names',$dctypeNames[0]->document_type_names);
                Session::put('document_type_ids',$dctypeNames[0]->document_type_ids);
                Session::put('dctype',$keyid);
            }else{
                Session::forget('document_type_names');
                Session::forget('document_type_ids');
                Session::forget('dctype');
            }
            
            if(!empty(Input::get('docno'))){
                Session::put('docno',Input::get('docno'));
            }else{
                Session::forget('docno');
            }
            
            if(!empty(Input::get('docname'))){
                Session::put('docname',Input::get('docname'));
            }else{
                Session::forget('docname');
            }    

            if( (Input::get('createddate_from'))) { 
                Session::put('date_from',Input::get('createddate_from'));
            }else{
                Session::forget('date_from');
            }

            if((Input::get('createddate_to')))  { 
                Session::put('date_to',Input::get('createddate_to'));
            }else{
                Session::forget('date_to');
            }

            return View::make('pages/audits/index')->with($data);     
            
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    // audits search 
    public function auditsSearchRecords(){

        $searchedValues  = json_decode(Input::get('searchedValues'));
        //$noOfRecords = '80';
        
        // To set order by
        $columns = array( 
        // datatable column index  => database column name
            0 => 'audit_user_name', 
            1 => 'audit_owner',
            2=> 'audit_action_desc',
            3=> 'audit_action_type',
            4=> 'created_at'
        );

        $requestData= $_REQUEST;
        // DB query starts
        //$query = DB::table('tbl_audits')->select('tbl_audits.audit_user_name','tbl_audits.audit_owner','tbl_audits.audit_action_desc','tbl_audits.audit_action_type',DB::RAW('DATE_FORMAT(tbl_audits.created_at, "%d-%m-%Y %h:%i:%s %p") AS created_at'));
        $query = DB::table('tbl_audits')->select('tbl_audits.audit_user_name','tbl_audits.audit_owner','tbl_audits.audit_action_desc','tbl_audits.audit_action_type','tbl_audits.created_at');
        // Search
        if( !empty($requestData['search']['value']) ) {
            $GLOBALS['searchIpValues'] = $requestData['search']['value']; 

            $query->where(function($query){
                $query->where("tbl_audits.audit_owner","LIKE","%".$GLOBALS['searchIpValues']."%");
                $query->orWhere("tbl_audits.audit_user_name","LIKE","%".$GLOBALS['searchIpValues']."%");
                $query->orWhere("tbl_audits.audit_action_desc","LIKE","%".$GLOBALS['searchIpValues']."%");
                $query->orWhere("tbl_audits.audit_action_type","LIKE","%".$GLOBALS['searchIpValues']."%");
                $query->orWhere("tbl_audits.created_at","LIKE","%".$GLOBALS['searchIpValues']."%");
            });

            // seperate query with statring and closing brackets
            $query->where(function($query){

                if(Session::get('username')){
                    $query->whereIn('tbl_audits.audit_user_name',explode(',',Session::get('username')));
                }

                if( (Session::get('date_from')) && (Session::get('date_to') == '')) { 
                    $query->where('tbl_audits.created_at','>=',Session::get('date_from')." 00:00:00");
                }

                if((Session::get('date_to')) && (Session::get('date_from') == ''))  { 
                    $query->where('tbl_audits.created_at','<=',Session::get('date_to')." 23:59:59");
                }

                if(Session::get('date_from') && Session::get('date_to')){ 
                    $query->whereBetween('tbl_audits.created_at', array(Session::get('date_from')." 00:00:00", Session::get('date_to')." 23:59:59"));
                }

                if(Session::get('items')){
                    $query->whereIn('tbl_audits.audit_owner', explode(',',Session::get('items')));
                }

                if(Session::get('docno')){
                    $query->where('tbl_audits.document_no','LIKE', '%'.Session::get('docno').'%');
                }

                if(Session::get('docname')){
                    $query->where('tbl_audits.document_name','LIKE' ,'%'.Session::get('docname').'%');
                }

                if(Session::get('stacks')){
                    $query->whereIn('tbl_audits.stack_id', explode(',',Session::get('stacks')));
                }

                if(Session::get('dept')){
                    $query->whereIn('tbl_audits.department_id', explode(',', Session::get('dept')));
                }

                if(Session::get('dctype')){
                    $query->whereIn('tbl_audits.document_type_id', explode(',', Session::get('dctype')));
                }

                if(Session::get('actions')){
                    $query->whereIn('tbl_audits.audit_action_type', explode(',', Session::get('actions')));
                }

            });
            $noOfRecords = $query->count();
         }

        
            $category = $searchedValues->category;
            if(!empty($category)){
                $keyid = "";
                $categoryCnt = count($searchedValues->category);
                for($i=0; $i < $categoryCnt; $i++){
                    $kid = $category[$i];
                    if($i==0){
                        $keyid = $kid;
                    }else if($i==count($category)-1){
                        $keyid = $keyid.','.$kid;
                    }else{
                        $keyid = $keyid.','.$kid;
                    }
                }
                $category[] = $keyid;
                $query->whereIn('tbl_audits.audit_owner', $category)->get();
                $noOfRecords = $query->count();
            }


            $username = $searchedValues->username;
            if(!empty($username)){
                $keyid = "";
                $usrnameCnt = count($searchedValues->username);
                for($i=0; $i < $usrnameCnt; $i++){
                    $kid = $username[$i];
                    if($i==0){
                        $keyid = $kid;
                    }else if($i==count($username)-1){
                        $keyid = $keyid.','.$kid;
                    }else{
                        $keyid = $keyid.','.$kid;
                    }
                }
                $username[] = $keyid;
                $query->whereIn('tbl_audits.audit_user_name', $username)->get();
                $noOfRecords = $query->count();
            }

            $action = $searchedValues->actions;
            if(!empty($action)){
                $keyid = "";
                $actionCnt = count($searchedValues->actions);
                for($i=0; $i < $actionCnt; $i++){
                    $kid = $action[$i];
                    if($i==0){
                        $keyid = $kid;
                    }else if($i==count($action)-1){
                        $keyid = $keyid.','.$kid;
                    }else{
                        $keyid = $keyid.','.$kid;
                    }
                }
                $action[] = $keyid;
                $query->whereIn('tbl_audits.audit_action_type', $action)->get();
                $noOfRecords = $query->count();
            }
            
            $stacks = $searchedValues->stacks; 
            if(!empty($stacks)){
                $keyid = "";
                $stacksCnt = count($searchedValues->stacks);
                for($i=0; $i < $stacksCnt; $i++){
                    $kid = $stacks[$i];
                    if($i==0){
                        $keyid = $kid;
                    }else if($i==count($stacks)-1){
                        $keyid = $keyid.','.$kid;
                    }else{
                        $keyid = $keyid.','.$kid;
                    }
                }
                
                $stacksName = DB::table('tbl_stacks')->select(DB::RAW('GROUP_CONCAT(stack_name) AS stack_names'))->whereIn('stack_id',explode(',',$keyid))->get();
                $stacks[] = $keyid;
                $query->whereIn('tbl_audits.stack_id', $searchedValues->stacks)->get();
                $noOfRecords = $query->count();
            }
            
            $dept = $searchedValues->dept; 
            if(!empty($dept)){
                $keyid = "";
                $deptCnt = count($searchedValues->dept);
                for($i=0; $i < $deptCnt; $i++){
                    $kid = $dept[$i];
                    if($i==0){
                        $keyid = $kid;
                    }else if($i==count($dept)-1){
                        $keyid = $keyid.','.$kid;
                    }else{
                        $keyid = $keyid.','.$kid;
                    }
                }
                $departmntNames = DB::table('tbl_departments')->select(DB::RAW('GROUP_CONCAT(department_name) AS department_names'))->whereIn('department_id',explode(',',$keyid))->get(); 
                $dept[] = $keyid;
                $query->whereIn('tbl_audits.department_id', $searchedValues->dept)->get();
                $noOfRecords = $query->count();
            }
            
            
            $dctype = $searchedValues->dctype; 
            if(!empty($dctype)){ 
                $keyid = "";
                $dctypeCnt = count($searchedValues->dctype);
                for($i=0; $i < $dctypeCnt; $i++){
                    $kid = $dctype[$i];
                    if($i==0){
                        $keyid = $kid;
                    }else if($i==count($dctype)-1){
                        $keyid = $keyid.','.$kid;
                    }else{
                        $keyid = $keyid.','.$kid;
                    }
                }
               
                $dctypeNames = DB::table('tbl_document_types')->select(DB::RAW('GROUP_CONCAT(document_type_id) as document_type_ids'),DB::RAW('GROUP_CONCAT(document_type_name) AS document_type_names'))->whereIn('document_type_id',$dctype)->get();
                $dctype[] = $keyid;
                $query->whereIn('tbl_audits.document_type_id', $searchedValues->dctype)->get();
                $noOfRecords = $query->count();
            }
            
            $docno = $searchedValues->docno; 
            if(!empty($docno)){
                $query->where('tbl_audits.document_no','LIKE', '%'.$docno.'%')->get();
                $noOfRecords = $query->count();
            }
            
            $docname = $searchedValues->docname; 
            if(!empty($docname)){
                $query->where('tbl_audits.document_name','LIKE' ,'%'.$docname.'%')->get();
                $noOfRecords = $query->count();
            }


            $createddt_frm = $searchedValues->createddate_from; 
            $createddt_to  = $searchedValues->createddate_to; 
            $fromDate = $createddt_frm." 00:00:00";
            $ToDate = $createddt_to." 23:59:59";

            if( ($createddt_frm) && ($createddt_to == '')) { 
                $query->where('tbl_audits.created_at','>=',$fromDate)->get();
                $noOfRecords = $query->count();
            }

            if(($createddt_to) && ($createddt_frm == ''))  { 
                $query->where('tbl_audits.created_at','<=',$ToDate)->get();
                $noOfRecords = $query->count();
            }

            if($createddt_frm && $createddt_to){ 
                $query->whereBetween('tbl_audits.created_at', array($fromDate, $ToDate))->get();
                $noOfRecords = $query->count();
            }

            // Ajax order by works
            $query->orderBy($columns[$requestData['order'][0]['column']], $requestData['order'][0]['dir']);
            $query->offset($requestData['start'])->limit($requestData['length']);
            
            // search result
            $data['data'] = $query->get();
            $queries = DB::getQueryLog();
            $last_query = end($queries);

            // For ajax result
            $data['draw'] = intval( $requestData['draw'] );
            $data['recordsTotal'] = $noOfRecords;
            $data['recordsFiltered'] = $noOfRecords;
            $data['request'] = $noOfRecords;

            $y = array();
           foreach( $data['data'] as $val):
            $x = (array) $val;
            $y[] = array_values($x);  
            endforeach;
            $data['data'] = $y;
            echo json_encode($data);  // send data as json format
    } 

    // Save password reset link
    public function savePasswordReset($emailId,$var2){
        // Get user name
        $userName = Users::where('email',$emailId)->get();
        $userName = $userName[0]->username;

        if(@$var2):
            $actionMsg = Lang::get('language.password_reset2_lang');
            $action    = 'Reset password';
            else:
                $actionMsg = Lang::get('language.password_reset_link_lang');
                $action    = 'Mail send';
                endif;
        
        $actionDes = $this->docObj->stringReplace('Reset password',$userName,$userName,$actionMsg);
        $this->log($userName,'Reset password',$action,$actionDes);
    }

    public function log($userName, $ownerName, $action,$actionDes)
    {
        $log= new AuditsModel;
        $log->audit_user_name= $userName;
        $log->audit_owner= $ownerName;
        $log->audit_action_type= $action;
        $log->audit_action_desc= $actionDes;
        if ($log->save()) {
            return $log->audit_id;
        } else {
            return 0;
        }
    }   

    public function loginLog($userName, $ownerName, $action,$actionDes,$ipAddr,$geoLoc)
    {
        $log= new AuditsModel;
        $log->audit_user_name= $userName;
        $log->audit_owner= $ownerName;
        $log->audit_action_type= $action;
        $log->audit_action_desc= $actionDes;
        $log->audit_user_ip= $ipAddr;
        $log->audit_geo_location= $geoLoc;
        if ($log->save()) {
            return $log->audit_id;
        } else {
            return 0;
        }
    }   
	
	public function stacklog($userName, $stackId, $ownerName, $action,$actionDes)
    {
        $log= new AuditsModel;
        $log->audit_user_name= $userName;
        $log->stack_id= $stackId;
		$log->audit_owner= $ownerName;
        $log->audit_action_type= $action;
        $log->audit_action_desc= $actionDes;
        if ($log->save()) {
            return $log->audit_id;
        } else {
            return 0;
        }
    } 
	
	public function dctypelog($userName, $dctypeId, $ownerName, $action,$actionDes)
    {
        $log= new AuditsModel;
        $log->audit_user_name= $userName;
		$log->document_type_id= $dctypeId;
        $log->audit_owner= $ownerName;
        $log->audit_action_type= $action;
        $log->audit_action_desc= $actionDes;
        if ($log->save()) {
            return $log->audit_id;
        } else {
            return 0;
        }
    } 
	
	public function dprtmntlog($userName, $deptmntId, $ownerName, $action,$actionDes)
    {
        $log= new AuditsModel;
        $log->audit_user_name= $userName;
		$log->department_id= $deptmntId;
        $log->audit_owner= $ownerName;
        $log->audit_action_type= $action;
        $log->audit_action_desc= $actionDes;
        if ($log->save()) {
            return $log->audit_id;
        } else {
            return 0;
        }
    }   

    //backup and restores section 
    public function backuplog($userName, $ownerName, $action,$actionDes)
    {
        $log= new AuditsModel;
        $log->audit_user_name= $userName;
        $log->audit_owner= $ownerName;
        $log->audit_action_type= $action;
        $log->audit_action_desc= $actionDes;
        if ($log->save()) {
            return $log->audit_id;
        } else {
            return 0;
        }
    }   
	
    public function dcmntslog($userName, $docid, $ownerName, $action, $actionDes , $docno , $docname, $docpath)
    {
        $log= new AuditsModel;
        $log->audit_user_name= $userName;
        $log->audit_owner= $ownerName;
        $log->document_id= $docid;
		$log->document_no= $docno;
		$log->document_name= $docname;
		$log->document_path= $docpath;
        $log->audit_action_type= $action;
        $log->audit_action_desc= $actionDes;
        if ($log->save()) {
            return $log->audit_id;
        } else {
            return 0;
        }
    } 
    public function formslog($userName, $docid, $ownerName, $action, $actionDes, $docname)
    {
        $log= new AuditsModel;
        $log->audit_user_name= $userName;
        $log->audit_owner= $ownerName;
        $log->document_id= $docid;
        $log->document_name= $docname;
        $log->audit_action_type= $action;
        $log->audit_action_desc= $actionDes;
        if ($log->save()) {
            return $log->audit_id;
        } else {
            return 0;
        }
    }
    public function workflowslog($userName, $docid, $ownerName, $action, $actionDes, $docname)
    {
        $log= new AuditsModel;
        $log->audit_user_name= $userName;
        $log->audit_owner= $ownerName;
        $log->document_id= $docid;
        $log->document_name= $docname;
        $log->audit_action_type= $action;
        $log->audit_action_desc= $actionDes;
        if ($log->save()) {
            return $log->audit_id;
        } else {
            return 0;
        }
    }

    public function appslog($userName,$dctypeId,$action,$actionDes)
    {
        $log= new AuditsModel;
        $log->audit_user_name= $userName;
        $log->document_type_id= $dctypeId;
        $log->audit_owner= 'App';
        $log->audit_action_type= $action;
        $log->audit_action_desc= $actionDes;
        if ($log->save()) {
            return $log->audit_id;
        } else {
            return 0;
        }
    }


    public function appRecordslog($userName, $docid, $action, $actionDes)
    {
        $log= new AuditsModel;
        $log->audit_user_name= $userName;
        $log->audit_owner= 'App Record';
        $log->document_id= $docid;
        //$log->document_no= $docno;
        //$log->document_name= $docname;
        //$log->document_path= $docpath;
        $log->audit_action_type= $action;
        $log->audit_action_desc= $actionDes;
        if ($log->save()) {
            return $log->audit_id;
        } else {
            return 0;
        }
    }  
    public function viewOnAudit()
    {
        if (Auth::user()) {
            $id=Input::get('doc_id');
            if($id)
            {
            $name = Input::get('doc_name');
            $user = Auth::user()->username;
            $action_desc = "The document '$name' viewed by $user";
            DB::table('tbl_audits')->insert(['document_id'=>$id,'audit_owner'=>'Document','audit_user_name'=>Auth::user()->username,'audit_action_type'=>'View','audit_action_desc'=>$action_desc,'created_at'=>date('Y-m-d h:i:s')]);
            @$encrypt_status = DB::table('tbl_documents')->select('document_encrypt_status','document_encrypted_by','document_encrypted_on','document_file_name')->where('document_id',$id)->first();

                if(@$encrypt_status->document_encrypt_status == 1) 
                {
                    echo "encrypted";
                    exit();
                } 
                if(@$encrypt_status->document_encrypt_status == null) 
                {
                    echo "not encrypted";
                    exit();
                } 
                if(!file_exists(config('app.base_path').$encrypt_status->document_file_name))
                {
                    echo "notexist";
                    exit();
                }
            }
            else
            {
                echo "empty";
                exit();
            }
        }
        else{
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    //from dashboard(home.blade.php->searchnotaccess() function ), documents list which are not accessed given number of days
    public function searchNotAccess(){
        if(Auth::user()){
            $value=Input::get('val');
            $data['dglist']=DB::table('tbl_audits')
            ->join('tbl_documents','tbl_audits.document_id','=','tbl_documents.document_id')
            ->select('tbl_audits.audit_action_desc',DB::raw('max(tbl_audits.created_at) as created_at'),'tbl_documents.document_no','tbl_documents.document_name','tbl_documents.document_file_name','tbl_documents.document_id')
            ->where('tbl_audits.audit_action_type','=','Open')
            ->where('tbl_audits.audit_user_name',Auth::user()->username)
            ->groupBy('tbl_audits.document_no')
            ->limit(5)
            ->get();
            $now = date('Y-m-d h:i:s');
            $now_date = strtotime($now);
            $newArray=array();
            foreach ($data['dglist'] as $val) {
                $your_date = strtotime($val->created_at);
                $datediff = $now_date - $your_date;
                $val->days= floor($datediff / (60 * 60 * 24));
                if($value <= $val->days){
                    $newArray[] = $val;
                }
            }
            
            if(count($newArray)>0){
                foreach ($newArray as $key => $val){
                    $ext = pathinfo($val->document_file_name, PATHINFO_EXTENSION); 
                    $date = date('Y-m-d',strtotime($val->created_at));
                    if($ext=='pdf'){
                        $src = "pdf.png";       
                    }elseif ($ext=='png'||$ext=='jpg'||$ext=='jpeg'||$ext=='tiff'||$ext=='TIF'||$ext=='TIFF') {
                        $src = "image.svg";
                    }elseif ($ext=='docx'||$ext=='doc') {
                        $src = 'word.png';
                    }elseif ($ext=='txt') {
                        $src = 'text.png';
                    }elseif ($ext=='zip'||$ext=='rar') {
                        $src = 'zip.png';
                    }elseif ($ext=='xls'||$ext=='xlsx') {
                        $src = 'excel.png';
                    }elseif ($ext=='mp3' || $ext=='wav' || $ext=='ogg') {
                        $src = "music.png";
                    }elseif ($ext=='webm' || $ext=='ogv' || $ext=='flv' || $ext=='mp4'){
                        $src="webm.png";
                    }else{
                        $src = 'file.svg';
                    }             
                    echo '<ul class="products-list product-list-in-box"><li class="item"><div class="product-img"><img src="public/images/icons/large/zip.png" alt="zip Image"></div><div class="product-info"><a href="listview?view=list&docid='.$val->document_id.'" class="product-title">'.$val->document_no.'</a><span class="product-description">'.$val->document_name.'</span><span class="product-description">Last accessed on : '.$date.'</span></div></li></ul>';
                }
            }else{
                echo '<div style="color:red;">No documents found</div>';
            }                           
        }else{
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function clearAudit()
    {
        if(Auth::user())
        {
            //$optionArray = Input::get('chk_group');
            $clear_date_from = Input::get('cleardate_from');
            $clear_date_to = Input::get('cleardate_to');
            $ClearFromDate = $clear_date_from." 00:00:00";
            $ClearToDate = $clear_date_to." 23:59:59";
            switch(Auth::user()->user_role)
                {
                    //super admin
                    case Session::get("user_role_super_admin"):
                        //$clear_audit_data = DB::table('tbl_audits')->whereIn('audit_action_type',$optionArray)->whereBetween('created_at', array(@$ClearFromDate, @$ClearToDate))->get();
                        $clear_audit_data = DB::table('tbl_audits')->whereBetween('created_at', array(@$ClearFromDate, @$ClearToDate))->get();
                    break;    
                }
            $actionMsg=Lang::get('language.audit_delete_success');
            $actionDes = $this->docObj->stringReplace($clear_date_from,$clear_date_to,Auth::user()->username,$actionMsg);
            $result = (new AuditsController)->log(Auth::user()->username,'Audits Success', 'Delete',$actionDes);
            return redirect()->back()->with('data', 'Audits cleared successfully');
            exit();
        }
        else
        {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }  
    public function insertPasswordFailure(){
        if(Auth::user())
        {
            $from = Input::get('date_from_clear');
            $to = Input::get('date_to_clear');
            $actionMsg=Lang::get('language.audit_delete_failure_msg');
            $actionDes = $this->docObj->stringReplace(Auth::user()->username,$from,$to,$actionMsg);
            echo $actionDes;
            exit();
            $result = (new AuditsController)->log(Auth::user()->username,'Audits Failure', 'Delete',$actionDes);
        }
        else
        {
            return redirect('')->withErrors("Please login")->withInput();
        }
    } 
    
    public function AuditDelete_Notifications()
    {
      if(Auth::user()){

        //Server validation
        if(Input::get('date_to_clear') <= $this->no_of_days){
                // Success
                $from = Input::get('date_from_clear');
                $to   = Input::get('date_to_clear').' 23:59:59';
               // Checking data already exists by fromand to date
               $dataAlreadyExists = DB::table('tbl_audits_delete_request')->where('delete_from_date',$from)->where('delete_to_date',$to)->exists();
               if(@$dataAlreadyExists){
                    echo "false";exit;// Ajax response
               }else{
                    // Success
                    //check already superadmin requested for delete audit approvel
                    $already_request = DB::table('tbl_audits_delete_request')->where('audits_delete_request_username',Auth::user()->username)->where('audits_delete_request_status',0)->exists();

                    if($already_request){ 
                            //Update
                            $dataToUpdate['delete_to_date'] = $to;
                            DB::table('tbl_audits_delete_request')->where('audits_delete_request_username',Auth::user()->username)->update($dataToUpdate);
                       }else{
                            // Insert
                            $super_admins = USers::select('username')->where('user_role',1)->where('username','!=',Auth::user()->username)->get();    
                           foreach ($super_admins as $value) {
                               //insert
                               DB::table('tbl_audits_delete_request')->insert(['audits_delete_request_username'=>Auth::user()->username,'audits_delete_request_approved_by'=>$value->username,'audits_delete_request_status'=>0,'delete_from_date'=>$from,'delete_to_date'=>$to]);
                           }
                       }
                    // Save in audits
                    $actionMsg = Lang::get('language.purge_audits_request');
                    // To Avoid time
                    $dateTo = $this->docObj->removeTimeFrmDate($to); 
                    $actionDes = $this->docObj->stringReplace($this->user_name,$dateTo,NULL,$actionMsg);                
                    (new AuditsController)->log(Auth::user()->username,$this->purgeAudits,$this->delete,$actionDes);
                    echo "Success";exit;// Ajax response
               }
           }else{
               echo "failed";exit;// Wrong date
           }

            

       }else{
           return redirect('')->withErrors("Please login")->withInput();
       }
    }

    // Delete selected audits records
    public function deleteAudits(){
        if (Auth::user()) { 
            // Get logged user password
            $getPsw = Users::select('password')->where('id',Auth::user()->id)->get();
            $hashedPassword = $getPsw[0]->password; 
            $psw = Input::get('input_val');

            if(Hash::check($psw,$hashedPassword)){ 
                // True password
                 $userName         = @Input::get('userName');
                 $user_full_name   = @Input::get('user_full_name');
                 $delete_from_date = Input::get('delete_from_date');
                 $delete_to_date   = Input::get('delete_to_date');

                // More
                if(Input::get('ownUser') == 'No'):
                    $audits_delete_requestDetails = DB::table('tbl_audits_delete_request')->select('audits_delete_request_status')->where('audits_delete_request_username',$userName)->where('audits_delete_request_approved_by',Auth::user()->username)->get();

                    // Checking already approved or not
                    if(@$audits_delete_requestDetails[0]->audits_delete_request_status == '0'):
                        // Approve delete action for audits
                        // Update audits with status 1
                        $data['audits_delete_request_status'] = '1';
                        $approvedBy['audits_delete_request_approved_by_who'] = '1';

                        DB::table('tbl_audits_delete_request')->where('audits_delete_request_username',$userName)->update($data);
                        // Update tbale that approved by who
                        DB::table('tbl_audits_delete_request')->where('audits_delete_request_username',$userName)->where('audits_delete_request_approved_by',Auth::user()->username)->update($approvedBy);
                        
                        // Delete audits [Main Process] and save in audits
                        $this->deleteAuditsAndSaveAudits($delete_from_date,$delete_to_date,$user_full_name,'No');
                        // Update session
                        $this->docObj->audit_clear_notification_check();

                        print_r("1");exit;// Aproved
                    else:
                        print_r("2");exit; // Already approved
                    endif;
                else:
                    // If only one admin
                    // Delete audits [Main Process] and save in audits
                    // Server side validation
                    if(Input::get('delete_to_date') <= $this->no_of_days){
                        $this->deleteAuditsAndSaveAudits($delete_from_date,$delete_to_date,$this->user_name,'Yes');
                        print_r("3");exit;// Aproved
                    }else{
                        echo "failed";exit;
                    }
                endif;

            }else{
                // Wrong password
                // Save in audits
                $actionMsg  = Lang::get('language.audits_signin_failure');   
                $actionDes  = $this->docObj->stringReplace($this->user_name,NULL,NULL,$actionMsg);               
                (new AuditsController)->log(Auth::user()->username,$this->purgeAudits,Lang::get('language.signin_failure'),$actionDes);
                print_r("0");exit; //Sign in failure
            }

        } else {
            return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        } 
    }

    // Delete audits request table
    public function deleteAuditsRequest(){
        if (Auth::user()) {
            $userName  = Input::get('request_username');
            DB::table('tbl_audits_delete_request')->where('audits_delete_request_username',$userName)->delete();
        }else{
            return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        } 
    }

    //<!--Common functions-->//
    // For reduce code repetition
    public function deleteAuditsAndSaveAudits($delete_from_date,$delete_to_date,$userName,$ownUser){
        // Delete audits [Main Process]
        DB::table('tbl_audits')->whereBetween('created_at', [$delete_from_date, $delete_to_date])->delete();
        // Save in audits
        $actionName = Lang::get('language.delete'); 
        if($ownUser == 'No'):
            // More than one admin
            $actionMsg  = Lang::get('language.audits_deleted');
            $var3       =  $this->user_name;
        else:
            // Own user
            $actionMsg  = Lang::get('language.audits_deleted2'); 
            $var3       =  NULL;
        endif;
        // To Avoid time from date
        $dateTo = $this->docObj->removeTimeFrmDate($delete_to_date); 
        $actionDes  = $this->docObj->stringReplace($dateTo,$userName,$var3,$actionMsg);        
        (new AuditsController)->log(Auth::user()->username,$this->purgeAudits,$actionName,$actionDes);
    }
    public function dbManager()
    {
        if(isset($_GET['action']))
        {
            //start saving or loading
            switch( $_GET['action'])
            {
                case "save":
                if(isset($_POST['state']) && isset($_POST['view']) && isset($_POST['type'])) 
                {
                    $this->saveState($_POST["state"],$_POST['view'],$_POST['type']);
                }
                break;
                case "load": 
                
                $this->loadState($_POST['view'],$_POST['type']);
                
                break;
            }
        }
    }
    public function saveState($state, $view, $type)
    {
            //if the name already exist then update else insert a new row in db
            $exists = DB::table("datatables_states")->where('user_id',Auth::user()->id)->where('view',$view)->where('type',$type)->first();
            if(!$exists)
            {
            $query = DB::table('datatables_states')->insert(['user_id'=>Auth::user()->id,'view'=>$view,'type'=>$type,'state'=>json_encode($state)]);
            }
            else
            {
            $query = DB::table('datatables_states')->where('user_id',Auth::user()->id)->where('view',$view)->where('type',$type)->update(['state'=>json_encode($state)]);
            }
            echo "saved";
    }
    public function loadState($view,$type)
    {
        $stmt = DB::table('datatables_states')->select('state')->where('user_id',Auth::user()->id)->where('view',$view)->where('type',$type)->first();
        echo @$stmt->state;
        // $values = json_decode($stmt->state);
        // print_r($values->ColReorder);
        if(!$stmt)
        {
        $stmt = DB::table('datatables_states')->select('state')->where('id',0)->first();
        echo @$stmt->state;
        }
    }
}/*<--END-->*/