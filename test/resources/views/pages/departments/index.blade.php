<?php
include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')
@section('main_content')

    {!! Html::style('plugins/datatables_new/jquery.dataTables.min.css') !!}
    {!! Html::style('plugins/datatables_new/rowReorder.dataTables.min.css') !!}
    {!! Html::script('plugins/datatables_new/jquery.dataTables.min.js') !!}
    {!! Html::script('plugins/datatables_new/dataTables.rowReorder.min.js') !!}
    {!! Html::script('plugins/datatables_new/jquery.dataTables.rowReordering.js') !!}

<style type="text/css">
    @media(max-width: 517px){
        .box{
            overflow-x:scroll;
        }
    }
    @media(max-width: 388px){
        div#documentGroupDTsub_wrapper {
            overflow-x: scroll;
        }
    }
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
</style>

<section class="content-header">
    <div class="col-sm-8">
        <span style="float:left;">
            <strong>
                @if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{trans('language.departments')}} @endif
            </strong> &nbsp;
        </span>
        <span style="float:left;">
            <?php
                $user_permission=Auth::user()->user_permission;
            ?>
            @if(stristr($user_permission,"add") && Auth::user()->user_role != Session::get('user_role_private_user'))
                <a href="" data-toggle="modal" data-target="#dTAddModal">
                    <button class="btn btn-block btn-info btn-flat newbtn">{{trans('language.add new')}}<i class="fa fa-plus"></i></button>
                </a>
            @endif
        </span>
    </div>
    <div class="col-sm-4">
       <!--  <ol class="breadcrumb">
            <li><a href="{{url('/home')}}"><i class="fa fa-dashboard"></i> {{trans('language.home')}}</a></li>
            <li class="active">@if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{trans('language.departments')}} @endif</li>
       </ol> -->
    </div>
</section>
<section class="content content-sty" id="msg" style="display:none;"></section>
<section class="content content-sty" id="msg_add" style="display:none;"></section>

@if(Session::has('flash_message_edit'))
<section class="content content-sty" id="spl-wrn">        
    <div class="alert alert-sty {{ Session::get('alert-class', 'alert-info') }} ">{{ Session::get('flash_message_edit') }}</div>        
</section>
@endif
<div class="preloader col-sm-12" style="text-align: center; margin-top: 50px; display: none;" id="bs">
      <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
      <span class="sr-only">Loading...</span>
</div> 
<section class="content" id="shw">
</section>
<!-- User add Form -->
<div class="modal fade" id="dTAddModal" data-backdrop="false" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="dGAddModal" >

   <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                   @if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{trans('language.departments')}} @endif
                   <small>- {{trans('language.add new')}}</small>
               </h4>
            </div>
            <div class="modal-body">            
                <!-- form start -->
                {!! Form::open(array('url'=> array('departmentSave','0'), 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'deptAddForm', 'id'=> 'deptAddForm','data-parsley-validate'=> '')) !!}            
                    <div class="form-group">
                        <label for="Department Name" class="col-sm-3 control-label">@if(Session::get('settings_dept_name')) {{ Session::get('settings_dept_name') }}@else {{trans('language.department name')}} @endif: <span class="compulsary">*</span></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="name" required="" data-parsley-required-message='@if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{trans('language.department')}} @endif {{trans('language.is_required')}}' data-parsley-trigger='change focusout' data-parsley-maxlength="{{ trans('language.max_length') }}" autofocus="autofocus" placeholder="@if(Session::get('settings_dept_name')){{ Session::get('settings_dept_name')}}@else{{trans('language.department name')}}@endif" title="@if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }} {{trans('language.length_others')}}@else {{trans('language.department')}} {{trans('language.length_others')}}@endif" name="name">   
                            <div id="dp">
                                <span id="dp_wrn" style="display:none;">
                                    <i class="fa fa-refresh fa-spin fa-1x fa-fw"></i>
                                    <span class="">Please wait...</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label(trans("language.description").': ', '', array('class'=> 'col-sm-3 control-label'))!!}
                        <div class="col-sm-8">
                            {!! Form:: 
                            textarea(
                            'description', '', 
                            array( 
                            'class'                  => 'form-control', 
                            'id'                     => 'description', 
                            'title'                  => trans('language.length_description'),
                            'data-parsley-maxlength' => trans('language.max_length_description'), 
                            'placeholder'            => 'Description'                            
                            )
                            ) 
                            !!}
                            <span class="dms_error">{{$errors->first('description')}}</span>
                        </div>
                    </div>

                
                    <div class="form-group">
                        <label class="col-sm-3 control-label"></label>
                        <div class="col-sm-8" style="text-align:right;">
                            {!!Form::submit(trans('language.save'), array('class' => 'btn btn-primary', 'id' => 'save')) !!} &nbsp;&nbsp;
                            {!!Form::button(trans('language.close'), array('class' => 'btn btn-primary btn-danger', 'id' => 'cn', 'data-dismiss'=> 'modal', 'aria-hidden'=> 'true')) !!}
                            <!-- </a> -->
                        </div>
                    </div><!-- /.col -->
                {!! Form::close() !!}
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- User add formend -->


<!-- User edit Form -->
<div class="modal fade" id="dTEditModal" data-backdrop="false" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- User edit form end -->

<script>

    $(function ($) {
        load();

        $("#save").prop( "disabled", true );
        //Ajax form
        var options = { 
            target:        '#msg_add',   // target element(s) to be updated with server response 
            beforeSubmit:  showRequest,  // pre-submit callback 
            success:       showResponse,  // post-submit callback     
            complete:      showStatus
        }; 
        // bind form using 'ajaxForm' 
        $('#deptAddForm').ajaxForm(options);

        //Add Modal reset
        $("#cn").click(function(){
            $("#deptAddForm")[0].reset();
            $('#deptAddForm').parsley().reset();
            $("#dp").text('');
        });
        $('#spl-wrn').delay(5000).slideUp('slow');

        //Duplicate entry
        $("#name").change(function(){
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: 'post',
                url: '{{URL('departmentDuplication')}}',
                dataType: 'json',
                data: {_token: CSRF_TOKEN, name: $("#name").val() },
                timeout: 50000,
                beforeSend: function() {
                    $("#dp_wrn").show();
                    $("#save").attr("disabled", true);
                },
                success: function(data, status){
                   
                    if(data != 1)
                    {
                        $("#dp").html(data);
                        $("#save").prop( "disabled", true );
                    }
                    else
                    {
                        $("#dp").text('');
                        $("#dp-inner").text('');  
                        $("#save").prop( "disabled", false );                     
                    }
                },
                error: function(jqXHR, textStatus, errorThrown){
                    console.log(jqXHR);    
                    console.log(textStatus);    
                    console.log(errorThrown);    
                },
                complete: function() {
                    $("#dp_wrn").hide();
                    //$("#save").attr("disabled", false);
                }
            });
        });
    });
    
    function load()
    {
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            type: 'get',
            url: '{{URL('departmentsList')}}',
            dataType: 'html',
            data: {_token: CSRF_TOKEN},
            timeout: 50000,
            beforeSend: function() {
                $("#bs").show();
            },
            success: function(data, status){
                $("#shw").html(data);               
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

    // pre-submit callback 
    function showRequest(formData, jqForm, options) { 
        // formData is an array; here we use $.param to convert it to a string to display it 
        // but the form plugin does this for you automatically when it submits the data 
        var queryString = $.param(formData); 

        // jqForm is a jQuery object encapsulating the form element.  To access the 
        // DOM element for the form do this: 
        // var formElement = jqForm[0]; 

        /*swal('About to submit: \n\n' + queryString); */
        $("#bs").show();

        // here we could return false to prevent the form from being submitted; 
        // returning anything other than false will allow the form submit to continue 
        return true; 
    } 

    // post-submit callback 
    function showResponse(responseText, statusText, xhr, $form)  {
        $('#cn').click();
        $("#deptAddForm")[0].reset();
        
        // setTimeout(function () {
        //         $("#msg_add").slideDown(1000);
        // }, 200);
        // setTimeout(function () {
        //     $('#msg_add').slideUp("slow");
        // }, 5000);
        // load();
        var text = $("#msg_add").text();
        swal({title: "", text: text, type: 
        "success"}).then(function(){ 
           location.reload();
           }
        ); 
    }
    function showStatus()
    {
        $("#bs").hide();
    }

    function del(id,docGroup)
    {   

        swal({
              title: "{{trans('language.confirm_delete_single')}}'" + docGroup + "' ?",
              text: "{{trans('language.Swal_not_revert')}}",
              type: "{{trans('language.Swal_warning')}}",
              showCancelButton: true
            }).then(function (result) {
                if(result){
                    // Success
                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    $('#spl-wrn').html('');//hide unwanted message box
                    $.ajax({
                        type: 'post',
                        url: '{{URL('departmentDelete')}}',
                        dataType: 'json',
                        data: {_token: CSRF_TOKEN, id:id,docGroup:docGroup},
                        timeout: 50000,
                        beforeSend: function() {
                            $("#bs").show();
                        },
                        success: function(data, status){ 
                             load();
                            // setTimeout(function () {
                            //     $('#msg').css('display','block');
                            //     $("#msg").html('<div class="alert alert-success alert-sty">'+ data +'</div>');
                            //     $("#msg").slideDown(1000);
                            // }, 200);
                            // setTimeout(function () {
                            //     $('#msg').slideUp("slow");
                            // }, 5000);
                            var text = data;
                              swal({title: "", text: text, type: 
                              ""}).then(function(){ 
                                 location.reload();
                                 }
                              );
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
          });

    }

    function duplication()
    {
        var val= $("#name_edi").val();
        var editVal= $("#edit_val").val();
        var oldVal= $("#oldVal").val();
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            type: 'post',
            url: '{{URL('departmentDuplication')}}',
            dataType: 'json',
            data: {_token: CSRF_TOKEN, name: val, editId:editVal, oldVal:oldVal },
            timeout: 50000,
            beforeSend: function() {
                $("#dp_wrn_edi").show();
                $("#saveEdi").attr("disabled", true);
            },
            success: function(data, status){

                if(data != 1)
                {
                    $("#dp_edi").html(data);
                    $("#name_edi").val('');
                }
                else
                {
                    $("#dp").text('');
                    $("#dp-inner").text('');                       
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                console.log(jqXHR);    
                console.log(textStatus);    
                console.log(errorThrown);    
            },
            complete: function() {
                $("#dp_wrn_edi").hide();
                $("#saveEdi").attr("disabled", false);
            }
        });
    }
// select all desired input fields and attach tooltips to them
      $("#deptAddForm :input").tooltip({
 
      // place tooltip on the right edge
      position: "center",
 
      // a little tweaking of the position
      offset: [-2, 10],
 
      // use the built-in fadeIn/fadeOut effect
      effect: "fade",
 
      // custom opacity setting
      opacity: 0.7
 
      }); 
</script>    
{!! Html::script('js/parsley.min.js') !!}  
{!! Html::script('js/jquery.form.js') !!}   
@endsection
