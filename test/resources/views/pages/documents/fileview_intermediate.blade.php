<?php
include (public_path()."/storage/includes/lang1.en.php" );
$user_permission=Auth::user()->user_permission;
?>
@extends('layouts.app')
@section('main_content')
<section class="content-header">
    <div class="col-sm-8">
        <span style="float:left;">
            <strong>{{$language['documents']}}</strong> &nbsp;
        </span>&nbsp;
        <span style="float:left;">
        <!--back button-->
            <a onclick="window.history.go(-1);">
                <button class="btn btn-block btn-info btn-flat newbtn" style="line-height:13px !important;">{{$language['back']}}</button>
            </a> 
        </span>
    </div>
    <div class="col-sm-4">
        <!-- <ol class="breadcrumb">
            <li><a href="{{url('/home')}}"><i class="fa fa-dashboard"></i> {{$language['home']}}</a></li>
            <li class="active">{{$language['documents']}}</li>
        </ol> -->
    </div>
</section>
<section class="content">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body">
                  <div class="alert alert-danger alert-dismissible">
                    <!-- <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> -->
                    <h4><i class="icon fa fa-ban"></i> Alert!</h4>
                    Document <b>{{$document_name}}</b> is encrypted by <b>{{$encrypted_by}}</b> on <b>{{$encrypted_on}}</b></br>
                    <?php
                    $user_permission=Auth::user()->user_permission;
                        if(stristr($user_permission,"decrypt"))
                        {
                          echo $language['decrypt_please']."<a style='cursor:pointer'; id='decrypt_doc'>click here</a>";
                        }
                        else
                        {
                          echo $language['decrypt_no_permission']."</br>";
                        }
                    ?>
                  </div>
                </div>
            <!-- /.box-body -->
            </div>
          <!-- /.box -->
        </div>
</section>
<script type="text/javascript">
//decrypt file
$('body').on('click','#decrypt_doc',function()
{
    var docid      = '{{@$document_id}}';
    var docname = '{{@$document_name}}';
    var doc_file_name = '{{@$file_name}}';
    var action = 'd';//Encrypt
    if(docname=="")
    {
        docname="{{$language['document']}}";
    }
    var view = "{{Session::get('view')}}";
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var ajaxURL_decrypt = 'documentDecrypt';
    swal({
          title: "{{$language['confirm_decrypt_single']}}'" + docname + "' ?",
          text: "{{$language['Swal_not_revert']}}",
          type: "{{$language['Swal_warning']}}",
          showCancelButton: true
        }).then(function (result) {
        if(result)
        {
            $.ajax({
                type: 'post',
                url: ajaxURL_decrypt,
                data: {_token: CSRF_TOKEN,docid:docid,view:view,action:action,file:doc_file_name,docname:docname},
                timeout: 50000,
                success: function(data){     
                    
                    if(data==1)
                    {
                        swal({
                        title: "{{$language['document']}} '"+docname+"' {{$language['success_decrypt']}}",
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ok'
                        }).then(function (result) {
                            if(result){
                                // Success move to fileviewer page
                                window.location.reload();
                            }
                        });
                    }
                    else
                    {
                        // data=0
                        swal(data);
                    }
                },
                error: function(errorThrown){
                    console.log(errorThrown);    
                }
            }); 
        } 
    });
});
</script>
<style type="text/css">
.content-wrapper {
        min-height: 757px !important;
    }
</style>
@endsection