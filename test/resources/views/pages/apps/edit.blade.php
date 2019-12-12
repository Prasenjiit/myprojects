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
{!! Html::style('plugins/EasyAutocomplete-1.3.5/easy-autocomplete.min.css') !!} 
<style type="text/css">
    [draggable] {
      -khtml-user-drag: element;
      -webkit-user-drag: element;
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

    .form_row,.form_row_choice{ margin-top: 3px; margin-bottom: 3px; }
    .test_button{background-color: #f4f4f4; color: #444; border-color: #ddd;     text-align: left !important;}
    .nopadding_class{ padding: 0 !important; margin: 0 !important; }
    .form_class{ margin: 1px !important;}
    .stick {
        /*position:fixed;
        top:0px;*/
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
    div.grippysmall {
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
    div.grippysmall::after {
        content: '.. .. ..';
    }
    .activity_row{display: none;}
    .fstElement{
        width: 100%;
    }
    .fstControls{
        width: 100% !important;
    }
    .chart{
        height: 257px;
    }
    .fstResults
    {
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
    .help{
      font-size:12px; color:#999;
    }
    .help_text{
  font-size:12px; color:#999;
}    

.no_padding{
margin-bottom: 2px;
}
</style>
<!-- Satrt Section -->
<section class="content-header">
  <div class="row">
    <div class="col-sm-8">
      <span style="float:left;">
        <strong>@if(!$app_id) {{ trans('apps.add_new_app') }} @else {{ Lang::get('apps.edit_app') }} - <small class="app_name"></small>@endif</strong> &nbsp;
      </span>
      <span style="float:left;">
       
      </span>
    </div>
    <div class="col-sm-4">
      <ol class="breadcrumb">
          <li><a href="{{url('/home')}}"><i class="fa fa-dashboard"></i> {{ Lang::get('language.home') }}</a></li>
          <li class="active">{{ Lang::get('apps.apps') }}</li>
      </ol>
    </div>
  </div>
  </section>
<section class="content">
 {!! Form::open(array('url'=> array('appsSave','0'), 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'appsSaveForm', 'id'=> 'appsSaveForm','data-parsley-validate'=> '')) !!}        
<div class="row">
  <div class="col-sm-12 alert_space"></div>
    <!-- left column -->
    <div class="col-md-3">
        <!-- general form elements -->
        <div class="box box-info sticky-scroll-box">
            <div class="box-header with-border"><h3 class="box-title">{{ Lang::get('apps.apps_components') }} </h3> </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form">
                <div class="box-body">
                    <input type="hidden" name="form_id" id="form_id" value="{{ @$form_id }}">
                    <input type="hidden" name="form_url" id="form_url" value="{{ url('save_dynamic_form')}}">
                    <input type="hidden" name="return_form_url" id="return_form_url" value="{{ url('forms')}}">
                    <input type="hidden" name="loadformurl" id="loadformurl" value="{{ url('load_form')}}">
                    <ul id="add-field">
                        @foreach($apps_types as $row_type)
                            <li>
                                <button type="button" class="btn btn-block test_button btn-xs add-field form_class" id="add-{{$row_type->form_input_type_value}}" data-type="{{$row_type->form_input_type_value}}" data-typename="{{$row_type->form_input_type_name}}" data-is_required="{{$row_type->is_required}}" data-is_options="{{$row_type->is_options}}" data-type_value="{{$row_type->form_input_type_value}}"><i class="{{$row_type->form_input_icon}}"></i> {{$row_type->form_input_type_name}}</button>
                            </li>
                        @endforeach     
                    </ul>
                </div>
                <!-- /.box-body -->
                <div class="box-footer"></div>
            </form>
        </div>
        <!-- /.box -->
    </div>
    <!--/.col (left) -->

    <!-- right column -->
    <div class="col-md-9">
      <!-- general form elements -->
    <div class="box box-info">
    <div class="box-header with-border">
      <h3 class="box-title">{{ Lang::get('apps.apps') }} </h3>
      <p style="font-size:12px; color:#999;">{{trans('forms.form_help1')}}</p>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
        
    
<div class="box-body">
    <!-- Form Element Start -->

           
<input type="hidden" name="app_id" id="app_id" value="{{ $app_id }}">   
            <input type="hidden" name="action" id="action" value="edit">        
    <div class="form-group">
                <label for="Doc Type" class="col-sm-2 control-label">{{ Lang::get('apps.app_name') }}: <span class="compulsary">*</span></label>
                <div class="col-sm-8">
                    <input class="form-control" id="name" placeholder="{{ Lang::get('apps.app_name') }}" data-parsley-required="true" data-parsley-required-message="{{ Lang::get('apps.app_name_required') }}" autofocus="autofocus" name="name"  type="search" value="" data-parsley-maxlength ="{{ Lang::get('apps.app_name_max_length') }}" title="">    
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
                    'placeholder'            => Lang::get('apps.description'),
                    'title'                  => Lang::get('apps.description_max_length_title'),
                    'data-parsley-maxlength'   => Lang::get('apps.description_max_length')
                    )
                    ) 
                    !!}
                    <span class="dms_error">{{$errors->first('description')}}</span>
                </div>
            </div>
     

    <div class="form-group">
                <div class="col-sm-12"><h4 class="box-title">{{Lang::get('apps.app_indexes')}}</h4></div>
            </div>            

    <div class="form-group">
                <div class="col-sm-12">
                    <table class="table" id="myTable">
                <thead>    
                <tr>
                  <th width="3%" nowrap>#</th>
                  <th>{!! Form::label(Lang::get('apps.field_type'), '', array('class'=> 'control-label'))!!}</th>
                  <th width="25%" nowrap>{!! Form::label(Lang::get('apps.index_field'), '', array('class'=> 'control-label'))!!}</th>
                  <th width="25%" nowrap>{!! Form::label(Lang::get('apps.default_value').'/'.Lang::get('apps.choices'), '', array('class'=> 'control-label'))!!}</th>
                  <th nowrap>{!! Form::label(Lang::get('apps.action'), '', array('class'=> 'control-label'))!!}</th>
                </tr>
                </thead>
                
              </table>
                </div>
            </div>        
           
    <div class="form-group">
            <label class="col-sm-4 control-label"></label>
            {!!Form::hidden('count-textbox','',array('id'=>'counttextbox'))!!}
            <div class="col-sm-8">
            <!-- <button type="button" onclick="myFunction()">Try it</button> -->
                
               
                
                <input class="btn btn-primary app_save" id="app_save" type="submit" value="{{Lang::get('language.save')}}" > &nbsp;&nbsp;
                <input class="btn btn-primary app_save" id="app_save_close" type="submit" value="{{Lang::get('language.save_close')}}" > &nbsp;&nbsp;
                <a href="{{url('apps')}}" class="btn btn-danger">{{Lang::get('language.cancel')}}</a>
                    &nbsp;&nbsp;
                 {!!Form::button(Lang::get('apps.add_new_indexfield'),array('class'=>'btn btn-primary button-add','id'=>'addButton','value'=>'addButton'))!!}

            </div>
        </div><!-- /.col -->
    

    <!--- Form Element End -->
    </div>
        <!-- /.box body -->
    </div>
    <!-- /.box -->

    <!-- Input addon -->
    </div>
    <!--/.col (right) -->
    </div>
    <!-- /.row -->

       
{!! Form::close() !!}    
    </section>
    <!-- END Section -->

   <div class="modal fade" id="link_to_app_modal" data-backdrop="false" data-keyboard="false">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header" style="border-bottom-color: deepskyblue;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">{!! trans('apps.link_to_other_apps') !!}</h4>
              </div>          
              <div class="modal-body" id="more_data">
                        <div class="row">
 <div class="col-xs-12 col-sm-12 col-md-6">
                          <div class="form-group">
                          <label class="no_padding" for="" class="">{{ Lang::get('apps.select_app_label') }}:<span class="compulsary">*</span></label>
                          <select class="form-control document_type" name="link_column_type" id="link_column_type" data-parsley-required="true" data-parsley-required-message="{{ Lang::get('apps.this_field_required') }}" data-parsley-group="link">
                            
                          </select>
                          </div>
                          </div>


                          <div class="col-xs-12 col-sm-12 col-md-6">
                          <div class="form-group">
                          <label class="no_padding" for="" class="">{!! Lang::get('apps.select_app_index_label') !!}:<span class="compulsary">*</span></label>
                          <select class="form-control document_type_option" name="link_column_type_index" id="link_column_type_index" data-parsley-required="true" data-parsley-required-message="{{ Lang::get('apps.this_field_required') }}" data-parsley-group="link">
                            
                          </select>
                          </div>
                          </div>
                        </div>
              </div>
              <div class="modal-footer">
                <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                   <button class="btn btn-primary save_link" id="" data-ctr="" type="button" >{{Lang::get('language.confirm')}}</button>
                    <button class="btn btn-danger" data-dismiss="modal" type="button" >{{Lang::get('language.close')}}</button>
              </div>     
                </div>
                </div>
               </div> 
               
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
 {!! Html::script('js/jquery-ui.min.js') !!}
{!! Html::script('plugins/EasyAutocomplete-1.3.5/jquery.easy-autocomplete.min.js') !!} 
<script type="text/javascript">

    $(document).ready(function(){

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

      var col_ctr = 0; /* Counter for*/
      var choice_ctr = 0; /* Counter for*/
      var link_ctr=0;
      var autocomplete_options = { url: "{{ URL('document_column_suggession') }}",getValue: "name" };

      var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
      var index_options = '';
      var selected_option=0;
      var doc_type_options = '<option value="">{{trans("apps.select_app")}}</option>';
      var doc_type_index_options = '<option value="">Select the Index Field</option>';
      /*load apps*/
      
    var fixHelper = function(e, ui) {  
  ui.children().children().each(function() { 
    $(this).width($(this).width()); 
    console.log(  $(this).width($(this).width()) ); 
  });  
  return ui;  
};
    $('.connectedSortable .widget_item').css('cursor', 'move');


/* Add New App Start*/    
var select_type ='';
@php  foreach ($apps_types as $key => $value) : @endphp
select_type +='<option value="{{ $value->form_input_type_value }}" data-is_options="{{ $value->is_options }}">{{ $value->form_input_type_name }}</option>';  
@php endforeach @endphp

$("#myTable").sortable({
  helper: fixHelper,
  items: "tbody"
});
      /*load apps*/
      var load_app_form = function() 
      {
      var url = "@php echo url('load_app_form?app_id='.$app_id.'&action=edit'); @endphp";
      var html = '';
      $.ajax({
            type: "GET",
            url: url,
            dataType:'JSON',
            success: function(response)
            {
                
                if(response.status == 1)
                {
                  
                  $("#name").val(response.app_name); 
                  $(".app_name").html(response.app_name); 
                  
                  $("#description").val(response.description); 
                  $.each(response.document_type_column, function(i, item) {

                    if(item.document_type_column_name !='')
                    {
                    col_ctr++;
                    var data_array = {'col_ctr':col_ctr,'column_type':item.document_type_column_type,'column_id':item.document_type_column_id,'column_name':item.document_type_column_name,'is_required':item.document_type_column_mandatory,'link_app_id':item.document_type_link,'link_app_index':item.document_type_link_column};
                    add_column(data_array);
                    
                    if(col_ctr == 1)
                    {
                        selected_option = col_ctr;
                    }
                    index_options +='<option value="'+col_ctr+'">'+item.document_type_column_name+'</option>';
                    if((item.document_type_options != null) && (item.document_type_options.length > 0))
                    {
                    var choices = item.document_type_options.split(',');
                    var default_value = item.document_type_default_value.split(',');
                    var parent = col_ctr;
                    $.each(choices, function(i1, item1) {

                      choice_ctr++;
                      var selected_item = ($.inArray(item1,default_value) != -1) ? 1:0;
                      var data_array = {'choice_ctr':choice_ctr,'parent':parent,'choice_name':item1,'selected_item':selected_item};
                      var choice = add_choice(data_array);
                      $('.group_tr_'+parent+':last').after(choice);
                      });
                   }


                 }
                  });

                   $.each(response.other_app_types, function(i, item) {
                      doc_type_options +='<option value="'+item.id+'">'+item.name+'</option>';
                      $.each(item.childrens, function(i1, item1) {
                       doc_type_index_options +='<option value="'+item1.id+'"" class="doc_type_options doc_type_'+item.id+'" style="display:none;">'+item1.name+'</option>';   
                      });
                   });
                }
                else
                {
                }
                $("#app_section").html(html); 
            }
        });
    }
    //call function for load apps
    load_app_form();

var add_column = function(data_array) 
{   console.log(data_array);
    var ctr = data_array.col_ctr;
    var column_type = (typeof data_array.column_type != 'undefined') ? data_array.column_type:'text';
    var is_option = (typeof data_array.is_option != 'undefined') ? data_array.is_option:0;
    var column_id = (typeof data_array.column_id != 'undefined') ? data_array.column_id:0
    var column_name = (typeof data_array.column_name != 'undefined') ? data_array.column_name:'';
    var column_value = (typeof data_array.column_value != 'undefined') ? data_array.column_value:'';
    var is_required = (typeof data_array.is_required != 'undefined') ? data_array.is_required:0;
    var link_app_id = (typeof data_array.link_app_id != 'undefined' && (data_array.link_app_id != 0)) ? data_array.link_app_id:'';
    var link_app_index = (typeof data_array.link_app_index != 'undefined' && (data_array.link_app_id != 0)) ? data_array.link_app_index:'';

    var required_check = (is_required == 1) ? 'checked="checked"':'';
    var checked_link_app = (link_app_id) ? 'checked="checked"':'';
    var app_link_check = '';
    var includeRequiredHTML = '';
    var includeChoicesHTML = '';
    var txtName = "txtName" + ctr;
    var txtAge = "txtAge" + ctr;
    var txtGender = "txtGender" + ctr;
    var txtOccupation = "txtOccupation" + ctr;
    var common_attr = 'data-ctr="'+ctr+'" data-parent="'+ctr+'"';
    includeRequiredHTML = '' +
            '<label>' +
            '<input class="toggle-required" type="checkbox" name="checkbox_required_'+ctr+'" id="checkbox_required_'+ctr+'" value="1" '+required_check+'> {{ Lang::get("apps.required") }}' +
            '</label>';
    var linkedAppHTML = '&nbsp;&nbsp;' +
            '<label>' +
            '<input class="toggle-required link_to_app" type="checkbox" name="checkbox_app_linked_'+ctr+'" id="checkbox_app_linked_'+ctr+'" value="1" '+app_link_check+' data-click_from="checkbox" ' + common_attr + ' '+checked_link_app+'><a href="javascript:(0);" class="link_to_app" data-click_from="link" ' + common_attr + '>Select from App</a>' +
            '</label>';
                   
    linkedAppHTML += '<input type="hidden" name="link_app_id_'+ctr+'" id="link_app_id'+ctr+'" value="'+link_app_id+'"  />';
    linkedAppHTML += '<input type="hidden" name="link_app_index_'+ctr+'" id="link_app_index'+ctr+'" value="'+link_app_index+'"  />';
                    
    var newTr = '<tbody style="border:none;"><tr class="tr_'+ctr+' group_tr_'+ctr+'">';

    if(column_type == 'checkbox' || column_type == 'radio' || column_type == 'select')
    {
      includeChoicesHTML += '<button class="btn btn-primary add_choice" id="" type="button" value="" ' + common_attr + '><i class="fa fa-plus"></i> {{Lang::get("apps.add_choice")}}</button>'; 
    }
    else
    { 
      includeChoicesHTML += '<input type="text" name="column_value_'+ctr+'" id="column_value_'+ctr+'" class="form-control" value="" placeholder="{{ Lang::get("apps.default_value") }}"/ ' + common_attr + '>';
    }

    newTr += '<td>';
    newTr +='<span title="Drag & Drop" class="widget_item">';
    newTr +='<div class="grippy"></div>';
    newTr +='</span>';  
    newTr += '</td>';

    newTr += '<td><select class="form-control column_type" name="column_type_'+ctr+'" id="column_type_'+ctr+'" ' + common_attr + '>' + select_type + '</select></td>';
    newTr += '<td>';
    newTr += '<input type="text" name="column_name_'+ctr+'" id="column_name_'+ctr+'" class="form-control column_names" value="'+column_name+'" placeholder="{{ Lang::get("apps.index_field") }}" ' + common_attr + '/>';
    newTr += '<input type="hidden" name="column_id['+ctr+']" id="column_id'+ctr+'" value="'+column_id+'"  />';
    newTr += '</td>';
    newTr += '<td id="choice_td_'+ctr+'">';
    newTr +=includeChoicesHTML;
    newTr += '</td>';
    newTr += '<td>';
    newTr +='<span>';
    newTr +=includeRequiredHTML;
    newTr +=linkedAppHTML;
    newTr +='</span>'; 
    newTr +='<span>';
    newTr +='&nbsp;&nbsp;<i class="fa fa-fw fa-trash deleteCol" title="Remove Field" ' + common_attr + '></i> ';
    newTr +='</span> '; 
    newTr += '</td>';

    newTr += '</tr></tbody>';
    $('#myTable').append(newTr);

    
    $("#column_type_"+ctr).val(column_type);

    $("#column_name_"+ctr).easyAutocomplete({
        url: function(query) {
            return "{{ URL('document_column_suggession') }}?search=" + query;
          },
        getValue: "name"
    });
};
$(document).on('click', '.button-add', function () {
    col_ctr++;
    var data_array = {'col_ctr':col_ctr};
    add_column(data_array);
    
});
$(document).on('click', '.add-field', function () {
        event.preventDefault();
        col_ctr++;
    var type_value  = $(this).attr('data-type_value');   
    var data_array = {'col_ctr':col_ctr,'column_type':type_value};
    add_column(data_array);
        //$('#form-fields').sortable();
    });

/* Add Choice Start */

var add_choice = function(data_array) 
{
    var ctr = data_array.choice_ctr;
    var parent = data_array.parent;
    var column_type = (typeof data_array.column_type != 'undefined') ? data_array.column_type:'text';
    var is_option = (typeof data_array.is_option != 'undefined') ? data_array.is_option:0;
    var column_id = (typeof data_array.column_id != 'undefined') ? data_array.column_id:0
    var choice_name = (typeof data_array.choice_name != 'undefined') ? data_array.choice_name:'';
    var column_value = (typeof data_array.column_value != 'undefined') ? data_array.column_value:'';
    var selected_item = (typeof data_array.selected_item != 'undefined') ? data_array.selected_item:'';
    var selected_item_check = (selected_item == 1) ? 'checked="checked"':'';
    var selectedHTML = '';
    var includeChoicesHTML = '';
    var txtName = "txtName" + ctr;
    var txtAge = "txtAge" + ctr;
    var txtGender = "txtGender" + ctr;
    var txtOccupation = "txtOccupation" + ctr;
    var common_attr = 'data-ctr="'+ctr+'" data-parent="'+parent+'"';
    selectedHTML = '' +
            '<label>' +
            '<input class="toggle-required" type="checkbox" name="choice_selected_'+parent+'['+ctr+']" id="choice_selected_'+ctr+'" value="'+ctr+'" '+selected_item_check+'> {{ Lang::get("apps.selected") }}' +
            '</label>';
    var new_choice_Tr = '<tr class="tr_'+ctr+' group_tr_'+parent+'" >';
    new_choice_Tr += '<td></td>';
    new_choice_Tr += '<td>';
    /*new_choice_Tr +='<span title="Drag & Drop" class="pull-right" >';
    new_choice_Tr +='<div class="grippy"></div>';
    new_choice_Tr +='</span>';  */
    new_choice_Tr += '</td>';
    new_choice_Tr += '<td>';
    new_choice_Tr += '<input type="text" name="choice_name_'+parent+'['+ctr+']" id="choice_name_'+ctr+'" class="form-control" value="'+choice_name+'" placeholder="{{ Lang::get("apps.choice") }}" />';
    new_choice_Tr += '<input type="hidden" name="choice_'+parent+'['+ctr+']" id="choice_'+ctr+'" value="'+ctr+'"  />';
    new_choice_Tr += '</td>';
    new_choice_Tr += '<td id="choice_td_'+ctr+'">';
    new_choice_Tr +=selectedHTML;
    new_choice_Tr +='&nbsp;&nbsp;<i class="fa fa-fw fa-trash deleteChoice" title="Remove Field" ' + common_attr + '></i>';
    new_choice_Tr += '</td>';
    new_choice_Tr += '<td>';
    new_choice_Tr += '</td>';

    new_choice_Tr += '</tr>';
    return new_choice_Tr;
};


$(document).on('click', '.add_choice', function () {
    
    choice_ctr++;
    var parent = $(this).attr('data-ctr');
    console.log("parent = "+parent);
    var data_array = {'choice_ctr':choice_ctr,'parent':parent};
    var choice = add_choice(data_array);
    $('.group_tr_'+parent+':last').after(choice);
    
});

/* Add Choice End

/* column type change START*/
$(document).on("change",".column_type",function(e)
   {
    
    var column_type=  $(this).val();
    var ctr = $(this).attr('data-ctr');
    console.log("column_type"+column_type);
    var includeChoicesHTML = '';
    var common_attr = 'data-ctr="'+ctr+'"';
    if(column_type == 'checkbox' || column_type == 'radio' || column_type == 'select')
    {
      includeChoicesHTML += '<button class="btn btn-primary add_choice" id="" type="button" value="" ' + common_attr + '><i class="fa fa-plus"></i> {{Lang::get("apps.add_choice")}}</button>'; 
    }
    else
    { 
      includeChoicesHTML += '<input type="text" name="column_value_'+ctr+'" id="column_value_'+ctr+'" class="form-control" value="" placeholder="{{ Lang::get("apps.default_value") }}"/ ' + common_attr + '>';
    }
    $("#choice_td_"+ctr).html(includeChoicesHTML);

   });
/* Column type change END*/

   

/* Delete Column */
$(document).on("click",".deleteCol",function(e) {
       
       var parent =$(this).attr('data-parent'); 

       swal({
        title:"{{trans('language.confirm_delete')}}",
        showCancelButton: true
        }).then((result) => {
        if(result){
         
          $(".group_tr_"+parent).remove();
           /*refresh_options();*/
        }
        else
        {
          //stay in same stage
        }
     });
     });
/* END Delete Column */

/* Delete Choices */
$(document).on("click",".deleteChoice",function(e) {
       
       var choice =$(this).attr('data-ctr'); 

       swal({
        title:"{{trans('language.confirm_delete')}}",
        showCancelButton: true
        }).then((result) => {
        if(result){
         
          $(".tr_"+choice).remove();
         
        }
        else
        {
          //stay in same stage
        }
     });
     });
/* END Delete Choices */


    var refresh_options = function()
   {
        var test_option = '';
        $(".column_names").each(function( index )
        {
           
           var stage_label = $(this).val();
           var c_ctr =$(this).attr('data-ctr');
            if(stage_label !='')
            {
              test_option += '<option value="' + c_ctr + '">' + stage_label + '</option>'; 
            }
        });
        index_options = test_option;
        console.log("index_options"+index_options);

        $(".link_column_types").each(function( index )
        {
            var l_ctr =$(this).attr('data-l_ctr');
            var to_satge = $(this).val(); 
            $('#link_column_type_'+l_ctr).html(index_options);
            $("#link_column_type_"+l_ctr).val(to_satge); 
            console.log("to_satge"+to_satge);
        });

        console.log("refresh_options");
   }

    $(document).on("change paste",".column_names",function(e) {
        /*refresh_options();*/
     });


    /* Link To Other App*/ 
    $(document).on('click', '.link_to_app', function () 
    {
        var click_from = $(this).attr('data-click_from');
        var l_ctr = $(this).attr('data-ctr');
        console.log("click_from"+click_from)
        var link_app_id = $("#link_app_id"+l_ctr).val();
        var link_app_index=    $("#link_app_index"+l_ctr).val();
        if(click_from == 'link')
        {
            $('#checkbox_app_linked_'+l_ctr).prop('checked', true);
        }   
        console.log("checked"+$('#checkbox_app_linked_'+l_ctr).is(':checked'))
         if($('#checkbox_app_linked_'+l_ctr).is(':checked') || click_from == 'link') 
         { 
            $('.app_name').html($('#name').val());
            $('.app_index').html($('#column_name_'+l_ctr).val());
            $('#link_column_type').html(doc_type_options);
            $('#link_column_type_index').html(doc_type_index_options);
            if(link_app_id)
            {
                $("#link_column_type").val(link_app_id).trigger("change");
            }
            
            $('#link_to_app_modal').modal().show();
            if(link_app_index)
            {
                $("#link_column_type_index").val(link_app_index).trigger("change");
            }

            
            $(".save_link").attr('data-ctr',l_ctr);

         }
    });

    $(document).on("change",".document_type",function(e) {
        var link_appid =$(this).val();
        console.log("link_appid"+link_appid);
        $("#link_column_type_index").val('');
        $(".doc_type_options").hide();
        $(".doc_type_"+link_appid).show();
     });


    $(document).on("click",".save_link",function(e) {
            var ctrc =$(this).attr('data-ctr');

            $('#appsSaveForm').parsley().validate("link");
            /*if ($('#link_column_type').parsley().isValid() && $('#link_column_type_index').parsley().isValid())*/
            console.log($("#link_column_type").parsley().isValid());
          /*  if($("#link_column_type").parsley().isValid() == false)
            {
                alert("Fire more code for Name!");
            }
            if ($('#appsSaveForm').parsley().isValid())  
            {
                console.log('valid');
            } 
            else 
            {
                
                console.log('not valid');
            }*/
            $("#link_app_id"+ctrc).val($('#link_column_type').val());
            $("#link_app_index"+ctrc).val($('#link_column_type_index').val());
            $('#link_to_app_modal').modal('hide');

        });

/* Link To Other App End*/ 

/* Save app start*/ 
$(document).on('click', '.app_save', function (e) {
   /* $("#appsSaveForm").submit(function(e) 
  {*/
    e.preventDefault(); /*avoid to execute the actual submit of the form.*/
    var button_id = $(this).attr('id');
    console.log("button_id"+button_id);
    $('.alert_space').html('');
    if($("#appsSaveForm").parsley().validate())
     { 
       var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
       var url = "@php echo url('save_apps'); @endphp";
       var postdata = {_token:CSRF_TOKEN}
       
       /*$('.pre_loader').show();*/
       $.ajax({
              type: "POST",
              url: url,
              dataType:'json',
              data: $('#appsSaveForm').serialize(), /*serializes the form's elements.*/
              success: function(response)
              {
                  if(response.status == 1)
                  {
                    //call function for load apps
                    
                  }
                  $('.alert_space').html(response.message);
                  if(button_id == 'app_save_close')
                  {
                     var save_close_url = "{{url('apps')}}";
                     console.log("url"+save_close_url);
                    window.location.href = save_close_url; 
                  }
                  if(response.url != '')
                  {
                     window.location.href = response.url;  
                    
                  }
              }
            });
     }
     else
     {
        console.log("validation Failed");
     }
   e.preventDefault(); /*avoid to execute the actual submit of the form.*/
   });
/* Save app end*/ 

    });
    </script>
@endsection