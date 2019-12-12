<?php
  include (public_path()."/storage/includes/lang1.en.php" );
?> 
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
</style>

<style type="text/css">
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
            <h3 class="box-title">{{ Lang::get('language.workflow_new_template') }}</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        <form role="form" id="closed_workflow_name" name="closed_workflow_name" data-parsley-validate="data-parsley-validate">
            <input type="hidden" name="workflow_id" id="workflow_id" class="workflow_id" value="{{$workflow_id}}">
            <div class="box-body">
                <div class="col-md-12 alert_space"></div>
                <div class="col-md-4">
                    <div class="box box-primary sticky-scroll-box">
                        <div class="box-body">
                            <div class="form-group">
                                <ul class="products-list product-list-in-box workflow_template">
                                    <!-- /.item -->
                                    <li class="item li_active wf_li" data-liid="0" id="wf_title_main">
                                        <div class="product-img">
                                            <i class="fa fa-play fa-2x"></i>
                                        </div>
                                        <div class="product-info">
                                            <h3 class="item_header" id="wf_name_label">&nbsp;</h3>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-primary btn-sm add_task" data-wf_type="node" style="float: left; margin-left: 5px;"><i class="fa fa-plus"></i> Add Stage</button>
                                <button type="button" class="btn btn-warning btn-sm preview" style="float: left; margin-left: 5px;"><i class="fa fa-eye"></i> Preview</button>
                                <button type="button" class="btn btn-primary btn-sm  save_workflow" style="float: left; margin-left: 5px;">
                                <i class="fa fa-save"></i> Save Template</button>
                                <button type="button" class="btn btn-danger btn-sm  cancel_wf_edit" style="float: left; margin-left: 5px;"> 
                                <i class="fa fa-close"></i> Cancel </button>
                                <div class="preloader" style="float: left; margin-top: 5px; display: none;" >
                                    <i class="fa fa-spinner fa-pulse fa-fw fa-lg"></i>
                                    <span class="sr-only">Loading...</span>
                                </div>                               
                            </div>
                        </div>
                    </div>
                </div>  

                <div class="col-md-8">
                    <div class="box box-primary">
                        <div class="box-body">
                            <div class="row right_wft_col clearfix">
                                <div class="col-md-12 taskdiv" id="rwf_div0">  
                                    <h3 class="" >Workflow Details</h3>
                                    <div class="form-group">
                                        <label>{{$language['workflow name']}}: <span class="compulsary">*</span></label>
                                        <input type="text" class="form-control" id="workflow_name" name="workflow_name" required="" data-parsley-required-message="Workflow name is required" data-parsley-trigger="change focusout" data-stageid="0">
                                    </div>
                                    <div class="form-group">
                                        <label>Workflow Object Type: <span class="compulsary">*</span></label>
                                        <select name="workflow_object_type" id="workflow_object_type" class="form-control select2" required=""  data-parsley-required-message="Form Workflow type is required" data-parsley-trigger="change focusout" data-stageid="0">
                                            <option value="">Select workflow object type</option>
                                            <option value="form">Form  based workflow</option>
                                            <option value="document">Document based workflow</option>
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
                                        <label>{{$language['color']}}: <span class="compulsary">*</span></label>
                                        <div id="cp2" class="input-group colorpicker-component" title="Using input value">
                                            <input type="text" name="color" id="workflow_color" class="form-control" value="#C0C0C0" readonly="" data-stageid="0" />
                                            <span class="input-group-addon"><i></i></span>
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: none;">
                                        <label>On Submission: </label>
                                        <div id="builder0" class="query-builder form-inline"></div>
                                    </div>

                                    <!---- TABS END -->
                                    <div class="form-group" style="display: none;">
                                        <label>Workflow deadline: </label>
                                        <select name="deadline" id="deadline" class="form-control select2" >
                                            <option value="0" selected="selected">Note Set</option>
                                            <option value="1" > After workflow start</option>
                                        </select>
                                    </div>

                                    <div class="form-group" style="display: none;">
                                        <div class="row"> 
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" id="deadline_value" name="deadline_value" >
                                            </div>
                                            <div class="col-md-8">
                                                <select name="deadline_type" id="deadline_type" class="form-control select2">
                                                    <option value="day" selected="selected">Days</option>
                                                    <option value="week" >Weeks</option>
                                                    <option value="month" >Months</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>   <!-- COL 12 --> 
                            </div>
                            <div class="box-footer" style="">
                                <div class="row right_wft_col">
                                    <div class="box box-solid">
                                        <div class="box-header with-border">
                                            <label>Preview</label>
                                        </div>
                                        <!-- /.box-header -->
                                        <div class="box-body text-center">
                                            <div id="mynetwork"></div>
                                        </div>
                                        <!-- /.box-body -->
                                        <div class="box-footer">
                                            <button type="button" class="btn btn-primary btn-sm  save_workflow" style="float: left; margin-left: 5px;">Save Template</button>
                                            <div class="preloader" style="float: left; margin-top: 5px; display: none;" >
                                                <i class="fa fa-spinner fa-pulse fa-fw fa-lg"></i>
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- ROW -->
            </div>
        </form>
    </div>
</section>
   
<input type="hidden" value="0" id="is_edit_true">
    {!! Html::script('js/jquery-ui.min.js') !!}
    <script>



 $(document).ready(function() {

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

  var stage_color = $("#workflow_color").val(); 
  var workflow_id = $('#workflow_id').val();  
  var stage_option = '<option value="">Select a stage</option>';
  var taskcount = actioncount = 1;
  var nodes, edges, network,nodecount,edgecount,stage_color;
  var nodelabels = [];
  var nodecount = 0; var edgecount = 0;
  var wf_object_type_id='';
  var departments = department_users = '';
  var rule_count=0;
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

  
 var rules_basic_base = {condition: 'OR',rules:[],stage_case:'',if_stage:0,else_stage:0};
 var operator_options ='';
 var rule_components=[];
 var myrcArray = {}
 var db_stage_option='';
 var validation_check=0;
 var first = 0;
 var last = 0;
 var auto_id="@php echo $auto_id; @endphp";
 var rg_count=0;
  $.each(rule_operators, function(i, item)
              {
                /*console.log(item);*/
                 operator_options +='<option value="'+i+'">'+item+'</option>';
              });

  $('#cp2').colorpicker({
            format: 'hex'
        }).on('change',
            function(ev) {
                //console.log(ev);
                if(stage_color != ev.target.value)
                {

                }
                /*stage_color = ev.target.value;*/
            });
  var nodeset = [];
  var nodes = new vis.DataSet(nodeset);

  var edgeset = [];
  var edges = new vis.DataSet(edgeset);

  // create a network
  var container = document.getElementById('mynetwork');
  var data = {
    nodes: nodes,
    edges: edges
  };
  var options = {
  height: '600px',
  width: '100%',
  layout: {
    randomSeed: undefined,
    improvedLayout:true,
    hierarchical: {
      enabled:false,
      levelSeparation: 150,
      nodeSpacing: 100,
      treeSpacing: 200,
      blockShifting: true,
      edgeMinimization: true,
      parentCentralization: true,
      direction: 'UD',        // UD, DU, LR, RL
      sortMethod: 'hubsize'   // hubsize, directed
    }
  },
  interaction: {
          navigationButtons: true
      }
  }
  var network = new vis.Network(container, data, options);network.fit();
  /* RULE GROUP TEMPLATE*/
  

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
    if(stage_dbid == 0)
    {
       var stage_options = '<option value="0">Select Stage</option>';
       var response = nodes.get();
      $.each(response, function(i, item) {
      if(item.stype != 'first')
      {  
      stage_options += '<option value="' + item.id + '">' + item.label + '</option>';
        }
      
       });

       var temp_stages_option = stage_options;
    }
    else
    {
       var temp_stages_option = db_stage_option; 
    }

    rule_count++;
    /*var json_type = {}; 
    setTOJson(json_type);*/
    var data_attr2 = 'data-stageid="'+stageid+'" data-edgeid="'+edgeid+'" data-level="'+level+'" data-parentid="'+parentid+'"';

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
    
        html+='<select class="form-control group_filter_update" name="builder_group_'+rule_count+'_cond" id="builder_group_'+rule_count+'_cond" '+data_attr1+' '+data_attr2+'><option value="OR">OR</option><option value="AND">AND</option></select>';

    if(parentid == 008900)
    {    
   selected = '';
    if(stage_case == '' || stage_case == 0)
    {
      selected='selected="selected"';
    }    
    html+='<select class="form-control group_stage_case_update" name="builder_group_stage'+rule_count+'_cond" id="builder_group_stage'+rule_count+'_cond" '+data_attr1+' '+data_attr2+' style="margin-left:5px;"><option value="" '+selected+'>------</option>';
    selected = '';
    if(stage_case == 'if')
    {
      selected='selected="selected"';
    }
    
    html+='<option value="if" '+selected+'>IF</option>';
    selected = '';
    if(stage_case == 'if_else')
    {
      selected='selected="selected"';
    }
    html+='<option value="if_else" '+selected+'>IF & ELSE </option>';
    html+='</select>';
      }  
    html+='</div>';
    /*rule-filter-container end */
    html+='<div class="error-container" data-toggle="tooltip"><i class="fa fa-warning"></i></div>';
    html+='</dt>';
    html+='<dd class="rules-group-body rule_group_'+stageid+'_'+level+'" id="dd_rule_group_'+rule_count+'">';
    html+='<ul class="rules-list" id="ul_rule_group_'+rule_count+'"></ul>';

     if_style="display:none";
    else_style="display:none";
   if(stage_case == 'if')
    {
      if_style="display:block";
    }
    else if(stage_case == 'if_else')
    {
      if_style="display:block";
      else_style="display:block";
    }

    html+='<div class="row">';

    html+='<div class="col-xs-4">';
    html+='<div class="form-group rule_group_stage_'+rule_count+'" id="rule_group_if_stage_'+rule_count+'" style="'+if_style+'">';
    html+='<label for="exampleInputEmail1">Go to stage </label>';
    html+='<br>';
    html+='<select class="form-control rule_stage_case c_rule_stages_option1_'+stageid+'" id="rule_stages_option1_'+rule_count+'"  name="" '+data_attr1+' '+data_attr2+'>'+temp_stages_option+'</select>';
    html+='</div>';
    html+='</div>';

    html+='<div class="col-xs-4">';
    html+='<div class="form-group rule_group_stage_'+rule_count+'" id="rule_group_if_else_stage_'+rule_count+'" style="'+else_style+'">';
    html+='<label for="exampleInputEmail1">Else go to the stage </label>';
    html+='<br>';
    html+='<select class="form-control rule_stage_case c_rule_stages_option2_'+stageid+'" id="rule_stages_option2_'+rule_count+'" name="" '+data_attr1+' '+data_attr2+'>'+temp_stages_option+'</select>';
    html+='</div>';
    html+='</div>';

    html+='<div class="col-xs-4">';
    if(parentid == 0)
    { 
    html+='<div class="form-group">';
    html+='<label for="exampleInputEmail1">&nbsp; </label>';
    html+='<br>';
    html+='<button type="button" class="btn btn-xs btn-success pull-left add_more_rule" id="add_more_rule" data-add="group"  data-stageid="'+stageid+'" data-edgeid="'+edgeid+'" data-level="0" data-parentid="0"><i class="fa fa-plus"></i> Add More Rule</button>';
     html+='</div>';
   }
    html+='</div>'; 

    html+='</div>';
    html+='</dd>';
    html+='</dl>';
    
    return html;
   };


   var AddRuleTemplate = function(rule_arry) 
   {
    var stageid = rule_arry.stageid;
    var edgeid = rule_arry.edgeid;
    var parentid = rule_arry.parentid;
    rule_count++;
    var data_attr2 = 'data-stageid="'+stageid+'" data-edgeid="'+edgeid+'" ';

    var data_attr1 = 'data-rc="'+rule_count+'" data-parentid="'+parentid+'"';

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

   /*var rule_arry = {"stageid":0,"edgeid":0,"first":1,"parentid":0,"level":0}
   var temp = AddGroupTemplate(rule_arry);
   $('#builder0').append(temp);*/
  
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
        if(wf_type == 'edge')
        {
            addAction(task);
        }
        sel_departments = task.sel_departments;
        sel_department_users = task.sel_department_users;
        department_users = task.department_users;
        escallation_day = task.escallation_day;
        escallation_stage = task.escallation_stage;
        escallation_activity_id = task.escallation_activity_id;
        wfTRHTML +='<li class="item wf_li" data-liid="'+id+'" id="wf_li'+id+'" data-wf_type="'+wf_type+'">';
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
        wfTLHTML +='<input type="radio" checked="checked" class="wf_actions" id="wf_actions2_'+id+'" name="wf_actions'+id+'" value="2" data-stageid="'+id+'"> By Hierarchy &nbsp;';
        wfTLHTML +='<input type="radio" class="wf_actions" id="wf_actions3_'+id+'" name="wf_actions'+id+'" value="3" data-stageid="'+id+'"> By Group &nbsp;';
        wfTLHTML +='<input type="radio" class="wf_actions" id="wf_actions1_'+id+'" name="wf_actions'+id+'" value="1" data-stageid="'+id+'" > By User &nbsp;';
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
        wfTLHTML +='<select name="escallation_stage" id="escallation_stage'+id+'" class="select2 escallation" data-stageid="'+id+'" style="width:25%;background-color: #fff;padding: 6px 12px;border: 1px solid #ccc;"><option value="0">Select Stage</option>';
        wfTLHTML +=escallation_option;            
        wfTLHTML +='</select>';
        wfTLHTML +='&nbsp;<select name="escallation_activity_id" id="escallation_activity_id'+id+'" class="escallation"  data-stageid="'+id+'" style="width:25%;background-color: #fff;padding: 6px 12px;border: 1px solid #ccc;"><option value="0">Select Task</option>';
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

        nodes.add({
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

        });

        $("#wf_actions"+stage_action+"_"+id).prop("checked", true);
        $("#stage_group"+stage_group+"_"+id).prop("checked", true);
        $("#escallation_day"+id).val(escallation_day); 
        $('#escallation_stage'+id).val(escallation_stage);
        $('#escallation_activity_id'+id).val(escallation_activity_id);
        
        change_stage_action(id);
        //change_stage_group(id);
        return wfTLHTML;
    }

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

      /*$('.collapse'+from_id).collapse('hide');*/
      var selectVal = parseInt($("input[name='wf_actions"+from_id+"']:checked").val());
      var common_data='data-stageid="'+from_id+'" data-edgeid="'+id+'"';
      //var stage_option = '<option value="0">Select Stage</option>';
      
      var response = nodes.get();
      $.each(response, function(i, item) {
      if(item.stype != 'first')
      {  
      stage_option += '<option value="' + item.id + '">' + item.label + '</option>';
      }
      
       });
      var action_html ='';
      action_html +='<li id="action_li'+id+'"><a href="#collapse'+id+'" id="action_div_'+id+'" data-toggle="tab"><span id="task_heading'+id+'">'+task.label+'</span> &nbsp; <button class="close deleteAction" type="button" title="Remove Action" data-satge_id="'+from_id+'" data-edgeid="'+id+'">×</button> </a></li>';

      $('#tab_stage_action_template'+from_id).append(action_html);

      var action_html ='';

          action_html +='<div id="collapse'+id+'" class="tab-pane collapse'+from_id+' collapse  active">';
         /* action_html +='<div class="box-body">';*/
          /* row START */
          action_html +='<div class="row">';
          action_html +='<div class="col-md-6" id="">'; 
          action_html +='<label>Action Name</label>';
          /*action_html +='<input type="text" class="form-control task_label_input" id="task_label'+id+'" name="task_label[]" value="'+task.label+'" '+common_data+' data-wf_type="'+wf_type+'">';*/
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
          rule_style='display:block';
          }
          action_html +='<option value="0" '+selected+'>Without Rule</option>';

          var selected='';
         /* if(with_rule == 1 && (selectVal == 1 || selectVal == 4))*/
          if(with_rule == 1)
          {
          selected='selected="selected"';
          rule_style='display:block';
          }

          if(selectVal == 2 || selectVal == 3)
          {
          /*selected='disabled="disabled"';*/
          selected='selected="selected"';
          }

        action_html +='<option value="1" '+selected+'>With Rule</option>';
        action_html +='</select>';
        action_html +='</div>';

        action_html +='</div>';
                    /* row END 

                    /* row START */
        action_html +='<div class="row">';            
        action_html +='<div class="col-md-12" id="ruletable'+id+'" style="'+rule_style+'">';

         /* Tree View START*/
         rg_count++;
         var rule_arry = {"stageid":from_id,"edgeid":id,"first":1,"parentid":0,"level":0,stage_case:'if_else',stage_dbid:dbid,rgcount:rg_count};
         var temp = AddGroupTemplate(rule_arry);
         action_html +='<div id="builder'+id+'" class="query-builder form-inline">'+temp+'</div>';

        /*action_html +='<div class="query-builder form-inline"><button type="button" class="btn btn-xs btn-success pull-right add_more_rule" id="add_more_rule" data-add="group"  data-stageid="'+id+'" data-edgeid="'+id+'" data-level="0" data-parentid="0"><i class="fa fa-plus"></i> Add More Rule</button></div>'; */       /* Tree View END*/
          
          action_html +='</div>';
          action_html +='</div>';
          /* row END */

           /* row START */
        action_html +='<div class="row">';
          
        action_html +='<div class="col-md-6" >'; 
        action_html +='<label id="rule_label'+id+'">To Stage:<span class="compulsary">*</span></label>';
        action_html +='<input type="hidden" name="from_satge" id="from_satge'+id+'">'; 
        action_html +='<select name="to_satge" id="to_satge'+id+'" class="form-control select2 stages_option stages_option'+from_id+'" '+common_data+'>';
        action_html +=stage_option;            
        action_html +='</select>';
        action_html +='</div>';
        action_html +='</div>';
                    /* row END */

        /*action_html +='</div>';*/
        action_html +='</div>';
        action_html +='</div>';

      $('#stage_action_template'+from_id).append(action_html);
      
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
          
      var rules_template = {condition: rules_basic.condition,rules:[],stage_case:'if_else',if_stage:0,else_stage:0};

      var group_rules_template = [{rc:rule_count,type:'group',condition: rules_basic.condition,stage_case:'if_else',if_stage:0,else_stage:0,parentid:0}];
      
      edges.add({
                      id: id,
                      dbid:dbid,
                      label:label,
                      from: task.from,
                      to: task.to,
                      arrows:'to',
                      with_rule:with_rule,
                      rules_basic:rules_template,
                      activity_id:activity_id,
                      rules_data:[]
                  });

      edge_data = edges.get(id);

      var group_rules_template = {rc:rule_count,type:'group',condition:'AND',stage_case:'',if_stage:0,else_stage:0,parentid:0};
         edge_data.rules_data.push(group_rules_template);
      $.each(rule_data, function(i, item) {
        
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
          
          $("#rule_stages_option1_"+rule_count).val(item.if_stage).trigger('change');
           
          $("#rule_stages_option2_"+rule_count).val(item.else_stage).trigger('change');
          
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
      console.log("myrcArray");
      console.log(myrcArray);
      $("#rule_"+id).val(with_rule).trigger('change');
      return true;
   } 

   /* Load work flow template */
  var loadWorkflowtemplate = function(wf) 
   {
      var loadformurl = "@php echo URL('load_Workflow_json'); @endphp";
      $.getJSON(loadformurl+'?workflow_id=' + wf, function(data) {
        taskcount = parseInt(data.nodecount);
        actioncount = parseInt(data.edgecount);
        edgecount = parseInt(data.edgecount);
        var workflow_name =data.workflow_name;
        stage_color =data.workflow_color;
        var nodeset = data.wf_states;
        var edgeset = data.wf_transitions;
        var task_flow = data.task_flow;

        var wf_object_type = data.wf_object_type;
        wf_object_type_id = data.wf_object_type_id;
        var deadline = data.deadline;
        var deadline_type = data.deadline_type;
        var deadline_value = data.deadline_value;
        sel_departments = data.sel_departments;
        sel_department_users = data.sel_department_users;
        db_stage_option = '<option value="">Select</option>';
        first = last = 0;
        $.each(nodeset, function(i, item)
        {
          console.log("Item"+item);
          if(item.stype != 'first')
      {
          db_stage_option += '<option value="' + item.id + '">' + item.label + '</option>'; 
      }
        });


        

        var wf_name_label = "Workflow Start";
        if(workflow_name != '')
        {
            wf_name_label = workflow_name;
        }
        $("#wf_name_label").html(wf_name_label);   
        

        $("#workflow_name").val(workflow_name); 
        $("#workflow_color").val(stage_color);
        $("#workflow_color").trigger('change');
        $("#workflow_flow_type").val(task_flow);

        $("#workflow_object_type").val(wf_object_type);

        $(".object_type_row").slideUp();  
        if(wf_object_type == 'form')
        {
         $(".form_row").slideDown();
        }
        if(wf_object_type == 'document')
        {
         $(".document_row").slideDown();
        }
        
        $("#deadline").val(deadline);
        $("#deadline_type").val(deadline_type);
        $("#deadline_value").val(deadline_value);
  
       var users_options= {sel_count:0,department_users: data.department_users, sel_department_users: sel_department_users}

        wf_manager_users(users_options);
        rule_components = data.rule_components; 
        rule_options='<option value="-1" data-column_type="invalid" data-key="invalid" data-object_type="invalid">------</option>';  
              $.each(rule_components, function(i, item)
              {
                var disbld ='';
                if(item.object_type=="invalid"){
                    disbld = 'disabled="disabled"';
                }
                rule_options +='<option value="'+item.id+'" data-column_type="'+item.column_type+'" data-key="'+i+'" data-object_type="'+item.object_type+'" '+disbld+'>'+item.column_name+'</option>';
              });
              
        $.each(nodeset, function(i, item)
        {
          
          var taskhtml = addTask(item);
        });

        if(first == 0)
        {

          var empty=[];
          var task= {id: taskcount, dbid: 0, label: 'Start (System Stage)', shape: "circle", color: "#c0c0c0", type: 'nodes',stype: 'first', description:'Start stage',department_users:empty,sel_departments:empty,sel_department_users:empty,stage_action:4,stage_group:1,stage_percentage:0,escallation_stage:0,escallation_day:0,escallation_activity_id:0}
          addTask(task);
          taskcount++;

        }

        if(last == 0)
        {

         var task= {id: taskcount, dbid: 0, label: 'End (System Stage)', shape: "circle", color: "#c0c0c0", type: 'nodes',stype: 'last', description:'End stage',department_users:empty,sel_departments:empty,sel_department_users:empty,stage_action:4,stage_group:1,stage_percentage:0,escallation_stage:0,escallation_day:0,escallation_activity_id:0}
          addTask(task);
          taskcount++;

        }
        $("#task_label1").attr("disabled", "disabled"); 
        $("#task_label2").attr("disabled", "disabled");
     $.each(edgeset, function(i, item)
        {
          var taskhtml = addAction(item);
        });
        load_workflow_objects();
     
      //$('#wf_managers').fastselect();        
      });
   };      
   loadWorkflowtemplate(workflow_id);

   /* Click Function on Stage List (LEFT SIDE) */
   $(document).on("click",".wf_li",function(e) {
       var liid =$(this).attr('data-liid'); 
       var wf_type =$(this).attr('data-wf_type'); 
       $(".taskdiv").hide(); /* Hide All Other Right DIV */
       $("#rwf_div"+liid).show(); /* Show Only Current Right DIV */
        $(".wf_li").removeClass("li_active");
        $(this).addClass("li_active");
        var from_satge = liid; 
        var stage_option = '<option value="0">Select Stage</option>';
        var response = nodes.get();
        $.each(response, function(i, item) {
        if(item.stype != 'first')
        {    
        stage_option += '<option value="' + item.id + '">' + item.label + '</option>';
        }
       });
        $( ".stages_option"+liid).each(function( index )
        {
            
            var edgeid =$(this).attr('data-edgeid');
            var to_satge = $(this).val(); 
            var rule = $("#rule_"+edgeid).val(); 
            $('#to_satge'+edgeid).html(stage_option);
            $("#to_satge"+edgeid).val(to_satge); 

        });

            var to_satge = $("#escallation_stage"+liid).val(); 
            $('#escallation_stage'+liid).html(stage_option);
            $("#escallation_stage"+liid).val(to_satge); 
        
        $( ".c_rule_stages_option1_"+liid).each(function( index )
        { 
            var rc =$(this).attr('data-rc');
            var to_satge1 = $(this).val(); 
            $('#rule_stages_option1_'+rc).html(stage_option);
            $('#rule_stages_option1_'+rc).val(to_satge1);

        });

        $( ".c_rule_stages_option2_"+liid).each(function( index )
        { 
            var rc =$(this).attr('data-rc');
            var to_satge2 = $(this).val(); 
            $('#rule_stages_option2_'+rc).html(stage_option);
            $('#rule_stages_option2_'+rc).val(to_satge2);

        });
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
        nodes.update({
            id: stageid,
            stage_action: selectVal
        });
        
        
   }

   var change_stage_group = function(stageid) 
   {
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
        nodes.update({
            id: stageid,
            stage_group: selectVal,
        });
    }


    $(document).on("change",".wf_actions",function(e) {
        var stageid =$(this).attr('data-stageid');      
        change_stage_action(stageid);
    });

    $(document).on("change",".stage_group",function(e) {
        var stageid =$(this).attr('data-stageid');      
        change_stage_group(stageid);
    });

    $(document).on("change keyup paste input",".stage_percentage",function(e) {
        try {
            var stageid = $(this).attr('data-stageid');     
            var label = $('#stage_percentage'+stageid).val();
            nodes.update({
                id: stageid,
                stage_percentage: label
            });     
        }catch (err) {
            alert(err);
        }
    });


   /* Preview OF Workflow Template */
   $(document).on("click",".preview",function(e) {
        $(".taskdiv").hide();
        $("#preview_div").show();
        $(".wf_li").removeClass("li_active");
     });


    $(document).on("click",".add_task",function(e) {
        validation_check=0;
    if($("#closed_workflow_name").parsley().validate())
        { 
    
        var wf_type =$(this).attr('data-wf_type'); 
          var label='Stage';
          var empty=[];
          var task= {id: taskcount, dbid: 0, label: label, shape: "box", color: "#c0c0c0", type: wf_type,stype: 'middle', description:'',department_users:empty,sel_departments:empty,sel_department_users:empty,stage_action:2,stage_group:1,stage_percentage:0,escallation_stage:0,escallation_day:0,escallation_activity_id:0}
          addTask(task);
         $( "#wf_li"+taskcount).trigger( "click" );
         taskcount++;
         }else{
            console.log("Validation failed in add task");
        }
     });


     $(document).on("click",".add_action",function(e) {
    
    
    var wf_type ='edge'; 
    var from_id =parseInt($(this).attr('data-stage')); 
      var label='Action';
      var rules_basic_base = {condition: 'AND',rules:[],stage_case:'if_else',if_stage:0,else_stage:0};
      var task= {id: actioncount, dbid: 0, label: label, type: wf_type, from: from_id, to: 0, description:'',with_rule:0,rules_basic:rules_basic_base,rule_data:[],activity_id:0}
        addAction(task);
        actioncount++;

        
     });


    $(document).on("change",".rule_change",function(e) {
        var with_rule = parseInt($(this).val());
        var edge_id = $(this).attr('data-edgeid'); 
        if(with_rule == 1)
        {
          $('#ruletable'+edge_id).show();
          $('#to_satge'+edge_id).hide();
          $('#rule_label'+edge_id).hide();
        }
        else
        {
          $('#ruletable'+edge_id).hide();
          $('#to_satge'+edge_id).show();
          $('#rule_label'+edge_id).show();
        } 

         
        if(edge_id != 0)
                {
                  
                  edges.update({
                    id: edge_id,
                    with_rule: with_rule
                });
                }
        
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

    $(document).on("change keyup paste input",".rule_filter_update",function(e) {

          var rc =$(this).attr('data-rc');
       var stageid = $(this).attr('data-stageid');
       var edgeid = $(this).attr('data-edgeid');
         var rule_arry = {"stageid":stageid,"edgeid":edgeid,"rc":rc}
      
     });



    $(document).on("change",".group_stage_case_update",function(e) {

       
       var selected_stage_case =$(this).val();
       var rc =$(this).attr('data-rc');
       var stageid = $(this).attr('data-stageid');
       var edgeid = $(this).attr('data-edgeid');

       edge_data = edges.get(edgeid);
       $(".rule_group_stage_"+rc).hide(); 
       
       if(selected_stage_case == 'if')
      {
        $("#rule_group_if_stage_"+rc).show();
      }
      else if(selected_stage_case == 'if_else')
      {
        $("#rule_group_if_stage_"+rc).show(); 
        $("#rule_group_if_else_stage_"+rc).show(); 
      } 
     });

    $(document).on("change",".rule_stage_case",function(e) {

       var rc =$(this).attr('data-rc');
       var stageid = $(this).attr('data-stageid');
       var edgeid = $(this).attr('data-edgeid'); 
       var to_satge = $("#to_satge"+edgeid).val();
       var rule = parseInt($("#rule_"+edgeid).val()); 
        $(".c_rule_stages_option1_"+stageid).each(function( index )
        {
              
              var check_st = $(this).val();
              var c_edgeid = $(this).attr('data-edgeid'); 
              if(check_st && c_edgeid == edgeid)
              {
                var to_satge = check_st;
                  if(rule == 1)
                {
                  $("#to_satge"+edgeid).val(to_satge);
                edges.update({
                        id: edgeid,
                        to: to_satge
                    });
                }   
              }    
                  

        });
     
     });

    var deleteRule = function(rule_arry) 
   {
    var stageid = rule_arry.stageid;
    var edgeid = rule_arry.edgeid;
    var rc = rule_arry.rc;
    edge_data = edges.get(edgeid);
   

     $.each(edge_data.rules_basic.rules, function(i, item)
              {
                /*console.log(item);*/
        if (typeof item != "undefined") 
        {
                if(item.rc == rc)
                {
                  edge_data.rules_basic.rules.splice(i, 1);
                }
        }        
                 
              });

     $.each(edge_data.rules_data, function(i, item)
              {
                /*console.log(item);*/
        if (typeof item != "undefined") 
        {
                if(item.rc == rc)
                {
                  edge_data.rules_data.splice(i, 1);
                }
        }        
                 
              });
     /*console.log(edge_data);*/
  };

    $(document).on("click",".delete_rule_template",function(e) {

          var rc =$(this).attr('data-rc');
       var stageid = $(this).attr('data-stageid');
       var edgeid = $(this).attr('data-edgeid');
         var rule_arry = {"stageid":stageid,"edgeid":edgeid,"rc":rc}
         deleteRule(rule_arry); 
      
     });

    $(document).on("click",".add_rule_template",function(e) {
       /*console.log("hi");*/
       var rc =$(this).attr('data-rc');
       var addtype =$(this).attr('data-add');
       var stageid = $(this).attr('data-stageid');
       var edgeid = $(this).attr('data-edgeid');
       var level = $(this).attr('data-level');
       var parentid = $(this).attr('data-parentid');
       var parentid = rc;
       var rgcount =rg_count;
        edge_data = edges.get(edgeid);
       var nextlevel = parseInt(level)+1;
       var rule_arry = {"stageid":stageid,"edgeid":edgeid,"parentid":rc,"first":0,"level":nextlevel,stage_dbid:0,rgcount:rgcount}
       var temp ='';
       if(addtype == 'rule')
       {
        /*rule_arry.level=level;*/
        rule_arry.rules_basic = rules_basic_base;
        var temp = AddRuleTemplate(rule_arry);
        
       
        /*console.log(edge_data);*/
        trules_basic = edge_data.rules_basic;
     
        var rule_value = {rc:rule_count,id: '',object_type:'',operator: '',value:''};
        //trules_basic.rules.push(rule_value);
        edge_data.rules_basic.rules.push(rule_value);


        /* Rule Template Start*/
         var group_rules_template = {rc:rule_count,type:addtype,id: '',object_type:'',operator: '',value:'',parentid:parentid};
         edge_data.rules_data.push(group_rules_template);
         /* Rule Template End*/

       }
       else if(addtype == 'group')
       {
        rule_arry.rules_basic = rules_basic_base;
        rule_arry.stage_case = '';
        var temp = AddGroupTemplate(rule_arry);

        /* Rule Template Start*/
         var group_rules_template = {rc:rule_count,type:addtype,condition:'AND',stage_case:'',if_stage:0,else_stage:0,parentid:parentid};
         edge_data.rules_data.push(group_rules_template);
         /* Rule Template End*/
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

    $(document).on("change keyup paste input",".stage_label_input",function(e) {
    try {
        var stage_id = $(this).attr('data-count');  
        var label = $('#task_label'+stage_id).val();
        var wf_type =$(this).attr('data-wf_type');
        nodes.update({
                    id: stage_id,
                    label: label
                });
    
        $("#label"+stage_id).html(label);
            }
            catch (err) {
                alert(err);
            }
      
     });

    $(document).on("change keyup paste input",".task_details",function(e) {
    try {
        var stage_id = $(this).attr('data-stageid');  
        var description = $('#task_details'+stage_id).val();
        nodes.update({
                    id: stage_id,
                    description: description
                });
            }
            catch (err) {
                alert(err);
            }
      
     });


     $(document).on("change keyup paste input",".task_label_input",function(e) {
    try {
        var edgeid = $(this).attr('data-edgeid');  
        var label = $('#activity_id'+edgeid+' option:selected').text();
        var activity_id = $('#activity_id'+edgeid).val();

        var wf_type =$(this).attr('data-wf_type');
        edges.update({
                    id: edgeid,
                    label: label,
                    activity_id: activity_id
                });
        /*console.log("#task_heading"+label);*/
    
        $("#task_heading"+edgeid).text(label);
            }
            catch (err) {
                alert(err);
            }
      
     });
   
   $(document).on("change",".stages_option",function(e) {
    try {
        var from_satge = $(this).attr('data-stageid'); 
        var edge_id = $(this).attr('data-edgeid');  
        /*console.log("from_satge"+from_satge);*/
       /* console.log("edge_id"+edge_id);*/
        var to_satge = $(this).val();
        
        var from_satge_name = $("#task_label"+from_satge).val();
        var to_satge_name = $("#to_satge"+edge_id+" option:selected").text();

        var label = $('#activity_id'+edge_id+' option:selected').text();
        var activity_id = $("#activity_id"+edge_id).val();
        if(from_satge && to_satge)
        {
        edges.update({
                      id: edge_id,
                      label:label,
                      from:from_satge,
                      to: to_satge,
                      activity_id:activity_id,
                      arrows:'to'
                  });
        //  $('#label_stage'+edge_id).html(from_satge_name+' -->'+to_satge_name);
          
        }
        else{ console.log("Hoy"); }
            }
            catch (err) {
                alert(err);
            }
      
     });


     $(document).on("click",".deleteState",function(e) {
       var node_id =$(this).attr('data-liid'); 

       swal({
        title:"{{$language['confirm_delete']}}",
        showCancelButton: true
        }).then((result) => {
        if(result){
         nodes.remove({id: node_id});
          $("#wf_li"+node_id).remove();
          $("#rwf_div"+node_id).remove();
           $("#wf_title_main").trigger('click');
        }
        else
        {
          //stay in same stage
        }
     });
      
     
     }); 

     $(document).on("click",".deleteAction",function(e) {
       var satge_id =$(this).attr('data-satge_id');
       var edge_id =$(this).attr('data-edgeid'); 

       swal({
        title:"{{$language['confirm_delete']}}",
        showCancelButton: true
        }).then((result) => {
        if(result){
         edges.remove({id: edge_id});
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


     $(document).on("click",".builder_rule_value_today",function(e) {
       var edge_id =$(this).attr('data-edgeid'); 
       var rc =$(this).attr('data-rc'); 
       var ckb = $(this).is(':checked');
        if(ckb == false) {
            $("#builder_rule_value_"+rc).attr("disabled", false);
        } else {
            $("#builder_rule_value_"+rc).attr("disabled", true);
        } 
      
     
     }); 

     


     $(document).on("change keyup paste input","#workflow_name",function(e) {
        var label = $(this).val();
        $("#wf_name_label").html(label);
      
     });

      $(document).on("change",".stage_users",function(e) {
        var label = $(this).val();
        /*console.log("label"+label);*/
        var stageid = $(this).attr("data-stageid");
        var stage_users = $(this).val();
        if(stageid != 0)
        {
          /*console.log("update nodes");*/
            nodes.update({
                id: stageid,
                sel_department_users: stage_users
            });
        }
      
     });

    $(document).on("change",".escallation",function(e) {
        var stageid = $(this).attr("data-stageid");
        var escallation_stage = $('#escallation_stage'+stageid).val();
        var escallation_day = $('#escallation_day'+stageid).val();
        var escallation_activity_id = $('#escallation_activity_id'+stageid).val();
        if(stageid != 0)
        {
          /*console.log("update nodes");*/
            nodes.update({
                id: stageid,
                escallation_stage: escallation_stage,
                escallation_day: escallation_day,
                escallation_activity_id:escallation_activity_id
            });
        }
      
     });

    $(document).on("change",".stage_dept",function(e) {
        var label = $(this).val();
    
        var stageid = $(this).attr("data-stageid");
        var stage_dept = $(this).val();
        /*console.log("stage dept"+stage_dept);*/
        if(stageid != 0)
        {
            /*console.log("update nodes");*/
            nodes.update({
                id: stageid,
                sel_departments: stage_dept
            });
        }
    });

    ///OLD

$(document).on("change keyup paste input",".stage_label",function(e) {
   
    
    try {
                var stage_id = $(this).attr('data-stage');  
        //console.log("save 122"+stage_id);
         nodes.update({
                    id: stage_id,
                    label: $('#stage_label_'+stage_id).val()
                });
        
        DrawEdges();
            }
            catch (err) {
                alert(err);
            }
      
     });

    $(document).on("click",".add_more_rule",function(e) {
        var stageid = $(this).attr("data-stageid");
        var edgeid = $(this).attr("data-edgeid");
        rg_count++;;
        var rule_arry = {"stageid":stageid,"edgeid":edgeid,"first":1,"parentid":0,"level":0,stage_case:'if_else',stage_dbid:0,rgcount:rg_count};
         var temp = AddGroupTemplate(rule_arry);
         $('#builder'+edgeid).append(temp);

    });

    $(document).on("click",".save_workflow",function(e) {
        e.preventDefault();
        $(".preloader").css("display", "block");
        $(".save_workflow").attr("disabled", true);
        $(".save_workflow").show();
        validation_check=0;
        $('.alert_space').html('');
        var e = edges.get();

        $(e).each(function (i, element) 
        {
           console.log(element);
            if(element.with_rule == 1)
            {
                
                $(element.rules_data).each(function (j, er) 
                {
                    var rc = er.rc;
                    if(er.type == 'group')
                    {
                        var filter_conditin = $("#builder_group_"+rc+"_cond").val();
                        var stage_case = $("#builder_group_stage"+rc+"_cond").val();
                        var if_stage = $("#rule_stages_option1_"+rc).val();
                        var else_stage = $("#rule_stages_option2_"+rc).val();
                        er.condition = filter_conditin;
                        er.stage_case = stage_case;
                        if(stage_case == "if")
                        {
                            er.if_stage = if_stage;
                            er.else_stage = 0;
                        }
                        else if(stage_case == "if_else")
                        {
                            er.if_stage = if_stage;
                            er.else_stage = else_stage;
                        }
                        else
                        {
                            er.if_stage = 0;
                            er.else_stage = 0;
                        }
                    }
                    if(er.type == 'rule')
                    {
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
                        
                        er.id = filter_id;
                        er.operator = operator;
                        er.value = value;
                        er.object_type=object_type;
                    }
                });
            }
        });  
        if($("#closed_workflow_name").parsley().validate())
        {
            var fields               ='';
            var CSRF_TOKEN           = $('meta[name="csrf-token"]').attr('content');
            var form_url             = "@php echo URL('save_closed_workflow'); @endphp";
            var workflow_id          = $('#workflow_id').val();
            var departments          = '';
            var department_users     = $('#wf_manager_users0').val();

            var view_departmentid    = $("#view_departmentid").val();
            var view_userid          = $("#view_userid").val();
            var view_authentication  = '';
            if($("#view_authentication").is(":checked")) {
                view_authentication = $("#view_authentication").val();
            }
            var delete_departmentid  = $("#delete_departmentid").val();
            var delete_userid        = $("#delete_userid").val();
            var delete_authentication = '';
            if($("#delete_authentication").is(":checked")) {
                delete_authentication = $("#delete_authentication").val();
            }
            var edit_departmentid   = $("#edit_departmentid").val();
            var edit_userid         = $("#edit_userid").val();
            var edit_authentication = '';
            if($("#edit_authentication").is(":checked")) {
                edit_authentication = $("#edit_authentication").val();
            }
            var add_departmentid    = $("#add_departmentid").val();
            var add_userid          = $("#add_userid").val();
            var add_authentication  = '';
            if($("#add_authentication").is(":checked")) {
                add_authentication = $("#add_authentication").val();
            }
            
            var data = {_token: CSRF_TOKEN,"workflow_id":workflow_id,"workflow_name":$('#workflow_name').val(),"workflow_color":$('#workflow_color').val(),"task_flow":2,"workflow_object_type":$('#workflow_object_type').val(),"form_id":$('#form_id').val(),"document_id":$('#document_id').val(),"workflow_stages":nodes.get(),"workflow_edges":edges.get(),"deadline":$('#deadline').val(),"deadline_value":$('#deadline_value').val(),"deadline_type":$('#deadline_type').val(),"departments":departments,"department_users":department_users,
            "view_departmentid[]":view_departmentid,"view_userid[]":view_userid,"view_authentication":view_authentication,"delete_departmentid":delete_departmentid,"delete_userid":delete_userid,
            "delete_authentication":delete_authentication,"edit_departmentid":edit_departmentid,
            "edit_userid":edit_userid,"edit_authentication":edit_authentication,"add_departmentid":add_departmentid,
            "add_userid":add_userid,"add_authentication":add_authentication
            };
            // console.log(data);
            $.ajax({
                method: "POST",
                url: form_url,
                data: data,
                dataType: 'json',
                success: function (msg) {
                    $(".preloader").css("display", "none");
                    $(".save_workflow").attr("disabled", false);
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

                    $("#add_departmentid").attr('required', 'required').parsley();
                    $("#add_userid").attr('required', 'required').parsley();
                    $("#edit_departmentid").attr('required', 'required').parsley();
                    $("#edit_userid").attr('required', 'required').parsley();
                    $("#delete_departmentid").attr('required', 'required').parsley();
                    $("#delete_userid").attr('required', 'required').parsley();
                    $("#view_departmentid").attr('required', 'required').parsley();
                    $("#view_userid").attr('required', 'required').parsley();
                }
            });
        }else{
            $(".preloader").css("display", "none");
            $(".save_workflow").attr("disabled", false);
            console.log("Validation failed");
        }
    });


    $('#closed_workflow_name').parsley().on('form:validate', function (formInstance) {
       // If any of these fields are valid
       /*
        if ($("#add_departmentid").parsley().isValid() || $("#add_userid").parsley().isValid()){
            $("#add_departmentid").removeAttr('required').parsley().destroy();
            $("#add_userid").removeAttr('required').parsley().destroy();
        }else{
            $("#add_departmentid").attr('required', 'required').parsley();
            $("#add_userid").attr('required', 'required').parsley();
        }
        if ($("#edit_departmentid").parsley().isValid() || $("#edit_userid").parsley().isValid()){
            $("#edit_departmentid").removeAttr('required').parsley().destroy();
            $("#edit_userid").removeAttr('required').parsley().destroy();
        }else{
            $("#edit_departmentid").attr('required', 'required').parsley();
            $("#edit_userid").attr('required', 'required').parsley();
        }
        if ($("#delete_departmentid").parsley().isValid() || $("#delete_userid").parsley().isValid()){
            $("#delete_departmentid").removeAttr('required').parsley().destroy();
            $("#delete_userid").removeAttr('required').parsley().destroy();
        }else{
            $("#delete_departmentid").attr('required', 'required').parsley();
            $("#delete_userid").attr('required', 'required').parsley();
        }
        if ($("#view_departmentid").parsley().isValid() || $("#view_userid").parsley().isValid()){
            $("#view_departmentid").removeAttr('required').parsley().destroy();
            $("#view_userid").removeAttr('required').parsley().destroy();   
        }else{
            $("#view_departmentid").attr('required', 'required').parsley();
            $("#view_userid").attr('required', 'required').parsley();
        }     */      
        return;
    });


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
   
   

  

   $(document).on("change","#workflow_object_type",function(e) {
    var wf_object_type=  $(this).val();
     //console.log(wf_object_type);
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
                        
                   var stage_option = '<option value="">Select</option>';
    
    $.each(msg.wf_objects, function(i, item) {
      var selected='';
      /*console.log("wf_object_type_id"+wf_object_type_id);*/
      if(wf_object_type_id == item.object_id)
      {
        
        selected='selected="selected"';
      }
      stage_option += '<option value="' + item.object_id + '" ' + selected + '>' + item.object_name + '</option>';
      
       });

                   if(wf_object_type == 'form')
                  {  
                       $('#form_id').html(stage_option);
                  }
                  else if(wf_object_type == 'document')
                  {  
                       $('#document_id').html(stage_option);
                  }
                  $('input').change(function() { 
                    $("#is_edit_true").val(1);
                  });
                }
              
            }
        });
    
   }; 

   
   

  var wf_manager_users = function(task) 
   {
   
    department_users = task.department_users;
    selectedusers = task.sel_department_users;
    selecteddept = task.sel_departments;
    sel_count = task.sel_count;
    var users_option = '<select name="wf_manager_users'+sel_count+'" id="wf_manager_users'+sel_count+'" class="form-control wf_manager_users" multiple="multiple" data-count="'+sel_count+'">';
    /*console.log(department_users);*/
    $.each(department_users, function(i, item) {
      
      var user_permission = item.user_permission;
      var workflow_module='workflow';
      if(user_permission.indexOf(workflow_module) != -1)
      {
                  var selected = '';
                  /*console.log('id'+item.id);*/
                  var user_id = item.id.toString();
                  if($.inArray(user_id, selectedusers) !== -1)
                  {
                    var selected = 'selected="selected"';

                  }
                  /*console.log('selected'+selected);*/
                  users_option += '<option value="' + item.id + '" '+selected+'>' + item.user_full_name+'</option>';
                  /*console.log("----"+item.user_full_name+"-----");*/
      }
                });
                
                users_option += '</select>';
                /*console.log(users_option);*/
                $('#clear_user'+sel_count).html(users_option);
                $('#wf_manager_users'+sel_count).fastselect(); 
   };

    $(document).on("change",".wf_managers",function(e) {
    
        /*console.log("div"+$(this).attr("data-count"));*/
        var sel_count = $(this).attr("data-count");
        var departments = $(this).val();
        var sel_department_users = $('#wf_manager_users'+sel_count).val();
        var sel_departments = $().val();
        var form_url = "@php echo URL('load_department_users'); @endphp";
        
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var data = {_token: CSRF_TOKEN,"departments":departments};
        $.ajax({
            method: "POST",
            url: form_url,
            data: data,
            dataType: 'json',
            success: function (msg) 
            {
                
                if(msg.status == 1)
                {
                 var users_options= {sel_count:sel_count,department_users: msg.department_users, sel_department_users: sel_department_users}
                wf_manager_users(users_options);

                if(sel_count != 0)
                {
                  console.log("update edge");
                  nodes.update({
                    id: sel_count,
                    sel_departments: departments
                });
                }
                }
              
            }
        });
     });

    $(document).on("change",".wf_manager_users",function(e) {
    
        /*console.log("div"+$(this).attr("data-count"));*/
        var sel_count = $(this).attr("data-count");
        var departments_users = $(this).val();
        if(sel_count != 0)
                {
                  /*console.log("update edge");*/
                  nodes.update({
                    id: sel_count,
                    sel_department_users: departments_users
                });
                }
     });

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

  });

  
</script>
   
  @endsection