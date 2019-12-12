<?php
include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')
  @section('main_content')

<style type="text/css">
    @media(max-width: 517px){
        .box{
            overflow-x:scroll;
        }
    }
    @media(max-width: 388px){
        div#documentGroupDTsub_wrapper {
            overflow-x: scroll;
        }
    }
    @media(max-width: 991px){
        .content-header {
            padding: 22px 15px 0 !important;
            height: 48px;
        }
        .content-header>h1 {
            margin-top: -19px;
        }
    }

    @media (max-width: 767px){
        .content-header {
            padding: 15px 0px 0 !important;
            height: 20px;
        }
    }

    /*<--mobile view table-->*/
    @media(max-width:500px){
        .box{
            overflow-x: auto;
        }
    }
</style>

<section class="content-header">
    <div class="col-sm-8">
        <span style="float:left;">
            <strong>
                {{trans('language.dimension_combitaion_details')}}
            </strong> &nbsp;
        </span>
    </div>
</section>



<section class="content" id="shw">
<div class="row">
    <div class="col-xs-12">
        <div class="box box-info">
            <div class="box-header">
                <span class="pull-right">
                    <?php
                    $user_permission=Auth::user()->user_permission;
                    include (public_path()."/storage/includes/lang1.en.php" );                         
                    ?>                        
                </span>
            </div>
            <div class="box-body">
                <form action="{{ route('dimentioncombinations.store') }}" method="POST">
		@csrf
	  
		<div class="py-3 ml-2">
			<div class="col-xs-12 col-sm-12 col-md-12">
				<div class="form-group">
					<label class="control-label col-sm-2">{{trans('language.LedgerCode') }}:</label>
					<div class="col-sm-5 float-right">
						<select name="LedgerCode" id="LedgerCode" class="form-control" onchange="selectLedgerName()">
						<option value="">--- Select {{trans('language.LedgerCode') }} ---</option>
						@foreach ($ledger_masters as $ledger_master)
						<option value="{{ $ledger_master->LedgerCode }}|{{ $ledger_master->LedgerName}}"> {{ $ledger_master->LedgerCode }}</option>
						@endforeach
						</select>
					</div>
					<div class="col-sm-5 float-right">
						<input type="text" readonly  name="LedgerName" id="LedgerName"  placeholder="{{trans('language.lblmsg_LedgerName') }}"  class="form-control" />
					</div>			
				</div>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-12">
				<div class="form-group">
					<label class="control-label col-sm-2">{{trans('language.CostCentre') }}:</label>
					<div class="col-sm-5 float-right">
					<select name="CostCentre" id="CostCentre" class="form-control" onchange="selectCostCentre()">
						<option value="">--- Select Cost Centre ---</option>
						@foreach ($dimension_masters as $dimension_master)
							@if($dimension_master->DimentionType == 'Costcenter')
								<option value="{{ $dimension_master->DimensionCode }}|{{ $dimension_master->DimensionName}}"> {{ $dimension_master->DimensionName }}</option>
							@endif
						@endforeach
					</select>
					</div>
					<div class="col-sm-5 float-right">
						<input type="text" readonly  name="DimensionName" id="DimensionName" placeholder="{{trans('language.lblmsg_DimensionName') }}"  class="form-control" />
					 
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-12">
				<div class="form-group">
					<label class="control-label col-sm-2">{{trans('language.Purpose') }}:</label>
					<div class="col-sm-5 float-right">
						<select name="Purpose" id="Purpose" class="form-control" onchange="selectPurpose()">
						<option value="">--- Select Purpose ---</option>
						@foreach ($dimension_masters as $dimension_master)
						@if($dimension_master->DimentionType == 'Purpose')
						<option value="{{ $dimension_master->DimensionCode }}|{{ $dimension_master->DimensionName}}"> {{ $dimension_master->DimensionName }}</option>
						@endif
						@endforeach
						</select>
					</div>
					<div class="col-sm-5 float-right">
						<input type="text" readonly  name="DimensionName" id="PurposeName" placeholder="{{trans('language.lblmsg_PurposeName') }}"  class="form-control" />
					</div>	
				</div>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-12">
				<div class="form-group">
					<label class="control-label col-sm-2">{{trans('language.Department') }}:</label>
					<div class="col-sm-5 float-right">
						<select name="Department" id="Department" class="form-control" onchange="selectDepartment()">
							<option value="">--- Select Department ---</option>
							@foreach ($department_masters as $department_master)
								<option value="{{ $department_master->DepartmentCode }}|{{ $department_master->DepartmentName}}"> {{ $department_master->DepartmentName }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-sm-5 float-right">
						<input type="text" readonly  name="DepartmentName" id="DepartmentName" placeholder="{{trans('language.lblmsg_Department') }}"  class="form-control" />
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-12">
				<div class="form-group">
					<label class="control-label col-sm-2">{{trans('language.ActiveComb')}}:</label>
					<div class="col-sm-10 float-right">
						<input type="checkbox" value="1" name="Active_Comb"/>
					</div>
				</div>
			</div>

			<div class="col-xs-12 col-sm-12 col-md-12">
				<div class="form-group">
					<label class="control-label col-sm-2">&nbsp;</label>
					<a class="btn btn-default btn-sm border rounded" href="{{ route('dimentioncombinations.index') }}"> {{trans('language.cancel') }}</a>
					<button type="submit" class="btn btn-primary btn-sm border rounded">{{trans('language.save') }}</button>
				</div>
			</div>
		</div>
	</form>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div><!-- /.col -->
</div><!-- /.row -->
</section>

<!-- User edit form end -->
  
  <script>

    $(document).ready(function() {
        $('#stackDT').DataTable( {
            "pagingType": "full_numbers"
        } );
    } );

</script>  

{!! Html::script('js/parsley.min.js') !!}  
{!! Html::script('js/jquery.form.js') !!}   
@endsection
