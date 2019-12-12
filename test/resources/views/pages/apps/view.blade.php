<?php
  include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')
@section('main_content')
{!! Html::script('js/parsley.min.js') !!} 
{!! Html::script('js/jquery.form.js') !!}   
{!! Html::style('css/font-awesome.min.css') !!} 
{!! Html::style('css/ionicons.min.css') !!} 
{!! Html::style('plugins/EasyAutocomplete-1.3.5/easy-autocomplete.min.css') !!} 
{!! Html::style('css/dropzone.min.css') !!}
{!! Html::style('plugins/datatables_new/jquery.dataTables.min.css') !!}
<style type="text/css">

  .resizewrapper{
        resize: vertical;
        overflow: auto;
        min-height: 200px;
    }
    .resizewrapperiframe{
        overflow-y: hidden;
        overflow-x: scroll;
        width:100%;
        height: 100%;
        min-height: 590px;
    }
    .resizable {
        resize: both;
        overflow-y: hidden;
        overflow-x: scroll;
        border: 1px solid black;
        /*min-height: 590px;*/
    }
    #pdfrender{
        overflow: auto;
    }
    .zoom{
        width:100%; /* you can use % */
        height: auto;
    }
</style>
<!-- Satrt Section -->
<section class="content-header">
  <div class="row">
    <div class="col-sm-8">
      <span style="float:left;">
        <strong>{{ trans('apps.app') }} -  <small class="app_name"></small> </strong> &nbsp;
      </span>
      <span style="float:left;">
        <?php
        $user_permission=Auth::user()->user_permission;                       
        ?> &nbsp;
         <a href="{{url('appslistview')}}/{{$app_id}}?app_saved_search=1">
                            <button class="btn btn-info btn-flat newbtn" style="line-height:13px !important;">{{ trans('language.back') }}</button>
                        </a> 
                         &nbsp;
        <a href="{{ url('viewapp') }}?action=add&appid={{$app_id}}" class="btn  btn-info btn-flat newbtn">{{ trans('apps.add_new_records') }}  &nbsp;<i class="fa fa-plus"></i>
            </a>&nbsp;
 <a href="{{ url('appslistview/'.$app_id) }}?search=search" class="btn  btn-info btn-flat newbtn">{{ trans('apps.search_records') }}  &nbsp;<i class="fa fa-search"></i>
            </a>&nbsp;
            <a href="{{ url('importRecords')}}" class="btn  btn-info btn-flat newbtn" title='{{trans("apps.import_tool_tip")}}'>{{trans("apps.import_records")}} &nbsp; <i class="fa fa-download"></i></a>
      </span>
    </div>
    <div class="col-sm-4">
      <ol class="breadcrumb">
          <li><a href="{{url('/home')}}"><i class="fa fa-dashboard"></i> {{ trans('language.home') }}</a></li>
          <li class="active">{{ trans('apps.apps') }}</li>
      </ol>
    </div>
  </div>
  </section>
<section class="content">
        
<div class="row">
  @if(Session::has('data'))
<div class="col-sm-12 alert_space" id="spl-wrn">
    <div class="alert alert-sty {{ Session::get('alert-class', 'alert-success') }} ">{{ Session::get('data') }}</div>        
</div>
@endif
   
 <!-- right column -->
<div class="col-xs-12 col-sm-4 col-md-4" id="col1">
  <form role="form" id="sjfb-sample" method="POST" action="{{url('saveAppValues')}}" enctype="multipart/form-data">
    {!! csrf_field() !!}
            <input type="hidden" name="hidd_action" id="hidd_action" value="edit">
            <input type="hidden" name="hidd_doc" id="hidd_doc" value="{{ $doc_id }}">
            <input type="hidden" name="app_id" id="app_id" value="{{ $app_id }}">
            <input type="hidden" name="reference_id" id="reference_id" value="{{ $reference_id }}">
            <input type="hidden" name="return_path" id="return_path" value="{{ $app_id }}">
    <!-- general form elements -->
    <div class="box box-info">
        <div class="box-header with-border">
          <span style="float:left;">
            <strong>{{ trans('apps.edit_index_field') }} -  <small class="app_name"></small> </strong> &nbsp;
          </span>
        </div>
        <!-- /.box-header -->
        <!-- form start -->

        <div class="box-body" id="dynamic_from">
         
        </div>
        <!-- /.box body -->
         <div class="box-footer">
          <div class="col-sm-8">
            <input type="submit" class="btn btn-primary" id="save_form" name="save_form" value="{{ trans('language.save') }}">
            &nbsp;

            <input type="submit" class="btn btn-primary" id="save_close" name="save_close" value="{{ trans('language.save_close') }}">
            &nbsp;
           
            <a href="{{url('appslistview')}}/{{$app_id}}" class="btn btn-danger">{{ trans('language.cancel') }}</a>
          </div>
          <div class="preloader" style="text-align: center; margin-top: 5px; display: none;" >
            <i class="fa fa-spinner fa-pulse fa-fw fa-lg"></i>
            <span class="sr-only">Loading...</span>
          </div>
        </div>
    </div>
    <!-- /.box -->
</form>
    <!-- Input addon -->
</div>
<!--/.col (right) -->

<div class="col-md-3" id="col2">
   <div class="box box-primary">
      <div class="box-header with-border">
         <strong>{{ Lang::get('documents.related_documents') }}</strong>  
         <span class="pull-right">
           <a href="{{url('link_to_doc')}}/{{$app_id}}/{{$doc_id}}" app_id="{{$app_id}}" data-doc_id="{{$doc_id}}" title="Link to documents" class="link_to_doc"><i class="fa fa-link"></i></a> &nbsp; &nbsp;
         <a href="{{url('documentAdd')}}?page=viewappdata&app_id={{$app_id}}&doc_id={{$doc_id}}" class=""><i class="fa fa-plus "></i></a>
         </span>
          
      </div>
      <div class="box-body" id="link_row">
        <div class="table-responsive mailbox-messages">
                <table class="table table-hover table-striped" id="dc_types">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>{{ trans('language.document') }}</th>
                      <th>{{ trans('language.document type') }}</th>
                      <th>{{ trans('language.created') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    
                  </tbody>
                </table>
                <!-- /.table -->
              </div>
      </div>
      <!-- /.box-body -->
     <div class="box-footer">
         
      </div>
   </div>
</div>

<div class="col-md-5" id="col3">
   <div class="box box-primary">
      <div class="box-header with-border">
         <strong>{{ Lang::get('apps.document') }} </strong>
        
      </div>
      <div class="box-body" id="link_row">
        <div id="pdfrender"></div>
      </div>
      <!-- /.box-body -->
      <div class="box-footer">
         
      </div>
   </div>
</div>
    
</div>
<!-- /.row -->  
    </section>
    <!-- END Section -->
<div class="modal fade" id="viewmoreModal" data-backdrop="true" data-keyboard="false">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header" style="border-bottom-color: deepskyblue;">
                            <h4 class="modal-title" style="float:left; width:90%;">{{trans('documents.all_documents')}}
                                <small>- {{trans('documents.view_all_data')}}</small>
                            </h4>

                            <a href="javascript:void(0);">
                                <button class="btn btn-primary btn-danger" id="cn" data-dismiss="modal" type="button">{{trans('language.close')}}</button>
                            </a>
                        </div>
              <div class="modal-body" id="more_data">
                
              </div>
              <div class="modal-footer">
               &nbsp;;
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->


        
<!-- /.modal ##DOC###-->
<div class="modal fade" id="link_to_doc_modal" data-backdrop="false" data-keyboard="false">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header" style="border-bottom-color: deepskyblue;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Link to Document</h4>
              </div>          
              <div class="modal-body" id="dupicate_data_div">

                 
                 <div class="row" >
                <div class="col-xs-12" style="overflow-x: auto; overflow-y: auto;">

               <table border="1" id="app_info" class="table table-bordered table-striped dataTable hover" width="100%">
                    <thead></thead>
                    <tbody></tbody>                    
                </table>
                </div>
</div>
                <div class="row" style="margin-top: 8px;margin-bottom: 8px;">
                   
                <div class="col-xs-4">
                  <select class="form-control" id="typeselect" name="typeselect" style="">
                            @foreach($doctypeApp as $val)
                            <option value="{{$val->document_type_id}}" <?php if((Session::get('view') != 'stack') || (Session::get('view') != 'department')){ if(@$type_id == $val->document_type_id) { echo 'selected';}} ?>>{{$val->document_type_name}}</option>
                            @endforeach
                        </select>  
                </div>
                <div class="col-sm-2" style="padding-right:0px;">  
                        <select class="form-control" id="linked_doc" name="linked_doc" >
                            <option value="show_all" selected="selected"> Show All</option>
                             <option value="linked_doc"> Show only Linked Documents</option>
                      </select> 
                    </div>

                <div class="col-sm-2" style="padding-right:0px;">  
                        <select class="form-control" id="search_column" name="search_column" >
                      </select> 
                    </div>
                <div class="col-sm-3" style="padding-left:1px;">
                     
                    <div class="input-group input-group-sm">
                    
                    <input type="text" class="form-control" id="search_text" name="search_text" value="@php echo (isset($search_text))?$search_text:''; @endphp">
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-info btn-flat docsrch" id="docsrch"><i class="fa fa-search"></i> {{trans('language.search')}} </button>
                    </span>
                  </div>
                    
                    </div>
                </div>
                 <div class="row">
                <div class="col-xs-12" style="overflow-x: auto; overflow-y: auto;">
               <table border="1" id="link_to_docDT" class="table table-bordered table-striped dataTable hover" width="100%">
                    <thead></thead>
                    <tbody></tbody>                    
                </table>
                </div>
</div>
              </div>
              <div class="modal-footer">
                <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <label class="control-label processing_spinner_modal"></label> 
                    <input type="hidden" id="link_doc_id" name="link_doc_id">
                   <button class="btn btn-primary save_link" type="button" id="replace">{{Lang::get('language.save')}}</button>
                   <button class="btn btn-danger" data-dismiss="modal" type="button">{{Lang::get('language.close')}}</button>
              </div>     
                </div>
                </div>
               </div> 
               
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->

       <!-- /.modal ##Link DOC###-->
   
 {!! Html::script('js/dropzone.js') !!}
{!! Html::script('plugins/EasyAutocomplete-1.3.5/jquery.easy-autocomplete.min.js') !!} 
{!! Html::script('plugins/simple-jquery-form-builder/js/sjfb-html-generator-app.js') !!}
{!! Html::script('plugins/datatables_new/jquery.dataTables.min.js') !!}
<script type="text/javascript">
$(document).ready(function(){
  $('#spl-wrn').delay(10000).slideUp('slow');
  var doctype_options=[];
  var formID = "{{@$app_id}}";
  var reference_id = "{{@$reference_id}}";
  var loadformurl = "{{ url('load_app')}}";
  var action = 'edit';
  var doc_id = '{{@$doc_id}}';
  var url_delete = "<?php echo URL('deleteAppAttached');?>";
  var url_delete = "<?php echo URL('deleteAppAttached');?>";
  var url_form_attach = "<?php echo URL('formAttachments');?>";
   var lengthMenu = getLengthMenu();//Function in app.blade.php
   var rows_per_page = '{{Session('settings_rows_per_page')}}';
  /*generateFormEdit(formID,loadformurl,action,doc_id,url_delete,url_form_attach);*/
var form_array = {'formID':formID,'url':loadformurl,'action':action,'doc_id':doc_id,'url_delete':url_delete,'url_form_attach':url_form_attach,'reference_id':reference_id,'col':'col_1'};
generateForm(form_array);
  var viewForm = function(data_array) 
      {
        
        var id = (typeof data_array.id != 'undefined') ? data_array.id:'';
        var dcno = (typeof data_array.dcno != 'undefined') ? data_array.dcno:'';
        var dcname = (typeof data_array.dcname != 'undefined') ? data_array.dcname:'';
        var doc_file_name = (typeof data_array.doc_file_name != 'undefined') ? data_array.doc_file_name:'';
        var exprstatus = (typeof data_array.exprstatus != 'undefined') ? data_array.exprstatus:'';
        var toplabel = (typeof data_array.toplabel != 'undefined') ? data_array.toplabel:'';


        console.log("id="+id+",dcno="+dcno+",dcname="+dcname+",doc_file_name="+doc_file_name+",exprstatus="+exprstatus+",toplabel="+toplabel);
        var path = doc_file_name;
        var ext=doc_file_name.split('.').pop(); //extension
        console.log("ext"+ext);
        var document_view_link =''; 
        if(ext == 'tiff')
        {
          $('#pdfrender').html('<div id="output" style="height:560px;"></div>');
              var xhr = new XMLHttpRequest();
              xhr.responseType = 'arraybuffer';
              xhr.open('GET',"{{ config('app.doc_url') }}"+path);
              xhr.onload = function (e) {
                  var tiff = new Tiff({buffer: xhr.response});
                  var canvas = tiff.toCanvas();
                  if (canvas) {
                    $('#output').empty().append(canvas);
                  }
              };
              xhr.send();
        }
        else if(ext=='gif'||ext=='jpg'||ext=='jpeg'||ext=='png')
        { 
            
            $("#pdfrender").html('<img class="zoom" src="{{ config('app.doc_url') }}'+path+'">');
        }
        else if(ext=='doc'||ext=="docx"||ext=='xls'||ext=='xlsx')
        {
                
                $("#pdfrender").html('<div class="resizable"><iframe src="https://docs.google.com/gview?url={{ config('app.doc_url') }}'+path+'&embedded=true" id="ifrm1" width="auto" height="auto;" class="resizewrapperiframe"></iframe></div>');  
        }
        else if(ext=='dwg'){
            

          $("#pdfrender").html('<div class="resizable"><iframe src="https://sharecad.org/cadframe/load?url={{config('app.doc_url') }}'+path+'" width="auto" id="ifrm1" height="auto;" class="resizewrapperiframe"></iframe></div>'); 
        }
        else if(ext=='zip' || ext == 'rar'){
            $("#pdfrender").html('<div><div class="info-box"><span class="info-box-icon bg-aqua"><i class="ion fa fa-file-archive-o"></i></span><div class="info-box-content"><span class="info-box-text" style="text-align: left !important;">This is a compressed document.</span><span style="display: block;font-size: 15px;margin-top: 18px;text-align: left !important;"><a href="{{ config('app.doc_url') }}'+path+'" download>Click here to download the document</a></span></div><!-- /.info-box-content --></div></div>');
        }
        else if(ext=='mp4'||ext=="ogv"||ext=='webm'||ext=='flv'){
            if(ext=='mp4'){
                $("#pdfrender").html('<div class="resizable"><video id="player1" style="width:100%;" poster="" preload="none" controls playsinline webkit-playsinline><source src="{{ config('app.doc_url') }}'+path+'" type="video/mp4"></video></div>');  
            }else if(ext=='ogv'){
                $("#pdfrender").html('<div class="resizable"><video id="player1" style="width:100%;" poster="" preload="none" controls playsinline webkit-playsinline><source src="{{ config('app.doc_url') }}'+path+'" type="video/ogg"></video></div>');  
            }else if(ext=='webm'){
                $("#pdfrender").html('<div class="resizable"><video id="player1" style="width:100%;" poster="" preload="none" controls playsinline webkit-playsinline><source src="{{ config('app.doc_url') }}'+path+'" type="video/webm"></video></div>');  
            }else if(ext=='flv'){
                $("#pdfrender").html('<div class="resizable"><div id="player_8168" style="display:inline-block;"><a href="http://get.adobe.com/flashplayer/">You need to install the Flash plugin</a></div>');
                    var flashvars_8168 = {};
                    var params_8168 = {
                        quality: "high",
                        wmode: "transparent",
                        bgcolor: "#ffffff",
                        allowScriptAccess: "always",
                        allowFullScreen: "true",
                        flashvars: "fichier={{ config('app.doc_url') }}"+path
                    };
                    var attributes_8168 = {};
                    flashObject("http://flash.webestools.com/flv_player/v1_27.swf", "player_8168", "490", "300", "8", false, flashvars_8168, params_8168, attributes_8168);  
            }
        }
        else if(ext=='mp3'||ext=="wav"||ext=='ogg'){
            if(ext=='mp3'){
                $("#pdfrender").html('<div class="resizable"><audio id="player2" preload="none" controls style="width:100%;"><source src="{{ config('app.doc_url') }}'+path+'" type="audio/mp3"></audio></div>');
            }else if(ext=='wav'){
                $("#pdfrender").html('<div class="resizable"><audio id="player2" preload="none" controls style="width:100%;"><source src="{{ config('app.doc_url') }}'+path+'" type="audio/x-wav"></audio></div>');
            }else if(ext=='ogg'){
                $("#pdfrender").html('<div class="resizable"><audio id="player2" preload="none" controls style="width:100%;"><source src="{{ config('app.doc_url') }}'+path+'" type="audio/ogg"></audio></div>');
            }  
        }
        else
        {
          $("#pdfrender").html('<div class="resizable"><iframe src="{{ config('app.doc_url') }}'+path+'#toolbar=0" id="ifrm1" width="auto" height="auto;" class="resizewrapperiframe"></iframe></div>');
        }
      }

  /*load apps*/
      var load_app_data = function() 
      {
      var url = "@php echo url('load_app_data?app_id='.$app_id.'&doc_id='.$doc_id.'&action=edit'); @endphp";
      var html = '';
      $.ajax({
            type: "GET",
            url: url,
            dataType:'JSON',
            success: function(response)
            {
                $('#dc_types tbody').html('');
                if(response.status == 1)
                {
                  app_name = response.app_name;
                  var c=0;
                  $.each(response.document_types, function(i, item) {

                    if(item.document_type_column_name !='')
                    {
                         c++;
                         var newTr   = '<tr>';
                         var data_attr = 'data-id="'+c+'" data-dcno="'+item.document_id+'" data-dcname="'+item.doc_name+'" data-doc_file_name="'+item.doc_file_name+'" data-exprstatus="'+item.exprstatus+'" data-toplabel="'+item.toplabel+'" data-type="document"';
                         newTr   += '<td width="3%" nowrap><a href="javascript:(0);" class="show_property" title="{{trans('apps.document_properties')}}" '+data_attr+'><i class="fa fa-ellipsis-v"  ></i></a></td>';
                            newTr   += '<td><a href="javascript:(0);" class="load_doc" '+data_attr+'>'+item.doc_name+'</a></td>';
                            newTr   += '<td>'+item.doc_type+'</td>';
                            newTr   += '<td>'+item.created_at+'</td>';
                             newTr   += '</tr>';
                             $('#dc_types tbody').append(newTr);
                    }

                    $(".load_doc").first().trigger("click");  
                  });
                 
                }
                else
                {
                }
                $("#app_section").html(html); 
                $(".app_name").html(app_name); 
            }
        });
    }
    //call function for load apps
    load_app_data();

    $(document).on('click', '.load_doc', function () {
    var id =$(this).attr('data-id');
    var dcno=$(this).attr('data-dcno');
    var dcname=$(this).attr('data-dcname');
    var doc_file_name=$(this).attr('data-doc_file_name');
    var exprstatus=$(this).attr('data-exprstatus');
    var toplabel=$(this).attr('data-toplabel');
    var data_array = {"id":id,"dcno":dcno,"dcname":dcname,"doc_file_name":doc_file_name,"exprstatus":exprstatus,"toplabel":toplabel}
    viewForm(data_array);
    $("#dc_types tbody tr").removeClass('row_selected'); 
    $(this).closest('tr').addClass('row_selected');   
    });

    

     $(document).on('click', '.view_attachment', function (event) {
      event.preventDefault()
    var id =$(this).attr('data-id');
    var dcno=$(this).attr('data-dcno');
    var dcname=$(this).attr('data-dcname');
    var doc_file_name=$(this).attr('data-doc_file_name');
    var exprstatus=$(this).attr('data-exprstatus');
    var toplabel=$(this).attr('data-toplabel');
    var data_array = {"id":id,"dcno":dcno,"dcname":dcname,"doc_file_name":doc_file_name,"exprstatus":exprstatus,"toplabel":toplabel}
    viewForm(data_array);
    });


     $(document).on('click', '.show_property', function (event) {

        var docid   = $(this).attr('data-dcno');
        var view = 'list';
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $('#viewmoreModal').modal().show();
        $.ajax({
      type: 'get',
      url: '{{URL::route('documensMoreDetails')}}',
      dataType: 'html',
      data: {_token: CSRF_TOKEN,docid:docid,view:view},
      timeout: 50000,
      success: function(data, status){     
        $("#more_data").html(data);
      },
      error: function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);    
        console.log(textStatus);    
        console.log(errorThrown);    
      }
        }); 
          
    });
/*dddd*/

var processing_spinner='<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>';
var tcolumns=[];
var doctype_options=[];
@php foreach($doctypeApp as $key => $row){ 
$index_filed = (isset($index_fileds[$row->document_type_id]))?$index_fileds[$row->document_type_id]:array();
$loop=0;
@endphp
var tcolumn=[];
var doctype_option='';
var loop = 0;
/*col START*/
tcolumn.push({"data":"actions","title":"<input type='checkbox' id='link_ckbCheckAll' data-orderable='false'></input> {{trans('language.actions')}}","class":"{{ $loop }} no-sort"});
doctype_option +="<option value='search_all' data-column_type='' data-dynamic_col='0'>{{trans('language.search_all')}}</option>";    
/*col END*/

/*col START*/
var department_name ="@if(Session::get('settings_department_name')){{ Session::get('settings_department_name') }} @else {{trans('language.department')}} @endif";

tcolumn.push({"data":"department_id","title":department_name,"class":"{{ ++$loop }}"});

doctype_option +="<option value='department' data-column_type='' data-dynamic_col='0'>"+department_name+"</option>";

/*col END*/

/*col START*/
var stack_name = "{{trans('language.Stack')}}";
tcolumn.push({"data":"stack_id","title":stack_name,"class":"{{ ++$loop }}"});
 doctype_option +="<option value='filename' data-column_type='' data-dynamic_col='0'>"+stack_name+"</option>";

/*col END*/

/*col START*/
var document_no_label ="{{$row->document_type_column_no}}";
tcolumn.push({"data":"document_no","title":document_no_label,"class":"{{ ++$loop }}"});
doctype_option +="<option value='document_no' data-column_type='' data-dynamic_col='0'>"+document_no_label+"</option>";

/*col END*/

/*col START*/
var document_name_label ="{{$row->document_type_column_name}}";
tcolumn.push({"data":"document_name","title":document_name_label,"class":"{{ ++$loop }}"});
 doctype_option +="<option value='document_name' data-column_type='' data-dynamic_col='0'>"+document_name_label+"</option>"; 

/*col END*/

/*col START*/
@php 
        if(count($index_filed)){
            foreach($index_filed as $value){ @endphp

tcolumn.push({"data":"{{$value->document_type_column_id}}","title":"{{$value->document_type_column_name}}", "class":"{{ ++$loop }} no-sort"}); 
                
doctype_option +="<option value='{{$value->document_type_column_id}}' data-column_type='{{$value->document_type_column_type}}' data-dynamic_col='1'>{{$value->document_type_column_name}}</option>";      
            @php } } 
        @endphp

/*col END*/

/*col START*/

/*col END*/

/*col START*/

/*col END*/

tcolumns["{{$row->document_type_id}}"] = tcolumn;
doctype_options["{{$row->document_type_id}}"] = doctype_option;
var  link_doc_id =0;
@php } /*end foreach */@endphp



     var load_app_doc = function(app_id,doc_id) 
      {
      var url = "@php echo url('load_app'); @endphp";
      url +="?form_id="+app_id+"&doc_id="+doc_id+"&action=view";
      var html = '';
      $.ajax({
            type: "GET",
            url: url,
            dataType:'JSON',
            success: function(response)
            {
                var newTh   = '<tr>';
                var newTr   = '<tr>';
                $.each(response.inputs, function(i, item) {
                    console.log(item);    
                    newTh   += '<th>'+item.label+'</th>';

                    newTr   += '<td>'+item.selected+'</td>'; 
                  });
                newTh   += '</tr>';

                newTr   += '</tr>';;
                //$("#app_section").html(html); 
                console.log(newTh);
                console.log(newTr);
                
                $("#app_info thead").html(newTh);
                $("#app_info tbody").html(newTr);
            }
        });
    }
var link_datatable = ''; 



var typeselect =   function()
    {   
        var obj = $('#typeselect').val();
        return obj;
    };

    var linked_doc_only =   function()
    {   
        var obj = $('#linked_doc').val();
        return obj;
    };

    var linkdocid =   function()
    {   
        var val = $('#link_doc_id').val();
        return val;
    };

    var search_text =   function()
    {   
        var obj = $('#search_text').val();
        return obj;
    };

     var search_column_data =   function()
    {   
        var column_type = $('#search_column').find(':selected').attr('data-column_type');
        var search_column_type =  (typeof column_type === "undefined")?"":column_type;
        var dynamic_col = $('#search_column').find(':selected').attr('data-dynamic_col');
        var search_dynamic_col =  (typeof dynamic_col === "undefined")?"0":dynamic_col;
        console.log("search_column_type = "+search_column_type);
        var obj = {'search_column':$('#search_column').val(),'search_column_type':search_column_type,'search_dynamic_col':search_dynamic_col};
        return obj;
    };
     var link_dtshow = function(){
            if ( $.fn.DataTable.isDataTable('#link_to_docDT')) 
            {
              console.log("destroy DataTable");
              $('#link_to_docDT').DataTable().destroy();
            }

            $('#link_to_docDT').empty();
            var value = $("#typeselect").val();
            $('#search_column').html(doctype_options[value]);
             
            var sort_order = "@php echo (isset($sort_order))?$sort_order:1; @endphp";
            var sort_direct = "@php echo (isset($sort_direct))?$sort_direct:'desc'; @endphp";
            
            var res_datatable = $('#link_to_docDT').DataTable( {
                "processing": true,
                "serverSide": true,
                "order": [[sort_order,sort_direct]],
                "lengthMenu": lengthMenu,
                "responsive": true,
                "destroy": true,
                "pageLength": rows_per_page,
                "displayStart": "@php echo (isset($displayStart))?$displayStart:0; @endphp",
                "colReorder": true,
                "scrollX": true,
                "scrollY": 350,
                "bFilter": true,
                /*"search": {"search": "@php echo (isset($search_text))?$search_text:''; @endphp"},*/
                "searching": false,
                "ajax": 
                {
                    "url": "@php echo url('link_to_doc_filter'); @endphp",
                    "type": "POST",
                    "headers": { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    "data":function(d)
                    {
                        //tcolumn.push({"data":"headsocument_status"});
                        d.type = typeselect();
                        d.link_doc_id = $('#link_doc_id').val();
                        d.search_text = search_text();
                        d.search_column_data = search_column_data();
                        d.linked_doc_only = linked_doc_only();
                        /*d.filter = filter();
                        d.view = '{{Session::get('view')}}';
                        d.id = "{{@$sel_item_id}}";
                        d.docid = $("#hidd-doc-id").val();
                        d.search_text = search_text();
                        d.search_column_data = search_column_data();*/
                    }
                },
                "columns": tcolumns[value],            
                "columnDefs": [{ "targets": 0, "orderable": false }],
                "language": { processing: '<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span> ',"searchPlaceholder": "{{trans('language.data_tbl_search_placeholder')}}"  },
                "rowCallback": function( row, data ) {
                    
                   // console.log(data);
                    

                },

            });
            //alert disable
            $.fn.dataTable.ext.errMode = 'none';

            //disable auto search of datatable
            $("div.dataTables_filter input").unbind();

            return res_datatable;
        };

     $(document).on('click', '.link_to_doc', function (e) 
    {
        e.preventDefault();
        link_doc_id = $(this).attr('data-doc_id');
        var app_id = "{{$app_id}}";
        $('#link_doc_id').val(link_doc_id);
        $(".checkBoxClass").prop('checked', false);
        $("#chk"+link_doc_id).prop('checked', true);
         $('#link_to_doc_modal').modal().show();
         load_app_doc(app_id,link_doc_id)
         link_datatable = link_dtshow();
    });

     $('#typeselect').change(function(){
        link_datatable = link_dtshow();
    });
$('#linked_doc').change(function(){
        link_datatable = link_dtshow();
    });

 $(document).on('click', '#link_ckbCheckAll',function () 
    {
        $(".link_checkBoxClass").prop('checked', $(this).prop('checked'));
        /*countChecked();*/
    });

   $(document).on('click', '.save_link', function (e) 
    {
        e.preventDefault();
        
        
        swal({
              title: "{{trans('language.Swal_are_you_sure')}}",
              text: "{{trans('language.Swal_not_revert')}}",
              type: "{{trans('language.Swal_warning')}}",
              showCancelButton: true
            }).then(function (result) {
                if(result){
                    linkdocid = $('#link_doc_id').val();
                    //$(".processing_spinner_modal").html(processing_spinner);
                    var arr = $('input:checkbox.link_checkBoxClass').filter(':checked').map(function () {
                                        return this.value;
                                        }).get();
                            
                    var uncheked_arr = $('input:checkbox.link_checkBoxClass').filter(':not(:checked)').map(function () {
                                        return this.value;
                                        }).get();
                    
                    var published_count = arr.length;
                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    
                    $.ajax({
                    type: 'post',
                    dataType:'json',
                    url: "@php echo url('link_to_doc_save'); @endphp",
                    data: {'_token': CSRF_TOKEN,'selected':arr,'link_doc_id':linkdocid,'uncheked_arr':uncheked_arr},
                    timeout: 50000,
                    beforeSend: function() {
                   // show_bar();
                    },
                    success: function(data)
                    {
                       swal(data.message);
                       load_app_data();
                    },
                    error: function(jqXHR, textStatus, errorThrown){
                        console.log(jqXHR);    
                        console.log(textStatus);    
                        console.log(errorThrown);    
                    },
                    complete: function() {
                        /*$("#bs").hide();*/
                        //hide_bar();
                    }
                    });
                }
            });
        

    });
 });   
</script>
@endsection