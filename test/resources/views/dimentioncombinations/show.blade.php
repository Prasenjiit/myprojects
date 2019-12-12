@extends('layouts.app')
<?php
include (public_path()."/storage/includes/lang1.en.php" );
?>  
@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">{{ __('message.lblmsg_DimensionCombinationDetails') }}</h6>
            </div>
   
    <div class="zrow py-3 ml-2">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
            <label><strong>{{trans('language.lblmsg_LedgerName') }}</strong>:</label>
                  @foreach ($ledger_masters as $ledger_master)
                    @if($ledger_master->LedgerCode == $dimentioncombination->LedgerCode)
                        @php($LedgerName = $ledger_master->LedgerName)
                    @endif
                @endforeach
                {{$LedgerName}}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
            <label><strong>{{ __('message.lblmsg_CostCentre') }}</strong>:</label>
            @foreach ($dimension_masters as $dimension_master)
                    @if($dimension_master->DimentionType == 'Costcenter')
                    @php($DimensionName = $dimension_master->DimensionName)
                    @endif
                @endforeach
                {{ $DimensionName }}
            </div>
        </div>


          <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <label><strong>{{trans('language.lblmsg_CurrencyName') }}</strong>:</label>
                @foreach ($department_masters as $department_master)
                 @if($department_master->DepartmentName == 'EMPLOYEE' || $department_master->DepartmentName == 'Officer' || $department_master->DepartmentName == 'Customer Care') 
                 @php($DepartmentName = $department_master->DepartmentName)
                 @endif
                @endforeach
                {{ $DepartmentName }}
            </div>
        </div>

         <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
            <label><strong>{{ __('message.lblmsg_DimensionName') }}</strong>:</label>
                 @foreach ($dimension_masters as $dimension_master)
                    @if($dimension_master->DimentionType == 'Purpose')
                    @php($DimensionName = $dimension_master->DimensionName)
                    @endif
                @endforeach
                {{ $DimensionName }}
            </div>
        </div>

          <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
               
                <input type="checkbox" @if($dimentioncombination->Active_Comb == 1){{ "checked"}}@endif name="Active_Comb">
            </div>
        </div>
    </div>
     <div class="col-xs-12 col-sm-12 col-md-12">
                <a class="btn btn-primary btn-sm border rounded" href="{{ route('dimentioncombinations.index') }}"> {{ __('message.lblmsg_Back') }}</a>
            </div>
</div>
@endsection