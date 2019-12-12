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
    <small>- {{$language['edit workflow']}}</small>
  </span>
</div>
<div class="col-xs-4">
  <!-- <ol class="breadcrumb">
      <li><a href="{{url('/home')}}"><i class="fa fa-dashboard"></i> {{$language['home']}}</a></li>
      <li class="active">{{$language['work_flow']}}</li>
  </ol> -->
</div>
</section>
<style type="text/css">
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
  @media(max-width:400px){
  div.grippy{
    margin-top: -0.7em !important;
    margin-right: -2.7em !important;
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
  <div class="modal-body">
          <!-- form start -->
          {!! Form::open(array('url'=> array('WorkflowSave',$wf_id), 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'workflowEditForm', 'id'=> 'workflowEditForm','data-parsley-validate'=> '')) !!} 
            @foreach($workflow_single as $key => $value)           
              <div class="form-group">
                  <label for="Workflow" class="col-sm-2 control-label">{{$language['workflow name']}}: <span class="compulsary">*</span></label>
                  <div class="col-sm-8">
                      {!! Form:: 
                      text('workflowname',$value->workflow_name, 
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
                    <input type="text" name="color" class="form-control" value="{{@$value->workflow_color}}" readonly="" />
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
                <input type="hidden" name="hidd_count_stages" id="hidd_count_stages">
                <input type="hidden" name="hidd_last_stage" id="hidd_last_stage">
                <div id="columns">
                @foreach($value->stage as $stage)
                  <div id="TextBoxDiv{{@$stage->workflow_stage_order}}" class="column" draggable="true" count="{{@$stage->workflow_stage_order}}">
                    <div class="form-group">
                      <div class="hidden-xs col-xs-2"></div>
                      <div class="col-xs-1" id="order_stage{{@$stage->workflow_stage_order}}">{{@$stage->workflow_stage_order}}</div>
                      <div class="col-xs-6">
                        <input type="hidden" id="stagefield{{@$stage->workflow_stage_order}}" name="stagefield{{@$stage->workflow_stage_order}}" value="{{@$stage->workflow_stage_id}}">
                        <input type="text" id="textbox{{@$stage->workflow_stage_order}}" name ="stage_name[]" count="{{@$stage->workflow_stage_order}}" class = "form-control" placeholder = "Stage Name" data-parsley-required-message = "Stage Name is required" data-parsley-trigger = "change focusout" required ondblclick="this.setSelectionRange(0, this.value.length)" data-parsley-maxlength ="{{$language['max_length']}}" title="'{{$language['index field']}}'{{$language['length_others']}}" value="{{@$stage->workflow_stage_name}}">
                      </div>
                      
                      
                      <div class="col-xs-2">
                        <small id="dctCnt{{@$stage->workflow_stage_order}}" style="cursor:pointer;padding-left: 10px;" order="{{@$stage->workflow_stage_order}}">
                            <i class="fa fa-trash fa-lg" style="font-size:20px; margin-top:7px;"></i>
                        </small>
                        <div class="grippy"></div>
                      </div>
                      <div class="hidden-xs col-xs-1"></div>
                    </div>
                  </div>
                  @endforeach
                </div>
          </div>
    <div class="form-group">
        <label class="hidden-xs col-sm-4 control-label"></label>
        {!!Form::hidden('count-textbox','',array('id'=>'counttextbox'))!!}
        <div class="col-sm-8">
            <input class="btn btn-primary" id="save" type="submit" value="{{$language['update']}}" > &nbsp;&nbsp;
            <a href="{{URL::route('workFlow')}}" class = "btn btn-primary btn-danger">Cancel</a>&nbsp;&nbsp;
            {!!Form::button($language['add stage'],array('class'=>'btn btn-primary','id'=>'addButton','value'=>'addButton'))!!}
        </div>
    </div><!-- /.modal -->
      {!! Form::close() !!}
  </div>
      @endforeach
</div>
</div>
</section>
<script type="text/javascript">
//set count of stages
var last = '{{$last}}';
var counter_stages = '{{$count_stages}}';
$('#hidd_count_stages').val(counter_stages);
$('#hidd_last_stage').val(last);
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
    newTextBoxDiv.after().html('<div class="form-group"><div class="hidden-xs col-xs-2"></div><div class="col-xs-1" id="order_stage'+counter+'">'+counter+'</div><div class="col-xs-6"><input type="hidden" id="stagefield' + counter + '" name="stagefield' + counter + '" value="0"><input type="text" autofocus="autofocus" id="textbox' + counter + '" name ="stage_name[]" value = "" count="'+ counter +'" class = "form-control" placeholder = "Stage Name" data-parsley-required-message = "Stage Name is required" data-parsley-trigger = "change focusout" required count="'+ counter +'" ondblclick="this.setSelectionRange(0, this.value.length)" data-parsley-maxlength ="30" title="Index Field length must be less than 30 characters"></div><div class="col-xs-2"><small id="dctCnt'+counter+'" style="cursor:pointer;padding-left: 9px;"" order="'+ counter +'"><i class="fa fa-trash fa-lg" style="font-size:20px; margin-top:7px;"></i></small><div class="grippy"></div></div><div class="hidden-xs col-xs-1"></div></div>');
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
$('#workflowEditForm').on('mouseover',function(){
  
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
    $("input[id^='stagefield']").each(function(i){
        $(this).attr('name',"stagefield" + (i + 1));
        $(this).attr('id',"stagefield" + (i + 1));
    });
});
//Delete stages

$('#workflowEditForm').on('click','small',function(){
  var count = $(this).attr('order');
  var counter = $("#counttextbox").val();
  var txtname = $("#textbox"+count).val();
  var previous_id=$("#stagefield"+count).val();
  if(counter==1){
       swal("{{$language['one_stage']}}");
       return false;
  }
  else
  {
    if(txtname){
        var answer = "{{$language['confirm_delete_single']}}"+txtname+"?";
    }else{
        var answer = "{{$language['confirm_delete_single']}}this stage?";
    }
    swal({
        title: ''+answer+'',
        text: "{{$language['Swal_not_revert']}}",
        type: "{{$language['Swal_warning']}}",
        showCancelButton: true
      }).then(function (result) {
        if(result){
        // Success

        if(previous_id!=0){

          var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
              type: 'post',
              url: '{{URL('workflowStageDelete')}}',
              data: {_token: CSRF_TOKEN, id:previous_id,stage_name:txtname},
              beforeSend: function() {
                  //$("#dp_wrn").show();
                  //$("#save").attr("disabled", true);
              },
              success: function(data, status){
                if(data==1)
                {
                swal("{{$language['Swal_stage_cant_be_deleted']}}");
                return false;
                }
                else
                {
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
                  $("input[id^='stagefield']").each(function(i){
                      $(this).attr('name',"stagefield" + (i + 1));
                      $(this).attr('id',"stagefield" + (i + 1));
                  });
                  $("#counttextbox").val(counter);
                        swal(data);
                      }
                    },
                    error: function(jqXHR, textStatus, errorThrown){
                        console.log(jqXHR);    
                        console.log(textStatus);    
                        console.log(errorThrown);    
                    },
                    complete: function() {
                        
                    }
                  });
                      
                  }
                  else
                  {

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
                    $("input[id^='stagefield']").each(function(i){
                        $(this).attr('name',"stagefield" + (i + 1));
                        $(this).attr('id',"stagefield" + (i + 1));
                    });
                    $("#counttextbox").val(counter); 
                }
              }
          });

  }
});
$(function () {
    $('#cp2').colorpicker({
            format: 'rgb'
        });
    });
</script>
{!! Html::script('js/jquery-ui.min.js') !!}
{!! Html::script('js/drag.js') !!} 
@endsection