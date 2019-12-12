<?php

namespace App\Http\Controllers\Auth;

use App\Users;
use Validator;
use App\Http\Controllers\Controller;
//use Illuminate\Foundation\Auth\ThrottlesLogins;
//use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use DB;
use Session;
use Input;
use DateTime;
use App\Http\Controllers\AuditsController;
use Lang;
use Auth;
class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';


    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct(){ 
     
        // can access settings details 
        $global_settings = DB::table('tbl_settings')->first();
        $datas           = array('settings_company_name'=>@$global_settings->settings_company_name,
                                'settings_document_no'  =>@$global_settings->settings_document_no,
                                'settings_document_name'=>@$global_settings->settings_document_name);
        Session::put($datas);
        $this->middleware('guest', ['except' => 'logout']);
        //$this->middleware($this->guestMiddleware(), ['except' => 'logout']);
    }

    // Get settings details
    public function getSettingsAuth(){
        $data = DB::table('tbl_settings')->select('settings_alphabets','settings_numerics','settings_special_characters','settings_capital_and_small','settings_password_length_from','settings_password_length_to')->get();
        echo json_encode($data[0]);exit;
    }

    //Checking user lock status
    public function checkUserLockStatus(){
        $userStatus  = DB::table('tbl_users')->select('user_lock_status','user_login_count_date','user_login_count')->where('username',Input::get('username'))->get();
        if($userStatus):
            date_default_timezone_set('Asia/Calcutta');
            $time1 = new DateTime($userStatus[0]->user_login_count_date);//time saved in db
            $time2 = new DateTime(date('Y-m-d H:i:s'));// get current time
            $interval = $time1->diff($time2);

            // Check if time interval b/w 5
            if($interval->i < 5){
                if($userStatus[0]->user_login_count == 4 || $userStatus[0]->user_login_count > 4){
                    echo "false";// error message
                }else{
                    echo "true";//success
                }
                
            }else{
                echo "true";//success
            } // Ajax response
        else:
            echo "true";
        endif;
    }

    // Distroy session
    public function distroySession(){
        Session::forget('is_limit_exceed');
        Session::forget('captcha_validation_error');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    /*protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }*/

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return Users::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    // Token mismatch error
    public function tokenMismatchAuth(){
        return redirect('login')->with("flash_message_wanning",Lang::get('language.token_error'));
    }

}/*<--END-->*/
