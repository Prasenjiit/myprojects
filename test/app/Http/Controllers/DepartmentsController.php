<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ApplicationLogController;
use App\Http\Requests;
use Auth;
use View;
use Validator;
use App\Mylibs\Common;
use App\Users as Users;
use App\DepartmentsModel as DepartmentsModel;
use App\DocumentsModel as DocumentsModel;
use App\AuditsModel as AuditModel;
use App\DocumentTypesModel as DocumentTypesModel;
use App\StacksModel as StacksModel;
use Input;
use Session;
use DB;
use Lang;

class DepartmentsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        Session::put('menuid', '3');
        $this->middleware(['auth', 'user.status']);

        // Define common variable
        $this->actionName = 'Department';
        $this->docObj     = new Common(); // class defined in app/mylibs

    }
    
    public function index()
    {   
        if (Auth::user()) {
            Session::put('menuid', '3');
            $data['docType'] = DocumentTypesModel::orderBy('document_type_order', 'ASC')->get();
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            return View::make('pages/departments/index')->with($data);
        } else {
            return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }

    // keep this function as common in document type
    /*public function documentsList(Request $request,$id)
    {   
        if (Auth::user()) {
            DB::enableQueryLog();

            $query = DB::table('tbl_documents_columns')->join('tbl_documents','tbl_documents.document_id','=','tbl_documents_columns.document_id')->SELECT ('*',
            DB::raw('group_concat( tbl_documents_columns.document_column_value) AS document_column_value'),  
            DB::raw('group_concat( tbl_documents_columns.document_column_name) AS document_column_name'))->groupBy('tbl_documents_columns.document_id');
            $data['deptName'] = DepartmentsModel::select('department_name')->where('department_id',$id)->get();
            if(Session::get('search_documentsIds'))
               $query->whereIn('tbl_documents.document_id',Session::get('search_documentsIds'));
            $data['dglist'] = $query->get();
            Session::put('doclist', $id);
            $queries = DB::getQueryLog();
            $last_query = end($queries);

            if(Auth::user()->user_role == '2' || Auth::user()->user_role == '3'){
                Session::get('auth_user_dep_ids') = explode(',',Auth::user()->department_id);
                $data['deptApp'] = DepartmentsModel::select('department_id','department_name')->whereIn('department_id',Session::get('auth_user_dep_ids'))->orderBy('created_at', 'DESC')->get();
            }else{
                $data['deptApp'] = DepartmentsModel::select('department_id','department_name')->orderBy('created_at', 'DESC')->get();
            }

            $data['stckApp'] = StacksModel::select('stack_id','stack_name')->orderBy('created_at', 'DESC')->get();
            $data['doctypeApp'] = DocumentTypesModel::select('document_type_id','document_type_name')->orderBy('created_at', 'DESC')->get();
            $data['records'] = DB::table('tbl_settings')->first();

            return View::make('pages/departments/doclist')->with($data);
        } else {
            return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }
     // keep this function as common in document type
    public function documentsListview()
    {
        if (Auth::user()) {
            Session::put('menuid', '0');
            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;
            $doclistid = Session::get('doclist');
            $deptid[] = $doclistid;
            // Get document list
            $query = DB::table('tbl_documents');
            $query->whereRaw('FIND_IN_SET('.$doclistid.',department_id)')->orderBy('updated_at', 'DESC')->get();
            // if(Session::get('search_documentsIds'))
            //    $query->whereIn('document_id',Session::get('search_documentsIds'));

            $data['dglist'] = $query->get();

            // Expanding dglits with required datas
            foreach($data['dglist'] as $val):
                //$val->document_type_columns = DB::table('tbl_documents_columns')->select('document_column_name','document_column_value')->where('document_id',$val->document_id)->get();
                $val->document_type_columns = DB::table('tbl_documents_columns')
                ->select('tbl_documents_columns.document_column_name','tbl_documents_columns.document_column_value')
                ->leftJoin('tbl_document_types_columns','tbl_document_types_columns.document_type_column_id','=','tbl_documents_columns.document_type_column_id')
                ->where('tbl_documents_columns.document_id',$val->document_id)
                ->orderby('tbl_document_types_columns.document_type_id','ASC')
                ->orderby('tbl_document_types_columns.document_type_column_order','ASC')
                ->get();
    
                // Get documentTypes
                $val->documentTypes = DB::table('tbl_document_types')->select(DB::raw('GROUP_CONCAT(document_type_name) AS document_type_names'))->whereIn('document_type_id',explode(',',$val->document_type_id))->get();
                // Get stack
                $val->stacks = DB::table('tbl_stacks')->select(DB::raw('GROUP_CONCAT(stack_name) AS stack_name'))->whereIn('stack_id',explode(',',$val->stack_id))->get();
                // Get Tag words
                $val->tagwords = DB::table('tbl_tagwords')->select(DB::raw('GROUP_CONCAT(tagwords_title) AS tagwords_title'))->whereIn('tagwords_id',explode(',',$val->document_tagwords))->get();
                // Get department
                $val->department = DB::table('tbl_departments')->select(DB::raw('GROUP_CONCAT(department_name) AS department_name'))->whereIn('department_id',explode(',',$val->department_id))->get();
                
            endforeach; 

            $data['docType'] = DocumentTypesModel::orderBy('document_type_order', 'ASC')->get();
            $data['stacks'] = StacksModel::all();
            $data['depts'] = DepartmentsModel::all();

            if(Auth::user()->user_role == '2' || Auth::user()->user_role == '3'){
                Session::get('auth_user_dep_ids') = explode(',',Auth::user()->department_id);
                $data['deptApp'] = DepartmentsModel::select('department_id','department_name')->whereIn('department_id',Session::get('auth_user_dep_ids'))->orderBy('created_at', 'DESC')->get();
            }else{
                $data['deptApp'] = DepartmentsModel::select('department_id','department_name')->orderBy('created_at', 'DESC')->get();
            }

            $data['stckApp'] = StacksModel::select('stack_id','stack_name')->orderBy('created_at', 'DESC')->get();
            $data['doctypeApp'] = DocumentTypesModel::select('document_type_id','document_type_name')->orderBy('created_at', 'DESC')->get();
            $data['records'] = DB::table('tbl_settings')->first();

            Session::forget('doclist');

            return View::make('pages/departments/doclistview')->with($data);
        } else {
            return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }*/

    /*public function documentsList(Request $request, $id)
    {
        if (Auth::user()) {
            $data['stckApp'] = StacksModel::select('stack_id','stack_name')->orderBy('created_at', 'DESC')->get();
            $data['deptApp'] = DepartmentsModel::select('department_id','department_name')->orderBy('created_at', 'DESC')->get();
            $data['doctypeApp'] = DocumentTypesModel::select('document_type_id','document_type_name')->orderBy('created_at', 'DESC')->get();

            $data['stacks'] = StacksModel::all();
            $data['deptName'] = DepartmentsModel::select('department_name')->where('department_id',$id)->get();
            $data['docType'] = DocumentTypesModel::orderBy('document_type_order', 'ASC')->get();
            $deptid[] = $id;
            $query = DB::table('tbl_documents');
            $query->whereIn('department_id', $deptid)->orderBy('updated_at', 'DESC')->get();
            $data['dglist']     =   $query->get();

            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;

            return View::make('pages/departments/doclist')->with($data);
        } else {
            return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }*/

    
    public function departmentsList()
    {
        if (Auth::user()) 
        {
            $user_permission=Auth::user()->user_permission;
            $data['docType'] = DocumentTypesModel::orderBy('document_type_order', 'ASC')->get();   
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
    
            if(stristr($user_permission,"view")){
                switch(Auth::user()->user_role)
                {
                    case Session::get('user_role_super_admin'):
                    {
                        $data['dglist']= DepartmentsModel::orderBy('created_at', 'DESC')->get();
                        return View::make('pages/departments/list')->with($data);
                    }
                    break;
                    case Session::get('user_role_group_admin'):
                    {
                        $data['dglist']= DepartmentsModel::whereIn('department_id', Session::get('auth_user_dep_ids'))->get();
                        return View::make('pages/departments/list')->with($data);
                    }
                    break;
                    case Session::get('user_role_regular_user'):
                    case Session::get('user_role_private_user'):
                    {
                        $data['dglist']= DepartmentsModel::whereIn('department_id', Session::get('auth_user_dep_ids'))->get();
                        return View::make('pages/departments/list')->with($data);
                    }
                    break;
                }    
            }
            else{
                echo '<div class="alert alert-danger alert-sty">'.Lang::get('language.dept_no_permission_msg').'</div>';
                exit();
            }
        }
        else 
        {
            return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }
    public function save(Request $request, $id)
    {
        if (Auth::user()) {

            if ($id) {
                $name= Input::get('name');
                $documentGroup= DepartmentsModel:: find($id);
                $documentGroup->department_name= Input::get('name');
                $documentGroup->department_description= Input::get('description');
                $documentGroup->updated_at= date('Y-m-d h:i:s');
                $documentGroup->department_modified_by= Auth::user()->username;

                if ($documentGroup->save()) {

                    // Save in audits
                    $user = Auth::user()->username;
    
                    // Get update action message
                    $actionMsg = Lang::get('language.update_action_msg');
                    $actionDes = $this->docObj->stringReplace($this->actionName,$name,$user,$actionMsg);
                    $result = (new AuditsController)->dprtmntlog(Auth::user()->username,$id,'Departments', 'Edit',$actionDes);
                    if($result > 0) { 
                        $msg = Lang::get('language.edit_success_msg');
                        $msg = str_replace('$object_name',Input::get('name'), $msg);               
                        Session::flash('flash_message_edit',Lang::get('language.Department').$msg);
                        Session::flash('alert-class', 'alert alert-success alert-sty');
                        return redirect('departments');
                    } else {
                        Session::flash('flash_message_edit', Lang::get('language.logfile_issue_msg_lang'));
                        Session::flash('alert-class', 'alert alert-danger alert-sty');
                        return redirect('departments');
                    }
                    
                } else {
                    Session::flash('flash_message_edit', Lang::get('language.dept_cant_edit_msg'));
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('departments');
                }
                
            } else {
                $validators= Validator:: make(
                    $request-> all(),
                    [
                    'name'=> 'required'
                    ]
                );
                if ($validators->passes()) {
                    $name= Input::get('name');
                    //Duplicate entry checking
                    $duplicateEntry= DepartmentsModel::where('department_name', '=', $name)->get();

                    if(count($duplicateEntry) > 0)
                    {
                        echo '<div class="alert alert-danger alert-sty">'. $name.Lang::get('language.already_db_msg').'</div>';
                        exit();
                    } else {
                        //get last entry department_order
                        $last_order = DB::table('tbl_departments')->select('department_order')->orderBy('department_order','DESC')->first();
                        if($last_order)
                        {
                            $next_order = $last_order->department_order+1;
                        }
                        else
                        {
                            $next_order = 1;
                        }
                        $documentGroup= new DepartmentsModel;
                        $documentGroup->department_name= $name;
                        $documentGroup->department_description= Input::get('description');
                        $documentGroup->created_at= date('Y-m-d h:i:s');
                        $documentGroup->department_created_by= Auth::user()->username;
                        $documentGroup->department_order=$next_order;
                        if ($documentGroup->save()) { 
                            // Save in audits
                            $user = Auth::user()->username;

                            $actionMsg = Lang::get('language.save_action_msg');
                            $actionDes = $this->docObj->stringReplace($this->actionName,$name,$user,$actionMsg);                        
                            $result = (new AuditsController)->dprtmntlog(Auth::user()->username,$documentGroup->department_id,'Departments', 'Add',$actionDes);
                            if($result > 0) {
                                $msg = Lang::get('language.add_success_msg');
                                $msg = str_replace('$object_name',Input::get('name'), $msg);
                                echo "<div class='alert alert-success alert-sty'>".Lang::get('language.Department').$msg."</div>";
                                exit();
                            } else {
                                echo Lang::get('language.logfile_issue_msg_lang');
                                exit;
                            }
                            
                        } else {
                            echo '<div class="alert alert-danger alert-sty">'.Lang::get('language.dept_cant_add_msg').'</div>';
                            exit();
                        }
                    }
                    
                } else {
                    echo '<div class="alert alert-danger alert-sty">'.Lang::get('language.dept_fill_correct_msg').'</div>';
                    exit();
                }
            }
            
        } else {
            return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }
    public function edit(Request $request, $id)
    {
        if (Auth::user()) {
                /*<--Common datas-->*/
                $data['docType'] = DocumentTypesModel::orderBy('document_type_order', 'ASC')->get();
                
                $data['stckApp'] = $this->docObj->common_stack();
                $data['deptApp'] = $this->docObj->common_dept();
                $data['doctypeApp'] = $this->docObj->common_type();
                $data['records'] = $this->docObj->common_records();
                /*<---->*/

                $data['datas']= DepartmentsModel:: find($id);
            return View::make('pages/departments/edit')->with($data);
        } else {
            return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }

    public function check()
    {
        if (Auth::user()) {
            $id= Input::get('id');
            $dept=Input::get('docGroup');
            $count_user = DB::table('tbl_users_departments')->where('department_id',$id)->count();
            if($count_user > 0){
                $msg = Lang::get('language.dept_not_delete_msg');
                $msg = str_replace('$dept',$dept, $msg);
                echo json_encode($msg);
            }
            else if($count_user == 0){
               return $this->delete($id);
            }
        }
    }
    public function delete()
    {
        if (Auth::user()) {
            $id= Input::get('id');
            $dept=Input::get('docGroup');
            $documentGroup= DepartmentsModel:: find($id);
            if ($documentGroup->delete())
            {    
                // Save in audits
                $user = Auth::user()->username;
                
                $actionMsg = Lang::get('language.delete_action_msg');
                $actionDes = $this->docObj->stringReplace($this->actionName,$documentGroup->department_name,$user,$actionMsg);           
                $result = (new AuditsController)->dprtmntlog(Auth::user()->username,Input::get('id'),'Departments', 'Delete',$actionDes);
                  if($result > 0) {
                    $msg = Lang::get('language.delete_success_msg');
                    $msg = str_replace('$object_name', $documentGroup->department_name, $msg);
                    echo json_encode(Lang::get('language.Department').$msg);
                    exit();
                } else {
                    echo json_encode(Lang::get('language.logfile_issue_msg_lang'));
                    exit;
                }
                
            }

        } else {
            return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }
    public function duplication()
    {
        if (Auth::user()) {
            $name= Input::get('name');
            $editId= Input::get('editId');
            $oldVal= Input::get('oldVal');
            if($editId > 0)
            {
                $duplicateEntry= DepartmentsModel::where('department_name', '=', $name )->where('department_name', '!=', $oldVal)->get();
            }
            else{
                $duplicateEntry= DepartmentsModel::where('department_name', '=', $name )->get();   
            }

            if(count($duplicateEntry) > 0 )
            {                
                echo json_encode('<div class="parsley-errors-list filled" id="dp-inner">'. $name. Lang::get('language.already_db_msg') .'</div>');
                exit();
            } else {
                echo json_encode('1');
                exit;
            }
        } else {
            return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }

     public function load_department_users()
    {   
          $data = array(); 
          $data['status'] = 1;
          $departments = (Input::get('departments'))?Input::get('departments'):array(0);
          $data['department_users'] = $this->docObj->department_users_list($departments);
          $data['departments'] =$departments;
          return json_encode($data);
    }
    public function rowReorderDept(Request $request)
    {
        $newval = Input::get('newval');
        //echo 'new:';print_r($newval);
        $oldval = Input::get('oldval');
        //echo 'old:';print_r($oldval);
        $name = Input::get('name');
        $id = Input::get('id');
        //echo 'name:';print_r($name);
        //update row order
        $count = count($newval);

        if($count)
        {
            for($i=0;$i<$count;$i++) {
                DB::table('tbl_departments')->where('department_id',$id[$i])->update(['department_order'=>$newval[$i]]);
                echo $name[$i].' new position '.$newval[$i];echo "</br>";
            }
            
        }
    }
}