<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
use Input;
use Auth;
class FormModel extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_forms'; // change it

    /**
    *Primary key
    */
    protected $primaryKey = 'form_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public static function form_types()
    {       
        $select ="tt.form_input_type,tt.form_input_type_value,tt.form_input_type_name,tt.form_input_icon,tt.is_options,tt.is_required,tt.form_input_type_common,tt.is_default_value";
        $result = DB::table('tbl_form_input_types as tt')->selectRaw($select)->orderBy('view_order','ASC')->get();
      
        return  $result;
    }
    public static function form_inputs_pre_exist($form_id=0,$form_responses_uique)    
    {
      $where = array('ti.form_id' => $form_id,'ti.form_response_unique_id'=>$form_responses_uique);
      $select ="ti.form_input_id,
      ti.form_input_type,
      ti.form_input_title,
      ti.form_Input_options,
      ti.form_input_require,
      ti.form_input_file_multiple,
      tt.form_input_type,
      tt.form_input_type_value,
      tt.is_options,
      tt.is_required,
      tt.form_input_type_name,
      tt.form_input_type_common";
      $query = DB::table('tbl_form_responses as ti');
      $query->join('tbl_form_input_types as tt','ti.form_input_type','=','tt.form_input_type');
      $query->selectRaw($select);
      $query->where($where)->orderBy('ti.form_input_order', 'ASC')->get();
      $result = $query->get();
      return $result;
    }
    public static function form_submit_edit($form_id,$form_response_unique_id)    
    {
      $where = array('tf.form_id' => $form_id);
      $select ="tf.form_input_id,tf.form_input_id as form_input_id2,
      tf.form_input_type,
      tf.form_input_title,
      tf.form_Input_options,
      tf.form_input_require,
      tf.form_input_file_multiple,
      tf.form_input_default_value,
      ti.form_response_selected,
      ti.document_file_name,
      ti.form_response_value,
      ti.form_response_id,
      tt.form_input_type,
      tt.form_input_type_value,
      tt.is_options,
      tt.is_required,
      tt.is_default_value,
      tt.is_input_type,
      tt.form_input_type_name,
      tt.form_input_type_common,
      tf.view_permission_users,
      tf.edit_permission_users";
      $query = DB::table('tbl_form_inputs as tf');
      $query->join('tbl_form_input_types as tt','tf.form_input_type','=','tt.form_input_type');

      $query->leftJoin('tbl_form_responses as ti', function($leftJoin)use($form_response_unique_id)
        {
            /*$where1 = array('ti.form_response_unique_id' => $form_response_unique_id);*/
            $leftJoin->on('ti.form_input_id', '=', 'tf.form_input_id');
            /*$leftJoin->where($where1);*/
            $leftJoin->on(DB::raw('ti.form_response_unique_id'), DB::raw('='),DB::raw("'".$form_response_unique_id."'"));


        });
      $query->selectRaw($select);
      $query->where($where)->orderBy('tf.form_input_order', 'ASC');
      $result = $query->groupBy('tf.form_input_id')->get();
      
      // foreach ($result as $key => $value) {
      //  $value->selected_value = DB::table('tbl_form_responses')->select('form_response_value','form_response_selected','document_file_name','form_response_id')->where('form_response_unique_id',$form_response_unique_id)->get();
      // }
      return $result;
      
    }
    public static function form_type_array()
    {       
        $select ="tt.form_input_type,tt.form_input_type_value,tt.is_options,tt.is_required";
      $result = DB::table('tbl_form_input_types as tt')->selectRaw($select)->get();
      $types = array();
      foreach ($result as $key => $value) 
      {
        $types[$value->form_input_type_value] = $value;
      }
        return  $types;
    }
    public static function form_inputs($form_id=0)    
    {
      $where = array('ti.form_id' => $form_id);
      $select ="ti.form_input_id,
      ti.form_input_type,
      ti.form_input_title,
      ti.form_Input_options,
      ti.form_input_require,
      ti.form_input_order,
      ti.form_input_file_multiple,
      ti.form_input_default_value,
      tt.form_input_type,
      tt.form_input_type_value,
      tt.is_options,
      tt.is_required,
      tt.is_default_value,
      tt.is_input_type,
      tt.form_input_type_name,
      tt.form_input_type_common,
      ti.view_permission_users,
      ti.edit_permission_users";
      $query = DB::table('tbl_form_inputs as ti');
      $query->join('tbl_form_input_types as tt','ti.form_input_type','=','tt.form_input_type');
      $query->selectRaw($select);
      $query->where($where)->orderBy('ti.form_input_order', 'ASC')->get();
      $result = $query->get();
      return $result;
    }
    public static function form_responses($form_id,$form_response_unique_id)    
    {
        $where = array('ti.form_id' => $form_id,'ti.form_response_unique_id' => $form_response_unique_id);
      $select ="ti.form_response_unique_id,
                ti.form_response_id,
                ti.created_at,
                ti.user_id,
                ti.form_input_id,ti.form_id,
                ti.form_name,
                ti.form_description,
                ti.form_input_type,
                ti.form_input_title,
                ti.form_response_value,
                ti.document_file_name,
                ti.form_response_file_size,
                ti.form_assigned_to,
                tt.form_input_type,
                tt.form_input_type_value,
                tt.is_options,
                tt.is_required,
                tt.form_input_type_name,
                tt.form_input_type_common,
                ti.response_activity_id,
                ti.response_activity_name,
                ti.response_activity_note,ti.response_activity_by,ti.response_activity_date
                ";
      $query = DB::table('tbl_form_responses as ti');
      $query->join('tbl_form_input_types as tt','ti.form_input_type','=','tt.form_input_type');
      $query->selectRaw($select);
      $query->where($where)->orderBy('ti.form_response_id', 'ASC')->get();
      
      $result = $query->get();
      return $result;
    }
    public static function assigned_users($form_id=0)
    {       
        $select ="tu.form_id,tu.form_user_id";
        $where = array('tu.form_id' => $form_id);
        $result = DB::table('tbl_form_users as tu')->selectRaw($select)->where($where)->get();
      
        return  $result;
    }

    public static function assigned_workflows($form_id=0)
    {       
        $select ="tw.form_id,tw.form_workflow_id,tw.form_activity_id";
        $where = array('tw.form_id' => $form_id);
        $result = DB::table('tbl_form_workflows as tw')->selectRaw($select)->where($where)->get();
      
        return  $result;
    }

    public static function form_privilages($form_id=0)
    {       
        $select ="tf.privilege_key,tf.privilege_status,tf.privilege_value_user,tf.privilege_value_department";
        $where = array('tf.form_id' => $form_id);
        $result = DB::table('tbl_form_privileges as tf')->selectRaw($select)->where($where)->get();
        //convert the comma searated values(dept,user) to array
        foreach ($result as $key => $value) {
            $dept = ($value->privilege_value_department)?explode(',', $value->privilege_value_department):array();
            $value->privilege_department_array = $dept;
            $user = ($value->privilege_value_user)?explode(',', $value->privilege_value_user):array();
            $value->privilege_user_array = $user;
        }
        
        return  $result;
    }

    public static function forms_list()
    {       
        $select ="tf.form_id,tf.form_name";
        $result = DB::table('tbl_forms as tf')->selectRaw($select)->orderBy('tf.form_name', 'ASC')->get();
      
        return  $result;
    }

    public static function form_activity()
    {       
        $select ="ta.activity_id,ta.activity_name";
        $result = DB::table('tbl_activities as ta')->selectRaw($select)->where('ta.activity_modules', 'LIKE', '%form_action%')->orderBy('ta.activity_name', 'ASC')->get();
      
        return  $result;
    }

    public static function addToWorkflow($form_id,$form_name,$form_responses_uique,$handler)
    {
      $q = DB::table('tbl_wf')
          ->leftjoin('tbl_form_workflows','tbl_wf.id','=','tbl_form_workflows.form_workflow_id');
          $q->where(function ($query) use($form_id) {
              $query->where('tbl_form_workflows.form_id','=',$form_id);
          });
          $q->orWhere(function($query) use($form_id) {
              $query->where('tbl_wf.wf_object_type_id','=',$form_id)->where('tbl_wf.wf_object_type','=','form');
          });

          $workflows_results = $q->select('tbl_wf.id')
          ->groupBy('tbl_wf.id')
          ->get();
          $timestamp = date("Y-m-d H:i:s");
          $timestamp_user = dtFormat($timestamp);
          //insert form to tbl_document_workflows
          foreach ($workflows_results as $workflows) 
          {
            $data = array();  
            $data['wf_operation_name'] = $form_name.' - '.$timestamp_user;
            $data['wf_id'] = $workflows->id;
            $data['wf_object_id'] = $form_responses_uique;
            $data['wf_object_type'] = 'form';
            $data['created_by'] = Auth::user()->id;
            $data['created_at'] = $timestamp;
            $data['updated_at'] = $timestamp;
                     
            $process_id = DB::table('tbl_wf_operation')->insertGetId($data);
            $query = DB::table('tbl_wf_states as ws');
            $where1 = array('ws.workflow_id' => $workflows->id);
            $query->where($where1);
            $query->orderBy('ws.mark', 'ASC');
            $result =    $query->get();
            $stageId = 0;
            $i=0;
            $processId = 0;
            foreach ($result as $key => $value) 
            {
              $i++;
              $operation_details = array();  
              $operation_details['wf_operation_id'] = $process_id;
              $operation_details['wf_stage'] = $value->id;
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
            //taking workflow id
            $wfid = $workflows_results[0]->id;
            $flag = 'workflow';
            if($stageId)
            {
              $update = array();
              //Update Current Stage to operation table
              $update['current_stage'] = $stageId;
              $where = array('id' => $processId);
              DB::table('tbl_wf_operation')->where($where)->update($update);
              $operation = array();
              $operation['wfId'] = $wfid;
              $operation['wfPrcsId'] = $processId;
              $operation['stageId'] = $stageId;
              $operation['flag'] = $flag;
              $operation['form_responses_uique'] = null;
              $operation['handler'] = $handler;
              $operation['wfObjectType'] = 'form';
              $operation['wfObjectId'] = $form_responses_uique;
              $workflow_process = WorkflowsModel::manageWorkflowOperation($operation);
            }
          }
    }

    public static function form_responses_new($form_id,$form_response_unique_id)    
    {
        $where = array('ti.form_id' => $form_id,'ti.form_response_unique_id' => $form_response_unique_id);
      $select ="ti.form_response_unique_id,
                ti.form_response_id,
                ti.created_at,
                ti.user_id,
                ti.form_input_id,ti.form_id,
                ti.form_name,
                ti.form_description,
                ti.form_input_type,
                ti.form_input_title,
                ti.form_response_value,
                ti.document_file_name,
                ti.form_response_file_size,
                ti.form_assigned_to,
                tt.form_input_type,
                tt.form_input_type_value,
                tt.is_options,
                tt.is_required,
                tt.form_input_type_name,
                tt.form_input_type_common,
                ti.response_activity_id,
                ti.response_activity_name,
                ti.response_activity_note,ti.response_activity_by,ti.response_activity_date
                ";
      $query = DB::table('tbl_form_responses as ti');
      $query->join('tbl_form_input_types as tt','ti.form_input_type','=','tt.form_input_type');
      $query->selectRaw($select);
      $query->where($where)->orderBy('ti.form_response_id', 'ASC')->groupBy('ti.form_response_id');
      
      $result = $query->get();
      return $result;
    }

}
