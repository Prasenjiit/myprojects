<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class DocumentsModel extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_documents';

    /**
    *Primary key
    */
    protected $primaryKey = 'document_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'document_file_no', 'document_file_name'
    ];

    public static function getStackColumns($stckid) {
        $stack_columns = DB::table('tbl_stack_columns')->where('stack_id',$stckid)->get();
        return $stack_columns;
    }
    public static function insertStackColumn($stackColumn) {
        return DB::table('tbl_documents_stack_columns')->insert($stackColumn);
    }
    
    public static function check_file_owners($table,$id,$document_file_name)
    {

        $multiple = 0;
        if($table =='tbl_documents')
        {
            $check_duplicate = DB::table('tbl_documents')->where('document_id','<>',$id)->where('document_file_name',$document_file_name)->first();
            if($check_duplicate) 
            {
                $multiple = 1;
            }
           //  else
           //  {   
           //      $check_duplicate = DB::table('tbl_temp_documents')->where('document_file_name',$document_file_name)->first();
           //      if($check_duplicate) 
           //      {
           //          $multiple = 2;
           //      }
           // }
        }
        else if($table =='tbl_temp_documents')
        {
            $check_duplicate = DB::table('tbl_temp_documents')->where('document_id','<>',$id)->where('document_file_name',$document_file_name)->first();
            if($check_duplicate) 
            {
                $multiple = 3;
            }
           //  else
           //  {   
           //      $check_duplicate = DB::table('tbl_documents')->where('document_file_name',$document_file_name)->first();
           //      if($check_duplicate) 
           //      {
           //          $multiple = 4;
           //      }
           // }
        }
           return  $multiple;
    }

    public static function check_file_data_exists($document_file_name)
    {

        $multiple = 0;
        $check_duplicate = DB::table('tbl_documents')->where('document_file_name',$document_file_name)->first();
        if($check_duplicate) 
        {
            $multiple = 1;
        }
        return  array('multiple' => $multiple,'dup_data' => $check_duplicate);
    }
    
}
