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


  </style>

<section class="content">
  <div class="box box-info">
    <div class="box-header with-border">
      <h3 class="box-title">{{ Lang::get('language.workflow_new_template') }}</h3>
      <!-- <p class="help-block">{{$language['form_help1']}}</p> -->
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <form role="form" id="closed_workflow_name" name="closed_workflow_name" data-parsley-validate="data-parsley-validate">
      <input type="hidden" name="workflow_id" id="workflow_id" class="workflow_id" value="{{$workflow_id}}">
      <div class="box-body">
        <div class="col-md-12 alert_space">
        </div>

        <div class="col-md-5">
          <div style="position: fixed; width: 30%;">
            <div class="form-group">        
              <ul class="products-list product-list-in-box workflow_template">
                 

                   <!-- /.item -->
                    <li class="item li_active wf_li" data-liid="0" id="wf_title_main">
                      <div class="product-img">
                        <i class="fa fa-play fa-2x"></i>
                      </div>
                      <div class="product-info">
                        <h2 class="item_header" id="wf_name_label">&nbsp;</h2>
                      </div>
                    </li>
              </ul>
            </div>
            <div class="form-group">
             <button type="button" class="btn btn-primary btn-sm add_task" data-wf_type="node"><i class="fa fa-plus"></i> Add Stage</button>
             <!-- <button type="button" class="btn btn-primary btn-sm add_task" data-wf_type="edge"><i class="fa fa-plus"></i> Add Actions</button> -->

             <button type="button" class="btn btn-warning btn-sm preview"><i class="fa fa-eye"></i> Preview</button>

             <button type="button" class="btn btn-primary btn-sm  save_workflow">Save Template</button>
            </div>
          </div>
        </div>  

        <div class="col-md-7 li_active">
          <div class="row right_wft_col clearfix">
            <div class="col-md-12 taskdiv" id="rwf_div0">  
              <h3 class="" >Workflow Details</h3>
              <div class="form-group">
                <label>{{$language['workflow name']}}: <span class="compulsary">*</span></label>
                 <input type="text" class="form-control" id="workflow_name" name="workflow_name" required="" data-parsley-required-message="Workflow name is required" data-parsley-trigger="change focusout">
              </div>


              <div class="form-group">
                <label>Tasks can be completed: <span class="compulsary">*</span></label>
                 <select name="workflow_flow_type" id="workflow_flow_type" class="form-control select2" required=""  data-parsley-required-message="Workflow flow type is required" data-parsley-trigger="change focusout">
                <option value="1">In any order</option>
                <option value="2" selected="selected"> One by one</option>
                </select>
              </div>

              <div class="form-group">
                <label>Workflow Object Type: <span class="compulsary">*</span></label>
                 <select name="workflow_object_type" id="workflow_object_type" class="form-control select2" required=""  data-parsley-required-message="Form Workflow type is required" data-parsley-trigger="change focusout">
                  <option value="">Select workflow object type</option>
               <!--  <option value="normal">Normal workflow</option> -->
                <option value="form">Form  based workflow</option>
                <option value="document">Document based workflow</option>
                </select>
              </div>


              <div class="form-group object_type_row form_row">
                <label>Form Name: <span class="compulsary">*</span></label>
                 <select name="form_id" id="form_id" class="form-control select2 rule_components" data-object_type="form">
                <option value="">Select</option>
                </select>
              </div>

              <div class="form-group object_type_row document_row">
                <label>Document Type: <span class="compulsary">*</span></label>
                 <select name="document_id" id="document_id" class="form-control select2 rule_components" data-object_type="document">
                <option value="">Select</option>
                </select>
              </div>
              

              <div class="form-group">
                <label>{{$language['color']}}: <span class="compulsary">*</span></label>
                <div id="cp2" class="input-group colorpicker-component" title="Using input value">
                  <input type="text" name="color" id="workflow_color" class="form-control" value="#C0C0C0" readonly="" />
                  <span class="input-group-addon"><i></i></span>
                </div>
              </div>

              <div class="form-group" style="display: none;">
                <label>On Submission: </label>
                <div id="builder0" class="query-builder form-inline"></div>
              </div>
                <!--- TABS START -->
                <div class="form-group">
                  <h4 class="box-title">{{$language['wf_permissions']}}</h4>
                    <p style="font-size:12px; color:#999;">{{$language['wf_permsn_info']}}</p>
                  <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs pull-left ui-sortable-handle">
                        <li class="active"><a href="#add-tab" data-toggle="tab" class="smtp-btn" aria-expanded="true">Add</a></li>
                        <li class=""><a href="#edit-tab" data-toggle="tab" class="smtp-btn" aria-expanded="false">Edit</a></li>
                        <li class=""><a href="#delete-tab" data-toggle="tab" class="smtp-btn" aria-expanded="false">Delete</a></li>
                        <li class=""><a href="#view-tab" data-toggle="tab" class="smtp-btn" aria-expanded="false">View</a></li>
                    </ul>
                    <div class="tab-content no-padding"> 
                        <!-- view -->
                        <div class="chart tab-pane" id="view-tab">
                            <div class="row form_row">
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Departments: </label>
                                    <div class="col-sm-9">
                                        <select name="view_departmentid[]" id="view_departmentid" class="multipleSelect form-control" multiple >
                                            <?php foreach ($deptApp as $key => $dept) { ?>
                                                <option value="<?php echo $dept->department_id; ?>"><?php echo $dept->department_name;?></option>
                                            <?php
                                            }
                                            ?>
                                        </select> 
                                        <p style="font-size:12px; color:#999;">Choose department(s) who can start a Workflow process</p>  
                                    </div>
                                </div>
                            </div>

                            <div class="row form_row">
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Users: </label>
                                    <div class="col-sm-9">
                                        <select class="form-control multipleSelect" id="view_userid"   name="view_userid[]" multiple >
                                            <?php
                                            foreach ($users as $key => $user) {
                                            ?>
                                                <option value="<?php echo $user->id; ?>"><?php echo $user->user_full_name;?> - {{ $user->departments[0]->department_name }}</option>
                                            <?php
                                            }
                                            ?>  
                                        </select> 
                                        <p style="font-size:12px; color:#999;">Choose user(s) who can start a Workflow process</p>
                                    </div>
                                </div>
                            </div>
                            <!--
                            <div class="row form_row">
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label"></label>
                                    <div class="col-sm-9"> 
                                        <input type="checkbox" id="view_authentication"  name="view_authentication" data-original-title="" title="" value="view" checked>  Enable view permission
                                    </div>
                                </div>
                            </div> -->
                        </div>
                        <!-- Delete -->
                        <div class="chart tab-pane" id="delete-tab">
                            <div class="row form_row">
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Departments: </label>
                                    <div class="col-sm-9">
                                        <select name="delete_departmentid[]" id="delete_departmentid" class="multipleSelect form-control" multiple >
                                            <?php
                                            foreach ($deptApp as $key => $dept) {
                                            ?>
                                            <option value="<?php echo $dept->department_id; ?>"><?php echo $dept->department_name;?></option>
                                            <?php
                                            }
                                            ?>
                                        </select> 
                                        <p style="font-size:12px; color:#999;">Choose department(s) who can delete a Workflow process</p>  
                                    </div>
                                </div>
                            </div>
                            <div class="row form_row">
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Users: </label>
                                    <div class="col-sm-9">
                                        <select class="form-control multipleSelect" id="delete_userid"   name="delete_userid[]" multiple >
                                            <?php
                                            foreach ($users as $key => $user) {
                                            ?>
                                            <option value="<?php echo $user->id; ?>"><?php echo $user->user_full_name;?> - {{ $user->departments[0]->department_name }}</option>
                                            <?php
                                            }
                                            ?> 

                                        </select> 
                                        <p style="font-size:12px; color:#999;">Choose user(s) who can delete a Workflow process</p>
                                    </div>
                                </div>
                            </div>
                            <!--
                            <div class="row form_row">
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label"></label>
                                    <div class="col-sm-9"> 
                                        <input type="checkbox" id="delete_authentication"  name="delete_authentication" data-original-title="" title="" value="delete" checked>  Enable delete permission
                                    </div>
                                </div>
                            </div> -->
                        </div>

                        <!-- Edit -->
                        <div class="chart tab-pane" id="edit-tab">
                            <div class="row form_row">
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Departments: </label>
                                    <div class="col-sm-9">
                                        <select name="edit_departmentid[]" id="edit_departmentid" class="multipleSelect form-control" multiple >
                                            <?php
                                            foreach ($deptApp as $key => $dept) {
                                            ?>
                                            <option value="<?php echo $dept->department_id; ?>"><?php echo $dept->department_name;?></option>
                                            <?php
                                            }
                                            ?>
                                        </select> 
                                        <p style="font-size:12px; color:#999;">Choose department(s) who can edit a Workflow process</p>  
                                    </div>
                                </div>
                            </div>
                            <div class="row form_row">
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Users: </label>
                                    <div class="col-sm-9">
                                        <select class="form-control multipleSelect" id="edit_userid"   name="edit_userid[]" multiple >
                                            <?php
                                            foreach ($users as $key => $user) {
                                            ?>
                                            <option value="<?php echo $user->id; ?>"><?php echo $user->user_full_name;?> - {{ $user->departments[0]->department_name }}</option>
                                            <?php
                                            }
                                            ?>  
                                        </select> 
                                        <p style="font-size:12px; color:#999;">Choose user(s) who can edit a Workflow process</p>
                                    </div>
                                </div>
                            </div>
                            <!--
                            <div class="row form_row">
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label"></label>
                                    <div class="col-sm-9"> 
                                        <input type="checkbox" id="edit_authentication"  name="edit_authentication" data-original-title="" title="" value="edit" checked>  Enable edit permission
                                    </div>
                                </div>
                            </div> -->
                        </div>

                        <!--Add-->
                        <div class="chart tab-pane  active" id="add-tab">
                            <div class="row form_row">
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Departments: </label>
                                    <div class="col-sm-9">
                                        <select name="add_departmentid[]" id="add_departmentid" class="multipleSelect form-control" multiple >
                                            <?php
                                            foreach ($deptApp as $key => $dept) {
                                            ?>
                                            <option value="<?php echo $dept->department_id; ?>"><?php echo $dept->department_name;?></option>
                                            <?php
                                            }
                                            ?>
                                        </select> 
                                        <p style="font-size:12px; color:#999;">Choose department(s) who can add a Workflow process</p>  
                                    </div>
                                </div>
                            </div>

                            <div class="row form_row">
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Users: </label>
                                    <div class="col-sm-9">
                                        <select class="form-control multipleSelect" id="add_userid"   name="add_userid[]" multiple >
                                            <?php
                                            foreach ($users as $key => $user) {
                                            ?>
                                            <option value="<?php echo $user->id; ?>"><?php echo $user->user_full_name;?> - {{ $user->departments[0]->department_name }}</option>
                                            <?php
                                            }
                                            ?>  
                                        </select> 
                                        <p style="font-size:12px; color:#999;">Choose user(s) who can add a Workflow process</p>
                                    </div>
                                </div>
                            </div>
                            <!--
                            <div class="row form_row">
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label"></label>
                                    <div class="col-sm-9"> 
                                        <input type="checkbox" id="add_authentication"  name="add_authentication" data-original-title="" title="" value="add" checked>  Enable add permission
                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </div><!--//End Tabs within a box -->
                    <!-- /.tab-content -->
                  </div>
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
          </div> <!-- ROW -->

       
          <div class="box-footer" style="">
            <div class="row right_wft_col">
              <div class="box box-solid">
                <div class="box-header with-border">
                  <h3 class="box-title">Preview</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body text-center">
                  <div id="mynetwork"></div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                  <button type="button" class="btn btn-primary btn-sm  save_workflow">Save Template</button>
                </div>
              </div>
            </div>
          </div>
        </div> 
      </div>
    </form>
  </div>
</section>
   

    {!! Html::script('js/jquery-ui.min.js') !!}
    <script>



 $(document).ready(function() {
  $('.multipleSelect').fastselect();
  /*$("#wf_title_main").click(function() {
    $(".wf_permssn_forms").show();
  });
  $(".add_task, .preview, .product-info").click(function() {
    $(".wf_permssn_forms").hide();
  }); */
  var stage_users_options='';
   @php foreach ($users as $key => $user){ @endphp
stage_users_options += @php echo "'<option value=\"$user->id\">$user->user_full_name</option>'"; @endphp;
  @php } @endphp              
  var stage_color = $("#workflow_color").val(); 
  var workflow_id = $('#workflow_id').val();  
  var stage_option = '<option value="">Select a stage</option>';
  var taskcount = 0;
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
    "in": "in",
    "not_in": "not in",
    "less": "less",
    "less_or_equal": "less or equal",
    "greater": "greater",
    "greater_or_equal": "greater or equal",
    "between": "between",
    "not_between": "not between",
    "begins_with": "begins with",
    "not_begins_with": "doesn't begin with",
    "contains": "contains",
    "not_contains": "doesn't contain",
    "ends_with": "ends with",
    "not_ends_with": "doesn't end with",
    "is_empty": "is empty",
    "is_not_empty": "is not empty",
    "is_null": "is null",
    "is_not_null": "is not null"
  };
  var rules_basic_base = {condition: 'AND',rules:[]};
 var operator_options ='';
 var rule_components=[];
  $.each(rule_operators, function(i, item)
              {
                /*console.log(item);*/
                 operator_options +='<option value="'+i+'">'+item+'</option>';
              });

  $(".add_new_transition").hide();
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
    var rules_basic = rule_arry.rules_basic;

    rule_count++;
    var data_attr2 = 'data-stageid="'+stageid+'" data-edgeid="'+edgeid+'" data-level="'+level+'"';

    var data_attr1 = 'data-rc="'+rule_count+'" ';

    html='<dl id="builder_group_'+rule_count+'" class="rules-group-container">';
    html+='<dt class="rules-group-header">';
    html+='<div class="btn-group pull-right group-actions">';
    html+='<button type="button" class="btn btn-xs btn-success add_rule_template" id="add_rule_template'+edgeid+'" data-add="rule" '+data_attr1+' '+data_attr2+'><i class="fa fa-plus"></i> Add rule </button>';
    if(first != 0)
    {
    /*html+='<button type="button" class="btn btn-xs btn-success add_rule_template" data-add="group" '+data_attr1+' '+data_attr2+'><i class="fa fa-plus"></i> Add group</button>';*/
    }
    if(first == 0)
    {
      html+='<button type="button" class="btn btn-xs btn-danger delete_rule_template" data-delete="group" '+data_attr1+' '+data_attr2+'><i class="fa fa-trash"></i></button>';
    }
    

    html+='</div>';
    
    html+='<div class="rule-filter-container">';
        html+='<select class="form-control group_filter_update" name="builder_group_'+rule_count+'_cond" id="builder_group_'+rule_count+'_cond" '+data_attr1+' '+data_attr2+'><option value="AND">AND</option><option value="OR">OR</option></select>';
    html+='</div>';
    html+='<div class="error-container" data-toggle="tooltip"><i class="fa fa-warning"></i></div>';
    html+='</dt>';
    html+='<dd class="rules-group-body" id="dd_rule_group_'+rule_count+'">';
    html+='<ul class="rules-list" id="ul_rule_group_'+rule_count+'"></ul>';
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

    var data_attr1 = 'data-rc="'+rule_count+'" ';

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
        var dbid = task.dbid;
        if(wf_type == 'edge')
        {
            addAction(task);
        }
        sel_departments = task.sel_departments;
        sel_department_users = task.sel_department_users;
        department_users = task.department_users;

        wfTRHTML +='<li class="item wf_li" data-liid="'+id+'" id="wf_li'+id+'" data-wf_type="'+wf_type+'">';
        wfTRHTML +='<div class="product-img">';
        wfTRHTML +='<i class="fa fa-list fa-2x"></i>';
        wfTRHTML +='</div>';

        wfTRHTML +='<div class="product-info">';
        wfTRHTML +='<a href="javascript:void(0)" class="product-title" id="label'+id+'">'+task.label+'</a><span class="pull-right action_span"><i class="fa fa-trash-o fa-2x deleteState" data-liid="'+id+'"></i></span>';         
        wfTRHTML +='</div>';

        wfTRHTML +='</li>'; 
        $('.workflow_template').append(wfTRHTML); 

        var wfTLHTML =''; 
        wfTLHTML +='<div class="col-md-12 taskdiv" id="rwf_div'+id+'" style="display:none;">';  
        wfTLHTML +='<h3 class="">Stage Name: '+task.label+'</h3>';
        wfTLHTML +='<div class="form-group">';
        wfTLHTML +='<label>Stage Name</label>';
        wfTLHTML +='<input type="text" class="form-control stage_label_input" id="task_label'+id+'" name="task_label[]" value="'+task.label+'" data-count="'+id+'" data-wf_type="'+wf_type+'">';
        wfTLHTML +='</div>';

        wfTLHTML +='<div class="form-group">';
        wfTLHTML +='<label>Stage Description</label>';
        wfTLHTML +='<textarea class="form-control" id="task_details" name="task_details[]">'+task.description+'</textarea>';
        wfTLHTML +='</div>';

        /////////////////////workflow actions///////////////////////
        wfTLHTML +='<div class="form-group" id="byuser">';
        wfTLHTML +='<label>Assigned users</label>';
        wfTLHTML +='<div class="full_widht" >';
        wfTLHTML +='<select name="stage_user'+id+'" id="stage_user'+id+'" class="form-control stage_users" multiple="multiple" style="width:100%;" data-stageid="'+id+'">'+stage_users_options+'</select>';
        wfTLHTML +='</div>';


        wfTLHTML +='<div class="form-group" >';
        wfTLHTML +='<div class="box box-solid">';
        wfTLHTML +='<div class="box-header with-border"><h3 class="box-title">Stage Actions</h3></div>';
        wfTLHTML +='<div class="box-group" id="stage_action'+id+'">';
        wfTLHTML +='</div>';
        wfTLHTML +='<div class="box-footer">';
        wfTLHTML +='<button type="button" class="btn btn-primary btn-sm add_action" data-wf_type="edge" data-stage="'+id+'" id="add_action'+id+'"><i class="fa fa-plus"></i> Add Action</button>';
        wfTLHTML +='</div>'; 
        wfTLHTML +='</div>';
        wfTLHTML +='</div>';
        wfTLHTML +='</div>';
        $('.right_wft_col').append(wfTLHTML); 
        $('#stage_user'+id).fastselect({
            placeholder: 'Select users',
            loadOnce: false
        });
        $.each(sel_department_users, function(i, item)
        {
            /*console.log(item);*/
            $('#stage_user'+id).data('fastselect').setSelectedOption($('#stage_user'+id+' option[value='+item+']').get(0));
            $("#stage_user"+id).val(item);  
        });

        nodes.add({
            id: id,
            dbid:dbid,
            label: task.label,
            shape: 'box',
            color:task.color,
            sel_departments:sel_departments,
            sel_department_users:sel_department_users
        });
        return wfTLHTML;
    }

    var addAction = function(task) 
   {
          
      var id=task.id;
          var action_html =''; 
      var wf_type = task.type;
      var dbid = task.dbid;
      var from_id =task.from;
      var with_rule = parseInt(task.with_rule);
      var rules_basic = task.rules_basic;

      $('.collapse'+from_id).collapse('hide');
      var common_data='data-stageid="'+from_id+'" data-edgeid="'+id+'"';
    var stage_option = '<option value="">Select Stage</option>';
    var response = nodes.get();
    $.each(response, function(i, item) {
      stage_option += '<option value="' + item.id + '">' + item.label + '</option>';
      
       });
     var action_html ='<div class="panel box">';
                  action_html +='<div class="box-header with-border">';
                    action_html +='<h4 class="box-title">';
                      action_html +='<a data-toggle="collapse" data-parent="#accordion" href="#collapse'+id+'" id="task_heading'+id+'" >'+task.label+'</a>';
                    action_html +='</h4>';
                  action_html +='</div>';
                  action_html +='<div id="collapse'+id+'" class="panel-collapse collapse'+from_id+' collapse  in">';
                    action_html +='<div class="box-body">';
                    /* row START */
                    action_html +='<div class="row">';
          
        action_html +='<div class="col-md-6" id="">'; 
        action_html +='<label>Action Name</label>';
        action_html +='<input type="text" class="form-control task_label_input" id="task_label'+id+'" name="task_label[]" value="'+task.label+'" '+common_data+' data-wf_type="'+wf_type+'">';
        action_html +='</div>';

        action_html +='<div class="col-md-6" id="">';           
        action_html +='<label>Rule</label>';
        action_html +='<select name="rule_'+id+'" class="form-control rule_change" '+common_data+'>';
        var selected='';
        var rule_style='display:none';
        if(with_rule == 0)
        {
          selected='selected="selected"';
          rule_style='display:none';
        }
        action_html +='<option value="0" '+selected+'>Without Rule</option>';
        var selected='';
        if(with_rule == 1)
        {
          selected='selected="selected"';
          rule_style='display:block';
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
         var rules_template = {condition: rules_basic.condition,rules:[]};
         var rule_arry = {"stageid":from_id,"edgeid":id,"first":1,"parentid":0,"level":0,"rules_basic":rules_template}
         var temp = AddGroupTemplate(rule_arry);
         action_html +='<div id="builder'+id+'" class="query-builder form-inline">'+temp+'</div>';
        /* Tree View END*/
          
          action_html +='</div>';
          action_html +='</div>';
          /* row END */
           /* row START */
                    action_html +='<div class="row">';
          
        action_html +='<div class="col-md-6" id="">'; 
        action_html +='<label>To Stage:<span class="compulsary">*</span></label>';
        action_html +='<input type="hidden" name="from_satge" id="from_satge'+id+'">'; 
        action_html +='<select name="to_satge" id="to_satge'+id+'" class="form-control select2 stages_option stages_option'+from_id+'" '+common_data+'>';
        action_html +=stage_option;            
        action_html +='</select>';
        action_html +='</div>';


        action_html +='</div>';
                    /* row END */
                    action_html +='</div>';
                  action_html +='</div>';
                action_html +='</div>';

$('#stage_action'+from_id).append(action_html);  
          
          //$('#from_satge'+id).val(task.from);
          $('#to_satge'+id).val(task.to);
          console.log("start");
      var rules_template = {condition: rules_basic.condition,rules:[]};
      
      edges.add({
                      id: id,
                      dbid:dbid,
                      label:task.label,
                      from: task.from,
                      to: task.to,
                      arrows:'to',
                      with_rule:with_rule,
                      rules_basic:rules_template
                  });
      console.log("#builder_group_"+rule_count+"_cond");
          $("#builder_group_"+rule_count+"_cond").val(rules_basic.condition).trigger('change');
          console.log($("#builder_group_"+rule_count+"_cond").val());
          $.each(rules_basic.rules, function(i, item)
              {
                 console.log("i"+i);
                 $("#add_rule_template"+id).trigger('click');
                 $("#builder_rule_"+rule_count+"_filter").val(item.id)
    .trigger('change');
    $("#builder_rule_"+rule_count+"_operator").val(item.operator)
    .trigger('change');
    $("#builder_rule_value_"+rule_count).val(item.value)
    .trigger('change');

              }); 

          console.log(edges.get(id));
          return action_html;
   } 

   /* Load work flow template */
  var loadWorkflowtemplate = function(wf) 
   {
      var loadformurl = "@php echo URL('load_Workflow_json'); @endphp";
      console.log("loadformurl"+loadformurl);
      $.getJSON(loadformurl+'?workflow_id=' + wf, function(data) {
        taskcount = data.taskcount;

        edgecount = data.edgecount;
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
        rule_options='<option value="-1">------</option>';  
              $.each(rule_components, function(i, item)
              {
                /*console.log(item);*/
                 rule_options +='<option value="'+item.id+'" data-column_type="'+item.column_type+'" data-key="'+i+'">'+item.column_name+'</option>';
              });

        $.each(nodeset, function(i, item)
        {
          console.log(item);
          var taskhtml = addTask(item);
          taskcount++;   
        });
    console.log('--------------------');
     $.each(edgeset, function(i, item)
        {
          
          console.log(item);
         // $("#add_action"+item.from).trigger('click');
          var taskhtml = addAction(item);
          taskcount++;   
        });
        load_workflow_objects();
     //nodes.add(nodeset);
         //edges.add(edgeset);
    
      //$('#wf_managers').fastselect();

      /*Load Previlages users add/edit/view/delete*/
      var a = [];
        var b = [];
        var c = [];
        var d = [];
        var e = [];
        var f = [];
        var g = [];
        var h = [];
        $.each(data.wf_privilages, function(i, items)
        {
            if(items.privilege_key == 'add'){
                $.each(items.privilege_department_array, function(i, item)
                {
                    if(item != ''){
                    $('#add_departmentid').data('fastselect').setSelectedOption($('#add_departmentid option[value='+item+']').get(0));
                    a.push(item);}
                    
                });
                $("#add_departmentid").val(a);
                $.each(items.privilege_user_array, function(i, item)
                {
                    if(item != ''){
                    $('#add_userid').data('fastselect').setSelectedOption($('#add_userid option[value='+item+']').get(0));
                     b.push(item);}
                });
                $("#add_userid").val(b);
                if(items.privilege_status == 1)
                {
                    $('#add_authentication').prop('checked', true);
                }
            }
            else if(items.privilege_key == 'edit'){
                $.each(items.privilege_department_array, function(i, item)
                {
                    if(item != ''){
                    $('#edit_departmentid').data('fastselect').setSelectedOption($('#edit_departmentid option[value='+item+']').get(0));
                    c.push(item);}
                });
                $("#edit_departmentid").val(c); 
                $.each(items.privilege_user_array, function(i, item)
                {
                    if(item != ''){
                    $('#edit_userid').data('fastselect').setSelectedOption($('#edit_userid option[value='+item+']').get(0));
                    d.push(item);}
                });
                $("#edit_userid").val(d); 
                if(items.privilege_status == 1)
                {
                    $('#edit_authentication').prop('checked', true);
                }
            }
            else if(items.privilege_key == 'delete'){
                $.each(items.privilege_department_array, function(i, item)
                {
                    if(item != ''){
                    $('#delete_departmentid').data('fastselect').setSelectedOption($('#delete_departmentid option[value='+item+']').get(0));
                    e.push(item);}
                });
                $("#delete_departmentid").val(e); 
                $.each(items.privilege_user_array, function(i, item)
                {
                    if(item != ''){
                    $('#delete_userid').data('fastselect').setSelectedOption($('#delete_userid option[value='+item+']').get(0));
                    f.push(item);}
                });
                $("#delete_userid").val(f); 
                if(items.privilege_status == 1)
                {
                    $('#delete_authentication').prop('checked', true);
                }
            }
            else if(items.privilege_key == 'view'){
                $.each(items.privilege_department_array, function(i, item)
                {
                    if(item != ''){
                    $('#view_departmentid').data('fastselect').setSelectedOption($('#view_departmentid option[value='+item+']').get(0));
                    g.push(item);}
                });
                $("#view_departmentid").val(g); 
                $.each(items.privilege_user_array, function(i, item)
                {
                    if(item != ''){
                    $('#view_userid').data('fastselect').setSelectedOption($('#view_userid option[value='+item+']').get(0));
                     h.push(item);}
                });
                $("#view_userid").val(h);
                if(items.privilege_status == 1)
                {
                    $('#view_authentication').prop('checked', true);
                }
            }
        });
        /*Load Previlages users add/edit/view/delete*/
        
      });
   };      
   loadWorkflowtemplate(workflow_id);

   /* Click Function on Stage List (LEFT SIDE) */
   $(document).on("click",".wf_li",function(e) {
       var liid =$(this).attr('data-liid'); 
       var wf_type =$(this).attr('data-wf_type'); 
       console.log(liid);
       $(".taskdiv").hide(); /* Hide All Other Right DIV */
       $("#rwf_div"+liid).show(); /* Show Only Current Right DIV */
        $(".wf_li").removeClass("li_active");
        $(this).addClass("li_active");
        var from_satge = liid; 
        var stage_option = '<option value="">Select</option>';
        var response = nodes.get();
        $.each(response, function(i, item) {
        stage_option += '<option value="' + item.id + '">' + item.label + '</option>';
       });
        $( ".stages_option"+liid).each(function( index )
        {
            
            var edgeid =$(this).attr('data-edgeid');
            var to_satge = $(this).val(); 
            $('#to_satge'+edgeid).html(stage_option);
            $("#to_satge"+edgeid).val(to_satge); 

        });
     });

   /* Preview OF Workflow Template */
   $(document).on("click",".preview",function(e) {
        $(".taskdiv").hide();
        $("#preview_div").show();
        $(".wf_li").removeClass("li_active");
     });

  

    $(document).on("click",".add_task",function(e) {
    
    taskcount++;
    var wf_type =$(this).attr('data-wf_type'); 
      var label='Stage';
      var empty=[];
      var task= {id: taskcount, dbid: 0, label: label, shape: "box", color: "#c0c0c0", type: wf_type, description:'',department_users:empty,sel_departments:empty,sel_department_users:empty}
      addTask(task);
     $( "#wf_li"+taskcount).trigger( "click" );
     });


     $(document).on("click",".add_action",function(e) {
    
    taskcount++;
    var wf_type ='edge'; 
    var from_id =parseInt($(this).attr('data-stage')); 
      var label='Action';
      var rules_basic_base = {condition: 'AND',rules:[]};
      var task= {id: taskcount, dbid: 0, label: label, type: wf_type, from: from_id, to: 0, description:'',with_rule:0,rules_basic:rules_basic_base}
        addAction(task);
     });


    $(document).on("change",".rule_change",function(e) {
        var with_rule = parseInt($(this).val());
        var edge_id = $(this).attr('data-edgeid'); 
        if(with_rule == 1)
        {
          $('#ruletable'+edge_id).show();
        }
        else
        {
          $('#ruletable'+edge_id).hide();
        } 

         
        if(edge_id != 0)
                {
                  console.log("update edges");
                  edges.update({
                    id: edge_id,
                    with_rule: with_rule
                });
                }
        
     });

     var updateRule = function(rule_arry) 
   {
    var stageid = rule_arry.stageid;
    var edgeid = rule_arry.edgeid;
    var rc = rule_arry.rc;
    edge_data = edges.get(edgeid);
     $.each(edge_data.rules_basic.rules, function(i, item)
              {
                console.log(item);
                
                if(item.rc == rc)
                {
                  var filter_id = $("#builder_rule_"+rc+"_filter").val();
                  var column_type = $("#builder_rule_"+rc+"_filter").find(':selected').attr('data-column_type'); 
                  var operator = $("#builder_rule_"+rc+"_operator").val(); 
                  var value = $("#builder_rule_value_"+rc).val();
                  item.id = filter_id;
                  item.operator = operator;
                  item.value = value;
                  console.log(item);
                }
                 
              });
     console.log(edge_data);
  };

  

    $(document).on("change",".rule_filter",function(e) {
        var lable = parseInt($(this).val());
        var stageid = $(this).attr('data-edgeid'); 
        var edgeid = $(this).attr('data-edgeid'); 
        var rc = $(this).attr('data-rc');

        var data_attr2 = 'data-stageid="'+stageid+'" data-edgeid="'+edgeid+'" ';

    var data_attr1 = 'data-rc="'+rc+'" ';

        var filter_option_key = $(this).find(':selected').attr('data-key'); 
       /* console.log(filter_option_key);
        console.log(rule_components[filter_option_key].column_type);*/
        if (typeof filter_option_key === "undefined") 
        {

          value_html +='<input class="form-control rule_filter_update" type="text" name="builder_rule_value_'+rc+'" id="builder_rule_value_'+rc+'" '+data_attr1+'" '+data_attr2+'>'; 
    
        }
        else
        {

          var column_type = rule_components[filter_option_key].column_type;
        var type_options = JSON.parse(rule_components[filter_option_key].type_options);
        var value_html='';
        if(column_type == 'select' || column_type == 'piclist')
        {
          value_html +='<select class="form-control rule_filter_update" name="builder_rule_value_'+rc+'" id="builder_rule_value_'+rc+'" '+data_attr1+'" '+data_attr2+'>'; 
              $.each(type_options, function(i, item)
              {
                /*console.log(item);*/
                 value_html +='<option value="'+item.label+'">'+item.label+'</option>';
              });
          value_html +='</select>';

        }
        else
        {
          value_html +='<input class="form-control rule_filter_update" type="text" name="builder_rule_value_'+rc+'" id="builder_rule_value_'+rc+'" '+data_attr1+'" '+data_attr2+'>'; 
        }

        }
        
        
         $('#rule-value-container_'+rc).html(value_html); 
         var rule_arry = {"stageid":stageid,"edgeid":edgeid,"rc":rc}
         updateRule(rule_arry); 
        
     });

    $(document).on("change keyup paste input",".rule_filter_update",function(e) {

          var rc =$(this).attr('data-rc');
       var stageid = $(this).attr('data-stageid');
       var edgeid = $(this).attr('data-edgeid');
         var rule_arry = {"stageid":stageid,"edgeid":edgeid,"rc":rc}
         updateRule(rule_arry); 
      
     });


    $(document).on("change",".group_filter_update",function(e) {

          var rc =$(this).attr('data-rc');
       var stageid = $(this).attr('data-stageid');
       var edgeid = $(this).attr('data-edgeid');
        edge_data = edges.get(edgeid);

    
    var filter_conditin = $("#builder_group_"+rc+"_cond").val();
    console.log("filter_conditin"+filter_conditin);
    edge_data.rules_basic.condition = filter_conditin;  
      
     });

    var deleteRule = function(rule_arry) 
   {
    var stageid = rule_arry.stageid;
    var edgeid = rule_arry.edgeid;
    var rc = rule_arry.rc;
    edge_data = edges.get(edgeid);
   

     $.each(edge_data.rules_basic.rules, function(i, item)
              {
                console.log(item);
        if (typeof item != "undefined") 
        {
                if(item.rc == rc)
                {
                  edge_data.rules_basic.rules.splice(i, 1);
                }
        }        
                 
              });
     console.log(edge_data);
  };

    $(document).on("click",".delete_rule_template",function(e) {

          var rc =$(this).attr('data-rc');
       var stageid = $(this).attr('data-stageid');
       var edgeid = $(this).attr('data-edgeid');
         var rule_arry = {"stageid":stageid,"edgeid":edgeid,"rc":rc}
         deleteRule(rule_arry); 
      
     });

    $(document).on("click",".add_rule_template",function(e) {
       console.log("hi");
       var rc =$(this).attr('data-rc');
       var addtype =$(this).attr('data-add');
       var stageid = $(this).attr('data-stageid');
       var edgeid = $(this).attr('data-edgeid');
       var level = $(this).attr('data-level');
       var nextlevel = parseInt(level)+1;
       var rule_arry = {"stageid":stageid,"edgeid":edgeid,"parentid":rc,"first":0,"level":nextlevel}
       var temp ='';
       if(addtype == 'rule')
       {
        /*rule_arry.level=level;*/
        rule_arry.rules_basic = rules_basic_base;
        var temp = AddRuleTemplate(rule_arry);
        
        edge_data = edges.get(edgeid);
        console.log(edge_data);
        trules_basic = edge_data.rules_basic;
     
        var rule_value = {rc:rule_count,id: '',operator: '',value:''};
        //trules_basic.rules.push(rule_value);
        edge_data.rules_basic.rules.push(rule_value);
         /*edges.update({
                        id: edgeid,
                        rules_basic: trules_basic
                    });*/
        console.log(edges.get(edgeid));
       }
       else if(addtype == 'group')
       {
        rule_arry.rules_basic = rules_basic_base;
        var temp = AddGroupTemplate(rule_arry);
       }
       
       $('#ul_rule_group_'+rc).append(temp);  
       
       
     });


    $(document).on("click",".delete_rule_template",function(e) {
       console.log("hi");
       var rc =$(this).attr('data-rc');
       var delete_type =$(this).attr('data-delete');
       var stageid = $(this).attr('data-stageid');
       var edgeid = $(this).attr('data-edgeid');
       var rule_arry = {"stageid":stageid,"edgeid":edgeid,"parentid":rc,"first":0}
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


     $(document).on("change keyup paste input",".task_label_input",function(e) {
    try {
        var edgeid = $(this).attr('data-edgeid');  
        var label = $('#task_label'+edgeid).val();
        var wf_type =$(this).attr('data-wf_type');
        edges.update({
                    id: edgeid,
                    label: label
                });
        console.log("#task_heading"+label);
    
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
        console.log("from_satge"+from_satge);
        console.log("edge_id"+edge_id);
        var to_satge = $('#to_satge'+edge_id).val();
        var from_satge_name = $("#task_label"+from_satge).val();
        var to_satge_name = $("#to_satge"+edge_id+" option:selected").text();
        if(from_satge && to_satge)
        {
        edges.update({
                      id: edge_id,
                      dbid:0,
                      label:$('#task_label'+edge_id).val(),
                      from:from_satge,
                      to: to_satge,
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
       var edge_id =$(this).attr('data-liid'); 

       swal({
        title:"{{$language['confirm_delete']}}",
        showCancelButton: true
        }).then((result) => {
        if(result){
         edges.remove({id: edge_id});
                $("#wf_li"+edge_id).remove();
        }
        else
        {
          //stay in same stage
        }
     });
      
     
     }); 

     


     $(document).on("change keyup paste input","#workflow_name",function(e) {
            var label = $(this).val();
            $("#wf_name_label").html(label);
      
     });

      $(document).on("change",".stage_users",function(e) {
            var label = $(this).val();
            console.log("label"+label);
        var stageid = $(this).attr("data-stageid");
        var stage_users = $(this).val();
        if(stageid != 0)
                {
                  console.log("update nodes");
                  nodes.update({
                    id: stageid,
                    sel_department_users: stage_users
                });
                }
      
     });



        ///OLD
 
  
    var s = $("#sticker");
    var pos = s.position();                    
   
  

  
 // console.log(" Scale "+network.getScale());
    
 
   var addNewState = function(newstate) 
   {
    nodecount++;
    try {
    var newtrHTML ='';
  var stage_id = nodecount; 
  var data_options ='data-stage="'+stage_id+'" data-edge=""  data-edge_label="Next" data-edge_from="'+stage_id+'" data-edge_to=""';
      newtrHTML += '<tr id="tr-'+stage_id+'"><td>1</td>';
      newtrHTML += '<td><input class="form-control input-sm stage_label" id="stage_label_'+stage_id+'" type="text" value="'+newstate+'" '+data_options+'></td>';
      newtrHTML += '<td>';
      /*newtrHTML += '<i class="fa fa-plus Addtransition" '+data_options+'></i> &nbsp; &nbsp;';*/
      newtrHTML += '<i class="fa fa-trash deleteState" '+data_options+'></i></td></tr>'; 
      $('#stages_table').append(newtrHTML);  

                /*nodes.add({
                    id: nodecount,
                    dbid:0,
                    label: newstate,
                    shape: 'box',
                    color:stage_color
                });*/
                return json = {}
            }
            catch (err) {
                alert(err);
            }
    
   }; 

  var DrawTabale = function() 
   {
   // nodeset = [];
   $("#stages_table > tbody").empty();
    var response = nodes.get();
    var trHTML = '';
    $.each(response, function(i, item) {
    var stage_id = item.id; 
    var data_options ='data-stage="'+stage_id+'" data-edge="" data-edge_label="Next" data-edge_from="'+stage_id+'" data-edge_to=""';
      trHTML += '<tr id="tr-'+stage_id+'"><td>' + item.id + '</td>';
      trHTML += '<td><input class="form-control input-sm stage_label" id="stage_label_'+stage_id+'" type="text" value="' + item.label + '" '+data_options+'></td>';
      trHTML += '<td>';

      /*trHTML += '<i class="fa fa-plus Addtransition" '+data_options+'></i> &nbsp; &nbsp;';*/
      trHTML += '<i class="fa fa-trash deleteState" '+data_options+'></i> </td></tr>';
       nodelabels[item.id] = item.label;
       });
    //console.log(trHTML);
    if(trHTML == '')
    {
      /*addNewState('Stage 1');
      addNewState('Stage 2');*/
      network.fit();
    }
    else
    {
      $('#stages_table').append(trHTML);
    }

      
     };

    
     
     

    var DrawEdges = function() 
   {
   // nodeset = [];
   $("#edges_table > tbody").empty();
    var response = edges.get();
    var trHTML = '';
    
    $.each(response, function(i, item) {
   // console.log(item);
    var from_node = nodes.get(item.from); 
    var to_node = nodes.get(item.to);
    if(from_node && to_node)
    {
      var data_options ='data-stage="'+item.from+'" data-edge="'+item.id+'" data-edge_label="'+item.label+'" data-edge_from="'+item.from+'" data-edge_to="'+item.to+'"'; 
      trHTML += '<tr id="tr-'+item.id+'"><td>' + item.id + '</td>';
      trHTML += '<td>'+item.label+'</td>';
      trHTML += '<td>'+from_node.label+'</td>';
      trHTML += '<td>'+to_node.label+'</td>';
      trHTML += '<td><i class="fa fa-edit Addtransition" '+data_options+'></i> &nbsp;&nbsp;<i class="fa fa-trash deleteEdge" '+data_options+'></i> </td></tr>';
    }
       });
    
      $('#edges_table').append(trHTML);
    

      
     };

    
     
     


     var loadWorkflow = function(wf) 
   {
      var loadformurl = "@php echo URL('load_Workflow_json'); @endphp";
      //console.log("loadformurl"+loadformurl);
      $.getJSON(loadformurl+'?workflow_id=' + wf, function(data) {
        nodecount = data.nodecount;
        edgecount = data.edgecount;
        var workflow_name =data.workflow_name;
        stage_color =data.workflow_color;
        var nodeset = data.wf_states;
        var edgeset = data.wf_transitions;
        $("#workflow_name").val(workflow_name); 
        $("#workflow_color").val(stage_color);
        /*var nodes = new vis.DataSet(nodeset);stage_color
        var edges = new vis.DataSet(edgeset);
        var container = document.getElementById('mynetwork');
        var data = { nodes: nodes,edges: edges};
  
        var options = {
                        height: '600px',
                        width: '100%'
        }
        var network = new vis.Network(container, data, options);network.fit();*/

        //nodes.add(nodeset);
        //edges.add(edgeset);

        DrawTabale();
        DrawEdges();
      });
   };
   //loadWorkflow(workflow_id);

     $(document).on("click",".addNewState",function(e) {

       addNewState('Stage');
     });

     

     $(document).on("click",".deleteEdge",function(e) {
       var edge_id =$(this).attr('data-edge'); 
        try {
              //  console.log(edge_id);
                edges.remove({id: edge_id});
                DrawEdges();
            }
            catch (err) {
                alert(err);
            }
            
     });

  var deleteState = function(node_id) 
   {
      
      try {
                nodes.remove({id: node_id});
                $("#tr-"+node_id).remove();
            }
            catch (err) {
                alert(err);
            }
    
   }; 

   $(document).on("click",".Addtransition",function(e) {
//console.log(nodes.get());
   // console.log(edges.get());
    var satge_id =$(this).attr('data-stage');
    var edge_id =$(this).attr('data-edge'); 
    var edge_from =$(this).attr('data-edge_from');
    var edge_to =$(this).attr('data-edge_to');
    var transition_name = $(this).attr('data-edge_label');
$('#activity_modal').modal({
                     show: 'show',
                     backdrop: false
               });
    var stage_option = '<option value="">Select</option>';
    var response = nodes.get();
    $.each(response, function(i, item) {
      stage_option += '<option value="' + item.id + '">' + item.label + '</option>';
      
       });
      $('.stages_option').html(stage_option);
      $('#transition_name').val(transition_name);
      $('#from_satge').val(edge_from);
      $('#to_satge').val(edge_to);
      $('#edge_id').val(edge_id);
      $('#add_transition').parsley().reset();
      load_transition_add();
     });
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

   $(document).on("click",".save_workflow",function(e) {
   e.preventDefault();
   
   if($("#closed_workflow_name").parsley().validate())
   {
    console.log("save");
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
        /*var data = JSON.stringify([{_token:CSRF_TOKEN},{"name":"formID","value":formID},{"name":"formFields","value":fields}]);*/
        var data = {_token: CSRF_TOKEN,"workflow_id":workflow_id,"workflow_name":$('#workflow_name').val(),"workflow_color":$('#workflow_color').val(),"task_flow":$('#workflow_flow_type').val(),"workflow_object_type":$('#workflow_object_type').val(),"form_id":$('#form_id').val(),"document_id":$('#document_id').val(),"workflow_stages":nodes.get(),"workflow_edges":edges.get(),"deadline":$('#deadline').val(),"deadline_value":$('#deadline_value').val(),"deadline_type":$('#deadline_type').val(),"departments":departments,"department_users":department_users,
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
              //  console.log(msg);
                $('.alert_space').html(msg.message);
                 
                

                if(msg.status == 1)
                {
                       workflow_id = msg.workflow_id;
                 $('#workflow_id').val(workflow_id);
                  console.log("workflow_id"+workflow_id);
                       console.log("status"+msg.status);
                        $('#closed_workflow_name').parsley().reset();
                       /* if(return_form == 'save_close')
                        {
                          window.location.href = return_form_url;      
                        }*/

                }
                
                $("html, body").animate({ scrollTop: 0 }, "fast");

                //Demo only
                //$('.alert textarea').val(JSON.stringify(fields));
            }
        });
    
   }
   else
   {
     console.log("Validation failed");
   }

     });
   
   
    $(document).on("click",".save_transition",function(e) {
   e.preventDefault();
   
   if($("#add_transition").parsley().validate())
   {
    //console.log("save");
    try {
                var edge_id = $('#edge_id').val();
                if(edge_id)
                {
                  edges.update({
                      id: edge_id,
                      dbid:0,
                      label:document.getElementById('transition_name').value,
                      from: document.getElementById('from_satge').value,
                      to: document.getElementById('to_satge').value,
                      arrows:'to'
                  });
                }
                else
                {
                  edgecount++;
                  edges.add({
                      id: edgecount,
                      dbid:0,
                      label:document.getElementById('transition_name').value,
                      from: document.getElementById('from_satge').value,
                      to: document.getElementById('to_satge').value,
                      arrows:'to'
                  });
                  $('#edge_id').val(edgecount);
                }
                
                DrawEdges();
                load_transition_add();
            }
            catch (err) {
                alert(err);
            }
   }

     });


    var load_transition_add = function() 
   {
    var edge_id = $('#edge_id').val();
    //console.log("edge_id"+edge_id); 
    if(edge_id)
    {
       $(".add_new_transition").show();

    }
    else
    {
       $(".add_new_transition").hide();

    }  
      
    
   }; 

   load_transition_add();

   $(document).on("click",".add_new_transition",function(e) { 


      $('#transition_name').val('Next');
      $('#from_satge').val('');
      $('#to_satge').val('');
      $('#edge_id').val('');
      $('#add_transition').parsley().reset();
      load_transition_add();

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
      console.log("wf_object_type_id"+wf_object_type_id);
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

                }
              
            }
        });
    
   }; 

   
   

  var wf_manager_users = function(task) 
   {
   
    department_users = task.department_users;
    selectedusers = task.sel_department_users;
    sel_count = task.sel_count;
    var users_option = '<select name="wf_manager_users'+sel_count+'" id="wf_manager_users'+sel_count+'" class="form-control wf_manager_users" multiple="multiple" data-count="'+sel_count+'">';
    console.log(department_users);
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
    
        console.log("div"+$(this).attr("data-count"));
        var sel_count = $(this).attr("data-count");
        var departments = $(this).val();
        console.log(departments);
        var sel_department_users = $('#wf_manager_users'+sel_count).val();
        console.log(department_users);
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
    
        console.log("div"+$(this).attr("data-count"));
        var sel_count = $(this).attr("data-count");
        var departments_users = $(this).val();
        if(sel_count != 0)
                {
                  console.log("update edge");
                  nodes.update({
                    id: sel_count,
                    sel_department_users: departments_users
                });
                }
     });

    $(document).on("change",".rule_components",function(e) {
    
        console.log("div"+$(this).attr("data-object_type"));
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
              rule_options='<option value="-1">------</option>';  
              $.each(rule_components, function(i, item)
              {
                /*console.log(item);*/
                 rule_options +='<option value="'+item.id+'" data-column_type="'+item.column_type+'" data-key="'+i+'">'+item.column_name+'</option>';
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