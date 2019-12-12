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
use App\ActivityModel as ActivityModel;
use App\StacksModel as StacksModel;
use App\DepartmentsModel as DepartmentsModel;
use App\DocumentTypesModel as DocumentTypesModel;

use App\Mylibs\Common;
use Lang;

class ActivityController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        Session::put('menuid', '13');
        $this->middleware(['auth', 'user.status']);
        // Set common variable
        $this->actionName = Lang::get('language.activity');
        $this->docObj     = new Common(); // class defined in app/mylibs
        $this->actionUrl  = 'activities';
    }
    
    public function index($id=NULL) 
    { 
        if (Auth::user()) {
            Session::put('menuid', '13');
            /*<--Common-->*/
            $data['docType'] = DocumentTypesModel::orderBy('document_type_order', 'ASC')->get();
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['records'] = $this->docObj->common_records();
            $data['doctypeApp'] = $this->docObj->common_type();

            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;
            /*<--// Common-->*/
            // Get url in view
            $data['actionUrl'] = $this->actionUrl;
            // Get activity content
            if($id):;
                // Update
                $data['activity'] = activityModel::where('activity_id',$id)->select('activity_id','activity_name','activity_modules','last_activity','activity_constant')->get();
                return view('pages/activity/edit')->with($data);
            else:
                return View::make('pages/activity/index')->with($data);
            endif;            
        } else {
            return redirect('')->withErrors(Lang::get('language.please_login_msg_lang'))->withInput();
        }
    }

    public function activityList()
    {
        // For server side pagination we need to assign table name and define columns here
        $tableName = "tbl_activities";
        // To set order by
        $columns = array( 
        // datatable column index  => database column name
            0 => 'activity_id', 
            1 => 'activity_name', 
            2 => 'activity_modules',
            3 => 'created_at',
            4 => 'updated_at'
        ); 
        // Write select query here
        $selectQuery = DB::table($tableName)->select($columns);
        // Get response
        $this->datatableList($tableName,$selectQuery,$columns);

    }

    // Server side pagination for common use
    public function datatableList($tableName,$selectQuery,$columns)
    {
        
        $noOfRecords   = DB::table($tableName)->count(); 
        $requestData= $_REQUEST; 
        // Get data 
        $query = $selectQuery;
        
        // Search
        if( !empty($requestData['search']['value']) ) {     
            $count =1;
            foreach($columns as $val):
                if((1%$count) == 0):
                    $query->where("$val","LIKE",''.$requestData['search']['value']."%");
                else:
                    $query->orWhere("$val","LIKE",''.$requestData['search']['value']."%");
                endif;    
                $count++;
                endforeach;
            $noOfRecords = $query->count();
         }
        // Ajax order by works
        $query->orderBy($columns[$requestData['order'][0]['column']], $requestData['order'][0]['dir']);
        $query->offset($requestData['start'])->limit($requestData['length']);

        $loop = $query->get();
        foreach($loop as $val):
            // Update edit button in activity name
            $name= $val->activity_name;
            if(Auth::user()->user_role == 1)
            {
                $val->activity_name = "<a href=".URL('/activities').'/'.$val->activity_id.">$val->activity_name</a>";
            }
            else
            {
                $val->activity_name = "<a>$val->activity_name</a>";                
            }
            if($val->activity_modules)
            {
                $action_response = $val->activity_modules;
                if(strpos($action_response, 'form_action')!== false)
                {
                    $val->activity_modules = "<i class='fa fa-check-square-o'></i>";
                }
                else
                {
                    $val->activity_modules = "<i class='fa fa-square-o'></i>";
                }
            }
            if(Auth::user()->user_role == 1){
            $val->action = "<a href=".URL('/activities').'/'.$val->activity_id." title='Edit' activity_id='$val->activity_id'>
                            <i class='fa fa-pencil' style='cursor:pointer;'></i>
                            </a>&nbsp;
                            <i class='activity-delete fa fa-trash' activityId='$val->activity_id' name='$name' style='color: red; cursor:pointer;' title='Delete'></i>";
            }
            endforeach;
        $data['data'] = $loop;
        //For ajax result
        $data['draw'] = intval( $requestData['draw'] );
        $data['recordsTotal'] = $noOfRecords;
        $data['recordsFiltered'] = $noOfRecords;
        $data['request'] = $requestData['order'][0]['dir'];

        $y=array();
        foreach( $data['data'] as $val):
            $x = (array) $val;
            $y[] = array_values($x);  
        endforeach;

        $data['data'] = $y;
        echo json_encode($data);  
    }

    // Save content
    public function activitySave(){ 
        // Preparing data
        $data   = new ActivityModel;   
        // checking wether data already exists or not
        $activity = Input::get('activity_id');
        $last_activity = Input::get('last_activity');
        $activity_module = Input::get('activity_module');
        $activity_module = ($activity_module)?implode('-', $activity_module):'workflows';

        $activity_constant = Input::get('activity_constant');
        if($activity_constant)
        {
        $reset= array('activity_constant' => NULL);
            ActivityModel::where('activity_constant', $activity_constant)->update($reset);
        }
        // Update
        if($activity):
            // update query
            

            $dataToUpdate = array('activity_name'       =>Input::get('activity_name'),
                                   'activity_updated_by'=>Auth::user()->id,
                                   'activity_modules'=> $activity_module,
                                    'activity_constant'=> $activity_constant,
                                   'last_activity' => $last_activity,
                                   'updated_at'     => date('Y-m-d H:i:s'));
            ActivityModel::where('activity_id', $activity)->update($dataToUpdate);

            

            // Updationg information in audits controller
            $name = Input::get('activity_name');
            $user = Auth::user()->username;

            // Get update action message
            $actionMsg = Lang::get('language.update_action_msg');
            $actionDes = $this->docObj->stringReplace($this->actionName,$name,$user,$actionMsg);
            $result = (new AuditsController)->log(Auth::user()->username, $this->actionName,Lang::get('language.update'),$actionDes);
            // redirect
            return redirect("/$this->actionUrl")->with('status', Lang::get('language.updated_successfully'));
        else:
            // Insert  
            $data->activity_name        = Input::get('activity_name');
            $data->activity_added_by    = Auth::user()->id;
            $data->activity_modules     = $activity_module;
            $data->last_activity        = $last_activity;
            $data->activity_constant        = $activity_constant;
            $data->created_at           = date('Y-m-d H:i:s');
            // Save data
            $data->save();
            // Save in audits
            $name = Input::get('activity_name');
            $user = Auth::user()->username;

            // Get save action message
            $actionMsg = Lang::get('language.save_action_msg');
            $actionDes = $this->docObj->stringReplace($this->actionName,$name,$user,$actionMsg);
            $result = (new AuditsController)->log(Auth::user()->username, $this->actionName,Lang::get('language.insert'),$actionDes);
            // redirect
            return redirect("/$this->actionUrl")->with('status', Lang::get('language.saved_successfully'));
        endif;
    }

    // Delete
    public function deleteActivity(){
        activityModel::where('activity_id',Input::get('activityId'))->delete();
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
