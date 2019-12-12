<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;
use View;
use Input;
use DB;
use Session;
use App\Mylibs\Common;
class NotificationController extends Controller
{
	public function __construct()
    {   
        Session::put('menuid', '15');
        $this->docObj     = new Common(); 
    }
    public function index()
    {
      if (Auth::user()) {
          $user_permission=Auth::user()->user_permission;
            $data['stckApp'] = $this->docObj->common_stack();
            $data['deptApp'] = $this->docObj->common_dept();
            $data['doctypeApp'] = $this->docObj->common_type();
            $data['records'] = $this->docObj->common_records();
            $settings = DB::table('tbl_settings')->select('settings_document_no','settings_document_name')->get();
            $data['settings_document_no']   = $settings[0]->settings_document_no;
            $data['settings_document_name'] = $settings[0]->settings_document_name;
            
            return view::make('pages/notification/list')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function read_notification()
    {
      $notification = (Input::get('notification'))?Input::get('notification'):0;
      if($notification)
      {
          $time_stamp = date('Y-m-d H:i:s');
          $recipient_array = array(
                    'notification_viewed'=> 1,
                    'viewd_at'=> $time_stamp,
                    );
          DB::table('tbl_notification_recipients')->where('id',$notification)->update($recipient_array);  
      } 

      $json =array('status' => 1);
      return json_encode($json);
    }

     public function load_notification()
    {
        $val = $this->docObj->getPasswordExpiryNotification();
        $this->docObj->commom_expiry_documents_check(null);
        $this->docObj->audit_clear_notification_check();
        $this->docObj->document_assign_notification();
        $this->docObj->document_reject_notification();
        $this->docObj->document_accept_notification();
        $this->docObj->get_workflow_notification();
        $this->docObj->get_user_notification();

      $data = array();  
      $json =array('status' => 1);
      $json['html'] = View::make('pages/notification/top_notification')->with($data)->render();
      
      return json_encode($json);
    }
    public function notificationfilter()
    {

      $length       =   Input::get("length");
      $start        =   Input::get("start");
      $filter       =   Input::get('filter');
      $type         =   Input::get('typeselect');

      $currentPage = ($start)?($start/$length)+1:1;

      \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($currentPage) {
        return $currentPage;
      });
      $search       =   (isset($_POST['search']['value']))?trim($_POST['search']['value']):'';
      $footer_search =  (isset($_POST['columns'][0]['search']['value']))?trim($_POST['columns'][0]['search']['value']):'';
      $footer_search1 =  (isset($_POST['columns'][1]['search']['value']))?trim($_POST['columns'][1]['search']['value']):'';
      $footer_search2 =  (isset($_POST['columns'][2]['search']['value']))?trim($_POST['columns'][2]['search']['value']):'';       
      $query        =   DB::table('tbl_notifications')
              ->join('tbl_notification_recipients','tbl_notifications.notification_id','=','tbl_notification_recipients.notification_id')
              ->leftjoin('tbl_users as sender','tbl_notifications.notification_sender','=','sender.id')
              ->leftjoin('tbl_users as receiver','tbl_notification_recipients.notification_recipient','=','receiver.id')
              ->select('tbl_notifications.*',
                        'tbl_notification_recipients.notification_recipient',
                        'tbl_notification_recipients.id as notification_id',
                        'tbl_notification_recipients.notification_viewed',
                        'sender.user_full_name as sender_user',
                        'receiver.user_full_name as receiver_user'
                        );
              
      switch (Auth::user()->user_role) 
      {
        case Session::get("user_role_private_user")://private user
        case Session::get("user_role_regular_user")://regular user
        $query->where('tbl_notification_recipients.notification_recipient',Auth::user()->id);
        break;
        case Session::get("user_role_super_admin")://super admin
        $query;
        break;
        case Session::get("user_role_group_admin")://group admin
        $auth_dep_users = DB::table('tbl_users_departments')
            ->select('users_id')
            ->whereIn('department_id',Session::get('auth_user_dep_ids'))->get();
            //users under the department.
            foreach ($auth_dep_users as $value) 
            {
              $auth_dep_users_array[] = $value->users_id;
            }
            $query->join('tbl_users_departments as us_dept', 'tbl_notification_recipients.notification_recipient', '=', 'us_dept.users_id')
            ->whereIn('us_dept.department_id',Session::get('auth_user_dep_ids'));
            //orwhere query
            $query->where(function ($query1) use($auth_dep_users_array) {
                $query1->whereIn('tbl_notification_recipients.notification_recipient',$auth_dep_users_array);      
            });
        break;
      }
      switch($filter)
      {
        case 0: //notification_all
          $query;
        break;
        case 1://notification_read
          $query->where('tbl_notification_recipients.notification_viewed','=',1);
        break;
        case 2://notification_not_read
          $query->where('tbl_notification_recipients.notification_viewed','!=',1);
        break;
        case 3://notification_assigned_by_me
          $query->where('tbl_notifications.notification_sender',Auth::user()->id);
        break;
        case 4://notification_assigned_to_me
          $query->where('tbl_notification_recipients.notification_recipient',Auth::user()->id);
        break;
        default:
        $data = array();
        break;
      }
      switch($type)
      {
        case 0: //all notification types
          $query;
        break;
        case 1://doc_notifications
          $query->where('tbl_notifications.notification_type','document');
        break;
        case 2://audit_notifications
          $query->where('tbl_notifications.notification_type','audit');
        break;
        case 3://pwd_notifications
          $query->where('tbl_notifications.notification_type','password');
        break;
        case 4://workflow_notifications
          $query->where('tbl_notifications.notification_type','workflow');
        break;
        case 5://form_notifications
          $query->where('tbl_notifications.notification_type','form');
        break;
        case 6://activity_notifications
          $query->where('tbl_notifications.notification_type','activity');
        break;
        case 7://general_notifications
          $query->where('tbl_notifications.notification_type','=','document')
                ->orwhere('tbl_notifications.notification_type','=','audit')
                ->orwhere('tbl_notifications.notification_type','=','password')
                ->orwhere('tbl_notifications.notification_type','=','activity');
        break;
        break;
        default:
        $data = array();
        break;
      }
      $query->groupBy('tbl_notification_recipients.notification_id');
      //ajax search
    if($search){
      $column = array('tbl_notifications.notification_title','sender.user_full_name','receiver.user_full_name','tbl_notifications.created_at');
      $query->Where(function($query1) use($column,$search) {
          foreach ($column as $key => $value) {
            $query1->orWhere($value,'LIKE','%'.$search.'%');
          }
      });
    }
      //tfoot column search
      //notification title
    if($footer_search){
      $tfoot_column1 = array('tbl_notifications.notification_title');
      $query->where(function($query1) use($tfoot_column1,$footer_search) {
        foreach ($tfoot_column1 as $key => $value) {
            $query1->orWhere($value,'LIKE','%'.$footer_search.'%');
          }
        });
    }
      //from
    if($footer_search1){
      $tfoot_column2 = array('sender.user_full_name');
      $query->where(function($query2) use($tfoot_column2,$footer_search1) {
        foreach ($tfoot_column2 as $key => $value) {
            $query2->orWhere($value,'LIKE','%'.$footer_search1.'%');
          }
        });
    }
      //to
    if($footer_search2){
      $tfoot_column3 = array('receiver.user_full_name');
      $query->where(function($query3) use($tfoot_column3,$footer_search2) {
        foreach ($tfoot_column3 as $key => $value) {
            $query3->orWhere($value,'LIKE','%'.$footer_search2.'%');
          }
        });
    }
      // Ajax order by works
      $order = (isset($_POST['order'][0]['column']))?$_POST['order'][0]['column']:3;
      $direct = (isset($_POST['order'][0]['dir']))?$_POST['order'][0]['dir']:'DESC';
      $data_item = (isset($_POST['columns'][$order]['data']))?strtolower($_POST['columns'][$order]['data']):'';
      switch($data_item)
      {
        case 'created':
        $table_column = 'tbl_notifications.created_at';
        break;
        case 'to':
        $table_column = 'receiver.user_full_name';
        break;
        case 'from':
        $table_column = 'sender.user_full_name';
        break;
        case 'notification':
        $table_column = 'tbl_notifications.notification_title';
        break;
        default:
        $table_column = 'tbl_notifications.created_at';
        break;
      }
      $query->orderBy("$table_column","$direct");
      $data = $query->paginate($length);
      $count_all = ($data)?$data->total():0;
      $i = $start;
      $data_table = array();
      foreach ($data as $value) {
      $i++;
      $row_d = array();
      $row_d['notification'] = $value->notification_title;  
      $row_d['from'] = $value->sender_user;
      $row_d['to'] = $value->receiver_user;
      $row_d['created'] = $value->created_at;  
      
      $row_d['actions'] = '<a title="Go Link" id="details" href="'.$value->notification_link.'" class="read_notification" data-notid="'.$value->notification_id.'"><i class="fa fa-ellipsis-v" style="cursor:pointer;"></i></a>';  
      $row_d['viewed'] = $value->notification_viewed;
      $row_d['notification_type'] = $value->notification_type;
      $data_table[] = $row_d;
      }
         $output = array(
            "draw" =>  Input::get('draw'),
            "recordsTotal" => $count_all,
            "recordsFiltered" => $count_all,
            "data" => $data_table
        );
    echo json_encode($output);
    }
}


