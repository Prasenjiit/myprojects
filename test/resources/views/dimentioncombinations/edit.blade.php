<?php
include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')   
@section('main_content')

<section class="content-header">
    <div class="col-sm-8">
        <span style="float:left;">
            <strong>
                {{trans('language.EditDimentionCombination')}}
            </strong> &nbsp;
        </span>
    </div>
</section>


<div class="card shadow mb-4">
   
    @if ($errors->any())
        <div class="alert alert-danger">
             {{trans('language.errmsg_Msg') }}
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('dimentioncombinations.update',$dimentioncombination->id) }}" method="POST">
        @csrf
        @method('PUT')
   
		<div class="py-3 ml-2">
			<div class="col-xs-12 col-sm-12 col-md-12">
				<div class="form-group">
					<label class="control-label col-sm-2">{{trans('language.LedgerCode') }}:</label>
					<div class="col-sm-5 float-right">
    					<select name="LedgerCode" id="LedgerCode" class="form-control" onchange="selectLedgerName()">
    					<option value="">--- Select Ledger Name ---</option>
    					@foreach ($ledger_masters as $ledger_master)
        					@if($ledger_master->LedgerCode == $dimentioncombination->LedgerCode)
        					   <option value="{{ $ledger_master->LedgerCode }}|{{ $ledger_master->LedgerName}}" selected> {{ $ledger_master->LedgerCode }}</option>
        					@else
        					   <option value="{{ $ledger_master->LedgerCode }}|{{ $ledger_master->LedgerName}}"> {{ $ledger_master->LedgerCode }}</option>
        					@endif
    					@endforeach
    					</select>
    				</div>
                    <div class="col-sm-5 float-right">
                        @foreach ($ledger_masters as $ledger_master)
                            @if($ledger_master->LedgerCode == $dimentioncombination->LedgerCode)
                                @php($LedgerName = $ledger_master->LedgerCode)
                            @endif
                        @endforeach      

                        <input type="text" readonly  name="LedgerName" id="LedgerName"  value="" class="form-control" />
                    </div>  

                    

				</div>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-12">
				<div class="form-group">
					<label class="control-label col-sm-2">{{trans('language.lblmsg_CostCentre') }}:</label>
					<div class="col-sm-5 float-right">				
					<select name="CostCentre" id="CostCentre" class="form-control" onchange="selectCostCentre()">
						<option value="">--- Select Cost Centre ---</option>
						@foreach ($dimension_masters as $dimension_master)
							@if($dimension_master->DimentionType == 'Costcenter')
								<option value="{{ $dimension_master->DimensionCode }}|{{ $dimension_master->DimensionName}}" selected> {{ $dimension_master->DimensionName }}</option>
							@else
								<option value="{{ $dimension_master->DimensionCode }}|{{ $dimension_master->DimensionName}}"> {{ $dimension_master->DimensionName }}</option>
							@endif
						@endforeach
					</select>
					</div>
                    <div class="col-sm-5 float-right">
                        @foreach ($dimension_masters as $dimension_master)
                            @if($dimension_master->DimentionType == 'Costcenter')
                                @php($DimensionName = $dimension_master->DimensionName)
                            @endif
                        @endforeach
                        
                        <input type="text" readonly  name="DimensionName" id="DimensionName"  value="{{ $DimensionName }}" class="form-control" />
                    </div>
				</div>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-12">
				<div class="form-group">
					<label class="control-label col-sm-2">{{trans('language.lblmsg_Purpose') }}:</label>
					<div class="col-sm-5 float-right">
						<select name="Purpose" id="Purpose" class="form-control" onchange="selectPurpose()">
							<option value="">--- Select Purpose ---</option>
							@foreach ($dimension_masters as $dimension_master)
								@if($dimension_master->DimentionType == 'Purpose')
									<option value="{{ $dimension_master->DimensionCode }}|{{ $dimension_master->DimensionName}}" selected> {{ $dimension_master->DimensionName }}</option>
								@else
									<option value="{{ $dimension_master->DimensionCode }}|{{ $dimension_master->DimensionName}}" selected> {{ $dimension_master->DimensionName }}</option>
								@endif
							@endforeach
						</select>
					</div>
                    <div class="col-sm-5 float-right">
                        @foreach ($dimension_masters as $dimension_master)
                            @if($dimension_master->DimentionType == 'Purpose')
                                @php($DimensionName = $dimension_master->DimensionName)
                            @endif
                        @endforeach
                        <input type="text" readonly  name="DimensionName" id="PurposeName"  value="{{ $DimensionName }}"  class="form-control" />
                    </div>
				</div>
			</div>
				<div class="col-xs-12 col-sm-12 col-md-12">
					<div class="form-group">
						<label class="control-label col-sm-2">{{trans('language.lblmsg_Department') }}:</label>
						<div class="col-sm-5 float-right">
							<select name="Department" id="Department" class="form-control" onchange="selectDepartment()">
								<option value="">--- Select Department ---</option>
								@foreach ($department_masters as $department_master)
									@if($department_master->DepartmentName == 'EMPLOYEE' || $department_master->DepartmentName == 'Officer' || $department_master->DepartmentName == 'Customer Care') 
										@php($DepartmentName = $department_master->DepartmentName)
										<option value="{{ $department_master->DepartmentCode }}|{{ $department_master->DepartmentName}}" selected> {{ $department_master->DepartmentName }}</option>
									@else
										<option value="{{ $department_master->DepartmentCode }}|{{ $department_master->DepartmentName}}"> {{ $department_master->DepartmentName }}
									@endif
								@endforeach
							</select>
						</div>
                        <div class="col-sm-5 float-right">
                                @foreach ($department_masters as $department_master)
                                    @if($department_master->DepartmentName == 'EMPLOYEE' || $department_master->DepartmentName == 'Officer' || $department_master->DepartmentName == 'Customer Care') 
                                        @php($DepartmentName = $department_master->DepartmentName)
                                    @endif
                                @endforeach             
                            <input type="text" readonly  name="DepartmentName" id="DepartmentName" value={{$DepartmentName}}  class="form-control" />
                        </div>
					</div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-12">
					<div class="form-group">
						<label class="control-label col-sm-2">{{trans('language.lblmsg_ActiveComb')}}:</label>
						<div class="col-sm-10 float-right">
							<input type="checkbox" value="1" @if($dimentioncombination->Active_Comb == 1){{ "checked"}}@endif name="Active_Comb">
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-12">
					<div class="form-group">
						<label class="control-label col-sm-2">&nbsp;</label>
						<a class="btn btn-default btn-sm border rounded" href="{{ route('dimentioncombinations.index') }}"> {{trans('language.lblmsg_Cancel') }}</a>
						<button type="submit" class="btn btn-primary btn-sm border rounded">{{trans('language.update') }}</button>
					</div>
				</div>
		</div>
    </form>
</div>
<script>
selectLedgerName = function(){
    var LedgerCode = $('#LedgerCode').val(); 
    //alert(selectFromCurrency); 
    var result = LedgerCode.split('|');
    //$('#LeCode').val(result[0]);
    $('#LedgerName').val(result[1]);
}

selectCostCentre = function(){
    var CostCentre = $('#CostCentre').val(); 
    //alert(ToCurrency); 
    var result_costcenter = CostCentre.split('|');
    //$('#LeCode').val(result[0]);
    $('#DimensionName').val(result_costcenter[1]);
}

selectPurpose = function(){
    var Purpose = $('#Purpose').val(); 
    //alert(Purpose); 
    var result_purpose = Purpose.split('|');
    //$('#LeCode').val(result[0]);
    $('#PurposeName').val(result_purpose[1]);
}

selectDepartment = function(){
    var Department = $('#Department').val(); 
    //alert(ToCurrency); 
    var result_department = Department.split('|');
    //$('#LeCode').val(result[0]);
    $('#DepartmentName').val(result_department[1]);
}

// When the document is ready
$(document).ready(function () {
	
	$('#example1').datepicker({
		format: "dd/mm/yyyy"
	});  

});
</script>
@endsection