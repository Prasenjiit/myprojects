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

<!--{!! Html::script('js/build.min.js') !!} 
{!! Html::style('css/build.min.css') !!} -->
{!! Html::style('css/fastselect.min.css') !!} 
{!! Html::script('js/fastselect.standalone.js') !!}   

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
   
    .resizewrapper {
        min-height: 195px;
        overflow: auto;
        resize: vertical;
    }
    .form-horizontal .control-label{
        text-align: left;
    }
    .resizewrapperiframe{
        overflow-y: hidden;
        overflow-x: scroll;
        width:100%;
        height: 100%;
        min-height: 590px;
    }
    .resizable {
        resize: both;
        overflow-y: hidden;
        overflow-x: scroll;
        border: 1px solid black;
        min-height: 590px;
    }

    #pdfrender{
        overflow: auto;
    }
    .mandatory{
        padding-left: 5px;
        font-size: 12px;
        color: #FF0000;
    }
    .dispoff{
        display: none;
    }
    .oddcol{
        height: 40px;
        background: #e6faff;
    }
    .evncol{
        height: 40px;
        background: #FFFFFF;
    }
    .evenclr{
        background-color: #e6faff;
        padding-top: 8px;
    }
    .odd{
        padding-top: 8px;
    }
    .sidbox{
        width: 100%;
    }
    .rowstyllft{
        padding-left: 5px;
        width:  150px;
        border-right: 1px solid #EEEEEE;
    }
    .rowstylmdl{
        padding-left: 5px;
        border-right: 1px solid #EEEEEE;
    }
    .rowstylrgt{
        padding-right: 5px;
        padding-left: 5px;
    }
    .head{
        height: 40px;
        background: #00c0ef;
        color: #FFFFFF;
        padding-left: 5px;
    }
    table a{
        float: right;
        padding-right: 10px;
        color: #FFFFFF;
        cursor: pointer;
    }
    .boxwidth{
        width: 65%;
    }

    table a:hover,a:focus,a:active{
        color: #FFFFFF;
    }
    canvas {
      max-width: 100%;
      }
</style>

<!-- Content Wrapper. Contains page content -->
<section class="content" id="shw">
    <section class="content-header">
        <h1>
            {{$language['documents']}}
            <small>- {{$language['edit']}} {{$language['document']}}</small>
        </h1>
       <!--  <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> {{$language['home']}}</a></li>
            <li class="active">{{$language['edit']}} {{$language['document']}}</li>
        </ol>     -->     
    </section>
    @if(Session::has('data'))
    <section class="content content-sty" id="spl-wrn">        
        <div class="alert alert-sty alert-success ">{{ Session::get('data') }}</div>        
    </section>
    @endif
    <!-- Main content -->
    <section class="content">    
        <div class="row">
            <div class="col-md-6" id="scroll" style="overflow:scroll; overflow-x: hidden; height:680px;">
                <div class="modal-body">    
                    <!-- form start -->
                    {!! Form::open(array('url'=> array('editDocument',$id), 'files'=>true, 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'documentManagementAddForm', 'id'=> 'documentManagementAddForm','data-parsley-validate'=> '')) !!}            
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

                        <div class="form-group" id="form-doctypeid">
                            <label for="Doc Types" class="col-sm-12 control-label">{{$language['document types']}}: <span class="compulsary">*</span></label>
                            <div class="col-sm-12">
                                <select name="doctypeid" id="doctypeid" class="multipleSelect" multiple required="" data-parsley-required-message = 'Document type is required'>
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
                        <label for="doc no" class="col-sm-12 control-label"><?php echo $settings_document_no.': '; ?><span class="compulsary">*</span></label>
                        <div class="col-sm-12">
                            <?php echo Form:: 
                            text('docno',$val->document_no, 
                            array( 
                            'class'=> 'form-control', 
                            'id'=> 'docno', 
                            'title'         => "'".$settings_document_no."'".$language['length_others'], 
                            'placeholder'=> ''.$settings_document_no.'',     
                            'required'               => '',                               
                            'data-parsley-required-message' => ''.$settings_document_no.' is required',                                    
                            )
                            );?>
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
                        <label for="document name" class="col-sm-12 control-label"><?php echo $settings_document_name.': '; ?><span class="compulsary">*</span></label>
                        <div class="col-sm-12">
                            <?php echo Form:: 
                            text(
                            'docname', $val->document_name, 
                            array( 
                            'class'                  => 'form-control', 
                            'id'                     => 'docname', 
                            'title'                  => "'".$settings_document_name."'".$language['length_others'], 
                            'placeholder'            => ''.$settings_document_name.'',
                            'required'               => '',
                            'data-parsley-required-message' => ''.$settings_document_name.' is required',
                            )
                            );?>
                            <div id="dn">
                                <span id="dn_wrn" style="display:none;">
                                    <i class="fa fa-refresh fa-spin fa-1x fa-fw"></i>
                                    <span class="">Please wait...</span>
                                </span>
                            </div>
                            <span class="dms_error">{{$errors->first('docname')}}</span>
                        </div>
                    </div>   
                    <div class="form-group" id="form-departmentid">
                            <label for="Department" class="col-sm-12 control-label">{{$language['department']}}: <span class="compulsary">*</span></label>
                            <div class="col-sm-12">
                                <select name="departmentid[]" id="departmentid" class="multipleSelect" multiple required="" data-parsley-required-message = 'Department is required'>
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
                        <label for="Stack" class="col-sm-12 control-label">{{$language['stack']}}:</label>
                        <div class="col-sm-12">
                            <select name="stack[]" id="stack" class="multipleSelect" value="{{$val->stack_id}}" multiple>
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
                        <label for="Tag Category" class="col-sm-12 control-label">{{$language['tag category']}}:</label>
                        <div class="col-sm-12">
                            <select name="tagscate[]" id="tagwrdcat" class="multipleSelect" multiple>
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
                        <label for="Tag Words" class="col-sm-12 control-label">{{$language['tag words']}}:</label>
                        <div class="col-sm-12">
                            <div id="keywd">
                                <select name="keywords" id="keywords" class="form-control">
                                    <option value="">{{$language['pls_select']}}</option>
                                </select>
                            </div>
                            <div id="reskeywrds"></div>
                            
                            <span class="dms_error">{{$errors->first('docname')}}</span>
                        </div>
                    </div>    
            
                    
                    <div id="sublist"></div>   
                    <div class="form-group">
                        <label for="Uploaded File" class="col-sm-12 control-label">{{$language['uploaded file']}}: <span class="compulsary">*</span></label>
                        <div class="col-sm-12">
                            <div>
                                <input type="text" id="fileLabel" name="fileLabel" class="form-control" value="@foreach ($dglist as $key => $val){{$val->document_file_name}}@endforeach" readonly required="" data-parsley-required-message = 'Upload file is required'>
                                <input type="hidden" id="hiddparent" name="hiddparent" value="{{$val->parent_id}}">
                            </div>             
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-12 control-label">{{$language['expir_date']}}: </label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" value="{{$val->document_expiry_date}}" class="form-control" id="doc_exp_date" name="document_expiry_date" placeholder="YYYY-MM-DD">
                            </div><!-- /.input group -->
                        </div>
                        <div class="col-sm-4">

                         <?php if($val->document_expiry_date):?>
                            {!! Form::checkbox('doc_exp_chk',1, false, ['id'=> 'doc_exp_chk']) !!} {{$language['no expiry']}}
                        <?php else: ?>
                            {!! Form::checkbox('doc_exp_chk',1, true, ['id'=> 'doc_exp_chk']) !!} {{$language['no expiry']}}
                        <?php endif;?>

                        
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="Document Folder" class="col-sm-12 control-label">{{$language['upload folder']}}: <span class="compulsary">*</span></label>
                        <div class="col-sm-12">
                            <div>
                                <input type="text" id="up_folder" name="up_folder" class="form-control" value="{{trim(preg_replace('/\s*\([^)]*\)/', '', $val->document_path))}}" readonly required="" data-parsley-required-message = 'Document folder is required'>
                                <input type="hidden" name="hidd_folder_id" id="hidd_folder_id" value="{{$val->parent_id}}">
                            </div>
                            <div class="box resizewrapper">
                            <div id="tree-container" style="padding:5px 0px;overflow: auto;min-height: 190px;"></div>
                            </div>              
                        </div>
                    </div>   
                    <?php } ?>
                    <!--Validation message shows-->
                    <div class="form-group">
                        <label class="col-sm-12 control-label" id="error-msg" style="color:red;text-align:center; margin-top:10px"></label>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-12 control-label"></label>

                        <div class="col-sm-12" style="text-align:center; margin-top:10px">
                            <input type="submit" value="{{$language['save']}}" name="save" id="save" class="btn btn-primary btn-save">
                            <input type="submit" value="{{$language['save_and_close']}}" id="save-and-close" name="save"  class="btn btn-primary btn-save">
                            <a href="{{url('listview')}}?view=import" value="Cancel" name="cancel" class="btn btn-primary btn-danger" id = "cn">{{$language['cancel']}}</a>
                        </div>
                    </div><!-- /.col -->
                    {!! Form::close() !!}        
                </div>
            </div>
            <div class="col-md-6">
            <!-- Add notes start-->
        <a class="btn btn-primary" id="notes" style="margin-bottom: 5px;">{{$language['notes']}}</a>
        <div class="row" id="mini-documents-add" style="width:91%">
            <div id="mini-doc-col">
                <div class="box box-primary" id="box">
                    <div id="notesbox" class="dispoff">
                    @if(count($noteList)>0)
                    <div id="notes-view">
                        <table class="sidbox">
                            <th class="head" colspan="3">{{$language['notes']}}
                                <a class="closebox" title="Close"><i class="fa fa-close"></i></a>
                                <a id="editnote"><i class="fa fa-plus"></i></a>   
                            </th>
                            <tr class="evncol">
                                <td class="rowstyllft">{{$language['note']}}</td>
                                <td class="rowstylmdl">{{$language['date']}}</td>
                                <td class="rowstylrgt">{{$language['by']}}</td>
                            </tr>
                            <tr id="newnote" style="display:none;">
                                <td colspan="3">
                                    <textarea style="width:99%;margin-left: 5px; placeholder="Add new note" name="newnotetxt" id="newnotetxt"></textarea>
                                    <div id="msgbox" class="mandatory"></div>

                                    <!--Btns-->
                                    <div class="box-footer">
                                        <input type="button" id="notesave" class="btn btn-info btn-flat" value="{{$language['save']}}">
                                        <input type="button" id="editnote" class="btn btn-primary btn-danger"  value="{{$language['cancel']}}">

                                    </div>
                                </td>
                            </tr>
                            
                            <?php $incr = 1; ?>
                            @foreach ($noteList as $key => $val)
                                @if(($incr % 2) == 1)
                                    <tr class="oddcol">
                                @else
                                    <tr class="evncol">
                                @endif
                                    <td class="rowstyllft"><div style="max-height:200px; overflow-y:auto;">{{ $val->document_note }}</div></td>
                                    <td class="rowstylmdl" style="width:100px;">{{ $val->created_at }}</td>
                                    <td  class="rowstylrgt" style="width:100px;">{{ $val->document_note_created_by }}</td>
                                    </tr>                                    
                                <?php $incr++; ?>                     
                            @endforeach
                
                        </table>
                    </div>
                    @else
                        <table class="sidbox" id="notes-view">
                            <th class="head">{{$language['notes']}}
                                <a class="closebox"><i class="fa fa-close"></i></a>
                                <a id="editnote"><i class="fa fa-plus"></i></a>    
                            </th>
                            <tr id="msgrow">
                                <td colspan="3">
                                    <div id="msgbox" style="color:#FF0000"></div>
                                </td>
                            </tr>
                            <tr id="newnote" style="display:none;">
                                <td colspan="3">
                                    <textarea style="width:100%" placeholder="Add new note" name="newnotetxt" id="newnotetxt"></textarea>
                                        
                                    <!--Btns-->
                                    <div class="box-footer">
                                        <input type="button" id="notesave" class="btn btn-info btn-flat" value="{{$language['save']}}">
                                        <input type="button" id="editnote" class="btn btn-primary btn-danger"  value="{{$language['cancel']}}">

                                    </div>

                                </td>
                            </tr>
                            <tr class="oddcol">
                                <td class="rowstyllft">{{$language['no notes']}}</td>
                            </tr>
                        </table>
                    @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- Add notes end-->
                <div class="frame">
                    <div class="box-body box-profile">
                        <div id="pdfrender">
                        @foreach($dglist as $key => $dval)
                           <?php $data=$dval->document_file_name;    
                            $ext = substr($data, strpos($data, ".") + 1);    
                            ?>
                           
                           <?php if($ext=='tiff'||$ext=='tif'||$ext=='TIFF'||$ext=='TIF'){?>
                           <script type="text/javascript">
                            var path='<?php echo $data;?>';
                            $('#pdfrender').html('<div id="output"></div>');
                            var xhr = new XMLHttpRequest();
                            xhr.responseType = 'arraybuffer';
                            xhr.open('GET', window.location.protocol + "//" + window.location.host + "/"+'dms/storage/documents/'+path);
                            xhr.onload = function (e) {
                                var tiff = new Tiff({buffer: xhr.response});
                                var canvas = tiff.toCanvas();
                                if (canvas) {
                              $('#output').empty().append(canvas);
                            }
                            };
                            xhr.send();

                           </script>
                           <?php }
                           else if($ext=='doc'||$ext=='docx'||$ext=='xls'||$ext=='xlsx'){
                            $connected = @fsockopen("www.google.com", 80); 
                            if ($connected){
                                $is_conn = true; //action when connected
                                ?>
                                    <iframe src="https://docs.google.com/gview?url=http://www.toptechinfo.net/dms/storage/documents/{{ $dval->document_file_name }}&embedded=true" width="auto" height="auto;" class="resizewrapperiframe"></iframe>
                                <?php
                                fclose($connected);
                            }else{
                                $is_conn = false; //action in connection failure
                                ?>
                                    <div>{{$language['no_internet']}}</div>  
                                <?php
                            }
                            ?>
                           <?php }
                            else
                                { ?>
                                <iframe src="http://www.toptechinfo.net/dms/storage/documents/{{ $dval->document_file_name }}" width="auto" height="auto;" class="resizewrapperiframe"></iframe>
                            <?php } ?>
                        
                       @endforeach
                        </div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>
        </div>
    </section>
</section><!-- /.content --> 

<script>
$( document ).ready(function() {

    // When click on save btn the action will change.That is how the "Save" and "Save And Close" button works. 
    // Butn Save
    $('.btn-save').click(function(){

        // Validation doc type and department(scroll top not working in select box)
        var departmentid = $('#departmentid').val();
        if(departmentid == null){
            $('#error-msg').html('<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Please fill Department');
        }
        var doctypeid = $('#doctypeid').val();
        if(doctypeid == null){
            $('#error-msg').html('<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Please fill Document type');
        }
        // Remove error msg
        if(departmentid && doctypeid){
            $('#error-msg').html('');
        }
    });

    $('#save').click(function(){
        var id     = $('#hidd_doc').val();
        var action = base_url+'/editDocument/'+id+'?uniqueId='+id+'';
        $('#documentManagementAddForm').attr('action',action);
    });

    $('#save-and-close').click(function(){
        var id     = $('#hidd_doc').val();
        var action = base_url+'/editDocument/'+id+'';
        $('#documentManagementAddForm').attr('action',action);
    });


    // Remove error
    $('#docno').click(function(){
        $('#dp-inner').text('');
    });

$("#mini-doc-col").hide();
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
                url: '{{URL('documentsSubListEdit')}}',
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
            var id_doc=$('#hidd_doc').val();
            $.ajax({
                type: 'post',
                url: '{{URL('docnoDuplication')}}',
                dataType: 'json',
                data: {_token: CSRF_TOKEN, name: $("#docno").val(), doc_id:id_doc },
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
            var id_doc=$('#hidd_doc').val();
            $.ajax({
                type: 'post',
                url: '{{URL('docnameDuplication')}}',
                dataType: 'json',
                data: {_token: CSRF_TOKEN, name: $("#docname").val(), doc_id:id_doc },
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
                          text: "{{$language['Swal_not_revert']}}",
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
                    $('#up_folder').val(path.replace(/ *\([^)]*\) */g, ""));
                });
                $("#notes").click(function(){
                $("#mini-doc-col").show();
                $(this).addClass("active");
                $("#notesbox").show();
            });
                $("body").on('click','#notesave',function(){
                var noteval = $("#newnotetxt").val();
                var docid=document.getElementById('hidd_doc').value;
                var view = 'add'; 
                if(noteval.length<1){
                    $("#msgbox").html("Note is required");
                }else{
                    $.ajax({
                        type: 'get',
                        url: '{{URL('tempdocumentsNoteSave')}}',
                        data: {docmntid:docid,noteval:noteval,action:view},
                        timeout: 50000,
                        beforeSend: function() {
                            
                        },
                        success: function(data){

                            $('#notes-view').html(data);
                            
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

                setTimeout(function(){
                    $("#msgbox").html("");
                },10000);

            });

                $(".closebox").click(function(){
                $("#mini-doc-col").hide();
                $("#msgbox").html("");
            });

                $('body').on('click','#editnote',function(){ 
                $("#newnote").toggle();
                $("#msgbox").html("");
                $("#newnotetxt").val('');

            });                               
</script>
<script src="js/tiff.min.js"></script> 
{!! Html::script('dist/jstree.min.js') !!}
{!! Html::style('dist/style.min.css') !!}
@endsection
