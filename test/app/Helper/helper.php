<?php

/* @ Helper 
created by bibin on 26/4/2019
*/


if (! function_exists('dtFormat')) {
    function dtFormat($date,$format='')
    {
        $format = ($format)?$format:Config::get('app.settings_datetimeformat');
        if($date) {
            $date = str_replace("/", "-", $date);
            return date($format, strtotime($date));
        }
        else
        {
            return '';
        }
    }
}

if (! function_exists('custom_date_Format')) {
    function custom_date_Format($date,$format='')
    {
        $format = ($format)?$format:Config::get('app.settings_dateformat');
        if($date) {
            $date = str_replace("/", "-", $date);
            return date($format, strtotime($date));
        }
         else
        {
            return '';
        }
    }
}

if (! function_exists('date_to_DBformat')) {
    function date_to_DBformat($date)
    {
        $format = "Y-m-d";
        if($date) 
        {
            $date = str_replace("/", "-", $date);
            return date($format, strtotime($date));
        }
         else
        {
            return '';
        }
    }
}


if (! function_exists('js_date_format')) 
{
    function js_date_format()
    {
        $format = Config::get('app.settings_dateformat');
        
        if($format == 'm-d-Y')
        {
           $js_format = "MM-DD-YYYY";     
        }
        else if($format == 'Y-m-d')
        {
           $js_format = "YYYY-MM-DD";     
        }
        else
        {
            $js_format = "DD-MM-YYYY";
        }

        return $js_format;
    }
}

if (! function_exists('placeholder_date_format')) 
{
    function placeholder_date_format($format='')
    {
        $format = ($format)?$format:Config::get('app.settings_dateformat');
        
        if($format == 'm-d-Y')
        {
           $js_format = "MM-DD-YYYY";     
        }
        else if($format == 'Y-m-d')
        {
           $js_format = "YYYY-MM-DD";     
        }
        else
        {
            $js_format = "DD-MM-YYYY";
        }

        return $js_format;
    }
}

if (! function_exists('dec_enc')) {
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

if ( ! function_exists('isActive'))
{
    function isActive($route, $className = 'active')
    {
        if (is_array($route)) {
            return in_array(Route::currentRouteName(), $route) ? $className : '';
        }
        if (Route::currentRouteName() == $route) {
            return $className;
        }
        if (strpos(URL::current(), $route)) return $className;
    }
}

if (! function_exists('check_valid_file_name')) 
{
    function check_valid_file_name($file_name='')
    {
        $valid = 1;
        if (preg_match('/[\'^£$%&*()}{@#~?><>,|=+¬"]/', $file_name))
        {
                $valid = 0;
        }    
        return $valid;
    }
}

?>