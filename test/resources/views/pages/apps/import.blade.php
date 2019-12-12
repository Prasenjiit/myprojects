@extends('layouts.app')
<?php
include (public_path()."/storage/includes/lang1.en.php" );
?>
@section('main_content')
{!! Html::script('js/dropzone.js') !!}
{!! Html::style('css/dropzone.min.css') !!}
<section class="content-header">
  <div class="row">
    <div class="col-sm-8">
      <span style="float:left;">
        <strong>{{ Lang::get('apps.apps') }}</strong> &nbsp;
        <a href="{{url('apps')}}" class="btn btn-primary">Back</a>
      </span>
    </div>
    <div class="col-sm-4">
      <ol class="breadcrumb">
          <li><a href="{{url('/home')}}"><i class="fa fa-dashboard"></i> {{ Lang::get('language.home') }}</a></li>
          <li class="active">{{ Lang::get('apps.apps') }}</li>
      </ol>
    </div>
    <div class="col-sm-12">
            <p style="font-size:12px; color:#999;">- {{$language['import_msg']}}</p>       
        </div> 
  </div>
</section> 
@if(Session::has('data'))
<section class="content content-sty" id="spl-wrn">        
  <div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
   {{ Session::get('data') }}
  </div>       
</section>
@endif
@if(Session::has('err'))
<section class="content content-sty" id="spl-err">
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <h4><i class="icon fa fa-ban"></i> Alert!</h4>
    {{ Session::get('err') }}
  </div>                
</section>
@endif
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">{{trans("apps.import_records")}}</h3>
        </div>
        <form action="{{ URL::to('app_import_parse') }}" class="form-horizontal" method="post" enctype="multipart/form-data" id="import-form" data-parsley-validate =''>{{ csrf_field() }}
          <div class="box-body">
            <div class="form-group">
              <label for="inputEmail3" class="col-sm-2 control-label">{{trans("apps.apps")}}: <span class="compulsary">*</span></label>

              <div class="col-sm-8">
                <select class="form-control" id="appselect" name="appselect" required="" >
                  <option value="0">{{trans("apps.select_an_app")}}</option>
                  @foreach($appsApp as $val)
                  <option value="{{$val->document_type_id}}">{{ucfirst($val->document_type_name)}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="form-group" id="msg" style="display: none;">
              <label class="col-sm-2 control-label"></label>
              <div class="col-sm-6">
                  <a id="import-button-sample" title="Get a sample file in csv format" style="cursor: pointer;">{{$language['download_sample']}}</a>
                  &nbsp;&nbsp;
                  <span class="text-warning">{{trans("apps.sample_text")}}</span>
              </div>
            </div>
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
              <label class="col-sm-2"></label>
                <div class="col-sm-6">
                    <div class="preloader" style="display: none;" >
                        <i class="fa fa-spinner fa-pulse fa-fw fa-lg"></i>{{trans("apps.preloader_msg")}}
                        <span class="sr-only">{{trans("apps.preloader_msg")}}</span>
                    </div>    
                </div>
            </div>
            <input type="hidden" name="hidd-app-selected" id="hidd-app-selected" value="0">
            <input type="hidden" name="hidd-app-selected-name" id="hidd-app-selected-name" value="">
            <div class="form-group">
              <label class="col-sm-2 control-label"></label>
              <div class="col-sm-8" style="text-align:right;">
                <input type="submit" class="btn btn-primary" id="real-import-button" value="{{$language['parse_csv']}}" title="{{$language['parse_csv']}}">
                <a href="{{url('apps')}}" class="btn btn-danger">{{Lang::get('language.cancel')}}</a>
              </div>
            </div>
          </div>
          </div>
          </div>
          <!-- /.box-footer -->
        </form>
      </div>
    </div>
  </div>
</section>
<script type="text/javascript">
$( document ).ready(function() {
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
                    if($(this).hasClass("dz-remove") || $(this).hasClass("dms-dz-remove"))
                    { 
                      //avoid remove link on dropzone
                    }
                    else
                    {
                      //add 'dz-remove-confirm' for all a tags
                      $(this).addClass("dms-dz-remove-confirm")
                    }

                 });
                  //browser back button press alert
                  /*if (window.history && window.history.pushState) 
                  {

                    $(window).on('popstate', function() {
                      var hashLocation = location.hash;
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
    function upload_delete_confirm()
    {
      
    }

      //app change
    $("#appselect").change(function(){
        $("#importbody").show();
        //$('#msg').show();
        var selectet_type=$(this).val()
        var selectet_type_name=$("#appselect option:selected").text();
        if(selectet_type==0)
        {
            swal("{{trans('apps.choose_app')}}");
            $("#importbody").hide();
            //$('#msg').hide();
            return false;
        }
        $("#hidd-app-selected").val(selectet_type);
        $("#hidd-app-selected-name").val(selectet_type_name);
        
    });
      //validate upload document
    $("#import-form").submit(function( event ) {
        var check=$("#hidd_file").val();
        $(".preloader").css("display", "block");
        if(check==""||check==null)
        {
            $("#file_error").text("<?php echo $language['no_file'];?>");
            $(".preloader").css("display", "none");
            return false;
        }
    });
    //download sample
    $('#import-button-sample').click(function(e){
            //e.preventDefault();
            var skillsSelect = document.getElementById("appselect");
            var selectedText = skillsSelect.options[skillsSelect.selectedIndex].text;
            $(".preloader").css("display", "block");
            var CSRF_TOKEN =$('meta[name="csrf-token"]').attr('content');
            var selectet_type_import=$("#hidd-app-selected").val();
            $.ajax({
                type:'post',
                url: '{{URL('appSample')}}',
                dataType: "json",
                data: {appid:selectet_type_import,selectedText:selectedText,_token:CSRF_TOKEN},
                success:function(response){
                  if(response){
                    var filename = response.filename;
                    var htmlData = "Sample data is ready. Please <a href="+filename+">click here</a> to download";
                    $(".preloader").css("display", "none");
                    swal({
                        html: htmlData,
                        showCancelButton: true,
                        cancelButtonText: "Close",
                        showConfirmButton: false
                    });
                }
                   
                }
            });
        });

    });

</script>
@endsection