<?php
include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')
@section('main_content')

{!! Html::script('js/parsley.min.js') !!}  
{!! Html::script('js/jquery.form.js') !!}   
{!! Html::style('css/font-awesome.min.css') !!} 
{!! Html::style('css/ionicons.min.css') !!} 

<!-- {!! Html::style('css/demo.css') !!}  -->

{!! Html::style('css/fastselect.min.css') !!} 
{!! Html::script('js/fastselect.standalone.js') !!}  

 <?php if(@Input::get('view') == 'checkout'):?>
    {!! Html::style('css/dropzone.min.css') !!}
    {!! Html::script('js/dropzone.js') !!}  
 <?php endif;?>


<!--For image rotation-->
@if(Request::segment(1) == 'editAllDocument' || Request::segment(1) == 'editcheckoutDocument' || Request::segment(1) == 'documentEdit')
    {!!Html::style('css/rotate-zoom/jquery.iviewer.css')!!}
    {!!Html::style('css/rotate-zoom/custom.css')!!}
@endif
 
<script>

$( document ).ready(function() {
    // Add class for minimize side bar 
    $('.sidebar-mini').addClass('sidebar-collapse');
});
</script>

<style>

    /*<--For image rotate and zoom-->*/
    .resizable {
  background:url(../images/loading/throbber.gif) center center no-repeat;
}
    .viewer
        {
            width: auto;
            height: 642px;
            position: relative;
        }
    .tabs{
            position: relative;
            top: 1%;
            background-color: #edf0f5;
            border: 2px solid #d2d6de;
            z-index: 0;
        }
    /*<--// For image rotate and zoom-->*/

    /*<--Reduce width of column-->*/
    .col-sm-12 {
        width: 99%;
    }

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
  
    .mandatory{
        padding-left: 5px;
        font-size: 12px;
        color: #FF0000;
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
    .fstElement{
        width: 100%;
    }
    .fstControls{
        width: 100% !important;
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
    .form-group {
        margin-bottom: 5px;
    }
    table a:hover,a:focus,a:active{
        color: #FFFFFF;
    }
    canvas {
        max-width: 100%;
    }

    /*<--jTip -->*/
    .formInfo a, .formInfo a:active, formInfo a:visited {
        margin-left: 0px !important;
    }

</style>

<!--Declear data-->
<?php
    /*<!--Get permission-->*/
    $user_permission=Auth::user()->user_permission; 
    $page = NULL;    
    $doc_url = config('app.doc_url');
    $check_path = config('app.base_path'); 
    // To make all document edit in one
    // Document list and grid view edit    ;
    if(@Input::get('page') == 'document' || @Input::get('page') == 'documentsList' || @Input::get('page') == 'stack' || @Input::get('page') == 'documentType' || @Input::get('page') == 'departments' || @Input::get('pge') == 'cOut' || @Input::get('page') == 'content' || @Input::get('page') == 'advsrch'  || @Input::get('frm') == 'advsrch'):
        $page      = 'document_edit';
        $action    = array('editDoc',$id);
        $noteSaveUrl = 'documentsNoteSave';
        $doc_url = config('app.doc_url');
        $check_path = config('app.base_path');     
        if(@Input::get('page') == 'document'):
            //$cancelBtnUrl = 'documents';
            $cancelBtnUrl = 'documentManagementView?dcno='.Input::get('id').'&page=document';
        endif;

        if(@Input::get('page') == 'documentsList'):
            //$cancelBtnUrl = 'listview?view=list';
            $cancelBtnUrl = 'documentManagementView?dcno='.Input::get('id').'&page=list';
        endif;

        if(@Input::get('page') == 'stack'):
            $cancelBtnUrl = 'documentManagementView?dcno='.Input::get('id').'&page='.Input::get('page').'';
        endif;

        if(@Input::get('page') == 'documentType'):
            $cancelBtnUrl = 'documentManagementView?dcno='.Input::get('id').'&page='.Input::get('page').'';
        endif;

        if(@Input::get('page') == 'departments'):
            $cancelBtnUrl = 'documentManagementView?dcno='.Input::get('id').'&page=department';
        endif; 

        if(@Input::get('pge') == 'cOut'):
            $cancelBtnUrl = 'documentManagementView?dcno='.Input::get('id').'&page='.Input::get('page').'';
        endif;

        if(@Input::get('page') == 'content'):
            $cancelBtnUrl = 'documentManagementView?dcno='.Input::get('id').'&page='.Input::get('page').'';
        endif;

        if(@Input::get('page') == 'advsrch' ):
            $cancelBtnUrl = 'documentManagementView?dcno='.Input::get('id').'&page=list&frm=advsrch';
        endif;

        if(@Input::get('frm') == 'advsrch' ):
            $cancelBtnUrl = 'documentAdvanceSearch/edit';
        endif;
        
    elseif(@Input::get('view') == 'checkout'):
            // Checkout list edit
            $page       = 'checkout'; 
            $action     = array('checkout',$id);
            $cancelBtnUrl = 'listview?view=checkout';
            $noteSaveUrl  = 'documentsNoteSave';
            $check_path = config('app.base_path');  
    else:
            // Import list edit
            $page       = 'import_list';
            $action     = array('editDocument',$id);
            $cancelBtnUrl = 'listview?view=import';
            $noteSaveUrl = 'tempdocumentsNoteSave';
            $doc_url = config('app.temp_document_url');
            $check_path = config('app.temp_document_path'); 
    endif; 

    // Fro cancel button
    if(Input::get('frm') == 'documentType' || Input::get('frm') == 'stack' || Input::get('frm') == 'department'):
        $cancelBtnUrl = 'docsList/'.Input::get('fid').'?page='.Input::get('frm').'';
    endif;

?>

<!-- Content Wrapper. Contains page content -->
<section class="content" id="shw">
    <section class="content-header">
    <div class="col-md-12">
        <strong>
            {{$language['documents']}}
            <small>- {{$language['edit_index_firelds']}}</small>
        </strong>
    </div>
             
    </section>
    @if(Session::has('data'))
    <section class="content content-sty" id="spl-wrn">        
        <div class="alert alert-sty alert-success ">{{ Session::get('data') }}</div>        
    </section>
    @endif
    <!-- Main content -->
    <section class="content">    
        <div class="row">
            <div class="col-md-4">
                <div class="modal-body">    
                    <!-- form start -->
                    {!! Form::open(array('url'=> @$action, 'files'=>true, 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'documentManagementAddForm', 'id'=> 'documentManagementAddForm','data-parsley-validate'=> '')) !!}      
                    <div id="form-first" style="overflow:scroll; overflow-x: hidden; height:680px;"> 
                        <!--Paqssing hidden variables for redirect to proper page after success-->
                        @if(@Input::get('frm') == 'documentType' || @Input::get('frm') == 'stack' || @Input::get('frm') == 'department')
                            <input type="hidden" value="{{Input::get('frm')}}" name="frm">
                            <input type="hidden" value="{{Input::get('fid')}}" name="fid">
                        @endif

                        <!--To redirect to the edit page, if page is document type,stack and department -->
                        <input type="hidden" value="{{Input::get('status')}}" name="status">
                        @if(@Input::get('page') == 'documentType' || @Input::get('page') == 'stack' || @Input::get('page') == 'departments')
                            <input type="hidden" value="{{Input::get('page')}}" name="frm">
                        @endif

                        @if(Input::get('pge'))
                            <input type="hidden" value="{{Input::get('pge')}}" name="pge">
                        @endif

                        @if(@$page == 'checkout')
                        <div class="form-group" id="focus">
                            <label for="UploadDocument" class="col-sm-12 control-label">{{$language['upload document']}}: <span class="compulsary"> *</span></label>                
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
                        @endif
                        <input type="hidden" name="field1" value="<?php echo $settings_document_no; ?>" id="field1">
                        <input type="hidden" name="field2" value="<?php echo $settings_document_name; ?>" id="field2">

                        <?php 
                        foreach ($dglist as $key => $val)
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
                        <input type="hidden" name="page" id="page" value="{{Input::get('page')}}">
                        <?php if((Input::get('status') == 'Review') && ($val->document_assigned_to == Auth::user()->username)){?>
                        <div class="form-group">
                            <label for="" class="col-sm-12 control-label">{{$language['assigned_by']}}: </label>
                            <div class="col-sm-12">
                                <input type="text" readonly="readonly" class="form-control" name="doc_assigned_by" value="{{ $val->document_created_by }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12 control-label">
                            <label class="col-sm-6">
                              <input type="radio" name="optionsRadios" id="optionsRadios" value="accept" checked="">
                              Accept
                            </label>
                            <label class="col-sm-6">
                              <input type="radio" name="optionsRadios" id="optionsRadios" value="reject">
                              Reject
                            </label>
                            </div>
                        </div>
                        <div class="form-group">
                          <label class="col-sm-12 control-label">{{$language['assign_add_note']}}:</label>
                          <div class="col-sm-12">
                          <textarea class="form-control" name="note_assign" rows="3" placeholder="{{$language['assign_placeholder']}}"></textarea>
                          </div>
                        </div>
                        <?php } ?>
                        <div class="form-group" id="form-doctypeid">
                            <label for="Doc Types" class="col-sm-12 control-label">{{$language['document types']}}: <span class="compulsary"> *</span></label>
                            <div class="@if(($val->document_version_no != '1.0' || @$page=='checkout') && @$page != 'import_list' ) col-sm-11 @else col-sm-12 @endif" @if(($val->document_version_no != '1.0' || @$page=='checkout') && @$page != 'import_list' ) style="cursor:not-allowed;" @endif>
                            <div id="test" @if(($val->document_version_no != '1.0' || @$page=='checkout') && @$page != 'import_list' ) style="pointer-events: none;" @endif>
                                <select name="doctypeid" id="doctypeid" class="multipleSelect" @if(($val->document_version_no != '1.0' || @$page=='checkout') && @$page != 'import_list' )style="cursor:not-allowed;" @else required="" data-parsley-required-message = 'Document type is required'; @endif>
                                    <option value="0">Select Document Types</option>
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
                            @if(($val->document_version_no != '1.0' || @$page=='checkout') && @$page != 'import_list' )
                                <div class="col-sm-1" style="padding-left:4px">
                                <span class="formInfo"><a href="{{url('doctypeSettingsEdit')}}" class="jTip" id="one" name="Note:">?</a></span>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                        <label for="document no" class="col-sm-12 control-label" id="docno_lbl"><?php echo $settings_document_no.':'; ?><span class="compulsary"> *</span></label>
                        <div class="col-sm-12">
                            <?php echo Form:: 
                            text('docno',$val->document_no, 
                            array( 
                            'class'         => 'form-control', 
                            'id'            => 'docno', 
                            'title'         => "'".$settings_document_no."'".$language['length_others'], 
                            'placeholder'   => ''.$settings_document_no.'', 
                            'data-parsley-maxlength' => $language['max_length'],                                   
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
                            <label for="document name" class="col-sm-12 control-label" id="docname_lbl"><?php echo $settings_document_name.':'; ?><span class="compulsary"> *</span></label>
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
                                'data-parsley-required-message' => 'This field is required',
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
                           <label for="Department" class="col-sm-12 control-label">{{$language['department']}}: <span class="compulsary"> *</span></label>
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
                            <label for="Stack" class="col-sm-12 control-label">{{$language['stack']}}: </label>
                            <div class="col-sm-12">
                                <select name="stack" id="stack" class="multipleSelect" data-parsley-required-message = 'Stack is required'; value="{{$val->stack_id}}">
                                    <?php
                                    foreach ($stacks as $key => $stck) {
                                        ?>
                                        <option value="<?php echo $stck->stack_id; ?>"<?php if(in_array($stck->stack_id,$arr_stack_id)){ echo "selected";}?>><?php echo $stck->stack_name;?></option>
                                        <?php
                                    }
                                    ?>
                                </select>

                                <?php /*<select name="stack[]" id="stack" class="multipleSelect" value="{{$val->stack_id}}" multiple>
                                    <?php
                                    foreach ($stack as $key => $value) {
                                        ?>
                                        <option value="<?php echo $value->stack_id; ?>" <?php if(in_array($value->stack_id,$arr_stack_id)){ echo "selected"; }  ?>><?php echo $value->stack_name;?></option>
                                        <?php
                                    }
                                    ?>
                                </select>    */ ?>                   
                                <span class="dms_error">{{$errors->first('docname')}}</span>
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
                               
                                
                                <span class="dms_error">{{$errors->first('docname')}}</span>
                            </div>
                        </div>   

                        <div class="form-group">
                            <label for="Tag Words" class="col-sm-12 control-label">{{$language['tag words']}}: </label>
                            <div class="col-sm-12">
                                <div id="keywd">
                                    <select name="keywords" id="keywords" class="form-control">
                                        <option></option>
                                    </select>
                                </div>
                                <div id="reskeywrds"></div>
                                
                                <span class="dms_error">{{$errors->first('docname')}}</span>
                            </div>
                        </div>   
            
                    
                        <div id="sublist"></div> 
                        <div id="stacksublist"></div>   

                        <div class="form-group">
                            <label for="Tag Words" class="col-sm-12 control-label">{{$language['cur_ver']}}: </label>
                            <div class="col-sm-12">
                                <input type="text" readonly="readonly" class="form-control" name="curver" value="{{ $val->document_version_no }}">
                            </div>
                        </div>   

                        <div class="form-group">
                            <label for="Uploaded File" class="col-sm-12 control-label">{{$language['uploaded file']}}:</label>
                            <div class="col-sm-12">
                                <div>
                                    <input type="text" id="fileLabel" name="fileLabel" class="form-control" value="{{$val->document_file_name}}" readonly required="" data-parsley-required-message = 'Upload file is required'>
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
                                    <input type="text" class="form-control" value="{{$val->document_expiry_date}}" id="doc_exp_date" name="document_expiry_date" placeholder="YYYY-MM-DD" style="position: initial;">
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
                    <!-- Document assign feature -->
                    <!-- document assign is only changed by the assigned user, this field is visible to him at the time of edit document-->
                    @if($val->document_created_by == Auth::user()->username)
                        <div class="form-group">
                            {!! Form::label($language['doc_assigned_to'].':', '', array('class'=> 'col-sm-12 control-label'))!!}
                            <div class="col-sm-12">
                                <select name="assign_users" id="assign_users" class="form-control">
                                <option value="">Select a User</option>
                                    <?php
                                    foreach ($users as $key => $user) {
                                        ?>
                                        <option value="<?php echo $user->username; ?>"<?php if($user->username == $val->document_assigned_to){ echo "selected";}?>><?php echo $user->username;?>@if(Auth::user()->user_role == Session::get("user_role_super_admin")) - {{ @$user->departments[0]->department_name }} @endif</option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                <span class="dms_error"></span>
                            </div>
                        </div> 
                    @endif
                    <input type="hidden" name="hidden_status" value="{{$val->document_status}}">
                    <input type="hidden" name="hidden_assign_user" value="{{$val->document_assigned_to}}">
                    <!-- document assign feature end -->
                    <div class="form-group">
                        <label for="Document Folder" class="col-sm-12 control-label">{{$language['upload folder']}}: <span class="compulsary"> *</span></label>
                        <div class="col-sm-12">
                            <div>
                                <input type="text" id="up_folder" name="up_folder" class="form-control" value="{{trim(preg_replace('/\s*\([^)]*\)/', '', $val->document_path))}}" readonly required="" data-parsley-required-message = 'Document folder is required'>
                                <input type="hidden" name="hidd_folder_id" id="hidd_folder_id" value="{{$val->parent_id}}">
                            </div>
                            <div class="box resizewrapper">
                                <div id="tree-container" style="padding:5px 0px;overflow: auto;min-height: 190px;">
                                </div>
                            </div>              
                        </div>
                    </div>   
                    <?php } ?> 

                    @if(@$page == 'checkout')
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
                    @endif  

                    <!--Validation message shows-->
                    <div class="form-group">
                        <label class="col-sm-12 control-label" id="error-msg" style="color:red;text-align:center; margin-top:10px"></label>
                    </div>
                </div><!-- // form-first -->

                    <!--Buttons-->
                    <div class="form-group">
                        <!-- <div class="col-sm-12" style="text-align:center;">
                            <input type="submit" value="{{$language['check in and publish']}}" name="Checkin" class="btn btn-primary">
                            <input type="submit" value="{{$language['check in as draft']}}" name="Draft" class="btn btn-primary">
                            @if($val->document_pre_status=="Draft")
                            <input type="submit" value="{{$language['discard check out']}}" name="Discard_draft" class="btn btn-primary">
                            @else
                            <input type="submit" value="{{$language['discard check out']}}" name="Discard_published" class="btn btn-primary">
                            @endif
                        </div> -->
 
                        <div class="col-sm-11" style="text-align:right; margin-top:10px">

                            <!--Save btns-->
                            <!--For edit document-->
                            @if(@$page == 'document_edit' || $page == 'document_edit')
                                <input type="submit" value="{{$language['save']}}" name="save" class="btn btn-primary save-btn">
                            @endif
                            <!--For inport list edit -->
                            @if($page == 'import_list')
                                <input type="submit" value="{{$language['save']}}" name="save" id="save-implist-btn" class="btn btn-primary save-btn">
                                <input type="submit" value="{{$language['save_and_close']}}" id="save-and-close-implist-btn" name="save"  class="btn btn-primary save-btn">
                            @endif
                            <!--For checkout list edit -->

                            @if($page == 'checkout')
                                <!--The value names inside submit buttons are used for check file required validation.If you change the language name you should change that same name in script also(validate upload document ). -->
                                <input type="submit" value="{{$language['check in and publish']}}" name="Submit_Btn" class="check_in_and_oublish btn btn-primary save-btn" id="validate" val='1'>
                                <input type="submit" value="{{$language['check in as draft']}}" name="Submit_Btn" id="validate" val='2' class="check_in_as_draft btn btn-primary save-btn">
                                @if($val->document_pre_status=="Draft")
                                <input type="submit" value="{{$language['discard check out']}}" name="Discard_draft" val='3' id="validate" class="discard_check_out btn btn-primary">
                                <input type="hidden" value="Discard Check Out" name="Submit_Btn" class="discard_check_out_hidden">
                                @else
                                <input type="submit" value="{{$language['discard check out']}}"  name="Submit_Btn" val='4' id="validate" class="discard_check_out btn btn-primary">
                                <input type="hidden" value="Discard Check Out" name="Submit_Btn" class="discard_check_out_hidden">
                                @endif
                             @endif

                             <!--Cancel btn-->
                             <a href="{{url($cancelBtnUrl)}}" value="{{$language['cancel']}}" name="cancel" class="btn btn-primary btn-danger" id = "cn">{{$language['cancel']}}</a>
                
                        </div>

                    </div><!-- /.col -->
                    {!! Form::close() !!}        
                </div>
            </div>
    <div class="col-md-8">
        <a href="{{$cancelBtnUrl}}">
            <button class="btn btn-primary" style="margin-bottom: 5px;">{{$language['back']}}</button>
        </a> 
            <!-- Add notes start-->
        <a class="btn btn-primary" id="notes" style="margin-bottom: 5px;">{{$language['notes']}}</a>&nbsp;
        @if (Session::get('module_activation_key4')==Session::get('tval'))  
            @if(stristr($user_permission,"workflow"))
            <a class="btn btn-primary" id="workflow" data-toggle="modal" data-target="#addview_workflow" style="padding: 2px 12px !important;margin-bottom:5px;" title="{{$language['add/view workflow']}}">{{$language['add/view workflow']}}</a>
            @endif
        @endif
        <div class="row" id="mini-documents-add" style="width:91%">
            <div id="mini-doc-col">
                <div class="box box-primary" id="box">
                    <div id="notesbox" class="dispoff">
                    @if(count($noteList)>0)
                    <div id="notes-view">
                        <table class="sidbox">
                            <th class="head" colspan="3">{{$language['notes']}}
                                <a class="closebox" title="Close"><i class="fa fa-close"></i></a>
                                @if(stristr($user_permission,"add"))
                                    <a id="editnote"><i class="fa fa-plus"></i></a>   
                                @endif
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
                                        <input type="button" id="editnote" class="btn btn-primary btn-danger cancel-btn" value="{{$language['cancel']}}">
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
                                        <input type="button" id="notesave" class="btn btn-info btn-flat"  value="{{$language['save']}}">
                                        <input type="button" id="editnote" class="btn btn-primary btn-danger cancel-btn" value="{{$language['cancel']}}">
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
                <div class="box box-info">
                    <div class="box-body">
                        <div id="pdfrender">
                    @foreach($dglist as $key => $dval)
                       <?php 
                       $data=$dval->document_file_name; 
                       if(file_exists($check_path.$data) && $data)
                       {
                        

                        //$ext = substr($data, strpos($data, ".") + 1);   
                         $ext = pathinfo($data, PATHINFO_EXTENSION);
                        $ext = strtolower($ext); 
                        ?>
                       
                       <?php if($ext=='tiff'||$ext=='tif'||$ext=='TIFF'||$ext=='TIF'){?>
                       <script type="text/javascript">
                            var path='<?php echo $data;?>';
                            $('#pdfrender').html('<div id="output"></div>');
                            Tiff.initialize({TOTAL_MEMORY: 16777216 * 10});
                            var xhr = new XMLHttpRequest();
                            xhr.responseType = 'arraybuffer';
                            xhr.open('GET', "{{ $doc_url }}"+path);
                            xhr.onload = function (e) {
                                var buffer = xhr.response;
                                var tiff = new Tiff({buffer: buffer});
                                for (var i = 0, len = tiff.countDirectory(); i < len; ++i) {
                                    tiff.setDirectory(i);
                                    var canvas = tiff.toCanvas();
                                    $('#output').append(canvas);
                                }
                            };
                            xhr.send();
                        // var xhr = new XMLHttpRequest();
                        // xhr.responseType = 'arraybuffer';
                        // xhr.open('GET', "{{ config('app.doc_url') }}"+path);
                        // xhr.onload = function (e) {
                        //     var tiff = new Tiff({buffer: xhr.response});
                        //     var canvas = tiff.toCanvas();
                        //     if (canvas) {
                        //   $('#output').empty().append(canvas);
                        // }
                        // };
                        // xhr.send();

                       </script>
                       <?php }
                       else if($ext=='doc'||$ext=='docx'||$ext=='xls'||$ext=='xlsx'){
                            $connected = @fsockopen("www.google.com", 80); 
                            if ($connected){
                                $is_conn = true; //action when connected
                                ?>
                                    <div class="resizable" style="width:100%; height:100vh;"><iframe src="https://docs.google.com/gview?url={{ $doc_url }}{{ $dval->document_file_name }}&embedded=true" width="auto" height="auto;" class="resizewrapperiframe"></iframe></div>
                                <?php
                                fclose($connected);
                            }else{
                                $is_conn = false; //action in connection failure
                                ?>
                                    <div>{{$language['no_internet']}}</div>  
                                <?php
                            }
                        
                        }
                        else if($ext=='dwg'){
                                ?>
                                <!-- old cad viewer -->
                                    <!-- <div class="resizable"><iframe src="cadView?file={{ @$dval->document_file_name }}" width="auto" height="auto;" class="resizewrapperiframe"></iframe></div> -->
                                <!-- new sharecad -->
                                    <div class="resizable"><iframe src="https://sharecad.org/cadframe/load?url={{ $doc_url }}{{ @$dval->document_file_name }}" width="auto" height="auto;" class="resizewrapperiframe"></iframe></div>
                                <?php
                        }
                        else if($ext=='gif'||$ext=='jpg'||$ext=='jpeg'||$ext=='png'){ ?>                        
                            <!-- <img class="zoom" src="{{ config('app.url') }}/storage/documents/{{ $dval->document_file_name }}"> -->
                            <!--Image preview-->
                            <div id="viewer" class="viewer"></div>
                            <!--Image preview-->
                        <?php }else if($ext=='mp4'||$ext=="ogv"||$ext=='webm'||$ext=='flv'){
                        //alert("heloo");
                        if($ext=='mp4'){ ?>
                            <div class="resizable"><video id="player1" style="width:100%;" poster="" preload="none" controls playsinline webkit-playsinline><source src="{{ $doc_url }}{{ $dval->document_file_name }}" type="video/mp4"></video></div>  
                        <?php }else if($ext=='ogv'){ ?>
                            <div class="resizable"><video id="player1" style="width:100%;" poster="" preload="none" controls playsinline webkit-playsinline><source src="{{ $doc_url }}{{ $dval->document_file_name }}" type="video/ogg"></video></div>
                        <?php }else if($ext=='webm'){ ?>
                            <div class="resizable"><video id="player1" style="width:100%;" poster="" preload="none" controls playsinline webkit-playsinline><source src="{{ $doc_url }}{{ $dval->document_file_name }}" type="video/webm"></video></div>
                        <?php }else if($ext=='flv'){ ?>
                            <!-- <div class="resizable"><video id="player1" style="width:100%;" poster="" preload="none" controls playsinline webkit-playsinline><source src="{{ config('app.doc_url') }}{{ $dval->document_file_name }}" type="video/flv"></video></div> -->
                            <script type="text/javascript" src="http://www.webestools.com/page/js/flashobject.js"></script>
                                <div id="player_2065" style="display:inline-block;">
                                    <a href="http://get.adobe.com/flashplayer/">You need to install the Flash plugin</a>
                                </div>
                                <script type="text/javascript">
                                    var flashvars_2065 = {};
                                    var params_2065 = {
                                        quality: "high",
                                        wmode: "transparent",
                                        bgcolor: "#ffffff",
                                        allowScriptAccess: "always",
                                        allowFullScreen: "true",
                                        flashvars: "fichier={{ $doc_url }}{{ @$dval->document_file_name }}"
                                    };
                                    var attributes_2065 = {};
                                    flashObject("http://flash.webestools.com/flv_player/v1_28.swf", "player_2065", "720", "405", "8", false, flashvars_2065, params_2065, attributes_2065);
                                </script>
                        <?php }           
                    }else if($ext=='mp3'||$ext=="wav"||$ext=='ogg'){
                        if($ext=='mp3'){ ?>
                            <div class="resizable"><audio id="player2" preload="none" controls style="width:100%;"><source src="{{ $doc_url }}{{ $dval->document_file_name }}" type="audio/mp3"></audio></div>
                        <?php }else if($ext=='wav'){ ?>
                            <div class="resizable"><audio id="player2" preload="none" controls style="width:100%;"><source src="{{ $doc_url  }}{{ $dval->document_file_name }}" type="audio/wav"></audio></div>
                        <?php }else if($ext=='ogg'){ ?>
                            <div class="resizable"><audio id="player2" preload="none" controls style="width:100%;"><source src="{{ $doc_url }}{{ $dval->document_file_name }}" type="audio/ogg"></audio></div>
                        <?php }      
                    }else if($ext=='zip' || $ext == 'rar'){  ?>
                        <!-- <div class="resizable">No preview available.</div> -->
                        <div><div class="info-box"><span class="info-box-icon bg-aqua"><i class="icon fa fa-file-archive-o"></i></span><div class="info-box-content"><span class="info-box-text" style="text-align: left !important;">This is a compressed document.</span><span style="display: block;font-size: 15px;margin-top: 18px;text-align: left !important;"><a href="{{ $doc_url }}{{ @$dval->document_file_name }}" download>Click here to download the document</a></span></div></div></div>
                    <?php }else{ ?>      
                        <div class="resizable" style="width:100%; height:100vh;"><iframe src="webviewer?file={{ $doc_url }}{{ $dval->document_file_name }}&dcno={{$id}}" width="auto" height="auto;" class="resizewrapperiframe"></iframe></div>              
                    <?php } } else { echo '<span><b>'.@$dval->document_file_name."</b></span><span style='color:red'> file does not exist".'</span>'; }?>                        
                       @endforeach
                        </div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>
        </div>
        <div class="modal fade" id="addview_workflow" data-backdrop="true" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div id="content_wf"></div>
        </div>
        </div>
    </section>
</section><!-- /.content --> 


<!--For image rotation-->
@if(Request::segment(1) == 'editAllDocument' || Request::segment(1) == 'editcheckoutDocument' || Request::segment(1) == 'documentEdit')
    <!--For image rotation-->
    {!!Html::script('js/rotate-zoom/jqueryui.js')!!}
    {!!Html::script('js/rotate-zoom/jquery.browser.js')!!}
    {!!Html::script('js/rotate-zoom/jquery.mousewheel.min.js')!!}
    {!!Html::script('js/rotate-zoom/jquery.iviewer.js')!!}
@endif

<script>

$(document).ready(function(){
    /*<!--Zoomin zoomout-->*/
    var isFileExists = "{{file_exists(public_path('images/test/'.$dval->document_file_name))}}";
    if(isFileExists){
        //<!--If file exists,show resized image-->
        var src = 'images/test/'+"{{ $dval->document_file_name }}";
    }else{
        //<!-- Show original image from the right path--> 
        var src = "{{ $doc_url }}{{ $dval->document_file_name }}";
    }
    
    // Initilize here
    var iv = $("#viewer").iviewer({
        src: src
    });
    /*<!--Zoomin zoomout-->*/

    /*<!--To disable right click on image after load it-->*/ 
    $('body').bind('contextmenu', function(e) {
        return false;
    });
});

var NoteSaveUrl = "<?php echo @$noteSaveUrl;?>";

<!--// checkin -->
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

<?php if(@$page == 'checkout'):?>
var max_file_size = "{{$language['max_upload_size']}}";
max_file_size = max_file_size.slice(0, -2); //remove M from string
    Dropzone.autoDiscover = false;
    var baseUrl = "{{ url('/') }}";
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var myDropzone = new Dropzone("div#dropzoneFileUpload", {
        type:'post',
        params: {_token:CSRF_TOKEN,module:'documentEdit'},
        url: baseUrl+"/dropzone/uploadFiles",
        paramName: 'file',
        maxFiles: 1,
        clickable: true,
        maxFilesize: max_file_size,
        uploadMultiple: false,
        addRemoveLinks: true,
        success:function(file,response,data){
        if(response=='invalidname')
            {   
                
                $(file.previewElement).find('.dz-error-message').text("{{trans('documents.invalidname')}}").css('opacity','1').css('display','block').css('top','90px');
                $(file.previewElement).find('.dz-error-mark').css('opacity','1').css('display','block');
                $(file.previewElement).find('.dz-filename').css('font-weight', 'bold');
            }    
        if(response=='invalid')
            {   
                $(file.previewElement).find('.dz-error-mark').css('opacity','1').css('display','block');
                alert ('{{$language['upload error']}}');}
        if(response=='exists')
            {   
                $(file.previewElement).find('.dz-error-mark').css('opacity','1').css('display','block');
                alert ('{{$language['doc_already_exist']}}');}
        if(response=='ftpinvalid')
            {   
                $("#file_error").html('Error in ftp connection. Make sure the ftp credentials is correct or try changing it to http in the settings.');
            }
        else{    
        var rand_name=file.xhr.response;
        $("#hidd_file").val(rand_name);
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
                            url: '{{URL('removeDocument')}}',
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
            //$("#pdfrender").html('<div class="resizable"><iframe src="https://docs.google.com/gview?url={{ $doc_url }}'+rand_name+'&embedded=true" width="100%" height="800px;"></iframe></div>');
            }
            else
            {
                $("pdfrender").html('<div> No internet conn</div>');
            }
        }
        else
        {
            //$("#pdfrender").html('<div class="resizable"><iframe src="{{ $doc_url }}'+rand_name+'?#toolbar=0" id="iframe" width="100%" height="800px;"></iframe></div>');
        }
        var org_name=$(".dz-filename").text();
        // remove extension.
        var n = org_name.indexOf('.');
        org_name = org_name.substring(0, n != -1 ? n : org_name.length);
        //$("#docname").val(org_name);
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
                    //$("#docname").val(null);
                     $("#hidd_file").val(null);
                    //$('#pdfrender').html('No document found');  
                    swal ('{{$language['success_remove_document']}}');
                }    
            }
        })
    });

    $( "#documentManagementAddForm" ).submit(function( event ) {
        var docNo    = $('#hidd_doc').val();
        var docname  = $('#docname').val();
        var check    =$("#hidd_file").val();
        var val      = $("input[type=submit][clicked=true]").val();
        var inputDatas = $('#documentManagementAddForm').serialize();
    
        if(val != 'Discard Check Out'){

            $('.discard_check_out_hidden').attr('name','');

        if(check==""||check==null)
            {   
                $("#file_error").text("Document file is required");
                $('#error-msg').html('<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Document file is required');
                return false;
            }
            } else{
                $('.discard_check_out_hidden').attr('name','Discard_published');
                swal({
                  title: 'Do you want to discard the Check Out of '+'"'+docname+'"'+'',
                  type: "{{$language['Swal_warning']}}",
                  showCancelButton: true
                }).then(function (result) {
                    if(result){
                        // Success
                        $.ajax({
                            type: 'POST',
                            url:base_url+'/checkout/'+docNo,
                            data:inputDatas,
                            success:function(response){

                                swal({
                                      title: "{{$language['check_out_discarded_success']}}",
                                      type: "{{$language['Swal_warning']}}",
                                      showCancelButton: false
                                    }).then(function (result) {
                                        if(result){
                                            // Success
                                            window.location.href = base_url+"/documents";
                                        }
                                    });
                          }
                        });
                        return true;
                        
                    }
                },function (dismiss) {
                  // dismiss can be 'cancel', 'overlay',
                  if (dismiss === 'cancel') {
                    return false;
                  }
                });
                return false;
            }
       
    });


<?php endif;?>

$(document).ready(function() {
    $('video, audio').mediaelementplayer();
    // Form validation focus out property
    $('.save-btn').click(function(){

        // Validation doc type and department(scroll top not working in select box)
        var departmentid = $('#departmentid').val();
        if(departmentid == null){
            $('#error-msg').html('<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Please fill Department');
        }
        var doctypeid = $('#doctypeid').val();
        if(doctypeid == null){
            $('#error-msg').html('<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Please fill Document type');
        }

        var stackid = $('#stack').val();
        if(stackid == null){
            $('#error-msg').html('<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Please fill Stack');
        }

        // Remove error msg
        if(departmentid && doctypeid && stackid){
            $('#error-msg').html('');
        }

    });

    //<!--Import list edit-->
    $('#save-implist-btn').click(function(){
        var id     = $('#hidd_doc').val();
        var action = base_url+'/editDocument/'+id+'?uniqueId='+id+'';
        $('#documentManagementAddForm').attr('action',action);
    });

    $('#save-and-close-implist-btn').click(function(){
        var id     = $('#hidd_doc').val();
        var action = base_url+'/editDocument/'+id+'';
        $('#documentManagementAddForm').attr('action',action);
    });
    //<!--Import list edit ends-->

    //<!--Checkout list edit-->
    //validate upload document
    $( "#checkinbtn" ).click(function() {
    var check=$("#hidd_file").val();
    var status=0;
    if(check==""||check==null)
        {   
            $(window).scrollTop($('#focus').offset().top);
            $("#file_error").text("<?php echo $language['no_file'];?>");
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
    
        // var docno=$('#docno').val();
        // if(docno==""||docno==null)
        //     {   
        //         $(window).scrollTop($('#form-docno').offset().top);
        //         $("#dp").text("Document number is required.");
        //         status=1;
        //     }else{
        //         $("#dp").text(" ");
        //     }

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

    
    $("form input[type=submit]").click(function() {
        $("input[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
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
            var page = document.getElementById('page').value;
            if(page){
                var page = page;
            }else{
                var page = '';
            }
        
            if($("#doctypeid").val()==null){
                var value = 0; 
            }else{
                var value = $("#doctypeid").val();
            }
            // Document list edit
            <?php if(@$page == 'document_edit'):?>
            $.ajax({
                type: 'post',
                url: '{{URL('documentsSubListCheckout')}}',
                data: {_token: CSRF_TOKEN, doctypeid: value, doc_id:docid, page:page },
                timeout: 50000,
                beforeSend: function() {
                    if(value!=0){
                        $.ajax({
                            type: 'post',
                            url: '{{URL('getDocumentIndexFields')}}',
                            data: {_token: CSRF_TOKEN, doctypeid: value },
                            timeout: 50000,
                            success: function(data, status){
                                if(data) {
                                    if(data['document_type_column_no']){
                                        $("#docno").attr("placeholder", data['document_type_column_no']);
                                        $("#docno_lbl").html(data['document_type_column_no']);
                                    }else{
                                        var field1 = $("#field1").val();
                                        $("#docno_lbl").html(field1);
                                        $("#docno").attr("placeholder", field1);
                                    }
                                    if(data['document_type_column_name']){
                                        $("#docname").attr("placeholder", data['document_type_column_name']);
                                        $("#docname_lbl").html(data['document_type_column_name']+'<span class="compulsary"> *</span>');
                                    }else{
                                        var field2 = $("#field2").val();
                                        $("#docname_lbl").html(field2+'<span class="compulsary"> *</span>');
                                        $("#docname").attr("placeholder", field2);
                                    }
                                }
                            }
                        });
                    }else{
                        var field1 = $("#field1").val();
                        $("#docno_lbl").html(field1);
                        $("#docno").attr("placeholder", field1);
                        var field2 = $("#field2").val();
                        $("#docname_lbl").html(field2+'<span class="compulsary"> *</span>');
                        $("#docname").attr("placeholder", field2);
                    }
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
            <?php endif;?> 

            <?php if(@$page == 'import_list'):?>
            // Import list edit
            $.ajax({
                type: 'post',
                url: '{{URL('documentsSubListEdit')}}',
                data: {_token: CSRF_TOKEN, doctypeid: value, doc_id:docid },
                timeout: 50000,
                beforeSend: function() {
                    if(value!=0){
                        $.ajax({
                            type: 'post',
                            url: '{{URL('getDocumentIndexFields')}}',
                            data: {_token: CSRF_TOKEN, doctypeid: value },
                            timeout: 50000,
                            success: function(data, status){
                                if(data) {
                                    if(data['document_type_column_no']){
                                        $("#docno").attr("placeholder", data['document_type_column_no']);
                                        $("#docno_lbl").html(data['document_type_column_no']);                                    
                                    }else{
                                        var field1 = $("#field1").val();
                                        $("#docno_lbl").html(field1);
                                        $("#docno").attr("placeholder", field1);
                                    }
                                    if(data['document_type_column_name']){
                                        $("#docname").attr("placeholder", data['document_type_column_name']);
                                        $("#docname_lbl").html(data['document_type_column_name']+'<span class="compulsary"> *</span>');
                                    }else{
                                        var field2 = $("#field2").val();
                                        $("#docname_lbl").html(field2+'<span class="compulsary"> *</span>');
                                        $("#docname").attr("placeholder", field2);
                                    }
                                }
                            }
                        });
                    }else{
                        var field1 = $("#field1").val();
                        $("#docno_lbl").html(field1);
                        $("#docno").attr("placeholder", field1);
                        var field2 = $("#field2").val();
                        $("#docname_lbl").html(field2+'<span class="compulsary"> *</span>');
                        $("#docname").attr("placeholder", field2);
                    }
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
            <?php endif;?> 

            <?php if(@$page == 'checkout'):?>
            // Checkout list edit
            $.ajax({
                type: 'post',
                url: '{{URL('documentsSubListCheckout')}}',
                data: {_token: CSRF_TOKEN, doctypeid: value, doc_id:docid },
                timeout: 50000,
                beforeSend: function() {
                    if(value!=0){
                        $.ajax({
                            type: 'post',
                            url: '{{URL('getDocumentIndexFields')}}',
                            data: {_token: CSRF_TOKEN, doctypeid: value },
                            timeout: 50000,
                            success: function(data, status){
                                if(data) {
                                    if(data['document_type_column_no']){
                                        $("#docno").attr("placeholder", data['document_type_column_no']);
                                        $("#docno_lbl").html(data['document_type_column_no']);                                    
                                    }else{
                                        var field1 = $("#field1").val();
                                        $("#docno_lbl").html(field1);
                                        $("#docno").attr("placeholder", field1);
                                    }
                                    if(data['document_type_column_name']){
                                        $("#docname").attr("placeholder", data['document_type_column_name']);
                                        $("#docname_lbl").html(data['document_type_column_name']+'<span class="compulsary"> *</span>');
                                    }else{
                                        var field2 = $("#field2").val();
                                        $("#docname_lbl").html(field2+'<span class="compulsary"> *</span>');
                                        $("#docname").attr("placeholder", field2);
                                    }
                                }
                            }
                        });
                    }else{
                        var field1 = $("#field1").val();
                        $("#docno_lbl").html(field1);
                        $("#docno").attr("placeholder", field1);
                        var field2 = $("#field2").val();
                        $("#docname_lbl").html(field2+'<span class="compulsary"> *</span>');
                        $("#docname").attr("placeholder", field2);
                    }
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

            <?php endif;?> 

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
                    $('#up_folder').val(path.replace(/ *\([^)]*\) */g, ""));
                }); 

    $(function ($) {
    //add/view workflow
    $('body').on('click','#workflow',function(){
        var docid      = '{{$id}}';
        var view = "{{Session::get('view')}}";
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            type: 'get',
            url: 'addviewWorkflow',
            dataType: 'html',
            data: {_token: CSRF_TOKEN,docid:docid,view:view},
            timeout: 50000,
            success: function(data, status){     
                $("#content_wf").html(data);
            },
            error: function(jqXHR, textStatus, errorThrown){
              console.log(jqXHR);    
              console.log(textStatus);    
              console.log(errorThrown);    
            }
        });  
    });

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
        $("#stack").change(function(){
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            if($("#stack").val()==null){
                var value = 0; 
            }else{
                var value = $("#stack").val();
            }
            $.ajax({
                type: 'post',
                url: '{{URL('stackSubList')}}',
                data: {_token: CSRF_TOKEN, stackid: value },
                timeout: 50000,
                beforeSend: function() {
                    
                },
                success: function(data, status){
                    //alert(data);
                    if(data) {
                        $("#stacksublist").html(data);
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
                    if(value!=0){
                        $.ajax({
                            type: 'post',
                            url: '{{URL('getDocumentIndexFields')}}',
                            data: {_token: CSRF_TOKEN, doctypeid: value },
                            timeout: 50000,
                            success: function(data, status){
                                if(data) {
                                    if(data['document_type_column_no']){
                                        $("#docno").attr("placeholder", data['document_type_column_no']);
                                        $("#docno_lbl").html(data['document_type_column_no']);                                    
                                    }else{
                                        var field1 = $("#field1").val();
                                        $("#docno_lbl").html(field1);
                                        $("#docno").attr("placeholder", field1);
                                    }
                                    if(data['document_type_column_name']){
                                        $("#docname").attr("placeholder", data['document_type_column_name']);
                                        $("#docname_lbl").html(data['document_type_column_name']+'<span class="compulsary"> *</span>');
                                    }else{
                                        var field2 = $("#field2").val();
                                        $("#docname_lbl").html(field2+'<span class="compulsary"> *</span>');
                                        $("#docname").attr("placeholder", field2);
                                    }
                                }
                            }
                        });
                    }else{
                        var field1 = $("#field1").val();
                        $("#docno_lbl").html(field1);
                        $("#docno").attr("placeholder", field1);
                        var field2 = $("#field2").val();
                        $("#docname_lbl").html(field2+'<span class="compulsary"> *</span>');
                        $("#docname").attr("placeholder", field2);
                    }
                },
                success: function(data, status){
                	//alert(data);
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
                url: base_url+'/'+NoteSaveUrl+'',
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

      $("#stack123").on('change',function() {
            var stckid = $("#stack").val();
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: 'post',
                url: '{{URL('loadStack')}}',
                data: {_token:CSRF_TOKEN,stckid:stckid},
                timeout: 50000,
                beforeSend: function() {
                    
                },
                success: function(data){
                    $("#stackDiv").html(data);
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
</script>
{!! Html::script('dist/jstree.min.js') !!}
{!! Html::style('dist/style.min.css') !!}
<script src="js/tiff.min.js"></script>  
{!!Html::style('build/mediaelementplayer.css')!!}
{!!Html::script('build/mediaelement-and-player.js')!!}
{!!Html::script('build/demo.js')!!}
@endsection
