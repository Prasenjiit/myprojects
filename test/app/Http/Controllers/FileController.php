<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Auth;
use View;
use Input;
use Session;
use DB;
use Lang;
use Response;

class FileController extends Controller
{
    public function getFile($foldername='',$filename='')
    {
        $filename = $filename;
        $download = Input::get('download');
        // $filename1 = str_replace(' ', '%20', $tmpfilename);
        // $filename = str_replace('+', '%20', $filename1);
        
        $data = array();
        if(Auth::user() || (!Auth::user() && !$download))
        {
            $fullpath="";
            if($foldername == 'documents')
            {
                $fullpath=config('app.base_path')."{$filename}";
            }
            else if($foldername == 'documents_backup')
            {
                $fullpath=config('app.backup_path')."{$filename}";
            }else if($foldername == 'temp_document')
            {
                $fullpath=config('app.temp_document_path')."{$filename}";
            }
            
            
            if($filename && file_exists($fullpath))
            {
                //return response()->download($fullpath, null, [], null);
                if($download)
                {
                    return Response::download($fullpath);
                }
                else
                {
                    return response()->file($fullpath);
                }
            }
            else
            {
               return response()->view('404_error',$data,404); 
            }
        }
        else
        {
           return response()->view('404_error',$data,404); 
        }
    }
    //download zip file
    public function getZip($filename='')
    {
        $data = array();
        if(Auth::user())
        {
            $headers = array(
            'Content-Type' => 'application/octet-stream',
            );
            $fullpath = config('app.export_path')."{$filename}";
            //check file exist
            if($filename && file_exists($fullpath))
            {
                //download file and delete after download automatically
                //return response()->download($fullpath, $filename,$headers)->deleteFileAfterSend(true);
                return response()->download($fullpath, $filename,$headers);
            }
            else
            {
               return response()->view('404_error',$data,404); 
            }
        }
        else
        {
           return response()->view('404_error',$data,404); 
        }
    }

    public function ExportSearch($filename='')
    {
        $data = array();
        if(Auth::user())
        {
            $headers = array(
                'Content-Type: text/csv; charset=utf-8'
            );
            $fullpath = config('app.export_path')."{$filename}";
            //check file exist
            if($filename && file_exists($fullpath))
            {
                //download file and delete after download automatically
                return response()->download($fullpath, $filename,$headers)->deleteFileAfterSend(true);
                //return response()->download($fullpath, $filename,$headers);
            }
            else
            {
               return response()->view('404_error',$data,404); 
            }
        }
        else
        {
           return response()->view('404_error',$data,404); 
        }
    }
    
    public function DeleteZip(Request $request)
    {
        $data = array();
        if(Auth::user())
        {
            $filename = Input::get('filename');
            $fullpath = config('app.export_path')."{$filename}";
            //check file exist
            if($filename && file_exists($fullpath))
            {
                //delete file
                unlink($fullpath);
                echo "1";
                exit();
            }
            else
            {
                //file not found
                echo "0";
                exit();
            }
        }
    }

     public function logo(Request $request,$filename='')
    {
        $fullpath = base_path('public/images/logo/')."{$filename}";
        if($filename && file_exists($fullpath))
            {
                return response()->file($fullpath);
            }
            else
        {
           return response()->view('404_error',$data,404); 
        }

    }
}