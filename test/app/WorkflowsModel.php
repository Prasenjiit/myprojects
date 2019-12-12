<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Input;
use Carbon\Carbon;
use Lang;
use App\Fpdf\FPDF;
class WorkflowsModel extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_wf'; // change it

    /**
    *Primary key
    */
    protected $primaryKey = 'workflow_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public static function get_rules($workflow_id,$stage,$process_id) {
      $result      = array();
        if($workflow_id==null || $stage==null || $process_id==null){
            return $result;
        }
        else {
          $search      = array('workflow_id' => $workflow_id,'from_state' => $stage);
          $transitions = WorkflowsModel::get_transitions($search);
          if($transitions) { 
            $transitions = WorkflowsModel::unserialize_rules($transitions);
            foreach ($transitions as $key => $rules) {
              if(isset($rules->rule)){
                if(($rules->stage_action==1 || $rules->stage_action==4) && $rules->with_rule==1) {
                  $condition  = $rules->rule[0]['condition'];
                  $stage_case = ($rules->rule[0]['stage_case'])?$rules->rule[0]['stage_case']:0;
                  $if_stage   = ($rules->rule[0]['if_stage'])?$rules->rule[0]['if_stage']:0;
                  $else_stage = ($rules->rule[0]['else_stage'])?$rules->rule[0]['else_stage']:0;
                  //$rules->status1 = $rules->rule[0]['rules'];
                  $RuleStatus = WorkflowsModel::Testmethod($rules->rule[0]['rules'],$condition,$process_id,$workflow_id,$stage_case,$if_stage,$else_stage);
                  //$rules->TestResult = $RuleStatus;
                  // echo '<pre>'; 
                  // print_r($RuleStatus);
                  //echo gettype($RuleStatus);
                  if(is_array($RuleStatus)) {
                    foreach ($RuleStatus as  $rs) {
                      $stageCase = $rs['stage_case'];
                      $ifStage   = $rs['if_stage'];
                      $elseStage = $rs['else_stage'];
                      if($condition=='OR') {
                        $rules->status    = $rs['status'];
                        if($rs['status']==1) {
                          $rules->stageCase = $stageCase;
                          $rules->if_stage   = $ifStage;
                          if($rules->if_stage) {
                            $cond1 = array(
                                           array('workflow_id','=',$workflow_id),
                                           array('mark','=',$rules->if_stage)
                                          );
                            $if_stage = DB::table('tbl_wf_states')->where($cond1)->first();
                            if($if_stage) {
                                $rules->if_stage = $if_stage->id;
                            }
                          }
                          $rules->else_stage = $elseStage;
                          if($rules->else_stage) {
                            $cond2 = array(
                                           array('workflow_id','=',$workflow_id),
                                           array('mark','=',$rules->else_stage)
                                          );
                            $else_stage = DB::table('tbl_wf_states')->where($cond2)->first();
                            if($else_stage) {
                                $rules->else_stage = $else_stage->id;
                            }
                          }
                        }
                        else if($rs['status']==0) {
                          $rules->stageCase = 0;
                          $rules->if_stage   = 0;
                          $rules->else_stage = 0;
                        }
                      }
                      else if($condition=='AND') {
                        if(array_search(0, array_column($RuleStatus, 'status')) !== False) {
                            $rules->stageCase = 0;
                            $rules->if_stage   = 0;
                            $rules->else_stage = 0;
                            $rules->status    = 0;
                        } else {                          
                            $rules->stageCase = $stageCase;
                            $rules->if_stage   = $ifStage;
                            if($rules->if_stage) {
                              $cond1 = array(
                                             array('workflow_id','=',$workflow_id),
                                             array('mark','=',$rules->if_stage)
                                            );
                              $if_stage = DB::table('tbl_wf_states')->where($cond1)->first();
                              if($if_stage) {
                                  $rules->if_stage = $if_stage->id;
                              }
                            }
                            $rules->else_stage = $elseStage;
                            if($rules->else_stage) {
                              $cond2 = array(
                                             array('workflow_id','=',$workflow_id),
                                             array('mark','=',$rules->else_stage)
                                            );
                              $else_stage = DB::table('tbl_wf_states')->where($cond2)->first();
                              if($else_stage) {
                                  $rules->else_stage = $else_stage->id;
                              }
                            }
                            $rules->status    = 1;
                        }
                      }
                    }
                  }
                  else {
                    $rules->status     = $RuleStatus;
                    $rules->stageCase  = $stage_case;
                    $rules->if_stage   = $if_stage;
                    if($rules->if_stage) {
                      $cond1 = array(
                                     array('workflow_id','=',$workflow_id),
                                     array('mark','=',$rules->if_stage)
                                    );
                      $if_stage = DB::table('tbl_wf_states')->where($cond1)->first();
                      if($if_stage) {
                          $rules->if_stage = $if_stage->id;
                      }
                    }
                    $rules->else_stage = $else_stage;
                    if($rules->else_stage) {
                      $cond2 = array(
                                     array('workflow_id','=',$workflow_id),
                                     array('mark','=',$rules->else_stage)
                                    );
                      $else_stage = DB::table('tbl_wf_states')->where($cond2)->first();
                      if($else_stage) {
                          $rules->else_stage = $else_stage->id;
                      }
                    }
                  }

                  
                  /*
                  $rules->stage_case = $RuleStatus['stage_case'];
                  $rules->if_stage = $RuleStatus['if_stage'];
                  $rules->else_stage = $RuleStatus['else_stage'];*/
                }
                else if($rules->with_rule==0){
                  $rules->status     = 0;
                  $rules->stageCase  = '';
                  $rules->if_stage   = $rules->to_state;
                  if($rules->if_stage) {
                    $cond1 = array(
                                   array('workflow_id','=',$workflow_id),
                                   array('mark','=',$rules->if_stage)
                                  );
                    $if_stage = DB::table('tbl_wf_states')->where($cond1)->first();
                    if($if_stage) {
                        $rules->if_stage = $if_stage->id;
                    }
                  }
                  $rules->else_stage = 0;
                }
              }
              else {
                  $rules->status     = 0;
                  $rules->stageCase  = '';
                  $rules->if_stage   = $rules->to_state;
                  if($rules->if_stage) {
                    $cond1 = array(
                                   array('workflow_id','=',$workflow_id),
                                   array('mark','=',$rules->if_stage)
                                  );
                    $if_stage = DB::table('tbl_wf_states')->where($cond1)->first();
                    if($if_stage) {
                        $rules->if_stage = $if_stage->id;
                    }
                  }
                  $rules->else_stage = 0;
              }
            }
            return $transitions;
          }
          return $result;
        }
    }
    public static function Testmethod($rules,$condition,$process_id,$workflow_id,$stage_case,$if_stage,$else_stage) {
      $array     = array();
      $returnVal = 0;
      foreach ($rules as $key => $value) {
        if(!isset($value['condition'])) {
          $returnVal =  WorkflowsModel::CheckActionEnabled($rules,$condition,$process_id,$workflow_id);
          return $returnVal;
        }
        else {
          $rules       = $value['rules'];
          $condition   = $value['condition'];
          $stage_case  = $value['stage_case'];
          $if_stage    = $value['if_stage'];
          $else_stage  = $value['else_stage'];
          $array[$key]['status']     = WorkflowsModel::Testmethod($rules,$condition,$process_id,$workflow_id,$stage_case,$if_stage,$else_stage);
          $array[$key]['stage_case']     = $stage_case;
          $array[$key]['if_stage']     = $if_stage;
          $array[$key]['else_stage']     = $else_stage;
        }
      }
      return $array;     
    }
    public static function getRules($workflow_id,$stage,$process_id) {
    $result      = array();
    $rules_array = array();
        if($workflow_id==null || $stage==null || $process_id==null){
            return $result;
        }
        else {
            $search      = array('workflow_id' => $workflow_id,'from_state' => $stage);
            $transitions = WorkflowsModel::get_transitions($search);
            if($transitions){ 
                $transitions = WorkflowsModel::unserialize_rules($transitions);
                foreach ($transitions as $key => $rules) {
                    if(isset($rules->rule)){
                        if(($rules->stage_action==1 || $rules->stage_action==4) && $rules->with_rule==1){
                            /*$rules->status =  WorkflowsModel::ParseRules($rules->rule,$rules->rule[0]['condition'],$process_id,$workflow_id);*/
                            $condition      = $rules->rule[0]['condition'];
                            $stage_case     = ($rules->rule[0]['stage_case'])?$rules->rule[0]['stage_case']:0;
                            $if_stage       = ($rules->rule[0]['if_stage'])?$rules->rule[0]['if_stage']:0;
                            $else_stage     = ($rules->rule[0]['else_stage'])?$rules->rule[0]['else_stage']:0;
                            $rules->status1  = WorkflowsModel::ParseRule($rules->rule[0]['rules'],$condition,$process_id,$workflow_id,$stage_case,$if_stage,$else_stage);
                            $rules->if_stage   = $rules->status1['if_stage'];
                            $rules->else_stage = $rules->status1['else_stage'];
                            $rules->status = $rules->status1['status'];
                        }
                        else {
                            $rules->if_stage   = 0;
                            $rules->else_stage = 0;
                            $rules->status     = 0;
                        } 
                    }
                    else {
                        $rules->if_stage   = 0;
                        $rules->else_stage = 0;
                        $rules->status     = 0;
                    }
                }
                return $transitions;
            }
        }
        return $result;
    }
    public static function ParseRule($rules,$condition,$process_id,$workflow_id,$stage_case,$if_stage,$else_stage) {
        $result     = array();
        $status     = 0;
        $enabled    = 0;
        $test       = array();
        //$stage_case = 0;
        //$if_stage   = 0;
        //$else_stage = 0;
        foreach ($rules as $key => $rule) { /*echo '<h3><pre>'; print_r($rules[$key]); echo '</pre></h3>'; exit;*/
           if(isset($rule['condition'])) {
            
            $stage_case = $rule['stage_case'];
            $if_stage   = $rule['if_stage'];
            $else_stage = $rule['else_stage'];
            $test[] =  WorkflowsModel::ParseRule($rule['rules'],$rule['condition'],$process_id,$workflow_id,$stage_case,$if_stage,$else_stage);
            if($rule['condition']=='AND') {
                if(in_array('0',$test)) {
                    $status = 0;
                }
                else {
                    $status = 1;
                }
            }
            else if($rule['condition']=='OR') {
                if(in_array('1',$test)) {
                    $status = 1;
                }
                else {
                    $status = 0;
                }
            }
           
            return $status;
           }
           else { 
            return WorkflowsModel::CheckActionEnabledOrNot($rules[$key],$condition,$process_id,$workflow_id); 
            echo $enabled.' = '.$condition.' -> '.$rules[$key]['rc'].'<br>';
                $result['stage_case'] = $stage_case;
                $result['if_stage']   = $if_stage;
                $result['else_stage'] = $else_stage;
                $result['status']     = $enabled; 
                if($condition=='AND' && $enabled==0) { 
                   return array(
                     'stage_case'=>$stage_case,
                     'if_stage'=>$if_stage,
                     'else_stage'=>$else_stage,
                     'status'=>$enabled
                     );
                }
                if($condition=='OR' && $enabled==1) {
                    return array(
                     'stage_case'=>$stage_case,
                     'if_stage'=>$if_stage,
                     'else_stage'=>$else_stage,
                     'status'=>$enabled
                     );
                }
                
                return array(
                     'stage_case'=>$stage_case,
                     'if_stage'=>$if_stage,
                     'else_stage'=>$else_stage,
                     'status'=>$enabled
                     );
            }

           }
           /*if($condition=='AND') {
                if(in_array('0',$result)) {
                    $status = 0;
                }
                else {
                    $status = 1;
                }
            }
            else if($condition=='OR') {
                if(in_array('1',$result)) {
                    $status = 1;
                }
                else {
                    $status = 0;
                }
            }*/
         return $result;
        return array(
                     'stage_case'=>$stage_case,
                     'if_stage'=>$if_stage,
                     'else_stage'=>$else_stage,
                     'status'=>$status
                     );
    }
    public static function CheckActionEnabledOrNot($rules,$condition,$process_id,$workflow_id) {

      $wf_operation_data = WorkflowsModel::workflow_process_data($process_id);
      $wf_data           = DB::table('tbl_wf')->where('id','=',$workflow_id)->first();
      $userID            = ($wf_operation_data->created_by)?$wf_operation_data->created_by:0;
      $userData          = DB::table('tbl_users')->where('id','=',$userID)->first();
      $user_role         = ($userData->user_role)?$userData->user_role:0;
      $user_id           = $userID;
      $userDepart        = ($userData->department_id)?$userData->department_id:'';
      $user_department   = explode(",",$userDepart);
      if($userData->user_role==1) {
        $user_department = array();
        $userDepart = DB::table('tbl_departments')->select('department_id as id')->get();
        foreach ($userDepart as $key => $value) {
            $user_department[] = $userDepart[$key]->id;
        }
      }
      /*End New Code */
      $result            = array();
      $return            = 0; 
 

      if($wf_operation_data->wf_object_type=='form' && $rules['object_type']=='form') { 
        $result[] = WorkflowsModel::CheckCondition($wf_operation_data->wf_object_id,$wf_operation_data->wf_object_type,$rules['id'],$rules['value'],
                $rules['operator'],$rules['object_type']);
            }
        $operator = $rules['operator'];
        switch($rules['object_type']) {
            case 'user_role':
                if($operator=='equal') {
                    if($user_role==$rules['value']) {
                        $result[] = 1; 
                    }
                    else {
                        $result[] = 0;
                    } 
                }
                else if($operator=='not_equal') {
                    if($user_role!=$rules['value']) {
                        $result[] = 1;
                    }
                    else {
                        $result[] = 0;
                    } 
                }
                
            break;
            case 'users':
                if($operator=='equal') {
                    if($user_id==$rules['value']) {
                        $result[] = 1;
                    }
                    else {
                        $result[] = 0;
                    }
                }
                else if($operator=='not_equal') {
                    if($user_id!=$rules['value']) {
                        $result[] = 1;
                    }
                    else {
                        $result[] = 0;
                    }
                }
                
            break;
            case 'department':
                if($operator=='equal') {
                    if(in_array($rules['value'], $user_department)) {
                        $result[] = 1;
                    }
                    else {
                        $result[] = 0;
                    }
                }
                else if($operator=='not_equal') {
                    if(in_array($rules['value'], $user_department)) {
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
        if($condition=='AND') {
            if(in_array('0',$result)) {
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
    public static function get_rulesOLD($workflow_id,$stage,$process_id) {
      /* get workflow_if 
      $wf_data     = DB::table('tbl_wf_operation')->where('id','=',$process_id)->first();
      $workflow_id = $wf_data->wf_id;

      */
      $result      = array();
      $rules_array = array();
      if($workflow_id==null || $stage==null || $process_id==null){
        return $result;
      }
      else {
        $search      = array('workflow_id' => $workflow_id,'from_state' => $stage);
        $transitions = WorkflowsModel::get_transitions($search); 
        if($transitions) {
          $transitions = WorkflowsModel::unserialize_rules($transitions);   
          foreach ($transitions as $key => $rules) {
            if(isset($rules->rule)){ 
                if($rules->with_rule==1 &&($rules->stage_action==1 || $rules->stage_action==4)) { 
                  $rules->status =  WorkflowsModel::ParseRules($rules->rule,$rules->rule[0]['condition'],$process_id,$workflow_id);
                    /*if($rules->rule[0]['condition']=='AND') {
                        if(in_array('0',$result)) {
                            $rules->status = 0;
                        }
                        else {
                            $rules->status = 1;
                        }
                    }
                    else if($rules->rule[0]['condition']=='OR') {
                        if(in_array(1,$result)) {
                            $rules->status = 1;
                        }
                        else {
                            $rules->status = 0;
                        }
                    }*/
                }
                else {
                  $rules->status =  1;
                }
                $rules->stage_case   =  (isset($rules->rule[0]['stage_case']))?$rules->rule[0]['stage_case']:0;
                $rules->if_stage   =  (isset($rules->rule[0]['if_stage']))?$rules->rule[0]['if_stage']:0;
                if($rules->if_stage) {
                    $cond1 = array(
                                   array('workflow_id','=',$workflow_id),
                                   array('mark','=',$rules->if_stage)
                                  );
                    $if_stage = DB::table('tbl_wf_states')->where($cond1)->first();
                    if($if_stage) {
                        $rules->if_stage = $if_stage->id;
                        
                    }
                }
                $rules->else_stage   =  (isset($rules->rule[0]['else_stage']))?$rules->rule[0]['else_stage']:0;
                if($rules->else_stage) {
                    $cond2 = array(
                                   array('workflow_id','=',$workflow_id),
                                   array('mark','=',$rules->else_stage)
                                  );
                    $else_stage = DB::table('tbl_wf_states')->where($cond2)->first();
                    if($else_stage) {
                        $rules->else_stage = $else_stage->id;
                    }
                }
            }
            else {
                $rules->status =  1; 
                $if_else_case  = unserialize($rules->rule_area);
                //$rules->stage_case   =  ($if_else_case['stage_case'])?$if_else_case['stage_case']:0;
                //$rules->if_stage     =  ($if_else_case['if_stage'])?$if_else_case['if_stage']:0;
                //$rules->else_stage   =  ($if_else_case['else_stage'])?$if_else_case['else_stage']:0;

                $rules->stage_case   =  (isset($if_else_case['stage_case']))?$if_else_case['stage_case']:0;
                $rules->if_stage   =  (isset($if_else_case['if_stage']))?$if_else_case['if_stage']:0;
                if($rules->if_stage) {
                    $cond1 = array(
                                   array('workflow_id','=',$workflow_id),
                                   array('mark','=',$rules->if_stage)
                                  );
                    $if_stage = DB::table('tbl_wf_states')->where($cond1)->first();
                    if($if_stage) {
                        $rules->if_stage = $if_stage->id;
                    }
                }
                $rules->else_stage   =  (isset($if_else_case['else_stage']))?$if_else_case['else_stage']:0;
                if($rules->else_stage) {
                    $cond2 = array(
                                   array('workflow_id','=',$workflow_id),
                                   array('mark','=',$rules->else_stage)
                                  );
                    $else_stage = DB::table('tbl_wf_states')->where($cond2)->first();
                    if($else_stage) {
                        $rules->else_stage = $else_stage->id;
                    }
                }
            }
            if($rules->with_rule==0) {
                $rules->if_stage = $rules->to_state;
            }
            
          }
          return $transitions;
        }
        return $result;
      }
    }

    public static function complete_worklflow($param = array())
    {

      $wf_operation_id = (isset($param['wf_operation_id']))?$param['wf_operation_id']:0;
      $handler = (isset($param['handler']))?$param['handler']:0;
      $select ="twop.completed_activity,twop.created_by,twop.wf_object_id,twop.created_at as wf_start_date,twop.updated_at as wf_end_date,tws.stage_action,tws.departments,tws.assigned_users,tws.message_content,tws.notify_requester,tws.other_user,twa.activity_name,tw.workflow_name,tw.wf_object_type,tw.wf_object_type_id";
        $query = DB::table('tbl_wf_operation as twop');
        $query->join('tbl_wf_states as tws','tws.workflow_id','=','twop.wf_id');
        $query->join('tbl_wf as tw','tw.id','=','twop.wf_id');
        $query->leftjoin('tbl_activities as twa','twa.activity_id','=','twop.completed_activity');
        $where = array('tws.type' => 'last','twop.id' => $wf_operation_id);
        $query->selectRaw($select);
        $query->where($where);
        $result =    $query->first(); 
        //print_r($result); 
        if($result)
        {
          $form_id= $result->wf_object_type_id;
          $form_response_unique_id= $result->wf_object_id;
          $form_details = \App\FormModel::form_responses_new($form_id,$form_response_unique_id);
/*echo "<pre>";
print_r($form_details);
echo "</pre>";
exit;*/
          $address = $logo = $company_name = '';
          $setings = DB::table('tbl_settings')->first();
            if($setings)
            {
                $address = $setings->settings_address;
                $logo = $setings->settings_logo;
                $company_name = $setings->settings_company_name;
            }
          $notify_requester = $result->notify_requester;
          $departments = unserialize($result->departments);
          $assigned_users = unserialize($result->assigned_users);
          $created_by = $result->created_by;
          if($notify_requester)
          {
            $assigned_users[]= $result->created_by;
          }

           $users_under_dept = DB::table('tbl_users_departments')
            ->select('users_id')
            ->whereIn('department_id',$departments)
            ->get();
            $users_ids = array();
            foreach ($users_under_dept as $users_id_val) 
            {
                $assigned_users[]= $users_id_val->users_id;
            }
          
          $other_user = explode(',',$result->other_user);
          /*print_r($other_user);
          exit;*/
          $activity_name = ($result->activity_name)?$result->activity_name:'';
          $message_content = $result->message_content;
          $workflow_name =$result->workflow_name;
          $wf_start_date =custom_date_Format($result->wf_start_date);
          $wf_end_date =custom_date_Format($result->wf_end_date);
         // $recipient_array = array('8');
          $recipient_array = array_unique($assigned_users);
          $message_content = str_replace("{status}",$activity_name,$message_content);
          $message_content = str_replace("{company name}",$company_name,$message_content);

          /************** In APp Notification START************/


          /*$notification = array();
      //notification to all users
      $notification['type']       = 'workflow';
      $notification['priority']   = 1;
      $notification['details']    = '';
      $notification['sender']     = 0;
      $notification['recipients'] = $created_by;
      $notification['type']   = 'form';
          $notification['title']  = $workflow_name.''.$activity_name;
          $notification['link']   = URL('form_details/'.$data['form_id']).'?response='.$data['form_responses_uique'];
      //function in mylibs/common
      $data['handler']->add_notification($notification);*/

       /************** In APP Notification END************/

          /************************PDF**************************/
          $pdf = new FPDF( 'P', 'mm', 'A4' );

          $pdf->Set_SiteLogo(url('logo/'.config('app.settings_logo')));
          $pdf->companyName = config('app.settings_company_name');
          $pdf->companyAddress = config('app.settings_address');
          $pdf->footerText = 'Private & Confidential ';
          $pdf->AddPage();
          
          
         /* // Set font format and font-size 
          $pdf->SetFont('Times', 'B', 20); 
            
          // Framed rectangular area 
          $pdf->Cell(176, 5, $company_name, 0, 0, 'C'); 
            
          // Set it new line 
          $pdf->Ln(); 
            
          // Set font format and font-size 
          $pdf->SetFont('Times', 'B', 12); 
            
          // Framed rectangular area 
          $pdf->Cell(176, 10, $workflow_name, 0, 0, 'C'); */
          $pdf->AliasNbPages();
          $width_cell = $pdf->GetPageWidth();
          $lmargin = $pdf->GetLMargin();
          $rmargin = $pdf->GetRMargin();
          $width_cell = ($width_cell - ($lmargin+$rmargin));
          $eq_width = $width_cell/2; 

          $pdf->Cell($width_cell,8,"Workflow Details",1,0,'L');  
          $pdf->Ln();    
          $pdf->Cell($eq_width,8,"Workflow",1,0,'L'); 
          $pdf->Cell($eq_width,8,ucfirst($workflow_name),1,0,'L');
          $pdf->Ln();   
          $pdf->Cell($eq_width,8,"Satrt Date",1,0,'L'); 
          $pdf->Cell($eq_width,8,$wf_start_date,1,0,'L'); 

          $pdf->Ln();   
          $pdf->Cell($eq_width,8,"End Date",1,0,'L'); 
          $pdf->Cell($eq_width,8,$wf_end_date,1,0,'L'); 

          $pdf->Ln();   
          $pdf->Cell($eq_width,8,"Final Status",1,0,'L'); 
          $pdf->Cell($eq_width,8,$activity_name,1,0,'L'); 

          $pdf->Ln(15);  
          $pdf->Cell($width_cell,8,"Form Details",1,0,'L');     
          $attachments = array();
         foreach($form_details as $dtc)  
         {          
            $pdf->Ln();   
            $pdf->Cell($eq_width,8,ucfirst($dtc->form_input_title),1,0,'L');  
            

                  $array_files = array();
                  $array_rand_files = array();
                  $array_size_files = array();
                        if($dtc->is_options)
                        {
                          $form_response_value = '';
                          $form_response_value_array = ($dtc->form_response_value)?unserialize($dtc->form_response_value):array();
                          foreach ($form_response_value_array as $key => $value) {
                            if($value['sel'])
                            {
                              $form_response_value = $form_response_value.''.$value['label'].', ';
                            }
                          }
                          $pdf->Cell($eq_width,8,trim(ucfirst($form_response_value),", "),1,0,'L');  
                          //$pdf->Cell(186, 10, trim(ucfirst($form_response_value),", "), 0, 0, 'L'); 
                        }
                        else{

                          $form_response_value = $dtc->form_response_value;
                          $form_response_file = $dtc->document_file_name;
                          $form_response_size = $dtc->form_response_file_size;
                          
    
                          if($dtc->form_input_type_name == 'File')
                          {
                          
                            $file_name = trim(ucfirst($form_response_value),", ");
                            $pdf->Cell($eq_width,8,$file_name,1,0,'L');  

                            $attachment = $file=config('app.base_path').$dtc->document_file_name;
                            if(file_exists($attachment) && $dtc->document_file_name)
                            {
                              $attachments[] = array('file' =>$attachment,'name' =>$file_name);
                            }
                            
                          }
                          else
                          {
                            //$pdf->Cell(186, 10, ucfirst($form_response_value), 0, 0, 'L'); 
                            $pdf->Cell($eq_width,8,trim(ucfirst($form_response_value),", "),1,0,'L');  
                          }
                        }

                      }
          $file_name = config('app.settings_company_name')."-".$workflow_name."-".time().".pdf"; 
          $file=config('app.backup_path').$file_name;
          //echo $file;
         // $pdf->Output(); 
          /*exit;*/

          /*echo "<pre>";
print_r($form_details);
print_r($attachments);
echo "</pre>";
exit;*/
          $pdf->Output($file,'F');
/************************PDF**************************/
          foreach ($recipient_array as $key => $value) {
                $recipient = DB::table('tbl_users')->select('email','user_full_name')->where('id',$value)->where('user_status',1)->first();
                if($recipient)
                {
                  //mail content details
                $email = trim($recipient->email);  
                if($email)
                {
                $subject = array('subject' =>'Workflow Complete','message' => $message_content,'to' =>$recipient->user_full_name,'email_to' => $email = $email,'link' => '', 'address'=>$address, 'logo'=>$logo, 'title'=>'','file' => $file,'attachments' => $attachments);
                //print_r($subject);
                //call mail function
                $handler->send_mail($subject);
              }
                }
                
            } 
            foreach ($other_user as $key => $value) {
                $value = trim($value);
                if($value)
                {
                  //mail content details
                $subject = array('subject' =>'Workflow Complete','message' => $message_content,'to' =>$value,'email_to' => $value,'link' => '', 'address'=>$address, 'logo'=>$logo, 'title'=>'','file' => $file,'attachments' => $attachments);
              // print_r($subject);
                //call mail function
                $handler->send_mail($subject);
                }
                
            }

        }
      return true;
    }

    ///////////////////////// Manage Workflow Operation Code //////////////////////////
    public static function manageWorkflowOperation($operation=array()){

        $wfId = (isset($operation['wfId']))?$operation['wfId']:0;
        $wfPrcsId = (isset($operation['wfPrcsId']))?$operation['wfPrcsId']:0;
        $flag = (isset($operation['flag']))?$operation['flag']:null;
        $stageId = (isset($operation['stageId']))?$operation['stageId']:0;
        $form_responses_uique = (isset($operation['form_responses_uique']))?$operation['form_responses_uique']:null;
        $handler = (isset($operation['handler']))?$operation['handler']:0;

        $handler = (isset($operation['handler']))?$operation['handler']:0;

        $wfObjectType = (isset($operation['wfObjectType']))?$operation['wfObjectType']:'';
        $wfObjectId = (isset($operation['wfObjectId']))?$operation['wfObjectId']:0;

        $select ="twod.id,twod.wf_operation_id,twod.wf_stage,twod.completed,twod.wf_stage_name,tws.stage_action,tws.departments,tws.assigned_users";
        $query = DB::table('tbl_wf_operation_details as twod');
        $query->join('tbl_wf_states as tws','tws.id','=','twod.wf_stage');
        $where = array('twod.wf_stage' => $stageId,'twod.wf_operation_id' => $wfPrcsId);
        $query->selectRaw($select);
        $query->where($where);
        $mwfo_results =    $query->get();  
        $stage_action = $mwfo_results[0]->stage_action; 
        $state = $mwfo_results[0]->wf_stage_name;
        $wf_data    = DB::table('tbl_wf')->where(array('id'=>$wfId))->first();
        $form_id = $wf_data->wf_object_type_id;
        $form_data = DB::table('tbl_forms')->select('form_name')->where('form_id',$form_id)->first();
        $form_name = $form_data->form_name;
        $wf_name    = ($wf_data->workflow_name)?$wf_data->workflow_name:'Workflow';
        //echo $stage_action;
        $data               = array();
        $data['wf_name']    = $wf_name;
        $data['form_name']  = $form_name;
        $data['state']      = $state;
        $data['wfPrcsId']   = $wfPrcsId;
        $data['stageId']    = $stageId;
        $data['form_id']    = $form_id;
        $data['flag']       = $flag;
        $data['priority']   = '1';
        $data['sender']     = Auth::user()->id;
        $data['form_responses_uique'] = $form_responses_uique;
        $data['handler']    = $handler;
         if($stage_action=='0')
        {
          $param = array('wf_operation_id' => $wfPrcsId,'handler'=>$handler);
          WorkflowsModel::complete_worklflow($param);
        }
        else if($stage_action==1)
        { // BY User
            
            $users_ids  = unserialize($mwfo_results[0]->assigned_users);
            if($users_ids) 
            {
                $data['users_ids'] = $users_ids;
                // send notification and emails assigned users and delegators
                WorkflowsModel::checkDelegation($data);
            }
            //insert notifiers to tbl_wf_operation_details
            $notified_users = implode(',', $users_ids);
            DB::table('tbl_wf_operation_details')->where('wf_stage',$stageId)->where('wf_operation_id',$wfPrcsId)->update(['notified_users'=>$notified_users]);
        }
        else if($stage_action==2)
        { // By Hierarchy
            
            $users_ids = array();
            //notification reports to
            $created = Auth::user()->report_to;
            if($created){
              array_push($users_ids, $created);
            }
              if($users_ids) 
              {
                  $data['users_ids'] = $users_ids;
                  // send notification and emails report users and delegators
                  WorkflowsModel::checkDelegation($data);
              }
              //insert notifiers to tbl_wf_operation_details
              $notified_users = implode(',', $users_ids);
              DB::table('tbl_wf_operation_details')->where('wf_stage',$stageId)->where('wf_operation_id',$wfPrcsId)->update(['notified_users'=>$notified_users]);    
        }
        else if($stage_action==3)
        { // By Group
            
            $dept_ids  = unserialize($mwfo_results[0]->departments);
            $users_under_dept = DB::table('tbl_users_departments')
            ->select('users_id')
            ->whereIn('department_id',$dept_ids)
            ->get();
            $users_ids = array();
            foreach ($users_under_dept as $users_id_val) 
            {
                array_push($users_ids, $users_id_val->users_id);
            }
            
            if($users_ids) 
            {
                $data['users_ids'] = $users_ids;
                // send notification and emails report users and delegators
                WorkflowsModel::checkDelegation($data);
            }
            //insert notifiers to tbl_wf_operation_details
            $notified_users = implode(',', $users_ids);
            DB::table('tbl_wf_operation_details')->where('wf_stage',$stageId)->where('wf_operation_id',$wfPrcsId)->update(['notified_users'=>$notified_users]);

        }
        else if($stage_action==4){ // Auto
            // echo $wfId;
            // echo $stageId;
            // echo $wfPrcsId;

           // $resrules = WorkflowsModel::get_rules($wfId,$stageId,$wfPrcsId);
            $search = array('workflow_id' => $wfId,'from_state' => $stageId,'process_id' => $wfPrcsId);
            $resrules = WorkflowsModel::validate_Rules($search);
            // print_r($resrules);
            // exit();
            $one=0;
            foreach($resrules as $rulval){
                $withRule  = $rulval->with_rule;
                $nextStageId = $rulval->to_state;
                $ruleStatus = $rulval->status;
                $ifStage = $rulval->if_stage;
                $elseStage = $rulval->else_stage;  
                $activity_id = $rulval->activity_id; 

                if($ruleStatus==1 && $ifStage && $one == 0)
                {
                    $one++;
                    //updating existing stage to completed 
                    WorkflowsModel::updateCompleteFromStage($wfPrcsId,$stageId,$activity_id,$wfObjectType,$wfObjectId,$handler);
                    WorkflowsModel::addActivity($wfId,$stageId,$wfPrcsId,$activity_id);
                            //updating existing next stage to current
                    if($stageId != $ifStage)
                    {
                                WorkflowsModel::updateCompleteToStage($wfId,$wfPrcsId,$ifStage,$activity_id);
                    $operation = array();
                    $operation['wfId'] = $wfId;
                    $operation['wfPrcsId'] = $wfPrcsId;
                    $operation['stageId'] = $ifStage;
                    $operation['flag'] = $flag;
                    $operation['form_responses_uique'] = $form_responses_uique;
                    $operation['handler'] = $handler;
                    $operation['wfObjectType'] = $wfObjectType;
                    $operation['wfObjectId'] = $wfObjectId;
                    WorkflowsModel::manageWorkflowOperation($operation);
                }
                }
                else if($ruleStatus==0 && $elseStage && $one == 0)
                {
                     $one++;
                     //updating existing stage to completed 
                    WorkflowsModel::updateCompleteFromStage($wfPrcsId,$stageId,$activity_id,$wfObjectType,$wfObjectId,$handler);
                    WorkflowsModel::addActivity($wfId,$stageId,$wfPrcsId,$activity_id);
                            //updating existing next stage to current
                    if($stageId != $elseStage)
                    {
                                WorkflowsModel::updateCompleteToStage($wfId,$wfPrcsId,$elseStage,$activity_id);

                     $operation = array();
                    $operation['wfId'] = $wfId;
                    $operation['wfPrcsId'] = $wfPrcsId;
                    $operation['stageId'] = $elseStage;
                    $operation['flag'] = $flag;
                    $operation['form_responses_uique'] = $form_responses_uique;
                    $operation['handler'] = $handler;
                    $operation['wfObjectType'] = $wfObjectType;
                    $operation['wfObjectId'] = $wfObjectId;
                    WorkflowsModel::manageWorkflowOperation($operation);
                   }
                } 
                
                /*// with rule is 1  
                if($withRule==1){
                    // checking the status is 0 or 1
                    if($ruleStatus==1){
                        
                        if($ifStage){ //checking if stage
                            WorkflowsModel::manageWorkflowOperation($wfId,$wfPrcsId,$ifStage,$flag,$form_responses_uique,$handler);
                            //updating existing stage to completed 
                            WorkflowsModel::updateCompleteFromStage($wfPrcsId,$stageId,$activity_id,$wfObjectType,$wfObjectId);
                            WorkflowsModel::addActivity($wfId,$stageId,$wfPrcsId,$activity_id);
                            //updating existing next stage to current
                            if($nextStageId){
                                WorkflowsModel::updateCompleteToStage($wfId,$wfPrcsId,$nextStageId,$activity_id);
                            }
                        }else if($elseStage){ //checking else stage
                            WorkflowsModel::manageWorkflowOperation($wfId,$wfPrcsId,$elseStage,$flag,$form_responses_uique,$handler);
                            //updating existing stage to completed 
                            WorkflowsModel::updateCompleteFromStage($wfPrcsId,$stageId,$activity_id,$wfObjectType,$wfObjectId);
                            WorkflowsModel::addActivity($wfId,$stageId,$wfPrcsId,$activity_id);
                            //updating existing next stage to current
                            if($nextStageId){
                                WorkflowsModel::updateCompleteToStage($wfId,$wfPrcsId,$nextStageId,$activity_id);
                            }
                        }
                    }else{ // (if) status is 0, check the else case is exist or not. (if) else case is exist, go to that stage.
                        if($elseStage){
                            WorkflowsModel::manageWorkflowOperation($wfId,$wfPrcsId,$elseStage,$flag,$form_responses_uique,$handler);
                            //updating existing stage to completed 
                            WorkflowsModel::updateCompleteFromStage($wfPrcsId,$stageId,$activity_id,$wfObjectType,$wfObjectId);
                            WorkflowsModel::addActivity($wfId,$stageId,$wfPrcsId,$activity_id);
                            //updating existing next stage to current
                            if($nextStageId){
                                WorkflowsModel::updateCompleteToStage($wfId,$wfPrcsId,$nextStageId,$activity_id);
                            }
                        }
                    }                    
                }else{
                    // if with rule is 0, call the function again with next stageid if exist
                    
                    if($nextStageId){
                        WorkflowsModel::manageWorkflowOperation($wfId,$wfPrcsId,$nextStageId,$flag,$form_responses_uique,$handler);
                        //updating existing stage to completed 
                        WorkflowsModel::updateCompleteFromStage($wfPrcsId,$stageId,$activity_id,$wfObjectType,$wfObjectId);
                        WorkflowsModel::addActivity($wfId,$stageId,$wfPrcsId,$activity_id);
                        //updating existing next stage to current
                        if($nextStageId){
                            WorkflowsModel::updateCompleteToStage($wfId,$wfPrcsId,$nextStageId,$activity_id);
                        }
                    }
                }*/            
            }

        }
        return true;
    }
    public static function checkDelegation($data = array())
    {
      $today = date('Y-m-d');
      $today=date('Y-m-d', strtotime($today));
      $users_ids = $data['users_ids'];
      $delegate_users = array();
      $notification = array();
      //notification to all users
      $notification['type']       = 'workflow';
      $notification['priority']   = $data['priority'];
      $notification['details']    = '';
      $notification['sender']     = $data['sender'];
      $notification['recipients'] = $users_ids;
      if($data['flag'] == 'workflow')
      {
	      $notification['title']      = 'Form \''.$data['form_name'].'\'  added into stage \''.$data['state'].'\' of workflow \''.$data['wf_name'].'\'';
	      $notification['link']       = URL('view_wf_process/'.$data['wfPrcsId']).'?stage='.$data['stageId'];
  	  }
      else if($data['flag'] == 'form')
      {
          $notification['type']   = 'form';
          $notification['title']  = 'Form \''.$data['form_name'].'\' under the work flow \''.$data['wf_name'].'\' submitted by \''.Auth::user()->user_full_name.'\'';
          $notification['link']   = URL('form_details/'.$data['form_id']).'?response='.$data['form_responses_uique'];
      }
      //function in mylibs/common
      $data['handler']->add_notification($notification);
      //check user list has any delegated users?
      foreach ($users_ids as $user) 
      {
        $delegated_details = DB::table('tbl_users')
        ->select('delegate_user',
                'delegate_from_date',
                'delegate_to_date',
                'user_full_name')
        ->where('id',$user)
        ->first();

        
        //insert to tbl_assigned_users
        DB::table('tbl_wf_assigned_users')
        ->insert(['operation_id'    =>$data['wfPrcsId'],
                  'stage_id'        =>$data['stageId'],
                  'user_id'         =>$user,
                  'action_taken_by' =>0,
                  'delegated_user'  =>(isset($delegated_details->delegate_user))?$delegated_details->delegate_user:0,
                  'activity_id'     =>0,
                  'created_at'      =>date('Y-m-d H:i:s'),
                  'updated_at'      =>date('Y-m-d H:i:s')]);

        if($delegated_details->delegate_user)
        {
          //notification to delegate users only
          $delegate_from_date = date('Y-m-d', strtotime($delegated_details->delegate_from_date));
          $delegate_to_date = date('Y-m-d', strtotime($delegated_details->delegate_to_date));
          if (($today > $delegate_from_date) && ($today < $delegate_to_date))
          {

            
            $notification['priority']   = $data['priority'];
            $notification['sender']     = $data['sender'];
            $notification['recipients'] = array($delegated_details->delegate_user);
            $notification['details']    = 'Delegated Notifications';
            if($data['flag'] == 'workflow')
            {
	            $notification['type']       = 'workflow';
	            $notification['title']      = 'Delegated Notification from '.$delegated_details->user_full_name.' - Form \''.$data['form_name'].'\'  added into stage \''.$data['state'].'\' of workflow \''.$data['wf_name'].'\'';
	            $notification['link']       = URL('view_wf_process/'.$data['wfPrcsId']).'?stage='.$data['stageId'];
            
            }
            if($data['flag'] == 'form')
            {
                $notification['type']   = 'form';
                $notification['title']  = 'Delegated Notification from '.$delegated_details->user_full_name.' - Form \''.$data['form_name'].'\' under the work flow \''.$data['wf_name'].'\' submitted by \''.Auth::user()->user_full_name.'\'';
                $notification['link']   = URL('form_details/'.$data['form_id']).'?response='.$data['form_responses_uique'];
            }
            //function in mylibs/common
            $data['handler']->add_notification($notification);
          }
          else
          {
            
          }
        }
      }
      //exit();
      
    }
    public static function addActivity($wfId,$stageId,$wfPrcsId,$activity_id,$note='') {
        DB::table('tbl_wf_operation_activity')
                                ->insert(array(
                                        'wf_operation_id'=>$wfPrcsId,
                                        'wf_stage'=>$stageId,
                                        'activity_id'=>$activity_id,
                                        'activity_order'=>0,
                                        'assigned_user'=>0,
                                        'assigned_by'=>(Auth::user())?Auth::user()->id:0,
                                        'due_date'=>date('Y-m-d'),
                                        'activity_note'=>$note,
                                        'completed'=>1,
                                        'created_at'=>date('Y-m-d H:i:s'),
                                        'updated_at'=>date('Y-m-d H:i:s')
                                        )
                                  );
    }

    /////////////////////// Updating completed stage /////////////////////////
    public static function updateCompleteFromStage($wfPrcsId,$fromStage,$activity_id,$wfObjectType='',$wfObjectId='',$handler=''){ 
        //Update Completed Stage to operation table
        $update=array();
        $update['completed_stage'] = $fromStage;
        $update['completed_activity'] = $activity_id;
        $update['updated_at'] = date("Y-m-d H:i:s");
        $where = array('id' => $wfPrcsId);
        DB::table('tbl_wf_operation')->where($where)->update($update);

        $update=array();
        $update['completed'] = 2;
        $update['activity_id'] = $activity_id;
        $where = array('wf_operation_id' => $wfPrcsId,'wf_stage' => $fromStage);
        DB::table('tbl_wf_operation_details')->where($where)->update($update);
        $criteria = DB::table('tbl_wf_states')->select('state','workflow_id')->where('id',$fromStage)->first();
        $wf_det = DB::table('tbl_wf')->select('workflow_name')->where('id',$criteria->workflow_id)->first();
        if($wfObjectType =='form')
        {
          $cond_order = array('activity_id'=>$activity_id);
          $activities   = DB::table('tbl_activities')->select('activity_name')->where($cond_order)->first();
          $update=array();
          $update['response_activity_id'] = $activity_id;
          $update['response_activity_name'] = ($activities)?$activities->activity_name:'';
          $update['updated_at'] = date("Y-m-d H:i:s");
          $update['response_activity_date'] = date("Y-m-d H:i:s");
          $where = array('form_response_unique_id' => $wfObjectId);
          DB::table('tbl_form_responses')->where($where)->update($update);
          $operation_details = DB::table('tbl_form_responses')->where($where)->first();

          if($activities)
           {
            $form_name = $operation_details->form_name;
            $activity = $activities->activity_name;

            $users_ids = array($operation_details->user_id);
            $notification               = array();
            $notification['type']       = 'form';
            $notification['priority']   = '1';
            $notification['title']      = 'Form \''.$form_name.'\' \''.$activity.'\' under the stage \''.$criteria->state.'\' of workflow \''.$wf_det->workflow_name;
            $notification['details']    = '';
            $notification['link']=URL('form_details/'.$operation_details->form_id).'?response='.$operation_details->form_response_unique_id;
            $notification['recipients']     = $users_ids;
            $notification['sender'] = Auth::user()->id;
            
            $handler->add_notification($notification);
            //print_r($notification);
           } 
        } 
        //exit();
        return true;      
    }

    public static function updateCompleteToStage($workflow_id,$wfPrcsId,$toStage,$activity_id){ 
        WorkflowsModel::updateAllCompleteUpToStage($workflow_id,$wfPrcsId,$toStage,$activity_id);
        $update=array();
        $update['completed'] = 1;
        $update['updated_at'] = date('Y-m-d H:i:s');
        $where = array('wf_operation_id' => $wfPrcsId,'wf_stage' => $toStage);
        DB::table('tbl_wf_operation_details')->where($where)->update($update);

        //Update Current Stage to operation table
        $update=array();
        $update['current_stage'] = $toStage;
        $update['updated_at'] = date("Y-m-d H:i:s");
        $where = array('id' => $wfPrcsId);
        DB::table('tbl_wf_operation')->where($where)->update($update);
    }
    public static function updateAllCompleteUpToStage($workflow_id,$wfPrcsId,$toStage,$activity_id){ 
        $cond_order       = array('id'=>$toStage,'workflow_id'=>$workflow_id);
            $order      = DB::table('tbl_wf_states')->select('mark')->where($cond_order)->first();
            $stages_between=array();
            if($order)
            {
            $stages_between = DB::table('tbl_wf_states as twf')
            ->join('tbl_wf as tw','twf.workflow_id','=','tw.id')
            ->select('twf.id as stage_id')
            ->where('twf.mark','<=',$order->mark)
            ->where('tw.id','=',$workflow_id)
            ->groupBy('twf.state')
            ->orderBy('twf.mark','asc')
            ->get();
            }
            $update=array();
            $update['completed'] = 3;
            if($stages_between){
                foreach ($stages_between as $key => $stage) {
                    DB::table('tbl_wf_operation_details')->where('wf_operation_id',$wfPrcsId)->where('wf_stage',$stage->stage_id)->where('completed',0)->update($update);
                }
            }
    }
    /////////////////////// Updating completed stage /////////////////////////


    public static function ApplyCondition($rules,$condition,$process_id,$workflow_id) {
      $result = array();
      foreach ($rules as $key => $rule) {
        if(isset($rule['rules']) && isset($rule['condition'])) {
          $result[] = WorkflowsModel::ParseRules($rule['rules'],$rule['condition'],$process_id,$workflow_id);
        }
        else {
          $result[] = WorkflowsModel::CheckActionEnabled($rules,$condition,$process_id,$workflow_id);
        }
      } 
        if($condition=='AND') {
            if(in_array('0',$result)) {
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
    }
    public static function ParseRules($rules,$condition=null,$process_id,$workflow_id) {
      if(isset($rules['rules']) && isset($rules['condition'])) {
        return WorkflowsModel::ParseRules($rules['rules'],$rules['condition'],$process_id,$workflow_id);
      }
      else {
        return WorkflowsModel::ApplyCondition($rules,$condition,$process_id,$workflow_id);
      }
    }
    public static function CheckActionEnabled($rules,$condition,$process_id,$workflow_id) {
      /*$wf_operation_data = WorkflowsModel::workflow_process_data($process_id);
      $user_role         = Auth::user()->user_role;
      $user_id           = Auth::user()->id;
      $user_department   = explode(",",Auth::user()->department_id);
      $result            = array();
      $return            = 0; */
      $wf_operation_data = WorkflowsModel::workflow_process_data($process_id);
      /* New Code */
      $wf_data           = DB::table('tbl_wf')->where('id','=',$workflow_id)->first();
      $userID            = ($wf_operation_data->created_by)?$wf_operation_data->created_by:0;
      $userData          = DB::table('tbl_users')->where('id','=',$userID)->first();
      $user_role         = ($userData->user_role)?$userData->user_role:0;
      $user_id           = $userID;
      $userDepart        = ($userData->department_id)?$userData->department_id:'';
      $user_department   = explode(",",$userDepart);
      if($userData->user_role==1) {
        $user_department = array();
        $userDepart = DB::table('tbl_departments')->select('department_id as id')->get();
        foreach ($userDepart as $key => $value) {
            $user_department[] = $userDepart[$key]->id;
        }
      }
      /*End New Code */
      $result            = array();
      $return            = 0; 
       
      foreach ($rules as $key => $rule) {
            if(!isset($rule['id'])) {
                //$result[] = 0;
            }
            else {
                if($wf_operation_data->wf_object_type=='form' && $rule['object_type']=='form') {
                $result[] = WorkflowsModel::CheckCondition($wf_operation_data->wf_object_id,$wf_operation_data->wf_object_type,$rule['id'],$rule['value'],
                $rule['operator'],$rule['object_type']);
            }
            else {
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
            }
            
        }
        if($condition=='AND') {
            if(in_array('0',$result)) {
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
    public static function CheckCondition($wf_object_id,$wf_object_type,$frm_inp_id,$value,$operator,$object_type) {
        $result = false;
        if($wf_object_type=='form' && $object_type=='form') {
            $table = 'tbl_form_responses';
            $query = DB::table($table);
            switch ($operator) { 
                case 'equal':
                    $where = array(
                            array('form_response_unique_id','=',$wf_object_id),
                            array('form_input_id','=',$frm_inp_id),
                            array('form_response_selected','=',$value)
                            ); 
                    $result = $query->where($where)->first(); 
                    
                    break;
                case 'not_equal':
                    $where = array(
                            array('form_response_unique_id','=',$wf_object_id),
                            array('form_input_id','=',$frm_inp_id),
                            array('form_response_selected','!=',$value)
                            );
                    $result = $query->where($where)->first();
                    break;
                case 'greater':
                    $where = array(
                            array('form_response_unique_id','=',$wf_object_id),
                            array('form_input_id','=',$frm_inp_id)
                            );
                    $result = $query->where($where)->first(); 
                    if($result) {
                        if($result->form_response_selected>$value) {
                            return 1;
                        }
                        else {
                            return 0;
                        }
                    }
                    break;
                case 'greater_or_equal':
                    $where = array(
                            array('form_response_unique_id','=',$wf_object_id),
                            array('form_input_id','=',$frm_inp_id)
                            );
                    $result = $query->where($where)->first(); 
                    if($result) {
                        if($result->form_response_selected>=$value) {
                            return 1;
                        }
                        else {
                            return 0;
                        }
                    }
                    break;
                case 'less':
                    $where = array(
                            array('form_response_unique_id','=',$wf_object_id),
                            array('form_input_id','=',$frm_inp_id)
                            );
                   $result = $query->where($where)->first(); 
                    if($result) {
                        if($result->form_response_selected<$value) {
                            return 1;
                        }
                        else {
                            return 0;
                        }
                    }
                    break;
                case 'less_or_equal':
                    $where = array(
                            array('form_response_unique_id','=',$wf_object_id),
                            array('form_input_id','=',$frm_inp_id)
                            );
                    $result = $query->where($where)->first();
                    if($result) {
                        if($result->form_response_selected<=$value) {
                            return 1;
                        }
                        else {
                            return 0;
                        }
                    }
                    break;
                case 'begins_with':
                    $where = array(
                            array('form_response_unique_id','=',$wf_object_id),
                            array('form_input_id','=',$frm_inp_id),
                            array('form_response_selected','like',$value.'%')
                            );
                    $result = $query->where($where)->first(); 
                    if($result) {
                        if($result->form_response_value<=$value) {
                            return 1;
                        }
                        else {
                            return 0;
                        }
                    }
                    break;
                case 'ends_with':
                    $where = array(
                            array('form_response_unique_id','=',$wf_object_id),
                            array('form_input_id','=',$frm_inp_id),
                            array('form_response_selected','like','%'.$value)
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
    public static function unserialize_rules($transitions) {
        //$wf_operation_data = WorkflowsModel::workflow_process_data($process_id);
        foreach ($transitions as $key => $trans) {
            if($trans->with_rule==1) {                
                $trans->rule = unserialize($trans->rule_area);
            }
        }
        return $transitions;
    }
    public static function workflow_process($data=array(),$limit,$start_from)
    {       
        
        $workflow_id=(isset($data['workflow_id']))?$data['workflow_id']:0;
        $process_id=(isset($data['process_id']))?$data['process_id']:0;
        $select ="wop.id as process_id,wop.wf_operation_name,wop.wf_id,wop.wf_object_id,wop.wf_object_type,wf.wf_object_type_id,wop.created_by,wop.created_at,wop.updated_at as last_updated_at,wop.completed,wop.completed_activity,ta.activity_name as completed_activity_name";
        $query = DB::table('tbl_wf_operation as wop');
        $query->join('tbl_wf as wf','wf.id','=','wop.wf_id');
        $query->leftJoin('tbl_activities as ta', function($join){
                        $join->on('ta.activity_id','=','wop.completed_activity');
                      });
        if($workflow_id)
        {
            $where = array('wop.wf_id' => $workflow_id);
            $query->where($where);
        } 

        if($process_id)
        {
            
            $where = array('wop.id' => $process_id);
            $query->where($where);
        }       
        $query->selectRaw($select);
        $query->orderBy('wop.updated_at', 'DESC');
        if(($limit != null) && ($start_from != null))
        {
        $result = $query->offset($start_from)->limit($limit)->get();
        }
        else{
          $result = $query->get();
        }
        $results = array();

        foreach($result as $r)
        {
          $wf_object_type_label= $wf_object_type_name='';
          
          $object_details =WorkflowsModel::wf_object_details($r->wf_object_id,$r->wf_object_type);  

          $obj_data=array('obj_label' => '','obj_name' => '','obj_icon' => '','obj_form_id' => '');
          $r->started = date("Y-m-d",strtotime($r->created_at));

          $r->obj_label = $object_details['obj_label'];
          $r->obj_name = $object_details['obj_name'];
          $r->obj_icon = $object_details['obj_icon'];


          $r->tasks =WorkflowsModel::get_wf_operation_details($r->process_id);  
          $results[] = $r;
        }

        //print_r($results);
        return  $results;
    }

    public static function get_wf_operation_details($wf_operation_id=0)
    {       
        $select ="twop.wf_stage,twop.wf_stage_name,twop.completed as state_completed,ts.type,twop.activity_id as state_completed_activity,ta.activity_name as state_completed_activity_name";
                    $query = DB::table('tbl_wf_operation_details as twop');
                    $query->Leftjoin('tbl_wf_states as ts','ts.id','=','twop.wf_stage');
                    $query->leftJoin('tbl_activities as ta', function($join){
                        $join->on('ta.activity_id','=','twop.activity_id');
                      });
                    $where = array('twop.wf_operation_id' => $wf_operation_id);
                    $query->selectRaw($select);
                    $query->where($where);
                    $result_doc =    $query->get();
                    return $result_doc;
    }


     public static function workflow_process_data($process_id=0)
    {
        $select ="wop.id as process_id,wop.wf_operation_name,wop.wf_id,wop.wf_object_id,wop.wf_object_type,wop.created_by,wop.created_at,wop.updated_at as last_updated_at,wop.completed,wop.current_stage,wop.completed_stage,wop.completed_activity,tw.wf_object_type_id";

        $select .=",tw.workflow_name,tw.workflow_color";
        $query = DB::table('tbl_wf_operation as wop');

        $query->join('tbl_wf as tw','tw.id','=','wop.wf_id');
        
        if($process_id)
        {
            $where = array('wop.id' => $process_id);
            $query->where($where);
        }       
        $query->selectRaw($select);
        $query->orderBy('wop.id', 'DESC');
        $result =    $query->first();
        return  $result;
    }

       public static function task_details($data = array())
    {
       
       $process_id=(isset($data['process_id']))?$data['process_id']:0;
       $stage=(isset($data['stage']))?$data['stage']:0;

       $select ="twop.wf_stage,twop.wf_stage_name,twop.completed as state_completed,ts.stage_action,ts.stage_group,ts.stage_percentage,ts.state,ts.type,ts.assigned_users,ts.departments";
        $query = DB::table('tbl_wf_operation_details as twop');
        $query->Leftjoin('tbl_wf_states as ts','ts.id','=','twop.wf_stage');
        $where = array('twop.wf_operation_id' => $process_id,'twop.wf_stage' => $stage);
        $query->selectRaw($select);
        $query->where($where);
        $result_doc =    $query->first();
        if(!$result_doc) 
        {
          $result_doc = new \stdClass();
        }

        $search = array('process_id' => $process_id,'stage' => $stage);


       /* if(isset($result_doc->type) && $result_doc->type == 'last')
        {
          $search = array('process_id',$process_id);
        }*/
        $result_doc->activities = WorkflowsModel::task_activities($search);  
        return $result_doc;
    }

     public static function task_activities($data = array())
    {
       $workflow_id=(isset($data['workflow_id']))?$data['workflow_id']:0;
       $process_id=(isset($data['process_id']))?$data['process_id']:0;
       $wf_stage=(isset($data['stage']))?$data['stage']:0;

        $select ="twopa.id,twopa.created_at,twopa.due_date,twopa.activity_note,twopa.completed as activity_completed,twopa.updated_at,ta.activity_name,ta.activity_modules,tu.user_full_name as user_full_name,tu1.user_full_name as user_full_name1,twopd.wf_stage_name as stage_name,twopa.wf_stage";
        $query = DB::table('tbl_wf_operation_activity as twopa');
        $query->join('tbl_activities as ta','ta.activity_id','=','twopa.activity_id');
        $query->join('tbl_wf_operation_details as twopd','twopa.wf_stage','=','twopd.wf_stage');
        $query->Leftjoin('tbl_users as tu','tu.id','=','twopa.assigned_user');
        $query->Leftjoin('tbl_users as tu1','tu1.id','=','twopa.assigned_by');

        $where = array();
                    $query->selectRaw($select);
                    if($process_id)
                    {
                        $where['twopa.wf_operation_id'] = $process_id;
                        $where['twopd.wf_operation_id'] = $process_id;
                    }
                     if($wf_stage)
                    {
                        $where['twopa.wf_stage'] = $wf_stage;
                    }
                    if($where)
                    {
                        $query->where($where);
                    }
                    
                    $query->groupBy('twopa.id');
                    $query->orderBy('twopa.id', 'DESC');
                    $result_doc =    $query->get();
                    return $result_doc;
    }


    public static function get_transitions($data = array())
    {
       $workflow_id=(isset($data['workflow_id']))?$data['workflow_id']:0;
       $from_state=(isset($data['from_state']))?$data['from_state']:0;
       $to_state=(isset($data['to_state']))?$data['to_state']:0;

        $select ="ts.id,ts.name,ts.from_state,ts.to_state,ts.workflow_id,ts.with_rule,ts.rule_area,ts.activity_id,wfst.stage_action";
        $query = DB::table('tbl_wf_transitions as ts')->join('tbl_wf_states as wfst','ts.from_state','=','wfst.id');
        $where = array();
                    $query->selectRaw($select);
                    if($workflow_id)
                    {
                        $where['ts.workflow_id'] = $workflow_id;
                    }
                     if($from_state)
                    {
                        $where['ts.from_state'] = $from_state;
                    }

                    if($to_state)
                    {
                        $where['ts.to_state'] = $to_state;
                    }
                    if($where)
                    {
                        $query->where($where);
                    }
                    $query->orderBy('ts.tr_order', 'ASC');
                    $result_doc =    $query->get();
                    return $result_doc;
    }


    public static function wf_object_details($obj_id='',$obj_type='')
    { 
            $obj_data=array('obj_label' => '','obj_name' => '','obj_icon' => '','obj_form_id' => '');
            if($obj_type == 'document')
            {
                    $select ="td.document_name,td.document_file_name";
                    $query = DB::table('tbl_documents as td');
                    $where = array('td.document_id' => $obj_id);
                    $query->selectRaw($select);
                    $query->where($where);
                    $result_doc =    $query->first();
                    
                    $obj_name=  ($result_doc)?$result_doc->document_name:''; 
                    $file_name=  ($result_doc)?$result_doc->document_file_name:''; 
                    $obj_icon='fa fa-file-o';
                    if($file_name)
                    {
                        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                        if($ext=='pdf')
                        {
                            $obj_icon='fa fa-file-pdf-o';
                        }
                        else if($ext=='png'||$ext=='jpg'||$ext=='jpeg'||$ext=='tiff'||$ext=='tif')
                        {
                            $obj_icon='fa fa-file-image-o';
                        }
                        else if($ext=='docx'||$ext=='doc')
                        {
                            $obj_icon='fa fa-file-word-o';
                        }
                        else if($ext=='txt')
                        {
                            $obj_icon='fa fa-file-text-o';
                        }
                        else if($ext=='zip'||$ext=='rar')
                        {
                            $obj_icon='fa fa-file-archive-o';
                        }
                        else if($ext=='xls'||$ext=='xlsx')
                        {
                            $obj_icon='fa fa-file-excel-o';
                        }
                        else
                        {
                            $obj_icon='fa fa-file-o';
                        }

                    }
                    

                    $obj_data=array('obj_label' => 'Document','obj_name' => $obj_name,'obj_icon' => $obj_icon,'obj_form_id' => '');
            }

            if($obj_type == 'form')
            {
                    $select ="tfr.form_name,tfr.created_at,tfr.user_id,tu.user_full_name,tfr.form_id";
                    $query = DB::table('tbl_form_responses as tfr');
                    $query->join('tbl_users as tu','tfr.user_id','=','tu.id');
                    $where = array('tfr.form_response_unique_id' => $obj_id);
                    $query->selectRaw($select);
                    $query->where($where);
                    $result_doc =    $query->first();
                    
                    $obj_name=  ($result_doc)?$result_doc->form_name.'-'.$result_doc->user_full_name.'-'.dtFormat($result_doc->created_at):''; 
                    $form_id=  ($result_doc)?$result_doc->form_id:''; 
                    $obj_data=array('obj_label' => 'Form','obj_name' => $obj_name,'obj_icon' => 'fa fa-newspaper-o','obj_form_id' => $form_id);
            }
            return $obj_data;
    }

    public static function get_task_count($workflow_id=0)
    {       
        $select ="COUNT(ts.id) as task_count";
                    $query = DB::table('tbl_wf_states as ts');
                    $where = array('ts.workflow_id' => $workflow_id);
                    $query->selectRaw($select);
                    $query->where($where);
                    $query->groupBy('ts.workflow_id');
                    $result_doc =    $query->first();
                    return (isset($result_doc->task_count))?$result_doc->task_count:0;
    }

     public static function get_process_count($workflow_id=0)
    {       
        $select ="COUNT(ts.id) as task_count";
                    $query = DB::table('tbl_wf_operation as ts');
                    $where = array('ts.wf_id' => $workflow_id);
                    $query->selectRaw($select);
                    $query->where($where);
                    $query->groupBy('ts.wf_id');
                    $result_doc =    $query->first();
                    return (isset($result_doc->task_count))?$result_doc->task_count:0;
    }
    public static function manage_wf_users($data=array())
    {
         //ECHO "-D-";
		 //print_r($data);
		 $timestamp = date("Y-m-d H:i:s");
         $wf_users  = (isset($data['wf_users']))?$data['wf_users']:array(); 
         $workflow_id = (isset($data['workflow_id']))?$data['workflow_id']:0;
         $wf_stage = (isset($data['wf_stage']))?$data['wf_stage']:0;
              $i=0;
              $reset = array('edit' => 0);
              $reset_state = array('workflow_id' => $workflow_id,'wf_stage' => $wf_stage);
              DB::table('tbl_wf_users')->where($reset_state)->update($reset);
              foreach ($wf_users as $key => $value) 
              {
                $i++;
             
              $users = array();
              $users['workflow_id'] = $workflow_id;
              $users['wf_stage'] = $wf_stage;
              $users['user_id'] = $value;
              $users['updated_at'] = $timestamp;
              $users['edit'] = 1;

              $where_state = array('workflow_id' => $workflow_id,'wf_stage' => $wf_stage,'user_id' => $value);
              $result = DB::table('tbl_wf_users')->where($where_state)->first(); 
              if($result)
              {
                  
                  DB::table('tbl_wf_users')->where($where_state)->update($users);
                            
              }
              else
              { 
                  $users['created_at'] = $timestamp;
                  $state_id = DB::table('tbl_wf_users')->insertGetId($users);
              }     
              }

              $reset_state = array('workflow_id' => $workflow_id,'wf_stage' => $wf_stage,'edit' => 0);
              DB::table('tbl_wf_users')->where($reset_state)->delete();
              return true;

    }

    //
    public static function get_workflows()
    {       
        $select ="wf.id as workflow_id,wf.workflow_name,wf.workflow_color";
        $result = DB::table('tbl_wf as wf')->selectRaw($select)->groupBy('wf.id')->orderBy('wf.workflow_name', 'ASC')->get();
        return  $result;
    }

public static function get_workflow($workflow_id=0)
    {       
        
        $where = array('wf.id' => $workflow_id);
        $select ="wf.id as workflow_id,wf.workflow_name,wf.workflow_color,wf.task_flow,wf.assigned_users,wf.wf_object_type,wf.wf_object_type_id";
        $query = DB::table('tbl_wf as wf');
        
        if($where)
        {
            $query->where($where);
        }       
        $query->selectRaw($select);
        
        $result =    $query->first();

        
        return  $result;
    }



    public static function get_workflow_stage_details($workflow_id=0)
    {       
        
        $where = array('wf.workflow_id' => $workflow_id);
        $select ="wf.workflow_id,wf.workflow_name,wf.workflow_stage_id,wf.workflow_stage_name,wf.workflow_stage_order";
        $query = DB::table('tbl_workflows as wf');
        
        if($where)
        {
            $query->where($where);
        }       
        $query->selectRaw($select);
        $query->groupBy('wf.workflow_stage_id');
        $query->orderBy('wf.workflow_stage_order', 'ASC');
        
        $result =    $query->get();

        $wf_details = array();
        foreach ($result as $value) 
        {
            $row_data = array();
            $row_data['workflow_stage_name'] = ($value->workflow_stage_name)?$value->workflow_stage_name:'';
            $row_data['workflow_stage_id'] = ($value->workflow_stage_id)?$value->workflow_stage_id:0;
            
            $row_data['docs'] = WorkflowsModel::doc_activity($row_data['workflow_stage_id']);
            $wf_details[] = $row_data;
        }
        
        return  $wf_details;
    } 

    
    public static function doc_activity($workflow_stage_id=0)
    { 
            $workflow_doc_id = config('app.workflow_doc_id');
            $workflow_object_type = config('app.workflow_object_type');
            $query = DB::table('tbl_document_workflows as tdw');
            $where = array('tdw.workflow_stage_id' => $workflow_stage_id);
            if($workflow_object_type)
            {
              $where['tdw.document_workflow_object_type']  =  $workflow_object_type;
            }
            if($workflow_doc_id)
            {
              $where['tdw.document_workflow_object_id']    =   $workflow_doc_id;
            }
            $query->where($where);
            $query->groupBy('tdw.document_workflow_object_id');
            $query->orderBy('tdw.document_workflow_id', 'DESC');
            $result_doc =    $query->get();
            $docs =array();
            foreach ($result_doc as $doc_value) 
            {
               $docs_row = array();
               $docs_row['obj_id']    =   $doc_value->document_workflow_object_id;
               $docs_row['obj_type']  =   $doc_value->document_workflow_object_type;
               $obj_datas  =   WorkflowsModel::obj_name_with_data($docs_row['obj_id'],$docs_row['obj_type']);

               $docs_row['obj_name']  = $obj_datas['obj_name'];
               $docs_row['obj_icon']  = $obj_datas['obj_icon'];
               $docs_row['obj_form_id']  = $obj_datas['obj_form_id'];
                $query = DB::table('tbl_document_workflows as tdw');
                $query->Leftjoin('tbl_activities as ta','tdw.activity_id','=','ta.activity_id');
                $where = array('tdw.document_workflow_object_id' => $docs_row['obj_id'],'tdw.workflow_stage_id' => $workflow_stage_id);
                $query->where($where);
                $query->orderBy('tdw.activity_order', 'ASC');
                $result_act =    $query->get();
                $activity =array();
                foreach ($result_act as $act_value) 
                {
                   $activity_row = array();
                   $activity_row['document_workflow_id']    =   $act_value->document_workflow_id;
                   $activity_row['activity_id']    =   $act_value->activity_id;
                   $activity_row['activity_note']  =   ($act_value->document_workflow_activity_notes)?$act_value->document_workflow_activity_notes:'';
                   $activity_row['activity_name']  =   $act_value->activity_name;
                   $activity_row['assigned_by']  =   $act_value->document_workflow_activity_by_user;

                   $activity_row['assigned_by_user_name']  =   '';

                   if($activity_row['assigned_by'])
                   {
                    $assigned_by_user = WorkflowsModel::get_user_data($activity_row['assigned_by']);
                    $activity_row['assigned_by_user_name']  = ($assigned_by_user)?$assigned_by_user->user_full_name:'';     
                   }

                   

                   $activity_row['assigned_to']  =   $act_value->document_workflow_responsible_user;

                   $activity_row['assigned_to_user_name']  =   '';

                   if($activity_row['assigned_to'])
                   {
                    $assigned_to_user = WorkflowsModel::get_user_data($activity_row['assigned_to']);
                    $activity_row['assigned_to_user_name']  = ($assigned_to_user)?$assigned_to_user->user_full_name:'';     
                   }
                   

                   $activity_row['activity_date']  =   ($act_value->document_workflow_activity_date)?date('Y-m-d',strtotime($act_value->document_workflow_activity_date)):'';
                   $activity_row['activity_due_date']  =   ($act_value->document_workflow_activity_due_date)?date('Y-m-d',strtotime($act_value->document_workflow_activity_due_date)):'';

                   $activity_row['action_activity']  =   ($act_value->action_activity)?$act_value->action_activity:'';
                   $activity_row['action_activity_name']  =   ($act_value->action_activity_name)?$act_value->action_activity_name:'';

                   $activity_row['action_activity_note']  =   ($act_value->action_activity_note)?$act_value->action_activity_note:'';

                   $activity_row['action_activity_date']  =   ($act_value->action_activity_date)?date('Y-m-d',strtotime($act_value->action_activity_date)):'';


                   $activity[]=$activity_row;
                }
                $docs_row['doc_activity']  =   $activity;
               $docs[]=$docs_row;
            }
            return $docs;
    }

public static function obj_name($obj_id='',$obj_type='')
    { 
            $obj_name='';
            if($obj_type == 'document')
            {
                    $select ="td.document_name";
                    $query = DB::table('tbl_documents as td');
                    $where = array('td.document_id' => $obj_id);
                    $query->selectRaw($select);
                    $query->where($where);
                    $result_doc =    $query->first();
                    
                    $obj_name=  ($result_doc)?$result_doc->document_name:''; 
            }

            if($obj_type == 'form')
            {
                    $select ="tfr.form_name,tfr.created_at,tfr.user_id,tu.user_full_name";
                    $query = DB::table('tbl_form_responses as tfr');
                    $query->join('tbl_users as tu','tfr.user_id','=','tu.id');
                    $where = array('tfr.form_response_unique_id' => $obj_id);
                    $query->selectRaw($select);
                    $query->where($where);
                    $result_doc =    $query->first();
                    
                    $obj_name=  ($result_doc)?$result_doc->form_name.'-'.$result_doc->user_full_name.'-'.$result_doc->created_at:''; 
            }
            return $obj_name;
    }

    public static function obj_name_with_data($obj_id='',$obj_type='')
    { 
            $obj_data=array('obj_name' => '','obj_icon' => '','obj_form_id' => '');
            if($obj_type == 'document')
            {
                    $select ="td.document_name,td.document_file_name";
                    $query = DB::table('tbl_documents as td');
                    $where = array('td.document_id' => $obj_id);
                    $query->selectRaw($select);
                    $query->where($where);
                    $result_doc =    $query->first();
                    
                    $obj_name=  ($result_doc)?$result_doc->document_name:''; 
                    $file_name=  ($result_doc)?$result_doc->document_file_name:''; 
                    $obj_icon='fa fa-file-o';
                    if($file_name)
                    {
                        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                        if($ext=='pdf')
                        {
                            $obj_icon='fa fa-file-pdf-o';
                        }
                        else if($ext=='png'||$ext=='jpg'||$ext=='jpeg'||$ext=='tiff'||$ext=='tif')
                        {
                            $obj_icon='fa fa-file-image-o';
                        }
                        else if($ext=='docx'||$ext=='doc')
                        {
                            $obj_icon='fa fa-file-word-o';
                        }
                        else if($ext=='txt')
                        {
                            $obj_icon='fa fa-file-text-o';
                        }
                        else if($ext=='zip'||$ext=='rar')
                        {
                            $obj_icon='fa fa-file-archive-o';
                        }
                        else if($ext=='xls'||$ext=='xlsx')
                        {
                            $obj_icon='fa fa-file-excel-o';
                        }
                        else
                        {
                            $obj_icon='fa fa-file-o';
                        }

                    }
                    

                    $obj_data=array('obj_name' => $obj_name,'obj_icon' => $obj_icon,'obj_form_id' => '');
            }

            if($obj_type == 'form')
            {
                    $select ="tfr.form_name,tfr.created_at,tfr.user_id,tu.user_full_name,tfr.form_id";
                    $query = DB::table('tbl_form_responses as tfr');
                    $query->join('tbl_users as tu','tfr.user_id','=','tu.id');
                    $where = array('tfr.form_response_unique_id' => $obj_id);
                    $query->selectRaw($select);
                    $query->where($where);
                    $result_doc =    $query->first();
                    
                    $obj_name=  ($result_doc)?$result_doc->form_name.'-'.$result_doc->user_full_name.'-'.$result_doc->created_at:''; 
                    $form_id=  ($result_doc)?$result_doc->form_id:''; 
                    $obj_data=array('obj_name' => $obj_name,'obj_icon' => 'fa fa-newspaper-o','obj_form_id' => $form_id);
            }
            return $obj_data;
    }

    public static function get_activities($type='')
    { 
        $select ="ta.activity_id,ta.activity_name,ta.last_activity";
        $result = DB::table('tbl_activities as ta')->selectRaw($select);
        if($type)
        {
            $result->where('ta.activity_modules', 'LIKE', '%'.$type.'%');
        }
        
        return $result->orderBy('ta.activity_name', 'ASC')->get();
    }

     public static function get_workflow_activity($document_workflow_id=0)
    { 
            $select ="tdw.*,ta.*,tw.*,tdw.created_at as created_date,ta.activity_id";
            $query = DB::table('tbl_document_workflows as tdw');
            $query->join('tbl_activities as ta','tdw.activity_id','=','ta.activity_id');
            $query->join('tbl_workflows as tw','tdw.workflow_stage_id','=','tw.workflow_stage_id');
            $where = array('tdw.document_workflow_id' => $document_workflow_id);
            $query->selectRaw($select);
            $query->where($where);
            return $query->first();



            
    }

     public static function wf_new_docs($document_workflow_id=0)
    { 
            $serach_query = (Input::get('query'))?Input::get('query'):'';
            $wf_id = (Input::get('wf_id'))?Input::get('wf_id'):0;
            
            $select ="td.document_id,td.document_name,td.document_no";
            $query = DB::table('tbl_documents as td');

            $query->selectRaw($select);
            if($serach_query)
            {
                $query->where('td.document_name', 'like', '%'.$serach_query.'%');
                $query->orwhere('td.document_id', 'like', '%'.$serach_query.'%');
            }
            
            $query->groupBy('td.document_id');
            $query->orderBy('td.document_name', 'ASC');
            return $query->get();



            
    }

     public static function users_list($document_workflow_id=0)
    { 
            $serach_query = (Input::get('query'))?Input::get('query'):'';
            
            $select ="tu.id,tu.username,tu.user_full_name";
            $query = DB::table('tbl_users as tu');
            $query->selectRaw($select);
            if($serach_query)
            {
                $query->where('tu.username', 'like', '%'.$serach_query.'%');
                $query->orwhere('tu.email', 'like', '%'.$serach_query.'%');
                $query->orwhere('tu.user_full_name', 'like', '%'.$serach_query.'%');
            }
            
            $query->groupBy('tu.id');
            $query->orderBy('tu.username', 'ASC');

            return $query->get();



            
    }

    public static function department_list($document_workflow_id=0)
    { 
            $serach_query = (Input::get('query'))?Input::get('query'):'';
            
            $select ="tu.department_id as id,tu.department_name as name";
            $query = DB::table('tbl_departments as tu');
            $query->selectRaw($select);
            if($serach_query)
            {
                $query->where('tu.department_name', 'like', '%'.$serach_query.'%');
            }
            
            $query->groupBy('tu.department_id');
            $query->orderBy('tu.department_name', 'ASC');

            return $query->get();



            
    }

     public static function read_notification($workflow_id=0,$username='')
    { 
            
            $update = array('tdw.document_workflow_notifcation_to_status' => 0);
            /*$where = array('tdw.document_workflow_responsible_user' => $username,'tdw.document_workflow_notifcation_to_status' => 1,'tw.workflow_id' => $workflow_id);
                $query = DB::table('tbl_document_workflows as tdw');
                $query->join('tbl_workflows as tw','tdw.workflow_stage_id','=','tw.workflow_stage_id');
                $query->where($where)->update($update);*/

                return true;

            
    }

     public static function get_user_data($username='')
    { 
          
            
            $select ="tu.id,tu.username,tu.user_full_name";
            $query = DB::table('tbl_users as tu');
            $query->selectRaw($select);
            $query->where('tu.username', '=', $username);

            return $query->first();



            
    }

    public static function get_workflow_docs($workflow_id=0)
    { 
            
            $where = array('tw.workflow_id' => $workflow_id);
            
            $select ="td.document_id,td.document_name,td.document_no";
            $query = DB::table('tbl_documents as td');
            $query->Join('tbl_document_workflows as tdw', function($join)
                         {
                             $join->on('tdw.document_workflow_object_id', '=', 'td.document_id');
                             $join->on('tdw.document_workflow_object_type','=',DB::raw("'document'"));
                         });
            /* $query->Join('tbl_workflows as tw', function($join)
                         {
                             $join->on('tdw.workflow_stage_id', '=', 'tw.workflow_stage_id');
                         });*/
            $query->selectRaw($select);
            $query->where($where);
            $query->groupBy('td.document_id');
            $query->orderBy('td.document_name', 'ASC');

return $query->get();
           /* $where = array('tw.workflow_id' => $workflow_id);
            
            $select ="tfr.form_response_unique_id as document_id,tfr.form_name as document_name,tfr.created_at as document_no";
            $query1 = DB::table('tbl_form_responses as tfr');
            $query1->Join('tbl_document_workflows as tdw', function($join)
                         {
                             $join->on('tdw.document_workflow_object_id', '=', 'tfr.form_response_unique_id');
                             $join->on('tdw.document_workflow_object_type','=',DB::raw("'form'"));
                         });
             $query1->Join('tbl_workflows as tw', function($join)
                         {
                             $join->on('tdw.workflow_stage_id', '=', 'tw.workflow_stage_id');
                         });
            $query1->selectRaw($select);
            $query1->where($where);
            $query1->groupBy('tfr.form_response_unique_id');
            $query1->orderBy('tfr.form_name', 'ASC');
            return $query->union($query1)->get();*/
            
    }

    public static function get_workflow_forms($workflow_id=0)
    { 
            
            $where = array('tw.workflow_id' => $workflow_id);
            $where = array();
            $select ="tfr.form_response_unique_id as id,tfr.form_name as name,tfr.created_at as date,tu.user_full_name";
            $query = DB::table('tbl_form_responses as tfr');
            $query->Join('tbl_document_workflows as tdw', function($join)
                         {
                             $join->on('tdw.document_workflow_object_id', '=', 'tfr.form_response_unique_id');
                             $join->on('tdw.document_workflow_object_type','=',DB::raw("'form'"));
                         });
             /*$query->Join('tbl_workflows as tw', function($join)
                         {
                             $join->on('tdw.workflow_stage_id', '=', 'tw.workflow_stage_id');
                         });*/
            $query->Join('tbl_users as tu', function($join)
                         {
                             $join->on('tfr.user_id', '=', 'tu.id');
                         }); 
            $query->selectRaw($select);
            $query->where($where);
            $query->groupBy('tfr.form_response_unique_id');
            $query->orderBy('tfr.form_name', 'ASC');
            return $query->get();
            
    }

     public static function add_form_to_workflow($form_response_id=0)
    {
          $select ="tfw.form_id,tfw.form_workflow_id";
          $where = array('tfr.form_response_id' => $form_response_id);
          $query = DB::table('tbl_form_responses as tfr');
          $query->join('tbl_form_workflows as tfw','tfw.form_id','=','tfr.form_id');
          $query->selectRaw($select);
          $query->where($where)->first();
          $result = $query->where($where)->first();
          $results[]=$result; 
          if($result)
          {
            $select ="tw.workflow_stage_id";
            $where = array('tw.workflow_id' => $result->form_id);
            $query = DB::table('tbl_workflows as tw');
            $query->selectRaw($select);
            $result1 = $query->orderBy('tw.workflow_stage_order','ASC')->where($where)->first();
            $results[]=$result1;
                  
          }
          
          return $results;
    }


    public static function serch_doc($data=array())
    { 
           $where=array();
            $res = array();
             $object_type = (Input::get('object_type'))?Input::get('object_type'):'';
          if($object_type == 'document')
          {   
             $object_name = (Input::get('object_name'))?Input::get('object_name'):'';
             $document_no = (Input::get('document_no'))?Input::get('document_no'):'';
             
             $document_type = (Input::get('document_type'))?Input::get('document_type'):'';
            
            if($document_type)
            {
                $where['td.document_type_id']=$document_type;
            }
            $select ="td.document_id,td.document_name,td.document_no,tdt.document_type_name";
            $query = DB::table('tbl_documents as td');
            $query->join('tbl_document_types as tdt','td.document_type_id','=','tdt.document_type_id');
            $query->selectRaw($select);
            if($where)
            {
              $query->where($where);
            }
            if($object_name !='')
            {
                $query->where('td.document_name', 'like', '%'.$object_name.'%');
            }
             if($document_no !='')
            {
                $query->where('td.document_no', 'like', '%'.$document_no.'%');
            }
            
            $query->groupBy('td.document_id');
            $res = $query->orderBy('td.document_name', 'desc')->paginate(10);
          }
          else if($object_type == 'form')
          {   
             $form_type = (Input::get('form_type'))?Input::get('form_type'):'';
             $form_user = (Input::get('form_user'))?Input::get('form_user'):'';

             $form_submit_date = (Input::get('form_submit_date'))?Input::get('form_submit_date'):'';
             
            
            $where=array();
            if($form_type)
            {
               $where['tf.form_id']=$form_type;
            }
            if($form_user)
            {
               $where['tr.user_id']=$form_user;
            }
            
            $select ="tr.form_response_id,tr.form_response_unique_id,tr.form_id,tr.form_name,tu.user_full_name,tr.created_at";
            $query = DB::table('tbl_form_responses as tr');
            $query->join('tbl_forms as tf','tr.form_id','=','tf.form_id');
            $query->join('tbl_users as tu','tr.user_id','=','tu.id');
            $query->selectRaw($select);
            if($where)
            {
              $query->where($where);
            }
           if($form_submit_date)
           {
            $query->where(DB::raw("(DATE_FORMAT(tr.created_at,'%Y-%m-%d'))"),$form_submit_date);
           }
             /*if($document_no !='')
           }
            {
                $query->where('td.document_no', 'like', '%'.$document_no.'%');
            }*/
            
            $query->groupBy('tr.form_response_unique_id');
            $res = $query->orderBy('tr.form_response_id', 'desc')->paginate(10);
          }

            return $res;
            
    }

    public static function wf_stages()
    {       
        
        
        $select ="wfs.id,wfs.id as stage_id,wfs.workflow_id,wfs.shape,wfs.state";
        $query = DB::table('tbl_wf_states as wfs');      
        $query->selectRaw($select);
        $query->orderBy('wfs.state', 'ASC');
        
        $result =    $query->get();

        
        return  $result;
    }


    public static function get_object_info($data=array())
    { 
           $where=array();
           $res = array();
           $object_type = (isset($data['objecttype']))?$data['objecttype']:'';
           $object_id = (isset($data['objectid']))?$data['objectid']:'';
          if($object_type == 'document')
          {   
             
            
            $where['td.document_id']=$object_id;
            
            $select ="td.document_id as object_id,'document' as object_type,td.document_name as object_name,td.document_no as object_data1,tdt.document_type_name as object_data2";
            $query = DB::table('tbl_documents as td');
            $query->join('tbl_document_types as tdt','td.document_type_id','=','tdt.document_type_id');
            $query->selectRaw($select);
            if($where)
            {
              $query->where($where);
            }
            
            
            $query->groupBy('td.document_id');
            $res = $query->orderBy('td.document_name', 'desc')->first();
          }
          else if($object_type == 'form')
          {   
             
            $where['tr.form_response_unique_id']=$object_id;          
            $select ="tr.form_response_unique_id as object_id,'form' as object_type,CONCAT_WS('-',tr.form_name,tu.user_full_name,tr.created_at) as object_name,'0' as object_data1,'1' as object_data2";
            $query = DB::table('tbl_form_responses as tr');
            $query->join('tbl_forms as tf','tr.form_id','=','tf.form_id');
            $query->join('tbl_users as tu','tr.user_id','=','tu.id');
            $query->selectRaw($select);
            if($where)
            {
              $query->where($where);
            }
           
            $query->groupBy('tr.form_response_unique_id');
            $res = $query->orderBy('tr.form_response_id', 'desc')->first();
          }

            return $res;
            
    }
    public static function get_count_stages($workflow_id=0)
    {
        $where = array('wf.id' => $workflow_id);
        $query = DB::table('tbl_wf as wf');
        if($where)
        {
            $query->where($where);
        } 
        $result = $query->count();
        return $result;

    }
    public static function get_object_name($wf_object_type,$wf_object_id)
    {
        if($wf_object_type == 'document')
          {
            $object = DB::table('tbl_documents')
            ->select('document_name as object_name')
            ->where('document_id',$wf_object_id)
            ->first();
          }
          else
          {
            $object = DB::table('tbl_forms')
            ->select('form_name as object_name')
            ->where('form_id',$wf_object_id)->first();
          }
          return $object;
    }
    public static function wf_privilages($workflow_id=0)
    {       
        $select ="twf.privilege_key,twf.privilege_status,twf.privilege_value_user,twf.privilege_value_department";
        $where = array('twf.workflow_id' => $workflow_id);
        $result = DB::table('tbl_wf_privileges as twf')->selectRaw($select)->where($where)->get();
        //convert the comma searated values(dept,user) to array
        foreach ($result as $key => $value) {
            $dept = explode(',', $value->privilege_value_department);
            $value->privilege_department_array = $dept;
            $user = explode(',', $value->privilege_value_user);
            $value->privilege_user_array = $user;
        }
        
        return  $result;
    }

     public static function rule_components($data=array())
    {       
        $wf_objects = array();  

        /* Common for all type rule */
        /*Add user roles to rule components */
        $wf_row = array();
        $wf_row['id'] = 'user_role';
        $wf_row['object_type'] = 'user_role';
        $wf_row['column_name'] = 'User Role';
        $wf_row['column_type'] = 'select';
        $type_options=WorkflowsModel::get_user_roles_component();  ;
        $wf_row['type_options'] = json_encode($type_options);
        $wf_row['multi_select'] = 1;
        $wf_objects[] = $wf_row;   
         /* Add user roles to rule components */


          /*Add user roles to rule components */
        $wf_row = array();
        $wf_row['id'] = 'users';
        $wf_row['object_type'] = 'users';
        $wf_row['column_name'] = 'User';
        $wf_row['column_type'] = 'select';
        $type_options=WorkflowsModel::get_user_component();  ;
        $wf_row['type_options'] = json_encode($type_options);
        $wf_row['multi_select'] = 1;
        $wf_objects[] = $wf_row;   
         /* Add user roles to rule components */

          /*Add user roles to rule components */
        $wf_row = array();
        $wf_row['id'] = 'department';
        $wf_row['object_type'] = 'department';
        $wf_row['column_name'] = 'Department';
        $wf_row['column_type'] = 'select';
        $type_options=WorkflowsModel::get_department_component();  ;
        $wf_row['type_options'] = json_encode($type_options);
        $wf_row['multi_select'] = 1;
        $wf_objects[] = $wf_row;   
         /* Add user roles to rule components */

        $object_type = (isset($data['object_type']))?$data['object_type']:'normal';
        $object_id = (isset($data['object_id']))?$data['object_id']:0;
        if($object_type == 'form')
        {
          
          $where=array('tfi.form_id' => $object_id);
          $select ="tfi.form_input_id as id,tfi.form_input_title as column_name,tfit.form_input_type_common as column_type,tfi.form_Input_options as type_options,tfit.is_options";
            $query = DB::table('tbl_form_inputs as tfi');
            $query->Leftjoin('tbl_form_input_types as tfit','tfi.form_input_type','=','tfit.form_input_type')->selectRaw($select)->where($where);
            $wf_objects_result = $query->orderBy('tfi.form_input_title', 'ASC')->get();

            if($wf_objects_result)
            {
                $wf_objects[] = WorkflowsModel::get_end_component();
            }

            foreach ($wf_objects_result as $key => $value) 
            {
                $wf_row = array();
                $wf_row['id'] = $value->id;
                $wf_row['object_type'] = 'form';
                $wf_row['column_name'] = $value->column_name;
                $wf_row['column_type'] = strtolower($value->column_type);
                if($value->is_options)
                {
                  $type_options = ($value->type_options)?unserialize($value->type_options):array();
                }
                else
                {
                  $type_options = array();
                }
                $wf_row['type_options'] = json_encode($type_options);
                $wf_row['multi_select'] = $value->is_options;;
                $wf_objects[] = $wf_row;    
            }      
        }
        else if($object_type == 'document')
        {
           $where=array('tdt.document_type_id' => $object_id);
           $select ="tdt.document_type_column_id as id,tdt.document_type_column_name as column_name,tdt.document_type_column_type as column_type,tdt.document_type_options as type_options";
           $wf_objects_result = DB::table('tbl_document_types_columns as tdt')->selectRaw($select)->where($where)->orderBy('tdt.document_type_column_name', 'ASC')->get();

           if($wf_objects_result)
            {
                $wf_objects[] = WorkflowsModel::get_end_component();
            }

           foreach ($wf_objects_result as $key => $value) 
            {
                $wf_row = array();
                $wf_row['id'] = $value->id;
                $wf_row['object_type'] = 'document';
                $wf_row['column_name'] = $value->column_name;
                $wf_row['column_type'] = $column_type = strtolower($value->column_type);
                if($column_type == 'piclist')
                {
                  $type_options = ($value->type_options)?explode(',', $value->type_options):array();
                }
                else
                {
                  $type_options = array();
                }
                $wf_row['type_options'] = json_encode($type_options);
                $wf_row['multi_select'] =0;
                $wf_objects[] = $wf_row;    
            }
        } 
        

        
        return  $wf_objects;
    }

     public static function get_workflow_single_stage($d = array())
    {       
        $where = array();
        $where['wfs.workflow_id'] = (isset($d['workflow_id']))?$d['workflow_id']:0;
        $where['wfs.id'] = (isset($d['stage_id']))?$d['stage_id']:0;
        $select ="wfs.*";
        $query = DB::table('tbl_wf_states as wfs');
        
        if($where)
        {
            $query->where($where);
        }       
        $query->selectRaw($select);
        
        $result =    $query->first();
        return  $result;
    }

     public static function object_rule_components($data=array())
    {       
        $wf_objects = array();
        $object_type = (isset($data['object_type']))?$data['object_type']:'normal';
        $object_id = (isset($data['object_id']))?$data['object_id']:0;
        $wf_object_id = (isset($data['wf_object_id']))?$data['wf_object_id']:0;
        if($object_type == 'form')
        {
          
          $where=array('tfr.form_response_unique_id' => $wf_object_id);
          $select ="tfr.form_response_selected as column_value,tfi.form_input_id as id,tfi.form_input_title as column_name,tfit.form_input_type_common as column_type,tfi.form_Input_options as type_options,tfit.is_options";
            $query = DB::table('tbl_form_responses as tfr');
            $query->join('tbl_form_inputs as tfi','tfr.form_input_id','=','tfi.form_input_id');
            $query->join('tbl_form_input_types as tfit','tfi.form_input_type','=','tfit.form_input_type')->selectRaw($select)->where($where);
            $wf_objects_result = $query->orderBy('tfi.form_input_title', 'ASC')->get();

            foreach ($wf_objects_result as $key => $value) 
            {
                $wf_row = array();
                $wf_row['id'] = $value->id;
                $wf_row['object_type'] = 'form';
                $wf_row['column_name'] = $value->column_name;
                $wf_row['column_type'] = strtolower($value->column_type);
                if($value->is_options)
                {
                  $type_options = ($value->type_options)?unserialize($value->type_options):array();
                }
                else
                {
                  $type_options = array();
                }
                $wf_row['type_options'] = json_encode($type_options);
                $wf_row['column_value'] = $value->column_value;
                $wf_row['multi_select'] =$value->is_options;
                $wf_objects[] = $wf_row;    
            }  
               /*Add user roles to rule components */
                $wf_row = array();
                $wf_row['id'] = 'user_role';
                $wf_row['object_type'] = 'user_role';
                $wf_row['column_name'] = 'User Role';
                $wf_row['column_type'] = 'select';
                $type_options=array();
                $wf_row['type_options'] = json_encode($type_options);
                $wf_row['column_value'] = WorkflowsModel::getRulevalues($param='userRole',$wf_object_id);
                $wf_row['multi_select'] = 1;
                $wf_objects[] = $wf_row;   
              /* Add user roles to rule components */


              /*Add user roles to rule components */
                $wf_row = array();
                $wf_row['id'] = 'users';
                $wf_row['object_type'] = 'users';
                $wf_row['column_name'] = 'User';
                $wf_row['column_type'] = 'select';
                $type_options=array();
                $wf_row['type_options'] = json_encode($type_options);
                $wf_row['column_value'] = WorkflowsModel::getRulevalues($param='User',$wf_object_id);
                $wf_row['multi_select'] = 1;
                $wf_objects[] = $wf_row;   
            /* Add user roles to rule components */

            /*Add user roles to rule components */
            $wf_row = array();
            $wf_row['id'] = 'department';
            $wf_row['object_type'] = 'department';
            $wf_row['column_name'] = 'Department';
            $wf_row['column_type'] = 'select';
            $type_options=array();
            $wf_row['type_options'] = json_encode($type_options);
            $wf_row['column_value'] = WorkflowsModel::getRulevalues($param='userDepartment',$wf_object_id);
            $wf_row['multi_select'] = 1;
            $wf_objects[] = $wf_row;   
          /* Add user roles to rule components */
    
        }
        else if($object_type == 'document')
        {
           $where=array('tdc.document_id' => $wf_object_id);
           $select ="tdc.document_column_value as column_value,tdt.document_type_column_id as id,tdt.document_type_column_name as column_name,tdt.document_type_column_type as column_type,tdt.document_type_options as type_options";
           $query = DB::table('tbl_documents_columns as tdc');
           $query->join('tbl_document_types_columns as tdt','tdc.document_type_column_id','=','tdt.document_type_column_id');
           $wf_objects_result = $query->selectRaw($select)->where($where)->orderBy('tdt.document_type_column_name', 'ASC')->get();
           foreach ($wf_objects_result as $key => $value) 
            {
                $wf_row = array();
                $wf_row['id'] = $value->id;
                $wf_row['object_type'] = 'document';
                $wf_row['column_value'] = $value->column_name;
                $wf_row['column_type'] = $column_type = strtolower($value->column_type);
                if($column_type == 'piclist')
                {
                  $type_options = ($value->type_options)?explode(',', $value->type_options):array();
                }
                else
                {
                  $type_options = array();
                }
                $wf_row['type_options'] = json_encode($type_options);
                $wf_row['column_value'] = $value->column_value;
                $wf_row['multi_select'] =0;
                $wf_objects[] = $wf_row;    
            }
        } 
        else
        {
            $wf_objects = array();
        }


        return  $wf_objects;
    }
    public static function getRulevalues($param,$wf_object_id) {
      $wf_Operation_Data = DB::table('tbl_wf_operation')->where('wf_object_id',$wf_object_id)->first();
      $CreatedUserID     = $wf_Operation_Data->created_by; // user id, who create the process
      $userData          = DB::table('tbl_users')->where('id',$CreatedUserID)->first();
      switch($param) {
        case 'userRole':
          $result = $userData->user_role;
        break;
        case 'userDepartment' :
          if($userData->user_role==1) {
            $departments = DB::table('tbl_departments')->select('department_id as id')->get();
            $result = array();
            foreach ($departments as $key => $value) {
              $result[] = $value->id;
            }
          }
          else {
            $result = explode(',',$userData->department_id);
          }          
        break;
        case 'User' :
          $result = $CreatedUserID;
        break;
        default:
        $result = '';
      }
      return $result;
    }
      public static function get_user_roles_component()
    {       
        $select ="user_role_id as id,user_role_name as label,'0' as sel";
        $query = DB::table('tbl_user_roles');      
        $query->selectRaw($select);
        $query->orderBy('user_role_name', 'ASC');
        $result =    $query->get();
        return  $result;
    }

    public static function get_department_component()
    {       
        $select ="department_id as id,department_name as label,'0' as sel";
        $query = DB::table('tbl_departments');      
        $query->selectRaw($select);
        $query->orderBy('department_name', 'ASC');
        $result =    $query->get();
        return  $result;
    }

    public static function get_end_component()
    {
      
        $wf_row = array();
        $wf_row['id'] = '-1';
        $wf_row['object_type'] = 'invalid';
        $wf_row['column_name'] = '------';
        $wf_row['column_type'] = 'invalid';
        $type_options=array('-1');
        $wf_row['type_options'] = json_encode($type_options);
        return $wf_row; 
    }

    public static function get_user_component()
    {       
        $select ="id as id,user_full_name as label,'0' as sel";
        $query = DB::table('tbl_users');      
        $query->selectRaw($select);
        $query->orderBy('user_full_name', 'ASC');
        $result =    $query->get();
        return  $result;
    }

     public static function getNestedChildren($arr=array(), $parent=0)
    {
      $out = array();
        foreach ($arr as $key => $val) 
        {
          if ($val['parentid'] == $parent) 
          {
            $rules = WorkflowsModel::getNestedChildren($arr, $val['rc']);
            if ($rules) 
            {
              $val['rules'] = $rules;
            } 
            $out[]=$val;
          }
        }
      return $out;
    }
    public static function get_all_users()
    {
        $users = DB::table('tbl_users')->select('id','user_full_name','department_id','user_role')->get();
            foreach($users as $val):
                $department_ids =  $val->department_id;
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
                $department_ids =  explode(',',$department_ids);
                $departments = DB::table('tbl_departments')->whereIn('department_id',$department_ids)->select(DB::raw('group_concat(department_name) as department_name'))->get(); 
                $val->departments = $departments;
            endforeach; 
        return $users;
    }

    
public static function setStatus($result=array(),$condition) 
        {
            $status=0;
            if($condition == 'AND')
            {
            $status=1;
            }
            foreach($result as $r)
                {
                  if($condition == 'OR')
                  {
                    if(isset($r['status']) && $r['status'] == 1)
                    {
                      $status=1;      
                    }    
                  }
                  else if($condition == 'AND')
                  {
                    if(isset($r['status']) && $r['status'] == 0)
                    {
                      $status=0;      
                    }    
                  }   
                   
                }
                return $status;
        }
        
       public static function parse_Group($rule, &$param,$o_items) 
        {
                $result = array();
                $condition = $rule['condition'];
                $stage_case = $rule['stage_case'];

                foreach($rule['rules'] as $i => $r) 
                {
                    if(array_key_exists('condition', $r)) 
                    {
                        $result[] = WorkflowsModel::parse_Group($r, $param,$o_items);
                    } 
                    else 
                    {
                       $result[] = WorkflowsModel::parse_Rule($r, $param,$o_items,$condition);
                    }
                }

                $status = WorkflowsModel::setStatus($result,$condition); 
                $row = array();
                $row['status'] = $status;
                $row['condition'] = $condition;
                $row['stage_case'] = $stage_case;
                $row['result'] = $result;  
                
            return $row;

        }
        public static function parse_Rule($rule, &$param,$o_items,$condition)  
        {
            $rresult = array();
            $rule['status'] = 0;
            $rule['rule_condition'] = $condition;
            $operator=(isset($rule['operator']))?$rule['operator']:'';
            $objectType = (isset($rule['object_type']))?$rule['object_type']:'';
            foreach($o_items as $oi)
            {
                if($oi['id'] == $rule['id'])
                {
                  $oi_column_value = $oi['column_value'];
                  $rule_value      = $rule['value'];
                  $column_type     = $oi['column_type'];
                  if($column_type=='date') {
                    $oi_column_value = ($oi_column_value)?date('Y-m-d',strtotime($oi_column_value)):'';
                    $rule_value = ($rule_value=='today')?date('Y-m-d'):date('Y-m-d',strtotime($rule_value));
                  }
                  $rule['oi_column_value'] = $oi_column_value;
                  $rule['rule_value'] = $rule_value;

                    switch($operator)
                    {
                       // CASE QUAL
                       case 'equal': 
                        if($objectType=='department') {
                          $rule_value = explode(',',$rule_value);
                          if(array_intersect($rule_value,$oi_column_value)) {
                            $rule['status'] = 1;
                          }
                        }
                        else if($objectType=='users') {
                          $rule_value = explode(',',$rule_value);
                          if(in_array($oi_column_value,$rule_value)){
                            $rule['status'] = 1;
                          }
                        }
                        else if($objectType=='user_role') {
                          $rule_value = explode(',',$rule_value);
                          if(in_array($oi_column_value,$rule_value)){
                            $rule['status'] = 1;
                          }
                        }
                        else {
                            if($oi_column_value == $rule_value)
                            {
                              $rule['status'] = 1;      
                            }
                        }                        
                        break;
                      // CASE NOT QUAL
                        case 'not_equal':
                        if($objectType=='department') {
                          $rule_value = explode(',',$rule_value);
                          if(!array_intersect($rule_value,$oi_column_value)){
                            $rule['status'] = 1;
                          }
                        }
                        else if($objectType=='users') {
                          $rule_value = explode(',',$rule_value);
                          if(!in_array($oi_column_value,$rule_value)){
                            $rule['status'] = 1;
                          }
                        }
                        else if($objectType=='user_role') {
                          $rule_value = explode(',',$rule_value);
                          if(!in_array($oi_column_value,$rule_value)){
                            $rule['status'] = 1;
                          }
                        }
                        else {
                          if($oi_column_value != $rule_value)
                          {
                            $rule['status'] = 1;      
                          }
                        }                        
                        break;

                        case 'greater': 
                        if($column_type=='date') {
                          $date_rule = new Carbon($rule_value); 
                          $date_form = new Carbon($oi_column_value);
                          if($date_form > $date_rule) {
                            $rule['status'] = 1;
                          }                          
                        }
                        else {
                          if($oi_column_value > $rule_value)
                          {
                            $rule['status'] = 1;      
                          }
                        }
                        
                        break;

                         case 'greater_or_equal': 
                        if($column_type=='date') {
                          $date_rule = new Carbon($rule_value); 
                          $date_form = new Carbon($oi_column_value);
                          if($date_form >= $date_rule) {
                            $rule['status'] = 1;
                          } 
                        }
                        else {
                          if($oi_column_value >= $rule_value)
                          {
                            $rule['status'] = 1;      
                          }
                        }
                        
                        break;

                         case 'less':
                        if($column_type=='date') {
                          $date_rule = new Carbon($rule_value); 
                          $date_form = new Carbon($oi_column_value);
                          if($date_form < $date_rule) {
                            $rule['status'] = 1;
                          }
                        }
                        else {
                           if($oi_column_value < $rule_value)
                            {
                              $rule['status'] = 1;      
                            }
                        }
                        break;

                         case 'less_or_equal':
                        if($column_type=='date') {
                          $date_rule = new Carbon($rule_value); 
                          $date_form = new Carbon($oi_column_value);
                          if($date_form <= $date_rule) {
                            $rule['status'] = 1;
                          }
                        }
                        else {
                          if($oi_column_value <= $rule_value)
                          {
                            $rule['status'] = 1;      
                          }
                        }
                        
                        break;

                    }
                }
            }
            return $rule;
        }
       public static function OLD_parse_Rule($rule, &$param,$o_items,$condition)  
        {
            $rresult = array();
            $rule['status'] = 0;
            $rule['rule_condition'] = $condition;
            $operator=(isset($rule['operator']))?$rule['operator']:'';
            $objectType = $rule['object_type'];
            foreach($o_items as $oi)
            {
                if($oi['id'] == $rule['id'])
                {
                  $oi_column_value = $oi['column_value'];
                  $rule_value      = $rule['value'];
                  $column_type     = $oi['column_type'];
                  if($column_type=='date') {
                    $oi_column_value = ($oi_column_value)?date('Y-m-d',strtotime($oi_column_value)):'';
                    $rule_value = ($rule_value=='today')?date('Y-m-d'):date('Y-m-d',strtotime($rule_value));
                  }
                  $rule['oi_column_value'] = $oi_column_value;
                  $rule['rule_value'] = $rule_value;

                    switch($operator)
                    {
                       case 'equal': 
                        if($objectType=='department') {
                          $rule_value = explode(',',$rule_value);
                          if(array_intersect($rule_value,$oi_column_value)) {
                            $rule['status'] = 1;
                          }
                        }
                        else if($objectType=='users') {
                          $rule_value = explode(',',$rule_value);
                          if(in_array($oi_column_value,$rule_value)){
                            $rule['status'] = 1;
                          }
                        }
                        else if($objectType=='user_role') {
                          $rule_value = explode(',',$rule_value);
                          if(in_array($oi_column_value,$rule_value)){
                            $rule['status'] = 1;
                          }
                        }
                        else {
                            if($oi_column_value == $rule_value)
                            {
                              $rule['status'] = 1;      
                            }
                        }
                        
                        break;
                        case 'not_equal':
                        if($objectType=='department') {
                          $rule_value = explode(',',$rule_value);
                          if(!array_intersect($rule_value,$oi_column_value)){
                            $rule['status'] = 1;
                          }
                        }
                        else if($objectType=='users') {
                          $rule_value = explode(',',$rule_value);
                          if(!in_array($oi_column_value,$rule_value)){
                            $rule['status'] = 1;
                          }
                        }
                        else if($objectType=='user_role') {
                          $rule_value = explode(',',$rule_value);
                          if(!in_array($oi_column_value,$rule_value)){
                            $rule['status'] = 1;
                          }
                        }
                        else {
                          if($oi_column_value != $rule_value)
                          {
                            $rule['status'] = 1;      
                          }
                        }                        
                        break;

                        case 'greater': 
                        if($oi_column_value > $rule_value)
                        {
                          $rule['status'] = 1;      
                        }
                        break;

                         case 'greater_or_equal': 
                        if($oi_column_value >= $rule_value)
                        {
                          $rule['status'] = 1;      
                        }
                        break;

                         case 'less': 
                        if($oi_column_value < $rule_value)
                        {
                          $rule['status'] = 1;      
                        }
                        break;

                         case 'less_or_equal': 
                        if($oi_column_value <= $rule_value)
                        {
                          $rule['status'] = 1;      
                        }
                        break;

                    }
                }
            }
            return $rule;
        }

        public static function validate_Rules($data=array())  
        {
            $workflow_id = $data['workflow_id'];
            $search = array();
            $search['workflow_id'] = $workflow_id;  
            $search['from_state'] = $data['from_state'];  

            $transitions = WorkflowsModel::get_transitions($search);

            $select ="twop.wf_object_type,twop.wf_object_id";
        $query = DB::table('tbl_wf_operation as twop');
        $query->Leftjoin('tbl_wf_operation_details as twod','twod.wf_operation_id','=','twop.id');
        $where = array('twod.wf_stage' => $data['from_state'],'twod.wf_operation_id' => $data['process_id']);
        $query->selectRaw($select);
        $query->where($where);
        $mwfo_results =    $query->first();
            $where = array();
            $where['object_type'] = ($mwfo_results)?$mwfo_results->wf_object_type:'';  
            $where['wf_object_id'] = ($mwfo_results)?$mwfo_results->wf_object_id:'';  
            $object_items = WorkflowsModel::object_rule_components($where);
            
            
            $buttons = array();
            foreach($transitions as $key => $tc)
            {
                 $result = $params = $full_result =array();
                 $row = array('name' => $tc->name);
                 $row['id'] = $tc->id;
                 $row['activity_id'] = $tc->activity_id;
                 $row['from_state'] = $tc->from_state;
                 $row['workflow_id'] = $tc->workflow_id;
                 $row['with_rule'] = $tc->with_rule;   
                 $row['to_state'] = $tc->to_state;
                 $row['status'] = 0;
                 $row['if_stage'] = 0;
                 $row['else_stage'] = 0;
                 // $row['object_items'] = $object_items; 
                //  $row['mwfo_results'] = $mwfo_results; 
                 if($tc->with_rule)
                 {
                   $wh = array('transition_id' => $tc->id);
              $rule_result = DB::table('tbl_wf_transition_rule')->where($wh)->orderBy('sort_order','ASC')->get();
              $chk=0;
              foreach ($rule_result as $key1 => $value1) 
              {

                   $result = array();
                    $rule_area = (isset($value1->rule_area) && $value1->rule_area)? unserialize($value1->rule_area):array();

                   $if_stage   = (isset($value1->if_stage) && $value1->if_stage)?$value1->if_stage:0;

                   $else_stage   = (isset($value1->else_stage) && $value1->else_stage)?$value1->else_stage:0;

                    $status=0;    
                    if(isset($rule_area[0]['condition']) && $rule_area[0]['condition']) 
                    {

            
                      $condition  = $rule_area[0]['condition'];
                     
                      $rules      = (isset($rule_area[0]['rules']))?$rule_area[0]['rules']:array();
                      $stage_case = (isset($rule_area[0]['stage_case']))?$rule_area[0]['stage_case']:'';
                      $status=0;
                    foreach($rules as $index => $rule) 
                    {
                        if(array_key_exists('condition', $rule)) 
                        {
                           $rule['stage_case']= $stage_case;
                           $result[] = WorkflowsModel::parse_Group($rule, $params,$object_items);
                           
                           $status = WorkflowsModel::setStatus($result,$condition); 
                           $full_result[] = $result;


                        } 
                        else 
                        {
                           $result[] = WorkflowsModel::parse_Rule($rule, $params,$object_items,$condition);
                           
                            
                        }

                        
                    }
                    $status = WorkflowsModel::setStatus($result,$condition);  
                  
            
                        
                    } 

                    if($chk == 0)
                    {
                      
                     

                    

                    $row['if_stage'] = $if_stage;
                    $row['else_stage'] = $else_stage;
                    $row['condition'] = $condition;
                    $row['stage_case'] = $stage_case;
                    $row['result'] = $result;

                    $new_status=0;
                    if($condition =='AND')
                    {
                          $new_status=1;
                    }
                    $l=0;
                    foreach($result as $r)
                    {
                      $r_status = $r['status'];
                          if($condition =='OR')
                          {
                              if($r['status'] == 1)
                              {
                                  $new_status=1;
                              }
                              
                          }
                          if($condition =='AND')
                          {
                              if($r['status'] == 0)
                              {
                                  $new_status=0;
                              }
                              
                          }
                    }

                    if($new_status && $if_stage)
                    {
                      $chk++;
                    }
                    elseif ($else_stage) 
                    {
                       $chk++;
                    } 

                    $row['status'] = $new_status;
                  }

                   

                  }
                  /* rule_result end */

                 }
                 else
                 {
                    $row['status'] = 1; 
                    $row['if_stage'] = $tc->to_state;
                    $row['activity_id'] = $tc->activity_id;
                    $row['from_state'] = $tc->from_state;
                    $row['condition'] = '';
                    $row['else_stage'] = 0;    
                 }
                
                $buttons[] = (object)$row;
                
            }

            
            return $buttons;
        }
         
        public static function getDdelegateUser($process_id,$stage) {
          $data   = array();
          $result = DB::table('tbl_wf_assigned_users')
                                                    ->select('delegated_user')
                                                    ->where('operation_id','=',$process_id)
                                                    ->where('stage_id','=',$stage)
                                                    ->get();
          if($result) {
            foreach ($result  as $key => $value) {
              $data[] = $value->delegated_user;
            }
          }
          return $data;
        }

         public static function applied_rule_text($data = array())
    {
      $stage_action   = (isset($data['stage_action']))?$data['stage_action']:0;
      $stage_group   = (isset($data['stage_group']))?$data['stage_group']:0;
      $stage_percentage   = (isset($data['stage_percentage']))?$data['stage_percentage']:0;
      $applied_rule_text = '';
      if($stage_action == 1)
      {
        $applied_rule_text .='By User';
      }
      else if($stage_action == 2)
      {
        $applied_rule_text .='By Heirarchy';
      }
      else if($stage_action == 3)
      {
        $applied_rule_text .='By Group';
        if($stage_group ==1)
        {
          $applied_rule_text .='- Any one';
        }
        else if($stage_group ==2)
        {
          $applied_rule_text .='- All';
        }
        else if($stage_group ==3)
        {
          /*$applied_rule_text .='- Percentage- <span class="badge bg-red">'.$stage_percentage.'</span>';*/
          $applied_rule_text .='- Percentage- '.$stage_percentage.'%';
        }
      }
      else if($stage_action == 4)
      {
        $applied_rule_text .='Auto';
      }
      return $applied_rule_text;
    }

    public static function assigned_departments($data = array())
    {
      $assigned_departments = 'ddd';
      if($data)
      {
        $tbl_departments = DB::table('tbl_departments')->whereIn('department_id',$data)->get(['department_name']);
        $dep=array();
        foreach ($tbl_departments as $deps) 
        {
          $dep[] = $deps->department_name;
        }
        $assigned_departments =implode(', ',$dep);
      }
      
      return $assigned_departments;
    }


     public static function get_assigned_users($data = array())
    {
      $select ="tau.user_id,tu1.user_full_name as assigned_user_name,tau.delegated_user,tu2.user_full_name as delegated_user_name,tau.activity_id";

      $where = array();
      $where['tau.operation_id'] = $data['operation_id'];
      $where['tau.stage_id'] = $data['stage_id'];
      $query = DB::table('tbl_wf_assigned_users as tau');
      $query->Leftjoin('tbl_users as tu1','tau.user_id','=','tu1.id');
      $query->Leftjoin('tbl_users as tu2','tau.delegated_user','=','tu2.id');
      $res = $query->selectRaw($select)->where($where)->groupBy('tau.user_id')->orderBy('tau.id','asc')->get(); 
      return $res;
    }
}
