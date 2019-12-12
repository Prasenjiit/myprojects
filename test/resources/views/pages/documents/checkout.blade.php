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

    
    .resizewrapper {
        min-height: 195px;
        overflow: auto;
        resize: vertical;
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
  
    .form-horizontal .control-label{
        text-align: left;
    }

</style>
<style>
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
.fstElement{
        width: 100%;
    }
    .fstControls{
        width: 100% !important;
    }
table a:hover,a:focus,a:active{
    color: #FFFFFF;
}
canvas {
      max-width: 100%;
      }

@media(max-width:505px){
    .discard_check_out{
        margin-top: 2px;
    }
}
@media(max-width:351px){
    .check_in_as_draft{
        margin-top: 2px;
    }
}

</style>

<!-- Content Wrapper. Contains page content -->
<section class="content" id="shw">
    <section class="content-header">
        <h1>
            {{$language['documents']}}
            <small>- {{$language['edit']}} {{$language['check out']}} {{$language['document']}}</small>
        </h1>
        <!-- <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> {{$language['home']}}</a></li>
            <li class="active">{{$language['edit']}} {{$language['check out']}} {{$language['document']}}</li>
        </ol>  -->           
    </section>
    @if(Session::has('data'))
    <section class="content content-sty" id="spl-wrn">        
        <div class="alert alert-sty {{ Session::get('alert-class', 'alert-info') }} ">{{ Session::get('data') }}</div>        
    </section>
    @endif
    <!-- Main content -->
    <section class="content">    
        <div class="row">
            <div class="col-md-6" style="overflow:scroll; overflow-x: hidden; height:680px;">
                <div class="modal-body">    
                    <!-- form start -->
                    {!! Form::open(array('url'=> array('checkout',$id), 'files'=>true, 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'documentManagementAddForm', 'id'=> 'documentManagementAddForm','data-parsley-validate'=> '')) !!}            
                       <div class="form-group" id="focus">
                            <label for="UploadDocument" class="col-sm-12 control-label">{{$language['upload document']}}: <span class="compulsary">*</span></label>                
                            <div class="col-sm-12">
                                <div class="dropzone" id="dropzoneFileUpload"></div>
                                <input type="hidden" name="hidd_file" id="hidd_file" value="">
                                <span class="dms_error compulsary" id="file_error"></span>      
                            </div>
                            <div class="col-sm-12">
                             @if(Session::get('settings_file_extensions'))
                                <span class="text-warning">{{$language['allowed_file_extensions']}}: {{Session::get('settings_file_extensions')}}</span> 
                                @endif
                                <span class="text-warning">{{$language['max upload msg']}} 
                                {{$language['max_upload_size']}}</span>  
                            </div>  
                        </div>

                         <?php foreach ($dglist as $key => $val)
                        $arr_dept_id=explode(',', $val->department_id);
                        $arr_type_id=explode(',', $val->document_type_id);
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
                            <div class="col-sm-11" style="cursor:not-allowed;">
                            <div id="test" style="pointer-events: none;">
                                <select name="doctypeid" id="doctypeid" class="multipleSelect" style="cursor:not-allowed;">
                                    <?php
                                    foreach ($docType as $key => $dType) {

                                        ?>

                                        <option value="<?php echo $dType->document_type_id; ?>"<?php if(in_array($dType->document_type_id,$arr_type_id)){ echo "selected";}?>><?php echo $dType->document_type_name;?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                </div>   
                            <span class="dms_error compulsary" id="type_error">{{$errors->first('docno')}}</span>
                            </div>
                            <div class="col-sm-1">
                            <span class="formInfo"><a href="{{url('doctypeSettings')}}"" class="jTip" id="one" name="Note:">?</a></span>
                            </div>
                        </div>
                        
                        <div class="form-group" id="form-docno">
                        <label for="document no" class="col-sm-12 control-label"><?php echo $settings_document_no.':'; ?><span class="compulsary">*</span></label>
                        <div class="col-sm-12">
                            <?php echo Form:: 
                            text('docno',$val->document_no, 
                            array( 
                            'class'         => 'form-control', 
                            'id'            => 'docno', 
                            'title'         => "'".$settings_document_no."'".$language['length_others'], 
                            'placeholder'   => ''.$settings_document_no.'',     
                            'required'      => '',
                            'data-parsley-maxlength' => $language['max_length'],                           
                            'data-parsley-required-message' => ''.$settings_document_no.' is required',                                    
                            )
                            );?>
                            <div id="dp" style="color:red;">
                                <span id="dp_wrn" style="display:none;">
                                    <i class="fa fa-refresh fa-spin fa-1x fa-fw"></i>
                                    <span class="">Please wait...</span>
                                </span>
                            </div>
                            <span class="dms_error compulsary" id="docno_error">{{$errors->first('docno')}}</span>            
                        </div>

                    </div>
                    
                    <div class="form-group" id="form-docname">
                        <label for="document name" class="col-sm-12 control-label"><?php echo $settings_document_name.':'; ?><span class="compulsary">*</span></label>
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
                            'data-parsley-maxlength' => $language['max_length'],
                            'data-parsley-required-message' => ''.$settings_document_name.' is required',
                            )
                            );?>
                            <div id="dn">
                                <span id="dn_wrn" style="display:none;">
                                    <i class="fa fa-refresh fa-spin fa-1x fa-fw"></i>
                                    <span class="">Please wait...</span>
                                </span>
                            </div>
                            <span class="dms_error compulsary" id="docname_error">{{$errors->first('docname')}}</span>
                        </div>
                    </div>   
                    <div class="form-group" id="form-departmentid">
                            <label for="Department" class="col-sm-12 control-label">{{$language['department']}} <span class="compulsary">*</span></label>
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
                                <span class="dms_error compulsary" id="dept_error">{{$errors->first('docno')}}</span>
                            </div>
                        </div>
                    <div class="form-group">
                        <label for="Stack" class="col-sm-12 control-label">{{$language['stack']}}: </label>
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
                            <span class="dms_error compulsary" id="stack_error">{{$errors->first('docname')}}</span>
                        </div>
                    </div>    
                    <div class="form-group">
                        <label for="Tag Category" class="col-sm-12 control-label">{{$language['tag category']}}: </label>
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
                           
                            
                            <span class="dms_error compulsary" id="tagwrdcat_error">{{$errors->first('docname')}}</span>
                        </div>
                    </div>   

                    <div class="form-group">
                        <label for="Tag Words" class="col-sm-12 control-label">{{$language['tag words']}}: </label>
                        <div class="col-sm-12">
                            <div id="keywd">
                                <select name="keywords" id="keywords" class="form-control">
                                    <option>{{$language['pls_select']}}</option>
                                </select>
                            </div>
                            <div id="reskeywrds"></div>
                            
                            <span class="dms_error compulsary" id="keywords_error">{{$errors->first('docname')}}</span>
                        </div>
                    </div>   
            
                    
                    <div id="sublist"></div>   


                    <div class="form-group">
                        <label for="Uploaded File" class="col-sm-12 control-label">{{$language['uploaded file']}}: <span class="compulsary">*</span></label>
                        <div class="col-sm-12">
                            <div>
                                <input type="text" id="fileLabel" name="fileLabel" class="form-control" value="{{$val->document_file_name}}" readonly required="" data-parsley-required-message = 'Upload file is required'>
                                <input type="hidden" id="hiddparent" name="hiddparent" value="{{$val->parent_id}}">
                            </div>             
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

                    <div class="form-group">                        
                        
                        <div class="col-sm-12">
                            <label>{{ $language['checked_version'] }}</label>
                            <div class="btn-group" id="toggle_event_editing">
                                <button type="button" class="btn btn-info locked_active">ON</button>
                                <button type="button" class="btn unlocked_inactive">OFF</button> 
                            </div>
                            <input type="hidden" id="chkver" checked="checked" name="version" value="Yes">
                            
                        </div>
                    </div>    

                    <!--Validation message shows-->
                    <div class="form-group">
                        <label class="col-sm-12 control-label" id="error-msg" style="color:red;text-align:center;"></label>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-12" style="text-align:center;">
                            <input type="submit" value="{{$language['check in and publish']}}" name="Checkin" class="check_in_and_oublish btn btn-primary btn-save" id="validate" val='1'>

                            <!-- <input type="submit" value="{{$language['check in and publish']}}" name="Checkin" id="validate" val='1' class="btn btn-primary"> --> 

                            <!--The value names inside submit buttons are used for check file required validation.If you change the language name you should change that same name in script also(validate upload document ). -->
                            <input type="submit" value="{{$language['check in as draft']}}" name="Draft" id="validate" val='2' class="check_in_as_draft btn btn-primary btn-save">
                            @if($val->document_pre_status=="Draft")
                            <input type="submit" value="{{$language['discard check out']}}" name="Discard_draft" val='3' id="validate" class="discard_check_out btn btn-primary btn-save">
                            @else
                            <input type="submit" value="{{$language['discard check out']}}"  name="Discard_published" val='4' id="validate" class="discard_check_out btn btn-primary btn-save">
                            @endif

                            <!--Cancel button-->
                            @if($view=='checkout')
                            <a href="{{url('listview')}}?view=checkout&val=cancel" value="{{$language['cancel']}}" name="cancel" class="btn btn-primary btn-danger" id = "cn">{{$language['cancel']}}</a>
                            @elseif(Input::get('page') == 'document')
                            <a href="{{url('documents')}}" value="{{$language['cancel']}}" name="cancel" class="btn btn-primary btn-danger" id = "cn">{{$language['cancel']}}</a>
                            <!-- Status change "check out" fun cancel -->
                            <!-- onclick="cancel({{ $val->document_id }},'{{ $val->document_name }}')" -->
                            @elseif(Input::get('page') == 'documentsList')
                            
                            <a href="{{url('listview')}}?view=list" value="{{$language['cancel']}}" name="cancel" class="btn btn-primary btn-danger" id = "cn">{{$language['cancel']}}</a>
                            <!-- onclick="cancel({{ $val->document_id }},'{{ $val->document_name }}')" -->
                            @endif

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
                                    <textarea style="width:99%;margin-left: 5px;" placeholder="Add new note" name="newnotetxt" id="newnotetxt"></textarea>
                                    <div id="msgbox" class="mandatory"></div>

                                    <!--Btns-->
                                    <div class="box-footer">
                                        <input type="button" id="notesave" class="btn btn-info btn-flat"  value="{{$language['save']}}">
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

                                    <div class="box-footer">
                                        <input type="button" id="notesave" class="btn btn-info btn-flat"  value="{{$language['save']}}">
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
                                    <div class="resizable"><iframe src="https://docs.google.com/gview?url=http://www.toptechinfo.net/dms/storage/documents/{{ $dval->document_file_name }}&embedded=true" width="auto" height="auto;" class="resizewrapperiframe"></iframe></div>
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
                            <div class="resizable"><iframe src="http://www.toptechinfo.net/dms/storage/documents/{{ $dval->document_file_name }}" width="auto" height="auto;" class="resizewrapperiframe"></iframe></div>
                        <?php } ?>
                        
                       @endforeach
                        </div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>
        </div>
    </section>
</section><!-- /.content --> 
<div class="modal fade" id="userAddModal" data-backdrop="false" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="dGAddModal" >
   <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body" style="min-height: 200px !important;">
                <div class="form-group">
                    Do you want to Check In <sapn class="share-doc-name"></sapn> ?
                </div>
                <div class="form-group">
                    <label for="Comments" class="col-sm-12 control-label">Check In Comments:<span style="color:red">*</span></label>
                    <div class="col-sm-12">
                        {!! Form:: 
                        text('comments','', 
                        array( 
                        'class'=> 'form-control', 
                        'id'=> 'comments', 
                        'title'=> "'".$language['full name']."'".$language['length_others'], 
                        'placeholder'=> 'Check In Comments',
                        'autofocus',                
                        )
                        ) 
                        !!}                                              
                        <span class="dms_error compulsary">{{$errors->first('name')}}</span>       
                        <span class="null_error" style="color:red"></span>        
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-5 control-label"></label>
                    <div class="col-sm-7" id="button-section" style="padding-top: 10px;">
                        {!!Form::submit($language['save'], array('class' => 'btn btn-primary sav_btn','id'=>'comment_save')) !!} &nbsp;&nbsp;

                            {!!Form::button($language['cancel'], array('class' => 'btn btn-primary btn-danger', 'id' => 'cn', 'data-dismiss' => 'modal')) !!}
                        </div>
                </div><!-- /.col -->
                {!!Form::close()!!}
            </div>
        </div>
    </div>
</div>

<script>
$('#comment_save').click(function(){
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var docid='<?php echo $id;?>';
    var comments=$('#comments').val();
    if(comments){
        // success
        $('.null_error').html('')
        $.ajax({
            type: 'post',
            url: '{{URL('commentAdd')}}',
            data: {_token:CSRF_TOKEN,hidd_doc_id:docid,comments:comments },
            beforeSend: function() {
                
            },
            success: function(data){
                $('#userAddModal').hide();
                document.getElementById("documentManagementAddForm").submit();
            },
            error: function(jqXHR, textStatus, errorThrown){
                console.log(jqXHR);    
                console.log(textStatus);    
                console.log(errorThrown);    
            },
            complete: function() {
                
            }
        });
    }else{
        // null
        $('.null_error').html('Please fill Check In comments.')
    }        
});

$('#toggle_event_editing button').click(function(){
    if($(this).hasClass('locked_active') || $(this).hasClass('unlocked_inactive')){
        /* code to do when unlocking */
        $('#chkver').val('No');
    }else{
        /* code to do when locking */
        $('#chkver').val('Yes');
    }
    
    /* reverse locking status */
    $('#toggle_event_editing button').eq(0).toggleClass('locked_inactive locked_active btn-info');
    $('#toggle_event_editing button').eq(1).toggleClass('unlocked_inactive unlocked_active btn-info');
});
var max_file_size = "{{$language['max_upload_size']}}";
max_file_size = max_file_size.slice(0, -2); //remove Mb from string
Dropzone.autoDiscover = false;
    var baseUrl = "{{ url('/') }}";
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var myDropzone = new Dropzone("div#dropzoneFileUpload", {
        type:'post',
        params: {_token:CSRF_TOKEN},
        url: baseUrl+"/dropzone/uploadFilesCheckin",
        paramName: 'file',
        maxFiles: 1,
        clickable: true,
        maxFilesize: max_file_size,
        uploadMultiple: false,
        addRemoveLinks: true,
        success:function(file,response,data){
        if(response==1)
        {   
            $(file.previewElement).find('.dz-error-mark').css('opacity','1').css('display','block');
            alert ('{{$language['upload error']}}');}
        else if(response==2)
        {   
            $(file.previewElement).find('.dz-error-mark').css('opacity','1').css('display','block');
            alert ('{{$language['doc_already_exist']}}');}
        else if(response=='invalid')
        {   
            $(file.previewElement).find('.dz-error-mark').css('opacity','1').css('display','block');
            alert ('{{$language['upload error']}}');}
        else if(response=='exists')
        {   
            $(file.previewElement).find('.dz-error-mark').css('opacity','1').css('display','block');
            alert ('{{$language['doc_already_exist']}}');}
        else if(response=='ftpinvalid')
        {   
            $("#file_error").html('Error in ftp connection. Make sure the ftp credentials is correct or try changing it to http in the settings.');
        }
        else if(response=='tokenMismatch')
        {   
            $(file.previewElement).find('.dz-error-message').text('Error, Try again ').css('opacity','1').css('display','block').css('top','90px');
            $(file.previewElement).find('.dz-error-mark').css('opacity','1').css('display','block');
            $(file.previewElement).find('.dz-filename').css('font-weight', 'bold');
        }
        else{    
        var rand_name=file.xhr.response;
        $("#hidd_file").val(rand_name);
        var ext=rand_name.split('.').pop();//extension
        if(ext=='tiff'||ext=='tif'||ext=='TIFF'||ext=='TIF')
        {
            $('#pdfrender').html('<div id="output"></div>');
            var reader = new FileReader();
            reader.onload = (function (theFile) {
                return function (e) {
                    var buffer = e.target.result;
                    var tiff = new Tiff({buffer: buffer});
                    var canvas = tiff.toCanvas();
                    var width = tiff.width();
                    var height = tiff.height();
                    if (canvas) {
                      $('#output').empty().append(canvas);
                    }
                };
            })(file);
        reader.readAsArrayBuffer(file);
        }
        else if(ext=='doc'||ext=="docx"||ext=='xls'||ext=='xlsx')
        {
            var online=navigator.onLine;
            if(online==true)
            {
            //$("#pdfrender").html('<div class="resizable"><iframe src="https://docs.google.com/gview?url=http://www.toptechinfo.net/dms/storage/documents/'+rand_name+'&embedded=true" width="100%" height="800px;"></iframe></div>');
            }
            else
            {
                $("pdfrender").html('<div> No internet conn</div>');
            }
        }
        else
        {
            //$("#pdfrender").html('<div class="resizable"><iframe src="http://www.toptechinfo.net/dms/storage/documents/'+rand_name+'?#toolbar=0" id="iframe" width="100%" height="800px;"></iframe></div>');
        }
        var org_name=$(".dz-filename").text();
        // remove extension.
        var n = org_name.indexOf('.');
        org_name = org_name.substring(0, n != -1 ? n : org_name.length);
        $("#docname").val(org_name);
        }
        },
    });
        //Remove file from dropzone
        myDropzone.on("removedfile", function(file,response,data) {
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var remove_file=file.xhr.response;
        $.ajax({
            type:'post',
            url: '{{URL('removeDocument')}}',
            data: {_token:CSRF_TOKEN,file:remove_file},
            success: function(data,response){
                if(data){
                    $("#docname").val(null);
                    $("#hidd_file").val(null);
                    $('#pdfrender').html('No document found');  
                    alert ('{{$language['success_remove_document']}}');
                }    
            }
        })
    });

$( document ).ready(function() {

    // Form validation focus out property
    $('.btn-save').click(function(){
        // Validation doc type and department(scroll top not working in select box)
        var departmentid = $('#departmentid').val();
        if(departmentid == null){
            $('#error-msg').html('<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Please fill Department');
        }
        
        // Remove error msg
        if(departmentid && doctypeid){
            $('#error-msg').html('');
        }

    });

    //validate upload document
    $( "#checkinbtn" ).click(function() {
    var check=$("#hidd_file").val();
    var status=0;
    if(check==""||check==null)
        {   
            $(window).scrollTop($('#focus').offset().top);
            $("#file_error").text("<?php echo $language['no_file_msg'];?>");
            status=1;
        }else{
            $("#file_error").text("");
        }

        var docname=$('#docname').val();
            if(docname=="" || docname == null)
            {
                $('.share-doc-name').text('the document');
            }
            else
            {
                $('.share-doc-name').text(docname);
            }

        if(docname=="" || docname==null){   
                $(window).scrollTop($('#form-docname').offset().top);
                $("#docname_error").text("Document name is required.");
                status=1;
            }else{
                $("#docname_error").text("");
            }
    
        var docno=$('#docno').val();
        if(docno==""||docno==null)
            {   
                $(window).scrollTop($('#form-docno').offset().top);
                $("#dp").text("Document number is required.");
                status=1;
            }else{
                $("#dp").text(" ");
            }

        var doctypeid=$('#doctypeid').val();
        if(doctypeid==""||doctypeid==null)
            {   
                $(window).scrollTop($('#form-doctypeid').offset().top);
                $("#type_error").text("Document type is required.");
                status=1;
            }else{
                $("#type_error").text(" ");
            }

        var departmentid=$('#departmentid').val();
        if(departmentid==""||departmentid==null)
            {    
                $(window).scrollTop($('#form-departmentid').offset().top);
                $("#dept_error").text("Department is required.");
                status=1;
            }else{
                $("#dept_error").text("");
            }

        if(status==1){
            return false;
        }
        });

    $( "#documentManagementAddForm" ).submit(function( event ) {

        var docname = $('#docname').val();
        var check=$("#hidd_file").val();
        var val = $("input[type=submit][clicked=true]").val();
        
        if(val != 'Discard Check Out'){

            if(check==""||check==null)
                {
                    $("#file_error").text("Document file is required.");
                    return false;
                }
                } else{

                    swal({
                      title: 'Do you want to discard the Check Out of '+'"'+docname+'"'+'',
                      type: "{{$language['Swal_warning']}}",
                      showCancelButton: true
                    }).then(function (result) {
                        if(result){
                            // Success
                            return true;
                        }
                        swal(
                        'Success'
                        )
                    },function (dismiss) {
                      // dismiss can be 'cancel', 'overlay',
                      if (dismiss === 'cancel') {
                        return false;
                      }
                    });

                }
    });
    $("form input[type=submit]").click(function() {
        $("input[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
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
                url: '{{URL('documentsSubListCheckout')}}',
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
            var id_doc=$('#hidd_doc').val();
            $.ajax({
                type: 'post',
                url: '{{URL('docnoDuplication')}}',
                dataType: 'json',
                data: {_token: CSRF_TOKEN, name: $("#docno").val(),doc_id:id_doc },
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
                data: {_token: CSRF_TOKEN, name: $("#docname").val(),doc_id:id_doc },
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
function cancel(id,docname)
    {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: 'post',
                url: '{{URL('cancelCheckout')}}',
               
                data: {_token: CSRF_TOKEN,id:id},
                timeout: 50000,
                beforeSend: function() {
                    $("#bs").show();
                },
                success: function(data, status){
                    //swal(data);
                    window.location.reload();
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
$("#notes").click(function(){
                $("#mini-doc-col").show();
                $(this).addClass("active");
                $("#notesbox").show();
            });
                $("body").on('click','#notesave',function(){
                var noteval = $("#newnotetxt").val();
                var docid=document.getElementById('hidd_doc').value;
                var view = 'checkout'; 
                if(noteval.length<1){
                    $("#msgbox").html("Note is required");
                }else{
                    $.ajax({
                        type: 'get',
                        url: '{{URL('documentsNoteSave')}}',
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
// select all desired input fields and attach tooltips to them
      $("#documentManagementAddForm :input").tooltip({
 
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
<script src="js/tiff.min.js"></script>  
{!! Html::script('dist/jstree.min.js') !!}
{!! Html::style('dist/style.min.css') !!}
@endsection
