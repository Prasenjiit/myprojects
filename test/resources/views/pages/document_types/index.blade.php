<?php
  include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')
@section('main_content')
    {!! Html::style('plugins/datatables_new/jquery.dataTables.min.css') !!}
    {!! Html::style('plugins/datatables_new/rowReorder.dataTables.min.css') !!}
    {!! Html::script('plugins/datatables_new/jquery.dataTables.min.js') !!}
    {!! Html::script('plugins/datatables_new/dataTables.rowReorder.min.js') !!}
    {!! Html::script('plugins/datatables_new/jquery.dataTables.rowReordering.js') !!}
<style type="text/css">
@media(max-width:527px){
    /*.box{
      overflow-x: scroll;
    }*/
  }
</style>
<section class="content-header">

	<div class="col-sm-8">
        <span style="float:left;">
            <strong>{{trans('document_types.document types')}} </strong>&nbsp;
        </span>
        <span style="float:left;">
            <?php
            $user_permission=Auth::user()->user_permission;                       
            ?>     
            @if(stristr($user_permission,"add") && Auth::user()->user_role != Session::get('user_role_private_user'))
                <a href="" data-toggle="modal" data-target="#dTAddModal">
                    <button class="btn btn-block btn-info btn-flat newbtn">Add New  <i class="fa fa-plus"></i></button>
                </a>
            @endif
        </span>
    </div>
    <div class="col-sm-4">
        <!-- <ol class="breadcrumb">
            <li><a href="{{url('/home')}}"><i class="fa fa-dashboard"></i> {{trans('language.home')}}</a></li>
            <li class="active">{{trans('document_types.document types')}}</li>
        </ol> -->
    </div>
    
  
</section>
<section class="content content-sty" id="msg" style="display:none;"></section>
<section class="content content-sty" id="msg_add" style="display:none;"></section>
@if(Session::has('flash_message_edit'))
<section class="content content-sty" id="spl-wrn">        
    <div class="alert alert-sty {{ Session::get('alert-class', 'alert-info') }} ">{{ Session::get('flash_message_edit') }}</div>        
</section>
@endif   
<section class="content content-sty" id="error-msg" style="display: none;"><div class="alert alert-danger alert-sty" id="twm-msg"><strong id="strong-msg">{{trans('language.error_document_types')}}</strong></div></section>
<div class="preloader col-sm-12" style="text-align: center; margin-top: 50px; display: none;" id="bs">
      <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
      <span class="sr-only">Loading...</span>
</div> 
<section class="content" id="shw">
    
</section>
<!-- User add Form -->
<div class="modal fade" id="dTAddModal" data-backdrop="false" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="dGAddModal" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                 {{trans('document_types.document types')}}
             </h4>
         </div>
         <div class="modal-body">
            <!-- form start -->
            {!! Form::open(array('url'=> array('documentTypeSave','0'), 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'documentTypeAddForm', 'id'=> 'documentTypeAddForm','data-parsley-validate'=> '')) !!}
                        
            <div class="form-group">
                <label for="Doc Type" class="col-sm-2 control-label">{{trans('document_types.document type')}}: <span class="compulsary">*</span></label>
                <div class="col-sm-8">
                    <input class="form-control" id="name" placeholder="Document Type" required="" data-parsley-required-message="Document type is required" autofocus="autofocus" name="name" type="search" value="" data-parsley-maxlength ="{{trans('language.max_length')}}" title="{{trans('document_types.document type')}} {{trans('language.length_others')}}">    
                    <div id="dp">
                        <span id="dp_wrn" style="display:none;">
                            <i class="fa fa-refresh fa-spin fa-1x fa-fw"></i>
                            <span class="">Please wait...</span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('Description:', '', array('class'=> 'col-sm-2 control-label'))!!}
                <div class="col-sm-8">
                    {!! Form:: 
                    textarea(
                    'description', '', 
                    array( 
                    'class'                  => 'form-control', 
                    'id'                     => 'description',  
                    'placeholder'            => 'Description',
                    'title'                  => trans('language.length_description'),
                    'data-parsley-maxlength'   => trans('language.max_length_description')
                    )
                    ) 
                    !!}
                    <span class="dms_error">{{$errors->first('description')}}</span>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-2"></div>
                <div class="col-sm-8">
                    <div style="color:#777;">Document number and Document Name are two mandatory fields for each document type. You can rename them to suit a particular document type. For e.g., it could be Employee Number and Employee Name for HR records, Case Number and Client Name for Law firms, Contract Number and Client Name for Consulting firms or Legal deapatments.</div>
                </div>
                <div class="col-sm-2"></div>
            </div>

             <div id='TextBoxesGroup'>

                <div class="form-group">
                <div class="col-sm-2"></div>
                <div class="col-sm-3">
                    <input type="text" value="{{@$settings_document_no}}" class="form-control" readonly="readonly" disabled>
                </div>
                <div class="col-sm-3">
                    <input type="text" value="{{@$settings_document_no}}" class="form-control" name="field1">
                </div>
                <div class="col-sm-1">
                    <input type="checkbox" readonly="readonly" checked="checked" disabled><br>                        
                </div>
                <div class="col-sm-1">
                    
                        <i class="fa fa-trash fa-lg" style="padding-left: 10px; font-size:20px; margin-top:7px; color: #d6d1d1;"></i>
                                           
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group">
                <div class="col-sm-2"></div>
                <div class="col-sm-3">
                    <input type="text" value="{{@$settings_document_name}}" class="form-control" readonly="readonly" disabled>
                </div>
                <div class="col-sm-3">
                    <input type="text" value="{{@$settings_document_name}}" class="form-control" name="field2">
                </div>
                <div class="col-sm-1">
                    <input type="checkbox" readonly="readonly" checked="checked" disabled
                    ><br>                        
                </div>
                <div class="col-sm-1">
                    
                        <i class="fa fa-trash fa-lg disabled" style="padding-left: 10px; font-size:20px; margin-top:7px; color: #d6d1d1;"></i>
                                          
                </div>
                <div class="col-sm-2"></div>
            </div>  

            <div class="col-sm-2"></div><div style="color:#777;">{{trans('document_types.doc_type_drag_msg')}}</div> 
            <div class="form-group">
                <div class="col-sm-2"></div>
                <!-- <div class="col-sm-1"></div> -->
                <div class="col-sm-3">
                    {!! Form::label(trans('language.index field'), '', array('class'=> 'control-label'))!!}
                </div>
                <div class="col-sm-3">
                    {!! Form::label(trans('language.field type'), '', array('class'=> 'control-label'))!!}
                </div>
                 <div class="col-sm-1">
                    {!! Form::label(trans('language.mandatory'), '', array('class'=> 'control-label'))!!}
                </div>
                <div class="col-sm-1">
                    {!! Form::label(trans('language.action'), '', array('class'=> 'control-label', 'style'=>'padding-left: 10px;'))!!}
                </div>
                <div class="col-sm-2"></div>
            </div>
                <div id="columns">
                  
                </div>
          </div>
        <div class="form-group">
            <label class="col-sm-4 control-label"></label>
            {!!Form::hidden('count-textbox','',array('id'=>'counttextbox'))!!}
            <div class="col-sm-8">
            <!-- <button type="button" onclick="myFunction()">Try it</button> -->
                <input class="btn btn-primary" id="save" type="submit" value="{{trans('language.save')}}" > &nbsp;&nbsp;
                {!!Form::button(trans('language.close'), array('class' => 'btn btn-primary btn-danger', 'data-dismiss'=> 'modal', 'aria-hidden'=> 'true')) !!}&nbsp;&nbsp;
                {!!Form::button(trans('language.add index'),array('class'=>'btn btn-primary','id'=>'addButton','value'=>'addButton'))!!}
            </div>
        </div><!-- /.col -->
        {!! Form::close() !!}
    </div>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<style type="text/css">
    .modal-body{
        overflow: auto;
        max-height: 550px;
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
    .slide-placeholder {
        background: #FFF;
        position: relative;
    }
    .slide-placeholder:after {
        content: " ";
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 0;
        background-color: #FFF;
    }
    .tooltip{
    background-color:#000;
    padding:10px 15px;
    width:auto;
    display:none;
    color:#fff;
    text-align:left;
    font-size:12px;
    top: 37px !important;
    left: 15px !important;
    }
</style>
<script type="text/javascript">
//<---------------------- all text are null -------------------->
// var elements = document.getElementsByTagName("input");
// for (var ii=0; ii < elements.length; ii++) {
//   if (elements[ii].type == "text") {
//     elements[ii].value = "";
//   }
// }
//<------------------------------------------------------------->
$('#documentTypeAddForm').on('change','select',function(){
  var id = $(this).val();
  var count=$(this).attr('count');
  switch (id){
    case "Piclist":
      $('#piclistdiv'+count+'').show();
      break;
    case "Yes/No":
      $('#piclistdiv'+count+'').css('display','none');
      break;
    case "Date":
      $('#piclistdiv'+count+'').css('display','none');
      break;
    case "Alphanumeric":
      $('#piclistdiv'+count+'').css('display','none');
      break;
    case "Number":
      $('#piclistdiv'+count+'').css('display','none');
      break;
  }
});

$('#documentTypeAddForm').on('click','input[type="button"]',function(){
  var count=$(this).attr('count');
  if($(this).attr('identity')=='picadd')
  {
    var text=$('#readme'+count).val();
    if(text.length>50)
    {
      swal("{{trans('language.option_length_must_be')}}");
      return false;
    }
    if(text.length==0)
    {
      swal("{{trans('language.please_fill_the_text')}}");
      return false;
    }
    $("#List"+count).append('<option>'+$('#readme'+count).val()+'</option>');
    myList = [];
    $('#List'+count+' option').each(function() {
    myList.push($(this).val())
    });
    var arr=myList.toString();
    document.getElementById("hidd_options"+count).value = arr;
  }
  if($(this).attr('identity')=='picdelete')
  {
    if( $("#List"+count).has('option').length == 0 ) {
      swal("{{trans('language.select_box_is_empty')}}");
      return false;
    }
    else
    {
    $("#List"+count+" option:selected").remove();
    myList = [];
    $('#List'+count+' option').each(function() {
    myList.push($(this).val())
    });
    var arr=myList.toString();
    document.getElementById("hidd_options"+count).value = arr;
  }
  }
});

//<------------get previous value(text missing)-------------->

// $('#documentTypeAddForm').on('keyup click blur','input[type="text"]',function(){
//   var count = $(this).attr('count');
//   if(count){
//     var x = $(this).val();
//     $('#textbox'+count).attr('value',x);
//   }
// });
//<------------------------------------------------------------->
$('#documentTypeAddForm').on('click','input[type="checkbox"]',function(){
  var count = $(this).attr('order');
      if($(this).prop("checked") == true){
      $(this).attr('checked',true);
      }
      else if($(this).prop("checked") == false){
      $(this).attr('checked',false);
      }
  });

$('#documentTypeAddForm').on('click','span',function(){
  var count = $(this).attr('order');
  var counter = $("#counttextbox").val();
  var txtname = $("#textbox"+count).val();
  // if(counter==1){
  //      swal("{{$language['one_index_field']}}");
  //      return false;
  // }else{
      if(txtname){

          swal({
              title: "{{trans('language.confirm_delete_single')}}"+txtname+"?",
              text: "{{trans('language.Swal_not_revert')}}",
              type: "{{trans('language.Swal_warning')}}",
              showCancelButton: true
            }).then(function (result) {
                if(result){
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
                    $("select[id^='select']").each(function(i) {
                        $(this).attr('id', "select" + (i + 1));
                        $(this).attr('count', "" + (i + 1));
                    });
                    $("input[id^='doc_mandatory']").each(function(i) {
                        $(this).attr('id', "doc_mandatory" + (i + 1));
                        $(this).attr('name', "doc_mandatory" + (i + 1));
                        $(this).attr('order', "" + (i + 1));
                    });
                    $("span[id^='dctCnt']").each(function(i) {
                        $(this).attr('id', "dctCnt" + (i + 1));
                        $(this).attr('order', "" + (i + 1));
                    });
                    $("div[id^='piclistdiv']").each(function(i) {
                        $(this).attr('id', "piclistdiv" + (i + 1));
                        $(this).attr('count', "" + (i + 1));
                    });
                    $("input[id^='readme']").each(function(i) {
                        $(this).attr('id', "readme" + (i + 1));
                        $(this).attr('count', "" + (i + 1));
                    });
                    $("select[id^='List']").each(function(i) {
                        $(this).attr('id', "List" + (i + 1));
                        $(this).attr('count', "" + (i + 1));
                    });
                    $("input[id^='delete']").each(function(i) {
                        $(this).attr('id', "delete" + (i + 1));
                        $(this).attr('count', "" + (i + 1));
                    });
                    $("input[id^='add']").each(function(i) {
                        $(this).attr('id', "add" + (i + 1));
                        $(this).attr('count', "" + (i + 1));
                    });
                    $("input[id^='hidd_options']").each(function(i) {
                        $(this).attr('id', "hidd_options" + (i + 1));
                        $(this).attr('count', "" + (i + 1));
                    });
                    $("input[id^='hidd_visibility']").each(function(i) {
                        $(this).attr('id', "hidd_visibility" + (i + 1));
                        $(this).attr('count', "" + (i + 1));
                    });
                    $("#counttextbox").val(counter); 

                swal(
                  "{{trans('language.Swal_deleted')}}"
                )
              }
          });

      }else{

           swal({
              title: "{{trans('language.confirm_delete_single')}}this column?",
              text: "{{trans('language.Swal_not_revert')}}",
              type: "{{trans('language.Swal_warning')}}",
              showCancelButton: true
            }).then(function (result) {
                if(result){
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
                    $("select[id^='select']").each(function(i) {
                        $(this).attr('id', "select" + (i + 1));
                        $(this).attr('count', "" + (i + 1));
                    });
                    $("input[id^='doc_mandatory']").each(function(i) {
                        $(this).attr('id', "doc_mandatory" + (i + 1));
                        $(this).attr('name', "doc_mandatory" + (i + 1));
                        $(this).attr('order', "" + (i + 1));
                    });
                    $("span[id^='dctCnt']").each(function(i) {
                        $(this).attr('id', "dctCnt" + (i + 1));
                        $(this).attr('order', "" + (i + 1));
                    });
                    $("div[id^='piclistdiv']").each(function(i) {
                        $(this).attr('id', "piclistdiv" + (i + 1));
                        $(this).attr('count', "" + (i + 1));
                    });
                    $("input[id^='readme']").each(function(i) {
                        $(this).attr('id', "readme" + (i + 1));
                        $(this).attr('count', "" + (i + 1));
                    });
                    $("select[id^='List']").each(function(i) {
                        $(this).attr('id', "List" + (i + 1));
                        $(this).attr('count', "" + (i + 1));
                    });
                    $("input[id^='delete']").each(function(i) {
                        $(this).attr('id', "delete" + (i + 1));
                        $(this).attr('count', "" + (i + 1));
                    });
                    $("input[id^='add']").each(function(i) {
                        $(this).attr('id', "add" + (i + 1));
                        $(this).attr('count', "" + (i + 1));
                    });
                    $("input[id^='hidd_options']").each(function(i) {
                        $(this).attr('id', "hidd_options" + (i + 1));
                        $(this).attr('count', "" + (i + 1));
                    });
                    $("input[id^='hidd_visibility']").each(function(i) {
                        $(this).attr('id', "hidd_visibility" + (i + 1));
                        $(this).attr('count', "" + (i + 1));
                    });
                    $("#counttextbox").val(counter); 

                swal(
                  "{{trans('language.Swal_deleted')}}"
                )
              }
          });

      }
 // }
});

$('#documentTypeAddForm').on('mouseover',function(){
  
    $("input[id^='textbox']").each(function(i) {
        $(this).attr('id', "textbox" + (i + 1));
        $(this).attr('count', "" + (i + 1));
    });
    $("div[id^='TextBoxDiv']").each(function(i) {
        $(this).attr('id', "TextBoxDiv" + (i + 1));
        $(this).attr('count', "" + (i + 1));
    });
    $("select[id^='select']").each(function(i) {
        $(this).attr('id', "select" + (i + 1));
        $(this).attr('count', "" + (i + 1));
    });
    $("input[id^='doc_mandatory']").each(function(i) {
        $(this).attr('id', "doc_mandatory" + (i + 1));
        $(this).attr('name', "doc_mandatory" + (i + 1));
        $(this).attr('order', "" + (i + 1));
    });
    $("span[id^='dctCnt']").each(function(i) {
        $(this).attr('id', "dctCnt" + (i + 1));
        $(this).attr('order', "" + (i + 1));
    });
    $("div[id^='piclistdiv']").each(function(i) {
        $(this).attr('id', "piclistdiv" + (i + 1));
        $(this).attr('count', "" + (i + 1));
    });
    $("input[id^='readme']").each(function(i) {
        $(this).attr('id', "readme" + (i + 1));
        $(this).attr('count', "" + (i + 1));
    });
    $("select[id^='List']").each(function(i) {
        $(this).attr('id', "List" + (i + 1));
        $(this).attr('count', "" + (i + 1));
    });
    $("input[id^='delete']").each(function(i) {
        $(this).attr('id', "delete" + (i + 1));
        $(this).attr('count', "" + (i + 1));
    });
    $("input[id^='add']").each(function(i) {
        $(this).attr('id', "add" + (i + 1));
        $(this).attr('count', "" + (i + 1));
    });
    $("input[id^='hidd_options']").each(function(i) {
        $(this).attr('id', "hidd_options" + (i + 1));
        $(this).attr('count', "" + (i + 1));
    });
    $("input[id^='hidd_visibility']").each(function(i) {
        $(this).attr('id', "hidd_visibility" + (i + 1));
        $(this).attr('count', "" + (i + 1));
    }); 
  
});
$('#documentTypeAddForm').on('change','select',function(){

  var count=$(this).attr('count');
  if(count){
  var text=$( "#select"+count+" option:selected" ).text();
  if(text=="Date"){
    $("#select"+count+" option[value='Alphanumeric']").removeAttr("selected");
    $("#select"+count+" option[value='Number']").removeAttr("selected");
    $("#select"+count+" option[value='Date']").attr("selected","selected");
    $("#select"+count+" option[value='Yes/No']").removeAttr("selected");
    $("#select"+count+" option[value='Piclist']").removeAttr("selected");
    $("#hidd_visibility"+count).val(0);
    document.getElementById("List"+count).required = false;
  }
  if(text=="Alphanumeric"){
    $("#select"+count+" option[value='Alphanumeric']").attr("selected","selected");
    $("#select"+count+" option[value='Number']").removeAttr("selected");
    $("#select"+count+" option[value='Date']").removeAttr("selected");
    $("#select"+count+" option[value='Yes/No']").removeAttr("selected");
    $("#select"+count+" option[value='Piclist']").removeAttr("selected");
    $("#hidd_visibility"+count).val(0);
    document.getElementById("List"+count).required = false;
  }
  if(text=="Number"){
    $("#select"+count+" option[value='Alphanumeric']").removeAttr("selected");
    $("#select"+count+" option[value='Number']").attr("selected","selected");
    $("#select"+count+" option[value='Date']").removeAttr("selected");
    $("#select"+count+" option[value='Yes/No']").removeAttr("selected");
    $("#select"+count+" option[value='Piclist']").removeAttr("selected");
    $("#hidd_visibility"+count).val(0);
    document.getElementById("List"+count).required = false;
  }
  if(text=="Yes/No"){
    $("#select"+count+" option[value='Alphanumeric']").removeAttr("selected");
    $("#select"+count+" option[value='Number']").removeAttr("selected");
    $("#select"+count+" option[value='Date']").removeAttr("selected");
    $("#select"+count+" option[value='Yes/No']").attr("selected","selected");
    $("#select"+count+" option[value='Piclist']").removeAttr("selected");
    $("#hidd_visibility"+count).val(0);
    document.getElementById("List"+count).required = false;
  }
  if(text=="Pick List"){
    $("#select"+count+" option[value='Alphanumeric']").removeAttr("selected");
    $("#select"+count+" option[value='Number']").removeAttr("selected");
    $("#select"+count+" option[value='Date']").removeAttr("selected");
    $("#select"+count+" option[value='Yes/No']").removeAttr("selected");
    $("#select"+count+" option[value='Piclist']").attr("selected","selected");
    $("#hidd_visibility"+count).val(1);
    document.getElementById("List"+count).required = true;
  }
  }

});
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

    $(function ($) {
		//$('#dTAddModal').hide();
        //$('input[type="search"]').focus();
        $("#counttextbox").val(0);

        load();
        //Ajax form
        var options = { 
            target:        '#msg_add',   // target element(s) to be updated with server response 
            beforeSubmit:  showRequest,  // pre-submit callback 
            success:       showResponse,  // post-submit callback     
            complete:      showStatus
        }; 
        // bind form using 'ajaxForm' 
        $('#documentTypeAddForm').ajaxForm(options);

        //Add Modal reset
        $("#cn").click(function(){
            // $("#documentTypeAddForm")[0].reset();
            // $('#documentTypeAddForm').parsley().reset();
            // $("#dp").text('');
            window.location.reload();
        });
        
        $('#spl-wrn').delay(5000).slideUp('slow');

        
        $("#addButton").click(function () {
            var counter = $("#counttextbox").val();
            counter++;
            var newTextBoxDiv = $(document.createElement('div')).attr("id", 'TextBoxDiv' + counter).attr("class", 'column').attr("draggable", true).attr("count",counter);
            newTextBoxDiv.after().html('<div class="form-group"><div class="col-sm-2"></div><div class="col-sm-3"><input type="text" autofocus="autofocus" id="textbox' + counter + '" name ="column_type_name[]" value = "" count="'+ counter +'" class = "form-control" placeholder = "Field Name" data-parsley-required-message = "Field Name is required" data-parsley-trigger = "change focusout" required count="'+ counter +'" ondblclick="this.setSelectionRange(0, this.value.length)" data-parsley-maxlength ="30" title="Index Field length must be less than 30 characters"></div><div class="col-sm-3"><select class="form-control" name="column_type[]" id="select' + counter + '" count="'+counter+'" count="'+ counter +'"><option value="Yes/No">Yes/No</option><option id="piclist" value="Piclist">Pick List</option><option value="Date">Date</option><option value="Number">Number</option><option value="Alphanumeric" selected="selected">Alphanumeric</option></select></div><div class="col-sm-1"><input type="checkbox" name="doc_mandatory' + counter + '" id="doc_mandatory' + counter + '" order="'+ counter +'"></div><div class="col-sm-1"><span id="dctCnt'+counter+'" style="cursor:pointer;padding-left: 10px;"" order="'+ counter +'"><i class="fa fa-trash fa-lg" style="font-size:20px; margin-top:7px;"></i></span><div class="grippy"></div></div><div class="col-sm-2"></div></div><div id="piclistdiv'+counter+'" style="display:none;" count="'+ counter +'"><div class="form-group"><div class="col-sm-2"></div><div class="col-sm-6"> <input type="text" class="form-control" id="readme'+counter+'" name="readme[]" placeholder="Insert option" count="'+ counter +'" ondblclick="this.setSelectionRange(0, this.value.length)" data-parsley-maxlength="30" title="Index Field length must be less than 30 characters" style="background-color: #eee;"></input></div><div class="col-sm-2"> <input id="add'+counter+'" class="btn btn-success" type="button" value="Insert Options" count="'+ counter +'" identity="picadd"></input></div><div class="col-sm-2"></div></div><div class="form-group"><div class="col-sm-2"></div><div class="col-sm-6"> <select id="List'+counter+'" name="List[]" class="form-control" count="'+ counter +'" style="background-color: #eee;"></select> <input type="hidden" name="hidd_options[]" value="" id="hidd_options'+counter+'" count="'+counter+'"></input> <input type="hidden" name="hidd_visibility[]" value="0" id="hidd_visibility'+counter+'" count="'+ counter +'"></div><div class="col-sm-2"> <input type="button" id="delete'+counter+'" value="Delete Options" class="btn btn-danger" count="'+ counter +'" identity="picdelete"></input></div><div class="col-sm-2"></div></div></div>');
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

        //Duplicate entry
        $("#name").change(function(){
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: 'post',
                url: '{{URL('documentTypeDuplication')}}',
                dataType: 'json',
                data: {_token: CSRF_TOKEN, name: $("#name").val() },
                timeout: 50000,
                beforeSend: function() {
                    $("#dp_wrn").show();
                    $("#save").attr("disabled", true);
                },
                success: function(data, status){

                    if(data != 1)
                    {
                        $("#dp").html(data);
                        $("#name").val('');
                    }
                    else
                    {
                        $("#dp").text('');
                        $("#dp-inner").text('');                       
                    }
                },
                error: function(jqXHR, textStatus, errorThrown){
                    console.log(jqXHR);    
                    console.log(textStatus);    
                    console.log(errorThrown);    
                },
                complete: function() {
                    $("#dp_wrn").hide();
                    $("#save").attr("disabled", false);
                }
            });
        });
    });

    function load()
    {
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            type: 'get',
            url: '{{URL('documentTypeList')}}',
            dataType: 'html',
            data: {_token: CSRF_TOKEN},
            timeout: 50000,
            beforeSend: function() {
                $(".preloader").show();
            },
            success: function(data, status){
                $("#shw").html(data);               
            },
            error: function(jqXHR, textStatus, errorThrown){
                console.log(jqXHR);    
                console.log(textStatus);    
                console.log(errorThrown);    
            },
            complete: function() {
                $(".preloader").hide();
            }
        });
    }

    // pre-submit callback 
    function showRequest(formData, jqForm, options) { 
        // formData is an array; here we use $.param to convert it to a string to display it 
        // but the form plugin does this for you automatically when it submits the data 
        var queryString = $.param(formData); 

        // jqForm is a jQuery object encapsulating the form element.  To access the 
        // DOM element for the form do this: 
        // var formElement = jqForm[0]; 

        /*swal('About to submit: \n\n' + queryString); */
        $(".preloader").show();

        // here we could return false to prevent the form from being submitted; 
        // returning anything other than false will allow the form submit to continue 
        return true; 
    } 

    // post-submit callback 
    function showResponse(responseText, statusText, xhr, $form)  {
        $('#cn').click();
        $("#documentTypeAddForm")[0].reset();
        
        // setTimeout(function () {
        //     $("#msg_add").slideDown(1000);
        // }, 200);
        // setTimeout(function () {
        //     $('#msg_add').slideUp("slow");
        // }, 5000);
        // load(); 
        var text = $("#msg_add").text();
        swal({title: "", text: text, type: 
        "success"}).then(function(){ 
           location.reload();
           }
        );

    }
    function showStatus()
    {
        $(".preloader").hide();
        $("#dTAddModal").hide();

    }

    function del(id,docGroup)
    {   
        $.ajax({
            type:'GET',
            url : base_url+'/hasChild',
            data :'id='+id,
            success:function(result){
                if(result){
                    //show warning message
                    $('#error-msg').css('display','block');
                    //setTimeout(function () {
                    $("#error-msg").slideDown(1000);
                    //}, 200);
                    // setTimeout(function () {
                    //     $('#error-msg').slideUp("slow");
                    // }, 4000);
                }else{

                    swal({
                        title: "{{trans('language.confirm_delete_single')}}'" + docGroup + "' ?",
                        text: "{{trans('language.Swal_not_revert')}}",
                        type: "{{trans('language.Swal_warning')}}",
                        showCancelButton: true
                      }).then(function (result) {
                          if(result){
                              // Success
                              $('#spl-wrn').html('');//hide unwanted message box
                              var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                              $.ajax({
                                  type: 'post',
                                  url: '{{URL('documentTypeDelete')}}',
                                  dataType: 'json',
                                  data: {_token: CSRF_TOKEN, id:id},
                                  timeout: 50000,
                                  beforeSend: function() {
                                      $(".preloader").show();
                                  },
                                  success: function(data, status){ 
                                      load();
                                      
                                          // $("#msg").html('<div class="alert alert-success alert-sty">'+ data +'</div>');
                                          // $("#msg").slideDown(1000);
                                          var text = data;
                                          swal({title: "Deleted", text: text, type: 
                                          "success"}).then(function(){ 
                                             location.reload();
                                             }
                                          );
                                      
                                  },
                                  error: function(jqXHR, textStatus, errorThrown){
                                      console.log(jqXHR);    
                                      console.log(textStatus);    
                                      console.log(errorThrown);    
                                  },
                                  complete: function() {
                                      $(".preloader").hide();
                                  }
                              });

                        }
                    });
                }
            }
        });
    }

    function duplication()
    {
        var val= $("#name_edi").val();
        var editVal= $("#edit_val").val();
        var oldVal= $("#oldVal").val();
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            type: 'post',
            url: '{{URL('documentTypeDuplication')}}',
            dataType: 'json',
            data: {_token: CSRF_TOKEN, name: val, editId:editVal, oldVal:oldVal },
            timeout: 50000,
            beforeSend: function() {
                $("#dp_wrn_edi").show();
                $("#saveEdi").attr("disabled", true);
            },
            success: function(data, status){

                if(data != 1)
                {
                    $("#dp_edi").html(data);
                    $("#name_edi").val('');
                }
                else
                {
                    $("#dp").text('');
                    $("#dp-inner").text('');                       
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                console.log(jqXHR);    
                console.log(textStatus);    
                console.log(errorThrown);    
            },
            complete: function() {
                $("#dp_wrn_edi").hide();
                $("#saveEdi").attr("disabled", false);
            }
        });
    }
    
</script> 
{!! Html::script('js/parsley.min.js') !!}  
{!! Html::script('js/jquery.form.js') !!}   
{!! Html::script('js/jquery-ui.min.js') !!}
{!! Html::script('js/drag.js') !!}   
@endsection