<?php
include (public_path()."/storage/includes/lang1.en.php" );
$user_permission=Auth::user()->user_permission;
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

{!! Html::style('plugins/datatables_new/jquery.dataTables.min.css') !!}

<style type="text/css">
@media(max-width: 991px){
    .content-header {
        padding: 22px 15px 0 !important;
        height: 48px;
    }
    .content-header>h1 {
        margin-top: -19px;
    }
}

@media (max-width: 767px){
    .content-header {
        padding: 15px 0px 0 !important;
        height: 20px;
    }
}

/*<--mobile view table-->*/
@media(max-width:500px){
    .box{
        overflow-x: auto;
    }
}

/*<--smtp and ftp form-->*/
.input-right{
    padding-left: 0px;
}
.input-left{
    padding-right: 0px;
}
.enable-smtp{
    padding-left: 0px;
}
.toptech-info{
    font-size:12px; 
    color:#999;
}

/*<--For mobile view SMTP-->*/
@media(max-width:767px){
   .input-right{
    padding-left: 15px !important;
    }
    .input-left{
        padding-right: 15px !important;
    } 
    .enable-smtp{
        padding-left: 15px !important;
    }
}

</style>
 <?php    $user_permission=Auth::user()->user_permission;
?>
    
<section class="content-header">
    <div class="col-sm-8">
        <span style="float:left;">
            <strong>{{trans('backup_restore.bckprstr')}}</strong> &nbsp;
        </span>
    </div>
    <div class="col-sm-4">
        <!-- <ol class="breadcrumb">
            <li><a href="{{url('/home')}}"><i class="fa fa-dashboard"></i> {{trans('language.home')}}</a></li>
            <li class="active">{{trans('language.settings')}}</li>
        </ol> -->
    </div>
</section>
<!--Success flash message-->
@if(Session::has('status'))
<section class="content content-sty" id="spl-wrn"> 
    <div class="alert alert-success" id="hide-div">
        <p><strong>Success!</strong> {{ Session::get('status') }}</p>
    </div>
</section>
@endif
<!-- ajax message -->


<!--Checking view permission-->
@if(Auth::user()->user_role == Session::get('user_role_super_admin'))   
<!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- {!! Form::open(array('url'=> array('BackupProcess'), 'files'=>true, 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'backupForm', 'id'=> 'backupForm','data-parsley-validate'=> '')) !!}  -->
                <div class="col-md-5">
                    <!-- company form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{trans('backup_restore.backup')}}</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse" title="Collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div><!-- /.box-header -->  
                        <!-- section back up -->
                        <div class="box-footer" style="">
                            <div class="modal-body">    
                                <!-- radio button with 3 options -->                            
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <input type="radio" name="bckup" value="1" checked="checked">&nbsp;{{trans('backup_restore.bckupdatabase')}}&nbsp;
                                        <input type="radio" name="bckup" value="2">&nbsp;{{trans('backup_restore.bckupdoc')}}&nbsp;
                                        <input type="radio" name="bckup" value="3">&nbsp;{{trans('backup_restore.bckupdocdb')}}&nbsp;                                  
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12" style="text-align:right;">
                                        <input type="button" value="Backup" id="backup" name="save" class="btn btn-primary">
                                    </div>
                                </div>  
                                <div class="form-group"> 
                                    <div class="col-sm-12" id="backupmsg" style="display: none;"></div>
                                    <div class="col-sm-12" id="preloader" style="display: none;">
                                        <i class="fa fa-spinner fa-spin fa-lg fa-fw"></i>
                                        <span id="loadmsg">Loading...</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12" style="text-align:right;">
                                        <div id="errmsg"></div>
                                    </div>
                                </div>                             
                            </div>
                        </div>
                    </div> 
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{trans('backup_restore.restore_ext')}}</h3>
                            <p style="padding-left:15px; font-size:12px; color:#999;">{{trans('backup_restore.rstr_desc') }}</p>
                        </div>
                         <!-- form start -->
                        <div class="box-footer" style="">   
                            <!-- form start -->
                            <!-- {!! Form::open(array('url'=> array('restoreProcess'), 'files'=>true, 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'backupForm', 'id'=> 'backupForm','data-parsley-validate'=> '')) !!}  -->
                                <div class="form-group" id="focus">
                                    <label for="UploadDocument" class="col-sm-12 control-label">{{trans('backup_restore.restore_file')}}: <span class="compulsary">*</span></label>                
                                    <div class="col-sm-12">
                                        <div class="dropzone" id="dropzoneFileUpload"></div>
                                        <input type="hidden" name="hidd_file" id="hidd_file" value="">
                                        <input type="hidden" name="hidd_file_size" id="hidd_file_size" value="">
                                        <span class="dms_error compulsary" id="file_error"></span>       
                                    </div>
                                    <div class="col-sm-12">
                                    <!-- connection checking -->
                                    @if(Session::get('settings_restore_file_extensions')) <!-- allowed extensions coming from tbl_settings table -->
                                        <span class="text-warning">{{$language['allowed_file_extensions']}}:
                                        {{Session::get('settings_restore_file_extensions')}}</span> 
                                    @endif
                                    <!-- upload size -->
                                    @if(Session::get("settings_ftp_upload") != 1)
                                        <span class="text-warning">{{$language['max upload msg']}} {{$language['max_upload_size']}}</span> 
                                    @endif
                                   </div>  
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-12" style="text-align:right;">
                                        <input type="button" id="restore" value="{{ trans('backup_restore.restore') }}" name="restore" class="btn btn-primary">
                                    </div>
                                </div><!-- /.col -->
                                <div class="form-group"> 
                                    <div class="col-sm-12" id="rstrmsg" style="display: none;"></div>
                                    <div class="col-sm-12" id="rstrpreloader" style="display: none;">
                                        <i class="fa fa-spinner fa-spin fa-lg fa-fw"></i>
                                        <span id="rstrloadmsg">Loading...</span>
                                    </div>
                                </div>
                            <!-- {!! Form::close() !!}  --> 
                        </div>                   
                    </div>
                </div><!--// General form end-->
            <!-- {!! Form::close() !!}   -->

            <div class="col-md-7">                    
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ trans('backup_restore.restore_int') }}</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" title="Collapse"><i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div><!-- /.box-header --> 
                    <!-- Restore Section -->
                    <div class="box-footer" style="">  
                        <!-- form start -->                            
                        <div class="form-group">
                            <div class="col-sm-12">
                                <table border="1" id="example" class="table table-bordered table-striped dataTable hover">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="ckbCheckAll" /></th>
                                            <th>{{trans('backup_restore.file_name')}}</th>
                                            <th>{{trans('backup_restore.file_size')}}</th>                         
                                            <th>{{trans('language.created date')}}</th>                       
                                            <th width="18%" nowrap="nowrap">{{trans('backup_restore.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody> 
                                        
                                        @foreach ($data as $key => $val)
                                            <tr>
                                                <td nowrap="nowrap"><input name="checkbox[]" type="checkbox" value="<?php echo $val['filename']; ?>" id="chk_<?php echo $val['filename'] ?>" class="checkBoxClass"></td>
                                                <td><?php echo $val['filename']; ?></td>
                                                <td><?php echo $val['size']; ?></td>
                                                <td><?php echo $val['date']; ?></td>
                                                <td><?php echo $val['actions']; ?></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-12">
                                <input type="hidden" name="hidd_count" id="hidd_count" value="0">
                                <span id="deletetag" style="float: right; margin-top: 10px;" value="{{trans('language.delete')}}" name="delete" class="btn btn-primary">{{trans('language.delete')}}</span> &nbsp;&nbsp;
                            </div>
                        </div>
                    </div>
                    
                </div> 
            </div>        
    </div>  
</section>
@else
    <section class="content"><div class="alert alert-danger alert-sty">{{trans('language.dont_hav_permission')}}</div></section>
@endif

<style type="text/css">
    .control-label{
        text-align: left !important;
    }   
    .dataTables_scrollBody{
        overflow-x: scroll;
        overflow-y: scroll;
    } 
    #prog[data-progress] {
        position: relative;
    }
    #prog[data-progress]:before {
        position: absolute;
        content: attr(data-progress);
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%); /* this sets the text to be exactly in the middle of the parent element */
    }
    .progress{
        text-indent: initial;
    }

</style>
{!! Html::script('plugins/datatables_new/jquery.dataTables.min.js') !!}
{!! Html::script('plugins/datatables_new/dataTables.fixedHeader.min.js') !!}

<script type="text/javascript">
    $(document).ready(function() {
        $('#example').DataTable({            
            "responsive": true,
            "scrollX": false,
            "scrollY": 350,
            "columnDefs": [{
                "targets": [ 0, 4 ],
                "sortable": false,                
            },
            {targets: 'no-sort', orderable: false}],
            "order": [[ 3, "desc" ]],
        });

        $("#ckbCheckAll").click(function () {
            $(".checkBoxClass").prop('checked', $(this).prop('checked'));
        });

        var countChecked = function() {
            var n = $( ".checkBoxClass:checked" ).length;
            document.getElementById('hidd_count').value=n;
        };
        countChecked();
        $( "input[type=checkbox]" ).on( "click", countChecked );


    });

    var max_file_size = "{{$language['max_upload_size']}}";
    max_file_size = max_file_size.slice(0, -2); //remove M from string
    @php if(Session::get("settings_ftp_upload") == 1)
    {
    @endphp
        var  max_file_size = 100000;
    @php
    }
    @endphp

    Dropzone.autoDiscover = false;
    var baseUrl = "{{ url('/') }}";
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var myDropzone = new Dropzone("div#dropzoneFileUpload", {
        type:'post',
        params: {_token:CSRF_TOKEN,module:'restoredb'},
        url: baseUrl+"/dropzone/bckpUploadFiles",
        paramName: 'file',
        clickable: true,
        maxFiles: 1,
        maxFilesize: max_file_size,
        uploadMultiple: false,
        addRemoveLinks: true,
        beforeSend: function() {
        },
        success:function(file,response,data){
            if(response=='invalid'){   
                $(file.previewElement).find('.dz-error-mark').css('opacity','1').css('display','block');
                swal('{{$language['upload error']}}');
            }else if(response=='exists'){   
                    $(file.previewElement).find('.dz-error-mark').css('opacity','1').css('display','block');
                    swal('{{$language['doc_already_exist']}}');
            }else if(response=='ftpinvalid'){   
                $("#file_error").html('Error in ftp connection. Make sure the ftp credentials is correct or try changing it to http in the settings.');
            }else{              
                var obj=response;
                var rand_name=obj.fileRandName;
                var size = obj.size;
                $("#hidd_file").val(rand_name);
                $("#hidd_file_size").val(size);               
            }      
        },
    });

    //Remove file from dropzone
    myDropzone.on("removedfile", function(file,response,data) {
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        //var remove_file=file.xhr.response;
        var remove_file = $("#hidd_file").val();
        $.ajax({
            type:'post',
            url: '{{URL('removebackupDocument')}}',
            data: {_token:CSRF_TOKEN,file:remove_file,module:'restoredb'},
            success: function(data,response){
                if(data){
                    $("#hidd_file").val(null);
                    $("#hidd_file_size").val(null); 
                    swal('{{$language['success_remove_document']}}');
                }    
            }
        })
    });

    //Delete selected
    $("#deletetag").click(function()
    {
        if(document.getElementById('hidd_count').value==0)
        {
            swal("{{trans('documents.not select')}}");
            return false;
        }
        else
        {   
            swal({
                title: "{{trans('documents.confirm_delete_multiple')}}",
                text: "{{trans('language.Swal_not_revert')}}",
                type: "{{trans('language.Swal_warning')}}",
                showCancelButton: true
            }).then(function (result) {
            if(result)
            {
                // Success
                var arr = $('input:checkbox.checkBoxClass').filter(':checked').map(function () {
                return this.value;
                }).get();
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    type: 'post',
                    url: '{{URL('deleteSelectedbckup')}}',
                    data: {_token: CSRF_TOKEN,selected:arr},
                    timeout: 50000,
                    beforeSend: function() {
                        $("#bs").show();
                    },
                    success: function(data, status){
                        swal({
                            title: "{{trans('backup_restore.backup')}} '"+docname+"' {{$language['success_delete']}}",
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ok'
                            }).then(function (result) {
                                if(result){
                                    // Success
                                    window.location.reload();
                                }
                            });
                    },
                    error: function(jqXHR, textStatus, errorThrown){
                        console.log(jqXHR);    
                        console.log(textStatus);    
                        console.log(errorThrown);    
                    },
                    complete: function() {
                        $("#bs").hide();
                    }
                });
            }
            swal(
            "{{trans('language.Swal_deleted')}}"
            )
            });
        }   
    });


    //backup click function
    $("#backup").click(function(){
        //var filename = $("#hidd_file").val();
        var radioValue = $("input[name='bckup']:checked").val();       
        $('#preloader').css("display", "block");
        $('#loadmsg').html('Generating backup, please wait.');
        $.ajax({
            type: 'post',
            url: '{{URL('BackupProcess')}}',
            data: {_token: CSRF_TOKEN, bckup: radioValue },
            timeout: 50000,     
            beforeSend: function() {
            },
            success: function(data, status){
                $('#preloader').css("display", "none");
                if(data){
                    $('#backupmsg').css("display", "block");
                    $("#backupmsg").css("color", "green");
                    $('#backupmsg').html('Success! Backup created successfully');                    
                    window.setTimeout(function(){location.reload()},3000);
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

    //restore click function
    $("#restore").click(function(){
        var filename = $("#hidd_file").val();
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var extens = fileExtension = filename.replace(/^.*\./, '');     // USING JAVASCRIPT REGULAR EXPRESSIONS.
        if(document.getElementById('hidd_file').value==0)
        {
            swal("{{trans('backup_restore.pls_select')}}");
            return false;
        }      
        swal({
            title: "{{trans('backup_restore.restore_confirmation')}}",
            type: "{{$language['Swal_warning']}}",
            showCancelButton: true
        }).then(function (result) {
            if(result)
            {
                $('#rstrpreloader').css("display", "block");
                $('#rstrloadmsg').html('Restoring files, please wait.');
                $.ajax({
                    type: 'get',
                    url: '{{URL('restoreProcess')}}',
                    data: {_token: CSRF_TOKEN, restrfname: filename, extnsion: extens },
                    timeout: 50000,
                    beforeSend: function() {
                    },
                    success: function(data, status){
                        $('#rstrpreloader').css("display", "none");
                        $('#rstrmsg').css("display", "block");
                        if(data==1){
                            swal({
                            title: "{{trans('backup_restore.restore')}} '"+filename+"' {{trans('backup_restore.success_restore')}}",
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ok'
                            }).then(function (result) {
                                if(result){
                                    // Success
                                    window.location.reload();
                                }
                            });
                            // $("#rstrmsg").css("color", "green");
                            // $("#rstrmsg").html('Success! File restored successfully');
                        }else{
                            swal("Failed! File restore failed, try again");
                            // $("#rstrmsg").css("color", "red");
                            // $("#rstrmsg").html('Failed! File restore failed, try again');
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
        });
    });

    // restore internal backuped files from the list
    function restorefile(docname){
        //alert(docname);
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var extens = fileExtension = docname.replace(/^.*\./, '');     // USING JAVASCRIPT REGULAR EXPRESSIONS.
        swal({
            title: "{{trans('backup_restore.restore_confirmation')}}",
            type: "{{$language['Swal_warning']}}",
            showCancelButton: true
        }).then(function (result) {
            if(result)
            {
                $('#rstrpreloader').css("display", "block");
                $('#rstrloadmsg').html('Restoring files, please wait.');
                $.ajax({
                    type: 'get',
                    url: '{{URL('restoreProcess')}}',
                    data: {_token: CSRF_TOKEN, restrfname: docname, extnsion: extens },
                    timeout: 50000,
                    beforeSend: function() {
                    },
                    success: function(data, status){
                        $('#rstrpreloader').css("display", "none");
                        $('#rstrmsg').css("display", "block");
                        if(data==1){
                            $("#rstrmsg").css("color", "green");
                            $("#rstrmsg").html('Success! File restored successfully');
                        }else{
                            $("#rstrmsg").css("color", "red");
                            $("#rstrmsg").html('Failed! File restore failed, try again');
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
        });       
    }

    //Delete single document
    function del(docname)
    {   
        if(docname=="")
        {
            docname="{{trans('backup_restore.backup')}}";
        }
        swal({
              title: "{{$language['confirm_delete_single']}}'" + docname + "' ?",
              type: "{{$language['Swal_warning']}}",
              showCancelButton: true
            }).then(function (result) {
            if(result)
            {
                // Success
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    type: 'post',
                    url: 'backupDeleteZip',
                    data: {_token: CSRF_TOKEN,filename:docname},
                    
                    beforeSend: function() {
                    },
                    success: function(data, status)
                    {
                        // success
                        if(data==1)
                        {
                            swal({
                            title: "{{trans('backup_restore.backup')}} '"+docname+"' {{$language['success_delete']}}",
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ok'
                            }).then(function (result) {
                                if(result){
                                    // Success
                                    window.location.reload();
                                }
                            });
                        }
                        else
                        {
                            // data=0
                            swal("File not exists");
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
        });
    }


</script>
@endsection