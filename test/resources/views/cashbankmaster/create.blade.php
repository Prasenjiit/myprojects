<?php
include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')

@section('main_content')
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

<div class="modal-body"> 
<div class="card shadow mb-4">
	<form action="{{ route('CBMaster.store') }}" method="POST" id="calendat_frm">
		@csrf
        <div class="alert alert-danger header_alert" style="display:none">
                <ul id="header_alert_ul">
                   
                   
                    </ul>
                </div>
	

		<div class="zrow py-3 ml-2 row">
			<div class="col-md-6">
				<div class="form-group">
					<label class="control-label col-sm-2">{{trans('language.lblmsg_Cbmaster_form_code') }} :</label>
					<div class="col-sm-10 float-right">
                        <input type="text" name="code" id="code"  class="form-control form-control-sm"  placeholder="{{trans('language.lblmsg_Cbmaster_form_code') }}" value="{{old('code')}}" maxlength="{{config('global.articleCodeLength')}}" />
                        <span class="alert text-danger" role="alert" id="error_code" style="font-weight:bold"></span>
					</div>
				</div>
			</div>
		          
            <div class="col-md-6">
				<div class="form-group">
					<label class="control-label col-sm-2">{{trans('language.lblmsg_Cbmaster_form_name') }} :</label>
					<div class="col-sm-10 float-right">
                        <input type="text" name="name" id="name"  class="form-control form-control-sm" value="{{old('name')}}"placeholder="{{trans('language.lblmsg_Cbmaster_form_name') }}" maxlength="{{config('global.articleCodeLength')}}" />
                        <span class="alert text-danger" id="error_name" style="font-weight:bold"></span>
					</div>
				</div>
            </div>
            
            <div class="col-md-6">
				<div class="form-group">
					<label class="control-label col-sm-2">{{trans('language.lblmsg_Cbmaster_form_ifsc') }} :</label>
					<div class="col-sm-10 float-right">
					   <input type="text" name="ifsc" id="ifsc"  class="form-control form-control-sm" value="{{old('ifsc')}}"placeholder="{{trans('language.lblmsg_Cbmaster_form_ifsc') }}" maxlength="{{config('global.articleCodeLength')}}" />
					</div>
				</div>
            </div>
            <div class="col-md-6">
				<div class="form-group">
					<label class="control-label col-sm-2">{{trans('language.lblmsg_Cbmaster_form_iban') }} :</label>
					<div class="col-sm-10 float-right">
					   <input type="text" name="iban" id="iban"  class="form-control form-control-sm" value="{{old('iban')}}" placeholder="{{trans('language.lblmsg_Cbmaster_form_iban') }}" maxlength="{{config('global.articleCodeLength')}}" />
					</div>
				</div>
            </div>

            <div class="col-md-6">
				<div class="form-group">
					<label class="control-label col-sm-2">{{trans('language.lblmsg_Cbmaster_form_swift') }} :</label>
					<div class="col-sm-10 float-right">
					   <input type="text" name="swift" id="swift"  class="form-control form-control-sm" value="{{old('swift')}}"placeholder="{{trans('language.lblmsg_Cbmaster_form_swift') }}" maxlength="{{config('global.articleCodeLength')}}" />
					</div>
				</div>
            </div>
            <div class="col-md-6">
				<div class="form-group">
					<label class="control-label col-sm-2">{{trans('language.lblmsg_Cbmaster_form_cash') }} : </label><input type="checkbox" name="cash" id="cash" value="1" />
					
				</div>
            </div>
            <div class="col-md-12">
				<div class="form-group">
					<label class="control-label col-sm-2">{{trans('language.lblmsg_Cbmaster_form_ledger_account') }} :</label>
					<div class="col-sm-10 float-right">
                        <select class="form-control form-control-sm" name="offset_ledger" id="offset_ledger">
                            <option value="">Select {{trans('language.lblmsg_Cbmaster_form_ledger_account') }} </option>
							<?php foreach($ledger_account as $row){ ?>
								<option value="{{ $row->LedgerCode }}">{{ $row->LedgerName }} </option>
							<?php } ?>
                        </select>
                        <span class="alert text-danger" id="error_offset_ledger" style="font-weight:bold"></span>
					</div>
				</div>
			</div>
    
            <div class="col-md-12">
                <input type="hidden" id="table_tr_count" value="1">
                <div class="col-md-10 shadow-lg p-3 mb-5 rounded">
                    <table class="table table-stripped table-bordered">
                        <tr>
                            <th width="40%">{{trans('language.lblmsg_Cbmaster_form_Address') }}</th>
                            <th width="35%">{{trans('language.lblmsg_Cbmaster_form_Address_type') }}</th>
                            <th width="18%">{{trans('language.lblmsg_Cbmaster_form_address_status') }}</th>
                            <th width="7%">Action</th>
                        </tr>
                        <tbody id="dynamic_table">
                            <tr id="tr_1">
                                <td>
                                    <select class="form-control form-control-sm fetch_address_id" name="address[]" id="address_1">
                                        <option value="">Select {{trans('language.lblmsg_Cbmaster_form_Address') }}</option>
                                        <?php foreach($addressbook as $adrow){ ?>
                                        <option value="{{$adrow->id}}">{{$adrow->Door_No.' , '.$adrow->House_No.' , '.$adrow->Street1.' , '.$adrow->Street2.' , '.$adrow->City.' , '.$adrow->Country.' , '.$adrow->State_Code}}</option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td>
                                        <select class="form-control form-control-sm fetch_address_type" name="address_type[]" id="address_type_1">
                                            <option value="">Select {{trans('language.lblmsg_Cbmaster_form_Address_type') }}</option>
                                            <?php foreach($address_type as $atrow){ ?>
                                            <option value="{{$atrow->AddressType}}">{{$atrow->AddressType}}</option>
                                            <?php } ?>
                                       </select>
                                </td>
                                <td align="center"><input type="hidden" name="tb_row" id="tb_row" class="tb_row" value="1">
                                    <input type="checkbox" class="check_address_status" name="primary_address[]" id="primary_address_1" value="1">
                                </td>
                                <td align="center"> <a href="javascript:void(0);" onclick="remove_table_tr(1);"><i class="fa fa-trash text-danger"></i></i></a> </td>
                            </tr>
                            
                        </tbody>
                    </table>
                </div>
                <div class="col-md-2  float-right" style=" padding-top:60px;" ><button type="button" onclick="table_add_new_row();" class="btn btn-info btn-sm"><i class="fa fa-plus"></i> ADD</button></div>

            </div>
            <div class="col-sm-12">
		        <div class="form-group">	
					  <div class="col-sm-offset-1 col-sm-9">
						<div class="get_success_total" align="center" style="background-color: #174b10;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
						<div class="get_success" align="center" style="display:none"><img src="/images/ajax_loader.gif" /></div>
						<div class="get_error_total" align="center" style="background-color: #bf0000;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px; display:none"></div>
				  	  </div>
				    </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <button type="button" id="submit_button"  class="btn btn-primary btn-sm border rounded float-right">{{trans('language.lblmsg_Submit') }}</button> &nbsp; &nbsp;
                    <a class="btn btn-info btn-sm border rounded float-right mr-1" href="{{ route('CBMaster.index') }}"> {{trans('language.lblmsg_Cancel') }}</a>
                </div>
            </div>

		</div>
	</form>
</div>
</div>

@endsection


<script>
        $('input').attr('autocomplete','off');
    function table_add_new_row(){
         var table_tr_count=$('#table_tr_count').val();
         table_tr_count = table_tr_count+1;
         $('#table_tr_count').val(table_tr_count);
         $('#dynamic_table').append('<tr id="tr_'+table_tr_count+'"><td><select class="form-control form-control-sm fetch_address_id" name="address[]" id="address_1"><option value="">Select {{trans('language.lblmsg_Cbmaster_form_Address') }}</option><?php foreach($addressbook as $adrow){ ?><option value="{{$adrow->id}}">{{$adrow->Door_No." , ".$adrow->House_No." , ".$adrow->Street1." , ".$adrow->Street2." , ".$adrow->City." , ".$adrow->Country." , ".$adrow->State_Code}}</option><?php } ?></select></td><td><select class="form-control form-control-sm fetch_address_type" name="address_type" id="address_type_1"><option value="">Select {{trans('language.lblmsg_Cbmaster_form_Address_type') }}</option><?php foreach($address_type as $atrow){ ?><option value="{{$atrow->AddressType}}">{{$atrow->AddressType}}</option><?php } ?></select></td><td align="center"><input type="hidden" name="tb_row" id="tb_row" class="tb_row" value="'+table_tr_count+'"><input type="checkbox" class="check_address_status" name="primary_address[]" id="primary_address_1" value="'+table_tr_count+'"></td><td align="center"><a href="javascript:void(0);" onclick="remove_table_tr('+table_tr_count+');"><i class="fa fa-trash text-danger"></i></i></a></td></tr>');
        

        

    }

    function remove_table_tr(id){
        $('#tr_'+id).remove();

    }
    
    $('#submit_button').click(function(){
        var delay=5000;
        var formdata=  new FormData();
        formdata.append('_token', '{{ csrf_token() }}');
        var code = $('#code').val();  formdata.append('code', code);
        
        var name = $('#name').val();  formdata.append('name', name);
        var ifsc = $('#ifsc').val();  formdata.append('ifsc', ifsc);
        var iban = $('#iban').val();  formdata.append('iban', iban);
        var swift = $('#swift').val();  formdata.append('swift', swift);
        var cash = $('#cash:checked').val();  formdata.append('cash', cash);
        if(cash != undefined){formdata.append('cash', 1);}else{ formdata.append('cash', 0);}
        var offset_ledger = $('#offset_ledger').val(); formdata.append('offset_ledger', offset_ledger);
        var fetch_address_id = $(".fetch_address_id");
        for(var i =0; i< fetch_address_id.length ; i++){
            formdata.append("address_id[]",fetch_address_id[i].value);
            
        }
        var fetch_address_type = $(".fetch_address_type");
        for(var i =0; i< fetch_address_type.length ; i++){
            formdata.append("address_type[]",fetch_address_type[i].value);
            
        }
        var check_address_status = $('.check_address_status:checked');
  
         if(check_address_status.length == 1){
            var address_status = $('.check_address_status:checked').val();
            formdata.append("address_status",address_status);  
         }else{ formdata.append("address_status",'undefinedundefined');  }   
            
        var tb_row = $(".tb_row");
        for(var i =0; i< tb_row.length ; i++){
            formdata.append("tb_row[]",tb_row[i].value);
            
        }   

        var error = '';
        if(code == ''){ error = error+1; $('#error_code').html('Enter Code'); }
        if(name == ''){ error = error+1; $('#error_name').html('Enter name'); }
        if(offset_ledger == ''){ error = error+1; $('#error_offset_ledger').html('Select Ledger Account'); }
        if(check_address_status.length > 1){ error = error+1; alert('Select Only One Primary Address') }
        setTimeout(function(){ $('.alert').empty(); }, 7000);
        if(error == ''){
            $(".get_success").fadeOut();
            $("#submit_button").prop('disabled','true');
            $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }); 
        
            $.ajax({
                   url: '{{ route('CBMaster.store') }}',
                    type: 'POST',
                    dataType: 'JSON',
                    data: formdata,
                    type: "POST",
                    contentType:false,
                    cache:true,
                    processData:false,
                    error: function (error) {
                        $(".get_error_total").html('Enter Unique Code');    
                        setTimeout(function(){  $(".get_error_total").hide()}, delay);
                        
                    },
                    success: function (res) { 

                       if(res){

                        if(res == 'success'){
                        $(".get_success").fadeOut();
                        $(".get_success_total").html('New Cash Bank Master is Inserted Successfully.');
                        
                        $(".get_success_total").fadeIn();
                        delay = 3000;
                        setTimeout(function(){ $('.get_success_total').fadeOut(); }, delay);
                        setTimeout(function(){ window.location.replace("{{ route('CBMaster.index') }}"); }, delay);
                    }else{
                        /*
                        $(".get_error_total").html('There is some problem in server connection, Please try after Some time...); 
                        setTimeout(function(){ window.location.replace("{{ route('CBMaster.index') }}"); }, delay);
                        */
                        
                    }
                    } }
                    
                }); 

        }


    });

</script>

