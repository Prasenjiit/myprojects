<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use Session;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class PathMiddleware
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
				$urlParts = explode('.', $_SERVER['HTTP_HOST']);
				$subdomain=array_shift($urlParts);

				$data = DB::table('tbl_settings')->select('settings_user_folder','settings_datetimeformat','settings_dateformat','settings_timeformat','settings_logo','settings_company_name','settings_address')->first();

			   if($data)
			{
				 $user_folder = $data->settings_user_folder.'/';
				config(['app.doc_url' => url('file/documents/').'/']);
				config(['app.doc_backup_url' => url('file/documents_backup/').'/']);
				config(['app.temp_document_url' => url('file/temp_document/').'/']);
				config(['app.base_path' => base_path('storage/app/'.$user_folder.'documents/')]);
				config(['app.backup_path' => base_path('storage/app/'.$user_folder.'documents/documents_backup/')]);
				config(['app.zip_backup_path' => base_path('storage/app/'.$user_folder.'backups/')]);
				config(['app.license_folder' => base_path('storage/app/'.$user_folder.'license/')]);
				config(['app.public_path' => public_path('storage/app/'.$user_folder.'documents/')]);
				config(['app.import_path' => base_path('storage/app/'.$user_folder.'import/')]);
				config(['app.export_path' => base_path('storage/app/'.$user_folder.'export/')]);
				config(['app.checkout_path' => base_path('storage/app/'.$user_folder.'checkout/')]);
				config(['app.annotation_path' => base_path('storage/app/'.$user_folder.'annotations/')]);
				config(['app.temp_document_path' => base_path('storage/app/'.$user_folder.'documents/temp/')]);
				config(['app.user_folder_name' => $user_folder]);
				config(['app.settings_datetimeformat' => $data->settings_datetimeformat]);
				config(['app.settings_dateformat' => $data->settings_dateformat]);
				config(['app.settings_timeformat' => $data->settings_timeformat]);

				config(['app.settings_logo' => $data->settings_logo]);
				config(['app.settings_company_name' => $data->settings_company_name]);
				config(['app.settings_address' => $data->settings_address]);

				Session::put('base_path',config('app.base_path')); 
        		Session::put('temp_document_path',config('app.temp_document_path'));
			}
			return $next($request);
		}else{
			return redirect()->guest('install');
		}
        
    }  
}
