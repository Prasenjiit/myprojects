<?php include (public_path()."/storage/includes/lang1.en.php" ); ?> 
@extends('layouts.app')
@section('main_content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.1/css/bootstrap-colorpicker.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.1/js/bootstrap-colorpicker.min.js"></script> 
{!! Html::style('plugins/vis-4.21.0/dist/vis-network.min.css') !!} 
{!! Html::script('plugins/vis-4.21.0/dist/vis.min.js') !!}  
{!! Html::script('js/parsley.min.js') !!}  
<!-- {!! Html::style('plugins/select2/select2.full.min.css') !!}
   {!! Html::script('plugins/select2/select2.full.min.js') !!}  -->  
{!! Html::style('css/fastselect.min.css') !!} 
{!! Html::script('js/fastselect.standalone.js') !!}
{!! Html::style('plugins/jQuery-QueryBuilder-master/dist/css/query-builder.default.css') !!} 
<style type="text/css">
   .help{
   font-size:12px; color:#999;
   }
   .item_header {
   margin-top: 0px !important; 
   margin-bottom: 0px !important;
   }
   .li_active {
   border: 1px solid #3c8dbc !important;
   }
   .products-list .product-img {
   padding-left: 5px;
   }
   .action_span {
   padding-right: 5px;
   color: #333;
   }
   .workflow_template li { cursor: pointer; }
   #mynetwork {
   /*width: 400px;
   height: 400px;*/
   border: 1px solid lightgray;
   }
   #stages_table thead tr {
   background-color: #fff !important;
   color: #333 !important;
   }
   #edges_table thead tr {
   background-color: #fff !important;
   color: #333 !important;
   }
   .vis-button:after {
   font-size: 2em;
   color: gray;
   }
   .vis-button:hover:after {
   font-size: 2em;
   color: lightgray;
   }
   .object_type_row{display: none;}
   .full_widht{width: 100% !important;}
   .fstElement {width: 100% !important;height: auto !important}
   .fstControls {width: 100% !important;}
   .fstResults {
   max-height: 124px !important;
   overflow-y: scroll !important;
   }
   .sticky-scroll-box{
   background:#f9f9f9;
   box-sizing:border-box;
   -moz-box-sizing:border-box;
   -webkit-box-sizing:border-box;
   }
   .fixed {
   position:fixed;
   top:0;
   /* z-index:99999;*/
   }
</style>
<section class="content">
   <div class="row">
      <div class="box-header with-border">
         <h3 class="box-title">{{ Lang::get('workflows.workflow_new_template') }}</h3>
      </div>
      <!-- /.box-header -->

@if(Session::get('enbval4')==Session::get('tval'))       
      <!-- form start -->
<form role="form" id="closed_workflow_name" name="closed_workflow_name" data-parsley-validate="data-parsley-validate">
   
<input type="hidden" name="workflow_id" id="workflow_id" class="workflow_id" value="{{$workflow_id}}">
  
   <div class="box-body">
      
      <div class="col-md-12 alert_space"></div>
      
      <!-- LEFT BOX START-->
      <div class="col-md-4">
         <div class="box box-primary sticky-scroll-box">
            <div class="box-body">
               
               <!-- START ROW -->
               <div class="row">
                  <div class="col-xs-12">
                     <ul class="products-list product-list-in-box workflow_template">
                        <!-- /.item -->
                        <li class="item li_active wf_li" data-liid="0" id="wf_title_main" data-stageid="0">
                           <div class="product-img">
                              <i class="fa fa-play fa-2x"></i>
                           </div>
                           <div class="product-info">
                              <h3 class="item_header" id="wf_name_label">Loading....</h3>
                           </div>
                        </li>
                     </ul>
                  </div>
               </div>
               <!-- END ROW -->

               <!-- START ROW -->
               <div class="row">
                  <!-- LODER LEFT SIDE -->
                  <div class="col-xs-12 text-center" style="margin-top: 3px;">
                     <div class="preloader" >
                        <i class="fa fa-spinner fa-pulse fa-fw fa-lg"></i>
                        <span class="sr-only">Loading...</span>
                     </div>
                  </div>
                  <!-- Action Buttons -->

                  <div class="col-xs-12">
                     <button type="button" class="btn btn-primary btn-sm  add_task action_button" data-wf_type="node" ><i class="fa fa-plus"></i> Add Stage</button>
                     
                     <button type="button" class="btn btn-primary btn-sm  save_workflow action_button"  >
                     <i class="fa fa-save"></i> Save Template</button>

                     <button type="button" class="btn btn-danger btn-sm  cancel_wf_edit action_button" > 
                     <i class="fa fa-close"></i> Cancel </button>
                  </div>
                  <!--<div class="col-xs-6">
                     <button type="button" class="btn btn-primary btn-sm btn-block add_task action_button" data-wf_type="node" ><i class="fa fa-plus"></i> Add Stage</button>
                     <button type="button" class="btn btn-warning btn-sm btn-block preview action_button" ><i class="fa fa-eye"></i> Preview</button>
                  </div>
                  <div class="col-xs-6">
                     <button type="button" class="btn btn-danger btn-sm  btn-block cancel_wf_edit action_button" > 
                     <i class="fa fa-close"></i> Cancel </button>
                     <button type="button" class="btn btn-primary btn-sm btn-block save_workflow action_button"  >
                     <i class="fa fa-save"></i> Save Template</button>
                  </div>-->
               </div>
               <!-- END ROW -->

            </div>
            <!-- END Box Body -->
         </div>
      </div>

      <!-- RIGHT BOX START-->

      <div class="col-md-8">
         <div class="box box-primary">
            <div class="box-body">

              <!-- ROW  START-->
               <div class="row right_wft_col clearfix">
                  
                  <!-- Workflow Template settings-->
                  <div class="col-md-12 taskdiv" id="rwf_div0">
                     <h3 class="" >Workflow Details</h3>
                     <div class="form-group">
                        <label>{{trans('workflows.workflow name')}}: <span class="compulsary">*</span></label>
                        <input type="text" class="form-control" id="workflow_name" name="workflow_name" required="" data-parsley-required-message="Workflow name is required" data-parsley-trigger="change focusout" data-stageid="0">
                     </div>
                     <div class="form-group">
                        <label>Workflow Object Type: <span class="compulsary">*</span></label>
                        <select name="workflow_object_type" id="workflow_object_type" class="form-control select2" required=""  data-parsley-required-message="Form Workflow type is required" data-parsley-trigger="change focusout" data-stageid="0">
                           <option value="">Select workflow object type</option>
                           <option value="form">Form  based workflow</option>
                           <!-- <option value="document">Document based workflow</option> -->
                        </select>
                     </div>
                     <div class="form-group object_type_row form_row">
                        <label>Form Name: <span class="compulsary">*</span></label>
                        <select name="form_id" id="form_id" class="form-control select2 rule_components" data-object_type="form" data-stageid="0">
                           <option value="">Select</option>
                        </select>
                     </div>
                     <div class="form-group object_type_row document_row">
                        <label>Document Type: <span class="compulsary">*</span></label>
                        <select name="document_id" id="document_id" class="form-control select2 rule_components" data-object_type="document" data-stageid="0">
                           <option value="">Select</option>
                        </select>
                     </div>
                     <div class="form-group">
                        <label>{{trans('language.color')}}: <span class="compulsary">*</span></label>
                        <div id="workflow_color_div" class="input-group colorpicker-component" title="Using input value">
                           <input type="text" name="color" id="workflow_color" class="form-control" value="#C0C0C0" readonly="" data-stageid="0" />
                           <span class="input-group-addon"><i></i></span>
                        </div>
                     </div>
                     
                     
                  </div>
                  <!-- Workflow Template settings END-->
                  <!-- COL 12 --> 


               </div>
               
               <div class="box-footer" style="">
                  <div class="row right_wft_col">
                     <div class="box box-solid">
                        <div class="box-header with-border">
                          <!-- <label>Preview</label> -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body text-center">
                           <div id="mynetwork"></div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                 
                  <div class="row">        
                  <!-- Action Buttons -->
                  <div class="col-xs-6">
                    <button type="button" class="btn btn-primary btn-sm  save_workflow action_button" >
                     <i class="fa fa-save"></i> Save Template</button>
                  
                     <button type="button" class="btn btn-danger btn-sm  cancel_wf_edit action_button" > 
                     <i class="fa fa-close"></i> Cancel </button>
                     
                  </div>

                  <div class="col-xs-4">
                     <div class="preloader" >
                        <i class="fa fa-spinner fa-pulse fa-fw fa-lg"></i>
                        <span class="sr-only">Loading...</span>
                     </div>
                  </div>
               </div>   
                        </div>
                     </div>
                  </div>
               </div>


            </div>
         </div>
      </div>
      <!-- RIGHT BOX END-->

      <!-- ROW -->


   </div>
</form>
@elseif(Session::get('enbval4')==Session::get('fval'))
      <section class="content" style="min-height: 797px;"><div class="alert alert-danger alert-sty">{{trans('language.purchase_now')}}</div></section>
  @else
      <section class="content" style="min-height: 797px;"><div class="alert alert-danger alert-sty">{{trans('language.module_expired')}}</div></section>
  @endif
      <!-- FORM END -->
   </div>
</section>
<input type="hidden" value="0" id="is_edit_true">
{!! Html::script('js/jquery-ui.min.js') !!}
<script>
   $(document).ready(function() {
   
      
      
      var top = $('.sticky-scroll-box').offset().top;
      $(window).scroll(function (event) {
      var y = $(this).scrollTop();
          if (y >= top) {
            $('.sticky-scroll-box').addClass('fixed');
          } else {
            $('.sticky-scroll-box').removeClass('fixed');
          }
          $('.sticky-scroll-box').width($('.sticky-scroll-box').parent().width());
      });
   
   
      var s = $("#sticker");
      var pos = s.position();                    
      $(window).scroll(function() {
          var windowpos = $(window).scrollTop();
      });
   
  
   $(".action_button").attr("disabled", true);   
  /*Set Color Picker */
  $('#workflow_color_div').colorpicker({
            format: 'hex'
        }).on('change',
            function(ev) {
                console.log("color_changed"+ev.target.value);
                //console.log(ev);
                /*if(stage_color != ev.target.value)
                {

                }*/
                /*stage_color = ev.target.value;*/
            });

   /*Cancel Button*/
    $(".cancel_wf_edit").click(function() {
          var change = $("#is_edit_true").val();
          var url    = "@php echo URL('allworkflow'); @endphp";
          if(change==1){
            var x = confirm("Changes could not be saved..");
            if(x==true){
                window.location = url;
            }
            return false;
          }
          else{
            window.location = url;
          }
      });
    /*End*/     

  //Common settings for workflow Template
  var stage_count  = 0; /*Stages Count*/
  var action_count = 0; /*Actions Count*/
  var stage_color =''; 
  var stage_option = '<option value="0" selected>Select Stage</option>';
  var first = 0;
  var last = 0;
  var db_stage_option='';
  var validation_check=0;
  var rg_count=0;
  var rule_count=0;
  var myrcArray = {};
  var rules_basic_base = {condition: 'AND',rules:[],stage_case:'if',if_stage:0,else_stage:0};

  /* By user List START*/
  var stage_users_options='';
  var stage_dept_options = '';
   @php foreach ($users as $key => $user){ @endphp
    stage_users_options += "<option value='{{@$user->id}}'>{{ucfirst(@$user->user_full_name)}} -{{@$user->user_role}}@if(@$user->departments[0]->department_name != "")- {{ucfirst(@$user->departments[0]->department_name)}}@endif</option>";
  @php } @endphp 

/* Department List Start*/
   @php foreach ($departments as $key => $departments){ @endphp
    stage_dept_options += @php echo "'<option value=\"$departments->department_id\">$departments->department_name</option>'"; @endphp;
  @php } @endphp 

/* Activity List Start*/
  var wfactivities_options = '';
   @php foreach ($wfactivities as $key => $ac){ @endphp
    wfactivities_options += "<option value='{{@$ac->activity_id}}'>{{ucfirst(@$ac->activity_name)}}</option>";
  @php } @endphp 


  var rule_options='';
  var rule_operators = {
    "equal": "equal",
    "not_equal": "not equal",
    /*"in": "in",
    "not_in": "not in",*/
    "less": "less",
    "less_or_equal": "less or equal",
    "greater": "greater",
    "greater_or_equal": "greater or equal",
    /*"between": "between",
    "not_between": "not between",*/
    "begins_with": "begins with",
    /*"not_begins_with": "doesn't begin with",
    "contains": "contains",
    "not_contains": "doesn't contain",*/
    "ends_with": "ends with"/*,
    "not_ends_with": "doesn't end with",
    "is_empty": "is empty",
    "is_not_empty": "is not empty",
    "is_null": "is null",
    "is_not_null": "is not null"*/
  };
    var operator_options =''; 
   $.each(rule_operators, function(i, item)
              {
                /*console.log(item);*/
                 operator_options +='<option value="'+i+'">'+item+'</option>';
              });

 

  /* This function loads all data for Add and Edit Template */
  var loadWorkflowtemplate = function() 
   {
     var workflow_id = $('#workflow_id').val();
     var loadformurl = "@php echo URL('load_Workflow_json_data'); @endphp";
      /*Start Ajax*/
      $.getJSON(loadformurl+'?workflow_id=' + workflow_id, function(data) {

        stage_count = parseInt(data.stage_count);
        action_count = parseInt(data.action_count);
        console.log("stage_count"+stage_count);
        console.log("action_count"+action_count);

        var workflow_name =data.workflow_name;
        stage_color = data.workflow_color;
        var wf_object_type = data.wf_object_type;
        wf_object_type_id = data.wf_object_type_id;

        var wf_name_label = "Workflow Start";
        if(workflow_name != '')
        {
            wf_name_label = workflow_name;
        }
        $("#wf_name_label").html(wf_name_label);

        $("#workflow_name").val(workflow_name); 
        $("#workflow_color").val(stage_color);
        $("#workflow_color").trigger('change');
        $("#workflow_object_type").val(wf_object_type);

        $(".object_type_row").slideUp();  
        if(wf_object_type == 'form')
        {
         $(".form_row").slideDown();
         load_workflow_objects();
        }
        else if(wf_object_type == 'document')
        {
         $(".document_row").slideDown();
         load_workflow_objects();
        }


        db_stage_option = '<option value="0" selected>Select</option>';
        //first = last = 0;
        $.each(data.wf_states, function(i, item)
        {
          
          if(item.stype != 'first')
          {
          db_stage_option += '<option value="' + item.id + '">' + item.label + '</option>'; 
          }
        });

        stage_option = db_stage_option;
        $.each(data.wf_states, function(i, item)
        {
          var taskhtml = addTask(item);
        });
        rule_components = data.rule_components; 
        rule_options='<option value="-1" data-column_type="invalid" data-key="invalid" data-object_type="invalid">------</option>';  
        $.each(rule_components, function(i, item)
        {
          var disbld ='';
          if(item.object_type=="invalid")
          {
                    disbld = 'disabled="disabled"';
          }
          rule_options +='<option value="'+item.id+'" data-column_type="'+item.column_type+'" data-key="'+i+'" data-object_type="'+item.object_type+'" '+disbld+'>'+item.column_name+'</option>';
              });

        $.each(data.wf_transitions, function(i, item)
        {
          var taskhtml = addAction(item);
        });

        //
        if(first == 0)
        {

          var empty=[];
          var task= {id: stage_count, dbid: 0, label: 'Start (System Stage)', shape: "circle", color: "#c0c0c0", type: 'nodes',stype: 'first', description:'Start stage',department_users:empty,sel_departments:empty,sel_department_users:empty,stage_action:4,stage_group:1,stage_percentage:0,escallation_stage:0,escallation_day:0,escallation_activity_id:0}
          addTask(task);
          stage_count++;

        }

        if(last == 0)
        {

         var task= {id: stage_count, dbid: 0, label: 'End (System Stage)', shape: "circle", color: "#c0c0c0", type: 'nodes',stype: 'last', description:'End stage',department_users:empty,sel_departments:empty,sel_department_users:empty,stage_action:4,stage_group:1,stage_percentage:0,escallation_stage:0,escallation_day:0,escallation_activity_id:0}
          addTask(task);
          stage_count++;

        }

        $(".action_button").attr("disabled", false);
        $('.preloader').hide();

});
      /*END Ajax*/
   };


   /*####### Workflow Object Type Change event START #####*/


   var load_workflow_objects = function() 
   {
        var wf_object_type = $('#workflow_object_type').val();
        var form_url = "@php echo URL('load_workflow_objects'); @endphp";
        var data = {"wf_object_type":wf_object_type};
        $.ajax({
            method: "GET",
            url: form_url,
            data: data,
            dataType: 'json',
            success: function (msg) 
            {
                if(msg.status == 1)
                {
                        
                   var stage_option_object = '<option value="">Select</option>';
    
    $.each(msg.wf_objects, function(i, item) {
      var selected='';
      /*console.log("wf_object_type_id"+wf_object_type_id);*/
      if(wf_object_type_id == item.object_id)
      {
        
        selected='selected="selected"';
      }
      stage_option_object += '<option value="' + item.object_id + '" ' + selected + '>' + item.object_name + '</option>';
      
       });

                   if(wf_object_type == 'form')
                  {  
                       $('#form_id').html(stage_option_object);
                  }
                  else if(wf_object_type == 'document')
                  {  
                       $('#document_id').html(stage_option_object);
                  }
                  $('input').change(function() { 
                   // $("#is_edit_true").val(1);
                  });
                }
              
            }
        });
    
   };


   $(document).on("change","#workflow_object_type",function(e)
   {
    
    var wf_object_type=  $(this).val();
    $(".object_type_row").slideUp();

     $('#form_id').attr('data-parsley-required', 'false');
     $('#document_id').attr('data-parsley-required', 'false');
     if(wf_object_type == 'form')
     {
        $(".form_row").slideDown();
        $('#form_id').attr('data-parsley-required', 'true');
        $('#form_id').attr('data-parsley-required-message', 'Form Type is required');
     }

     if(wf_object_type == 'document')
     {
        $(".document_row").slideDown();
        $('#document_id').attr('data-parsley-required', 'true');
        $('#document_id').attr('data-parsley-required-message', 'Document Type is required');
     }

       if(wf_object_type == 'form' || wf_object_type == 'document')
       {  
        load_workflow_objects();
       }

   });
  /*####### Workflow Object Type Change event END #####*/


  $(document).on("change",".rule_components",function(e) {
    
        
        var object_type = $(this).attr("data-object_type");
        var object_id = $(this).val();
        var form_url = "@php echo URL('load_rule_components'); @endphp";
        
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var data = {_token: CSRF_TOKEN,"object_type":object_type,"object_id":object_id};
        $.ajax({
            method: "POST",
            url: form_url,
            data: data,
            dataType: 'json',
            success: function (msg) 
            {
              rule_components = msg.rule_components;
              rule_options='<option value="-1" data-column_type="invalid" data-key="invalid" data-object_type="invalid">------</option>'; 
               
              $.each(rule_components, function(i, item)
              {
                /*console.log(item);*/
                var disbld ='';
                if(item.object_type=="invalid"){
                    disbld = 'disabled="disabled"';
                }
                rule_options +='<option value="'+item.id+'" data-column_type="'+item.column_type+'" data-key="'+i+'" data-object_type="'+item.object_type+'" '+disbld+'>'+item.column_name+'</option>';
              });

              $( ".rule_filter").each(function( index )
              {
                  
                  var rc =$(this).attr('data-rc');
                  var selected_filter = $(this).val(); 
                  $('#builder_rule_'+rc+'_filter').html(rule_options);
                  if(rc != '')
                  {
                    $('#builder_rule_'+rc+'_filter').val(selected_filter); 
                  }
                  console.log("Updated filter");
                  

              });

              //console.log("rule_options"+rule_options);
                    
            }
        });
     });


  /*###### add Stage event START ######*/

  $(document).on("click",".add_task",function(e) {
        validation_check=0;
    if($("#closed_workflow_name").parsley().validate())
        {
    
          var wf_type =$(this).attr('data-wf_type'); 
          var label='Stage';
          var empty=[];
          var task= {id: stage_count, dbid: 0, label: label, shape:"box", color: "#c0c0c0", type: wf_type,stype: 'middle', description:'',department_users:empty,sel_departments:empty,sel_department_users:empty,stage_action:2,stage_group:1,stage_percentage:0,escallation_stage:0,escallation_day:0,escallation_activity_id:0}
          addTask(task);
          $( "#wf_li"+stage_count).trigger( "click" );
         stage_count++;
         refresh_options();
         }
         else
         {
            console.log("Validation failed in add task");
        }
     });


    var addTask = function(task) 
    {
        var id=task.id;
        var wfTRHTML =''; 
        var wf_type = task.type;
        var stype = task.stype;
        var dbid = task.dbid;
        if(dbid != 0)
        {
           escallation_option = db_stage_option;
        }
        else
        {
            escallation_option = stage_option;
        }
        
        sel_departments = task.sel_departments;
        sel_department_users = task.sel_department_users;
        department_users = task.department_users;
        escallation_day = task.escallation_day;
        escallation_stage = parseInt(task.escallation_stage);
        escallation_activity_id = parseInt(task.escallation_activity_id);

        var text = "Your request is completed with status - {status}.\nThe attached document in this email contain the contents of the form you submitted. This is confidential information of {company name}. The information is intended only for the use of the individual or entity named above. If you are not the intended recipient, you are hereby notified that any disclosure, copying, distribution or taking of any action in reliance on the contents of this information is strictly prohibited. If you have received this email in error, please immediately notify your contact by email or telephone to arrange for return of the original document to us.\nPlease contact your manager or the administrator if you have any questions.\nThank you";

        var message_content = (typeof task.message_content != 'undefined') ? task.message_content:text;
        if(message_content == '')
        {
          message_content = text;
        }
        var other_user = (typeof task.other_user != 'undefined') ? task.other_user:'';
        var notify_requester = (typeof task.notify_requester != 'undefined') ? task.notify_requester:0;

        var common_data='data-stageid="'+id+'" data-dbid="'+dbid+'" data-stype="'+stype+'"';

        wfTRHTML +='<li class="item wf_li" data-liid="'+id+'" id="wf_li'+id+'" data-wf_type="'+wf_type+'" '+common_data+'>';
        wfTRHTML +='<div class="product-img">';
        wfTRHTML +='<i class="fa fa-list fa-lg"></i>';
        wfTRHTML +='</div>';

    
        wfTRHTML +='<div class="product-info">';
        wfTRHTML +='<a href="javascript:void(0)" class="product-title" id="label'+id+'">'+task.label+'</a>';
        var description_style='readonly="readonly"';
if(stype == 'middle')
    { 
        wfTRHTML +='<span class="pull-right action_span">';
        wfTRHTML +='<i class="fa fa-trash-o fa-lg deleteState" data-liid="'+id+'"></i>';
        wfTRHTML +='</span>';  
        description_style='';
    }           
        wfTRHTML +='</div>';
    

        wfTRHTML +='</li>'; 
    if(stype == 'first')
    {
        first=1;
       // $('.workflow_template').append(wfTRHTML); 
       // $('.workflow_template li:eq(1)').before(wfTRHTML);
        $( ".workflow_template li:nth-child(1)" ).after(wfTRHTML);
    }
    else if(stype == 'last')
    {
        last=1;
        $('.workflow_template').append(wfTRHTML); 
    }
    else
    {
        
        if(last == 0)
        {
           $('.workflow_template').append(wfTRHTML);  
        }
        else
        {
           $('.workflow_template').find(' > li:nth-last-child(1)').before(wfTRHTML); 
        }
        
    }
    

        var wfTLHTML =''; 
        wfTLHTML +='<div class="col-md-12 taskdiv" id="rwf_div'+id+'" style="display:none;">';  
        wfTLHTML +='<h3 class="">Stage Name: '+task.label+'</h3>';

        wfTLHTML +='<div class="form-group">';
        wfTLHTML +='<label>Stage Name</label>';
        wfTLHTML +='<input type="text" class="form-control stage_label_input" id="task_label'+id+'" name="task_label[]" value="'+task.label+'" data-count="'+id+'" data-wf_type="'+wf_type+'">';
        wfTLHTML +='</div>';

        wfTLHTML +='<div class="form-group">';
        wfTLHTML +='<label>Stage Description</label>';
       /* wfTLHTML +='<textarea class="form-control" id="task_details" name="task_details[]">'+task.description+'</textarea>';*/
       wfTLHTML +='<input type="text" class="form-control task_details" id="task_details'+id+'" name="task_details[]" value="'+task.description+'" data-stageid="'+id+'" data-wf_type="'+wf_type+'" '+description_style+'>';
        wfTLHTML +='</div>';

        if(stype != 'last')
        {
        /////////////////////workflow actions///////////////////////
        wfTLHTML +='<div class="form-group">';
        wfTLHTML +='<label>Stage Assignment</label><br/>';
        if(stype == 'middle')
        {
        wfTLHTML +='<input type="radio" checked="checked" class="wf_actions" id="wf_actions2_'+id+'" name="wf_actions'+id+'" value="2" data-stageid="'+id+'"> By Hierarchy &nbsp;&nbsp;&nbsp;';
        wfTLHTML +='<input type="radio" class="wf_actions" id="wf_actions3_'+id+'" name="wf_actions'+id+'" value="3" data-stageid="'+id+'"> By Group &nbsp;&nbsp;&nbsp;';
        wfTLHTML +='<input type="radio" class="wf_actions" id="wf_actions1_'+id+'" name="wf_actions'+id+'" value="1" data-stageid="'+id+'" > By User &nbsp;&nbsp;&nbsp;';
       }
       
        wfTLHTML +='<input type="radio" class="wf_actions" id="wf_actions4_'+id+'" name="wf_actions'+id+'" value="4" data-stageid="'+id+'"> Auto';
        
        wfTLHTML +='</div>';
        }
        /////////////// BY USER HELP TEXT ////////////////
        wfTLHTML +='<p class="help_text_user_'+id+'" id="help1_'+id+'" style="font-size:12px; color:#999; display:none;">Select the users who can take actions at this stage </p>';
        wfTLHTML +='<p class="help_text_user_'+id+'" id="help2_'+id+'" style="font-size:12px; color:#999; display:none;">When this is selected, the manager of the user who submitted a form will get a notification automatically.</p>';
        wfTLHTML +='<p class="help_text_user_'+id+'" id="help3_'+id+'" style="font-size:12px; color:#999; display:none;">Select the group who can take actions at this stage </p>';
        wfTLHTML +='<p class="help_text_user_'+id+'" id="help4_'+id+'" style="font-size:12px; color:#999; display:none;">When this is selected, workflow will move to the next stage depending on the rule.</p>';
        /////////////// BY USER HELP TEXT ////////////////

        wfTLHTML +='<div class="form-group" id="byuser'+id+'">';
        wfTLHTML +='<label>Assigned users <span class="compulsary">*<p class="help">[SA] - Super Admin, [DA] - Department Admin, [RU] - Regular User, [PU] - Private User</p></span></label>';
        wfTLHTML +='<div class="full_widht">';
        wfTLHTML +='<select name="stage_user'+id+'" id="stage_user'+id+'" class="form-control stage_users" multiple="multiple" style="width:100%;" data-stageid="'+id+'">'+stage_users_options+'</select>';
        wfTLHTML +='</div>';
        wfTLHTML +='</div>';

        wfTLHTML +='<div class="form-group" id="bygroup'+id+'" style="display:none;">';

        wfTLHTML +='<label>Assigned Department <span class="compulsary">*</span></label>';
        wfTLHTML +='<div class="full_widht">';
        wfTLHTML +='<select name="stage_dept'+id+'" id="stage_dept'+id+'" class="form-control stage_dept" multiple="multiple" style="width:100%;" data-stageid="'+id+'">'+stage_dept_options+'</select>';
        wfTLHTML +='</div>';

        wfTLHTML +='<label></label>';
        wfTLHTML +='<input type="radio" class="stage_group" id="stage_group1_'+id+'" name="stage_group'+id+'" value="1" data-stageid="'+id+'" checked="checked"> Any One &nbsp;';
        wfTLHTML +='<input type="radio" class="stage_group" id="stage_group2_'+id+'" name="stage_group'+id+'" value="2" data-stageid="'+id+'"> All &nbsp;';
        wfTLHTML +='<input type="radio" class="stage_group" id="stage_group3_'+id+'" name="stage_group'+id+'" value="3" data-stageid="'+id+'"> Percentage &nbsp;';
        wfTLHTML +='</div>';

        /////////////// BY GROUP HELP TEXT ////////////////
        wfTLHTML +='<p class="help_text_group_'+id+'" id="grouphelp1_'+id+'" style="font-size:12px; color:#999; display:none;">When this is selected, any one must be take actions in the above mentioned group</p>';
        wfTLHTML +='<p class="help_text_group_'+id+'" id="grouphelp2_'+id+'" style="font-size:12px; color:#999; display:none;">When this is selected, all users must be take actions at this stage in the above mentioned group</p>';   
        wfTLHTML +='<p class="help_text_group_'+id+'" id="grouphelp3_'+id+'" style="font-size:12px; color:#999; display:none;">When this is selected, the actions will be taken when the percentage given in the text field meet the average clicks </p>';
        /////////////// BY GROUP HELP TEXT ////////////////
 
        wfTLHTML +='<div class="form-group" id="bypercentage'+id+'" style="display:none;">';
        wfTLHTML +='<label>Percentage Value <span class="compulsary">*</span></label>';
        wfTLHTML +='<input type="text" class="form-control stage_percentage"  id="stage_percentage'+id+'" name="stage_percentage'+id+'" data-stageid="'+id+'" value="'+task.stage_percentage+'" >';
        //data-parsley-min="0" data-parsley-max="100" data-parsley-trigger="focusout" data-parsley-type="number"
        wfTLHTML +='</div>';
        /////////////////////workflow actions///////////////////////
        if(stype != 'last')
        {
        //////////////////Escallation////////////////////////////////////
        wfTLHTML +='<div class="form-group" id="escallation_'+id+'">';
        wfTLHTML +='<label>Escalation Rule</label>';
        wfTLHTML +='<p>Move to stage &nbsp;';
        wfTLHTML +='<select name="escallation_stage" id="escallation_stage'+id+'" class="select2 escallation escallation_stage" data-stageid="'+id+'" style="width:25%;background-color: #fff;padding: 6px 12px;border: 1px solid #ccc;">';
        wfTLHTML +=escallation_option;            
        wfTLHTML +='</select>';
        wfTLHTML +='&nbsp;<select name="escallation_activity_id" id="escallation_activity_id'+id+'" class="escallation escallation_activity_id"  data-stageid="'+id+'" style="width:25%;background-color: #fff;padding: 6px 12px;border: 1px solid #ccc;"><option value="0">Select Task</option>';
        wfTLHTML +=wfactivities_options;
        wfTLHTML +='</select>';
        wfTLHTML +=' &nbsp;after &nbsp;';
        wfTLHTML +='<input type="number" name="escallation_day" id="escallation_day'+id+'" class="escallation" placeholder="" value="0" data-stageid="'+id+'" style="width:10%;background-color: #fff;padding: 6px 12px;border: 1px solid #ccc;" min="0">';

        wfTLHTML +=' &nbsp;days of inaction</p>';
        wfTLHTML +='<p class="help">You can set escalation rules here. The document will move to the selected stage if no action was taken within the specified days. No escalation will be done if number of days set to 0 or no stage is selected</p>';
        wfTLHTML +='</div>';
        ///////////////////////////End escallation/////////////////////


        wfTLHTML +='<div class="form-group" >';
        wfTLHTML +='<label>Stage Actions</label>';
        wfTLHTML +='<div class="nav-tabs-custom">';
        wfTLHTML +='<ul class="nav nav-tabs" id="tab_stage_action_template'+id+'">';
        wfTLHTML +='<li class="pull-right"><button type="button" class="btn btn-primary btn-sm add_action" data-wf_type="edge" data-stage="'+id+'" id="add_action'+id+'"><i class="fa fa-plus"></i> Add Action</button></li>';
        wfTLHTML +='</ul>';
        wfTLHTML +='<div class="tab-content" id="stage_action_template'+id+'">';

        wfTLHTML +='</div>'; /*tab-content*/
        wfTLHTML +='</div>'; /*End nav-tabs-custom*/
        wfTLHTML +='</div>'; /*End Form Group*/
         }

         if(stype == 'last')
        {

         /*****/////////////////Notify Customer///////////////////////////////////****/
        wfTLHTML +='<div class="form-group" id="">';
       
        wfTLHTML +='<div class="row">';
        
        wfTLHTML +='<div class="col-md-12" id="">';
        wfTLHTML +='<label>Notify following users at the completion of workflow</label>';
        wfTLHTML +='</div>';
        var notify_requester_checked = (notify_requester == 1)?'checked="checked"':'';
       wfTLHTML +='<div class="col-md-12" id="">';
        wfTLHTML +='<input type="checkbox" '+notify_requester_checked+' class="" id="notify_requester'+id+'" name="notify_requester'+id+'" value="1"> Notify original requester';

        var requester_attachment = (typeof task.requester_attachment != 'undefined') ? task.requester_attachment:0;
        var requester_attachment_checked = (requester_attachment == 1)?'checked="checked"':'';
        wfTLHTML +='&nbsp;&nbsp;<input type="checkbox" '+requester_attachment_checked+' class="" id="requester_attachment'+id+'" name="requester_attachment'+id+'" value="1"> Include all attachments in the form';
        wfTLHTML +='</div>';

        wfTLHTML +='<div class="col-md-12" id="">';
        var user_attachment = (typeof task.user_attachment != 'undefined') ? task.user_attachment:0;
        var user_attachment_checked = (user_attachment == 1)?'checked="checked"':'';  
        wfTLHTML +='<label>User(s) </label> &nbsp;&nbsp;<input type="checkbox" '+user_attachment_checked+' class="" id="user_attachment'+id+'" name="user_attachment'+id+'" value="1"> Include all attachments in the form';
        /*wfTLHTML +='<label>User(s) </label>';*/

        wfTLHTML +='<select name="notify_user'+id+'[]" id="notify_user'+id+'" class="form-control notify_user" multiple="multiple" style="width:100%;" data-stageid="'+id+'">'+stage_users_options+'</select>';
        
        
        wfTLHTML +='</div>';

        wfTLHTML +='<div class="col-md-12" id="">';
        /*wfTLHTML +='<label>Department(s)</label>';*/
        var department_attachment = (typeof task.department_attachment != 'undefined') ? task.department_attachment:0;
        var department_attachment_checked = (department_attachment == 1)?'checked="checked"':''; 
        wfTLHTML +='<label>Department(s) </label> &nbsp;&nbsp;<input type="checkbox" '+department_attachment_checked+' class="" id="department_attachment'+id+'" name="department_attachment'+id+'" value="1"> Include all attachments in the form';
        wfTLHTML +='<select name="notify_dep'+id+'[]" id="notify_dep'+id+'" class="form-control notify_dep" multiple="multiple" style="width:100%;" data-stageid="'+id+'">'+stage_dept_options+'</select>';
         wfTLHTML +='<p class="help">All the users in the selected department(s) notified</p>';
        wfTLHTML +='</div>';

        wfTLHTML +='<div class="col-md-12" id="">';
        /*wfTLHTML +='<label>External Email Address(es)</label>';*/
         var external_attachment = (typeof task.external_attachment != 'undefined') ? task.external_attachment:0;
        var external_attachment_checked = (external_attachment == 1)?'checked="checked"':''; 
        wfTLHTML +='<label>External Email Address(es) </label> &nbsp;&nbsp;<input type="checkbox" '+external_attachment_checked+' class="" id="external_attachment'+id+'" name="external_attachment'+id+'" value="1"> Include all attachments in the form';
        wfTLHTML +='<input type="text"  class="form-control" id="notify_others'+id+'" name="notify_others'+id+'" value="" data-parsley-emailcheck="">';
        wfTLHTML +='<p class="help">Use comma(,) to separate multiple email addresses</p>';
        wfTLHTML +='</div>';
        
        wfTLHTML +='<div class="col-md-12" id="">';
        wfTLHTML +='<label>Message Content</label>';
        wfTLHTML +='<textarea type="text"  class="form-control" id="notify_message'+id+'" name="notify_message'+id+'">'+message_content+'</textarea>';
        wfTLHTML +='</div>';

        


        
        wfTLHTML +='</div>'; /*End row*/

        wfTLHTML +='</div>'; /*End form Group*/
        /*****/////////////////End Notify Customer///////////////////////////////////****/

       
         }
        wfTLHTML +='</div>';
        $('.right_wft_col').append(wfTLHTML); 
        stage_action = task.stage_action;
        stage_group = task.stage_group;

        $('#stage_user'+id).fastselect({
            placeholder: 'Select users',
            loadOnce: false
        });

        $('#stage_dept'+id).fastselect({
            placeholder: 'Select departments',
            loadOnce: false
        });

        if(stype == 'last')
        {
           $('#notify_user'+id).fastselect({
            placeholder: 'Select users',
            loadOnce: false
        });

            $('#notify_dep'+id).fastselect({
            placeholder: 'Select departments',
            loadOnce: false
        });
        }

        

        /*nodes.add({
            id: id,
            dbid:dbid,
            label: task.label,
            description: task.description,
            shape: 'box',
            color:task.color,
            stype:stype,
            stage_action:stage_action,
            stage_group:stage_group,
            stage_percentage:task.stage_percentage,
            sel_departments:sel_departments,
            sel_department_users:sel_department_users,
            escallation_stage:escallation_stage,
            escallation_day:escallation_day,
            escallation_activity_id:escallation_activity_id

        });*/
        

        if(stype != 'last')
        {
          $.each(sel_department_users, function(i, item)
        {
           
            if(item !='')
            {
            if($('#stage_user'+id+' option[value='+item+']').length > 0)
           {     
            $('#stage_user'+id).data('fastselect').setSelectedOption($('#stage_user'+id+' option[value='+item+']').get(0));
            $("#stage_user"+id).val(item);  
           }
            }
        });

        $.each(sel_departments, function(i, item)
        {
            
            if(item !='')
            {
           if($('#stage_dept'+id+' option[value='+item+']').length > 0)
           {    
            $('#stage_dept'+id).data('fastselect').setSelectedOption($('#stage_dept'+id+' option[value='+item+']').get(0));
            $("#stage_dept"+id).val(item);  
           }
          }
        });

          console.log("stage_id"+id+", escallation_stage"+escallation_stage);
         $("#wf_actions"+stage_action+"_"+id).prop("checked", true);
          $("#stage_group"+stage_group+"_"+id).prop("checked", true);
         $("#escallation_day"+id).val(escallation_day); 
          $('#escallation_stage'+id).val(escallation_stage);
          $('#escallation_activity_id'+id).val(escallation_activity_id);
        }
         else if(stype == 'last')
        {
          
         $("#notify_others"+id).val(other_user); 
          $('#notify_message'+id).val(message_content);
           $.each(sel_department_users, function(i, item)
        {
           
            if(item !='')
            {
            if($('#notify_user'+id+' option[value='+item+']').length > 0)
           {     
            $('#notify_user'+id).data('fastselect').setSelectedOption($('#notify_user'+id+' option[value='+item+']').get(0));
            $("#notify_user"+id).val(item);  
           }
            }
        });

            $.each(sel_departments, function(i, item)
        {
            
            if(item !='')
            {
           if($('#notify_dep'+id+' option[value='+item+']').length > 0)
           {    
            $('#notify_dep'+id).data('fastselect').setSelectedOption($('#notify_dep'+id+' option[value='+item+']').get(0));
            $("#notify_dep"+id).val(item);  
           }
          }
        });
        }
        
        change_stage_action(id);
        $("#tab_stage_action_template"+id).sortable({
});  
        
        //change_stage_group(id);
        return wfTLHTML;
    }
  /*###### add Stage event END ######*/


  /*############ ADD ACTION START  ########## */

   $(document).on("click",".add_action",function(e) {
    var wf_type ='edge'; 
    var from_id =parseInt($(this).attr('data-stage')); 
      var label='Action';
      var task= {id: action_count, dbid: 0, label: label, type: wf_type, from: from_id, to: 0, description:'',with_rule:0,rules_basic:rules_basic_base,rule_data:[],activity_id:0}
        addAction(task);
        action_count++;

        
     });

   var addAction = function(task) 
   {
          
      var id=task.id;
      var action_html =''; 
      var wf_type = task.type;
      var dbid = task.dbid;
      var from_id =task.from;
      var to_id =task.to;
      var with_rule = parseInt(task.with_rule);
      var rules_basic = task.rules_basic;
      var rule_data = task.rule_data;
      var activity_id = task.activity_id;
       if(dbid != 0)
        {
           temp_stage_option = db_stage_option;
        }
        else
        {
            temp_stage_option = stage_option;
        }
      /*$('.collapse'+from_id).collapse('hide');*/
      var selectVal = parseInt($("input[name='wf_actions"+from_id+"']:checked").val());
      var common_data=' data-stageid="'+from_id+'" data-edgeid="'+id+'" data-dbid="'+dbid+'"';
      //var stage_option = '<option value="0">Select Stage</option>';
      
      var action_html ='';
      action_html +='<li id="action_li'+id+'" class="action_li"  '+common_data+'><a href="#collapse'+id+'" id="action_div_'+id+'" data-toggle="tab"><span id="task_heading'+id+'">'+task.label+'</span> &nbsp; <button class="close deleteAction" type="button" title="Remove Action" data-satge_id="'+from_id+'" data-edgeid="'+id+'">×</button> </a></li>';

      $('#tab_stage_action_template'+from_id).append(action_html);

      var action_html ='';

          action_html +='<div id="collapse'+id+'" class="tab-pane collapse'+from_id+' collapse  active">';
         /* action_html +='<div class="box-body">';*/
          /* row START */
          action_html +='<div class="row">';
          action_html +='<div class="col-md-6" id="">'; 
          action_html +='<label>Action Name</label>';
          action_html +='<select name="activity_id'+id+'" id="activity_id'+id+'" class="form-control task_label_input task_label_input_'+from_id+'" '+common_data+' data-wf_type="'+wf_type+'">'+wfactivities_options+'</select>';
          action_html +='</div>';

          action_html +='<div class="col-md-6" id="">';           
          action_html +='<label>Rule</label>';
          action_html +='<select name="rule_'+id+'" id="rule_'+id+'" class="form-control rule_change rule_change_'+from_id+'" '+common_data+'>';

          var selected='';
          var rule_style='display:none';
          /*if(with_rule == 0 || selectVal == 2 || selectVal == 3)*/
          if(with_rule == 0)  
          {
          selected='selected="selected"';
          rule_style='display:none';
          }
          action_html +='<option value="0" '+selected+'>Without Rule</option>';

          var selected='';
         /* if(with_rule == 1 && (selectVal == 1 || selectVal == 4))*/
          if(with_rule == 1)
          {
          selected='selected="selected"';
          rule_style='display:block';
          }

          /*if(selectVal == 2 || selectVal == 3)
          {
         
          selected='selected="selected"';
          }*/

        action_html +='<option value="1" '+selected+'>With Rule</option>';
        action_html +='</select>';
        action_html +='</div>';

        action_html +='</div>';
                    /* row END 

        /*############## Rule row START #################*/
        
        /*################ Rule row END #################*/

        /* row START */
        action_html +='<div class="row">';
          
        action_html +='<div class="col-md-6" >'; 
        action_html +='<label id="rule_label'+id+'">To Stage:<span class="compulsary">*</span></label>';
        action_html +='<input type="hidden" name="from_satge" id="from_satge'+id+'">'; 
        action_html +='<select name="to_satge" id="to_satge'+id+'" class="form-control select2 stages_option stages_option'+from_id+'" '+common_data+'>';
        action_html +=temp_stage_option;            
        action_html +='</select>';
        action_html +='</div>';
        action_html +='</div>';
        /* row END */

        /* Rule row START */            
        action_html +='<div class="row">';            
        action_html +='<div class="col-md-12" id="ruletable'+id+'" style="'+rule_style+'">';  

        action_html +='<div id="builder'+id+'" class="query-builder form-inline"></div>';

        action_html+='<div class="form-group">';
    action_html+='<button type="button" class="btn btn-xs btn-success pull-right add_more_rule" id="add_more_rule'+id+'" data-add="group"  data-stageid="'+from_id+'" data-edgeid="'+id+'" data-level="0" data-parentid="0"><i class="fa fa-plus"></i> Add More Rule</button>';
     action_html+='</div>';
        
        action_html +='</div>';
        action_html +='</div>';          
         /* Rule row END */

        /*action_html +='</div>';*/
        action_html +='</div>';
        action_html +='</div>';

      $('#stage_action_template'+from_id).append(action_html);

      if(with_rule == 1)  
      {
        $('#ruletable'+id).show();
        $('#to_satge'+id).hide();
        $('#rule_label'+id).hide();
        var stage_action_rules = task.stage_action_rules;
        $.each(stage_action_rules, function(j, items)
        {
           console.log(items);
           $('#add_more_rule'+id).trigger('click');
           var rc = rule_count;
          $("#rule_stages_option1_"+rc).val(items.if_stage).trigger('change');
           
          $("#rule_stages_option2_"+rc).val(items.else_stage).trigger('change');

          if(items.enable_else == 1)
          {
            $("#check_else_"+rc).trigger('click');
          }
           console.log("......ITEM S.......");
           $.each(items.rule_data, function(i, item) {
        
        if(i ==0)
        {
          myrcArray[item.rc]=rule_count;
        }
        var first = 0;
        
        
        if(item.type == 'group')
        {
          if(i !=0)
          {

            var kll = item.parentid;
            var parenID = myrcArray[kll];
          
          $("#add_group_template_"+parenID).trigger('click');
          }

          $("#builder_group_"+rule_count+"_cond").val(item.condition).trigger('change');
          $("#builder_group_stage"+rule_count+"_cond").val(item.stage_case).trigger('change');
          if(item.parentid == 0)
          {
         /* $("#rule_stages_option1_"+rule_count).val(item.if_stage).trigger('change');
           
          $("#rule_stages_option2_"+rule_count).val(item.else_stage).trigger('change');*/
          }
          
        }
        else
        {
          if(i !=0)
          {

          var kll = item.parentid;
            var parenID = myrcArray[kll];
          
          $("#add_rule_template_"+parenID).trigger('click');

          $("#builder_rule_"+rule_count+"_filter").val(item.id).trigger('change');
          $("#builder_rule_"+rule_count+"_operator").val(item.operator).trigger('change');

          var column_type = $("#builder_rule_"+rule_count+"_filter").find(':selected').attr('data-column_type');
          

         if(column_type == 'radio' || column_type == 'checkbox')
          {
            $(".builder_rule_value_"+rule_count).each(function( index )
              {
                var rcv =$(this).val();
                if(rcv == item.value)
                {
                    $(this).prop("checked", true);
                }
                
              });
          }
          if(column_type == 'date')
          {
            
                if('today' == item.value)
                {
                    $("#builder_rule_value_today_"+rule_count).prop("checked", true);
                }
                else
                {
                    $("#builder_rule_value_"+rule_count).val(item.value).trigger('change');
                }

        var ckb = $("#builder_rule_value_today_"+rule_count).is(':checked');
        if(ckb == false) 
        {
            $("#builder_rule_value_"+rule_count).attr("disabled", false);
        } else 
        {
            $("#builder_rule_value_"+rule_count).attr("disabled", true);
        }
                
          }
          else
          {
            $("#builder_rule_value_"+rule_count).val(item.value).trigger('change');
          }
          

          }
          
       }

        if(i !=0)
        {
          myrcArray[item.rc]=rule_count;
        }

      });
           console.log("......ITEM E.......");
        });
      }
      
      $('#action_div_'+id).trigger('click');
      $('#to_satge'+id).val(to_id);

      if(activity_id>0)
      {
        
        $('#activity_id'+id).val(activity_id);
      }
      else
      {
       // activity_id = 
      }
      

      var label = $('#activity_id'+id+' option:selected').text();

      $("#task_heading"+id).text(label); 
     
      return true;
   }



   $(document).on("click",".add_more_rule",function(e) {
        var stageid = $(this).attr("data-stageid");
        var edgeid = $(this).attr("data-edgeid");
        rg_count++;;
        var rule_arry = {"stageid":stageid,"edgeid":edgeid,"first":1,"parentid":0,"level":0,stage_case:'else_if',stage_dbid:0,rgcount:rg_count};
         var temp = AddGroupTemplate(rule_arry);
         $('#builder'+edgeid).append(temp);

    });


    $(document).on("change",".rule_change",function(e) {
        var with_rule = parseInt($(this).val());
        var stageid = $(this).attr('data-stageid'); 
        var edgeid = $(this).attr('data-edgeid'); 
        if(with_rule == 1)
        {
          $('#ruletable'+edgeid).show();
          $('#to_satge'+edgeid).hide();
          $('#rule_label'+edgeid).hide();
        }
        else
        {
          $('#ruletable'+edgeid).hide();
          $('#to_satge'+edgeid).show();
          $('#rule_label'+edgeid).show();
        } 

         
        if(edgeid != 0)
        {
                  
                  
        }

        if(with_rule == 1)
        {
        rg_count++;
        var rule_arry = {"stageid":stageid,"edgeid":edgeid,"first":1,"parentid":0,"level":0,stage_case:'if_else',stage_dbid:0,rgcount:rg_count};
        console.log("rg_count"+rg_count);
         var temp = AddGroupTemplate(rule_arry);
         $('#builder'+edgeid).append(temp);
         $("#add_more_rule"+edgeid).show();
       }
       else
       {
         $('#builder'+edgeid).empty();
         console.log("Cleared");

       }
        
     });

     $(document).on("click",".check_else",function(e) {
        var ckb = $(this).is(':checked');
        var stageid = $(this).attr("data-stageid");
        var edgeid = $(this).attr("data-edgeid");
        var rc = $(this).attr("data-rc");
        console.log("ckb"+ckb);
        if(ckb == true)
        {
          $("#rule_stages_option2_"+rc).attr("disabled", false);
          $("#add_more_rule"+edgeid).hide(); /*Hide add more*/

        }
        else
        {
          $("#rule_stages_option2_"+rc).val(0);
          $("#rule_stages_option2_"+rc).attr("disabled", true);
          $("#add_more_rule"+edgeid).show(); /*Show add more*/
        }

    }); 

  /*############ ADD ACTION END ########## */


  /*############### GROUP TEMPLATE START######### */
  var AddGroupTemplate = function(rule_arry) 
   {
    
    var stageid = rule_arry.stageid;
    var edgeid = rule_arry.edgeid;
    var first = rule_arry.first;
    var parentid = rule_arry.parentid;
    var level = rule_arry.level;
    var nextlevel = parseInt(level)+1;

    /*var rules_basic = rule_arry.rules_basic;*/
    var stage_case = rule_arry.stage_case;
    var stage_dbid = rule_arry.stage_dbid;
    /*console.log("stage_dbid"+stage_dbid);*/
    var rgcount = rule_arry.rgcount;

    rule_count++;
    /*var json_type = {}; 
    setTOJson(json_type);*/
    var data_attr2 = 'data-stageid="'+stageid+'" data-edgeid="'+edgeid+'" data-level="'+level+'" data-parentid="'+parentid+'" data-rgcount="'+rgcount+'" data-addtype="group"';

    var data_attr1 = 'data-rc="'+rule_count+'" ';

    html='<dl id="builder_group_'+rule_count+'" class="rules-group-container">';
    html+='<dt class="rules-group-header">';

/*group-actions START*/
    html+='<div class="btn-group pull-right group-actions">';
    html+='<button type="button" style="margin-right:3px;" class="btn btn-xs btn-success add_rule_template" id="add_rule_template_'+rule_count+'" data-add="rule" '+data_attr1+' '+data_attr2+'><i class="fa fa-plus"></i> Add Rule </button>';
    if(first != 0)
    {
    html+='<button type="button" style="margin-right:3px;" class="btn btn-xs btn-success add_rule_template" id="add_group_template_'+rule_count+'" data-add="group" '+data_attr1+' '+data_attr2+'><i class="fa fa-plus"></i> Add Group</button>';
    }
    /*if(first == 0)
    {*/
      html+='<button type="button" class="btn btn-xs btn-danger delete_rule_template" data-delete="group" '+data_attr1+' '+data_attr2+'><i class="fa fa-trash"></i></button>';
    /*}*/
    html+='</div>';
/*group-actions END*/

    /*rule-filter-container Strt */
    html+='<div class="rule-filter-container">';
    
        html+='<select class="form-control group_filter_update rule_filtere_'+edgeid+'" name="builder_group_'+rule_count+'_cond" id="builder_group_'+rule_count+'_cond" '+data_attr1+' '+data_attr2+'><option value="AND">AND</option><option value="OR">OR</option></select>';

    if(parentid == 0)
    {    
   selected = '';
    if(stage_case == '' || stage_case == 0)
    {
      selected='selected="selected"';
    }    
    html+='<select class="form-control group_stage_case_update" name="builder_group_stage'+rule_count+'_cond" id="builder_group_stage'+rule_count+'_cond" '+data_attr1+' '+data_attr2+' style="margin-left:5px;">';
    selected = '';
    if(stage_case == 'if')
    {
      selected='selected="selected"';
    }
    
    html+='<option value="if" '+selected+'>IF</option>';
   /* selected = '';
    if(stage_case == 'if_else')
    {
      selected='selected="selected"';
    }
    html+='<option value="if_else" '+selected+'>IF & ELSE </option>';*/

    selected = '';
    if(stage_case == 'else_if')
    {
      selected='selected="selected"';
    }
    html+='<option value="else_if" '+selected+'>ELSE IF</option>';
    html+='</select>';
      }  
    html+='</div>';
    /*rule-filter-container end */
    html+='<div class="error-container" data-toggle="tooltip"><i class="fa fa-warning"></i></div>';
    html+='</dt>';
    html+='<dd class="rules-group-body rule_group_'+stageid+'_'+level+'" id="dd_rule_group_'+rule_count+'">';
    html+='<ul class="rules-list" id="ul_rule_group_'+rule_count+'"></ul>';
if(parentid == 0)
    {
    if_style="display:block";
    else_style="display:block";
   

    html+='<div class="row">';

    html+='<div class="col-xs-4">';
    html+='<div class="form-group rule_group_stage_'+rule_count+'" id="rule_group_if_stage_'+rule_count+'" style="'+if_style+'">';
    html+='<label for="exampleInputEmail1">Go to stage </label>';
    html+='<br>';
    html+='<select class="form-control rule_stage_case c_rule_stages_option1_'+stageid+'" id="rule_stages_option1_'+rule_count+'"  name="" '+data_attr1+' '+data_attr2+'>'+temp_stage_option+'</select>';
    html+='</div>';
    html+='</div>';

    html+='<div class="col-xs-4">';
    html+='<div class="form-group rule_group_stage_'+rule_count+'" id="rule_group_if_else_stage_'+rule_count+'" style="'+else_style+'">';
    html+='<label for="exampleInputEmail1"><input type="checkbox" name="check_else" class="check_else" id="check_else_'+rule_count+'" value="1" '+data_attr1+' '+data_attr2+'> Else go to the stage </label>';
    html+='<br>';
    html+='<select class="form-control rule_stage_case c_rule_stages_option2_'+stageid+'" id="rule_stages_option2_'+rule_count+'" name="" '+data_attr1+' '+data_attr2+' disabled>'+temp_stage_option+'</select>';
    html+='</div>';
    html+='</div>';
}
    

    html+='</div>';
    html+='</dd>';
    html+='</dl>';
    
    return html;
   };


   $(document).on("click",".add_rule_template",function(e) {
       /*console.log("hi");*/
       var rc =$(this).attr('data-rc');
       var addtype =$(this).attr('data-add');
       var stageid = $(this).attr('data-stageid');
       var edgeid = $(this).attr('data-edgeid');
       var level = $(this).attr('data-level');
       var parentid = $(this).attr('data-parentid');
       var parentid = rc;
       /*var rgcount =rg_count;*/
       var rgcount =$(this).attr('data-rgcount');
        
       var nextlevel = parseInt(level)+1;
       var rule_arry = {"stageid":stageid,"edgeid":edgeid,"parentid":rc,"first":0,"level":nextlevel,stage_dbid:0,rgcount:rgcount}
       var temp ='';
       if(addtype == 'rule')
       {
        /*rule_arry.level=level;*/
        rule_arry.rules_basic = rules_basic_base;
        var temp = AddRuleTemplate(rule_arry);

       }
       else if(addtype == 'group')
       {
        rule_arry.rules_basic = rules_basic_base;
        rule_arry.stage_case = '';
        var temp = AddGroupTemplate(rule_arry);
       }
       
       $('#ul_rule_group_'+rc).append(temp);  
       
       
     });

   $(document).on("click",".delete_rule_template",function(e) {
       /*console.log("hi");*/
       var rc =$(this).attr('data-rc');
       var delete_type =$(this).attr('data-delete');
       var stageid = $(this).attr('data-stageid');
       var edgeid = $(this).attr('data-edgeid');
       var parentid = $(this).attr('data-parentid');
       var rule_arry = {"stageid":stageid,"edgeid":edgeid,"parentid":parentid,"first":0}
       var temp ='';
       if(delete_type == 'rule')
       {
        $('#builder_rule_'+rc).remove();  
       }
       else if(delete_type == 'group')
       {
        $('#builder_group_'+rc).remove();  
       }
     });
  /*############### GROUP TEMPLATE END #########*/


  /*################ GROUP RULE START ########*/

  var AddRuleTemplate = function(rule_arry) 
   {
    var stageid = rule_arry.stageid;
    var edgeid = rule_arry.edgeid;
    var parentid = rule_arry.parentid;
    var rgcount=rule_arry.rgcount;
    rule_count++;
    var data_attr2 = 'data-stageid="'+stageid+'" data-edgeid="'+edgeid+'" data-addtype="rule"';

    var data_attr1 = 'data-rc="'+rule_count+'" data-parentid="'+parentid+'" data-rgcount="'+rgcount+'" ';

    html='<li id="builder_rule_'+rule_count+'" class="rule-container">';
    html+='<div class="rule-header">';
        html+='<div class="btn-group pull-right rule-actions">';
            html+='<button type="button" class="btn btn-xs btn-danger delete_rule_template" data-delete="rule" '+data_attr1+' '+data_attr2+'><i class="fa fa-trash"></i></button>';
        html+='</div>';
    html+='</div>';
    html+='<div class="error-container"><i class="fa fa-warning-sign"></i></div>';
    html+='<div class="rule-filter-container">';
        html+='<select class="form-control rule_filter rule_filtere_'+edgeid+'" id="builder_rule_'+rule_count+'_filter" name="builder_rule_'+rule_count+'_filter" '+data_attr1+'" '+data_attr2+'>'+rule_options+'</select>';
    html+='</div>';
    html+='<div class="rule-operator-container" id="rule-operator-container_'+rule_count+'">';
          html+='<select class="form-control rule_filter_update" name="builder_rule_'+rule_count+'_operator" id="builder_rule_'+rule_count+'_operator" '+data_attr1+'" '+data_attr2+'>'+operator_options+'</select>';
    html+='</div>';
    html+='<div class="rule-value-container" id="rule-value-container_'+rule_count+'"></div>';
  
    html+='</li>';

    return html;
   };

  /*################ GROUP RULE END ##########*/


  /*Left Transition LI Click START*/

  $(document).on("click",".wf_li",function(e) 
  {
       validation_check=0;
       var liid     = $(this).attr('data-liid'); //Get Stage id
       var wf_type  =  $(this).attr('data-wf_type'); 
       $(".taskdiv").hide(); /* Hide All Other Right DIV */
       $("#rwf_div"+liid).show(); /* Show Only Current Right DIV */
        $(".wf_li").removeClass("li_active");
        $(this).addClass("li_active");
        var from_satge = liid; 
        
     });
 /*Left Transition LI Click END*/

/* #####START - Change Stage Assignment,By Hierarchy-2,By Group-3, By User-1 , AUTO-4 ####### */
 $(document).on("change",".wf_actions",function(e) {
        var stageid =$(this).attr('data-stageid');      
        change_stage_action(stageid);
    });
 var change_stage_action = function(stageid) 
   {
    var selectVal = $("input[name='wf_actions"+stageid+"']:checked").val();
        $(".help_text_user_"+stageid).hide();
        $(".help_text_group_"+stageid).hide();
        //$("#escallation_"+stageid).hide();
        
        $('#stage_user'+stageid).attr('data-parsley-required', 'false');
        $('#stage_dept'+stageid).attr('data-parsley-required', 'false');
        $('#stage_percentage'+stageid).attr('data-parsley-required', 'false');
        if(selectVal==1){
            $("#byuser"+stageid).show();
            $("#bygroup"+stageid).hide();
            $("#bypercentage"+stageid).hide();
            $("#escallation_"+stageid).show();      
            //set required attribute on input to true
            $('#stage_user'+stageid).attr('data-parsley-required', 'true');
            $('#stage_user'+stageid).attr('data-parsley-required-message','Please select users');
        }else if(selectVal==2){
            $("#bygroup"+stageid).hide();
            $("#byuser"+stageid).hide();
            $("#bypercentage"+stageid).hide();
            $("#escallation_"+stageid).show();
        }else if(selectVal==3){
            $("#bygroup"+stageid).show();
            $("#byuser"+stageid).hide();
            $("#bypercentage"+stageid).hide();
            $("#grouphelp"+selectVal+"_"+stageid).show();
            $("#escallation_"+stageid).show();  
            change_stage_group(stageid);
            $('#stage_dept'+stageid).attr('data-parsley-required', 'true');
            $('#stage_dept'+stageid).attr('data-parsley-required-message','Please select departments');
            $('#stage_percentage'+stageid).attr('data-parsley-required', 'true');
            $('#stage_percentage'+stageid).attr('data-parsley-required-message','Please enter percentage value');
        }else{
            $("#bygroup"+stageid).hide();
            $("#byuser"+stageid).hide();
            $("#bypercentage"+stageid).hide();
            $("#escallation_"+stageid).hide();
        }



        $("#help"+selectVal+"_"+stageid).show();
        /*nodes.update({
            id: stageid,
            stage_action: selectVal
        });*/
        
        
   }


   $(document).on("change",".stage_group",function(e) {
        var stageid =$(this).attr('data-stageid');      
        change_stage_group(stageid);
    });

   var refresh_options = function()
   {
        var test_option = '<option value="0" selected>Select</option>';
        $(".wf_li").each(function( index )
        {
           var stype =$(this).attr('data-stype');
           var stageid = parseInt($(this).attr('data-stageid'));
           var stage_label = $('#task_label'+stageid).val();;
            if(stype != 'first' && stageid > 0)
            {
              test_option += '<option value="' + stageid + '">' + stage_label + '</option>'; 
            }
        });
        stage_option = test_option;

         $(".stages_option").each(function( index )
        {
            var edgeid =$(this).attr('data-edgeid');
            var to_satge = $(this).val(); 
            var rule = $("#rule_"+edgeid).val(); 
            $('#to_satge'+edgeid).html(stage_option);
            $("#to_satge"+edgeid).val(to_satge); 
        });


        $(".escallation_stage").each(function( index )
        {
            var stageid =$(this).attr('data-stageid');
            var to_satge = $(this).val(); 
            $('#escallation_stage'+stageid).html(stage_option);
            $("#escallation_stage"+stageid).val(to_satge); 
        });

        console.log("refresh_options");
   }

  

   


   var change_stage_group = function(stageid) 
   {
    console.log("change_stage_group");
    $(".help_text_group_"+stageid).hide();
    var selectVal = $("input[name='stage_group"+stageid+"']:checked").val();
        if(selectVal==1){
            $("#bypercentage"+stageid).hide();
        }else if(selectVal==2){
            $("#bypercentage"+stageid).hide();
        }else if(selectVal==3){
            $("#bypercentage"+stageid).show();
        }
        $("#grouphelp"+selectVal+"_"+stageid).show();   
        /*nodes.update({
            id: stageid,
            stage_group: selectVal,
        });*/
    }
/* #######END - Change Stage Assignment ###### */

  window.Parsley.on('field:error', function() {

    console.log('Validation failed for: ', this.$element.attr('name'));
    if(validation_check == 0)
    {
       validation_check++;
        data_stageid = this.$element.attr('data-stageid');
        if(typeof data_stageid != "undefined") 
        {
            
            if(data_stageid == 0)
            {
                data_area = this.$element.attr('data-wf-area');
                if(data_area=="permission"){
                    data_operation = this.$element.attr('data-wf-operation');
                    $( "#"+data_operation+"tab").trigger( "click" );
                }
                $( "#wf_title_main").trigger( "click" );
            }
            else
            {
              $("#wf_li"+data_stageid).trigger( "click" );  
            }
     

        }

    }    
    
    });


    $(document).on("click",".save_workflow",function(e) {
        e.preventDefault();
        
        validation_check=0;
        $('.alert_space').html('');
        if($("#closed_workflow_name").parsley().validate())
        {
          $(".preloader").show();
          $(".action_button").attr("disabled", true);

          var CSRF_TOKEN  = $('meta[name="csrf-token"]').attr('content');
          var form_url    = "@php echo URL('save_closed_workflow'); @endphp";
          var workflow_id = $('#workflow_id').val();

          var stages = []; /*Create Stages array*/
           $( ".wf_li").each(function( index )
              {
                  stage_items = {};
                  var stageid =parseInt($(this).attr('data-stageid'));
                  if(stageid > 0)
                  {
                    console.log("stageid"+stageid);
                    var dbid =parseInt($(this).attr('data-dbid'));
                    var stype =$(this).attr('data-stype');
                    stage_items.id = stageid;
                    stage_items.dbid = dbid;
                    stage_items.label = $("#task_label"+stageid).val();
                    stage_items.description = $("#task_details"+stageid).val();
                    
                    stage_items.stage_action = 4; /*Default Auto*/
                    stage_items.escallation_stage =0;
                    stage_items.escallation_activity_id =0;
                    stage_items.escallation_day =0;
                    stage_items.stage_group =0;
                    stage_items.stage_percentage =0;
                    stage_items.shape ='box';
                    stage_items.stype =stype;
                    if(stype == 'middle')
                    {

                      var stage_action = $("input[name='wf_actions"+stageid+"']:checked").val();
                      stage_items.stage_action = parseInt(stage_action);
                      if(stage_items.stage_action == 3) /*By Group*/
                      {
                        var stage_group = $("input[name='stage_group"+stageid+"']:checked").val();
                        stage_items.stage_group = parseInt(stage_group);
                        if(stage_items.stage_group == 3)
                        {
                         stage_items.stage_percentage = $("#stage_percentage"+stageid).val();
                        }
                        stage_items.stage_departments = $("#stage_dept"+stageid).val();
                      }

                      if(stage_items.stage_action == 1) /*By User*/
                      {
                        stage_items.stage_user = $("#stage_user"+stageid).val();
                      }

                      stage_items.escallation_stage = $("#escallation_stage"+stageid).val();
                      stage_items.escallation_activity_id = $("#escallation_activity_id"+stageid).val();
                      stage_items.escallation_day = $("#escallation_day"+stageid).val();
                    }
                    else if(stype == 'last')
                    {
                      var notify_requester = $("#notify_requester"+stageid).is(":checked");
                      stage_items.notify_requester = notify_requester;
                      console.log("notify_requester = "+notify_requester);

                      stage_items.notify_others = $("#notify_others"+stageid).val();

                      stage_items.notify_message = $("#notify_message"+stageid).val();

                      stage_items.notify_user = $("#notify_user"+stageid).val();
                      stage_items.notify_dep = $("#notify_dep"+stageid).val();
                      stage_items.requester_attachment = $("#requester_attachment"+stageid).is(":checked");
                      stage_items.user_attachment = $("#user_attachment"+stageid).is(":checked");
                      stage_items.department_attachment = $("#department_attachment"+stageid).is(":checked");
                      stage_items.external_attachment = $("#external_attachment"+stageid).is(":checked");


                    }
                    else
                    {

                    }

                    stages.push(stage_items);
                  }
                  

              });
           
            $.each(stages, function(i, item) 
            {
              console.log(item);
            });

            console.log("----------action_li Start------------");
            var actions = []; /*Create Stages array*/
           $( ".action_li").each(function( index )
              {
                  action_items = {};
                  var stageid =parseInt($(this).attr('data-stageid'));
                  var edgeid =parseInt($(this).attr('data-edgeid'));
                  if(stageid > 0 && edgeid > 0)
                  {
                    console.log("stageid"+stageid);
                    console.log("edgeid"+edgeid);
                    var dbid =parseInt($(this).attr('data-dbid'));
                    var stype =$(this).attr('data-stype');
                    action_items.id = edgeid;
                    action_items.dbid = dbid;
                    action_items.name = $('#activity_id'+edgeid+' option:selected').text();
                    action_items.activity_id = $("#activity_id"+edgeid).val();
                    action_items.with_rule = parseInt($("#rule_"+edgeid).val());
                    action_items.from_state =stageid;
                    action_items.to_state =0;
                    if(action_items.with_rule == 0)
                    {
                      action_items.to_state = $("#to_satge"+edgeid).val();
                    }
                    else if(action_items.with_rule == 1)
                    {
                      var rule_data=[];
                      $( ".rule_filtere_"+edgeid).each(function( index2 )
                      {

                        console.log("ddd"+$(this).val());

                        var stageid = $(this).attr("data-stageid");
                        var edgeid = $(this).attr("data-edgeid");
                        var rc = $(this).attr("data-rc");

                        var addtype = $(this).attr("data-addtype");
                        var parentid = $(this).attr("data-parentid");
                        var rgcount = $(this).attr("data-rgcount");

                        var rule_data_items = {};
                        rule_data_items.rc = rc;
                        rule_data_items.type = addtype;
                        rule_data_items.parentid = parentid;
                        rule_data_items.rgcount = rgcount;
                        if(addtype == 'rule')
                        {

                        var value='';  
                        var filter_id = $("#builder_rule_"+rc+"_filter").val();
                        var object_type = $("#builder_rule_"+rc+"_filter").find(':selected').attr('data-object_type'); 
                        var column_type = $("#builder_rule_"+rc+"_filter").find(':selected').attr('data-column_type'); 
                        var operator = $("#builder_rule_"+rc+"_operator").val(); 
                        /*console.log("column_type"+column_type);*/
                        if(column_type == 'checkbox' || column_type == 'radio')
                        {
                         var value = $('input[name="builder_rule_value_'+rc+'[]"]:checked').val();  
                        }
                        else if(column_type == 'date')
                        {
                         var value = $("#builder_rule_value_"+rc).val(); 
                         var t = $('#builder_rule_value_today_'+rc+':checked').val(); 
                         if(t == 'today')
                         {
                            var value = t;
                         }
                        }
                        else
                        {
                         var value = $("#builder_rule_value_"+rc).val();   
                        }  
                        rule_data_items.id = filter_id;
                        rule_data_items.operator = operator;
                        rule_data_items.value = value;
                        rule_data_items.object_type=object_type;
                        }
                        else if(addtype == 'group')
                        {

                        var filter_conditin = $("#builder_group_"+rc+"_cond").val();
                        var stage_case = $("#builder_group_stage"+rc+"_cond").val();
                        var if_stage = $("#rule_stages_option1_"+rc).val();
                        var else_stage = $("#rule_stages_option2_"+rc).val();  
                        rule_data_items.condition = filter_conditin;
                        rule_data_items.stage_case = stage_case;
                        rule_data_items.if_stage = if_stage;
                        rule_data_items.else_stage = else_stage;
                        }
                        
                        rule_data.push(rule_data_items);
                        
                      });

                      action_items.rule_data = rule_data;
                    }
                    action_items.from_state = stageid;

                    actions.push(action_items);
                  }
                  

              });
           console.log("----------action_li END------------");

            $.each(actions, function(i, item) 
            {
              console.log(item);
            });

            var data = {_token: CSRF_TOKEN,"workflow_id":workflow_id,"workflow_name":$('#workflow_name').val(),"workflow_color":$('#workflow_color').val(),"task_flow":2,"workflow_object_type":$('#workflow_object_type').val(),"form_id":$('#form_id').val(),"document_id":$('#document_id').val(),"workflow_stages":stages,"workflow_edges":actions
            };

            $.ajax({
                method: "POST",
                url: form_url,
                data: data,
                dataType: 'json',
                success: function (msg) {
                    $(".preloader").hide();
                    $(".action_button").attr("disabled", false);
                    $('.alert_space').html(msg.message);
                    $("#is_edit_true").val(0);
                    if(msg.status == 1)
                    {
                        workflow_id = msg.workflow_id;
                        $('#workflow_id').val(workflow_id);
                        if(msg.reload == 1)
                        {
                          window.location.href= msg.url;  
                        }
                        $('#closed_workflow_name').parsley().reset();
                    }
                    $("html, body").animate({ scrollTop: 0 }, "fast");

                    
                }
            });
        }
        else
        {
            $(".preloader").hide();
            $(".action_button").attr("disabled", false);
            console.log("Validation failed");
        }

     });

     $(document).on("change keyup paste input","#workflow_name",function(e) {
        var label = $(this).val();
        $("#wf_name_label").html(label);
      
     });

     $(document).on("change keyup paste input",".stage_label_input",function(e) {
    try {
        var stage_id = $(this).attr('data-count');  
        var label = $('#task_label'+stage_id).val();
        var wf_type =$(this).attr('data-wf_type');
        /*nodes.update({
                    id: stage_id,
                    label: label
                });*/
    
        $("#label"+stage_id).html(label);
        refresh_options();
            }
            catch (err) {
                alert(err);
            }
      
     });

     $(document).on("change keyup paste input",".task_label_input",function(e) {

        var edgeid = $(this).attr('data-edgeid');  
        var label = $('#activity_id'+edgeid+' option:selected').text();
        $("#task_heading"+edgeid).text(label);
            
      
     });

      $(document).on("click",".deleteState",function(e) {
       var node_id =$(this).attr('data-liid'); 

       swal({
        title:"{{trans('language.confirm_delete')}}",
        showCancelButton: true
        }).then((result) => {
        if(result){
         /*nodes.remove({id: node_id});*/
          $("#wf_li"+node_id).remove();
          $("#rwf_div"+node_id).remove();
           $("#wf_title_main").trigger('click');
           refresh_options();
        }
        else
        {
          //stay in same stage
        }
     });
     });    

       $(document).on("click",".deleteAction",function(e) {
      console.log("+++++CLICK+++++++++"); 
       var satge_id =$(this).attr('data-satge_id');
       var edge_id =$(this).attr('data-edgeid'); 

       swal({
        title:"{{trans('language.confirm_delete')}}",
        showCancelButton: true
        }).then((result) => {
        if(result){
         /*edges.remove({id: edge_id});*/
         console.log("#action_li"+edge_id);
                $("#action_li"+edge_id).remove();
                $("#collapse"+edge_id).remove();
        var tabFirst = $('#tab_stage_action_template'+satge_id+' a:first');
        tabFirst.tab('show');      
        }
        else
        {
          //stay in same stage
        }
     });
      
     
     }); 



       $(document).on("change",".rule_filter",function(e) {
        var lable = parseInt($(this).val());
        var stageid = $(this).attr('data-edgeid'); 
        var edgeid = $(this).attr('data-edgeid'); 
        var rc = $(this).attr('data-rc');

        var data_attr2 = 'data-stageid="'+stageid+'" data-edgeid="'+edgeid+'" ';

    var data_attr1 = 'data-rc="'+rc+'" ';

        var filter_option_value = $(this).val(); 
        var filter_option_key = $(this).find(':selected').attr('data-key');
        var filter_option_column_type = $(this).find(':selected').attr('data-column_type'); 
        var value_html='';
        if (typeof filter_option_key === "undefined") 
        {

          value_html +='<input class="form-control rule_filter_update builder_rule_value_'+rc+'" type="text" name="builder_rule_value_'+rc+'" id="builder_rule_value_'+rc+'" '+data_attr1+'" '+data_attr2+'>'; 
    
        }
        else if(filter_option_key == "invalid" || filter_option_column_type == "invalid")
        {

        }
        else
        {

        var column_type = rule_components[filter_option_key].column_type;
        var type_options = JSON.parse(rule_components[filter_option_key].type_options);
        
        if(column_type == 'select' || column_type == 'piclist')
        {
          value_html +='<select class="form-control rule_filter_update" name="builder_rule_value_'+rc+'" id="builder_rule_value_'+rc+'" '+data_attr1+'" '+data_attr2+'>'; 
              $.each(type_options, function(i, item)
              {
               
                var option_value='';
                var option_label='';
                if(typeof item.label != "undefined")
                {
                  option_label=item.label;
                  option_value=item.label;
                }
                if(typeof item.id != "undefined")
                {
                  option_value=item.id;
                }
                 value_html +='<option value="'+option_value+'">'+option_label+'</option>';
              });
          value_html +='</select>';

        }
        else if(column_type == 'checkbox')
        {
            var no_option=0;
            $.each(type_options, function(i, item)
              {
                no_option=1;
                var option_value='';
                var option_label='';
                if(typeof item.label != "undefined")
                {
                  option_label=item.label;
                  option_value=item.label;
                }
                if(typeof item.id != "undefined")
                {
                  option_value=item.id;
                }
                 value_html +='<label class="checkbox-inline"><input type="checkbox" class="builder_rule_value_'+rc+'" name="builder_rule_value_'+rc+'[]" value="'+option_value+'">'+option_label+'</label>';
              });
              if(no_option == 0)
              {
                value_html +='<label class="checkbox-inline"><input type="checkbox" class="builder_rule_value_'+rc+'" name="builder_rule_value_'+rc+'[]" value="on">Test</label>';
              }

        }
        else if(column_type == 'radio')
        {
            $.each(type_options, function(i, item)
              {
                var option_value='';
                var option_label='';
                if(typeof item.label != "undefined")
                {
                  option_label=item.label;
                  option_value=item.label;
                }
                if(typeof item.id != "undefined")
                {
                  option_value=item.id;
                }
                 value_html +='<label class="radio-inline"><input type="radio" class="builder_rule_value_'+rc+'" name="builder_rule_value_'+rc+'[]" value="'+option_value+'">'+option_label+'</label>';
              });

        }
        else if(column_type == 'date')
        {
          value_html +='<label class="checkbox-inline"><input type="checkbox" class="builder_rule_value_today" name="builder_rule_value_today_'+rc+'" id="builder_rule_value_today_'+rc+'"  value="today" '+data_attr1+'" '+data_attr2+'>Submission Date</label>';

          value_html +='<br/><div class="input-group"><div class="input-group-addon"><i class="fa fa-calendar"></i></div>';
          value_html +='<input class="rule_filter_update" type="text" name="builder_rule_value_'+rc+'" id="builder_rule_value_'+rc+'" '+data_attr1+'" '+data_attr2+'>'; 
          value_html +='</div>';
        }
        else
        {
          value_html +='<input class="form-control rule_filter_update" type="text" name="builder_rule_value_'+rc+'" id="builder_rule_value_'+rc+'" '+data_attr1+'" '+data_attr2+'>'; 
        }

        }
        
        
         $('#rule-value-container_'+rc).html(value_html); 
         
          if(column_type == 'date')
        {
          $('#builder_rule_value_'+rc).daterangepicker({
            singleDatePicker: true,
            "drops": "up",
            showDropdowns: true
        });
        }
        
     });


    window.Parsley.addValidator('emailcheck', {
  validateString: function(value) {
    var emails = value.split(/[;,]+/); // split element by , and ;
    valid = true;
    for (var i in emails) 
    {
             value = $.trim(emails[i]);
             console.log(value);
             if (/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(value) && valid == true) 
             {
                valid = true;
              } 
              else 
              {
                valid = false;
            }
    }
    return valid;
  },
  messages: {
    en: 'Invalid email format: please use a comma to separate multiple email addresses'
  }
});
     
    loadWorkflowtemplate();


   
    });

   
    
</script>
@endsection