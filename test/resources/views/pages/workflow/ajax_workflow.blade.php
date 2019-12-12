<style>
.card_content {
/*padding: 0 !important;
margin: 0 !important;
padding-left: 3px !important;*/
min-width:400px !important;
}

.card_content_padding {
padding-right: 2 !important;
padding-left: 2px !important;
}

.nopadding_table
{
      padding: 0 !important;
      margin: 0 !important;
}

.borderless td, .borderless th {
    border: none !important;
    padding: 2px !important;
    background-color: #fff !important;

}
.expired{
  color:red;
}
.noexpire{
  color:black;
}
           
</style>

@php
$workflow_color =(isset($wf_details->workflow_color)&&$wf_details->workflow_color)?$wf_details->workflow_color:'';
$background = ($workflow_color)?'background:'.$workflow_color.';':'';
$background_color =  ($workflow_color)?'background-color:'.$workflow_color.';':'';  
$border =  ($workflow_color)?'border:1px solid '.$workflow_color.';':'';
$loops=0;
$loopend= sizeof($wf_stage_details);
$k=0;
@endphp

@foreach($wf_stage_details as $key => $value)
@php $loops++; @endphp
         <div class="col-lg-5 card_content @if($loops > 1) card_content_padding @endif">
            <div class="box  box-solid">
               <div class="box-header wf_header" style="color: #fff; {{$background}}  {{$background_color}}">
                  <h3 class="box-title">{{$value['workflow_stage_name']}} </h3>
                  @if($loops != $loopend)
                     <i class="fa fa-play pull-right"></i>
                  @endif
               </div>
               <div class="row">
               <div class="col-sm-12" >
                <ul class="connectedSortable_doc" id="{{$value['workflow_stage_id']}}" style="border-color:{{$workflow_color}}">  
                @foreach($value['docs'] as $key_doc => $value_doc) 
                 @php
                  $data_options = 'data-activity="0" data-workflow='.$workflow_id.' data-stageid='.$value['workflow_stage_id'].' data-objectid='.$value_doc['obj_id'].' data-objecttype='.$value_doc['obj_type']; 
                   $k++; 
                 @endphp
                  
                 <li {{$data_options}} style="border-color:{{$workflow_color}};padding-left: 5px;padding-right: 5px;">
                  <table class="table nopadding_table" style="background-color: #fff;">
                <tbody>
                <tr>
                  
                  <th>
                    @if($value_doc['obj_type'] == 'document')
                    <a href="{{ url('documentManagementView') }}?dcno={{$value_doc['obj_id']}}&page=document" title="Open Document" target="_blank" style="color: #333">
                    <i class="{{$value_doc['obj_icon']}}"></i> 
                   </a> 
                   @endif
                   @if($value_doc['obj_type'] == 'form')
                    <a href="{{ url('form_details') }}/{{$value_doc['obj_form_id']}}?response={{$value_doc['obj_id']}}" title="Open Form" target="_blank" style="color: #333">
                    <i class="{{$value_doc['obj_icon']}}"></i> 
                   </a> 
                   @endif
                  {{$value_doc['obj_name']}} </th>
                  
                </tr>

                <tr>
                  
                  <td align="center">
                    <a href="javascript:void(0)" {{ $data_options }} class="btn btn-box-tool add_activity" data-activity="0" data-activity="0" data-action="add"><i class="fa fa-plus"></i> Add Activity </a> 
                    <a href="javascript:void(0)" {{ $data_options }} class="btn btn-box-tool complete_work_flow" ><i class="fa fa-location-arrow"></i> Complete Workflow </a>

                    <a href="javascript:void(0)"  {{ $data_options }} class="btn btn-box-tool handler_doc change_stage" ><i class="fa fa-exchange "></i> Change stage </a>

                    <!-- <a href="javascript:void(0)"  {{ $data_options }} class="btn btn-box-tool show_more_activity" data-loop="{{$k}}"><i class="fa fa-angle-down"></i></a> -->

                  </td>
                  
                </tr>
                </tbody>
                </table>
                 <ul style="display: block;" class="products-list product-list-in-box connectedSortable_activity nopadding_class" id="show_li_{{$k}}"> 
                @php 
                $doc_activity_count = sizeof($value_doc['doc_activity']);
                $dc=0; 
                @endphp
                @foreach($value_doc['doc_activity'] as $key_activity => $value_activity)  
                @php 
                $dc++; 
                @endphp
                <li  style="border-top: 1px dotted #d2d6de" <?php if(($value_activity['activity_due_date'] != null) && ($value_activity['activity_due_date'] <= date('Y-m-d'))){?> class="expired"<?php }else{ ?> class="noexpire" <?php } ?>>
                 <table class="table nopadding_table borderless" style="@if($dc != $doc_activity_count )border-bottom: 1px dotted {{$workflow_color}} @endif background-color: #fff !important;">
                <tbody> 
                <tr>
                  <td width="50%" class="handler">Activity: {{$value_activity['activity_name']}}</td> 
                  <td width="50%" align="right">

               <span class="pull-right tool_activity">
               

               <a href="javascript:void(0)"  data-activity="{{$value_activity['document_workflow_id']}}" data-objectid="{{$value_doc['obj_id']}}" data-objecttype="{{$value_doc['obj_type']}}" data-stageid="{{$value['workflow_stage_id']}}'" class="add_activity" data-action="view"><i class="fa fa-eye"></i> </a>
@php 
if(in_array($value_activity['assigned_by'],$wf_users) || in_array($value_activity['assigned_to'],$wf_users) || $super_admin){
@endphp
              <a href="javascript:void(0)"  data-activity="{{$value_activity['document_workflow_id']}}" data-objectid="{{$value_doc['obj_id']}}" data-objecttype="{{$value_doc['obj_type']}}" data-stageid="{{$value['workflow_stage_id']}}'" class="add_activity" data-action="edit"><i class="fa fa-edit"></i> </a>
@php 
}
if(in_array($value_activity['assigned_by'],$wf_users) || $super_admin ){
@endphp
            <a href="javascript:void(0)" data-workflow="{{$workflow_id}}" data-activity="{{$value_activity['document_workflow_id']}}" class="delete_activity"><i class="fa fa-trash-o" ></i> </a>
@php 
}
@endphp
            </span>
                    
                  </td>
                </tr>
                <tr>
                  <td width="50%">Due Date: {{$value_activity['activity_due_date']}}</td>
                  <td width="50%" align="right">Date: {{$value_activity['activity_date']}}</td>
                </tr>
                <tr>
                  <td width="50%">Assigned to: {{$value_activity['assigned_to_user_name']}}</td>
                  <td width="50%" align="right">Assigned by: {{$value_activity['assigned_by_user_name']}}</td>
                </tr>
                <tr>
                  <td colspan="2">Note: {{$value_activity['activity_note']}}</td>
                </tr>
                @php if($value_activity['action_activity']){ @endphp
                <tr>
                  <td width="50%">Action: {{$value_activity['action_activity_name']}}</td>
                  <td width="50%" align="right">Date: {{$value_activity['action_activity_date']}}</td>
                </tr>
                <tr>
                  <td colspan="2">Note: {{$value_activity['action_activity_note']}}</td>
                </tr>
               @php } @endphp
                </tbody></table>  

                </li>
                @endforeach
                </ul>
                </li>   
              
              @endforeach
              </ul>
</div>
                

              
             
               </div>
               
            </div>
         </div>
         @endforeach


