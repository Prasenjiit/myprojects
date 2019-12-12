@extends('layouts.app')
@section('main_content')

{!! Html::script('js/parsley.min.js') !!}  
{!! Html::script('js/jquery.form.js') !!}   
{!! Html::style('css/font-awesome.min.css') !!} 
{!! Html::style('css/ionicons.min.css') !!} 
{!! Html::style('plugins/datatables/dataTables.bootstrap.css') !!}
<section class="content-header">
    <h1>
        Document Type Column
        <small>Document Type Column List</small>
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="{{url('/home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Document Type Column Management</li>
    </ol> -->
</section>
<section class="content content-sty" id="msg"></section>
<section class="content content-sty" id="msg_add"></section>
@if(Session::has('flash_message_edit'))
<section class="content content-sty" id="spl-wrn">        
    <div class="alert alert-sty {{ Session::get('alert-class', 'alert-info') }} ">{{ Session::get('flash_message_edit') }}</div>        
</section>
@endif
<section class="content" id="shw">
    <div id="bs" style="display:none; text-align:center;">
        <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
        <span class="sr-only">Loading...</span>
    </div>
</section>


<!-- User add Form -->
<div class="modal fade" id="dTAddModal" data-backdrop="false" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="dGAddModal" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                   Document Type Column Management
                   <small>- Add New Document Type Column</small>
               </h4>
            </div>
            <div class="modal-body">

                <!-- form start -->
                {!! Form::open(array('url'=> array('documentTypeColumnSave','0'), 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'documentTypeAddForm', 'id'=> 'documentTypeAddForm','data-parsley-validate'=> '')) !!}            

                    <div class="form-group">
                        {!! Form::label('Doc Type :', '', array('class'=> 'col-sm-4 control-label'))!!}
                        <div class="col-sm-8">
                            <select name="doctypeid" id="doctypeid" class="form-control" data-parsley-required-message="Document type name is required" required >
                               <option value="">Select a doc type</option>
                               <?php
                               foreach ($docType as $key => $dType) {
                               ?>
                                    <option value="<?php echo $dType->document_types_id; ?>"><?php echo $dType->document_types_name;?></option>
                                <?php
                            }
                            ?>
                            </select>                          
                        </div>
                    </div>
                   
                    <div class="form-group">
                        {!! Form::label('Doc Type Column Name :', '', array('class'=> 'col-sm-4 control-label'))!!}
                        <div class="col-sm-8">
                            {!! Form:: 
                            text('colname','', 
                            array( 
                            'class'=> 'form-control', 
                            'id'=> 'name', 
                            'title'=> 'Column Name', 
                            'placeholder'=> 'Column Name',
                            'required'               => '',
                            'data-parsley-required-message' => 'Document type column name is required',
                            'data-parsley-trigger'          => 'change focusout',
                            )
                            ) 
                            !!}      
                            <div id="dp">
                                <span id="dp_wrn" style="display:none;">
                                    <i class="fa fa-refresh fa-spin fa-1x fa-fw"></i>
                                    <span class="">Please wait...</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('Doc Type Column Type :', '', array('class'=> 'col-sm-4 control-label'))!!}
                        <div class="col-sm-8">
                            <select name="coltype" id="coltype" class="form-control" data-parsley-required-message="Document type column type is required" required >
                                <option value="">Select a type</option>
                                <option value="interger">Interger</option>
                                <option value="string">String</option>
                            </select>      
                            
                        </div>
                    </div>

                
                    <div class="form-group">
                        <label class="col-sm-4 control-label"></label>
                        <div class="col-sm-8">
                            {!!Form::submit('Save', array('class' => 'btn btn-primary', 'id' => 'save')) !!} &nbsp;&nbsp;

                            {!!Form::button('Cancel', array('class' => 'btn btn-primary btn-danger', 'id' => 'cn', 'data-dismiss'=> 'modal', 'aria-hidden'=> 'true')) !!}
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
        //Ajax form
        var options = { 
            target:        '#msg_add',   // target element(s) to be updated with server response 
            beforeSubmit:  showRequest,  // pre-submit callback 
            success:       showResponse,  // post-submit callback     
            complete:      showStatus
        }; 
        // bind form using 'ajaxForm' 
        $('#documentTypeAddForm').ajaxForm(options);

        //Add Modal reset
        $("#cn").click(function(){
            $("#documentTypeAddForm")[0].reset();
            $('#documentTypeAddForm').parsley().reset();
            $("#dp").text('');
        });
        $('#spl-wrn').delay(5000).slideUp('slow');

        //Duplicate entry
        $("#name").change(function(){
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: 'post',
                url: '{{URL('documentTypeColumnDuplication')}}',
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
                        $("#name").val('');
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
                    $("#dp_wrn").hide();
                    $("#save").attr("disabled", false);
                }
            });
        });

    });
    
    function load()
    { 
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            type: 'get',
            url: '{{URL('documentTypeColumnList')}}',
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

        /*alert('About to submit: \n\n' + queryString); */
        $("#bs").show();

        // here we could return false to prevent the form from being submitted; 
        // returning anything other than false will allow the form submit to continue 
        return true; 
    } 

    // post-submit callback 
    function showResponse(responseText, statusText, xhr, $form)  {
        $('#cn').click();
        $("#documentTypeAddForm")[0].reset();
        
        setTimeout(function () {
                $("#msg_add").slideDown(1000);
        }, 200);
        setTimeout(function () {
            $('#msg_add').slideUp("slow");
        }, 5000);
        load(); 
    }
    function showStatus()
    {
        $("#bs").hide();
    }

    function del(id)
    {
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            type: 'post',
            url: '{{URL('documentTypeColumnDelete')}}',
            dataType: 'json',
            data: {_token: CSRF_TOKEN, id:id},
            timeout: 50000,
            beforeSend: function() {
                $("#bs").show();
            },
            success: function(data, status){ 
                load();
                setTimeout(function () {
                    $("#msg").html('<div class="alert alert-success alert-sty">'+ data +'</div>');
                    $("#msg").slideDown(1000);
                }, 200);
                setTimeout(function () {
                    $('#msg').slideUp("slow");
                }, 5000);
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
    function duplication()
    {
        var val= $("#colname_edi").val();
        var editVal= $("#edit_val").val();
        var oldVal= $("#oldVal").val();
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: 'post',
                url: '{{URL('documentTypeColumnDuplication')}}',
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
                        $("#colname_edi").val('');
                    }
                    else
                    {
                        $("#dp_edi").text('');
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


</script>   
@endsection