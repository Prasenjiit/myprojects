<?php
namespace App\Http\Controllers;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Users as Users;
use App\DepartmentsModel as DepartmentsModel;
use App\DocumentTypesModel as DocumentTypesModel;
use App\DocumentsModel as DocumentsModel;
use App\StacksModel as StacksModel;
use App\TagWordsModel as TagWordsModel;
use App\AuditsModel as AuditsModel;
use App\HomeModel as HomeModel;
use App\FormModel as FormModel;
use App\TempDocumentsModel as TempDocumentsModel;
use App\DocumentsCheckoutModel as DocumentsCheckoutModel;
use Session;
use DB;
use Auth;
use Input;
use App\Mylibs\Common;
use Lang;
use Imagick;
use Artisan;
use Illuminate\Support\Facades\Crypt;

class HomeController extends Controller
{
    public function __construct()
    {
        Artisan::call('config:clear');

        //user_roles are put in session for avoid hard coding
        Session::put('user_role_super_admin', '1');
        Session::put('user_role_group_admin', '2');
        Session::put('user_role_regular_user', '3');
        Session::put('user_role_private_user', '4');

        // Setting password complexity put in session for avoid hard coding
        Session::put('settings_alphabets', '1');
        Session::put('settings_numerics', '1');
        Session::put('settings_special_characters', '1');
        Session::put('settings_capital_and_small', '1');

        Session::put('activity_task_notifications', '1');
        Session::put('form_notifications', '1');
        Session::put('document_notifications', '1');
        Session::put('signin_notifications', '1');
        Session::put('override_email_notifications_settings', '1');

        Session::put('apply_to_all_users', '1');
        Session::put('apply_to_new_users', '0');

        //User status are put in session for avoid hard coding
        Session::put('user_activity_task_notifications', '1');
        Session::put('user_form_notifications', '1');
        Session::put('user_document_notifications', '1');
        Session::put('user_signin_notifications', '1');

        //User status are put in session for avoid hard coding
        Session::put('user_status_Active', '1');
        Session::put('user_status_Inactive', '0');

        //User login_status are put in session for avoid hard coding
        Session::put('login_status_Login', '1');
        Session::put('login_status_Logout', '0');

        //User user_lock_status are put in session for avoid hard coding
        Session::put('user_lock_status_Locked', '1');
        Session::put('user_lock_status_Unlocked', '0');
        
        // Put no of dates to purge audits records
        Session::put('no_of_days','180'); 
		
		$premisevalue = (env('ON_PREMISE'))?env('ON_PREMISE'):0;
        Session::put('onpremise',$premisevalue);
		
        //menu id
        Session::put('menuid', '5');
        $this->middleware(['auth', 'user.status']);

        // Set common variable
        $this->docObj = new Common(); // class defined in app/mylibs

        // Get department id of logged in user
        Session::put('auth_user_dep_ids',explode(',',@Auth::user()->department_id));
    }

    public function index()
    {        
        //user_roles are put in session for avoid hard coding
        Session::put('user_role_super_admin', '1');
        Session::put('user_role_group_admin', '2');
        Session::put('user_role_regular_user', '3');
        Session::put('user_role_private_user', '4');

        // Setting password complexity put in session for avoid hard coding
        Session::put('settings_alphabets', '1');
        Session::put('settings_numerics', '1');
        Session::put('settings_special_characters', '1');
        Session::put('settings_capital_and_small', '1');

        Session::put('activity_task_notifications', '1');
        Session::put('form_notifications', '1');
        Session::put('document_notifications', '1');
        Session::put('signin_notifications', '1');
        Session::put('override_email_notifications_settings', '1');

        Session::put('apply_to_all_users', '1');
        Session::put('apply_to_new_users', '0');

        //User status are put in session for avoid hard coding
        Session::put('user_activity_task_notifications', '1');
        Session::put('user_form_notifications', '1');
        Session::put('user_document_notifications', '1');
        Session::put('user_signin_notifications', '1');

        //User status are put in session for avoid hard coding
        Session::put('user_status_Active', '1');
        Session::put('user_status_Inactive', '0');

        //User login_status are put in session for avoid hard coding
        Session::put('login_status_Login', '1');
        Session::put('login_status_Logout', '0');

        //User user_lock_status are put in session for avoid hard coding
        Session::put('user_lock_status_Locked', '1');
        Session::put('user_lock_status_Unlocked', '0');
        
        // Put no of dates to purge audits records
        Session::put('no_of_days','180'); 
        
        $premisevalue = (env('ON_PREMISE'))?env('ON_PREMISE'):0;
        Session::put('onpremise',$premisevalue);
        
        //menu id
        Session::put('menuid', '5');
        $this->middleware(['auth', 'user.status']);
        // Get department id of logged in user
        Session::put('auth_user_dep_ids',explode(',',@Auth::user()->department_id));

        
        Session::forget('is_limit_exceed');
        Session::forget('captcha_validation_error');
        
        // <!--Get notifications for header-->
        // Get security details from settings 
        $data['tbl_settings'] = DB::table('tbl_settings')->first();
        Session::put('settings_file_extensions',$data['tbl_settings']->settings_file_extensions);
        Session::put('settings_document_expiry',$data['tbl_settings']->settings_document_expiry);
        Session::put('settings_company_name',$data['tbl_settings']->settings_company_name);// Company name
        Session::put('settings_rows_per_page',$data['tbl_settings']->settings_rows_per_page);// settings_rows_per_page
        if($data['tbl_settings']->settings_department_name){
            Session::put('settings_department_name',$data['tbl_settings']->settings_department_name);
            Session::put('settings_department_name_all',"All ".$data['tbl_settings']->settings_department_name);
            Session::put('settings_dept_name',$data['tbl_settings']->settings_department_name." Name");
            Session::put('settings_department_name_diply_all',"Display of All ".$data['tbl_settings']->settings_department_name);
        }

        //$this->docObj->check_module_status();

        $no_of_users = 0;
        $view_only_users =0;
        //account details
        $installation_date = dec_enc('decrypt',$data['tbl_settings']->settings_installation_date);
        $expiry_date = dec_enc('decrypt',$data['tbl_settings']->settings_expiry_date);
        $tmp_no_of_users = dec_enc('decrypt',$data['tbl_settings']->settings_no_of_users);
        $tmp_view_only_users = dec_enc('decrypt',$data['tbl_settings']->settings_view_only_users);
        $license_key = dec_enc('decrypt',$data['tbl_settings']->settings_license_key);    

        if($tmp_no_of_users==-1){
            $no_of_users = "Unlimited";
        }
        if($tmp_view_only_users==-1){
            $view_only_users = "Unlimited";
        }

        Session::put('settings_installation_date',$installation_date);
        Session::put('settings_expiry_date',$expiry_date);
        Session::put('settings_no_of_users',$no_of_users);
        Session::put('settings_view_only_users',$view_only_users);
        Session::put('settings_license_key',$license_key);
        //other users under the logged user dept
        $auth_dep_users_array = array();
        $auth_dep_users = DB::table('tbl_users_departments')
        ->select('users_id')
        ->whereIn('department_id',Session::get('auth_user_dep_ids'))->get();
        //users under the department.
        foreach ($auth_dep_users as $value) 
        {
        $auth_dep_users_array[] = $value->users_id;
        }
        $auth_dep_users_array_unique = array_unique($auth_dep_users_array);
        
        Session::put('auth_users_dep_under',$auth_dep_users_array_unique);

        $data['docType'] = DocumentTypesModel::where('is_app',0)->orderBy('document_type_order', 'ASC')->get();
        $doc_types = array();    
        foreach ($data['docType'] as $key => $value) {
            $doc_types[] = $value->document_type_id;
        }

        // <!--Dashboard count Start-->
        $data['tagsCnt'] = TagWordsModel::count();
        $data['stacksCnt'] = StacksModel::count();
        $data['doctypesCnt'] = DocumentTypesModel::where('is_app',0)->count();

        //////////////recent documents//////////////
        $data['docs'] = DB::table('tbl_audits')->
        select('tbl_audits.audit_action_desc','tbl_audits.created_at','tbl_documents.document_no','tbl_documents.document_name','tbl_documents.document_file_name','tbl_documents.document_id')
        ->join('tbl_documents','tbl_audits.document_id','=','tbl_documents.document_id')
        ->where('tbl_audits.audit_action_type','=','Open')
        ->where('tbl_audits.audit_user_name',Auth::user()->username)
        ->orderBy('tbl_audits.created_at', 'desc')
        ->groupBy('tbl_audits.document_no')
        ->limit(5)
        ->distinct()
        ->get(['tbl_audits.document_no']);
        //////////////recent documents//////////////

        $count         = count(Session::get('auth_user_dep_ids'));
        $data['departmntCnt']=$count;
        if($count == 1):
            $x=0;
        else:
            $x=1;
        endif;

        // Submitted forms
        $query = DB::table('tbl_form_responses')->groupBy('tbl_form_responses.form_response_unique_id');

        //Active workflows
        /*$query1 = DB::table('tbl_document_workflows')
        ->join('tbl_workflows as wf','tbl_document_workflows.workflow_stage_id','=','wf.workflow_stage_id')
        ->groupBy('wf.workflow_id');
        */
        $query1 = DB::table('tbl_wf');
        //count of diff users
        switch(Auth::user()->user_role)
        {
            //super admin
            case Session::get("user_role_super_admin"):
                $data['usrCnt']         = Users::count();
                $data['auditCnt']       = AuditsModel::count();
                $data['departmntCnt']   = DepartmentsModel::count();

                $data['docsCnt']        = DocumentsModel::whereIn('document_type_id',$doc_types)->count();//published documents
                $data['un_docsCnt']     = TempDocumentsModel::whereIn('document_type_id',$doc_types)->count();//unpublished docs
                $data['checkCnt']       = DocumentsCheckoutModel::whereIn('document_type_id',$doc_types)->count();//checkout docs
                $data['archive_Cnt']    = DocumentsModel::whereIn('document_type_id',$doc_types)->where('document_file_name', 'like', '%' . '.zip' . '%')->orWhere('document_file_name', 'like', '%' . '.rar' . '%')->count();
                $active_wfs             = $query1->get();
                $data['workflowCnt']    = count($active_wfs);
                $my_forms               = $query->where('tbl_form_responses.user_id',Auth::user()->id)->get();
                $data['formCnt']        = count($my_forms);
                Session::put("un_docsCnt",$data['un_docsCnt']);
            break;    
            //group admin
            case Session::get("user_role_group_admin"):
                $data['usrCnt']         = count(Session::get('auth_users_dep_under'));
                $data['docsCnt']        = DocumentsModel::whereIn('document_type_id',$doc_types)->whereIn('department_id',Session::get('auth_user_dep_ids'))->count();
                $data['un_docsCnt']     = TempDocumentsModel::whereIn('document_type_id',$doc_types)->whereIn('department_id',Session::get('auth_user_dep_ids'))->count();//unpublished docs
                $data['archive_Cnt']    = DocumentsModel::whereIn('document_type_id',$doc_types)->whereIn('department_id',Session::get('auth_user_dep_ids'))->where('document_file_name', 'like', '%' . '.zip' . '%')->orWhere('document_file_name', 'like', '%' . '.rar' . '%')->count();
                $data['checkCnt']       = DocumentsCheckoutModel::whereIn('document_type_id',$doc_types)->whereIn('department_id',Session::get('auth_user_dep_ids'))->count();;//checkout docs
                $data['auditCnt']       = AuditsModel::where('audit_user_name',Auth::user()->username)->count();
                $active_wfs             = $query1->get();
                $data['workflowCnt']    = count($active_wfs);
                $my_forms = $query->where('tbl_form_responses.user_id',Auth::user()->id)->get();
                $data['formCnt']        = count($my_forms);
                Session::put("un_docsCnt",$data['un_docsCnt']);
            break;
            //Regular user  
            case Session::get("user_role_regular_user"):
                //count of users under the dept like group admin
                $data['usrCnt']         = count(Session::get('auth_users_dep_under'));
                $data['docsCnt']        = DocumentsModel::whereIn('document_type_id',$doc_types)->whereIn('department_id',Session::get('auth_user_dep_ids'))->count();
                $data['archive_Cnt']    = DocumentsModel::whereIn('document_type_id',$doc_types)->whereIn('department_id',Session::get('auth_user_dep_ids'))->where('document_file_name', 'like', '%' . '.zip' . '%')->orWhere('document_file_name', 'like', '%' . '.rar' . '%')->count();
                $data['un_docsCnt']     = TempDocumentsModel::whereIn('document_type_id',$doc_types)->whereIn('department_id',Session::get('auth_user_dep_ids'))->count();//unpublished docs
                $data['checkCnt']       = DocumentsCheckoutModel::whereIn('document_type_id',$doc_types)->whereIn('department_id',Session::get('auth_user_dep_ids'))->count();//checkout docs
                $data['auditCnt']       = AuditsModel::where('audit_user_name',Auth::user()->username)->count();
                $active_wfs             = $query1->get();
                $data['workflowCnt']    = count($active_wfs);
                $my_forms = $query->where('tbl_form_responses.user_id',Auth::user()->id)->get();
                $data['formCnt']        = count($my_forms);
                Session::put("un_docsCnt",$data['un_docsCnt']);
            break;
            //private user
            case Session::get("user_role_private_user"):
                $data['usrCnt']       = '1'; //single user
                $data['checkCnt']       = DocumentsCheckoutModel::whereIn('document_type_id',$doc_types)->where('document_ownership',Auth::user()->username)->count();//checkout docs
                $data['archive_Cnt']    = DocumentsModel::whereIn('document_type_id',$doc_types)->where('document_ownership',Auth::user()->username)->where('document_file_name', 'like', '%' . '.zip' . '%')->orWhere('document_file_name', 'like', '%' . '.rar' . '%')->count();
                $data['docsCnt']      = DocumentsModel::whereIn('document_type_id',$doc_types)->where('document_ownership',Auth::user()->username)->count();
                $data['un_docsCnt']     = TempDocumentsModel::whereIn('document_type_id',$doc_types)->where('document_ownership',Auth::user()->username)->count();//unpublished docs
                $data['auditCnt']     = AuditsModel::where('audit_user_name',Auth::user()->username)->count();
                $active_wfs             = $query1->get();
                $data['workflowCnt']    = count($active_wfs);
                $my_forms = $query->where('tbl_form_responses.user_id',Auth::user()->id)->get();
                $data['formCnt']        = count($my_forms);
                Session::put("un_docsCnt",$data['un_docsCnt']);
            break;
        }
        
        // Get data which don't has geolocation ,to update it
        /*$audits = DB::table('tbl_audits')->select('audit_id','audit_user_ip','audit_geo_location')->where('audit_user_ip','!=','')->where('audit_geo_location','=','')->get();
        if($audits):
            foreach($audits as $val):
                $geoLocation = unserialize(file_get_c_countryCode'];
                $value['audit_action_desc']  = $val->audit_user_ip.','.$geoLocation['geoplugin_city'].','.$geoLocation['geoplugin_region'].','.$ontents('http://www.geoplugin.net/php.gp?ip='.$val->audit_user_ip));
                $value['audit_geo_location'] = $geoLocation['geoplugin_city'].','.$geoLocation['geoplugin_region'].','.$geoLocation['geoplugingeoLocation['geoplugin_countryCode'];
                // update table 
                DB::table('tbl_audits')->where('audit_id',$val->audit_id)->update($value);
                endforeach;
        endif;*/
               
                
                $data['stckApp'] = $this->docObj->common_stack();
                $data['deptApp'] = $this->docObj->common_dept();
                $data['doctypeApp'] = $this->docObj->common_type();
                $data['records'] = $this->docObj->common_records();
                $this->docObj->common_workflow();
                $this->docObj->common_forms();
                // Dashboard widget postions
                $data['widget_postion'] = HomeModel::prepare_widget_postion();
                //menu id
        Session::put('menuid', '5');
        return view('home')->with($data);
    }

    public function toggle(){

        Session::put('toggled',true);
    }

public function getDept(){

    $mb_value = 1048576;
    $gb_value = 1073741824;
    $highlights=array(
    'Aqua',
    'Aquamarine',        
    'Blue',
    'BlueViolet',      
    'Brown',   
    'BurlyWood',  
    'CadetBlue',   
    'Chartreuse', 
    'Chocolate', 
    'Coral',   
    'CornflowerBlue',    
    'Crimson',
    'Cyan', 
    'DarkBlue',
    'DarkCyan',
    'DarkGoldenRod', 
    'DarkGreen',
    'DarkKhaki',
    'DarkMagenta',     
    'DarkOliveGreen',
    'DarkOrange',     
    'DarkOrchid',      
    'DarkRed',
    'DarkSalmon',   
    'DarkSeaGreen',   
    'DarkSlateBlue',   
    'DarkTurquoise',   
    'DarkViolet',     
    'DeepPink',
    'DeepSkyBlue',   
    'DodgerBlue',      
    'FireBrick',   
    'ForestGreen',     
    'Fuchsia',        
    'Gold',    
    'GoldenRod',   
    'Green',   
    'GreenYellow',       
    'HotPink',     
    'IndianRed',      
    'Indigo',       
    'Khaki',     
    'LawnGreen',
    'Violet',            
    'Yellow',      
    'YellowGreen'      
    );
    $colors=array(   
    'LightCoral',
    'LightPink',   
    'LightSalmon',
    'LightSeaGreen',  
    'LightSkyBlue',    
    'LightSteelBlue',         
    'Lime',
    'LimeGreen',     
    'Magenta',    
    'Maroon',     
    'MediumAquaMarine',  
    'MediumOrchid',
    'MediumPurple',   
    'MediumSlateBlue',
    'MediumSpringGreen',
    'MediumTurquoise',   
    'MediumVioletRed',
    'MidnightBlue',   
    'MistyRose',
    'Moccasin',
    'Navy',    
    'Olive',   
    'OliveDrab',  
    'Orange',     
    'OrangeRed',   
    'Orchid',
    'PaleGoldenRod',   
    'PaleGreen',   
    'PaleTurquoise',  
    'PaleVioletRed',  
    'Peru',   
    'Pink',    
    'Plum',   
    'PowderBlue',     
    'Purple',    
    'RebeccaPurple',  
    'Red',     
    'RosyBrown',   
    'RoyalBlue',  
    'SaddleBrown',     
    'Salmon',      
    'SandyBrown',     
    'SeaGreen',       
    'Sienna',            
    'SkyBlue',     
    'SlateBlue',        
    'SpringGreen',    
    'SteelBlue',  
    'Tan',    
    'Teal',    
    'Thistle',    
    'Tomato',     
    'Turquoise'   
    );

    
    //Department 
    
    $data['dept']=DB::table('tbl_departments')
    ->select('department_name as label','department_id')
    ->whereIn('department_id',Session::get('auth_user_dep_ids'))
    ->orderBy('department_order', 'ASC')->get();

    $data['dept_size']=DB::table('tbl_departments')
    ->select('department_name as label','department_id')
    ->whereIn('department_id',Session::get('auth_user_dep_ids'))
    ->get();
    
    switch (Auth::user()->user_role) 
    {
        case Session::get("user_role_private_user")://private user

            $data['user']=DB::table('tbl_users')
            ->select('username as label')
            ->where('id',Auth::user()->id)
            ->get();

            $data['user_size']=DB::table('tbl_users')
            ->select('username as label')
            ->where('id',Auth::user()->id)
            ->get();

            $file_extension=DB::table('tbl_documents')->leftjoin('tbl_document_types','tbl_documents.document_type_id','=','tbl_document_types.document_type_id')->where('tbl_document_types.is_app',0)
            ->select('tbl_documents.document_file_name')
            ->where('tbl_documents.document_created_by',Auth::user()->username)
            ->get();
        break;
        

        case Session::get("user_role_group_admin")://group admin
        case Session::get("user_role_regular_user")://regular user
            $data['user']=DB::table('tbl_users')
            ->select('username as label')
            ->whereIn('id',Session::get('auth_users_dep_under'))
            ->get();

            $array = json_decode(json_encode($data['user']), True);

            $data['user_size']=DB::table('tbl_users')
            ->select('username as label')
            ->whereIn('id',Session::get('auth_users_dep_under'))
            ->get();

            $file_extension=DB::table('tbl_documents')->leftjoin('tbl_document_types','tbl_documents.document_type_id','=','tbl_document_types.document_type_id')->where('tbl_document_types.is_app',0)
            ->select('tbl_documents.document_file_name')
            ->whereIn('tbl_documents.document_created_by',$array)
            ->get();

        break;

        case Session::get("user_role_super_admin")://super admin

            $data['dept']=DB::table('tbl_departments')
            ->select('department_name as label','department_id')
            ->orderBy('department_order', 'ASC')->get();

            $data['dept_size']=DB::table('tbl_departments')
            ->select('department_name as label','department_id')
            ->get();

            $data['user']=DB::table('tbl_users')
            ->select('username as label')
            ->get();

            $data['user_size']=DB::table('tbl_users')
            ->select('username as label')
            ->get();

            $file_extension=DB::table('tbl_documents')->leftjoin('tbl_document_types','tbl_documents.document_type_id','=','tbl_document_types.document_type_id')->where('tbl_document_types.is_app',0)
            ->select('tbl_documents.document_file_name')
            ->get();
            
        break;
    }

    //NOTE: if two graphs required on home page plz remove the $val->size from the for loops.

    $k=0;
    foreach ($data['dept'] as $val) {
       $val->value=DB::table('tbl_documents')->where('department_id',$val->department_id)->count();
       $val->size=number_format(DB::table('tbl_documents')->where('department_id',$val->department_id)->sum('document_size')/ $mb_value, 2); //Mb format
       $val->color=$colors[$k];
       $val->highlight=$highlights[$k];
       unset($val->department_id);
       $k++;
       if($k > 45){ $k=0; } /*colors and  highlights array limit are 45*/
    }

    

    //No. and Size of Docs by Document Types
    $user_role = Auth::user()->user_role;
    $user_role_super_admin      = Session::get("user_role_super_admin");  
    $user_role_group_admin      = Session::get("user_role_group_admin");
    $user_role_private_user     = Session::get("user_role_private_user");
    $user_role_regular_user     = Session::get("user_role_regular_user");  

    $doctype_query = DB::table('tbl_document_types as dt');
    $doctype_query->join('tbl_documents as doc', 'dt.document_type_id', '=', 'doc.document_type_id')->where('dt.is_app',0);
    $doctype_query->selectRaw('dt.document_type_name as label,dt.document_type_id,count(doc.document_id) as document_count,SUM(doc.document_size) as document_size_sum');
    
    switch($user_role)
    {
        case $user_role_group_admin:
        case $user_role_regular_user:
            $doctype_query->join('tbl_users as user', 'doc.document_created_by', '=', 'user.username');
            $doctype_query->whereIn('user.id',Session::get('auth_users_dep_under'));
        break;

        case $user_role_super_admin:
        break;

        case $user_role_private_user:
            $doctype_query->where('doc.document_created_by',Auth::user()->username); 
        break;
    } 
    
    $data['doctype'] = $doctype_query
                    ->groupBy('dt.document_type_id')
                    ->orderBy('dt.document_type_order')
                    ->get();

    $count_type=count($data['doctype']);
    if($count_type!=0)
    {
        $l=0;
        foreach ($data['doctype'] as $val) 
        {
           $size = ($val->document_size_sum)?number_format($val->document_size_sum/ $mb_value, 2):0;
           $val->value = $size; /*for doughnut graph size*/
           $val->size = $size; 
           $val->color=$colors[$l];
           $val->highlight=$highlights[$l];
           unset($val->document_type_id);

           $l++;
           if($l > 45){ $l=0; } /*colors and  highlights array limit are 45*/
        }
    }
    else
    {
        $data['doctype']= Lang::get('language.text_empty');
    }
    
    //Users

    $m=0;
    foreach ($data['user'] as $val) {
       $val->value=DB::table('tbl_documents')->leftjoin('tbl_document_types','tbl_documents.document_type_id','=','tbl_document_types.document_type_id')->where('tbl_document_types.is_app',0)->where('tbl_documents.document_created_by',$val->label)->count();
       $val->size=number_format(DB::table('tbl_documents')->leftjoin('tbl_document_types','tbl_documents.document_type_id','=','tbl_document_types.document_type_id')->where('tbl_document_types.is_app',0)->where('tbl_documents.document_created_by',$val->label)->sum('tbl_documents.document_size')/ $mb_value, 2);
       $val->color=$colors[$m];
       $val->highlight=$highlights[$m];
       $m++;
       if($m > 45){ $m=0; } /*colors and  highlights array limit are 45*/
    }

    //Document Extension

    $where ='';
    $extension_query = DB::table('tbl_document_types as dt');
    $extension_query->join('tbl_documents as doc', 'dt.document_type_id', '=', 'doc.document_type_id');

    $extension_query->selectRaw("RIGHT(doc.document_file_name, LOCATE('.', REVERSE(doc.document_file_name)) - 1)  as  Extension,doc.document_size");
    
    if($user_role == $user_role_group_admin || $user_role == $user_role_regular_user)
    {
        $extension_query->join('tbl_users as user', 'doc.document_created_by', '=', 'user.username');
        $extension_query->join('tbl_users_departments as users_department', 'user.id', '=', 'users_department.users_id');
       $department_ids = (isset(Auth::user()->department_id))?trim(Auth::user()->department_id,', '):'';
       if($department_ids)
       {
        $where .=(trim($where))?"AND":"";
        $where .=" users_department.department_id IN(".$department_ids.")";
       }
       
    }
    else if($user_role == $user_role_super_admin)
    {
        
    }
    else 
    {
       $where .=(trim($where))?"AND":"";
       $where .=" doc.document_created_by='".addslashes(Auth::user()->username)."'";
    } 
    
   /* $data['extension_query'] = $extension_query->groupBy('Extension')->get();*/

   
   $extension_query_sql = $extension_query->toSql();
   if(trim($where))
   {
    $extension_query_sql .="WHERE(".$where.")";
   }
   
   $data['extension'] = DB::table(DB::raw("({$extension_query_sql}) as sub"))->select(DB::raw("sub.Extension as label, count(sub.Extension) as count,SUM(sub.document_size) as document_size_sum"))->groupBy('sub.Extension')->get();

   /*apps are merge with the docs, so the first extension changed to 'apps'*/
   if($data['extension'][0]->label == null)
   {
    $data['extension'][0]->label = 'Apps';
   }
   
   if(count(@$data['extension']))
   {

        $l=0;
        foreach (@$data['extension'] as $val) 
        {
           $size = ($val->document_size_sum)?number_format($val->document_size_sum/ $mb_value, 2):0;
           $val->value = $size; /*for doughnut graph size*/
           $val->color=$colors[$l];
           $val->highlight=$highlights[$l];
           unset($val->document_type_id);

           $l++;
           if($l > 45){ $l=0; } /*colors and  highlights array limit are 45*/
        }
        
    }
    else
    {
        $data['extension']= Lang::get('language.text_empty');
    }

    
//Database files
    $decimals = 2;  
    $bckupsize = 0;
    $totalfilecnt  =0;
    foreach (glob(rtrim(config('app.zip_backup_path'), '/').'/*', GLOB_NOSORT) as $each) {
         $totalfilecnt++;
        $bckupsize += filesize($each);
    }

    $bckpbytes = number_format($bckupsize/(1024*1024),$decimals);
    $bckpbytes = floatval(preg_replace('/[^\d.]/', '', $bckpbytes));//comma seperates
    $backup_files_array[] = array(
            'label' => 'Backup',
            'value' => $bckpbytes,
            'size' => $bckpbytes,
            'color'=>'Red',
            'highlight'=>'Blue',
            'length'=>$totalfilecnt);


    $tables = DB::select("show table status");
    $db_size = 0; 
    foreach($tables as $row) {  
        $db_size += $row->Data_length + $row->Index_length; 
    }
    $mbytes = number_format($db_size/(1024*1024),$decimals);
    $mbytes = floatval(preg_replace('/[^\d.]/', '', $mbytes));//comma seperates
    $db_files_array[] = array(
            'label' => 'Database',
            'value' => $mbytes,
            'size' => $mbytes,
            'color'=>'SeaGreen',
            'highlight'=>'Blue',
            'length'=>1);
//unpublished
    $type_files = $data['extension'];
    
    $unpublished_files_size = DB::table('tbl_temp_documents')->sum('document_size');
    $unpublished_files_size_mb = number_format($unpublished_files_size / $mb_value, 2);
    $unpublished_files_size_mb = floatval(preg_replace('/[^\d.]/', '', $unpublished_files_size_mb));//comma seperates
    $unpublished_files_array[] = array(
            'label' => 'Unpublished Docs',
            'value' => $unpublished_files_size_mb,
            'size' => $unpublished_files_size_mb,
            'color'=>'Turquoise',
            'highlight'=>'LAVENDER',
            'length' => Session::get("un_docsCnt"));

    if(is_array($type_files)) {
        foreach ($type_files as $value) 
        {
                        
            if($value->label)
            {
                if($value->label == 'png' || $value->label == 'jpeg' || $value->label == 'jpg' || $value->label == 'tiff' || $value->label == 'TIF' || $value->label == 'tif' || $value->label == 'flv' || $value->label == 'mp3' || $value->label == 'ogg' || $value->label == 'mp4' || $value->label == 'webm' || $value->label == 'ogv' || $value->label == 'wav' || $value->label == 'mpeg')
                {
                    $value->file_type= 'Media Files';
                    $value->length = 'Media_count';
                }
                else if( $value->label == 'pdf' ||  $value->label == 'doc' ||  $value->label == 'docx' ||  $value->label == 'xls' || $value->label == 'xlsx' || $value->label == 'txt')
                {
                    $value->file_type = 'Documents';
                    $value->length = 'Published_count';
                }
                else if( $value->label == 'rar' || $value->label == 'zip')
                {
                    $value->file_type = 'Archives';
                    $value->length = 'Archives_count';
                }
                elseif($value->label=='' || $value->label== null)
                {
                    $value->file_type = 'Others';
                    $value->length = 'Others_count';
                }
                else
                {
                    $value->file_type = 'Others';
                    $value->length = 'Others_count';
                }
            }
            elseif($value->label=='' || $value->label== null)
            {
                $value->file_type = 'Others';
                $value->length = 'Others_count';
            }
            else
            {
                $value->file_type = 'Others';
                $value->length = 'Others_count';
            }
        }

    }
    
    $args = json_decode(json_encode($type_files), True);
    $tmp = array();
    $tmp1 = array();
    if(is_array($args)) {
       foreach($args as $arg)
        {
            $tmp[@$arg['file_type']][] = $arg['document_size_sum'];
            $tmp1[@$arg['length']][] = $arg['count'];
        } 
    }
    

    $output = array();
    $outputs = array();
    $z=0;
    foreach($tmp as $file_type => $document_size_sum)
    {
        $size_file = array_sum($document_size_sum);
        $size_file_mb = number_format($size_file / $mb_value, 2);
        $size_file_mb = floatval(preg_replace('/[^\d.]/', '', $size_file_mb));//comma seperates
        $output[] = array(
            'label' => $file_type,
            'value' => $size_file_mb,
            'size' => $size_file_mb,
            'color'=>$colors[$z],
            'highlight'=>$highlights[$z]
        );
        $z++;
        if($z > 45){ $z=0; } /*colors and  highlights array limit are 45*/
    }
    
    foreach($tmp1 as $length => $count)
    {
        $count_file[]  = array_sum($count);
    }
    $k=0;
    foreach ($output as $name => $locations) {
    $locations['length'] = $count_file[$k];
    $k++;
    $outputs[] = $locations;
    }
    
    $data['file_size'] = array_merge($outputs,$unpublished_files_array,$db_files_array,$backup_files_array);
//disk space
    $path = getcwd();
    $ds = disk_total_space($path);
    $total_space =  number_format($ds / $gb_value, 2);//to GB
    $total_space = floatval(preg_replace('/[^\d.]/', '', $total_space));//comma seperates
    // echo 'total_space='.$total_space;
    // echo '</br>';
    $df = disk_free_space($path);
    $free_space =  number_format($df / $gb_value, 2);
    $free_space = floatval(preg_replace('/[^\d.]/', '', $free_space));
    // echo 'free_space='.$free_space;
    // echo '</br>';
    $dv = $ds-$df;
    $used_space =  number_format($dv / $gb_value, 2);
    $used_space = floatval(preg_replace('/[^\d.]/', '', $used_space));
    // echo 'used_space='.$used_space;
    // echo '</br>';
    $disk_array[0] = array(
            'label' => 'Free Space',
            'value' => $free_space,
            'size' => $free_space,
            'color'=>'Green',
            'highlight'=>'YellowGreen');
    $disk_array[1] = array(
            'label' => 'Used Space',
            'value' => $used_space,
            'size' => $used_space,
            'color'=>'Red',
            'highlight'=>'HotPink');
    $data['disk_size'] = $disk_array;
    
    //dept size

    $p=0;
    foreach ($data['dept_size'] as $val) {
       $val->value=number_format(DB::table('tbl_documents')->where('department_id',$val->department_id)->sum('document_size')/ $mb_value, 2); //Mb format
       $val->color=$colors[$p];
       $val->highlight=$highlights[$p];
       unset($val->department_id);
       $p++;
       if($p > 45){ $p=0; } /*colors and  highlights array limit are 45*/
    }

   
    //Users size

    $s=0;
    foreach ($data['user_size'] as $val) {
       $val->value=number_format(DB::table('tbl_documents')->leftjoin('tbl_document_types','tbl_documents.document_type_id','=','tbl_document_types.document_type_id')->where('tbl_document_types.is_app',0)->where('tbl_documents.document_created_by',$val->label)->sum('tbl_documents.document_size')/ $mb_value, 2);
       $val->color=$colors[$s];
       $val->highlight=$highlights[$s];
       $s++;
       if($s > 45){ $s=0; } /*colors and  highlights array limit are 45*/
    }

   
    return $data;
  }

  public function saveWidgetPostion()
  {
    $center_div1 = (Input::has('center_div1'))?Input::get('center_div1'):array();
    $center_div2 = (Input::has('center_div2'))?Input::get('center_div2'):array();
    $center_div3 = (Input::has('center_div3'))?Input::get('center_div3'):array();

    
    $changedList = (Input::has('changedList'))?Input::get('changedList'):'';
    $widget_postion = array('center_div1' => $center_div1,'center_div2' => $center_div2,'center_div3' => $center_div3); 

    $dataToUpdate = array('dashboard_widgets' => serialize($widget_postion));  
     
    $id = Auth::user()->id; 
    if($id)
    {
        DB::table('tbl_users')->where('id', $id)->update($dataToUpdate);
    }
    $json = array('status' => 1,'v'=> $widget_postion);
    return json_encode($json);

    
  }

    // 404 page not found
    public function pageNotFound(){
        // view
        //return view('404_error');
    }

    // Token mismatch error
    public function tokenMismatch(){
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
        { 
            return "tokenMismatch";
        }
        else
        {
            return view('tokenError');
        } 
    }
    //store skin
    public function changeSkin(){
        $skin = (Input::get('val'))?Input::get('val'):'skin-blue';
        if($skin)
        {
            DB::table('tbl_users')->where('id',Auth::user()->id)->update(['user_skin'=>$skin]);
            return $skin;
        }
    }
    //get skin
    public function getSkin(){
        $skin =  DB::table('tbl_users')->select('user_skin')->where('id',Auth::user()->id)->first();
        return $skin->user_skin;
    }
}/*<--END-->*/