<?php
include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')
@section('main_content')

{!! Html::script('js/wheelzoom.js') !!}  

{!! Html::script('js/parsley.min.js') !!}  
{!! Html::script('js/jquery.form.js') !!}   
{!! Html::style('css/font-awesome.min.css') !!} 
{!! Html::style('css/ionicons.min.css') !!} 
{!! Html::script('js/dropzone.js') !!}  
<!--{!! Html::script('js/build.min.js') !!} 
{!! Html::style('css/build.min.css') !!} -->
{!! Html::style('css/fastselect.min.css') !!} 
{!! Html::script('js/fastselect.standalone.js') !!}   
{!! Html::style('css/dropzone.min.css') !!}
 
{!!Html::style('build/mediaelementplayer.css')!!}
{!!Html::script('build/mediaelement-and-player.js')!!}
{!!Html::script('build/demo.js')!!} 

<script type="text/javascript">
$(document).ready(function(){
$('video, audio').mediaelementplayer();
wheelzoom(document.querySelector('img.zoom'));			 
			
});
</script>
<style>
	.zoom {
        cursor: zoom-in;
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
    .form-horizontal .control-label{
    	text-align: left;
    }
    }
    .box{
        height: 195px;
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

      /*<--Mobile view-->*/
      @media(max-width:522px){
      	#notesave{
      		width:20% !important;
      	}
      	.cancel-btn{
      		width:20% !important;
      	}
      }

      /*<--Mobile view-->*/
      @media(max-width:522px){
      	.cancel-btn{
      		width:23% !important;
      	}
      }

      @media(max-width:370px){
      	#ciad {
		    margin-top: 5px !important;
		}
		#cn {
		    margin-top: 4px !important;
		}
      }

      @media(max-width:437px){
      	#cn {
		    margin-top: 2px !important;
		}
      }
      /*Note*/
      @media(max-width:991px){
	      div#box {
		    overflow-x: auto;
		    height: 154px !important;
		}
	}
	.form-group {
	    margin-bottom: 5px;
	}

	/*Validation message style only for doc type*/
	#doctypeid.li.parsley-required {
	    position: absolute;
	    bottom: -16px;
	}
</style>

<!-- Content Wrapper. Contains page content -->
    <section class="content-header">
        <div class="col-md-8">
        <strong>
            {{$language['documents']}}
            <small>- {{$language['add new']}} {{$language['document']}}</small>
        </strong>
       </div>
       <div class="col-md-4"> 
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i>{{$language['home']}}</a></li>
            <li class="active">{{$language['documents']}}</li>
        </ol>
       </div>
        <input type="hidden" id="docid" value=0>            
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
	            {!! Form::open(array('url'=> array('addNewDocument','0'), 'files'=>true, 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'documentManagementAddForm', 'id'=> 'documentManagementAddForm','data-parsley-validate'=> '')) !!}
	            
	            <div class="form-group" id="focus">
	                <label for="UploadDocument" class="col-sm-12 control-label">{{$language['upload document']}}: <span class="compulsary">*</span></label>                
	                <div class="col-sm-12">
	                    <div class="dropzone" id="dropzoneFileUpload"></div>
	                    <input type="hidden" name="hidd_file" id="hidd_file" value="">
	                    <input type="hidden" name="hidd_file_size" id="hidd_file_size" value="">
	                     <input type="hidden" name="page"  value="{{ Input::get('page') }}">
	                    <span class="dms_error compulsary" id="file_error"></span>       
	                </div>
	                <div class="col-sm-12">
	                <!-- connection checking -->
	                <?php $connected = @fsockopen("www.google.com", 80); 
                        if ($connected)
                        {
                            $is_conn = true; //action when connected
                        }
                        else
                        {
                        	$is_conn = false; //action when connected 
                        }
                    ?>
	                @if(Session::get('settings_file_extensions')) <!-- allowed extensions coming from tbl_settings table -->
	                    <span class="text-warning">{{$language['allowed_file_extensions']}}:
	                    @if($is_conn === true) 
	                    .doc, .docx, .xls, .xlsx, 
	                    @endif
	                    {{Session::get('settings_file_extensions')}}</span> 
	                @endif
	                @if(Session::get("settings_ftp_upload") != 1)
	                <span class="text-warning">{{$language['max upload msg']}}
                     {{$language['max_upload_size']}}</span> 
	                @endif
	               </div>  
	            </div>
	            <div class="form-group" id="form-doctypeid">
	                    <label for="DocTypes" class="col-sm-12 control-label">{{$language['document types']}}: <span class="compulsary">*</span></label>
	                    <div class="col-sm-12">
	                        <select name="doctypeid" id="doctypeid" class="multipleSelect form-control" required="" data-parsley-required-message = 'Document type is required'>
	                        <option value="0">Select a Document Type</option>
	                            <?php
	                            foreach ($docType as $key => $dType) {
	                                ?>
	                                <option value="<?php echo $dType->document_type_id; ?>"><?php echo $dType->document_type_name;?></option>
	                                <?php
	                            }
	                            ?>
	                        </select>   
	              
	                    </div>
	                </div>
	                <input type="hidden" name="field1" value="<?php echo $settings_document_no; ?>" id="field1">
           			<input type="hidden" name="field2" value="<?php echo $settings_document_name; ?>" id="field2">

	                <div class="form-group">
	                <label for="Docno" class="col-sm-12 control-label" id="docno_lbl"><?php echo $settings_document_no.':'; ?> <span class="compulsary">*</span></label>      
	                <div class="col-sm-12">
	                    <?php echo Form:: 
	                    text('docno','', 
	                    array( 
	                    'class'=> 'form-control', 
	                    'id'=> 'docno', 
	                    'title'=> "'".$settings_document_no."'".$language['length_others'], 
	                    'placeholder'=> ''.$settings_document_no.'',     
	                    'required'               => '',
	                    'data-parsley-maxlength' => $language['max_length'],
	                    'data-parsley-required-message' => 'This field is required',                                    
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
	                <label for="Docname" class="col-sm-12 control-label" id="docname_lbl"><?php echo $settings_document_name.':'; ?> <span class="compulsary">*</span></label>
	                <div class="col-sm-12">
	                    <?php echo Form:: 
	                    text(
	                    'docname', Session::get("sess_filename"), 
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
	            <div id="sublist"></div>
	            <div class="form-group">
	                    <label for="Department" class="col-sm-12 control-label">@if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{$language['department']}} @endif: <span class="compulsary">*</span></label>
	                    <div class="col-sm-12">
	                        <select name="departmentid[]" id="departmentid" class="multipleSelect" multiple required="" data-parsley-required-message = '@if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{$language['department']}} @endif {{$language['is_required']}}'>
	                            <?php
	                            foreach ($deptApp as $key => $dept) {
	                            ?>
	                            <option value="<?php echo $dept->department_id; ?>"><?php echo $dept->department_name;?></option>
	                            <?php
	                            }
	                            ?>
	                        </select>   
	              
	                    </div>
	                </div> 
	           
	            <div class="form-group" id="form-stack">
	                    <label for="stack" class="col-sm-12 control-label">{{$language['stack']}}: </label>
	                    <div class="col-sm-12">
	                        <select name="stack" id="stack" class="multipleSelect form-control" required="" data-parsley-required-message = 'Stack is required'>
	                        <option value="0">Select a Stack</option>
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
	            <div id="stackDiv"></div> 
	            <div class="form-group">
	                {!! Form::label($language['tag category'].':', '', array('class'=> 'col-sm-12 control-label'))!!}
	                <div class="col-sm-12">
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
	                {!! Form::label($language['tag words'].':', '', array('class'=> 'col-sm-12 control-label'))!!}
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

	             <div class="form-group">
                    <label class="col-sm-12 control-label">{{$language['expir_date']}}: </label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control" id="doc_exp_date" name="document_expiry_date" placeholder="YYYY-MM-DD">
                        </div><!-- /.input group -->
                    </div>
                    <div class="col-sm-4">
                    {!! Form::checkbox('doc_exp_chk',1, true, ['id'=> 'doc_exp_chk']) !!} {{$language['no expiry']}}
                    </div>
                </div>
                <!-- Document assign feature -->
                <div class="form-group">
	                {!! Form::label($language['doc_assigned_to'].':', '', array('class'=> 'col-sm-12 control-label'))!!}
	                <div class="col-sm-12">
	                    <select name="assign_users" id="assign_users" class="form-control">
	                    <option value="">Select a User</option>
	                        <?php
	                        foreach ($users as $key => $user) {
	                            ?>
	                            <option value="<?php echo $user->username; ?>"><?php echo $user->username;?>@if(Auth::user()->user_role == Session::get("user_role_super_admin")) - {{ $user->departments[0]->department_name }} @endif</option>
	                            <?php
	                        }
	                        ?>
	                    </select>
	                    <span class="dms_error"></span>
	                </div>
	            </div>
        		<div class="form-group">
                  <label class="col-sm-12 control-label">{{$language['assign_add_note']}}:</label>
                  <div class="col-sm-12">
                  <textarea class="form-control" name="note_assign" rows="3" placeholder="{{$language['assign_add_note']}}"></textarea>
                  </div>
                </div>   
	            <!-- document assign feature end -->
	            <div class="form-group">
	                <label for="Upload" class="col-sm-12 control-label">{{$language['upload folder']}}: <span class="compulsary">*</span></label>
	                <div class="col-sm-12">
	                    <div>
	                        <?php $str = Session::get('SESS_path');?>
	                        <input type="text" id="up_folder" name="up_folder" class="form-control" value="<?php echo trim(preg_replace('/\s*\([^)]*\)/', '', $str));?>" readonly required="" data-parsley-required-message = 'Upload folder is required'>
	                        <input type="hidden" name="hidd_folder_id" id="hidd_folder_id" value="{{Session::get('SESS_parentIdd')}}">
	                    </div>
	                    <div class="box resizewrapper">
	                    <div id="tree-container" style="padding:5px 0px;overflow: auto;min-height: 190px;"></div>
	                    </div>                
	                </div>
	            </div>    

	          	<!--Validation message shows-->
                <div class="form-group">
                    <label class="col-sm-12 control-label" id="error-msg" style="color:red;"></label>
                </div>

	            <div class="form-group">
	                <div class="col-sm-12" style="text-align:right;">
	                    <input type="submit" id="ciap" value="{{$language['check in and publish']}}" name="savepublish" class="btn btn-primary btn-save">
	                    <input type="submit" id="ciad" value="{{$language['check in as draft']}}" name="draft" class="btn btn-primary btn-save">
	                    @if(@$_GET['page'] == 'documents')
	                        <input type="button" value="{{$language['cancel']}}" name="cancel" class="btn btn-primary btn-danger" id ="cn" onclick=window.location="documents">
	                    @elseif(@$_GET['page'] == 'documentsListview')
	                        <input type="button" value="$language['cancel']" name="cancel" class="btn btn-primary btn-danger" id ="cn" onclick=window.location="documentsList">
	                     @elseif(@$_GET['page'] == 'viewappdata')
	                    @php 
	                    $app_doc_id = Input::get('doc_id');
	                    $app_id = Input::get('app_id');
	                    @endphp
	                    	<input type="hidden" name="app_doc_id"  value="{{ $app_doc_id }}">	
	                    	<input type="hidden" name="app_id"  value="{{ $app_id }}">
	                        <input type="button" value="{{ trans('language.cancel') }}" name="cancel" class="btn btn-primary btn-danger" id ="cn" onclick=window.location="@php echo url('viewappdata/'.$app_id.'/'.$app_doc_id); @endphp">
	                    @endif 
	                    
	                </div>
	            </div><!-- /.col -->
	            {!! Form::close() !!}        
	        </div>
	    </div>

	    <div class="col-md-6">
	    <!-- Add notes start-->
	    <a class="btn btn-primary" style="padding: 2px 12px !important; margin-bottom:5px;" id="notes" style="margin-bottom: 5px;">{{$language['notes']}}</a>
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
			                        <input type="button" id="notesave" class="btn btn-info btn-flat" style="margin-left:30%;float:left;" value="{{$language['save']}}">
			                        <input type="button" id="editnote" class="btn btn-primary btn-danger cancel-btn" style="margin-left:5px; float:left;" value="{{$language['cancel']}}">
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
			                        <input type="button" id="notesave" class="btn btn-info btn-flat" style="margin-left:30%;float:left;" value="{{$language['save']}}">
			                        <input type="button" id="editnote" class="btn btn-primary btn-danger cancel-btn" style="margin-left:5px; float:left;" value="{{$language['cancel']}}">
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
	   
	    	<div class="box box-info"">
                <div class="box-body>
                    <div id="pdfrender">
                    	&nbsp;&nbsp;{{$language['no_document_found']}}
                    </div>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
	    </div>
	</div>
    </section>
<!-- {!! Html::script('js/flashobject.js') !!} -->
<script type="text/javascript">
var max_file_size = "{{$language['max_upload_size']}}";
max_file_size = max_file_size.slice(0, -2); //remove M from string
console.log(max_file_size);
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
        params: {_token:CSRF_TOKEN,module:'documentAdd'},
        url: baseUrl+"/dropzone/uploadFiles",
        paramName: 'file',
        clickable: true,
        maxFiles: 1,
        maxFilesize: max_file_size,
        uploadMultiple: false,
        addRemoveLinks: true,
        success:function(file,response,data){
        	// alert(data);
        	// alert(response);
        if(response=='invalid')
        {   
            $(file.previewElement).find('.dz-error-mark').css('opacity','1').css('display','block');
            swal('{{$language['upload error']}}');
        }
        else if(response=='tokenMismatch')
        {   
            $(file.previewElement).find('.dz-error-message').text('Error, Try again ').css('opacity','1').css('display','block').css('top','90px');
            $(file.previewElement).find('.dz-error-mark').css('opacity','1').css('display','block');
            $(file.previewElement).find('.dz-filename').css('font-weight', 'bold');
        }
        else if(response=='exists')
        {   
            $(file.previewElement).find('.dz-error-mark').css('opacity','1').css('display','block');
            swal('{{$language['doc_already_exist']}}');
        }
		else if(response=='ftpinvalid')
        {   
        	$("#file_error").html('Error in ftp connection. Make sure the ftp credentials is correct or try changing it to http in the settings.');
        }
        else{    
        var obj=response;
        var rand_name=obj.fileRandName;
        var size = obj.size;
        $("#hidd_file").val(rand_name);
        $("#hidd_file_size").val(size);
        if(rand_name)
        {
        var ext=rand_name.split('.').pop();//extension
        var file_upload = rand_name;
    	}
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
        // alert(ext);
        // exit();
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
        }else if(ext=='doc'||ext=="docx"||ext=='xls'||ext=='xlsx'){
        	var online=navigator.onLine;
        	if(online==true)
        	{
        	$("#pdfrender").html('<div class="resizable"><iframe src="https://docs.google.com/gview?url='+base_url_path+'/storage/documents/'+rand_name+'&embedded=true" width="auto" height="auto;" class="resizewrapperiframe"></iframe></div>');
        	}
        	else
        	{
        		$("pdfrender").html('<div> No internet connection</div>');
        	}
        }else if(ext=='gif'||ext=='jpg'||ext=='jpeg'||ext=='png'){ 
        	$("#pdfrender").html('<img class="zoom" src="'+base_url_path+'/storage/documents/'+rand_name+'" style="width:100%; height:600px; overflow:scroll;">');
        	wheelzoom(document.querySelector('img.zoom'));
        }else if(ext=='mp4'||ext=="ogv"||ext=="ogg"||ext=='webm'||ext=='flv'){
        	
	        if(ext=='mp4'){
	            $("#pdfrender").html('<video id="player1" style="width:100%;" poster="" preload="none" controls playsinline webkit-playsinline><source src="'+base_url_path+'/storage/documents/'+rand_name+'" type="video/mp4"></video>');  
	        }else if(ext=='ogv'){
	            $("#pdfrender").html('<video id="player1" style="width:100%;" poster="" preload="none" controls playsinline webkit-playsinline><source src="'+base_url_path+'/storage/documents/'+rand_name+'" type="video/ogg"></video>');  
	        }else if(ext=='ogg'){
	            $("#pdfrender").html('<video id="player1" style="width:100%;" poster="" preload="none" controls playsinline webkit-playsinline><source src="'+base_url_path+'/storage/documents/'+rand_name+'" type=video/ogg></video>');  
	        }else if(ext=='webm'){
	            $("#pdfrender").html('<video id="player1" style="width:100%;" poster="" preload="none" controls playsinline webkit-playsinline><source src="'+base_url_path+'/storage/documents/'+rand_name+'" type="video/webm"></video>');  
	        }
	        // flash player code
	        
	        // else if(ext=='flv'){
	        	
	        //     // $("#pdfrender").html('<video id="player1" style="width:100%;" poster="" preload="none" controls playsinline webkit-playsinline><source src="'+base_url_path+'/storage/documents/'+rand_name+'" type="video/flv"></video>');  
	            
         //    $("#pdfrender").html('<div id="player_2065" style="display:inline-block;"><a href="http://get.adobe.com/flashplayer/">You need to install the Flash plugin</a></div>');
            
         //        var flashvars_2065 = {};
         //        var params_2065 = {
         //            quality: "high",
         //            wmode: "transparent",
         //            bgcolor: "#ffffff",
         //            allowScriptAccess: "always",
         //            allowFullScreen: "true",
         //            flashvars: "fichier={{ config('app.url') }}/storage/documents/"+rand_name
         //        };
         //        var attributes_2065 = {};
         //        flashObject("http://flash.webestools.com/flv_player/v1_28.swf", "player_2065", "720", "405", "8", false, flashvars_2065, params_2065, attributes_2065);
                                
	        // }	        
	    }else if(ext=='mp3'||ext=="wav"||ext=='ogg'){
	        if(ext=='mp3'){
	            $("#pdfrender").html('<audio id="player2" preload="none" controls style="width:100%;"><source src="'+base_url_path+'/storage/documents/'+rand_name+'" type="audio/mp3"></audio>');
	        }else if(ext=='wav'){
	            $("#pdfrender").html('<audio id="player2" preload="none" controls style="width:100%;"><source src="'+base_url_path+'/storage/documents/'+rand_name+'" type="audio/wav"></audio>');
	        }else if(ext=='ogg'){
	            $("#pdfrender").html('<audio id="player2" preload="none" controls style="width:100%;"><source src="'+base_url_path+'/storage/documents/'+rand_name+'" type="audio/ogg"></audio>');
	        }        
	    }else if(ext=='zip'){
	    	$("#pdfrender").html('No preview available.');
		}else if(ext=='rar'){
	    	$("#pdfrender").html('No preview available.');
		}
		else{
	        $("#pdfrender").html('<div class="resizable"><iframe src="'+base_url_path+'/storage/documents/'+rand_name+'?#toolbar=0" id="iframe" width="auto" height="auto;" class="resizewrapperiframe"></iframe></div>');
	        //<div class="resizable"><iframe src='"+base_path('storage/documents')"'"+rand_name+"'></iframe></div>");
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
                    $("#hidd_file_size").val(null);
                    $('#pdfrender').html('No document found');  
                    swal('{{$language['success_remove_document']}}');
                }    
            }
        })
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
        //validate upload document
        $( "#documentManagementAddForm" ).submit(function( event ) {

	        var check=$("#hidd_file").val();
	        if(check==""||check==null)
	            {
	            	$(window).scrollTop($('#focus').offset().top);
	                $("#file_error").text("<?php echo $language['no_file'];?>");
	                return false;
	            }else{
		            $("#file_error").text("");
		        }

	        var doctypeid=$('#doctypeid').val();
	        if(doctypeid==""||doctypeid==null)
	            {   
	                $(window).scrollTop($('#form-doctypeid').offset().top);
	                
	            }

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
        });


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
	                        	console.log(data);
	                            if(data) {
	                                if(data['document_type_column_no']){
	                                    $("#docno").attr("placeholder", data['document_type_column_no']);
	                                    $("#docno_lbl").html(data['document_type_column_no']+'<span class="compulsary">*</span>');                                    
	                                }else{
	                                    var field1 = $("#field1").val();
	                                    $("#docno_lbl").html(field1+'<span class="compulsary">*</span>');
	                                    $("#docno").attr("placeholder", field1);
	                                }
	                                if(data['document_type_column_name']){
	                                    $("#docname").attr("placeholder", data['document_type_column_name']);
	                                    $("#docname_lbl").html(data['document_type_column_name']+'<span class="compulsary">*</span>');
	                                }else{
	                                    var field2 = $("#field2").val();
	                                    $("#docname_lbl").html(field2+'<span class="compulsary">*</span>');
	                                    $("#docname").attr("placeholder", field2);
	                                }
	                            }
	                        }
	                    });
	                }else{
	                	var field1 = $("#field1").val();
		                $("#docno_lbl").html(field1+'<span class="compulsary">*</span>');
		                $("#docno").attr("placeholder", field1);
		                var field2 = $("#field2").val();
		                $("#docname_lbl").html(field2+'<span class="compulsary">*</span>');
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
        });
    //Duplicate entry
    //     $("#docno").change(function(){
    //         var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    //         $.ajax({
    //             type: 'post',
    //             url: '{{URL('docnoDuplication')}}',
    //             dataType: 'json',
    //             data: {_token: CSRF_TOKEN, name: $("#docno").val() },
    //             timeout: 50000,
    //             beforeSend: function() {
    //                 $("#dp_wrn").show();
    //                 $('input[type="submit"]').prop('disabled', true);
    //             },
    //             success: function(data, status){
    //                 if(data)
    //                 {
    //                     $("#dp").html(data);
    //                     $("#docno").val('');
    //                 }
    //                 else
    //                 {
    //                     $("#dp-inner").text('');
    //                 }
    //             },
    //             error: function(jqXHR, textStatus, errorThrown){
    //                 console.log(jqXHR);    
    //                 console.log(textStatus);    
    //                 console.log(errorThrown);    
    //             },
    //             complete: function() {
    //                 $("#dp_wrn").hide();
    //                 $('input[type="submit"]').prop('disabled', false);
    //             }
    //         });
    //     });
    // $("#docname").change(function(){
    //         var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    //         $.ajax({
    //             type: 'post',
    //             url: '{{URL('docnameDuplication')}}',
    //             dataType: 'json',
    //             data: {_token: CSRF_TOKEN, name: $("#docname").val() },
    //             timeout: 50000,
    //             beforeSend: function() {
    //                 $("#dn_wrn").show();
    //                 $('input[type="submit"]').prop('disabled', true);
    //             },
    //             success: function(data, status){
    //                 if(data)
    //                 {
    //                     $("#dn").html(data);
    //                     $("#docname").val('');
    //                 }
    //                 else
    //                 {
    //                     $("#dn-inner").text('');
    //                 }
    //             },
    //             error: function(jqXHR, textStatus, errorThrown){
    //                 console.log(jqXHR);    
    //                 console.log(textStatus);    
    //                 console.log(errorThrown);    
    //             },
    //             complete: function() {
    //                 $("#dn_wrn").hide();
    //                 $('input[type="submit"]').prop('disabled', false);
    //             }
    //         });
    //     });
    });
$(document).ready(function(){

	// When click on save btn the action will change.That is how the "Save" and "Save And Close" button works. 
    // Butn Save
    $('.btn-save').click(function(){

        // Validation doc type and department(scroll top not working in select box)
        var departmentid = $('#departmentid').val();
        if(departmentid == null){
            $('#error-msg').html('<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Please fill Department');
        }
        var doctypeid = $('#doctypeid').val();
       
        if(doctypeid){
            // No message
        }else{
        	$('#error-msg').html('<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Please fill Document type');
        }
        // Remove error msg
        if(departmentid && doctypeid){
            $('#error-msg').html('');
        }
    });

	// Remove error
    $('#docno').click(function(){
        $('#dp-inner').text('');
    });

	$("#mini-doc-col").hide();
    
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

                $("#notes").click(function(){
                $("#mini-doc-col").show();
                $(this).addClass("active");
                $("#notesbox").show();
            });
                $("body").on('click','#notesave',function(){
                var noteval = $("#newnotetxt").val();
                var docid  = $("#docid").val();
                var view = 'add'; 
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

      $("#stack").on('change',function() {
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
<script src="js/tiff.min.js"></script>
{!! Html::script('dist/jstree.min.js') !!}
{!! Html::style('dist/style.min.css') !!}
@endsection