<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
use Input;
class WorkflowModel extends Model
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
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public static function get_workflows()
    {       
        $select ="wf.id as workflow_id,wf.workflow_name,wf.workflow_color";
        $result = DB::table('tbl_wf as wf')->selectRaw($select)->groupBy('wf.id')->orderBy('wf.workflow_name', 'ASC')->get();
        return  $result;
    }
public static function get_workflow($workflow_id=0)
    {       
        
        $where = array('wf.workflow_id' => $workflow_id);
        $select ="wf.workflow_id,wf.workflow_name,wf.workflow_color";
        $query = DB::table('tbl_workflows as wf');
        
        if($where)
        {
            $query->where($where);
        }       
        $query->selectRaw($select);
        $query->orderBy('wf.workflow_stage_id', 'ASC');
        
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
            
            $row_data['docs'] = WorkflowModel::doc_activity($row_data['workflow_stage_id']);
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
               $obj_datas  =   WorkflowModel::obj_name_with_data($docs_row['obj_id'],$docs_row['obj_type']);

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
                    $assigned_by_user = WorkflowModel::get_user_data($activity_row['assigned_by']);
                    $activity_row['assigned_by_user_name']  = ($assigned_by_user)?$assigned_by_user->user_full_name:'';     
                   }

                   

                   $activity_row['assigned_to']  =   $act_value->document_workflow_responsible_user;

                   $activity_row['assigned_to_user_name']  =   '';

                   if($activity_row['assigned_to'])
                   {
                    $assigned_to_user = WorkflowModel::get_user_data($activity_row['assigned_to']);
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
            $select ="tdw.*,ta.*,tw.*,tdw.created_at as created_date";
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
            $where = array('tdw.document_workflow_responsible_user' => $username,'tdw.document_workflow_notifcation_to_status' => 1,'tw.workflow_id' => $workflow_id);
                $query = DB::table('tbl_document_workflows as tdw');
                $query->join('tbl_workflows as tw','tdw.workflow_stage_id','=','tw.workflow_stage_id');
                $query->where($where)->update($update);

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
             $query->Join('tbl_workflows as tw', function($join)
                         {
                             $join->on('tdw.workflow_stage_id', '=', 'tw.workflow_stage_id');
                         });
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
            
            $select ="tfr.form_response_unique_id as id,tfr.form_name as name,tfr.created_at as date,tu.user_full_name";
            $query = DB::table('tbl_form_responses as tfr');
            $query->Join('tbl_document_workflows as tdw', function($join)
                         {
                             $join->on('tdw.document_workflow_object_id', '=', 'tfr.form_response_unique_id');
                             $join->on('tdw.document_workflow_object_type','=',DB::raw("'form'"));
                         });
             $query->Join('tbl_workflows as tw', function($join)
                         {
                             $join->on('tdw.workflow_stage_id', '=', 'tw.workflow_stage_id');
                         });
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
        
        
        $select ="wf.workflow_id,wf.workflow_name,wf.workflow_stage_name,wf.workflow_stage_id";
        $query = DB::table('tbl_workflows as wf');      
        $query->selectRaw($select);
        $query->orderBy('wf.workflow_stage_name', 'ASC');
        
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
        $where = array('wf.workflow_id' => $workflow_id);
        $query = DB::table('tbl_workflows as wf');
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
}
