<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;
use Auth;
class Users extends Authenticatable
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_users';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_full_name', 'email', 'password', 'user_role', 'user_status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'user_status'
    ];

    /*
    * A relation to Document group table
    */

    public function documentGroup()
    {
        return $this->belongsTo('App\DepartmentsModel');
    }
   public static function get_report_to_users($id) {
        $users = DB::table('tbl_users')->select('id','user_full_name','department_id','user_role')->where('id','!=',$id)->orderBy('user_full_name','asc')->get();
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
     public static function get_users() {
        $users = DB::table('tbl_users')->select('id','user_full_name','department_id','user_role')->orderBy('user_full_name','asc')->get();
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
    public static function getUser($report_to_user_id) {
      $user = DB::table('tbl_users')->select('id','user_full_name','department_id','user_role')->where('id','=',$report_to_user_id)->first();
      if($user) {
        if($user->user_role == 1)
        {
          $user->user_role = '[SA]';
        }
        elseif($user->user_role == 2)
        {
          $user->user_role = '[DA]';
        }
        elseif($user->user_role == 3)
        {
          $user->user_role = '[RU]';
        }
        elseif($user->user_role == 4)
        {
          $user->user_role = '[PU]';
        }
      }
      return $user;
    }
    //fetch ip without net conn
    public static function getUserIpAddr(){
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            //ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            //ip pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
    //check connection
    public static function is_connected()
    {
        $connected = @fsockopen("www.google.com", 80); 
                                            //website, port  (try 80 or 443)
        if ($connected){
            $is_conn = true; //action when connected
            fclose($connected);
        }else{
            $is_conn = false; //action in connection failure
        }
        return $is_conn;

    }
}
