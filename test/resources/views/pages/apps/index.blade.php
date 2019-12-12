@extends('layouts.app')
<?php
    include (public_path()."/storage/includes/lang1.en.php" );
?>
<style type="text/css">
    .fa-pencil,.fa-link,.fa-trash, .fa-ellipsis-v{
      color: #97a0b3 !important;
    }
    .fa-pencil:hover,.fa-link:hover,.fa-trash:hover {
      color: #fff !important;
    }
    .small-box 
    {
      margin-bottom: 0px !important;
    }
    .small-box .icon 
    {
        margin-top: 17px !important;
        font-size: 50px !important;
    }

    .small-box:hover .icon
    {
        font-size: 55px !important;
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
</style>
{!! Html::style('plugins/EasyAutocomplete-1.3.5/easy-autocomplete.min.css') !!} 
{!! Html::style('plugins/datatables_new/jquery.dataTables.min.css') !!}
@section('main_content')
  <section class="content-header">
  <div class="row">
    <div class="col-sm-8">
      <span style="float:left;">
        <strong>{{ Lang::get('apps.apps') }}</strong> &nbsp;
      </span>
      <span style="float:left;">
        <?php
        $user_permission=Auth::user()->user_permission;                       
        ?>     
        @if(stristr($user_permission,"add") && Auth::user()->user_role != Session::get('user_role_private_user'))
            <a href="{{ url('appsEdit/0')}}" class="btn  btn-info" title='{{trans("apps.add_tool_tip")}}'>{{ Lang::get('apps.add_new_app') }}  <i class="fa fa-plus"></i>
            </a> &nbsp;
            <a href="{{ url('importRecords')}}" class="btn  btn-info" title='{{trans("apps.import_tool_tip")}}'>{{trans("apps.import_records")}}  <i class="fa fa-download"></i></a>
        @endif
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
        <div class="preloader_app" style="text-align: center; display: block;" >
          <i class="fa fa-spinner fa-pulse fa-fw fa-lg"></i>
          <span class="sr-only">Loading...</span>
        </div>   
        <div class="row" id="app_section">       
        </div>
    </section>
    <!-- User add Form -->
<div class="modal fade" id="dTAddModal" data-backdrop="false" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="dGAddModal" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                 {{ Lang::get('apps.apps') }}
             </h4>
         </div>
         <div class="modal-body">
            <!-- form start -->
            {!! Form::open(array('url'=> array('appsSave','0'), 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'appsSaveForm', 'id'=> 'appsSaveForm','data-parsley-validate'=> '')) !!}
            <input type="hidden" name="app_id" id="app_id" value="0">   
            <input type="hidden" name="action" id="action" value="add">            
            <div class="form-group">
                <label for="Doc Type" class="col-sm-2 control-label">{{ Lang::get('apps.app_name') }}: <span class="compulsary">*</span></label>
                <div class="col-sm-8">
                    <input class="form-control" id="name" placeholder="{{ Lang::get('apps.app_name') }}" data-parsley-required="true" data-parsley-required-message="{{ Lang::get('apps.app_name_required') }}" autofocus="autofocus" name="name" type="search" value="" data-parsley-maxlength ="{{ Lang::get('apps.app_name_max_length') }}" title="{{ Lang::get('apps.app_name_max_length_title') }}">    
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
                
                <div class="col-sm-12">
                   <table class="table" id="myTable">
                <thead>    
                <tr>
                  <th width="3%" nowrap>#</th>
                  <th width="25%">{!! Form::label(Lang::get('apps.field_type'), '', array('class'=> 'control-label'))!!}</th>
                  <th width="25%">{!! Form::label(Lang::get('apps.index_field'), '', array('class'=> 'control-label'))!!}</th>
                  <th width="20%">{!! Form::label(Lang::get('apps.default_value').'/'.Lang::get('apps.choices'), '', array('class'=> 'control-label'))!!}</th>
                  <th >{!! Form::label(Lang::get('apps.action'), '', array('class'=> 'control-label'))!!}</th>
                </tr>
                </thead>
                <tbody>
              </tbody>  
              </table>
                </div>
            </div>

        <div class="form-group">
            <label class="col-sm-4 control-label"></label>
            {!!Form::hidden('count-textbox','',array('id'=>'counttextbox'))!!}
            <div class="col-sm-8">
            <!-- <button type="button" onclick="myFunction()">Try it</button> -->
                <input class="btn btn-primary" id="save" type="submit" value="{{Lang::get('apps.save')}}" > &nbsp;&nbsp;
                {!!Form::button(Lang::get('apps.close'), array('class' => 'btn btn-primary btn-danger', 'data-dismiss'=> 'modal', 'aria-hidden'=> 'true')) !!}&nbsp;&nbsp;
                {!!Form::button(Lang::get('apps.add_new_indexfield'),array('class'=>'btn btn-primary button-add','id'=>'addButton','value'=>'addButton'))!!}
            </div>
        </div><!-- /.col -->
        {!! Form::close() !!}
    </div>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="modal fade" id="SearchModal" data-backdrop="false" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="dGSearchModal" >
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">
          Search in index fields
        </h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>Search Keyword:</label>
          <input type="text" class="form-control" id="search_word" placeholder="Enter the keyword">
        </div>
        
      </div>
      <div class="box-footer">
        <div class="form-group">
            <div class="col-sm-8">
                <button class="btn btn-primary search_index" id="search_index">Search</button> &nbsp;&nbsp;
                {!!Form::button(Lang::get('apps.close'), array('class' => 'btn btn-primary btn-danger', 'data-dismiss'=> 'modal', 'aria-hidden'=> 'true')) !!}
            </div>
        </div><!-- /.col -->        
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- detalis of app -->

<div class="modal fade" id="app_response_modal">
   <div class="modal-dialog">
      <div class="modal-content app_response_remote">
         
      <div class="modal-footer"></div>
      </div>
      <!-- /.modal-content -->
   </div>
   <!-- /.modal-dialog -->
</div>
{!! Html::script('plugins/simple-jquery-form-builder/js/sjfb-html-generator-app.js') !!}
{!! Html::script('plugins/simple-jquery-form-builder/js/sjfb-html-generator-edit-app.js') !!}
{!! Html::script('plugins/EasyAutocomplete-1.3.5/jquery.easy-autocomplete.min.js') !!} 
{!! Html::script('js/jquery-ui.min.js') !!}
    <script type="text/javascript">

    $(document).ready(function(){
      // view form more details
      var loading_src = "{{ URL::asset('images/loading/loading.gif') }}";
      var loading_text ='<div  style="text-align: center;"><img src="'+loading_src+'"></div>';
      var col_ctr = 0; /* Counter for*/
      
      var autocomplete_options = { url: "{{ URL('document_column_suggession') }}",getValue: "name" };

      var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
      /*load apps*/
      var load_app = function() 
      {
      var url = "@php echo url('load_apps'); @endphp";
      var html = '';
      $.ajax({
            type: "GET",
            url: url,
            dataType:'JSON',
            success: function(response)
            {
                 $(".preloader_app").css("display", "none");
                if(response.status == 1)
                {
                 
                  $.each(response.data, function(i, item) {
                  var document_type_modified_by = '-'; 
                  if(item.document_type_modified_by != null)
                    {
                      document_type_modified_by = item.document_type_modified_by;
                    }
                  var updated_at = '-'; 
                  if(item.updated_at != null)
                    {
                      updated_at = item.updated_at;
                    }
                  html += '<div id="div_sort'+i+'" class="col-md-3 connectedSortable">';
                  html += '<div class="box box-info widget_item" id="'+item.document_type_id+'">';
                  html += '<div class="box-body">';
                  html += '<div class="small-box label-success">';
                  html += '<div class="inner">';
                  html += '<div>';
                  html += '<span style="font-size: 15px !important;font-weight: 700;">'+item.document_type_name.toUpperCase()+'</span>';
                  html += '&nbsp;';
                  html += '<a href="{{url("appsEdit")}}/'+item.document_type_id+'" title="Edit Index Fields"  id="document-type-edit" documentTypeId="'+item.document_type_id+'"><i class="fa fa-pencil"></i></a>';
                  html += '&nbsp;&nbsp;';
                  html += '<a href="{{url("appsLink")}}/'+item.document_type_id+'" title="Link this App to Document type"  id="document-type-edit" documentTypeId="'+item.document_type_id+'"><i class="fa fa-link"></i></a>';
                  html += '&nbsp;&nbsp;';
                  html += '<a title="Delete this App" class="app-delete" documentTypeId="'+item.data_count+'" documentTypeName="'+item.document_type_name+'" style="cursor:pointer;"><i class="fa fa-trash"></i></a>';
                  html += '<a style="cursor:move;" class="pull-right" title="Drag & Drop"><i class="fa fa-ellipsis-v"></i>&nbsp;<i class="fa fa-ellipsis-v"></i></a>';
                  html += '&nbsp;&nbsp;';
                  html += '<a title="More Details"  id="app_more" data-toggle="modal" aria-hidden="true" documentTypeId="'+item.document_type_id+'" style="cursor:pointer;"><i class="fa fa-ellipsis-v"></i></a>';
                  html += '</div>';
        
                  html += '<p><i class="fa fa-file-text-o" style="cursor:pointer;" title="No. of Records: '+item.data_count+'"></i> '+item.data_count+'</p>';
                  html += '<div class="btn-toolbar">';
                  html += '<button class="btn btn-primary"><a href="appslistview/'+item.document_type_id+'" title="View Records" id="document-type-edit" documentTypeId="'+item.document_type_id+'" style="color: white !important;"><i class="fa  fa-list-ol"></i></a></button>';
                  html += '<a href="appslistview/'+item.document_type_id+'?search=search"class="btn btn-primary search_index_icon" title="Search" app_id="'+item.document_type_id+'" app_name="'+item.document_type_name+'"><i class="fa fa-search"></i></a>';
                  html += '<a href="{{URL('viewapp')}}?action=add&appid='+item.document_type_id+'" app_id="'+item.document_type_id+'" title="{{ trans('language.add_new') }}" id="viewform" class ="viewform btn btn-primary" documentTypeId="'+item.document_type_id+'" style="color: white !important;"><i class="fa fa-plus"></i></a>';
                  
                  html += '</div>';
                  html += '</div>';//inner
                  html += '</div>';//small-box
                  html += '</div>';//box-body
                  html += '</div>';//box
                  html += '</div>';//col-md-3
                  });
                  /*sortable function*/
                  $('#app_section').sortable({
                    handle              : '.widget_item',
                    forcePlaceholderSize: true,
                    zIndex              : 999999,
                    stop: function (event, ui) {
                        var changedList = this.id;
                        var div_sort = new Array();
                        var data = $(this).sortable('serialize');
                        $(".widget_item").each(function( index ) {
                           div_sort.push($( this ).attr('id'));

                        });
                          $.ajax({
                            data:{_token:CSRF_TOKEN,data:div_sort},
                            type: 'POST',
                            dataType:'json',
                            url: '{{URL('saveAppsWidgetPostion')}}',
                            success:function(response)
                            {
                              /*console.log(response);*/
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.log(jqXHR.status);
                                }
                        });
                    }
                    /*end sortable*/
                  });
                }
                else
                {
                  html += '<div class="col-sm-12">';
                  html += '<div class="callout callout-warning">';
                  html += '<h4>No apps found</h4>';
                  
                  html += 'No apps found';
                  html += '</div></div>';
                }
                $("#app_section").html(html); 
            }
        });
    }
    //call function for load apps
    load_app();
    $('.connectedSortable .widget_item').css('cursor', 'move');


/* Add New App Start*/    
var select_type ='';
@php  foreach ($apps_types as $key => $value) : @endphp
select_type +='<option value="{{ $value->value }}" data-is_options="{{ $value->is_options }}">{{ $value->name }}</option>';  
@php endforeach @endphp

var add_column = function(data_array) 
{
    var ctr = data_array.col_ctr;
    var column_type = (typeof data_array.column_type != 'undefined') ? data_array.column_type:'text';
    var is_option = (typeof data_array.is_option != 'undefined') ? data_array.is_option:0;
    var column_id = (typeof data_array.column_id != 'undefined') ? data_array.column_id:0
    var column_name = (typeof data_array.column_name != 'undefined') ? data_array.column_name:'';
    var column_value = (typeof data_array.column_value != 'undefined') ? data_array.column_value:'';
    var includeRequiredHTML = '';
    var includeChoicesHTML = '';
    var txtName = "txtName" + ctr;
    var txtAge = "txtAge" + ctr;
    var txtGender = "txtGender" + ctr;
    var txtOccupation = "txtOccupation" + ctr;
    var common_attr = 'data-ctr="'+ctr+'" data-parent="'+ctr+'"';
    includeRequiredHTML = '' +
            '<label>' +
            '<input class="toggle-required" type="checkbox" name="checkbox_required_'+ctr+'" id="checkbox_required_'+ctr+'"> {{ Lang::get("apps.required") }}' +
            '</label>';
    var newTr = '<tr class="tr_'+ctr+' group_tr_'+ctr+'">';

    if(column_type == 'checkbox' || column_type == 'radio' || column_type == 'select')
    {
      includeChoicesHTML += '<button class="btn btn-primary add_choice" id="" type="button" value="" ' + common_attr + '><i class="fa fa-plus"></i> {{Lang::get("apps.add_choice")}}</button>'; 
    }
    else
    { 
      includeChoicesHTML += '<input type="text" name="column_value_'+ctr+'" id="column_value_'+ctr+'" class="form-control" value="" placeholder="{{ Lang::get("apps.default_value") }}"/ ' + common_attr + '>';
    }

    newTr += '<td>';
    newTr +='<span title="Drag & Drop">';
    newTr +='<div class="grippy"></div>';
    newTr +='</span>';  
    newTr += '</td>';

    newTr += '<td><select class="form-control column_type" name="column_type_'+ctr+'" id="column_type_'+ctr+'" ' + common_attr + '>' + select_type + '</select></td>';
    newTr += '<td>';
    newTr += '<input type="text" name="column_name_'+ctr+'" id="column_name_'+ctr+'" class="form-control" value="'+column_name+'" placeholder="{{ Lang::get("apps.index_field") }}" />';
    newTr += '<input type="hidden" name="column_id['+ctr+']" id="column_id'+ctr+'" value="'+column_id+'"  />';
    newTr += '</td>';
    newTr += '<td id="choice_td_'+ctr+'">';
    newTr +=includeChoicesHTML;
    newTr += '</td>';
    newTr += '<td>';
    newTr +='<span>';
    newTr +=includeRequiredHTML;
    newTr +='</span>'; 
    newTr +='<span>';
    newTr +='&nbsp;&nbsp;<i class="fa fa-fw fa-trash deleteCol" title="Remove Field" ' + common_attr + '></i>';
    newTr +='</span> '; 
    newTr += '</td>';

    newTr += '</tr>';
    $('#myTable').append(newTr);

    
    $("#column_type_"+ctr).append(column_type);
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

$(document).on("click","#app_more",function(e) {
   e.preventDefault();
   var obj = {app_id: $(this).attr('documentTypeId')};
   show_app_details(obj);
   
   });

var show_app_details = function(response) 
   {
    $('.app_response_remote').html(loading_text);
    $('#app_response_modal').modal({
                     show: 'show',
                     backdrop: true
               }); 
    var appID = response.app_id;

    $.ajax({
            type: 'get',
            url: "{{URL('appMoreDetails')}}",
            dataType: 'json',
            data: {appid:appID},
            timeout: 50000,
            success: function(data)
            {
                $('.app_response_remote').html(data.html);
            }
        });
   };
/* Add Choice Start */

var add_choice = function(data_array) 
{
    var ctr = data_array.col_ctr;
    var parent = data_array.parent;
    var column_type = (typeof data_array.column_type != 'undefined') ? data_array.column_type:'text';
    var is_option = (typeof data_array.is_option != 'undefined') ? data_array.is_option:0;
    var column_id = (typeof data_array.column_id != 'undefined') ? data_array.column_id:0
    var choice_name = (typeof data_array.choice_name != 'undefined') ? data_array.choice_name:'';
    var column_value = (typeof data_array.column_value != 'undefined') ? data_array.column_value:'';
    var selectedHTML = '';
    var includeChoicesHTML = '';
    var txtName = "txtName" + ctr;
    var txtAge = "txtAge" + ctr;
    var txtGender = "txtGender" + ctr;
    var txtOccupation = "txtOccupation" + ctr;
    var common_attr = 'data-ctr="'+ctr+'" data-parent="'+parent+'"';
    selectedHTML = '' +
            '<label>' +
            '<input class="toggle-required" type="checkbox" name="choice_selected_'+parent+'['+ctr+']" id="choice_selected_'+ctr+'" value="'+ctr+'"> {{ Lang::get("apps.selected") }}' +
            '</label>';
    var new_choice_Tr = '<tr class="tr_'+ctr+' group_tr_'+parent+'" >';
    new_choice_Tr += '<td></td>';
    new_choice_Tr += '<td>';
    new_choice_Tr +='<span title="Drag & Drop" class="pull-right">';
    new_choice_Tr +='<div class="grippy"></div>';
    new_choice_Tr +='</span>';  
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
    
    col_ctr++;
    var parent = $(this).attr('data-ctr');
    console.log("parent = "+parent);
    var data_array = {'col_ctr':col_ctr,'parent':parent};
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

$("#appsSaveForm").submit(function(e) 
  {
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
                    load_app();
                    $('#dTAddModal').modal().hide();
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
/* Add New App End*/    

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

/* Delete Column */
$(document).on("click",".deleteChoice",function(e) {
       
       var choice =$(this).attr('data-ctr'); 

       swal({
        title:"{{trans('language.confirm_delete')}}",
        showCancelButton: true
        }).then((result) => {
        if(result){
         
          $(".tr_"+choice).remove();
          /*refresh_options();*/
        }
        else
        {
          //stay in same stage
        }
     });
     });
/* END Delete Column */

// view form
    $(document).on("click",".viewform",function(e) {
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var appID = $(this).attr('app_id');
        $.ajax({
            type: 'post',
            url: '{{URL('viewapp')}}',
            data: {_token: CSRF_TOKEN,appid:appID},
            timeout: 50000,
            beforeSend: function() {
              $("#bs").show();
            },
            success: function(data)
            {
                $("#content_form").html(data);
            }
        });

    });
    $(document).on("click",".app-delete",function(e) {
      var id = $(this).attr('documentTypeId');
      var docGroup = $(this).attr('documentTypeName');
        $.ajax({
        type:'GET',
        url : base_url+'/checkhasRecords',
        data :'id='+id,
        success:function(result){
            if(result == 'exist'){
                console.log(result);
                swal({
                title: "There are records under the App '"+docGroup.toUpperCase()+"'. Do you want to delete the records?",
                type: "{{trans('language.Swal_warning')}}",
                showCancelButton: true
              }).then((result) => {
                if (result) {
                  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                  $.ajax({
                      type: 'post',
                      url: '{{URL('appDelete')}}',
                      data: {_token: CSRF_TOKEN, id:id},
                      beforeSend: function() {
                          $(".preloader").show();
                      },
                      success: function(data){ 
                        if(data = 'deleted'){
                          swal({title:"App '"+docGroup+"' and its records deleted"}).then(function (result) {
                                  load_app();});
                        }
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
                  
                } else {
                  swal("Records are safe");
                }
              });
            }
            else
            {
                swal({
                    title: "Do you want to delete App '" + docGroup.toUpperCase() + "' ?",
                    text: "All records in this App will be deleted!",
                    type: "{{trans('language.Swal_warning')}}",
                    showCancelButton: true
                  }).then(function (result) {
                      if(result){
                          // Success
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
                              success: function(data){ 
                                  swal({title:data}).then(function (result) {
                                  load_app();});
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
    });
    //search
    $(".search_index").click(function(){
      var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
      var search_word = $("#search_word").val();
      var appid = $('.search_index_icon').attr('app_id');
      var app_name = $('.search_index_icon').attr('app_name');
      if(search_word == '')
      {
        swal("Please enter the keyword for search");
        return false;
      }
      else
      {
        window.location.href='{{URL('appslistview')}}'+'/'+appid+'?search='+search_word;
      }
      });

    });
    
    </script>
@endsection