@extends('layouts.app')
<?php
include (public_path()."/storage/includes/lang1.en.php" );
?>
{!! Html::style('plugins/EasyAutocomplete-1.3.5/easy-autocomplete.min.css') !!} 
@section('main_content')
<section class="content-header">
  <div class="row">
    <div class="col-sm-8">
      <span style="float:left;">
        <strong>{{ trans('apps.add_new_records') }} - <small>{{ucfirst(@$app_details->document_type_name)}}</small></strong> &nbsp;
      </span>
      <a href="{{ url('importRecords')}}" class="btn  btn-info" title='{{trans("apps.import_tool_tip")}}'>{{trans("apps.import_records")}}  <i class="fa fa-download"></i></a>
    </div>
    <div class="col-sm-4">
      <ol class="breadcrumb">
          <li><a href="{{url('/home')}}"><i class="fa fa-dashboard"></i> {{ Lang::get('language.home') }}</a></li>
          <li class="active">{{ Lang::get('apps.apps') }}</li>
      </ol>
    </div>
  </div>
</section> 
@if(Session::has('data'))
<section class="content content-sty" id="spl-wrn">        
    <div class="alert alert-sty {{ Session::get('alert-class', 'alert-success') }} ">{{ Session::get('data') }}</div>        
</section>
@endif
<section class="content">
<div class="row">
  <!-- right column -->
<div class="col-xs-12 col-sm-12 col-md-12">
  <form role="form" id="sjfb-sample" method="POST" action="{{url('saveAppValues')}}" enctype="multipart/form-data">
    {!! csrf_field() !!}
            <input type="hidden" name="hidd_action" id="hidd_action" value="{{ @$action }}">
            <input type="hidden" name="hidd_doc" id="hidd_doc" value="{{ @$doc }}">
            <input type="hidden" name="app_id" id="app_id" value="{{ $app_details->document_type_id }}">
            <input type="hidden" name="app_name" id="app_name" value="{{ $app_details->document_type_name }}">
            <input type="hidden" name="app_description" id="app_description" value="{{ $app_details->document_type_description }}">
            <input type="hidden" name="return_path" id="return_path" value="{{ @$app_id }}">
    <!-- general form elements -->
    <div class="box box-info">
        
        <!-- /.box-header -->
        <!-- form start -->

        <div class="box-body" id="dynamic_from">
         
        </div>
        <!-- /.box body -->
         <div class="box-footer">
          <div class="col-sm-8">
            <input type="submit" class="btn btn-primary" id="save_form" value="{{ trans('language.save') }}">
            &nbsp;
             <input type="submit" class="btn btn-primary" id="save_close" name="save_close" value="{{ trans('language.save_close') }}">
            &nbsp;
            @if($action == 'add')
            <a href="{{url('apps')}}" class="btn btn-danger">Cancel</a>
            @else
            <a href="{{url('appslistview')}}/{{$app_details->document_type_id}}/{{$app_details->document_type_name}}" class="btn btn-danger">Cancel</a>
            @endif
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
</div> 
<input type="hidden" name="form_id" id="form_id" value="{{ $app_details->document_type_id }}">
<input type="hidden" name="loadformurl" id="loadformurl" value="{{ url('load_app')}}">
</section>
<style type="text/css">
.description
{
  color: #777;
  font-size: 15px;
  display: inline-block;
  padding-left: 4px;
  font-weight: 300;
}
#sjfb-sample .required-field > label:after {
  content: " *";
  color: red;
  font-size: 90%;
}
.assign{
  padding-top: 10px;
  font-size: 15px;
  font-weight: 500;
}
#wrong{
  color: red;
  margin-top: -5px;
}
label {
  text-transform: capitalize;
}
</style>
{!! Html::script('js/dropzone.js') !!}
{!! Html::style('css/dropzone.min.css') !!}
{!! Html::script('plugins/EasyAutocomplete-1.3.5/jquery.easy-autocomplete.min.js') !!}
{!! Html::script('plugins/simple-jquery-form-builder/js/sjfb-html-generator-app.js') !!}

<script type="text/javascript">
$('#spl-wrn').delay(5000).slideUp('slow');

$(function(){
var formID = $('#form_id').val();
var loadformurl = $('#loadformurl').val();
var action = '{{$action}}';
var doc = '{{@$doc}}';
var url_delete = "<?php echo URL('deleteAttachments');?>";
var url_delete = "<?php echo URL('deleteAppAttached');?>";
var url_form_attach = "<?php echo URL('formAttachments');?>";

var form_array = {'formID':formID,'url':loadformurl,'action':action,'url_form_attach':url_form_attach,'url_delete':url_delete,'col':'col_2'};
generateForm(form_array);
});

$( "#sjfb-sample" ).submit(function( event ) {
$(".preloader").css("display", "block");
var validform = 1;
  $('.hiddendrop').each(function() {
    var req = $(this).attr('data-req');
    var val = $(this).val();
    var uniqueid = $(this).attr('data');
    if((req == 1) && (val == "" || val == null))
    {
      validform =0;
      $('#span-'+uniqueid).text("No file has been uploaded");
      
    }
    else if((req == 1) && (val != "" || val != null))
    {
      $('#span-'+uniqueid).text("");
    }

  });

if(validform == 0)
{
  event.preventDefault();
}
});

function updateList(ip) {
  
  var input = document.getElementById('file-'+ip);
  
  var output = document.getElementById('fileList-'+ip);

  output.innerHTML = '<ul>';
  for (var i = 0; i < input.files.length; ++i) {
    output.innerHTML += '<li>' + input.files.item(i).name + '</li>';
  }
  output.innerHTML += '</ul>';
}
</script>
@endsection