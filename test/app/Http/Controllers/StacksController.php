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
use App\StacksModel as StacksModel;
use App\DocumentsModel as DocumentsModel;
use App\StacksColumnModel as StacksColumnModel;
use App\DocumentsColumnCheckoutModel as DocumentsColumnCheckoutModel;
use App\DocumentsColumnModel as DocumentsColumnModel;
use App\DocumentsStacksColumnModel as DocumentsStacksColumnModel;
use App\DocumentsStacksColumnCheckoutModel as DocumentsStacksColumnCheckoutModel;
use App\DepartmentsModel as DepartmentsModel;
use App\TempDocumentsColumnModel as TempDocumentsColumnModel;
use App\TempDocumentsStacksColumnModel as TempDocumentsStacksColumnModel;
use DB;
use Lang;

class StacksController extends Controller
{
    public function __construct()
    {
        
        Session::put('menuid', '7');
        $this->middleware(['auth', 'user.status']);
        // Set common variable
        $this->actionName = 'Stacks';
        $this->docObj     = new Common(); // class defined in app/mylibs
    }

    public function index()
    { 
        if (Auth::user()) {
            Session::put('menuid', '7');
            $data['docType'] = StacksModel::all();            
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            
            return View::make('pages/stacks/index')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    public function documentsList(Request $request,$id)
    {   
        if (Auth::user()) {
            Session::put('menuid', '7');
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

            return View::make('pages/stacks/doclist')->with($data);
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
            return View::make('pages/stacks/doclistview')->with($data);

        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
  
    public function stacksList()
    {
        if (Auth::user()) {
            $user_permission=Auth::user()->user_permission;
            if(stristr($user_permission,"view")){
                $data['dglist']= StacksModel::all();
                $data['docType'] = StacksModel::all();
            
                $data['stckApp'] = $this->docObj->common_stack();
                $data['deptApp'] = $this->docObj->common_dept();
                $data['doctypeApp'] = $this->docObj->common_type();
                $data['records'] = $this->docObj->common_records();
                return View::make('pages/stacks/list')->with($data);
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
                //$counttext  =   Input::get('textboxcnt');
                $stacks = StacksModel:: find($doctypeid);
                $stacks->stack_name = Input::get('name');
                $stacks->stack_description = Input::get('description');
                $stacks->updated_at = date('Y-m-d h:i:s');
                $stacks->stack_modified_by = Auth::user()->username;
                $stacks->save();
                // get values in array format
                // $column_type_name_edit=(Input::get('column_type_name_edit'))?Input::get('column_type_name_edit'):array('');
                // $column_type_name_edit_array = array_combine(range(1, count($column_type_name_edit)), $column_type_name_edit);
                // $column_type_edit=(Input::get('column_type_edit'))?Input::get('column_type_edit'):array('');
                // $column_type_edit_array = array_combine(range(1, count($column_type_edit)), $column_type_edit);
                // $column_type_options=(Input::get('hidd_options'))?Input::get('hidd_options'):array('');
                // $column_type_options_array=array_combine(range(1, count($column_type_options)), $column_type_options);
                // $column_type_options_visibility=(Input::get('hidd_visibility'))?Input::get('hidd_visibility'):array('');
                // $column_type_options_visibility_array=array_combine(range(1, count($column_type_options_visibility)), $column_type_options_visibility);
                
                // for($i=1;$i<=$counttext;$i++)
                // {    
                //     $counttextedit = Input::get('doctypecolumn'.$i);//new field                    
                //     if($counttextedit==0){
                //         $stackColumns = new StacksColumnModel;
                //         $stackColumns->stack_column_order=$i;
                //         $stackColumns->stack_column_name =$column_type_name_edit_array[$i];
                //         $stackColumns->stack_column_type =$column_type_edit_array[$i];
                //         if(Input::get('doc_mandatory'.$i)):
                //             $stackColumns->stack_column_mandatory = '1';
                //         else:
                //             $stackColumns->stack_column_mandatory = '0';
                //         endif;
                //         $stackColumns->stack_options = $column_type_options_array[$i];
                //         $stackColumns->stack_option_visibility=$column_type_options_visibility_array[$i];
                //         $stackColumns->stack_id = $id;
                //         $stackColumns->created_at= date('Y-m-d h:i:s');
                //         $stackColumns->stack_column_created_by=Auth::user()->username;
                //         $stackColumns->save();
                //         StacksColumnModel::where('stack_option_visibility','=',0)->update(['stack_options'=>NULL]);
                //         // For update newly added document column name
                //         $tbl_stack_columns = DB::table('tbl_stack_columns')->select('stack_column_id')->where('stack_id',$id)->get();
                //         if(Input::get('doc_mandatory'.$i)):
                //             $mandatory = '1';
                //         else:
                //             $mandatory = '0';
                //         endif;
                //         if($tbl_stack_columns){
                //             foreach($tbl_stack_columns as $dtc):
                //                 $stackColumnIds[] = $dtc->stack_column_id; 
                //                 endforeach;
                //             $tbl_documents_columnsIds = DB::table('tbl_documents_stack_columns')->select('document_id')->whereIn('stack_column_id',$stackColumnIds)->distinct()->get();
                //             $tbl_documents_checkout_columnsIds = DB::table('tbl_documents_stack_columns_checkout')->select('document_id')->whereIn('stack_column_id',$stackColumnIds)->distinct()->get();
                //             $tbl_documents_temp_columnsIds = DB::table('tbl_temp_documents_stack_columns')->select('document_id')->whereIn('stack_column_id',$stackColumnIds)->distinct()->get();
                //             if($tbl_documents_columnsIds):
                //                 foreach($tbl_documents_columnsIds as $val):
                //                     $value1 = array('document_id'=>$val->document_id,
                //                             'stack_column_id'=>$stackColumns->stack_column_id,
                //                             'document_stack_column_name'=>$column_type_name_edit_array[$i],
                //                             'document_stack_column_value'=>NULL,
                //                             'document_stack_column_type'=>$column_type_edit_array[$i],
                //                             'document_stack_column_mandatory'=>$mandatory);
                //                     $document_column_id = DB::table('tbl_documents_stack_columns')->insert($value1);
                //                     endforeach;
                //             endif;
                //             //checkout columns update
                //             if($tbl_documents_checkout_columnsIds):
                //                 foreach ($tbl_documents_checkout_columnsIds as $value) {
                //                     $value2 =   array('document_id'=>$value->document_id,
                //                             'stack_column_id'=>$stackColumns->stack_column_id,
                //                             'document_stack_column_name'=>$column_type_name_edit_array[$i],
                //                             'document_stack_column_value'=>NULL,
                //                             'document_stack_column_type'=>$column_type_edit_array[$i],
                //                             'document_stack_column_mandatory'=>$mandatory);
                //                     $document_checkout_column_id=DB::table('tbl_documents_stack_columns_checkout')->insert($value2);
                //                 }
                //             endif;
                //             //temp columns update
                //             if($tbl_documents_temp_columnsIds):
                //                 foreach ($tbl_documents_temp_columnsIds as $value) {
                //                     $value3 =   array('document_id'=>$value->document_id,
                //                             'stack_column_id'=>$stackColumns->stack_column_id,
                //                             'document_stack_column_name'=>$column_type_name_edit_array[$i],
                //                             'document_stack_column_value'=>NULL,
                //                             'document_stack_column_type'=>$column_type_edit_array[$i],
                //                             'document_stack_column_mandatory'=>$mandatory);
                //                     $document_temp_column_id=DB::table('tbl_temp_documents_stack_columns')->insert($value3);
                //                 }
                //             endif;
                //         }
                //     }else{                         
                //         $duplicateEntry = StacksColumnModel::where('stack_column_id', '=', $counttextedit)->get();
                //         if(count($duplicateEntry) > 0)
                //         {
                //             if(Input::get('doc_mandatory'.$i)):
                //                 //print_r('one');exit;
                //                 $mandatory = '1';
                //             else:
                //                 //print_r('zero');exit;
                //                 $mandatory = '0';
                //             endif;
                //             StacksColumnModel::where('stack_column_id', $counttextedit)->update(array(
                //             'stack_column_name'    =>  $column_type_name_edit_array[$i],
                //             'stack_column_type' =>  $column_type_edit_array[$i],
                //             'stack_column_mandatory'=> $mandatory,
                //             'stack_column_order'=>$i,
                //             'stack_options'=>$column_type_options_array[$i],
                //             'stack_option_visibility'=>$column_type_options_visibility_array[$i],
                //             'stack_id'  => $doctypeid,
                //             'updated_at' => date('Y-m-d h:i:s'),
                //             'stack_column_modified_by' => Auth::user()->username));
                //             StacksColumnModel::where('stack_option_visibility','=',0)->update(['stack_options'=>NULL]);
                //             DocumentsStacksColumnModel::where('stack_column_id', $counttextedit)->update(array('document_stack_column_name' => $column_type_name_edit_array[$i]));
                //             DocumentsStacksColumnCheckoutModel::where('stack_column_id', $counttextedit)->update(array('document_stack_column_name' => $column_type_name_edit_array[$i]));
                //             TempDocumentsStacksColumnModel::where('stack_column_id', $counttextedit)->update(array('document_stack_column_name' => $column_type_name_edit_array[$i]));
                //         }
                //     }
                // }                
                $name = Input::get('name');
                $user = Auth::user()->username;
                $actionMsg = Lang::get('language.update_action_msg');
                $actionDes = $this->docObj->stringReplace($this->actionName,$name,$user,$actionMsg);
                $result = (new AuditsController)->dctypelog(Auth::user()->username,$id,'Stack', 'Edit',$actionDes);
                if($result > 0) 
                {                    
                    Session::flash('flash_message_edit', "Stack '". Input::get('name') ."' edited successfully.");
                    Session::flash('alert-class', 'alert alert-success alert-sty');
                    return redirect('stacks');
                } 
                else 
                {
                    Session::flash('flash_message_edit', "Some issues in log file,contact admin.");
                    Session::flash('alert-class', 'alert alert-danger alert-sty');
                    return redirect('stacks');
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
                    $duplicateEntry= StacksModel::where('stack_name', '=', $name)->get();

                    if(count($duplicateEntry) > 0)
                    {
                        echo '<div class="alert alert-danger alert-sty">'. $name.' is already in our database. </div>';
                        exit();
                    } 
                    else 
                    {
                        //get last entry department_order
                        $last_order = DB::table('tbl_stacks')->select('stack_order')->orderBy('stack_order','DESC')->first();
                        if($last_order)
                        {
                            $next_order = $last_order->stack_order+1;
                        }
                        else
                        {
                            $next_order = 1;
                        }
                        $stack= new StacksModel;
                        $stack->stack_name= $name;
                        $stack->stack_description= Input::get('description');
                        $stack->stack_created_by= Auth::user()->username;   
                        $stack->stack_order=$next_order;                    
                        $stack->created_at= date('Y-m-d h:i:s');
                        $count_text=Input::get('count-textbox');                        
                        if($stack->save())
                        {
                            // For audits table
                            $user = Auth::user()->username;
                            $actionMsg = Lang::get('language.save_action_msg');
                            $actionDes = $this->docObj->stringReplace($this->actionName,$name,$user,$actionMsg);
                            (new AuditsController)->stacklog(Auth::user()->username,$stack->stack_id,'Stack', 'Add',$actionDes);
                            $lastInsertedID = $stack->stack_id;

                            // $column_type_name=Input::get('column_type_name');
                            // $column_type_name_array = array_combine(range(1, count($column_type_name)), $column_type_name);
                            // $column_type=Input::get('column_type');
                            // $column_type_array = array_combine(range(1, count($column_type)), $column_type);
                            // $column_type_options=Input::get('hidd_options');
                            // $column_type_options_array=array_combine(range(1, count($column_type_options)), $column_type_options);
                            // $column_type_options_visibility=Input::get('hidd_visibility');
                            // $column_type_options_visibility_array=array_combine(range(1, count($column_type_options_visibility)), $column_type_options_visibility);
                            // for($i=1;$i<=$count_text;$i++)
                            // {
                            //     $stackColumn= new StacksColumnModel;
                            //     $stackColumn->stack_column_order=$i;
                            //     $stackColumn->stack_column_name=$column_type_name_array[$i];
                            //     $stackColumn->stack_column_type=$column_type_array[$i];
                            //     if(Input::get('doc_mandatory'.$i)):
                            //         $stackColumn->stack_column_mandatory = '1';
                            //     else:
                            //         $stackColumn->stack_column_mandatory = '0';
                            //     endif;
                            //     $stackColumn->stack_options = $column_type_options_array[$i];
                            //     $stackColumn->stack_option_visibility=$column_type_options_visibility_array[$i];
                            //     $stackColumn->stack_id=$lastInsertedID;
                            //     $stackColumn->created_at= date('Y-m-d h:i:s');
                            //     $stackColumn->stack_column_created_by=Auth::user()->username;
                            //     $stackColumn->save();
                            // }
                            echo "<div class='alert alert-success alert-sty'>Stack  '". $name ."' added successfully.</div>";
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
                $data['docType'] = StacksModel::all();            
                $data['stckApp'] = $this->docObj->common_stack();
                $data['deptApp'] = $this->docObj->common_dept();
                $data['doctypeApp'] = $this->docObj->common_type();
                $data['records'] = $this->docObj->common_records();
                //$count_columns=StacksColumnModel::where('stack_id','=',$id)->count();
                //$documentTypeColumnData=StacksColumnModel::select('stack_column_id','stack_column_name','stack_column_type','stack_column_order','stack_column_mandatory','stack_options','stack_option_visibility')->where('stack_id','=',$id)->orderBy('stack_column_order')->get();
                $data['datas']= StacksModel::find($id);                
                return View::make('pages/stacks/edit')->with($data);
            }else {
                return redirect('documentTypes');
            }
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function documentTypeAjaxEdit($id){
        $data1 = DB::table('tbl_document_types')->where('document_type_id',$id)->select('document_type_id','document_type_name','document_type_description')->get();
        $data2 = DB::table('tbl_document_types_columns')->where('document_type_id',$id)->select('document_type_column_id','document_type_id','document_type_column_name','document_type_column_type')->get();
        echo '<form method="POST" action="http://localhost:8000/documentTypeSave/'.$id.'" accept-charset="UTF-8" class="form-horizontal" name="documentTypeAddForm" id="documentTypeAddForm" data-parsley-validate="" novalidate=""><input name="_token" type="hidden" value="5TGrkjjY0Z5PsmcDLJvatmaRMSERRUotUo9KkHwb"><div class="form-group"><label for="Doc Type: " class="col-sm-3 control-label">Doc Type: </label><div class="col-sm-8"><input class="form-control" id="name_edi" title="Document Type " placeholder="Document Type" required="" data-parsley-required-message="Document type is required" onchange="duplication()" autofocus="autofocus" name="name" type="text" value='.$data1[0]->document_type_name.'><div id="dp_edi"><span id="dp_wrn_edi" style="display:none;"><i class="fa fa-refresh fa-spin fa-1x fa-fw"></i><span class="">Please wait...</span></span></div></div></div><div class="form-group"><label for="Description: " class="col-sm-3 control-label">Description: </label><div class="col-sm-8"><textarea class="form-control" id="description" title="Description" placeholder="Description" required="" data-parsley-required-message="Description is required" name="description" cols="50" rows="10">'.$data1[0]->document_type_description.'</textarea><span class="dms_error"></span></div></div><div id="TextBoxesGroupEdit"><div class="form-group"><div class="col-sm-5"><label for="Index Field: " class="control-label">Index Field: </label></div><div class="col-sm-4"><label for="Field Type: " class="control-label">Field Type: </label></div><div class="col-sm-1"><label for="Action: " class="control-label">Action: </label></div></div>';
        $count=0;
        foreach($data2 as $dat):
            $count++;
            echo '<div id="TextBoxDivEdit'.$count.'"><div class="form-group"><div class="col-sm-5"><input type="hidden" name="doctypecolumn1" id="doctypecolumn1" value="4"><input type="text" id="textboxedit1" name="column_type_name_edit1" value="Purchase Orders No" class="form-control" title="Field Name" placeholder="Field Name" data-parsley-required-message="Field Name is required" data-parsley-trigger="change focusout" required=""></div><div class="col-sm-4"><select class="form-control" name="column_type_edit1" id="selectedit1"><option value="Date">Date</option><option value="Integer">Integer</option><option value="String" selected="selected">String</option></select></div><div class="col-sm-1"><span onclick="deleteDocColType(1,4);" id="dctCntedit1" style="cursor:pointer;"><i class="fa fa-trash fa-lg" style="font-size:20px; margin-top:7px;"></i></span></div></div></div></div>';
        endforeach;
        echo '<div class="form-group"><label class="col-sm-4 control-label"></label><input type="hidden" name="textboxcnt" id="textboxcnt" value="1"> <input type="hidden" id="edit_val" name="edit_val" value="5"><div class="col-sm-8"><input class="btn btn-primary" id="saveEdi" type="submit" value="Save">&nbsp;&nbsp;<button class="btn btn-primary btn-danger" data-dismiss="modal" id="cnEdi" type="button">Cancel</button>&nbsp;&nbsp;<button class="btn btn-primary" id="addButtonEdit" value="addButtonEdit" type="button">Add New Index Field</button></div></div></form>';        
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
            return View::make('pages/stacks/showdocuments')->with($data);
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
            $stacks= StacksModel:: find($id);
            //$stackColumnData=StacksColumnModel::where('stack_id','=',$id);
            if ($stacks->delete())
            {       
                // Save in audits
                $name = $stacks->stack_name;
                $user = Auth::user()->username;
        
                // Get delete action message
                $actionMsg = Lang::get('language.delete_action_msg');
                $actionDes = $this->docObj->stringReplace($this->actionName,$name,$user,$actionMsg);                   
                $result = (new AuditsController)->dctypelog(Auth::user()->username,Input::get('id'),'Stacks', 'Delete',$actionDes);
                if($result > 0) {
                    echo json_encode("Stack '". $stacks->stack_name ."' deleted successfully.");
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
                $duplicateEntry= StacksModel::where('stack_name', '=', $name )
                ->where('stack_name', '!=', $oldVal)->get();
            }
            else{
                $duplicateEntry= StacksModel::where('stack_name', '=', $name )->get();   
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
    public function stackFieldDelete()
    {
        if (Auth::user()) {
            $id= Input::get('id');
            
            // $exist_in_doc=DocumentsColumnModel::where('document_type_column_id','=',$id)->where('document_column_value','!=',NULL)->get();
            // $exist_in_checkout=DocumentsColumnCheckoutModel::where('document_type_column_id','=',$id)->where('document_column_value','!=',NULL)->get();
            // if(($exist_in_doc->count()>0) || ($exist_in_checkout->count()>0))
            // {
            //     echo "1";
            //     exit();
            // }
            // else
            // {    
                $stackColumnData=StacksColumnModel::find($id);
                if ($stackColumnData->delete())
                {  
                    //Delete from doccolumns and chkoutcolumns
                    DocumentsStacksColumnModel::where('stack_column_id','=',$id)->delete();
                    DocumentsStacksColumnCheckoutModel::where('stack_column_id','=',$id)->delete();
                    // Save in audits
                    $user = Auth::user()->username;                    
                    // Get delete action message
                    $actionMsg = Lang::get('language.delete_action_msg');
                    $actionDes = $this->docObj->stringReplace('Stack column',$stackColumnData->stack_column_name,$user,$actionMsg);
                    $result = (new AuditsController)->log(Auth::user()->username, 'Stack Column', 'Delete',$actionDes);
                    if($result > 0) {
                        echo ("Stack column '". $stackColumnData->stack_column_name ."' deleted successfully.");
                        exit();
                    } else {
                        echo ("Some issues in log file,contact admin");
                        exit;
                    }
                }
            //}
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
            $option_exist_in_doc=DocumentsStacksColumnModel::where('stack_column_id','=',$col_id)->where('document_stack_column_value','=',$option_del)->get();
            $option_exist_in_chkout=DocumentsStacksColumnCheckoutModel::where('stack_column_id','=',$col_id)->where('document_stack_column_value','=',$option_del)->get();
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
            $option_exist_in_doc=DocumentsStacksColumnModel::where('stack_column_id','=',$col_id)->where('document_stack_column_name','=',$option_change)->where('document_stack_column_value','!=','')->get();
            $option_exist_in_chkout=DocumentsStacksColumnCheckoutModel::where('stack_column_id','=',$col_id)->where('document_stack_column_name','=',$option_change)->where('document_stack_column_value','!=','')->get();
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
     public function rowReorderStack(Request $request)
    {
        $newval = Input::get('newval');
        $oldval = Input::get('oldval');
        $name = Input::get('name');
        $id = Input::get('id');
        //update row order
        $count = count($newval);
        if($count)
        {
            for($i=0;$i<$count;$i++) {
                DB::table('tbl_stacks')->where('stack_id',$id[$i])->update(['stack_order'=>$newval[$i]]);
                echo $name[$i].' new position '.$newval[$i];echo "</br>";
            }
            
        }
    }
}