<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class SettingsModel extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_settings';

    /**
    *Primary key
    */
    protected $primaryKey = 'settings_id';

    // get settings details
    public static function getSettingsDetails(){
        return DB::table('tbl_settings')->select('settings_id','settings_document_no','settings_document_name')->get();
    }

    public static function extract_date_time($string=''){
        $date ='d-m-Y'; $time ='h:i A';
        if($string == 'Y-m-d h:i A' || $string == 'Y-m-d')
        {
           $date ='Y-m-d'; $time ='h:i A';     
        }
        else if($string == 'm-d-Y h:i A' || $string == 'm-d-Y')
        {
           
           $date ='m-d-Y'; $time ='h:i A';     
        }

         return array('date' => $date,'time' => $time);   
    }

}/*<--END-->*/