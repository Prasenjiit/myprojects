<?php include (public_path()."/storage/includes/lang1.en.php" ); ?>
@extends('layouts.app')
@section('main_content')
{!! Html::script('js/parsley.min.js') !!}    
{!! Html::script('js/jquery.form.js') !!}   
{!! Html::style('css/font-awesome.min.css') !!} 
{!! Html::style('css/ionicons.min.css') !!} 
{!! Html::style('plugins/datatables/dataTables.bootstrap.css') !!}
{!! Html::style('css/dropzone.min.css') !!}
{!! Html::script('js/dropzone.js') !!}  
<style>
    .tooltip-item {
        position: relative;
        cursor: pointer;
    }
    #test { font-size:0; }
    #test a { font-size:16px;}
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
                        {{trans('language.workflows')}}ssss
                    </strong> &nbsp;
                </span>
            </div>
            <div class="col-sm-8" style="font-size:12px;"></div>
           
        </div>
    </section>
  
<section class="content">
<form id="sjfb-sample" method="POST" action="{{url('transition_click')}}">
    {!! csrf_field() !!}
    <input type="hidden" name="h_workflow_id" id="h_workflow_id" value="{{$workflow_id}}">
    <input type="hidden" name="h_stage_id" id="h_stage_id" value="{{$stage_id}}">
    <input type="hidden" name="h_process_id" id="h_process_id" value="{{$process_id}}">
    
    <div class="box box-primary">
        <div class="box-header with-border">
            <label >Workflow: @php echo $process_details->workflow_name; @endphp</label>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="box-group" id="accordion">                 
                @php  foreach($workflow_process as $r){ @endphp 
                    <div class="box-header" style="border:1px solid;margin: 3px 0px 3px 0px; ">
                        <div class="row">
                            <div class="col-md-8">    
                                <!-- <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" class="collapsed">Process : {{$r->wf_operation_name}}</a> -->
                                Process : {{$r->wf_operation_name}}
                            </div>
                            <div class="col-md-4">
                              <label class="pull-right"><span >Last Updated: {{ dtFormat(@$r->last_updated_at) }}</span></label>
                            </div>
                            <div class="col-md-12">  
                                <?php if($r->wf_object_type=="document"){ ?>
                                    <a href="{{ url('documentManagementView') }}?dcno={{$r->wf_object_id}}&page=document" title="Open Document" target="_blank" style="cursor:pointer;">
                                        <i class="{{$r->obj_icon}}" title="{{$r->obj_label}}"></i> {{$r->obj_name}}
                                    </a>
                                <?php }else if($r->wf_object_type=="form"){ ?>
                                    <a href="{{ url('form_details') }}/{{$r->wf_object_type_id}}?response={{$r->wf_object_id}}" title="Open Form" target="_blank" style="cursor:pointer;">
                                        <i class="{{$r->obj_icon}}" title="{{$r->obj_label}}"></i> {{$r->obj_name}}
                                    </a> 
                                <?php } ?>  
                                <!-- <label><i class="{{$r->obj_icon}}" title="{{$r->obj_label}}"></i> {{$r->obj_name}}</label> -->
                            </div>
                            <div class="col-md-8" id="test">
                            <?php
                            
                            ?>
                                @php 

    $wf_object_type = $process_details->wf_object_type;                           
    $disabled=true;
    $state_completed=$task_details->state_completed;
    $stage_action =$task_details->stage_action;
    if($state_completed == 1)
    {
            $disabled=false;
    }
    $activity_note_flag = 0;
    if($state_completed ==1)
    {
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
           
            
            if($next_permission)
            {
                $activity_note_flag = 1;
            }

        }
    }
        $skin_color = str_replace("skin-","",Auth::user()->user_skin);
                                    $tasks = ($r->tasks)?$r->tasks:array();
                                    $task_count = sizeof($tasks);
                                    $percentage = ($task_count)?(95/$task_count):0;
                                @endphp    
                                @php $c=0; foreach($tasks as $t){ 
                                    $c++;
                                    $class='progress-bar-warning';
                                    $style='background-color: #ffffff;';
                                    if($t->state_completed == 1)
                                    {
                                        $class='progress-bar-primary';
                                        $style='background-color:'.$process_details->workflow_color.';';
                                    }
                                    else if($t->state_completed == 2)
                                    {
                                        $class='progress-bar-success';
                                        $style='background-color:'.$process_details->workflow_color.';';
                                    }
                                    if($stage_id == $t->wf_stage)
                                    {
                                        $style .= 'border:1px solid red;';
                                    }
                                    else
                                    {
                                        $style .= 'border-right: 1px solid #A9A9A9;border-top: 1px solid #A9A9A9;border-bottom: 1px solid #A9A9A9;';
                                        if($c == 1)
                                        {
                                            $style .= 'border-left: 1px solid #A9A9A9;';
                                        }
                                    }
                                    @endphp               
                                    <a href="@php echo URL('view_wf_process/'.$r->process_id).'?stage='.$t->wf_stage; @endphp" data-message="@php echo $t->wf_stage_name; @endphp" class="tooltip-item" style="{{$style}} height:10px;">
                                        <div style="text-align:center;display:inline-block; width:{{$percentage}}%;">@if($t->state_completed == 3)<i class="fa fa-fw fa-ban"></i>@endif</div></a>
                                        <input type="hidden" name="process_id" id="process_id" value="{{$r->process_id}}">
                                @php } @endphp 
                            </div>
                            <div class="col-md-2">{{$task_count}} stages </div>
                        </div> <!--row -->
                    </div>
                @php } 
                    
                @endphp  
            </div>
        <div class="box-group" id="accordion">
            <!-- stage details -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="box-tools pull-left">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                    <h3 class="box-title">Stage : {{ucfirst(@$task_details->wf_stage_name)}}</h3>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table class="table table-bordered">
                        <tbody>
                            
                           @if($task_details->stage_action && $task_details->type != 'last')

                            <tr>
                              
                              <td width="20%">Applied Rule:</td>
                              
                              <td>{{$applied_rule_text}}</td>
                            </tr>
                            

                            @if($task_details->stage_action == 1)
                            <!-- By USer -->
                            <tr>
                              
                              <td>Assigned User:</td>
                              
                              <td>{!! implode(', ',$users_name) !!}</td>
                            </tr>
                            @endif


                            @if($task_details->stage_action == 2)
                            <!-- Hirachy -->
                            <tr>
                              
                              <td>Assigned User:</td>
                              
                              <td>{!! implode(', ',$users_name) !!}</td>
                            </tr>
                            @endif

                            @if($task_details->stage_action == 3)
                            <!-- Group -->
                            <tr>
                              
                              <td>Assigned Departments:</td>
                              
                              <td>{{$assigned_departments}}</td>
                            </tr>

                             <tr>
                              
                              <td>Assigned User:</td>
                              
                              <td>{!! implode(', ',$users_name) !!}</td>
                            </tr>   
                            @if($task_details->stage_group == 3)
                            <tr>
                              
                              <td>Action percentage:</td>
                              
                              <td>
                                  @php 
                                  $percentege_text='';

                                  foreach($transitions as $tc)
                                  {
                                    $activity_count = (isset($activity_id_count[$tc->activity_id]))?$activity_id_count[$tc->activity_id]:0;
                                    $avg = 0;
                                    if(($total_users && $activity_count) && ($state_completed ==1 || $state_completed ==2))
                                    {
                                        $avg = round(($activity_count/$total_users)*100);

                                    }
                                    $percentege_text .=$tc->name.'- '.$avg.'%, ';
                                  }
                                   echo trim($percentege_text,', ');

                                  @endphp
                              </td>
                            </tr>
                            @endif


                            @endif

                            @endif

                            <tr>
                              
                              <td width="20%">Current Stage:</td>
                              
                              <td>
                              @if($data_current_stage)
                              <span><a href="@php echo URL('view_wf_process/'.$process_id).'?stage='.$data_current_stage->id; @endphp">{{ucfirst($data_current_stage->state)}}</a></span>
                              @endif
                              </td>
                            </tr>
                            
                            @if($data_completed_activity)
                            <tr>
                              
                              <td>Current Status:</td>
                              
                              <td><span>{{$data_completed_activity->activity_name}}
                              @if($data_completed_stage) (In Stage: <a href="@php echo URL('view_wf_process/'.$process_id).'?stage='.$data_completed_stage->id; @endphp">{{$data_completed_stage->state}}</a>) @endif</span></td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>

        @if($wf_object_type == 'form')
          <!-- form area -->
          <div class="box box-primary collapsed-box">
            <div class="box-header with-border">
                <div class="box-tools pull-left">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                    </button>
                </div>
                <h3 class="box-title">Form Details</h3>
            <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <!-- Edit Form START-->
                <div class="modal-body form_values_edit" id="">
                <div id="sjfb-wrap"> 
                    <input type="hidden" name="form_response_unique_id" id="form_response_unique_id" value="{{ $process_details->wf_object_id }}">
                    <input type="hidden" name="form_id" id="form_id" value="{{$process_details->wf_object_type_id}}">
                    <input type="hidden" name="form_name" id="form_name" value="{{@$form_name}}">
                    <input type="hidden" name="form_reject_status" id="form_reject_status" value="{{@$status}}">
                    <div id="sjfb-fields">
                    </div>
                    <div id='div-append'>
                    </div>
                </div>
                </div>
                <!-- Edit Section -->
            </div>
            <!-- /.box-body -->
          </div>
        @endif
        </div>

    
    
           
         @if($task_details->type != 'last' && $state_completed == 1)
           <span class="pull-right" style="padding-left: 6px;">

                <button type="button" class="btn btn-block btn-info btn-flat wf_new_activity" data-activity="0">{{ Lang::get('workflows.workflow_new_activity') }}  <i class="fa fa-plus"></i></button>
                </span>&nbsp;&nbsp;&nbsp;

                @endif

                @if($activity_note_flag==1 && in_array($user_id,$assgnUser))
         <span class="pull-right">
             <button type="button" style="float: left; margin-left: 5px;" class="btn btn-primary delegate"
             data-wf_operation_id="{{$process_id}}" data-wf_stage="{{$stage_id}}"
             >Delegate <i class="fa fa-share"></i></button> </span>&nbsp;&nbsp;&nbsp;
             @endif
                @php 
                if($state_completed !== 3)
                {
                @endphp 

                <table class="table table-bordered" id="activity_table">
                <thead><tr>
                <th>Task</th>
                <th>Due Date</th>
                <th>Assigned To</th>
                <th>Note</th>
                <th>Stage</th>
                <th>Assigned By</th>
                <th>Assigned Date</th>
                </tr>
                </thead>
                <tbody>
                @php foreach($activities as $ac){ @endphp 
                <tr>
                <td>{{ ucfirst($ac->activity_name) }}</td>
                <td>{{ custom_date_Format($ac->due_date) }}</td>
                <td>{{ ucfirst($ac->user_full_name) }}</td>
                <td>{{ $ac->activity_note }}</td>
                <td>{{ ucfirst($ac->stage_name) }}</td>
                <td>{{ ucfirst($ac->user_full_name1) }}</td>
                <td>{{ dtFormat($ac->created_at) }}</td>
                </tr>
                @php } @endphp 
                </tbody>
                </table>

                @php } @endphp <!-- end state_completed==3-->
        </div>
        <!-- /.box-body -->
        <div class="box-body">

             @if($activity_note_flag)
            <div class="form-group">
              <label>
                <input type="checkbox" id="add_activity_note_checkbox" name="add_activity_note_checkbox"> Add Task Note
              </label>
              <textarea class="form-control" rows="2" placeholder="Add Task Note" id="act_activity_note_new" name="act_activity_note_new" style="display: none; height: 60px !important;"></textarea>
            </div>
       @endif
       <!-- start check1 -->
            @php
            /*echo "<pre>";print_r($object_items);echo "</pre>";*/  
            

                   /* echo "<pre>";print_r($transitions);echo "</pre>";*/
           
            
           
            if(($state_completed == 1 || $state_completed == 2 || $state_completed == 0) && ($state_type == 'middle')) 
            {       
            foreach($transitions as $tc){
            $next_permission=0;
            $to_state = 0;
            if($state_completed ==1)
            {
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

            }

             if($tc->if_stage){
                    $to_state = $tc->if_stage;
                }else{
                    $to_state = $tc->else_stage;
                }
           
           
            @endphp            
            <button type="button" style="float: left; margin-left: 5px;" class="btn btn-primary  @if($next_permission)  transition_button @endif" data-from_state="{{$tc->from_state}}" data-to_state="{{$to_state}}" data-workflow_id="{{$tc->workflow_id}}" data-process_id="{{$process_id}}" data-activity_id="{{$tc->activity_id}}" data-STATUS="{{$tc->status}}" data-stage_action="{{$stage_action}}" data-transition_id="{{$tc->id}}" data-wf_object_type="{{$wf_object_type}}" data-form_response_unique_id="{{@$process_details->wf_object_id}}" data-form_id="{{@$process_details->wf_object_type_id}}"  @if(!$next_permission) disabled="disabled" @endif>{{ $tc->name}}</button> 
            @php }  @endphp 
            <input type="hidden" name="workflow_id" id="workflow_id" value="{{$tc->workflow_id}}">
            <input type="hidden" name="process_id" id="process_id" value="{{$process_id}}">
            <input type="hidden" name="stage_action" id="stage_action" value="{{$stage_action}}">

            <input type="hidden" name="wf_object_type" id="wf_object_type" value="{{$wf_object_type}}">

            <input type="hidden" name="activity_id" id="activity_id" value="">
            <input type="hidden" name="from_state" id="from_state" value="">
            <input type="hidden" name="to_state" id="to_state" value="">
            <input type="hidden" name="transition_id" id="transition_id" value="">
            
            
            <div class="preloader" style="float: left; margin-top: 5px; display: none;">
                <i class="fa fa-spinner fa-pulse fa-fw fa-lg"></i>
                <span class="sr-only">Loading...</span>
            </div>
            @if($disabled == true) 
            <br />
            @php
                if($state_completed == 2 || $state_completed == 3)
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
            <br>    
            <p class="help-text" style="font-size:12px; color:#999;">{{$texts}}</p>  
            @endif 

            @php }   @endphp <!-- end check1 -->
        </div>
       
    </div>
    </form>    
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
    <div class="modal fade" id="delegate_user_modal">
        <div class="modal-dialog">
            <div class="modal-content delegate_user_remote">
                <div class="modal-footer"></div>
            </div>
            <!-- /.modal-content -->
        </div>
    <!-- /.modal-dialog -->
    </div>
</section>

<!-- User edit form end -->
<!-- {!! Html::script('plugins/jQueryUI/jquery-ui.min.js') !!} -->
{!! Html::script('js/jquery-ui.min.js') !!}
{!! Html::script('plugins/select2/select2.full.min.js') !!}
{!! Html::script('plugins/simple-jquery-form-builder/js/sjfb-html-generator-edit.js') !!}
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
    //note add text area
    $(document).on("click",'#add_activity_note_checkbox',function(){
        $('#act_activity_note_new').toggle();
    });
    $(document).on("click",".transition_click",function() {
    $(".preloader").css("display", "block");
    var workflow_id = $(this).attr('data-workflow_id');
   var process_id = $(this).attr('data-process_id');
   var from_state = $(this).attr('data-from_state');
   var to_state = $(this).attr('data-to_state');
   var transition_id=$(this).attr('data-transition_id');
   var stage_action = $(this).attr('data-stage_action');
   var activity_id = $(this).attr('data-activity_id');
   //set attributes for the button transition_click
   $(".transition_button").attr({'data-workflow_id':workflow_id,
                                'data-process_id':process_id,
                                'data-from_state':from_state,
                                'data-to_state':to_state,
                                'data-transition_id':transition_id,
                                'data-stage_action':stage_action,
                                'data-activity_id':activity_id
    });
});

     /*Delegate*/

    $(".delegate").click(function() {
        var loading_src = "{{ URL::asset('images/loading/loading.gif') }}";
        var loading_text ='<div  style="text-align: center;"><img src="'+loading_src+'"></div>';
        $('.delegate_user_remote').html(loading_text);
        $("#delegate_user_modal").modal({
            show: 'show',
            backdrop: false
            });
        var wf_operation_id = $(this).data('wf_operation_id');
        var wf_stage        = $(this).data('wf_stage');
        $.ajax({
            type: 'get',
            url: "{{URL('delegateUser')}}",
            dataType: 'json',
            data: {wf_operation_id:wf_operation_id,wf_stage:wf_stage},
            timeout: 50000,
            success: function(data)
            {
                $('.delegate_user_remote').html(data.html);
            }
        });
    });

    $(document).on("click",".transition_button",function() {
    $(".preloader").css("display", "block");
    $(".transition_click").attr("disabled", true);

   var activity_id = $(this).attr('data-activity_id');
   $("#activity_id").val(activity_id);

   var from_state = $(this).attr('data-from_state');
   $("#from_state").val(from_state);


   var to_state = $(this).attr('data-to_state');
   $("#to_state").val(to_state);

   var transition_id = $(this).attr('data-transition_id');
   $("#transition_id").val(transition_id);


   
   $("#sjfb-sample").submit();
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
   var activity_name=$(this).attr('data-name');
   var transition_id=$(this).attr('data-transition_id');
   var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
   var url = "@php echo url('workflow_delete_activity'); @endphp";
   var postdata = {_token:CSRF_TOKEN,"workflow_id":workflow_id,"activity_id":activity_id,"process_id":process_id,"stage_id":stage_id,"transition_id":transition_id}
   swal({
            title: "Do you want to delete '"+activity_name+"'?",
            text: "{{trans('language.Swal_not_revert')}}",
            type: "{{trans('language.Swal_warning')}}",
            showCancelButton: true
        }).then(function (result) {
        if(result)
        {
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
        }
        swal(
        "{{trans('language.Swal_deleted')}}"
        )
        });
   });


        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var formID = $("#form_id").val();
        var loadformurl = "{{ url('load_form')}}";
        var action = 'edit';
        var form_unique_id = $("#form_response_unique_id").val();
        var url_delete = "<?php echo URL('deleteAttached');?>";
        var url_form_attach = "<?php echo URL('formAttachments');?>";
        generateFormEdit(formID,loadformurl,action,form_unique_id,url_delete,url_form_attach);

 });
   
</script>    
@endsection