<?php
include (public_path()."/storage/includes/lang1.en.php" );
?>
{!! Html::style('css/dropzone.min.css') !!}
{!! Html::script('plugins/simple-jquery-form-builder/js/sjfb-html-generator-edit.js') !!}
{!! Html::script('js/dropzone.js') !!}  
<input type="hidden" name="form_id" id="form_id" value="{{ $form_details->form_id }}">
<input type="hidden" name="loadformurl" id="loadformurl" value="{{ url('load_form')}}">
<div class="modal-content">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">×</span></button>
    <h4 class="modal-title">{{ucfirst(@$form_details->form_name)}} <span class="description"> - {{ucfirst(@$form_details->form_description)}}</span></h4>
  </div>
  <form id="sjfb-sample" method="POST" action="{{url('saveFormValues')}}" enctype="multipart/form-data">
  <div class="modal-body" style="max-height: 500px !important; overflow-y: auto;">
    <div id="sjfb-wrap">
        
            {!! csrf_field() !!}
            <input type="hidden" name="form_id" id="form_id" value="{{ $form_details->form_id }}">
            <input type="hidden" name="form_name" id="form_name" value="{{ $form_details->form_name }}">
            <input type="hidden" name="form_description" id="form_description" value="{{ $form_details->form_description }}">
            <div id="sjfb-fields">
            </div>
            
        <div id='div-append'>
        </div>

    </div>
  </div>
  <div class="modal-footer">
  <div class="col-sm-8">
    <input type="submit" class="btn btn-primary" id="save_form" value="Submit">
      &nbsp;
    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
  </div>
  <div class="preloader" style="float: left; margin-top: 5px; display: none;" >
      <i class="fa fa-spinner fa-pulse fa-fw fa-lg"></i>
      <span class="sr-only">Loading...</span>
  </div>
  </div>
  </form>
</div>
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
<script type="text/javascript">

function checkEmail(id)
{
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    var uniqueid = 'email-'+id;
    var str = $('#'+uniqueid).val();
    if(!re.test(str))
    {
      $('#wrong').text("Please enter a valid email address");
      $('#'+uniqueid).focus();
      $("#save_form").attr("disabled", true);
    }
    else{
      $('#wrong').text("");
      $("#save_form").attr("disabled", false);
    }
}
//On document ready
$(function(){
  $('#view_form').on('hidden.bs.modal', function () { 
    $('#content_form').html('');
    console.log('clear');
  });
var formID = $('#form_id').val();
var loadformurl = $('#loadformurl').val();
var action = 'add';
generateForm(formID,loadformurl,action);
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
<script type="text/javascript">
             $(document).ready(function(){

             });
</script>