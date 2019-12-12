<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApplicationLogController;
use App\Http\Requests;
use Auth;
use View;
use URL;
use Validator;
use Input;
use Session;
use DB;
use Config;
use Storage;
use Response;
use App\Mylibs\Common;
use App\DocumentsColumnModel as DocumentsColumnModel;
use App\AppsModel as AppsModel;
use App\DocumentsModel as DocumentsModel;
use App\DocumentTypesModel as DocumentTypesModel;
use App\DocumentTypeColumnModel as DocumentTypeColumnModel;
use App\AppLinksModel as AppLinksModel;
use Lang;
use App\CsvDataModel as CsvData;
use App\StacksModel as StacksModel;
use App\TagWordsCategoryModel as TagWordsCategoryModel;
use App\FormModel as FormModel;
class AppsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        Session::put('menuid', '-1');
        $this->middleware(['auth', 'user.status']);

        // Set common variable
        $this->actionName = 'App';
        $this->actionNameRecord = 'App Record';
        $this->docObj     = new Common(); // class defined in app/mylibs 
    }

    public function load_apps()
    {
        if (Auth::user()) { 
            $response = array();
            $positions = '';
            $user_widget = (isset(Auth::user()->app_widgets) && Auth::user()->app_widgets)?unserialize(Auth::user()->app_widgets):array();
            if(isset($user_widget) && $user_widget)
            {
                /*seperated by doublequotes and comma*/
                $positions = $_words = '"'.implode('","', $user_widget).'"';
            }
            
            $query = DB::table('tbl_document_types')->where('is_app',1);
            if($positions != '')
            {
                /*show result based on sort order*/
                $query->orderBy(DB::raw("FIELD(document_type_id, $positions)"));
            }
            $data = $query->get();
            foreach ($data as  $value) {
              $data_count = DocumentsModel::where('document_type_id','=',$value->document_type_id)->count();
              $value->data_count = ($data_count)?$data_count:0;
            }
            if($data)
            {
                $response['data'] = $data;
                $response['status'] = 1;
                $response['widget_postion'] = $user_widget;
            }
            else
            {
                $response['status'] = 0;
            }
            
        }
    
        return json_encode($response);
    }
    
    public function saveAppsWidgetPostion()
    {
        $id = Auth::user()->id; 
        $widget_postion = (Input::has('data'))?Input::get('data'):array();
        $dataToUpdate = array('app_widgets' => serialize($widget_postion));
        $id = Auth::user()->id; 
        if($id)
        {
            DB::table('tbl_users')->where('id', $id)->update($dataToUpdate);
        }
        $json = array('status' => 1,'position'=> $widget_postion);
        return json_encode($json);
        
    }
    
    public function index()
    {   
        // checking wether user logged in or not
        if (Auth::user()) { 
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            $data['apps_types'] = AppsModel::apps_types_old();
           return View::make('pages/apps/index')->with($data);
       } else {
           return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }

    public function list_apps($id=NULL,$app=NULL)
    {   
        // checking wether user logged in or not
        if (Auth::user()) {
            $saved_search = (Input::get('app_saved_search'))?1:0;
            //echo $id;
            $user_permission=Auth::user()->user_permission;
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;
            $query = DB::table('tbl_document_types as tt')->leftjoin('tbl_document_types_columns as tc','tt.document_type_id','=','tc.document_type_id')->select('tc.document_type_column_name','tc.document_type_column_id','tc.document_type_column_type','tc.document_type_options','tt.document_type_name')->where('tt.is_app',1)->where('tt.document_type_id',$id)->orderBy('tc.document_type_column_order','ASC');
            
            $data['heads'] = $query->get();
            $app='';
            foreach ($data['heads'] as $key1 => $value1) 
            {
              $app = $value1->document_type_name;
            }
            $data['app'] = $app;
            $index_fileds = array();
             foreach($data['doctypeApp'] as $val)
            {
                $res = DB::table('tbl_document_types_columns as tc')->select('tc.document_type_column_name','tc.document_type_column_id','tc.document_type_column_type','tc.document_type_options')->where('tc.document_type_id',$val->document_type_id)->orderBy('tc.document_type_column_order','ASC')->get();
                $index_fileds[$val->document_type_id] = (count($res))?$res:array();
            }
            $data['index_fileds'] = $index_fileds;

            $data['id'] = $id;
            $data['app_id'] = $id;
            Session::put('back_app',$app);
            $data['search'] = Input::get("search");
            $data['displayStart'] = (Session::get('app_serach_start') && $saved_search)?Session::get('serach_start'):0;
            $data['rows_per_page'] = (Session::get('app_serach_length'))?Session::get('serach_length'):Session::get('settings_rows_per_page');
            $data['serach_filter'] = (Session::get('app_serach_filter') && $saved_search)?Session::get('serach_filter'):trans('documents.radio_all');
            $data['search_text'] = (Session::get('app_search_text') && $saved_search)?Session::get('search_text'):'';
            
            return view::make('pages/apps/list')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function my_apps_filter()
    {
      $id = Input::get("id");
      $length       =   Input::get("length");
      $start        =   Input::get("start");
      $currentPage = ($start)?($start/$length)+1:1;
      \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($currentPage) {
        return $currentPage;
      });
      
      $filter_val = (isset($_POST['filter']))?$_POST['filter']:[];

      $search = (isset($_POST['search']['value']))?trim($_POST['search']['value']):'';
      $order = (isset($_POST['order'][0]['column']))?$_POST['order'][0]['column']:1;
      $direct = (isset($_POST['order'][0]['dir']))?$_POST['order'][0]['dir']:'DESC';
      $data_item = (isset($_POST['columns'][$order]['data']))?$_POST['columns'][$order]['data']:'';

      Session::put('app_serach_length',$length);
      Session::put('app_serach_start',$start);
      Session::put('app_serach_doc_type',$id);
      Session::put('app_serach_filter',$filter_val);
      //Session::put('app_serach_view',$view);
      Session::put('app_search_text',$search);
      Session::put('app_serach_order',$order);
      Session::put('app_serach_direct',$direct);

      DB::enableQueryLog();
      $table_column = 'tbl_documents_columns.document_column_name';
      $dynamic_col =0;

      switch($data_item)
        {
          case 'updated_at':
          $table_column = 'tdc.updated_at';
          break;
          case 'document_modified_by':
          $table_column = 'tdc.document_modified_by';
          break;
          case 'created_at':
          $table_column = 'tdc.created_at';
          break;
          default:
          $table_column = 'tdc.document_column_value';
          $dynamic_col = 1;
          break;
        }

      $query1 = DB::table('tbl_documents as tm')
      ->join('tbl_document_types as tt','tm.document_type_id','=','tt.document_type_id');
      
      $query1->join('tbl_document_types_columns as tc','tc.document_type_id','=','tt.document_type_id');
      $query1->join('tbl_documents_columns as tdc', function($join) use($dynamic_col,$data_item){
                    $join->on('tc.document_type_column_id','=','tdc.document_type_column_id');
                    $join->on('tdc.document_id','=','tm.document_id');

                    if($dynamic_col)
                    {
                      $join->where('tdc.document_type_column_id','=',$data_item);
                    }

                  });
        
        $query1->where('tc.document_type_id',$id);
        $query1->where('tm.document_type_id',$id);

      //normal search
      if($search)
      {
        $query1->where('tdc.document_column_value','LIKE','%'.$search.'%');
      }

      if($filter_val)
      {
        $query1->where(function ($query2) use ($filter_val) {
        foreach ($filter_val as $value) 
        {
          //search options search
          if($value['search_option'] == 'and'){
              // search_option="AND"
              $queryWhere    = 'where';
              $whereBetween  = 'whereBetween';
              
          }
          else if($value['search_option'] == 'or')
          {
              // search_option="OR"
              $queryWhere    = 'orWhere';
              $whereBetween  = 'whereBetween';
              
          }
          $type = isset($value['type'])?$value['type']:'';
          if($type == 'date')
          {
            
            $query2->queryWhere('tdc.document_column_name',$value['name']);
            $from = date("Y-m-d",strtotime($value['from']));
            $to = date("Y-m-d",strtotime($value['to']));
            
            if($value['from'] && $value['to'])//date between
            {
              $query2->whereBetween('tdc.document_column_value',[$from,$to]);
            }
            elseif((isset($value['to'])) && $value['to'])
            {
              $query2->whereDate('tdc.document_column_value','<=',$value['to']);
            }
            elseif((isset($value['from'])) && $value['from'])
            {
              $query2->whereDate('tdc.document_column_value','>=',$value['from']);
            }
          }

          elseif($type == 'time')
          {
            
            $query2->Where('tdc.document_column_name',$value['name']);

            if($value['from'] && $value['to'])//date between
            {
              $from = date("H:i",strtotime($value['from']));
              $to = date("H:i",strtotime($value['to']));
              $query2->whereBetween('tdc.document_column_value', [trim($from),trim($to)]);
            }
            elseif((isset($value['to'])) && $value['to'])
            {
              $query2->where('tdc.document_column_value','=',trim($value['to']));
            }
            elseif((isset($value['from'])) && $value['from'])
            {
              $query2->where('tdc.document_column_value','=',trim($value['from']));
            }
          }
          //index field = all index
          elseif($type == 'all')//search in all index fields
          {
            if((isset($value['value'])) && $value['value'])
            {
              /*$query2->$queryWhere('tdc.document_column_value','LIKE','%'.$value['value'].'%');*/
               $query2->$queryWhere(function ($query3) use ($value) {
                $query3->Where('tdc.document_column_value','LIKE','%'.$value['value'].'%');
              });
            }
          }
          //others
          else
          {
            if((isset($value['value'])) && $value['value'])
            {
              /*$query2->Where('tdc.document_column_name',$value['name']);
              $query2->$queryWhere('tdc.document_column_value','LIKE','%'.$value['value'].'%');*/

              $query2->$queryWhere(function ($query3) use ($value) {
                $query3->Where('tdc.document_column_name',$value['name']);
                $query3->Where('tdc.document_column_value','LIKE','%'.$value['value'].'%');
              });
            }
          }
        }
        });
      }
      /* $query1->get();
      $queries = DB::getQueryLog();
      print_r($queries);
    exit();*/

    /*commented for index sorting*/
    

        $type_of_col = DB::table('tbl_documents_columns')->select('document_column_type')->where('document_type_column_id',$data_item)->first();
        

        $dp = DB::getTablePrefix();
            
            if($dynamic_col)
            {
              switch($type_of_col->document_column_type)
              {
                case 'number':
                {
                  $order_by = "CAST(" . $dp . "$table_column AS unsigned) $direct";
                  $query1->orderByRaw($order_by);
                }
                break;
                case 'text':
                case 'textarea':
                case 'radio':
                case 'label':
                case 'select':
                case 'checkbox':
                case 'email':
                case 'label':
                
                {
                  $order_by = "CAST(" . $dp . "$table_column AS CHAR) $direct";
                  $query1->orderByRaw($order_by);
                }
                break;
                
                case 'time':
                
                {
                  $order_by = "CAST(" . $dp . "$table_column AS TIME) $direct";
                  $query1->orderByRaw($order_by);
                }
                break;
                case 'date':
                {
                  

                  $order_by = "CAST(" . $dp . "$table_column AS date) $direct";
                  $query1->orderByRaw($order_by);
                  
                }
                break;
                default:
                {
                  $order_by = "($table_column) $direct";
                  $query1->orderByRaw($order_by);
                }
              }
            }
            else
            {
              $query1->orderBy("$table_column","$direct");
            }


        

        $data1 = $query1->groupBy('tm.document_id')->paginate($length);
        $queries = DB::getQueryLog();
        $count_all = ($data1)?$data1->total():0;
      $data_table = array();

            foreach ($data1 as $value) 
            {
                $row_d = array(); 
                $document_id = $value->document_id;
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
                    'tc.document_type_column_name'
                    )
                ->where('tc.document_type_id',$id)
                ->orderBy('tc.document_type_column_order','ASC');
                $data2 = $query->get();
                $action = '';
                $action.='<input name="checkbox[]" type="checkbox" value="'.$document_id.'" id="chk'.$document_id.'" class="checkBoxClass">';
                $action .='&nbsp;&nbsp;&nbsp;';
                $action .= '<a href="'.url("viewappdata/".$value->document_type_id."/".$document_id).'" app_id="'.$value->document_type_id.'" title="View & Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                
                $action .='&nbsp;&nbsp;&nbsp;';

                $action .= '<a href="'.url("link_to_doc/".$value->document_type_id."/".$document_id).'" app_id="'.$value->document_type_id.'" data-doc_id="'.$document_id.'" title="Create a transaction record" class="duplicate_to_doc"><i class="fa fa-clone" aria-hidden="true"></i></a>';
                $action .='&nbsp;&nbsp;&nbsp;';

                 $action .= '<a href="'.url("link_to_doc/".$value->document_type_id."/".$document_id).'" app_id="'.$value->document_type_id.'" data-doc_id="'.$document_id.'" title="Link this record to other documents" class="link_to_doc"><i class="fa fa-link" aria-hidden="true"></i></a>';
                $action .='&nbsp;&nbsp;&nbsp;';

                $action .= '<a href="'.url("related_app_doc/".$value->document_type_id."/".$document_id).'" app_id="'.$value->document_type_id.'" data-doc_id="'.$document_id.'" title="View Transactions" class=""><i class="fa fa-files-o" aria-hidden="true"></i></a>';
                $action .='&nbsp;&nbsp;&nbsp;';

                 

                $action .= '&nbsp;<i class="fa fa-trash delrow" data-id="'.$document_id.'" app_id="'.$value->document_type_id.'" title="Delete" style="color: red; cursor:pointer;"></i>';
                
                $row_d["action"] = $action;
                $row_d["updated_by"] = $value->document_modified_by;
                $row_d["created_at"] = dtFormat($value->created_at);
                $row_d["updated_at"] = dtFormat($value->updated_at);
                
                foreach ($data2 as $value2) 
                {
                      $value_column = ucfirst($value2->document_column_value);
                      $row_d["$value2->document_type_column_id"] = ($value_column)?$value_column:'-';
                }
                
                $data_table[] = $row_d;
            }

            /*sorting section index array_data_table*/
            
            /*if(is_numeric($data_item))
            {
              $data_table = $this->array_sort($data_table,$data_item,$direct);
            }*/

            $output = array(
                  "draw" =>  Input::get('draw'),
                  "recordsTotal" => $count_all,
                  "recordsFiltered" => $count_all,
                  "data" => $data_table,
                  "queries" => $queries
                );
                echo json_encode($output);
                exit();
    }

     public function previous_versions_filter()
    {
      $app_id = Input::get("app_id");
      $doc_id = Input::get("doc_id");
      $length       =   Input::get("length");
      $start        =   Input::get("start");
      $currentPage = ($start)?($start/$length)+1:1;
      \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($currentPage) {
        return $currentPage;
      });
      
      $filter_val = (isset($_POST['filter']))?$_POST['filter']:[];

      $search = (isset($_POST['search']['value']))?trim($_POST['search']['value']):'';
      $order = (isset($_POST['order'][0]['column']))?$_POST['order'][0]['column']:1;
      $direct = (isset($_POST['order'][0]['dir']))?$_POST['order'][0]['dir']:'DESC';
      $data_item = (isset($_POST['columns'][$order]['data']))?$_POST['columns'][$order]['data']:'';

        $select ="tia.*";
        $query = DB::table('tbl_documents_columns_archive as tia');
        $query->selectRaw($select);
        $query->orderBy('tia.document_column_id', 'DESC');
        $query->where('tia.document_id','=',$doc_id);
        $data1 = $query->groupBy('tia.duplicate_ref_id')->paginate($length);
        $queries = DB::getQueryLog();
        $count_all = ($data1)?$data1->total():0;
        $data_table = array();
        foreach ($data1 as $value) 
            {
              $row_d = array(); 
                $document_id = $value->document_id;
                $duplicate_ref_id = $value->duplicate_ref_id;
                $query = DB::table('tbl_document_types_columns as tc')
                ->leftJoin('tbl_documents_columns_archive as tdc', function($join) use($document_id,$duplicate_ref_id){
                    $join->on('tc.document_type_column_id','=','tdc.document_type_column_id');
                    $join->where('tdc.document_id','=',$document_id);
                    $join->where('tdc.duplicate_ref_id','=',$duplicate_ref_id);
                  })
                ->select('tdc.document_column_value',
                    'tc.document_type_column_id',
                    'tdc.document_column_name',
                    'tdc.document_column_modified_by',
                    'tdc.document_column_created_by',
                    'tdc.updated_at',
                    'tdc.document_column_id',
                    'tdc.document_id',
                    'tc.document_type_id',
                    'tc.document_type_column_name'
                    )
                ->where('tc.document_type_id',$app_id)
                ->orderBy('tc.document_type_column_order','ASC');
                $data2 = $query->get();
                $action = '';
				 $action.='<input name="checkbox[]" type="checkbox" value="'.$document_id.'" id="chk'.$document_id.'"  data-duplicate_ref_id="'.$duplicate_ref_id.'" class="checkBoxClass">';
                $action .='&nbsp;&nbsp;&nbsp;';
                $action .= '<a href="'.url("viewappdata/".$app_id."/".$document_id).'?reference_id='.$duplicate_ref_id.'" app_id="'.$app_id.'" data-duplicate_ref_id="'.$duplicate_ref_id.'"  title="View & Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                
                $action .='&nbsp;&nbsp;&nbsp;';

                $action .= '&nbsp;<i class="fa fa-trash delrow1" data-id="'.$document_id.'" app_id="'.$app_id.'" data-duplicate_ref_id="'.$duplicate_ref_id.'" title="Delete" style="color: red; cursor:pointer;"></i>';
                $row_d["action"] = $action;
                $row_d["updated_by"] = $value->document_column_created_by;
                $row_d["created_at"] = dtFormat($value->created_at);
                $row_d["updated_at"] = dtFormat($value->updated_at);
                
                foreach ($data2 as $value2) 
                {
                      $value_column = ucfirst($value2->document_column_value);
                      $row_d["$value2->document_type_column_id"] = ($value_column)?$value_column:'-';
                }
                
                $data_table[] = $row_d;
            }
      $output = array(
                  "draw" =>  Input::get('draw'),
                  "recordsTotal" => $count_all,
                  "recordsFiltered" => $count_all,
                  "data" => $data_table,
                  "queries" => $queries
                );
                echo json_encode($output);
                exit();

    }
    /*public function array_sort($array, $on, $direct)
    {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }
            //date cases
            if($on == 'created_at' || $on == 'updated_at')
            {
                
                switch ($direct) {
                    case 'asc':
                        usort($sortable_array, array($this,'date_asort'));
                        break;
                    case 'desc':
                        usort($sortable_array, array($this,'date_rsort'));
                        break;
                }
                
            }
            else
            {
                switch ($direct) {
                    case 'asc':
                        asort($sortable_array);
                        break;
                    case 'desc':
                        arsort($sortable_array);
                        break;
                }
            }
            

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }
    public function date_asort($a, $b) {
        return strtotime($a) - strtotime($b);
    }
    public function date_rsort($a, $b) {
        return strtotime($b) - strtotime($a);
    }*/


    public function deleteSubmittedIndexvalue()
    {
        if (Auth::user()) 
        {
            $id = Input::get('id');

			$app_id = (Input::get('app_id'))?Input::get('app_id'):0;
      $reference_id = (Input::get('reference_id'))?Input::get('reference_id'):0;

      $appsData= AppsModel:: find($app_id);
      $app_name = $appsData->document_type_name;
      $user = Auth::user()->username;
      $tbl_documents_modl= DocumentsModel::find($id);
      $result = AppsModel::form_inputs($app_id,$id,$reference_id);
      $loop=0;
      $audit_text='';
      foreach ($result as $key => $value) {
        $loop++;
        if($loop ==1)
          {
            $audit_text =' ('.$value->document_type_column_name.' = '.$value->document_column_value.', '.$tbl_documents_modl->created_at.')';
            
          }
        $actionMsg = Lang::get('language.delete_action_msg');
        $actionDes = $this->docObj->stringReplace($this->actionNameRecord,$app_name.$audit_text,$user,$actionMsg);
        $audit_result = (new AuditsController)->appRecordslog(Auth::user()->username,$id,'Delete',$actionDes);
      }
			if($reference_id)
			{
				$delete_col = DB::table('tbl_documents_columns_archive')->where('document_id',$id)->where('duplicate_ref_id',$reference_id)->delete();
			}
			else
			{
			$delete_doc = DB::table('tbl_documents')->where('document_id',$id)->delete();
            $delete_col = DB::table('tbl_documents_columns')->where('document_id',$id)->delete();
            }
            if($delete_col || $delete_doc)
            {
              echo "1";
            }
            else
            {
              echo "0";
            }
        }
        else 
        {
          return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function saveAppValues(Request $request)
    {
      /*echo "<pre>";
      print_r($_POST);
      exit;*/
      if (Auth::user()) 
      {
        $action = Input::get('hidd_action');
        $app_id = (Input::get('app_id'))?Input::get('app_id'):0;
        $doc_id = (Input::get('hidd_doc'))?Input::get('hidd_doc'):0;
        $app_name = (Input::get('app_name'))?Input::get('app_name'):'App';
        $reference_id = (Input::get('reference_id'))?Input::get('reference_id'):0;
		$tbl_documents_columns = "tbl_documents_columns";
	  if($reference_id)
	  {
		  $tbl_documents_columns = "tbl_documents_columns_archive";
	  }
        $appsData= AppsModel:: find($app_id);
        $app_name = $appsData->document_type_name;
        $user = Auth::user()->username;
        $result = AppsModel::form_inputs($app_id,$doc_id,$reference_id);
         /*echo "<pre>";
        print_r($result);
        echo "</pre>";
        exit;*/
        $audit_text = '';
        $audit_text_duplicate = '';

        if($action == 'add' || !$doc_id)
        {
          
          $tbl_documents_modl=new DocumentsModel;
          $tbl_documents_modl->document_type_id      = $app_id;
          $tbl_documents_modl->document_name         = '';
          $tbl_documents_modl->document_file_name    = '';
          $tbl_documents_modl->parent_id             = Session::get('SESS_parentIdd');
          $tbl_documents_modl->department_id         = '';
          $tbl_documents_modl->stack_id              = '';
          $tbl_documents_modl->document_version_no   = "1.0";
          $tbl_documents_modl->document_ownership    = Auth::user()->username;
          $tbl_documents_modl->document_created_by   = Auth::user()->username;
          $tbl_documents_modl->document_modified_by  = Auth::user()->username;
          $tbl_documents_modl->document_tagwords     = '';
          $tbl_documents_modl->document_no           = '';
          $tbl_documents_modl->document_path         = '';
          $tbl_documents_modl->document_status       = "Published";
          $tbl_documents_modl->created_at            = date('Y-m-d h:i:s');
          $tbl_documents_modl->updated_at            = date('Y-m-d h:i:s');
          $tbl_documents_modl->document_size         = '';
          $tbl_documents_modl->document_expiry_date  = '';
          $tbl_documents_modl->save();
          $doc_id = $tbl_documents_modl->document_id;

           
        
        
        }
        else
        {
          $tbl_documents_modl= DocumentsModel::find($doc_id);
        }

        

        $duplicate_ref_id =0;
        $loop=0;
        foreach ($result as $key => $value) 
        {
         $loop++;
         if($action == 'duplicate')
         {
          if(!$duplicate_ref_id)
          {
            $where = array('document_id' => $doc_id);
            $duplicate_ref_id = DB::table('tbl_documents_columns_archive')->where($where)->max('duplicate_ref_id');
            $duplicate_ref_id = ($duplicate_ref_id)?$duplicate_ref_id+1:1;
          }
          $archive = array();
          $archive['document_id'] = $doc_id;
          $archive['duplicate_ref_id'] = $duplicate_ref_id;
          $archive['document_type_column_id'] = $value->document_type_column_id;
          $archive['document_column_name'] = $value->document_type_column_name;
          $archive['document_column_value'] = $value->document_column_value;
          $archive['document_column_type'] = $value->document_type_column_type;
          $archive['document_file_name'] = $value->document_file_name;
          $archive['document_file_size'] = $value->document_file_size;
          $archive['document_column_mandatory'] = $value->document_type_column_mandatory;
          $archive['document_column_created_by'] = $value->document_type_column_created_by;
          $archive['document_column_modified_by'] = $value->document_type_column_modified_by;
          $archive['created_at'] = $value->created_at;
          $archive['updated_at'] = $value->updated_at;
          $chk = DB::table('tbl_documents_columns_archive')->insert($archive);
         }

          $row = array();
          $row['document_id'] = $doc_id;
          $row['document_type_column_id'] = $value->document_type_column_id;
          $row['document_column_name'] = $value->document_type_column_name;
          if($value->is_options)//check is option ==1
          {
            if(Input::get($value->document_type_column_id))
            {
              //array convert to string
              $row['document_column_value'] = implode(',', Input::get($value->document_type_column_id));
            }
          }
          else
          {
            $row['document_column_value'] = Input::get($value->document_type_column_id);
          }
          
          /*if type = date then change document column value to date(y-m-d) format*/

          if($value->document_type_column_type =='date')
          {
              $cal_date = Input::get($value->document_type_column_id);
              $date = ($cal_date)?date('Y-m-d',strtotime($cal_date)):'';
              $row['document_column_value'] = $date;
          }

          //file
          if($value->document_type_column_type == 'file')
          {
            $form_response_file = Input::get('randname'.$value->document_type_column_id);
            $form_response_value = Input::get('name'.$value->document_type_column_id);
            $form_response_file_size = Input::get('size'.$value->document_type_column_id);
           
            if($form_response_file)
            {
              $form_response_file = implode(',',$form_response_file);
              $row['document_file_name'] = $form_response_file;
            }
           
            if($form_response_value)
            {
              $form_response_value = implode(',',$form_response_value);
              $row['document_column_value'] = $form_response_value;
            }
            
            if($form_response_file_size)
            {
              $form_response_file_size = implode(',',$form_response_file_size);
              $row['document_file_size'] = $form_response_file_size;
            }
          }

          $row['document_column_type'] = $value->document_type_column_type;
          $row['document_column_mandatory'] = $value->document_type_column_mandatory;
          $row['document_column_created_by'] = Auth::user()->username;
          $row['document_column_modified_by'] = Auth::user()->username;
          $row['created_at'] =date("Y-m-d H:i:s");
          $row['updated_at'] = date("Y-m-d H:i:s");

           
          if($loop ==1)
          {
            $audit_text_duplicate =' ('.$value->document_type_column_name.' = '.$value->document_column_value.', '.$tbl_documents_modl->created_at.')';
            if(isset($row['document_column_value']))
            {
              $audit_text =' ('.$value->document_type_column_name.' = '.$row['document_column_value'].', '.$tbl_documents_modl->created_at.')';
            }
            
          }
          if($value->document_column_id)
          {
             $chk = DB::table("$tbl_documents_columns")->where('document_column_id',$value->document_column_id)->update($row);

          }
          else
          {
            $chk = DB::table("$tbl_documents_columns")->insert($row);
           /* 
              echo "<pre>";
        print_r($row);
        echo "</pre>";*/
          }
          
        }
        $msg = ($action == 'add')?trans('language.saved_successfully'):trans('language.updated_successfully');
       
        if($action == 'add')
        {
          $actionMsg = Lang::get('language.save_action_msg');
           $actionDes = $this->docObj->stringReplace($this->actionNameRecord,$app_name.$audit_text,$user,$actionMsg);
           $audit_result = (new AuditsController)->appRecordslog(Auth::user()->username,$doc_id,'Add',$actionDes);
        }
        else if($action == 'edit')
        {
          $actionMsg = Lang::get('language.update_action_msg');
          $actionDes = $this->docObj->stringReplace($this->actionNameRecord,$app_name.$audit_text,$user,$actionMsg);
          $audit_result = (new AuditsController)->appRecordslog(Auth::user()->username,$doc_id,'Edit',$actionDes);
        }
        else if($action == 'duplicate')
        {
          $actionMsg = Lang::get('language.duplicate_action_msg');
          $actionDes = $this->docObj->stringReplace($this->actionNameRecord,$app_name.$audit_text_duplicate,$user,$actionMsg);
          $audit_result = (new AuditsController)->appRecordslog(Auth::user()->username,$doc_id,'Edit',$actionDes);

          $actionMsg = Lang::get('language.save_action_msg');
          $actionDes = $this->docObj->stringReplace($this->actionNameRecord,$app_name.$audit_text,$user,$actionMsg);
          $audit_result = (new AuditsController)->appRecordslog(Auth::user()->username,$doc_id,'Add',$actionDes);
        
        }

        if(Input::get('save_close') == trans('language.save_close'))
        {
            return redirect('appslistview/'.$app_id)->with('data', $msg);
        }
        else
        {
            return redirect()->back()->with('data', $msg);
        }
        
        exit();
        
      }
      else
      {
        
      }
    }
    public function view_app()
    {
      if (Auth::user()) {
            $action = Input::get('action');
            $app_id = Input::get('appid');
            if(Input::get('doc'))
            {
              $data['doc'] = Input::get('doc');
            }
            $data['app_details'] = DB::table('tbl_document_types')->where('document_type_id',$app_id)->first();
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            $data['apps_types'] = AppsModel::apps_types();
            $data['action'] = $action;
            return view::make('pages/apps/app')->with($data);
        }
        else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function view_apps() {
        if(Auth::user()) {
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            return View::make('pages/apps/view')->with($data);
        }
        else {

        }
    }
      public function load_app()
    {
      $action = (Input::get('action'))?Input::get('action'):'add';
      $form_id = (Input::get('form_id'))?Input::get('form_id'):0;
      $doc_id = (Input::get('doc_id'))?Input::get('doc_id'):0;
      $reference_id = (Input::get('reference_id'))?Input::get('reference_id'):0;
      $json = array();
      $inputs = array();
      
        $result = AppsModel::form_inputs($form_id,$doc_id,$reference_id);
        foreach ($result as $key => $value) 
        {
          $row = array();   
          $row['type'] = ($value->form_input_type_value)?$value->form_input_type_value:'text';
          $row['label'] = ($value->document_type_column_name)?$value->document_type_column_name:'';
          $row['input_id'] = ($value->document_type_column_id)?$value->document_type_column_id:0;
          $selected = ($value->document_column_value)?$value->document_column_value:'';
          $row['selected'] = $selected;
          $row['col_id'] = ($value->document_column_id)?$value->document_column_id:0;
          $row['type_id'] = ($value->form_input_type)?$value->form_input_type:0;
          $row['input_type_name'] = ($value->form_input_type_name)?$value->form_input_type_name:'';
          $row['req'] = ($value->document_type_column_mandatory)?$value->document_type_column_mandatory:0;
          $row['is_options'] = (@$value->is_options)?@$value->is_options:0;
          $row['multiple'] = (@$value->form_input_file_multiple)?@$value->form_input_file_multiple:0;
          $row['is_required'] = (@$value->document_type_column_mandatory)?@$value->document_type_column_mandatory:0;
          $row['type_common'] = (@$value->form_input_type_common)?@$value->form_input_type_common:'text';
          $defaults = (@$value->document_type_default_value)?@$value->document_type_default_value:'';
          $row['defaults'] = $defaults;
          $choices = (@$value->document_type_options)?explode(',',@$value->document_type_options):array(); 
          $row['choices'] = $choices;
          $row['link_to_app'] = (@$value->document_type_link)?@$value->document_type_link:0;
          $row['link_to_app_column'] = (@$value->document_type_link_column)?@$value->document_type_link_column:0;
          $row['auto_complete_url']=  ($row['link_to_app'])?URL('auto_complete_document_column'):'';
          $row['col_1'] = (@$value->col_1)?@$value->col_1:'';
          $row['col_2'] = (@$value->col_2)?@$value->col_2:'';
          $row['col_3'] = (@$value->col_3)?@$value->col_3:'';
          if(($row['link_to_app'] && $row['link_to_app_column']) && ($row['type'] == 'checkbox' || $row['type'] == 'radio' || $row['type'] == 'select'))
          {
          $where = array('tdtc.document_type_id'=>$row['link_to_app'],'tdtc.document_type_column_id'=>$row['link_to_app_column']);  
          $query = DB::table('tbl_document_types_columns as tdtc')->selectRaw("tdtc.document_type_column_type,tdtc.document_type_options,tdtc.document_type_default_value");
          $query->where($where);
          $results =    $query->first();  
          if($results)
          {
             $type = $results->document_type_column_type;
            if($type == 'checkbox' || $type == 'radio' || $type == 'select')
            {
                $data = array();
                $options = explode(',',$results->document_type_options);
                $default = $results->document_type_default_value;
                $row['choices'] = $options;
                $row['defaults'] = $default;
            }
          }
          }
          $row['files']  = ($value->document_file_name)?$value->document_file_name:'';
          $row['sizes']  = ($value->document_file_size)?$value->document_file_size:0;
          $inputs[] = $row;
        }
      
      
      
      $json['inputs']=  $inputs;
      $json['auto_complete_url']=  URL('document_column_suggession');
      
      return json_encode($json);
    }

     public function auto_complete_document_column(){
        $search = (Input::get('search'))?Input::get('search'):'';
        $link_to_app = (Input::get('link_to_app'))?Input::get('link_to_app'):'';
        $link_to_app_column = (Input::get('link_to_app_column'))?Input::get('link_to_app_column'):'';
        /*$where = array('tdtc.document_type_id'=>$link_to_app,'tdtc.document_type_column_id'=>$link_to_app_column);  
        $query = DB::table('tbl_document_types_columns as tdtc')->selectRaw("tdtc.document_type_column_type,tdtc.document_type_options,tdtc.document_type_default_value");
        $query->where($where);
        $results =    $query->first();  
        if($results)
        {
           $type = $results->document_type_column_type;
          if($type == 'checkbox' || $type == 'radio' || $type == 'select')
          {
              $data = array();
              $options = explode(',',$results->document_type_options);
              $default = $results->document_type_default_value;
              foreach ($options as $key => $value) 
              {
                  $selected=($value == $default)?1:0;
                  $data[] = array("name" => $value,"value" => $value,"selected" => $selected); 
              } 
              return json_encode($data);
          }
        }*/

        $where = array('tb.document_type_column_id'=>$link_to_app_column);
        $select ="tb.document_column_value as name,tb. 
document_column_name as value";
        $query = DB::table('tbl_documents_columns as tb');
        $query->selectRaw($select);
        $query->where($where);
        if($search)
        {
          $query->Where("tb.document_column_value",'LIKE','%'.$search.'%');
        }
        
        $query->groupBy('tb.document_column_value');
        $query->orderBy('tb.document_column_value', 'ASC');
        $results =    $query->get();
        $data = array();
        
         foreach ($results as $key => $value) {
            $data[] = array("name" => $value->name,"value" => $value->value,"selected"=>"09"); 
        }
        return json_encode($data);
    }
    public function edit_apps($app_id=0)
    {   
        // checking wether user logged in or not
        if (Auth::user()) { 
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            $data['apps_types'] = AppsModel::apps_types();
            $data['app_id'] = $app_id;
           return View::make('pages/apps/edit')->with($data);
       } else {
           return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }
    public function checkhasRecords()
    {
      $app_id =Input::get('id');
      $check = DB::table('tbl_documents')->where('document_type_id',$app_id)->exists();
      if($check){
        echo 'exist';
      }
    }
    public function appDelete(Request $request)
    {
      $app_id = Input::get('id');
      $documentType= DocumentTypesModel:: find($app_id);
      if($documentType)
      {
      $del_apps = DB::table('tbl_document_types')->where('document_type_id',$app_id)->delete();
      $docs = DB::table('tbl_documents')->select('document_id')->where('document_type_id',$app_id)->get();
      $del_docs = DB::table('tbl_documents')->where('document_type_id',$app_id)->delete();
      //delete doc columns
      if($del_docs)
      {
        foreach ($docs as $key => $value) {
          DB::table('tbl_documents_columns')->where('document_id',$value->document_id)->delete();
        }
      }
        $name = $documentType->document_type_name;
        $user = Auth::user()->username;
        $actionMsg = Lang::get('language.delete_action_msg');
        $actionDes = $this->docObj->stringReplace($this->actionName,$name,$user,$actionMsg);
        $audit = (new AuditsController)->appslog(Auth::user()->username,$app_id,'Delete',$actionDes);
      }
      echo "deleted";
    }
    public function deleteAppAttached(Request $request)
    {
      $file = Input::get('name');
      $colId = Input::get('colId');
      if($file)
      {
        $update = DB::table('tbl_documents_columns')->where('document_column_id',$colId)->where('document_column_type','file')->update(['document_column_value'=>'','document_file_name'=>'','document_file_size'=>'']);
        if($update)
        {
          $destinationPath = config('app.base_path'); // upload path
          unlink($destinationPath."/".$file);
        }
        return 1;
      } 
    }
      public function save_apps()
    {
        $app_id = (Input::get('app_id'))?Input::get('app_id'):0;
        $action = (Input::get('action'))?Input::get('action'):'add';
        $name = (Input::get('name'))?Input::get('name'):'';
        $description = (Input::get('description'))?Input::get('description'):'';

        $document_type = DB::table('tbl_document_types')->where('document_type_id',$app_id)->first();
        $documentType= DocumentTypesModel::find($app_id);
        if($document_type)
        {
             
        }
        else
        {
            $last_order = DB::table('tbl_document_types')->select('document_type_order')->orderBy('document_type_order','DESC')->first();
            if($last_order)
            {
                $next_order = $last_order->document_type_order+1;
            }
            else
            {
                $next_order = 1;
            }
            $action = 'add';
            $documentType= new DocumentTypesModel;
            $documentType->is_app = 1;
            $documentType->document_type_order = $next_order;
            $documentType->document_type_created_by = Auth::user()->username;
            $documentType->created_at = date('Y-m-d h:i:s');
        }   


        $documentType->document_type_name = $name;
        $documentType->document_type_description = $description;
        $documentType->document_type_column_no = '';
        $documentType->document_type_column_name = '';
        $documentType->updated_at = date('Y-m-d h:i:s');
        $documentType->document_type_modified_by = Auth::user()->username;
        $documentType->save();
        $document_type_id = $documentType->document_type_id; 
        /* reset all rows edit 0*/
        DocumentTypeColumnModel::where('document_type_id', $documentType->document_type_id)->update(['edit' => 0]);
        $column_id_array = (Input::get('column_id'))?Input::get('column_id'):array();
        $document_types_columns = array();
        $order=0;
        $app_col =array();
        foreach ($column_id_array as $key => $value) 
        {
           $order++;
           $documentTypeColum= DocumentTypeColumnModel::find($value);
           if($value)
           {

           }
           else
           {
               $documentTypeColum= new DocumentTypeColumnModel;
               $documentTypeColum->document_type_column_created_by = Auth::user()->username;
           }

           $documentTypeColum->edit = 1;
           $documentTypeColum->document_type_id = $documentType->document_type_id; 
           $documentTypeColum->document_type_column_name = (Input::get('column_name_'.$key))?Input::get('column_name_'.$key):''; 
           $documentTypeColum->document_type_column_type = (Input::get('column_type_'.$key))?Input::get('column_type_'.$key):''; 
           $documentTypeColum->document_type_column_mandatory = (Input::get('checkbox_required_'.$key))?1:0;  
           $documentTypeColum->document_type_column_order = $order; 
           $choice_name_array = (Input::get('choice_name_'.$key))?Input::get('choice_name_'.$key):array();
           $choice_selected_array = (Input::get('choice_selected_'.$key))?Input::get('choice_selected_'.$key):array();
           $document_type_options = implode(',', $choice_name_array);
           $select_options_array = array();
           foreach ($choice_name_array as $key1 => $value1) 
           {
               if(in_array($key1, $choice_selected_array))
               {
                   $select_options_array[] = $value1;
               }
           }
           $select_type_options = implode(',', $select_options_array);
           $documentTypeColum->document_type_options = $document_type_options; 
           $documentTypeColum->document_type_option_visibility = ($choice_selected_array)?1:0; 
           $documentTypeColum->document_type_default_value = $select_type_options; 
           $checkbox_app_linked  = (Input::get('checkbox_app_linked_'.$key))?1:0;
           $documentTypeColum->document_type_link  = ($checkbox_app_linked && Input::get('link_app_id_'.$key))?Input::get('link_app_id_'.$key):0; 
           $documentTypeColum->document_type_link_column  = ($checkbox_app_linked && Input::get('link_app_index_'.$key))?Input::get('link_app_index_'.$key):0; 
           $documentTypeColum->save();
           
           $app_col[$order] = $documentTypeColum->document_type_column_id;
           
            
        }
        /* delete rows edit 0*/
        DocumentTypeColumnModel::where('document_type_id', $documentType->document_type_id)->where('edit',0)->delete();
        /*AppLinksModel::where('fk_app_id', $documentType->document_type_id)->update(['edit' => 0]);*/
        $link_id_array = (Input::get('link_id'))?Input::get('link_id'):array();
        $document_types_columns = array();
        $order=0;
        foreach ($link_id_array as $key => $value) 
        {
           $fk_app_column_id = (Input::get('link_column_type_'.$key))?Input::get('link_column_type_'.$key):0; 
           if(isset($app_col[$fk_app_column_id]))
           {
               $order++;
               $app_links= AppLinksModel::find($value);
               if($app_links)
               {

               }
               else
               {
                   $app_links= new AppLinksModel;
               }
               $app_links->edit = 1;
               $app_links->fk_app_id = $documentType->document_type_id; 
               $app_links->fk_app_column_id = $app_col[$fk_app_column_id]; 
               $app_links->document_type_id = (Input::get('document_type_'.$key))?Input::get('document_type_'.$key):''; 
               $app_links->fk_document_column_id = (Input::get('column_type_option_'.$key))?Input::get('column_type_option_'.$key):''; 
               $app_links->save();
          }
            
        }
        /*AppLinksModel::where('fk_app_id', $documentType->document_type_id)->where('edit',0)->delete();*/
        $name = Input::get('name');
        $user = Auth::user()->username;
                
        if($action == 'add')
        {
           $actionMsg = Lang::get('language.save_action_msg');
           $actionDes = $this->docObj->stringReplace($this->actionName,$name,$user,$actionMsg);
           (new AuditsController)->appslog(Auth::user()->username,$document_type_id,'Add',$actionDes);
        }
        else if($action == 'edit')
        {
          $actionMsg = Lang::get('language.update_action_msg');
          $actionDes = $this->docObj->stringReplace($this->actionName,$name,$user,$actionMsg);
          $result = (new AuditsController)->appslog(Auth::user()->username,$document_type_id,'Edit',$actionDes);
        }
        $json = array();
        $json['status'] = 1;
        $message = '<div class="alert alert-success text-center">'.trans('apps.save_success').'</div>';
        $json['message'] = $message;
        $json['url'] = (!$app_id)?url('appsEdit/'.$document_type_id):'';
        return json_encode($json);
    }


    public function document_column_suggession(){
        $search = (Input::get('search'))?Input::get('search'):'';
        $select ="tb.document_type_column_name as name,tb. 
document_type_options as value";
        $query = DB::table('tbl_document_types_columns as tb');
        $query->selectRaw($select);
        $query->Where("tb.document_type_column_name",'LIKE','%'.$search.'%');
        $query->groupBy('tb.document_type_column_name');
        $query->orderBy('tb.document_type_column_name', 'ASC');
        $results =    $query->get();
        $data = array();
        
         foreach ($results as $key => $value) {
            $data[] = array("name" => $value->name,"value" => $value->value); 
        }
        return json_encode($data);
    }

     public function load_app_form(){
        $app_id = (Input::get('app_id'))?Input::get('app_id'):0;
        $action = (Input::get('action'))?Input::get('action'):'add';
        $description = (Input::get('description'))?Input::get('description'):'';

        $document_type = DB::table('tbl_document_types')->where('document_type_id',$app_id)->first();

        $document_type_column = DB::table('tbl_document_types_columns')->where('document_type_id',$app_id)->get();
        $loop=0;
       /* $app_col =array();
        foreach ($document_type_column as $key => $value) 
        {
           $app_col[$value->document_type_column_id] = ++$loop;
        }*/
        $form = array();
        
        $json = array();
        $json['status'] = 1;
        $json['app_name'] = ($document_type)?$document_type->document_type_name:'';
        $json['description'] = ($document_type)?$document_type->document_type_description:'';
        $json['document_type_column'] = $document_type_column;
        $document_type = DocumentTypesModel::select('document_type_id','document_type_name as name')->where('is_app',0)->orderBy('document_type_name','ASC')->get();
        $document_types = array();
        foreach ($document_type as $key => $value) 
        {
            $row = array();
            $row['id'] = $value->document_type_id;
            $row['name'] = $value->name;
            $children = ($value->childrens)?$value->childrens:array();
            $childrens = array();
            foreach ($children as $key1 => $value1) 
            {   
                if($value1->document_type_column_name)       
                {   
                $childrens[] = array('id' => $value1->document_type_column_id,'name' => $value1->document_type_column_name);
                }
            } 
            $row['childrens'] = $childrens;
            $document_types[] = $row;
        }
        $json['document_types'] = $document_types;

        $app_link = AppLinksModel::where('fk_app_id',$app_id)->orderBy('id','ASC')->get();
        $app_links = array();
        foreach ($app_link as $key => $value) 
        {
                $row = array();
                $row['app_link_id'] = $value->id;
                $row['app_column_id'] = $value->fk_app_column_id;
                $row['document_type_id'] = $value->document_type_id;
                $row['document_column_id'] = $value->fk_document_column_id;
                $app_links[] = $row;
        }
        $json['app_links'] = $app_links;
        $other_app_type = DocumentTypesModel::select('document_type_id','document_type_name as name')->where('is_app',1)->where('document_type_id','<>',$app_id)->orderBy('document_type_name','ASC')->get();
        $other_app_types = array();
        foreach ($other_app_type as $key => $value) 
        {
            $row = array();
            $row['id'] = $value->document_type_id;
            $row['name'] = $value->name;
            $children = ($value->childrens)?$value->childrens:array();
            $childrens = array();
            foreach ($children as $key1 => $value1) 
            {   
                if($value1->document_type_column_name)       
                {   
                $childrens[] = array('id' => $value1->document_type_column_id,'name' => $value1->document_type_column_name);
                }
            } 
            $row['childrens'] = $childrens;
            $other_app_types[] = $row;
        }
        $json['other_app_types'] = $other_app_types;
        return json_encode($json);
    }

     public function edit_link($app_id=0)
    {   
        // checking wether user logged in or not
        if (Auth::user()) { 
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            $data['apps_types'] = AppsModel::apps_types();
            $data['app_id'] = $app_id;
           return View::make('pages/apps/links')->with($data);
       } else {
           return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }

     public function save_app_links($app_id=0)
    {   
        // checking wether user logged in or not
        if (Auth::user()) { 
        $app_id = (Input::get('app_id'))?Input::get('app_id'):0;    
        AppLinksModel::where('fk_app_id', $app_id)->update(['edit' => 0]);
        $link_id_array = (Input::get('link_id'))?Input::get('link_id'):array();
        $document_types_columns = array();
        $order=0;
        foreach ($link_id_array as $key => $value) 
        {
           /*$fk_app_column_id = (Input::get('link_column_type_'.$key))?Input::get('link_column_type_'.$key):0; */
           $fk_app_column_id = (Input::get('link_column_type'))?Input::get('link_column_type'):0;  
           if($fk_app_column_id)
           {
               $order++;
               $app_links= AppLinksModel::find($value);
               if($app_links)
               {

               }
               else
               {
                   $app_links= new AppLinksModel;
               }
               $app_links->edit = 1;
               $app_links->fk_app_id = $app_id; 
               $app_links->fk_app_column_id = $fk_app_column_id; 
               $app_links->document_type_id = (Input::get('document_type_'.$key))?Input::get('document_type_'.$key):''; 
               $app_links->fk_document_column_id = (Input::get('column_type_option_'.$key))?Input::get('column_type_option_'.$key):''; 
               $app_links->save();
          }
            
        }
        AppLinksModel::where('fk_app_id', $app_id)->where('edit',0)->delete();

        $json = array();
        $json['status'] = 1;
        $message = '<div class="alert alert-success text-center">'.trans('apps.link_save_success').'</div>';
        $json['message'] = $message;
        return json_encode($json);
       } else {
           return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }

    public function viewappdata($app_id=0,$doc_id=0)
    {   
        // checking wether user logged in or not
        if (Auth::user()) { 
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            $data['apps_types'] = AppsModel::apps_types();
            $data['app_id'] = $app_id;
            $data['doc_id'] = $doc_id;
			$data['reference_id'] = (Input::get('reference_id'))?Input::get('reference_id'):0;
            //session for highlight the opendoc on list page view
            Session::put('app_selected_doc_list',$doc_id);
            $data['collapse_sidebar'] = 1;
             $index_fileds = array();
             foreach($data['doctypeApp'] as $val)
            {
                $res = DB::table('tbl_document_types_columns as tc')->select('tc.document_type_column_name','tc.document_type_column_id','tc.document_type_column_type','tc.document_type_options')->where('tc.document_type_id',$val->document_type_id)->orderBy('tc.document_type_column_order','ASC')->get();
                $index_fileds[$val->document_type_id] = (count($res))?$res:array();
            }
            $data['index_fileds'] = $index_fileds;
           return View::make('pages/apps/view')->with($data);
       } else {
           return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }

    public function related_app_doc($app_id=0,$doc_id=0)
    {   
        // checking wether user logged in or not
        if (Auth::user()) { 
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            $data['apps_types'] = AppsModel::apps_types();
            $data['app_id'] = $app_id;
            $data['doc_id'] = $doc_id;
            //session for highlight the opendoc on list page view
            Session::put('app_selected_doc_list',$doc_id);
            $data['collapse_sidebar'] = 1;
             $index_fileds = array();
             foreach($data['doctypeApp'] as $val)
            {
                $res = DB::table('tbl_document_types_columns as tc')->select('tc.document_type_column_name','tc.document_type_column_id','tc.document_type_column_type','tc.document_type_options')->where('tc.document_type_id',$val->document_type_id)->orderBy('tc.document_type_column_order','ASC')->get();
                $index_fileds[$val->document_type_id] = (count($res))?$res:array();
            }
            $data['index_fileds'] = $index_fileds;

            $query = DB::table('tbl_document_types as tt')->leftjoin('tbl_document_types_columns as tc','tt.document_type_id','=','tc.document_type_id')->select('tc.document_type_column_name','tc.document_type_column_id','tc.document_type_column_type','tc.document_type_options','tt.document_type_name')->where('tt.is_app',1)->where('tt.document_type_id',$app_id)->orderBy('tc.document_type_column_order','ASC');
            
            $data['heads'] = $query->get();
           return View::make('pages/apps/related')->with($data);
       } else {
           return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }

   public function load_app_data(){
        $app_id = (Input::get('app_id'))?Input::get('app_id'):0;
        $doc_id = (Input::get('doc_id'))?Input::get('doc_id'):0;
        $action = (Input::get('action'))?Input::get('action'):'add';
        $description = (Input::get('description'))?Input::get('description'):'';

        $document_type = DB::table('tbl_document_types')->where('document_type_id',$app_id)->first();
        $form = array();
        
        $json = array();
        $json['status'] = 1;
        $json['app_name'] = ($document_type)?$document_type->document_type_name:'';
        $document_type = AppsModel::related_doc_types($app_id,$doc_id);
        $document_types = array();
        foreach ($document_type as $key => $value) 
        {
            $row = array();
            $row['id'] = $value->id;
            $row['document_id'] = $value->id;
            $row['doc_name'] = $value->doc_name.'.'.pathinfo($value->doc_file_name, PATHINFO_EXTENSION);
            $row['doc_file_name'] = $value->doc_file_name;
            $row['doc_type'] = $value->doc_type;
            $row['exprstatus'] = 0;
            $row['toplabel'] = '';
            $row['docs'] = '';
            $row['created_at'] = $value->created_at;
            $document_types[] = $row;
        }

        $document_type = AppsModel::related_docs($doc_id);
        foreach ($document_type as $key => $value) 
        {
            $row = array();
            $row['id'] = $value->id;
            $row['document_id'] = $value->id;
            $row['doc_name'] = $value->doc_name.'.'.pathinfo($value->doc_file_name, PATHINFO_EXTENSION);
            $row['doc_file_name'] = $value->doc_file_name;
            $row['doc_type'] = $value->doc_type;
            $row['exprstatus'] = 0;
            $row['toplabel'] = '';
            $row['docs'] = '';
            $row['created_at'] = $value->created_at;
            $document_types[] = $row;
        }
        $json['document_types'] = $document_types;

        return json_encode($json);
    }
    public function importrecords()
    {
      if (Auth::user()) { 
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            $data['appsApp'] = $this->docObj->common_apps();
           return View::make('pages/apps/import')->with($data);
       } else {
           return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }
    //get count of records
    public function appSample(Request $request)
    { 
        if(Input::get('appid')){
        $text = Input::get('selectedText');
        $text = preg_replace('/\s+/', '', $text);//remove spaces
        $query = DB::table('tbl_document_types as tt')->leftjoin('tbl_document_types_columns as tc','tt.document_type_id','=','tc.document_type_id')->select('tc.document_type_column_name','tc.document_type_column_id','tc.document_type_column_type','tc.document_type_options')->where('tt.is_app',1)->where('tt.document_type_id',Input::get('appid'))->orderBy('tc.document_type_column_order','ASC');
          $result = $query->get();
          $csv_header_fields = [];
          if($result)
          {
            foreach ($result as $key => $value) {
              if($value->document_type_column_name)
              {
                $csv_header_fields[] = $value->document_type_column_name;                        
              }
            }
          }
          //return $result;
        $datetime = date("Y-m-d_H-i-s");
        $import_sample_name = Auth::user()->username.Config::get('constants.sample_app').$text.'_'.$datetime;
        $file = config('app.sample_path').$import_sample_name.".csv";
        $destinationPath  = config('app.sample_path'); // sample path
        if(!file_exists($destinationPath))
        {
            //create directory export
            File::makeDirectory($destinationPath, $mode = 0777, true, true);
        }
        if(!file_exists($file))
        {
            //create csv file
            Storage::put($file,'');
        }
        $headers = array('Content-Type: text/plain');
        $out = fopen($file, 'w');
        fputcsv($out, $csv_header_fields);//write first line
        fclose($out);
                    
        $response = array(
            'filename'      => config('app.sample_url').$import_sample_name.".csv"
        );
        echo json_encode($response);

        }
    }
    public function appParseImport()
    {        
      if (Auth::user()) {
          $file = config('app.import_path').Session::get('csv_import_filename');
          
          $check = file_exists($file);
          $app_id = Input::get('hidd-app-selected');
          if($app_id){
              if($check==true){
                  $resdata = array_map('str_getcsv', file($file));
                  
                  if(count(@$resdata[0][0])==0){
                      return redirect('importRecords')->with('err', Lang::get('language.emptyfile'));
                  }else{
                      $jres = json_encode($resdata);
                      if (count($resdata) > 0) {                    
                          // $csv_header = array_slice($resdata, 0, 1);
                          $csv_header_fields = [];
                          $csv_header_res_no = [];
                          $csv_header_res_name = [];
                          $csv_header_res1 = [];

                          $doctype_name = DB::table('tbl_document_types')->select('tbl_document_types.document_type_name')->where('tbl_document_types.document_type_id',$app_id)->where('is_app',1)->get();
                      
                          $csv_header_res_no = DB::table('tbl_document_types')->select('tbl_document_types.document_type_column_no')->where('tbl_document_types.document_type_id',$app_id)->where('is_app',1)->get();
                         
                          $csv_header_res_name = DB::table('tbl_document_types')->select('tbl_document_types.document_type_column_name')->where('tbl_document_types.document_type_id',$app_id)->where('is_app',1)->get();

                          $csv_header_res1 = DB::table('tbl_document_types_columns')->select('tbl_document_types_columns.document_type_column_name')->where('tbl_document_types_columns.document_type_id',$app_id)->get();
                          
                          if(count($csv_header_res_no)>0){
                              if($csv_header_res_no[0]->document_type_column_no){
                                  foreach ($csv_header_res_no as $key => $value) {
                                      if($value->document_type_column_no){
                                          $csv_header_fields[] = $value;                        
                                      }
                                  }
                              }
                          }

                          if(count($csv_header_res_name)>0){
                              if($csv_header_res_name[0]->document_type_column_name){
                                  foreach ($csv_header_res_name as $key => $value) {
                                      if($value->document_type_column_name){
                                          $csv_header_fields[] = $value;                        
                                      }
                                  }
                              }
                          }
                          

                          if(count($csv_header_res1)>0){
                              if($csv_header_res1[0]->document_type_column_name){
                                  foreach ($csv_header_res1 as $key => $value) {
                                      if($value->document_type_column_name){
                                          $csv_header_fields[] = $value;                        
                                      }
                                  }
                              }
                          }

                          $csv_data = array_slice($resdata, 0, 5);
                          $doctypeid = $app_id;
            
                          $result = CsvData::create([
                              'csv_data_filename' => Session::get('csv_import_filename'),
                              'document_type_id' => @$doctypeid,
                              'csv_data' => $jres
                          ]);
                          $lastInsertedId= $result->id;
                          $data['result'] = DB::table('tbl_csv_data')->where('csv_data_id',$lastInsertedId)->get();
                      } else {
                          return redirect()->back();
                      }
                  }
              }
              Session::put('menuid', '0');
              Session::put('import_app_name', @$doctype_name[0]->document_type_name);
              Session::put('import_app_id',$app_id);
              $data['doc_type_id'] = @$doctypeid;
              $data['csv_data'] = @$csv_data;
              $data['csv_header_fields'] = @$csv_header_fields;
   
              $data['stack']      = StacksModel::all();
              $data['tagsCateg']  = TagWordsCategoryModel::all();
              $data['docType']    = $this->docObj->common_type();

              $docObj = new Common(); // class defined in app/mylibs
              $data['stckApp'] = $docObj->common_stack();
              $data['deptApp'] = $docObj->common_dept();
              $data['doctypeApp'] = $docObj->common_type();
              $data['records'] = $docObj->common_records();
              return View::make('pages/apps/import_fields')->with($data);
          }else{
              return redirect('importRecords')->with('err', Lang::get('language.doctype_null'));
          }
      } 
      else 
      {
        return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
      }
    }
    public function app_importExcel()
    {
        $headercnt = Input::get('headercnt');
        /*unique field for updation*/
        $unique_field = Input::get('unique_field');

        // echo $unique_field;
        // exit();

        $file = config('app.import_path').Session::get('csv_import_filename');
        
        $row_count = count(file($file));

        $query = DB::table('tbl_document_types as tt')->leftjoin('tbl_document_types_columns as tc','tt.document_type_id','=','tc.document_type_id')->select('tc.document_type_column_name','tc.document_type_column_id','tc.document_type_column_type','tc.document_type_options')->where('tt.is_app',1)->where('tt.document_type_id',Session::get('import_app_id'))->orderBy('tc.document_type_column_order','ASC');
          $index_field_count = $query->count();

        //check file exist or not?
        $check=file_exists($file);
        if(!$check){
            return redirect('importRecords')->with('err', Lang::get('language.choose_import'));
            //unlink file if exist
            if (file_exists(config('app.import_path').Session::get('csv_import_filename'))) { unlink (config('app.import_path').Session::get('csv_import_filename')); }
            exit();
        }

        $heder_data = array();
        $heder_id = array();
        for($i=1;$i<=$headercnt;$i++){ 
          $header_column_id = DB::table('tbl_document_types_columns')->select('document_type_column_id')->where('document_type_column_name',Input::get('header'.$i.''))->where('document_type_id',Session::get('import_app_id'))->first();   
            $heder_id[] = $header_column_id->document_type_column_id;       
            
            $heder_data[] = Input::get('header'.$i.'');

        }

        $num = count($heder_data);

        //if missmatch occurs csv columns and index fields error
        
        if(($headercnt <= $index_field_count) || ($num <= $index_field_count))
        {
          $update_count = 0;
          /*open file in read mode*/
          $file = fopen(config('app.import_path').Session::get('csv_import_filename'), "r");

          /*fetch csv data*/
          
          while (($data = fgetcsv($file, 1000, ",")) !== FALSE) 
          {
              foreach ($heder_data as $key => $value) 
              {
              /*if index field == unique field*/
              if($value == $unique_field)
              {
                $existing_datas = DB::table('tbl_documents_columns')->select('document_id')->where('document_column_name',$unique_field)->where('document_column_value',$data[$key])->get();
                /*Delete previous*/
                foreach ($existing_datas as $exist)
                {
                  /*delete duplicate docs*/
                  $del_doc = DB::table('tbl_documents')->where('document_id',$exist->document_id)->delete();
                  if($del_doc)
                  {
                    $update_count++;
                  }
                  /*delete duplicate cols*/
                  $del_col = DB::table('tbl_documents_columns')->where('document_id',$exist->document_id)->delete();
                }
              }
            }
        
            $result = array();
            /*Insert to tbl_documents*/
        
            $tbl_documents_modl=new DocumentsModel;
            $tbl_documents_modl->document_type_id      = Session::get('import_app_id');
            $tbl_documents_modl->document_name         = '';
            $tbl_documents_modl->document_file_name    = '';
            $tbl_documents_modl->parent_id             = Session::get('SESS_parentIdd');
            $tbl_documents_modl->department_id         = '';
            $tbl_documents_modl->stack_id              = '';
            $tbl_documents_modl->document_version_no   = "1.0";
            $tbl_documents_modl->document_ownership    = Auth::user()->username;
            $tbl_documents_modl->document_created_by   = Auth::user()->username;
            $tbl_documents_modl->document_modified_by  = Auth::user()->username;
            $tbl_documents_modl->document_tagwords     = '';
            $tbl_documents_modl->document_no           = '';
            $tbl_documents_modl->document_path         = '';
            $tbl_documents_modl->document_status       = "Published";
            $tbl_documents_modl->created_at            = date('Y-m-d h:i:s');
            $tbl_documents_modl->updated_at            = date('Y-m-d h:i:s');
            $tbl_documents_modl->document_size         = '';
            $tbl_documents_modl->document_expiry_date  = '';
            $tbl_documents_modl->save();
            $last_inserted_id = $tbl_documents_modl->document_id;//last inserted id   
          
          foreach ($heder_data as $key => $value) {
            
            $row = array();
            $row['name'] = $value;
            $row['value'] = isset($data[$key])?$data[$key]:'';          
            $result[] = $row;
            
            
            //index fields insert
            $documenttypecolModl   =   new DocumentsColumnModel;
            $documenttypecolModl->document_id =   @$last_inserted_id;
            $documenttypecolModl->document_type_column_id = $heder_id[$key];
            $documenttypecolModl->document_column_name = $row['name'];
            $documenttypecolModl->document_column_value = $row['value'];
            $documenttypecolModl->document_column_type = '';
            $documenttypecolModl->document_column_mandatory = '';
            $documenttypecolModl->document_column_created_by = Auth::user()->username;
            $documenttypecolModl->document_column_modified_by = Auth::user()->username;
            $documenttypecolModl->save();
          }
        }

        fclose($file);
        $insert_count = ($row_count-$update_count);
        if($insert_count>=0)
        {
          return redirect('importRecords')->with('data',('Totally '.$row_count).' rows have been imported. '.$update_count.' rows updated. '.$insert_count.' rows inserted.');
        }
        else
        {
          return redirect('importRecords')->with('data',('Totally '.$row_count).' rows have been imported. '.$update_count.' rows updated.');
        }
      }
      else
      {
        return redirect('importRecords')->with('err', 'Miss match count of index fields. Index fields count of App:\''.$index_field_count.'\' and imported csv index fields count:\''.$num. '\' . These are not equal');
        exit();               
      }
        
    }
    //delete all from list views
    public function deleteAll()
    {
        if (Auth::user()) 
        {
            $arr=Input::get('selected');
            foreach ($arr as $key => $value) 
            {
                $documents_cols = DB::table('tbl_documents_columns')->where('document_id',$value)->get();
                foreach ($documents_cols as $data) 
                {
                    DB::table('tbl_documents')->where('document_id','=',$value)->delete();
                    DB::table('tbl_documents_columns')->where('document_id','=',$value)->delete();
                    DB::table('tbl_document_notes')->where('document_id','=',$value)->delete();
                    if($data->document_file_name)
                    {
                      $destinationPath = config('app.base_path'); // upload path
                      if(file_exists($destinationPath."/".$data->document_file_name))
                      {
                        unlink($destinationPath."/".$data->document_file_name);
                      }
                      else
                      {

                      }
                    }
                    // // Save audits
                    // $name = $data->document_name;
                    // $user = Auth::user()->username;
                    // // Get delete action message
                    // $actionMsg = Lang::get('language.delete_action_msg');
                    // $actionDes = $this->docObj->stringReplace($this->actionName2,$name,$user,$actionMsg);
                    // $result = (new AuditsController)->log(Auth::user()->username, $heading, 'Delete',$actionDes);
                }
            }
            echo("App records deleted successfully.");
        }
        else 
        {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function appMoreDetails()
    {
      if (Auth::user()) 
      {
            $appid = Input::get('appid');
            $data['app_details'] = DB::table('tbl_document_types')->select('document_type_name','document_type_description','document_type_created_by','created_at','document_type_modified_by','updated_at')->where('is_app',1)->where('document_type_id',$appid)->first();
            $data_count = DocumentsModel::where('document_type_id','=',$appid)->count();
            $data_count = ($data_count)?$data_count:0;
            $data['count_records'] = $data_count;
            $app_link = AppLinksModel::where('fk_app_id',$appid)->orderBy('id','ASC')->get();
            $app_links = array();
            foreach ($app_link as $key => $value) 
            {
                    $row = array();

                    $app_link_id = DB::table('tbl_document_types')->select('document_type_name')->where('document_type_id',$value->id)->first();

                    $row['app_link_name'] = @$app_link_id->document_type_name;

                    $app_column_id = DB::table('tbl_document_types_columns')->select('document_type_column_name')->where('document_type_column_id',$value->fk_app_column_id)->first();

                    $row['app_column_name'] = @$app_column_id->document_type_column_name;

                    $document_type_id = DB::table('tbl_document_types')->select('document_type_name')->where('document_type_id',$value->document_type_id)->first();

                    $row['document_type_name'] = @$document_type_id->document_type_name;

                    $document_column_id = DB::table('tbl_document_types_columns')->select('document_type_column_name')->where('document_type_column_id',$value->fk_document_column_id)->first();

                    $row['document_column_name'] = @$document_column_id->document_type_column_name;

                    array_push($app_links, $row);
            }
            $data['app_links'] = $app_links;
            
            
            //exit();
            $json['status']=  1;
            $json['html']=  view::make('pages/apps/more')->with($data)->render();
            return json_encode($json);
        }
        else 
        {
             $json['status']=  1;
             $json['html']=  "Session Expired.Please Login";
             return json_encode($json);
        }
    }
}/*<--END-->*/
