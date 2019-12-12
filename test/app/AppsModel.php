<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Input;

class AppsModel extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_document_types';

    /**
    *Primary key
    */
    protected $primaryKey = 'document_type_id';

    public static function apps_types_old()
    {       
        $select ="titp.form_input_type as id,titp.form_input_type_name as name,titp. 
form_input_type_value as value,titp.is_options,titp. 
form_input_type_common as common";
        $query = DB::table('tbl_form_input_types as titp');
        $query->selectRaw($select);
        $query->orderBy('titp.view_order', 'ASC');
        $result_doc =    $query->get();
        return $result_doc;
    }

    public static function apps_types()
    {       
        $select ="titp.form_input_type,titp.form_input_type_name,titp. 
form_input_type_value as value,titp.is_options,titp. 
form_input_type_common as common";
        $query = DB::table('tbl_form_input_types as titp');
       // $query->selectRaw($select);
        $query->orderBy('titp.view_order', 'ASC');
        $result_doc =    $query->get();
        return $result_doc;
    }
    
    public static function form_inputs($form_id=0,$document_id=0,$reference_id=0)    
    {
      $tbl_documents_columns = "tbl_documents_columns";
	  if($reference_id)
	  {
		  $tbl_documents_columns = "tbl_documents_columns_archive";
	  }
	  $where = array('ti.document_type_id' => $form_id);
      $select ="
      ti.*,
      tt.form_input_type,
      tt.form_input_type_value,
      tt.is_options,
      tt.is_required,
      tt.form_input_type_name,
      tt.form_input_type_common,
      tt.col_1,
      tt.col_2,
      tt.col_3,tdc.document_column_id,tdc.document_column_value,tdc.document_file_name,tdc.document_file_size";
      $query = DB::table('tbl_document_types_columns as ti');
      $query->join('tbl_form_input_types as tt','ti.document_type_column_type','=','tt.form_input_type_value');
      $query->Leftjoin("$tbl_documents_columns as tdc", function($join) use($document_id,$reference_id){
                      $join->on('tdc.document_type_column_id','=','ti.document_type_column_id');
                      $join->where('tdc.document_id','=',$document_id);
					  if($reference_id)
					  {
						  $join->where('tdc.duplicate_ref_id','=',$reference_id);
					  }
                    });
      $query->selectRaw($select);
      $query->where($where)->orderBy('ti.document_type_column_order', 'ASC')->get();
      $result = $query->get();
      return $result;
    }
    public static function app_submit_edit($form_id,$document_id=0)    
    {
      $where = array('ti.document_id'=>$document_id);
      $select ="
      ti.*,
      tt.form_input_type,
      tt.form_input_type_value,
      tt.is_options,
      tt.is_required,
      tt.form_input_type_name,
      tt.form_input_type_common,
      tc.document_type_options,
      tc.document_type_link,
      tc.document_type_link_column";
      $query = DB::table('tbl_documents_columns as ti');
      $query->join('tbl_form_input_types as tt','ti.document_column_type','=','tt.form_input_type_value');
      /*$query->join('tbl_document_types_columns as tc','ti.document_type_column_id','=','tc.document_type_column_id');*/
      $query->Leftjoin('tbl_document_types_columns as tc', function($join){
                      $join->on('ti.document_type_column_id','=','tc.document_type_column_id');
                    });
      $query->selectRaw($select);
      $query->where($where)->orderBy('tc.document_type_column_order', 'ASC');
      $result = $query->get();
      return $result;
    }

    public static function related_docs($link_doc_id=0)    
    {
      $where = array('tl.app_document_id' => $link_doc_id);
      $select ="tm.document_id as id,tm.document_name as doc_name,tm.document_file_name as doc_file_name,td.document_type_name as doc_type,tm.created_at";
      $query = DB::table('tbl_document_links as tl');
      $query->join('tbl_documents as tm','tl.document_id','=','tm.document_id');
      $query->join('tbl_document_types as td','tm.document_type_id','=','td.document_type_id');
      $query->selectRaw($select);
      $query->where($where)->groupBy('tm.document_id')->orderBy('tl.id', 'ASC');
      $result = $query->get();
      return $result;
    }
    
     public static function related_doc_types($app_id=0,$doc_id=0)    
    {
      $where = array('tl.fk_app_id' => $app_id);
      $select ="td.document_type_id,td.document_type_name,tl.fk_app_column_id,tl.fk_document_column_id";
      $query = DB::table('tbl_app_links as tl');
      $query->join('tbl_document_types as td','tl.document_type_id','=','td.document_type_id');
      $query->selectRaw($select);
      $query->where($where)->groupBy('td.document_type_id')->orderBy('td.document_type_name', 'ASC')->get();
      $result = $query->get();

      $docs = array();
      foreach ($result as $key => $value) 
      {
      $where = array('tdc.document_id' => $doc_id,'tdc.document_type_column_id' => $value->fk_app_column_id);  
      $master_data = DB::table('tbl_documents_columns as tdc')->select('tdc.document_column_value')->where($where)->orderBy('tdc.document_column_id', 'DESC')->first();  /*Get Master data for this document type*/
      if($master_data)
      {
        $document_column_id = $value->fk_document_column_id;
        $master_column_value = $master_data->document_column_value;
        $where = array('tm.document_type_id' => $value->document_type_id);
        $select ="tm.document_id as id,tm.document_name as doc_name,tm.document_file_name as doc_file_name,tdt.document_type_name as doc_type,tm.created_at";
        $query1 = DB::table('tbl_documents as tm');
        $query1->join('tbl_document_types as tdt','tdt.document_type_id','=','tm.document_type_id');
        $query1->join('tbl_documents_columns as tdc', function($join)use($document_column_id,$master_column_value){
                      $join->on('tdc.document_id','=','tm.document_id');
                      $join->where('tdc.document_type_column_id','=',$document_column_id);
                      $join->where('tdc.document_column_value','=',$master_column_value);
                    });
        $query1->join('tbl_document_types_columns as tdtc', function($join){
                      $join->on('tdtc.document_type_column_id','=','tdc.document_type_column_id');
                    });
        $query1->selectRaw($select);
        /*$query1->where('tc.document_type_id',$id);
        $query1->where('tm.document_type_id',$id);*/

        $data1 = $query1->groupBy('tm.document_id')->get();
          foreach ($data1 as $key1 => $value1)
          {
            /*$value1->created_at = dtFormat($value1->created_at);*/
            $docs[]= $value1;
          }
      }
      }
      $object = (object)$docs;
      return $object;
    }

    public static function related_doc_types1($app_id=0)    
    {
      $where = array('tl.fk_app_id' => $app_id);
      $select ="tm.document_id as id,tm.document_name as doc_name,tm.document_file_name as doc_file_name,tdt.document_type_name as doc_type";
      $query1 = DB::table('tbl_documents as tm');
      $query1->join('tbl_document_types as tdt','tdt.document_type_id','=','tm.document_type_id');
      $query1->join('tbl_documents_columns as tdc', function($join){
                    $join->on('tdc.document_id','=','tm.document_id');
                  });
      $query1->join('tbl_document_types_columns as tdtc', function($join){
                    $join->on('tdtc.document_type_column_id','=','tdc.document_type_column_id');
                  });
      $query1->selectRaw($select);
      /*$query1->where('tc.document_type_id',$id);
      $query1->where('tm.document_type_id',$id);*/
      $query1->join('tbl_app_links as tl','tl.document_type_id','=','tdt.document_type_id');

    $data1 = $query1->groupBy('tm.document_id')->get();
    return $data1;  
    }
}
