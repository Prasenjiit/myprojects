<style type="text/css">
    .form_view_tr,.form_edit_tr
    {
        display: none;
    }
</style>
<?php include (public_path()."/storage/includes/lang1.en.php" ); ?>
{!! Html::script('js/parsley.min.js') !!}
<div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title">Workflow Activity</h4>
               </div>
<div class="modal-body">
 
@php $show_js = false; @endphp
@if($actions=='add' || $actions=='edit')
{!! Form::open(array('url'=> array('workflow_activity_save'), 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'workflow_activity_form', 'id'=> 'workflow_activity_form','data-parsley-validate'=> '')) !!} 
                  <input type="hidden" name="wf_id"  class="wf_id" value="{{ $workflow_id }}" id = "add_wf_id">   
                  <input type="hidden" name="wf_activity_id" class="wf_activity_id" value="{{$activity}}" id = "add_wf_activity_id">
                  <input type="hidden" name="wf_stage_id" class="wf_stage_id" value="{{$stageid}}" id = "add_wf_stage_id">
                  <input type="hidden" name="wf_object_id" class="wf_object_id" value="{{$objectid}}" id = "add_wf_object_id">
                  <input type="hidden" name="wf_object_type" class="wf_object_type" value="{{$objecttype}}" id = "add_wf_object_type">
                   <input type="hidden" name="workflow_doc_id" class="workflow_doc_id" value="" id = "add_workflow_doc_id">
                   <input type="hidden" name="add_hidd_stage_order" id = "add_hidd_stage_order" value="{{@$add_stage_order}}">
                  <div class="form-group">
                     <div class="col-sm-11 alert_space">
                     </div>
                  </div>

                   <div class="form-group">
                     <label for="inputEmail3" class="col-sm-3 control-label" style="padding-top: 0;">{{ucfirst($objecttype)}} {{ $language['name'] }}</label>
                     <div class="col-sm-8">
                         @if($object_info)
                  {{$object_info->object_name}}
                @endif
                     </div>
                  </div>

                   <div class="form-group">
                     <label for="inputEmail3" class="col-sm-3 control-label" style="padding-top: 0;">{{ $language['workflow'] }}</label>
                     <div class="col-sm-8">
                         @if($workflow_info)
                  {{$workflow_info->workflow_name}}
                @endif
                     </div>
                  </div>

                  <div class="form-group">
                     <label for="inputEmail3" class="col-sm-3 control-label">{{ $language['activity'] }}: <span class="compulsary">*</span></label>
                     <div class="col-sm-8">
                        <select class="form-control" id="activity_name"  name="activity_name" required="" data-parsley-required-message="{{$language['activity_name_required']}}" data-parsley-trigger="change focusout">
                           <option value="">{{$language["select_activity"]}}</option>
                           @foreach($activities as $row)
                           <option value="{{$row->activity_id}}" add_last_activity="{{$row->last_activity}}" @if($row->activity_id == $activity_id) selected="selected" @endif>{{$row->activity_name}}</option>
                           @endforeach  
                        </select>
                     </div>
                  </div>
                  <div class="form-group">
                     <label for="activity_due_date" class="col-sm-3 control-label">{{ $language['activity_due_date'] }}:</label>
                     <div class="col-sm-8">
                        <div class="input-group">
                           <div class="input-group-addon">
                              <i class="fa fa-calendar"></i>
                           </div>
                           <input type="text" class="form-control" id="activity_due_date" name="activity_due_date" placeholder="YYYY-MM-DD" value="{{$activity_due_date}}">
                        </div>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label for="new_object_id" class="col-sm-3 control-label">{{ $language['responsible_user'] }}:</label>
                     <div class="col-sm-8">
                        <select class="form-control" id="assigned_to"  name="assigned_to">
                        <option value="">{{ $language['select_user'] }}</option>
                           @foreach($user as $row)
                           <option value="{{$row->username}}" @if($row->username == $activity_to_user) selected="selected" @endif>{{$row->user_full_name}}</option>
                           @endforeach  
                        </select>
                     </div>
                  </div>
                  <div class="form-group">
                     <label for="activity_note" class="col-sm-3 control-label">{{ $language['note'] }}:</label>
                     <div class="col-sm-8">
                        <textarea class="form-control" name="activity_note" id="activity_note" rows="3">{{$activity_note}}</textarea> 
                     </div>
                  </div>
               
                  </form>

@else
@php $show_js = true; @endphp
{!! Form::open(array('url'=> array('save_action_workflow'), 'method'=> 'post', 'class'=> '', 'name'=> 'workflow_action_form', 'id'=> 'workflow_action_form','data-parsley-validate'=> '')) !!} 
<input type="hidden" name="wf_id"  class="wf_action_id" id="wf_action_id" value="{{ $workflow_id }}">   
                  <input type="hidden" name="wf_action_activity_id" id="wf_action_activity_id" value="{{$activity}}">
                  <input type="hidden" id="hidd_wf_id" name="wf_id"  class="wf_id" value="{{ $workflow_id }}">   
                  <input type="hidden" name="wf_activity_id" class="wf_activity_id" value="{{$activity}}">
                  <input type="hidden" id="hidd_wf_stage_id" name="wf_stage_id" class="wf_stage_id" value="{{$stageid}}">
                  <input type="hidden" id="hidd_wf_object_id" name="wf_object_id" class="wf_object_id" value="{{$objectid}}">
                  <input type="hidden" id="hidd_wf_object_type" name="wf_object_type" class="wf_object_type" value="{{$objecttype}}">
                   <input type="hidden" id="hidd_workflow_doc_id" name="workflow_doc_id" class="workflow_doc_id" value="">
<div class="row">
<div class="col-sm-12 alert_form"></div>
<div class="col-sm-12">
<table border="1" id="documentGroupDT" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
        <thead>
            <tr>
                <th colspan="2">Acivity Information</th>
            </tr>
        </thead>
        <tbody>
        <tr>
            <td width="25%" nowrap="nowrap">{{ $language['activity'] }}:</td>   
            <td>
                @foreach($activities as $row)
                           @if($row->activity_id == $activity_id) 
                            {{$row->activity_name}}
                           @endif
                           @endforeach  
           </td>   
            </tr>
            <tr>
            <td>{{ $language['activity_due_date'] }}:</td> 
            <td>{{$activity_due_date}}</td>  
            </tr>
            <tr>
            <td>{{ $language['responsible_user'] }}:</td> 
            <td>
               
               @foreach($user as $row)
                           @if($row->username == $activity_to_user) 
                            {{$row->user_full_name}}
                           @endif
                           @endforeach 
            </td>  
            </tr>

            <tr>
            <td>{{ $language['note'] }}:</td> 
            <td>{{$activity_note}}</td>  
            </tr>
             
            
        
        </tbody>

        <thead>
            <tr>
                <th colspan="2">Additional Details</th>
            </tr>
        </thead>
        <tbody>
        <tr>
            <td width="25%" nowrap="nowrap">{{ $language['name'] }}:</td>   
            <td>
                @if($object_info)
                  {{$object_info->object_name}}
                @endif
           </td>   
            </tr>

             <tr>
            <td width="25%" nowrap="nowrap">{{ $language['workflow'] }}:</td>   
            <td>
                @if($workflow_info)
                  {{$workflow_info->workflow_name}}
                @endif
           </td>   
            </tr>

            <tr>
            <input type="hidden" name="hidd_stage_order" id = "hidd_stage_order" value="{{@$stage_order}}">
            <td width="25%" nowrap="nowrap">{{ $language['stage'] }}:</td>   
            <td>
                @if($workflow_info)
                  {{$stage_name}}
                @endif
           </td>   
            </tr>
<tr>
            <td>{{ $language['assigned_by'] }}:</td> 
            <td>{{$activity_by_user_name}}</td>  
            </tr> 
            <tr>
            <td>{{ $language['activity_date'] }}:</td> 
            <td>{{$activity_date}}</td>  
            </tr> 

            
    </tbody>


        
          @php
        if($activity_to_user)
        {  
        if($activity_details)
        {
           $action_activity= ($activity_details->action_activity)?$activity_details->action_activity:0; 
           $action_activity_note= ($activity_details->action_activity_note)?$activity_details->action_activity_note:''; 
           $action_activity_name= ($activity_details->action_activity_name)?$activity_details->action_activity_name:''; 
           $action_activity_date= ($activity_details->action_activity_date)?$activity_details->action_activity_date:'';  
        }
        else
        {
          $action_activity= 0;
          $action_activity_note= '';
          $action_activity_name = '';
          $action_activity_date='';
        }
       
        $action= (in_array($activity_to_user,$wf_assigned_to) && !$action_activity)?true:false;
        $owner= (in_array($activity_to_user,$wf_assigned_to))?true:false;
        
        @endphp
      <thead>
            <tr>
                <th colspan="2">Actions @if($owner) <i class="fa fa-pencil pull-right edit_action " style="cursor:pointer;"></i> @endif</th> 
            </tr>
        </thead>

        <tbody>
        @php
        if($owner)
        {
        @endphp
           <tr class="form_edit_tr">
            <td width="25%" nowrap="nowrap">Status: <span class="compulsary">*</span></td> 
            <td>
               <div class="form-group">
               <div class="row">
                  <div class="col-sm-6">
                        <select name="action_activity_id" id="action_activity_id" class="form-control" required="" data-parsley-required-message="Status is required" data-parsley-trigger="change focusout">
                  <option value="">--Select--</option>
                  @foreach ($action_activities as $fa)  
                  <option value="{{$fa->activity_id}}" last_activity="{{$fa->last_activity}}" @if($fa->activity_id == $action_activity) selected="selected" @endif>{{$fa->activity_name}}</option>
                  @endforeach
               </select>
                     </div>
               </div>
                     
                  </div>
            </td> 
            </tr>
            <tr class="form_edit_tr">
            <td>{{ $language['note'] }}:</td>  
            <td>
               <div class="form-group">
               <div class="row">
                  <div class="col-sm-12">
                        <textarea class="form-control" autocomplete="off" id="action_activity_note" name="action_activity_note" rows="3">{{$action_activity_note}}</textarea>
                     </div>
               </div>
                     
                  </div>
            </td> 
            </tr>
            @php } @endphp

            <tr class="form_view_tr">
            <td width="25%" nowrap="nowrap">Status:</td> 
            <td>
               @php 
               
               foreach($action_activities as $fa)  
                  if($fa->activity_id == $action_activity)
                  {
                     $action_activity_name = $fa->activity_name;
                  }
                  echo ($action_activity_name)?$action_activity_name:'';
                @endphp
            </td> 
            </tr>
            <tr class="form_view_tr">
            <td>{{ $language['note'] }}:</td>  
            <td>
               @php 
               
               
                echo ($action_activity_note)?$action_activity_note:'';
                @endphp
            </td> 
            </tr>
            <tr class="form_view_tr">
            <td>Action By:</td>  
            <td>
               @php 
               
               
                echo ($activity_by_user)?$activity_by_user:'';
                @endphp
            </td> 
            </tr>
            <tr class="form_view_tr">
            <td>Date:</td>  
            <td>
               @php 
               
               
                echo ($action_activity_date)?$action_activity_date:'';
                @endphp
            </td> 
            </tr>
            
            </tbody> 
@php }
                @endphp
        
        
    </table>
</div>


</div>
</form>
@endif                  
                  
</div>
<div class="modal-footer">
<div class="row">
<div class="col-sm-12">
@if($actions=='add' || $actions=='edit')
<button type="button" class="btn btn-primary save_activity" >{{ $language['save'] }}</button>


@else
   <button type="button" class="btn btn-primary  editaccess save_action_workflows form_edit_tr"  >Save</button>                       
@endif                         
                        <button type="button" class="btn btn-danger" data-dismiss="modal">{{ $language['cancel'] }}</button>
                        
                     </div>
               </div>
 </div>              
<script type="text/javascript">
$(document).ready(function(){
$('#activity_due_date').daterangepicker({
           singleDatePicker: true,
           "drops": "down",
           format: 'YYYY-MM-DD',
           showDropdowns: true
       });
@php if($activity_to_user){ if($show_js == true){ @endphp
var edit=0;   
 @php if(!$action_activity && $owner)
 { 
@endphp
    $(".form_edit_tr").show();
    edit=1;
@php } else { @endphp
    $(".form_view_tr").show();
    edit=0;
@php }   @endphp

$(document).on("click",".edit_action",function(e) {
    if(edit == 1)
    {
       $(".form_view_tr").show(); 
       $(".form_edit_tr").hide();
       edit =0;
    }
    else if(edit == 0)
    {
       
       $(".form_edit_tr").show();
       $(".form_view_tr").hide(); 
       edit =1;
    }
   
   });
   @php }  } @endphp
});
</script>          