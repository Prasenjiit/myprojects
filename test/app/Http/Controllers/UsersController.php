<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApplicationLogController;
use App\Http\Requests;
use Auth;
use View;
use Input;
use Validator;
use Session;
use DB;
use Hash;
use App\Users as Users;
use App\AuditsModel as AuditModel;
use App\StacksModel as StacksModel;
use App\DocumentTypesModel as DocumentTypesModel;
use App\DepartmentsModel as DepartmentsModel;
use App\Mylibs\Common;
use Lang;
use Carbon\Carbon;
class UsersController extends Controller
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
        // Set common variable
        $this->actionName      = 'User';
        $this->user_permission = 'add,edit,view,delete,checkout,import,export,workflow,decrypt';
        $this->form_permission = 'add,edit,view,delete,export';
        $this->wf_permission   = 'add,edit,view,delete';

        $this->vou_permission   = 'view';

        $this->docObj          = new Common(); // class defined in app/mylibs
    }
    
    public function index() {  
        if (Auth::user()) {
            // Get department id of logged in user
            Session::put('auth_user_dep_ids',explode(',',@Auth::user()->department_id));
            Session::put('menuid', '4');
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();           
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            $data['emailNotif']  = DB::table('tbl_email_notifications')->get(); 
            $settings = DB::table('tbl_settings')->select('settings_password_length_from','settings_password_length_to')->get();
            $data['settings_password_length_from']  =   $settings[0]->settings_password_length_from;
            $data['settings_password_length_to']    =   $settings[0]->settings_password_length_to;
            $data['users']    = Users::get_users();
            // echo '<pre>';
            // print_r(Session::get('auth_user_dep_ids'));
    
            if(Auth::user()->user_role == Session::get('user_role_group_admin') || Auth::user()->user_role == Session::get('user_role_regular_user'))
            {
                $data['departments'] = DepartmentsModel::select('*')->whereIn('department_id',Session::get('auth_user_dep_ids'))->orderBy('department_name', 'ASC')->get();
            }
            else
            {
                $data['departments'] = DepartmentsModel::orderBy('department_name', 'ASC')->get();
            }
            return View::make('pages/users/index')->with($data);
        } else {
            return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }

    
    // Get user permission<-- this function is common -->
    public function getUserPermission(){        
        if(Input::get('editUserId')):
            // User private user click trigger 
            $permissions = Users::select('user_permission','user_form_permission','user_workflow_permission')->where('id',Input::get('editUserId'))->get();
            //echo json_encode($permissions); exit();
            $Userpermissions     = @$permissions[0]->user_permission;
            $Formpermissions     = @$permissions[0]->user_form_permission;
            $WorkFlowpermissions = @$permissions[0]->user_workflow_permission;
        else:
            $Userpermissions = Auth::user()->user_permission;
        endif;
        /* Fill User Permission */        
        if(stristr(@$Userpermissions,'add'))
            $hasPermissionAdd = 'add';        
        if(stristr(@$Userpermissions,'edit'))
            $hasPermissionEdit = 'edit';
        if(stristr(@$Userpermissions,'view'))
            $hasPermissionView = 'view';
        if(stristr(@$Userpermissions,'delete'))
            $hasPermissionDelete = 'delete'; 
        if(stristr(@$Userpermissions,'checkout'))
            $hasPermissionDownload = 'checkout'; 
        if(stristr(@$Userpermissions,'import'))
            $hasPermissionImport = 'import'; 
        if(stristr(@$Userpermissions,'export'))
            $hasPermissionExport = 'export'; 
        if(stristr(@$Userpermissions,'workflow'))
            $hasPermissionDecrypt = 'workflow'; 
        if(stristr(@$Userpermissions,'decrypt'))
            $hasPermissionDecrypt = 'decrypt'; 

        /* Fill Form Permission */  
        if(stristr(@$Formpermissions,'add'))
            $hasFormPermissionAdd = 'add';        
        if(stristr(@$Formpermissions,'edit'))
            $hasFormPermissionEdit = 'edit';
        if(stristr(@$Formpermissions,'view'))
            $hasFormPermissionView = 'view';
        if(stristr(@$Formpermissions,'delete'))
            $hasFormPermissionDelete = 'delete'; 
        if(stristr(@$Formpermissions,'export'))
            $hasFormPermissionExport = 'export'; 

        /* Fill Work Flow Permission */  
        if(stristr(@$WorkFlowpermissions,'add'))
            $hasWrkflwPermissionAdd = 'add';        
        if(stristr(@$WorkFlowpermissions,'edit'))
            $hasWrkflwPermissionEdit = 'edit';
        if(stristr(@$WorkFlowpermissions,'view'))
            $hasWrkflwPermissionView = 'view';
        if(stristr(@$WorkFlowpermissions,'delete'))
            $hasWrkflwPermissionDelete = 'delete'; 

        $data = array( 'user_add'       => @$hasPermissionAdd,
                       'user_edit'      => @$hasPermissionEdit,
                       'user_view'      => @$hasPermissionView,
                       'user_delete'    => @$hasPermissionDelete,
                       'user_download'  => @$hasPermissionDownload,
                       'user_import'    => @$hasPermissionImport,
                       'user_export'    => @$hasPermissionExport,
                       'user_workflow'  => @$hasPermissionWorkflow,
                       'user_decrypt'   => @$hasPermissionDecrypt,
                       'form_add'       => @$hasFormPermissionAdd,
                       'form_edit'      => @$hasFormPermissionEdit,
                       'form_view'      => @$hasFormPermissionView,
                       'form_delete'    => @$hasFormPermissionDelete,
                       'form_export'    => @$hasFormPermissionExport,
                       'workflow_add'   => @$hasWrkflwPermissionAdd,
                       'workflow_edit'  => @$hasWrkflwPermissionEdit,
                       'workflow_view'  => @$hasWrkflwPermissionView,
                       'workflow_delete'=> @$hasWrkflwPermissionDelete
                       );
        echo json_encode($data);exit;// ajax response
    }

    public function usersList() { 
        if (Auth::user()) {
            Session::put('menuid', '4');
            $user_permission=Auth::user()->user_permission;
            if(stristr($user_permission,"view")){
               /*<--updated on 1-11-2016 -->*/  
                // user list according to the departments
                if(Auth::user()->user_role == Session::get('user_role_super_admin')):
                    // Admin
                    $users = Users::orderBy('user_full_name', 'ASC')->select('*')->get();// All List
                elseif(Auth::user()->user_role == Session::get('user_role_private_user')):
                    //  Private user
                    $users = Users::orderBy('user_full_name', 'ASC')->select('*')->where('id',Auth::user()->id)->get();// Get own details
                else:
                    // Department admin or Regular user or Private user
                    $users = DB::table('tbl_users')->leftJoin('tbl_users_departments', 'tbl_users.id', '=', 'tbl_users_departments.users_id')->whereIn('tbl_users_departments.department_id',Session::get('auth_user_dep_ids'))->select('tbl_users.*')->groupBy('tbl_users.id')->orderBy('tbl_users.username','ASC')->get();
                endif; 
                foreach($users as $val):
                    $department_ids =  $val->department_id;
                    $department_ids =  explode(',',$department_ids);
                    $departments = DB::table('tbl_departments')->whereIn('department_id',$department_ids)->select(DB::raw('group_concat(department_name) as department_name'))->get(); 
                    $val->departments = $departments;
                endforeach; 
                $data['usersList']   = $users;               
                $data['stckApp'] = $this->docObj->common_stack();
                $data['deptApp'] = $this->docObj->common_dept();
                $settings = DB::table('tbl_settings')->select('settings_pasword_expiry')->get();
                $data['expry_no']   = $settings[0]->settings_pasword_expiry;
                $data['doctypeApp'] = $this->docObj->common_type();
                $data['records'] = $this->docObj->common_records();
                return View::make('pages/users/list')->with($data);
            }else{
                echo '<div class="alert alert-danger alert-sty">'.Lang::get('language.dont_hav_permission_lang').'</div>';
                exit();
            }
        } else {
            return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }
    //show users
    public function show(Request $request, $id,$name)
    {        
        Session::put('menuid', '4');
        if (Auth::user()) {
            $result= Users::select('username','user_full_name','email')->where('department_id', 'LIKE', '%'.$id.'%')->get();
            return View::make('pages/departments/showusers')->with(['name'=>$name,'results'=>$result]);
        } else {
            return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    { 
        if (Auth::user()) {
            Session::put('menuid', '4');
                if ($id) {
                    $data['stckApp'] = $this->docObj->common_stack();
                    $data['deptApp'] = $this->docObj->common_dept();
                    $settings = DB::table('tbl_settings')->select('settings_password_length_from','settings_password_length_to')->get();
                    $data['settings_password_length_from']  =   $settings[0]->settings_password_length_from;
                    $data['settings_password_length_to']    =   $settings[0]->settings_password_length_to;
                    $data['doctypeApp'] = $this->docObj->common_type();
                    $data['records'] = $this->docObj->common_records();
                    $data['emailNotif']  = DB::table('tbl_email_notifications')->get(); 
                    $data['logged_in_userId'] = Auth::user()->id;
                    $data['userData'] = Users::find($id);
                    $data['users']    = Users::get_report_to_users($id);
                    $data['reportsTo']  = Users::getUser($data['userData']->report_to);
                    $data['delegateTo'] = Users::getUser($data['userData']->delegate_to);
                    if(Auth::user()->user_role == Session::get('user_role_group_admin') || Auth::user()->user_role == Session::get('user_role_regular_user')){
                        $data['departments']= DepartmentsModel::select('*')->whereIn('department_id',Session::get('auth_user_dep_ids'))->get();
                    }else{
                        $data['departments']= DepartmentsModel::all();
                    }
                    $data['adminUserRole'] = Auth::user()->user_role;
                    // Edit if Both admin or gropu admin
                    if(Auth::user()->user_role == Session::get('user_role_super_admin') || Auth::user()->user_role == Session::get('user_role_group_admin')):
                        return View::make('pages/users/edit')->with($data);
                    else:
                        // Edit own edit form if private user or regular user
                        if(Auth::user()->id == $id):
                            return View::make('pages/users/edit')->with($data);
                        else:
                            return View::make('404_error');
                        endif;
                    endif;
                } else {
                    return redirect('users');
                }           
        } else {
            return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }

    //user history
    public function history($username)
    { 
        if (Auth::user()) {
            if ($username) {
                Session::put('menuid', '4');
                $data['stckApp'] = $this->docObj->common_stack();
                $data['deptApp'] = $this->docObj->common_dept();
                $data['doctypeApp'] = $this->docObj->common_type();
                $data['records'] = $this->docObj->common_records();
                $data['userName']       =     $username;
                $data['usersList'] = DB::table('tbl_audits')->where('audit_user_name',$username)->orderBy('created_at', 'DESC')->get();
                return View::make('pages/users/history')->with($data);
            } else {
                return redirect('users');
            }
        } else {
            return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }

    public function reset() {
        if (Auth::user()) {
            $data['stckApp'] = $this->docObj->common_stack();
            // update table
            // $dataToUpdate['login_status'] = 0;
            // DB::table('tbl_users')->where('id', Auth::user()->id)->update($dataToUpdate);
            return View::make('pages/users/reset')->with($data);
        }
       
    }

    public function resetSubmit(){
        $id = Input::get('id');
        $getPsw = DB::table('tbl_users')->select('password')->where('id',$id)->get();
        $hashedPassword = $getPsw[0]->password;
        
        if(Hash::check(Input::get('current_password'), $hashedPassword)){  
            // update password 
            $dataToUpdate['password']= bcrypt(Input::get('correct_password'));
            $dataToUpdate['password_date'] = date("Y-m-d");
            // update table
            DB::table('tbl_users')->where('id',$id)->update($dataToUpdate);
            return redirect('/home');
        }else{
            // Show wanning message
            Session::flash('flash_message_wanning', Lang::get('language.psw_not_match_lang'));
            return redirect("reset"); 
        }

    }


    public function save(Request $request, $id)
    {   
        if (Auth::user()) {
            /*<--EDIT-->*/
            if ($id) {
                /*<----------- updated on 31-10-2016 ------------------>*/   
                $user = Users:: find($id);
                $user_full_name   = Input::get('name');
                $email            = Input::get('email');
                $user_modified_by = Auth::user()->username;
                $previousPassword = $user->password;
                $viewonly_user = 0; // default value is 0. which means the user is not view only user
                // Get user permissions
                switch(Input::get('user_type')){
                    // Either Admin or Group admin
                    case "1":
                    case "2":
                        $user_permission = $this->user_permission;
                        $form_permission = $this->form_permission;
                        $wf_permission   = $this->wf_permission;
                        break;
                    // Either Regular user or Private user
                    case "3":
                    case "4":
                        if(Input::get('user_permission')){
                            $user_permission  = implode(',',Input::get('user_permission'));
                        }else{
                            $user_permission = '';
                        }
                        if(Input::get('privileges_frm')){
                            $form_permission  = implode(',',Input::get('privileges_frm'));
                        }else{
                            $form_permission  = '';
                        }   
                        if(Input::get('privileges_wf')){
                            $wf_permission  = implode(',',Input::get('privileges_wf'));
                        }else{
                            $wf_permission  = '';
                        }
                        break;
                    // Admin edit
                    default:
                        $user_permission = $this->user_permission;
                        $form_permission = $this->form_permission;
                        $wf_permission   = $this->wf_permission;
                        break;
                }
            

                $department_id = '';
                if(Input::get('document_group')){
                    $department_id = implode(',',Input::get('document_group'));
                }
                
                
                $delegate_user = (Input::get('delegate_user')!='')?Input::get('delegate_user'):NULL;
                if($delegate_user) {
                    $delegate_period = explode('_',Input::get('delegate_period'));
                }
                else {
                    $delegate_period = array(NULL,NULL);
                }
                
                $dataToUpdate = array('user_full_name'     => @$user_full_name,
                                      'email'              => @$email,
                                      'user_modified_by'   => @$user_modified_by,
                                      'updated_at'         => date('Y-m-d h:i:s'),
                                      'report_to'          => Input::get('report_to'),
                                      'delegate_user'      => $delegate_user,
                                      'delegate_from_date' => $delegate_period[0],
                                      'delegate_to_date'   => $delegate_period[1],
                                      );
                // Change table datas tbl_wf_assigned_users
               if($delegate_user) {
                     $updata = array('delegated_user'=>$delegate_user);
                    DB::table('tbl_wf_assigned_users')
                              ->where('user_id','=',$id)
                              ->where('activity_id','=',0)
                              ->update($updata);
                }
                
                /*<--password cahange-->*/
                if(Input::get('pwd_edit') == 1){ 

                    // Check password complexity
                    $inputPassword = Input::get('confirm_password');
                    $isComplexityOk = $this->docObj->checkPasswordComplexity($inputPassword );
                    
                    switch($isComplexityOk){ 
                        case "alphabet_false":
                                              return redirect("userEdit/$id")->with('flash_msg',Lang::get('language.psw_alphabet_complexity_msg'))->with('alert_msg','alert-warning');
                                              break;
                        case "special_character_false":
                                              return redirect("userEdit/$id")->with('flash_msg',Lang::get('language.psw_splChar_complexity_msg'))->with('alert_msg','alert-warning');
                                              break;
                        case "numerics_false":
                                              return redirect("userEdit/$id")->with('flash_msg',Lang::get('language.psw_numerics_complexity_msg'))->with('alert_msg','alert-warning'); 
                                              break;
                        case "capital_and_small_false":
                                              return redirect("userEdit/$id")->with('flash_msg',Lang::get('language.psw_capital_and_small_complexity_msg'))->with('alert_msg','alert-warning'); 
                                              break;
                    }

                    // Checking new password is equal to previous one
                    //<---------------------------------------------------------------->
                    if(Hash::check($inputPassword, $previousPassword)){
                        // error message
                        Session::flash('flash_message_wanning', Lang::get('language.new_psw_diffr_lang'));
                        Session::flash('alert-class', 'alert alert-warning alert-sty');
                        return redirect("userEdit/$id");
                    }else{
                        $dataToUpdate['password_date']= date("Y-m-d h:i:sa");
                        // Checking current password
                        if(Input::get('current_password')){ // It should updated by current users(SA/GA/RU)
                            $getPsw = DB::table('tbl_users')->select('password')->where('id',$id)->get();
                            $hashedPassword = $getPsw[0]->password;
                            if(Hash::check(Input::get('current_password'), $hashedPassword)){  
                                // update password 
                                $dataToUpdate['password']= bcrypt(Input::get('password'));
                                $dataToUpdate['password_date'] = date('Y-m-d h:i:s');
                            }else{
                                // Show wanning message
                                Session::flash('flash_message_wanning', Lang::get('language.psw_not_match_lang'));
                                Session::flash('alert-class', 'alert alert-warning alert-sty');
                                return redirect("userEdit/$id"); 
                            }
                        }else{
                            // update password 
                            $dataToUpdate['password']= bcrypt(Input::get('password')); // It should updated by Super admin
                        }
                    }//<---------------------------------------------------------------->

                }/*<--password check ends-->*/
                if(Input::get('viewonly')=='1'){
                    $dataToUpdate['user_permission'] = $this->vou_permission;
                    $dataToUpdate['user_form_permission'] = $this->vou_permission;
                    $dataToUpdate['user_workflow_permission'] = $this->vou_permission;
                }else{
                    if(@$user_permission){
                        $dataToUpdate['user_permission'] = @$user_permission;
                        $dataToUpdate['user_form_permission'] = @$form_permission;
                        $dataToUpdate['user_workflow_permission'] = @$wf_permission;
                    }
                }
                

                if($department_id && Input::get('user_type') != Session::get('user_role_super_admin'))
                    $dataToUpdate['department_id'] = @$department_id;

                if(Input::get('user_type'))
                    $dataToUpdate['user_role'] = Input::get('user_type');
                if(Input::get('viewonly')==1)
                    $dataToUpdate['user_view_only'] = '1';
                
                if(@Input::get('user_status')==Session::get('user_status_Inactive')){
                    $dataToUpdate['user_status'] = 0;
                }else{
                    if(Input::get('user_status')):
                        $dataToUpdate['user_status'] = Input::get('user_status');
                    endif;
                }
                

                if(Input::get('login_exp_chk_edi') != 1){
                    $dataToUpdate['user_login_expiry'] = Input::get('exp_date_edi');
                }else{
                    $dataToUpdate['user_login_expiry'] = "";
                }

                if(Input::get('stngs_ovrwritepref')): 
                    if(Input::get('system_prefer')):                   
                        $dataToUpdate['user_activity_task_notifications'] = Input::get('stngs_activitytasknotif');
                        $dataToUpdate['user_form_notifications']          = Input::get('stngs_formnotif');
                        $dataToUpdate['user_document_notifications']      = Input::get('stngs_documentnotif');
                        $dataToUpdate['user_signin_notifications']        = Input::get('stngs_singninnotif');                    
                    else:
                        //<--Email settings-->
                        if(Input::get('user_activity_task_notifications')):
                            $dataToUpdate['user_activity_task_notifications'] = '1';
                        else:
                            $dataToUpdate['user_activity_task_notifications'] = '0';
                        endif;
                        if(Input::get('user_form_notifications')):
                            $dataToUpdate['user_form_notifications'] = '1';
                        else:
                            $dataToUpdate['user_form_notifications'] = '0';
                        endif;
                        if(Input::get('user_document_notifications')):
                            $dataToUpdate['user_document_notifications'] = '1';
                        else:
                            $dataToUpdate['user_document_notifications'] = '0';
                        endif;
                        if(Input::get('user_signin_notifications')):
                            $dataToUpdate['user_signin_notifications'] = '1';
                        else:
                            $dataToUpdate['user_signin_notifications'] = '0';
                        endif;
                    endif;
                endif;

                // if new dep selected then add
                if($department_id):
                    // delete existing data 
                    DB::table('tbl_users_departments')->where('users_id',$id)->delete();
                    // add new one
                    foreach(Input::get('document_group') as $dep):
                            $value = array('users_id'=>$id,
                                            'department_id'=>$dep);
                            DB::table('tbl_users_departments')->insert($value);     
                    endforeach;
                endif;

                // update table
                DB::table('tbl_users')->where('id',$id)->update($dataToUpdate);
                // Save in audits
                $userValue = Auth::user()->username;
                // Get update action message
                $actionMsg = Lang::get('language.update_action_msg');
                $actionDes = $this->docObj->stringReplace($this->actionName,$user_full_name,$userValue,$actionMsg);
                (new AuditsController)->log(Auth::user()->username, $user->username, 'Update',$actionDes);
                 // <!--Update notifications for header-->
                if((Input::get('pwd_edit') == 1) && (Auth::user()->id == $id)){    
                    //<!--Get password expiry notifiction-->   
                    $this->docObj->getPasswordExpiryNotification();
                }// <!--Notifications End-->
                // reload
                
                return redirect()->back()->with('data', "User '". $user->username ."' ".Lang::get('language.info_edited_success')." ");   
                /*<----------- updated on 31-10-2016 ------------------>*/
            }else {
                // Add
                $userName= Input::get('username');
                //Duplicate entry checking
                $duplicateEntry= Users::where('username', '=', $userName )->get();
                if(count($duplicateEntry) > 0 )
                {
                    echo '<div class="alert alert-danger alert-sty">'. $name.' '.Lang::get('language.already_db_msg').' </div>';
                    exit();
                } else {
                    $user= new Users;
                    $user->user_full_name= Input::get('name');
                    $user->email= Input::get('email');
                    $user->username= $userName;
                    $user->password= bcrypt(Input::get('password'));
                    $user->created_at= date('Y-m-d h:i:s');
                    $user->password_date= date('Y-m-d h:i:s');
                    $user->user_skin = 'skin-blue';//default skin
                    $user->user_status= (Input::get('user_status'))?Input::get('user_status'):0;
                    $user->report_to = Input::get('report_to');
                    $viewonly_user = 0; // default value is 0. which means the user is not view only user
                    //User role
                    if(Input::get('u-type') == 'super_admin')
                    {
                        $userRole= Session::get('user_role_super_admin');
                        $userPermission= $this->user_permission;
                        $formPermission = $this->form_permission;
                        $wfPermission = $this->wf_permission;
                        $documentGroup = '';
                        $loginExp = Input::get('exp_date');//$loginExp = '';

                    } else if (Input::get('u-type') == 'group_admin') {
                        $userRole= Session::get('user_role_group_admin');
                        $userPermission= $this->user_permission;
                        $formPermission = $this->form_permission;
                        $wfPermission = $this->wf_permission;
                        //Document Group itration
                        $dgs = Input::get('document_group');
                        $documentGroup= implode(",",$dgs);

                        if(Input::get('login_exp_chk') != 1)
                        {
                            $loginExp= Input::get('exp_date');
                        } else {
                            $loginExp = '';
                        }                            
                    } else if( (Input::get('u-type') == 'regular_user') || (Input::get('u-type') == 'private_user') || (Input::get('u-type') == 'viewonly_user')) {
                        if(Input::get('u-type') == 'regular_user'):
                            $userRole= Session::get('user_role_regular_user');
                        elseif(Input::get('u-type') == 'viewonly_user'):
                            $userRole= Session::get('user_role_regular_user');
                            $viewonly_user = 1; // user is view only user
                        elseif(Input::get('u-type') == 'private_user'):
                            $userRole= Session::get('user_role_private_user');
                        endif;

                        if(Input::get('u-type') == 'viewonly_user'){ // if the user is view only, all permission must be view only
                            $userPermission = $this->vou_permission;
                            $formPermission = $this->vou_permission;
                            $wfPermission   = $this->vou_permission;
                        }else{ // if the user is not view only, the permission goes on
                            if(Input::get('privileges')){
                                $userPermission  = implode(',',Input::get('privileges'));
                            }else{
                                $userPermission = '';
                            }
                            if(Input::get('privileges_frm')){
                                $formPermission  = implode(',',Input::get('privileges_frm'));
                            }else{
                                $formPermission  = '';
                            }   
                            if(Input::get('privileges_wf')){
                                $wfPermission  = implode(',',Input::get('privileges_wf'));
                            }else{
                                $wfPermission  = '';
                            }
                        }

                        if(Input::get('login_exp_chk') != 1)
                        {
                            $loginExp= Input::get('exp_date');
                        } else {
                            $loginExp = '';
                        } 
                        $dgs = Input::get('document_group');
                        $documentGroup= implode(",",$dgs);  
                    }
                                             
                    if(Input::get('system_prefer')):
                        $user->user_activity_task_notifications = Input::get('stngs_activitytasknotif');
                        $user->user_form_notifications          = Input::get('stngs_formnotif');
                        $user->user_document_notifications      = Input::get('stngs_documentnotif');
                        $user->user_signin_notifications        = Input::get('stngs_singninnotif');
                    else:
                        if(Input::get('activity_task_notifications')):
                            $user->user_activity_task_notifications = '1';
                        else:
                            $user->user_activity_task_notifications = '0';
                        endif;
                        if(Input::get('form_notifications')):
                            $user->user_form_notifications = '1';
                        else:
                            $user->user_form_notifications = '0';
                        endif;
                        if(Input::get('document_notifications')):
                            $user->user_document_notifications = '1';
                        else:
                            $user->user_document_notifications = '0';
                        endif;
                        if(Input::get('signin_notifications')):
                            $user->user_signin_notifications = '1';
                        else:
                            $user->user_signin_notifications = '0';
                        endif;
                    endif;
                    

                    $user->department_id= $documentGroup;
                    $user->user_role= $userRole;
                    $user->user_view_only = $viewonly_user;
                    $user->user_permission= $userPermission;
                    $user->user_form_permission= $formPermission;
                    $user->user_workflow_permission= $wfPermission;
                    $user->user_login_expiry= $loginExp;
                    $user->user_created_by= Auth::user()->username;
                    $departmentIds = explode(',',$user->department_id);
                    if ($user->save()) {
                        // insert department and users table
                        if(Input::get('u-type') != 'super_admin'):
                            foreach($departmentIds as $id):
                                $value = array('users_id'=>$user->id,
                                               'department_id'=>$id);
                                DB::table('tbl_users_departments')->insert($value);
                            endforeach;
                        endif;
                        // Save in audits
                        $userValue = Auth::user()->username;
                        // Get save action message
                        $actionMsg = Lang::get('language.save_action_msg');
                        $actionDes = $this->docObj->stringReplace($this->actionName,$user->username,$userValue,$actionMsg);
                        $result = (new AuditsController)->log(Auth::user()->username, $user->username, 'Add',$actionDes);
                        
                        if($result > 0) {                    
                            echo "<div class='alert alert-success alert-sty'>User '". Input::get('name') ."' ".Lang::get('language.add_success_msg_lang')." </div>";
                        exit();
                        } else {
                            echo Lang::get('language.logfile_issue_msg_lang');
                            exit;
                        }
                        
                    } else {
                        echo '<div class="alert alert-danger alert-sty">'.Lang::get('language.add_user_err_msg_lang').'</div>';
                        exit();
                    }
                }           
            }
        }
        else {
            return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }

    public function duplication()
    {
        if (Auth::user()) {
            $name= Input::get('name');
            $duplicateEntry= Users::where('username', '=', $name )->exists();
            if($duplicateEntry){
                //exists
                $array = array('status'=>'true');
                return json_encode($array);
            }else{
                // no data
                $array = array('status'=>'false');
                return json_encode($array);
            }

        } 
    }

    // Checking email duplication
    public function emailDuplication()
   {
       if(@Input::get('username'))
       {
           if (Users::where('email', '=', Input::get('emialId'))->where('username', '!=', @Input::get('username'))->exists())
           {echo 'true';exit();}

       }
       else
       {
           if (Users::where('email', '=', Input::get('emialId'))->exists())
           {echo 'true';exit();}

       }
       
   }

    public function delete()
    {
        if (Auth::user()) {
            $id= Input::get('id');
            // checking user already logged in or not(ajax call when click on delete button) 
            $loginStatus = Users::where('id',Input::get('id'))->select('login_status')->get();
            if($loginStatus[0]->login_status == Session::get('user_status_Inactive')){
                $user= Users:: find($id);
                if ($user->delete())
                {  
                    // delete existing data 
                    DB::table('tbl_users_departments')->where('users_id',$id)->delete(); 
                    // Save in audits
                    $userValue = Auth::user()->username;
                    // Get delete action message
                    $actionMsg = Lang::get('language.delete_action_msg');
                    $actionDes = $this->docObj->stringReplace($this->actionName,$user->username,$userValue,$actionMsg);           
                    $result = (new AuditsController)->log(Auth::user()->username, $user->username, 'Delete',$actionDes);
                    if($result > 0) {
                        echo json_encode("User '". $user->username ."' ".Lang::get('language.delete_success_msg_lang')." ");
                        exit();
                    } else {
                        echo json_encode(Lang::get('language.logfile_issue_msg_lang'));
                        exit;
                    }
                    
                }
            }else{
                echo json_encode('false');exit;
            }


        } else {
            return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }

    public function unlock()
    {
        if (Auth::user()) {
            $id= Input::get('id');
            $username= Input::get('name');
            // checking user already logged in or not(ajax call when click on logout button) 
            $records= Users::find($id);
            $records->user_lock_status = 0;
            if ($records->save()) 
            {     
                // Save in audits
                $userValue = Auth::user()->username;
                $actionMsg = Lang::get('language.unlocked_action_msg');
                $actionDes = $this->docObj->stringReplace($this->actionName,$username,$userValue,$actionMsg);
                $result = (new AuditsController)->stacklog(Auth::user()->username,$id,'Users', 'Unlocked',$actionDes);
                if($result) {                    
                    echo $username .Lang::get('language.account_unlock_msg_lang');
                } else {
                    echo Lang::get('language.logfile_issue_msg_lang');
                }                    
            } else {                
                echo Lang::get('language.try_again_msg_lang');
            }
        } else {
            return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }

    public function logout()
    {
        if (Auth::user()) {
            $id= Input::get('id');
            $username= Input::get('name');
            // checking user already logged in or not(ajax call when click on logout button) 
            $records= Users::find($id);
            $records->login_status = 0;
            if ($records->save()) 
            {    
                // Save in audits
                $userValue = Auth::user()->username;
                $actionMsg = Lang::get('language.forcefullySignedOut_action_msg');
                $actionDes = $this->docObj->stringReplace($this->actionName,$username,$userValue,$actionMsg);                  
                $result = (new AuditsController)->stacklog(Auth::user()->username,$id,'Users', 'Logout',$actionDes);
                if($result) {                    
                    echo $username .Lang::get('language.sign_out_success_msg_lang');
                } else {
                    echo Lang::get('language.logfile_issue_msg_lang');
                }                    
            } else {                
                echo Lang::get('language.try_again_updation_error_lang');
            }
        } else {
            return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }

    // Show user required messages
    public function getUserMessages(){
        // For input field date
        if(Input::get('which_message') == 'date_setting_msg'){
            echo '<ul>
                <li><span class="fa fa-star text-yellow"></span> ' .Lang::get('language.setting_a_date').'</li>
                </ul>';
        }
    }
    public function getUserAvailable() {
        $status = 0;
        $msg    = '';
        $userid = Input::get('delegate_user');
        if($userid==null || $userid=='' || $userid==0) {
            $msg = 'Invalid User....';
        }
        else {
            $userdata = DB::table('tbl_users')->select('user_full_name','delegate_to_date','delegate_from_date')
                            ->where('id','=',$userid)
                            ->first();
            if($userdata) {
                if($userdata->delegate_from_date!=null && $userdata->delegate_to_date!=null) {
                  //Now check the user is available now
                  $user_delegate_from_date = new Carbon($userdata->delegate_from_date);
                  $user_delegate_to_date   = new Carbon($userdata->delegate_to_date);
                  $today                   = new Carbon();
                  if($today>=$user_delegate_from_date && $today<=$user_delegate_to_date) {
                    $msg = 'User '.$userdata->user_full_name.' is not available from '.$userdata->delegate_from_date.' to '.$userdata->delegate_to_date;
                  }
                  else {
                    $status = 1;
                  }
                }
                else {
                  $status = 1;
                }
            }
            else {
                $msg = 'Invalid User....';
            }
        }

        echo json_encode(array('msg'=>$msg,'status'=>$status));
    }
}/*<--END-->*/
