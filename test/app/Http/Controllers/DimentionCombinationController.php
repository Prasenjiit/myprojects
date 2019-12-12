<?php
namespace App\Http\Controllers;
use App\DepartmentMaster;
use App\DimensionMaster;
use App\LedgerMaster;
use App\COA;
use Session;
use App\DimentionCombination;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Lang;
use App\Users as Users;
use App\DepartmentsModel as DepartmentsModel;
use App\DocumentsModel as DocumentsModel;
use App\AuditsModel as AuditModel;
use App\DocumentTypesModel as DocumentTypesModel;
use App\StacksModel as StacksModel;
use App\Mylibs\Common;

class DimentionCombinationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        Session::put('menuid', '22');
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
        Session::put('menuid', '22');
        // $whr = array(['LeCode','=','GST'],['UserCode','=','admin@gmail.com']);
        $department_masters 	= DepartmentMaster::all();
        $dimension_masters 		= DimensionMaster::all();
        $ledger_masters 		= COA::all();
        $dimentioncombinations 	= DimentionCombination::all();
        $records = $this->docObj->common_records();

        $docType = DocumentTypesModel::orderBy('document_type_order', 'ASC')->get();
        $stckApp = $this->docObj->common_stack();
        $deptApp = $this->docObj->common_dept();
        $doctypeApp = $this->docObj->common_type();

        //dd($dimentioncombinations);
        return view('dimentioncombinations.index',compact('dimentioncombinations','dimension_masters','ledger_masters', 'department_masters','records','docType','stckApp','deptApp','doctypeApp'))->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         //dd($currency_masters);
        $department_masters = DepartmentMaster::all();
        $dimension_masters = DimensionMaster::all();
        $ledger_masters = LedgerMaster::all();

        $records = $this->docObj->common_records();
        $docType = DocumentTypesModel::orderBy('document_type_order', 'ASC')->get();
        $stckApp = $this->docObj->common_stack();
        $deptApp = $this->docObj->common_dept();
        $doctypeApp = $this->docObj->common_type();

        return view('dimentioncombinations.create', compact('ledger_masters', 'dimension_masters', 'department_masters','records','docType','stckApp','deptApp','doctypeApp'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'LedgerCode' => ['required'],
            'CostCentre' => ['required'],
            'Department' => ['required'],
            'Purpose' =>    ['required']
        ]);

		$LedgerCode = explode('|',$request->LedgerCode); 
		$data['LedgerCode'] = $LedgerCode[0]; 

		$CostCentre = explode('|', $request->CostCentre); 
		$data['CostCentre'] = $CostCentre[0];

		$Department = explode('|', $request->Department);
		$data['Department'] =  $Department[0]; 


		$Purpose = explode('|', $request->Purpose); 
		$data['Purpose'] = $Purpose[0]; 

		$active_comb = 0;
		if(isset($request->Active_Comb)){
			$active_comb = $request->Active_Comb;
		}
        $data['Active_Comb'] = $active_comb;
        $data['LeCode']= session('LeCode');
        $data['UserCode']= session('UserCode');
		DimentionCombination::create($data);
        return redirect()->route('dimentioncombinations.index')
                        ->with('success','Dimention Combination Added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(DimentionCombination $dimentioncombination)
    {
        $department_masters = DepartmentMaster::all();
        $dimension_masters = DimensionMaster::all();
        $ledger_masters = LedgerMaster::all();
        return view('dimentioncombinations.show', compact('ledger_masters', 'dimension_masters', 'department_masters','dimentioncombination'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //Request $request, DimentionCombination $dimentioncombination
    public function edit(Request $request, DimentionCombination $dimentioncombination)
    {
        $department_masters = DepartmentMaster::all();       
        $dimension_masters = DimensionMaster::all();
        // dd($department_masters);
        $ledger_masters = LedgerMaster::all();
        //$dimentioncombination = $id;
        $records = $this->docObj->common_records();
        $docType = DocumentTypesModel::orderBy('document_type_order', 'ASC')->get();
        $stckApp = $this->docObj->common_stack();
        $deptApp = $this->docObj->common_dept();
        $doctypeApp = $this->docObj->common_type();
        return view('dimentioncombinations.edit', compact('dimentioncombination', 'ledger_masters', 'dimension_masters', 'department_masters','records','docType','stckApp','deptApp','doctypeApp'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DimentionCombination $dimentioncombination)
    {
        $request->validate([
            'LedgerCode' => ['required'],
            'CostCentre' => ['required'],
            'Department' => ['required'],
            'Purpose' =>    ['required'],
            
        ]);

        $LedgerCode = explode('|',$request->LedgerCode); 
		$data['LedgerCode'] = $LedgerCode[0]; 

		$CostCentre = explode('|', $request->CostCentre); 
		$data['CostCentre'] = $CostCentre[0];

		$Department = explode('|', $request->Department);
		$data['Department'] =  $Department[0]; 


		$Purpose = explode('|', $request->Purpose); 
		$data['Purpose'] = $Purpose[0]; 

		$active_comb = 1;
		if(!isset($request->Active_Comb)){
			$active_comb = 0;
		}
		$data['Active_Comb'] = $active_comb;
        //dd($data);
        $dimentioncombination->update($data);

        //dd($data);
        //User::create($request->all());
   
        return redirect()->route('dimentioncombinations.index')
                        ->with('success','Dimention Combination Updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        DimentionCombination::where('id',[$request->dimentioncombinationid])->delete();
        return redirect()->route('dimentioncombinations.index')
                        ->with('success','Dimension Combinations Deleted Successfully');
    }
}
