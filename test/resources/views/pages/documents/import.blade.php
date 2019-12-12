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
{!! Html::script('js/dropzone.js') !!}  
<!--{!! Html::script('js/build.min.js') !!} 
{!! Html::style('css/build.min.css') !!} -->
{!! Html::style('css/fastselect.min.css') !!} 
{!! Html::script('js/fastselect.standalone.js') !!}   
{!! Html::style('css/dropzone.min.css') !!}

<style>
    .file-node{
        color:#337ab7;
    }
    .fstElement { font-size: 1.2em; }
    .fstToggleBtn { min-width: 16.5em; }

    .submitBtn { display: none; }

    .fstMultipleMode { display: block; }
    .fstMultipleMode .fstControls { width: 100%; }
    .modal-body {
        min-height: 470px !important;
    }
    .content-header {
        padding: 7px 15px 0px !important;
        height: 55px;
    }
    /*responsive*/
    @media(max-width:848px){
        .breadcrumb{
            display:none;
        }
    }
    @media(max-width:397px){
        #small{
            display:none;
        }
    }
    @media(max-width:767px){
        .content-header {
            padding: 27px 15px 0px !important;
        }
    }
    .content-sty {
        margin-top: 0px !important;
    }
    /*responsive*/
</style>

<!-- Content Wrapper. Contains page content -->
    <section class="content-header">
        <div class="row">
            <div class="col-sm-8">
                <span style="float:left;">
                    <strong>
                        {{$language['import data']}}
                    </strong>
                    &nbsp;
                    <a href="{{url('listview')}}?view=import" class="btn btn-primary">Back</a>
                </span>
            </div>
           <!--  <div class="col-sm-4">
                <ol class="breadcrumb">
                    <li><a href="#"><i class="fa fa-dashboard"></i> {{$language['home']}}</a></li>
                    <li class="active">{{$language['import data']}}</li>
                </ol>  
            </div> -->
            <div class="col-sm-12">
                <p style="font-size:12px; color:#999;">- {{$language['import_msg']}}</p>       
            </div> 
        </div>           
    </section>
    @if(Session::has('data'))
    <section class="content content-sty" id="spl-wrn">
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>        
        <div class="alert alert-sty {{ Session::get('alert-class', 'alert-success') }} ">{{ Session::get('data') }}</div> 
    </div>       
    </section>
    @endif

    @if(Session::has('wrn'))
    <section class="content content-sty" id="spl-err-wrn">
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <div class="alert alert-sty {{ Session::get('alert-class', 'alert-error') }} ">{{ Session::get('data') }}</div> 
        </div>       
    </section>
    @endif
    @if(Session::has('err'))
        <section class="content content-sty" id="spl-err">
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>        
            <div class="alert alert-sty {{ Session::get('alert-class', 'alert-error') }} ">{{ Session::get('err') }}</div>
        </div>
        </section>
    @endif 
    @if(Session::has('error'))
        <section class="content content-sty" id="spl-err">
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>        
            <div class="alert alert-sty {{ Session::get('alert-class', 'alert-error') }} ">{{ Session::get('error') }} <a href="{{ URL::to('exportError') }}" title="Download the list of mismatched data in csv format">{{$language['import_mismatch']}}</a></div>
        </div>
        </section>
    @endif        
    <!-- Main content -->
    <section class="content">  
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Import Data</h3>
                    </div>  
                    <form action="{{ URL::to('import_parse') }}" class="form-horizontal" method="post" enctype="multipart/form-data" id="import-form" data-parsley-validate =''>{{ csrf_field() }}   
                        <div class="box-body">
                            <div class="form-group">
                                <div class="col-sm-2"></div>
                                <div class="col-sm-8">
                                    <div style="text-align:center;">
                                        <a href="{{ URL::to('exportSample') }}" id="import-button" title="Download the Master Data in csv format">{{$language['download_data']}}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Document Type" class="col-sm-2 control-label">{{$language['document types']}}: <span class="compulsary">*</span></label>
                                <div class="col-sm-8">
                                    <select class="form-control" id="typeselect" name="typeselect" required="" >
                                    <option value="0">Select a document type</option>
                                    @foreach($docType as $val)
                                    <option value="{{$val->document_type_id}}">{{$val->document_type_name}}</option>
                                    @endforeach
                                    </select>
                                    <input type="hidden" name="hidd-type-selected" id="hidd-type-selected" value="0">
                                    <input type="hidden" name="hidd-type-selected-name" id="hidd-type-selected-name" value="">
                                </div>
                            </div>
                
                            <!-- <div class="form-group">
                                <label class="col-sm-2 control-label"></label>
                                <div class="col-sm-6">
                                    <a id="import-button-sample" title="Get a sample file in csv format" style="display: none;cursor: pointer;">{{$language['download_sample']}}</a>
                                    &nbsp;&nbsp;
                                    <span style="display: none;color: #777;" id='msg'>Please use this Sample File to input your data and import</span>
                                </div>
                            </div> -->
                
                            <div id="importbody" style="display: none;"> 
                                <div class="form-group">
                                    <label for="Import File" class="col-sm-2 control-label">{{$language['import_file']}}: <span class="compulsary">*</span></label>
                                    <div class="col-sm-8">
                                        <div class="dropzone" id="dropzoneFileUpload"></div>
                                        <input type="hidden" name="hidd_file" id="hidd_file" value="">
                                        <span class="compulsary" id="file_error"></span>  <br/>
                                        <span class="text-warning">{{$language['file_extension_csv']}},</span>
                                        <span class="text-warning">{{$language['max upload msg']}}
                                        {{$language['max_upload_size']}}</span>          
                                    </div>
                                </div>  
                                <div class="form-group" style="height: 20px;">
                                    <div class="col-sm-8">
                                        <div class="preloader" style="display: none;" >
                                            <i class="fa fa-spinner fa-pulse fa-fw fa-lg"></i>Please wait, operations are in progress...
                                            <span class="sr-only">Please wait, operations are in progress...</span>
                                        </div>    
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"></label>
                                    <div class="col-sm-8" style="text-align:right;">
                                        <input type="hidden" name="hidd_chunk_size" value="1000">
                                        <input id="backbuttonstate" type="hidden" value="0" />
                                        <input type="submit" class="btn btn-primary" id="real-import-button" value="{{$language['parse_csv']}}" title="{{$language['parse_csv']}}">
                                        <!-- <input type="button" value="{{$language['cancel']}}" name="cancel" class="btn btn-primary btn-danger" id = "cn" onclick=window.location="listview?view=import"> -->
                                        <a href="{{url('listview')}}?view=import" class="btn btn-danger" id = "cn">{{Lang::get('language.cancel')}}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

<script type="text/javascript">
$(function(){
  $('#import-form').submit(function() {
    $(".preloader").css("display", "block");
    return true;
  });
});
$( document ).ready(function() {
// $('#spl-err-wrn').delay(8000).slideUp('slow');
// $('#spl-wrn').delay(8000).slideUp('slow');
// $('#spl-err').delay(8000).slideUp('slow');


$(".preloader").css("display", "none");
var max_file_size = "{{$language['max_upload_size']}}";
max_file_size = max_file_size.slice(0, -2); //remove M from string
Dropzone.autoDiscover = false;
var baseUrl = "{{ url('/') }}";
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var myDropzone = new Dropzone("div#dropzoneFileUpload", {
        type:'post',
        params: {_token:CSRF_TOKEN},
        url: baseUrl+"/dropzone/uploadFiles2",
        paramName: 'file',
        maxFiles: 1,
        clickable: true,
        maxFilesize: max_file_size,
        uploadMultiple: false,
        addRemoveLinks: true,
        success:function(file,response,data){
            if(response==1)
            {   
                $(file.previewElement).find('.dz-error-message').text('Invalid file extension.').css('opacity','1').css('display','block');
                $(file.previewElement).find('.dz-error-mark').css('opacity','1').css('display','block');
            }
            else
            {
              var file_upload = response;
              $("#hidd_file").val(response);
              if(file_upload)
              {
                $("a").each(function(){
                    if($(this).hasClass("dz-remove") || $(this).hasClass("dms-dz-remove") || $(this).hasClass("jstree-anchor"))
                    { 
                      //avoid remove link on dropzone 
                    }
                    else
                    {
                      //add 'dz-remove-confirm' for all a tags
                      $(this).addClass("dms-dz-remove-confirm")
                    }

                 });
                //browser back alert
                /*if (window.history && window.history.pushState) 
                  {

                    $(window).on('popstate', function() {
                      var hashLocation = location.hash;
                      console.log(hashLocation);
                      var hashSplit = hashLocation.split("#!/");
                      var hashName = hashSplit[1];

                      if (hashName !== '') {
                        var hash = window.location.hash;
                        if (hash === '') {
                          if(confirm("Changes have been done to this form. Do you want to abandon the changes?")){
                              //if('yes'): delete the upload file.
                              {
                                var hidd_file_upload = $("#hidd_file").val();
                                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                                $.ajax({
                                    type:'post',
                                    url: '{{URL('removeDocumentOnNavigation')}}',
                                    data: {upload_file:hidd_file_upload,_token:CSRF_TOKEN},
                                    success: function(data,response){
                                        if(data){
                                            $("#hidd_file").val(null);
                                            window.history.go(-1);
                                        }    
                                    }
                                });
                              }
                          }
                          else{
                              //if('no') : no action
                              return false;
                          }
                        }
                      }
                    });

                    window.history.pushState('forward', null, './#forward');
                  }*/
              }
            }
            $("#file_error").text("");
        },
    });
        //Remove file from dropzone
      myDropzone.on("removedfile", function(file) {
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var hidd_file_upload = $("#hidd_file").val();
        $.ajax({
            type:'post',
            url: '{{URL('removeDocumentTemp')}}',
            data: {_token:CSRF_TOKEN,upload_file:hidd_file_upload},
            success: function(data,response){
                if(data){
                    swal("{{$language['success_remove_document']}}");
                    $("#hidd_file").val(null);
                }    
            }
        })
    });
      //delete upload file when navigate to other links.
    $(document).on('click','.dms-dz-remove-confirm',function () {
        if(confirm("Changes have been done to this form. Do you want to abandon the changes?")){
            //if('yes'): delete the upload file.
            {
              var hidd_file_upload = $("#hidd_file").val();
              var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
              $.ajax({
                  type:'post',
                  url: '{{URL('removeDocumentOnNavigation')}}',
                  data: {upload_file:hidd_file_upload,_token:CSRF_TOKEN},
                  success: function(data,response){
                      if(data){
                          $("#hidd_file").val(null);
                      }    
                  }
              });
            }
        }
        else{
            //if('no') : no action
            return false;
        }
        });
        //validate upload document
$("#import-form").submit(function( event ) {
    var check=$("#hidd_file").val();
    if(check==""||check==null)
    {
        $("#file_error").text("<?php echo $language['no_file'];?>");
        $(".preloader").css("display", "none");
        return false;
    }
});

    $("#typeselect").change(function(){
        $("#importbody").show();
        $("#import-button-sample").show();
        $('#msg').show();
        var selectet_type=$(this).val()
        var selectet_type_name=$("#typeselect option:selected").text();
        if(selectet_type==0)
        {
            swal("Please choose a Document type.");
            $("#importbody").hide();
            $("#import-button-sample").hide();
            $('msg').hide();
            return false;
        }
        $("#hidd-type-selected").val(selectet_type);
        $("#hidd-type-selected-name").val(selectet_type_name);
        
    });
    $("#typeselect").change(function(){
        
        var CSRF_TOKEN =$('meta[name="csrf-token"]').attr('content');
        var selectet_type_import=$("#hidd-type-selected").val();
        $.ajax({
            url:'{{URL('selectTypeimport')}}',
            data:{_token:CSRF_TOKEN,type:selectet_type_import},
            type:"post",
            success:function(data,response)
            {
                //swal(data);
            }
        });
    });
    
        var exportedRecords = 0;
        var chunkSize = 5000; // as per query performance
        //submit form
        $('#import-button-sample').click(function(e){
            //e.preventDefault();
            var skillsSelect = document.getElementById("typeselect");
            var selectedText = skillsSelect.options[skillsSelect.selectedIndex].text;
            $(".preloader").css("display", "block");
            var CSRF_TOKEN =$('meta[name="csrf-token"]').attr('content');
            var selectet_type_import=$("#hidd-type-selected").val();
            $.ajax({
                type:'post',
                url: '{{URL('RecordscountImport')}}',
                dataType: "json",
                data: {doctypeid:selectet_type_import,_token:CSRF_TOKEN},
                success:function(response){
                   console.log(response);
                   var totalRecords = response;
                   if(totalRecords == 0) 
                   {
                    $(".preloader").css("display", "none");
                    swal("No documents found");
                    return false;
                   }
                   for( start=0; start < totalRecords; start += chunkSize)
                   {
                        //console.log(start);
                        chunkCSVImport(start, chunkSize,totalRecords,selectedText);
                    }
                }
            });
        });

        function chunkCSVImport(start,chunkSize,totalRecords,selectedText){
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var selectet_type_import=$("#hidd-type-selected").val();
            var url = '{{URL('chunkImport')}}';
            //console.log(url);
            jQuery.ajax({
                type : "post",
                dataType : "json",
                url: url,
                data: {_token:CSRF_TOKEN,doctypeid:selectet_type_import,start:start,chunkSize:chunkSize},
                success: function(response) {
                    console.log(response);
                    exportedRecords += chunkSize;
                    downloadfile(totalRecords,exportedRecords,selectedText);
                }
            });
        }
        function downloadfile(totalRecords,exportedRecords,selectedText)
        {   
            if(exportedRecords>=totalRecords){
                $(".preloader").css("display", "none");
                //for direct download file csv
                //window.location.href = base_url+'/storage/Export/DMS_export.csv';
                //from popup download
                var today = new Date();
                var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
                var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
                var datetime = date+' '+time;
                var htmlData = "Sample data is ready. Please <a href="+base_url+"/storage/Export/fileeazy_sample_data.csv download='fileeazy_import"+selectedText+"_"+datetime+".csv'>click here</a> to download";

                swal({
                    html: htmlData,
                    showCancelButton: true,
                    cancelButtonText: "Close",
                    showConfirmButton: false
                });
            }
        }
    });
</script>
@endsection