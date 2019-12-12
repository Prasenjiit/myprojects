<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ApplicationLogController;
use App\Http\Requests;
use Auth;
use View;
use Validator;
use Input;
use Session;

use DB;
use App\FaqModel as FaqModel;
use App\StacksModel as StacksModel;
use App\DepartmentsModel as DepartmentsModel;
use App\DocumentTypesModel as DocumentTypesModel;

use App\Mylibs\Common;
use Lang;

class FaqController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        Session::put('menuid', '4');
        $this->middleware(['auth', 'user.status']);
        // Set common variable
        $this->actionName = 'Faq';
        $this->docObj     = new Common(); // class defined in app/mylibs
    }
    
    public function index() { 
        if (Auth::user()) {
            /*<--Common-->*/
            $data['docType'] = DocumentTypesModel::all();
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();

            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;
            /*<--// Common-->*/
            // Get FAQ content
            $data['faq'] = DB::table('tbl_faq')->orderBy('faq_id','DESC')->get();
            return View::make('pages/faq/index')->with($data);
        } else {
            return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }

    // Save content
    public function faqSave(){
        // Preparing data
        $data   = new FaqModel;   
        // checking wether data already exists or not
        $faq = Input::get('faq_id');
        // Update
        if($faq):
            // update query
            $dataToUpdate = array('faq_title'       =>Input::get('faq_title'),
                                   'faq_description'=>Input::get('faq_description'),
                                   'faq_updated_by' =>Auth::user()->id,
                                   'updated_at'     => date('Y-m-d h:i:s'));
            FaqModel::where('faq_id', $faq)->update($dataToUpdate);
            // Updationg information in audits controller
            $name = Input::get('faq_title');
            $user = Auth::user()->username;

            // Get update action message
            $actionMsg = Lang::get('language.update_action_msg');
            $actionDes = $this->docObj->stringReplace($this->actionName,$name,$user,$actionMsg);
            $result = (new AuditsController)->log(Auth::user()->username, $this->actionName,Lang::get('language.update'),$actionDes);
            // redirect
            return redirect('/faqs')->with('status', Lang::get('language.updated_successfully'));
        else:
            // Insert  
            $data->faq_title       = Input::get('faq_title');
            $data->faq_description = Input::get('faq_description');
            $data->faq_added_by    = Auth::user()->id;
            $data->created_at      = date('Y-m-d h:i:s');
            // Save data
            $data->save();
            // Save in audits
            $name = Input::get('faq_title');
            $user = Auth::user()->username;

            // Get save action message
            $actionMsg = Lang::get('language.save_action_msg');
            $actionDes = $this->docObj->stringReplace($this->actionName,$name,$user,$actionMsg);
            $result = (new AuditsController)->log(Auth::user()->username, $this->actionName,Lang::get('language.insert'),$actionDes);
            // redirect
            return redirect('/faqs')->with('status', Lang::get('language.saved_successfully'));
        endif;
    }

    // Edit Faq
    public function faqEdit(){
        /*<--Common-->*/
        $data['docType'] = DocumentTypesModel::all();
                
                $data['stckApp'] = $this->docObj->common_stack();
                $data['deptApp'] = $this->docObj->common_dept();
                $data['doctypeApp'] = $this->docObj->common_type();
                $data['records'] = $this->docObj->common_records();
        $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
        $data['settings_document_no']   = $settings[0]->settings_document_no;
        $data['settings_document_name'] = $settings[0]->settings_document_name;
        /*<--// Common-->*/
        // Get Faq
        $data['faq'] = FaqModel::where('faq_id',Input::get('faqId'))->select('faq_id','faq_title','faq_description')->get();
        return view('pages/faq/edit')->with($data);
    }

    // Delete
    public function deleteFaq(){
        FaqModel::where('faq_id',Input::get('faqId'))->delete();
        // Update in audits
        // Save in audits
        $name = Input::get('title');
        $user = Auth::user()->username;

        // Get delete action message
        $actionMsg = Lang::get('language.delete_action_msg');
        $actionDes = $this->docObj->stringReplace($this->actionName,$name,$user,$actionMsg);
        $result = (new AuditsController)->log(Auth::user()->username, $this->actionName, Lang::get('language.deleted'),$actionDes);
        echo Lang::get('language.deleted_successfully');exit;// Ajax response
    }

   
}/*<--END-->*/
