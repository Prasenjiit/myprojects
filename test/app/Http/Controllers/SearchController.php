<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request as CookieRequest;
use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;
use Input;
use URL;
use Session;
use Config;
use DB;
use Lang;
use Response;
use File;
use App\Fpdf\FPDF;
class SearchController extends Controller
{
    public function __construct()
    {
        

    }
    //Advance search document submit 
    public function searchadvncdoc(Request $request)
    {   
        if (Auth::user()) 
        {  
            // Fetching settings_no and name label
            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;
            
            //For save search criteria
            if(@Input::get('search_criteria_id')){
                $criteria_name = DB::table('tbl_search_criteria')->select('criteria_name')->where('search_criteria_id',Input::get('search_criteria_id'))->get();
                Session::put('search_criteria_name',$criteria_name[0]->criteria_name);
            }else{
                Session::forget('search_criteria_name');
            }

            DB::enableQueryLog();
            $table = 'tbl_documents';
            //old query
            //$query = DB::table('tbl_documents')->select('tbl_documents.*','tbl_documents_columns.document_id as doc_val_document_id','tbl_documents_columns.document_column_value',DB::RAW('GROUP_CONCAT(document_column_name) AS document_column_names'),DB::RAW('GROUP_CONCAT(document_column_value) AS document_column_values'))->leftJoin('tbl_documents_columns','tbl_documents_columns.document_id','=','tbl_documents.document_id');

            $query = DB::table($table)
            ->leftjoin('tbl_documents_columns',$table.'.'.'document_id','=','tbl_documents_columns.document_id')
            ->leftjoin('tbl_departments',$table.'.'.'department_id','=','tbl_departments.department_id')
            ->leftjoin('tbl_stacks',$table.'.'.'stack_id','=','tbl_stacks.stack_id')
            ->leftjoin('tbl_document_types',$table.'.'.'document_type_id','=','tbl_document_types.document_type_id');
            $select ="
            tbl_departments.department_id,
            tbl_stacks.stack_id,
            $table.document_type_id,
            tbl_document_types.document_type_name as document_type_name,
            $table.document_id,
            $table.document_no as document_no,
            $table.document_name as document_name,
            $table.department_id,
            tbl_departments.department_name as department_name,
            $table.stack_id,
            tbl_stacks.stack_name as stack_name,
            $table.document_ownership,
            $table.created_at,
            $table.updated_at,
            $table.document_expiry_date,
            $table.document_status,
            $table.parent_id,
            $table.document_version_no,
            $table.document_file_name,
            $table.document_modified_by,
            $table.document_encrypt_status,
            $table.document_encrypted_by,
            tbl_documents_columns.document_column_name,
            tbl_documents_columns.document_column_value"
            ;
            //check user = private user, fetch only the docs of that user
            if(Auth::user()->user_role == Session::get("user_role_private_user"))
            {
                $query->where('tbl_documents.document_ownership',Auth::user()->username);
            }
            // CHECK WETHER WHERE IS "AND" or "OR".
            if(Input::get('search_option') == 'AND')
            {
                $queryWhere    = 'where';
                $queryWhereIn  = 'whereIn';
                $queryWhereRaw = 'whereRaw';
            }
            else
            {
                // search_option="OR"
                $queryWhere    = 'orWhere';
                $queryWhereIn  = 'orWhereIn';
                $queryWhereRaw = 'orWhereRaw';
            }
            
            //////////////// KEYWORD SEARCH ///////////////////////
            if(Input::get('keywrd_srchtxt')):   
                // Search by users
                if(Auth::user()->user_role != Session::get('user_role_super_admin'))
                {
                    $department_id = explode(',',Auth::user()->department_id);
                    $count         = count($department_id);
                    if($count == 1):
                        $x=0;
                    else:
                        $x=1;
                    endif;                    
                    foreach($department_id as $depid):
                       if($x == 1):
                            $query->$queryWhereRaw('('.'FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        elseif($x == $count):
                            $query->orWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)'.')');
                        else:
                            $query->$queryWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        endif;
                        $x++;
                    endforeach;
                    $query->where('tbl_documents.document_no','LIKE','%'.Input::get('keywrd_srchtxt').'%');
                    $query->orWhere('tbl_documents.document_name','LIKE','%'.Input::get('keywrd_srchtxt').'%');
                    $query->orWhere('tbl_documents.document_no','=',Input::get('keywrd_srchtxt'));
                }
                else//search by super admin
                {
                    $query->where('tbl_documents_columns.document_column_value','LIKE','%'.Input::get('keywrd_srchtxt').'%');
                    $query->orWhere('tbl_documents.document_name','LIKE','%'.Input::get('keywrd_srchtxt').'%');
                    $query->orWhere('tbl_documents.document_no','=',Input::get('keywrd_srchtxt'));
                }
            endif;
            //////////////// END KEYWORD SEARCH ///////////////////////

            // If document no exists
            if(Input::get('docno')):

                // Search by users department list
                if(Auth::user()->user_role != Session::get('user_role_super_admin'))
                {
                    $department_id = explode(',',Auth::user()->department_id);
                    $count         = count($department_id);
                    if($count == 1):
                        $x=0;
                    else:
                        $x=1;
                    endif;
                    
                    foreach($department_id as $depid):
                       if($x == 1):
                            $query->$queryWhereRaw('('.'FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        elseif($x == $count):
                            $query->orWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)'.')');
                        else:
                            $query->$queryWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        endif;
                        $x++;
                    endforeach;
                    $query->where('tbl_documents.document_no','LIKE','%'.Input::get('docno').'%');
                }
                else
                {
                    $query->$queryWhere('tbl_documents.document_no','LIKE','%'.Input::get('docno').'%');
                }
            endif;

            // If document name exists
            if(Input::get('docname')):

                // Search by users department list
                if(Auth::user()->user_role != Session::get('user_role_super_admin'))
                {
                    $department_id = explode(',',Auth::user()->department_id);
                    $count         = count($department_id);
                    if($count == 1):
                        $x=0;
                    else:
                        $x=1;
                    endif;
                    
                    foreach($department_id as $depid):
                       if($x == 1):
                            $query->$queryWhereRaw('('.'FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        elseif($x == $count):
                            $query->orWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)'.')');
                        else:
                            $query->$queryWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        endif;
                        $x++;
                    endforeach;

                    $query->where('tbl_documents.document_name','LIKE','%'.Input::get('docname').'%');
                }
                else
                {
                    $query->$queryWhere('tbl_documents.document_name','LIKE','%'.Input::get('docname').'%');
                }
            endif;
     
            // If ownership exists
            if(Input::get('ownership')):

                // Search by users department list
                if(Auth::user()->user_role != Session::get('user_role_super_admin'))
                {
                    $department_id = explode(',',Auth::user()->department_id);
                    $count         = count($department_id);
                    if($count == 1):
                        $x=0;
                    else:
                        $x=1;
                    endif;
                    
                    foreach($department_id as $depid):
                       if($x == 1):
                            $query->$queryWhereRaw('('.'FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        elseif($x == $count):
                            $query->orWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)'.')');
                        else:
                            $query->$queryWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        endif;
                        $x++;
                    endforeach;

                    $query->whereIn('tbl_documents.document_ownership',Input::get('ownership'));

                }
                else
                {
                    $query->$queryWhereIn('tbl_documents.document_ownership',Input::get('ownership'));
                }

                
                foreach(Input::get('ownership') as $owe):
                    $oweIds = DB::table('tbl_users')->select('id')->where('username',$owe)->get();
                    $ownerIds[] = $oweIds['0']->id;
                    $owes[]  = $owe;
                endforeach;  
            endif;

            // If created by exists
            if(Input::get('created_by')):
                // Search by users department list
                if(Auth::user()->user_role != Session::get('user_role_super_admin'))
                {
                    $department_id = explode(',',Auth::user()->department_id);
                    $count         = count($department_id);
                    if($count == 1):
                        $x=0;
                    else:
                        $x=1;
                    endif;
                    
                    foreach($department_id as $depid):
                       if($x == 1):
                            $query->$queryWhereRaw('('.'FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        elseif($x == $count):
                            $query->orWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)'.')');
                        else:
                            $query->$queryWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        endif;
                        $x++;
                    endforeach;

                    $query->whereIn('tbl_documents.document_created_by',Input::get('created_by'));
                }
                else
                {
                    $query->$queryWhereIn('tbl_documents.document_created_by',Input::get('created_by'));
                }

                
                foreach(Input::get('created_by') as $created_by_owe):
                    $created_by_oweIds = DB::table('tbl_users')->select('id')->where('username',$created_by_owe)->get();
                    $created_by_ownerIds[] = $created_by_oweIds['0']->id;
                    $created_by_owes[]  = $created_by_owe;
                endforeach; 
            endif;

            // If updated by exists
            if(Input::get('updated_by')):

                // Search by users department list
                if(Auth::user()->user_role != Session::get('user_role_super_admin'))
                {
                    $department_id = explode(',',Auth::user()->department_id);
                    $count         = count($department_id);
                    if($count == 1):
                        $x=0;
                    else:
                        $x=1;
                    endif;
                    
                    foreach($department_id as $depid):
                       if($x == 1):
                            $query->$queryWhereRaw('('.'FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        elseif($x == $count):
                            $query->orWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)'.')');
                        else:
                            $query->$queryWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        endif;
                        $x++;
                    endforeach;

                    $query->whereIn('tbl_documents.document_modified_by',Input::get('updated_by'));
                }
                else
                {
                    $query->$queryWhereIn('tbl_documents.document_modified_by',Input::get('updated_by'));
                }

                
                foreach(Input::get('updated_by') as $updated_by_owe):
                    $updated_by_oweIds = DB::table('tbl_users')->select('id')->where('username',$updated_by_owe)->get();
                    $updated_by_ownerIds[] = $updated_by_oweIds['0']->id;
                    $updated_by_owes[]  = $updated_by_owe;
                endforeach;
            endif;

            // If department exists
            if(Input::get('department')):

                foreach(Input::get('department') as $depid):
                    $query->$queryWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                    $departments[] = DB::table('tbl_departments')->select('department_name')->where('department_id',$depid)->get();
                endforeach;
            endif;

            // If document type id exists
            if(Input::get('doctypeid')):

                // Search by users department list
                if(Auth::user()->user_role != Session::get('user_role_super_admin'))
                {
                    $department_id = explode(',',Auth::user()->department_id);
                    $count         = count($department_id);
                    if($count == 1):
                        $x=0;
                    else:
                        $x=1;
                    endif;
                    
                    foreach($department_id as $depid):
                       if($x == 1):
                            $query->$queryWhereRaw('('.'FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        elseif($x == $count):
                            $query->orWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)'.')');
                        else:
                            $query->$queryWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        endif;
                        $x++;
                    endforeach;
                    // search with document type
                    //$query->whereRaw('FIND_IN_SET('.Input::get('doctypeid').',tbl_documents.document_type_id)');
                    $query->where('tbl_documents.document_type_id',Input::get('doctypeid'));
                }
                else
                {
                    // search with document type
                    //$query->$queryWhereRaw('FIND_IN_SET('.Input::get('doctypeid').',tbl_documents.document_type_id)');
                    $query->$queryWhere('tbl_documents.document_type_id',Input::get('doctypeid'));
                }
                $documentTypeNames = DB::table('tbl_document_types')->select('document_type_name','document_type_column_no','document_type_column_name')->where('document_type_id',Input::get('doctypeid'))->get();
                //column names fetches
            	$data['col_names'] = DB::table('tbl_document_types_columns')->select('document_type_column_name')->where('document_type_id',Input::get('doctypeid'))->orderBy('document_type_column_order','ASC')->get();
            else:
                // Distory session
                Session::forget('search_document_type_name');
                Session::forget('doctypeids');
                Session::forget('doctypeid');
            endif;
            //if document columns exists
            $coltypcnt  = Input::get('coltypecnt'); 
            // if one index entry result okey.(both AND ,OR)  
            //if multiple index entry result not ok in AND case
            if($coltypcnt)
            {
                for($i=0;$i<$coltypcnt;$i++)
                {
                    if(Input::get('doccol')[$i]): 
                    $search = Input::get('doccol')[$i];
   	
                	if(Input::get('doc_col_type')[$i] == 'Date')
                	{
                		
                		$datesearch = custom_date_Format($search,"d-m-Y");
                    	$label = Input::get('doclabl')[$i];
       					$query->$queryWhere(function($query1) use($label,$datesearch) {

       					$query1->orWhere(function($query2) use($label,$datesearch) {
                        $query2->Where("tbl_documents_columns.document_column_type",'=','Date'); 
                        $query2->Where("tbl_documents_columns.document_column_value",'=',$datesearch);
          				});

       					$datesearch = str_replace("-", "/",$datesearch);

          				$query1->orWhere(function($query2) use($label,$datesearch) {
                        $query2->Where("tbl_documents_columns.document_column_type",'=','Date'); 
                        $query2->Where("tbl_documents_columns.document_column_value",'=',$datesearch);
          				});

       					});
                    }
                    else
                    {
                  
                    	$query->$queryWhere('tbl_documents_columns.document_column_value','like','%'.Input::get('doccol')[$i].'%');
                    }
                        
                    endif;
                }    
            }
           
            // If statcks exists
            if(Input::get('stacks')):

                if(Auth::user()->user_role != Session::get('user_role_super_admin'))
                {
                    $department_id = explode(',',Auth::user()->department_id);
                    $count         = count($department_id);
                    if($count == 1):
                        $x=0;
                    else:
                        $x=1;
                    endif;
                    
                    foreach($department_id as $depid):
                       if($x == 1):
                            $query->$queryWhereRaw('('.'FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        elseif($x == $count):
                            $query->orWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)'.')');
                        else:
                            $query->$queryWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        endif;
                        $x++;
                    endforeach;

                    $stackCount = count(Input::get('stacks'));
                    if($stackCount == 1):
                        $y=0;
                    else:
                        $y=1;
                    endif;
                
                    foreach(Input::get('stacks') as $satckIds):
                        if($y == 1):
                            $query->whereRaw('('.'FIND_IN_SET('.$satckIds.',tbl_documents.stack_id)');
                        elseif($y == $stackCount):
                            $query->$queryWhereRaw('FIND_IN_SET('.$satckIds.',tbl_documents.stack_id)'.')');
                        else:
                            $query->whereRaw('FIND_IN_SET('.$satckIds.',tbl_documents.stack_id)');
                        endif;
                        $stackNames[] = DB::table('tbl_stacks')->select('stack_name')->where('stack_id',$satckIds)->get();
                        $y++;
                    endforeach;
                }
                else
                {
                    foreach(Input::get('stacks') as $satckIds):
                    $query->$queryWhereRaw('FIND_IN_SET('.$satckIds.',tbl_documents.stack_id)');
                    $stackNames[] = DB::table('tbl_stacks')->select('stack_name')->where('stack_id',$satckIds)->get();
                    endforeach;
                }
			endif;

            // If created_date_from exists 
            if(Input::get('created_date_from')):

                // Search by users department list
                if(Auth::user()->user_role != Session::get('user_role_super_admin')){
                    $department_id = explode(',',Auth::user()->department_id);
                    $count         = count($department_id);
                    if($count == 1):
                        $x=0;
                    else:
                        $x=1;
                    endif;
                    
                    foreach($department_id as $depid):
                       if($x == 1):
                            $query->$queryWhereRaw('('.'FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        elseif($x == $count):
                            $query->orWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)'.')');
                        else:
                            $query->$queryWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        endif;
                        $x++;
                    endforeach;

                    $query->where('tbl_documents.created_at','>=',Input::get('created_date_from').' 00:00:00');
                }else{
                    $query->$queryWhere('tbl_documents.created_at','>=',Input::get('created_date_from').' 00:00:00');
                }
            endif;

            // If created_date_to exists 
            if(Input::get('created_date_to')):

                // Search by users department list
                if(Auth::user()->user_role != Session::get('user_role_super_admin')){
                    $department_id = explode(',',Auth::user()->department_id);
                    $count         = count($department_id);
                    if($count == 1):
                        $x=0;
                    else:
                        $x=1;
                    endif;
                    
                    foreach($department_id as $depid):
                       if($x == 1):
                            $query->$queryWhereRaw('('.'FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        elseif($x == $count):
                            $query->orWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)'.')');
                        else:
                            $query->$queryWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        endif;
                        $x++;
                    endforeach;

                    $query->where('tbl_documents.created_at','<=',Input::get('created_date_to').' 23:59:59');
                }else{
                    $query->$queryWhere('tbl_documents.created_at','<=',Input::get('created_date_to').' 23:59:59');
                } 
            endif;

            // If last_modified_from exists 
            if(Input::get('last_modified_from')):

                // Search by users department list
                if(Auth::user()->user_role != Session::get('user_role_super_admin')){
                    $department_id = explode(',',Auth::user()->department_id);
                    $count         = count($department_id);
                    if($count == 1):
                        $x=0;
                    else:
                        $x=1;
                    endif;
                    
                    foreach($department_id as $depid):
                       if($x == 1):
                            $query->$queryWhereRaw('('.'FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        elseif($x == $count):
                            $query->orWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)'.')');
                        else:
                            $query->$queryWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        endif;
                        $x++;
                    endforeach;

                    $query->where('tbl_documents.updated_at','>=',Input::get('last_modified_from').' 00:00:00');
                }else{
                    $query->$queryWhere('tbl_documents.updated_at','>=',Input::get('last_modified_from').' 00:00:00');
                }
            endif;

            // If last_modified_to exists 
            if(Input::get('last_modified_to')):

                // Search by users department list
                if(Auth::user()->user_role != Session::get('user_role_super_admin')){
                    $department_id = explode(',',Auth::user()->department_id);
                    $count         = count($department_id);
                    if($count == 1):
                        $x=0;
                    else:
                        $x=1;
                    endif;
                    
                    foreach($department_id as $depid):
                       if($x == 1):
                            $query->$queryWhereRaw('('.'FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        elseif($x == $count):
                            $query->orWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)'.')');
                        else:
                            $query->$queryWhereRaw('FIND_IN_SET('.$depid.',tbl_documents.department_id)');
                        endif;
                        $x++;
                    endforeach;

                    $query->where('tbl_documents.updated_at','<=',Input::get('last_modified_to').' 23:59:59');
                }else{
                    $query->$queryWhere('tbl_documents.updated_at','<=',Input::get('last_modified_to').' 23:59:59');
                } 
            endif;

            // Expanding dglits with required datas
            $data_format         =     Input::get("data_format");
            $length         =     Input::get("length");
            $start          =     Input::get("start");
            $currentPage = ($start)?($start/$length)+1:1;

            \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($currentPage) {
                return $currentPage;
            });
            //ajax search
            $column = array('document_type_name',$table.'.'.'document_no',$table.'.'.'document_name','tbl_departments.department_name','tbl_stacks.stack_name',$table.'.'.'document_ownership',$table.'.'.'document_path',$table.'.'.'created_at',$table.'.'.'updated_at',$table.'.'.'document_expiry_date',$table.'.'.'document_status','tbl_documents_columns.document_column_value');
            $search       =   (isset($_POST['search']['value']))?trim($_POST['search']['value']):'';
            Session::put('adv_dt_search_text',$search);
            if($search)
            {
                $query->Where(function($query1) use($column,$search) 
                {
                    foreach ($column as $key => $value) 
                    {
                        if($value=="tbl_documents.updated_at" || $value=="tbl_documents.created_at" || $value=="tbl_documents.document_expiry_date"){
                                $datesearch = date("Y-m-d", strtotime($search));    
                                $query1->orWhere($value,'LIKE','%'.$datesearch.'%');
                        }
                        else
                        {
                            $query1->orWhere($value,'LIKE','%'.$search.'%');        
                        }
                    }
                });
            }
            // Ajax order by works
            $order = (isset($_POST['order'][0]['column']))?$_POST['order'][0]['column']:1;
            $direct = (isset($_POST['order'][0]['dir']))?$_POST['order'][0]['dir']:'DESC';
            $data_item = (isset($_POST['columns'][$order]['data']))?$_POST['columns'][$order]['data']:'';
            // echo $data_item;
            // exit();
	        switch($data_item)
	        {
				case 'document_type_id':
				$table_column = $table.'.'.'document_type_id';
				break;
				case 'document_no':
				$table_column = $table.'.'.'document_no';
				break;
				case 'document_name':
				$table_column = $table.'.'.'document_name';
				break;
				case 'department_id':
				$table_column = 'tbl_departments'.'.'.'department_name';
				break;
				case 'stack_id':
				$table_column = 'tbl_stacks'.'.'.'stack_name';
				break;
				case 'document_ownership':
				$table_column = $table.'.'.'document_ownership';
				break;
				case 'created_at':
				$table_column = $table.'.'.'created_at';
				break;
				case 'updated_at':
				$table_column = $table.'.'.'updated_at';
				break;
				case 'document_expiry_date':
				$table_column = $table.'.'.'document_expiry_date';
				break;
				case 'document_status':
				$table_column = $table.'.'.'document_status';
				break;
				case 'document_modified_by':
				$table_column = $table.'.'.'document_modified_by';
				break;
				default:
				$table_column = 'tbl_departments'.'.'.'department_name';
				break;
	        }
            //save order and direction
            if(!$data_format)
            {
            
                $request->session()->put('dt_order_column', $table_column);
                $request->session()->put('dt_order_direct', $direct);
                $request->session()->save();
            
            }

            // print_r( $request->session()->get('dt_order_column'));
            // print_r( $request->session()->get('dt_order_direct'));
            // exit();

            // Run the query
            $query->orderBy($request->session()->get('dt_order_column'),$request->session()->get('dt_order_direct'));
            $query->selectRaw($select);
            $query->groupBy('tbl_documents.document_id');
            if($length)
            {
                $data['dglist'] = $query->paginate($length); 
            }
            else
            {
                $data['dglist'] = $query->get(); 
                
            }
            // $queries = DB::getQueryLog();
            // echo '<pre>';
            // print_r($queries);
            // exit();
            $i = $start;
            $data_table = array();
            foreach($data['dglist'] as $value): 
            $ext = pathinfo($value->document_file_name, PATHINFO_EXTENSION);
            $user_permission=Auth::user()->user_permission;
            $current_view =Lang::get('language.list_view');
            $i++;
            $row_d = array();
            $action =''; 
            //column values
            @$value->document_type_columns = DB::table('tbl_documents_columns')->select('document_column_name','document_column_value','document_column_type')->where('document_id',$value->document_id)->get();
            //To check document has workfowhistory
            @$value->hasWorkfowHistory = DB::table('tbl_workflow_histories')->where('document_workflow_object_id',$value->document_id)->exists();
            if($value->document_status!='Checkout')
            {
                $action.='<input name="checkbox[]" type="checkbox" value="'.$value->document_id.'" id="chk'.$value->document_id.'" class="checkBoxClass">';
            }
            else
            {
                $action.='<input name="checkbox_disabled[]" type="checkbox" onclick="return swal(You have no permission)" value="'.$value->document_id.'" disabled=true>';
            }
            	$action .='&nbsp;';
                $action.='<div class="btn-group">
                      <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" title="Actions">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                      </button>
                      <ul class="dropdown-menu" role="menu" id="icons-list">
                    
                  ';
                $action .='&nbsp;';
                $action .='&nbsp;';
                 
                    $action.='<a title="Open Document" href="documentManagementView?dcno='.$value->document_id.'&page=content&frm='.Lang::get('language.search_view').'">';
                
                    if($ext=="pdf"){
                        $action.='<i class="fa fa-file-pdf-o"></i>';
                    }
                    elseif ($ext=='png'||$ext=='jpg'||$ext=='jpeg'||$ext=='tiff'||$ext=='tif'||$ext=='TIFF'||$ext=='TIF'||$ext=='gif') {
                        $action.='<i class="fa fa-file-image-o"></i>';
                    }
                    elseif ($ext=='docx'||$ext=='doc') {
                        $action.='<i class="fa fa-file-word-o"></i>';
                    }
                    elseif ($ext=='txt') {
                        $action.='<i class="fa fa-file-text-o"></i>';
                    }
                    elseif ($ext=='zip'||$ext=='rar') {
                        $action.='<i class="fa fa-file-archive-o"></i>';
                    }
                    elseif ($ext=='xls'||$ext=='xlsx') {
                        $action.='<i class="fa fa-file-excel-o"></i>';
                    }
                    elseif ($ext=='wav'||$ext=='mp3'||$ext=='ogg') {
                        $action.='<i class="icon fa fa-file-sound-o"></i>';
                    }
                    elseif ($ext=='flv'||$ext=='mp4'||$ext=='ogv'||$ext=='webm') {
                        $action.='<i class="icon fa fa-file-video-o"></i>';
                    }
                    elseif ($ext=='dwg') {
                        $action.='<i class="icon fa fa-clipboard"></i>';
                    }
                    else{
                        $action.='<i class="fa fa-file-o"></i>';
                    }
                $action .='</a>';
                $action .='&nbsp;';
                    $action.='
                    <!-- History -->';
                    $action.='<a title="History" href="documentHistory/'.$value->document_id.'"><i class="fa fa-history" ></i></a>';
                    $action.='
                    <!-- Check Out Document -->';
                        if(stristr($user_permission,"checkout")){
                        if($value->document_status =='Published' || $value->document_status =='Rejected')
                        {
                            if(@$value->document_encrypt_status == 1){
                            $action .='&nbsp;';
                            $action.='<a href="javascript:void(0);" title="Check Out" onclick="return swal(\''.ucfirst($value->document_name).' is curently encrypted by '.ucfirst($value->document_encrypted_by).' It must be decrypt first before you can perform this operation'.'\')"><i class="fa fa-share" ></i></a>';
                            }
                            else{
                            $action .='&nbsp;';
                            $action.='<a href="javascript:void(0);"  title="Check Out" onclick="return myFunction(\''.$value->document_name.'\',\''.$value->document_file_name.'\',\''.$value->document_id.'\',\''.$i.'\')"><i class="fa fa-share"></i></a>';
                            }
                        }
                        elseif($value->document_status =='Checkout'){
                        $action .='&nbsp;';
                        $action.='<a href="javascript:void(0);" title="Check Out" onclick="return swal(\''.ucfirst($value->document_name).' is curently Checked Out by '.ucfirst($value->document_modified_by).' It must be Checked In first before you can perform this operation'.'\')"><i class="fa fa-share" ></i></a>';
                        }
                        elseif($value->document_status =='Review'){
                        $action .='&nbsp;';
                        $action.='<a href="javascript:void(0);" title="Check Out" onclick="return swal(\''.ucfirst($value->document_name).' is currently under review'.'\')"><i class="fa fa-share" ></i></a>';
                        }
                        }
                    $action.='
                    <!-- Delete -->';
                        if(stristr($user_permission,"delete")){
                            if($value->document_status!='Checkout'){
                                $action .='&nbsp;';
                                 $action.='<i title="Delete" class="fa fa-trash" onclick="del( \''.$value->document_id.'\',\''.$value->document_name.'\')" style="color: red; cursor:pointer;"></i>';
                            }
                            else{
                                $action .='&nbsp;';
                                 $action.='<i title="Delete" class="fa fa-trash" onclick="return swal(\''.ucfirst($value->document_name).' is curently Checked Out by '.ucfirst($value->document_modified_by).'. It must be Checked In first before you can perform this operation.'.'\')" style="color: red; cursor:pointer;"></i>';
                            }
                            
                        }
                    $action.='
                    <!-- Related document -->';
                        $action .='&nbsp;';
                        $action.='<a title="Related documents" href="relatedsearch/'.$value->document_id.'/'.$value->document_no.'/'.$value->document_name.'"><i class="fa fa-files-o" ></i></a>';
                    $action.='';
               
                    
                    if (Session::get('module_activation_key6')==1){
                        if(date("Y-m-d") > Session::get('module_expiry_date6')){ 
                        }else{
                            $action .='&nbsp; &nbsp;';
                            if(@$value->document_encrypt_status == 0 && @$value->document_status !='Checkout')
                            {
                                $action.='
                                <!-- encryption -->';
                                $action.='<a count="'.$value->document_id.'" doc_name="'.$value->document_name.'" doc_file_name="'.$value->document_file_name.'" title="Encrypt File" id="encrypt_doc" style="cursor:pointer;"><i class="fa fa-lock" ></i></a>';
                            }
                            elseif(@$value->document_encrypt_status == 0 && @$value->document_status =='Checkout')
                            {
                                $action.='
                                <!-- encryption -->';
                                $action.='<a title="Encrypt File" onclick="return swal(\''.ucfirst($value->document_name).' is curently Checked Out by '.ucfirst($value->document_modified_by).'. It must be Checked In first before you can perform this operation.'.'\')" style="cursor:pointer;"><i class="fa fa-lock" ></i></a>';
                            }
                            else
                            {
                                $action.='
                                <!-- decryption -->';
                                $action.='<a count="'.$value->document_id.'" doc_name="'.$value->document_name.'" doc_file_name="'.$value->document_file_name.'" title="Decrypt File" id="decrypt_doc" style="cursor:pointer;"><i class="fa fa-unlock" ></i></a>';
                            }
                        }
                    }

                    $action.='
                    <!-- more details -->';
                    $action .='&nbsp; &nbsp;';
                    $action.='<a count="'.$value->document_id.'" id="moredet" data-toggle="modal"  style="cursor:pointer; padding-left:2px; padding-right:2px;" data-target="#viewmoreModal" title="More Details" ><i class="fa fa-ellipsis-v" aria-hidden="true"></i></a>';
                    $action .='</ul></div>';
            $row_d['actions'] = $action;
            if(@$value->document_type_name)
                {$row_d['document_type_id'] = ucfirst(@$value->document_type_name);}
           if(@$value->document_no)
                {$row_d['document_no'] = @$value->document_no;}
            if(@$value->document_name)
                {$row_d['document_name'] = ucfirst(@$value->document_name);}
            if(@$value->department_name)
                {$row_d['department_id'] = ucfirst(@$value->department_name);}
            if(@$value->stack_name)
                {$row_d['stack_id'] = ucfirst(@$value->stack_name);}
            $document_id = $value->document_id;
            //echo $document_id;
            $query = DB::table('tbl_document_types_columns as tc')
            ->leftJoin('tbl_documents_columns as tdc', function($join) use($document_id){
                $join->on('tc.document_type_column_id','=','tdc.document_type_column_id');
                $join->where('tdc.document_id','=',$document_id);
              })
            ->select('tdc.document_column_value',
                'tc.document_type_column_id',
                'tdc.document_column_name',
                'tdc.document_column_modified_by',
                'tdc.updated_at',
                'tdc.document_column_id',
                'tdc.document_id',
                'tc.document_type_id',
                'tc.document_type_column_name',
                'tc.document_type_column_type'
                )
            ->where('tc.document_type_id',Session::get('doctypeids'))
            ->orderBy('tc.document_type_column_order','ASC');
            $data2 = $query->get();                   
  
            foreach ($data2 as $value2) 
            {
                 if($value2->document_type_column_type == "Date")
                {
                        $value_column = custom_date_Format($value2->document_column_value);
                }
                else
                {
                    $value_column = ucfirst($value2->document_column_value);
                }
                
                $row_d["$value2->document_type_column_id"] = ($value_column)?$value_column:'-';
            }
            $row_d['document_ownership'] = $value->document_ownership;
            $row_d['created_at'] = dtFormat($value->created_at);
            $row_d['updated_at'] = dtFormat($value->updated_at);
            $row_d['document_encrypt_status'] = @$value->document_encrypt_status;
            $row_d['document_id'] = @$value->document_id;
            $row_d['document_expiry_date'] = custom_date_Format($value->document_expiry_date);
                if(($value->document_expiry_date > date('Y-m-d'))){
                    $todaydate = date('Y-m-d'); // or your date as well
                    $docexpdate = $value->document_expiry_date;
                    $datediff = abs(strtotime($docexpdate) - strtotime($todaydate));
                    $row_d['noofdays'] = round($datediff / (60 * 60 * 24));                
                }else{
                    $row_d['noofdays'] = 0;
                } 
            $row_d['document_status'] = ucfirst($value->document_status);
            $data_table[] = $row_d;
            endforeach;
            if($length)//for datatable
            {
                $count_all = ($data['dglist'])?$data['dglist']->total():0;
                $output = array(
                  "draw" =>  Input::get('draw'),
                  "recordsTotal" => @$count_all,
                  "recordsFiltered" => @$count_all,
                  "data" => $data_table
                );
                echo json_encode($output);
            }
            else // for export
            {
                
                if(Input::get('data_items'))
                {
                    $fields_export=Input::get('data_items');
                }
                if(Input::get('data_values'))
                {
                    $title_export=Input::get('data_values');
                }
                
                //fetched data write to the csv file here
                $selectedText_change = 'search_result';

                $datetime = date("Y-m-d_H-i-s");

                $destinationPath  = config('app.export_path'); // export path

                if(!file_exists($destinationPath))
                {
                    //create directory export
                    File::makeDirectory($destinationPath, $mode = 0777, true, true);
                }

                if($data_format == 'csv')
                {
                    $export_csv_name = Config::get('constants.search_export_file').$selectedText_change.'_'.$datetime.".csv";
                    $export_csv_name = str_replace("username",Auth::user()->username,$export_csv_name);
                    $file = config('app.export_path').$export_csv_name;
                    Session::put('search_export_csv_filename',$file);
                                        
                    $headers = array(
                      'Content-Type: text/csv; charset=utf-8'
                    );
                    $out = fopen($file, 'w');
                    fputcsv($out, $title_export);
                    
                    //write to the csv file

                    foreach ($data_table as $key => $value) {
                        $row = array();
                        foreach ($fields_export as $key) {
                            $row[] = @$value[$key];
                        }
                        fputcsv($out, $row);
                    }
                    
                    fclose($out);
                    $output = array(
                      "recordsTotal" => count($data['dglist']),
                      "file"=> $export_csv_name
                    );
                    echo json_encode($output);
                }
                else if($data_format == 'pdf')
                {
                    include (public_path()."/storage/includes/lang1.en.php" );
                    $export_pdf_name = Config::get('constants.search_export_file_pdf').$selectedText_change.'_'.$datetime.".pdf";
                    $export_pdf_name = str_replace("username",Auth::user()->username,$export_pdf_name);
                    $file = config('app.export_path').$export_pdf_name;
                    $fpdf = new FPDF();
                    //header
                    /*$fpdf->Set_SiteLogo(url('logo/'.config('app.settings_logo')));
                    $fpdf->companyName = config('app.settings_company_name');
                    $fpdf->companyAddress = config('app.settings_address');*/
                    $search_parameters = array();

                    if(Session::get('keyword')):
                        $search_parameters[$language['keyword_search']]=Session::get('keyword');
                    endif;
                    if(Session::get('serach_doc_no')):
                        $search_parameters[$language['document no']]=Session::get('serach_doc_no');
                    endif;
                    if(Session::get('search_docname')):
                        $search_parameters[$language['document name']]=Session::get('search_docname');
                    endif;
                    if(Session::get('search_ownership')):
                        $search_parameters[$language['ownership']]=(implode(',',Session::get('search_ownership')));
                    endif;
                    if(Session::get('search_created_by')):
                        $search_parameters[$language['created by']]=(implode(',',Session::get('search_created_by')));
                    endif;
                    if(Session::get('search_updated_by')):
                        $search_parameters[$language['last modified by']]=(implode(',',Session::get('search_updated_by')));
                    endif;                
                    if(Session::get('search_departments')):
                        $search_parameters[$language['department']]=(implode(',',Session::get('search_departments')));
                    endif;                
                    if(Session::get('search_document_type_name')):
                        $search_parameters[$language['document type']]=Session::get('search_document_type_name');
                    endif;
                    if(Session::get('search_stack')):
                        $search_parameters[$language['stack']]=(implode(',',Session::get('search_stack')));
                    endif;             
                    if(Session::get('search_created_date_from')):
                        $search_parameters[$language['created date - from']]=Session::get('search_created_date_from');
                    endif;
                    if(Session::get('search_created_date_to')):
                        $search_parameters[$language['created date - to']]=Session::get('search_created_date_to');
                    endif;                    
                    if(Session::get('search_last_modified_from')):
                        $search_parameters[$language['last modified - from']]=Session::get('search_last_modified_from');
                    endif; 
                    if(Session::get('search_last_modified_to')):
                        $search_parameters[$language['last modified - to']]=Session::get('search_last_modified_to');
                    endif; 
                    // echo '<pre>';
                    // print_r($search_parameters);
                    // exit();       

                    $fpdf->AddPage('L');//page orientation
                    $fpdf->AliasNbPages();//total page count
                    // Logo
                    $records = DB::table('tbl_settings')->select('settings_logo')->first();
                    $image = public_path('images/logo/'.$records->settings_logo);
                    $fpdf->Image($image,250,5,30,12);

                    $fpdf->SetFont('Arial','I',12);
                    
                    //pdf printed date
                    $currentDateTime = date('d-m-Y h:i:s');
                    $newDateTime = date('d-m-Y h:i:s A', strtotime($currentDateTime));

                    $fpdf->Cell(100,7,'PDF printed on: '.$newDateTime,0,0,'L');
                    $fpdf->Ln();
                    // Title
                    foreach ($search_parameters as $key => $value) {
                        $fpdf->Cell(100,7,@$key.': '.@$value,0,0,'L');
                        $fpdf->Ln();
                    }
                    
                    // Line break
                    $fpdf->Ln(10);
                    // Colors, line width and bold font
                    $fpdf->SetFillColor(255,0,0);
                    $fpdf->SetTextColor(255);
                    $fpdf->SetDrawColor(128,0,0);
                    $fpdf->SetLineWidth(.1);
                    $fpdf->SetFont('Arial','B',9);
                    //Write heading of columns to pdf

                    foreach($title_export as $heading) {
                        $fpdf->Cell(50,8,$heading,1,0,'L',true);
                    }
                    $fpdf->Ln();
                    // Color and font restoration
                    $fpdf->SetFillColor(224,235,255);
                    $fpdf->SetTextColor(0);
                    $fpdf->SetFont('Arial','',8);
                    $fill = false;
                    //write datas to pdf

                    foreach($data_table as $key => $value) 
                    {
                        foreach($fields_export as $key)
                        $fpdf->Cell(50,7,@$value[$key],1,0,'L',$fill);
                        $fill = !$fill;
                        $fpdf->Ln();
                    }
                    
                    //store pdf
                    $fpdf->Output($file,'F');
                    $output = array(
                      "recordsTotal" => count($data['dglist']),
                      "file"=> $export_pdf_name
                    );
                    echo json_encode($output);
                }
            } 
            
            // Set session for change criteria
            if(Input::get('doctypeid')){
                Session::put('dtid',Input::get('doctypeid'));
            }else{
                Session::forget('dtid');
            }
        
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
}
?>