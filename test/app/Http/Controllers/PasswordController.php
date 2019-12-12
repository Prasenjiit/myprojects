<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ApplicationLogController;
use App\Http\Controllers\AuditsController; 
use App\Http\Requests;
use Auth;
use View;
use Validator;
use App\Users as Users;
use App\DepartmentsModel as DepartmentsModel;
use Input;
use Session;
use App\AuditsModel as AuditModel;
use App\StacksModel as StacksModel;
use App\DocumentTypesModel as DocumentTypesModel;
use DB;
use Hash;

use App\Mylibs\Common;
use Lang;

class PasswordController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
   
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
        
    public function __construct()
    {
        
        $this->docObj = new Common(); // class defined in app/mylibs

    }

    public function reset() {
        if (Auth::user()) {

            $tbl_settings = DB::table('tbl_settings')->first();
            Session::set('settings_password_length_from',$tbl_settings->settings_password_length_from);
            Session::set('settings_password_length_to',$tbl_settings->settings_password_length_to);
            Session::set('auth_user_id',Auth::user()->id); 
            //For identify when it log out.Because audits updated when it calls log out function.
            Session::put('resetLogout','true');
            app('App\Http\Controllers\Auth\AuthController')->logout();// Logout the session
            return View::make('pages/users/reset');
        }
        //return View::make('pages/users/reset');
        else{
            return redirect('')->withErrors("Please login")->withInput();
        }
       
    }

    public function resetSubmit(){
        $id = Input::get('id');
        $getPsw = DB::table('tbl_users')->select('password','username')->where('id',$id)->get();
        $hashedPassword = $getPsw[0]->password;      
        $username       = $getPsw[0]->username;      

        if(Hash::check(Input::get('current_password'), $hashedPassword)){  
            if(Hash::check(Input::get('correct_password'), $hashedPassword)){  
                // Show wanning message
                Session::flash('flash_message_wanning', "New password must be different from the old password.");
                return redirect("reset"); 
            }else{
                // update password 
                $dataToUpdate['password']= bcrypt(Input::get('correct_password'));
                $dataToUpdate['password_date'] = date("Y-m-d");
                // update table
                DB::table('tbl_users')->where('id',$id)->update($dataToUpdate);

                // Save in audits
                $actionMsg = Lang::get('language.password_reset_lang');
                $actionDes = $this->docObj->stringReplace('Reset password',$username,'',$actionMsg);
                (new AuditsController)->log('','Reset password','Reset password',$actionDes);

                Session::flash('flash_message_wanning', "Your password has changed, Please Sign In with the new password.");
                return redirect('/login');                
            }
        }else{
            // Show wanning message
            Session::flash('flash_message_wanning', "Current password does not match.");
            return redirect("reset"); 
        }
    }

    // Get Password security message
    public function getsecuritySettings(){
        // Get security details from settings 
        $data['tbl_settings'] = DB::table('tbl_settings')->first();

        // For Password expiry 
        if($data['tbl_settings']->settings_pasword_expiry != 0):
            if($_GET['id'] != 'null'):
                $tbl_users = DB::table('tbl_users')->select('password_date')->where('id',$_GET['id'])->get();
                $dateDifference = date_diff(date_create($tbl_users[0]->password_date),date_create(date('Y-m-d')))->days; 
                $expiryDate     = ($data['tbl_settings']->settings_pasword_expiry - $dateDifference);
                if($expiryDate > 0){
                    if($expiryDate == '1'):
                        $expire_date_message = '<span class="fa fa-star text-yellow"></span> '.Lang::get('language.expire_date_message1_lang').'';
                    elseif($expiryDate == '2'):
                        $expire_date_message = '<span class="fa fa-star text-yellow"></span> '.Lang::get('language.expire_date_message2_lang').'';
                    else:
                        $msg = Lang::get('language.expire_date_message3_lang');
                        $msg = str_replace('$expiryDate',$expiryDate,$msg);
                        $expire_date_message = '<span class="fa fa-star text-yellow"></span> '.$msg.'';
                    endif;
                }else{
                    $expire_date_message = '<li><span class="fa fa-star text-yellow"></span> '.Lang::get('language.expire_date_message4_lang').'</li>';//Expiry date is over for this user.
                }
            else:
                $expire_date_message = '<li><span class="fa fa-star text-yellow"></span> Password Expiry: <strong>'.$data['tbl_settings']->settings_pasword_expiry.'</strong> days</li>';
            endif;
        endif;// For Password expiry end
        
        if($data['tbl_settings']->settings_numerics == '1'){
           $settings_numerics = '<li><span class="fa fa-star text-yellow"></span> Password must contain at least <strong>one number</strong></li>';
        }
        if($data['tbl_settings']->settings_special_characters == '1'){
           $settings_special_characters = '<li><span class="fa fa-star text-yellow"></span> Password must contain at least <strong>one special characters</strong> (@, %, etc)</li>';
        }
        if($data['tbl_settings']->settings_capital_and_small == '1'){
           $settings_capital_and_small = '<li><span class="fa fa-star text-yellow"></span> Password must contain at least <strong>one capital and small letter</strong></li>';
        } 

        
        // Show warning message
        echo '<ul>
                '.@$expire_date_message.'
                <li><span class="fa fa-star text-yellow"></span> Password must be <strong>between '.$data['tbl_settings']->settings_password_length_from.' - '.$data['tbl_settings']->settings_password_length_to.' characters</strong> long</li>
                '.@$settings_numerics.'
                '.@$settings_special_characters.'
                '.@$settings_capital_and_small.'
             </ul>';
    }
    public function getexpiryMessage(){
        echo '<ul>
                <li><span class="fa fa-star text-yellow"></span>Setting a date here will automatically disallow a user from logging in after that date. This is helpful when you have temp staff as users.</li>
                </ul>';
    }

} /*<--END-->*/
