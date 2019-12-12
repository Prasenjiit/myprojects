<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ApplicationLogController;
use App\Http\Requests;
use Auth;
use View;
use Validator;
use Input;
use Session;
use App\Mylibs\Common;
use App\WorkflowsModel as WorkflowsModel;
// Common Models
use App\StacksModel as StacksModel;
use App\DepartmentsModel as DepartmentsModel;
use App\DocumentTypesModel as DocumentTypesModel;
use App\FormModel as FormModel;
use DB;
use Lang;
use Carbon\Carbon;
class WorkflowsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {   
        Session::put('menuid', '12');
        $this->middleware(['auth', 'user.status']);

        // Set common variable
        $this->actionName1 = 'WorkFlow';
        $this->actionName2 = 'WorkFlowStage';
        $this->docObj     = new Common(); // class defined in app/mylibs
        $this->docObj->common_workflow();
        $this->docObj->get_workflow_notification();
    }
    
    public function delete(){

        if (Auth::user()) {
            $id= Input::get('id');
            $name=Input::get('name');
            //$stackname = Input::get('name');
            // checking wether stack has any entry or not. 
            $hasData = DB::table('tbl_wf_operation')->where('wf_id',$id)->exists();
            if($hasData):
                echo json_encode("false");
            else:
                //$data = WorkflowsModel::find($id);
                $res = DB::table('tbl_wf')->where('id',$id)->delete();
                //$res = WorkflowsModel::where('faq_id',Input::get('faqId'))->delete();
                if ($res==1){                
                    // Save in audits
                    //$name = name;
                    $user = Auth::user()->username;    

                    // Get delete action message
                    $actionMsg = Lang::get('language.delete_action_msg');
                    $actionDes = $this->docObj->stringReplace($this->actionName1,$name,$user,$actionMsg);
                    $result = (new AuditsController)->stacklog(Auth::user()->username,Input::get('id'),'Workflow ', 'Delete',$actionDes);
                    if($result > 0) {
                        $msg = Lang::get('language.delete_success_msg');
                        $msg = str_replace('$object_name', $name, $msg);
                        echo json_encode(Lang::get('language.Stack'). $msg);
                        exit();
                    } else {
                        echo json_encode(Lang::get('language.logfile_issue_msg_lang'));
                        exit;
                    }
                }
            endif;
        } else {
            return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }


    // All Workflow
    public function allworkflow()
    {
        if (Auth::user()) {
           Session::put('menuid', '12');
            $user_permission=Auth::user()->user_permission;
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            $this->docObj->common_workflow();
            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;
            return view::make('pages/workflows/list')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    public function ajax_workflow_list()
    {
        $user_role = Auth::user()->user_role;
        $user_id = Auth::user()->id;
        $dept_id = Auth::user()->department_id;
        $wf_template_permission = Auth::user()->user_workflow_permission;

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
      
      $select ="twf.id,twf.workflow_name,twf.workflow_color,twf.created_at,twf.assigned_users";
      $query = DB::table('tbl_wf as twf')
      ->selectRaw($select);
      switch (Auth::user()->user_role) 
      {
        case Session::get("user_role_private_user")://private user
        case Session::get("user_role_regular_user")://regular user
        break;
        case Session::get("user_role_super_admin")://super admin
        break;
        case Session::get("user_role_group_admin")://group admin
        break;
      }

      $query->groupBy('twf.id');
      
        //ajax search
      if($search){
        $column = array('twf.workflow_name');
        $query->Where(function($query1) use($column,$search) {
            foreach ($column as $key => $value) {
              $query1->orWhere($value,'LIKE','%'.$search.'%');
            }
        });
      }
        //tfoot column search
        //notification title
      if($footer_search){
        $tfoot_column1 = array('twf.workflow_name');
        $query->where(function($query1) use($tfoot_column1,$footer_search) {
          foreach ($tfoot_column1 as $key => $value) {
              $query1->orWhere($value,'LIKE','%'.$footer_search.'%');
            }
          });
      }
        //from
      
        // Ajax order by works
        $order = (isset($_POST['order'][0]['column']))?$_POST['order'][0]['column']:3;
        $direct = (isset($_POST['order'][0]['dir']))?$_POST['order'][0]['dir']:'DESC';
        $data_item = (isset($_POST['columns'][$order]['data']))?$_POST['columns'][$order]['data']:'';
        switch($data_item)
        {
          case 'created':
          $table_column = 'twf.created_at';
          break;
          default:
          $table_column = 'twf.created_at';
          break;
        }
        $query->orderBy("$table_column","$direct");
              $data = $query->paginate($length);
              $count_all = ($data)?$data->total():0;
              $i = $start;
              $data_table = array();
                foreach ($data as $value) {
                $i++;
                $assigned_users  =  (isset($value->assigned_users) && $value->assigned_users)?unserialize($value->assigned_users):array();
            $wf_permission = false; 
            if(Auth::user()->user_role == 1)
            {
               $wf_permission = true; 
            }
            else
            {
              /*$user_permission = Auth::user()->user_permission;
              $user_permission_array = explode(',',$user_permission);
              if(in_array('workflow', $user_permission_array))*/
                if(in_array(Auth::user()->id, $assigned_users))
                {
                  $wf_permission = true; 
                }
               
            }

            //////////////////// Workflow Privileges ////////////////////
            $wf_privileges = DB::table('tbl_wf_privileges')
            ->where('workflow_id',$value->id)
            ->where('privilege_status',1)
            ->get();            
            //////////////////// Workflow Privileges ////////////////////

                $row_d = array();
                $row_d['workflow_name'] = $value->workflow_name;  
                $row_d['stages_count'] = WorkflowsModel::get_task_count($value->id);
                $row_d['Process_count'] = WorkflowsModel::get_process_count($value->id);
                $row_d['created'] = dtFormat($value->created_at);  
                $row_d['status'] = '';
                $row_d['updated'] = '';
                $actions = '';

                    $actions = '<a href="'.url('view_workflow/'.$value->id).'" title="View Workflow" data-workflow_id="'.$value->id.'"><i class="fa  fa-list-ol" style="cursor:pointer;"></i></a>'; 
            
                
                

                   /* $actions .= '&nbsp;&nbsp;<a href="'.url('/workflow_process').'?workflow='.$value->id.'" title="Start this Workflow" class="start_process" data-workflow_id="'.$value->id.'"><i class="fa fa-play" style="cursor:pointer;"></i></a>';*/
    

                 if (strpos($wf_template_permission, 'edit') !== false) {
                    $actions .= '&nbsp;&nbsp;<a href="'.url('/closed_workflow',$value->id).'" title="Edit Workflow Template"><i class="fa fa-pencil" style="cursor:pointer;"></i></a>';
                }  

                if(stristr($wf_template_permission,"delete")){
                    $actions .= '&nbsp;<i class="fa fa-trash" onclick="wfdel('."'".$value->id."',"."'".$value->workflow_name."'".')" title="Delete this Workflow" style="color: red; cursor:pointer;"></i>';
                }


                $row_d['actions'] = $actions;
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
   
   


    public function load_workflow_objects($workflow_id=0)
    {
        $wf_object_type = (Input::get('wf_object_type'))?Input::get('wf_object_type'):'';
        $data['status'] = 1;
        $wf_objects = array();
        if($wf_object_type == 'form')
        {
           $select ="tf.form_id as object_id,'form' as object_type,tf.form_name as object_name";
           $wf_objects = DB::table('tbl_forms as tf')->selectRaw($select)->orderBy('tf.form_name', 'ASC')->get();      
        } 

        if($wf_object_type == 'document')
        {
           $select ="tdt.document_type_id as object_id,'document' as object_type,tdt.document_type_name as object_name";
           $wf_objects = DB::table('tbl_document_types as tdt')->selectRaw($select)->orderBy('tdt.document_type_name', 'ASC')->get();      
        } 
        $data['wf_objects'] = $wf_objects;
        return json_encode($data);
            }


    public function view_workflow($id=0)
    {
        if(Auth::user())
        {
          $data = array();
          /*<--Common-->*/
            $data['docType'] = DocumentTypesModel::orderBy('document_type_order', 'ASC')->get();
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            /*<--// Common-->*/
            $data['workflows'] = WorkflowsModel::get_workflows();
            $data['workflow_id'] = $id;
            $data['wf_details'] = $wf_details = WorkflowsModel::get_workflow($id);
            if(!$wf_details) {
                return redirect('allworkflow')->withErrors("")->withInput();
            }
            $assigned_users  =  (isset($wf_details->assigned_users) && $wf_details->assigned_users)?unserialize($wf_details->assigned_users):array();
            $wf_permission = false; 
            if(Auth::user()->user_role == 1)
            {
               $wf_permission = true; 
            }
            else
            {
              /*$user_permission = Auth::user()->user_permission;
              $user_permission_array = explode(',',$user_permission);
              if(in_array('workflow', $user_permission_array))*/
                if(in_array(Auth::user()->id, $assigned_users))
                {
                  $wf_permission = true; 
                }

            } 

            $data['wf_permission'] = $wf_permission;

            if(Auth::user()->user_role == 1)
            {
              $assigned_users[] = Auth::user()->user_role;
            }

            $data['activities'] = WorkflowsModel::get_activities('workflows');
            $data['user'] = WorkflowsModel::users_list();
            $data['wf_stage_count'] = WorkflowsModel::get_count_stages($id);
            $action_from = (Input::get('action_from'))?Input::get('action_from'):'';
            if($action_from == 'notification')
            {
                WorkflowsModel::read_notification($id,Auth::user()->username);
                $this->docObj->get_workflow_notification();
            }
            
            //////////////////// Workflow Privileges ////////////////////
            $data['wf_privileges_add'] = DB::table('tbl_wf_privileges')
            ->where('workflow_id',$id)
            ->where('privilege_key','add')
            ->where('privilege_status',1)
            ->get();
            //////////////////// Workflow Privileges ////////////////////            
           
            
            $data['object_id']= (Input::get('object_id'))?Input::get('object_id'):'';
            $data['object_type']= $object_type = (Input::get('object_type'))?Input::get('object_type'):'';

            $data['activity_view']= (Input::get('activity_view'))?Input::get('activity_view'):'';

         if($object_type == 'document')
          {
            $data['wf_docs'] = WorkflowsModel::get_workflow_docs($id);
            $data['wf_forms'] = array();
          }
          elseif($object_type == 'form')
          {
            $data['wf_docs'] = array();
            $data['wf_forms'] = WorkflowsModel::get_workflow_forms($id);
          }
          else
          {
            //$data['wf_docs'] = WorkflowsModel::get_workflow_docs($id);
            $data['wf_docs'] = array();
            $data['wf_forms'] = WorkflowsModel::get_workflow_forms($id);
          }
          $limit = 20; 
            $total_records = DB::table('tbl_wf_operation')->where('wf_id',$id)->count();
            $data['total_pages'] = ceil($total_records / $limit);
             return View::make('pages/workflows/view_workflow')->with($data);
        }
        else
        {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    public function wfProcessDelete() {
        $data            = array();
        $wf_operation_id = (Input::get('id'))?Input::get('id'):0;
        $wf_details      = DB::table('tbl_wf_operation')->where('id', '=', $wf_operation_id)->first();
        $delete_process  = DB::table('tbl_wf_operation')->where('id', '=', $wf_operation_id)->delete();
        if($delete_process) {
            //delete from table "tbl_wf_operation_activity,tbl_wf_operation_details"
            $data['response'] = array('success'=>true);
            DB::table('tbl_wf_operation_activity')->where('wf_operation_id', '=', $wf_operation_id)->delete();
            DB::table('tbl_wf_operation_details')->where('wf_operation_id', '=', $wf_operation_id)->delete();
            //Save log to table "tbl_audits"
            $log['document_id']        = 0;
            $log['stack_id']           = 0;
            $log['department_id']      = 0;
            $log['document_type_id']   = 0;
            $log['document_no']        = 0;
            $log['document_name']      = '';
            $log['document_path']      = '';
            $log['audit_user_name']    = Auth::user()->user_full_name;
            $log['audit_owner']        = '';
            $log['audit_action_type']  = 'Delete';
            $log['audit_action_desc']  = 'Workflow '.$wf_details->wf_operation_name.' was deleted...';
            $log['audit_user_ip']      = '';
            $log['audit_geo_location'] = '';
            $log['created_at']         = date('Y-m-d H:i:s');
            $log['updated_at']         = date('Y-m-d H:i:s');
            DB::table('tbl_audits')->insert($log);
        }
        echo  json_encode($data);
    }
    public function workflows_stages()
    {
          $workflow_id = (Input::get('workflow_id'))?Input::get('workflow_id'):0;
          $object_id = (Input::get('object_id'))?Input::get('object_id'):'';
          $object_type = (Input::get('object_type'))?Input::get('object_type'):'';
         
          $data['status'] = 1;
          $data['workflow_id'] = $workflow_id;
          $data['wf_details'] = WorkflowsModel::get_workflow($workflow_id);
          $search = array('workflow_id' => $workflow_id);
          $limit = 20;  
          if (Input::get('page')) 
            { $page  = Input::get('page'); } 
            else { $page=1; };  
          $start_from = ($page-1) * $limit;
          $data['workflow_process'] = WorkflowsModel::workflow_process($search,
            $limit,$start_from);
          //$data['wf_stage_details'] = WorkflowsModel::get_workflow_stage_details($workflow_id);
          $data['wf_privileges_delete'] = DB::table('tbl_wf_privileges')
            ->where('workflow_id',$workflow_id)
            ->where('privilege_key','delete')
            ->get();
          $data['html'] = View::make('pages/workflows/ajax_workflow')->with($data)->render();
          return json_encode($data);
    }


    public function add_to_workflows_modal($id=0)
    {
            $data = array();
            $data['workflows'] = WorkflowsModel::get_workflows();
            $data['activities'] = WorkflowsModel::get_activities('workflows');
            $data['wf_stages'] = WorkflowsModel::wf_stages();
            $data['user'] = WorkflowsModel::users_list();

            $data['object_id']= (Input::get('object_id'))?Input::get('object_id'):'';
            $data['object_type']= (Input::get('type'))?Input::get('type'):'';
            $data['workflow_id']= (Input::get('workflow_id'))?Input::get('workflow_id'):'';
            $data['object_type']= (Input::get('object_type'))?Input::get('object_type'):'';
            $workflow_data      = DB::table('tbl_wf')->where('id', '=', $data['workflow_id'])->first();
            $data['object_type'] = $workflow_data->wf_object_type;
            $data['task_flow']   = $workflow_data->task_flow;
            //echo json_encode($data); exit();
            return View::make('pages/workflows/add_workflow_modal')->with($data);
       
    }
    public function get_wf_details() {
        $workflow_id         = (Input::get('wf_id'))?Input::get('wf_id'):'';
        $workflow_data       = DB::table('tbl_wf')->where('id', '=', $workflow_id)->first();
        $data['object_type'] = ($workflow_data->wf_object_type)?$workflow_data->wf_object_type:'document';
        $data['task_flow']   = ($workflow_data->task_flow)?$workflow_data->task_flow:'2';
        echo json_encode($data);
    }
    public function start_workflow_process()
    {
          
          $workflow_id = (Input::get('workflow_id'))?Input::get('workflow_id'):0;

          $workflow_name = (Input::get('workflow_name'))?Input::get('workflow_name'):'Untitled Process';

          $wf_object_type = (Input::get('object_type'))?Input::get('object_type'):'document';

          
          $wf_object_id = (Input::get('object_id'))?Input::get('object_id'):'';
          $wf_stage_id = (Input::get('stage_id'))?Input::get('stage_id'):0;

          $activity_name = (Input::get('activity_name'))?Input::get('activity_name'):''; 

          $activity_name_label = (Input::get('activity_name_label'))?Input::get('activity_name_label'):''; 

          $activity_date = date("Y-m-d");

          $activity_due_date = (Input::get('activity_due_date'))?date("Y-m-d",strtotime(Input::get('activity_due_date'))):null;    

          $assigned_to = (Input::get('assigned_to'))?Input::get('assigned_to'):0;

          $activity_note = (Input::get('activity_note'))?Input::get('activity_note'):'';
          $timestamp = date("Y-m-d H:i:s");
          $timestamp_user = dtFormat($timestamp);


          $data = array();  
          $data['wf_operation_name'] = $workflow_name.' - '.$timestamp_user;
          $data['wf_id'] = $workflow_id;
          $data['wf_object_id'] = $wf_object_id;
          $data['wf_object_type'] = $wf_object_type;
          $data['created_by'] = Auth::user()->id;
          $data['created_at'] = $timestamp;
          $data['updated_at'] = $timestamp;

                   
          $process_id = DB::table('tbl_wf_operation')->insertGetId($data);

          $query = DB::table('tbl_wf_states as ws');
          $where = array('ws.workflow_id' => $workflow_id);
          $query->where($where);
          $query->orderBy('ws.mark', 'ASC');
          $result =    $query->get();
          $stageId = 0;
          $processId = 0;
          $i=0;
          foreach ($result as $key => $value) 
          {
            $i++;
            $operation_details = array();  
          $operation_details['wf_operation_id'] = $process_id;
          $operation_details['wf_stage'] = $value->id;
          //$operation_details['completed'] = ($wf_stage_id == $value->id)?1:0;
          $operation_details['completed'] = ($i == 1)?1:0;
          $operation_details['wf_stage_name'] = $value->state;
          $operation_details['created_at'] = $timestamp;
          $operation_details['updated_at'] = $timestamp;

          $operation_details_id = DB::table('tbl_wf_operation_details')->insertGetId($operation_details);
              if($i==1){
                $stageId = $value->id;
                $processId = $process_id;
              }
          } 
          $flag ='workflow';        
            if($stageId){
               $operation = array();
                $operation['wfId'] = $workflow_id;
                $operation['wfPrcsId'] = $processId;
                $operation['stageId'] = $stageId;
                $operation['flag'] = $flag;
                $operation['form_responses_uique'] = null;
                $operation['handler'] = $this->docObj;
                $workflow_process = WorkflowsModel::manageWorkflowOperation($operation);
            
            }
          if($activity_name)
          {
          $activity = array();  
          $activity['wf_operation_id'] = $process_id;
          $activity['wf_stage'] = $wf_stage_id;
          $activity['activity_id'] = $activity_name;
          $activity['activity_order'] = 1;
          $activity['assigned_user'] = $assigned_to;
          $activity['due_date'] = $activity_due_date;
          $activity['activity_note'] = $activity_note;
          $activity['completed'] = 0;
          $activity['created_at'] = $timestamp;
          $activity['updated_at'] = $timestamp;


          $activity_details_id = DB::table('tbl_wf_operation_activity')->insertGetId($activity);
            $notification = true;  
            if($notification && $assigned_to)
            {
                    $recipients = array($assigned_to);
                    $notification = array();
                    $notification['type']='workflow';
                    $notification['priority']='1';
                    $notification['title']= 'Workflow activity "'.$activity_name_label.'" assigned by '.Auth::user()->user_full_name;
                    $notification['details']='';
                    $link=URL('view_wf_process/'.$process_id).'?stage='.$wf_stage_id.'&activity_id='.$activity_details_id;
                    $notification['link']=$link;
                    $notification['sender']=Auth::user()->id;
                    $notification['recipients']=$recipients;
                    $this->docObj->add_notification($notification);
          }
        }
        //exit();
              
          $message='<div class="alert alert-success">Process Started Successfully</div>';
          $data = array(); 
          $data['status'] = 1;
          $data['workflow_id'] = $workflow_id;
          $data['url'] = URL('view_workflow/'.$workflow_id);
          return json_encode($data);
    }
    public function view_wf_process($process_id=0)
    {
        if(Auth::user())
        {
            $data = array();
            /*<--Common-->*/
            $data['docType'] = DocumentTypesModel::orderBy('document_type_order', 'ASC')->get();
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            /*<--// Common-->*/

            $process_details = WorkflowsModel::workflow_process_data($process_id); 



            if($process_details)
            {
              
              $data['process_details'] = $process_details;
              $workflow_id = $process_details->wf_id;

              $stage = (Input::get('stage'))?Input::get('stage'):0;
              $search = array('process_id' => $process_id,'stage' => $stage);
              $task_details = WorkflowsModel::task_details($search);

              $data['workflow_id'] = $workflow_id;
              $data['process_id'] = $process_id;
              $data['stage_id'] = $stage;

              /* stage_details used for  state_completed=0 */
              $where = array('workflow_id' => $workflow_id,'stage_id' => $stage);
              $stage_details = WorkflowsModel::get_workflow_single_stage($where);
              /* stage_details used for  state_completed=0 */

              $data['task_details'] = $task_details;

              $search = array('process_id' => $process_id);
              $data['workflow_process'] = WorkflowsModel::workflow_process($search,null,null);
              $search = array('workflow_id' => $workflow_id,'from_state' => $stage,'process_id' => $process_id);
              $data['transitions'] = WorkflowsModel::validate_Rules($search);

              $data['process_id']   = $process_id; 
              $data['stage_id']     = $stage;    
              $wf_action_permission = false;  
              $state_completed=$task_details->state_completed;
              $task_details->current_user = Auth::user()->id;
              $users_name = array(); //Assigned Users names 
              $activity_id_count=array();
              $total_users = 0;
              $assgnUser   = array();
                if($state_completed == 1 || $state_completed == 2 || $state_completed == 3)
                {
                    //History View
                   
                    $para = array();
                    $para['operation_id'] = $process_id;
                    $para['stage_id'] = $stage;
                    $get_assigned_users = WorkflowsModel::get_assigned_users($para);
                    $db_assigned_users = array();
                    $state_type =  $task_details->type;
                    foreach ($get_assigned_users as $key => $value) 
                    {
                      $total_users++;
                      $users_name_text='';
                      $check = (!in_array($value->user_id, $db_assigned_users))?true:false;
                      if($value->user_id)
                      {
                        $db_assigned_users[]=$value->user_id;
                        $users_name_text=$value->assigned_user_name;
                        $assgnUser[] = $value->user_id;
                      }
                      if($value->delegated_user)
                      {
                        $db_assigned_users[]=$value->delegated_user;
                        $users_name_text.= " [<i>Delegated to ".$value->delegated_user_name."</i>]";
                      }


                      if($check)
                      {
                        $users_name[]=$users_name_text; 
                      }

                      if($value->activity_id)
                      {
                        $activity_id_count[$value->activity_id] =(isset($activity_id_count[$value->activity_id]))?$activity_id_count[$value->activity_id]+1:1;
                      }




                    }
                    $db_assigned_users = array_unique($db_assigned_users);  

                     $data['db_assigned_users'] = $db_assigned_users;
                    $para = array('stage_action' => $task_details->stage_action,'stage_group' => $task_details->stage_group,'stage_percentage' => $task_details->stage_percentage);
                    $applied_rule_text = WorkflowsModel::applied_rule_text($para);

                    if($task_details->stage_action == 1)
                    {
                      
                      $assigned_users = ($task_details->assigned_users)?unserialize($task_details->assigned_users):array();
                      $db_assigned_users = array_merge($db_assigned_users,$assigned_users);
                      
                    }

                    if($task_details->stage_action == 2)
                    {
                    }

                   if($task_details->stage_action == 3)
                    {  
                  //Assigned Departments  
                  $dept_ids  = ($task_details->departments)?unserialize($task_details->departments):array();
                  $assigned_departments = WorkflowsModel::assigned_departments($dept_ids);
                  $data['assigned_departments'] = $assigned_departments;

                  $department_id='';
                  }



                  if($stage_details->stage_action == 4)
                    {
                    }
                $db_assigned_users = array_unique($db_assigned_users);     
                if((in_array(Auth::user()->id, $db_assigned_users) && $state_completed == 1))
                {
                  $wf_action_permission = true; 
                }
                }
                else
                {
                    /*$users_ids  = ($stage_details->assigned_users)?unserialize($stage_details->assigned_users):array(); */
                    $para = array('stage_action' => $stage_details->stage_action,'stage_group' => $stage_details->stage_group,'stage_percentage' => $stage_details->stage_percentage);
                    $applied_rule_text = WorkflowsModel::applied_rule_text($para);
                    $state_type =  $stage_details->type;
                    if($stage_details->stage_action == 1)
                    {
                      $assigned_users = ($stage_details->assigned_users)?unserialize($stage_details->assigned_users):array();
                    }

                    if($stage_details->stage_action == 2)
                    {
                    }

                    if($stage_details->stage_action == 3)
                    {
                    //Assigned Departments  
                    $dept_ids  = ($stage_details->departments)?unserialize($stage_details->departments):array();
                    $assigned_departments = WorkflowsModel::assigned_departments($dept_ids);
                    $data['assigned_departments'] = $assigned_departments;
                    }

                    if($stage_details->stage_action == 4)
                    {
                    }
                }

              //current stage
            $data['data_current_stage'] = DB::table('tbl_wf_states')->select('state','id')->where('id',$process_details->current_stage)->first();
            //completed stage
            $data['data_completed_stage'] = DB::table('tbl_wf_states')->select('state','id')->where('id',$process_details->completed_stage)->first();
            //current activity
            $data['data_completed_activity'] = DB::table('tbl_activities')->select('activity_name')->where('activity_id',$process_details->completed_activity)->first();

              $data['wf_action_permission'] = $wf_action_permission;  
              $data['applied_rule_text']  = $applied_rule_text;
              $data['users_name']  = $users_name;
              $data['activity_id_count']  = $activity_id_count;//Find Percentage
              $data['total_users']  = $total_users;//Total assigned users
              $data['state_type']  = $state_type;
              if($state_type == 'last')
              {
                $search = array('process_id' => $process_id);
              }
              else
              {
                $search = array('process_id' => $process_id,'stage' => $stage);
              }
              
              $data['activities'] = WorkflowsModel::task_activities($search);
            $delegateUsers         = WorkflowsModel::getDdelegateUser($process_id,$stage);
            //echo '<pre>'; print_r($delegateUsers);
            $data['delegate']      = array('wf_operation_id'=>$process_id,'wf_stage'=>$stage,'delegate_users'=>$delegateUsers);
            $data['assgnUser'] = $assgnUser;
              return View::make('pages/workflows/view_wf_process')->with($data);
            }
            else
            {
              return redirect('allworkflow')->withErrors("")->withInput();
            }
        }
        else
        {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    

    public function view_wf_process_old($process_id=0)
    {
        if(Auth::user())
        {
            $data = array();
            /*<--Common-->*/
            $data['docType'] = DocumentTypesModel::orderBy('document_type_order', 'ASC')->get();
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            /*<--// Common-->*/
            $data['process_details'] = $result = WorkflowsModel::workflow_process_data($process_id); 

            $data['process_id'] = $process_id; 

            $stage_id = (Input::get('stage'))?Input::get('stage'):0;
            $data['stage_id'] = $stage_id;   
            $workflow_id = ($result)?$result->wf_id:0;
            $data['workflow_id'] = $workflow_id; 
            $data['wf_details'] =$wf_details =  WorkflowsModel::get_workflow($workflow_id);
            $search = array('process_id' => $process_id);
            $data['workflow_process'] = WorkflowsModel::workflow_process($search,null,null);

            $stage = (Input::get('stage'))?Input::get('stage'):0;
            $search = array('process_id' => $process_id,'stage' => $stage);
            $data['task_details'] = WorkflowsModel::task_details($search);
            
            if(!isset($data['task_details']->wf_stage)) {
                return redirect('allworkflow')->withErrors("")->withInput();
            }

            $where = array('workflow_id' => $workflow_id,'stage_id' => $stage_id);
            $data['stage_details'] = $stage_details = WorkflowsModel::get_workflow_single_stage($where);
            $users_ids  = unserialize($stage_details->assigned_users);
            if(@$stage_details->stage_action == 2)//heirarchy
            {
              $user_reports_to = DB::table('tbl_wf_operation_details')->select('notified_users')->where('wf_stage',$stage_id)->where('wf_operation_id',$process_id)->first();
              $data['assigned_users_name_reports_to'] = DB::table('tbl_users')->select('user_full_name')->where('id',$user_reports_to->notified_users)->get();
            }
            //assigned users
            $data['assigned_users_name'] = DB::table('tbl_users')->select('user_full_name')->whereIn('id',$users_ids)->get();
            $dept_ids  = unserialize($stage_details->departments);
            //assigned departments
            $data['assigned_departments'] = DB::table('tbl_departments')->select('department_name')->whereIn('department_id',$dept_ids)->get();
            //transition id of stage
            $transition = DB::table('tbl_wf_transitions')->select('id','activity_id','name')->where('from_state',$stage_id)->where('workflow_id',$workflow_id)->get();
            //user under dept
            $data['users_all'] = DB::table('tbl_users_departments as td')->join('tbl_users as tu','tu.id','=','td.users_id')->select('td.users_id','tu.user_full_name')->whereIn('td.department_id',$dept_ids)->groupBy('td.users_id')->get();
            //current details
            $current = DB::table('tbl_wf_operation')->select('current_stage','completed_stage','completed_activity')->where('id',$process_id)->first();
            //current stage
            $data['data_current_stage'] = DB::table('tbl_wf_states')->select('state','id')->where('id',$current->current_stage)->first();
            //completed stage
            $data['data_completed_stage'] = DB::table('tbl_wf_states')->select('state','id')->where('id',$current->completed_stage)->first();
            //current activity
            $data['data_completed_activity'] = DB::table('tbl_activities')->select('activity_name')->where('activity_id',$current->completed_activity)->first();
            //approved_users lsit
            if($transition){
                foreach ($transition as $key => $trans) {
                $trans->approved_users = DB::table('tbl_wf_group_transitions as tgt')
                ->leftjoin('tbl_users as tu','tgt.user_id','=','tu.id')
                ->select('tu.user_full_name','tgt.approval_percentage')
                ->where('tgt.operation_id',$process_id)
                ->where('tgt.transition_id',$trans->id)
                ->where('tgt.activity_id',$trans->activity_id)
                ->get();
                
                }
                $data['transitions_det'] = $transition;
                foreach ($transition as $key => $value) {
                    if(@$value->approved_users[0]->approval_percentage != null)
                    {
                        $data['transitions_percentage'] = 1;
                    }
                }
            }
            else{$data['transitions_percentage'] = 0;}

            //notifiction send users list

            $notifiers = DB::table('tbl_wf_operation_details')->select('notified_users')->where('wf_stage',$stage)->where('wf_operation_id',$process_id)->first();
            if($notifiers)
            {
              $notified_array = explode(',',$notifiers->notified_users);
            }

            //assigned and delegated user list

            $assigners_delegaters = DB::table('tbl_wf_assigned_users')
            ->select('user_id','delegated_user')
            ->where('stage_id',$stage)
            ->where('operation_id',$process_id)
            ->get();

           $assigners = array();//assigned users array
           $delegaters = array();// delegated users array

           if($assigners_delegaters){
             foreach ($assigners_delegaters as $delegated) {
               array_push($assigners, $delegated->user_id);
               array_push($delegaters, $delegated->delegated_user);
             }
           }
            /************  Action Permission ************/

            $assigned_users  =  (isset($stage_details->assigned_users) && $stage_details->assigned_users)?unserialize($stage_details->assigned_users):array();
            $assigned_depts  =  (isset($stage_details->departments) && $stage_details->departments)?unserialize($stage_details->departments):array();
            $wf_action_permission = false; 
            // if((Auth::user()->user_role == 1)||(in_array(Auth::user()->id, @$notified_array)))
            // {
            //    $wf_action_permission = true; 
            // }
            // else
            // {
                $stage_action  =  (isset($data['task_details']->stage_action))?$data['task_details']->stage_action:1;

                $dept_id = Auth::user()->department_id;

              if((in_array(Auth::user()->id, $assigned_users))||(in_array(Auth::user()->id, @$notified_array)) || (in_array(Auth::user()->id, $assigners)) || (in_array(Auth::user()->id, $delegaters)) && $stage_action ==1) // and stage action = 1
                {
                  $wf_action_permission = true; 
                }
                /*else if($stage_action == 2) // and stage action = 2
                {
                    $users_ids = array();
                    //wf created user
                    $created = DB::table('tbl_wf_operation')->select('created_by')->where('id',$process_id)->first();
                    //created user role and dept
                    $created_user_details = DB::table('tbl_users')->select('user_role','department_id')->where('id',$created->created_by)->first();
                    switch($created_user_details->user_role)
                    {
                        case '1'://super admin

                        break;
                        case '2'://dept admin (notifications)-> super admins
                            $heirarchy_super = DB::table('tbl_users')->select('id')->where('user_role',1)->get();
                            foreach ($heirarchy_super as $super) {
                                array_push($users_ids, $super->id);
                            }
                            if((in_array(Auth::user()->id, $users_ids))||(in_array(Auth::user()->id, @$notified_array)))
                            {
                                $wf_action_permission = true; 
                            }
                            else
                            {
                                $wf_action_permission = false; 
                            }
                        break;
                        case '3'://regular user
                        case '4'://private user (notificatios)-> group admin and super
                            $heirarchy_super = DB::table('tbl_users')->select('id')->where('user_role',1)->get();
                            foreach ($heirarchy_super as $super) {
                                array_push($users_ids, $super->id);
                            }
                            $departments_users = explode(',',$created_user_details->department_id);
                            $heirarchy_dept = DB::table('tbl_users')->join('tbl_users_departments','tbl_users.id','=','tbl_users_departments.users_id')->select('tbl_users_departments.users_id as users_id')->where('tbl_users.user_role',2)->whereIn('tbl_users_departments.department_id',$departments_users)->get();
                            foreach ($heirarchy_dept as $super_d) {
                                array_push($users_ids, $super_d->users_id);
                            } 
                            if((in_array(Auth::user()->id, $users_ids))||(in_array(Auth::user()->id, @$notified_array)))
                            {
                                $wf_action_permission = true; 
                            }
                            else
                            {
                                $wf_action_permission = false; 
                            }
                        break;
                    }
                }*/
                else if($stage_action == 2) // and stage action = 2
                {
                  if(in_array(Auth::user()->id, @$notified_array) || (in_array(Auth::user()->id, $assigners)) || (in_array(Auth::user()->id, $delegaters)))
                  {
                    $wf_action_permission = true; 
                  }
                }
                else if($stage_action == 3) // and stage action = 3
                {               
                    $key_dept_value = explode(',',$dept_id);
                    foreach ($key_dept_value as $val) { 
                        if((in_array($val,$assigned_depts))||(in_array(Auth::user()->id, @$notified_array)) || (in_array(Auth::user()->id, $assigners)) || (in_array(Auth::user()->id, $delegaters)))
                        {
                            $wf_action_permission = true; 
                        }
                    } 
                }
                // else if stage action ==2 manager of wf submitted user
               
                //it();
            //} 
            $data['wf_action_permission'] = $wf_action_permission;

            $where = array('object_type' => $result->wf_object_type,'wf_object_id' => $result->wf_object_id);
            $data['object_items'] = WorkflowsModel::object_rule_components($where);

            /************  Action Permission ************/

            //////////////////// Workflow Privileges ////////////////////
            $data['wf_privileges_add'] = DB::table('tbl_wf_privileges')
            ->where('workflow_id',$workflow_id)
            ->where('privilege_key','add')
            ->get();
            
            $data['wf_privileges_edit'] = DB::table('tbl_wf_privileges')
            ->where('workflow_id',$workflow_id)
            ->where('privilege_key','edit')
            ->get();

            $data['wf_privileges_delete'] = DB::table('tbl_wf_privileges')
            ->where('workflow_id',$workflow_id)
            ->where('privilege_key','delete')
            ->get();

            //////////////////// Workflow Privileges ////////////////////   
            $search = array('workflow_id' => $workflow_id,'from_state' => $stage,'process_id' => $process_id);
            $data['transitions2'] = WorkflowsModel::get_transitions($search);
            $data['transitions'] = WorkflowsModel::validate_Rules($search);  
            //$data['transition_condition'] = $this->process_transition($data['transitions'],$process_id); 
            //$data['transitions'] = WorkflowsModel::get_rules($workflow_id,$stage,$process_id);
            //$data['transitions'] = WorkflowsModel::getRules($workflow_id,$stage,$process_id);
            $delegateUsers         = WorkflowsModel::getDdelegateUser($process_id,$stage);
            //echo '<pre>'; print_r($delegateUsers);
            $data['delegate']      = array('wf_operation_id'=>$process_id,'wf_stage'=>$stage,'delegate_users'=>$delegateUsers);
            return View::make('pages/workflows/view_workflow_process')->with($data);
        }
        else
        {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function process_transition($transitions,$process_id) {

        $wf_operation_data = WorkflowsModel::workflow_process_data($process_id);
        foreach ($transitions as $key => $trans) {
            if($trans->with_rule==1) {                
                $trans->rule = unserialize($trans->rule_area);
                
                foreach ($trans->rule['rules'] as $key1 => $r) {
                    $trans->rule['rules'][$key1]['wf_object_id']   = $wf_operation_data->wf_object_id;
                    $trans->rule['rules'][$key1]['wf_object_type'] = $wf_operation_data->wf_object_type;
                }
                $trans->status     = $this->check_action_enabled($trans->rule);
                $trans->stage_case = $trans->rule['stage_case'];
                $trans->if_stage   = $trans->rule['if_stage'];
                $trans->else_stage = $trans->rule['else_stage'];
            }
            else {
                $trans->status = 1;
            }
        }
        return $transitions;
    }
    public function check_action_enabled($rules) {
        $condition       = $rules['condition'];
        $rules_array     = $rules['rules'];
        $user_role       = Auth::user()->user_role;
        $user_id         = Auth::user()->id;
        $user_department = explode(",",Auth::user()->department_id);
        $result          = array();
        $return          = 0;
        foreach ($rules_array as $key => $rule) {
            $result[$key] = $this->check_condition($rule['wf_object_id'],$rule['wf_object_type'],$rule['id'],$rule['value'],
                $rule['operator'],$rule['object_type']);
            $operator = $rule['operator'];
            switch($rule['object_type']) {
                case 'user_role':
                    if($operator=='equal') {
                        if($user_role==$rule['value']) {
                            $result[] = 1;
                        }
                        else {
                            $result[] = 0;
                        } 
                    }
                    else if($operator=='not_equal') {
                        if($user_role!=$rule['value']) {
                            $result[] = 1;
                        }
                        else {
                            $result[] = 0;
                        } 
                    }
                    
                break;
                case 'users':
                    if($operator=='equal') {
                        if($user_id==$rule['value']) {
                            $result[] = 1;
                        }
                        else {
                            $result[] = 0;
                        }
                    }
                    else if($operator=='not_equal') {
                        if($user_id!=$rule['value']) {
                            $result[] = 1;
                        }
                        else {
                            $result[] = 0;
                        }
                    }
                    
                break;
                case 'department':
                    if($operator=='equal') {
                        if(in_array($rule['value'], $user_department)) {
                            $result[] = 1;
                        }
                        else {
                            $result[] = 0;
                        }
                    }
                    else if($operator=='not_equal') {
                        if(in_array($rule['value'], $user_department)) {
                            $result[] = 0;
                        }
                        else {
                            $result[] = 1;
                        }
                    }
                    
                break;
                default:
                        # code...
                        break;
            }
        }

        //if($$rule['object_type']==)
        if($condition=='AND') {
            if(in_array(0,$result)) {
                return 0;
            }
            else {
                return 1;
            }
        }
        else if($condition=='OR') {
            if(in_array(1,$result)) {
                return 1;
            }
            else {
                return 0;
            }
        }
        return $return;
    }
    public function check_condition($wf_object_id,$wf_object_type,$frm_inp_id,$value,$operator,$object_type) {
        $result = false;
        if($wf_object_type=='form' && $object_type=='form') {
            $table = 'tbl_form_responses';
            $query = DB::table($table);
            switch ($operator) {
                case 'equal':
                    $where = array(
                            array('form_response_unique_id','=',$wf_object_id),
                            array('form_input_id','=',$frm_inp_id),
                            array('form_response_value','=',$value)
                            );
                    $result = $query->where($where)->first();
                    break;
                case 'not_equal':
                    $where = array(
                            array('form_response_unique_id','=',$wf_object_id),
                            array('form_input_id','=',$frm_inp_id),
                            array('form_response_value','!=',$value)
                            );
                    $result = $query->where($where)->first();
                    break;
                case 'greater':
                    $where = array(
                            array('form_response_unique_id','=',$wf_object_id),
                            array('form_input_id','=',$frm_inp_id),
                            array('form_response_value','>',$value)
                            );
                    $result = $query->where($where)->first();
                    break;
                case 'greater_or_equal':
                    $where = array(
                            array('form_response_unique_id','=',$wf_object_id),
                            array('form_input_id','=',$frm_inp_id),
                            array('form_response_value','>=',$value)
                            );
                    $result = $query->where($where)->first();
                    break;
                case 'less':
                    $where = array(
                            array('form_response_unique_id','',$wf_object_id),
                            array('form_input_id','=',$frm_inp_id),
                            array('form_response_value','<',$value)
                            );
                    $result = $query->where($where)->first();
                    break;
                case 'less_or_equal':
                    $where = array(
                            array('form_response_unique_id','',$wf_object_id),
                            array('form_input_id','=',$frm_inp_id),
                            array('form_response_value','<=',$value)
                            );
                    $result = $query->where($where)->first();
                    break;
                case 'begins_with':
                    $where = array(
                            array('form_response_unique_id','',$wf_object_id),
                            array('form_input_id','=',$frm_inp_id),
                            array('form_response_value','like',$value.'%')
                            );
                    $result = $query->where($where)->first();
                    break;
                case 'ends_with':
                    $where = array(
                            array('form_response_unique_id','',$wf_object_id),
                            array('form_input_id','=',$frm_inp_id),
                            array('form_response_value','like','%'.$value)
                            );
                    $result = $query->where($where)->first();
                    break;
                default:
                    # code...
                    break;
            }
        }
        else if($wf_object_type=='document') {
            $table = 'tbl_documents';
        }
        if($result) {
            return 1;
        }
        return 0;
    }
      public function transition_click()
    {
        $workflow_id = (Input::get('workflow_id'))?Input::get('workflow_id'):0;
        $process_id = (Input::get('process_id'))?Input::get('process_id'):0;
        $from_state = (Input::get('from_state'))?Input::get('from_state'):0;
        $to_state = (Input::get('to_state'))?Input::get('to_state'):0;
        $transition_id = (Input::get('transition_id'))?Input::get('transition_id'):0;
        $stage_action = (Input::get('stage_action'))?Input::get('stage_action'):0;
        $activity_id  = (Input::get('activity_id'))?Input::get('activity_id'):0;
        $note  = (Input::get('act_activity_note_new'))?Input::get('act_activity_note_new'):'';
        $check =0;
        $user_id = Auth::user()->id;
        $timestamp = date("Y-m-d H:i:s");
        /*Update Form*/
        $wf_object_type  = (Input::get('wf_object_type'))?Input::get('wf_object_type'):0;

        if($wf_object_type == 'form')
        {
          $resp_id = Input::get('resp_id');
          $form_id  = (Input::get('form_id'))?Input::get('form_id'):0;
          $form_response_unique_id  = (Input::get('form_response_unique_id'))?Input::get('form_response_unique_id'):0;
         
          $form_responses_uique = $form_response_unique_id;
          $selected = "";
          $files = "";
          $v = array();
          $result = FormModel::form_submit_edit($form_id,$form_responses_uique);

          $loop=0;
          foreach ($result as $key => $value) 
          {
            
            
            $edit_permission_users = ($value->edit_permission_users)?explode(',',$value->edit_permission_users):array();
            if(in_array($user_id,$edit_permission_users) || (Auth::user()->user_role == 1))
            {

            $form_response_id = (isset($resp_id[$loop]))?$resp_id[$loop]:0;
            $form_key = $loop;  

            $input_value = Input::get($value->form_input_id);

            $val[$value->form_input_id] = $input_value;  
            $row = array();
            $row['input_value'] = $input_value;
            $skip = 0;
            if(!$value->is_input_type)
            {
              $form_response_value = "";
              $selected = "";
              $form_response_file="";
              $form_response_file_size="";
            }
            else if($value->is_options)//check is option ==1
            {
              $response_value_array = (is_array($input_value))?$input_value:array();
              $form_Input_options = unserialize($value->form_Input_options);
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
             
             
              if($form_response_file)
              {
                $form_response_file = implode(',',$form_response_file);

              }
              else
              {
                 $skip = 1;
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
           
            $row['key'] = $form_key;
            $row['form_response_id'] = $form_response_id;
            $row['form_input_title'] = $value->form_input_title;
            $row['form_input_type_common'] = $value->form_input_type_common;
            $row['form_response_value'] = $form_response_value;
            $row['form_response_selected'] = $selected;
            $row['document_file_name'] = $form_response_file;
            $row['form_response_file_size'] = $form_response_file_size;
            $row['created_at'] = $timestamp;

            if($skip == 0)
            {
             
             
              if($form_response_id == "null")
              {
                
                $new_row = array();
                $new_row['form_id'] = $form_id;
                $new_row['user_id'] = Auth::user()->id;
                $new_row['form_name'] = '';
                $new_row['form_description'] = '';
                $new_row['form_input_title'] = $value->form_input_title;
          $new_row['form_response_value'] = $row['form_response_value'];
          $new_row['form_response_selected'] = $row['form_response_selected'];
          $new_row['document_file_name'] = $row['document_file_name'];
          $new_row['form_response_file_size'] = $row['form_response_file_size'];
          $new_row['form_input_type'] = $value->form_input_type;
          $new_row['form_input_id'] = $value->form_input_id;
          $new_row['created_at'] = $timestamp;
          $new_row['form_response_unique_id'] = $form_response_unique_id;
          $new_row['form_assigned_to'] = NULL;
          $results = DB::table('tbl_form_responses')->insert($new_row);
              }
              else
              {
              $v[]=$row;
              }
              
            }
            $loop++;
            }
          }
 

          
            foreach ($v as $key => $value) 
            {
              
              $results = DB::table('tbl_form_responses')->where('form_response_id', $value['form_response_id'])->update(['form_response_value'=>$value['form_response_value'],
              'form_response_selected'=>$value['form_response_selected'],
              'document_file_name'=>$value['document_file_name'],
              'form_response_file_size'=>$value['form_response_file_size'],
              'created_at'=>$value['created_at']
              ]);
            }
         
        }
 

        /*END UPDATE FOEM*/

         $operation_details = DB::table('tbl_wf_operation')->select('*')->where('id',$process_id)->first();

        $wf_data    = DB::table('tbl_wf')->where(array('id'=>$workflow_id))->first();
                $form_data = DB::table('tbl_forms')->select('form_name')->where('form_id',$wf_data->wf_object_type_id)->first();
                $form_name = $form_data->form_name;
                $wf_name    = ($wf_data->workflow_name)?$wf_data->workflow_name:'';
                $cond       = array('id'=>$from_state);
                $criteria      = DB::table('tbl_wf_states')->where($cond)->first();
                $stage_group = $criteria->stage_group;
                $departments_assigned = $criteria->departments;
                $percentage = $criteria->stage_percentage;

                
                $pass_values = array();
                $pass_values['process_id'] = $process_id;
                $pass_values['transition_id'] = $transition_id;
                $pass_values['stage_group'] = $stage_group;
                $pass_values['departments_assigned'] = $departments_assigned;
                $pass_values['percentage'] = $percentage;
                $pass_values['from_state'] = $from_state;
                $pass_values['to_state'] = $to_state;
                $pass_values['to_state_name'] = $criteria->state;
                $pass_values['wf_name'] = $wf_name;
                $pass_values['wf_id'] = $workflow_id;
                $pass_values['form_name'] = $form_name;
                $pass_values['activity_id'] = $activity_id;
           
           $activity_data = DB::table('tbl_activities')->select('activity_name')->where('activity_id',$activity_id)->first();
            $activity = ($activity_data)?$activity_data->activity_name:'';
           
           if($stage_action == 3 )
          {
            
            $check_user_id = Auth::user()->id;
            $query = DB::table('tbl_wf_assigned_users')->where('operation_id',$process_id)->where('stage_id',$from_state);

            $query->where(function($q) use ($check_user_id){
              $q->where('user_id', $check_user_id)
            ->orWhere('delegated_user', $check_user_id);
            });
            $exists = $query->exists();
            if($exists)
            {
              $update=array('action_taken_by' => $check_user_id,'activity_id' => $activity_id,'updated_at' =>date('Y-m-d H:i:s'));

              $query = DB::table('tbl_wf_assigned_users')->where('operation_id',$process_id)->where('stage_id',$from_state);

            $query->where(function($q) use ($check_user_id){
              $q->where('user_id', $check_user_id)
            ->orWhere('delegated_user', $check_user_id);
            });
            $query->update($update);
            }
            else
            {
              DB::table('tbl_wf_assigned_users')
        ->insert(['operation_id'    =>$process_id,
                  'stage_id'        =>$from_state,
                  'user_id'         =>$check_user_id,
                  'action_taken_by' =>$check_user_id,
                  'delegated_user'  =>0,
                  'activity_id'     =>$activity_id,
                  'created_at'      =>date('Y-m-d H:i:s'),
                  'updated_at'      =>date('Y-m-d H:i:s')]);
            }
            $check = $this->check_transition_approval($pass_values);
          }
          elseif($stage_action == 1 ||$stage_action == 2)
          {
                $check_exist = DB::table('tbl_wf_group_transitions')->where('operation_id',$process_id)->where('transition_id',$transition_id)->where('user_id',Auth::user()->id)->where('activity_id',$activity_id)->exists();
                $entry = array();
                if($check_exist)
                {//update
                    $entry['updated_at'] =date("Y-m-d H:i:s");
                    DB::table('tbl_wf_group_transitions')->where('operation_id',$process_id)->where('transition_id',$transition_id)->where('user_id',Auth::user()->id)->where('activity_id',$activity_id)->update($entry);
                }
                else
                {//insert
                    $entry['operation_id'] = $process_id;
                    $entry['transition_id'] =$transition_id;
                    $entry['user_id'] = Auth::user()->id;
                    $entry['created_at'] =date("Y-m-d H:i:s");
                    $entry['activity_id'] = @$activity_id;
                    DB::table('tbl_wf_group_transitions')->insert($entry);
                }
                
          }

          if($activity_id){
                WorkflowsModel::addActivity($workflow_id,$from_state,$process_id,$activity_id,$note);
            }
           
          if($stage_action == '1' || $stage_action =='2' || $stage_action == '4' || $check == 1)
          {
           
            $wfObjectType = ($operation_details)?$operation_details->wf_object_type:'';
            $wfObjectId = ($operation_details)?$operation_details->wf_object_id:'';
            $form_responses_uique = $wfObjectId;

            WorkflowsModel::updateCompleteFromStage($process_id,$from_state,$activity_id,$wfObjectType,$wfObjectId,$this->docObj);

            WorkflowsModel::updateCompleteToStage($workflow_id,$process_id,$to_state,$activity_id);

            $flag = 'workflow';
            $operation = array();
            $operation['wfId'] = $workflow_id;
            $operation['wfPrcsId'] = $process_id;
            $operation['stageId'] = $to_state;
            $operation['flag'] = $flag;
            $operation['form_responses_uique'] = null;
            $operation['handler'] = $this->docObj;
            $operation['wfObjectType'] = $wfObjectType;
            $operation['wfObjectId'] = $wfObjectId;
            $workflow_process = WorkflowsModel::manageWorkflowOperation($operation);

          }
         /* echo "<pre>";
          print_r($resp_id);
echo "</pre>";        
echo "<pre>";
          print_r($v);
echo "</pre>";
exit;*/
          $data = array(); 
          $data['status'] = 1;
          $data['workflow_id'] = $workflow_id;
          $data['check'] = $check;
          if(!$check && $stage_action ==3){
            $data['url'] = URL('view_wf_process/'.$process_id).'?stage='.$from_state;
          }
          else
          {
            $data['url'] = URL('view_wf_process/'.$process_id).'?stage='.$to_state;
          }
         
          return redirect($data['url'])->withErrors("")->withInput();
         // return json_encode($data);
    }
    public function check_transition_approval($pass_values=array())
    {
            //assigned departments 
            $para = array();
                    $para['operation_id'] = $pass_values['process_id'];
                    $para['stage_id'] = $pass_values['from_state'];
                    $get_assigned_users = WorkflowsModel::get_assigned_users($para);
                    $activity_id =$pass_values['activity_id'];
                    $total_users = 0;
                    $activity_count = 0;
            foreach ($get_assigned_users as $key => $value) 
                    {
                      $total_users++;
                      if($value->activity_id == $activity_id)
                      {
                        $activity_count++;
                      }
         
                    }
            $return_flag =0;
            switch($pass_values['stage_group'])
            {
              //any one 
              case '1':
              //echo "anyone";
              if($activity_count>0)
              {
                  //approve to next transition
                  $return_flag =1;
              }
              
              break;
              //all
              case '2':
              //echo "all";
              if(($activity_count == $total_users) && $activity_count)
              {
                  //approve to next transition
                  $return_flag =1;
              }
              
              break;
              //percentage
              case '3':
              //echo "percentage";
              if($total_users && $activity_count)
              {
                  $avg = round(($activity_count/$total_users)*100);
                  if($avg >= $pass_values['percentage'])
                  {
                      //approve to next transition
                      $return_flag =1;
                  }
              }
              
              break;

              default:
              return 0;
            }
            return $return_flag;
    }
    public function move_to_next_stage($pass_values)
    {
        $update = array();
        $update['completed'] = 2;
        $where = array(
            'wf_operation_id' => $pass_values['process_id'],
            'wf_stage' => $pass_values['from_state']
            );
        DB::table('tbl_wf_operation_details')
        ->where($where)->update($update);

        //history save
        $actionMsg = Lang::get('language.move_action_msg');

        $actionDes = $this->docObj->stringReplace(
            $pass_values['form_name'],
            $pass_values['wf_name'],
            $pass_values['to_state_name'],
            $actionMsg);

        $result_history = (new AuditsController)->formslog(
            Auth::user()->username,
            $pass_values['wf_id'],
            'Workflow',
            'Transisted',
            $actionDes,
            $pass_values['wf_name']);
    }
    public function workflow_new_activity($id=0)
    {
            $data = array();
            $data['activities'] = WorkflowsModel::get_activities('workflows');
            $data['user'] = WorkflowsModel::users_list();

            /*$data['object_id']= (Input::get('object_id'))?Input::get('object_id'):'';
            $data['object_type']= (Input::get('type'))?Input::get('type'):'';*/

            $data['workflow_id']= (Input::get('workflow_id'))?Input::get('workflow_id'):'';
            
            $data['actions']= (Input::get('action'))?Input::get('action'):'view';

            $data['process_id']= (Input::get('process_id'))?Input::get('process_id'):0;

            $data['stage_id']= (Input::get('stage_id'))?Input::get('stage_id'):0;

            $data['activity_id']= (Input::get('activity_id'))?Input::get('activity_id'):0;
           $activity_name = $due_date = $assigned_to =   $activity_note = '';
           if($data['activity_id'])
           {
            $where = array('wf.id' => $data['activity_id']);
            $select ="wf.*";
            $result = DB::table('tbl_wf_operation_activity as wf')->selectRaw($select)->where($where)->first();
			if($result)
			{
				$activity_name = ($result->activity_id)?$result->activity_id:0;
				$due_date = ($result->due_date)?$result->due_date:0;
				$assigned_to = ($result->assigned_user)?$result->assigned_user:0;
				$activity_note = ($result->activity_note)?$result->activity_note:0;
			}
           }   
           $data['activity_name']   =  $activity_name; 
           $data['due_date']        =  $due_date;
           $data['assigned_to']     =  $assigned_to;
           $data['activity_note']   =  $activity_note;

            return View::make('pages/workflows/ajax_workflow_activity')->with($data);
       
    }

    public function workflows_activity_save()
    {
          
          $activity_id = (Input::get('activity_id'))?Input::get('activity_id'):0;
          $workflow_id = (Input::get('workflow_id'))?Input::get('workflow_id'):0;

          $workflow_name = (Input::get('workflow_name'))?Input::get('workflow_name'):'Untitled Process';

          $process_id = (Input::get('process_id'))?Input::get('process_id'):0;
          $stage_id = (Input::get('stage_id'))?Input::get('stage_id'):0;  
          $activity_name = (Input::get('activity_name'))?Input::get('activity_name'):'';

          $activity_name_label = (Input::get('activity_name_label'))?Input::get('activity_name_label'):'';  

          $activity_date = date("Y-m-d");

          $activity_due_date = (Input::get('activity_due_date'))?date("Y-m-d",strtotime(Input::get('activity_due_date'))):null;    

          $assigned_to = (Input::get('assigned_to'))?Input::get('assigned_to'):0;

          $activity_note = (Input::get('activity_note'))?Input::get('activity_note'):'';
          $timestamp = date("Y-m-d H:i:s");

          if($activity_name)
          {
			$activity = array();  
			$activity['activity_id'] = $activity_name;
			$activity['assigned_user'] = $assigned_to;
			$activity['due_date'] = $activity_due_date;
			$activity['activity_note'] = $activity_note;
            $activity['assigned_by'] = Auth::user()->id;
		    $activity['updated_at'] = $timestamp;
			$where = array('wf.id' => $activity_id);
            $select ="wf.*";
            $result = DB::table('tbl_wf_operation_activity as wf')->selectRaw($select)->where($where)->first();
			if($result)
			{
				$where = array('wf.id' => $result->id);
				DB::table('tbl_wf_operation_activity as wf')->where($where)->update($activity);
			}
			else
			{				
         
          $activity['wf_operation_id'] = $process_id;
          $activity['wf_stage'] = $stage_id;
          
          $activity['activity_order'] = 1;
          $activity['assigned_by'] = Auth::user()->id;
          $activity['completed'] = 0;
          $activity['created_at'] = $timestamp;
         
		  $activity_id  = DB::table('tbl_wf_operation_activity')->insertGetId($activity);
			}

$notification = true;  
            if($notification && $assigned_to)
            {
                    $recipients = array($assigned_to);
                    $notification = array();
                    $notification['type']='workflow';
                    $notification['priority']='1';
                    $notification['title']= 'Workflow activity "'.$activity_name_label.'" assigned by '.Auth::user()->user_full_name;
                    $notification['details']='';
                    $link=URL('view_wf_process/'.$process_id).'?stage='.$stage_id.'&activity_id='.$activity_id;
                    $notification['link']=$link;
                    $notification['sender']=Auth::user()->id;
                    $notification['recipients']=$recipients;
                    $this->docObj->add_notification($notification);
          }


          
          
          }


              
          $message='<div class="alert alert-success">Process Started Successfully</div>';
          $data = array(); 
          $data['status'] = 1;
          $data['workflow_id'] = $workflow_id;
          $data['url'] = URL('view_wf_process/'.$process_id.'?stage='.$stage_id);

          return json_encode($data);
    }


    public function ajax_activity_list()
    {
          $data['status'] = 1;
          $data['workflow_id'] = $workflow_id;
          $data['wf_details'] = WorkflowsModel::get_workflow($workflow_id);
          $search = array('workflow_id' => $workflow_id);
          $data['workflow_process'] = WorkflowsModel::workflow_process($search,null,null);
          $data['wf_stage_details'] = WorkflowsModel::get_workflow_stage_details($workflow_id);
          $data['html'] = View::make('pages/workflows/ajax_workflow')->with($data)->render();
          return json_encode($data);
    }
	
	public function workflow_delete_activity()
    {
          
          $activity_id = (Input::get('activity_id'))?Input::get('activity_id'):0;
          $workflow_id = (Input::get('workflow_id'))?Input::get('workflow_id'):0;
          $process_id = (Input::get('process_id'))?Input::get('process_id'):0;
          $stage_id = (Input::get('stage_id'))?Input::get('stage_id'):0;  
		  DB::table('tbl_wf_operation_activity')->where('id',$activity_id)->delete();	
              
          $message='<div class="alert alert-success">Activity Deleted Successfully</div>';
          $data = array(); 
          $data['status'] = 1;
          $data['workflow_id'] = $workflow_id;
          $data['url'] = URL('view_wf_process/'.$process_id.'?stage='.$stage_id);
          return json_encode($data);
    }

     public function load_rule_components($workflow_id=0)
    {
        $search = array();
        $search['object_type'] = (Input::get('object_type'))?Input::get('object_type'):'';
        $search['object_id'] = (Input::get('object_id'))?Input::get('object_id'):0;
        $data['status'] = 1;
        $data['rule_components'] = WorkflowsModel::rule_components($search);
        return json_encode($data);
    }
    public function delegateUser() {
      $wf_operation_id  = Input::get('wf_operation_id');
      $wf_stage         = Input::get('wf_stage');
      $data             = array();
      $result           = array();
      $data['wf_operation_id'] = $wf_operation_id;
      $data['wf_stage'] = $wf_stage;
      $data['delegated'] = DB::table('tbl_wf_assigned_users')
                                        ->select()
                                        ->where('operation_id','=',$wf_operation_id)
                                        ->where('stage_id','=',$wf_stage)
                                        ->where('user_id','=',Auth::user()->id)
                                        ->first();
      $data['users']    = DB::table('tbl_users')->select('id','user_full_name','department_id','user_role')->where('id','!=',Auth::user()->id)->get();
      foreach($data['users'] as $val) {
        if($val->user_role == 1)
        {
          $val->user_role = '[SA]';
        }
        elseif($val->user_role == 2)
        {
          $val->user_role = '[DA]';
        }
        elseif($val->user_role == 3)
        {
          $val->user_role = '[RU]';
        }
        elseif($val->user_role == 4)
        {
          $val->user_role = '[PU]';
        }
      }
      $result['html']   = View::make('pages/workflows/ajax_delegate_user')->with($data)->render();

      echo json_encode($result);
    }
    public function delegate_user_save() {
      $msg              = '';
      $status           = 0;
      $flag             = 0;
      $wf_operation_id  = Input::get('wf_operation_id');
      $wf_stage         = Input::get('wf_stage');
      $delegate_user_id = Input::get('delegate_user_id');
      $userdata         = DB::table('tbl_users')->select('user_full_name','delegate_to_date','delegate_from_date')
                                                ->where('id','=',$delegate_user_id)
                                                ->first();
      if($userdata) {
        //Now check this user has an active another delegation
        if($userdata->delegate_from_date!=null && $userdata->delegate_to_date!=null) {
          //Now check the user is available now
          $user_delegate_from_date = new Carbon($userdata->delegate_from_date);
          $user_delegate_to_date   = new Carbon($userdata->delegate_to_date);
          $today                   = new Carbon();
          if($today>=$user_delegate_from_date && $today<=$user_delegate_to_date) {
            $msg = '<div class="alert alert-warning alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h4><i class="icon fa fa-warning"></i> Not available !</h4> User <b>'.$userdata->user_full_name.'</b> is not available from <b>'.$userdata->delegate_from_date.'</b> to <b>'.$userdata->delegate_to_date.'</b></div>';
          }
          else {
            $flag = 1;
          }
        }
        else {
          $flag = 1;
        }

        if($flag==1) {
          //Check table "tbl_wf_assigned_users" has value
          $hasEntry = DB::table('tbl_wf_assigned_users')
                      ->select()
                      ->where('operation_id','=',$wf_operation_id)
                      ->where('stage_id','=',$wf_stage)
                      ->where('user_id','=',Auth::user()->id)
                      ->first();
          if($hasEntry) {
            $updata       = array('delegated_user'=>$delegate_user_id,'updated_at'=>date('Y-m-d H:i:s'));
            $InsertData   = DB::table('tbl_wf_assigned_users')
                              ->where('operation_id','=',$wf_operation_id)
                              ->where('stage_id','=',$wf_stage)
                              ->where('user_id','=',Auth::user()->id)
                              ->update($updata);
            DB::table('tbl_wf_assigned_users')
                      ->where('operation_id','=',$wf_operation_id)
                      ->where('stage_id','=',$wf_stage)
                      ->where('delegated_user','=',Auth::user()->id)
                      ->delete();
          }
          else {
            DB::table('tbl_wf_assigned_users')
                      ->where('operation_id','=',$wf_operation_id)
                      ->where('stage_id','=',$wf_stage)
                      ->where('delegated_user','=',Auth::user()->id)
                      ->delete();
            $updata = array(
                          'operation_id'=>$wf_operation_id,
                          'stage_id'=>$wf_stage,
                          'user_id'=>Auth::user()->id,
                          'action_taken_by'=>0,
                          'delegated_user'=>$delegate_user_id,
                          'activity_id'=>'',
                          'instant_delegation'=>1, 
                          'created_at'=>date('Y-m-d H:i:s'),
                          'updated_at'=>date('Y-m-d H:i:s')
                         );
            $InsertData = DB::table('tbl_wf_assigned_users')->insert($updata);
          }

          if($InsertData) {
            $status = 1;
            $msg    = '<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h4><i class="icon fa fa-warning"></i> Success !</h4>Assigned to user <b>'.$userdata->user_full_name.' </b></div>';
            $recipients                 = array($delegate_user_id);
            $notification               = array();
            $notification['type']       = 'workflow';
            $notification['priority']   = '1';
            $notification['title']      = 'Workflow activity  delegated to you by '.Auth::user()->user_full_name;
            $notification['details']    = '';
            $link                       = URL('view_wf_process/'.$wf_operation_id).'?stage='.$wf_stage;
            $notification['link']       = $link;
            $notification['sender']     = Auth::user()->id;
            $notification['recipients'] = $recipients;
            $this->docObj->add_notification($notification);
          }
        }
      }
      else{
        $msg = '<div class="alert alert-warning alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h4><i class="icon fa fa-warning"></i> Error !</h4> Invalid User</div>';
      }

      echo json_encode(array('msg'=>$msg,'status'=>$status));
    }


    //Closed Workflow
    public function closed_workflow($workflow_id=0)
    {
        // checking wether user logged in or not
        if (Auth::user()) {

            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            $data['users'] = WorkflowsModel::get_all_users();
            $data['workflow_id'] = $workflow_id;
            $this->docObj->common_workflow();
            $data['departments'] = DepartmentsModel::select('department_id','department_name')->orderBy('created_at', 'DESC')->get();

            $data['wfactivities'] = WorkflowsModel::get_activities('form_action');
            $auto_id=0;
            foreach ($data['wfactivities'] as $k => $v) 
            {
                if(strtolower($v->activity_name) == 'auto')
                {
                    $auto_id=$v->activity_id;
                }
            }
            $data['auto_id'] = $auto_id;
            return View::make('pages/workflows/closed_workflow')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

     public function save_closed_workflow($workflow_id=0)
    {
        // checking wether user logged in or not
        if (Auth::user()) 
        {
           $workflow_id = (Input::get('workflow_id'))?Input::get('workflow_id'):0;
            $reload = ($workflow_id)?0:1;
            $timestamp = date("Y-m-d H:i:s");

            $wf_data = array();
            $wf_data['workflow_name'] = (Input::get('workflow_name'))?Input::get('workflow_name'):'';
            $wf_data['workflow_color'] = (Input::get('workflow_color'))?Input::get('workflow_color'):'';
            $wf_data['task_flow'] = (Input::get('task_flow'))?Input::get('task_flow'):1;
            $wf_data['wf_object_type'] = (Input::get('workflow_object_type'))?Input::get('workflow_object_type'):'';
            if($wf_data['wf_object_type'] =='form')
            {
              $wf_object_type_id = (Input::get('form_id'))?Input::get('form_id'):0;  
            }
            else if($wf_data['wf_object_type'] =='document')
            {
              $wf_object_type_id = (Input::get('document_id'))?Input::get('document_id'):0;  
            }
            else
            {
              $wf_object_type_id = 0; 
            }
            $wf_data['wf_object_type_id'] = $wf_object_type_id;   
            $wf_data['updated_at'] = $timestamp;
            $wf_data['updated_by'] = Auth::user()->id;

            $where = array('id' => $workflow_id);
            $result = DB::table('tbl_wf')->where($where)->first(); 
            if($result)
            {
                DB::table('tbl_wf')->where($where)->update($wf_data);
              
            }
            else
            { 
                
                $wf_data['created_at'] = $timestamp;
                $wf_data['created_by'] = Auth::user()->id;
                $workflow_id = DB::table('tbl_wf')->insertGetId($wf_data);
            }
       


          /*########### SAVE STAGES DETAILS START ##########*/  

        $workflow_stages = (Input::get('workflow_stages'))?Input::get('workflow_stages'):array(); 
        $i=1;
        $reset = array('edit' => 0);
        $reset_state = array('workflow_id' => $workflow_id);
        $state_array= array();
        DB::table('tbl_wf_states')->where($reset_state)->update($reset);

          foreach ($workflow_stages as $key => $value) 
              {
                
              $stype = (isset($value['stype']) && $value['stype'])?$value['stype']:'middle';
              if($stype == 'middle')
              {
                $i++;

              }
              $mark = $i;
              if($stype == 'first')
              {
                $mark = 1;

              }

              if($stype == 'last')
              {
                $mark = count($workflow_stages);

              }

              $id = (isset($value['id']))?$value['id']:0;  
              $state = (isset($value['label']))?$value['label']:'stage';
              $shape = (isset($value['shape']))?$value['shape']:'box';
              $description = (isset($value['description']))?$value['description']:$state;

              $dbid = (isset($value['dbid']))?$value['dbid']:0;
              $stage_action = (isset($value['stage_action']))?$value['stage_action']:1;
              $stage_group = (isset($value['stage_group']))?$value['stage_group']:0;
              $stage_percentage = (isset($value['stage_percentage']))?$value['stage_percentage']:0;
              $departments =  (isset($value['stage_departments']) && $value['stage_departments'])?$value['stage_departments']:array(); 
              $department_users =  (isset($value['stage_user']) && $value['stage_user'])?$value['stage_user']:array(); 
              $escallation_stage = (isset($value['escallation_stage']))?$value['escallation_stage']:'';
              $escallation_day = (isset($value['escallation_day']))?$value['escallation_day']:0;
              $escallation_activity_id = (isset($value['escallation_activity_id']))?$value['escallation_activity_id']:0;
              $other_user =null;
              $message_content =null;
              $notify_requester=0;
              $requester_attachment=0;
              $user_attachment=0;
              $department_attachment=0;
              $external_attachment=0;
              if($stype == 'last')
              {
                $stage_action =0;
                $departments =  (isset($value['notify_dep']) && $value['notify_dep'])?$value['notify_dep']:array(); 
                $department_users =  (isset($value['notify_user']) && $value['notify_user'])?$value['notify_user']:array();
                $other_user =  (isset($value['notify_others']) && $value['notify_others'])?$value['notify_others']:null;  
                $message_content =  (isset($value['notify_message']) && $value['notify_message'])?$value['notify_message']:null; 
                $notify_requester =  (isset($value['notify_requester']) && ($value['notify_requester'] == 'true'))?1:0; 

                $requester_attachment =  (isset($value['requester_attachment']) && ($value['requester_attachment'] == 'true'))?1:0; 
                $user_attachment =  (isset($value['user_attachment']) && ($value['user_attachment'] == 'true'))?1:0; 
                $department_attachment =  (isset($value['department_attachment']) && ($value['department_attachment'] == 'true'))?1:0; 
                $external_attachment =  (isset($value['external_attachment']) && ($value['external_attachment'] == 'true'))?1:0; 
                
              }
              $stages = array();
              $stages['workflow_id'] = $workflow_id;
              $stages['type'] = $stype;
              $stages['state'] = $state;
              $stages['description'] = $description;
              $stages['shape'] = $shape;
              $stages['updated_at'] = $timestamp;
              $stages['edit'] = 1;
              $stages['mark'] = $mark;
              $stages['stage_action'] = $stage_action;
              $stages['stage_group'] = $stage_group;
              $stages['stage_percentage'] = $stage_percentage;
              $stages['departments'] = serialize($departments);
              $stages['assigned_users'] = serialize($department_users);
              $stages['other_user'] = $other_user;
              $stages['message_content'] = $message_content;
              $stages['notify_requester'] = $notify_requester;

              $stages['requester_attachment'] = $requester_attachment;
              $stages['user_attachment'] = $user_attachment;
              $stages['department_attachment'] = $department_attachment;
              $stages['external_attachment'] = $external_attachment;

              $where_state = array('id' => $dbid,'workflow_id' => $workflow_id);
              $result = DB::table('tbl_wf_states')->where($where_state)->first(); 
              if($result)
              {
                  
                  DB::table('tbl_wf_states')->where($where_state)->update($stages);
                  $state_id =$result->id;
                  $state_array[$id] = $state_id;
                  
                            
              }
              else
              { 
                  $stages['created_at'] = $timestamp;
                  $state_id = DB::table('tbl_wf_states')->insertGetId($stages);
                  $state_array[$id] = $state_id;
              }   

        } 



        foreach ($workflow_stages as $key => $value) 
        {
          $id = (isset($value['id']))?$value['id']:0;  
          $escallation_stage = (isset($value['escallation_stage']))?$value['escallation_stage']:'';
          $escallation_activity_id = (isset($value['escallation_activity_id']))?$value['escallation_activity_id']:'';
          $escallation_day = (isset($value['escallation_day']))?$value['escallation_day']:0;
              $escallation_stage = (isset($state_array[$escallation_stage]))?$state_array[$escallation_stage]:0;
          $stages = array();
          $stages['escallation_stage'] = $escallation_stage;
          $stages['escallation_activity_id'] = $escallation_activity_id;
          $stages['escallation_day'] = $escallation_day;
              
          $dbid = (isset($state_array[$id]))?$state_array[$id]:0;
              $where_state = array('id' => $dbid,'workflow_id' => $workflow_id);
          $result = DB::table('tbl_wf_states')->where($where_state)->first(); 
          if($result)
          {
                  
              DB::table('tbl_wf_states')->where($where_state)->update($stages);           
            }

        }

        $reset_state = array('workflow_id' => $workflow_id,'edit' => 0);
        DB::table('tbl_wf_states')->where($reset_state)->delete();

        /*########### SAVE STAGES DETAILS END ##########*/


        /*############# SAVE TRANSITION START ###########*/

        $workflow_edges = (Input::get('workflow_edges'))?Input::get('workflow_edges'):array(); 
        $i=1;
        $reset = array('edit' => 0);
        $reset_state = array('workflow_id' => $workflow_id);
        DB::table('tbl_wf_transitions')->where($reset_state)->update($reset);
        foreach ($workflow_edges as $key => $value) 
        {
                $i++;
                
              $name = (isset($value['name']) && $value['name'])?$value['name']:'Action';
              $activity_id = (isset($value['activity_id']))?$value['activity_id']:0;

              $dbid = (isset($value['dbid']))?$value['dbid']:0;
              $from_state = (isset($value['from_state']))?$value['from_state']:0;
              $to_state = (isset($value['to_state']))?$value['to_state']:0;
              $rules_basic = array();

              $from_state = (isset($state_array[$from_state]))?$state_array[$from_state]:0;
              $to_state = (isset($state_array[$to_state]))?$state_array[$to_state]:0;
              if($from_state)
              {  
              $with_rule = (isset($value['with_rule']))?$value['with_rule']:0;
              $rule_area= $rules_data = array();


              
              $transitions = array();
              $transitions['workflow_id'] = $workflow_id;
              $transitions['name'] = $name;
              $transitions['activity_id'] = $activity_id;
              $transitions['from_state'] = $from_state;
              $transitions['to_state'] = $to_state;
              $transitions['updated_at'] = $timestamp;
              $transitions['edit'] = 1;
              $transitions['tr_order'] = $i;
              $transitions['with_rule'] = $with_rule;
               // $transitions['rule_area'] = $rules_basic;
              $transitions['rule_area'] = serialize($rule_area);
              $transitions['rule_array'] = serialize($rules_data);
              $where_state = array('id' => $dbid,'workflow_id' => $workflow_id);
              $result = DB::table('tbl_wf_transitions')->where($where_state)->first(); 
              if($result)
              {
                  
                  $wf_transition_id = $dbid; 
                  DB::table('tbl_wf_transitions')->where($where_state)->update($transitions);
                            
              }
              else
              {
                  $stages['created_at'] = $timestamp;

                  $wf_transition_id = DB::table('tbl_wf_transitions')->insertGetId($transitions);

              }

              //Reset table
              $reset_state = array('transition_id' => $wf_transition_id);
              DB::table('tbl_wf_transition_rule')->where($reset_state)->delete(); 
              if($with_rule)
              {
                $sort=0;
                $rule_data = (isset($value['rule_data']))?$value['rule_data']:array();
               /* echo "<pre>";
                print_r($rule_data);
                echo "</pre>";*/
                $final_rule=array();
                $stage_array=array(); /*to remove*/
                 foreach ($rule_data as $keyt => $valuet) 
                {

                 $rgcount = (isset($valuet['rgcount']))?$valuet['rgcount']:0; 
                 if(isset($valuet['type']) && ($valuet['type'] == 'group'))
                 {
                        $if_stage = (isset($valuet['if_stage']))?$valuet['if_stage']:0;
                        $valuet['if_stage'] = (isset($state_array[$if_stage]))?$state_array[$if_stage]:0;

                        $else_stage = (isset($valuet['else_stage']))?$valuet['else_stage']:0; 
                        $valuet['else_stage'] = (isset($state_array[$else_stage]))?$state_array[$else_stage]:0;

                        if(!array_key_exists($rgcount,$stage_array))
                        {
                          $a =array();
                          $a['if_stage'] = $valuet['if_stage'];
                          $a['else_stage'] = $valuet['else_stage'];
                          $stage_array[$rgcount]= $a;
                        }
                       
                 }

                 if(array_key_exists($rgcount,$final_rule))
                 {
                    $final_rule[$rgcount][]=$valuet;
                    //echo "aaaa";
                 }
                 else
                 {
                   $final_rule[$rgcount]=array();
                   $final_rule[$rgcount][]=$valuet;
                   //echo "dddd";
                 } 
                } 

               /* echo "<pre>";
                print_r($final_rule);
                echo "</pre>";
                echo "<pre>";
                print_r($stage_array);
                echo "</pre>";*/
                foreach ($final_rule as $keyf => $valuef) 
                {
                $sort++;
                $rule_area = WorkflowsModel::getNestedChildren($valuef);
                $transition_rule = array();
                $transition_rule['transition_id'] = $wf_transition_id;
                $transition_rule['rule_condition'] = ($sort == 1)?'if':'else_if';
                $transition_rule['rule_area'] = serialize($rule_area);
                $transition_rule['rule_array'] = serialize($valuef);
                $transition_rule['if_stage'] = (isset($stage_array[$keyf]['if_stage']))?$stage_array[$keyf]['if_stage']:0;
                $transition_rule['else_stage'] = (isset($stage_array[$keyf]['else_stage']))?$stage_array[$keyf]['else_stage']:0;

                $transition_rule['edit'] = 1;
                $transition_rule['sort_order'] = $sort;
                $transition_rule['created_at'] = $timestamp;
                $transition_rule['updated_at'] = $timestamp;
                DB::table('tbl_wf_transition_rule')->insert($transition_rule);
                }
              }    

              } 
        }
        $reset_state = array('workflow_id' => $workflow_id,'edit' => 0);
        DB::table('tbl_wf_transitions')->where($reset_state)->delete();    

        /*############# SAVE TRANSTIONS END ############ */


        $data = array(); 
        $data['status'] = 1;
        $data['workflow_id'] = $workflow_id;
        $data['state_array'] = $state_array;
        $data['url']=URL('closed_workflow/'.$workflow_id);
        $data['reload']=$reload;
        $message = '<div class="alert alert-success text-center">Workflow saved successfully</div>';
        $data['message'] = $message;
        return json_encode($data);           
              
        }
        else 
        {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }


     public function load_Workflow_json_data($workflow_id=0)
    {
        $workflow_id = (Input::get('workflow_id'))?Input::get('workflow_id'):0;
        $data['status'] = 1;

        $data['workflow_id'] = $workflow_id;

        //Fetch Workflow Details
        $where = array('id' => $workflow_id);
        $wf_data = DB::table('tbl_wf')->where($where)->first();

        //Fetch Workflow Stages
        $where_state = array('workflow_id' => $workflow_id);
        $wf_states = DB::table('tbl_wf_states')->where($where_state)->orderBy('tbl_wf_states.mark','asc')->get(); 

        //Fetch Workflow Transitions
        $where_state = array('workflow_id' => $workflow_id);
        $wf_transitions = DB::table('tbl_wf_transitions')->where($where_state)->orderBy('tbl_wf_transitions.tr_order','asc')->get(); 
        $wf_state = $wf_transition = $sel_departments = $sel_department_users = array();

        $workflow_color = '#c0c0c0'; 
        $workflow_name = $deadline_type  = $deadline_value  ='';
        $task_flow=2;
        $wf_object_type ='';
        $wf_object_type_id  = $deadline  = 0;
        if($wf_data)
        {
           $workflow_name =  $wf_data->workflow_name;
           $workflow_color =  $wf_data->workflow_color;
           $task_flow =  $wf_data->task_flow;
           $wf_object_type =  $wf_data->wf_object_type;
           $wf_object_type_id  =  $wf_data->wf_object_type_id ;
           $deadline  =  $wf_data->deadline ;
           $deadline_type  =  $wf_data->deadline_type ;
           $deadline_value  =  $wf_data->deadline_value;
           $sel_departments  =  (isset($wf_data->departments) && $wf_data->departments)?unserialize($wf_data->departments):array();
           $sel_department_users  =  (isset($wf_data->assigned_users) && $wf_data->assigned_users)?unserialize($wf_data->assigned_users):array();
        }
        $s=$t=1;
        $i=0;
        $states_array = array();
        foreach ($wf_states as $value) 
        {
            $i++;
            $states_array[$value->id] = $i;
        }
        $i=0;
        foreach ($wf_states as $value) 
        {
            $i++;
            $row = array();
            $row['id']    = $s;
            $row['dbid']  = $value->id;
            $row['label'] = $value->state;
            $row['description'] = ($value->description)?$value->description:$value->state;
            $row['shape'] = $value->shape;
            $row['color'] = $workflow_color;
            $row['type']  = 'node';
            $row['stype']  = $value->type;

            $sel_departments_stages  =  (isset($value->departments) && $value->departments)?unserialize($value->departments):array();
            $sel_department_users_stages  =  (isset($value->assigned_users) && $value->assigned_users)?unserialize($value->assigned_users):array(); 
            $row['stage_action'] = ($value->stage_action)?$value->stage_action:1;
            $row['stage_group'] = $value->stage_group;
            $row['stage_percentage'] = $value->stage_percentage;  
            $row['sel_departments'] = $sel_departments_stages;
            $row['sel_department_users'] = $sel_department_users_stages;
            $row['department_users'] = $this->docObj->department_users_list($sel_departments_stages);
            $row['escallation_stage'] = (isset($states_array[$value->escallation_stage]))?$states_array[$value->escallation_stage]:0;
            $row['escallation_day'] = ($value->escallation_day)?$value->escallation_day:0;
            $row['escallation_activity_id'] = (isset($value->escallation_activity_id))?$value->escallation_activity_id:0;
            $row['message_content'] = ($value->message_content)?$value->message_content:'';
            $row['other_user'] = ($value->other_user)?$value->other_user:'';
            $row['notify_requester'] = ($value->notify_requester)?$value->notify_requester:0;

            $row['requester_attachment'] = ($value->requester_attachment)?$value->requester_attachment:0;
            $row['user_attachment'] = ($value->user_attachment)?$value->user_attachment:0;
            $row['department_attachment'] = ($value->department_attachment)?$value->department_attachment:0;
            $row['external_attachment'] = ($value->external_attachment)?$value->external_attachment:0;
            $wf_state[]   = $row;
            $s++;

        }

        foreach ($wf_transitions as $value) 
        {
            $i++;
            $row = array();
            $row['id'] = $t;
            $row['dbid'] = $value->id;
            $row['label'] = ($value->name)?$value->name:'Action';
            $row['activity_id'] = ($value->activity_id)?$value->activity_id:0;
            $row['description'] = $value->name;
            $row['from'] = (isset($states_array[$value->from_state]))?$states_array[$value->from_state]:0;
            $row['to'] = (isset($states_array[$value->to_state]))?$states_array[$value->to_state]:0;
            $row['arrows'] = 'to';
                  $row['type'] = 'edge';
            $row['with_rule'] = $value->with_rule; 
            if($value->with_rule)
            {
              $wh = array('transition_id' => $value->id);
              $rule_result = DB::table('tbl_wf_transition_rule')->where($wh)->orderBy('sort_order','ASC')->get();
              $stage_action_rules = array();
              $rc_count=sizeof($rule_result);
              $loop=0;
              foreach ($rule_result as $key1 => $value1) 
              {
                $loop++;
                $r['rule_condition'] = (isset($value1->rule_condition))?$value1->rule_condition:'if'; 
                $r['rule_data'] = (isset($value1->rule_array) && $value1->rule_array)?unserialize($value1->rule_array):array(); 
                $if_stage = $value1->if_stage;
                $r['if_stage'] = (isset($states_array[$if_stage]))?$states_array[$if_stage]:0; 
                $else_stage = $value1->else_stage;
                $r['else_stage'] = (isset($states_array[$else_stage]))?$states_array[$else_stage]:0;
                $r['enable_else'] = (($rc_count == $loop) && $r['else_stage'])?1:0;
                $stage_action_rules[]= $r;

              }
             $row['stage_action_rules'] =  $stage_action_rules; 
            } 
            $rules_basic_res =($value->rule_area)?unserialize($value->rule_area):array('condition' => 'AND','rules' => array());   
            /*$row['rules_basic'] =  json_encode($rules_basic);*/
            $rules_basic = array();
            $rules_basic['condition'] = (isset($rules_basic_res['condition']))?$rules_basic_res['condition']:'AND';
            $rules_basic['rules'] = (isset($rules_basic_res['rules']))?$rules_basic_res['rules']:array();
            $rules_basic['stage_case'] = (isset($rules_basic_res['stage_case']))?$rules_basic_res['stage_case']:'';
             $rules_basic['if_stage'] = (isset($rules_basic_res['if_stage']))?$rules_basic_res['if_stage']:'';
              $rules_basic['else_stage'] = (isset($rules_basic_res['else_stage']))?$rules_basic_res['else_stage']:'';
            $row['rules_basic'] =  $rules_basic;


            $rules_data = (isset($value->rule_array) && $value->rule_array)?unserialize($value->rule_array):array('condition' => 'AND','rules' => array()); 

            foreach ($rules_data as $key => $value) 
            {
                 if(isset($value['type']) && ($value['type'] == 'group'))
                 {
                        $if_stage = $value['if_stage'];
                        $rules_data[$key]['if_stage'] = (isset($states_array[$if_stage]))?$states_array[$if_stage]:0;

                        $else_stage = $value['else_stage']; 
                        $rules_data[$key]['else_stage'] = (isset($states_array[$else_stage]))?$states_array[$else_stage]:0;
                        
                 } 
            }
            $row['rule_data_old'] =  $rules_data;   

            $wf_transition[] = $row;
            $t++;
        }

        $data['workflow_name'] = $workflow_name;
        $data['workflow_color'] = $workflow_color;
        $data['task_flow'] = $task_flow;
        $data['wf_object_type'] = $wf_object_type;
        $data['wf_object_type_id'] = $wf_object_type_id;
        $data['deadline'] = $deadline;
        $data['deadline_type'] = $deadline_type;
        $data['deadline_value'] = $deadline_value;
        $data['wf_states'] = $wf_state;
        $data['wf_transitions'] = $wf_transition;
        $data['stage_count'] = $s;
        $data['action_count'] = $t;
        $data['taskcount'] = $i;
        $data['sel_departments'] = $sel_departments;
        $data['sel_department_users'] = $sel_department_users;
        $data['department_users'] = $this->docObj->department_users_list($sel_departments);
        $data['wf_privilages']=  WorkflowsModel::wf_privilages($workflow_id);

        $search['object_type'] = $wf_object_type;
        $search['object_id'] = $wf_object_type_id;
        $data['rule_components'] = WorkflowsModel::rule_components($search);
        return json_encode($data);
            }

}/*<--END-->*/