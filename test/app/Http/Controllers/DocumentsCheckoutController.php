<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request as CookieRequest;
use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;
use View;
use Validator;
use Input;
use URL;
use Response;
use Session;
use File;
use Cookie;
use App\Mylibs\Common;
use App\TagWordsModel as TagWordsModel;
use App\DocumentTypeColumnModel as DocumentTypeColumnModel;
use App\TempDocumentsModel as TempDocumentsModel;
use App\TagWordsCategoryModel as TagWordsCategoryModel;
use App\DocumentsCheckoutModel as DocumentsCheckoutModel;
use App\DocumentTypesModel as DocumentTypesModel;
use App\StacksModel as StacksModel;
use App\DocumentsModel as DocumentsModel;
use App\DocumentsColumnModel as DocumentsColumnModel;
use App\DocumentsColumnCheckoutModel as DocumentsColumnCheckoutModel;
use App\DocumentHistoryModel as DocumentHistoryModel;
use App\DocumentHistoryColumnModel as DocumentHistoryColumnModel;
use App\DepartmentsModel as DepartmentsModel;
use App\DocumentNoteModel as DocumentNoteModel;
use App\TreeDataModel as TreeDataModel;
use App\TreeStructModel as TreeStructModel;
use DB;
use Lang;

class DocumentsCheckoutController extends Controller
{
    public function __construct()
    {
        Session::put('menuid', '1');
        $this->middleware(['auth', 'user.status']);

        // Define common variable
        $this->actionName = 'Document';// Action name for insert in audits 
        $this->docObj     = new Common(); // class defined in app/mylibs

    }
    
    public function editDocument(){
        $this->docObj->document_assign_notification();
        $this->docObj->document_reject_notification();
        $this->docObj->document_accept_notification();
        Session::forget('ancestor');
        $id=Input::get('id');
        $view=Input::get('view');
        $documentStatus=DocumentsModel::select('document_status','document_name')->where('document_id',$id)->get();
        foreach ($documentStatus as $key => $value) {
            $doc_status=$value->document_status;
            $doc_name=$value->document_name;
        }
        $data['stacks'] = StacksModel::all();
        $data['tagsCateg'] = TagWordsCategoryModel::all();
        $data['docType'] = DocumentTypesModel::where('is_app',0)->orderBy('document_type_order', 'ASC')->get();
        //$data['dglist'] = DocumentsCheckoutModel::where('document_id',$id)->get();
        //$tag = DocumentsCheckoutModel::select('document_tagwords')->where('document_id',$id)->first();
        $data['dglist'] = DocumentsModel::where('document_id',$id)->get();
        $this->ancestor($data['dglist'][0]->parent_id);
        $tag            = DocumentsModel::select('document_tagwords')->where('document_id',$id)->first();
        $tagstr = $tag->document_tagwords;
        $tagarray = explode(',' , $tagstr);
        //print_r($tagarray);
        $data['taglist'] = TagWordsModel::select('tagwords_category_id')->whereIn('tagWords_id', $tagarray)->get();
        $data['view']=$view;
        $data['raw_count']=1;
        $data['id']=$id;
        
        $data['stckApp'] = $this->docObj->common_stack();
        $data['deptApp'] = $this->docObj->common_dept();
        $data['doctypeApp'] = $this->docObj->common_type();
        $data['records'] = $this->docObj->common_records();
        $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
        $data['settings_document_no']   = $settings[0]->settings_document_no;
        $data['settings_document_name'] = $settings[0]->settings_document_name;
        // For audits
        $documentsData = DB::table('tbl_documents')->select('document_no','document_name','document_path')->where('document_id',Input::get('id'))->get();

        $data['noteList']= DocumentNoteModel::where('document_id', '=', $id )
            ->orderBy('created_at', 'desc')->get();
        // list users
        switch(Auth::user()->user_role)
        {
        //superadmin -> all users are list
        case Session::get("user_role_super_admin"):
        $users = DB::table('tbl_users')->select('*')->where('id','!=',Auth::user()->id)->get();
        foreach($users as $val):
            $department_ids =  $val->department_id;
            $department_ids =  explode(',',$department_ids);
            $departments = DB::table('tbl_departments')->whereIn('department_id',$department_ids)->select(DB::raw('group_concat(department_name) as department_name'))->get(); 
            $val->departments = $departments;
        endforeach; 
        $data['users'] = $users;
        break;
        //group admin or regular user or private user-> deptvise
        case Session::get("user_role_group_admin"):
        case Session::get("user_role_regular_user"):
        case Session::get("user_role_private_user"):
        $data['users']=DB::table('tbl_users')->leftJoin('tbl_users_departments', 'tbl_users.id', '=', 'tbl_users_departments.users_id')->whereIn('tbl_users_departments.department_id',Session::get('auth_user_dep_ids'))->select('tbl_users.username as username')->where('tbl_users.id','!=',Auth::user()->id)->groupBy('tbl_users.id')->get();
        break;
        }
        Session::put('menuid', '1');// Set menu id
        $path_array=Session::get('ancestor');
        $path_array_rev=array_reverse($path_array);
        $path=implode('/',$path_array_rev);
        foreach ($data['dglist'] as $key => $value) {
            $value->document_path=$path;
        }
        return View::make('pages/documents/editdocument')->with($data);
    }
    public function ancestor($parentid) {
        $folder_name=TreeDataModel::select('nm')->where('id',$parentid)->get();
        Session::push('ancestor',$folder_name->last()->nm);
        $result=TreeStructModel::select('pid')->where('id',$parentid)->get();
        $x = 0;
        if(($result[$x]->pid)!= 0) {
           $this->ancestor($result[$x]->pid);
        }
    }

    public function checkout(){
        $id=Input::get('id');
        $view=Input::get('view');
        $documentStatus=DocumentsModel::select('document_status','document_name')->where('document_id',$id)->get();
        foreach ($documentStatus as $key => $value) {
            $doc_status=$value->document_status;
            $doc_name=$value->document_name;
            $doc_checkin_date=$value->document_checkin_date;
        }
        DocumentsModel::where('document_id',$id)->update(['document_status' => "Checkout",'document_pre_status' => $doc_status,'document_checkout_date'=>date('Y-m-d h:i:s'),'document_checkin_date'=>$doc_checkin_date,'document_modified_by'=>Auth::user()->username]);
        $duplicate_doc=DocumentsCheckoutModel::where('document_id','=',$id)->get();
        if(count($duplicate_doc)>=0)
        {
            DocumentsCheckoutModel::where('document_id','=',$id)->delete();    
        } 
        $doc_items = DocumentsModel::where('document_id', '=', $id )->get()->toArray();
        foreach ($doc_items as $data) { 
            $checkmodl= new DocumentsCheckoutModel;
            $checkmodl->insert($data);
        }
        $duplicate_col=DocumentsColumnCheckoutModel::where('document_id','=',$id)->get();
        if(count($duplicate_col)>=0)
        {
            DocumentsColumnCheckoutModel::where('document_id','=',$id)->delete();    
        } 
        $doc_columns=DocumentsColumnModel::where('document_id', '=', $id )->get()->toArray(); 
        foreach ($doc_columns as $columns) { 
            $col_checkmodl= new DocumentsColumnCheckoutModel;
            $col_checkmodl->insert($columns);
        }
        $data['stack'] = StacksModel::all();
        $data['tagsCateg'] = TagWordsCategoryModel::all();
        $data['docType'] = DocumentTypesModel::where('is_app',0)->orderBy('document_type_order', 'ASC')->get();
        $data['dglist'] = DocumentsCheckoutModel::where('document_id',$id)->get();
        $tag = DocumentsCheckoutModel::select('document_tagwords')->where('document_id',$id)->first();
        $tagstr = $tag->document_tagwords;
        $tagarray = explode(',' , $tagstr);
        $data['taglist'] = TagWordsModel::select('tagwords_category_id')->whereIn('tagWords_id', $tagarray)->get();
        $data['view']=$view;
        $data['raw_count']=1;
        $data['id']=$id;
        
        $data['stckApp'] = $this->docObj->common_stack();
        $data['deptApp'] = $this->docObj->common_dept();
        $data['doctypeApp'] = $this->docObj->common_type();
        $data['records'] = $this->docObj->common_records();
        $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
        $data['settings_document_no']   = $settings[0]->settings_document_no;
        $data['settings_document_name'] = $settings[0]->settings_document_name;
        // For audits
        $documentsData = DB::table('tbl_documents')->select('document_no','document_name','document_path')->where('document_id',Input::get('id'))->get();
        
        $user = Auth::user()->username;
        
        $actionMsg = Lang::get('language.checkOut_action_msg');
        $actionDes = $this->docObj->stringReplace($this->actionName,$doc_name,$user,$actionMsg);
        $result = (new AuditsController)->dcmntslog(Auth::user()->username, $id, 'Document', 'Check Out',$actionDes,$documentsData[0]->document_no,$documentsData[0]->document_name,$documentsData[0]->document_path);
        $data['noteList']= DocumentNoteModel::where('document_id', '=', $id )
            ->orderBy('created_at', 'desc')->get();
        return View::make('pages/documents/checkout')->with($data);
    }

    public function edit()
    {   
        // Distroy Session
        Session::forget('ancestor_chk');
        Session::forget('new_file_name');
        Session::forget('extension');

        $id=Input::get('id');
        $view=Input::get('view');
        //----------------Edit authenticate user wise and group-----------------//
        
        if(Auth::user()->user_role==Session::get('user_role_regular_user'))//user
        {
            $doc_checkoutby=DB::table('tbl_documents_checkout')->select('document_modified_by')->where('document_id',$id)->get();
            if(Auth::user()->username!=$doc_checkoutby[0]->document_modified_by)
            {
                return redirect('documentsCheckoutListview')->with('error',"Sorry, you can't edit this document.");                
            }
        }
        else if(Auth::user()->user_role==Session::get('user_role_group_admin'))//group admin
        {
            $doc_dept=DB::table('tbl_documents_checkout')->select('department_id')->where('document_id',$id)->get();
            $doc_dept_array=explode(',',$doc_dept[0]->department_id);
            $dept_usr=explode(',', Auth::user()->department_id);
        if(!array_intersect($doc_dept_array, $dept_usr))
            {
                return redirect('documentsCheckoutListview')->with('error',"Sorry, you can't edit this document.");
                
            }
        }
        //-------------------------------------------------------// 
        if (Auth::user()) {
            
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            

            $data['stacks'] = StacksModel::all();
            $data['tagsCateg'] = TagWordsCategoryModel::all();
            $data['docType'] = DocumentTypesModel::where('is_app',0)->orderBy('created_at',"ASC")->get();
            $data['dglist'] = DocumentsCheckoutModel::where('document_id',$id)->get();
            $this->ancestor_chk($data['dglist'][0]->parent_id);
            $tag = DocumentsCheckoutModel::select('document_tagwords')->where('document_id', '=', $id)->first();
            $tagstr = $tag->document_tagwords;
         
            $tagarray = explode(',' , $tagstr);
            $data['taglist'] = TagWordsModel::select('tagwords_category_id')->whereIn('tagWords_id', $tagarray)->get();
            $data['raw_count']=1;
            $data['id']=$id;  
            $data['view']=$view;
            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;
            $data['noteList']= DocumentNoteModel::where('document_id', '=', $id )
            ->orderBy('created_at', 'desc')->get();
            $data['users'] = DB::table('tbl_users')->select('username')->get();
            $path_array=Session::get('ancestor_chk');
            $path_array_rev=array_reverse($path_array);
            $path=implode('/',$path_array_rev);
            foreach ($data['dglist'] as $key => $value) {
                $value->document_path=$path;
            }
            return View::make('pages/documents/editdocument')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function ancestor_chk($parentid) {
        $folder_name=TreeDataModel::select('nm')->where('id',$parentid)->get();
        Session::push('ancestor_chk',$folder_name->last()->nm);
        $result=TreeStructModel::select('pid')->where('id',$parentid)->get();
        $x = 0;
        if(($result[$x]->pid)!= 0) {
           $this->ancestor_chk($result[$x]->pid);
        }
    }
    public function editdoc(Request $request,$id)
    {   
        if (isset($_POST['save'])) { 
            Session::put('menuid', '1');
            if (Auth::user()) {
                // $validators= Validator:: make(
                // $request-> all(),
                // [
                // 'docno'=> 'required',
                // 'docname'=> 'required'
                // ]
                // );
                // if ($validators->passes()) {
                    $change_folder_id=Input::get('hidd_folder_id');
                    $change_folder_path=Input::get('up_folder');
                    $selctdkeywrds = "";
                    $documenttypeid = "";
                    $deparmentid="";
                    $stackid = "";
                    $keywrds = (Input::get('keywords'))?Input::get('keywords'):[''];
                    $coltypcnt  = Input::get('coltypecnt');
                    if($keywrds!="Please select tag category"){
                        $keywrdsCnt = count($keywrds);
                        for($i=0; $i < $keywrdsCnt; $i++){
                            if($i == $keywrdsCnt-1){
                                $selctdkeywrds = $selctdkeywrds.($keywrds[$i]);
                            }else{
                                $selctdkeywrds = $selctdkeywrds.($keywrds[$i] . ",");
                            }
                        }
                    }else{
                         $selctdkeywrds = [''];
                    }
                    
                    // $keywrds = Input::get('keywords');
                    // $coltypcnt  = Input::get('coltypecnt');
                    // $keywrdsCnt = count(Input::get('keywords'));
                    // for($i=0; $i < $keywrdsCnt; $i++){
                    //     if($i == $keywrdsCnt-1){
                    //         $selctdkeywrds = $selctdkeywrds.($keywrds[$i]);
                    //     }else{
                    //         $selctdkeywrds = $selctdkeywrds.($keywrds[$i] . ",");
                    //     }
                    // }
                    //---Doc types---
                    $dctyp = Input::get('doctypeid');
                    
                    $stack = Input::get('stack');
                    //$stackCnt = count(Input::get('stack'));
                    $stackCnt = 1;
                    $stackcolcnt = Input::get('stackcolcnt');
                    /*Add document stack column*/
                    if($stackcolcnt>0) {
                        for($i=1;$i<=$stackcolcnt;$i++) {
                            $stackColumn = array();
                            $stackColumn['document_id']                       = $id;
                            $stackColumn['stack_column_id']                   = Input::get('stckcolid'.$i);
                            $stackColumn['document_stack_column_name']        = Input::get('stckcolname'.$i);
                            $stackColumn['document_stack_column_value']       = Input::get('stckcol'.$i);
                            $stackColumn['document_stack_column_type']        = Input::get('stckcoltype'.$i);
                            $stackColumn['document_stack_column_mandatory']   = Input::get('stckcolmandatory'.$i);
                            $stackColumn['document_stack_column_created_by']  = Auth::user()->id;
                            $stackColumn['document_stack_column_modified_by'] = Auth::user()->id;
                            $stackColumn['created_at']                        = date('Y-m-d H:i:s');
                            $stackColumn['updated_at']                        = date('Y-m-d H:i:s');
                            $insertStackColumn = DocumentsModel::insertStackColumn($stackColumn);
                        }
                    }
                    /*Add document stack column end*/
                    for($i=0; $i < $stackCnt; $i++){
                        if($i == $stackCnt-1){
                            $stackid = $stackid.($stack[$i]);
                        }else{
                            $stackid = $stackid.($stack[$i] . ",");
                        }
                    }
                    $departments = Input::get('departmentid');
                    $departmentsCnt = count(Input::get('departmentid'));
                    for($i=0; $i < $departmentsCnt; $i++){
                        if($i == $departmentsCnt-1){
                            $deparmentid = $deparmentid.($departments[$i]);
                        }else{
                            $deparmentid = $deparmentid.($departments[$i] . ",");
                        }
                    }
                    //update document status
                    $status = @Input::get('hidden_status');
                    if(@Input::get('assign_users') == "")
                    {
                        $status = 'Published';
                    }
                    elseif(@Input::get('assign_users')!="")
                    {
                        $status = 'Review';
                    }
                    // To update document table
                    $documentDatas = array('document_type_id' => @$dctyp,
                                            'document_no' => Input::get('docno'),
                                            'document_name' => Input::get('docname'),
                                            'department_id' => @implode(',',Input::get('departmentid')),
                                            'stack_id' => $stack,
                                            'document_tagwords' => @implode(',',Input::get('keywords')),
                                            'parent_id' => Input::get('hidd_folder_id'),
                                            'updated_at'=>date('Y-m-d h:i:s'),
                                            'document_expiry_date'=>@Input::get('document_expiry_date'),
                                            'document_assigned_to'=>@Input::get('assign_users'),
                                            'document_status'=>$status,
                                            'document_modified_by' => Auth::user()->username);                    
                    DB::table('tbl_documents')->where('document_id',$id)->update($documentDatas);
                    //tbl_document changes are also effect in tbl_documents_checkout
                    // To update tbl_documents_checkout
                    if(DocumentsCheckoutModel::where('document_id',$id)->exists()){
                    DB::table('tbl_documents_checkout')->where('document_id',$id)->update($documentDatas);
                    }
                    if(DocumentsColumnModel::where('document_id',$id)->exists()){
                        DocumentsColumnModel::where('document_id','=',$id)->delete();    
                    }
                    if(DocumentsColumnCheckoutModel::where('document_id',$id)->exists()){
                        DocumentsColumnCheckoutModel::where('document_id','=',$id)->delete();
                    }
                    if($coltypcnt>0){
                        //update documents columns
                        for($i=1;$i<=$coltypcnt;$i++){
                        $tempdocumenttypecolModl   =   new DocumentsColumnModel;
                        $tempdocumenttypecolModl->document_id =   $id;
                        $tempdocumenttypecolModl->document_type_column_id    =   Input::get('docid'.$i);
                        $tempdocumenttypecolModl->document_column_type  =   Input::get('doctype'.$i);
                        $tempdocumenttypecolModl->document_column_mandatory = Input::get('docmandatory'.$i);
                        $tempdocumenttypecolModl->document_column_name   =   Input::get('doclabl'.$i);

                        /*if type = date then change document column value to date(y-m-d) format*/

                        if(Input::get('doctype'.$i) == 'Date')
                        {
                            $cal_date = Input::get('doccol'.$i);
                            $date = ($cal_date)?date('Y-m-d',strtotime($cal_date)):'';
                            $tempdocumenttypecolModl->document_column_value   =   $date;
                        }
                        else
                        {

                            $tempdocumenttypecolModl->document_column_value   =   Input::get('doccol'.$i);
                        }

                        $tempdocumenttypecolModl->document_column_created_by=Auth::user()->username;
                        $tempdocumenttypecolModl->document_column_modified_by=Auth::user()->username;                     
                        $tempdocumenttypecolModl->save();
                        }
                        // update to document_checkout columns
                        for($i=1;$i<=$coltypcnt;$i++){
                        $checkout_documenttypecolModl   =   new DocumentsColumnCheckoutModel;
                        $checkout_documenttypecolModl->document_id =   $id;
                        $checkout_documenttypecolModl->document_type_column_id    =   Input::get('docid'.$i);
                        $checkout_documenttypecolModl->document_column_type  =   Input::get('doctype'.$i);
                        $checkout_documenttypecolModl->document_column_mandatory = Input::get('docmandatory'.$i);
                        $checkout_documenttypecolModl->document_column_name   =   Input::get('doclabl'.$i);

                        /*if type = date then change document column value to date(y-m-d) format*/

                        if(Input::get('doctype'.$i) == 'Date')
                        {
                            $cal_date = Input::get('doccol'.$i);
                            $date = ($cal_date)?date('Y-m-d',strtotime($cal_date)):'';
                            $checkout_documenttypecolModl->document_column_value   =   $date;
                        }
                        else
                        {

                            $checkout_documenttypecolModl->document_column_value   =   Input::get('doccol'.$i);
                        }

                        $checkout_documenttypecolModl->document_column_value   =   Input::get('doccol'.$i);
                        $checkout_documenttypecolModl->document_column_created_by=Auth::user()->username;
                        $checkout_documenttypecolModl->document_column_modified_by=Auth::user()->username;                     
                        $checkout_documenttypecolModl->save();
                        }
                    }  
                    // update document model
                    // Save in audits
                    $name = Input::get('docname');
                    $user = Auth::user()->username;
                    
                    // Get update action message
                    $actionMsg = Lang::get('language.update_action_msg');
                    $actionDes = $this->docObj->stringReplace($this->actionName,$name,$user,$actionMsg);
                    $result = (new AuditsController)->dcmntslog(Auth::user()->username, $id, 'Document', 'Edit',$actionDes,Input::get('docno'),Input::get('docname'),Input::get('up_folder'));
                    //accept or reject note add and audit add
                    if(Input::get('optionsRadios'))
                    {
                        if(Input::get('optionsRadios') == 'accept')
                        {
                            DB::table('tbl_documents')->where('document_id',$id)->update(['document_status'=>'Published','document_assigned_to'=>Input::get('hidden_assign_user')]);
                            //add to audits
                            $actionMsg = Lang::get('language.accept_action_msg');
                            $actionDes = $this->docObj->stringReplace($name,@Input::get('doc_assigned_by'),Auth::user()->username,$actionMsg);
                            $result = (new AuditsController)->dcmntslog(Auth::user()->username,'','Document', 'Accepted',$actionDes,@Input::get('docno'),@Input::get('docname'),trim(preg_replace('/\s*\([^)]*\)/', '', $change_folder_path)));
                        }
                        elseif(Input::get('optionsRadios') == 'reject')
                        {
                            DB::table('tbl_documents')->where('document_id',$id)->update(['document_status'=>'Rejected','document_assigned_to'=>NULL]);
                            //add to audits
                            $actionMsg = Lang::get('language.reject_action_msg');
                            $actionDes = $this->docObj->stringReplace($name,@Input::get('doc_assigned_by'),Auth::user()->username,$actionMsg);
                            $result = (new AuditsController)->dcmntslog(Auth::user()->username,'','Document', 'Rejected',$actionDes,@Input::get('docno'),@Input::get('docname'),trim(preg_replace('/\s*\([^)]*\)/', '', $change_folder_path)));
                        }
                        DB::table('tbl_document_notes')->insert(['document_id'=>$id,'document_note'=>Input::get('note_assign'),'document_note_created_by'=>Auth::user()->username]);
                    }
                    // else
                    // {
                    //     //add note by without click of addnote button
                    //     if($note_add != "")
                    //     {
                    //         DB::table('tbl_document_notes')->insert(['document_id'=>$id,'document_note'=>$note_add,'document_note_created_by'=>Auth::user()->username]);
                    //     }
                    // }
                //}
                
                    $data['stckApp'] = $this->docObj->common_stack();
                    $data['deptApp'] = $this->docObj->common_dept();
                    $data['doctypeApp'] = $this->docObj->common_type();
                    $data['records'] = $this->docObj->common_records();
                    if(Input::get('document_expiry_date')){
                    $this->docObj->commom_expiry_documents_check(null);}

                // redirect to the required pages
                if(Input::get('pge') == 'cOut'){
                    return redirect('listview?view=checkout&saved_search=1')->with('data', "'".Input::get('docname')."'".' updated successfully.');
                }

                // From document type,stack and department pages 
                if(@Input::get('page') == 'documentType' || @Input::get('page') == 'stack' || @Input::get('page') == 'departments' ){
                    $url = 'editAllDocument?id='.$id.'&page='.Input::get('page').'&status='.Input::get('status').'';
                    return redirect($url)->with('data',"'".Input::get('docname')."'".' '.Lang::get('language.updated_sucess').' ');     
                }

                if(@Input::get('frm')){
                    // From document type,stack and department pages
                    return redirect('docsList/'.Input::get('fid').'?page='.Input::get('frm').'')->with('flash_message_edit', "'".Input::get('docname')."'".' updated successfully.');          
                }

                if(Input::get('page') == 'documentsList' || Input::get('page') == 'advsrch'){
                    return redirect('listview?view=list&saved_search=1')->with('data', "'".Input::get('docname')."'".' updated successfully.');          
                }elseif(Input::get('page') == 'document'){
                    return redirect('documents')->with('data', "'".Input::get('docname')."'".' updated successfully.');    
                }

                if(Input::get('page') == 'content'){
                    $url = 'editAllDocument?id='.$id.'&page='.Input::get('page').'&status='.Input::get('status').'';
                    return redirect($url)->with('data',"'".Input::get('docname')."'".' '.Lang::get('language.updated_sucess').' '); 
                }

                //return redirect('documentsCheckoutListview')->with('data', "'".Input::get('docname')."'".' updated successfully.');          
            }else {
               return redirect('')->withErrors("Please login")->withInput();
            }
        }
        
    }

    public function checkoutdoc(Request $request,$id)
    {   
        $new_file_name = Session::get('new_file_name');
        $extension     = Session::get('extension');
        $history_last_ins_id=0;
        $action = $_POST['Submit_Btn'];
    
        if($action =='Discard Check Out') 
        {

            if($new_file_name){
                $source = config('app.temp_document_path').$new_file_name;
                if(file_exists($source)){
                    unlink($source);
                }
            }
        
            DocumentsModel::where('document_id',$id)->update(['document_status' => "published"]);
            DocumentsCheckoutModel::where('document_id','=',$id)->delete(); 
            DocumentsColumnCheckoutModel::where('document_id','=',$id)->delete();
            
            $name = Input::get('docname');
            $user = Auth::user()->username;
            
            // checkout discarded
            $actionMsg = Lang::get('language.checkOutDiscarded_action_msg');
            $actionDes = $this->docObj->stringReplace($this->actionName,$name,$user,$actionMsg);
            $result = (new AuditsController)->dcmntslog(Auth::user()->username, $id, 'Document', 'Document Check Out discarded',$actionDes,Input::get('docno'),Input::get('docname'),Input::get('up_folder'));
            echo "Document Check Out discarded";exit;// ajax response
            //return redirect('documents')->with('data','Document Check Out discarded.');   
        }

        switch($action)
        {
        case 'Check In and Publish':
        case 'Check In as Draft':
         {
            if (Auth::user()) {
               
                    $change_folder_id=Input::get('hidd_folder_id');
                    $change_folder_path=Input::get('up_folder');
                    $selctdkeywrds = "";
                    $documenttypeid = "";
                    $departmentid = "";
                    $stackid = "";
                    $keywrds = (Input::get('keywords'))?Input::get('keywords'):[''];
                    $coltypcnt  = Input::get('coltypecnt');
                    if($keywrds!="Please select tag category"){
                        $keywrdsCnt = count($keywrds);
                        for($i=0; $i < $keywrdsCnt; $i++){
                            if($i == $keywrdsCnt-1){
                                $selctdkeywrds = $selctdkeywrds.($keywrds[$i]);
                            }else{
                                $selctdkeywrds = $selctdkeywrds.($keywrds[$i] . ",");
                            }
                        }
                    }else{
                         $selctdkeywrds = [''];
                    }
                    //---Department---
                    $dept = Input::get('departmentid');
                    $deptCnt = count(Input::get('departmentid'));
                    for($i=0; $i < $deptCnt; $i++){
                        if($i == $deptCnt-1){
                            $departmentid = $departmentid.($dept[$i]);
                        }else{
                            $departmentid = $departmentid.($dept[$i] . ",");
                        }
                    }

                    //---Doc types---
                    $documenttypeid = Input::get('doctypeid');
                    
                    $stackid = (Input::get('stack'))?Input::get('stack'):'';
                    // print_r($stack);
                    // echo $stackCnt = count($stack);

                    // for($i=0; $i < $stackCnt; $i++){
                    //     if($i == $stackCnt-1){
                    //         $stackid = $stackid.($stack[$i]);
                    //     }else{
                    //         $stackid = $stackid.($stack[$i] . ",");
                    //     }
                    // }
                    $documentMgmtTemp =   new DocumentsCheckoutModel;
                    $duplicate_entry=$documentMgmtTemp->where('document_id','=',$id)->get();
                    if(count($duplicate_entry)>0)
                    {
                       DocumentsCheckoutModel::where('document_id','=',$id)->delete();  
                    }

                        $bckupdestinationPath   = config('app.backup_path'); 
                        if(!file_exists($bckupdestinationPath))
                        {
                
                         File::makeDirectory($bckupdestinationPath, $mode = 0777, true, true);
                        } 
                  
                    foreach ($duplicate_entry as $data) 
                    {
                        $oldFileName   = $data->document_file_name;
                        $history_file_name=$data->document_file_name;   
                        $source = config('app.base_path').$oldFileName;
                        

                    /*Move document to backup folder*/        
                    if($oldFileName)
                    {
                        $splitVersions = pathinfo($oldFileName);
                        $extension = $splitVersions['extension'];
                        $partfilename = $splitVersions['filename'];
                        $version_no = $data->document_version_no;
                        $source = config('app.base_path').$oldFileName;

                        $history_file_name = $partfilename.'_'.$version_no.'.'.$extension;     
                        $dest = $bckupdestinationPath.$history_file_name;
                        if(file_exists($source))
                        {
                             
                            $copy = copy($source, $dest);
                            unlink($source);//reomve original file    
                        }
                    }

                        /*Move temp folder to document folder*/
                        if($new_file_name)
                        {
                            /*$document_file_name = ($oldFileName)?$oldFileName:$new_file_name;*/
                            $source         =   config('app.temp_document_path').$new_file_name;
                            $dest           =   config('app.base_path').$new_file_name;
                            if(file_exists($source))
                            { //copy the file source to destination
                                $copy = copy($source, $dest);
                                unlink($source);//reomve original file
                            }           
                        } 

                        //If automatically increment the version
                        if(Input::get('version') == 'Yes')
                        {
                            // update new version
                            $version_old=$data->document_version_no;
                            $version_new=($version_old+0.1);  

                        }
                        else
                        {
                            // save old version
                            $version_new=$data->document_version_no;
                            
                        }
                        // upload new file if exists
                        if($new_file_name)
                        {
                            $newFileName  = $new_file_name;
                        }
                        else
                        {
                            $newFileName = $data->document_file_name;
                        }

                        $data_doc_modl=new DocumentsModel;
                        if($action == 'Check In as Draft')
                        {
                            $a="Draft";
                        }
                        else if($action == 'Check In and Publish')
                        {
                            $a="Published";
                        }
                        
                        $data_doc_modl->where('document_id',$id)->update([
                        'document_type_id'      => $documenttypeid,
                        'document_name'         => Input::get('docname'),
                        'document_file_name'    => $newFileName,
                        'parent_id'             => $change_folder_id,
                        'department_id'         => $departmentid,
                        'stack_id'              => $stackid,
                        'document_checkin_date' => date('Y-m-d h:i:s'),
                        'document_checkout_date'=> $data->document_checkout_date,
                        'document_checkout_path'=> $data->document_checkout_path,
                        'document_version_no'   => $version_new,
                        'document_ownership'    => $data->document_ownership,
                        'document_created_by'   => $data->document_created_by,
                        'document_modified_by'  => Auth::user()->username,
                        'document_tagwords'     => $selctdkeywrds,
                        'document_no'           => Input::get('docno'),
                        'document_path'         => $change_folder_path,
                        'document_status'       => $a,
                        'document_expiry_date'  =>  @Input::get('document_expiry_date'),
                        'document_pre_status'   => "Published",
                        'created_at'            => $data->created_at,
                        'updated_at'            => date('Y-m-d h:i:s'),
                        'document_size'         => $data->document_size]);
                        //doc expiry notification
                        if(Input::get('document_expiry_date')){
                            //call common function for doc expiry notification
                            
                            $this->docObj->commom_expiry_documents_check(null);
                        }
                        //insert into documents history
                        $dataHistoryModl=new DocumentHistoryModel;
                        if($action=='Check In as Draft')
                        {
                            $b="Checkin as Draft";
                        }
                        else if($action == 'Check In and Publish')
                        {
                            $b="Checkin";
                        }
                        $dataHistoryModl->document_id           = $id;
                        $dataHistoryModl->document_no           = $data->document_no;
                        $dataHistoryModl->document_type_id      = $data->document_type_id;
                        $dataHistoryModl->stack_id              = $data->stack_id;
                        $dataHistoryModl->department_id         = $data->department_id;
                        $dataHistoryModl->document_name= $data->document_name;
                        $dataHistoryModl->document_file_name    = $history_file_name;
                        $dataHistoryModl->document_size         = $data->document_size;
                        $dataHistoryModl->document_checkin_date = $data->document_checkin_date;
                        $dataHistoryModl->document_checkout_date= $data->document_checkout_date;
                        $dataHistoryModl->document_modified_by  = $data->document_modified_by;
                        $dataHistoryModl->documents_checkout_by = $data->documents_checkout_by;
                        $dataHistoryModl->document_path         = $data->document_path;
                        $dataHistoryModl->document_version_no   = $data->document_version_no;
                        $dataHistoryModl->document_status       = $b;
                        $dataHistoryModl->document_history_created_by= Auth::user()->username;
                        $dataHistoryModl->created_at            = date('Y-m-d h:i:s');
                        $dataHistoryModl->updated_at            = date('Y-m-d h:i:s');
                        $dataHistoryModl->save();
                        //$history_last_ins_id = $dataHistoryModl->getConnection()->getPdo()->lastInsertId();
                        $history_last_ins_id = $dataHistoryModl->document_id;
                    }  
                    //save documents columns to history columns
                        $data_from_check_col = DocumentsColumnCheckoutModel::where('document_id',$id )->get();
                        // echo '<pre>';
                        // print_r($data_from_check_col);
                        // exit();
                        foreach ($data_from_check_col as $data) 
                        { 
                        $tbl_documents_col_history=new DocumentHistoryColumnModel;
                        $tbl_documents_col_history->document_id=$data->document_id;
                        $tbl_documents_col_history->document_history_id=$history_last_ins_id;
                        $tbl_documents_col_history->document_type_column_id=$data->document_type_column_id;
                        $tbl_documents_col_history->document_column_name=$data->document_column_name;
                        $tbl_documents_col_history->document_column_value=$data->document_column_value;
                        $tbl_documents_col_history->document_column_type=$data->document_column_type;
                        $tbl_documents_col_history->document_column_mandatory=$data->document_column_mandatory;
                        $tbl_documents_col_history->document_column_created_by=$data->document_column_created_by;
                        $tbl_documents_col_history->document_column_modified_by=Auth::user()->username;
                        $tbl_documents_col_history->created_at=$data->created_at;
                        $tbl_documents_col_history->updated_at=date('Y-m-d h:i:s');
                        $tbl_documents_col_history->save();
                        }                          
                    $documentMgmtTemp->where('document_id',$id)->delete();
                    $duplicate_col_chk=DocumentsColumnCheckoutModel::where('document_id','=',$id)->get();
                    if(count($duplicate_col_chk)>0)
                    {
                        DocumentsColumnCheckoutModel::where('document_id','=',$id)->delete();    
                    }
                    $duplicate_col=DocumentsColumnModel::where('document_id','=',$id)->get();
                    if(count($duplicate_col)>0)
                    {
                        DocumentsColumnModel::where('document_id','=',$id)->delete();    
                    }
                    if($coltypcnt>0){
                        for($i=1;$i<=$coltypcnt;$i++){
                        $tempdocumenttypecolModl   =   new DocumentsColumnModel;
                        $tempdocumenttypecolModl->document_id =   $id;
                        $tempdocumenttypecolModl->document_type_column_id    =   Input::get('docid'.$i);
                        $tempdocumenttypecolModl->document_column_type  =   Input::get('doctype'.$i);
                        $tempdocumenttypecolModl->document_column_mandatory = Input::get('docmandatory'.$i);
                        $tempdocumenttypecolModl->document_column_name   =   Input::get('doclabl'.$i);
                        /*if type = date then change document column value to date(y-m-d) format*/

                        if(Input::get('doctype'.$i) == 'Date')
                        {
                            $cal_date = Input::get('doccol'.$i);
                            $date = ($cal_date)?date('Y-m-d',strtotime($cal_date)):'';
                            $tempdocumenttypecolModl->document_column_value   =   $date;
                        }
                        else
                        {

                            $tempdocumenttypecolModl->document_column_value   =   Input::get('doccol'.$i);
                        }
                        
                        $tempdocumenttypecolModl->document_column_created_by=Auth::user()->username;
                        $tempdocumenttypecolModl->document_column_modified_by=Auth::user()->username;
                        $tempdocumenttypecolModl->save();
                        }
                        
                    }
                
                // Audits save
                $name = Input::get('docname');
                $user = Auth::user()->username;
                
                if($action == 'Check In and Publish'){
    
                // checked in
                $actionMsg = Lang::get('language.checkedIn_action_msg');
                $actionDes = $this->docObj->stringReplace($this->actionName,$name,$user,$actionMsg);
                $result = (new AuditsController)->dcmntslog(Auth::user()->username, $id, 'Document', 'Checked In',$actionDes,Input::get('docno'),Input::get('docname'),Input::get('up_folder'));
                return redirect('documents')->with('data', Input::get('docname').  ' Checked In successfully.'); 
                } 
                else if($action == 'Check In as Draft') 
                {
                
                // published as draft
                $actionMsg = Lang::get('language.publishedAsDraft_action_msg');
                $actionDes = $this->docObj->stringReplace($this->actionName,$name,$user,$actionMsg);
                $result = (new AuditsController)->dcmntslog(Auth::user()->username, $id, 'Document', 'Published as Draft',$actionDes,Input::get('docno'),Input::get('docname'),Input::get('up_folder'));
                return redirect('documents')->with('data', Input::get('docname').  ' published as Draft successfully.'); 
                }            
            }else {
                return redirect('')->withErrors("Please login")->withInput();
            }
        }
        break;
    }
}

    public function documentsSubListCheckout()
    { 
        if (Auth::user()) {
            $doctypeid = Input::get('doctypeid');
            $status      = Input::get('status'); 
            $dctpeid = "";
            $doc=Input::get('doc_id');
            $doctypeid = $dctpeid;

                
                    $data['stckApp'] = $this->docObj->common_stack();
                    $data['deptApp'] = $this->docObj->common_dept();
                    $data['doctypeApp'] = $this->docObj->common_type();
                    $data['records'] = $this->docObj->common_records();
                $data['documentTypeData'] = DocumentTypeColumnModel::where('document_type_id', $doctypeid)->orderby('document_type_id','ASC')->orderby('document_type_column_order','ASC')->get();
                // if page exists get  fdata from normal documents coloumn else get from tbl_documents_columns_checkout
                if($status!='checkout'){
                    $data['fetch_doc_col'] =DB::table('tbl_documents_columns')
                    ->join('tbl_document_types_columns', 'tbl_documents_columns.document_type_column_id', '=', 'tbl_document_types_columns.document_type_column_id')->select('tbl_documents_columns.*', 'tbl_document_types_columns.*')->where('tbl_documents_columns.document_id', $doc)->orderby('tbl_document_types_columns.document_type_id','ASC')->orderby('tbl_document_types_columns.document_type_column_order')->get();
                }elseif($status=='checkout'){
                    $data['fetch_doc_col'] =DB::table('tbl_documents_columns_checkout')
                    ->join('tbl_document_types_columns', 'tbl_documents_columns_checkout.document_type_column_id', '=', 'tbl_document_types_columns.document_type_column_id')->select('tbl_documents_columns_checkout.*', 'tbl_document_types_columns.*')->where('tbl_documents_columns_checkout.document_id', $doc)->orderby('tbl_document_types_columns.document_type_id','ASC')->orderby('tbl_document_types_columns.document_type_column_order')->get();
                    //$data['fetch_doc_col']=DocumentsColumnCheckoutModel::where('document_id', $doc)->get();
                }               
            
            return View::make('pages/documents/sublist_edit')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }


    public function checkin(){
        $id=Input::GET('id');
        $selctdkeywrds = "";
        $documenttypeid = "";
        $stackid = "";
        $keywrds = Input::get('keywords');
        $coltypcnt  = Input::get('coltypecnt');
        $keywrdsCnt = count(Input::get('keywords'));
        for($i=0; $i < $keywrdsCnt; $i++){
            if($i == $keywrdsCnt-1){
                $selctdkeywrds = $selctdkeywrds.($keywrds[$i]);
            }else{
                $selctdkeywrds = $selctdkeywrds.($keywrds[$i] . ",");
            }
        }
        //---Doc types---
        $dctyp = Input::get('doctypeid');
        $dctypCnt = count(Input::get('doctypeid'));
        for($i=0; $i < $dctypCnt; $i++){
            if($i == $dctypCnt-1){
                $documenttypeid = $documenttypeid.($dctyp[$i]);
            }else{
                $documenttypeid = $documenttypeid.($dctyp[$i] . ",");
            }
        }
        $stack = Input::get('stack');
        $stackCnt = count(Input::get('stack'));
        for($i=0; $i < $stackCnt; $i++){
            if($i == $stackCnt-1){
                $stackid = $stackid.($stack[$i]);
            }else{
                $stackid = $stackid.($stack[$i] . ",");
            }
        }
        //----------------Check in authenticate user wise and group-----------------//
        
        if(Auth::user()->user_role==Session::get('user_role_private_user') || Auth::user()->user_role==Session::get('user_role_regular_user'))//user
        {
            $doc_checkoutby=DB::table('tbl_documents_checkout')->select('document_modified_by')->where('document_id',$id)->get();
            if(Auth::user()->username!=$doc_checkoutby[0]->document_modified_by)
            {
                echo "Sorry, you can't check in this document";
                exit();
            }
        }
        else if(Auth::user()->user_role== Session::get('user_role_group_admin'))//group admin
        {
            $doc_dept=DB::table('tbl_documents_checkout')->select('department_id')->where('document_id',$id)->get();
            $doc_dept_array=explode(',',$doc_dept[0]->department_id);
            $dept_usr=explode(',', Auth::user()->department_id);
        if(!array_intersect($doc_dept_array, $dept_usr))
            {
                echo "Sorry, you can't check in this document";
                exit();
            }
        }
        //-------------------------------------------------------//
        $documentMgmtTemp =   new DocumentsCheckoutModel;
        $duplicate_entry=$documentMgmtTemp->where('document_id','=',$id)->get();
        if(count($duplicate_entry)>0)
        {
            DocumentsCheckoutModel::where('document_id','=',$id)->delete();    
        }
        foreach ($duplicate_entry as $data) {
            $version_old=$data->document_version_no;
            $version_new=($version_old+0.1);
            $data_doc_modl=new DocumentsModel;
            $data_doc_modl->where('document_id',$id)->update([
            'document_type_id'      => $data->document_type_id,
            'document_name'         => $data->document_name,
            'document_file_name'    => $data->document_file_name,
            'parent_id'             => $data->parent_id,
            'department_id'         => $data->department_id,
            'stack_id'              => $data->stack_id,
            'document_checkin_date' => date('Y-m-d h:i:s'),
            'document_checkout_date'=> $data->document_checkout_date,
            'document_checkout_path'=> $data->document_checkout_path,
            'document_version_no'   => $version_new,
            'document_ownership'    => $data->document_ownership,
            'document_created_by'   => $data->document_created_by,
            'document_modified_by'  => Auth::user()->username,
            'document_tagwords'     => $data->document_tagwords,
            'document_no'           => $data->document_no,
            'document_path'         => $data->document_path,
            'document_status'       => "Published",
            'document_pre_status'   => "Published",
            'created_at'            => $data->created_at,
            'updated_at'            => date('Y-m-d h:i:s'),
            'document_size'         => $data->document_size]);
            //insert into documents history
            $dataHistoryModl=new DocumentHistoryModel;
            $dataHistoryModl->document_id           = $id;
            $dataHistoryModl->document_no           = $data->document_no;
            $dataHistoryModl->document_type_id      = $data->document_type_id;
            $dataHistoryModl->stack_id              = $data->stack_id;
            $dataHistoryModl->department_id         = $data->department_id;
            $docName=$dataHistoryModl->document_name= $data->document_name;
            $dataHistoryModl->document_file_name    = $oldFileName;
            $dataHistoryModl->document_size         = $data->document_size;
            $dataHistoryModl->document_checkin_date = $data->document_checkin_date;
            $dataHistoryModl->document_checkout_date= $data->document_checkout_date;
            $dataHistoryModl->document_modified_by  = $data->document_modified_by;
            $dataHistoryModl->documents_checkout_by = $data->documents_checkout_by;
            $dataHistoryModl->document_path         = $data->document_path;
            $dataHistoryModl->document_version_no   = $data->document_version_no;
            $dataHistoryModl->document_status       = 'Checkin';
            $dataHistoryModl->document_history_created_by= Auth::user()->username;
            $dataHistoryModl->created_at            = date('Y-m-d h:i:s');
            $dataHistoryModl->updated_at            = date('Y-m-d h:i:s');
            $dataHistoryModl->save();
            $history_last_ins_id = $dataHistoryModl->getConnection()->getPdo()->lastInsertId();
        }
                            
        $documentMgmtTemp->where('document_id',$id)->delete();
        
        $documentcol =   new DocumentsColumnModel;
        $duplicate_entry=$documentcol->where('document_id','=',$id)->get();
        
        if(count($duplicate_entry)>0)
        {
            DocumentsColumnModel::where('document_id','=',$id)->delete();    
        }
        $documentcol_chkout =   new DocumentsColumnCheckoutModel;
        $duplicate_entry_columns=$documentcol_chkout->where('document_id','=',$id)->get();
        if(count($duplicate_entry_columns)>0)
        {
            DocumentsColumnCheckoutModel::where('document_id','=',$id)->delete();    
        }
        foreach ($duplicate_entry_columns as $data) {
            $documenttypecolModl=new DocumentsColumnModel;
            $documenttypecolModl->document_id =   $id;
            $documenttypecolModl->document_type_column_id       =$data->document_type_column_id;
            $documenttypecolModl->document_column_name          =$data->document_column_name;
            $documenttypecolModl->document_column_value         =$data->document_column_value;
            $documenttypecolModl->document_column_type          =$data->document_column_type;
            $documenttypecolModl->document_column_mandatory     =$data->document_column_mandatory;
            $documenttypecolModl->document_column_created_by    =$data->document_column_created_by;
            $documenttypecolModl->document_column_modified_by   =Auth::user()->username;
            $documenttypecolModl->save();
        }
        foreach ($duplicate_entry_columns as $data) {
            $documenttypecolModl=new DocumentHistoryColumnModel;
            $documenttypecolModl->document_id =   $id;
            $documenttypecolModl->document_history_id = $history_last_ins_id;
            $documenttypecolModl->document_type_column_id       =$data->document_type_column_id;
            $documenttypecolModl->document_column_name          =$data->document_column_name;
            $documenttypecolModl->document_column_value         =$data->document_column_value;
            $documenttypecolModl->document_column_type          =$data->document_column_type;
            $documenttypecolModl->document_column_mandatory     =$data->document_column_mandatory;
            $documenttypecolModl->document_column_created_by    =$data->document_column_created_by;
            $documenttypecolModl->document_column_modified_by   =Auth::user()->username;
            $documenttypecolModl->save();
        }

        // save audits
        $documentsData = DB::table('tbl_documents')->select('document_no','document_name','document_path')->where('document_id',Input::get('id'))->get();
        // Save audits
        $user = Auth::user()->username;
        
        $actionMsg = Lang::get('language.checkedIn_action_msg');
        $actionDes = $this->docObj->stringReplace($this->actionName,$docName,$user,$actionMsg);
        $result = (new AuditsController)->dcmntslog(Auth::user()->username, $id, 'Document', 'Checked In',$actionDes,$documentsData[0]->document_no,$documentsData[0]->document_name,$documentsData[0]->document_path);
        echo "Document ".$docName." Checked In successfully.";                 
    }

    public function discard_published(){
        $id=Input::GET('id');
        //----------------Discard authenticate user wise and group-----------------//
        
        if(Auth::user()->user_role==Session::get('user_role_regular_user'))//user
        {
            $doc_checkoutby=DB::table('tbl_documents_checkout')->select('document_modified_by')->where('document_id',$id)->get();
            if(Auth::user()->username!=$doc_checkoutby[0]->document_modified_by)
            {
                echo "Sorry, you can't discard Check Out of this document";
                exit();
            }
        }
        else if(Auth::user()->user_role==Session::get('user_role_group_admin'))//group admin
        {
            $doc_dept=DB::table('tbl_documents_checkout')->select('department_id')->where('document_id',$id)->get();
            $doc_dept_array=explode(',',$doc_dept[0]->department_id);
            $dept_usr=explode(',', Auth::user()->department_id);
        if(!array_intersect($doc_dept_array, $dept_usr))
            {
                echo "Sorry, you can't discard Check Out of this document";
                exit();
            }
        }
        //-------------------------------------------------------//  
        $docName = DB::table('tbl_documents')->select('document_no','document_name','document_path')->where('document_id',$id)->get();   
        DocumentsModel::where('document_id',$id)->update(['document_status' => "Published"]);
        DocumentsCheckoutModel::where('document_id','=',$id)->delete(); 
        DocumentsColumnCheckoutModel::where('document_id','=',$id)->delete();
        // Audits save
        $name = $docName[0]->document_name;
        $user = Auth::user()->username;
        
        $actionMsg = Lang::get('language.checkOutDiscarded_action_msg');
        $actionDes = $this->docObj->stringReplace($this->actionName,$name,$user,$actionMsg);
        (new AuditsController)->dcmntslog(Auth::user()->username, $id, 'Document', 'Document Check Out discarded',$actionDes,$docName[0]->document_no,$docName[0]->document_name,$docName[0]->document_path);
        echo "Document Check Out discarded.";
    }
    public function discard_draft(){
        $id=Input::GET('id'); 
        //----------------Discard authenticate user wise and group-----------------//
        
        if(Auth::user()->user_role==Session::get('user_role_regular_user'))//user
        {
            $doc_checkoutby=DB::table('tbl_documents_checkout')->select('document_modified_by')->where('document_id',$id)->get();
            if(Auth::user()->username!=$doc_checkoutby[0]->document_modified_by)
            {
                echo "Sorry, you can't discard Check Out of this document";
                exit();
            }
        }
        else if(Auth::user()->user_role==Session::get('user_role_group_admin'))//group admin
        {
            $doc_dept=DB::table('tbl_documents_checkout')->select('department_id')->where('document_id',$id)->get();
            $doc_dept_array=explode(',',$doc_dept[0]->department_id);
            $dept_usr=explode(',', Auth::user()->department_id);
        if(!array_intersect($doc_dept_array, $dept_usr))
            {
                echo "Sorry, you can't discard Check Out of this document";
                exit();
            }
        }
        //-------------------------------------------------------//   
        $docName = DB::table('tbl_documents')->select('document_no','document_name','document_path')->where('document_id',$id)->get();
        DocumentsModel::where('document_id',$id)->update(['document_status' => "Draft"]);
        DocumentsCheckoutModel::where('document_id','=',$id)->delete(); 
        DocumentsColumnCheckoutModel::where('document_id','=',$id)->delete();
        // Save audits
        $name = $docName[0]->document_name;
        $user = Auth::user()->username;
        
        // checkout discarded
        $actionMsg = Lang::get('language.checkOutDiscarded_action_msg');
        $actionDes = $this->docObj->stringReplace($this->actionName,$name,$user,$actionMsg);
        (new AuditsController)->dcmntslog(Auth::user()->username, $id, 'Document', 'Document Check Out discarded',$actionDes,$docName[0]->document_no,$docName[0]->document_name,$docName[0]->document_path);
        echo json_encode('Document Check Out discarded.');
    }
    public function discardAll()
    {
        $arr=Input::get('selected');
        if (Auth::user()) {
            foreach ($arr as $key => $value) {
                $preStatus=DocumentsCheckoutModel::select('document_pre_status')->where('document_id',$value)->get();
                foreach ($preStatus as $key => $va) 
                {
                    $ss=$va->document_pre_status;
                    echo "ss".$ss." value".$value;
                    DocumentsModel::where('document_id',$value)->update(['document_status' =>$ss]);
                }
                DocumentsCheckoutModel::where('document_id',$value)->delete(); 
                DocumentsColumnCheckoutModel::where('document_id',$value)->delete();
                $docName = DB::table('tbl_documents')->select('document_no','document_name','document_path')->where('document_id',$value)->get(); 
                // Save audits
                $name = $docName[0]->document_name;
                $user = Auth::user()->username;
                
                // checkout discarded
                $actionMsg = Lang::get('language.checkOutDiscarded_action_msg');
                $actionDes = $this->docObj->stringReplace($this->actionName,$name,$user,$actionMsg);
                (new AuditsController)->dcmntslog(Auth::user()->username, $value, 'Document', 'Documents Check Out discarded',$actionDes,$docName[0]->document_no,$docName[0]->document_name,$docName[0]->document_path);
            }
            
            echo json_encode('Documents Check Out discarded.');
        }
        else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function cancel(){
        $id=Input::GET('id');    
        DocumentsModel::where('document_id',$id)->update(['document_status' => "Published"]);
        DocumentsCheckoutModel::where('document_id','=',$id)->delete(); 
        DocumentsColumnCheckoutModel::where('document_id','=',$id)->delete();
        echo "Check out canceled";
    }
public function draft(){
        $id=Input::GET('id');
        //----------------Draft authenticate user wise and group-----------------//
        
        if(Auth::user()->user_role==Session::get('user_role_regular_user'))//user
        {
            $doc_checkoutby=DB::table('tbl_documents_checkout')->select('document_modified_by')->where('document_id',$id)->get();
            if(Auth::user()->username!=$doc_checkoutby[0]->document_modified_by)
            {
                echo "Sorry, you can't publish this document as draft";
                exit();
            }
        }
        else if(Auth::user()->user_role==Session::get('user_role_group_admin'))//group admin
        {
            $doc_dept=DB::table('tbl_documents_checkout')->select('department_id')->where('document_id',$id)->get();
            $doc_dept_array=explode(',',$doc_dept[0]->department_id);
            $dept_usr=explode(',', Auth::user()->department_id);
        if(!array_intersect($doc_dept_array, $dept_usr))
            {
                echo "Sorry, you can't publish this document as draft";
                exit();
            }
        }
        //-------------------------------------------------------//
        $documentMgmtTemp =   new DocumentsCheckoutModel;
            $duplicate_entry=$documentMgmtTemp->where('document_id','=',$id)->get();
            if(count($duplicate_entry)>0)
            {
                DocumentsCheckoutModel::where('document_id','=',$id)->delete();    
            }
            foreach ($duplicate_entry as $data) {
            $version_old=$data->document_version_no;
            $version_new=($version_old+0.1);
            $data_doc_modl=new DocumentsModel;
            $data_doc_modl->where('document_id',$id)->update([
            'document_type_id'      => $data->document_type_id,
            'document_name'         => $data->document_name,
            'document_file_name'    => $data->document_file_name,
            'parent_id'             => $data->parent_id,
            'department_id'         => $data->department_id,
            'stack_id'              => $data->stack_id,
            'document_checkin_date' => date('Y-m-d h:i:s'),
            'document_checkout_date'=> $data->document_checkout_date,
            'document_checkout_path'=> $data->document_checkout_path,
            'document_version_no'   => $version_new,
            'document_ownership'    => $data->document_ownership,
            'document_created_by'   => $data->document_created_by,
            'document_modified_by'  => Auth::user()->username,
            'document_tagwords'     => $data->document_tagwords,
            'document_no'           => $data->document_no,
            'document_path'         => $data->document_path,
            'document_status'       => "Draft",
            'document_pre_status'   => "Draft",
            'created_at'            => $data->created_at,
            'updated_at'            => date('Y-m-d h:i:s'),
            'document_size'         => $data->document_size]);
            //insert into documents history
            $dataHistoryModl=new DocumentHistoryModel;
            $dataHistoryModl->document_id           = $id;
            $dataHistoryModl->document_no           = $data->document_no;
            $dataHistoryModl->document_type_id      = $data->document_type_id;
            $dataHistoryModl->stack_id              = $data->stack_id;
            $dataHistoryModl->department_id         = $data->department_id;
            $docName=$dataHistoryModl->document_name= $data->document_name;
            $dataHistoryModl->document_file_name    = $oldFileName;
            $dataHistoryModl->document_size         = $data->document_size;
            $dataHistoryModl->document_checkin_date = $data->document_checkin_date;
            $dataHistoryModl->document_checkout_date= $data->document_checkout_date;
            $dataHistoryModl->document_modified_by  = $data->document_modified_by;
            $dataHistoryModl->documents_checkout_by = $data->documents_checkout_by;
            $dataHistoryModl->document_path         = $data->document_path;
            $dataHistoryModl->document_version_no   = $data->document_version_no;
            $dataHistoryModl->document_status       = 'checkin';
            $dataHistoryModl->document_history_created_by= Auth::user()->username;
            $dataHistoryModl->created_at            = date('Y-m-d h:i:s');
            $dataHistoryModl->updated_at            = date('Y-m-d h:i:s');
            $dataHistoryModl->save();
            $history_last_ins_id = $dataHistoryModl->getConnection()->getPdo()->lastInsertId();
            }
            
            $documentMgmtTemp->where('document_id',$id)->delete();
            $duplicate_col_chk=DocumentsColumnCheckoutModel::where('document_id','=',$id)->get();
            if(count($duplicate_col_chk)>0)
            {
                DocumentsColumnCheckoutModel::where('document_id','=',$id)->delete();    
            }
            $duplicate_col=DocumentsColumnModel::where('document_id','=',$id)->get();
            if(count($duplicate_col)>0)
            {
                DocumentsColumnModel::where('document_id','=',$id)->delete();    
            }
                
            foreach($duplicate_col_chk as $columns){
                $tempdocumenttypecolModl   =   new DocumentsColumnModel;
                $tempdocumenttypecolModl->document_id =   $columns->document_id;
                $tempdocumenttypecolModl->document_type_column_id    =   $columns->document_id;
                $tempdocumenttypecolModl->document_column_name   =   $columns->document_column_name;
                $tempdocumenttypecolModl->document_column_value   =   $columns->document_column_value;
                $tempdocumenttypecolModl->document_column_type   =   $columns->document_column_type;
                $tempdocumenttypecolModl->document_column_mandatory   =   $columns->document_column_mandatory;
                $tempdocumenttypecolModl->document_column_created_by=$columns->document_column_created_by;
                $tempdocumenttypecolModl->document_column_modified_by=Auth::user()->username;
                $tempdocumenttypecolModl->save();
            }

        // save audits
        $documentsData = DB::table('tbl_documents')->select('document_no','document_name','document_path')->where('document_id',Input::get('id'))->get();
        
        $user = Auth::user()->username;
        
        // published as draft
        $actionMsg = Lang::get('language.publishedAsDraft_action_msg');
        $actionDes = $this->docObj->stringReplace($this->actionName,$docName,$user,$actionMsg);
        $result = (new AuditsController)->dcmntslog(Auth::user()->username, $id, 'Document', 'Document Published As Draft',$actionDes,$documentsData[0]->document_no,$documentsData[0]->document_name,$documentsData[0]->document_path);
        echo 'Document published as Draft successfully.';
    } 
    public function getsecuritySettings(){
        // Show warning message
        echo '<ul>
                <li>Document type cannot be changed while checking in a document. This is to preserve the integrity of the system.</li>
             </ul>';
    } 
    public function getsecuritySettingsEdit(){
        // Show warning message
        echo '<ul>
                <li>Document type cannot be changed while editing a document. This is to preserve the integrity of the system.</li>
             </ul>';
    }          
}
