<?php include (public_path()."/storage/includes/lang1.en.php" ); ?>
@extends('layouts.app')
@section('main_content')
{!! Html::script('js/parsley.min.js') !!}    
{!! Html::script('js/jquery.form.js') !!}   
{!! Html::style('css/font-awesome.min.css') !!} 
{!! Html::style('css/ionicons.min.css') !!} 
{!! Html::style('plugins/datatables/dataTables.bootstrap.css') !!}

<style>
            
            
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
           
            .tool_activity a{    color: #97a0b3 !important; }
         
         </style>
<section class="content-header">
   <div class="row">
      <div class="col-sm-4">
         <span style="float:left;">
         <strong>
         {{$language['workflows']}}
         </strong> &nbsp;
         </span>
         <span style="float:left;">
         <a href="javascript:(0);"  class="add_to_workflows">
          <button class="btn btn-block btn-info btn-flat">{{$language['add_new_obj']}}  <i class="fa fa-plus"></i></button>
      </a>
    </span>
         
      </div>
      <div class="col-sm-4" style="font-size:12px;">    
        {{ $language['color_legend'] }} &nbsp;
        <button type="button" style="margin-left: 10px; cursor: context-menu; padding: 1px 12px !important; " class="btn btn-danger"></button>&nbsp;{{$language['no_action_taken']}}
      </div>
      <div class="col-sm-4">
         <!-- <ol class="breadcrumb">
            <li><a href="{{url('/home')}}"><i class="fa fa-dashboard"></i> {{$language['home']}}</a></li>
            <li class="active">{{$language['workflows']}}</li>
         </ol> -->
      </div>
   </div>
</section>
  
<section class="content">
   <div class="row">
      <div class="col-lg-3">
         <div class="form-group">
            <label for="inputEmail3" class="control-label">{{$language['workflow']}}</label>
            <select name="workflow_id" id="workflow_id" class="form-control workflow_load">
               <option value="0">{{ $language['select_workflow'] }}</option>
               @foreach ($workflows as $wf_row)  
               <option value="{{$wf_row->workflow_id}}" @if($wf_row->workflow_id == $workflow_id) selected="selected" @endif>{{$wf_row->workflow_name}}</option>
               @endforeach
            </select>
         </div>
      </div>
      <div class="col-lg-2">
         <div class="form-group">
            <label for="inputEmail3" class="control-label">{{$language['workflow_type']}}</label>
           <select class="form-control workflow_load" id="workflw_object_type"  name="workflw_object_type">
           <option value="0">Select a Type</option>
                           <option value="document" @if($object_type == 'document') selected="selected" @endif>{{$language['document']}}</option>
                           <option value="form" @if($object_type == 'form') selected="selected" @endif>{{$language['form']}}</option>
                        </select>
         </div>
      </div>
      <div class="col-lg-5">
         <div class="form-group">
            <label for="inputEmail3" class="control-label">{{$language['document']}}</label>
            <select name="workflow_id" id="workflow_doc_id" class="form-control workflow_doc_change">
               <option value="">{{ $language['select_document'] }}</option>
                @foreach ($wf_docs as $wf_row)  
               <option value="{{$wf_row->document_id}}" @if($wf_row->document_id == $object_id && $object_type == 'document') selected="selected" @endif>{{$wf_row->document_name}}</option>
               @endforeach

               @foreach ($wf_forms as $wf_row)  
               <option value="{{$wf_row->id}}" @if($wf_row->id == $object_id && $object_type == 'form') selected="selected" @endif>{{$wf_row->name}} - {{$wf_row->user_full_name}} - {{$wf_row->date}} </option>
               @endforeach
             
            </select>
         </div>
      </div>
     
<style>
            .card {
  min-width: 100%;
  display: flex;
  overflow-x: auto;
            }
             .panner {
    /*border:1px dotted #00c0ef;*/
    display:block;
    top:50%;
    padding: 5px 2px !important;
    margin: 0 !important;
    color: #d2d6de;
    cursor: pointer;
    background-color: rgba(0,0,0,0.7);   
    color: #fff;    
}
.card::-webkit-scrollbar {
  display: none;
}
#panLeft {
    float: left;
    
}
#panRight {
    right:0px;
    float: right;
}
.wf_header {
   /* position: fixed;
    z-index:2;*/
}

.panner:hover { 
    background-color: #00c0ef;
    color: #fff; 
    /*border:1px dotted #d2d6de;*/
}
 </style>            
      <div class="col-lg-12">
         <!-- <span id="panLeft" class="panner" style="
    position:inherit;" data-scroll-modifier='-1'>Left</span>
         <span id="panRight" class="panner" style="
    position:inherit;" data-scroll-modifier='1'>Right</span> -->

     <span id="panLeft" class="panner" style="
    position:fixed;z-index:1;" data-scroll-modifier='-1'><i class="fa  fa-chevron-left"></i> Left </span>
         <span id="panRight" class="panner" style="
    position:fixed;z-index:1;" data-scroll-modifier='1'> Right <i class="fa  fa-chevron-right"></i></span>
         <div class="row card" id="nav_stages">
         </div>
      </div>
      
      <div class="modal fade" id="activity_modal">
         <div class="modal-dialog">
            <div class="modal-content form_response_remote">
               
            <div class="modal-footer"></div>
            </div>
            <!-- /.modal-content -->
         </div>
         <!-- /.modal-dialog -->
      </div>

      
      <div class="modal fade" id="change_stage_modal">
         <div class="modal-dialog">
            <div class="modal-content">
               <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title">{{ $language['workflow_stage_change'] }}</h4>
               </div>
               <div class="modal-body">
                  {!! Form::open(array('url'=> array('change_workflow_stage'), 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'change_stage_form', 'id'=> 'change_stage_form','data-parsley-validate'=> '')) !!}  
                  <input type="hidden" name="wf_id" class="wf_id" value="{{ $workflow_id }}">
                  <input type="hidden" name="wf_stage_id" class="wf_stage_id" value="0">
                  <input type="hidden" name="wf_object_id" class="wf_object_id" value="0">
                  <input type="hidden" name="wf_object_type" class="wf_object_type" value="document">
                  <input type="hidden" name="workflow_doc_id" class="workflow_doc_id" value="">
                  <input type="hidden" name="stage_count" id="stage_count" value="{{$wf_stage_count}}">
                  <div class="form-group">
                     <label for="inputEmail3" class="col-sm-3 control-label">{{ $language['workflow_stage'] }}:</label>
                     <div class="col-sm-8">
                        <select class="form-control workflow_stages_list" id="change_stage_id"  name="change_stage_id" required="" data-parsley-required-message="{{ $language['workflow_stage_required'] }}" data-parsley-trigger="change focusout"></select>
                     </div>
                  </div>
                  <div class="form-group">
                     <div class="col-sm-3"></div>
                     <div class="col-sm-8">
                        <button type="submit" class="btn btn-primary">{{ $language['save'] }}</button>
                        <button type="button" class="btn btn-danger reset_stage" >{{ $language['cancel'] }}</button>
                     </div>
                  </div>
                  </form>
               </div>
               <div class="modal-footer">
               </div>
            </div>
            <!-- /.modal-content -->
         </div>
         <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->


      <div class="modal fade" id="add_to_workflow_modal_new">
         <div class="modal-dialog">
            <div class="modal-content add_workflow_remote">
               
            <div class="modal-footer"></div>
            </div>
            <!-- /.modal-content -->
         </div>
         <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->
    
<div class="modal-container-child"></div> 
      <div class="modal-container"></div>
      
   </div>
</section>
<!-- User edit form end -->
<!-- {!! Html::script('plugins/jQueryUI/jquery-ui.min.js') !!} -->
{!! Html::script('js/jquery-ui.min.js') !!}
{!! Html::script('plugins/select2/select2.full.min.js') !!}
<script>
   $(function ($) {
    $('#panRight').click(function(event) {
      event.preventDefault();
      $('#nav_stages').animate({
        scrollLeft: "+=200px"
      }, "slow");
   });
   
     $('#panLeft').click(function(event) {
      event.preventDefault();
      $('#nav_stages').animate({
        scrollLeft: "-=200px"
      }, "slow");
   });
   
   var today = "@php echo date('d-m-Y'); @endphp";  
   
   var show_work_flow = function(response) 
   {
   var html = ''; 
   if(response.status == 1)
             {
               $(".wf_id").val(response.workflow_id);
               if(response.wf_details == null)
               {
                  $("#nav_stages").html(html);
               }
               else
               {
               
              $("#nav_stages").html(response.html);

               html += '<ul class="navbuttons">';
               var workflow_color =(typeof response.wf_details.workflow_color != 'undefined' && response.wf_details.workflow_color)?response.wf_details.workflow_color:'';
               var background = (workflow_color)?'background:'+workflow_color+';':'';
               var background_color =  (workflow_color)?'background-color:'+workflow_color+';':'';  
               var border =  (workflow_color)?'border:1px solid '+workflow_color+';':'';
               console.log("border"+border);
               console.log("wf_details"+response.wf_details.workflow_color);
               var workflow_stages_option ='<option value="">{{ $language["select_workflow_stage"] }}</option>';
               var loops=0;
               var loopend=response.wf_stage_details.length;
               $.each(response.wf_stage_details, function( key, value ) {
                  loops =loops+1;
                 html +='<li class="navbuttons_li"><div class="col-lg-12 nopadding_div">';
                html +='<div class="box  box-solid" ">';
                html +='<div class="box-header" style="color: #fff; '+background+' '+background_color+'">';
                html +='<h3 class="box-title">'+value['workflow_stage_name']+'</h3>';
                if(loops != loopend)
                {
                   html +='<i class="fa fa-play pull-right"></i>';
                }
               
                html +='</div>';
                 html +='<ul class="connectedSortable_doc nopadding_class" id="'+value['workflow_stage_id']+'" style="border-color:'+workflow_color+'">';
                 workflow_stages_option +='<option value="'+value['workflow_stage_id']+'">'+value['workflow_stage_name']+'</option>';
                $.each(value.docs, function( key_doc, value_doc ) 
                  {
                     var data_options = 'data-activity="0"  data-workflow="'+response.workflow_id+'" data-stageid="'+value['workflow_stage_id']+'"  data-objectid="'+value_doc.obj_id+'" data-objecttype="'+value_doc.obj_type+'"';  
                   html += '<li '+data_options+' style="border-color:'+workflow_color+'">';
                    html += '<div class="box  document_box">';
                    html += '<div class="box-body ">';
           html += '<p  class=""><b>'+value_doc.obj_name+'</b></p>';
            html += '<div class="box-tools text-center">';
          
              html += '<a href="javascript:void(0)" '+data_options+' class="btn btn-box-tool add_activity" data-activity="0" data-activity="0" data-action="add"><i class="fa fa-plus"></i> Add Activity </a>';
   
               html += '<a href="javascript:void(0)" '+data_options+' class="btn btn-box-tool complete_work_flow" ><i class="fa fa-location-arrow"></i> Complete Workflow </a>';
   
               html += '<a href="javascript:void(0)"  '+data_options+' class="btn btn-box-tool handler_doc change_stage" ><i class="fa fa-exchange "></i> Change stage </a>';
   
             html += '</div>';
   
             html += '<ul class="products-list product-list-in-box connectedSortable_activity nopadding_class" style="display:block;">';
             
              
              $.each(value_doc.doc_activity, function( key_activity, value_activity ) 
              {
   
               html += '<li class="item nopadding_class li_activity" data-actid="'+value_activity.document_workflow_id+'">';
               span = '<span class="pull-right tool_activity">';
               /*html += '<a href="javascript:void(0)"  data-activity="'+value_activity.document_workflow_id+'" class="handler"><i class="fa fa-arrows " title="Sort Activity"></i> </a>';*/

               span += '<a href="javascript:void(0)"  data-activity="'+value_activity.document_workflow_id+'" data-objectid="'+value_doc.obj_id+'" data-objecttype="'+value_doc.obj_type+'" data-stageid="'+value['workflow_stage_id']+'" class="add_activity" data-action="view"><i class="fa fa-eye"></i> </a>';

               span += '<a href="javascript:void(0)"  data-activity="'+value_activity.document_workflow_id+'" data-objectid="'+value_doc.obj_id+'" data-objecttype="'+value_doc.obj_type+'" data-stageid="'+value['workflow_stage_id']+'" class="add_activity" data-action="edit"><i class="fa fa-edit"></i> </a>';
               span += '<a href="javascript:void(0)" data-workflow="'+response.workflow_id+'" data-activity="'+value_activity.document_workflow_id+'" class="delete_activity"><i class="fa fa-trash-o" ></i> </a>';
               span += '</span>';
              html += '<p class="handler nopadding_class">Activity: '+value_activity.activity_name+''+span+'</p>';
              html += '<p class="nopadding_class">';
              html += '<label class"pull-left">Due Date: '+value_activity.activity_due_date+'</label>';
              html += '<label class"pull-right" style="float:right;">Date: '+value_activity.activity_date+'</label>';
              html += '</p>'; 
   
               html += '<p class="nopadding_class">';
              html += '<span class"pull-left">Assigned to: '+value_activity.assigned_to_user_name+'</span>';
              html += '<span class"pull-right" style="float:right;">Assigned by: '+value_activity.assigned_by_user_name+'</span>';
              html += '</p>'; 
              html += '<dl class="nopadding_class"><dd>Note:'+value_activity.activity_note+'</dd></dl>';
            html += '<div class="handler nopadding_class dont-break-out" style="width:200 px;"><span class="dont-break-out"></span></div>'
            
              
               html += '</li>';
               });  
                
               
             html += '</ul>';
           html += '</div>';
           html += '</div>';
                    html += '</li>';
                });
               
                html += '</ul>';
                html += '</div></div>';
                html += '</li>';
               });
                html += '</ul>';
                html += '</div>';
   
                //$("#nav_stages").html(html);
               $('.connectedSortable_doc').sortable({
   placeholder         : 'sort-highlight',
   connectWith         : '.connectedSortable_doc',
   handle              : '.handler_doc',
   forcePlaceholderSize: true,
   zIndex              : 999999,
       helper: 'clone',
       tolerance: "pointer",
   stop: function (event, ui) {
       
     console.log("stop");

   },
   receive: function(event, ui) {
      $("#change_stage_id").val(this.id);
      $(".wf_stage_id").val(ui.item.attr("data-stageid"));
         $(".wf_object_id").val(ui.item.attr("data-objectid"));
         $(".wf_object_type").val(ui.item.attr("data-objecttype"));
   $('#change_stage_form').parsley().reset();
     $('#change_stage_modal').modal({
         show: 'show',
         backdrop: false
     });
        console.log("dropped on = "+this.id);
          console.log("sender = "+ui.sender[0].id);

    } 
   });     
   
   $('.connectedSortable_activity').sortable({
   placeholder         : 'sort-highlight',
   handle              : '.handler',
   forcePlaceholderSize: true,
   zIndex              : 999999,
   helper: 'clone',
   tolerance: "pointer",
   stop: function (event, ui) {
      var data_activity = $(this).sortable('toArray', { attribute: 'data-actid' });
      console.log(data_activity);
      console.log("stop activity");
      var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
          $.ajax({
            data:{_token:CSRF_TOKEN,data_activity:data_activity},
            type: 'POST',
            dataType:'json',
            url: "{{URL('saveActivityPostion')}}",
            success:function(response)
            {
              /*console.log(response);*/
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(jqXHR.status);
                }
        });
   }
   });
   $(".workflow_stages_list").html(workflow_stages_option);
             }
           }
   };  
   
   var get_workflow_stages = function() {
    
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
         $.ajax({
           data:{_token:CSRF_TOKEN,workflow_id:$('#workflow_id').val(),workflow_doc_type:$('#workflow_doc_type').val(),object_type:$('#workflw_object_type').val(),object_id:$('#workflow_doc_id').val()},
           type: 'POST',
           dataType:'json',
           url: '{{URL('workflow_stages')}}',
           success:function(response)
           {
             console.log(response);
             show_work_flow(response);

             
           },
           error: function(jqXHR, textStatus, errorThrown) 
           {
               console.log(jqXHR.status);
           }
       });
   }

   var get_workflow_docs = function() {
    
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
         $.ajax({
           data:{_token:CSRF_TOKEN,workflow_id:$('#workflow_id').val(),object_type:$('#workflw_object_type').val()},
           type: 'POST',
           dataType:'json',
           url: '{{URL("get_workflow_docs")}}',
           success:function(response)
           {
             console.log(response);
              if(response.wf_docs != null || response.wf_forms != null)
               {
               
               var wf_docs_option = '<option value="">{{$language["select_document"]}}</option>';
               $.each(response.wf_docs, function( key, value ) 
               {
                  wf_docs_option +='<option value="'+value.document_id+'">'+value.document_name+'</option>';
                }); 

                $.each(response.wf_forms, function( key, value ) 
               {
                  wf_docs_option +='<option value="'+value.id+'">'+value.name+'-'+value.user_full_name+'-'+value.date+'</option>';
                });    
               $("#workflow_doc_id").html(wf_docs_option);
               }
             
           },
           error: function(jqXHR, textStatus, errorThrown) 
           {
               console.log(jqXHR.status);
           }
       });
   }
    
   get_workflow_stages();

    $('.workflow_load').on('change', function() {
    $('#workflow_doc_id').val('');  
    $('.workflow_doc_id').each(function (index, value) { 
         $(this).val(''); 
   });
   get_workflow_docs();
   get_workflow_stages();
   });
   
   $('.workflow_doc_change').on('change', function() {

      $('.workflow_doc_id').each(function (index, value) { 
         $(this).val($('#workflow_doc_id').val()); 
   });
   get_workflow_stages();

   });
  
   var loading_src = "{{ URL::asset('images/loading/loading.gif') }}";
   var loading_text ='<div  style="text-align: center;"><img src="'+loading_src+'"></div>';
   $(document).on("click",".add_activity",function(e){
    e.preventDefault();
    var activity = $(this).attr("data-activity");
    var objectid = $(this).attr("data-objectid");
    var objecttype = $(this).attr("data-objecttype");
    var stageid = $(this).attr("data-stageid");
    var action = $(this).attr("data-action");
    var workflow_id= $("#workflow_id").val();
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var post_data = {_token: CSRF_TOKEN,"activity":activity,"objectid":objectid,"objecttype":objecttype,"workflow_id":workflow_id,"stageid":stageid,"action":action};
     show_Activity_details(post_data);
   });  

   var show_Activity_details = function(post_data) 
   {
    $('#activity_modal .form_response_remote').html(loading_text);
    $('#activity_modal').modal({
                     show: 'show',
                     backdrop: false
               }); 
    
    $.ajax({
            type: 'POST',
            url: "{{URL('load_activity_form')}}",
            dataType: 'json',
            data: post_data,
            timeout: 50000,
            success: function(data)
            {
                $('.form_response_remote').html(data.html);

            }
        });
   };
@php
    if(isset($activity_view) && $activity_view)
    {
    @endphp
      var workflow_id= $("#workflow_id").val();    
      var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var obj = {_token: CSRF_TOKEN,activity: "@php echo $activity_view; @endphp","workflow_id":workflow_id,"action":"view"};
        show_Activity_details(obj);
    @php
    }
@endphp 

$(document).on("click",".save_action_workflows",function(e) {
   e.preventDefault();
   var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
   var workflow_id = $('#wf_action_id').val();
   var activity= $('#wf_action_activity_id').val();
   var activity_id= $('#action_activity_id').val();
   var activity_name= $('#action_activity_id').find('option:selected').text();
   var activity_note= $('#action_activity_note').val();
   var last_activity_check = $('#action_activity_id').find('option:selected').attr('last_activity');
   var stage_order = $('#hidd_stage_order').val();
   var stage_count = $('#stage_count').val();
   if($("#workflow_action_form").parsley().validate())
   {
    if(last_activity_check == 1)
    {
      swal({
        title:"{{$language['last_activity_wf_next_stage']}}",
        showCancelButton: true
        }).then((result) => {
        if(result){
          var url = "@php echo url('change_workflow_stage'); @endphp";
            $.ajax({
              type: "POST",
              url: url,
              dataType:'json',
              data: {_token: CSRF_TOKEN,
                "wf_id":$("#hidd_wf_id").val(),
                "wf_stage_id":$("#hidd_wf_stage_id").val(),
                "wf_object_id":$("#hidd_wf_object_id").val(),
                "wf_object_type":$("#hidd_wf_object_type").val(),
                "current_stage_order":stage_order
              },
              success: function(response)
              {
                  if(response.status == 1)
                  {
                   show_work_flow(response);
                   $('#change_stage_modal').modal('hide');
                  }
                  
              }
            });
        }
        else
        {
          //stay in same stage
        }
     });
    }
    if((stage_order == stage_count) && (last_activity_check == 1))
    {
      var exit = "@php echo url('workflow_exit'); @endphp?wf="+$("#hidd_wf_id").val()+"&objecttype="+$("#hidd_wf_object_type").val()+"&objectid="+$("#hidd_wf_object_id").val();
      swal({
        title:"{{$language['last_activity_wf_exit']}}",
        showCancelButton: true
        }).then((result) => {
                           if(result){
                            window.location.href= exit;
                           }
                           else{
                             //stay in workflow last stage
                           }
                         });
    }

   $.ajax({
            type: 'POST',
            url: "{{URL('save_action_workflow')}}",
            data: {_token:CSRF_TOKEN,workflow_id: workflow_id,activity_id: activity_id,activity_name: activity_name,activity_note: activity_note,activity: activity},
            dataType: 'json',
            timeout: 50000,
            success: function(data)
            {
                if(data.html !='')
                {
                  $('.form_response_remote').html(data.html);
                }
                if(data.message !='')
                {
                   $('.alert_form').html(data.message);
                }
                get_workflow_stages();
            }
        });
   }
   });
  
  
   
   
   $(document).on("click",".change_stage",function() {
     
         $("#change_stage_id").val('');
         $(".wf_stage_id").val($(this).attr("data-stageid"));
         $(".wf_object_id").val($(this).attr("data-objectid"));
         $(".wf_object_type").val($(this).attr("data-objecttype"));
   $('#change_stage_form').parsley().reset();
     $('#change_stage_modal').modal({
         show: 'show',
         backdrop: false
     }); 
   });

   $(document).on("click",".reset_stage",function() {
     
         $(".connectedSortable_doc").sortable('cancel');
         $('#change_stage_modal').modal('hide');
   });
   

   $(document).on("click",".complete_work_flow",function() {
   var complete = "@php echo url('workflow_exit'); @endphp?wf="+$(this).attr("data-workflow")+"&objecttype="+$(this).attr("data-objecttype")+"&objectid="+$(this).attr("data-objectid");
     var Stage_name = swal({
                         title: "{{$language['workflow_complete_note']}}", 
                         showCancelButton: true
                       }).then((result) => {
                           if(result){
                             
                             window.location.href= complete;
                           }
                           else{
                             
                           }
                         });
    
   });
 
   

   
   
   $(document).on("click",".delete_activity",function() {
   var deleteurl = "@php echo url('workflow_activity_delete'); @endphp?wf="+$(this).attr("data-workflow")+"&activity="+$(this).attr("data-activity");
     var Stage_name = swal({
                         title: "{{$language['activity_delete_note']}}", 
                         showCancelButton: true
                       }).then((result) => {
                           if(result){
                             console.log(deleteurl);
                               window.location.href = deleteurl;
   
                           }
                           else{
                             
                           }
                         });
    
   });
   
   
   console.log("tos"+today);
  
  
   $(document).on("click",".save_activity",function(e) {
   e.preventDefault();
   var last_activity_check = $('#activity_name').find('option:selected').attr('add_last_activity');
   var stage_order = $('#add_hidd_stage_order').val();
   var stage_count = $('#stage_count').val();
   if($("#workflow_activity_form").parsley().validate())
   {   
      if(last_activity_check == 1)
      {
        var url = "@php echo url('workflow_activity_save'); @endphp";
        swal({
        title:"{{$language['last_activity_wf_next_stage']}}",
        showCancelButton: true
        }).then(function(result) {
         $.ajax({
              type: "POST",
              url: url,
              dataType:'json',
              data: $("#workflow_activity_form").serialize() + "&last_activity_flag="+last_activity_check, /*serializes the form's elements.*/
              success: function(response)
              {
                var url = "@php echo url('change_workflow_stage'); @endphp";
              
                  $.ajax({
                  type: "POST",
                  url: url,
                  dataType:'json',
                  data: $("#workflow_activity_form").serialize() + "&last_activity_flag="+last_activity_check, /*serializes the form's elements.*/
                  success: function(response)
                  {
                    if(response.status == 1)
                    {
                     show_work_flow(response);
                     $('#activity_modal').modal('hide');
                    }
                  }
                  });
              }
            });
        }, 
        function(dismiss) {
         var url = "@php echo url('workflow_activity_save'); @endphp";
              $.ajax({
                type: "POST",
                url: url,
                dataType:'json',
                data: $("#workflow_activity_form").serialize(), /*serializes the form's elements.*/
                success: function(response)
                {
                     /*show response from the php script.*/
                    $(".alert_space").html(response.message); 
                    if(response.status == 1)
                    {
                     show_work_flow(response);
                     //$('#activity_modal').modal('hide');
                    }
                    
                }
              });
       });
      }
      else
      {
        var url = "@php echo url('workflow_activity_save'); @endphp";
        $.ajax({
          type: "POST",
          url: url,
          dataType:'json',
          data: $("#workflow_activity_form").serialize(), /*serializes the form's elements.*/
          success: function(response)
          {
               /*show response from the php script.*/
              $(".alert_space").html(response.message); 
              if(response.status == 1)
              {
               show_work_flow(response);
               $('#activity_modal').modal('hide');
              }
              
          }
        });
      }
     
//check last order and last stage?
      if((stage_order == stage_count) && (last_activity_check == 1))
      {
        var exit = "@php echo url('workflow_exit'); @endphp?wf="+$("#add_wf_id").val()+"&objecttype="+$("#add_wf_object_type").val()+"&objectid="+$("#add_wf_object_id").val();
        swal({
          title:"{{$language['last_activity_wf_exit']}}",
          showCancelButton: true
          }).then((result) => {
                             if(result){
                              window.location.href= exit;
                             }
                             else{
                               //stay in workflow last stage
                             }
                           });
      }
    }
  });
   
   $("#change_stage_form").submit(function(e) {
   
   
   var url = "@php echo url('change_workflow_stage'); @endphp";
   
   $.ajax({
          type: "POST",
          url: url,
          dataType:'json',
          data: $("#change_stage_form").serialize(), /*serializes the form's elements.*/
          success: function(response)
          {
               /*show response from the php script.*/
   //alert_space").html(response.message); 
              if(response.status == 1)
              {
               show_work_flow(response);
               $('#change_stage_modal').modal('hide');
              }
              
          }
        });
   
   e.preventDefault(); /*avoid to execute the actual submit of the form.*/
   });
   
  
   
   
   //refine code

   $(document).on("click",".add_to_workflows",function(e) {
   e.preventDefault();
   var src = "{{ URL::asset('images/loading/loading.gif') }}";
   var loading ='<div  style="text-align: center;"><img src="'+src+'"></div>';
   $('.add_workflow_remote').html(loading);
   $('#add_to_workflow_modal_new').modal({
                     show: 'show',
                     backdrop: false
               }); 
   var workflow_id=$('#workflow_id').val();
   var object_type='document';  
   $('.add_workflow_remote').load("@php echo url('add_to_workflow_modal') @endphp?workflow_id="+workflow_id+"&object_type="+object_type,function(result){
         /*$('#loading_model').modal('hide');*/
         /*$('#add_to_workflow_modal_new').modal({
                     show: 'show',
                     backdrop: false
               }); */
         
         });
   });

  $(document).on('hidden.bs.modal','#add_to_workflow_modal_new', function () {
         get_workflow_stages();
      
   });

  $(document).on("click",".show_more_activity",function(e) {
   e.preventDefault();
   
   var kloop = $(this).attr("data-loop");
   console.log("HI"+kloop);
   if ($("#show_li_"+kloop).css('display') == 'none') 
   {
    $("#show_li_"+kloop).slideDown();
   }
   else
   {
    $("#show_li_"+kloop).slideUp();
   }
   
   });
   
   });
   
</script>    
@endsection