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
use App\WorkflowModel as WorkflowModel;
// Common Models
use App\StacksModel as StacksModel;
use App\DepartmentsModel as DepartmentsModel;
use App\DocumentTypesModel as DocumentTypesModel;
use App\FormModel as FormModel;
use DB;
use Lang;

class WorkflowController extends Controller
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
    
    public function index() {
        if (Auth::user()) {
            /*<--Common-->*/
            Session::put('menuid', '12');
            $data['docType'] = DocumentTypesModel::all();
                
                $data['stckApp'] = $this->docObj->common_stack();
                $data['deptApp'] = $this->docObj->common_dept();
                $data['doctypeApp'] = $this->docObj->common_type();
                $data['records'] = $this->docObj->common_records();
            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;
            /*<--// Common-->*/
            $data['workflows'] = DB::table('tbl_workflows')->select('workflow_name','workflow_color','workflow_id')->groupBy('workflow_id')->get();

            $data['count_stages'] = DB::table('tbl_workflows')->select('workflow_stage_order','workflow_stage_name','workflow_stage_id')->count();
            $last = DB::table('tbl_workflows')
            ->select('workflow_stage_id')
            ->orderBy('workflow_stage_id','DESC')
            ->first();
            $data['last'] = $last->workflow_stage_id;
            foreach ($data['workflows'] as $workflow) {

                $workflow->stage = DB::table('tbl_workflows')->select('workflow_stage_order','workflow_stage_name','workflow_stage_id')->where('workflow_id',$workflow->workflow_id)->orderBy('workflow_stage_order','ASC')->get(); 
                $query = DB::table('tbl_document_workflows as tdw');
                $query->join('tbl_workflows as tw','tdw.workflow_stage_id','=','tw.workflow_stage_id');
                $query->select(DB::raw('count(distinct(tdw.document_workflow_object_id)) as numc'));
                $workflow->count = $query->where('tw.workflow_id',$workflow->workflow_id)->groupBy('tw.workflow_id')->get();
            }
            // echo '<pre>';
            // print_r($data['workflows']);
            // exit;
            return View::make('pages/workflow/index')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    Public function selectStages()
    {
        $wfid = Input::get('wfid');
        $stages['wf_stages'] = DB::table('tbl_workflows')->select('workflow_stage_order','workflow_stage_name','workflow_stage_id')->where('workflow_id',$wfid)->orderBy('workflow_stage_order','ASC')->get();
        return json_encode($stages);
        
    }
    public function WorkflowSelectSave(Request $request,$id)
    {
        if (Auth::user()) {
            //insert to document_workflow
            $wf_id = (Input::get('workflow_select'))?Input::get('workflow_select'):0;
             $select_stage = (Input::get('select_stage'))?Input::get('select_stage'):0;
            $activity_name = (Input::get('doc_activity_name'))?Input::get('doc_activity_name'):'';     
            $activity_date = date("Y-m-d");
            $activity_due_date = (Input::get('doc_activity_due_date'))?date("Y-m-d",strtotime(Input::get('doc_activity_due_date'))):null;          
            $assigned_to = (Input::get('doc_assigned_to'))?Input::get('doc_assigned_to'):'';
            $activity_note = (Input::get('doc_activity_note'))?Input::get('doc_activity_note'):'';
            $wf_activity_id = DB::table('tbl_document_workflows')->insertGetId([
                'document_workflow_object_id'=>$id,
                'document_workflow_object_type'=>'document',
                'workflow_stage_id'=>$select_stage,
                'activity_id'=>$activity_name,
                'document_workflow_responsible_user'=>$assigned_to,
                'document_workflow_notifcation_to_status'=>1,
                'document_workflow_activity_by_user'=>Auth::user()->username,
                'document_workflow_activity_date'=>$activity_date,
                'document_workflow_activity_due_date'=>$activity_due_date,
                'document_workflow_activity_notes'=>$activity_note]);

            if($assigned_to)
                  {
                    $where = array('username' => $assigned_to);
                    $result = DB::table('tbl_users')->where($where)->first();
                        
                    if($result && $result->id)
                    {    
                    $recipients = array($result->id);
                    $notification = array();
                    $notification['type']='workflow';
                    $notification['priority']='1';
                    $notification['title'] = 'Workflow Activity "'.$activity_name.'" assigned by '.Auth::user()->user_full_name;
                    $notification['details']='';
                    $notification['link']='viewworkflow/'.$wf_id.'?activity_view='.$wf_activity_id.'&object_id='.$id.'&object_type=document';
                    $notification['sender']=Auth::user()->id;
                    $notification['recipients']=$recipients;
                    $this->docObj->add_notification($notification);
                        }
                  }
                  return redirect('viewworkflow/'.$wf_id);
        }
        else
        {
           return redirect('')->withErrors("Please login")->withInput(); 
        }
    }
    Public function addviewWorkflow()
    {
        $docid = Input::get('docid');
        $docname = DB::table('tbl_documents')->select('document_name')->where('document_id',$docid)->first();
        $data['document'] = $docname->document_name;
        $data['docid'] = $docid;
        $data['activities'] = WorkflowModel::get_activities('workflows');
        $data['user'] = WorkflowModel::users_list();
        $check_exists = DB::table('tbl_document_workflows')->where('document_workflow_object_id',$docid)->where('document_workflow_object_type','document')->first();
        if ($check_exists == null || $check_exists == "") {
           $data['workflows'] = DB::table('tbl_workflows')->select('workflow_name','workflow_color','workflow_id')->groupBy('workflow_id')->get();
        $data['exists'] ='0';
        }
        else
        {
            $query = DB::table('tbl_document_workflows as tdw');
            $query->join('tbl_workflows as tw','tdw.workflow_stage_id','=','tw.workflow_stage_id');
            $select ="tw.workflow_name,tw.workflow_color,tw.workflow_id";
            $query->selectRaw($select);
            $query->where('tdw.document_workflow_object_id',$docid);
            $query->distinct('tw.workflow_id');
            $data['workflows'] = $query->get();
            foreach ($data['workflows'] as $key) {
                $key->stage_count = DB::table('tbl_workflows')->select('workflow_stage_id')->where('workflow_id',$key->workflow_id)->count();
            }
            $data['exists'] ='1';
        }
        // print_r($data['workflows']);
        // exit();
        return view::make('pages/workflow/add_view')->with($data);
    }
    public function workflowsave(Request $request,$id)
    {
        if(Auth::user())
        {//save
                $workflow_name = Input::get('workflowname');
                $workflow_color = Input::get('color');
                $stage_name = Input::get('stage_name');
                $stage_name_array = array_combine(range(1, count($stage_name)), $stage_name);
            if($id == 0)
            {
                //last stage is set as the workflow_id of workflow
                $last_stage_id = DB::table('tbl_workflows')->select('workflow_stage_id')->orderBy('workflow_stage_id', 'desc')->first();
                if($last_stage_id)
                {
                    $workflow_id = $last_stage_id->workflow_stage_id+1;
                }
                else
                {
                    $workflow_id = 1;
                }
                for($i=1;$i<=count($stage_name_array);$i++)
                {
                    DB::table('tbl_workflows')->insert(['workflow_id'=>$workflow_id,'workflow_name'=>$workflow_name,'workflow_color'=>$workflow_color,'workflow_stage_order'=>$i,'workflow_stage_name'=>$stage_name_array[$i],'workflow_added_by'=>Auth::user()->username]);
                }
                return back();
            }
            //update
            else
            {
                $count_stages_bef = Input::get('hidd_count_stages');
                $count_stages_edit = Input::get('count-textbox');
                $total_stages = ($count_stages_bef+$count_stages_edit);
                for($i=1;$i<=$total_stages;$i++)
                {    
                    $stages_edit = Input::get('stagefield'.$i);
                    //new stages insert here                   
                    if($stages_edit==0)
                    {
                        DB::table('tbl_workflows')->insert(['workflow_id'=>$id,'workflow_name'=>$workflow_name,'workflow_color'=>$workflow_color,'workflow_stage_order'=>$i,'workflow_stage_name'=>$stage_name_array[$i],'workflow_added_by'=>Auth::user()->username]);
                    }
                    //existing stages are update here
                    else
                    {
                        DB::table('tbl_workflows')->where('workflow_id',$id)->where('workflow_stage_id',$stages_edit)->update(['workflow_name'=>$workflow_name,'workflow_color'=>$workflow_color,'workflow_stage_order'=>$i,'workflow_stage_name'=>$stage_name_array[$i],'workflow_added_by'=>Auth::user()->username]);
                    }    
                }   
            }
            return back();
        }
        else
        {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function workflowEdit($wf_id)
    {
        /*<--Common-->*/
            $data['docType'] = DocumentTypesModel::all(); 
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;
        /*<--// Common-->*/
            $data['workflow_single'] = DB::table('tbl_workflows')->select('workflow_name','workflow_color','workflow_id')->where('workflow_id',$wf_id)->groupBy('workflow_id')->get();
            foreach ($data['workflow_single'] as $key => $workflow) {
                $workflow->stage = DB::table('tbl_workflows')->select('workflow_stage_order','workflow_stage_name','workflow_stage_id')->where('workflow_id',$workflow->workflow_id)->orderBy('workflow_stage_order','ASC')->get(); 
            }
            $data['count_stages'] = DB::table('tbl_workflows')->where('workflow_id',$wf_id)->count();
            $last = DB::table('tbl_workflows')
            ->select('workflow_stage_id')
            ->orderBy('workflow_stage_id','DESC')
            ->first();
            $data['last'] = $last->workflow_stage_id;
            $data['wf_id'] = $wf_id;
            return View::make('pages/workflow/edit')->with($data);
    }
    public function renameNewStage()
    {
        $wf_id = Input::get('wf_id');
        $stage_id = Input::get('stage_id');
        $stage_name = Input::get('stage_name');
        $update = DB::table('tbl_workflows')->where('workflow_stage_id',$stage_id)->where('workflow_id',$wf_id)->update(['workflow_stage_name'=>$stage_name]);
        if($update)
        {
            echo "stage updated successfully";
        }
        else
        {
            $data_related_workflow = DB::table('tbl_workflows')->select('workflow_name','workflow_color')->where('workflow_id',$wf_id)->first();
            DB::table('tbl_workflows')->insert(['workflow_id'=>$wf_id,'workflow_name'=>$data_related_workflow->workflow_name,'workflow_color'=>$data_related_workflow->workflow_color,'workflow_stage_name'=>$stage_name,'workflow_added_by'=>Auth::user()->username]);
            echo "new stage inserded successfully";
        }
    }
    public function workflowDelete()
    {
        $wf_id = Input::get('id');
        $exist = 0;
        //stages under selected workflow
        $workflow_stages = DB::table('tbl_workflows')->select('workflow_stage_id','workflow_name')->where('workflow_id',$wf_id)->orderBy('workflow_stage_id')->get();
        $workflow_name = $workflow_stages[0]->workflow_name;
        foreach ($workflow_stages as $key => $value) {
            $check_stage_exists = DB::table('tbl_document_workflows')->where('workflow_stage_id',$value->workflow_stage_id)->exists();
            if($check_stage_exists == 1)
            {
                $exist = 1;
                break;
            }
        
        }
        
        if($exist == 1)
        {
            echo "Sorry, there are entries under workflow '".$workflow_name."'";
        }
        else
        {
            DB::table('tbl_workflows')->where('workflow_id',$wf_id)->delete();
            echo "Workflow '".$workflow_name."' deleted successfully";
        }
    }
    public function workflowReArrangeStages()
    {
        $data =Input::get('data');
        $i=1;
        foreach ($data as $val) {
            DB::table('tbl_workflows')->where('workflow_stage_id',$val)->update(['workflow_stage_order'=>$i]);
            $i++;
        }
    }
    public function addNewStage()
    {
        $wf_id = Input::get('wf_id');
        $stage_id = Input::get('stage_id');
        $stage_name = 'New stage';
        $data =Input::get('data');
        $wf_details = DB::table('tbl_workflows')->select('workflow_name','workflow_color','workflow_added_by')->where('workflow_id',$wf_id)->first();
        $wf_name = $wf_details->workflow_name;
        $wf_color = $wf_details->workflow_color;
        $wf_added_by = $wf_details->workflow_added_by;
        $i=1;
        foreach ($data as $val) {
            if(isset($val))
            {
                $result = DB::table('tbl_workflows')->where('workflow_id',$wf_id)->where('workflow_stage_id',$val)->first();
                if($result)
                {
                    DB::table('tbl_workflows')->where('workflow_stage_id', $result->workflow_stage_id)->update(['workflow_stage_order'=>$i,'workflow_updated_by'=>Auth::user()->username]);
                    echo $i." updated \n";
                }
                else
                {
                    DB::table('tbl_workflows')->insert(['workflow_stage_id'=>$val,'workflow_stage_name'=>$stage_name,'workflow_stage_order'=>$i,'workflow_id'=>$wf_id,'workflow_name'=>$wf_name,'workflow_color'=>$wf_color,'workflow_added_by'=>$wf_added_by,'workflow_updated_by'=>Auth::user()->username]);
                    echo $i." inserted \n";
                }
            }
            $i++;
        }
    }
    public function workflowStageDelete()
    {
        if (Auth::user()) {
            $id= Input::get('id');
            $stage_name = Input::get('stage_name');
            $check_stage_exists = DB::table('tbl_document_workflows')->where('workflow_stage_id',$id)->exists();
            if($check_stage_exists ==1)
            {
                //echo "Sorry, there are entries under the stage '".$stage_name."'";
                echo "1";
            }
            else
            {
                DB::table('tbl_workflows')->where('workflow_stage_id',$id)->delete();
                echo "Stage '".$stage_name."' Deleted";
            }
        }
        else
        {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    //workflow by faisal Start

      public function view_workflow($id=0)
    {
        if(Auth::user())
        {
          $data = array();
          /*<--Common-->*/
            $data['docType'] = DocumentTypesModel::all();
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            /*<--// Common-->*/
            $data['workflows'] = WorkflowModel::get_workflows();
            $data['workflow_id'] = $id;
            $data['activities'] = WorkflowModel::get_activities('workflows');
            $data['user'] = WorkflowModel::users_list();
            $data['wf_stage_count'] = WorkflowModel::get_count_stages($id);
            $action_from = (Input::get('action_from'))?Input::get('action_from'):'';
            if($action_from == 'notification')
            {
                WorkflowModel::read_notification($id,Auth::user()->username);
                $this->docObj->get_workflow_notification();
            }
            
           
            
            $data['object_id']= (Input::get('object_id'))?Input::get('object_id'):'';
            $data['object_type']= $object_type = (Input::get('object_type'))?Input::get('object_type'):'';

            $data['activity_view']= (Input::get('activity_view'))?Input::get('activity_view'):'';

         if($object_type == 'document')
          {
            $data['wf_docs'] = WorkflowModel::get_workflow_docs($id);
            $data['wf_forms'] = array();
          }
          elseif($object_type == 'form')
          {
            $data['wf_docs'] = array();
            $data['wf_forms'] = WorkflowModel::get_workflow_forms($id);
          }
          else
          {
            $data['wf_docs'] = WorkflowModel::get_workflow_docs($id);
            $data['wf_forms'] = WorkflowModel::get_workflow_forms($id);
          }
             return View::make('pages/workflow/view_workflow')->with($data);
        }
        else
        {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

     public function workflow_stages()
    {
          $workflow_id = (Input::get('workflow_id'))?Input::get('workflow_id'):0;
          $object_id = (Input::get('object_id'))?Input::get('object_id'):'';
          $object_type = (Input::get('object_type'))?Input::get('object_type'):'';
           //Access Control
            $wf_users = array(Auth::user()->username); 
            $super_admin=0;  
            switch (Auth::user()->user_role) 
          {    
            case Session::get("user_role_super_admin")://super admin
              
            $super_admin=1;
            break;
            case Session::get("user_role_group_admin")://group admin
            $auth_dep_users = DB::table('tbl_users_departments as td')
            ->join('tbl_users as tu','tu.id','=','td.users_id')
            ->select('tu.username')
            ->whereIn('td.department_id',Session::get('auth_user_dep_ids'))->get();
            
            foreach ($auth_dep_users as $value) 
            {
              
                $wf_users[] = $value->username;
            }
            break;
          }      
          $data['super_admin'] = $super_admin;
          $data['wf_users'] = $wf_users;
          config(['app.workflow_doc_id' => $object_id]);
          config(['app.workflow_object_type' => $object_type]);
          $data['status'] = 1;
          $data['workflow_id'] = $workflow_id;
          $data['wf_details'] = WorkflowModel::get_workflow($workflow_id);
          $data['wf_stage_details'] = WorkflowModel::get_workflow_stage_details($workflow_id);
          $data['html'] = View::make('pages/workflow/ajax_workflow')->with($data)->render();
          return json_encode($data);
    }

     public function get_workflow_docs()
    {
          $workflow_id = (Input::get('workflow_id'))?Input::get('workflow_id'):0;
          $object_type = (Input::get('object_type'))?Input::get('object_type'):0;
          $data['status'] = 1;
          if($object_type == 'document')
          {
            $data['wf_docs'] = WorkflowModel::get_workflow_docs($workflow_id);
            $data['wf_forms'] = array();
          }
          elseif($object_type == 'form')
          {
            $data['wf_docs'] = array();
            $data['wf_forms'] = WorkflowModel::get_workflow_forms($workflow_id);
          }
          else
          {
            $data['wf_docs'] = WorkflowModel::get_workflow_docs($workflow_id);
            $data['wf_forms'] = WorkflowModel::get_workflow_forms($workflow_id);
          }
          
          return json_encode($data);
    }

     public function workflow_activity_save()
    {
          $wf_id = (Input::get('wf_id'))?Input::get('wf_id'):0;
          $wf_activity_id = (Input::get('wf_activity_id'))?Input::get('wf_activity_id'):0;
          $wf_stage_id = (Input::get('wf_stage_id'))?Input::get('wf_stage_id'):0;
          $wf_object_id = (Input::get('wf_object_id'))?Input::get('wf_object_id'):'';
          $wf_object_type = (Input::get('wf_object_type'))?Input::get('wf_object_type'):'document';
          $activity_name = (Input::get('activity_name'))?Input::get('activity_name'):0;     
          $activity_date = date("Y-m-d");
          $activity_due_date = (Input::get('activity_due_date'))?date("Y-m-d",strtotime(Input::get('activity_due_date'))):NULL;          
          $assigned_to = (Input::get('assigned_to'))?Input::get('assigned_to'):'';
          $activity_note = (Input::get('activity_note'))?Input::get('activity_note'):'';
          $notification = false;
            $timestamp = date("Y-m-d H:i:s");
                $data = array();  
                $data['activity_id'] = $activity_name;
                $data['document_workflow_responsible_user'] = $assigned_to;
                $data['document_workflow_activity_due_date'] = $activity_due_date;
                $data['document_workflow_activity_notes'] = $activity_note;
                $data['document_workflow_notifcation_to_status'] = 0;
                $data['updated_at'] = $timestamp;
                $where = array('document_workflow_id' => $wf_activity_id);
                $result = DB::table('tbl_document_workflows')->where($where)->first();
                if($result)
                {
                    $wf_object_id = $result->document_workflow_object_id;
                    $wf_object_type=$result->document_workflow_object_type;
                    if($result->document_workflow_responsible_user != $assigned_to && $assigned_to)
                    {
                      $notification = true;  
                      $data['document_workflow_activity_by_user'] = Auth::user()->username;
                    }
                    DB::table('tbl_document_workflows')->where($where)->update($data);
                  $message='<div class="alert alert-success">Activity Saved Successfully</div>';
                }
                else
                {               
                    $notification = true;  
                    $data['document_workflow_activity_date'] = $activity_date;
                    
                    $data['document_workflow_object_id'] = $wf_object_id;
                    $data['document_workflow_object_type'] = $wf_object_type;
                    $data['workflow_stage_id'] = $wf_stage_id;
                    $data['document_workflow_activity_by_user'] = Auth::user()->username;
                    
                    $data['created_at'] = $timestamp;

                    $where = array('document_workflow_object_id' => $wf_object_id,'document_workflow_object_type' => $wf_object_type,'workflow_stage_id' => $wf_stage_id);
                    $result = DB::table('tbl_document_workflows')->selectRaw("max(activity_order) as activity_order")->where($where)->first();
                    $activity_order = ($result && isset($result->activity_order))?$result->activity_order+1:1;
                    $data['activity_order'] = $activity_order;
                    $wf_activity_id = DB::table('tbl_document_workflows')->insertGetId($data);
                  $message='<div class="alert alert-success">Activity Saved Successfully</div>';           
                  }

                  if($notification && $assigned_to)
                  {
                    $where = array('username' => $assigned_to);
                    $result = DB::table('tbl_users')->where($where)->first();
                        
                    if($result && $result->id)
                    {    
                    $recipients = array($result->id);
                    $notification = array();
                    $notification['type']='workflow';
                    $notification['priority']='1';
                    $notification['title']= 'Workflow activity "'.$activity_name.'" assigned by '.Auth::user()->user_full_name;
                    $notification['details']='';
                    $notification['link']='viewworkflow/'.$wf_id.'?activity_view='.$wf_activity_id.'&object_id='.$wf_object_id.'&object_type='.$wf_object_type;
                    $notification['sender']=Auth::user()->id;
                    $notification['recipients']=$recipients;
                    $this->docObj->add_notification($notification);
                        }
                  }
          $wf_users = array(Auth::user()->username); 
            $super_admin=0;  
            switch (Auth::user()->user_role) 
          {    
            case Session::get("user_role_super_admin")://super admin
              
            $super_admin=1;
            break;
            case Session::get("user_role_group_admin")://group admin
            $auth_dep_users = DB::table('tbl_users_departments as td')
            ->join('tbl_users as tu','tu.id','=','td.users_id')
            ->select('tu.username')
            ->whereIn('td.department_id',Session::get('auth_user_dep_ids'))->get();
            
            foreach ($auth_dep_users as $value) 
            {
              
                $wf_users[] = $value->username;
            }
            break;
          } 
          $data = array(); 

          $data['super_admin'] = $super_admin;
          $data['wf_users'] = $wf_users;        
          
          $data['status'] = 1;
          $data['workflow_id'] = $wf_id; 
          $data['message'] = $message;
          $workflow_doc_id = (Input::get('workflow_doc_id'))?Input::get('workflow_doc_id'):'';
          //config(['app.workflow_doc_id' => $wf_object_id]);
          //config(['app.workflow_object_type' => $wf_object_type]);

          $data['wf_details'] = WorkflowModel::get_workflow($wf_id);
          $data['wf_stage_details'] = WorkflowModel::get_workflow_stage_details($wf_id);
          $data['html'] = View::make('pages/workflow/ajax_workflow')->with($data)->render();
          return json_encode($data);
    }

    public function get_workflow_activity()
    {
          $activity = (Input::get('activity'))?Input::get('activity'):0;
          $data['status'] = 1;
          $data['activity_details'] = WorkflowModel::get_workflow_activity($activity);
          return json_encode($data);
    }



    public function add_to_workflow()
    {
          
          $wf_id = (Input::get('wf_id'))?Input::get('wf_id'):0;
          $wf_stage_id = (Input::get('stage_id'))?Input::get('stage_id'):0;
          $wf_object_id = (Input::get('object_id'))?Input::get('object_id'):'';
          $wf_object_type = (Input::get('object_type'))?Input::get('object_type'):'document';
          $activity_name = (Input::get('activity_name'))?Input::get('activity_name'):'';     
          $activity_date = date("Y-m-d");
          $activity_due_date = (Input::get('activity_due_date'))?date("Y-m-d",strtotime(Input::get('activity_due_date'))):null;          
          $assigned_to = (Input::get('assigned_to'))?Input::get('assigned_to'):'';
          $activity_note = (Input::get('activity_note'))?Input::get('activity_note'):'';
            $timestamp = date("Y-m-d H:i:s");
                $data = array();  
                $data['activity_id'] = $activity_name;
                $data['document_workflow_responsible_user'] = $assigned_to;
                $data['document_workflow_activity_date'] = $activity_date;
                $data['document_workflow_activity_due_date'] = $activity_due_date;
                $data['document_workflow_activity_notes'] = $activity_note;
                $data['updated_at'] = $timestamp;

                $where = array('tdw.document_workflow_object_id' => $wf_object_id,'tdw.document_workflow_object_type' => $wf_object_type,'tw.workflow_id' => $wf_id);
                $query = DB::table('tbl_document_workflows as tdw');
                $query->join('tbl_workflows as tw','tdw.workflow_stage_id','=','tw.workflow_stage_id');
                $result = $query->where($where)->first();
                if($result)
                {
                   $data = array(); 
                   $data['status'] = 0;
                   $data['message'] = '<div class="alert alert-danger">This '.ucwords($wf_object_type).' is already exist in workflow</div>';
                   return json_encode($data);
                }
                else
                {               
                    $data['document_workflow_object_id'] = $wf_object_id;
                    $data['document_workflow_object_type'] = $wf_object_type;
                    $data['workflow_stage_id'] = $wf_stage_id;
                
                    $data['document_workflow_activity_by_user'] = Auth::user()->username;
                    $data['created_at'] = $timestamp;
                    $wf_activity_id = DB::table('tbl_document_workflows')->insertGetId($data);
                  if($assigned_to)
                  {
                    $where = array('username' => $assigned_to);
                    $result = DB::table('tbl_users')->where($where)->first();
                        
                    if($result && $result->id)
                    {    
                    $recipients = array($result->id);
                    $notification = array();
                    $notification['type']='workflow';
                    $notification['priority']='1';
                    $notification['title']= 'Workflow activity "'.$activity_name.'" assigned by '.Auth::user()->user_full_name;
                    $notification['details']='';
                    $notification['link']='viewworkflow/'.$wf_id.'?activity_view='.$wf_activity_id.'&object_id='.$wf_object_id.'&object_type='.$wf_object_type;
                    $notification['sender']=Auth::user()->id;
                    $notification['recipients']=$recipients;
                    $this->docObj->add_notification($notification);
                        }
                  }
                             
                  }
                  $message='<div class="alert alert-success">document Saved Successfully</div>';
          $data = array(); 
          $data['status'] = 1;
          $data['workflow_id'] = $wf_id;
          $data['wf_new_docs'] = WorkflowModel::wf_new_docs($wf_id);      
          $data['message'] = $message;
          $data['wf_details'] = WorkflowModel::get_workflow($wf_id);
          config(['app.workflow_doc_id' => $wf_object_id]);
          config(['app.workflow_object_type' => $wf_object_type]);
          $data['wf_stage_details'] = WorkflowModel::get_workflow_stage_details($wf_id);
          return json_encode($data);
    }


     public function get_obejects()
    {
          $data = array(); 
          $data['status'] = 1;
          $data['wf_new_docs'] = WorkflowModel::wf_new_docs();
          return json_encode($data);
    }

     public function get_users_list()
    {
          $data = array(); 
          $data['status'] = 1;
          $data['user'] = WorkflowModel::users_list();
          return json_encode($data);
    }

     public function workflow_activity_delete()
    {
          $workflow_id = (Input::get('wf'))?Input::get('wf'):0;
          $activity = (Input::get('activity'))?Input::get('activity'):0;
          $where = array('document_workflow_id' => $activity);
          DB::table('tbl_document_workflows')->where($where)->delete();
          return redirect('viewworkflow/'.$workflow_id);
    }

    public function workflow_exit()
    {
          $workflow_id = (Input::get('wf'))?Input::get('wf'):0;
          $wf_object_id = (Input::get('objectid'))?Input::get('objectid'):'';
          $wf_object_type = Input::get('objecttype');

          $object = WorkflowModel::get_object_name($wf_object_type,$wf_object_id);

           $where = array('tdw.document_workflow_object_id' => $wf_object_id,'tdw.document_workflow_object_type' => $wf_object_type,'tw.workflow_id' => $workflow_id);
           $select ="tdw.workflow_stage_id,tw.workflow_name";
          $query = DB::table('tbl_document_workflows as tdw');
                $query->join('tbl_workflows as tw','tdw.workflow_stage_id','=','tw.workflow_stage_id');
                $query->selectRaw($select);
                $result = $query->where($where)->first();
               
          if($result)
          {
            $current_stage = $result->workflow_stage_id;
                $where = array('document_workflow_object_id' => $wf_object_id,'document_workflow_object_type' => $wf_object_type,'workflow_stage_id' => $current_stage);
                $this->saveWorkflowHistory($where);
                $delete_action = DB::table('tbl_document_workflows')->where($where)->delete();
                if($delete_action){
                // Save in audits
                $audit = (new AuditsController)->log(Auth::user()->username,'Workflow',Lang::get('language.workflow_completed'), $wf_object_type." '".$object->object_name. "' completed workflow '".$result->workflow_name."'");
                }
              
          }        
          return redirect('viewworkflow/'.$workflow_id);
    }
    
    public function workflow_complete()
    {
          $workflow_id = (Input::get('wf'))?Input::get('wf'):0;
          $wf_object_id = (Input::get('objectid'))?Input::get('objectid'):'';
          $wf_object_type = (Input::get('objecttype'))?Input::get('objecttype'):'document';

           $where = array('tdw.document_workflow_object_id' => $wf_object_id,'tdw.document_workflow_object_type' => $wf_object_type,'tw.workflow_id' => $workflow_id);
           $select ="tdw.workflow_stage_id";
          $query = DB::table('tbl_document_workflows as tdw');
                $query->join('tbl_workflows as tw','tdw.workflow_stage_id','=','tw.workflow_stage_id');
                $query->selectRaw($select);
                $result = $query->where($where)->first();
               
          if($result)
          {
              $current_stage = $result->workflow_stage_id;

              $select ="workflow_stage_id";
              $where = array('workflow_id' => $workflow_id); 
              $query = DB::table('tbl_workflows')->where($where);
              $query->selectRaw($select);
              $query->orderBy('workflow_stage_order', 'DESC');
              $row =    $query->first();

              if($row)
              {
                 $complte_stage = $row->workflow_stage_id;
                 if($current_stage != $complte_stage)
                 {
                    $where = array('tdw.document_workflow_object_id' => $wf_object_id,'tdw.document_workflow_object_type' => $wf_object_type,'tw.workflow_id' => $workflow_id);
                    $timestamp = date("Y-m-d H:i:s");
                     $data = array();  
                $data['workflow_stage_id'] = $complte_stage;
                $data['updated_at'] = $timestamp;
                $where = array('document_workflow_object_id' => $wf_object_id,'document_workflow_object_type' => $wf_object_type,'workflow_stage_id' => $current_stage);
                $this->saveWorkflowHistory($where);
                DB::table('tbl_document_workflows')->where($where)->update($data);
                 }
              }
              
          }        

        
          return redirect('viewworkflow/'.$workflow_id);
    }

      public function change_workflow_stage()
    {
          $data = array(); 
          $wf_id = (Input::get('wf_id'))?Input::get('wf_id'):0;
          $wf_stage_id = (Input::get('wf_stage_id'))?Input::get('wf_stage_id'):0;
          $wf_object_id = (Input::get('wf_object_id'))?Input::get('wf_object_id'):'';
          $wf_object_type = (Input::get('wf_object_type'))?Input::get('wf_object_type'):'document';
          $object = WorkflowModel::get_object_name($wf_object_type,$wf_object_id);
          @$last_activity = (Input::get('last_activity_flag'))?Input::get('last_activity_flag'):0;
//last activity on view activity case edit
          if($current_stage_order = Input::get('current_stage_order'))
            {
                $next_stage_id = DB::table('tbl_workflows')
                ->select('workflow_stage_id','workflow_stage_name','workflow_name')
                ->where('workflow_id',$wf_id)
                ->where('workflow_stage_order',$current_stage_order+1)
                ->first();
            $change_stage_id = $next_stage_id->workflow_stage_id;
            $change_stage_name = $next_stage_id->workflow_stage_name;
            $wf_name = $next_stage_id->workflow_name;
            }
//change stage normal case
        if(!$current_stage_order && !$last_activity)
            {
              $change_stage_id = (Input::get('change_stage_id'))?Input::get('change_stage_id'):0;
              $next_stage_id = DB::table('tbl_workflows')
                ->select('workflow_stage_name','workflow_name')
                ->where('workflow_id',$wf_id)
                ->where('workflow_stage_id',$change_stage_id)
                ->first();
            $change_stage_name = $next_stage_id->workflow_stage_name;
            $wf_name = $next_stage_id->workflow_name;
            }
//last activity case in add activity
        if($last_activity == 1)
            {
                $stage_details = DB::table('tbl_workflows')->select('workflow_stage_order')->where('workflow_stage_id',$wf_stage_id)->first();
                if($stage_details)
                {
                    $current_stage_order = $stage_details->workflow_stage_order;
                }
                $next_stage_id = DB::table('tbl_workflows')
                    ->select('workflow_stage_id','workflow_stage_name','workflow_name')
                    ->where('workflow_id',$wf_id)
                    ->where('workflow_stage_order',$current_stage_order+1)
                    ->first();
                $change_stage_id = $next_stage_id->workflow_stage_id;
                $change_stage_name = $next_stage_id->workflow_stage_name;
            }
          $timestamp = date("Y-m-d H:i:s");
          $data = array();  
                $data['workflow_stage_id'] = $change_stage_id;
                $data['updated_at'] = $timestamp;
                $where = array('document_workflow_object_id' => $wf_object_id,'document_workflow_object_type' => $wf_object_type,'workflow_stage_id' => $wf_stage_id);
                // Update workfow history
                $this->saveWorkflowHistory($where);
                //audits insert
                // $audit = (new AuditsController)->log(Auth::user()->username,'Workflow',Lang::get('language.stage_changed'), $wf_object_type." '".$object->object_name. "' change stage to '".$change_stage_name."' of Workflow '".$wf_name."'");
                // Update new stage
                DB::table('tbl_document_workflows')->where($where)->update($data);
                  $message='<div class="alert alert-success">Activity Saved Successfully</div>';
        //access control        
          $wf_users = array(Auth::user()->username); 
            $super_admin=0;  
            switch (Auth::user()->user_role) 
          {    
            case Session::get("user_role_super_admin")://super admin
              
            $super_admin=1;
            break;
            case Session::get("user_role_group_admin")://group admin
            $auth_dep_users = DB::table('tbl_users_departments as td')
            ->join('tbl_users as tu','tu.id','=','td.users_id')
            ->select('tu.username')
            ->whereIn('td.department_id',Session::get('auth_user_dep_ids'))->get();
            
            foreach ($auth_dep_users as $value) 
            {
              
                $wf_users[] = $value->username;
            }
            break;
          }      
          $data['super_admin'] = $super_admin;
          $data['wf_users'] = $wf_users;     
          // end access control
          $data['status'] = 1;
          $data['workflow_id'] = $wf_id;
          $data['message'] = $message;
          $data['wf_details'] = WorkflowModel::get_workflow($wf_id);
          $data['wf_stage_details'] = WorkflowModel::get_workflow_stage_details($wf_id);
          $data['html'] = View::make('pages/workflow/ajax_workflow')->with($data)->render();
          return json_encode($data);
    }
    //workflow by faisal END

    // Save workflow history
    public function saveWorkflowHistory($where)
    {   
        $workflow_stage_id = $where['workflow_stage_id'];
        $document_workflow_object_id = $where['document_workflow_object_id'];
        $document_workflow_object_type=$where['document_workflow_object_type'];
        
        $query = DB::table('tbl_document_workflows');
                 $query->select('tbl_document_workflows.*','tbl_activities.activity_name','tbl_workflows.workflow_stage_name','tbl_workflows.workflow_id','tbl_workflows.workflow_name','tbl_workflows.workflow_color');  
                 $query->join('tbl_workflows','tbl_document_workflows.workflow_stage_id','=','tbl_workflows.workflow_stage_id');
                 $query->join('tbl_activities','tbl_document_workflows.activity_id','=','tbl_activities.activity_id');
                 $query->where('tbl_document_workflows.document_workflow_object_id',$document_workflow_object_id);
                 $query->where('tbl_document_workflows.workflow_stage_id',$workflow_stage_id);
                 $query->where('tbl_document_workflows.document_workflow_object_type',$document_workflow_object_type);
                 $record = $query->get();

        // Save history
        foreach($record as $val):
            
            //Prepare data to be saved
            $data = array('workflow_id'=>$val->workflow_id,
                          'workflow_name'=>$val->workflow_name,
                          'workflow_color'=>$val->workflow_color,
                          'document_workflow_object_id'=>$val->document_workflow_object_id,
                          'document_workflow_object_type'=>$val->document_workflow_object_type,
                          'workflow_stage_id'=>$val->workflow_stage_id,
                          'workflow_stage_name'=>$val->workflow_stage_name,
                          'activity_id'=>$val->activity_id,
                          'activity_name'=>$val->activity_name,
                          'document_workflow_responsible_user'=>$val->document_workflow_responsible_user,
                          'document_workflow_activity_by_user'=>$val->document_workflow_activity_by_user,
                          'document_workflow_activity_date'=>$val->document_workflow_activity_date,
                          'document_workflow_activity_due_date'=>$val->document_workflow_activity_due_date,
                          'document_workflow_activity_notes'=>$val->document_workflow_activity_notes,
                          'document_workflow_created_by'=>$val->document_workflow_created_by,
                          'document_workflow_updated_by'=>$val->document_workflow_updated_by,
                          'document_workflow_created_at'=>$val->created_at,
                          'document_workflow_updated_at'=>$val->updated_at,
                          'action_activity_name'=>$val->action_activity_name,
                      'action_activity_note'=>$val->action_activity_note,
                  'action_activity_by'=>$val->action_activity_by,
                  'action_activity_date'=>$val->action_activity_date);
            // Insert history
            DB::table('tbl_workflow_histories')->insert($data);

            // Save in audits
            $result = (new AuditsController)->log(Auth::user()->username,Lang::get('language.workflow_history'), 'Insert', $val->workflow_name);
            endforeach;
    }

    // Show workflow history
    public function showWorkflowHistory($name,$id)
    {   
        // Destroy session for avoid active class in workflow
        Session::put('menuid', '1515151');
        /*<--Common-->*/
        $data['docType'] = DocumentTypesModel::all();   
        $data['stckApp'] = $this->docObj->common_stack();
        $data['deptApp'] = $this->docObj->common_dept();
        $data['doctypeApp'] = $this->docObj->common_type();
        $data['records'] = $this->docObj->common_records();
        $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
        $data['settings_document_no']   = $settings[0]->settings_document_no;
        $data['settings_document_name'] = $settings[0]->settings_document_name;
        /*<--// Common-->*/

        $query = DB::table('tbl_workflow_histories');
                 $query->select('workflow_history_id','workflow_id','workflow_name','workflow_color',DB::RAW('GROUP_CONCAT(workflow_stage_id) AS workflow_stage_ids'));
                 $query->where('document_workflow_object_id',$id); 
        $records = $query->groupBy('workflow_id')->get();
        
        foreach($records as $val):  
            // Get stage under workflow
            $val->stages = DB::table('tbl_workflow_histories')->select('workflow_stage_id','workflow_stage_name')->where('document_workflow_object_id',$id)->whereIn('workflow_stage_id',explode(',',$val->workflow_stage_ids))->groupBy('workflow_stage_id')->get();

            foreach($val->stages as $stage):
                $stage->maindate = DB::table('tbl_workflow_histories')->select(DB::RAW("DATE_FORMAT(document_workflow_activity_date,'%Y-%m-%d') AS main_date"))->where('document_workflow_object_id',$id)->where('workflow_stage_id',$stage->workflow_stage_id)->groupBy(DB::RAW("DATE_FORMAT(document_workflow_activity_date,'%Y-%m-%d')"))->orderBy('document_workflow_activity_date', 'desc')->get();

                foreach($stage->maindate as $main):
    
                    $query2 = DB::table('tbl_workflow_histories');
                              $query2->select('tbl_workflow_histories.workflow_history_id','tbl_workflow_histories.workflow_stage_id','tbl_workflow_histories.activity_name','tbl_workflow_histories.document_workflow_activity_date','tbl_workflow_histories.document_workflow_activity_due_date','tbl_workflow_histories.document_workflow_activity_notes','tbl_workflow_histories.created_at','tbl_workflow_histories.action_activity_name','tbl_workflow_histories.action_activity_note','tbl_workflow_histories.action_activity_by','tbl_workflow_histories.action_activity_date','A.user_full_name AS responsible_user','B.user_full_name AS activity_by_user');
                              $query2->leftJoin('tbl_users AS A','tbl_workflow_histories.document_workflow_responsible_user','=','A.username');
                              $query2->leftJoin('tbl_users AS B','tbl_workflow_histories.document_workflow_activity_by_user','=','B.username');
                              $query2->where('tbl_workflow_histories.document_workflow_object_id',$id);  
                              $query2->where('tbl_workflow_histories.workflow_stage_id',$stage->workflow_stage_id);  
                              $query2->where(DB::RAW("DATE_FORMAT(tbl_workflow_histories.document_workflow_activity_date,'%Y-%m-%d')"),$main->main_date);
                    $main->activityDdtails = $query2->get();
                    
                endforeach;
            endforeach;
        endforeach;
        
        $data['data']     = $records;
        if($name =='document')
        {
            $data['document'] = DB::table('tbl_documents')->select('document_name as document_name')->where('document_id',$id)->get();
        }
        elseif($name == 'form')
        {
            $data['document'] = DB::table('tbl_forms')->select('form_name as document_name')->where('form_id',$id)->get();
        }
        return View::make('pages/workflow/history')->with($data);      
    }

     public function saveActivityPostion()
    {
          
          $data_activity = (Input::get('data_activity'))?Input::get('data_activity'):array();
          $i=0;
          $data = array(); 
          $data['status'] = 1;
          foreach ($data_activity as $key => $value) 
          {
              $i++;
              $update = array('tdw.activity_order' => $i);
              $where = array('tdw.document_workflow_id' => $value);
              $query = DB::table('tbl_document_workflows as tdw');
              $query->where($where)->update($update);
              $data['activity_order'] = $i;
          }    
          
          $data['status'] = 1;
          $data['data_activity'] = $data_activity;
          return json_encode($data);
    }

    public function add_to_workflow_modal($id=0)
    {
            $data = array();
            $data['workflows'] = WorkflowModel::get_workflows();
            $data['activities'] = WorkflowModel::get_activities('workflows');
            $data['wf_stages'] = WorkflowModel::wf_stages();
            $data['user'] = WorkflowModel::users_list();

            $data['object_id']= (Input::get('object_id'))?Input::get('object_id'):'';
            $data['object_type']= (Input::get('type'))?Input::get('type'):'';
            $data['workflow_id']= (Input::get('workflow_id'))?Input::get('workflow_id'):'';
            $data['object_type']= (Input::get('object_type'))?Input::get('object_type'):'';


            return View::make('pages/workflow/add_workflow_modal')->with($data);
       
    }

    public function search_object_to_workflow_modal($id=0)
    {
            $data = array();
            
            $data['object_type']= (Input::get('object_type'))?Input::get('object_type'):'';
             $data['object_name']= (Input::get('object_name'))?Input::get('object_name'):'';
            if($data['object_type'] == 'document')
            {
                $data['docType'] = DocumentTypesModel::all(); 
            }
            if($data['object_type'] == 'form')
            {
                $data['formType'] = FormModel::forms_list(); 
                $data['user'] = WorkflowModel::users_list();
            }
            

            return View::make('pages/workflow/object_to_workflow_modal')->with($data);
       
    }
    //Ajax search for document and forms
     public function search_object_data($id=0)
    {
            $data = array();
            
            $data['object_type']= (Input::get('object_type'))?Input::get('object_type'):'';
             $data['object_name']= (Input::get('object_name'))?Input::get('object_name'):'';
             $data['object_id']= (Input::get('object_id'))?Input::get('object_id'):'';
             $data['document_no']= (Input::get('document_no'))?Input::get('document_no'):'';
             $data['object_id']= (Input::get('object_id'))?Input::get('object_id'):'';
            
             $object_data = WorkflowModel::serch_doc($data); 
             $serch_data=array('object_type' => $data['object_type']); 
             if($data['object_name'])
             {
                $serch_data['object_name'] = $data['object_name'];
             }
            
            $data['document_no']= (Input::get('document_no'))?Input::get('document_no'):''; 
            $data['form_type']= (Input::get('form_type'))?Input::get('form_type'):'';
            if($data['object_type'] == 'document')
            {
                
            }
            if($data['object_type'] == 'form')
            {
               
            }
            $object_data->appends($serch_data);
            $data['object_data'] = $object_data;
          $data['status'] = 1;
          $data['html'] = View::make('pages/workflow/search_object_data')->with($data)->render();
          return json_encode($data);
       
    }

    public function load_activity_form()
    {
      if (Auth::user()) 
      {
            $actions = Input::get('action');
            $wf_activity = Input::get('activity');
            $workflow_id = Input::get('workflow_id');
            $objectid = Input::get('objectid');
            $objecttype = Input::get('objecttype');
            $stageid = Input::get('stageid');

            if($stageid)
            {
                $stage_details = DB::table('tbl_workflows')->select('workflow_stage_order')->where('workflow_stage_id',$stageid)->first();
                if($stage_details)
                {
                    $data['add_stage_order'] = $stage_details->workflow_stage_order;
                }
            }

            $activity_details = '';    
            $activity_id = 0;
            $activity_note = $activity_to_user = $activity_due_date = $activity_by_user = $activity_date = $stage_name = $activity_by_user_name = '';
            $wf_assigned_to_array = array(Auth::user()->username);   
            if($wf_activity)
            {
              $activity_details = WorkflowModel::get_workflow_activity($wf_activity);
              if($activity_details)
              {
                $activity_id =$activity_details->activity_id;
                $objectid = $activity_details->document_workflow_object_id;
                $objecttype = $activity_details->document_workflow_object_type;
                $stageid = $activity_details->workflow_stage_id;
                $activity_note =$activity_details->document_workflow_activity_notes;
                $activity_to_user =$activity_details->document_workflow_responsible_user;

                $activity_due_date =$activity_details->document_workflow_activity_due_date;
                $stage_name = $activity_details->workflow_stage_name;
                $stage_order = $activity_details->workflow_stage_order;
                $activity_by_user  =$activity_details->document_workflow_activity_by_user;
                $where = array('username' => $activity_details->document_workflow_activity_by_user);
                $activity_by_user_result = DB::table('tbl_users')->where($where)->first();
                $activity_by_user_name = ($activity_by_user_result)?$activity_by_user_result->user_full_name:'';
                $activity_date = ($activity_details)?date("Y-m-d",strtotime($activity_details->created_date)):'';

          switch (Auth::user()->user_role) 
          {
            
            case Session::get("user_role_super_admin")://super admin
              $wf_assigned_to_array[] = $activity_to_user;
            break;
            case Session::get("user_role_group_admin")://group admin
            $auth_dep_users = DB::table('tbl_users_departments as td')
            ->join('tbl_users as tu','tu.id','=','td.users_id')
            ->select('tu.username')
            ->whereIn('td.department_id',Session::get('auth_user_dep_ids'))->get();
            $auth_dep_users_array = array();
            //users under the department.
            foreach ($auth_dep_users as $value) 
            {
              if($value->username == $activity_to_user);
              {
                $wf_assigned_to_array[] = $activity_to_user;
              }
            }
            break;
          }
              }
            }
            $data['object_info'] = array();
            if($objectid && $objecttype)
            {
            $search = array('objectid' => $objectid,'objecttype' => $objecttype);
            $data['object_info'] = WorkflowModel::get_object_info($search);
            }

            $data['workflow_info'] = array();
            if($workflow_id)
            {
            $data['workflow_info'] = WorkflowModel::get_workflow($workflow_id);
            }
              
             

            $data['activity'] = (int)$wf_activity;
            $data['workflow_id'] = $workflow_id;
            $data['activity_id'] = $activity_id;
            $data['objectid'] = $objectid;
            $data['objecttype'] = $objecttype;
            $data['stageid'] = $stageid;
            $data['activity_note'] = $activity_note;
            $data['activity_to_user'] = $activity_to_user;
            $data['activity_due_date'] = $activity_due_date;
            $data['activity_date'] = $activity_date;
            $data['stage_name'] = $stage_name;
            $data['stage_order'] = @$stage_order;
            $data['actions'] = $actions;
            $data['activity_details'] = $activity_details;
            $data['activity_by_user'] = $activity_by_user;
            $data['activity_by_user_name'] = $activity_by_user_name;
            
            $data['wf_assigned_to'] = $wf_assigned_to_array; 
            
            $user_id = Input::get('user_id');
            
            $data['activities'] = WorkflowModel::get_activities();
            $data['action_activities'] = WorkflowModel::get_activities('form_action');
            $data['user'] = WorkflowModel::users_list();
             $json['status']=  1;
             $json['html']=  view::make('pages/workflow/activity_form')->with($data)->render();
             return json_encode($json);
        }
        else 
        {
             $json['status']=  1;
             $json['html']=  "Session Expired.Please Login";
             return json_encode($json);
        }
    }

    public function save_action_workflow()
    {
      $activity = (Input::get('activity'))?Input::get('activity'):0;
      $workflow_id = (Input::get('workflow_id'))?Input::get('workflow_id'):0;
     
      $where = array('document_workflow_id' => $activity);
      
      $result = DB::table('tbl_document_workflows')->where($where)->first();
      $json = array();
      $form_name= $form_description  = '';
      if($result)
      {
        $update['action_activity'] = (Input::get('activity_id'))?Input::get('activity_id'):0;
        
        $update['action_activity_name'] = (Input::get('activity_name'))?Input::get('activity_name'):'';
        $update['action_activity_note'] = (Input::get('activity_note'))?Input::get('activity_note'):'';
         $update['action_activity_by'] = Auth::user()->id;
         $update['action_activity_date'] = date("Y-m-d H:i:s");
        DB::table('tbl_document_workflows')->where($where)->update($update);

        /* For Notification Start */
        if($result->document_workflow_activity_by_user)
       {  
         $wf_object_id=$result->document_workflow_object_id;
         $wf_object_type=$result->document_workflow_object_type;
          $where = array('username' => $result->document_workflow_activity_by_user);
                    $result_user = DB::table('tbl_users')->where($where)->first();
                        
                    if($result_user && $result_user->id)
                    {    
                    $recipients = array($result_user->id);
                    $notification = array();
                    $notification['type']='workflow';
                    $notification['priority']='1';
                    $notification['title']= 'Workflow activity status changed to "'.$update['action_activity_name'].'" by '.Auth::user()->user_full_name;
                    $notification['details']='';
                    $notification['link']='viewworkflow/'.$workflow_id.'?activity_view='.$activity.'&object_id='.$wf_object_id.'&object_type='.$wf_object_type;
                    $notification['sender']=Auth::user()->id;
                    $notification['recipients']=$recipients;
                    $this->docObj->add_notification($notification);
           /* For Notification END*/
       }
      }
      }

      $json['status']=  1;
      $message = '<div class="alert alert-success text-center">Status saved successfully</div>';
      $json['message']=  $message;
      $formMoreDetails = json_decode($this->load_activity_form());
      $json['html']=  (isset($formMoreDetails->html))?$formMoreDetails->html:'';
      return json_encode($json);
    }

    public function closed_workflow($workflow_id=0)
    {
        // checking wether user logged in or not
        if (Auth::user()) {

            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            
            $data['workflow_id'] = $workflow_id;
            
            return View::make('pages/workflow/closed_workflow')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    public function save_closed_workflow($workflow_id=0)
    {
        // checking wether user logged in or not
        if (Auth::user()) {
            $workflow_id = (Input::get('workflow_id'))?Input::get('workflow_id'):0;
            $timestamp = date("Y-m-d H:i:s");

            $where = array('id' => $workflow_id);
            $result = DB::table('tbl_wf')->where($where)->first(); 
            $wf_data = array();
            $wf_data['workflow_name'] = (Input::get('workflow_name'))?Input::get('workflow_name'):'';
            $wf_data['workflow_color'] = (Input::get('workflow_color'))?Input::get('workflow_color'):'';
            $wf_data['updated_at'] = $timestamp;
            $wf_data['updated_by'] = Auth::user()->id;
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

              $workflow_stages = (Input::get('workflow_stages'))?Input::get('workflow_stages'):array(); 
              $i=0;
              $reset = array('edit' => 0);
              $reset_state = array('workflow_id' => $workflow_id);
              DB::table('tbl_wf_states')->where($reset_state)->update($reset);
              foreach ($workflow_stages as $key => $value) 
              {
                $i++;
                
              $state = (isset($value['label']))?$value['label']:'stage';

              $dbid = (isset($value['dbid']))?$value['dbid']:0;
             
              $stages = array();
              $stages['workflow_id'] = $workflow_id;
              $stages['type'] = 'normal';
              $stages['state'] = $state;
              $stages['updated_at'] = $timestamp;
              $stages['edit'] = 1;
              $stages['mark'] = $i;

              $where_state = array('id' => $dbid,'workflow_id' => $workflow_id);
              $result = DB::table('tbl_wf_states')->where($where_state)->first(); 
              if($result)
              {
                  
                  DB::table('tbl_wf_states')->where($where_state)->update($stages);
                            
              }
              else
              { 
                  $stages['created_at'] = $timestamp;
                  DB::table('tbl_wf_states')->insert($stages);
              }     
              }

              
              $reset_state = array('workflow_id' => $workflow_id,'edit' => 0);
              DB::table('tbl_wf_states')->where($reset_state)->delete();



              $workflow_edges = (Input::get('workflow_edges'))?Input::get('workflow_edges'):array(); 
              $i=0;
              $reset = array('edit' => 0);
              $reset_state = array('workflow_id' => $workflow_id);
              DB::table('tbl_wf_transitions')->where($reset_state)->update($reset);
              foreach ($workflow_edges as $key => $value) 
              {
                $i++;
                
              $name = (isset($value['label']))?$value['label']:'Next';

              $dbid = (isset($value['dbid']))?$value['dbid']:0;
              $from_state = (isset($value['from']))?$value['from']:0;
              $to_state = (isset($value['to']))?$value['to']:0;
             
              $transitions = array();
              $transitions['workflow_id'] = $workflow_id;
              $transitions['name'] = $name;
              $transitions['from_state'] = $from_state;
              $transitions['to_state'] = $to_state;
              $transitions['updated_at'] = $timestamp;
              $transitions['edit'] = 1;
              $transitions['tr_order'] = $i;

              $where_state = array('id' => $dbid,'workflow_id' => $workflow_id);
              $result = DB::table('tbl_wf_transitions')->where($where_state)->first(); 
              if($result)
              {
                  
                  DB::table('tbl_wf_transitions')->where($where_state)->update($transitions);
                            
              }
              else
              { 
                  $stages['created_at'] = $timestamp;
                  DB::table('tbl_wf_transitions')->insert($transitions);
              }     
              }

              
              $reset_state = array('workflow_id' => $workflow_id,'edit' => 0);
              DB::table('tbl_wf_transitions')->where($reset_state)->delete();    
             
           $data = array(); 
          $data['status'] = 1;
          $data['workflow_id'] = $workflow_id;
          $message = '<div class="alert alert-success text-center">Workflow saved successfully</div>';
          $data['message'] = $message;
          return json_encode($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    public function load_Workflow_json($workflow_id=0)
    {
        $workflow_id = (Input::get('workflow_id'))?Input::get('workflow_id'):0;
        $data['status'] = 1;
        $data['workflow_id'] = $workflow_id;
        $where = array('id' => $workflow_id);
        $wf_data = DB::table('tbl_wf')->where($where)->first();

        $where_state = array('workflow_id' => $workflow_id);
        $wf_states = DB::table('tbl_wf_states')->where($where_state)->get(); 

        $where_state = array('workflow_id' => $workflow_id);
        $wf_transitions = DB::table('tbl_wf_transitions')->where($where_state)->get(); 
        $wf_state = $wf_transition = array();

        $workflow_color = '#c0c0c0'; 
        $workflow_name ='';
        if($wf_data)
        {
           $workflow_name =  $wf_data->workflow_name;
           $workflow_color =  $wf_data->workflow_color;
        }
        $s=$t=1;
        foreach ($wf_states as $value) 
        {
            $row = array();
            $row['id'] = $s;
            $row['dbid'] = $value->id;
            $row['label'] = $value->state;
            $row['shape'] = $value->shape;
            $row['color'] = $workflow_color;
            $wf_state[] = $row;
            $s++;
        }

        foreach ($wf_transitions as $value) 
        {
            $row = array();
            $row['id'] = $t;
            $row['dbid'] = $value->id;
            $row['label'] = $value->name;
            $row['from'] = $value->from_state;
            $row['to'] = $value->to_state;
            $row['arrows'] = 'to';
            $wf_transition[] = $row;
            $t++;
        }

        $data['workflow_name'] = $workflow_name;
        $data['workflow_color'] = $workflow_color;
        $data['wf_states'] = $wf_state;
        $data['wf_transitions'] = $wf_transition;
        $data['nodecount'] = $s;
        $data['edgecount'] = $t;
        return json_encode($data);
            }
}/*<--END-->*/