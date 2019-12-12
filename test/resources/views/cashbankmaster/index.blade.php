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
                {{trans('language.lblmsg_Cbmaster_title') }}
            </strong> &nbsp;
        </span>
        <span style="float:left;">
          <a class="btn btn-primary btn-sm" href="{{ route('CBMaster.create') }}">{{trans('language.lblmsg_Cbmaster_title_create')}} </a>            
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
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th>SL. No.</th>
							<th>{{trans('language.lblmsg_Cbmaster_td_code') }}</th>
							<th>{{trans('language.lblmsg_Cbmaster_td_name') }}</th>
                            <th>{{trans('language.lblmsg_Cbmaster_td_ledger_account') }}</th>
                            <th>{{trans('language.lblmsg_Cbmaster_td_cash') }}</th>
                           
                          
							<th width="11%" >Action</th>
						</tr>
					</thead>
					<tbody>
						<?php if(!empty($view)){ foreach($view as $key=>$row){ ?>
						<tr>
							<td width="10%">{{$key+1}}</td>
							<td>{{$row->Cash_BankCode}}</td>
							<td>{{$row->Bank_Name}}</td>
							<td>{{$row->LedgerName}}</td>
							<td><?php if($row->Cash == 1){echo "Yes"; }else{echo "No";} ?></td>
							<td> 
							<a  href="{{ route('CBMaster.show',$row->id) }}"><i class="fa fa-eye text-info" ></i></a>	
							<a href="{{route('CBMaster.edit',$row->id)}}" class="btn btn-sm" title="EDIT"><i class="fa fa-cogs text-danger" ></i></a>&nbsp;&nbsp;
							<!--<a href="JavaScript:Void(0);" id="view_data" onclick="view_details({{$row->id}})"  title="View"><i class="fa fa-trash text-danger" ></i></a>-->

							 </td>
							</tr>
						<?php  }} ?>
					</tbody>
				</table>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div><!-- /.col -->
</div><!-- /.row -->
</section>

<!-- User edit form end -->
  
<script>
function view_details(id){
	$('#uq_id').val(id);
	$('#myModal').modal('show');
}
$(document).ready(function() {
    $('#dataTable').DataTable( {
        "pagingType": "full_numbers"
    } );
} );

</script>

{!! Html::script('js/parsley.min.js') !!}  
{!! Html::script('js/jquery.form.js') !!}   
@endsection
