<?php
  include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')
@section('main_content')
   
{!! Html::script('js/jquery.form.js') !!}  
{!! Html::style('css/font-awesome.min.css') !!} 
{!! Html::style('css/ionicons.min.css') !!} 
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.1/css/bootstrap-colorpicker.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.1/js/bootstrap-colorpicker.min.js"></script>  
<section class="content-header">
  <div class="col-xs-8">
    <span style="float:left;">
      <strong>{{$language['workflows']}}</strong> &nbsp;
    </span>
     <!-- super admin can only add wfs -->
    @if(Auth::user()->user_role == 1)
    <span style="float:left;">
      <a href="" data-toggle="modal" data-target="#dTAddModal">
          <button class="btn btn-block btn-info btn-flat newbtn">{{$language['add new']}}  <i class="fa fa-plus"></i></button>
      </a>
    </span>
    @endif
  </div>
  <div class="col-xs-4">
    <!-- <ol class="breadcrumb">
        <li><a href="{{url('/home')}}"><i class="fa fa-dashboard"></i> {{$language['home']}}</a></li>
        <li class="active">{{$language['work_flow']}}</li>
    </ol> -->
  </div>
</section>
<style type="text/css">
.cont{
    width:685px;
    overflow-x:hidden;
}
.active_row {
    color:red;
}
#panLeft {
    margin-top: 4px;
    float: left;
    text-align: center;
    display: inline;
    cursor: pointer;
}
#panRight {
    float: right;
    margin-top: 4px;
    cursor: pointer;
    text-align: center;
    display: inline;
}
.arrow {
    border-style: dashed;
    border-color: transparent;
    border-width: 14px;
    display: -moz-inline-box;
    display: inline-block;
    /* Use font-size to control the size of the arrow. */
    font-size: 100px;
    height: 0;
    line-height: 0;
    position: relative;
    vertical-align: middle;
    width: 0;
    background-color:#fff; /* change background color acc to bg color */ 
    border-left-width: 0.2em;
    border-left-style: solid;
    /*border-left-color: #ff0000;*/
    left:0.25em;
}
[draggable] {
      -khtml-user-drag: element;
      -webkit-user-drag: element;
    }
div.grippy {
      content: '....';
      width: 10px;
      
      display: inline-block;
      line-height: 5px;
      padding: 3px 4px;
      cursor: move;
      vertical-align: middle;
      margin-top: -.7em;
      margin-right: .3em;
      font-size: 14px;
      font-family: sans-serif;
      letter-spacing: 2px;
      color: black;
      text-shadow: 1px 0 1px black;
    }
    div.grippy::after {
      content: '.. .. .. ..';
    }
    .stagebuttons {
   display: inline-block;
   max-width: 100%;
   margin: 0 0 1em;
   white-space: nowrap;
}

.stagebuttons li {
   display: inline-block;
   vertical-align: top;
   position: relative;
}
@media(max-width:400px){
  div.grippy{
    margin-top: -0.7em !important;
    margin-right: -2.7em !important;
  }
}
@media(max-width:770px){
  .cont{
    overflow-x: hidden;
    width: 200px;
  }
  .box{
  overflow: scroll;
}
}
@media (max-width: 900px) and (min-width: 771px){
  .cont{
    overflow-x: hidden;
    width: 350px;
  }
  .box{
  overflow: scroll;
}
}
@media (max-width: 1500px) and (min-width: 901px){
  .cont{
    overflow-x: hidden;
    width: 685px;
  }
  .box{
  overflow: scroll;
}
}
@media (max-width: 1900px) and (min-width: 1501px){
  .cont{
    overflow-x: hidden;
    width: 990px;
  }
  .box{
  overflow: scroll;
}
}
@media (max-width: 2500px) and (min-width: 1901px){
  .cont{
    overflow-x: hidden;
    width: 1300px;
  }
  .box{
  overflow: scroll;
}
}
@media(max-width: 770px){
  #stage_label{
    margin-left: 20px;
  }
}
</style>
<section class="content" id="shw">
<div class="row">
<div class="col-xs-12">
<div class="box" style="overflow: auto;">
<div class="box-body">
@if(count($workflows) == 0)
  <div class="col-md-12">
          <div class="box">
              <div class="callout callout-warning">
                <h4>{{$language['no workflow']}}</h4>
              </div>
          </div>
          <!-- /.box -->
        </div>
@else
  <input type="hidden" name="hidd_count_stages" id="hidd_count_stages">
  <input type="hidden" name="hidd_last_stage" id="hidd_last_stage">
  <!-- <div class="table-responsive"> -->
  <table border="1" id="documentTypeDT" class="table table-bordered dataTable" role="grid" aria-describedby="example1_info">
    <tbody>
    <thead>
      <tr>
      <th nowrap>{{$language['actions']}}</th>
      <th>{{$language['workflow name']}} ({{$language['no:docs']}})</th>
      <th colspan="3">{{$language['stages']}}</th>
      </tr>
    </thead>
      @foreach ($workflows as $key => $value)
      <tr>
    
        <td nowrap>
        <a href="viewworkflow/{{@$value->workflow_id}}"><i class="fa fa-ellipsis-v" title="View Workflow"></i></a>
    <!-- super admin can only edit/delete forms -->
    @if(Auth::user()->user_role == 1)
        &nbsp;
        <a href="workflowEdit/{{@$value->workflow_id}}"><i class="fa fa-fw fa-pencil" title="Edit"></i></a>
        &nbsp;
        <i class="fa fa-close" onclick="del({{ @$value->workflow_id }}, '{{@$value->workflow_name}}')" title="Delete" style="color: red; cursor:pointer;"></i>
    @endif
      </td>
      <td>
        <span id ="{{@$value->workflow_id}}"><a @if(Auth::user()->user_role == 1)
        href="workflowEdit/{{@$value->workflow_id}}" @endif>{{ucfirst(@$value->workflow_name)}}</a>&nbsp;(@if(@$value->count[0]->numc != ''){{@$value->count[0]->numc}}@else{{0}}@endif)</span>
      </td>
      <td>
       <span id="panLeft" class="panner" data-scroll-modifier='-1' scroll='{{@$value->workflow_id}}'><i class="fa fa-fw fa-chevron-circle-left" title="Scroll Left"></i></span>
      </td>
      <td>
        <div id="container{{@$value->workflow_id}}" class="cont">

          <ul class="stagebuttons pull-left Sortable_stages" id ="test{{@$value->workflow_id}}">
            @foreach($value->stage as $stage)
              <li stage_id="{{$stage->workflow_stage_id}}" id="stage{{$stage->workflow_stage_id}}" style="background-color: {{@$value->workflow_color}} !important;border-color: {{@$value->workflow_color}} !important;margin-right: 15px;" @if(Auth::user()->user_role == 1) title="Double click to rename stage" @endif>

              <a order ="{{@$stage->workflow_stage_order}}" name ="stage_value{{@$value->workflow_id}}[]" id="stage_content{{$stage->workflow_stage_id}}"type="button" class="btn btn-default handler" style="background-color: {{@$value->workflow_color}} !important;border-color: {{@$value->workflow_color}} !important;min-width: 150px;" @if(Auth::user()->user_role == 1) ondblclick="myFunction({{$stage->workflow_stage_id}},{{@$value->workflow_id}},'{{$stage->workflow_stage_name}}');" @endif>{{ucfirst(@$stage->workflow_stage_name)}}<a>

              </a></a> <!-- super admin can only add stages -->
            @if(Auth::user()->user_role == 1)
            <i class="fa fa-fw fa-plus-circle" onclick="addNewStage('{{@$stage->workflow_stage_id}}','{{@$value->workflow_color}}',{{@$value->workflow_id}});" style="margin-right:-18px; cursor: pointer;" id="addnew{{$stage->workflow_stage_id}}" title="Add New Satge"></i>
            @endif
            <span class="arrow" style="border-left-color: {{@$value->workflow_color}} !important;"></span>

              </li>
            @endforeach
          </ul>
        </div>
      </td>
      <td>
        <span id="panRight" class="panner" data-scroll-modifier='1' scroll='{{@$value->workflow_id}}'><i class="fa fa-fw fa-chevron-circle-right" title="Scroll Right"></i></span>
      </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  <!-- </div> -->
  @endif
  <div class="modal fade" id="dTAddModal" data-backdrop="false" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="dGAddModal" >
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">
            {{$language['workflows']}}
            <small>- {{$language['add new']}}</small>
          </h4>
        </div>
        <div class="modal-body">
          <!-- form start -->
          {!! Form::open(array('url'=> array('WorkflowSave',0), 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'workflowAddForm', 'id'=> 'workflowAddForm','data-parsley-validate'=> '')) !!}            
              <div class="form-group">
                  <label for="Workflow" class="col-sm-2 control-label">{{$language['workflow name']}}: <span class="compulsary">*</span></label>
                  <div class="col-sm-8">
                      {!! Form:: 
                      text('workflowname','', 
                      array( 
                      'class'=> 'form-control', 
                      'id'=> 'workflowname', 
                      'title'=> "'".$language['workflow name']."'".$language['length_others'],
                      'placeholder'=> $language['workflow name'],
                      'required'               => '',
                      'data-parsley-required-message' => 'Workflow name is required',
                      'data-parsley-trigger'          => 'change focusout',
                      'data-parsley-maxlength' => $language['max_length'],
                      'autofocus'
                      )
                      ) 
                      !!}      
                      <!-- <div id="dp">
                          <span id="dp_wrn" style="display:none;">
                              <i class="fa fa-refresh fa-spin fa-1x fa-fw"></i>
                              <span class="">Please wait...</span>
                          </span>
                      </div> -->
                  </div>
              </div>
              <div class="form-group">
                <label class="col-xs-2 control-label">{{$language['color']}}:</label>
                <div class="col-xs-8">
                  <!-- color pallette -->
                  <div id="cp2" class="input-group colorpicker-component" title="Using input value">
                    <input type="text" name="color" class="form-control" value="#DD0F20" readonly="" />
                    <span class="input-group-addon"><i></i></span>
                  </div>
              </div>
              </div>
              <div id='TextBoxesGroup'>

                <div class="form-group">
                    <div class="hidden-xs col-xs-2">
                    </div>
                    <div class="col-xs-1">
                      {!! Form::label($language['order'], '', array('class'=> 'control-label'))!!}
                    </div>
                    <div class="col-xs-6">
                        {!! Form::label($language['stage'], '', array('class'=> 'control-label','id'=>'stage_label'))!!}
                    </div>
                    <div class="col-xs-2">
                        {!! Form::label($language['action'], '', array('class'=> 'control-label', 'style'=>'padding-left: 10px;'))!!}
                    </div>
                    <div class="hidden-xs col-xs-1"></div>
                </div>
                <div id="columns">
                  <div id="TextBoxDiv1" class="column" draggable="true" count="counter">
                    <div class="form-group">
                      <div class="hidden-xs col-xs-2"></div>
                      <div class="col-xs-1" id="order_stage1">1</div>
                      <div class="col-xs-6">
                        <input type="text" id="textbox1" name ="stage_name[]" count="1" class = "form-control" placeholder = "Stage Name" data-parsley-required-message = "Stage Name is required" data-parsley-trigger = "change focusout" required ondblclick="this.setSelectionRange(0, this.value.length)" data-parsley-maxlength ="{{$language['max_length']}}" title="'{{$language['index field']}}'{{$language['length_others']}}">
                      </div>
                      
                      
                      <div class="col-xs-2">
                        <small id="dctCnt1" style="cursor:pointer;padding-left: 10px;" order="1">
                            <i class="fa fa-trash fa-lg" style="font-size:20px; margin-top:7px;"></i>
                        </small>
                        <div class="grippy"></div>
                      </div>
                      <div class="hidden-xs col-xs-1"></div>
                    </div>
                  </div>
                </div>
          </div>
        <div class="form-group">
            <label class="col-sm-4 control-label hidden-xs"></label>
            {!!Form::hidden('count-textbox','',array('id'=>'counttextbox'))!!}
            <div class="col-sm-8">
                <input class="btn btn-primary" id="save" type="submit" value="{{$language['save']}}" > &nbsp;&nbsp;
                {!!Form::button($language['close'], array('class' => 'btn btn-primary btn-danger', 'data-dismiss'=> 'modal', 'aria-hidden'=> 'true')) !!}&nbsp;&nbsp;
                {!!Form::button($language['add stage'],array('class'=>'btn btn-primary','id'=>'addButton','value'=>'addButton'))!!}
            </div>
        </div><!-- /.col -->
        {!! Form::close() !!}
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
  
</div>
</div>
</div>
</div>
</section>
{!! Html::script('js/jquery-ui.min.js') !!}

<script type="text/javascript">
//set count of stages
  var last = '{{$last}}';
  var counter_stages = '{{$count_stages}}';
  $('#hidd_count_stages').val(counter_stages);
  $('#hidd_last_stage').val(last);
  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
// Add new stage

  function addNewStage(id,color,row_no)
  {
    
    if(color == null)
    {
      color = "{{$language['workflow_default_color']}}";
    }
    var counter_stages = $('#hidd_last_stage').val();
    counter_stages++;
        $('<li stage_id="'+counter_stages+'" id="stage'+counter_stages+'" style="background-color: '+color+' !important;border-color: '+color+' !important;margin-right: 18px;" title="Double click to rename stage"><a order="0" name="stage_value'+row_no+'[]" id="stage_content'+counter_stages+'" type="button" class="btn btn-default handler" style="background-color: '+color+' !important;border-color: '+color+' !important;min-width: 150px;" ondblclick="myFunction('+counter_stages+','+row_no+',);">New Stage<a></a></a><i class="fa fa-fw fa-plus-circle" onclick="addNewStage('+counter_stages+',\''+color+'\','+row_no+');" style="margin-right:-18px; cursor:pointer;" id="addnew'+counter_stages+'" title="Add New Satge"></i><span class="arrow" style="border-left-color:'+color+'"></span></li>').insertAfter('#stage'+id);
        $('#hidd_count_stages').val(counter_stages); 
        $('#hidd_last_stage').val(last);
        var data = $('#test'+row_no).sortable('toArray', { attribute: 'stage_id' });
        console.log(data);
        $.ajax({
            type: 'post',
            url: '{{URL('addNewStage')}}',
            dataType: 'json',
            data: {_token: CSRF_TOKEN, data:data,wf_id:row_no,stage_id:counter_stages},
            timeout: 50000,
            beforeSend: function() {
                $("#bs").show();
            },
            success: function(data, status){ 
              swal(data);
              return true;
                },
            complete: function() {
                $("#bs").hide();
            }
        });

  };

// Add new name to stage

  function myFunction(id,row_no,txt) {
    if(!txt)
    {
      var txt = "New Stage";
    }
    var Stage_name = swal({
                          title: 'Enter stage name', 
                          input: 'text',
                          inputValue: txt,
                          showCancelButton: true
                        }).then((result) => {
                            if(result){
                              $("#stage_content"+id).text(result);
                              $.ajax({
                                  type: 'post',
                                  url: '{{URL('renameNewStage')}}',
                                  dataType: 'json',
                                  data: {_token: CSRF_TOKEN,wf_id:row_no,stage_id:id,stage_name:result},
                                  timeout: 50000,
                                  beforeSend: function() {
                                      $("#bs").show();
                                  },
                                  success: function(data, status){ }
                                });
                            }
                            else{
                              $("#stage_content"+id).text(txt);
                            }
                          });
}
$("#counttextbox").val(1);
var dragSrcEl = null;
function handleDragStart(e) {
 dragSrcEl = this;

  e.dataTransfer.effectAllowed = 'move';
  e.dataTransfer.setData('text/html', this.innerHTML);
}
function handleDragOver(e) {
  if (e.preventDefault) {
    e.preventDefault(); // Necessary. Allows us to drop.
  }

  e.dataTransfer.dropEffect = 'move';  // See the section on the DataTransfer object.

  return false;
}

function handleDragEnter(e) {
  // this / e.target is the current hover target.
  this.classList.add('over');
}

function handleDragLeave(e) {
  this.classList.remove('over');  // this / e.target is previous target element.
}

function handleDrop(e) {
  // this/e.target is current target element.

  if (e.stopPropagation) {
    e.stopPropagation(); // Stops some browsers from redirecting.
  }

  // Don't do anything if dropping the same column we're dragging.
  if (dragSrcEl != this) {
    // Set the source column's HTML to the HTML of the column we dropped on.
    dragSrcEl.innerHTML = this.innerHTML;
    this.innerHTML = e.dataTransfer.getData('text/html');
  }

  return false;
}

function handleDragEnd(e) {
  // this/e.target is the source node.

  [].forEach.call(cols, function (col) {
    col.classList.remove('over');
  });
}

var cols = document.querySelectorAll('#columns .column');
[].forEach.call(cols, function(col) {
  col.addEventListener('dragstart', handleDragStart, false);
  col.addEventListener('dragenter', handleDragEnter, false);
  col.addEventListener('dragover', handleDragOver, false);
  col.addEventListener('dragleave', handleDragLeave, false);
  col.addEventListener('drop', handleDrop, false);
  col.addEventListener('dragend', handleDragEnd, false);
});

$("#addButton").click(function () {
    var counter = $("#counttextbox").val();
    counter++;
    var newTextBoxDiv = $(document.createElement('div')).attr("id", 'TextBoxDiv' + counter).attr("class", 'column').attr("draggable", true).attr("count",counter);
    newTextBoxDiv.after().html('<div class="form-group"><div class="hidden-xs col-xs-2"></div><div class="col-xs-1" id="order_stage'+counter+'">'+counter+'</div><div class="col-xs-6"><input type="text" autofocus="autofocus" id="textbox' + counter + '" name ="stage_name[]" value = "" count="'+ counter +'" class = "form-control" placeholder = "Stage Name" data-parsley-required-message = "Stage Name is required" data-parsley-trigger = "change focusout" required count="'+ counter +'" ondblclick="this.setSelectionRange(0, this.value.length)" data-parsley-maxlength ="30" title="Index Field length must be less than 30 characters"></div><div class="col-xs-2"><small id="dctCnt'+counter+'" style="cursor:pointer;padding-left: 10px;"" order="'+ counter +'"><i class="fa fa-trash fa-lg" style="font-size:20px; margin-top:7px;"></i></small><div class="grippy"></div></div><div class="hidden-xs col-xs-1"></div></div>');
    newTextBoxDiv.appendTo("#columns");  
    $("#textbox"+counter-1).blur();    
    $("#textbox"+counter).focus();    
    $("#counttextbox").val(counter);
    var cols = document.querySelectorAll('#columns .column');
    [].forEach.call(cols, function(col) {
      col.addEventListener('dragstart', handleDragStart, false);
      col.addEventListener('dragenter', handleDragEnter, false);
      col.addEventListener('dragover', handleDragOver, false);
      col.addEventListener('dragleave', handleDragLeave, false);
      col.addEventListener('drop', handleDrop, false);
      col.addEventListener('dragend', handleDragEnd, false);
    });    

});
//on mouse over reset all counts ids
$('#workflowAddForm').on('mouseover',function(){
  
    $("input[id^='textbox']").each(function(i) {
        $(this).attr('id', "textbox" + (i + 1));
        $(this).attr('count', "" + (i + 1));
    });
    $("div[id^='TextBoxDiv']").each(function(i) {
        $(this).attr('id', "TextBoxDiv" + (i + 1));
        $(this).attr('count', "" + (i + 1));
    });
    $("small[id^='dctCnt']").each(function(i) {
        $(this).attr('id', "dctCnt" + (i + 1));
        $(this).attr('order', "" + (i + 1));
    });
    $("div[id^='order_stage']").each(function(i) {
        $(this).attr('id', "order_stage" + (i + 1));
        $(this).text((i + 1));
    });
});
//Delete stages
$('#workflowAddForm').on('click','small',function(){
  var count = $(this).attr('order');
  var counter = $("#counttextbox").val();
  var txtname = $("#textbox"+count).val();
  if(counter==1)
  {
    swal("{{$language['one_stage']}}");
    return false;
  }
  else
  {
    if(txtname)
    {
    swal({
        title: "{{$language['confirm_delete_single']}}"+txtname+"?",
        text: "{{$language['Swal_not_revert']}}",
        type: "{{$language['Swal_warning']}}",
        showCancelButton: true
      }).then(function (result)
      {
        if(result)
        {
          // Success
          $("#TextBoxDiv" + count).remove();
          counter--;
          $("input[id^='textbox']").each(function(i) {
              $(this).attr('id', "textbox" + (i + 1));
              $(this).attr('count', "" + (i + 1));
          });
          $("div[id^='TextBoxDiv']").each(function(i) {
              $(this).attr('id', "TextBoxDiv" + (i + 1));
              $(this).attr('count', "" + (i + 1));
          });
          $("small[id^='dctCnt']").each(function(i) {
              $(this).attr('id', "dctCnt" + (i + 1));
              $(this).attr('order', "" + (i + 1));
          });
          $("div[id^='order_stage']").each(function(i) {
              $(this).attr('id', "order_stage" + (i + 1));
              $(this).text((i + 1));
          });
          $("#counttextbox").val(counter); 
          swal(
            '{{$language['Swal_deleted']}}'
              )
        }
      });

    }
    else
    {
     swal({
        title: "{{$language['confirm_delete_single']}}this stage?",
        text: "{{$language['Swal_not_revert']}}",
        type: "{{$language['Swal_warning']}}",
        showCancelButton: true
      }).then(function (result) 
      {
        if(result)
        {
            // Success
             $("#TextBoxDiv" + count).remove();
          counter--;
          $("input[id^='textbox']").each(function(i) {
            $(this).attr('id', "textbox" + (i + 1));
            $(this).attr('count', "" + (i + 1));
          });
          $("div[id^='TextBoxDiv']").each(function(i) {
              $(this).attr('id', "TextBoxDiv" + (i + 1));
              $(this).attr('count', "" + (i + 1));
          });
          $("small[id^='dctCnt']").each(function(i) {
              $(this).attr('id', "dctCnt" + (i + 1));
              $(this).attr('order', "" + (i + 1));
          });
          $("div[id^='order_stage']").each(function(i) {
              $(this).attr('id', "order_stage" + (i + 1));
              $(this).text((i + 1));
          });
          $("#counttextbox").val(counter); 
          swal(
            '{{$language['Swal_deleted']}}'
            )
        }
      });
    }
  }
});

  $(function () {
    $('#cp2').colorpicker({
            format: 'rgb'
        });

    $('.Sortable_stages').sortable({
    placeholder         : 'sort-highlight',
    handle              : '.handler',
    forcePlaceholderSize: true,
    zIndex              : 999999,
    helper: 'clone',
    tolerance: "pointer",
    stop: function (event, ui) {

      var data = $(this).sortable('toArray', { attribute: 'stage_id' });
      console.log(data);
      var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            type: 'post',
            url: '{{URL('workflowReArrangeStages')}}',
            dataType: 'json',
            data: {_token: CSRF_TOKEN, data:data},
            timeout: 50000,
            beforeSend: function() {
                $("#bs").show();
            },
            success: function(data, status){ 
              swal(data);
              return true;
                },
            complete: function() {
                $("#bs").hide();
            }
        });
       
    }
  });
  });
  function del(id,workflow)
  {   
    swal({
        title: "{{$language['confirm_delete_single']}}'" + workflow + "' ?",
        text: "{{$language['Swal_not_revert']}}",
        type: "{{$language['Swal_warning']}}",
        showCancelButton: true
      }).then(function (result) {
          if(result){
              var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
              $.ajax({
                  type: 'post',
                  url: '{{URL('workflowDelete')}}',
                  data: {_token: CSRF_TOKEN, id:id},
                  beforeSend: function() {
                      $("#bs").show();
                  },
                  success: function(data, status){ 
                    swal({
                      title: data,
                      showCancelButton: false
                    }).then(function (result) {
                        window.location.reload();
                  });
                  },
                  complete: function(data) {
                      $("#bs").hide();
                  }
              });

        }
    });
  }
  (function () {

    var scrollHandle = 0;
    var scrollStep = 2;


    //Start the scrolling process
    $(".panner").on("mouseenter", function () {
        var data = $(this).data('scrollModifier'),
            direction = parseInt(data, 10);
var id = $(this).attr('scroll');
var parent = $("#container"+id);
        $(this).addClass('active_row');

        startScrolling(direction, scrollStep, parent);
    });

    //Kill the scrolling
    $(".panner").on("mouseleave", function () {
        stopScrolling();
        $(this).removeClass('active_row');
    });

    //Actual handling of the scrolling
    function startScrolling(modifier, step,parent) {
        if (scrollHandle === 0) {
            scrollHandle = setInterval(function () {
                var newOffset = parent.scrollLeft() + (scrollStep * modifier);

                parent.scrollLeft(newOffset);
            }, 10);
        }
    }

    function stopScrolling() {
        clearInterval(scrollHandle);
        scrollHandle = 0;
    }

}());
</script>
{!! Html::script('js/drag.js') !!} 
@endsection