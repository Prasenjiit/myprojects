<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use User;
use App\Http\Controllers\AuditsController;

class UserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::user()) {
            if (Auth::user()->user_status == 0) {
                $request->session()->forget('key');
                $request->session()->flush();           
                $result = (new AuditsController)->log(Auth::user()->username, Auth::user()->username, 'Sign In Failure', 'Sign In Failure: Inactive account');
                return redirect('login')->withErrors("You are not an active, Please contact admin")->withInput();
            }
            else{
                $expDate= Auth::user()->user_login_expiry;
                if($expDate != '0000-00-00')
                {
                    $d= date('Y-m-d');
                    $date1=date_create($d);
                    $date2=date_create($expDate);
                    $diff=date_diff($date1,$date2);
                    if( $diff->format("%R%a") < 0)
                    {
                        $request->session()->forget('key');
                        $request->session()->flush();     
                        $result = (new AuditsController)->log(Auth::user()->username, Auth::user()->username, 'Sign In Failure', 'Sign In Failure:account has expired');           
                        return redirect('login')->withErrors("Sorry, Your account has expired.")->withInput();
                    }
                }
            }
            if (Auth::user()->login_status == 0) {
                $request->session()->forget('key');
                $request->session()->flush();                                  
                return redirect('login')->withErrors("You have been Signed Out of the system. Please Sign In again.")->withInput();
            }

        }
        return $next($request);
    }
}
