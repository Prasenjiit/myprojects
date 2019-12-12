<?php
  include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')
@section('main_content')
{!! Html::script('js/parsley.min.js') !!} 
{!! Html::script('js/jquery.form.js') !!}   
{!! Html::style('css/font-awesome.min.css') !!} 
{!! Html::style('css/ionicons.min.css') !!} 
{!! Html::style('plugins/datatables/dataTables.bootstrap.css') !!}
<div class="modal-header">
    <strong>
        {{trans('stack.stack')}}
    </strong>
    <small>- {{trans('language.edit')}} {{trans('stack.stack')}}: {{ $datas->stack_name }}</small>  
</div>
<div class="modal-body"> 
            <!-- form start -->
           {!! Form::open(array('url'=> array('stackSave',$datas->stack_id), 'method'=> 'post', 'class'=> 'form-horizontal stack_add_form', 'name'=> 'stackAddForm', 'id'=> 'stackAddForm','data-parsley-validate'=> '')) !!}             

    <div class="form-group">
        <label for="Stack" class="col-sm-2 control-label">Stack: <span class="compulsary">*</span></label>
        <div class="col-sm-6">
            {!! Form:: 
            text('name',$datas->stack_name, 
            array( 
            'class'=> 'form-control', 
            'id'=> 'name_edi', 
            'title'=> "'".trans('stack.stack')."'".trans('language.length_others'),
            'placeholder'=> 'Stack',
            'required'               => '',
            'data-parsley-required-message' => 'Stack name is required',
            'data-parsley-trigger'          => 'change focusout',
            'data-parsley-maxlength' => trans('language.max_length'),
            'autofocus'
            )
            ) 
            !!}      
            <div id="dp">
            <span id="dp_wrn_edi" style="display:none;">
                    <i class="fa fa-refresh fa-spin fa-1x fa-fw"></i>
                    <span class="">Please wait...</span>
                </span>
            </div>              
        </div>
    </div>
    <div class="form-group">
        {!! Form::label(trans('language.description').': ', '', array('class'=> 'col-sm-2 control-label'))!!}
        <div class="col-sm-6">
            {!! Form:: 
            textarea(
            'description', $datas->stack_description, 
            array( 
            'class'                  => 'form-control', 
            'id'                     => 'description', 
            'placeholder'            => 'Description',
            'title'                  => trans('language.length_description'),
            'data-parsley-maxlength' => trans('language.max_length_description')
            )
            ) 
            !!}
            <span class="dms_error">{{$errors->first('description')}}</span>
     
        </div>  
    </div>
 
    <div class="form-group">
        <label class="col-sm-2 control-label"></label>
        <input type="hidden" id="edit_val" name="edit_val" value="{{$datas->stack_id}}">
        <input type="hidden" id="oldVal" name="oldVal" value="{{$datas->stack_name}}">
        <div class="col-sm-6" style="text-align:right;">
            {!!Form::submit('Update', array('class' => 'btn btn-primary', 'id'=> 'saveEdi')) !!}&nbsp;&nbsp;
            <a href="{{URL::route('stacks')}}" class = "btn btn-primary btn-danger">Cancel</a>          
        </div>
    </div><!-- /.col -->
    {!! Form::close() !!}    
</div><!-- /.modal-dialog -->
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
    .slide-placeholder {
        background: #ecf0f5;
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
</style>
<script type="text/javascript">
$('#stackAddForm').on('change','select',function(){
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

//-------------check field type already exist-----------------//
$('#stackAddForm').on('focus',"select[id^='selectedit']",function(){
  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
  var count=$(this).attr('count');
  var optiontochange=$('#textboxedit'+count).val();
  var select_val=$('#selectedit'+count).val();
  var type_id=$('#edit_val').val();
  var type_column_id=$("#doctypecolumn"+count).val();
  if(type_column_id!=0){
  $.ajax({
    type:'post',
    url:'{{URL('stackoptionChange')}}',
    data:{_token:CSRF_TOKEN,id:type_id, col_id:type_column_id, option:optiontochange},
    success:function(data)
    {
      if(data==1){
          $('#selectedit'+count).val(select_val);
          $('#selectedit'+count).blur();
          window.focus();   
          swal('<?php echo trans('language.index_change');?>');     
          return false;
        }
        else{}
    }

  });
  }
});
//---------------check index field already exis--------------------//
$('#stackAddForm').on('click',"input[id^='textboxedit']",function(){
  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
  var count=$(this).attr('count');
  var optiontochange=$('#textboxedit'+count).val();
  var type_id=$('#edit_val').val();
  var type_column_id=$("#doctypecolumn"+count).val();
  if(type_column_id!=0){
  $.ajax({
    type:'post',
    url:'{{URL('stackoptionChange')}}',
    data:{_token:CSRF_TOKEN,id:type_id, col_id:type_column_id, option:optiontochange},
    success:function(data)
    {
      if(data==1){
          $('#textboxedit'+count).prop("readonly", true);
          swal('<?php echo trans('language.index_change');?>');
          return false;
        }
        else{}
    }

  });
  }
});


$('#stackAddForm').on('click','input[type="button"]',function(){
  var count=$(this).attr('count');
  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
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

  //delete options from select box

  if($(this).attr('identity')=='picdelete')
  {
    alert('test');
    if( $("#List"+count).has('option').length == 0 ) {
      swal("{{trans('language.select_box_is_empty')}}");
      return false;
    }
    else{
        var option_delete=$("#List"+count+" option:selected").val();
        var type_id=$('#edit_val').val();
        var type_column_id=$("#doctypecolumn"+count).val();
    swal({
          title: "{{trans('language.confirm_delete_single')}}'" + option_delete + "' ?",
          text: "{{trans('language.Swal_not_revert')}}",
          type: "{{trans('language.Swal_warning')}}",
          showCancelButton: true
        }).then(function (result) {
            if(result){
                // Success
                 $.ajax({
                  type:'post',
                  url:'{{URL('stackoptionDelete')}}',
                  data:{_token:CSRF_TOKEN, option:option_delete, id:type_id, col_id:type_column_id},
                  success:function(data){
                    if(data==1){
                      swal("{{trans('language.Swal_row_cant_be_deleted')}}");
                      return false;
                    }
                    else{
                      $("#List"+count+" option:selected").remove();
                      myList = [];
                      $('#List'+count+' option').each(function() {
                      myList.push($(this).val())
                      });
                      var arr=myList.toString();
                      document.getElementById("hidd_options"+count).value = arr;
                      swal("Option '"+ option_delete + "' deleted successfully");
                      return false;
                    }
                  }

                });
          }
      });

    }
}
});
$('#stackAddForm').on('mouseover',function(){
  
    $("input[id^='textboxedit']").each(function(i) {
        $(this).attr('id', "textboxedit" + (i + 1));
        $(this).attr('count', "" + (i + 1));
    });
    $("div[id^='TextBoxDiv']").each(function(i) {
        $(this).attr('id', "TextBoxDiv" + (i + 1));
        $(this).attr('count', "" + (i + 1));
    });
    $("select[id^='selectedit']").each(function(i) {
        $(this).attr('id', "selectedit" + (i + 1));
        $(this).attr('count', "" + (i + 1));
    });
    $("input[id^='doc_mandatory']").each(function(i) {
        $(this).attr('id', "doc_mandatory" + (i + 1));
        $(this).attr('name', "doc_mandatory" + (i + 1));
        $(this).attr('order', "" + (i + 1));
    });
    $("span[id^='dctCntedit']").each(function(i) {
        $(this).attr('id', "dctCntedit" + (i + 1));
        $(this).attr('order', "" + (i + 1));
    });
    $("input[id^='doctypecolumn']").each(function(i){
        $(this).attr('name',"doctypecolumn" + (i + 1));
        $(this).attr('id',"doctypecolumn" + (i + 1));
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
$('#stackAddForm').on('click','input[type="checkbox"]',function(){
  var count = $(this).attr('order');
    if($(this).prop("checked") == true){
    $(this).attr('checked',true);
    }
    else if($(this).prop("checked") == false){
    $(this).attr('checked',false);
    }
});
$('#stackAddForm').on('click','span',function(){
  var count = $(this).attr('order');
  var counter = $("#textboxcnt").val();
  var txtname = $("#textboxedit"+count).val();
  var previous_id=$("#doctypecolumn"+count).val();
  // if(counter==1){
  //      swal("{{$language['one_index_field']}}");
  //      return false;
  // }else{

        if(txtname){
          var answer = "{{trans('language.confirm_delete_single')}}"+txtname+"?";
      }else{
          var answer = "{{trans('language.confirm_delete_single')}}this column?";
      }

          swal({
              title: ''+answer+'',
              text: "{{trans('language.Swal_not_revert_custom')}}",
              type: "{{trans('language.Swal_warning')}}",
              showCancelButton: true
            }).then(function (result) {
                if(result){
                    // Success
                    if(previous_id!=0){
         
                      var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                        $.ajax({
                          type: 'post',
                          url: '{{URL('stackFieldDelete')}}',
                          //dataType: 'json',
                          data: {_token: CSRF_TOKEN, id:previous_id},
                          timeout: 50000,
                          beforeSend: function() {
                              $("#dp_wrn").show();
                              $("#save").attr("disabled", true);
                          },
                          success: function(data, status){
                            if(data==1)
                            {
                            swal("{{trans('language.Swal_row_cant_be_deleted')}}");
                            return false;
                            }
                            else
                            {
                              $("#TextBoxDiv" + count).remove();
                              counter--;
                              $("input[id^='textboxedit']").each(function(i) {
                                  $(this).attr('id', "textboxedit" + (i + 1));
                                  $(this).attr('count', "" + (i + 1));
                              });
                              $("div[id^='TextBoxDiv']").each(function(i) {
                                  $(this).attr('id', "TextBoxDiv" + (i + 1));
                                  $(this).attr('count', "" + (i + 1));
                              });
                              $("select[id^='selectedit']").each(function(i) {
                                  $(this).attr('id', "selectedit" + (i + 1));
                                  $(this).attr('count', "" + (i + 1));
                              });
                              $("input[id^='doc_mandatory']").each(function(i) {
                                  $(this).attr('id', "doc_mandatory" + (i + 1));
                                  $(this).attr('name', "doc_mandatory" + (i + 1));
                                  $(this).attr('order', "" + (i + 1));
                              });
                              $("span[id^='dctCntedit']").each(function(i) {
                                  $(this).attr('id', "dctCntedit" + (i + 1));
                                  $(this).attr('order', "" + (i + 1));
                              });
                              $("input[id^='doctypecolumn']").each(function(i){
                                  $(this).attr('name',"doctypecolumn" + (i + 1));
                                  $(this).attr('id',"doctypecolumn" + (i + 1));
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
                              $("#textboxcnt").val(counter);
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
                  else{

                      $("#TextBoxDiv" + count).remove();
                      //counter--;
                    $("input[id^='textboxedit']").each(function(i) {
                        $(this).attr('id', "textboxedit" + (i + 1));
                        $(this).attr('count', "" + (i + 1));
                    });
                    $("div[id^='TextBoxDiv']").each(function(i) {
                        $(this).attr('id', "TextBoxDiv" + (i + 1));
                        $(this).attr('count', "" + (i + 1));
                    });
                    $("select[id^='selectedit']").each(function(i) {
                        $(this).attr('id', "selectedit" + (i + 1));
                        $(this).attr('count', "" + (i + 1));
                    });
                    $("input[id^='doc_mandatory']").each(function(i) {
                        $(this).attr('id', "doc_mandatory" + (i + 1));
                        $(this).attr('name', "doc_mandatory" + (i + 1));
                        $(this).attr('order', "" + (i + 1));
                    });
                    $("span[id^='dctCntedit']").each(function(i) {
                        $(this).attr('id', "dctCntedit" + (i + 1));
                        $(this).attr('order', "" + (i + 1));
                    });
                    $("input[id^='doctypecolumn']").each(function(i){
                        $(this).attr('name',"doctypecolumn" + (i + 1));
                        $(this).attr('id',"doctypecolumn" + (i + 1));
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
                    //$("#textboxcnt").val(counter); 
                }
              }
          });

  //}
});
//<---------------------Get previous value on drag------------------------->
/*$('#stackAddForm').on('keyup click blur','input',function(){
  var count = $(this).attr('count');
  if(count){
    var x = $(this).val();
    $('#textboxedit'+count).attr('value',x);
  }  
});*/
//<-------------------------------------------------------------------------->
$('#stackAddForm').on('change','select',function(){
  var count=$(this).attr('count');
  if(count){
  var text=$( "#selectedit"+count+" option:selected" ).text();
  if(text=="Date"){
    $("#selectedit"+count+" option[value='Alphanumeric']").removeAttr("selected");
    $("#selectedit"+count+" option[value='Number']").removeAttr("selected");
    $("#selectedit"+count+" option[value='Date']").attr("selected","selected");
    $("#selectedit"+count+" option[value='Yes/No'").removeAttr("selected");
    $("#selectedit"+count+" option[value='Piclist']").removeAttr("selected");
    $("#hidd_visibility"+count).val(0);
    document.getElementById("List"+count).required = false;
  }
  if(text=="Alphanumeric"){
    $("#selectedit"+count+" option[value='Alphanumeric']").attr("selected","selected");
    $("#selectedit"+count+" option[value='Number']").removeAttr("selected");
    $("#selectedit"+count+" option[value='Date']").removeAttr("selected");
    $("#selectedit"+count+" option[value='Yes/No'").removeAttr("selected");
    $("#selectedit"+count+" option[value='Piclist']").removeAttr("selected");
    $("#hidd_visibility"+count).val(0);
    document.getElementById("List"+count).required = false;
  }
  if(text=="Number"){
    $("#selectedit"+count+" option[value='Alphanumeric']").removeAttr("selected");
    $("#selectedit"+count+" option[value='Number']").attr("selected","selected");
    $("#selectedit"+count+" option[value='Date']").removeAttr("selected");
    $("#selectedit"+count+" option[value='Yes/No'").removeAttr("selected");
    $("#selectedit"+count+" option[value='Piclist']").removeAttr("selected");
    $("#hidd_visibility"+count).val(0);
    document.getElementById("List"+count).required = false;
  }
  if(text=="Yes/No"){
    $("#selectedit"+count+" option[value='Alphanumeric']").removeAttr("selected");
    $("#selectedit"+count+" option[value='Number']").removeAttr("selected");
    $("#selectedit"+count+" option[value='Date']").removeAttr("selected");
    $("#selectedit"+count+" option[value='Yes/No'").attr("selected","selected");
    $("#selectedit"+count+" option[value='Piclist']").removeAttr("selected");
    $("#hidd_visibility"+count).val(0);
    document.getElementById("List"+count).required = false;
  }
  if(text=="Pick List"){
    $("#selectedit"+count+" option[value='Alphanumeric']").removeAttr("selected");
    $("#selectedit"+count+" option[value='Number']").removeAttr("selected");
    $("#selectedit"+count+" option[value='Date']").removeAttr("selected");
    $("#selectedit"+count+" option[value='Yes/No'").removeAttr("selected");
    $("#selectedit"+count+" option[value='Piclist']").attr("selected","selected");
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
        $("#cnEdi").click(function() {
            window.location.reload();
        });
        // $("#addButtonEdit").click(function () {
        //     var counter = $("#textboxcnt").val();
        //     var newinsrtcnt = $("#newInsrtVal").val();
        //     newinsrtcnt++;
        //     counter++;
        //     var newTextBoxDivEdit = $(document.createElement('div')).attr("id", 'TextBoxDivEdit' + counter).attr("class", 'column').attr("draggable",true);
        //     newTextBoxDivEdit.after().html('<div class="form-group"><div class="col-sm-2"></div><div class="col-sm-3"><input type="hidden" name="doctypecolumn'+counter+'" id="doctypecolumn'+counter+'" value="0"><input type="text" id="textboxedit' + counter + '" name ="column_type_name_edit[]" value = "" count="'+ counter +'" class = "form-control" placeholder = "Field Name" data-parsley-required-message = "Field Name is required" data-parsley-trigger = "change focusout" required ondblclick="this.setSelectionRange(0, this.value.length)" data-parsley-maxlength ="30"></div><div class="col-sm-3"><select class="form-control" name="column_type_edit[]" id="selectedit' + counter + '" count="'+ counter +'"><option value="Yes/No">Yes/No</option><option id="piclist" value="Piclist">Pick List</option><option value="Date">Date</option><option value="Number">Number</option><option value="Alphanumeric" selected="selected">Alphanumeric</option></select></div><div class="col-sm-1"><input type="checkbox" name="doc_mandatory'+counter+'" id="doc_mandatory'+counter+'" ><br></div><div class="col-sm-1"><span id="dctCntedit'+counter+'" style="cursor:pointer;"><i class="fa fa-trash fa-lg" style="font-size:20px; margin-top:7px;"></i></span><div class="grippy"></div></div><div class="col-sm-2"></div></div><div id="piclistdiv'+counter+'" style="display:none;" count="'+ counter +'"><div class="form-group"><div class="col-sm-2"></div><div class="col-sm-6"> <input type="text" class="form-control" id="readme'+counter+'" name="readme[]" placeholder="Insert option" count="'+ counter +'" ondblclick="this.setSelectionRange(0, this.value.length)" data-parsley-maxlength="30" ></input></div><div class="col-sm-2"> <input id="add'+counter+'" class="btn btn-success" type="button" value="Insert Options" count="'+ counter +'" identity="picadd"></input></div><div class="col-sm-2"></div></div><div class="form-group"><div class="col-sm-2"></div><div class="col-sm-6"> <select id="List'+counter+'" name="List[]" class="form-control" count="'+ counter +'"></select> <input type="hidden" name="hidd_options[]" value="0" id="hidd_options'+counter+'" count="'+counter+'"></input> <input type="hidden" name="hidd_visibility[]" value="0" id="hidd_visibility'+counter+'" count="'+ counter +'"></div><div class="col-sm-2"> <input type="button" id="delete'+counter+'" value="Delete Options" class="btn btn-danger" count="'+ counter +'" identity="picdelete"></input></div><div class="col-sm-2"></div></div></div>');
        //     newTextBoxDivEdit.appendTo("#columns");
        //     $("#textboxcnt").val(counter); 
        //     $("#newInsrtVal").val(newinsrtcnt);
        //     var cols = document.querySelectorAll('#columns .column');
        //     [].forEach.call(cols, function(col) {
        //       col.addEventListener('dragstart', handleDragStart, false);
        //       col.addEventListener('dragenter', handleDragEnter, false);
        //       col.addEventListener('dragover', handleDragOver, false);
        //       col.addEventListener('dragleave', handleDragLeave, false);
        //       col.addEventListener('drop', handleDrop, false);
        //       col.addEventListener('dragend', handleDragEnd, false);
        //     });  
        // });
    });

    $(function ($) {
    //Duplicate entry
        $("#name_edi").change(function(){
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: 'post',
                url: '{{URL('stackDuplication')}}',
                dataType: 'json',
                data: {_token: CSRF_TOKEN, name: $("#name_edi").val(),editId: $('#edit_val').val(),oldVal: $('#oldVal').val() },
                timeout: 50000,
                beforeSend: function() {
                    $("#dp_wrn").show();
                    $("#save").attr("disabled", true);
                },
                success: function(data, status){
                    if(data != 1) {
                        $("#dp").html(data);
                        $("#saveEdi").prop( "disabled", true );
                    }else{
                        $("#dp").text('');
                        $("#dp-inner").text(''); 
                        $("#saveEdi").prop( "disabled", false );                      
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
    // select all desired input fields and attach tooltips to them
      $("#stackAddForm :input").tooltip({
 
      // place tooltip on the right edge
      position: "center",
 
      // a little tweaking of the position
      offset: [-2, 10],
 
      // use the built-in fadeIn/fadeOut effect
      effect: "fade",
 
      // custom opacity setting
      opacity: 0.7
 
      });
</script>
{!! Html::script('js/jquery-ui.min.js') !!}
{!! Html::script('js/drag.js') !!}    
@endsection