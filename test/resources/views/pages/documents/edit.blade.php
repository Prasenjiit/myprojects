@extends('layouts.app')
@section('main_content')

{!! Html::script('js/parsley.min.js') !!}  
{!! Html::script('js/jquery.form.js') !!}   
{!! Html::style('css/font-awesome.min.css') !!} 
{!! Html::style('css/ionicons.min.css') !!} 
{!! Html::style('plugins/datatables/dataTables.bootstrap.css') !!}

<!--{!! Html::script('js/build.min.js') !!} 
{!! Html::style('css/build.min.css') !!} -->
{!! Html::style('css/fastselect.min.css') !!} 
{!! Html::script('js/fastselect.standalone.js') !!}   

<style>
    .fstElement { font-size: 1.2em; }
    .fstToggleBtn { min-width: 16.5em; }

    .submitBtn { display: none; }

    .fstMultipleMode { display: block; }
    .fstMultipleMode .fstControls { width: 100%; }
    .modal-body {
        min-height: 470px !important;
    }

</style>

<!-- Content Wrapper. Contains page content -->
<section class="content" id="shw">
    <section class="content-header">
        <h1>
            Documents
            <small>- Edit Document</small>
        </h1>
       <!--  <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Edit Document</li>
        </ol>    -->         
    </section>
    @if(Session::has('data'))
    <section class="content content-sty" id="spl-wrn">        
        <div class="alert alert-sty {{ Session::get('alert-class', 'alert-info') }} ">{{ Session::get('data') }}</div>        
    </section>
    @endif
    <!-- Main content -->
    <section class="content">    
        <div class="modal-body">    
            <!-- form start -->
            {!! Form::open(array('url'=> array('editDocumentPublished',$id), 'files'=>true, 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'documentManagementAddForm', 'id'=> 'documentManagementAddForm','data-parsley-validate'=> '')) !!}            
                <?php foreach ($dglist as $key => $val)
                
                $arr_type_id=explode(',', $val->document_type_id);
                $arr_dept_id=explode(',', $val->department_id);
                $arr_stack_id=explode(',', $val->stack_id);
                $arr_tag_id=explode(',', $val->document_tagwords);

                foreach ($taglist as $key => $tags) {
                    $arr_tagcat_id[]= $tags->tagwords_category_id;
                }
                if(empty($arr_tagcat_id))
                {
                    $arr_tagcat_id[]=0;
                }
                {?>
                <input type="hidden" name="hidd_doc" id="hidd_doc" value="{{$id}}">
                <input type="hidden" name="hidd_var" id="hidd_var" value="{{$val->document_tagwords}}">

                <div class="form-group">
                    <label for="Doc Types" class="col-sm-2 control-label">Doc Types: <span class="compulsary">*</span></label>
                    <div class="col-sm-8">
                        <select name="doctypeid[]" id="doctypeid" class="multipleSelect" multiple required="" data-parsley-required-message = 'Document type is required'>
                            <?php
                            foreach ($docType as $key => $dType) {
                            ?>
                            <option value="<?php echo $dType->document_type_id; ?>"<?php if(in_array($dType->document_type_id,$arr_type_id)){ echo "selected";}?>><?php echo $dType->document_type_name;?>
                            </option>
                            <?php
                            }
                            ?>
                        </select>   
              
                    </div>
                </div>
                
                <div class="form-group">
                <label for="Doc No" class="col-sm-2 control-label">Doc No: <span class="compulsary">*</span></label>
                <div class="col-sm-8">
                    {!! Form:: 
                    text('docno',$val->document_no, 
                    array( 
                    'class'=> 'form-control', 
                    'id'=> 'docno', 
                    'title'=> 'Document No', 
                    'placeholder'=> 'Document No',     
                    'required'               => '',                               
                    'data-parsley-required-message' => 'This field is required',                                    
                    )
                    ) 
                    !!}
                    <div id="dp">
                        <span id="dp_wrn" style="display:none;">
                            <i class="fa fa-refresh fa-spin fa-1x fa-fw"></i>
                            <span class="">Please wait...</span>
                        </span>
                    </div>
                    <span class="dms_error">{{$errors->first('docno')}}</span>              
                </div>

            </div>
            
            <div class="form-group">
                <label for="Doc Name" class="col-sm-2 control-label">Doc Name: <span class="compulsary">*</span></label>
                <div class="col-sm-8">
                    {!! Form:: 
                    text(
                    'docname', $val->document_name, 
                    array( 
                    'class'                  => 'form-control', 
                    'id'                     => 'docname', 
                    'title'                  => 'Document Name', 
                    'placeholder'            => 'Document Name',
                    'required'               => '',
                    'data-parsley-required-message' => 'This field is required',
                    )
                    ) 
                    !!}
                    <div id="dn">
                        <span id="dn_wrn" style="display:none;">
                            <i class="fa fa-refresh fa-spin fa-1x fa-fw"></i>
                            <span class="">Please wait...</span>
                        </span>
                    </div>
                    <span class="dms_error">{{$errors->first('docname')}}</span>
                </div>
            </div>   
            <div class="form-group">
                    <label for="Department" class="col-sm-2 control-label">@if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{$language['department']}} @endif: <span class="compulsary">*</span></label>
                    <div class="col-sm-8">
                        <select name="departmentid[]" id="departmentid" class="multipleSelect" multiple required="" data-parsley-required-message = '@if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{$language['department']}} @endif {{$language['is_required']}}'>
                            <?php
                            foreach ($deptApp as $key => $dept) {
                            ?>
                            <option value="<?php echo $dept->department_id; ?>"<?php if(in_array($dept->department_id,$arr_dept_id)){ echo "selected";}?>><?php echo $dept->department_name;?>
                            </option>
                            <?php
                            }
                            ?>
                        </select>   
              
                    </div>
                </div>
            <div class="form-group">
                <label for="Stack" class="col-sm-2 control-label">Stack: <span class="compulsary">*</span></label>
                <div class="col-sm-8">
                    <select name="stack[]" id="stack" class="multipleSelect" value="{{$val->stack_id}}" multiple required="" data-parsley-required-message = 'Stack is required'>
                        <?php
                        foreach ($stack as $key => $value) {
                            ?>
                            <option value="<?php echo $value->stack_id; ?>" <?php if(in_array($value->stack_id,$arr_stack_id)){ echo "selected"; }  ?>><?php echo $value->stack_name;?></option>
                            <?php
                        }
                        ?>
                    </select>                      
                    <span class="dms_error">{{$errors->first('docname')}}</span>
                </div>
            </div>    
            <div class="form-group">
                <label for="Tag Category" class="col-sm-2 control-label">Tag Category: <span class="compulsary">*</span></label>
                <div class="col-sm-8">
                    <select name="tagscate[]" id="tagwrdcat" class="multipleSelect" multiple required="" data-parsley-required-message = 'Tag category is required'>
                        <?php
                        foreach ($tagsCateg as $key => $tagcat) {
                            ?>
                            <option value="<?php echo $tagcat->tagwords_category_id; ?>"<?php if(in_array($tagcat->tagwords_category_id,$arr_tagcat_id)){ echo "selected"; }  ?>><?php echo $tagcat->tagwords_category_name;?></option>
                            <?php
                        }
                        ?>
                    </select>   
                   
                    
                    <span class="dms_error">{{$errors->first('docname')}}</span>
                </div>
            </div>   

            <div class="form-group">
                <label for="Tag Words" class="col-sm-2 control-label">Tag Words: <span class="compulsary">*</span></label>
                <div class="col-sm-8">
                    <div id="keywd">
                        <select name="keywords" id="keywords" class="form-control">
                            <option value="">Please select tag category</option>
                        </select>
                    </div>
                    <div id="reskeywrds"></div>
                    
                    <span class="dms_error">{{$errors->first('docname')}}</span>
                </div>
            </div>    
    
            
            <div id="sublist"></div>   
            <div class="form-group">
                <label for="Uploaded File" class="col-sm-2 control-label">Uploaded File: <span class="compulsary">*</span></label>
                <div class="col-sm-8">
                    <div>
                        <input type="text" id="fileLabel" name="fileLabel" class="form-control" value="@foreach ($dglist as $key => $val){{$val->document_file_name}}@endforeach" readonly required="" data-parsley-required-message = 'Upload file is required'>
                        <input type="hidden" id="hiddparent" name="hiddparent" value="{{$val->parent_id}}">
                    </div>             
                </div>
            </div>
            <div class="form-group">
                <label for="Upload Folder" class="col-sm-2 control-label">Upload Folder: <span class="compulsary">*</span></label>
                <div class="col-sm-8">
                    <div>
                        <input type="text" id="up_folder" name="up_folder" class="form-control" value="{{$val->document_path}}" readonly required="" data-parsley-required-message = 'Upload folder is required'>
                        <input type="hidden" name="hidd_folder_id" id="hidd_folder_id" value="{{$val->parent_id}}">
                    </div>
                    <div class="box">
                    <div id="tree-container" style="padding:5px 0px;"></div>
                    </div>              
                </div>
            </div>   
            <?php } ?>
            <div class="form-group">
                <label class="col-sm-2 control-label"></label>
                <div class="col-sm-8">
                    
                    <input type="submit" value="Save" name="save" id="save" class="btn btn-primary">
                    <a href="{{url('documentsList')}}" value="Cancel" name="cancel" class="btn btn-primary btn-danger" id = "cn">Cancel</a>
                </div>
            </div><!-- /.col -->
            {!! Form::close() !!}        
        </div>
    </section>
</section><!-- /.content --> 

<script>
$( document ).ready(function() {
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var tagwrd=document.getElementById('hidd_var').value;
             if($("#tagwrdcat").val()==null){
                var value = 0; 
            }else{
                var value = $("#tagwrdcat").val();
            }
            $.ajax({
                type: 'post',
                url: '{{URL('documentsTagwords')}}',
                data: {_token: CSRF_TOKEN, tagcatid: value ,tagword:tagwrd},
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
//function to get the sublist of the document type on document ready
            var docid=document.getElementById('hidd_doc').value;
            if($("#doctypeid").val()==null){
                var value = 0; 
            }else{
                var value = $("#doctypeid").val();
            }
            $.ajax({
                type: 'post',
                url: '{{URL('documentfileSubListEdit')}}',
                data: {_token: CSRF_TOKEN, doctypeid: value, doc_id:docid },
                timeout: 50000,
                beforeSend: function() {
                    
                },
                success: function(data, status){
                    if(data) {
                        $("#sublist").html(data);
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
    $(function ($) {
        $('.multipleSelect').fastselect();
        //Duplicate entry
        $('#spl-wrn').delay(5000).slideUp('slow');
        // Onchange function to get the sublist of the document type
        $("#tagwrdcat").change(function(){
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var tagwrd=document.getElementById('hidd_var').value;
             if($("#tagwrdcat").val()==null){
                var value = 0; 
            }else{
                var value = $("#tagwrdcat").val();
            }
            $.ajax({
                type: 'post',
                url: '{{URL('documentsTagwords')}}',
                data: {_token: CSRF_TOKEN, tagcatid: value ,tagword:tagwrd},
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


        // Onchange function to get the sublist of the document type
        $("#doctypeid").change(function(){
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            if($("#doctypeid").val()==null){
                var value = 0; 
            }else{
                var value = $("#doctypeid").val();
            }
            $.ajax({
                type: 'post',
                url: '{{URL('documentsSubList')}}',
                data: {_token: CSRF_TOKEN, doctypeid: value },
                timeout: 50000,
                beforeSend: function() {
                    
                },
                success: function(data, status){
                    if(data) {
                        $("#sublist").html(data);
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
     //Duplicate entry
        $("#docno").change(function(){
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: 'post',
                url: '{{URL('docnoDuplication')}}',
                dataType: 'json',
                data: {_token: CSRF_TOKEN, name: $("#docno").val() },
                timeout: 50000,
                beforeSend: function() {
                    $("#dp_wrn").show();
                    $('input[type="submit"]').prop('disabled', true);
                },
                success: function(data, status){
                    if(data)
                    {
                        $("#dp").html(data);
                        $("#docno").val('');
                    }
                    else
                    {
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
                    $('input[type="submit"]').prop('disabled', false);
                }
            });
        });
    $("#docname").change(function(){
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: 'post',
                url: '{{URL('docnameDuplication')}}',
                dataType: 'json',
                data: {_token: CSRF_TOKEN, name: $("#docname").val() },
                timeout: 50000,
                beforeSend: function() {
                    $("#dn_wrn").show();
                    $('input[type="submit"]').prop('disabled', true);
                },
                success: function(data, status){
                    if(data)
                    {
                        $("#dn").html(data);
                        $("#docname").val('');
                    }
                    else
                    {
                        $("#dn-inner").text('');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown){
                    console.log(jqXHR);    
                    console.log(textStatus);    
                    console.log(errorThrown);    
                },
                complete: function() {
                    $("#dn_wrn").hide();
                    $('input[type="submit"]').prop('disabled', false);
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
                    'force_text' : true,
                    'plugins' : ['state','dnd','contextmenu','wholerow']
                })
                .on('delete_node.jstree', function (e, data) {
                    $.get('response?operation=delete_node', { 'id' : data.node.id })
                        .fail(function () {
                            data.instance.refresh();
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
</script>

{!! Html::script('dist/jstree.min.js') !!}
{!! Html::style('dist/style.min.css') !!}

@endsection