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
                        {{trans('workflows.workflows')}}
                    </strong> &nbsp;
                </span>
            </div>
            <div class="col-sm-4" style="font-size:12px;"></div>
            <div class="col-sm-4">
                <!-- <ol class="breadcrumb">
                    <li><a href="{{url('/home')}}"><i class="fa fa-dashboard"></i> {{trans('language.home')}}</a></li>
                    <li class="active">{{trans('workflows.workflows')}}</li>
                </ol> -->
            </div>
        </div>
    </section>
  
<section class="content">
    <input type="hidden" name="h_workflow_id" id="h_workflow_id" value="{{$workflow_id}}">
    <input type="hidden" name="h_stage_id" id="h_stage_id" value="{{$stage_id}}">
    <input type="hidden" name="h_process_id" id="h_process_id" value="{{$process_id}}">
    <div class="box box-primary">
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
                                <!-- <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" class="collapsed">Process : {{$r->wf_operation_name}}</a> -->
                                Process : {{$r->wf_operation_name}}
                            </div>
                            <div class="col-md-4">
                              <label class="pull-right"><span >Date: {{$r->started}}</span></label>
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
                            $skin_color = str_replace("skin-","",Auth::user()->user_skin);
                            ?>
                                @php 
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
                                        $style='background-color:'.$wf_details->workflow_color.';';
                                    }
                                    else if($t->state_completed == 2)
                                    {
                                        $class='progress-bar-success';
                                        $style='background-color:'.$wf_details->workflow_color.';';
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
                                        <div style="display:inline-block; width:{{$percentage}}%;"></div></a>
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
            <div class="box-group" id="accordion">
                <div class="panel box box-primary">
                  <div class="box-header with-border">
                    <h4 class="box-title">
                      <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" class="collapsed">
                        Stage : {{ucfirst(@$task_details->wf_stage_name)}}
                      </a>
                    </h4>
                  </div>
                  <div id="collapseOne" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                    <div class="box-body">
                      <table class="table table-bordered">
                        <tbody>
                        
                        @if((@$stage_details->stage_action) && (@$stage_details->type != 'last'))
                        <tr>
                          
                          <td width="20%">Applied Rule:</td>
                          
                          <td><span>@if(@$stage_details->stage_action == 1){{'By User'}}
                        @elseif(@$stage_details->stage_action == 2){{'By Heirarchy'}}
                        @elseif(@$stage_details->stage_action == 3){{'By Group '}}
                          @if(@$stage_details->stage_group ==1){{'- Any one'}}
                          @elseif(@$stage_details->stage_group ==2){{'- All'}}
                          @elseif(@$stage_details->stage_group ==3){{'- Percentage '}}
                            @if(@$stage_details->stage_percentage)
                            - <span class="badge bg-red">{{@$stage_details->stage_percentage}}%</span>
                            @endif
                          @endif
                        @elseif(@$stage_details->stage_action == 4){{' Auto'}}
                        @else{{''}}
                        @endif</span></td>
                        </tr>
                        @endif
                        @if(@$stage_details->stage_action == 1)
                        @if(@$assigned_users_name)
                        <tr>
                          
                          <td>Assigned Users:</td>
                          
                          <td><span><?php foreach(@$assigned_users_name as $name){
                            echo ucfirst(@$name->user_full_name);echo ','.'&nbsp';}?></span></td>
                        </tr>
                        @endif
                        @endif
                        @if(@$stage_details->stage_action == 2)
                        @if(@$assigned_users_name_reports_to)
                        <tr>
                          
                          <td>Assigned Users:</td>
                          
                          <td><span><?php foreach(@$assigned_users_name_reports_to as $name){
                            echo ucfirst(@$name->user_full_name);}?></span></td>
                        </tr>
                        @endif
                        @endif
                        @if(@$stage_details->stage_action == 3)
                        @if(@$assigned_departments)
                        <tr>
                          
                          <td>Assigned Departments:</td>
                          
                          <td><span><?php foreach($assigned_departments as $name){
                        echo ucfirst(@$name->department_name);echo ','.'&nbsp';}?></span></td>
                        </tr>
                        @endif
                        @endif
                        
                        <?php if((@$stage_details->stage_action == 1) || (@$stage_details->stage_action == 2) || (@$stage_details->stage_action == 3)){
                            echo "<tr>";
                      if(@$users_all)
                        {   echo "<td> Users Under The Assigned Departments:</td>";
                            echo "<td><span>";
                            foreach(@$users_all as $users)
                               { 
                                echo ucfirst(@$users->user_full_name);
                                echo ','.'&nbsp';
                               }
                            echo "</span></td>";
                        }
                        echo "</tr>";
                      if(@$transitions_det)
                      {
                      echo "<tr>";
                      foreach (@$transitions_det as $key => $value) {
                       echo "<td>";
                       echo ucfirst($value->name).' Users: ';
                       echo "</td>";
                       echo "<td><span>";
                       foreach($value->approved_users as $users)
                       {
                        echo ucfirst(@$users->user_full_name);
                        echo ','.'&nbsp';
                       }
                       echo "</span></td>";
                       echo "</tr>";
                      }

                      }
                      if(@$stage_details->stage_group ==3)
                      {
                          if(@$transitions_percentage == 1)
                          {
                            echo "<tr>";
                            foreach (@$transitions_det as $key => $value) {
                            echo "<td>";
                            echo ucfirst($value->name).' Percentage : ';
                            echo "</td>";
                            if((@$value->approved_users[0]->approval_percentage != null)||(@$value->approved_users[0]->approval_percentage != ""))
                            {
                                echo "<td>";
                                echo "<span class='badge bg-red'>".@$value->approved_users[0]->approval_percentage.'%'."</span>";
                            }
                            echo "</td>";
                            echo "</tr>";
                            }
                          }
                        }
                      }
                      ?>
                      <tr>
                          
                          <td width="20%">Current Stage:</td>
                          
                          <td>
                          @if(@$data_current_stage)
                          <span><a href="@php echo URL('view_wf_process/'.$r->process_id).'?stage='.$data_current_stage->id; @endphp">{{ucfirst(@$data_current_stage->state)}}</a></span>
                          @endif
                          </td>
                        </tr>
                        
                        @if(@$data_completed_activity)
                        <tr>
                          
                          <td width="20%">Current Status:</td>
                          
                          <td><span>{{@$data_completed_activity->activity_name}}
                          @if(@$data_completed_stage) (In Stage: <a href="@php echo URL('view_wf_process/'.$r->process_id).'?stage='.$data_completed_stage->id; @endphp">{{@$data_completed_stage->state}}</a>) @endif</span></td>
                        </tr>
                        @endif
                      </tbody></table>
                    </div>
                  </div>
                </div>
            </div>
            @if($stage_details->type != 'last' && $task_details->state_completed == 1)
           <span class="pull-right">
                <button class="btn btn-block btn-info btn-flat wf_new_activity" data-activity="0">{{ Lang::get('workflows.workflow_new_activity') }}  <i class="fa fa-plus"></i></button>
                </span>
                @endif
                @php 
                $activities = ($task_details && $task_details->activities)?$task_details->activities:array();
                @endphp 
                <table class="table table-bordered" id="activity_table">
                <thead><tr>
                <th>Activity</th>
                <th>Date</th>
                <th>Due Date</th>
                <th>Note</th>
                <th>Activity By</th>
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
                        {{ $ac->user_full_name1 }}
                    </td>
                    <td>
                        {{ $ac->user_full_name }}
                    </td>
                    <td>
                         <?php if($ac->activity_modules == 'workflows'){ ?>
                            <a href="javascript:void(0);" title="Edit Activity" data-activity="{{$ac->id}}" class="wf_new_activity"><i class="fa fa-pencil" style="cursor:pointer;"></i></a>  
                             
                       
                            <a href="javascript:void(0);" title="Delete Activity" data-activity="{{$ac->id}}"  data-name="{{ $ac->activity_name }}" class="wf_delete_activity"><i class="fa fa-close" style="color: red; cursor:pointer;"></i></a>&nbsp;
                          <?php } ?> 
                    </td>
                </tr>
                @php } @endphp 
                </tbody>
                </table>
        </div>
        <!-- /.box-body -->
        <div class="box-body">
            @php
            /*echo "<pre>";print_r($object_items);echo "</pre>";   
            

                    echo "<pre>";print_r($transitions);echo "</pre>";*/
           
            
            $activity_note_flag = 0;
                    
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
            if(in_array($user_id,$delegate['delegate_users'])) {
                $next_permission=1;
            }
            if($state_completed == 2 || $state_completed == 0)
            {
                $next_permission=0;
            }
            if($next_permission)
            {
                $activity_note_flag = 1;
            }
            @endphp            
            <button type="button" style="float: left; margin-left: 5px;" class="btn btn-primary  @if($next_permission)  transition_button @endif" data-from_state="{{$tc->from_state}}" data-to_state="{{$to_state}}" data-workflow_id="{{$tc->workflow_id}}" data-process_id="{{$process_id}}" data-activity_id="{{$tc->activity_id}}" data-STATUS="{{$tc->status}}" data-stage_action="{{$stage_action}}" data-transition_id="{{$tc->id}}" @if(!$next_permission) disabled="disabled" @endif>{{ $tc->name}}</button> 
            @php }  @endphp 

             @if($activity_note_flag==1 && (@$stage_details->stage_action==1 || @$stage_details->stage_action==2))
             <button type="button" style="float: left; margin-left: 5px;" class="btn btn-primary delegate"
             data-wf_operation_id="{{$delegate['wf_operation_id']}}" data-wf_stage="{{$delegate['wf_stage']}}"
             >Delegate</button> 
             @endif
            <div class="preloader" style="float: left; margin-top: 5px; display: none;">
                <i class="fa fa-spinner fa-pulse fa-fw fa-lg"></i>
                <span class="sr-only">Loading...</span>
            </div>
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
        @if($activity_note_flag)
        <div class="box-footer">
            <div class="form-group">
              <label>
                <input type="checkbox" id="add_activity_note_checkbox" name="add_activity_note_checkbox"> Add Activity Note
              </label>
              <textarea class="form-control" rows="2" placeholder="Add Activity Note" id="act_activity_note_new" name="act_activity_note_new" style="display: none; height: 60px !important;"></textarea>
            </div>
        </div>
       @endif
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
    var new_note = $('#act_activity_note_new').val();
   var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
   var url = "@php echo url('transition_click'); @endphp";
   var workflow_id = $(this).attr('data-workflow_id');
   var process_id = $(this).attr('data-process_id');
   var from_state = $(this).attr('data-from_state');
   var to_state = $(this).attr('data-to_state');
   var transition_id=$(this).attr('data-transition_id');
   var stage_action = $(this).attr('data-stage_action');
   var activity_id = $(this).attr('data-activity_id');
   var postdata = {_token:CSRF_TOKEN,"workflow_id":workflow_id,"from_state":from_state,"process_id":process_id,"to_state":to_state,"stage_action":stage_action,"transition_id":transition_id,"activity_id":activity_id,"note":new_note}
   $.ajax({
          type: "POST",
          url: url,
          dataType:'json',
          data: postdata, /*serializes the form's elements.*/
          success: function(response)
          {
            $(".preloader").css("display", "none");
                $(".transition_click").attr("disabled", false);
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


 });
   
</script>    
@endsection