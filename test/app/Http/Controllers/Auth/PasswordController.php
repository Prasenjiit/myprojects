<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Session;
use DB;

class PasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        // saving global settings detils
        $settingsDetails = DB::table('tbl_settings')->first();
        Session::put(array('settings_company_name'=>@$settingsDetails->settings_company_name,'settings_logo'=>@$settingsDetails->settings_logo,'settings_email'=>@$settingsDetails->settings_email,'settings_password_length_from'=>@$settingsDetails->settings_password_length_from,'settings_password_length_to'=>@$settingsDetails->settings_password_length_to));
        // For mail 
        config(['mail.from' => ['address' => @$settingsDetails->settings_email, 'name' => @$settingsDetails->settings_company_name] ]);
    }
}
