<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use DB;

use App\WorkflowsModel as WorkflowsModel;

class Croncontroller extends Controller
{
    public function crontest()
    {
    	//DB::table('tbl_faq')->insert(['faq_title'=>'crontest','faq_description'=>'dron description','faq_added_by'=>'5','faq_updated_by'=>'4']);

      $state = DB::table('tbl_wf_states as twf')
      ->join('tbl_wf_operation_details as twod','twf.id','=','twod.wf_stage')
      ->select('twod.completed','twod.updated_at as modify_date','twod.wf_operation_id','twf.escallation_day','twf.escallation_stage','twf.escallation_activity_id','twf.workflow_id','twf.id as from_stage','twod.id as wf_operation_details_id')
      ->where('twf.escallation_day','>',0)
      ->where('twf.escallation_stage','!=',0)
      ->where('twod.completed','=',1)->get();
      
      $today = date('Y-m-d');

      foreach ($state as $value) 
      {
      	$modify_date = date('Y-m-d',strtotime($value->modify_date));
      	$day_diff = date_diff(date_create($modify_date),date_create($today))->days;
      	$value->day_diff = $day_diff;
      	$value->change = 0;
        if($day_diff >= $value->escallation_day)//(modified date - today) >= escallation_days
        {
          $value->change = 1;
          //stage changes
        	$workflow_id = (isset($value->workflow_id))?$value->workflow_id:0;
        	$wf_operation_id = (isset($value->wf_operation_id))?$value->wf_operation_id:0;
        	$escallation_stage = (isset($value->escallation_stage))?$value->escallation_stage:0;
          $from_stage = (isset($value->from_stage))?$value->from_stage:0;
        	$escallation_activity_id = (isset($value->escallation_activity_id))?$value->escallation_activity_id:0;
          //insert in to table cron
          DB::table('tbl_cron')->insert(['workflow_id'=>$workflow_id,
            'wf_operation_id'=>$wf_operation_id,
            'escallation_stage'=>$escallation_stage,
            'from_stage'=>$from_stage,
            'escallation_activity_id'=>$escallation_activity_id,
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s")]);

        //Update Completed Stage to operation table
        $update=array();
        $update['completed_stage'] = $from_stage;
        $update['completed_activity'] = $escallation_activity_id;
        $update['updated_at'] = date("Y-m-d H:i:s");
        $where = array('id' => $wf_operation_id);
        DB::table('tbl_wf_operation')->where($where)->update($update);

        $update=array();
        $update['completed'] = 2;
        $update['activity_id'] = $escallation_activity_id;
        $update['updated_at'] = date("Y-m-d H:i:s");

        $where = array('wf_operation_id' => $wf_operation_id,'wf_stage' => $from_stage);
        DB::table('tbl_wf_operation_details')->where($where)->update($update);

          WorkflowsModel::updateCompleteToStage($workflow_id,$wf_operation_id,$escallation_stage,$escallation_activity_id);
          //activity insert to activity table
          WorkflowsModel::addActivity($workflow_id,$from_stage,$wf_operation_id,$escallation_activity_id,'By Escallation');
          
        }
      }

      
    }
    //delete duplicate entries from tbl_documents_columns
    public function dup_delete()
    {
      $sub = DB::table('tbl_documents_columns')->orderBy('document_column_id','DESC');
      $data = DB::table(DB::raw("({$sub->toSql()}) as sub"))->select('*', DB::raw('COUNT(document_id) as document_id_count'))->groupBy('document_id','document_type_column_id')->orderBy('document_column_id','DESC')->having('document_id_count', '>' , 1) ->get();
        //$data = DB::table('tbl_documents_columns')->select('*', DB::raw('COUNT(document_id) as document_id_count'))->groupBy('document_id','document_type_column_id')->orderBy('document_column_id','DESC')->get();

        foreach ($data as $key => $value) {
           DB::table('tbl_documents_columns')->where('document_id',$value->document_id)->where('document_type_column_id',$value->document_type_column_id)->where('document_column_id','!=',$value->document_column_id)->delete();
        }
        echo '<pre>';
        print_r($data);
    }
}
