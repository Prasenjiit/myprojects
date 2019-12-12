<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
use Input;
use Auth;
use Session;

Use DateTime;
class AjaxModel extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = ''; // change it

    /**
    *Primary key
    */
    protected $primaryKey = '';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public static function ajax_filter($table,$tbl_documents_columns,$doclistid,$curr_id,$loggedUsersdepIds,$view,$filter,$docid,$search,$search_column,$length)
    {       

      $linked_doc_only = Input::has('linked_doc_only')?Input::get('linked_doc_only'):0;

      // Ajax order by works

      $order = (isset($_POST['order'][0]['column']))?$_POST['order'][0]['column']:1;
      $direct = (isset($_POST['order'][0]['dir']))?$_POST['order'][0]['dir']:'DESC';
      $data_item = (isset($_POST['columns'][$order]['data']))?$_POST['columns'][$order]['data']:'';

      $table_column = 'tbl_documents_columns.document_column_name';
      $dynamic_col =0;

      //order by according to cases

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
          case 'document_path':
          $table_column = $table.'.'.'document_path';
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
          case 'document_checkout_date':
          $table_column = $table.'.'.'document_checkout_date';
          break;
          case 'document_modified_by':
          $table_column = $table.'.'.'document_modified_by';
          break;
          default:
          $table_column = $tbl_documents_columns.'.'.'document_column_value';
          $dynamic_col = 1;
          break;
        }
           
           
            $query = DB::table($table);
            $query->leftjoin('tbl_document_types',$table.'.'.'document_type_id','=','tbl_document_types.document_type_id');
            $query->leftJoin($tbl_documents_columns, function($join) use($table,$tbl_documents_columns,$dynamic_col,$data_item){

                $join->on($table.'.'.'document_id','=',$tbl_documents_columns.'.document_id');

                if($dynamic_col)
                {
                  $join->where($tbl_documents_columns.'.document_type_column_id','=',$data_item);
                }
                
            });
            $query->leftjoin('tbl_departments',$table.'.'.'department_id','=','tbl_departments.department_id');
            $query->leftjoin('tbl_stacks',$table.'.'.'stack_id','=','tbl_stacks.stack_id');
          
            $select ="
            $table.document_type_id,
            $table.stack_id,
            $table.document_id,
            $table.document_no as document_no,
            $table.document_name as document_name,
            $table.department_id,
            $table.stack_id,
            tbl_stacks.stack_name as stack_name,
            $table.document_ownership,
            $table.document_assigned_to,
            $table.document_path,
            $table.created_at,
            $table.updated_at,
            $table.document_expiry_date,
            $table.document_status,
            $table.parent_id,
            $table.document_version_no,
            $table.document_file_name,
            $table.document_modified_by";

          if($view != trans('language.import_view'))
          { 
            $select.=",$table.document_encrypt_status,$table.document_encrypted_by";
          } 
       
        $query->where('tbl_document_types.is_app',0);    
        $query->where($table.'.'.'document_type_id',$doclistid);

        if($view == trans('language.checkout_view'))//checkout view
        {
            $query->leftjoin('tbl_users',$table.'.'.'documents_checkout_by','=','tbl_users.id');
            $select.=",$table.document_checkout_date,$table.document_pre_status,tbl_users.username";
            $column[] = $table.'.'.'document_checkout_date';
            $column[] = 'tbl_users.username';
        }
        
        else if($view == trans('language.stack_view'))
        {
            $query->Where("$table.stack_id",$curr_id);
        }
        else if($view == trans('language.department_view'))
        {
            $query->Where("$table.department_id",$curr_id);
        }
        
       
        if($search){
           
            $search_column_type  = (isset($_POST['search_column_data']['search_column_type']))?trim(strtolower($_POST['search_column_data']['search_column_type'])):'';
            $search_dynamic_col  = (isset($_POST['search_column_data']['search_dynamic_col']))?trim($_POST['search_column_data']['search_dynamic_col']):0;
        $column  = array();   
        switch($search_column)
        {
          case 'search_all':
           $column = array(
                $table.'.'.'document_no',
                $table.'.'.'document_name',
                'tbl_departments.department_name',
                'tbl_stacks.stack_name',
                $table.'.'.'document_file_name',
                $table.'.'.'document_ownership',
                $table.'.'.'document_path',
                $table.'.'.'created_at',
                $table.'.'.'updated_at',
                $table.'.'.'document_expiry_date',
                $table.'.'.'document_status',
                $tbl_documents_columns.'.'.'document_column_value'
            );
              if($view == trans('language.checkout_view'))//checkout view
              {
                $column[] = $table.'.'.'document_checkout_date';
                $column[] = 'tbl_users.username';
              }
          break; 
          case 'department':
          $column[] = 'tbl_departments.department_name';
          break; 
          case 'stack':
          $column[] = 'tbl_stacks.stack_name';
          break; 
          case 'document_no':
          $column[] = $table.'.'.'document_no';
          break; 
          case 'document_name':
          $column[] = $table.'.'.'document_name';
          break;    
          case 'filename':
          $column[] = $table.'.'.'document_file_name';
          break; 
          case 'check_out_date':
          $column[] = $table.'.'.'document_checkout_date';
          break;    
          case 'check_out_by':
          $column[] = 'tbl_users.username';
          break;    
          case 'last_updated':
          $column[] = $table.'.'.'updated_at';
          break;    
          case 'expir_date':
          $column[] = $table.'.'.'document_expiry_date';
          break;  
          case 'status':
          $column[] = $table.'.'.'document_status';
          break;    
         default:
                $column[] =  $tbl_documents_columns.'.'.'document_column_value';
          
          break;
        }  
            $query->Where(function($query1) use($column,$search,$table,$tbl_documents_columns,$search_column,$search_column_type,$search_dynamic_col) {
                
                 
                foreach ($column as $key => $value) {
                    if($value=="$table.updated_at" || $value=="$table.document_checkout_date" || $value=="$table.document_expiry_date")
                    {
      
                        $datesearch = date("Y-m-d", strtotime($search));    
                        
                        $query1->orWhere($value,'LIKE','%'.$datesearch.'%');
                        $query1->orWhere($value,'LIKE','%'.$search.'%');       
                    }
                    else if($value=="$tbl_documents_columns.document_column_value")
                    {
                        if($search_dynamic_col)
                        {
                            if($search_column_type == 'date')
                            {
                    $datesearch = custom_date_Format($search,"d-m-Y");            
                    $query1->orWhere(function($query2) use($datesearch,$tbl_documents_columns,$search_column) {
                            $query2->Where("$tbl_documents_columns.document_type_column_id",'=',$search_column); 
                            $query2->Where("$tbl_documents_columns.document_column_value",'=',$datesearch);
                       });
                    $datesearch = str_replace("-", "/",$datesearch);
                    $query1->orWhere(function($query2) use($datesearch,$tbl_documents_columns,$search_column) {
                            $query2->Where("$tbl_documents_columns.document_type_column_id",'=',$search_column); 
                            $query2->Where("$tbl_documents_columns.document_column_value",'=',$datesearch);
                       });

                    $datesearch = custom_date_Format($search,"m-d-Y");
                    $query1->orWhere(function($query2) use($datesearch,$tbl_documents_columns,$search_column) {
                            /*$query2->Where("$tbl_documents_columns.document_column_type",'=','Date'); */
                            $query2->Where("$tbl_documents_columns.document_type_column_id",'=',$search_column); 
                            $query2->Where("$tbl_documents_columns.document_column_value",'=',$datesearch);
                       });
                    $datesearch = str_replace("-", "/",$datesearch);
                    $query1->orWhere(function($query2) use($datesearch,$tbl_documents_columns,$search_column) {
                            $query2->Where("$tbl_documents_columns.document_type_column_id",'=',$search_column);  
                            $query2->Where("$tbl_documents_columns.document_column_value",'=',$datesearch);
                       });

                    $datesearch = custom_date_Format($search,"Y-m-d");
                    $query1->orWhere(function($query2) use($datesearch,$tbl_documents_columns,$search_column) {
                            $query2->Where("$tbl_documents_columns.document_type_column_id",'=',$search_column); 
                            $query2->Where("$tbl_documents_columns.document_column_value",'=',$datesearch);
                       });
                    $datesearch = str_replace("-", "/",$datesearch);
                    $query1->orWhere(function($query2) use($datesearch,$tbl_documents_columns,$search_column) {
                            $query2->Where("$tbl_documents_columns.document_type_column_id",'=',$search_column); 
                            $query2->Where("$tbl_documents_columns.document_column_value",'=',$datesearch);
                       });
                            }
                            
                               
                        } 
                       
                        $query1->orWhere(function($query2) use($search,$tbl_documents_columns,$search_column,$search_dynamic_col) 
                        {
                                if($search_dynamic_col)
                                {
                                $query2->Where("$tbl_documents_columns.document_type_column_id",'=',$search_column); 
                                }
                                $query2->Where("$tbl_documents_columns.document_column_value",'LIKE','%'.$search.'%');
                        }); 
                        
                        

                           
                    }
                    else
                    {
                        $query1->orWhere($value,'LIKE','%'.$search.'%');        
                    }
                  }
               
            });
        }
        
            // Get data by department wise
            if(Auth::user()->user_role == Session::get("user_role_group_admin") || Auth::user()->user_role == Session::get("user_role_regular_user")) 
            {
               
                $query->whereIn($table.'.department_id',$loggedUsersdepIds);
            }
            //check user = private user, fetch only the docs of that user
            else if(Auth::user()->user_role == Session::get("user_role_private_user"))
            {
                $query->where($table.'.'.'document_ownership',Auth::user()->username);
            }
            else//super admin
            {
                $query;
            }
           
            //filter according to radio button in listview(expiry)
            switch($filter)
            {

            case trans('language.exclude'): //docs not expired
            {
            $query->where($table.'.'.'document_expiry_date','>',date('Y-m-d'))->orWhere($table.'.'.'document_expiry_date','=',null);
            }
            break;
            case trans('language.expired')://expired docs only fetch
            {
            $query->where($table.'.'.'document_expiry_date','<=',date('Y-m-d'));
            }
            break;
            case trans('language.all')://all docs
            {
            $query;
            }
            break;
            case trans('language.expire_soon')://expire soon docs
            {
            $datetomarrow = new DateTime('tomorrow');
            $query->where($table.'.'.'document_expiry_date','!=','null')->whereBetween($table.'.'.'document_expiry_date',[$datetomarrow->format('Y-m-d'),Session::get('expiry_date_from_settings')]);
            }
            break;
            case trans('language.assigned')://assigned docs only
            {
            $query->where($table.'.'.'document_status','Review')->where($table.'.'.'document_assigned_to',Auth::user()->username);
            }
            break;
            case trans('language.rejected')://rejected docs only
            {
            $query->where($table.'.'.'document_status','Rejected')->where($table.'.'.'document_created_by',Auth::user()->username);
            }
            break;
            case trans('language.accepted')://accepted docs only
            {
            $query->where($table.'.'.'document_status','Published')->where($table.'.'.'document_created_by',Auth::user()->username)->where($table.'.'.'document_assigned_to','!=',"");
            }
            break;
            case trans('language.assigned_to_whom')://assignedt to docs only
            {
                $query->where($table.'.'.'document_status','Review')
                ->where($table.'.'.'document_created_by',Auth::user()->username)
                ->where($table.'.'.'document_assigned_to','!=',"");
            }
            break;
            case trans('Level 1')://assignedt to docs only
            {                      
                $query->where('tbl_documents_columns.document_column_name','=',$filter)->where('tbl_documents_columns.document_column_value','=','yes');
            }
            break;
            case trans('Level 2')://assignedt to docs only
            {                      
              $query->where('tbl_documents_columns.document_column_name','=',$filter)->where('tbl_documents_columns.document_column_value','=','yes');
            }
            break;
            case trans('Level 3')://assignedt to docs only
            {                      
              $query->where('tbl_documents_columns.document_column_name','=',$filter)->where('tbl_documents_columns.document_column_value','=','yes');
            }
            break;
            }
           
            
            /* document column sorting */

            $type_of_col = DB::table('tbl_documents_columns')->select('document_column_type')->where('document_type_column_id',$data_item)->first();

            $dp = DB::getTablePrefix();
            
            if($dynamic_col)
            {
              switch($type_of_col->document_column_type)
              {
                case 'Number':
                {
                  $order_by = "CAST(" . $dp . "$table_column AS unsigned) $direct";
                  $query->orderByRaw($order_by);
                }
                break;
                case 'Alphanumeric':
                {
                  $order_by = "CAST(" . $dp . "$table_column AS char) $direct";
                  $query->orderByRaw($order_by);
                }
                break;
                case 'Date':
                {
                  

                  $order_by = "CAST(" . $dp . "$table_column AS date) $direct";
                  $query->orderByRaw($order_by);
                  
                }
                break;
                default:
                {
                  $order_by = "($table_column) $direct";
                  $query->orderByRaw($order_by);
                }
              }
              
              
          }
          else
          {
            $query->orderBy("$table_column","$direct");
          }

          // $query->orderBy('tbl_departments.department_name',"$direct");
          
          // if($table_column != "$table.document_id")
          // {
          //     $query->orderBy("$table.document_id",'DESC');
          // }
          
          $data['dglist'] = $query->selectRaw($select)->groupBy("$table.document_id")->paginate($length);

          
            $parent=array();
            session::put('child','');
            
            
            return $data;
    } 
    public static function ajax_filter_new($table,$tbl_documents_columns,$doclistid,$curr_id,$loggedUsersdepIds,$view,$filter,$docid,$search,$search_column,$length)
    {       

            $linked_doc_only = Input::has('linked_doc_only')?Input::get('linked_doc_only'):0;
           // $tbl_documents_columns = "tbl_documents_columns";
           
            $query = DB::table($table);

            if($linked_doc_only == 'linked_doc')
            {
              $query->join('tbl_document_links',$table.'.'.'document_id','=','tbl_document_links.document_id');

            }
            $query->leftjoin('tbl_document_types',$table.'.'.'document_type_id','=','tbl_document_types.document_type_id');
            $query->leftjoin($tbl_documents_columns,$table.'.'.'document_id','=',$tbl_documents_columns.'.document_id');
            $query->leftjoin('tbl_departments',$table.'.'.'department_id','=','tbl_departments.department_id');
            $query->leftjoin('tbl_stacks',$table.'.'.'stack_id','=','tbl_stacks.stack_id');
          
            $select ="
            $table.document_type_id,
            $table.stack_id,
            $table.document_id,
            $table.document_no as document_no,
            $table.document_name as document_name,
            $table.department_id,
            $table.stack_id,
            tbl_stacks.stack_name as stack_name,
            $table.document_ownership,
            $table.document_assigned_to,
            $table.document_path,
            $table.created_at,
            $table.updated_at,
            $table.document_expiry_date,
            $table.document_status,
            $table.parent_id,
            $table.document_version_no,
            $table.document_file_name,
            $table.document_modified_by";

          if($view != trans('language.import_view'))
          { 
            $select.=",$table.document_encrypt_status,$table.document_encrypted_by";
          } 
        if($linked_doc_only == 'linked_doc')
            {
              $link_doc_id = Input::get('link_doc_id')?Input::get('link_doc_id'):0;
              $query->where('tbl_document_links.app_document_id',$link_doc_id); 
            }  
        $query->where('tbl_document_types.is_app',0);    
        $query->where($table.'.'.'document_type_id',$doclistid);

        if($view == trans('language.checkout_view'))//checkout view
        {
            $query->leftjoin('tbl_users',$table.'.'.'documents_checkout_by','=','tbl_users.id');
            $select.=",$table.document_checkout_date,$table.document_pre_status,tbl_users.username";
            $column[] = $table.'.'.'document_checkout_date';
            $column[] = 'tbl_users.username';
        }
        
        else if($view == trans('language.stack_view'))
        {
            $query->Where("$table.stack_id",$curr_id);
        }
        else if($view == trans('language.department_view'))
        {
            $query->Where("$table.department_id",$curr_id);
        }
        
       
        if($search){
           
            $search_column_type  = (isset($_POST['search_column_data']['search_column_type']))?trim(strtolower($_POST['search_column_data']['search_column_type'])):'';
            $search_dynamic_col  = (isset($_POST['search_column_data']['search_dynamic_col']))?trim($_POST['search_column_data']['search_dynamic_col']):0;
        $column  = array();   
        switch($search_column)
        {
          case 'search_all':
           $column = array(
                $table.'.'.'document_no',
                $table.'.'.'document_name',
                'tbl_departments.department_name',
                'tbl_stacks.stack_name',
                $table.'.'.'document_file_name',
                $table.'.'.'document_ownership',
                $table.'.'.'document_path',
                $table.'.'.'created_at',
                $table.'.'.'updated_at',
                $table.'.'.'document_expiry_date',
                $table.'.'.'document_status',
                $tbl_documents_columns.'.'.'document_column_value'
            );
              if($view == trans('language.checkout_view'))//checkout view
              {
                $column[] = $table.'.'.'document_checkout_date';
                $column[] = 'tbl_users.username';
              }
          break; 
          case 'department':
          $column[] = 'tbl_departments.department_name';
          break; 
          case 'stack':
          $column[] = 'tbl_stacks.stack_name';
          break; 
          case 'document_no':
          $column[] = $table.'.'.'document_no';
          break; 
          case 'document_name':
          $column[] = $table.'.'.'document_name';
          break;    
          case 'filename':
          $column[] = $table.'.'.'document_file_name';
          break; 
          case 'check_out_date':
          $column[] = $table.'.'.'document_checkout_date';
          break;    
          case 'check_out_by':
          $column[] = 'tbl_users.username';
          break;    
          case 'last_updated':
          $column[] = $table.'.'.'updated_at';
          break;    
          case 'expir_date':
          $column[] = $table.'.'.'document_expiry_date';
          break;  
          case 'status':
          $column[] = $table.'.'.'document_status';
          break;    
         default:
                $column[] =  $tbl_documents_columns.'.'.'document_column_value';
          
          break;
        }  
            $query->Where(function($query1) use($column,$search,$table,$tbl_documents_columns,$search_column,$search_column_type,$search_dynamic_col) {
                
                 
                foreach ($column as $key => $value) {
                    if($value=="$table.updated_at" || $value=="$table.document_checkout_date" || $value=="$table.document_expiry_date")
                    {
      
                        $datesearch = date("Y-m-d", strtotime($search));    
                        
                        $query1->orWhere($value,'LIKE','%'.$datesearch.'%');
                        $query1->orWhere($value,'LIKE','%'.$search.'%');       
                    }
                    else if($value=="$tbl_documents_columns.document_column_value")
                    {
                        if($search_dynamic_col)
                        {
                            if($search_column_type == 'date')
                            {
                    $datesearch = custom_date_Format($search,"d-m-Y");            
                    $query1->orWhere(function($query2) use($datesearch,$tbl_documents_columns,$search_column) {
                            $query2->Where("$tbl_documents_columns.document_type_column_id",'=',$search_column); 
                            $query2->Where("$tbl_documents_columns.document_column_value",'=',$datesearch);
                       });
                    $datesearch = str_replace("-", "/",$datesearch);
                    $query1->orWhere(function($query2) use($datesearch,$tbl_documents_columns,$search_column) {
                            $query2->Where("$tbl_documents_columns.document_type_column_id",'=',$search_column); 
                            $query2->Where("$tbl_documents_columns.document_column_value",'=',$datesearch);
                       });

                    $datesearch = custom_date_Format($search,"m-d-Y");
                    $query1->orWhere(function($query2) use($datesearch,$tbl_documents_columns,$search_column) {
                            /*$query2->Where("$tbl_documents_columns.document_column_type",'=','Date'); */
                            $query2->Where("$tbl_documents_columns.document_type_column_id",'=',$search_column); 
                            $query2->Where("$tbl_documents_columns.document_column_value",'=',$datesearch);
                       });
                    $datesearch = str_replace("-", "/",$datesearch);
                    $query1->orWhere(function($query2) use($datesearch,$tbl_documents_columns,$search_column) {
                            $query2->Where("$tbl_documents_columns.document_type_column_id",'=',$search_column);  
                            $query2->Where("$tbl_documents_columns.document_column_value",'=',$datesearch);
                       });

                    $datesearch = custom_date_Format($search,"Y-m-d");
                    $query1->orWhere(function($query2) use($datesearch,$tbl_documents_columns,$search_column) {
                            $query2->Where("$tbl_documents_columns.document_type_column_id",'=',$search_column); 
                            $query2->Where("$tbl_documents_columns.document_column_value",'=',$datesearch);
                       });
                    $datesearch = str_replace("-", "/",$datesearch);
                    $query1->orWhere(function($query2) use($datesearch,$tbl_documents_columns,$search_column) {
                            $query2->Where("$tbl_documents_columns.document_type_column_id",'=',$search_column); 
                            $query2->Where("$tbl_documents_columns.document_column_value",'=',$datesearch);
                       });
                            }
                            
                               
                        } 
                       
                        $query1->orWhere(function($query2) use($search,$tbl_documents_columns,$search_column,$search_dynamic_col) 
                        {
                                if($search_dynamic_col)
                                {
                                $query2->Where("$tbl_documents_columns.document_type_column_id",'=',$search_column); 
                                }
                                $query2->Where("$tbl_documents_columns.document_column_value",'LIKE','%'.$search.'%');
                        }); 
                        
                        

                           
                    }
                    else
                    {
                        //print_r("else");
                        $query1->orWhere($value,'LIKE','%'.$search.'%');        
                    }
                  }
                

                
               /* */

               
            });
        }
        
        // Ajax order by works
        $order = (isset($_POST['order'][0]['column']))?$_POST['order'][0]['column']:1;
        $direct = (isset($_POST['order'][0]['dir']))?$_POST['order'][0]['dir']:'DESC';
        $data_item = (isset($_POST['columns'][$order]['data']))?$_POST['columns'][$order]['data']:'';
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
          case 'document_path':
          $table_column = $table.'.'.'document_path';
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
          case 'document_checkout_date':
          $table_column = $table.'.'.'document_checkout_date';
          break;
          case 'document_modified_by':
          $table_column = $table.'.'.'document_modified_by';
          break;
          default:
          $table_column = 'tbl_departments'.'.'.'department_name';
          break;
        }
        
            // Get data by department wise
            if(Auth::user()->user_role == Session::get("user_role_group_admin") || Auth::user()->user_role == Session::get("user_role_regular_user")) 
            {
               
                $query->whereIn($table.'.department_id',$loggedUsersdepIds);
            }
            //check user = private user, fetch only the docs of that user
            else if(Auth::user()->user_role == Session::get("user_role_private_user"))
            {
                $query->where($table.'.'.'document_ownership',Auth::user()->username);
            }
            else//super admin
            {
                $query;
            }
           
            //filter according to radio button in listview(expiry)
            switch($filter)
            {

            case trans('language.exclude'): //docs not expired
            {
            $query->where($table.'.'.'document_expiry_date','>',date('Y-m-d'))->orWhere($table.'.'.'document_expiry_date','=',null);
            }
            break;
            case trans('language.expired')://expired docs only fetch
            {
            $query->where($table.'.'.'document_expiry_date','<=',date('Y-m-d'));
            }
            break;
            case trans('language.all')://all docs
            {
            $query;
            }
            break;
            case trans('language.expire_soon')://expire soon docs
            {
            $datetomarrow = new DateTime('tomorrow');
            $query->where($table.'.'.'document_expiry_date','!=','null')->whereBetween($table.'.'.'document_expiry_date',[$datetomarrow->format('Y-m-d'),Session::get('expiry_date_from_settings')]);
            }
            break;
            case trans('language.assigned')://assigned docs only
            {
            $query->where($table.'.'.'document_status','Review')->where($table.'.'.'document_assigned_to',Auth::user()->username);
            }
            break;
            case trans('language.rejected')://rejected docs only
            {
            $query->where($table.'.'.'document_status','Rejected')->where($table.'.'.'document_created_by',Auth::user()->username);
            }
            break;
            case trans('language.accepted')://accepted docs only
            {
            $query->where($table.'.'.'document_status','Published')->where($table.'.'.'document_created_by',Auth::user()->username)->where($table.'.'.'document_assigned_to','!=',"");
            }
            break;
            case trans('language.assigned_to_whom')://assignedt to docs only
            {
                $query->where($table.'.'.'document_status','Review')
                ->where($table.'.'.'document_created_by',Auth::user()->username)
                ->where($table.'.'.'document_assigned_to','!=',"");
            }
            break;
            case trans('Level 1')://assignedt to docs only
            {                      
                $query->where('tbl_documents_columns.document_column_name','=',$filter)->where('tbl_documents_columns.document_column_value','=','yes');
            }
            break;
            case trans('Level 2')://assignedt to docs only
            {                      
              $query->where('tbl_documents_columns.document_column_name','=',$filter)->where('tbl_documents_columns.document_column_value','=','yes');
            }
            break;
            case trans('Level 3')://assignedt to docs only
            {                      
              $query->where('tbl_documents_columns.document_column_name','=',$filter)->where('tbl_documents_columns.document_column_value','=','yes');
            }
            break;
            }
                //$table_type = 'tbl_document_types';
            //    $query->whereRaw('FIND_IN_SET('.$doclistid.','.$table.'.document_type_id)');
                
           
            
            $query->orderBy("$table_column","$direct");
            //$query->orderBy('tbl_departments.department_name',"$direct");
            if($table_column != "$table.document_id")
            {
                $query->orderBy("$table.document_id",'DESC');
            }
            
            $data['dglist'] = $query->selectRaw($select)->groupBy("$table.document_id")->paginate($length);

          
            $parent=array();
            session::put('child','');
            
            
            return $data;
    } 

    
    
}
