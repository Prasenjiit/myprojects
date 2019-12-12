@extends('layouts.app')
@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">{{ __('message.lblmsg_Cbmaster_title') }}</h6>
            </div>
   
    <div class="zrow py-3 ml-2">
        <div class="col-xs-12 col-sm-12 col-md-12">
               <table class="table table-striped table-bordered table-hover">
				
				<tbody>
                    <tr>
                      <th width=25%>{{ __('message.lblmsg_Cbmaster_td_code') }}</th>
                      <td>{{$view->Cash_BankCode}}</td>
                    </tr>
                   
                    <tr>
                      <th>{{ __('message.lblmsg_Cbmaster_td_name') }}</th>
                      <td>{{$view->Bank_Name}}</td>
                    </tr>
                    <tr>
                      <th>{{ __('message.lblmsg_Cbmaster_td_ifsc') }}</th>
                      <td>{{$view->IFSC}}</td>
                    </tr>
                    <tr>
                      <th>{{ __('message.lblmsg_Cbmaster_td_iban') }}</th>
                      <td>{{$view->IBAN}}</td>
                    </tr>
                    <tr>
                      <th>{{ __('message.lblmsg_Cbmaster_td_swift') }}</th>
                      <td>{{$view->SWIFT}}</td>
                    </tr>
                    <tr>
                      <th>{{ __('message.lblmsg_Cbmaster_td_ledger_account') }}</th>
                      <td>{{$view->LedgerName}}</td>
                    </tr>
                    <tr>
                      <th>{{ __('message.lblmsg_Cbmaster_td_cash') }}</th>
                      <td><?php if($view->Cash == 1){echo "Yes"; }else{echo "No";} ?></td>
                    </tr>
                  </tbody>
			  </table>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <table class="table table-striped table-bordered table-hover">
             <thead>
                 <tr>
                    <th>{{ __('message.lblmsg_Cbmaster_td_Address') }}</th>
                    <th width="20%">{{ __('message.lblmsg_Cbmaster_td_Address_type') }}</th>
                    <th width="20%">{{ __('message.lblmsg_Cbmaster_td_address_status') }}</th>
                 </tr>
             </thead>
             <tbody>
                 <?php foreach ($slave as $key => $value) { ?>
                    <tr>
                             <td>{{$value->Door_No.' , '.$value->House_No.' , '.$value->Street1.' , '.$value->Street2.' , '.$value->City.' , '.$value->Country.' , '.$value->State_Code}}</td>
                             <td>{{$value->address_type}}</td>
                            <td><?php if($value->primary_address == 1){echo "Active"; } ?></td>
                          </tr>
                <?php } ?>
                 
               </tbody>
           </table>
     </div>
        
    </div>
     <div class="col-xs-12 col-sm-12 col-md-12">
                <a class="btn btn-primary btn-sm border rounded" href="{{ route('CBMaster.index') }}"> Back</a>
            </div>
</div>
@endsection