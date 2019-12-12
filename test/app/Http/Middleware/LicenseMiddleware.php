<?php

namespace App\Http\Middleware;
use Closure;
use DB;
use Session;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class LicenseMiddleware
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
        if(env('APP_INSTALL')=='installed'){
				$moduleStatusAccountview = DB::table('tbl_modules')->select('module_id','module_name','module_activation_key','module_activation_count','module_activation_date','module_expiry_date')->orderBy('module_id','ASC')->get();
				$incr_av = 1;
				 foreach ($moduleStatusAccountview as $value) {
					// Get data
					$module_id              	 = $value->module_id;
					$module_name_av              = $value->module_name;
					$module_activation_key_av    = $value->module_activation_key;
					$module_activation_count_av  = $value->module_activation_count;
					$module_activation_date_av   = $value->module_activation_date;
					$module_expiry_date_av       = $value->module_expiry_date;

					$status_av = $this->dec_enc('decrypt',$module_activation_key_av);
		            $count_av = $this->dec_enc('decrypt',$module_activation_count_av);
		            $activdate_av = $this->dec_enc('decrypt',$module_activation_date_av);
		            $expdate_av = $this->dec_enc('decrypt',$module_expiry_date_av);
		            $tval_av = $this->dec_enc('encrypt',"true");    
		            $fval_av = $this->dec_enc('encrypt',"false");
		            $expmod_av = $this->dec_enc('encrypt',"expired");

		            $statusarr = explode(',',$status_av);
		            if($module_id==1){
			            if(($statusarr[0]==1)&&($statusarr[1]=="Export/Import")){
			            	$activated_status_av = 1; 
			            }else{
			            	$activated_status_av = 0; 
			            }
			        }
			        if($module_id==2){
			            if(($statusarr[0]==1)&&($statusarr[1]=="Document Annotation")){
			            	$activated_status_av = 1; 
			            }else{
			            	$activated_status_av = 0; 
			            }
			        }
			        if($module_id==3){
			            if(($statusarr[0]==1)&&($statusarr[1]=="OCR")){
			            	$activated_status_av = 1; 
			            }else{
			            	$activated_status_av = 0; 
			            }
			        }
			        if($module_id==4){
			            if(($statusarr[0]==1)&&($statusarr[1]=="Forms & Work Flow")){
			            	$activated_status_av = 1; 
			            }else{
			            	$activated_status_av = 0; 
			            }
			        }
			        if($module_id==5){
			            if(($statusarr[0]==1)&&($statusarr[1]=="Audit Trail")){
			            	$activated_status_av = 1; 
			            }else{
			            	$activated_status_av = 0; 
			            }
			        }
			        if($module_id==6){
			            if(($statusarr[0]==1)&&($statusarr[1]=="Encrypt/Decrypt")){
			            	$activated_status_av = 1; 
			            }else{
			            	$activated_status_av = 0; 
			            }
			        }
			        if($module_id==7){
			            if(($statusarr[0]==1)&&($statusarr[1]=="Apps")){
			            	$activated_status_av = 0; 
			            }else{
			            	$activated_status_av = 0; 
			            }
			        }
			        if($module_id==8){
			            if(($statusarr[0]==1)&&($statusarr[1]=="Email Documents")){
			            	$activated_status_av = 1; 
			            }else{
			            	$activated_status_av = 0; 
			            }
			        }
			        if($module_id==9){
			            if(($statusarr[0]==1)&&($statusarr[1]=="Website Publishing")){
			            	$activated_status_av = 1; 
			            }else{
			            	$activated_status_av = 0; 
			            }
			        }
			        if($module_id==10){
			            if(($statusarr[0]==1)&&($statusarr[1]=="Task Manager")){
			            	$activated_status_av = 1; 
			            }else{
			            	$activated_status_av = 0; 
			            }
			        }
			        if($module_id==11){
			            if(($statusarr[0]==1)&&($statusarr[1]=="Scanner")){
			            	$activated_status_av = 1; 
			            }else{
			            	$activated_status_av = 0; 
			            }
			        }

					Session::put('module_name_av'.$incr_av,$module_name_av); 
					Session::put('module_activation_key_av'.$incr_av,$activated_status_av); 
					Session::put('module_activation_count_av'.$incr_av,$count_av); 
					Session::put('module_activation_date_av'.$incr_av,$activdate_av); 
					Session::put('module_expiry_date_av'.$incr_av,$expdate_av); 
					Session::put('totalcnt_av',$incr_av); 

					Session::put('tval_av',$tval_av); 
					Session::put('fval_av',$fval_av); 
					Session::put('expmod_av',$expmod_av); 


					if (Session::get('module_activation_key_av'.$incr_av)==1){
						if(Session::get('module_expiry_date_av'.$incr_av)!="No Expiry"){
							if(date("Y-m-d") > Session::get('module_expiry_date'.$incr_av)) {
								Session::put('enbval_av'.$incr_av,Session::get('expmod_av'));
							}else{
								Session::put('enbval_av'.$incr_av,Session::get('tval_av'));
							}
						}else{

							Session::put('enbval_av'.$incr_av,Session::get('tval_av'));
						}
					}else{
						Session::put('enbval_av'.$incr_av,Session::get('fval_av'));
					}
					$incr_av++;
				}


				$moduleStatus = DB::table('tbl_modules')->select('module_id','module_name','module_activation_key','module_activation_count','module_activation_date','module_expiry_date')->orderBy('module_id','ASC')->get();
				$incr = 1;
				foreach ($moduleStatus as $value) {
					// Get data
					$module_id                = $value->module_id;
					$module_name              = $value->module_name;
					$module_activation_key    = $value->module_activation_key;
					$module_activation_count  = $value->module_activation_count;
					$module_activation_date   = $value->module_activation_date;
					$module_expiry_date       = $value->module_expiry_date;

					$status = $this->dec_enc('decrypt',$module_activation_key);
		            $count = $this->dec_enc('decrypt',$module_activation_count);
		            $activdate = $this->dec_enc('decrypt',$module_activation_date);
		            $expdate = $this->dec_enc('decrypt',$module_expiry_date);
		            $tval = $this->dec_enc('encrypt',"true");    
		            $fval = $this->dec_enc('encrypt',"false");
		            $expmod = $this->dec_enc('encrypt',"expired");

		            $statusarr = explode(',',$status);
		            if($module_id==1){
			            if(($statusarr[0]==1)&&($statusarr[1]=="Export/Import")){
			            	$activated_status = 1; 
			            }else{
			            	$activated_status = 0; 
			            }
			        }
			        if($module_id==2){
			            if(($statusarr[0]==1)&&($statusarr[1]=="Document Annotation")){
			            	$activated_status = 1; 
			            }else{
			            	$activated_status = 0; 
			            }
			        }
			        if($module_id==3){
			            if(($statusarr[0]==1)&&($statusarr[1]=="OCR")){
			            	$activated_status = 1; 
			            }else{
			            	$activated_status = 0; 
			            }
			        }
			        if($module_id==4){
			            if(($statusarr[0]==1)&&($statusarr[1]=="Forms & Work Flow")){
			            	$activated_status = 1; 
			            }else{
			            	$activated_status = 0; 
			            }
			        }
			        if($module_id==5){
			            if(($statusarr[0]==1)&&($statusarr[1]=="Audit Trail")){
			            	$activated_status = 1; 
			            }else{
			            	$activated_status = 0; 
			            }
			        }
			        if($module_id==6){
			            if(($statusarr[0]==1)&&($statusarr[1]=="Encrypt/Decrypt")){
			            	$activated_status = 1; 
			            }else{
			            	$activated_status = 0; 
			            }
			        }
			        if($module_id==7){
			            if(($statusarr[0]==1)&&($statusarr[1]=="Apps")){
			            	$activated_status = 0; 
			            }else{
			            	$activated_status = 0; 
			            }
			        }
			        if($module_id==8){
			            if(($statusarr[0]==1)&&($statusarr[1]=="Email Documents")){
			            	$activated_status = 1; 
			            }else{
			            	$activated_status = 0; 
			            }
			        }
			        if($module_id==9){
			            if(($statusarr[0]==1)&&($statusarr[1]=="Website Publishing")){
			            	$activated_status = 1; 
			            }else{
			            	$activated_status = 0; 
			            }
			        }
			        if($module_id==10){
			            if(($statusarr[0]==1)&&($statusarr[1]=="Task Manager")){
			            	$activated_status = 1; 
			            }else{
			            	$activated_status = 0; 
			            }
			        }
			        if($module_id==11){
			            if(($statusarr[0]==1)&&($statusarr[1]=="Scanner")){
			            	$activated_status = 1; 
			            }else{
			            	$activated_status = 0; 
			            }
			        }			        

					Session::put('module_name'.$incr,$module_name); 
					Session::put('module_activation_key'.$incr,$activated_status); 
					Session::put('module_activation_count'.$incr,$count); 
					Session::put('module_activation_date'.$incr,$activdate); 
					Session::put('module_expiry_date'.$incr,$expdate); 
					Session::put('totalcnt',$incr); 

					Session::put('tval',$tval); 
					Session::put('fval',$fval); 
					Session::put('expmod',$expmod); 


					if (Session::get('module_activation_key'.$incr)==1){
						if(Session::get('module_expiry_date'.$incr)!="No Expiry"){
							if(date("Y-m-d") > Session::get('module_expiry_date'.$incr)) {
								Session::put('enbval'.$incr,Session::get('expmod'));
							}else{
								Session::put('enbval'.$incr,Session::get('tval'));
							}
						}else{
							Session::put('enbval'.$incr,Session::get('tval'));
						}
					}else{
						Session::put('enbval'.$incr,Session::get('fval'));
					}


					$incr++;
				}
		}
        return $next($request);
    }  

    function dec_enc($action, $string) {
        $output = false;
     
        $encrypt_method = "AES-256-CBC";
        $secret_key = '12dasdq3g5b2434b';
        $secret_iv = '35dasqq3t5b9431q';
     
        // hash
        $key = hash('sha256', $secret_key);
        
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
     
        if( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        }
        else if( $action == 'decrypt' ){
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
     
        return $output;
    }

}
