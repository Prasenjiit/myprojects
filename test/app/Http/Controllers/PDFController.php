<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Users as Users;
use App\DepartmentsModel as DepartmentsModel;
use App\DocumentTypesModel as DocumentTypesModel;
use App\DocumentsModel as DocumentsModel;
use App\StacksModel as StacksModel;
use App\TagWordsModel as TagWordsModel;
use App\AuditsModel as AuditsModel;
use Session;

class PDFController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
        return view('pages/pdfedit/index');
    }

    public function toggle(){
        Session::put('toggled',true);
    }
}
