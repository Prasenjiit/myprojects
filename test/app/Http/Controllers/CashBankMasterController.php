<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CashBankMaster;
use App\CashBankMaster_slave;
use DB;
use Validator;
//use Input;
use Session;
use Response;
use App\Users as Users;
use App\DepartmentsModel as DepartmentsModel;
use App\DocumentsModel as DocumentsModel;
use App\AuditsModel as AuditModel;
use App\DocumentTypesModel as DocumentTypesModel;
use App\StacksModel as StacksModel;
use App\Mylibs\Common;

class CashBankMasterController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        Session::put('menuid', '23');
        // $this->middleware(['auth', 'user.status']);

        // // Define common variable
        // $this->actionName = 'Department';
        $this->docObj     = new Common(); // class defined in app/mylibs

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        Session::put('menuid', '23');
        $view = Self::cashbank_details();
        $records = $this->docObj->common_records();
        $docType = DocumentTypesModel::orderBy('document_type_order', 'ASC')->get();
        $stckApp = $this->docObj->common_stack();
        $deptApp = $this->docObj->common_dept();
        $doctypeApp = $this->docObj->common_type();        
        return view('cashbankmaster.index',compact('view','records','docType','stckApp','deptApp','doctypeApp'));
    }

    public function create()
    {
        $ledger_account = DB::table('ledger_masters')->get();
        $address_type = DB::table('enum_address_type')->where('ad_status','1')->get(); 
        $addressbook = DB::table('addressbook')->get();

        $records = $this->docObj->common_records();
        $docType = DocumentTypesModel::orderBy('document_type_order', 'ASC')->get();
        $stckApp = $this->docObj->common_stack();
        $deptApp = $this->docObj->common_dept();
        $doctypeApp = $this->docObj->common_type();   
       return view('cashbankmaster.create',compact('addressbook','ledger_account','address_type','records','docType','stckApp','deptApp','doctypeApp'));
    }

   
    public function store(Request $request)
    {
        $validator = $request->validate([
            'code' => 'required|unique:cash_bank_master,Cash_BankCode',
           // 'name' => 'required',
            //'offset_ledger' => 'required',
        ]);
       
        $data['Cash_BankCode']=$request->input('code');
        $data['Bank_Name']=$request->input('name');
        $data['IFSC']=$request->input('ifsc');
        $data['IBAN']=$request->input('iban');
        $data['SWIFT']=$request->input('swift');
        $data['Cash']=$request->input('cash');
        $data['LedgerAccount']=$request->input('offset_ledger');
        $data['Warehouse']='RUBY';
        $data['LeCode'] 		= session('LeCode');
        $data['UserCode'] 		= session('UserCode');
        $newdata['cbm_id'] = DB::table('cash_bank_master')->create($data)->id;
        $address_id = $request->input('address_id');
        $address_type = $request->input('address_type');
        $tb_row = $request->input('tb_row');
        $address_status = $request->input('address_status');
        for($i=0; $i < count($address_id); $i++){
            $newdata['address_id'] = $address_id[$i];
            $newdata['address_type'] = $address_type[$i];
            if($tb_row[$i] == $address_status){
                $newdata['primary_address'] = '1';
            }else{
                $newdata['primary_address'] = '0';
            }
         
            CashBankMaster_slave::create($newdata);
        }        
       return json_encode('success');
    }

   
    public function show($id)
    {
        $view=self::cashbank_details($id);
        $slave=self::cashbank_slave_details($id);
        
      //  echo "<pre>";print_r($address_type);exit;
        return view('cashbankmaster.show',compact('view','slave'));
    }

    public function edit($id)
    {
        $view=self::cashbank_details($id);
        $slave=self::cashbank_slave_details($id);
        $ledger_account = DB::table('ledger_masters')->get();
        $address_type = DB::table('enum_address_type')->where('ad_status','1')->get(); 
        $addressbook = DB::table('addressbook')->get();

        $records = $this->docObj->common_records();
        $docType = DocumentTypesModel::orderBy('document_type_order', 'ASC')->get();
        $stckApp = $this->docObj->common_stack();
        $deptApp = $this->docObj->common_dept();
        $doctypeApp = $this->docObj->common_type();       
        
        return view('cashbankmaster.edit',compact('view','slave','ledger_account','address_type','addressbook','records','docType','stckApp','deptApp','doctypeApp'));

    }

   
    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required',
            'name' => 'required',
            'offset_ledger' => 'required',
                     
        ]);
        $data['Cash_BankCode']=$request->input('code');
        $data['Bank_Name']=$request->input('name');
        $data['IFSC']=$request->input('ifsc');
        $data['IBAN']=$request->input('iban');
        $data['SWIFT']=$request->input('swift');
        $data['Cash']=$request->input('cash');
        $data['LedgerAccount']=$request->input('offset_ledger');
        $data['Warehouse']='RUBY';

        CashBankMaster::where('id',$id)->update($data);
        $address_id = $request->input('address_id');
        $address_type = $request->input('address_type');
        $tb_row = $request->input('tb_row');
        $address_status = $request->input('address_status');
        for($i=0; $i < count($address_id); $i++){
            $newdata['address_id'] = $address_id[$i];
            $newdata['address_type'] = $address_type[$i];
            if($tb_row[$i] == $address_status){
                $newdata['primary_address'] = '1';
            }else{
                $newdata['primary_address'] = '0';
            }
         
            CashBankMaster_slave::where('id',$tb_row[$i])->update($newdata);;
        }
        return json_encode('success');
    }

    public function destroy($id)
    {
        //
    }
    public function cashbank_details($id = NULL){
        //$whr = array(['LeCode','=',session('LeCode')],['UserCode','=',session('UserCode')]);
        $query= DB::table('cash_bank_master')
            ->select('cash_bank_master.*','ledger_masters.LedgerName')
            ->join('ledger_masters', 'ledger_masters.LedgerCode', '=', 'cash_bank_master.LedgerAccount');

        if($id != NULL){
            $query->where('cash_bank_master.id', $id);
            $res= $query->first();
        }else{

            $res= $query->get();
        }
        //dd($res);
        return $res;
    }
    public function cashbank_slave_details($id = NULL){
        $query= DB::table('cash_bank_master_slave')
            ->select('cash_bank_master_slave.*','addressbook.*')
            ->join('addressbook', 'addressbook.id', '=', 'cash_bank_master_slave.address_id');

        $query->where('cash_bank_master_slave.cbm_id', $id);
        $res= $query->get();
        return $res;
    }
   
}
