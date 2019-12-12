<?php include (public_path()."/storage/includes/lang1.en.php" ); ?>
@extends('layouts.app')
@section('main_content')
{!! Html::script('js/parsley.min.js') !!}    
{!! Html::script('js/jquery.form.js') !!}   
{!! Html::style('css/font-awesome.min.css') !!} 
{!! Html::style('css/ionicons.min.css') !!} 
{!! Html::style('plugins/datatables/dataTables.bootstrap.css') !!}

<style>
    .tooltip-item {
        position: relative;
        cursor: pointer;
    }

    .tooltip-item:after {
        content: attr(data-message);
        position: absolute;
        left: 105%;
        white-space: nowrap;
        display: none;
        font-size: 1.2rem;
        background-color: #232323;
        padding-left: 10px;
        padding-right: 10px;
        padding-top: 5px;
        padding-bottom: 5px;
        border-radius: 3px;
        color: #ffffff;
        font-family: Tahoma, Verdana, Segoe, sans-serif;
        font-weight: normal;
    }
            
    .connectedSortable_doc
    {
        min-height:75px;
        min-width:300px;
        /*max-width:400px;*/
        border-bottom: 2px solid #f4f4f4;
    }
    .connectedSortable_activity
    {
        /*min-height:75px;*/
        min-width:300px;
    }   

    .connectedSortable_doc > li
    {
        border-bottom: 2px solid;
        margin-bottom: 3px !important;
    }

    .connectedSortable_doc > li:last-child
    {
        border-bottom: none;
        margin-bottom: 0px !important;
    }   
            
    .li_activity {
    /*width:300px !important;*/
    }
    .li_activity .tools {
        display: none;
        color: #97a0b3;
    }

    .nopadding_class
    {
        padding: 0 !important;
        margin: 0 !important;
    }

    .tool_activity a{ color: #97a0b3 !important; }
    </style>
    <?php 
        $user_id = Auth::user()->id;
        $dept_id = Auth::user()->department_id;
        $user_role = Auth::user()->user_role;
        $wf_permission = Auth::user()->user_workflow_permission;
    ?>
    <section class="content-header">
        <div class="row">
            <div class="col-sm-4">
                <span style="float:left;">
                    <strong>
                        {{$language['workflows']}}
                    </strong> &nbsp;
                </span>
            </div>
            <div class="col-sm-4" style="font-size:12px;"></div>
            <div class="col-sm-4">
                <ol class="breadcrumb">
                    <li><a href="{{url('/home')}}"><i class="fa fa-dashboard"></i> {{$language['home']}}</a></li>
                    <li class="active">{{$language['workflows']}}</li>
                </ol>
            </div>
        </div>
    </section>
  
<section class="content">
    <input type="hidden" name="h_workflow_id" id="h_workflow_id" value="{{$workflow_id}}">
    <input type="hidden" name="h_stage_id" id="h_stage_id" value="{{$stage_id}}">
    <input type="hidden" name="h_process_id" id="h_process_id" value="{{$process_id}}">
    <div class="box">
        <div class="box-header with-border">
            <label >Workflow: @php echo ($wf_details)?$wf_details->workflow_name:'All'; @endphp</label>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="box-group" id="accordion">
                @php foreach($workflow_process as $r){ @endphp 
                    <div class="box-header" style="border:1px solid;margin: 3px 0px 3px 0px; ">
                        <div class="row">
                            <div class="col-md-8">    
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" class="collapsed">Process : {{$r->wf_operation_name}}</a>
                            </div>
                            <div class="col-md-4">
                              <label class="pull-right"><span >Date: {{$r->started}}</span></label>
                            </div>
                            <div class="col-md-12">    
                               
                                <label><i class="{{$r->obj_icon}}" title="{{$r->obj_label}}"></i> {{$r->obj_name}}</label>
                            </div>
                            <div class="col-md-8">
                                @php 
                                    $tasks = ($r->tasks)?$r->tasks:array();
                                    $task_count = sizeof($tasks);
                                    $percentage = ($task_count)?(95/$task_count):0;
                                @endphp    
                                @php $c=0; foreach($tasks as $t){ 
                                    $c++;
                                    $class='progress-bar-warning';
                                    $style='background-color: #996600;';
                                    if($t->state_completed == 1)
                                    {
                                        $class='progress-bar-primary';
                                        $style='background-color:'.$wf_details->workflow_color.';';
                                    }
                                    else if($t->state_completed == 2)
                                    {
                                        $class='progress-bar-success';
                                        $style='background-color:'.$wf_details->workflow_color.';';
                                    }
                                    @endphp               
                                    <a href="@php echo URL('view_wf_process/'.$r->process_id).'?stage='.$t->wf_stage; @endphp" data-message="@php echo $t->wf_stage_name; @endphp" class="tooltip-item" style="{{$style}} border-radius:4px; height:10px;">
                                        <div style="display:inline-block; width:{{$percentage}}%; @if($c != 1) @endif @if($c != $task_count) border-right:1px solid white; @endif"></div></a>
                                        <input type="hidden" name="process_id" id="process_id" value="{{$r->process_id}}">
                                @php } @endphp 
                            </div>
                            <div class="col-md-2">{{$task_count}} stages </div>
                        </div> <!--row -->
                    </div>
                @php } 
                    $disabled=true;
                    $state_completed=$task_details->state_completed;
                    $stage_action =$task_details->stage_action;
                    if($state_completed == 1)
                    {
                        $disabled=false;
                    }
                @endphp  
            </div>
            <label><strong>Stage : {{$task_details->wf_stage_name}} </strong></label>
            <?php if($user_role==1 || $user_role==2){ ?>
                <span class="pull-right">
                <button class="btn btn-block btn-info btn-flat wf_new_activity" data-activity="0">{{ Lang::get('language.workflow_new_activity') }}  <i class="fa fa-plus"></i></button>
                </span>  
                <?php }else{
                    if(count($wf_privileges_add)>0){
                        foreach ($wf_privileges_add as $val) {                    
                            $wfprevkey_tmp = $val->privilege_key;
                            $wfprevkey = explode(',',$wfprevkey_tmp);
                            if(in_array('add', $wfprevkey) && $val->privilege_status==1){ 
                                $uservalue = $val->privilege_value_user; 
                                $deptvalue = $val->privilege_value_department;
                                $key_user_value = explode(',',$uservalue);
                                $key_dept_value = explode(',',$deptvalue);                                            
                                if(in_array($user_id, $key_user_value) || in_array($dept_id, $key_dept_value)) { ?>
                                    <span class="pull-right">
                                    <button class="btn btn-block btn-info btn-flat wf_new_activity" data-activity="0">{{ Lang::get('language.workflow_new_activity') }}  <i class="fa fa-plus"></i></button>
                                    </span>
                                <?php }
                            }
                        }
                    }
                } ?>
                @php 
                $activities = ($task_details && $task_details->activities)?$task_details->activities:array();
                @endphp 
                <table class="table table-bordered" id="activity_table">
                <thead><tr>
                <th>Activity</th>
                <th>Date</th>
                <th>Due Date</th>
                <th>Note</th>
                <th>Assigned To</th>
                <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @php foreach($activities as $ac){ @endphp 
                <tr>
                    <td>{{ $ac->activity_name }}</td>
                    <td>{{ $ac->created_at }}</td>
                    <td>{{ $ac->due_date }}</td>
                    <td>{{ $ac->activity_note }}</td>
                    <td>
                        @php 
                        $status='';
                        if($ac->activity_completed == '0')
                        { 
                            $status='Inactive';
                        }
                        if($ac->activity_completed == '1')
                        { 
                            $status='On-Going';
                        }
                        if($ac->activity_completed == '2')
                        { 
                            $status='Completed';
                        }  
                        @endphp
                        {{ $ac->user_full_name }}
                    </td>
                    <td>
                        <?php if($user_role==1 || $user_role==2){ ?>
                            <a href="javascript:void(0);" title="Edit Activity" data-activity="{{$ac->id}}" class="wf_new_activity"><i class="fa fa-pencil" style="cursor:pointer;"></i></a>  
                            <?php }else{
                            if(count($wf_privileges_edit)>0){
                                foreach ($wf_privileges_edit as $val) { 
                                    $wfprevkey_tmp = $val->privilege_key;
                                    $wfprevkey = explode(',',$wfprevkey_tmp);
                                    if(in_array('edit', $wfprevkey) && $val->privilege_status==1){ 
                                        $uservalue = $val->privilege_value_user; 
                                        $deptvalue = $val->privilege_value_department;
                                        $key_user_value = explode(',',$uservalue);
                                        $key_dept_value = explode(',',$deptvalue);                                            
                                        if(in_array($user_id, $key_user_value) || in_array($dept_id, $key_dept_value)) { ?>
                                            <a href="javascript:void(0);" title="Edit Activity" data-activity="{{$ac->id}}" class="wf_new_activity"><i class="fa fa-pencil" style="cursor:pointer;"></i></a>&nbsp;
                                        <?php }
                                    }
                                }
                            }
                        } ?>    
                        <?php if($user_role==1 || $user_role==2){ ?>
                            <a href="javascript:void(0);" title="Delete Activity" data-activity="{{$ac->id}}" class="wf_delete_activity"><i class="fa fa-close" style="color: red; cursor:pointer;"></i></a>&nbsp;
                            <?php }else{
                            if(count($wf_privileges_delete)>0){
                                foreach ($wf_privileges_delete as $val) {                    
                                    $wfprevkey_tmp = $val->privilege_key;
                                    $wfprevkey = explode(',',$wfprevkey_tmp);
                                    if(in_array('delete', $wfprevkey) && $val->privilege_status==1){ 
                                        $uservalue = $val->privilege_value_user; 
                                        $deptvalue = $val->privilege_value_department;
                                        $key_user_value = explode(',',$uservalue);
                                        $key_dept_value = explode(',',$deptvalue);                                            
                                        if(in_array($user_id, $key_user_value) || in_array($dept_id, $key_dept_value)) { ?>
                                            <a href="javascript:void(0);" title="Delete Activity" data-activity="{{$ac->id}}" class="wf_delete_activity"><i class="fa fa-close" style="color: red; cursor:pointer;"></i></a>&nbsp;
                                        <?php }
                                    }
                                }
                            }
                        } ?> 
                    </td>
                </tr>
                @php } @endphp 
                </tbody>
                </table>
        </div>
        <!-- /.box-body -->
        <div class="box-body">
            @php 
            foreach($transitions as $tc){
            $next_permission=0;
            $to_state = 0;
            if($tc->status == 1)
            {
                if($tc->if_stage){
                    $to_state = $tc->if_stage;
                }else{
                    $to_state = $tc->else_stage;
                }
            }
            else
            {
                if($tc->with_rule)
                {
                    $to_state = $tc->else_stage;
                }
            }  
            if($to_state && $wf_action_permission)
            {
                $next_permission=1;
            }
            if($state_completed == 2 || $state_completed == 0)
            {
                $next_permission=0;
            }
            @endphp            
            <button type="button" class="btn btn-primary  @if($next_permission)  transition_click @endif" data-from_state="{{$tc->from_state}}" data-to_state="{{$to_state}}" data-workflow_id="{{$tc->workflow_id}}" data-process_id="{{$process_id}}" data-stage_action="{{$stage_action}}" data-transition_id="{{$tc->id}}" @if(!$next_permission) disabled="disabled" @endif>{{ $tc->name}}</button> 
            @php }  @endphp 

            @if($disabled == true) 
            <br />
            @php
                if($state_completed == 2)
                {
                    $texts = "These buttons are disabled because these stages are alredy completed.";
                }
                else
                {
                    $texts = "These buttons are disabled because the previous stages are not completed yet.";
                }
                if(!$transitions)
                {
                    $texts = "";
                }
            @endphp  
            <p class="help-text">{{$texts}}</p>  
            @endif                
        </div>
    </div>
    <div class="modal fade" id="add_to_wf_activity_model">
    <div class="modal-dialog">
    <div class="modal-content workflow_new_activity">
    <div class="modal-footer"></div>
    </div>
    <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
    <div class="modal-container-child"></div> 
    <div class="modal-container"></div>
</section>
<!-- User edit form end -->
<!-- {!! Html::script('plugins/jQueryUI/jquery-ui.min.js') !!} -->
{!! Html::script('js/jquery-ui.min.js') !!}
{!! Html::script('plugins/select2/select2.full.min.js') !!}

<script type="text/javascript">
  (function(selector, horizontalOffset, verticalOffset) {
  var items;
  
  selector = selector || '.tooltip';
  horizontalOffset = horizontalOffset || 5;
  verticalOffset = verticalOffset || 5;
  
  items = document.querySelectorAll(selector);
  items = Array.prototype.slice.call(items);
  
  items.forEach(function(item) {
    // Every time the pointer moves over the element the 
    //  CSS-rule in overwritten with new values for 
    //  top and left.
    item.addEventListener('mousemove', function(e) {
      let countCssRules = document.styleSheets[0].cssRules.length;
      let newRule = selector +
        ':hover:after { display: block; ' + 
                       'left: ' + '0px; ' +
                       'top: ' +  (e.offsetY + verticalOffset) + 'px; }';
      
      document.styleSheets[0].insertRule(newRule, countCssRules);
    });
  });
})('.tooltip-item', 0, -40);

   $(function ($) {

    $(document).on("click",".transition_click",function() {

   var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
   var url = "@php echo url('transition_click'); @endphp";
   var workflow_id = $(this).attr('data-workflow_id');
   var process_id = $(this).attr('data-process_id');
   var from_state = $(this).attr('data-from_state');
   var to_state = $(this).attr('data-to_state');
   var transition_id=$(this).attr('data-transition_id');
   var stage_action = $(this).attr('data-stage_action');
   var postdata = {_token:CSRF_TOKEN,"workflow_id":workflow_id,"from_state":from_state,"process_id":process_id,"to_state":to_state,"stage_action":stage_action,"transition_id":transition_id}
   $.ajax({
          type: "POST",
          url: url,
          dataType:'json',
          data: postdata, /*serializes the form's elements.*/
          success: function(response)
          {
               /*show response from the php script.*/
   //alert_space").html(response.message); 
              if(response.status == 1)
              {
               //show_work_flow(response);

               $('#add_to_workflow_modal_new').modal('hide');
               if(response.url != '')
               {
                window.location.href= response.url;
               }
              }
              else if(response.status == 0)
              {
                $('.alert_modal').html(response.message);
              }
              
          }
        });
    });
    $(document).on("click",".wf_new_activity",function() {
  
    console.log("log");
   
   var src = "{{ URL::asset('images/loading/loading.gif') }}";
   var loading ='<div  style="text-align: center;"><img src="'+src+'"></div>';
   $('.add_wf_activity_remote').html(loading);
   $('#add_to_wf_activity_model').modal({
                     show: 'show',
                     backdrop: false
               }); 
   var workflow_id=$("#h_workflow_id").val();
   var process_id=$("#h_process_id").val(); 
   var stage_id=$("#h_stage_id").val();
   var activity_id=$(this).attr('data-activity');
   console.log("before"); 
   $('.workflow_new_activity').load("@php echo url('workflow_new_activity') @endphp?workflow_id="+workflow_id+"&process_id="+process_id+"&stage_id="+stage_id+"&activity_id="+activity_id,function(result){
    console.log("after");
         /*$('#loading_model').modal('hide');*/
         /*$('#add_to_wf_activity_model').modal({
                     show: 'show',
                     backdrop: false
               }); */
         
         });
   });
   
   $(document).on("click",".wf_delete_activity",function() {
  
   
   var workflow_id=$("#h_workflow_id").val();
   var process_id=$("#h_process_id").val(); 
   var stage_id=$("#h_stage_id").val();
   var activity_id=$(this).attr('data-activity');
   var transition_id=$(this).attr('data-transition_id');
   var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
   var url = "@php echo url('workflow_delete_activity'); @endphp";
   var postdata = {_token:CSRF_TOKEN,"workflow_id":workflow_id,"activity_id":activity_id,"process_id":process_id,"stage_id":stage_id,"transition_id":transition_id}
   $.ajax({
          type: "POST",
          url: url,
          dataType:'json',
          data: postdata, /*serializes the form's elements.*/
          success: function(response)
          {
               
              if(response.status == 1)
              {

               if(response.url != '')
               {
                window.location.href= response.url;
               }
              }
              
              
          }
        });
   });


 });
   
</script>    
@endsection