<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;
use View;
use Validator;
use Input;
use Session;
use App\Mylibs\Common;
use App\DocumentTypesModel as DocumentTypesModel;
use App\DocumentsModel as DocumentsModel;
use App\DocumentTypeColumnModel as DocumentTypeColumnModel;
use App\DocumentsColumnCheckoutModel as DocumentsColumnCheckoutModel;
use App\DocumentsColumnModel as DocumentsColumnModel;
use App\StacksModel as StacksModel;
use App\DepartmentsModel as DepartmentsModel;
use App\TempDocumentsColumnModel as TempDocumentsColumnModel;
use DB;
use Lang;

class DocumentTypesController extends Controller
{
    public function __construct()
    {
        Session::put('menuid', '2');
        $this->middleware(['auth', 'user.status']);
        // Set common variable
        $this->actionName = 'Document Type';
        $this->docObj     = new Common(); // class defined in app/mylibs
    }

    public function index()
    { 
        if (Auth::user()) {
            $data['docType'] = DocumentTypesModel::orderBy('document_type_order', 'ASC')->get();
            
                $data['stckApp'] = $this->docObj->common_stack();
                $data['deptApp'] = $this->docObj->common_dept();
                $data['doctypeApp'] = $this->docObj->common_type();
                $data['records'] = $this->docObj->common_records();

            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;
             Session::put('menuid', '2');
            return View::make('pages/document_types/index')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    public function documentsList(Request $request,$id)
    {   
        if (Auth::user()) {

            /*<!--Common for Document,stack and department-->*/
            DB::enableQueryLog();
            $query = DB::table('tbl_documents_columns')->join('tbl_documents','tbl_documents.document_id','=','tbl_documents_columns.document_id')->SELECT ('*',
            DB::raw('group_concat( tbl_documents_columns.document_column_value) AS document_column_value'),  
            DB::raw('group_concat( tbl_documents_columns.document_column_name) AS document_column_name'))->groupBy('tbl_documents_columns.document_id');
            
            if(Session::get('search_documentsIds'))
               $query->whereIn('tbl_documents.document_id',Session::get('search_documentsIds'));

            $data['dglist'] = $query->get();

            Session::put('doclist', $id);
            $queries = DB::getQueryLog();
            $last_query = end($queries);
                    
            $data['docType'] = DocumentTypesModel::orderBy('document_type_order', 'ASC')->get();
            
                $data['stckApp'] = $this->docObj->common_stack();
                $data['deptApp'] = $this->docObj->common_dept();
                $data['doctypeApp'] = $this->docObj->common_type();
                $data['records'] = $this->docObj->common_records();
            /*<--End-->*/

            if(Input::get('page') == 'documentType'):
                /*<--Document Type-->*/
                $data['listName'] = DocumentTypesModel::select('document_type_name As name')->where('document_type_id',$id)->get();
            elseif(Input::get('page') == 'department'):
                /*<--Department-->*/
                Session::put('menuid', '3');
                $data['listName'] = DepartmentsModel::select('department_name As name')->where('department_id',$id)->get();
            elseif(Input::get('page') == 'stack'):
                /*<--Stack-->*/
                Session::put('menuid', '7');
                $data['listName'] = StacksModel::select('stack_name As name')->where('stack_id',$id)->get();
            endif;

            // For Document type
            $data['type_id'] = $id;
            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;
            $data['urlId']                  = $id;

            return View::make('pages/document_types/doclist')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    public function documentsListview()
    {   
        if (Auth::user()) {

            Session::put('menuid', '0');
            $doclistid = Session::get('doclist');
            
            // Get doc_type_colum headers
            $data['doc_type_name']=DB::table('tbl_document_types_columns')
            ->select('document_type_column_name')
            ->orderBy('document_type_column_order')
            ->where('document_type_id',$doclistid)->get();

            // Get document list
            $loggedUsersdepIds = explode(',',Auth::user()->department_id);
            $query = DB::table('tbl_documents');

            if((Input::get('page') == 'documentType') || (Input::get('page') == 'stack')){

                if(Auth::user()->user_role == Session::get('user_role_group_admin') || Auth::user()->user_role == Session::get('user_role_regular_user') || Auth::user()->user_role == Session::get('user_role_private_user')){
                    $count = count($loggedUsersdepIds);
                    if($count == 1):
                        $x=0;
                    else:
                        $x=1;
                    endif;
                    
                    foreach($loggedUsersdepIds as $depId):
                        if($x == 1):      
                            $query->orWhereRaw('('.'FIND_IN_SET('.$depId.',department_id)');
                        elseif($x == $count):    
                            $query->orWhereRaw('FIND_IN_SET('.$depId.',department_id)'.')');
                        else:        
                            $query->orWhereRaw('FIND_IN_SET('.$depId.',department_id)');
                        endif;

                        $x++;
                    endforeach;
                }

                if(Input::get('page') == 'documentType'){
                    $query->whereRaw('FIND_IN_SET('.$doclistid.',document_type_id)');
                }elseif(Input::get('page') == 'stack'){
                    $query->whereRaw('FIND_IN_SET('.$doclistid.',stack_id)');
                }

            }elseif(Input::get('page') == 'department'){
                $query->whereRaw('FIND_IN_SET('.$doclistid.',department_id)')->orderBy('updated_at', 'DESC')->get();
            }
            
            $query->orderBy('updated_at', 'DESC')->get();
            $data['dglist'] = $query->get(); 


            // Expanding dglits with required datas
            foreach($data['dglist'] as $val):
                $document_type_columns_query = DB::table('tbl_documents_columns');
                $document_type_columns_query->select('tbl_documents_columns.*','tbl_document_types_columns.*');
                $document_type_columns_query->leftJoin('tbl_document_types_columns','tbl_document_types_columns.document_type_column_id','=','tbl_documents_columns.document_type_column_id');

                $document_type_columns_query->where('tbl_documents_columns.document_id',$val->document_id);

                if(Input::get('page') == 'documentType'){
                    $document_type_columns_query->where('tbl_document_types_columns.document_type_id',$doclistid);
                }
                
                $document_type_columns_query->orderby('tbl_documents_columns.document_id');
                $document_type_columns_query->orderby('tbl_document_types_columns.document_type_id','ASC');
                $document_type_columns_query->orderby('tbl_document_types_columns.document_type_column_order','ASC');
                $val->document_type_columns = $document_type_columns_query->get();
                
                // Get documentTypes
                $val->documentTypes = DB::table('tbl_document_types')->select(DB::raw('GROUP_CONCAT(document_type_name) AS document_type_names'))->whereIn('document_type_id',explode(',',$val->document_type_id))->get();
                // Get stack
                $val->stacks = DB::table('tbl_stacks')->select(DB::raw('GROUP_CONCAT(stack_name) AS stack_name'))->whereIn('stack_id',explode(',',$val->stack_id))->get();
                // Get Tag words
                $val->tagwords = DB::table('tbl_tagwords')->select(DB::raw('GROUP_CONCAT(tagwords_title) AS tagwords_title'))->whereIn('tagwords_id',explode(',',$val->document_tagwords))->get();
                // Get department
                $val->department = DB::table('tbl_departments')->select(DB::raw('GROUP_CONCAT(department_name) AS department_name'))->whereIn('department_id',explode(',',$val->department_id))->get();
            endforeach;   

            $data['docType'] = DocumentTypesModel::orderBy('document_type_order', 'ASC')->get();
            $data['stacks']  = StacksModel::all();
            $data['depts']   = DepartmentsModel::all();
            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;
            
            $data['docType'] = DocumentTypesModel::orderBy('document_type_order', 'ASC')->get();
            
                $data['stckApp'] = $this->docObj->common_stack();
                $data['deptApp'] = $this->docObj->common_dept();
                $data['doctypeApp'] = $this->docObj->common_type();
                $data['records'] = $this->docObj->common_records();

            if(Input::get('page') == 'documentType'){
                $dtypeid[] = $doclistid;
                $data['colName'] = DocumentTypeColumnModel::select('document_type_column_name')->where('document_type_id',$dtypeid)->get();
            }
            Session::forget('doclist');

            $data['urlId']      = @Input::get('urlId');
            return View::make('pages/document_types/doclistview')->with($data);

        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
  
    public function documentTypeList()
    {
        if (Auth::user()) {
            $user_permission=Auth::user()->user_permission;
            if(stristr($user_permission,"view")){
                 $data['dglist']= DocumentTypesModel::orderBy('document_type_order', 'ASC')->where('is_app',0)->get();;
                $data['docType'] = DocumentTypesModel::orderBy('document_type_order', 'ASC')->where('is_app',0)->get();
            
                $data['stckApp'] = $this->docObj->common_stack();
                $data['deptApp'] = $this->docObj->common_dept();
                $data['doctypeApp'] = $this->docObj->common_type();
                $data['records'] = $this->docObj->common_records();

                $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
                $data['settings_document_no']   = $settings[0]->settings_document_no;
                $data['settings_document_name'] = $settings[0]->settings_document_name;
                return View::make('pages/document_types/list')->with($data);
            }else{
                echo '<div class="alert alert-danger alert-sty">Sorry! You don\'t have the permission</div>';
                exit();
            }            
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    public function checkdoc() // Not in use
    {
        if(Auth::user()){
            $id = Input::get('id');

            if($id==0){
                echo "0";
            }else{
                $isEntry = DocumentsColumnModel::where('document_type_column_id', '=', $id)->get();
                if(count($isEntry) > 0)
                {
                    echo "1";
                }else{
                    $columnData = DocumentTypeColumnModel::where('document_type_column_id', '=', $id);
                    if ($columnData->delete())
                    {    
                        // Save in audits

                        $result = (new AuditsController)->log(Auth::user()->username, 'Document Type Column', 'Delete', 'Document Type Column:');
                        if($result > 0) {
                            echo "0";
                            exit();
                        } else {
                            echo "2";
                            exit;
                        }
                    }                
                }
            }
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    public function save(Request $request, $id)
    { 
        if (Auth::user()) 
        {
            if ($id) 
            {
                $doctypeid = Input::get('edit_val');
                $counttext  =   Input::get('textboxcnt');
                $documentType = DocumentTypesModel:: find($doctypeid);
                $documentType->document_type_name = Input::get('name');
                $documentType->document_type_description = Input::get('description');
                $documentType->document_type_column_no = Input::get('field1');
                $documentType->document_type_column_name = Input::get('field2');
                $documentType->updated_at = date('Y-m-d h:i:s');
                $documentType->document_type_modified_by = Auth::user()->username;
                $documentType->save();
                // get values in array format
                $column_type_name_edit=(Input::get('column_type_name_edit'))?Input::get('column_type_name_edit'):array('');
                $column_type_name_edit_array = array_combine(range(1, count($column_type_name_edit)), $column_type_name_edit);
                $column_type_edit=(Input::get('column_type_edit'))?Input::get('column_type_edit'):array('');
                $column_type_edit_array = array_combine(range(1, count($column_type_edit)), $column_type_edit);
                $column_type_options=(Input::get('hidd_options'))?Input::get('hidd_options'):array('');
                $column_type_options_array=array_combine(range(1, count($column_type_options)), $column_type_options);
                $column_type_options_visibility=(Input::get('hidd_visibility'))?Input::get('hidd_visibility'):array('');
                $column_type_options_visibility_array=array_combine(range(1, count($column_type_options_visibility)), $column_type_options_visibility);
                
                for($i=1;$i<=$counttext;$i++)
                {    
                    $counttextedit = Input::get('doctypecolumn'.$i);//new field                    
                    if($counttextedit==0){
                        $documentTypeColumns = new DocumentTypeColumnModel;
                        $documentTypeColumns->document_type_column_order=$i;
                        $documentTypeColumns->document_type_column_name =$column_type_name_edit_array[$i];
                        $documentTypeColumns->document_type_column_type =$column_type_edit_array[$i];
                        if(Input::get('doc_mandatory'.$i)):
                            $documentTypeColumns->document_type_column_mandatory = '1';
                        else:
                            $documentTypeColumns->document_type_column_mandatory = '0';
                        endif;
                        $documentTypeColumns->document_type_options = $column_type_options_array[$i];
                        $documentTypeColumns->document_type_option_visibility=$column_type_options_visibility_array[$i];
                        $documentTypeColumns->document_type_id = $id;
                        $documentTypeColumns->created_at= date('Y-m-d h:i:s');
                        $documentTypeColumns->document_type_column_created_by=Auth::user()->username;
                        $documentTypeColumns->save();
                        DocumentTypeColumnModel::where('document_type_option_visibility','=',0)->update(['document_type_options'=>NULL]);
                        // For update newly added document column name
                        $tbl_document_types_columns = DB::table('tbl_document_types_columns')->select('document_type_column_id')->where('document_type_id',$id)->get();
                        if(Input::get('doc_mandatory'.$i)):
                            $mandatory = '1';
                        else:
                            $mandatory = '0';
                        endif;
                        if($tbl_document_types_columns){
                            foreach($tbl_document_types_columns as $dtc):
                                $documentTypeColumnIds[] = $dtc->document_type_column_id; 
                                endforeach;
                            //fetch documents with certain column id
                            $tbl_documents_columnsIds = DB::table('tbl_documents_columns')->select('document_id')->whereIn('document_type_column_id',$documentTypeColumnIds)->distinct()->get();
                            //if no columns on tbl_documents_columns
                            if(!$tbl_documents_columnsIds)
                            {
                               $tbl_documents_docs =  DB::table('tbl_documents')->select('document_id')->where('document_type_id',$id)->get();
                            }
                            
                            $tbl_documents_checkout_columnsIds = DB::table('tbl_documents_columns_checkout')->select('document_id')->whereIn('document_type_column_id',$documentTypeColumnIds)->distinct()->get();
                            //if no columns on tbl_documents_chk_columns
                            if(!$tbl_documents_checkout_columnsIds)
                            {
                               $tbl_documents_checkout_docs =  DB::table('tbl_documents_checkout')->select('document_id')->where('document_type_id',$id)->get();
                            }

                            $tbl_documents_temp_columnsIds = DB::table('tbl_temp_documents_columns')->select('document_id')->whereIn('document_type_column_id',$documentTypeColumnIds)->distinct()->get();
                            //if no columns on tbl_documents_temp_columns
                            if(!$tbl_documents_temp_columnsIds)
                            {
                               $tbl_documents_temp_docs =  DB::table('tbl_temp_documents')->select('document_id')->where('document_type_id',$id)->get();
                            }
                            //documents columns update
                            if($tbl_documents_columnsIds)
                            {
                                foreach($tbl_documents_columnsIds as $val):
                                    $value1 = array('document_id'=>$val->document_id,
                                            'document_type_column_id'=>$documentTypeColumns->document_type_column_id,
                                            'document_column_name'=>$column_type_name_edit_array[$i],
                                            'document_column_value'=>NULL,
                                            'document_column_type'=>$column_type_edit_array[$i],
                                            'document_column_mandatory'=>$mandatory);
                                    $document_column_id = DB::table('tbl_documents_columns')->insert($value1);
                                    endforeach;
                            }
                            elseif($tbl_documents_docs)
                            {
                                foreach($tbl_documents_docs as $val):
                                    $value11 = array('document_id'=>$val->document_id,
                                            'document_type_column_id'=>$documentTypeColumns->document_type_column_id,
                                            'document_column_name'=>$column_type_name_edit_array[$i],
                                            'document_column_value'=>NULL,
                                            'document_column_type'=>$column_type_edit_array[$i],
                                            'document_column_mandatory'=>$mandatory);
                                    $document_column_id = DB::table('tbl_documents_columns')->insert($value11);
                                    endforeach;
                            }
                            //checkout columns update
                            if($tbl_documents_checkout_columnsIds)
                            {
                                foreach ($tbl_documents_checkout_columnsIds as $value) {
                                    $value2 =   array('document_id'=>$value->document_id,
                                            'document_type_column_id'=>$documentTypeColumns->document_type_column_id,
                                            'document_column_name'=>$column_type_name_edit_array[$i],
                                            'document_column_value'=>NULL,
                                            'document_column_type'=>$column_type_edit_array[$i],
                                            'document_column_mandatory'=>$mandatory);
                                    $document_checkout_column_id=DB::table('tbl_documents_columns_checkout')->insert($value2);
                                }
                            }
                            elseif($tbl_documents_checkout_docs)
                            {
                                foreach ($tbl_documents_checkout_docs as $value) {
                                    $value22 =   array('document_id'=>$value->document_id,
                                            'document_type_column_id'=>$documentTypeColumns->document_type_column_id,
                                            'document_column_name'=>$column_type_name_edit_array[$i],
                                            'document_column_value'=>NULL,
                                            'document_column_type'=>$column_type_edit_array[$i],
                                            'document_column_mandatory'=>$mandatory);
                                    $document_checkout_column_id=DB::table('tbl_documents_columns_checkout')->insert($value22);
                                }
                            }
                            //temp columns update
                            if($tbl_documents_temp_columnsIds){
                                foreach ($tbl_documents_temp_columnsIds as $value) {
                                    $value3 =   array('document_id'=>$value->document_id,
                                            'document_type_column_id'=>$documentTypeColumns->document_type_column_id,
                                            'document_column_name'=>$column_type_name_edit_array[$i],
                                            'document_column_value'=>NULL,
                                            'document_column_type'=>$column_type_edit_array[$i],
                                            'document_column_mandatory'=>$mandatory);
                                    $document_temp_column_id=DB::table('tbl_temp_documents_columns')->insert($value3);
                                }
                            }
                            elseif($tbl_documents_temp_docs){
                                foreach ($tbl_documents_temp_columnsIds as $value) {
                                    $value33 =   array('document_id'=>$value->document_id,
                                            'document_type_column_id'=>$documentTypeColumns->document_type_column_id,
                                            'document_column_name'=>$column_type_name_edit_array[$i],
                                            'document_column_value'=>NULL,
                                            'document_column_type'=>$column_type_edit_array[$i],
                                            'document_column_mandatory'=>$mandatory);
                                    $document_temp_column_id=DB::table('tbl_temp_documents_columns')->insert($value33);
                                }
                            }
                        }
                    }else{                         
                        $duplicateEntry = DocumentTypeColumnModel::where('document_type_column_id', '=', $counttextedit)->get();
                        if(count($duplicateEntry) > 0)
                        {
                            if(Input::get('doc_mandatory'.$i)):
                                //print_r('one');exit;
                                $mandatory = '1';
                            else:
                                //print_r('zero');exit;
                                $mandatory = '0';
                            endif;
                            DocumentTypeColumnModel::where('document_type_column_id', $counttextedit)->update(array(
                            'document_type_column_name'    =>  $column_type_name_edit_array[$i],
                            'document_type_column_type' =>  $column_type_edit_array[$i],
                            'document_type_column_mandatory'=> $mandatory,
                            'document_type_column_order'=>$i,
                            'document_type_options'=>$column_type_options_array[$i],
                            'document_type_option_visibility'=>$column_type_options_visibility_array[$i],
                            'document_type_id'  => $doctypeid,
                            'updated_at' => date('Y-m-d h:i:s'),
                            'document_type_column_modified_by' => Auth::user()->username));
                            //type column update
                            DocumentTypeColumnModel::where('document_type_option_visibility','=',0)->update(['document_type_options'=>NULL]);
                            //documents column update
                            DocumentsColumnModel::where('document_type_column_id', $counttextedit)->update(array('document_column_name' => $column_type_name_edit_array[$i],'document_column_type' =>  $column_type_edit_array[$i],'document_column_mandatory'=> $mandatory));
                            //checkout column update
                            DocumentsColumnCheckoutModel::where('document_type_column_id', $counttextedit)->update(array('document_column_name' => $column_type_name_edit_array[$i],'document_column_type' =>  $column_type_edit_array[$i],'document_column_mandatory'=> $mandatory));
                            //temp column update
                            TempDocumentsColumnModel::where('document_type_column_id', $counttextedit)->update(array('document_column_name' => $column_type_name_edit_array[$i],'document_column_type' =>  $column_type_edit_array[$i],'document_column_mandatory'=> $mandatory));
                        }
                    }
                }                
                $name = Input::get('name');
                $user = Auth::user()->username;
                $actionMsg = Lang::get('language.update_action_msg');
                $actionDes = $this->docObj->stringReplace($this->actionName,$name,$user,$actionMsg);
                $result = (new AuditsController)->dctypelog(Auth::user()->username,$id,'Document Type', 'Edit',$actionDes);
                if($result > 0) 
                {                    
                    Session::flash('flash_message_edit', "Document type '". Input::get('name') ."' edited successfully.");
                    Session::flash('alert-class', 'alert alert-success alert-sty');
                    return redirect('documentTypes');
                } 
                else 
                {
                    Session::flash('flash_message_edit', "Some issues in log file,contact admin.");
                    Session::flash('alert-class', 'alert alert-danger alert-sty');
                    return redirect('documentTypes');
                }
            }
            else 
            {
                $validators= Validator:: make(
                    $request-> all(),
                    [
                    'name'=> 'required',
                    ]
                    );
                if ($validators->passes()) 
                {
                    $name= Input::get('name');
                    //Duplicate entry checking
                    $duplicateEntry= DocumentTypesModel::where('document_type_name', '=', $name)->get();

                    if(count($duplicateEntry) > 0)
                    {
                        echo '<div class="alert alert-danger alert-sty">'. $name.' is already in our database. </div>';
                        exit();
                    } 
                    else 
                    {
                        //get last entry document_type_order
                        $last_order = DB::table('tbl_document_types')->select('document_type_order')->orderBy('document_type_order','DESC')->first();
                        if($last_order)
                        {
                            $next_order = $last_order->document_type_order+1;
                        }
                        else
                        {
                            $next_order = 1;
                        }
                        $documentType= new DocumentTypesModel;
                        $documentType->document_type_name= $name;
                        $documentType->document_type_description= Input::get('description');
                        $documentType->document_type_created_by= Auth::user()->username;
                        $documentType->document_type_column_no = Input::get('field1');
                        $documentType->document_type_column_name = Input::get('field2');
                        $documentType->created_at= date('Y-m-d h:i:s');
                        $documentType->document_type_order = $next_order;
                        $count_text=Input::get('count-textbox');                        
                        if($documentType->save())
                        {
                            // For audits table
                            $user = Auth::user()->username;
                            $actionMsg = Lang::get('language.save_action_msg');
                            $actionDes = $this->docObj->stringReplace($this->actionName,$name,$user,$actionMsg);
                            (new AuditsController)->dctypelog(Auth::user()->username,$documentType->document_type_id,'Document Type', 'Add',$actionDes);
                            $lastInsertedID = $documentType->document_type_id;
                            $column_type_name=(Input::get('column_type_name'))?Input::get('column_type_name'):array('');
                            $column_type_name_array = array_combine(range(1, count($column_type_name)), $column_type_name);
                            $column_type=(Input::get('column_type'))?Input::get('column_type'):array('');
                            $column_type_array = array_combine(range(1, count($column_type)), $column_type);
                            $column_type_options=(Input::get('hidd_options'))?Input::get('hidd_options'):array('');
                            $column_type_options_array=array_combine(range(1, count($column_type_options)), $column_type_options);
                            $column_type_options_visibility=(Input::get('hidd_visibility'))?Input::get('hidd_visibility'):array('');
                            $column_type_options_visibility_array=array_combine(range(1, count($column_type_options_visibility)), $column_type_options_visibility);
                            for($i=1;$i<=$count_text;$i++)
                            {
                                $documentTypeColumn= new DocumentTypeColumnModel;
                                $documentTypeColumn->document_type_column_order=$i;
                                $documentTypeColumn->document_type_column_name=$column_type_name_array[$i];
                                $documentTypeColumn->document_type_column_type=$column_type_array[$i];
                                if(Input::get('doc_mandatory'.$i)):
                                    $documentTypeColumn->document_type_column_mandatory = '1';
                                else:
                                    $documentTypeColumn->document_type_column_mandatory = '0';
                                endif;
                                $documentTypeColumn->document_type_options = $column_type_options_array[$i];
                                $documentTypeColumn->document_type_option_visibility=$column_type_options_visibility_array[$i];
                                $documentTypeColumn->document_type_id=$lastInsertedID;
                                $documentTypeColumn->created_at= date('Y-m-d h:i:s');
                                $documentTypeColumn->document_type_column_created_by=Auth::user()->username;
                                $documentTypeColumn->save();
                            }

                            echo "<div class='alert alert-success alert-sty'>Document Type  '". $name ."' added successfully.</div>";
                                exit();
                        }
                        else 
                        {
                            echo '<div class="alert alert-danger alert-sty">Sorry you cant add Document Type. </div>';
                            exit();
                        }
                    }                    
                } 
                else 
                {
                    echo '<div class="alert alert-danger alert-sty">Please fill the Document Type correctely.</div>';
                    exit();
                }
            }            
        }
        else 
        {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function edit(Request $request, $id)
    {
        if (Auth::user()) {
            $usrPermisn= explode(",",Auth::user()->user_permission);
            if(in_array("edit", $usrPermisn)){
                $data['docType'] = DocumentTypesModel::orderBy('document_type_order', 'ASC')->get();            
                $data['stckApp'] = $this->docObj->common_stack();
                $data['deptApp'] = $this->docObj->common_dept();
                $data['doctypeApp'] = $this->docObj->common_type();
                $data['records'] = $this->docObj->common_records();
                $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
                $data['settings_document_no']   = $settings[0]->settings_document_no;
                $data['settings_document_name'] = $settings[0]->settings_document_name;
                $count_columns=DocumentTypeColumnModel::where('document_type_id','=',$id)->count();
                $documentTypeColumnData=DocumentTypeColumnModel::select('document_type_column_id','document_type_column_name','document_type_column_type','document_type_column_order','document_type_column_mandatory','document_type_options','document_type_option_visibility')->where('document_type_id','=',$id)->orderBy('document_type_column_order')->get();
                $data['datas']= DocumentTypesModel::find($id);                
                return View::make('pages/document_types/edit')->with($data)->with(['count_columns'=>$count_columns])->with(['results'=>$documentTypeColumnData]);
            }else {
                return redirect('documentTypes');
            }
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    
    public function showdoc(Request $request,$id,$name)
    {
        if(Auth::user()){
            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;
            $result= DocumentsModel::select('document_no','document_name')->where('document_type_id', 'LIKE', '%'.$id.'%')->get();
            $data['name']    = $name;
            $data['results'] = $result;
            return View::make('pages/document_types/showdocuments')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function deleteColumn()
    {
        if (Auth::user()) {
            $id= Input::get('id');
            $documentType= DocumentTypesModel:: find($id);
            $documentTypeColumnData=DocumentTypeColumnModel::where('document_type_id','=',$id);
            if ($documentType->delete() && $documentTypeColumnData->delete())
            {    
                $name = $documentType->document_type_name;
                $user = Auth::user()->username;
                
                // Get delete action message
                $actionMsg = Lang::get('language.delete_action_msg');
                $actionDes = $this->docObj->stringReplace($this->actionName,$name,$user,$actionMsg);

                $result = (new AuditsController)->log(Auth::user()->username, 'Document Type', 'Delete',$actionDes);
                if($result > 0) {
                    echo json_encode("Document type '". $documentType->document_type_name ."' deleted successfully.");
                    exit();
                } else {
                    echo json_encode("Some issues in log file,contact admin");
                    exit;
                }
            }
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function delete()
    {
        if (Auth::user()) {
            $id= Input::get('id');
            $type= Input::get('type');
            $documentType= DocumentTypesModel:: find($id);
            
            $documentTypeColumnData=DocumentTypeColumnModel::where('document_type_id','=',$id);
            
            
            if ($documentType)
            {       
                // Save in audits
                $name = $documentType->document_type_name;
                $user = Auth::user()->username;
        
                // Get delete action message
                $actionMsg = Lang::get('language.delete_action_msg');
                
                if($documentType)
                {
                  $documentType->delete();  
                }

                if($documentTypeColumnData)
                {
                  $documentTypeColumnData->delete();  
                }

                if($type == 'App')
                {
                    $actionDes = $this->docObj->stringReplace('App',$name,$user,$actionMsg);
                    $result = (new AuditsController)->appslog(Auth::user()->username,$id,'Delete',$actionDes);
                    if($result > 0) {
                    echo json_encode("App '". $name ."' deleted successfully.");
                    exit();
                } else {
                    echo json_encode("Some issues in log file,contact admin");
                    exit;
                }
                }        
                else
                {
                    $actionDes = $this->docObj->stringReplace($this->actionName,$name,$user,$actionMsg);
                    $result = (new AuditsController)->dctypelog(Auth::user()->username,Input::get('id'),'Document Type', 'Delete',$actionDes);
                    if($result > 0) {
                    echo json_encode("Document type '". $documentType->document_type_name ."' deleted successfully.");
                    exit();
                } else {
                    echo json_encode("Some issues in log file,contact admin");
                    exit;
                }
                }           
                
                
            }
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    // checking documt has any sub datas while delete
    public function hasChild(){ 
        $exists = DocumentsModel::whereRaw('FIND_IN_SET('.Input::get('id').',document_type_id)')->exists();
        print_r($exists);exit;
    }

    public function duplication()
    {
        if (Auth::user()) {
            $name= Input::get('name');
            $editId= Input::get('editId');
            $oldVal= Input::get('oldVal');
            if($editId > 0)
            {
                $duplicateEntry= DocumentTypesModel::where('document_type_name', '=', $name )
                ->where('document_type_name', '!=', $oldVal)->get();
            }
            else{
                $duplicateEntry= DocumentTypesModel::where('document_type_name', '=', $name )->get();   
            }

            if(count($duplicateEntry) > 0 )
            {                
                echo json_encode('<div class="parsley-errors-list filled" id="dp-inner">'. $name.' is already in our database. </div>');
                exit();
            } else {
                echo json_encode('1');
                exit;
            }
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function documentTypeFieldDelete()
    {
        if (Auth::user()) {
            $id= Input::get('id');
            
                $documentTypeColumnData=DocumentTypeColumnModel::find($id);
                //table type columns delete
                if ($documentTypeColumnData->delete())
                {  
                    //Delete from doccolumns and chkoutcolumns
                    //documents columns
                    DocumentsColumnModel::where('document_type_column_id','=',$id)->delete();
                    //checkout table columns
                    DocumentsColumnCheckoutModel::where('document_type_column_id','=',$id)->delete();
                    //temp table columns
                    TempDocumentsColumnModel::where('document_type_column_id','=',$id)->delete();
                    // Save in audits
                    $user = Auth::user()->username;                    
                    // Get delete action message
                    $actionMsg = Lang::get('language.delete_action_msg');
                    $actionDes = $this->docObj->stringReplace('Document Type column',$documentTypeColumnData->document_type_column_name,$user,$actionMsg);
                    $result = (new AuditsController)->log(Auth::user()->username, 'Document Type Column', 'Delete',$actionDes);
                    if($result > 0) {
                        echo ("Document type column '". $documentTypeColumnData->document_type_column_name ."' deleted successfully.");
                        exit();
                    } else {
                        echo ("Some issues in log file,contact admin");
                        exit;
                    }
                }
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function optionDelete()
    {
        if(Auth::user()){
            $option_del=Input::get('option');
            $col_id=Input::get('col_id');
            $type_id=Input::get('id');
            $option_exist_in_doc=DocumentsColumnModel::where('document_type_column_id','=',$col_id)->where('document_column_value','=',$option_del)->get();
            $option_exist_in_chkout=DocumentsColumnCheckoutModel::where('document_type_column_id','=',$col_id)->where('document_column_value','=',$option_del)->get();
            if(($option_exist_in_doc->count()>0) || ($option_exist_in_chkout->count()>0))
            {
                echo "1";
                exit();
            }
            else{
                echo "0";
                exit();
            }
        }
        else{
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    public function optionChange()
    {
        if(Auth::user()){
            $option_change=Input::get('option');
            $type_id=Input::get('id');
            $col_id=Input::get('col_id');
            $option_exist_in_doc=DocumentsColumnModel::where('document_type_column_id','=',$col_id)->where('document_column_name','=',$option_change)->where('document_column_value','!=','')->get();
            $option_exist_in_chkout=DocumentsColumnCheckoutModel::where('document_type_column_id','=',$col_id)->where('document_column_name','=',$option_change)->where('document_column_value','!=','')->get();
            if(($option_exist_in_doc->count()>0) || ($option_exist_in_chkout->count()>0))
            {
                echo "1";
                exit();
            }
            else{
                echo "0";
                exit();
            }
        }
        else{
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function rowReorder(Request $request)
    {
        $newval = Input::get('newval');
        //echo 'new:';print_r($newval);
        $oldval = Input::get('oldval');
        //echo 'old:';print_r($oldval);
        //$name = Input::get('name');
        $id = Input::get('id');
        //echo 'name:';print_r($name);
        //update row order
        $count = count($newval);

        if($count)
        {
            for($i=0;$i<$count;$i++) {
                DB::table('tbl_document_types')->where('document_type_id',$id[$i])->update(['document_type_order'=>$newval[$i]]);
            }
            
        }
    }
}