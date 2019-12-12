<?php

namespace App\Http\Middleware;

use Closure;
//Load model
use App\SettingsModel as SettingsModel;

class TimeZone
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
			// Get time zone from settings table
			$settings = SettingsModel::select('settings_timezone','settings_datetimeformat','settings_dateformat','settings_timeformat')->first();
			if($settings)
			{
				config(['app.timezone'=>$settings->settings_timezone]);
				date_default_timezone_set($settings->settings_timezone);
				config(['app.settings_datetimeformat' => $settings->settings_datetimeformat]);
				config(['app.settings_dateformat' => $settings->settings_dateformat]);
				config(['app.settings_timeformat' => $settings->settings_timeformat]);
			}
		}
        return $next($request);
    }
}
