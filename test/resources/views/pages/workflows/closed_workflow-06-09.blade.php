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
        z-index:99999;
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
                                <button type="button" class="btn btn-primary btn-sm add_task" data-wf_type="node"><i class="fa fa-plus"></i> Add Stage</button>
                                <button type="button" class="btn btn-warning btn-sm preview"><i class="fa fa-eye"></i> Preview</button>
                                <button type="button" class="btn btn-primary btn-sm  save_workflow">Save Template</button>
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

                                    <!--- TABS START -->
                                    <div class="form-group">
                                        <h4 class="box-title">{{$language['wf_permissions']}}</h4>
                                        <p style="font-size:12px; color:#999;">{{$language['wf_permsn_info']}}</p>
                                        <div class="nav-tabs-custom">
                                            <ul class="nav nav-tabs pull-left ui-sortable-handle">
                                                <li class="active"><a href="#add-tab" id="addtab" data-toggle="tab" class="smtp-btn" aria-expanded="true">Add</a></li>
                                                <li class=""><a href="#edit-tab" id="edittab" data-toggle="tab" class="smtp-btn" aria-expanded="false">Edit</a></li>
                                                <li class=""><a href="#delete-tab" id="deletetab" data-toggle="tab" class="smtp-btn" aria-expanded="false">Delete</a></li>
                                                <li class=""><a href="#view-tab" id="viewtab" data-toggle="tab" class="smtp-btn" aria-expanded="false">View</a></li>
                                            </ul>
                                            <div class="tab-content no-padding"> 
                                                <!-- view -->
                                                <div class="chart tab-pane" id="view-tab">
                                                    <div class="row form_row">
                                                        <div class="form-group">
                                                            <label for="" class="col-sm-2 control-label">Departments: </label>
                                                            <div class="col-sm-9">
                                                                <select name="view_departmentid[]" id="view_departmentid" class="multipleSelect form-control" multiple required="" data-parsley-required-message="Please select users" data-parsley-trigger="change focusout" data-stageid="0" data-wf-area="permission" data-wf-operation="view">
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
                                                                <select class="form-control multipleSelect" id="view_userid"   name="view_userid[]" multiple required="" data-parsley-required-message="Please select users" data-parsley-trigger="change focusout" data-stageid="0" data-wf-area="permission" data-wf-operation="view">
                                                                <?php
                                                                  foreach ($users as $key => $user) {
                                                                      ?>
                                                                      <option value="<?php echo $user->id; ?>"><?php echo ucfirst(@$user->user_full_name);?> -{{@$user->user_role}}@if(@$user->departments[0]->department_name != "")- {{ ucfirst(@$user->departments[0]->department_name) }} @endif</option>
                                                                      <?php
                                                                  }
                                                                  ?>  
                                                                </select> 
                                                                <p class="help">Choose user(s) who can start a Workflow process</p>
                                                                <p class="help">[SA] - Super Admin</p>
                                                                <p class="help">[DA] - Department Admin</p>
                                                                <p class="help">[RU] - Regular User</p>
                                                                <p class="help">[PU] - Private User</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Delete -->
                                                <div class="chart tab-pane" id="delete-tab">
                                                    <div class="row form_row">
                                                        <div class="form-group">
                                                            <label for="" class="col-sm-2 control-label">Departments: </label>
                                                            <div class="col-sm-9">
                                                                <select name="delete_departmentid[]" id="delete_departmentid" class="multipleSelect form-control" multiple required="" data-parsley-required-message="Please select users" data-parsley-trigger="change focusout" data-stageid="0" data-wf-area="permission" data-wf-operation="delete">
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
                                                                <select class="form-control multipleSelect" id="delete_userid"   name="delete_userid[]" multiple required="" data-parsley-required-message="Please select users" data-parsley-trigger="change focusout" data-stageid="0" data-wf-area="permission" data-wf-operation="delete">
                                                                <?php
                                                                      foreach ($users as $key => $user) {
                                                                          ?>
                                                                          <option value="<?php echo $user->id; ?>"><?php echo ucfirst(@$user->user_full_name);?> -{{@$user->user_role}}@if(@$user->departments[0]->department_name != "")- {{ ucfirst(@$user->departments[0]->department_name) }} @endif</option>
                                                                          <?php
                                                                      }
                                                                      ?>  
                                                                    </select> 
                                                                    <p class="help">Choose user(s) who can delete a Workflow process</p>
                                                                    <p class="help">[SA] - Super Admin</p>
                                                                    <p class="help">[DA] - Department Admin</p>
                                                                    <p class="help">[RU] - Regular User</p>
                                                                    <p class="help">[PU] - Private User</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Edit -->
                                                <div class="chart tab-pane" id="edit-tab">
                                                    <div class="row form_row">
                                                        <div class="form-group">
                                                            <label for="" class="col-sm-2 control-label">Departments: </label>
                                                            <div class="col-sm-9">
                                                                <select name="edit_departmentid[]" id="edit_departmentid" class="multipleSelect form-control" multiple required="" data-parsley-required-message="Please select users" data-parsley-trigger="change focusout" data-stageid="0" data-wf-area="permission" data-wf-operation="edit">
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
                                                                <select class="form-control multipleSelect" id="edit_userid"   name="edit_userid[]" multiple required="" data-parsley-required-message="Please select users" data-parsley-trigger="change focusout" data-stageid="0" data-wf-area="permission" data-wf-operation="edit">
                                                                <?php
                                                                      foreach ($users as $key => $user) {
                                                                          ?>
                                                                          <option value="<?php echo $user->id; ?>"><?php echo ucfirst(@$user->user_full_name);?> -{{@$user->user_role}}@if(@$user->departments[0]->department_name != "")- {{ ucfirst(@$user->departments[0]->department_name) }} @endif</option>
                                                                          <?php
                                                                      }
                                                                      ?>  
                                                                    </select> 
                                                                    <p class="help">Choose user(s) who can edit a Workflow process</p>
                                                                    <p class="help">[SA] - Super Admin</p>
                                                                    <p class="help">[DA] - Department Admin</p>
                                                                    <p class="help">[RU] - Regular User</p>
                                                                    <p class="help">[PU] - Private User</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!--Add-->
                                                <div class="chart tab-pane  active" id="add-tab">
                                                    <div class="row form_row">
                                                        <div class="form-group">
                                                            <label for="" class="col-sm-2 control-label">Departments: </label>
                                                            <div class="col-sm-9">
                                                                <select name="add_departmentid[]" id="add_departmentid" class="multipleSelect form-control" multiple required="" data-parsley-required-message="Please select departments" data-parsley-trigger="change focusout" data-stageid="0" data-wf-area="permission" data-wf-operation="add">
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
                                                                <select class="form-control multipleSelect" id="add_userid"   name="add_userid[]" multiple required="" data-parsley-required-message="Please select users" data-parsley-trigger="change focusout" data-stageid="0" data-wf-area="permission" data-wf-operation="add">
                                                                <?php
                                                                      foreach ($users as $key => $user) {
                                                                          ?>
                                                                          <option value="<?php echo $user->id; ?>"><?php echo ucfirst(@$user->user_full_name);?> -{{@$user->user_role}}@if(@$user->departments[0]->department_name != "")- {{ ucfirst(@$user->departments[0]->department_name) }} @endif</option>
                                                                          <?php
                                                                      }
                                                                      ?>  
                                                                    </select> 
                                                                    <p class="help">Choose user(s) who can add a Workflow process</p>
                                                                    <p class="help">[SA] - Super Admin</p>
                                                                    <p class="help">[DA] - Department Admin</p>
                                                                    <p class="help">[RU] - Regular User</p>
                                                                    <p class="help">[PU] - Private User</p>
                                                            </div>
                                                        </div>
                                                    </div>
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
                                            <button type="button" class="btn btn-primary btn-sm  save_workflow">Save Template</button>
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

  $('.multipleSelect').fastselect();
  /*$("#wf_title_main").click(function() {
    $(".wf_permssn_forms").show();
  });
  $(".add_task, .preview, .product-info").click(function() {
    $(".wf_permssn_forms").hide();
  }); */
  var stage_users_options='';
  var stage_dept_options = '';
   @php foreach ($users as $key => $user){ @endphp
	stage_users_options += "<option value='{{@$user->id}}'>{{ucfirst(@$user->user_full_name)}} -{{@$user->user_role}}@if(@$user->departments[0]->department_name != "")- {{ucfirst(@$user->departments[0]->department_name)}}@endif</option>";
  @php } @endphp  

   @php foreach ($departments as $key => $departments){ @endphp
	stage_dept_options += @php echo "'<option value=\"$departments->department_id\">$departments->department_name</option>'"; @endphp;
  @php } @endphp 

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
  var rules_basic_base = {condition: 'AND',rules:[],stage_case:'',if_stage:0,else_stage:0};
 var operator_options ='';
 var rule_components=[];
 var myrcArray = {}
 var db_stage_option='';
 var validation_check=0;
 var auto_id="@php echo $auto_id; @endphp"
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
  var setTOJson = function(rule_data) 
   {
      var result = [];

    $(rule_data).each(function (i, element) 
    {
        //rule_data.push(element.attr.id);

        if (element.children && element.children.length > 0) 
        {
            var ids = get_all_ids(element.children);

            result = result.concat(ids); // or $.merge(result, ids);
        }
    });

    return result;
   }

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
      stage_options += '<option value="' + item.id + '">' + item.label + '</option>';
      
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
    html+='<div class="btn-group pull-right group-actions">';
    html+='<button type="button" style="margin-right:5px;" class="btn btn-xs btn-success add_rule_template" id="add_rule_template_'+rule_count+'" data-add="rule" '+data_attr1+' '+data_attr2+'><i class="fa fa-plus"></i> Add Rule </button>';
    if(first != 0)
    {
    html+='<button type="button" class="btn btn-xs btn-success add_rule_template" id="add_group_template_'+rule_count+'" data-add="group" '+data_attr1+' '+data_attr2+'><i class="fa fa-plus"></i> Add Group</button>';
    }
    if(first == 0)
    {
      html+='<button type="button" class="btn btn-xs btn-danger delete_rule_template" data-delete="group" '+data_attr1+' '+data_attr2+'><i class="fa fa-trash"></i></button>';
    }
    

    html+='</div>';
    
    html+='<div class="rule-filter-container">';
        html+='<select class="form-control group_filter_update" name="builder_group_'+rule_count+'_cond" id="builder_group_'+rule_count+'_cond" '+data_attr1+' '+data_attr2+'><option value="AND">AND</option><option value="OR">OR</option></select>';
   selected = '';
    if(stage_case == '' || stage_case == 0)
    {
      selected='selected="selected"';
    }    
    html+='<select class="form-control group_stage_case_update" name="builder_group_stage'+rule_count+'_cond" id="builder_group_stage'+rule_count+'_cond" '+data_attr1+' '+data_attr2+' style="margin-left:5px;"><option value="" '+selected+' disabled="disabled">------</option>';
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
        
    html+='</div>';
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

    html+='<div class="col-xs-5">';
    html+='<div class="form-group rule_group_stage_'+rule_count+'" id="rule_group_if_stage_'+rule_count+'" style="'+if_style+'">';
    html+='<label for="exampleInputEmail1">Go to stage </label>';
    html+='<br>';
    html+='<select class="form-control rule_stage_case c_rule_stages_option1_'+stageid+'" id="rule_stages_option1_'+rule_count+'"  name="" '+data_attr1+' '+data_attr2+'>'+temp_stages_option+'</select>';
    html+='</div>';
    html+='</div>';

    html+='<div class="col-xs-5">';
    html+='<div class="form-group rule_group_stage_'+rule_count+'" id="rule_group_if_else_stage_'+rule_count+'" style="'+else_style+'">';
    html+='<label for="exampleInputEmail1">Else go to the stage </label>';
    html+='<br>';
    html+='<select class="form-control rule_stage_case c_rule_stages_option2_'+stageid+'" id="rule_stages_option2_'+rule_count+'" name="" '+data_attr1+' '+data_attr2+'>'+temp_stages_option+'</select>';
    html+='</div>';
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
        wfTRHTML +='<i class="fa fa-list fa-lg"></i>';
        wfTRHTML +='</div>';

        wfTRHTML +='<div class="product-info">';
        wfTRHTML +='<a href="javascript:void(0)" class="product-title" id="label'+id+'">'+task.label+'</a><span class="pull-right action_span"><i class="fa fa-trash-o fa-lg deleteState" data-liid="'+id+'"></i></span>';         
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
		wfTLHTML +='<div class="form-group">';
		wfTLHTML +='<label>Stage Action</label><br/>';
		wfTLHTML +='<input type="radio" class="wf_actions" id="wf_actions2_'+id+'" name="wf_actions'+id+'" value="2" data-stageid="'+id+'"> By Hierarchy &nbsp;';
		wfTLHTML +='<input type="radio" class="wf_actions" id="wf_actions3_'+id+'" name="wf_actions'+id+'" value="3" data-stageid="'+id+'"> By Group &nbsp;';
		wfTLHTML +='<input type="radio" checked="checked" class="wf_actions" id="wf_actions1_'+id+'" name="wf_actions'+id+'" value="1" data-stageid="'+id+'" > By User &nbsp;';
        wfTLHTML +='<input type="radio" class="wf_actions" id="wf_actions4_'+id+'" name="wf_actions'+id+'" value="4" data-stageid="'+id+'"> Auto';
		wfTLHTML +='</div>';

		/////////////// BY USER HELP TEXT ////////////////
		wfTLHTML +='<p class="help_text_user_'+id+'" id="help1_'+id+'" style="font-size:12px; color:#999; display:none;">Select the users who can take actions at this stage </p>';
		wfTLHTML +='<p class="help_text_user_'+id+'" id="help2_'+id+'" style="font-size:12px; color:#999; display:none;">When this is selected, the manager of the user who submitted a form will get a notification automatically.</p>';
		wfTLHTML +='<p class="help_text_user_'+id+'" id="help3_'+id+'" style="font-size:12px; color:#999; display:none;">Select the group who can take actions at this stage </p>';
		wfTLHTML +='<p class="help_text_user_'+id+'" id="help4_'+id+'" style="font-size:12px; color:#999; display:none;">When this is selected, WF will move to the next stage depending on the user name or department, or one or more of the form field values using "and" and "or" operator (e.g. when a CEO submits a leave application or a purchase request, it is auto approved.</p>';
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

        wfTLHTML +='<div class="form-group" >';
        wfTLHTML +='<div class="box box-solid">';
        wfTLHTML +='<div class="box-header with-border"><label>Stage Actions</label></div>';
        wfTLHTML +='<div class="box-group" id="stage_action_template'+id+'">';
        wfTLHTML +='</div>';
        wfTLHTML +='<div class="box-footer">';
        wfTLHTML +='<button type="button" class="btn btn-primary btn-sm add_action" data-wf_type="edge" data-stage="'+id+'" id="add_action'+id+'"><i class="fa fa-plus"></i> Add Action</button>';
        wfTLHTML +='</div>'; 
        wfTLHTML +='</div>';
        wfTLHTML +='</div>';
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
            /*console.log(item);*/
            if(item !='')
            {
            $('#stage_user'+id).data('fastselect').setSelectedOption($('#stage_user'+id+' option[value='+item+']').get(0));
            $("#stage_user"+id).val(item);  
            }
        });

        $.each(sel_departments, function(i, item)
        {
            /*console.log(item);*/
             if(item !='')
            {
            $('#stage_dept'+id).data('fastselect').setSelectedOption($('#stage_dept'+id+' option[value='+item+']').get(0));
            $("#stage_dept"+id).val(item);  
          }
        });

        nodes.add({
            id: id,
            dbid:dbid,
            label: task.label,
            shape: 'box',
            color:task.color,
            stage_action:stage_action,
            stage_group:stage_group,
            stage_percentage:task.stage_percentage,
            sel_departments:sel_departments,
            sel_department_users:sel_department_users
        });

        $("#wf_actions"+stage_action+"_"+id).prop("checked", true);
        $("#stage_group"+stage_group+"_"+id).prop("checked", true);
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

      $('.collapse'+from_id).collapse('hide');
      var selectVal = parseInt($("input[name='wf_actions"+from_id+"']:checked").val());
      console.log("selectVal="+selectVal);
      var common_data='data-stageid="'+from_id+'" data-edgeid="'+id+'"';
      var stage_option = '<option value="0">Select Stage</option>';
      
      var response = nodes.get();
      $.each(response, function(i, item) {
      stage_option += '<option value="' + item.id + '">' + item.label + '</option>';
      
       });

      var action_html ='<div class="panel box box box-primary">';
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
          /*action_html +='<input type="text" class="form-control task_label_input" id="task_label'+id+'" name="task_label[]" value="'+task.label+'" '+common_data+' data-wf_type="'+wf_type+'">';*/
          action_html +='<select name="activity_id'+id+'" id="activity_id'+id+'" class="form-control task_label_input task_label_input_'+from_id+'" '+common_data+' data-wf_type="'+wf_type+'">'+wfactivities_options+'</select>';
          action_html +='</div>';

          action_html +='<div class="col-md-6" id="">';           
          action_html +='<label>Rule</label>';
          action_html +='<select name="rule_'+id+'" id="rule_'+id+'" class="form-control rule_change rule_change_'+from_id+'" '+common_data+'>';

          var selected='';
          var rule_style='display:none';
          if(with_rule == 0 || selectVal == 2 || selectVal == 3)
          {
          selected='selected="selected"';
          rule_style='display:block';
          }
          action_html +='<option value="0" '+selected+'>Without Rule</option>';

          var selected='';

          if(with_rule == 1 && (selectVal == 1 || selectVal == 4))
          {
          selected='selected="selected"';
          rule_style='display:block';
          }

          if(selectVal == 2 || selectVal == 3)
          {
          selected='disabled="disabled"';
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
         
         var rule_arry = {"stageid":from_id,"edgeid":id,"first":1,"parentid":0,"level":0,stage_case:'if_else',stage_dbid:dbid};
         var temp = AddGroupTemplate(rule_arry);
         action_html +='<div id="builder'+id+'" class="query-builder form-inline">'+temp+'</div>';
        /* Tree View END*/
          
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

        action_html +='</div>';
        action_html +='</div>';
        action_html +='</div>';

      $('#stage_action_template'+from_id).append(action_html);

      $('#to_satge'+id).val(to_id);

      if(activity_id>0)
      {
        console.log("activity_id"+activity_id);
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
          $("#builder_rule_value_"+rule_count).val(item.value).trigger('change');
          }
          
       }

        if(i !=0)
        {
          myrcArray[item.rc]=rule_count;
        }

      });  
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
        $.each(nodeset, function(i, item)
        {
          db_stage_option += '<option value="' + item.id + '">' + item.label + '</option>'; 
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
     $.each(edgeset, function(i, item)
        {
          var taskhtml = addAction(item);
        });
        load_workflow_objects();
     
      $('#wf_managers').fastselect();

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
       $(".taskdiv").hide(); /* Hide All Other Right DIV */
       $("#rwf_div"+liid).show(); /* Show Only Current Right DIV */
        $(".wf_li").removeClass("li_active");
        $(this).addClass("li_active");
        var from_satge = liid; 
        var stage_option = '<option value="0">Select Stage</option>';
        var response = nodes.get();
        $.each(response, function(i, item) {
        stage_option += '<option value="' + item.id + '">' + item.label + '</option>';
       });
        $( ".stages_option"+liid).each(function( index )
        {
            
            var edgeid =$(this).attr('data-edgeid');
            var to_satge = $(this).val(); 
            var rule = $("#rule_"+edgeid).val(); 
            $('#to_satge'+edgeid).html(stage_option);
            $("#to_satge"+edgeid).val(to_satge); 

        });

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
        $('#stage_user'+stageid).attr('data-parsley-required', 'false');
        $('#stage_dept'+stageid).attr('data-parsley-required', 'false');
        $('#stage_percentage'+stageid).attr('data-parsley-required', 'false');
        if(selectVal==1){
			$("#byuser"+stageid).show();
			$("#bygroup"+stageid).hide();
			$("#bypercentage"+stageid).hide();		
            //set required attribute on input to true
            $('#stage_user'+stageid).attr('data-parsley-required', 'true');
            $('#stage_user'+stageid).attr('data-parsley-required-message','Please select users');
        }else if(selectVal==2){
			$("#bygroup"+stageid).hide();
			$("#byuser"+stageid).hide();
			$("#bypercentage"+stageid).hide();
		}else if(selectVal==3){
			$("#bygroup"+stageid).show();
			$("#byuser"+stageid).hide();
			$("#bypercentage"+stageid).hide();
			$("#grouphelp"+selectVal+"_"+stageid).show();	
			change_stage_group(stageid);
            $('#stage_dept'+stageid).attr('data-parsley-required', 'true');
            $('#stage_dept'+stageid).attr('data-parsley-required-message','Please select departments');
            $('#stage_percentage'+stageid).attr('data-parsley-required', 'true');
            $('#stage_percentage'+stageid).attr('data-parsley-required-message','Please enter percentage value');
		}else{
			$("#bygroup"+stageid).hide();
			$("#byuser"+stageid).hide();
			$("#bypercentage"+stageid).hide();
		}



		$("#help"+selectVal+"_"+stageid).show();
		nodes.update({
			id: stageid,
			stage_action: selectVal
		});
        

        $( ".task_label_input_"+stageid).each(function( index )
        {
            
            console.log("CselectVal"+selectVal);
            if(selectVal == 4)
            {
                console.log("auto_id"+auto_id);
                if(auto_id != 0)
                {
                    $(this).val(auto_id).trigger( "change" );
                }
            }
            
            

        });

        $( ".rule_change_"+stageid).each(function( index )
        {
            
            /*var edgeid =$(this).attr('data-edgeid');
            var to_satge = $(this).val(); 
            var rule = $("#rule_"+edgeid).val(); 
            $('#to_satge'+edgeid).html(stage_option);
            $("#to_satge"+edgeid).val(to_satge); */
            if(selectVal == 1 || selectVal == 4)
            {
                $(this).find("option[value='1']").attr('disabled', false);
                if(selectVal == 4)
                {
                    $(this).val(1).trigger( "change" );
                
                }
            }
            else
            {
                $(this).val(0).trigger( "change" );
                $(this).find("option[value='1']").attr('disabled', true);;
            }
            

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
          var task= {id: taskcount, dbid: 0, label: label, shape: "box", color: "#c0c0c0", type: wf_type, description:'',department_users:empty,sel_departments:empty,sel_department_users:empty,stage_action:1,stage_group:1,stage_percentage:0}
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

     var updateRule = function(rule_arry) 
   {
    var stageid = rule_arry.stageid;
    var edgeid = rule_arry.edgeid;
    var rc = rule_arry.rc;
    /*edge_data = edges.get(edgeid);
     $.each(edge_data.rules_basic.rules, function(i, item)
              {
                
                if(item.rc == rc)
                {
                  var filter_id = $("#builder_rule_"+rc+"_filter").val();
                  var object_type = $("#builder_rule_"+rc+"_filter").find(':selected').attr('data-object_type'); 
                  var column_type = $("#builder_rule_"+rc+"_filter").find(':selected').attr('data-column_type'); 
                  var operator = $("#builder_rule_"+rc+"_operator").val(); 
                  var value = $("#builder_rule_value_"+rc).val();
                  item.id = filter_id;
                  item.operator = operator;
                  item.value = value;
                  item.object_type=object_type;
                }
                 
              });*/
  };



     var updateRuleCond = function(rule_arry) 
   {
    var stageid = rule_arry.stageid;
    var edgeid = rule_arry.edgeid;
    var rc = rule_arry.rc;
    /*edge_data = edges.get(edgeid);
     var filter_conditin = $("#builder_group_"+rc+"_cond").val();
     var stage_case = $("#builder_group_stage"+rc+"_cond").val();
     var if_stage = $("#rule_stages_option1_"+rc).val();
     var else_stage = $("#rule_stages_option2_"+rc).val();
    edge_data.rules_basic.condition = filter_conditin;
    edge_data.rules_basic.stage_case = stage_case;
    edge_data.rules_basic.if_stage = if_stage;
    edge_data.rules_basic.else_stage = else_stage;*/ 
  };




  

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

          value_html +='<input class="form-control rule_filter_update" type="text" name="builder_rule_value_'+rc+'" id="builder_rule_value_'+rc+'" '+data_attr1+'" '+data_attr2+'>'; 
    
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
         var rule_arry = {"stageid":stageid,"edgeid":edgeid,"rc":rc}
         updateRuleCond(rule_arry); 

      
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

       var rule_arry = {"stageid":stageid,"edgeid":edgeid,"rc":rc}
      updateRuleCond(rule_arry); 
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
        edge_data = edges.get(edgeid);
       var nextlevel = parseInt(level)+1;
       var rule_arry = {"stageid":stageid,"edgeid":edgeid,"parentid":rc,"first":0,"level":nextlevel,stage_dbid:0}
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
                        var value = $("#builder_rule_value_"+rc).val();
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
                    //  console.log(msg);
                    $('.alert_space').html(msg.message);
                    if(msg.status == 1)
                    {
                        workflow_id = msg.workflow_id;
                        $('#workflow_id').val(workflow_id);
                        $('#closed_workflow_name').parsley().reset();
                    }
                    $("html, body").animate({ scrollTop: 0 }, "fast");
                }
            });
        }else{
            console.log("Validation failed");
        }
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
    
        console.log("div"+$(this).attr("data-count"));
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