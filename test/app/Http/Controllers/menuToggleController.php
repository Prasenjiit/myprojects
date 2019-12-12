<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;
use App\Http\Requests;
use Auth;
use Input;
use Cookie;
class menuToggleController extends Controller
{
    public function status()
    {
        if (Auth::user()) {
            $id= Input::get('id');
            $res = Request::cookie('toggleStatus');
            if($res==0){
                $value = 1;
            }else{
            	$value = 0;
            }
            Cookie::queue('toggleStatus', $value, 43200);
            $res = Request::cookie('toggleStatus');
            echo json_encode($res);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    public function showcookies(){
    	echo Cookie::get('toggleStatus');
    }
}
