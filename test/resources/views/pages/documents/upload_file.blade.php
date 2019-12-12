<?php
include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')
@section('main_content')

{!! Html::script('js/parsley.min.js') !!}  
{!! Html::script('js/jquery.form.js') !!}   
{!! Html::style('css/font-awesome.min.css') !!} 
{!! Html::style('css/ionicons.min.css') !!} 
{!! Html::script('js/dropzone.js') !!}  
{!! Html::style('css/fastselect.min.css') !!} 
{!! Html::script('js/fastselect.standalone.js') !!}   
{!! Html::style('css/dropzone.min.css') !!}

{!! Html::style('css/sweetalert2.min.css') !!} 
{!! Html::script('js/sweetalert2.min.js') !!} 

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
    .fstElement{
        width: 100%;
    }
    .fstControls{
        width: 100% !important;
    }

</style>

<section class="content-header">
    <div class="col-sm-8">
        <strong>
            {{$language['bulk_import_doc']}}
        </strong>
    </div>
    <!-- <div class="col-sm-4">
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> {{$language['home']}}</a></li>
            <li class="active">{{$language['bulk_import_doc']}}</li>
        </ol>            
    </div> -->
</section>
       
<!-- Main content -->
<section class="content"> 
<?php

$string= null;
$status= 'unpublished';

?>            
    <div class="modal-body">    
        <div class="form-horizontal">
            <div class="form-group" id="importbody">
                <label for="{{$language['import_file']}}:" class="col-sm-2 control-label">{{$language['import_file']}}: <span class="compulsary">*</span></label>
                <div class="col-sm-8">
                    <div class="dropzone" id="dropzoneFileUpload" style="overflow: auto;height: auto;">
                    </div>
                    <input type="hidden" id="hiddUp" name="hiddUp">
                    <p class="dms_error compulsary" id="file_error"></p>
                    @if(Session::get('settings_file_extensions'))
                    <span class="text-warning">{{$language['allowed_file_extensions']}}: {{Session::get('settings_file_extensions')}}</span> 
                    @endif  
                    @if(Session::get("settings_ftp_upload") != 1)
                    <span class="text-warning">{{$language['max upload msg']}}
                    {{$language['max_upload_size']}}</span>
                    @endif  
                    <br>
                    <div id="vud" style="margin-bottom: 5px;">
                        <!-- edit uploaded files (hide not necessory) -->
                        <!-- <a href="{{URL::route('listview')}}?view=import" title="Edit the uploaded documents" id="uploadEdit" style="display:none" class="btn btn-primary">
                        {{$language['edit_upload']}}</a> -->   
                        <!-- <a class="btn btn-primary" href="{{ url('listview') }}?view=import" title="{{$language['view upload documents']}}">{{$language['view upload documents']}}</a> -->
                        <a class="btn btn-danger" title="{{$language['delete_all']}}" id="remove_all" style="display:none">{{$language['delete_all']}}</a>  
                    </div>  
                    
                </div>
            </div>
            {!! Form::open(array('url'=> array('documentsSaveAll',$status,$string), 'files'=>true, 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'documentManagementAddForm', 'id'=> 'documentManagementAddForm','data-parsley-validate'=> '')) !!}
            <div class="form-group">

            <input type="hidden" name="hidd_file" id="hidd_file" value="">
            <input type="hidden" name="hidd_file_size" id="hidd_file_size" value="">
            <input type="hidden" name="hidd_last_inserted" id="hidd_last_inserted" value="">
                    <label for="Doc Types" class="col-sm-2 control-label">{{$language['document types']}}: <span class="compulsary">*</span></label>
                    <div class="col-sm-8">
                        <select name="doctypeid" id="doctypeid" class="multipleSelect form-control" >
                        <!-- <option value="">Select a Document Type</option> -->
                            <?php
                            $i = 0;
                            foreach ($docType as $key => $dType) {
                                ?>
                                <option value="<?php echo $dType->document_type_id; ?>" <?php if($i==0){echo 'selected';} ?>><?php echo $dType->document_type_name;?></option>
                                <?php
                                $i++;
                            }
                            ?>
                        </select>   
                        <label class="dms_error compulsary" id="doctypeerror" style="display: none;">Please select document type</label> 
              
                    </div>

            </div>
            <div class="form-group">
                <label for="department" class="col-sm-2 control-label">@if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{$language['department']}} @endif: <span class="compulsary">*</span></label>
                <div class="col-sm-8">
                    <select name="departments[]" id="departments" class="multipleSelect" multiple>
                        <?php
                        foreach ($deptApp as $key => $val) {
                            ?>
                            <option value="<?php echo $val->department_id; ?>"><?php echo $val->department_name;?></option>
                            <?php
                        }
                        ?>
                    </select>                      
                    <span class="dms_error">{{$errors->first('docname')}}</span>
                </div>
            </div> 
            <div class="form-group">
                {!! Form::label($language['stack'].':', '', array('class'=> 'col-sm-2 control-label'))!!}
                <div class="col-sm-8">
                    <select name="stack[]" id="stack" class="multipleSelect" multiple>
                        <?php
                        foreach ($stack as $key => $val) {
                            ?>
                            <option value="<?php echo $val->stack_id; ?>"><?php echo $val->stack_name;?></option>
                            <?php
                        }
                        ?>
                    </select>                      
                    <span class="dms_error">{{$errors->first('docname')}}</span>
                </div>
            </div>   
            <div class="form-group">
                {!! Form::label($language['tag category'].':', '', array('class'=> 'col-sm-2 control-label'))!!}
                <div class="col-sm-8">
                    <select name="tagscate[]" id="tagwrdcat" class="multipleSelect" multiple>
                        <?php
                        foreach ($tagsCateg as $key => $tagcat) {
                            ?>
                            <option value="<?php echo $tagcat->tagwords_category_id; ?>"><?php echo $tagcat->tagwords_category_name;?></option>
                            <?php
                        }
                        ?>
                    </select>   
                   
                    
                    <span class="dms_error">{{$errors->first('docname')}}</span>
                </div>
            </div>   

            <div class="form-group">
                {!! Form::label($language['tag words'].':', '', array('class'=> 'col-sm-2 control-label'))!!}
                <div class="col-sm-8">
                    <div id="keywd">
                        <select name="keywords" id="keywords" class="form-control">
                            <option value="">{{$language['pls_select']}}</option>
                        </select>
                    </div>
                    <div id="reskeywrds"></div>
                    
                    <span class="dms_error">{{$errors->first('docname')}}</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">{{$language['expir_date']}}: </label>
                <div class="col-sm-4">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control" id="doc_exp_date" name="document_expiry_date" placeholder="YYYY-MM-DD">
                    </div><!-- /.input group -->
                </div>
                <div class="col-sm-6">
                {!! Form::checkbox('doc_exp_chk',1, true, ['id'=> 'doc_exp_chk']) !!} {{$language['no expiry']}}
                </div>
            </div>

            <div class="form-group">
                <label for="Folde path" class="col-sm-2 control-label">{{$language['upload folder']}}: </label>
                <div class="col-sm-8">
                    <div>
                        <input type="text" id="up_folder" name="up_folder" class="form-control" readonly placeholder="Choose a Document folder">
                        <input type="hidden" name="hidd_folder_id" id="hidd_folder_id" value="{{Session::get('SESS_parentIdd')}}">
                    </div>
                    <div class="box">
            
                    <!-- <span id='change_Folder' style="cursor:pointer;color: #3c8dbc;">Change Upload Folder</span> -->
                    <div id="tree-container" style="padding:5px 0px;overflow: auto;max-height: 100px;"></div>
                    
                    </div>              
                </div>
            </div>   

            <div class="form-group">
                <label class="col-sm-2 control-label"></label>
                <div class="col-sm-8">
                    <p style="font-size:12px; color:#999;">The files have been added to a temporary table. The next step is to update these records with missing information before you are able to publish the data. You can update those information by either editing invidiual records or importing data in bulk using .csv file.</p>
                    <input type="submit" value="Save & Import CSV" name="save" class="btn btn-primary" id= "save" style="">
                    <input type="submit" value="Save & Import CSV Later" name="saveonly" class="btn btn-primary" id= "save1" style="">
                    <!-- <a href="{{url('listview')}}?view=import" value="Cancel" name="cancel" class="btn btn-primary btn-danger" id = "cn">{{$language['cancel']}}</a> -->
                    <a action="action" href="{{url('listview')}}?view=import" class="btn btn-primary btn-danger" id = "cn">{{$language['cancel']}}</a>
                </div>
            </div> 
            {!! Form::close() !!}
        </div>
    </div>
</section>

<script type="text/javascript">
var rand_name = [];
var size = [];
var last_inserted = [];
var max_file_size = "{{$language['max_upload_size']}}";
max_file_size = max_file_size.slice(0, -2); //remove M from string
@php if(Session::get("settings_ftp_upload") == 1)
{
@endphp
 var  max_file_size = 100000;
@php
}
@endphp
$('#spl-wrn').delay(8000).slideUp('slow');
    Dropzone.autoDiscover = false;
        var baseUrl = "{{ url('/') }}";
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        Dropzone.autoDiscover = false;
        var par_id=document.getElementById('hiddUp').value;
        var myDropzone = new Dropzone("div#dropzoneFileUpload", {
            type:'post',
            url: baseUrl + "/dropzone/uploadFiles",
            init: function() {
            this.on("sending", function(file, xhr, formData) {
              formData.append("doctypeid", $('#doctypeid').val());
              formData.append("_token", "{{ csrf_token() }}");
              console.log(formData)
            });
          },
            params: {module:'bulkImport',par_id:par_id},
            addRemoveLinks: true,
            maxFilesize: max_file_size,
            autoProcessQueue: true,
            parallelUploads: 1,
            success:function(file,response,data)
            {
                $("#uploadEdit").show();
                if(response=='invalidname')
                {   
                
                $(file.previewElement).find('.dz-error-message').text("{{trans('documents.invalidname')}}").css('opacity','1').css('display','block').css('top','90px');
                $(file.previewElement).find('.dz-error-mark').css('opacity','1').css('display','block');
                $(file.previewElement).find('.dz-filename').css('font-weight', 'bold');
                }  
               else if(response=='invalid')
                    {   
                        $(file.previewElement).find('.dz-error-message').text('Invalid file extension').css('opacity','1').css('display','block').css('top','90px');
                        $(file.previewElement).find('.dz-error-mark').css('opacity','1').css('display','block');
                    }
                else if(response=='tokenMismatch')
                    {   
                        $(file.previewElement).find('.dz-error-message').text('Error, Try again ').css('opacity','1').css('display','block').css('top','90px');
                        $(file.previewElement).find('.dz-error-mark').css('opacity','1').css('display','block');
                        $(file.previewElement).find('.dz-filename').css('font-weight', 'bold');
                    }
                else if(response=='exists')
                    {   
                        $(file.previewElement).find('.dz-error-message').text('Document already exists').css('opacity','1').css('display','block').css('top','90px');
                        $(file.previewElement).find('.dz-error-mark').css('opacity','1').css('display','block');
                    }
                else if(response=='ftpinvalid')
                {   
                    $("#file_error").html('Error in ftp connection. Make sure the ftp credentials is correct or try changing it to http in the settings.');
                }
                else
                {
                    
                    var obj=response;
                    rand_name.push(obj.fileRandName);
                    size.push(obj.size);
                    last_inserted.push(obj.last_inserted);
                    $("#hidd_file").val(rand_name);
                    $("#hidd_file_size").val(size);
                    $("#hidd_last_inserted").val(last_inserted);
                    var file_upload = rand_name;
    
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
                                        url: '{{URL('removeDocumentTemp')}}',
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
                      var docidval = $('#doctypeid').val();
                        if(docidval){
                            $("#save").prop('disabled', false);
                            $("#save1").prop('disabled', false);
                            
                        }
                }

            },
    });

        myDropzone.on("complete", function (file,response,data) {
        $('#remove_all').css('display','-webkit-inline-box');// show delete button
        var upload_path=$("#uploadFile").text();
        var real_path=upload_path.split(":").pop();
        var check=file.xhr.response;
        if(check=='invalid' || check=='exists')
        {
            swal("{{$language['err_upload']}}");
        }
        if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0 && check!=1 && check!=2) 
        {   $("#uploadEdit").show();

            // Hide popup window for upload edit
            /*swal({
              title: "Documents have been uploaded to ' " + real_path.trim() + " ' Do you want to edit them?",
              text: "{{$language['Swal_not_revert']}}",
              type: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes'
            }).then(function (result) {
                if(result){
                    // Success

                    window.location="uploadFileEdit";

                }
            });*/
        }
        });
        
        //Remove file from dropzone
        myDropzone.on("removedfile", function(file,response,data) {
            var obj=$.parseJSON(file.xhr.response);
            var remove_file = obj.fileRandName;
            $.ajax({
                type:'post',
                url: '{{URL('removeDocument')}}',
                data: '_token='+CSRF_TOKEN+'&file_name='+remove_file+'&file_name='+remove_file,
                success: function(response){
                    var object=response;
                    
                    rand_name = rand_name.filter(e => e !== object.fileRandName);
                    size = size.filter(e => e !== object.size);
                    last_inserted = last_inserted.filter(e => e !== object.last_inserted);

                    $("#hidd_file").val(rand_name);
                    $("#hidd_file_size").val(size);
                    $("#hidd_last_inserted").val(last_inserted);
                }
            });

    });

    // Remove all files
    $('#remove_all').click(function(){
        var files = myDropzone.files;
        if(files.length == '0'){
            swal('There is no files to be deleted.');
        }else{
            // Delete
            swal({
                  title: '{{$language['Swal_are_you_sure']}}',
                  text: "{{$language['Swal_not_revert']}}",
                  type: "{{$language['Swal_warning']}}",
                  showCancelButton: true
                }).then(function (result) {
                    if(result){
                         myDropzone.removeAllFiles();
                         $('#remove_all').css('display','none');// hide after delete button
                    }
                    swal(
                    '{{$language['Swal_deleted']}}'
                    )
                });
            }
       
    });

    //delete upload file when navigate to other links.
    $(document).on('click','.dms-dz-remove-confirm',function () {
        if(confirm("Changes have been done to this form. Do you want to abandon the changes?")){
            //if('yes'): delete the upload file.
            {
              var files = myDropzone.files;
              myDropzone.removeAllFiles();
              $('#remove_all').css('display','none');// hide after delete button
            }
        }
        else{
            //if('no') : no action
            return false;
        }
        });

    $(function ($) {

        $("#save").prop('disabled', true);
        $("#save1").prop('disabled', true);
        //Initilize date picker
        var d           = new Date();
        var currentYear = d.getFullYear();
        var newDate     = currentYear+10;
        var date        = '12/31/'+newDate;

         // disable previous year
        var dd = d.getDate();
        var mm = d.getMonth()+1; 
        if(dd<10) {
            dd = '0'+dd
        } 

        if(mm<10) {
            mm = '0'+mm
        } 
        today = currentYear+'-'+mm+'-'+dd;
        $('#doc_exp_date').daterangepicker({
                singleDatePicker: true,
                "drops": "up",
                minDate: today,
                maxDate: moment(date),
                showDropdowns: true
            });
        //
        //Expire checking
        if($("#doc_exp_chk").prop('checked') == true){
            $("#doc_exp_date").attr("disabled", true);
        }  

        $("#doctypeid").change(function(){
            var docidval = $("#doctypeid").val();
            if(docidval){
                $("#save").prop('disabled', false);
                $("#save1").prop('disabled', false);
                $("#doctypeerror").css("display", "none");
            }else{
                $("#save").prop('disabled', true);
                $("#save1").prop('disabled', true);
                $("#doctypeerror").css("display", "block");
            }
        });

        $('#doc_exp_chk').click(function() {
            var ckb = $("#doc_exp_chk").is(':checked');
            if(ckb == false) {
                $("#doc_exp_date").attr("disabled", false);
                $('#doc_exp_date').attr('required','required');
                $('#doc_exp_date').attr('data-parsley-required-message','Expiry date is required');
            } else {
                $("#doc_exp_date").attr("disabled", true);
                $('#doc_exp_date').attr('required',false);
                $('#doc_exp_date').val('');
            }           
        }); //

        $('.multipleSelect').fastselect();
        //Duplicate entry
        $('#spl-wrn').delay(5000).slideUp('slow');
        // Onchange function to get the sublist of the document type
        $("#tagwrdcat").change(function(){
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
             if($("#tagwrdcat").val()==null){
                var value = 0; 
            }else{
                var value = $("#tagwrdcat").val();
            }
            $.ajax({
                type: 'post',
                url: '{{URL('documentsTagwords')}}',
                data: {_token: CSRF_TOKEN, tagcatid: value },
                timeout: 50000,
                beforeSend: function() {
                    
                },
                success: function(data, status){
                    if(data){
                        $("#keywd").hide();
                        $("#reskeywrds").html(data);
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
        });
    });
$(document).ready(function(){
    $('#tree-container')
                .jstree({
                    'core' : {
                        'data' : {
                            'url' : 'response?operation=get_node',
                            'data' : function (node) {
                                return { 'id' : node.id };
                            }
                        },
                        'check_callback' : true,
                        'themes' : {
                            'responsive' : false
                        }
                    },
                    types: {
                        "child": {
                          "icon" : "glyphicon glyphicon-folder-open file-node"
                        }                        
                    },  
                    'force_text' : true,
                    'plugins' : ['state','dnd','contextmenu','wholerow','types']
                })
                .on('delete_node.jstree', function (e, data)
                {
                    var folder_name=data.node.text.replace(/ *\([^)]*\) */g, "");

                    swal({
                      title: "{{$language['confirm_delete_single']}} folder '" + folder_name + "' ?",
                      type: "{{$language['Swal_warning']}}",
                      showCancelButton: true
                    }).then(function (result) {
                        if(result){
                            // Success
                            $.ajax({
                            type : 'GET',
                            url  : 'response?operation=delete_node',
                            data : {id:data.node.id},
                            complete: function(response) {
                            if(response.responseText=='true')
                            {
                            //swal("{{$language['success_del_folder']}}");
                            window.location.reload();
                            }
                            else if(response.responseText=='null')
                            {
                            //swal("{{$language['folder_check']}}");
                            window.location.reload();
                            }    
                            else if(response.responseText=='root')
                            {
                            //swal("{{$language['root_delete']}}");
                            window.location.reload();
                            }
                            else if(response.responseText=='temp')
                            {
                            //swal("{{$language['temp_folder_check']}}");
                            window.location.reload();
                            }
                            }
                            });
                        }
                        swal(
                        'Success'
                        )
                    },function (dismiss) {
                      // dismiss can be 'cancel', 'overlay',
                      if (dismiss === 'cancel') {
                        window.location.reload();
                      }
                    }); 
                })
                .on('create_node.jstree', function (e, data) {
                    $.get('response?operation=create_node', { 'id' : data.node.parent, 'position' : data.position, 'text' : data.node.text })
                        .done(function (d) {
                            data.instance.set_id(data.node, d.id);
                        })
                        .fail(function () {
                            data.instance.refresh();
                        });
                })
                .on('rename_node.jstree', function (e, data) {
                    $.get('response?operation=rename_node', { 'id' : data.node.id, 'text' : data.text })
                        .fail(function () {
                            data.instance.refresh();
                        });
                })
                .on('move_node.jstree', function (e, data) {
                    $.get('response?operation=move_node', { 'id' : data.node.id, 'parent' : data.parent, 'position' : data.position })
                        .fail(function () {
                            data.instance.refresh();
                        });
                })
                .on('copy_node.jstree', function (e, data) {
                    $.get('response?operation=copy_node', { 'id' : data.original.id, 'parent' : data.parent, 'position' : data.position })
                        .always(function () {
                            data.instance.refresh();
                        });
                })                        
                });
                $('#tree-container').on('ready.jstree', function() {
                $("#tree-container").jstree("open_all");          
                });
                $("#tree-container").on('changed.jstree', function (e, data) {
                    var path = data.instance.get_path(data.node,'/');
                    var dataString='lang=' + path;
                    var n= $("#tree-container").jstree(true).get_selected();
                    $('#hidd_folder_id').val(n);
                    $('#up_folder').val(path);
                }); 

            //validate upload document
            $( "#save" ).click(function( event ) {
            var check=$("#hidd_file").val();
            if(check==""||check==null)
                {
                    $(window).scrollTop($('#importbody').offset().top);
                    $("#file_error").text("No new file has been uploaded");
                    return false;
                }else{
                    $("#file_error").text("");
                }  
            });   

            $( "#save1" ).click(function( event ) {
            var check=$("#hidd_file").val();
            if(check==""||check==null)
                {
                    $(window).scrollTop($('#importbody').offset().top);
                    $("#file_error").text("No new file has been uploaded");
                    return false;
                }else{
                    $("#file_error").text("");
                }  
            });        
</script>
{!! Html::script('dist/jstree.min.js') !!}
{!! Html::style('dist/style.min.css') !!}
@endsection