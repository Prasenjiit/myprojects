<?php
include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')
 
@section('main_content')

<section class="content-header">
    <div class="col-sm-8">
        <span style="float:left;">
            <strong>
                {{trans('language.lblmsg_Cbmaster_title_update') }}
            </strong> &nbsp;
        </span>
    </div>
</section> 

<div class="card shadow mb-4">
	<form action="{{ route('CBMaster.update',$view->id) }}" method="POST" id="calendat_frm">
		@csrf
		@if ($errors->any())
			<div class="alert alert-danger">
				{!! __('message.errmsg_Msg') !!}
				<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif

		<div class="zrow py-3 ml-2 row">
			<div class="col-md-6">
				<div class="form-group">
					<label class="control-label col-sm-2">{{trans('language.lblmsg_Cbmaster_form_code') }} :</label>
					<div class="col-sm-10 float-right">
                        <input type="text" name="code" id="code"  class="form-control form-control-sm"  placeholder="{{ __('message.lblmsg_Cbmaster_form_code') }}" value="{{$view->Cash_BankCode}}" maxlength="{{config('global.articleCodeLength')}}" readonly />
                        <span class="alert text-danger" role="alert" id="error_code" style="font-weight:bold"></span>
					</div>
				</div>
			</div>
		          
            <div class="col-md-6">
				<div class="form-group">
					<label class="control-label col-sm-2">{{trans('language.lblmsg_Cbmaster_form_name') }} :</label>
					<div class="col-sm-10 float-right">
                    <input type="text" name="name" id="name"  class="form-control form-control-sm" value="{{$view->Bank_Name}}"placeholder="{{trans('language.lblmsg_Cbmaster_form_name') }}" maxlength="{{config('global.articleCodeLength')}}" />
                    <span class="alert text-danger" id="error_name" style="font-weight:bold"></span>
					</div>
				</div>
            </div>
            
            <div class="col-md-6">
				<div class="form-group">
					<label class="control-label col-sm-2">{{trans('language.lblmsg_Cbmaster_form_ifsc') }} :</label>
					<div class="col-sm-10 float-right">
					<input type="text" name="ifsc" id="ifsc"  class="form-control form-control-sm" value="{{$view->IFSC}}"placeholder="{{trans('language.lblmsg_Cbmaster_form_ifsc') }}" maxlength="{{config('global.articleCodeLength')}}" />
					</div>
				</div>
            </div>
            <div class="col-md-6">
				<div class="form-group">
					<label class="control-label col-sm-2">{{trans('language.lblmsg_Cbmaster_form_iban') }} :</label>
					<div class="col-sm-10 float-right">
					<input type="text" name="iban" id="iban"  class="form-control form-control-sm" value="{{$view->IBAN}}" placeholder="{{ __('message.lblmsg_Cbmaster_form_iban') }}" maxlength="{{config('global.articleCodeLength')}}" />
					</div>
				</div>
            </div>
            <div class="col-md-6">
				<div class="form-group">
					<label class="control-label col-sm-2">{{trans('language.lblmsg_Cbmaster_form_swift') }} :</label>
					<div class="col-sm-10 float-right">
					<input type="text" name="swift" id="swift"  class="form-control form-control-sm" value="{{$view->SWIFT}}"placeholder="{{trans('language.lblmsg_Cbmaster_form_swift') }}" maxlength="{{config('global.articleCodeLength')}}" />
					</div>
				</div>
            </div>
            <div class="col-md-6">
				<div class="form-group">
                <label class="control-label col-sm-2">{{trans('language.lblmsg_Cbmaster_form_cash') }} : </label><input type="checkbox" name="cash" id="cash" value="1" <?php if($view->Cash == 1){ ?> checked <?php } ?> />
					
				</div>
            </div>
            <div class="col-md-12">
				<div class="form-group">
					<label class="control-label col-sm-2">{{trans('language.lblmsg_Cbmaster_form_ledger_account') }} :</label>
					<div class="col-sm-10 float-right">
                        <select class="form-control form-control-sm" name="offset_ledger" id="offset_ledger" disabled>
                            <option value="">Select {{trans('language.lblmsg_Cbmaster_form_ledger_account') }} </option>
							<?php foreach($ledger_account as $row){ ?>
                            <option <?php if($row->LedgerCode == $view->LedgerAccount){ ?> selected <?php } ?> value="{{ $row->LedgerCode }}">{{ $row->LedgerName }} </option>
							<?php } ?>
                        </select>
                        <span class="alert text-danger" id="error_offset_ledger" style="font-weight:bold"></span>
					</div>
				</div>
			</div>
    
            <div class="col-md-12">
                <div class="col-md-10 shadow-lg p-3 mb-5 rounded">
                    <table class="table table-stripped table-bordered">
                        <tr>
                            <th width="40%">{{trans('language.lblmsg_Cbmaster_form_Address') }}</th>
                            <th width="35%">{{trans('language.lblmsg_Cbmaster_form_Address_type') }}</th>
                            <th width="18%">{{trans('language.lblmsg_Cbmaster_form_address_status') }}</th>
                            
                        </tr>
                        <tbody id="dynamic_table">
                          <?php foreach($slave as $row){ ?>  
                            <tr id="tr_1">
                                <td>
                                    <select class="form-control form-control-sm fetch_address_id" name="address[]" id="address_1">
                                        <option value="">Select {{trans('language.lblmsg_Cbmaster_form_Address') }}</option>
                                        <?php foreach($addressbook as $adrow){ ?>
                                        <option <?php if($row->address_id == $adrow->id){ ?> selected <?php } ?>  value="{{$adrow->id}}">{{$adrow->Door_No.' , '.$adrow->House_No.' , '.$adrow->Street1.' , '.$adrow->Street2.' , '.$adrow->City.' , '.$adrow->Country.' , '.$adrow->State_Code}}</option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td>
                                        <select class="form-control form-control-sm fetch_address_type" name="address_type[]" id="address_type_1">
                                            <option value="">Select {{trans('language.lblmsg_Cbmaster_form_Address_type') }}</option>
                                            <?php foreach($address_type as $atrow){ ?>
                                            <option <?php if($row->address_type == $atrow->AddressType){ ?> selected <?php } ?> value="{{$atrow->AddressType}}">{{$atrow->AddressType}}</option>
                                            <?php } ?>
                                       </select>
                                </td>
                               <td align="center"><input type="hidden" name="tb_row" id="tb_row" class="tb_row" value="{{$row->id}}">
                                    <input type="checkbox" class="check_address_status" name="primary_address[]" id="primary_address_1" value="{{$row->id}}" <?php if($row->primary_address == 1){ ?> checked <?php } ?>>
                                </td>
                                
                            </tr>
                        <?php } ?>  
                        </tbody>
                    </table>
                </div>

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

  			<div class="col-md-12">
				<div>
                <button type="button" id="submit_button"  class="btn btn-primary btn-sm border rounded float-right">{{trans('language.lblmsg_Submit') }}</button> &nbsp; &nbsp;
			<a class="btn btn-info btn-sm border rounded float-right mr-1" href="{{ route('CBMaster.index') }}"> {{trans('language.lblmsg_Cancel') }}</a>
				</div>
			</div>
		</div>
	</form>
</div>

@endsection

@section('script')

<script>
      $('input').attr('autocomplete','off');
   
    function remove_table_tr(id){
        $('#tr_'+id).remove();

    }
    
    $('#submit_button').click(function(){
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
                   url: '{{ url("/CBMaster/update/".$view->id)}}',
                    type: 'POST',
                    dataType: 'JSON',
                    data: formdata,
                    type: "POST",
                    contentType:false,
                    cache:true,
                    processData:false,

                    success: function (res) {  if(res){ 
                        if(res == 'success'){
						$(".get_success").fadeOut();
						$(".get_success_total").html('Cash Bank Master details is Updated Successfully.');
						
						$(".get_success_total").fadeIn();
						delay = 3000;
						setTimeout(function(){ $('.get_success_total').fadeOut(); }, delay);
						setTimeout(function(){ window.location.replace("{{ route('CBMaster.index') }}"); }, delay);
					}else{
						//$(".get_success_total").fadeIn();
						error_message('There is some problem in server connection, Please try after Some time...');	
						setTimeout(function(){ window.location.replace("{{ route('CBMaster.index') }}"); }, delay);
					}
                    } }
                }); 

        }


    });

</script>
@endsection