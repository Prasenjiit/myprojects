<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{

    /**
     * Display the installer welcome page.
     *
     * @return \Illuminate\Http\Response
     */
    public function welcome()
    {
        if(env('APP_INSTALL')=='notinstalled'){
        	return view('installer.welcome');	
        } else{
            return redirect('login');
        }    
    }
}
