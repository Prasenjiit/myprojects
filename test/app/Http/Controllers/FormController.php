<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;
use View;
use Validator;
use Input;
use DB;
use Session;
use App\Mylibs\Common;
use App\FormModel as FormModel;
use App\WorkflowsModel as WorkflowsModel;

class FormController extends Controller
{
  public function __construct()
    {   
        Session::put('menuid', '14');
        $this->middleware(['auth', 'user.status']);

        // Set common variable
        $this->docObj     = new Common(); // class defined in app/mylibs
        $this->docObj->common_workflow();
        $this->docObj->common_forms();
        $this->docObj->get_workflow_notification();
    }
    public function forms()
    {
        if (Auth::user()) {
          Session::put('menuid', '14');
            $user_permission=Auth::user()->user_permission;
            $user_form_permission = Auth::user()->user_form_permission;
            
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;
            $this->docObj->common_forms();

            $data['dglist'] = DB::table('tbl_forms')
            ->select('tbl_forms.form_id','tbl_forms.form_name','tbl_forms.form_description','tbl_forms.form_created_by','tbl_forms.created_at')
            ->orderBy('tbl_forms.form_id','DESC')
            ->groupBy('tbl_forms.form_id')
            ->get();
            foreach ($data['dglist'] as $value) {
              
              $value->created_by = DB::table('tbl_users')->select('user_full_name')->where('id',$value->form_created_by)->get();
              //form_privileges
              $value->form_privileges = DB::table('tbl_form_privileges')->where('form_id',$value->form_id)->get();
            }
           
            return view::make('pages/forms/list')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function form_adv_search()
    {
      if (Auth::user()) {
        Session::put('menuid', '14');
        $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;
            // CHECK WETHER WHERE IS "AND" or "OR".
            if(Input::get('search_option') == 'AND'){
                $queryWhere    = 'where';
                $queryWhereIn  = 'whereIn';
                $querywhereBetween = 'whereBetween';
                Session::put('form_search_option',Input::get('search_option'));
            }else{
                // search_option="OR"
                $queryWhere    = 'orWhere';
                $queryWhereIn  = 'orWhereIn';
                $querywhereBetween = 'whereBetween';
                Session::put('form_search_option',Input::get('search_option'));
            }
              $query = DB::table('tbl_form_responses')
              ->select('user_id','form_id','form_name','form_description','created_at','form_response_unique_id','form_assigned_to','response_activity_name','updated_at','response_activity_date','document_file_name','form_response_value','form_response_file_size','form_response_id');
        if(Input::get('form_name'))
        {
          Session::put('form_search_form_name',Input::get('form_name'));
          $query->$queryWhereIn('form_id',Input::get('form_name'));
        }
        else
        {
          Session::forget('form_search_form_name');
        }
        if(Input::get('assigned_to'))
        {
          Session::put('form_search_assigned_to',Input::get('assigned_to'));
          $query->$queryWhere('form_assigned_to',Input::get('assigned_to'));
        }
        else
        {
          Session::forget('form_search_assigned_to');
        }
        if(Input::get('workflow_id'))
        {
          Session::put('form_search_workflow_id',Input::get('workflow_id'));
          $query->$queryWhere('resp_doc_workflow_id',Input::get('workflow_id'));
        }
        else
        {
          Session::forget('form_search_workflow_id');
        }
        if(Input::get('activity_id'))
        {
          Session::put('form_search_activity_id',Input::get('activity_id'));
          $query->$queryWhere('response_activity_id',Input::get('activity_id'));
        }
        else
        {
          Session::forget('form_search_activity_id');
        }
        if(Input::get('created_by'))
        {
          Session::put('form_search_created_by',Input::get('created_by'));
          $query->$queryWhere('user_id',Input::get('created_by'));
        }
        else
        {
          Session::forget('form_search_created_by');
        }
        if(Input::get('submitted_date_from') != "")
        {
          Session::put('form_search_submitted_date_from',Input::get('submitted_date_from'));
          $query->$queryWhere('created_at','>=',Input::get('submitted_date_from'));
        }
        elseif(Input::get('submitted_date_to') != "")
        {
          Session::put('form_search_submitted_date_to',Input::get('submitted_date_to'));
          $query->$queryWhere('created_at','<=',Input::get('submitted_date_to'));
        }
        elseif((Input::get('submitted_date_from') != "") && (Input::get('submitted_date_to') != ""))
        {
          Session::put('form_search_submitted_date_from',Input::get('submitted_date_from'));
          Session::put('form_search_submitted_date_to',Input::get('submitted_date_to'));
          $query->querywhereBetween('created_at', [Input::get('submitted_date_from'), Input::get('submitted_date_to')]);
        }
        else if((Input::get('submitted_date_from') == "") && (Input::get('submitted_date_to') == ""))
        {
          Session::forget('form_search_submitted_date_from');
          Session::forget('form_search_submitted_date_to');
        }
        $data['dglist'] = $query->orderBy('created_at','DESC')->groupBy('form_response_unique_id')->distinct()->get();
        
        //content search working(if any text enter in the content search text box)
            
              $section = 'forms';//identify from which section either document or form
              $comb = Input::get('searchformat');
              $keyword = Input::get('content_srchtxt');
              Session::put('form_content_search',$keyword);
              Session::put('form_content_search_comb',$comb);
              if(($comb != "") && ($keyword!= ""))
              { 
                $attached = Input::get('attached');
                Session::put('form_content_search_attach',$attached);
                if($attached == 'on')//
                {  
                  $keyword = ltrim($keyword);
                  $keyword = rtrim($keyword);
                  return app('App\Http\Controllers\DocumentsController')->contentSearch($data['dglist'],$comb,$keyword,$section);
                } 
                else
                {
                  Session::forget('form_content_search_attach');
                  $data['dglist'] = DB::table('tbl_form_responses')->where('form_response_selected', 'like', '%' . $keyword . '%')->orderBy('created_at','DESC')->groupBy('form_response_unique_id')->distinct()->get();
                }       
              }
        foreach ($data['dglist'] as $value) {
              $value->created_by = DB::table('tbl_users')->select('user_full_name')->where('id',$value->user_id)->first();
              //Assigned to
              $value->assigned_to = DB::table('tbl_users')->select('user_full_name')->where('id',$value->form_assigned_to)->first();
              //form_privileges
              $value->form_privileges = DB::table('tbl_form_privileges')->where('form_id',$value->form_id)->get();
              //To check document has workfowhistory
              $value->hasWorkfowHistory = DB::table('tbl_workflow_histories')->where('document_workflow_object_id',$value->form_response_unique_id)->exists();
            }

        $data['view'] = 'form_adv_search';
        return view::make('pages/forms/details')->with($data);
      }
      else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function form_details($form_id)
    {
        if (Auth::user()) 
        {
          Session::put('menuid', '14');
            $user_permission=Auth::user()->user_permission;
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            

            $query = DB::table('tbl_form_responses')
            ->leftjoin('tbl_wf_operation as wf_op','tbl_form_responses.form_response_unique_id','=','wf_op.wf_object_id')
            ->leftjoin('tbl_wf_states as stages','stages.id','=','wf_op.current_stage')
            ->leftjoin('tbl_wf as wf','wf.id','=','stages.workflow_id')
            ->select('user_id',
              'tbl_form_responses.form_id',
              'tbl_form_responses.form_name',
              'tbl_form_responses.form_description',
              'tbl_form_responses.created_at',
              'tbl_form_responses.form_response_unique_id',
              'tbl_form_responses.form_assigned_to',
              'tbl_form_responses.response_activity_name',
              'tbl_form_responses.updated_at',
              'tbl_form_responses.response_activity_date',
              'wf_op.current_stage',
              'stages.state',
              'wf.workflow_name')
            ->where('wf_op.wf_object_type','=','form')
            ->where('tbl_form_responses.form_id',$form_id)
            ->groupBy('tbl_form_responses.form_response_unique_id');
            switch (Auth::user()->user_role) 
            {
              case Session::get("user_role_private_user")://private user
              case Session::get("user_role_regular_user")://regular user
                $query->where(function ($query1) use($form_id) {
                    $query1->where('form_assigned_to',Auth::user()->id)
                        ->where('form_id','=',$form_id);
                })->orWhere(function($query1) use($form_id) {
                    $query1->where('user_id',Auth::user()->id)
                        ->where('form_id','=',$form_id);
                });
                $data['dglist'] = $query
                ->groupBy('form_response_unique_id')
                ->orderBy('created_at','DESC')
                ->get();
              break;
              case Session::get("user_role_super_admin")://super admin
                $data['dglist'] = $query
                ->groupBy('form_response_unique_id')
                ->orderBy('created_at','DESC')
                ->get();
              break;
              case Session::get("user_role_group_admin")://group admin
              $auth_dep_users = DB::table('tbl_users_departments')
              ->select('users_id')
              ->whereIn('department_id',Session::get('auth_user_dep_ids'))->get();
              //users under the department.
              foreach ($auth_dep_users as $value) 
              {
                $auth_dep_users_array[] = $value->users_id;
              }
                $q = DB::table('tbl_form_responses')
                ->join('tbl_users_departments', 'tbl_form_responses.form_assigned_to', '=', 'tbl_users_departments.users_id')
                ->select('tbl_form_responses.user_id','tbl_form_responses.form_id','tbl_form_responses.form_name','tbl_form_responses.form_description','tbl_form_responses.created_at','tbl_form_responses.form_response_unique_id','tbl_form_responses.form_assigned_to','tbl_form_responses.response_activity_name','tbl_form_responses.updated_at','tbl_form_responses.response_activity_date') 
                ->whereIn('tbl_users_departments.department_id',Session::get('auth_user_dep_ids'));
                //orwhere query
        				$q->where(function ($query) use($auth_dep_users_array,$form_id) {
        				    $query->whereIn('tbl_form_responses.form_assigned_to',$auth_dep_users_array)
        				        ->where('tbl_form_responses.form_id','=',$form_id);
        				})->orWhere(function($query) use($auth_dep_users_array,$form_id) {
        				    $query->whereIn('tbl_form_responses.user_id',$auth_dep_users_array)
        				        ->where('tbl_form_responses.form_id','=',$form_id);
        				});


                $data['dglist'] = $q->groupBy('tbl_form_responses.form_response_unique_id')
                ->orderBy('tbl_form_responses.created_at','DESC')
                ->get();
              break;
            }
            foreach ($data['dglist'] as $value) {
              $value->created_by = DB::table('tbl_users')->select('user_full_name')->where('id',$value->user_id)->first();
              
              //To check document has workfowhistory
              $value->hasWorkfowHistory = DB::table('tbl_workflow_histories')->where('document_workflow_object_id',$value->form_response_unique_id)->exists();
              //form_privileges
              $value->form_privileges = DB::table('tbl_form_privileges')->where('form_id',$value->form_id)->get();
            }
            $data['form_privileges'] = DB::table('tbl_form_privileges')->where('form_id',$form_id)->get();
            // echo '<pre>';
            // print_r($data['form_privileges']);
            
            // exit();
            $data['form_id'] = $form_id;
            $data['response'] = Input::get('response');
            return view::make('pages/forms/details')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }


     public function form_details_old($form_id)
    {
        if (Auth::user()) {
          $user_permission=Auth::user()->user_permission;
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;
            $query = DB::table('tbl_form_responses')
            ->leftjoin('tbl_wf_operation as wf_op','tbl_form_responses.form_response_unique_id','=','wf_op.wf_object_id')
            ->leftjoin('tbl_wf_states as stages','stages.id','=','wf_op.current_stage')
            ->leftjoin('tbl_wf as wf','wf.id','=','stages.workflow_id')
            ->select('user_id',
              'tbl_form_responses.form_id',
              'tbl_form_responses.form_name',
              'tbl_form_responses.form_description',
              'tbl_form_responses.created_at',
              'tbl_form_responses.form_response_unique_id',
              'tbl_form_responses.form_assigned_to',
              'tbl_form_responses.response_activity_name',
              'tbl_form_responses.updated_at',
              'tbl_form_responses.response_activity_date',
              'wf_op.current_stage',
              'stages.state',
              'wf.workflow_name')
            ->where('wf_op.wf_object_type','=','form')
            ->where('tbl_form_responses.form_id',$form_id)
            ->groupBy('tbl_form_responses.form_response_unique_id');
            switch (Auth::user()->user_role) 
            {
              case Session::get("user_role_private_user")://private user
              case Session::get("user_role_regular_user")://regular user
                $query->where(function ($query1) use($form_id) {
                    $query1->where('form_assigned_to',Auth::user()->id)
                        ->where('form_id','=',$form_id);
                })->orWhere(function($query1) use($form_id) {
                    $query1->where('user_id',Auth::user()->id)
                        ->where('form_id','=',$form_id);
                });
                $data['dglist'] = $query
                ->groupBy('form_response_unique_id')
                ->orderBy('created_at','DESC')
                ->get();
              break;
              case Session::get("user_role_super_admin")://super admin
                $data['dglist'] = $query
                ->groupBy('form_response_unique_id')
                ->orderBy('created_at','DESC')
                ->get();
              break;
              case Session::get("user_role_group_admin")://group admin
              $auth_dep_users = DB::table('tbl_users_departments')
              ->select('users_id')
              ->whereIn('department_id',Session::get('auth_user_dep_ids'))->get();
              //users under the department.
              foreach ($auth_dep_users as $value) 
              {
                $auth_dep_users_array[] = $value->users_id;
              }
                $q = DB::table('tbl_form_responses')
                ->join('tbl_users_departments', 'tbl_form_responses.form_assigned_to', '=', 'tbl_users_departments.users_id')
                ->select('tbl_form_responses.user_id','tbl_form_responses.form_id','tbl_form_responses.form_name','tbl_form_responses.form_description','tbl_form_responses.created_at','tbl_form_responses.form_response_unique_id','tbl_form_responses.form_assigned_to','tbl_form_responses.response_activity_name','tbl_form_responses.updated_at','tbl_form_responses.response_activity_date') 
                ->whereIn('tbl_users_departments.department_id',Session::get('auth_user_dep_ids'));
                //orwhere query
                $q->where(function ($query) use($auth_dep_users_array,$form_id) {
                    $query->whereIn('tbl_form_responses.form_assigned_to',$auth_dep_users_array)
                        ->where('tbl_form_responses.form_id','=',$form_id);
                })->orWhere(function($query) use($auth_dep_users_array,$form_id) {
                    $query->whereIn('tbl_form_responses.user_id',$auth_dep_users_array)
                        ->where('tbl_form_responses.form_id','=',$form_id);
                });


                $data['dglist'] = $q->groupBy('tbl_form_responses.form_response_unique_id')
                ->orderBy('tbl_form_responses.created_at','DESC')
                ->get();
              break;
            }
            foreach ($data['dglist'] as $value) {
              $value->created_by = DB::table('tbl_users')->select('user_full_name')->where('id',$value->user_id)->first();
              
              //To check document has workfowhistory
              $value->hasWorkfowHistory = DB::table('tbl_workflow_histories')->where('document_workflow_object_id',$value->form_response_unique_id)->exists();
              //form_privileges
              $value->form_privileges = DB::table('tbl_form_privileges')->where('form_id',$value->form_id)->get();
            }
            $data['form_privileges'] = DB::table('tbl_form_privileges')->where('form_id',$form_id)->get();
            // echo '<pre>';
            // print_r($data['form_privileges']);
            
            // exit();
            $data['form_id'] = $form_id;
            $data['response'] = Input::get('response');
            return view::make('pages/forms/details')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    /*View FORM*/


    public function view_form()
    {
        if (Auth::user()) 
        {
          Session::put('menuid', '14');
            $user_id= Auth::user()->id;
            $user_role= Auth::user()->user_role;
            $action = (Input::get('action'))?Input::get('action'):'add';
            $formid = (Input::get('formid'))?Input::get('formid'):0;
            $form_response_unique_id = (Input::get('uq_id'))?Input::get('uq_id'):0;
            $columns = (Input::get('columns'))?Input::get('columns'):1;
            $data['action'] = $action;
            $data['formid'] = $formid;
            $data['form_response_unique_id'] = $form_response_unique_id;
            $data['form_id'] = $formid;
            $where = array('form_id' => $formid);
            
            $result = DB::table('tbl_forms')->where($where)->first();
            $form_name = $form_description  = $activity_id  = '';
            if($result)
            {
                $form_name         =   ($result->form_name)?$result->form_name:'';
                $form_description  =   ($result->form_description)?$result->form_description:'';
            }
            $data['form_name'] = $form_name;
            $data['form_description'] = $form_description;
            $data['columns'] = $columns;

      $inputs = array();
      if($action == 'add')
      {
        $result = FormModel::form_inputs($formid);
        foreach ($result as $key => $value) 
        {
          $row = array();   
          $row['is_input_type'] = ($value->is_input_type)?$value->is_input_type:0;
          $row['input_type'] = ($value->form_input_type_value)?$value->form_input_type_value:'text';
          $row['input_label'] = ($value->form_input_title)?$value->form_input_title:'';
          $row['input_id'] = ($value->form_input_id)?$value->form_input_id:0;
          $row['input_type_id'] = ($value->form_input_type)?$value->form_input_type:0;
          $row['input_type_name'] = ($value->form_input_type_name)?$value->form_input_type_name:'';
          $row['is_required'] = ($value->form_input_require)?$value->form_input_require:0;
          $row['is_options'] = ($value->is_options)?$value->is_options:0;
          $row['multiple_files'] = ($value->form_input_file_multiple)?$value->form_input_file_multiple:0;
          //$row['is_required'] = ($value->is_required)?$value->is_required:0;
          $row['input_type_common'] = ($value->form_input_type_common)?$value->form_input_type_common:'text';
          
          $values = (isset($value->form_input_default_value))?$value->form_input_default_value:''; 
          $row['input_values'] = $values;

          $choices = ($value->form_Input_options)?@unserialize($value->form_Input_options):array(); 
          $choices = ($choices)?$choices:array();
          $row['input_choices'] = $choices;


          $edit_permission_users = ($value->edit_permission_users)?explode(',',$value->edit_permission_users):array();

          $row['form_edit_permision'] = (in_array($user_id, $edit_permission_users) || ($user_role == Session::get("user_role_super_admin")))?1:0;

          $view_permission_users = ($value->view_permission_users)?explode(',',$value->view_permission_users):array();

          $row['form_view_permission'] = (in_array($user_id, $view_permission_users) || ($user_role == Session::get("user_role_super_admin")))?1:0;

          $row['attached_files'] = array();
          $row['form_response_id'] = 0;

          $column = 'col-xs-12 col-sm-12 col-md-12'; 
          /*if($columns == 1)
          {
            $column = $value->col_1; 
          }
          else if($columns == 2)
          {
            $column = $value->col_2; 
          }
          else if($columns == 3)
          {
            $column = $value->col_3; 
          }*/
          $row['column'] = $column;

          $inputs[] = $row;
        }
      }
      else if(($action == 'edit') || ($action == 'resubmit'))
      {
        $result = FormModel::form_submit_edit($formid,$form_response_unique_id);
        
        foreach ($result as $key => $value) 
        {
          $row = array();   
          $row['is_input_type'] = ($value->is_input_type)?$value->is_input_type:0;
          $row['input_type'] = ($value->form_input_type_value)?$value->form_input_type_value:'text';
          $row['input_label'] = ($value->form_input_title)?$value->form_input_title:'';
          $row['input_id'] = ($value->form_input_id)?$value->form_input_id:0;
          $row['input_type_id'] = ($value->form_input_type)?$value->form_input_type:0;
          $row['input_type_name'] = ($value->form_input_type_name)?$value->form_input_type_name:'';
          $row['is_required'] = ($value->form_input_require)?$value->form_input_require:0;
          $row['is_options'] = ($value->is_options)?$value->is_options:0;
          $row['multiple_files'] = ($value->form_input_file_multiple)?$value->form_input_file_multiple:0;
          //$row['is_required'] = ($value->is_required)?$value->is_required:0;
          $row['input_type_common'] = ($value->form_input_type_common)?$value->form_input_type_common:'text';
          
          $values = $value->form_response_value; 
          $row['input_values'] = $values;

          $row['form_edit_permision'] = ($value->edit_permission_users)?explode(',',$value->edit_permission_users):array();
          $row['form_view_permission'] = ($value->view_permission_users)?explode(',',$value->view_permission_users):array();
          
          $choices = array();
          if($row['is_options'])
          {
            $choices = ($value->form_response_value)?@unserialize($value->form_response_value):array(); 
            $choices = ($choices)?$choices:array();
          }
          
          $row['input_choices'] = $choices;
          
          $row['selected_item'] = ($value->form_response_selected)?explode(',',$value->form_response_selected):array(); 

          $attach = $value->document_file_name;

          $edit_permission_users = ($value->edit_permission_users)?explode(',',$value->edit_permission_users):array();

          $row['form_edit_permision'] = (in_array($user_id, $edit_permission_users) || ($user_role == Session::get("user_role_super_admin")))?1:0;

          $view_permission_users = ($value->view_permission_users)?explode(',',$value->view_permission_users):array();

          $row['form_view_permission'] = (in_array($user_id, $view_permission_users) || ($user_role == Session::get("user_role_super_admin")))?1:0;

          $row['attached_files'] = ($attach)?explode(',',$attach):array(); 
          $row['form_response_id'] = $value->form_response_id;
          
          $column = 'col-xs-12 col-sm-12 col-md-12'; 
          /*if($columns == 1)
          {
            $column = $value->col_1; 
          }
          else if($columns == 2)
          {
            $column = $value->col_2; 
          }
          else if($columns == 3)
          {
            $column = $value->col_3; 
          }*/
          $row['column'] = $column;
          $inputs[] = $row;
          
        }
      }
      $data['inputs']=  $inputs;

            //return view::make('pages/forms/form')->with($data);
            return view::make('pages/forms/view_form')->with($data);
        }
        else {
            return redirect('')->withErrors("Please login")->withInput();
        }

    }
    /*END View FORM*/
    public function deleteform()
    {
      if (Auth::user()) 
      {
        $form_id = Input::get('formid');
        $form_name = Input::get('form_name');
        if (DB::table('tbl_form_responses')->where('form_id', '=', $form_id)->exists()) 
        {
          echo "Sorry, there are entries under form '".$form_name."'";
        }
        else{
          DB::table('tbl_forms')->where('form_id',$form_id)->delete();
          DB::table('tbl_form_inputs')->where('form_id',$form_id)->delete();
          echo "Form '".$form_name."' deleted successfully";
        }
      }
      else {
          return redirect('')->withErrors("Please login")->withInput();
      }
    }
    public function deleteSingleSubmittedform()
    {
      if (Auth::user()) 
      {
        $form_id = Input::get('form_id');
        $form_name = Input::get('form_name');
        $form_response_unique_id = Input::get('form_response_unique_id');
        $delete = DB::table('tbl_form_responses')->where('form_id',$form_id)->where('form_response_unique_id',$form_response_unique_id)->delete();
        if($delete)
        {
          echo "1";
        }
        else
        {
          echo "0";
        }
      }
      else {
          return redirect('')->withErrors("Please login")->withInput();
      }
    }
    public function formAttachments(Request $request)
    {
        $input = Input::all();
        
        $element_id = Input::get('element_id');
        
        $unique_id = Input::get('unique');
        $label = Input::get('label');

        $mime  = Input::file('file')->getMimeType();
        $size  = filesize(Input::file('file')); 
        //if($mime=="application/pdf" || $mime=="image/jpeg" || $mime=="image/png" || $mime=="application/msword" || $mime=="application/vnd.openxmlformats-officedocument.wordprocessingml.document" || $mime=="application/vnd.ms-excel" || $mime=="application/zip" || $mime=="application/vnd.ms-office" || $mime=="text/plain" || $mime=="image/tiff" || $mime=="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" || $mime=="audio/mp3"){
        $destinationPath  = config('app.base_path'); // upload path
        $file        = Input::file('file');
        $fileName = "atch_".uniqid();
        $fileRandName = $fileName.'.' .$file->getClientOriginalExtension();
        $without_extension = pathinfo($fileRandName, PATHINFO_FILENAME);
        $originalfileName = basename(Input::file('file')->getClientOriginalName());
        $size  = filesize(Input::file('file')); 
        //Move Uploaded File
        $destinationPath = config('app.base_path'); // upload path
        $file->move($destinationPath,$fileRandName);
        //}
        $output = array(
          "original_name" => $originalfileName,
          "random_name" => $fileRandName,
          "rand"=> $without_extension,
          "element_id" => $element_id,
          "unique_id" => $unique_id,
          "label" => $label,
          "size" => $size
          );
        
          echo json_encode($output);  
    }
    public function deleteAttachments(Request $request)
    {
      $file = Input::get('name');
      //Delete
      if($file)
      {
        $destinationPath = config('app.base_path'); // upload path
        unlink($destinationPath."/".$file);
      } 
    }
    public function deleteAttached()
    {
      $file = Input::get('name');
      $id= Input::get('id');
      $original_name = Input::get("original_name");
      //Delete
      if($file)
      {
        $files = DB::table('tbl_form_responses')->select('document_file_name','form_response_value','form_response_selected')->where('form_response_id',$id)->first();
        $document_file_name_array = explode(',', $files->document_file_name);
        $form_response_value_array = explode(',', $files->form_response_value);
        $form_response_selected_array = explode(',', $files->form_response_selected);
        $remove_document_file_name_array = array_values(array_diff($document_file_name_array, [$file]));
        $remove_form_response_value_array = array_values(array_diff($form_response_value_array, [$original_name]));
        $remove_form_response_selected_array = array_values(array_diff($form_response_selected_array, [$original_name]));
        $update = DB::table('tbl_form_responses')->where('form_response_id',$id)->update(['document_file_name'=>implode(',', $remove_document_file_name_array),'form_response_value'=>implode(',', $remove_form_response_value_array),'form_response_selected'=>implode(',', $remove_form_response_selected_array)]);
        if($update)
        {
          $destinationPath = config('app.base_path'); // upload path
          unlink($destinationPath."/".$file);
        }
        return 1;
      } 
    }
    public function getattachDetails(Request $request)
    {
      $resp_id = Input::get('id');
      //details
      if($resp_id)
      {
        $details = DB::table('tbl_form_responses')->where('form_response_id',$resp_id)->first();
        return json_encode($details);
      } 
    }
    //old function before form resubmit
   public function saveFormValues_old(Request $request)
    {
      if (Auth::user()) 
      {
        $handler = $this->docObj;
        $form_id = (Input::get('form_id'))?Input::get('form_id'):0;
        /*assigned user name*/
        $assign_to = DB::table('tbl_form_users')->join('tbl_users','tbl_form_users.form_user_id','=','tbl_users.id')->select('tbl_users.user_full_name','tbl_form_users.form_user_id')->where('tbl_form_users.form_id',$form_id)->first();
        if($assign_to)
        {
          $assign_user = $assign_to->user_full_name;
        }
        else
        {
          $assign_user = '';
          $form_assigned_to=0;
        }
        /*end*/
        $form_name = (Input::get('form_name'))?Input::get('form_name'):0;
        $user_id = Auth::user()->id;
        $form_responses_uique = $user_id.'-'.str_random(3).'-'.time();
        $form_descrition = (Input::get('form_description'))?Input::get('form_description'):0;
        $timestamp = date("Y-m-d H:i:s");
        $result = FormModel::form_inputs($form_id);

        //edit

        if(Input::get('form_response_unique_id'))
        {
          $form_response_unique_id = Input::get('form_response_unique_id');
          $resp_id = Input::get('resp_id');
          $status = (Input::get('form_reject_status'))?Input::get('form_reject_status'):0;
          $form_responses_uique = $form_response_unique_id;
          $selected = "";
          $files = "";
          $v = array();

          foreach ($result as $key => $value) 
          {
            $edit_permission_users = ($value->edit_permission_users)?explode(',',$value->edit_permission_users):array();
            if(in_array($user_id,$edit_permission_users) || (Auth::user()->user_role == 1))
            {
            $input_value = Input::get($value->form_input_id);
            $val[$value->form_input_id] = $input_value;  
            $row = array();
            
            if(!$value->is_input_type)
            {
              $form_response_value = "";
              $selected = "";
              $form_response_file="";
              $form_response_file_size="";
            }
            else if($value->is_options)//check is option ==1
            {
              $response_value_array = (is_array($val[$value->form_input_id]))?$val[$value->form_input_id]:array();
              $form_Input_options = @unserialize($value->form_Input_options);
              $form_Input_options = ($form_Input_options)?$form_Input_options:array();
              $form_response_value = array();
              $selected1 = array();

              foreach($form_Input_options as $key1 => $val1)
              {
                $row1 = array();
                $row1['label'] = $val1['label'];
                $row1['sel'] = (in_array($val1['label'], $response_value_array))?1:0;
                $form_response_value[] = $row1;
              }

              foreach($form_response_value as $a)
              {
                if($a['sel'] == 1)
                {
                  array_push($selected1,$a['label']);
                }
              }
              
              $form_response_value = serialize($form_response_value);
              $selected = implode(',', $selected1);
              $form_response_file="";
              $form_response_file_size="";
            }
            
            else if($value->form_input_type_name == 'File')
            {
              $form_response_file = Input::get('randname'.$value->form_input_id);
              $form_response_value = Input::get('name'.$value->form_input_id);
              $form_response_file_size = Input::get('size'.$value->form_input_id);
              if($form_response_file_size == "")
              {
                $files = DB::table('tbl_form_responses')->select('form_response_id')->where('form_response_unique_id',$form_response_unique_id)->where('form_input_type',11)->orderBy('form_response_id')->get();
              }
             
              if(($form_response_file != null) ||($form_response_file != ""))
              {
                $form_response_file = implode(',',$form_response_file);
              }
             
              if(($form_response_value != null) || ($form_response_value != ""))
              {
                $form_response_value = implode(',',$form_response_value);
              }
              
              if(($form_response_file_size != null) || ($form_response_file_size !=""))
              {
                $form_response_file_size = implode(',',$form_response_file_size);
              }
              $selected = "";
            }
            
            else
            {
              $form_response_value = $val[$value->form_input_id];
              $selected = "";
              $form_response_file="";
              $form_response_file_size="";
            }

            $row['form_response_value'] = $form_response_value;
            $row['form_response_selected'] = $selected;
            $row['document_file_name'] = $form_response_file;
            $row['form_response_file_size'] = $form_response_file_size;
            $row['created_at'] = $timestamp;

            $v[]=$row;
            }
          }

          $resp_id = array_unique($resp_id);
          $resp_id = array_values($resp_id);
          $c = array_combine($resp_id, $v);
          echo "<pre>";
         print_r($c);
         echo "</pre>";
          if($files)
          {
            foreach ($c as $key => $value)
            {
              $results = DB::table('tbl_form_responses')->where('form_response_id', $key)->where('form_response_unique_id',$form_response_unique_id)->where('form_input_type','!=',11)
             ->update(['form_response_value'=>$value['form_response_value'],
              'form_response_selected'=>$value['form_response_selected'],
              'document_file_name'=>$value['document_file_name'],
              'form_response_file_size'=>$value['form_response_file_size'],
              'created_at'=>$value['created_at']
              ]);
            }
          }
          else
          {
            foreach ($c as $key => $value) 
            {
              $results = DB::table('tbl_form_responses')->where('form_response_id', $key)->where('form_response_unique_id',$form_response_unique_id)
             ->update(['form_response_value'=>$value['form_response_value'],
              'form_response_selected'=>$value['form_response_selected'],
              'document_file_name'=>$value['document_file_name'],
              'form_response_file_size'=>$value['form_response_file_size'],
              'created_at'=>$value['created_at']
              ]);
            }
          }

          if($result)
          {
            //check is it a reject status form?
            if($status == 1)
            {
              $addWF = FormModel::addToWorkflow($form_id,$form_name,$form_responses_uique,$handler);
            }
            Session::flash('flash_message_success', "Form '". $form_name ."' edited and submitted to '".$assign_user."' successfully.");
            Session::flash('alert-class', 'alert alert-success alert-sty');
            return back();
          }
        }
         /*END EDIT*/     
        //save

        foreach ($result as $key => $value) 
        {
          $input_value = Input::get($value->form_input_id);
          $val[$value->form_input_id] = $input_value;  
          $row = array();
          $row['form_id'] = $form_id;
          $row['user_id'] = Auth::user()->id;
          $row['form_name'] = $form_name;
          $row['form_description'] = $form_descrition;
          $row['form_input_title'] =$value->form_input_title;

          if(!$value->is_input_type)
            {
              $form_response_value = "";
              $selected = "";
              $form_response_file="";
              $form_response_file_size="";
            }
          else if($value->is_options)//check is option ==1
          {
            $response_value_array = (is_array($val[$value->form_input_id]))?$val[$value->form_input_id]:array();
            $form_Input_options = @unserialize($value->form_Input_options);
            $form_Input_options = ($form_Input_options)?$form_Input_options:array();
            $form_response_value = array();
            $selected1 = array();

            foreach($form_Input_options as $key1 => $val1)
            {
              $row1 = array();
              $row1['label'] = $val1['label'];
              $row1['sel'] = (in_array($val1['label'], $response_value_array))?1:0;
              $form_response_value[] = $row1;
            }

            foreach($form_response_value as $a)
            {
              if($a['sel'] == 1)
              {
                array_push($selected1,$a['label']);
              }
            }
            
            $form_response_value = serialize($form_response_value);
            $selected = implode(',', $selected1);
            $form_response_file="";
            $form_response_file_size="";
          }
          

            else if($value->form_input_type_name == 'File')
            {
             $form_response_file = Input::get('randname'.$value->form_input_id);
             $form_response_value = Input::get('name'.$value->form_input_id);
             $form_response_file_size = Input::get('size'.$value->form_input_id);
             //$form_input_title =Input::get('elementlabel'.$value->form_input_id);
             //$form_input_id = Input::get('elementuniqueid'.$value->form_input_id);
             if($form_response_file)
             {
                $form_response_file = implode(',',$form_response_file);
             }
             if($form_response_value)
             {
                $form_response_value = implode(',',$form_response_value);
                $selected = $form_response_value;
              }
              if($form_response_file_size)
             {
                $form_response_file_size = implode(',',$form_response_file_size);
             }
            }

            else
            {
              $form_response_value = $val[$value->form_input_id];
              $selected = $form_response_value;
              $form_response_file="";
              $form_response_file_size="";
            }

          $row['form_response_value'] = $form_response_value;
          $row['form_response_selected'] = $selected;
          $row['document_file_name'] = $form_response_file;
          $row['form_response_file_size'] = $form_response_file_size;
          $row['form_input_type'] = $value->form_input_type;
          $row['form_input_id'] = $value->form_input_id;
          $row['created_at'] = $timestamp;
          $row['form_response_unique_id'] = $form_responses_uique;
          $row['form_assigned_to'] = (isset($assign_to->form_user_id))?$assign_to->form_user_id:NULL;
          $results = DB::table('tbl_form_responses')->insert($row);
        

        }
        if($result)
        {
          $addWF = FormModel::addToWorkflow($form_id,$form_name,$form_responses_uique,$handler);
           //notification to assigned users when form submit
           //Check assigned user  
          $form_users = DB::table('tbl_form_users as tfu')
          ->where('tfu.form_id',$form_id)->get();
          if(count($form_users))
          {
            $recipients=array();
            foreach($form_users as $fkey => $fvalue) 
            {
              $recipients[]=$fvalue->form_user_id;
            }
            //check if receipients has the delegation?
            if($recipients)  
            {
              $today = date('Y-m-d');
              $today=date('Y-m-d', strtotime($today));
              $notification = array();
              $notification['type']='form';
              $notification['priority']='1';
              $notification['details']='';
              $notification['sender']=Auth::user()->id;
              //For Notification to assigned users only
              $notification['title']= 'Form "'.$form_name.'" submitted by '.Auth::user()->user_full_name;
              $notification['details']='';
              $notification['link']=URL('form_details/'.$form_id).'?response='.$form_responses_uique;
              $notification['recipients']=$recipients;
              $this->docObj->add_notification($notification);
              foreach ($recipients as $user) 
              {

                $delegated_details = DB::table('tbl_users')
                ->select('delegate_user','delegate_from_date','delegate_to_date','user_full_name')
                ->where('id',$user)
                ->first();
                if($delegated_details->delegate_user)
                {
                  
                  $delegate_from_date = date('Y-m-d', strtotime($delegated_details->delegate_from_date));
                  $delegate_to_date = date('Y-m-d', strtotime($delegated_details->delegate_to_date));
                  if (($today > $delegate_from_date) && ($today < $delegate_to_date))
                  {
                    $notification['type']='form';
                    $notification['priority']='1';
                    $notification['details']='';
                    $notification['sender']=Auth::user()->id;
                    //For Notification to delegated users only
                    $notification['title']= 'Delegated Notification from '.$delegated_details->user_full_name.' - Form "'.$form_name.'" submitted by '.Auth::user()->user_full_name;
                    $notification['link']=URL('form_details/'.$form_id).'?response='.$form_responses_uique;
                    $notification['details']='Delegated Notifications';
                    $notification['recipients']=array($delegated_details->delegate_user);
                    $this->docObj->add_notification($notification);
                  }
                  else
                  {
                  
                  }
                }
              }
            }
          }
          
          Session::flash('flash_message_success', "Form '". $form_name ."' submitted successfully");
          Session::flash('alert-class', 'alert alert-success alert-sty');
          return back();
        }
      }
      else 
      {
        return redirect('')->withErrors("Please login")->withInput();
      }
    }
  public function saveFormValues(Request $request)
    {
      if (Auth::user()) 
      {
        $handler = $this->docObj;
        $form_id = (Input::get('form_id'))?Input::get('form_id'):0;
        /*assigned user name*/
        $assign_to = DB::table('tbl_form_users')->join('tbl_users','tbl_form_users.form_user_id','=','tbl_users.id')->select('tbl_users.user_full_name','tbl_form_users.form_user_id')->where('tbl_form_users.form_id',$form_id)->first();
        if($assign_to)
        {
          $assign_user = $assign_to->user_full_name;
        }
        else
        {
          $assign_user = '';
          $form_assigned_to=0;
        }
        /*end*/
        $form_name = (Input::get('form_name'))?Input::get('form_name'):0;
        $user_id = Auth::user()->id;
        $form_responses_uique = $user_id.'-'.str_random(3).'-'.time();
        $form_descrition = (Input::get('form_description'))?Input::get('form_description'):0;
        $timestamp = date("Y-m-d H:i:s");
        $result = FormModel::form_inputs($form_id);

        //edit

        if(Input::get('form_response_unique_id') && (Input::get('action')=='edit'))
        {
          $form_response_unique_id = Input::get('form_response_unique_id');
          $resp_id = Input::get('resp_id');
          $status = (Input::get('form_reject_status'))?Input::get('form_reject_status'):0;
          $form_responses_uique = $form_response_unique_id;
          $selected = "";
          $files = "";
          $v = array();

          foreach ($result as $key => $value) 
          {
            $edit_permission_users = ($value->edit_permission_users)?explode(',',$value->edit_permission_users):array();
            if(in_array($user_id,$edit_permission_users) || (Auth::user()->user_role == 1))
            {
            $input_value = Input::get($value->form_input_id);
            $val[$value->form_input_id] = $input_value;  
            $row = array();
            
            if($value->is_options)//check is option ==1
            {
              $response_value_array = (is_array($val[$value->form_input_id]))?$val[$value->form_input_id]:array();
              $form_Input_options = @unserialize($value->form_Input_options);
              $form_Input_options = ($form_Input_options)?$form_Input_options:array();
              $form_response_value = array();
              $selected1 = array();

              foreach($form_Input_options as $key1 => $val1)
              {
                $row1 = array();
                $row1['label'] = $val1['label'];
                $row1['sel'] = (in_array($val1['label'], $response_value_array))?1:0;
                $form_response_value[] = $row1;
              }

              foreach($form_response_value as $a)
              {
                if($a['sel'] == 1)
                {
                  array_push($selected1,$a['label']);
                }
              }
              
              $form_response_value = serialize($form_response_value);
              $selected = implode(',', $selected1);
              $form_response_file="";
              $form_response_file_size="";
            }
            
            else if($value->form_input_type_name == 'File')
            {
              $form_response_file = Input::get('randname'.$value->form_input_id);
              $form_response_value = Input::get('name'.$value->form_input_id);
              $form_response_file_size = Input::get('size'.$value->form_input_id);
              if($form_response_file_size == "")
              {
                $files = DB::table('tbl_form_responses')->select('form_response_id')->where('form_response_unique_id',$form_response_unique_id)->where('form_input_type',11)->orderBy('form_response_id')->get();
              }
             
              if(($form_response_file != null) ||($form_response_file != ""))
              {
                $form_response_file = implode(',',$form_response_file);
              }
             
              if(($form_response_value != null) || ($form_response_value != ""))
              {
                $form_response_value = implode(',',$form_response_value);
              }
              
              if(($form_response_file_size != null) || ($form_response_file_size !=""))
              {
                $form_response_file_size = implode(',',$form_response_file_size);
              }
              $selected = "";
            }
            
            else
            {
              $form_response_value = $val[$value->form_input_id];
              $selected = "";
              $form_response_file="";
              $form_response_file_size="";
            }

            $row['form_response_value'] = $form_response_value;
            $row['form_response_selected'] = $selected;
            $row['document_file_name'] = $form_response_file;
            $row['form_response_file_size'] = $form_response_file_size;
            $row['created_at'] = $timestamp;

            $v[]=$row;
            }
          }

          $resp_id = array_unique($resp_id);
          $resp_id = array_values($resp_id);
          $c = array_combine($resp_id, $v);
          if(Input::get('action')=='edit')
          {
            if($files)
            {
              foreach ($c as $key => $value)
              {
                $results = DB::table('tbl_form_responses')->where('form_response_id', $key)->where('form_response_unique_id',$form_response_unique_id)->where('form_input_type','!=',11)
               ->update(['form_response_value'=>$value['form_response_value'],
                'form_response_selected'=>$value['form_response_selected'],
                'document_file_name'=>$value['document_file_name'],
                'form_response_file_size'=>$value['form_response_file_size'],
                'created_at'=>$value['created_at']
                ]);
              }
            }
            else
            {
              foreach ($c as $key => $value) 
              {
                $results = DB::table('tbl_form_responses')->where('form_response_id', $key)->where('form_response_unique_id',$form_response_unique_id)
               ->update(['form_response_value'=>$value['form_response_value'],
                'form_response_selected'=>$value['form_response_selected'],
                'document_file_name'=>$value['document_file_name'],
                'form_response_file_size'=>$value['form_response_file_size'],
                'created_at'=>$value['created_at']
                ]);
              }
            }
            if($result)
            {
              //check is it a reject status form?
              if($status == 1)
              {
                $addWF = FormModel::addToWorkflow($form_id,$form_name,$form_responses_uique,$handler);
              }
              Session::flash('flash_message_success', "Form '". $form_name ."' edited and submitted to '".$assign_user."' successfully.");
              Session::flash('alert-class', 'alert alert-success alert-sty');
              return back();
            }
          }
          
        }

        //save

        foreach ($result as $key => $value) 
        {
          $input_value = Input::get($value->form_input_id);
          $val[$value->form_input_id] = $input_value;  
          $row = array();
          $row['form_id'] = $form_id;
          $row['user_id'] = Auth::user()->id;
          $row['form_name'] = $form_name;
          $row['form_description'] = $form_descrition;
          $row['form_input_title'] =$value->form_input_title;

          if($value->is_options)//check is option ==1
          {
            $response_value_array = (is_array($val[$value->form_input_id]))?$val[$value->form_input_id]:array();
            $form_Input_options = @unserialize($value->form_Input_options);
            $form_Input_options = ($form_Input_options)?$form_Input_options:array();
            $form_response_value = array();
            $selected1 = array();

            foreach($form_Input_options as $key1 => $val1)
            {
              $row1 = array();
              $row1['label'] = $val1['label'];
              $row1['sel'] = (in_array($val1['label'], $response_value_array))?1:0;
              $form_response_value[] = $row1;
            }

            foreach($form_response_value as $a)
            {
              if($a['sel'] == 1)
              {
                array_push($selected1,$a['label']);
              }
            }
            
            $form_response_value = serialize($form_response_value);
            $selected = implode(',', $selected1);
            $form_response_file="";
            $form_response_file_size="";
          }
          

            else if($value->form_input_type_name == 'File')
            {
             $form_response_file = Input::get('randname'.$value->form_input_id);
             $form_response_value = Input::get('name'.$value->form_input_id);
             $form_response_file_size = Input::get('size'.$value->form_input_id);
             //$form_input_title =Input::get('elementlabel'.$value->form_input_id);
             //$form_input_id = Input::get('elementuniqueid'.$value->form_input_id);
             if($form_response_file)
             {
                $form_response_file = implode(',',$form_response_file);
             }
             if($form_response_value)
             {
                $form_response_value = implode(',',$form_response_value);
                $selected = $form_response_value;
              }
              if($form_response_file_size)
             {
                $form_response_file_size = implode(',',$form_response_file_size);
             }
            }

            else
            {
              $form_response_value = $val[$value->form_input_id];
              $selected = $form_response_value;
              $form_response_file="";
              $form_response_file_size="";
            }

          $row['form_response_value'] = $form_response_value;
          $row['form_response_selected'] = $selected;
          $row['document_file_name'] = $form_response_file;
          $row['form_response_file_size'] = $form_response_file_size;
          $row['form_input_type'] = $value->form_input_type;
          $row['form_input_id'] = $value->form_input_id;
          $row['created_at'] = $timestamp;
          $row['form_response_unique_id'] = $form_responses_uique;
          $row['form_assigned_to'] = (isset($assign_to->form_user_id))?$assign_to->form_user_id:NULL;
          $results = DB::table('tbl_form_responses')->insert($row);

        }
        if($result)
        {
          $addWF = FormModel::addToWorkflow($form_id,$form_name,$form_responses_uique,$handler);
           //notification to assigned users when form submit
           //Check assigned user  
          $form_users = DB::table('tbl_form_users as tfu')
          ->where('tfu.form_id',$form_id)->get();
          if(count($form_users))
          {
            $recipients=array();
            foreach($form_users as $fkey => $fvalue) 
            {
              $recipients[]=$fvalue->form_user_id;
            }
            //check if receipients has the delegation?
            if($recipients)  
            {
              $today = date('Y-m-d');
              $today=date('Y-m-d', strtotime($today));
              $notification = array();
              $notification['type']='form';
              $notification['priority']='1';
              $notification['details']='';
              $notification['sender']=Auth::user()->id;
              //For Notification to assigned users only
              $notification['title']= 'Form "'.$form_name.'" submitted by '.Auth::user()->user_full_name;
              $notification['details']='';
              $notification['link']=URL('form_details/'.$form_id).'?response='.$form_responses_uique;
              $notification['recipients']=$recipients;
              $this->docObj->add_notification($notification);
              foreach ($recipients as $user) 
              {

                $delegated_details = DB::table('tbl_users')
                ->select('delegate_user','delegate_from_date','delegate_to_date','user_full_name')
                ->where('id',$user)
                ->first();
                if($delegated_details->delegate_user)
                {
                  
                  $delegate_from_date = date('Y-m-d', strtotime($delegated_details->delegate_from_date));
                  $delegate_to_date = date('Y-m-d', strtotime($delegated_details->delegate_to_date));
                  if (($today > $delegate_from_date) && ($today < $delegate_to_date))
                  {
                    $notification['type']='form';
                    $notification['priority']='1';
                    $notification['details']='';
                    $notification['sender']=Auth::user()->id;
                    //For Notification to delegated users only
                    $notification['title']= 'Delegated Notification from '.$delegated_details->user_full_name.' - Form "'.$form_name.'" submitted by '.Auth::user()->user_full_name;
                    $notification['link']=URL('form_details/'.$form_id).'?response='.$form_responses_uique;
                    $notification['details']='Delegated Notifications';
                    $notification['recipients']=array($delegated_details->delegate_user);
                    $this->docObj->add_notification($notification);
                  }
                  else
                  {
                  
                  }
                }
              }
            }
          }
          if(input::get('action')=='resubmit')
          {
            Session::flash('flash_message_success', "Form '". $form_name ."' resubmitted successfully");
          }
          else
          {
            Session::flash('flash_message_success', "Form '". $form_name ."' submitted successfully");
          }
          
          Session::flash('alert-class', 'alert alert-success alert-sty');
          return back();
        }
      }
      else 
      {
        return redirect('')->withErrors("Please login")->withInput();
      }
    }

    public function saveFormValuesAdd(Request $request)
    {
      if (Auth::user()) 
      {
        $handler = $this->docObj;
        $form_id = (Input::get('form_id'))?Input::get('form_id'):0;
       
        $form_name = (Input::get('form_name'))?Input::get('form_name'):0;
        $user_id = Auth::user()->id;
        $form_responses_uique = $user_id.'-'.str_random(3).'-'.time();
        $form_descrition = (Input::get('form_description'))?Input::get('form_description'):0;
        $timestamp = date("Y-m-d H:i:s");
        $action = (Input::get('action'))?Input::get('action'):'add';
        $result = FormModel::form_inputs($form_id);
        $timestamp = date("Y-m-d H:i:s");
        if($action == 'add')
        {
          foreach ($result as $key => $value) 
        {
          $row = array();   
          $is_input_type = ($value->is_input_type)?$value->is_input_type:0;
          $input_type = ($value->form_input_type_value)?$value->form_input_type_value:'text';
          $form_input_title = ($value->form_input_title)?$value->form_input_title:'';
          $form_input_id = ($value->form_input_id)?$value->form_input_id:0;
          $is_options = ($value->is_options)?$value->is_options:0;
          $form_input_type = ($value->form_input_type)?$value->form_input_type:0;

          if($is_input_type)
          {
            $row = array();
            $row['form_id'] = $form_id;
            $row['user_id'] = Auth::user()->id;
            $row['form_name'] = $form_name;
            $row['form_description'] = $form_descrition;
            $row['form_input_title'] =$form_input_title;


            $form_response_value = $selected = $document_file_name = $form_response_file_size="";
            if($input_type == 'file')
            {
              $attachments_name = (Input::get('attachment_name-'.$input_type.'-'.$form_input_id))?Input::get('attachment_name-'.$input_type.'-'.$form_input_id):array();
              $form_response_value = implode(',', $attachments_name);
              $selected = $form_response_value;

              $document_file_name = (Input::get('attachment_new_name-'.$input_type.'-'.$form_input_id))?Input::get('attachment_new_name-'.$input_type.'-'.$form_input_id):array();
              $document_file_name = implode(',', $document_file_name);

              $form_response_file_size = (Input::get('attachment_size-'.$input_type.'-'.$form_input_id))?Input::get('attachment_size-'.$input_type.'-'.$form_input_id):array();
              $form_response_file_size = implode(',', $form_response_file_size);
            }
            else if($is_options)
            {
              $input_value = (Input::get($input_type.'-'.$form_input_id))?Input::get($input_type.'-'.$form_input_id):array();

              $response_value_array = (is_array($input_value))?$input_value:array();
              $form_Input_options = @unserialize($value->form_Input_options);
              $form_Input_options = ($form_Input_options)?$form_Input_options:array();
              $form_response_value = array();
              $selected1 = array();
              foreach($form_Input_options as $key1 => $val1)
              {
                $row1 = array();
                $row1['label'] = $val1['label'];
                $row1['sel'] = (in_array($val1['label'], $response_value_array))?1:0;
                $form_response_value[] = $row1;
              }

              foreach($form_response_value as $a)
              {
                if($a['sel'] == 1)
                {
                  array_push($selected1,$a['label']);
                }
              }
              
              $form_response_value = serialize($form_response_value);
              $selected = implode(',', $selected1);
              
            }
            else
            {
              $input_value = (Input::get($input_type.'-'.$form_input_id))?Input::get($input_type.'-'.$form_input_id):'';
              $form_response_value = $input_value;
              $selected = $form_response_value;
            }
            $row['form_response_value'] = $form_response_value;
            $row['form_response_selected'] = $selected;
            $row['document_file_name'] = $document_file_name;
            $row['form_response_file_size'] = $form_response_file_size;
            $row['form_input_type'] = $form_input_type;
            $row['form_input_id'] = $form_input_id;
            $row['created_at'] = $timestamp;
            $row['form_response_unique_id'] = $form_responses_uique;
            $row['form_assigned_to'] = NULL;
            $results = DB::table('tbl_form_responses')->insert($row);
          } /* END is_input_type*/
          
          
        }
         $addWF = FormModel::addToWorkflow($form_id,$form_name,$form_responses_uique,$handler);
        }

        if($action=='resubmit')
          {
            Session::flash('flash_message_success', "Form '". $form_name ."' resubmitted successfully");
          }
          else
          {
            Session::flash('flash_message_success', "Form '". $form_name ."' submitted successfully");
          }
          
          Session::flash('alert-class', 'alert alert-success alert-sty');

          /*echo "<pre>"; print_r($_POST); echo "</pre>"; exit;*/
          return back();
        
      }
    }
    public function form($form_id=0)
    {
        // checking wether user logged in or not
        if (Auth::user()) {

            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;
            $data['form_id'] = $form_id;
            $this->docObj->common_forms();
            $data['form_types'] = $types = FormModel::form_types();;
            $data['user'] = WorkflowsModel::users_list();
            $data['workflows'] = WorkflowsModel::get_workflows();
            $data['activities'] = WorkflowsModel::get_activities();
            
            $data['users'] = WorkflowsModel::get_all_users();
            return View::make('pages/forms/index')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

     public function load_form()
    {
      $action = (Input::get('action'))?Input::get('action'):'add';
      $uniqid = Input::get('uq_id');
      $form_id = (Input::get('form_id'))?Input::get('form_id'):0;
      $where = array('form_id' => $form_id);
      
      $result = DB::table('tbl_forms')->where($where)->first();
      $json = array();
      $form_name = $form_description  = $activity_id  = '';
      if($result)
      {
          $form_name         =   ($result->form_name)?$result->form_name:'';
          $form_description  =   ($result->form_description)?$result->form_description:'';
      }
      $inputs = array();
      if($action == 'add')
      {
        $result = FormModel::form_inputs($form_id);
        foreach ($result as $key => $value) 
        {
          $row = array();   
          $row['type'] = ($value->form_input_type_value)?$value->form_input_type_value:'text';
          $row['label'] = ($value->form_input_title)?$value->form_input_title:'';
          $row['input_id'] = ($value->form_input_id)?$value->form_input_id:0;
          $row['type_id'] = ($value->form_input_type)?$value->form_input_type:0;
          $row['input_type_name'] = ($value->form_input_type_name)?$value->form_input_type_name:'';
          $row['req'] = ($value->form_input_require)?$value->form_input_require:0;
          $row['is_options'] = ($value->is_options)?$value->is_options:0;
          $row['multiple'] = ($value->form_input_file_multiple)?$value->form_input_file_multiple:0;
          $row['is_required'] = ($value->is_required)?$value->is_required:0;
          $row['type_common'] = ($value->form_input_type_common)?$value->form_input_type_common:'text';
          $row['edit_per'] = ($value->edit_permission_users)?explode(',',$value->edit_permission_users):array();
          $row['view_per'] = ($value->view_permission_users)?explode(',',$value->view_permission_users):array();

          $choices = ($value->form_Input_options)?@unserialize($value->form_Input_options):array(); 
          $choices = ($choices)?$choices:array();
          $row['choices'] = $choices;

          $inputs[] = $row;
        }
      }
      else if(($action == 'edit') || ($action == 'resubmit'))
      {
        $result = FormModel::form_submit_edit($form_id,$uniqid);
        
        foreach ($result as $key => $value) 
        {
          $row = array();   
          $row['type'] = ($value->form_input_type_value)?$value->form_input_type_value:'text';
          $row['label'] = ($value->form_input_title)?$value->form_input_title:'';
          $row['input_id'] = ($value->form_input_id)?$value->form_input_id:0;
          $row['type_id'] = ($value->form_input_type)?$value->form_input_type:0;
          $row['input_type_name'] = ($value->form_input_type_name)?$value->form_input_type_name:'';
          $row['req'] = ($value->form_input_require)?$value->form_input_require:0;
          $row['is_options'] = ($value->is_options)?$value->is_options:0;
          $row['multiple'] = ($value->form_input_file_multiple)?$value->form_input_file_multiple:0;
          $row['is_required'] = ($value->is_required)?$value->is_required:0;
          $row['type_common'] = ($value->form_input_type_common)?$value->form_input_type_common:'text';
          $row['edit_per'] = ($value->edit_permission_users)?explode(',',$value->edit_permission_users):array();
          $row['view_per'] = ($value->view_permission_users)?explode(',',$value->view_permission_users):array();
          $values = $value->form_response_value; 
          $row['values'] = $values;
          
          $attach = $value->document_file_name;
          $row['files'] = $attach;

          $ids = $value->form_response_id;
          $row['res_id'] = $ids;
          $choices = array();
          if($row['is_options'])
          {
            $choices = ($value->form_response_value)?@unserialize($value->form_response_value):array(); 
            $choices = ($choices)?$choices:array();
          }
          
          $row['choices'] = $choices;
          $row['selected_item'] = ($value->form_response_selected)?explode(',',$value->form_response_selected):array();  
          $inputs[] = $row;
          
        }
      }
      
      $json['form_name']=  $form_name;
      $json['form_description']=  $form_description;
      $json['inputs']=  $inputs;
      $json['user_id'] = (String)Auth::user()->id;
      $json['user_role'] = (int)Auth::user()->user_role;
      $json['assigned_users']=  FormModel::assigned_users($form_id);
      $json['assigned_workflows']=  FormModel::assigned_workflows($form_id);
      $json['form_privilages']=  FormModel::form_privilages($form_id);
      return json_encode($json);
    }

    public function formMoreDetails()
    {
      if (Auth::user()) 
      {
            $form_id = Input::get('formid');
            $user_id = Input::get('user_id');
            $form_response_unique_id = Input::get('form_response_unique_id');
            $data['form'] = DB::table('tbl_forms')
            ->where('tbl_forms.form_id',$form_id)->first();
            $data['form_details'] = FormModel::form_responses($form_id,$form_response_unique_id);
            $data['form_privilages'] = DB::table('tbl_form_privileges')->where('form_id',$form_id)->where('privilege_key','edit')->first();
            $form_assigned_to_array = array(Auth::user()->id);
            $data['form_from_user'] = $data['form_to_user'] = $data['form_action_user']='';
            $form_owner = array();
            if($data['form_details'])
            {

              $data['form_from_user'] = DB::table('tbl_users')->select('user_full_name')->where('id',$data['form_details'][0]->user_id)->first();
              //Assigned to
              $data['form_to_user'] = DB::table('tbl_users')->select('user_full_name')->where('id',$data['form_details'][0]->form_assigned_to)->first();
              $data['form_action_user'] = DB::table('tbl_users')->select('user_full_name')->where('id',$data['form_details'][0]->response_activity_by)->first();
              $activity_to_user = $data['form_details'][0]->form_assigned_to;
              $file_name = $data['form_details'][0]->document_file_name;
              $form_owner[] = $data['form_details'][0]->user_id;
              switch (Auth::user()->user_role) 
          {
            
            case Session::get("user_role_super_admin")://super admin

              $form_owner[] = Auth::user()->id;
            break;
            case Session::get("user_role_group_admin")://group admin
            case Session::get("user_role_regular_user"):
            case Session::get("user_role_private_user"):
            $auth_dep_users = DB::table('tbl_users_departments as td')
            ->select('td.users_id')
            ->whereIn('td.department_id',Session::get('auth_user_dep_ids'))->get();
            $auth_dep_users_array = array();
            //users under the department.
            foreach ($auth_dep_users as $value) 
            {
              if($value->users_id == $activity_to_user);
              {
                $form_assigned_to_array[] = $activity_to_user;
              }
            }
            break;
          }
            }
            

            foreach ($data['form_details'] as $key => $frm) 
            {
              $data['status'] = ($frm->response_activity_name=='Reject')?1:0;
            }
            $data['form_activity'] = FormModel::form_activity();
            $data['form_assigned_to'] = $form_assigned_to_array;
            $data['form_response_unique_id'] = $form_response_unique_id;
            $data['form_response_form_id'] = $form_id;
            $data['document_file_name'] = $file_name;
            $data['form_owner'] = $form_owner;

            $where = array('activity_constant' => 'reject');
            $reject_data = DB::table('tbl_activities')->select('activity_name','activity_id')->where($where)->first();
             $data['reject_id']=  ($reject_data)?$reject_data->activity_id:0;

             $data['response_activity_id']= (isset($data['form_details'][0]->response_activity_id))?$data['form_details'][0]->response_activity_id:0;
             
             $json['status']=  1;
             $json['html']=  view::make('pages/forms/more')->with($data)->render();
             return json_encode($json);
        }
        else 
        {
             $json['status']=  1;
             $json['html']=  "Session Expired.Please Login";
             return json_encode($json);
        }
    }

    public function save_action_form()
    {
      $form_id = (Input::get('formid'))?Input::get('formid'):0;
      $form_response_unique_id = (Input::get('form_response_unique_id'))?Input::get('form_response_unique_id'):0;
      $where = array('form_id' => $form_id,'form_response_unique_id' => $form_response_unique_id);
      
      $result = DB::table('tbl_form_responses')->where($where)->first();
      $json = array();
      $form_name= $form_description  = '';
      if($result)
      {
        $update['response_activity_id'] = (Input::get('activity_id'))?Input::get('activity_id'):0;
        $update['response_activity_name'] = (Input::get('activity_name'))?Input::get('activity_name'):'';
        $update['response_activity_note'] = (Input::get('activity_note'))?Input::get('activity_note'):'';
         $update['response_activity_by'] = Auth::user()->id;
         $update['response_activity_date'] = date("Y-m-d H:i:s");
        DB::table('tbl_form_responses')->where($where)->update($update);

        if($result->user_id)  
          {
            /* For Notification Start */
          $recipients = array($result->user_id);
          $notification = array();
          $notification['type']='form';
          $notification['priority']='1';
          $notification['title']='Form "'.$result->form_name.'" status changed to "'.$update['response_activity_name'].'" by '.Auth::user()->user_full_name;
          $notification['details']='';
          $notification['link']=URL('form_details/'.$form_id).'?response='.$form_response_unique_id;
          $notification['sender']=Auth::user()->id;
          $notification['recipients']=$recipients;
          $this->docObj->add_notification($notification);
           /* For Notification END*/
         }


        $workflows = DB::table('tbl_form_workflows as tfw')->select('tfw.form_workflow_id','tfw.form_activity_id')->where('tfw.form_id',$form_id)->first();

        if($result->resp_doc_workflow_id)
        {
         
          $where = array('document_workflow_id' => $result->resp_doc_workflow_id);  

          $update = array();
          $update['action_activity'] = (Input::get('activity_id'))?Input::get('activity_id'):0;
        
        $update['action_activity_name'] = (Input::get('activity_name'))?Input::get('activity_name'):'';
        $update['action_activity_note'] = (Input::get('activity_note'))?Input::get('activity_note'):'';
         $update['action_activity_by'] = Auth::user()->id;
         $update['action_activity_date'] = date("Y-m-d H:i:s");
        DB::table('tbl_document_workflows')->where($where)->update($update);

        }

      }
      

      $json['status']=  1;
      $message = '<div class="alert alert-success text-center">Status saved successfully</div>';
      $json['message']=  $message;
      $formMoreDetails = json_decode($this->formMoreDetails());
      $json['html']=  (isset($formMoreDetails->html))?$formMoreDetails->html:'';
      return json_encode($json);
    }
    public function my_forms()
    {
      if (Auth::user()) {
          $user_permission=Auth::user()->user_permission;
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;
            return view::make('pages/forms/list_all')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function my_forms_filter()
    {
      $length       =   Input::get("length");
      $start        =   Input::get("start");
      $filter       =   Input::get('filter');
      //$type         =   Input::get('typeselect');
      $currentPage = ($start)?($start/$length)+1:1;

      \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($currentPage) {
        return $currentPage;
      });
      $search       =   (isset($_POST['search']['value']))?trim($_POST['search']['value']):'';
      $footer_search =  (isset($_POST['columns'][0]['search']['value']))?trim($_POST['columns'][0]['search']['value']):'';
      $footer_search1 =  (isset($_POST['columns'][1]['search']['value']))?trim($_POST['columns'][1]['search']['value']):'';
      $footer_search2 =  (isset($_POST['columns'][3]['search']['value']))?trim($_POST['columns'][3]['search']['value']):'';
      $footer_search3 =  (isset($_POST['columns'][4]['search']['value']))?trim($_POST['columns'][4]['search']['value']):'';
      $query = DB::table('tbl_form_responses')
      ->leftjoin('tbl_users as sender','tbl_form_responses.user_id','=','sender.id')
      ->leftjoin('tbl_wf_operation as wf_op','tbl_form_responses.form_response_unique_id','=','wf_op.wf_object_id')
      ->leftjoin('tbl_wf_states as stages','stages.id','=','wf_op.current_stage')
      ->leftjoin('tbl_wf as wf','wf.id','=','stages.workflow_id')
      ->where('wf_op.wf_object_type','=','form')
      ->select('tbl_form_responses.form_name',
        'sender.user_full_name as sender_user',
        'tbl_form_responses.created_at',
        'tbl_form_responses.form_id',
        'tbl_form_responses.form_response_unique_id',
        'tbl_form_responses.form_assigned_to',
        'tbl_form_responses.user_id',
        'tbl_form_responses.response_activity_name',
        'tbl_form_responses.response_activity_date',
        'wf_op.current_stage',
        'stages.state',
        'wf.workflow_name')
      ->groupBy('tbl_form_responses.form_response_unique_id');
      switch (Auth::user()->user_role) 
      {
        case Session::get("user_role_private_user")://private user
        case Session::get("user_role_regular_user")://regular user
          $query;
        break;
        case Session::get("user_role_super_admin")://super admin
          $query;
        break;
        case Session::get("user_role_group_admin")://group admin
        $auth_dep_users = DB::table('tbl_users_departments')
        ->select('users_id')
        ->whereIn('department_id',Session::get('auth_user_dep_ids'))->get();
        //users under the department.
        foreach ($auth_dep_users as $value) 
        {
          $auth_dep_users_array[] = $value->users_id;
        }
          $q = DB::table('tbl_form_responses')
          ->join('tbl_users_departments', 'tbl_form_responses.form_assigned_to', '=', 'tbl_users_departments.users_id')
          ->select('tbl_form_responses.user_id',
            'tbl_form_responses.form_id',
            'tbl_form_responses.form_name',
            'tbl_form_responses.created_at',
            'tbl_form_responses.form_response_unique_id',
            'tbl_form_responses.form_assigned_to',
            'tbl_form_responses.response_activity_name',
            'tbl_form_responses.updated_at',
            'tbl_form_responses.response_activity_date') 
          ->whereIn('tbl_users_departments.department_id',Session::get('auth_user_dep_ids'));


          //orwhere query
          $q->where(function ($query) use($auth_dep_users_array) {
              $query->whereIn('tbl_form_responses.form_assigned_to',$auth_dep_users_array)
                  ->get();
          })->orWhere(function($query) use($auth_dep_users_array) {
              $query->whereIn('tbl_form_responses.user_id',$auth_dep_users_array)
                  ->get();
          });


          $q->groupBy('tbl_form_responses.form_response_unique_id');
        break;
      }
      switch($filter)
      {
        case 0: //show_all_forms
          $query
          ->where('tbl_form_responses.form_assigned_to',Auth::user()->id)
          ->orWhere('tbl_form_responses.user_id',Auth::user()->id);
        break;
        case 1://form_submitted_by_me
          $query->where('tbl_form_responses.user_id',Auth::user()->id);
        break;
        case 2://form_submitted_to_me
          $query->where('tbl_form_responses.form_assigned_to',Auth::user()->id);
        break;
        default:
        $data = array();
        break;
      }
        //ajax search
      if($search){
        $column = array('tbl_form_responses.form_name','sender.user_full_name','wf.workflow_name','stages.state','tbl_form_responses.created_at','tbl_form_responses.response_activity_name','tbl_form_responses.response_activity_date');
        $query->Where(function($query1) use($column,$search) {
            foreach ($column as $key => $value) {
              $query1->orWhere($value,'LIKE','%'.$search.'%');
            }
        });
      }
        //tfoot column search
        //notification title
      if($footer_search){
        $tfoot_column1 = array('tbl_form_responses.form_name');
        $query->where(function($query1) use($tfoot_column1,$footer_search) {
          foreach ($tfoot_column1 as $key => $value) {
              $query1->orWhere($value,'LIKE','%'.$footer_search.'%');
            }
          });
      }
        //from
      if($footer_search1){
        $tfoot_column2 = array('sender.user_full_name');
        $query->where(function($query2) use($tfoot_column2,$footer_search1) {
          foreach ($tfoot_column2 as $key => $value) {
              $query2->orWhere($value,'LIKE','%'.$footer_search1.'%');
            }
          });
      }
        //stage
      if($footer_search2){
        $tfoot_column3 = array('wf.workflow_name');
        $query->where(function($query3) use($tfoot_column3,$footer_search2) {
          foreach ($tfoot_column3 as $key => $value) {
              $query3->orWhere($value,'LIKE','%'.$footer_search2.'%');
            }
          });
      }
      //wf
      if($footer_search3){
        $tfoot_column4 = array('stages.state');
        $query->where(function($query4) use($tfoot_column4,$footer_search3) {
          foreach ($tfoot_column4 as $key => $value) {
              $query4->orWhere($value,'LIKE','%'.$footer_search3.'%');
            }
          });
      }
        // Ajax order by works
        $order = (isset($_POST['order'][0]['column']))?$_POST['order'][0]['column']:3;
        $direct = (isset($_POST['order'][0]['dir']))?$_POST['order'][0]['dir']:'DESC';
        $data_item = (isset($_POST['columns'][$order]['data']))?$_POST['columns'][$order]['data']:'';
        switch($data_item)
        {
          case 'created':
          $table_column = 'tbl_form_responses.created_at';
          break;
          case 'state':
          $table_column = 'stages.state';
          break;
          case 'from':
          $table_column = 'sender.user_full_name';
          break;
          case 'form_name':
          $table_column = 'tbl_form_responses.form_name';
          break;
          case 'status':
          $table_column = 'tbl_form_responses.response_activity_name';
          break;
          case 'updated':
          $table_column = 'tbl_form_responses.response_activity_date';
          break;
          default:
          $table_column = 'tbl_form_responses.created_at';
          break;
        }
        $query->orderBy("$table_column","$direct");
              $data = $query->paginate($length);
              foreach ($data as $key => $item) {
                $item->form_privileges = DB::table('tbl_form_privileges')->where('form_id',$item->form_id)->get();
              }
              $count_all = ($data)?$data->total():0;
              $i = $start;
              $data_table = array();
                foreach ($data as $value) {
                $i++;
                $row_d = array();
                $row_d['form_name'] = $value->form_name;  
                $row_d['from'] = $value->sender_user;
                $row_d['wf'] = $value->workflow_name;
                $row_d['state'] = $value->state;
                $row_d['created'] = $value->created_at;  
                $row_d['status'] = $value->response_activity_name;
                $row_d['updated'] = $value->response_activity_date;
                $row_d['actions'] = '';
                if(Auth::user()->user_role==1 || Auth::user()->user_role==2)
                {
                  $row_d['actions'] .= '<a class="view_form_response_details" form_id="'.$value->form_id.'" form_response_unique_id="'.$value->form_response_unique_id.'" user_id="'.$value->user_id.'" data-toggle="modal"  style="cursor:pointer; padding-left:2px; padding-right:2px;"  title="More Details" ><i class="fa fa-ellipsis-v" aria-hidden="true"></i></a>';
                  $row_d['actions'] .='&nbsp;&nbsp;<a class="delete_submitted" form_id="'.$value->form_id.'" form_response_unique_id="'.$value->form_response_unique_id.'" user_id="'.$value->user_id.'" onclick="delete_single('.$value->form_id.',\''.$value->form_response_unique_id.'\',\''.$value->form_name.'\');" style="cursor:pointer; padding-left:2px; padding-right:2px; color:red;"  title="Delete Submitted Form" ><i class="fa fa-trash"></i></a>';
                }
                else
                { 
                    if($value->form_privileges[0]->privilege_key=="view")
                    {
                      $uservalue = $value->form_privileges[0]->privilege_value_user; 
                      $deptvalue = $value->form_privileges[0]->privilege_value_department;
                      $key_user_value = explode(',',$uservalue);
                      $key_dept_value = explode(',',$deptvalue);
                      $dept_id = Auth::user()->department_id;
                      $depart         = explode(',',$dept_id); 
                      $intersection   = array_intersect($depart,$key_dept_value);                                            
                      if(in_array(Auth::user()->id, $key_user_value) || count($intersection)>0) {
                      $row_d['actions'] .= '<a class="view_form_response_details" form_id="'.$value->form_id.'" form_response_unique_id="'.$value->form_response_unique_id.'" user_id="'.$value->user_id.'" data-toggle="modal"  style="cursor:pointer; padding-left:2px; padding-right:2px;"  title="More Details" ><i class="fa fa-ellipsis-v" aria-hidden="true"></i></a>';
                      } 
                    }
                    if($value->form_privileges[2]->privilege_key=="delete")
                    {
                      $uservalue = $value->form_privileges[2]->privilege_value_user; 
                      $deptvalue = $value->form_privileges[2]->privilege_value_department;
                      $key_user_value = explode(',',$uservalue);
                      $key_dept_value = explode(',',$deptvalue);
                      $dept_id = Auth::user()->department_id;
                      $depart         = explode(',',$dept_id); 
                      $intersection   = array_intersect($depart,$key_dept_value);                                            
                      if(in_array(Auth::user()->id, $key_user_value) || count($intersection)>0) {
                        $row_d['actions'] .='&nbsp;&nbsp;<a class="delete_submitted" form_id="'.$value->form_id.'" form_response_unique_id="'.$value->form_response_unique_id.'" user_id="'.$value->user_id.'" onclick="delete_single('.$value->form_id.',\''.$value->form_response_unique_id.'\',\''.$value->form_name.'\');" style="cursor:pointer; padding-left:2px; padding-right:2px; color:red;"  title="Delete Submitted Form" ><i class="fa fa-trash"></i></a>';
                      }
                    }
                }
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
     public function save_dynamic_form()
    {
        // checking wether user logged in or not
        if (Auth::user()) 
        {
                $form_id = (Input::get('form_id'))?Input::get('form_id'):0;
                $timestamp = date("Y-m-d H:i:s");
               $form_data = array();
               $form_privilages = array();
               $form_data['form_description'] = (Input::get('form_description'))?Input::get('form_description'):'';

               //form privilages get

              if(Input::get('view_authentication'))
              {
                $form_privilages[0]['privilege_key'] = 'view';
                if(Input::get('view_authentication') == 'true')
                {
                  $form_privilages[0]['privilege_status'] = '1';
                }
                else
                {
                  $form_privilages[0]['privilege_status'] = '0';
                }
              }
              if(Input::get('view_departmentid'))
              {
               $form_privilages[0]['privilege_value_department'] = implode(',',Input::get('view_departmentid'));
              }
              else
              {
                $form_privilages[0]['privilege_value_department'] = null;
              }
              if(Input::get('view_userid'))
              {
                $form_privilages[0]['privilege_value_user'] = implode(',',Input::get('view_userid'));
              }
              else
              {
                $form_privilages[0]['privilege_value_user'] = null;
              }

              if(Input::get('edit_authentication'))
              {
                $form_privilages[1]['privilege_key'] = 'edit';
                if(Input::get('edit_authentication') == 'true')
                {
                  $form_privilages[1]['privilege_status'] = '1';
                }
                else
                {
                  $form_privilages[1]['privilege_status'] = '0';
                }
              }

              if(Input::get('edit_departmentid'))
              {
               $form_privilages[1]['privilege_value_department'] = implode(',',Input::get('edit_departmentid'));
              }
              else
              {
                $form_privilages[1]['privilege_value_department'] = null;
              }
              if(Input::get('edit_userid'))
              {
               $form_privilages[1]['privilege_value_user'] = implode(',',Input::get('edit_userid'));
              }
              else
              {
                $form_privilages[1]['privilege_value_user'] = null;
              }

              if(Input::get('delete_authentication'))
              {
                $form_privilages[2]['privilege_key'] = 'delete';
                if(Input::get('delete_authentication') == 'true')
                {
                  $form_privilages[2]['privilege_status'] = '1';
                }
                else
                {
                  $form_privilages[2]['privilege_status'] = '0';
                }
              }

              if(Input::get('delete_departmentid'))
              {
               $form_privilages[2]['privilege_value_department'] = implode(',',Input::get('delete_departmentid'));
              }
              else
              {
                $form_privilages[2]['privilege_value_department'] = null;
              }
              if(Input::get('delete_userid'))
              {
               $form_privilages[2]['privilege_value_user'] = implode(',',Input::get('delete_userid'));
              }
              else
              {
                $form_privilages[2]['privilege_value_user'] = null;
              }

              if(Input::get('add_authentication'))
              {
                $form_privilages[3]['privilege_key'] = 'add';
                if(Input::get('add_authentication') == 'true')
                {
                  $form_privilages[3]['privilege_status'] = '1';
                }
                else
                {
                  $form_privilages[3]['privilege_status'] = '0';
                }
              }

              if(Input::get('add_departmentid'))
              {
               $form_privilages[3]['privilege_value_department'] = implode(',',Input::get('add_departmentid'));
              }
              else
              {
                $form_privilages[3]['privilege_value_department'] = null;
              }
              if(Input::get('add_userid'))
              {
               $form_privilages[3]['privilege_value_user'] = implode(',',Input::get('add_userid'));
              }
              else
              {
                $form_privilages[3]['privilege_value_user'] = null;
              }

               $form_data['form_name'] = (Input::get('form_name'))?Input::get('form_name'):'';
               $form_data['updated_at'] = $timestamp;
               $form_data['form_updated_by'] = Auth::user()->id;
               $where = array('form_id' => $form_id);
               $result = DB::table('tbl_forms')->where($where)->first(); 
               //update exist
               if($result)
                {
                  DB::table('tbl_forms')->where($where)->update($form_data);
                  //delete the duplication
                  DB::table('tbl_form_privileges')->where($where)->delete();
                  foreach ($form_privilages as $key => $value)
                  {
                    DB::table('tbl_form_privileges')->insert([
                        'form_id' => $form_id,
                        'privilege_key' => $value['privilege_key'],
                        'privilege_status' => $value['privilege_status'],
                        'privilege_value_user' => $value['privilege_value_user'],
                        'privilege_value_department' => $value['privilege_value_department']
                    ]);
                  }
                }
              //save fresh
                else
                { 
                    $form_data['form_created_by'] = Auth::user()->id;
                    $form_data['created_at'] = $timestamp;
                    $form_id = DB::table('tbl_forms')->insertGetId($form_data);
                    foreach ($form_privilages as $key => $value)
                    {
                    DB::table('tbl_form_privileges')->insert([
                        'form_id' => $form_id,
                        'privilege_key' => $value['privilege_key'],
                        'privilege_status' => $value['privilege_status'],
                        'privilege_value_user' => $value['privilege_value_user'],
                        'privilege_value_department' => $value['privilege_value_department']
                    ]);
                    }
                    
                }

    /* Assigned user*/
    $assigned_to = (Input::get('assigned_to'))?Input::get('assigned_to'):0;
    $where  = array('form_id' => $form_id);
    $reset = array('edit'=>0);
    $result = DB::table('tbl_form_users')->where($where)->update($reset); 
    if($assigned_to)
    { 
    $form_users = array('form_id' => $form_id,'form_user_id' => $assigned_to,'updated_at' => $timestamp,'edit' => 1);           
    $where  = array('form_id' => $form_id);
    $result = DB::table('tbl_form_users')->where($where)->first(); 
    if($result)
    {
        $where = array('id' => $result->id);
        DB::table('tbl_form_users')->where($where)->update($form_users);
                  
    }
    else
    { 
        $form_users['created_at'] = $timestamp;
        DB::table('tbl_form_users')->insert($form_users);
    }     
    }
   
    $where  = array('form_id' => $form_id,'edit' => 0);
    DB::table('tbl_form_users')->where($where)->delete();
    
    /* Assigned workflow*/  
    $workflow_id = (Input::get('workflow_id'))?Input::get('workflow_id'):0;
    $where  = array('form_id' => $form_id);
    $reset = array('edit'=>0);
    $result = DB::table('tbl_form_workflows')->where($where)->update($reset);
    if($workflow_id)
    { 
    $activity_id = (Input::get('activity_id'))?Input::get('activity_id'):0;  
    $form_users = array('form_id' => $form_id,'form_workflow_id' => $workflow_id,'form_activity_id' => $activity_id,'updated_at' => $timestamp,'edit' => 1);           
    $where  = array('form_id' => $form_id);
    $result = DB::table('tbl_form_workflows')->where($where)->first(); 
    if($result)
    {
        $where = array('id' => $result->id);
        DB::table('tbl_form_workflows')->where($where)->update($form_users);
                  
    }
    else
    { 
        $form_users['created_at'] = $timestamp;
        DB::table('tbl_form_workflows')->insert($form_users);
    }     
    }
    $where  = array('form_id' => $form_id,'edit' => 0);
    DB::table('tbl_form_workflows')->where($where)->delete();



      

  $formFields = (Input::get('formFields'))?Input::get('formFields'):array(); 
  
  $i=0;

      
  $types = FormModel::form_type_array();

  foreach ($formFields as $key => $value) 
  {
    $i++;
  $input_type = (isset($types[$value['type']]))?$types[$value['type']]->form_input_type:0;
  $is_options = (isset($types[$value['type']]))?$types[$value['type']]->is_options:0;
  $req  = (isset($value['req']))?$value['req']:0;
  $multiple  = (isset($value['multiple']))?$value['multiple']:0;
  $input_id  = (isset($value['input_id']))?$value['input_id']:0;
  $where = array('form_input_id' => $input_id);
  $inputs = array();
  $inputs['form_id'] = $form_id;
  $inputs['form_input_type'] = $input_type;
  $inputs['form_input_title'] = (isset($value['label']))?$value['label']:'';
  $inputs['form_input_default_value'] = (isset($value['defaultVal']) && $value['defaultVal'])?$value['defaultVal']:null;
  $inputs['updated_at'] = $timestamp;
  $inputs['form_input_edit'] = 1;
  $inputs['form_input_require'] = $req;
  $inputs['form_input_file_multiple'] = $multiple;
  $inputs['form_input_order'] = $i;
  $choices = (isset($value['choices']) && is_array($value['choices']))?$value['choices']:array();
  $inputs['form_Input_options'] = ($is_options)?serialize($choices):'';
  $result = DB::table('tbl_form_inputs')->where($where)->first(); 
  if($result)
  {
      
      DB::table('tbl_form_inputs')->where($where)->update($inputs);
                
  }
  else
  { 
      $form_input_data['created_at'] = $timestamp;
      DB::table('tbl_form_inputs')->insert($inputs);
  }     
  }

  $where = array('form_id' => $form_id,'form_input_edit' => 0);
  DB::table('tbl_form_inputs')->where($where)->delete();

  $update = array('form_input_edit' => 0);
  $where = array('form_id' => $form_id);
  DB::table('tbl_form_inputs')->where($where)->update($update);
               

          $data = array(); 
          $data['status'] = 1;
          $data['form_id'] = $form_id;
          $data['form_url'] = url('form/'.$form_id);
          $message = '<div class="alert alert-success text-center">Form saved successfully</div>';
          $data['message'] = $message;
          request()->session()->flash('alert_msg', '<div class="alert alert-success text-center">Form saved successfully</div>');
          return json_encode($data);
            
        } else 
        {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    public function single_forms_filter_ajax()
    {
      $length       =   Input::get("length");
      $start        =   Input::get("start");
      $filter       =   Input::get('filter');
      //$type         =   Input::get('typeselect');
      $currentPage = ($start)?($start/$length)+1:1;

      $form_id       = Input::get("form_id");

      \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($currentPage) {
        return $currentPage;
      });

      $activities = DB::table('tbl_activities')->select('activity_name','activity_id')->pluck('activity_name','activity_id');

      $search       =   (isset($_POST['search']['value']))?trim($_POST['search']['value']):'';
      $footer_search =  (isset($_POST['columns'][0]['search']['value']))?trim($_POST['columns'][0]['search']['value']):'';
      $footer_search1 =  (isset($_POST['columns'][1]['search']['value']))?trim($_POST['columns'][1]['search']['value']):'';
      $footer_search2 =  (isset($_POST['columns'][3]['search']['value']))?trim($_POST['columns'][3]['search']['value']):'';
      $footer_search3 =  (isset($_POST['columns'][4]['search']['value']))?trim($_POST['columns'][4]['search']['value']):'';
      $where = array('tfr.form_id' => $form_id);
      //Query
      $select ="tfr.form_name,tfr.created_at,tfr.form_id,tfr.form_response_unique_id,tfr.user_id,tfr.response_activity_name,tfr.response_activity_id,tfr.response_activity_date";
      $select .=",sender.user_full_name as sender_user";
      $select .=",twf.workflow_name";

DB::enableQueryLog();
      $q = DB::table('tbl_form_responses as tfr');
      $q->join('tbl_users as sender','tfr.user_id','=','sender.id');
      $q->leftjoin('tbl_wf as twf', function($join)
        {
            $join->on('twf.wf_object_type_id', '=', 'tfr.form_id');
            $join->on('twf.wf_object_type', '=', DB::raw("'form'"));
        });

      $q->leftjoin('tbl_wf_operation as twop', function($join)
        {
            $join->on('tfr.form_response_unique_id', '=', 'twop.wf_object_id');
            $join->on('twop.wf_object_type', '=', DB::raw("'form'"));
        });

      $q->selectRaw($select);
      $q->where('tfr.form_id','=',$form_id);

      $auth_dep_users_array = array(Auth::user()->id);
      switch (Auth::user()->user_role) 
      {
        
        case Session::get("user_role_group_admin")://group admin
        $auth_dep_users = DB::table('tbl_users_departments')
        ->select('users_id')
        ->whereIn('department_id',Session::get('auth_user_dep_ids'))->get();
        //users under the department.
        foreach ($auth_dep_users as $value) 
        {
          $auth_dep_users_array[] = $value->users_id;
        }  
        break;
      }

      if(Auth::user()->user_role != Session::get("user_role_super_admin"))
      {
      //orwhere query
          $q->where(function ($query) use($auth_dep_users_array) {
              $query->whereIn('tfr.user_id',$auth_dep_users_array);
          });
       }   
      if($search)
      {
        $column = array('tfr.form_name','sender.user_full_name','twf.workflow_name');
        $q->Where(function($query1) use($column,$search) {
            foreach ($column as $key => $value) {
              $query1->orWhere($value,'LIKE','%'.$search.'%');
            }
        });
      }
        //tfoot column search
        //notification title
        $q->groupBy('tfr.form_response_unique_id');

        // Ajax order by works
        $order = (isset($_POST['order'][0]['column']))?$_POST['order'][0]['column']:3;
        $direct = (isset($_POST['order'][0]['dir']))?$_POST['order'][0]['dir']:'DESC';
        $data_item = (isset($_POST['columns'][$order]['data']))?$_POST['columns'][$order]['data']:'';
        switch($data_item)
        {
          case 'created':
          $table_column = 'tfr.created_at';
          break;
          case 'sender_user':
          $table_column = 'tfr.user_id';
          break;
          case 'workflow':
          $table_column = 'twf.workflow_name';
          break;
          default:
          $table_column = 'tfr.created_at';
          break;
        }
        $q->orderBy("$table_column","$direct");
        $data = $q->paginate($length);
        $queries = DB::getQueryLog();

/*echo "<pre>";
print_r($queries);*/
        foreach ($data as $key => $item)
        {
          $item->form_privileges = DB::table('tbl_form_privileges')->where('form_id',$item->form_id)->get();
        }
        $count_all = ($data)?$data->total():0;
        $i = $start;
        $data_table = array();



        foreach ($data as $value) 
        {
          $completed_activity = ($value)?$value->response_activity_id:0;
          $workflow_name = ($value->workflow_name)?$value->workflow_name:'';
          
          if($workflow_name)
          {
             $condition=array('wop.wf_object_type' => 'form','wop.wf_object_id' => $value->form_response_unique_id);
             $stages = DB::table('tbl_wf_operation as wop')->join('tbl_wf_operation_details as wopd','wop.id','=','wopd.wf_operation_id')->where($condition)->selectRaw('wop.completed_stage,wop.completed_activity,wopd.wf_stage_name')->first();

            

            $workflow_stage = ($stages)?$stages->wf_stage_name:'';
            /*$completed_activity = ($value)?$value->response_activity_id:0;*/

            
            //$workflow_status = $completed_activity;
            //$workflow_status = json_encode($activities);
          }
          else
          {
            $workflow_stage = $workflow_status = '';
          }

          $workflow_status = (isset($activities[$completed_activity]))?$activities[$completed_activity]:'';
          $i++;
          $row_d = array();
          $row_d['sender_user'] = $value->sender_user;  
          $row_d['from'] = $value->sender_user;
          $row_d['wf'] = $workflow_name;
          $row_d['state'] = $workflow_stage;
          $row_d['status'] = $workflow_status;
          $row_d['created'] = $value->created_at; 
          $row_d['updated'] = $value->response_activity_date;
          $row_d['actions'] = '';

                $view = $edit = $delete =false;
                if(Auth::user()->user_role==1 || Auth::user()->user_role==2)
                {
                  $view = $edit = $delete =true;

                }
                 if($value->form_privileges[0]->privilege_key=="view")
                    {
                      $view = true;
                    }

                    if($value->form_privileges[0]->privilege_key=="edit")
                    {
                      $edit = true;
                    }

                  if($value->form_privileges[0]->privilege_key=="delete")
                    {
                      $delete = true;
                    }
                  
if($view || $edit)  
                  {
                  $row_d['actions'] .= '<a class="view_form_response_details" form_id="'.$value->form_id.'" form_response_unique_id="'.$value->form_response_unique_id.'" user_id="'.$value->user_id.'" data-toggle="modal"  style="cursor:pointer; padding-left:2px; padding-right:2px;"  title="More Details" ><i class="fa fa-ellipsis-v" aria-hidden="true"></i></a>';
                }
                if($delete)  
                  {
                  $row_d['actions'] .='&nbsp;&nbsp;<a class="delete_single trashIcon" form_id="'.$value->form_id.'" form_response_unique_id="'.$value->form_response_unique_id.'" docname="'.$value->form_name.'"   title="Delete Submitted Form" ><i class="fa fa-trash"></i></a>';
                }
                
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

     public function form_permission($form_id)
    {
        if (Auth::user()) 
        {
          Session::put('menuid', '14');
            $user_permission=Auth::user()->user_permission;
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            $data['users'] = WorkflowsModel::users_list();

            $data['form_id'] = $form_id;
            $where = array('form_id' => $form_id);  
            $form_data = DB::table('tbl_forms')->where($where)->first();
            $data['form_data'] = $form_data;
            $data['response'] = Input::get('response');

            $q = DB::table('tbl_form_inputs as ti');
            $q->join('tbl_form_input_types as tt','ti.form_input_type','=','tt.form_input_type');
            $q->select('tt.form_input_type_name','ti.form_input_title','ti.form_input_id','ti.view_permission_users','ti.edit_permission_users') 
                ->where('ti.form_id',$form_id)->orderBy('ti.form_input_order','ASC');
            $form_fields =     $q->get();
            $data['form_fields'] = $form_fields;    
            return view::make('pages/forms/form_permission')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    public function form_permissionSave($form_id=0)
    {
        if (Auth::user()) 
        {
            $q = DB::table('tbl_form_inputs as ti');
            $q->join('tbl_form_input_types as tt','ti.form_input_type','=','tt.form_input_type');
            $q->select('tt.form_input_type_name','ti.form_input_title','ti.form_input_id','ti.view_permission_users','ti.edit_permission_users') 
                ->where('ti.form_id',$form_id)->orderBy('ti.form_input_order','ASC');
            $form_fields =     $q->get();     
                foreach ($form_fields as $key => $value) 
                {

                   $view_permission_users = Input::get('view_permission_users_'.$value->form_input_id);
                   $view_permission_users = ($view_permission_users)?implode(',', $view_permission_users):null;
                   $edit_permission_users = Input::get('edit_permission_users_'.$value->form_input_id);
                   $edit_permission_users = ($edit_permission_users)?implode(',', $edit_permission_users):null;
                   $update = array();
                   $update['view_permission_users'] = $view_permission_users;
                   $update['edit_permission_users'] = $edit_permission_users;
                   $where = array('form_input_id' => $value->form_input_id);
                   DB::table('tbl_form_inputs')->where($where)->update($update);
                  
                }
            return redirect("form_permission/".$form_id)->with('flash_message_success','Form permissions are saved');
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function load_form_counts() {
      $q    = DB::select('SELECT form_id,COUNT(*) as count
                            FROM tbl_form_responses  WHERE user_id='.Auth::user()->id.'    
                            GROUP BY form_id');      
      echo json_encode($q);
    }
}