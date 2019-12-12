<?php
	include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')
@section('main_content')
{!! Html::script('js/jquery.form.js') !!}   
{!! Html::style('css/font-awesome.min.css') !!} 
<!-- {!! Html::style('css/custom-css.css') !!} -->
{!! Html::script('dist/jstree.min.js') !!}
{!! Html::style('dist/style.min.css') !!}

<!-- Content Wrapper. Contains page content -->
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
            height: 10px;
        }
    }

    /*<--mobile view table-->*/
    @media(max-width:500px){
        .box{
            overflow-x: auto;
        }
    }

    .resizewrapper{
        resize: vertical;
        overflow: auto;
        min-height: 200px;
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
        /*min-height: 590px;*/
    }
    #pdfrender{
        overflow: auto;
    }
    .evenclr{
        background-color: #e6faff;
        padding-top: 8px;
    }
    .odd{
        padding-top: 8px;
    }

    .file-node{
        color:#337ab7;
    }

    .head {
        background: #00c0ef none repeat scroll 0 0;
        color: #ffffff;
        height: 40px;
        padding-left: 10px;
        padding-top: 10px;
    }

    .col-md-3{
        padding-left: 0px !important;
    }
    .col-md-6{
        padding-left: 0px !important;
        padding-right: 0px !important;
    }
    .box{
          margin-bottom: 10px !important;
    }
    .btn{
        padding: 4px 12px !important;
    }
    .main-header > .navbar {
        min-height: 0px;
    }

    .oddcol{
        height: 40px;
        background: #e6faff;
    }
    .evncol{
        height: 40px;
        background: #FFFFFF;
    }
    canvas {
        max-width: 100%;
    }
    .modal-body{
        overflow: auto;
        max-height: 550px;
    }
    .ScrollStyle
    {
        max-height: 570px;
        overflow-y: auto;
        overflow-x: hidden;
    }
    .box-modify {
        border-radius: 0px 0px 3px 3px;
        padding-bottom: 40px;
    }
    .zoom{
        width:100%; /* you can use % */
        height: auto;
    }
    .material-switch > input[type="checkbox"] {
    display: none;   
    }

    .material-switch > label {
        cursor: pointer;
        height: 0px;
        position: relative; 
        width: 40px;  
    }

    .material-switch > label::before {
        background: rgb(0, 0, 0);
        box-shadow: inset 0px 0px 10px rgba(0, 0, 0, 0.5);
        border-radius: 8px;
        content: '';
        height: 16px;
        margin-top: -8px;
        position:absolute;
        opacity: 0.3;
        transition: all 0.4s ease-in-out;
        width: 40px;
    }
    .material-switch > label::after {
        background: rgb(255, 255, 255);
        border-radius: 16px;
        box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
        content: '';
        height: 24px;
        left: -4px;
        margin-top: -8px;
        position: absolute;
        top: -4px;
        transition: all 0.3s ease-in-out;
        width: 24px;
    }
    .material-switch > input[type="checkbox"]:checked + label::before {
        background: inherit;
        opacity: 0.5;
    }
    .material-switch > input[type="checkbox"]:checked + label::after {
        background: inherit;
        left: 20px;
    }
</style>

<?php
	$user_permission=Auth::user()->user_permission;       
?> 
<!-- Content Header (Page header) -->
<section class="content-header">   
    <div class="col-sm-2" style="padding-left:0px;">
        <strong>
            {{trans('documents.all documents')}}
        </strong>
        <?php $usrRole= explode(",",Auth::user()->user_role); ?>    
        <a href="{{url('listview?view=list')}}"><i class="fa fa-th-list fa-lg" aria-hidden="true" title="Documents List View" id="view-change"></i></a>
        <a><i class="fa fa-bars fa-lg" aria-hidden="true" id="togg-wrkspace" title="{{trans('documents.toggle-title')}}"></i></a>
    </div>
    <div class="col-sm-7" style="padding-left:0px; font-size:12px;">
        {{trans('documents.color_legend')}}
        <button type="button" id="legend_button" class="btn btn-warning-btn"></button>&nbsp;{{trans('documents.soon_tobe_expired')}} <?php echo Session::get('settings_document_expiry')." days"; ?>
        <button type="button" id="legend_button" class="btn btn-danger-btn"></button>&nbsp;{{trans('documents.expired_docs')}}
        <button type="button" id="legend_button" class="btn btn-encrypt-btn"></button>&nbsp;{{trans('documents.encrypted_docs')}}
        <button type="button" id="legend_button" class="btn btn-checkout-btn"></button>&nbsp;{{trans('documents.checkout_docs')}}
        <button type="button" id="legend_button" class="btn btn-success-btn"></button>&nbsp;{{trans('documents.all_other_docs')}}
    </div>
    <div class="col-sm-3" style="font-size:12px;">                       
        <!-- <ol class="breadcrumb">
            <li><a href="{{url('/home')}}"><i class="fa fa-dashboard"></i> {{trans('language.home')}}</a></li>
            <li class="active">{{trans('documents.all documents')}}</li>
        </ol> -->
    </div>   
</section>

    <div class="content" style="padding:10px !important;">
        <!--checking view permission-->
        <input type="hidden" value="<?php echo stristr($user_permission,"add");?>" id="user_permission">    
        @if(stristr($user_permission,"view"))
            @if(Session::has('data'))
                <section class="content content-sty" id="spl-wrn">        
                    <div class="alert alert-sty {{ Session::get('alert-class', 'alert-success') }} ">{{ Session::get('data') }}</div>        
                </section>
                @endif 
            @if(Session::has('error'))
                <section class="content content-sty" id="spl-err">        
                    <div class="alert alert-sty {{ Session::get('alert-class', 'alert-error') }} ">{{ Session::get('error') }}</div>        
                </section>
                @endif         
            <!--View more content-->
            <div class="modal fade" id="viewmoreModal" data-backdrop="true" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                        <div class="modal-header" style="border-bottom-color: deepskyblue;">
                            <h4 class="modal-title" style="float:left; width:90%;">
                                {{trans('documents.all documents')}}
                                <small>- View All Data</small>
                            </h4>

                            <a href="javascript:void(0);">
                                <button class="btn btn-primary btn-danger" id="cn" data-dismiss="modal" type="button">{{trans('language.home')}}</button>
                            </a>
                        </div>

                        <div class="modal-body" id="more">  
                                   
                        </div><!-- /.modal-dialog -->

                    </div><!-- /.modal -->
                </div>
            </div>
            <!-- add/view workflow content -->
            <div class="modal fade" id="addview_workflow" data-backdrop="true" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div id="content_wf"></div>
            </div>
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-md-3" id="wrkspace-div">
                    <!-- option to set default -->
                    <div style="width: 170px;padding-top: 35px;padding-bottom: 5px;">
                        {{trans('documents.set_as_default')}}
                        <div class="material-switch pull-right">
                            <input id="someSwitchOptionSuccess" name="someSwitchOption001" type="checkbox" class="defaultview" title="{{trans('documents.set_as_default_title')}}">
                            <label for="someSwitchOptionSuccess" class="label-success"></label>
                        </div>
                    </div>
                    <!--end option to set default -->
                        <h5 id="uploadFile">{{trans('documents.current path')}} : 
                        <?php $str = Session::get('SESS_path');?>
                        <?php echo trim(preg_replace('/\s*\([^)]*\)/', '', $str));?>/</h5>
                        <p style="font-size:12px; font-style:italic; color:#808080;">{{trans('documents.wrkspacemsg')}}</p>
                        <!-- <h5 id="uploadFile"></h5> -->
                        <!-- Profile Image -->
                        <div class="box box-primary resizewrapper">
                            <div id="tree-container" style="overflow: auto; min-height: 300px;"></div> 
                        </div><!-- /.box -->
          	<div id="upload_file" style="margin-bottom: 10px;">
	            @if(stristr($user_permission,"add"))
	                <a class="btn btn-primary" id="b" style="width: 100%;" title="{{trans('documents.upload files')}}">{{trans('documents.upload files')}}</a>
	            @endif
	            <div class="box-body" id="a" style="display: none;">
	                <div class="dropzone" id="dropzoneFileUpload" style="overflow: auto;max-height: 150px;">
	                </div>
	                <input type="hidden" id="hiddUp" name="hiddUp">
	            </div>
	        </div>
            
            <div id="vud" style="margin-bottom: 10px;">
                <a href="{{URL::route('uploadFileEdit')}}" title="Edit" id="uploadEdit" style="display:none">
                <i class="fa fa-pencil" style="cursor:pointer;"></i>{{trans('documents.edit_upload')}}</a>    
                <a class="btn btn-primary" style="width: 100%;" href="{{ url('listview') }}?view=import" title="{{trans('documents.view upload documents')}}">{{trans('documents.view upload documents')}}</a> 
            </div>

            <!--To view checkout list in mobile view-->
            <div id="checkout-list" style="display:none"> 
                <a class="btn btn-primary" style="width: 100%;" href="{{ url('documentsCheckoutListview') }}" title="{{trans('documents.chkout list')}}">{{trans('documents.chkout list')}}</a>
            </div>

                <!--Recent search list-->
                @if(Session::get('search_list_exists'))
                <div class="box box-danger" style="position: relative;top: 20px;">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{trans('documents.recent search')}}:</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body no-padding col-sm-16">

                        @if(Session::get('serach_doc_no'))
                        <div class="col-sm-12"><span style="font-style: italic; font-weight:bold;">{{trans('language.document no')}}:</span> <?php echo Session::get('serach_doc_no');?></div>@endif
                        
                        @if(Session::get('search_docname'))
                        <div class="col-sm-12"><span style="font-style: italic; font-weight:bold;">{{trans('language.document name')}}:</span> <?php echo Session::get('search_docname');?></div>@endif

                        @if(Session::get('search_ownership'))
                        <div class="col-sm-12"><span style="font-style: italic; font-weight:bold;">{{trans('documents.ownership')}}:</span> <?php echo Session::get('search_ownership');?></div>@endif

                        @if(Session::get('search_departments'))
                        <div class="col-sm-12"><span style="font-style: italic; font-weight:bold;">{{trans('language.department')}}:</span> <?php echo Session::get('search_departments');?></div>@endif

                        @if(Session::get('search_document_type_name'))
                        <div class="col-sm-12"><span style="font-style: italic; font-weight:bold;">{{trans('language.document type')}}:</span> <?php echo Session::get('search_document_type_name');?></div>@endif

                        @if(Session::get('search_stack'))
                        <div class="col-sm-12"><span style="font-style: italic; font-weight:bold;">{{trans('language.stack')}}:</span> <?php echo Session::get('search_stack');?></div>@endif

                        @if($is_category_exists)
                            @if(Session::get('search_tags'))
                            <div class="col-sm-12"><span style="font-style: italic; font-weight:bold;">{{trans('documents.tag words')}}:</span> <?php echo Session::get('search_tags');?></div>@endif
                        @else
                            <?php Session::forget('search_tags');?>
                            <?php Session::forget('tagscate');?>
                        @endif

                        @if(Session::get('search_created_date_from'))
                        <div class="col-sm-12"><span style="font-style: italic; font-weight:bold;">{{trans('documents.created date - from')}}:</span> <?php echo date('d-m-Y h:i:s a', strtotime(Session::get('search_created_date_from')));?></div>@endif

                        @if(Session::get('search_created_date_to'))
                        <div class="col-sm-12"><span style="font-style: italic; font-weight:bold;">{{trans('documents.created date - to')}}:</span> <?php echo date('d-m-Y h:i:s a', strtotime(Session::get('search_created_date_to')));?></div>@endif

                        @if(Session::get('search_last_modified_from'))
                        <div class="col-sm-12"><span style="font-style: italic; font-weight:bold;">{{trans('documents.last modified - from')}}:</span> <?php echo date('d-m-Y h:i:s a', strtotime(Session::get('search_last_modified_from')));?></div>@endif

                        @if(Session::get('search_last_modified_to'))
                        <div class="col-sm-12"><span style="font-style: italic; font-weight:bold;">{{trans('documents.last modified - to')}}:</span> <?php echo date('d-m-Y h:i:s a', strtotime(Session::get('search_last_modified_to')));?></div>@endif
                    <!-- /.search-list -->

                    <!-- /.box-footer -->
                    <div class="col-sm-12" style="border-top: 1px solid #f4f4f4;">
                        <a href="{{ url('documentAdvanceSearch/edit') }}" style="cursor:pointer;color: #3c8dbc;" title="Change Criteria">Change Criteria</a>
                    </div>

                    </div>

                </div><!--Recent search list ends-->
                @endif
                    
                    </div><!-- /.tab-content -->
                    <div class="col-md-3" id='list-div'>
                        <h5><div id="countshow-div">
                        <p id="num-of-docs">{{trans('documents.last')}}{{count($dglist)}}{{trans('documents.num_docs')}}</p>
                        </div></h5>
                        <!-- Profile Image -->
                        <div class="box box-primary">
                            <div class="box-body box-profile">
                                <div class="input-group input-group-sm"  style="width:100%">
                                                                     
                                        <div class="input-group">
                                            <input type="text" style="height: 34px;" id="srchtxt" class="form-control" name="q" value="<?php if (!empty($searchres)){ ?>{{$searchres}}<?php } ?>" placeholder="Search by {{trans('language.no')}}, {{trans('documents.name')}}"> 
                                            <span class="input-group-btn">
                                                <button id="docsrch" class="btn btn-default srchbtn" style="height:34px;">
                                                    <span class="glyphicon glyphicon-search"></span>
                                                </button>
                                                <a href="{{ url('documents') }}" class="btn btn-default srchbtn" style="margin-left:1px; height:34px;">
                                                    <span class="glyphicon glyphicon-refresh" style="padding-top:4px;"></span>
                                                </a>
                                            </span>
                                        </div>
                                    
                                    <a href="{{URL::route('documentAdvanceSearch')}}?page={{Request::segment(1)}}" style="cursor:pointer;color: #3c8dbc;" title="Advance Search">{{trans('documents.advance search')}}</a>&nbsp;(<u><a href="javascript:void(0);" id="hint" style="cursor:pointer;color: #3c8dbc;" title="{{trans('documents.hint_message')}}">{{trans('documents.hint')}}</a></u>)
                                </div><!-- /input-group -->
                            </div><!-- /.box-body -->
                        </div><!-- /.box -->
                        <!-- preloader -->
                        <div class="preloader" style="text-align: center; margin-top: 5px; display: none;" >
                          <i class="fa fa-spinner fa-pulse fa-fw fa-lg"></i>
                          <span class="sr-only">Loading...</span>
                        </div>
                        <!-- preloader -->
                        <div  class="ScrollStyle">
                        
                        <div id="datas">
                        
                        <!-- When reload to maintain Current Path  and file -->
                        <?php if(count($dglist)>0){ ?>
                            <?php $i = 1; ?>
                            @foreach ($dglist as $val) 
                            
                                    <input type="hidden" name="filepath<?php echo $i; ?>" id="path<?php echo $i; ?>" value="{{ $val->document_file_name }}" encrypt_status = "{{ $val->document_encrypt_status}}">
                                    <input type="hidden" name="urlpath<?php echo $i; ?>" id="url<?php echo $i; ?>" value="{{ $val->document_id }}">
                                    <input type="hidden" name="dcno<?php echo $i; ?>" id="dcno<?php echo $i; ?>" value="{{ $val->document_no }}">
                                    <input type="hidden" name="dcname<?php echo $i; ?>" id="dcname<?php echo $i; ?>" value="{{ $val->document_name }}">
                                    <input type="hidden" name="dcstatus<?php echo $i; ?>" id="dcstatus<?php echo $i; ?>" value="{{ $val->document_status }}">
                             <?php $i++; ?>
                            @endforeach
                            <input type="hidden" name="sesdcno" id="sesdcno" value="{{Session::get('sess_docview_id')}}">
                            <input type="hidden" name="sesrowno" id="sesrowno" value="{{Session::get('sess_row_id')}}">
                                      
                        <?php }else{ ?>
                        <script type="text/javascript">
                           $('#num-of-docs').text("No documents found");
                        </script>
                            <div class="box box-primary">
                                <div class="box-body box-profile">
                                    <div class="input-group input-group-sm">
                                        {{trans('documents.no_doc__msg')}}
                                    </div><!-- /input-group -->
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        <?php } ?>
                    <!-- When reload to maintain Current Path  and file END -->
                    </div> <!---/.datas -->
                    </div><!-- /.tab-content -->
                    </div>
                    <div class="col-md-6" id="view-doc-div">
                        <?php if(count($dglist)>0){ 
                            $stat = 1;                      
                        }else{ 
                            $stat = 0;
                        } ?>
                        <?php if($stat==1){ ?>
                            <h5><label id="top_lbl">Doc No</label>: <span id="filid"></span></h5>
                            <div class="box box-primary">
                                <div class="box-body box-profile">

                                    <div id="pdfrender">
                                    
                                    </div>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        <?php }else { ?>
                            <h5>&nbsp;</h5>
                            <!-- Profile Image -->
                            <div class="box box-primary">
                                <div class="box-body box-profile">
                                    <div id="pdfrender">
                                    {{trans('documents.no_doc__msg')}}
                                    </div>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        <?php  }  ?>
                        
                    </div><!-- /.tab-content -->
                </div><!-- /.nav-tabs-custom -->
            </section><!-- /.content -->
        @else
            <section class="content content-sty">        
                <div class="alert alert-sty alert-error">{{trans('language.dont_hav_permission')}}</div>        
            </section>
        @endif

        <!-- Adv Search -->
<div class="modal fade" id="dTSearchModal" data-backdrop="false" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- User add Form -->
<div class="modal fade" id="userAddModal" data-backdrop="false" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="dGAddModal" >
   <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-group">
                    {{trans('documents.confirm_checkout_single')}}<sapn class="share-doc-name"></sapn>
                </div>
                <div class="form-group">
                    <label for="Comments" class="col-sm-12 control-label">{{trans('documents.comments')}}:<span style="color:red">*</span></label>
                    <div class="col-sm-12">
                        {!! Form:: 
                        text('comments','', 
                        array( 
                        'class'=> 'form-control', 
                        'id'=> 'comments', 
                        'title'=> "'".trans('language.full name')."'".trans('language.length_others'), 
                        'placeholder'=> trans('documents.comments'),
                        'autofocus',                
                        )
                        ) 
                        !!}
                        <input type="hidden" id="hidd-doc-id" name="hidd_doc_id">
                        <input type="hidden" id="hidd-doc-name" name="hidd_doc_name">
                        <input type="hidden" id="hidd-doc-count" name="hidd_doc_count">    
                        <span class="dms_error">{{$errors->first('name')}}</span>       
                        <span class="null_error" style="color:red"></span>        
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-5 control-label"></label>
                    <div class="col-sm-7" id="button-section" style="padding-top: 10px;">
                        {!!Form::submit(trans('language.save'), array('class' => 'btn btn-primary sav_btn','id'=>'comment_save')) !!} &nbsp;&nbsp;
                        {!!Form::button(trans('language.cancel'), array('class' => 'btn btn-primary btn-danger', 'id' => 'cn', 'data-dismiss' => 'modal')) !!}
                    </div>
                </div><!-- /.col -->
                {!!Form::close()!!}
            </div>
        </div>
    </div>
</div>

<!-- Adv search form end --> 
</div><!-- /.content-wrapper -->  
<script type="text/javascript" src="http://www.webestools.com/page/js/flashobject.js"></script>
<script type="text/javascript">
$(function() {
    var $defaultview = '{{Auth::user()->user_documents_default_view}}';
    if($defaultview == 'grid')
    {
        $(".defaultview").prop('checked', true);
    }
    else
    {
        $(".defaultview").prop('checked', false);
    }
  $(".defaultview").change(function() {
    if ($(this).is(":checked"))
    {
        var defaultview = 'grid';
    }
    else
    {
        var defaultview = 'list';
    }
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            type: 'post',
            url: '{{URL('setview')}}',
            data: {_token:CSRF_TOKEN,defaultview:defaultview},
            success: function(data){
                console.log(data);
                if(data == 'list')
                {
                    $('#navigate_default').attr('href',base_url+'/listview?view=list');
                }
                else
                {
                    $('#navigate_default').attr('href',base_url+'/documents');
                }
            },
        });
  });
});
function myFunction(docname,docid,count) {
    // alert('hi');
    // return false;
    $('#userAddModal').modal('toggle');
    $('#userAddModal').modal('show');
    $('.share-doc-name').text(docname+' ?');
    $("#hidd-doc-id").val(docid);
    $('#hidd-doc-name').val(docname);
    $('#hidd-doc-count').val(count);
}
$('#comment_save').click(function(){
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var docid=$("#hidd-doc-id").val();
    var docname=$('#hidd-doc-name').val();
    var comments=$('#comments').val();
    var count=$('#hidd-doc-count').val();
    if(comments){
        // success
        $('.null_error').html('')
        $.ajax({
            type: 'post',
            url: '{{URL('commentAdd')}}',
            data: {_token:CSRF_TOKEN,hidd_doc_id:docid,hidd_doc_name:docname,comments:comments },
            beforeSend: function() {                
            },
            success: function(data){
                $('#userAddModal').modal('hide');
                $('#status'+count).text('Checkout');
                window.location.href = 'download';
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
        $('.null_error').html('Please fill Check Out Comments.')
    }        
});

// View More
$(document).ready(function(){
    $('video, audio').mediaelementplayer();
    var toplabel = $("#toplbl").val();
    $("#top_lbl").html(toplabel);
	$("#srchtxt").focus();
	// Checking add permission
	$('body').on('click','#check_permission_add',function(e){
		e.preventDefault();	   
		$.ajax({
			type:'GET',
			url : base_url+'/getUserPermission',
			dataType:'JSON',
			success:function(result){  
				if (result.user_add){  
					location.href = base_url+'/documentAdd';        
				} else {
					swal('{{trans('documents.no_permission_add')}}');
				}
			}
		});	
	});
	$('#hint').click(function(){
		swal('{{trans('documents.hint_message')}}');
	});
	// Checking add permission
	$('body').on('click','#download',function(){
		var href = $('#download').attr('downloadlink');
		$.ajax({
			type:'GET',
			url : base_url+'/getUserPermission',
			dataType:'JSON',
			success:function(result){  
				if (result.user_download){  
					location.href = href;      
				} else {
					swal('{{trans('documents.no_permission_download')}}');
				}
			}
		});	
	});
    //Drag and drop toggle on click
	$('#b').click(function(){
		var upload_path=$("#uploadFile").text();
		var real_path=upload_path.split(":").pop();
		swal({
			title: "Documents will be uploaded to the currently selected '" + real_path.trim() + "' folder. If you would like to change the destination folder, please select that folder first. Click OK to upload to the current folder, Cancel to select another folder.",
			
			type: "{{trans('language.Swal_warning')}}",
			showCancelButton: true
		}).then(function (result) {
			if(result){
				// Success
				// Go to the new page
				window.location.href = base_url+"/documents?uploadFile=yes";
			}		
		},function (dismiss) {
		  	// dismiss can be 'cancel', 'overlay',
		  	if (dismiss === 'cancel') {
				$('#a').hide();
		  	}
		});
	});
    
    $('body').on('click','#moredet',function(){
    	var count = $(this).attr('count');
        var docid   = $('#doc_id'+count+'').val(); 
        var view = 'list';
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
			type: 'get',
			url: '{{URL::route('documensMoreDetails')}}',
			dataType: 'html',
			data: {_token: CSRF_TOKEN,docid:docid,view:view},
			timeout: 50000,
			success: function(data, status){     
				$("#more").html(data);
			},
			error: function(jqXHR, textStatus, errorThrown){
				console.log(jqXHR);    
				console.log(textStatus);    
				console.log(errorThrown);    
			}
        });    
   });
   
   // checking user permision  
   $.ajax({
        type : 'GET',
        url  : base_url+'/getUserPermission',
        cache:false,
        dataType:'json',
        success:function(result){
            // checking add permission
            if(result.user_add == null){
                $('#add-btn').css('display','none');
            }			           
        }
    });
    var baseUrl = "{{ url('/') }}";
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
        'plugins' : ['state','dnd','contextmenu','wholerow','types']
    })
    .on('delete_node.jstree', function (e, data){
        var folder_name=data.node.text.replace(/ *\([^)]*\) */g, "");
        swal({
			title: "{{trans('language.confirm_delete_single')}} folder '" + folder_name + "' ?",
		  	type: "{{trans('language.Swal_warning')}}",
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
							window.location.reload();
						}
						else if(response.responseText=='null')
						{
							window.location.reload();
						}    
						else if(response.responseText=='root')
						{
							window.location.reload();
						}
						else if(response.responseText=='temp')
						{
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
    .on('create_node.jstree', function (e, data){
        $.get('response?operation=create_node', { 'id' : data.node.parent, 'position' : data.position, 'text' : data.node.text })
        .done(function (d) {
        	data.instance.set_id(data.node, d.id);
        })
        .fail(function () {
        	data.instance.refresh();
        });
    })
    .on('rename_node.jstree', function (e, data){
        $.get('response?operation=rename_node', { 'id' : data.node.id, 'text' : data.text })
        .fail(function () {
        	data.instance.refresh();
        });
    })
    .on('move_node.jstree', function (e, data){
        $.get('response?operation=move_node', { 'id' : data.node.id, 'parent' : data.parent, 'position' : data.position })
        .fail(function () {
        	data.instance.refresh();
        });
    })
    .on('copy_node.jstree', function (e, data){
        $.get('response?operation=copy_node', { 'id' : data.original.id, 'parent' : data.parent, 'position' : data.position })
        .always(function () {
        	data.instance.refresh();
        });
    })

});

$(function ($) {
	$('#spl-wrn').delay(5000).slideUp('slow');
	$('#spl-err').delay(5000).slideUp('slow');
	document.getElementById('hiddUp').value=1;
    var user_permission_all = '{{$user_permission}}';
    var substring = 'decrypt';
	var myElem = document.getElementById('path1');
	if (myElem === null) {var path=null;}
	else{
		var path = $("#path1").val();
        console.log(path);
        var ext=path.split('.').pop();//extension
        var encrypt_status = $("#path1").attr("encrypt_status");
        
        var id = $("#path1").attr("count");
        var dcnumber = $("#path1").attr("dcno");
        var dcname = $("#path1").attr("dcname");
        var doc_file_name = $("#path1").attr("doc_file_name");
        var exprstatus = $("#path1").attr("exprstatus");
        var toplabel = $("#path1").attr("toplabel");
	}
	var url = $("#url1").val();
	var dcno = $("#dcno1").val();
	var fdcname = $("#dcname1").val();
	var dcstatus = $("#dcstatus1").val();
	var status = '<?php echo count($dglist); ?>';

	$("#filid").html(dcno);
	$("#seldocid").val(url);
	$("#selid").val(1);
	var sesdcno = $("#sesdcno").val();
	var sesrowno = $("#sesrowno").val();
	if(sesdcno=="" && sesrowno==""){
		var tmpid = 1;
		var dcno = url;
	}else{
		var tmpid = sesrowno;
		var dcno = sesdcno;
		// var path = $("#path"+tmpid).val();
		// var ext=path.split('.').pop();//extension
		var myElem = document.getElementById('path'+tmpid);
		if (myElem === null) {var path=null;}
		else{
			var path = $("#path"+tmpid).val();
			var ext=path.split('.').pop();//extension
		}
	}
        
	$("#box"+tmpid+" .box.box-primary").css("background-color", "#e6faff");

    viewfile(id,dcno,dcname,doc_file_name,exprstatus,toplabel);

	//delete topbar button click
	$("#topdel").click(function(){
		var seli = $("#selid").val();
		var selectddocid = $("#url"+seli).val();            
		var selname = $("#dcname"+seli).val();
		del(selectddocid,selname);
	});
	var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
	$.ajax({
		type: 'post',
		url: '{{URL::route('viewsrchToggle')}}',
		dataType: 'html',
		data: {_token: CSRF_TOKEN},
		timeout: 50000,
		success: function(data, status){     
		},
		error: function(jqXHR, textStatus, errorThrown){
		  console.log(jqXHR);    
		  console.log(textStatus);    
		  console.log(errorThrown);    
		}
	});

	//Search docs
	$("#docSearch").click(function(){
		var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
		$.ajax({
			type: 'get',
			url: '{{URL('documentSearch')}}',
			dataType: 'json',
			data: {_token: CSRF_TOKEN, name: $("#searchKey").val() },
			timeout: 50000,
			beforeSend: function() {
			   //$("#bs").show(); 
			},
			success: function(data, status){
				swal(data);			   
			},			
			complete: function() {				
			},			
			error: function(jqXHR, textStatus, errorThrown){
				console.log(jqXHR);    
				console.log(textStatus);    
				console.log(errorThrown);    
			}
		});
	});
});

        
//view all folders
$('#tree-container').on('ready.jstree', function() {
	//$('#tree-container').jstree('select_node', '1');
	$("#tree-container").jstree("open_all");          
});

//Datas Visible On Folder select
var $treeview = $("#tree-container");
$("#tree-container").on('changed.jstree', function (e, data) {
	var SESS_path = "<?php echo Session::get('SESS_path');?>";
	var SESS_parentIdd = "<?php echo Session::get('SESS_parentIdd');?>";
if(data){
	var path = data.instance.get_path(data.node,'/');
}else{
	var path = SESS_path;
}

$('#uploadFile').text("Current Path : "+ path.replace(/ *\([^)]*\) */g, "")+"/").show();
if(data){
	var n= $("#tree-container").jstree(true).get_selected();
}else{
	var n= SESS_parentIdd;
}
        
document.getElementById('hiddUp').value = n;
var par_id=document.getElementById('hiddUp').value;
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
$.ajax({
	type:'get',
	data:{_token: CSRF_TOKEN,par_id:par_id,path:path},
	url:'wrkspacesrchdoc',
	beforeSend: function() {
		$("#bs").show();
        $('.preloader').show();
	},
	success:function(data){
        $('.preloader').hide();
		$("#datas").html(data);
		loadFile();
		var tot_doc=$('#doccnt').val();
		if(tot_doc){
			$('#num-of-docs').text('Last '+tot_doc+ ' documents');
		}else{
			$('#num-of-docs').text('No documents found');
		}
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

    });

    function loadFile(){
        //$("#box1 .box.box-primary").css("background-color", "#e6faff");
        var user_permission_all = '{{$user_permission}}';
        var substring = 'decrypt';
        var myElem = document.getElementById('path1');
        console.log(myElem);
        //return false;
        if (myElem === null)
        {
            var path=null;
        }
        else
        {
            var path = $("#path1").val();
            var ext=path.split('.').pop();//extension
            var encrypt_status = $("#path1").attr("encrypt_status");
            
            var id = $("#path1").attr("count");
            var dcno = $("#path1").attr("dcno");
            var dcname = $("#path1").attr("dcname");
            var doc_file_name = $("#path1").attr("doc_file_name");
            var exprstatus = $("#path1").attr("exprstatus");
            var toplabel = $("#path1").attr("toplabel");
            var is_file_exist = $("#path1").attr("isfileexist");
        }
        //var ext=path.split('.').pop();//extension
        var docid = $("#url1").val();
        var docno = $("#dcno1").val();
       
        // show doc no 
        if(docno){
            $('#filid').html(docno);
        }else{
            $('#filid').html('');
        }

	    var dcstatus = $("#dcstatus1").val();
        //var sesdcno = $("#sesdcno").val();
        var sesrowno = $("#sesrowno").val();
        if(sesrowno==""){
            var tmpid = 1;
        }else{
            var tmpid = sesrowno;
        }
        var user_permission = $('#user_permission').val();
        $("#box"+tmpid+" .box.box-primary").css("background-color", "#e6faff"); 

        if(path)
        {
            if(encrypt_status == 1)
            {//check user have the decrypt permisiion
                if(user_permission_all.includes(substring))
                {

                    $("#pdfrender").html('<a href="{{ url('documentAdd') }}?page={{Request::segment(1)}}"><button  id="add-btn" style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a><br/><div><div class="info-box"><span class="info-box-icon bg-aqua"><i class="fa fa-lock" aria-hidden="true"></i></span><div class="info-box-content"><span class="info-box-text" style="text-align: left !important;">This is a encrypted document.</span><span style="display: block;font-size: 15px;margin-top: 18px;text-align: left !important;"><a count="'+id+'" dcno="'+dcno+'" exprstatus="'+exprstatus+'" toplabel="'+toplabel+'" doc_name="'+dcname+'" doc_file_name="'+doc_file_name+'" style="cursor:pointer" id="decrypt_doc">Click here to decrypt the document</a></span></div><!-- /.info-box-content --></div></div>');
                }
                else
                {
                    $("#pdfrender").html('<a href="{{ url('documentAdd') }}?page={{Request::segment(1)}}"><button  id="add-btn" style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a><br/><div><div class="info-box"><span class="info-box-icon bg-aqua"><i class="fa fa-lock" aria-hidden="true"></i></span><div class="info-box-content"><span class="info-box-text" style="text-align: left !important;">This is a encrypted document.</span><span style="display: block;font-size: 15px;margin-top: 18px;text-align: left !important;"><p>You have no permission to decrypt the document</p></span></div><!-- /.info-box-content --></div></div>');
                }
            }
            else if(is_file_exist == 'notexist')
            {
                $("#pdfrender").html('<a href="{{ url('documentAdd') }}?page={{Request::segment(1)}}"><button  id="add-btn" style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a><br/><div>The requested document does not exist.</div>');
            }
            else
            {
            if(ext=='tiff'||ext=='tif'||ext=='TIFF'||ext=='TIF')
            {   
                if(user_permission){
                    var addDocument1 = '<a href="{{ url('documentAdd') }}"><button style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a>';
                }else{
                    var addDocument1 = '';
                }

                $('#pdfrender').html('<a href="{{ url('documentManagementView') }}'+"?dcno="+docid+"&id="+tmpid+"&page=document"+'"><button style="margin-bottom:5px;" class="btn btn-primary">Open</button></a>'+addDocument1+'<br/><div id="output" style="height:560px;"></div>');
                var xhr = new XMLHttpRequest();
	            xhr.responseType = 'arraybuffer';
	            xhr.open('GET',"{{ config('app.doc_url') }}"+path);
	            xhr.onload = function (e) {
	                var tiff = new Tiff({buffer: xhr.response});
	                var canvas = tiff.toCanvas();
	                if (canvas) {
	                  $('#output').empty().append(canvas);
	                }
	            };
	            xhr.send();
            }else if(ext=='doc'||ext=="docx"||ext=='xls'||ext=='xlsx'){
                if(user_permission){
                    var addDocument2 = '<a href="{{ url('documentAdd') }}?page={{Request::segment(1)}}" ><button id="add-btn" style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a>';
                }else{
                    var addDocument2 = '';
                }
                $("#pdfrender").html('<a href="{{ url('documentManagementView') }}'+"?dcno="+docid+"&id="+tmpid+"&page=document"+'"><button style="margin-bottom:5px;" class="btn btn-primary">Open</button></a>'+addDocument2+'<br/><div class="resizable"><iframe src="https://docs.google.com/gview?url={{ config('app.doc_url') }}'+path+'&embedded=true" id="ifrm1" width="auto" height="auto;" class="resizewrapperiframe"></iframe></div>');  
            }
            else if(ext=='dwg'){
                if(user_permission){
                    var addDocument2 = '<a href="{{ url('documentAdd') }}?page={{Request::segment(1)}}" ><button id="add-btn" style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a>';
                }else{
                    var addDocument2 = '';
                }
                $("#pdfrender").html('<a href="{{ url('documentManagementView') }}'+"?dcno="+docid+"&id="+tmpid+"&page=document"+'"><button style="margin-bottom:5px;" class="btn btn-primary">Open</button></a>'+addDocument2+'<br/><div class="resizable"><iframe src="cadView?file='+doc_file_name+'" id="ifrm1" width="auto" height="auto;" class="resizewrapperiframe"></iframe></div>');  
            }
            else if(ext=='gif'||ext=='jpg'||ext=='jpeg'||ext=='png'){ 
                if(user_permission){
                    var addDocument3 = '<a href="{{ url('documentAdd') }}"><button style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a>';
                }else{
                    var addDocument3 = '';
                }
                
                $("#pdfrender").html('<a href="{{ url('documentManagementView') }}'+"?dcno="+docid+"&id="+tmpid+"&page=document"+'"><button style="margin-bottom:5px;" class="btn btn-primary">Open</button></a>'+addDocument3+'<br/><img class="zoom" src="{{ config('app.doc_url') }}'+path+'">');
                
                $("#tpchkout").html('<a href="{{ url('checkoutDocument') }}'+"?id="+docid+"&view=grid"+'" title="Check Out"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-edit fa-stack-1x fa-inverse"></i><div style="font-size:12px; padding-left:8px; color:#FFFFFF; width:100px; padding-top:25px;">Check Out</div></a>');
            }
            else if(ext=='mp4')
            {
                    $("#pdfrender").html('<a href="{{ url('documentManagementView') }}'+"?dcno="+docid+"&id="+tmpid+"&page=document"+'"><button style="margin-bottom:5px;" class="btn btn-primary">Open</button></a><a href="{{ url('documentAdd') }}?page={{Request::segment(1)}}" ><button id="add-btn" style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a><br/><div class="resizable"><video id="player1" style="width:100%;" poster="" preload="none" controls playsinline webkit-playsinline><source src="{{ config('app.doc_url') }}'+path+'" type="video/mp4"></video></div>');  
            }else if(ext=='ogv')
            {
                    $("#pdfrender").html('<a href="{{ url('documentManagementView') }}'+"?dcno="+docid+"&id="+tmpid+"&page=document"+'"><button style="margin-bottom:5px;" class="btn btn-primary">Open</button></a><a href="{{ url('documentAdd') }}?page={{Request::segment(1)}}" ><button id="add-btn" style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a><br/><div class="resizable"><video id="player1" style="width:100%;" poster="" preload="none" controls playsinline webkit-playsinline><source src="{{ config('app.doc_url') }}'+path+'" type="video/ogg"></video></div>');  
            }else if(ext=='webm')
            {
                    $("#pdfrender").html('<a href="{{ url('documentManagementView') }}'+"?dcno="+docid+"&id="+tmpid+"&page=document"+'"><button style="margin-bottom:5px;" class="btn btn-primary">Open</button></a><a href="{{ url('documentAdd') }}?page={{Request::segment(1)}}" ><button id="add-btn" style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a><br/><div class="resizable"><video id="player1" style="width:100%;" poster="" preload="none" controls playsinline webkit-playsinline><source src="{{ config('app.doc_url') }}'+path+'" type="video/webm"></video></div>');  
            }else if(ext=='flv')
            { 
                    $("#pdfrender").html('<a href="{{ url('documentManagementView') }}'+"?dcno="+docid+"&id="+tmpid+"&page=document"+'"><button style="margin-bottom:5px;" class="btn btn-primary">Open</button></a><a href="{{ url('documentAdd') }}?page={{Request::segment(1)}}" ><button id="add-btn" style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a><br/><div class="resizable"><div id="player_8168" style="display:inline-block;"><a href="http://get.adobe.com/flashplayer/">You need to install the Flash plugin</a></div>');
            
                    var flashvars_8168 = {};
                    var params_8168 = {
                        quality: "high",
                        wmode: "transparent",
                        bgcolor: "#ffffff",
                        allowScriptAccess: "always",
                        allowFullScreen: "true",
                        flashvars: "fichier={{ config('app.doc_url') }}"+path
                    };
                    var attributes_8168 = {};
                    flashObject("http://flash.webestools.com/flv_player/v1_27.swf", "player_8168", "490", "300", "8", false, flashvars_8168, params_8168, attributes_8168);
                                   
            }else if(ext=='mp3')
            {
                    $("#pdfrender").html('<a href="{{ url('documentManagementView') }}'+"?dcno="+docid+"&id="+tmpid+"&page=document"+'"><button style="margin-bottom:5px;" class="btn btn-primary">Open</button></a><a href="{{ url('documentAdd') }}?page={{Request::segment(1)}}" ><button id="add-btn" style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a><br/><div class="resizable"><audio id="player2" preload="none" controls style="width:100%;"><source src="{{ config('app.doc_url') }}'+path+'" type="audio/mp3"></audio></div>');
            }else if(ext=='wav')
            {
                    $("#pdfrender").html('<a href="{{ url('documentManagementView') }}'+"?dcno="+docid+"&id="+tmpid+"&page=document"+'"><button style="margin-bottom:5px;" class="btn btn-primary">Open</button></a><a href="{{ url('documentAdd') }}?page={{Request::segment(1)}}" ><button id="add-btn" style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a><br/><div class="resizable"><audio id="player2" preload="none" controls style="width:100%;"><source src="{{ config('app.doc_url') }}'+path+'" type="audio/x-wav"></audio></div>');
            }else if(ext=='ogg')
            {
                    $("#pdfrender").html('<a href="{{ url('documentManagementView') }}'+"?dcno="+docid+"&id="+tmpid+"&page=document"+'"><button style="margin-bottom:5px;" class="btn btn-primary">Open</button></a><a href="{{ url('documentAdd') }}?page={{Request::segment(1)}}" ><button id="add-btn" style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a><br/><div class="resizable"><audio id="player2" preload="none" controls style="width:100%;"><source src="{{ config('app.doc_url') }}'+path+'" type="audio/ogg"></audio></div>');
            }else if(ext=='zip' || ext == 'rar')
            {
                    $("#pdfrender").html('<a href="{{ url('documentAdd') }}?page={{Request::segment(1)}}"><button  id="add-btn" style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a><br/><div><div class="info-box"><span class="info-box-icon bg-aqua"><i class="ion fa fa-file-archive-o"></i></span><div class="info-box-content"><span class="info-box-text" style="text-align: left !important;">This is a compressed document.</span><span style="display: block;font-size: 15px;margin-top: 18px;text-align: left !important;"><a href="{{ config('app.doc_url') }}'+path+'" download>Click here to download the document</a></span></div><!-- /.info-box-content --></div></div>');
            }
            else
            {
                if(user_permission){
                    var addDocument3 = '<a href="{{ url('documentAdd') }}"><button style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a>';
                }else{
                    var addDocument3 = '';
                }                
                $("#pdfrender").html('<a href="{{ url('documentManagementView') }}'+"?dcno="+docid+"&id="+tmpid+"&page=document"+'"><button style="margin-bottom:5px;" class="btn btn-primary">Open</button></a>'+addDocument3+'<br/><iframe src="webviewer?file={{ config('app.doc_url') }}'+path+'#toolbar=0" id="ifrm1" width="auto" height="auto;" class="resizewrapperiframe"></iframe>');
                $("#tpchkout").html('<a href="{{ url('checkoutDocument') }}'+"?id="+docid+"&view=grid"+'" title="Check Out"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-edit fa-stack-1x fa-inverse"></i><div style="font-size:12px; padding-left:8px; color:#FFFFFF; width:100px; padding-top:25px;">Check Out</div></a>');
            } 
           } 
        }
        else
        {
            if(user_permission){
                var addDocument4 = '<a href="{{ url('documentAdd') }}"><button  id="add-btn" style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a>';
            }else{
                var addDocument4 = '';
            }
            $("#pdfrender").html(''+addDocument4+'<br/><div>There are no documents in this folder.</div>');
        }
    }

    // Trigger js tree to show documents when reload the page
    $('#tree-container').trigger('changed.jstree');
    function viewfile(id,dcno,dcname,doc_file_name,exprstatus,toplabel){
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var user_permission_all = '{{$user_permission}}';
        var substring = 'decrypt';
        $.ajax({
            type:'post',
            data:{_token:CSRF_TOKEN,doc_id:dcno,doc_name:dcname},
            url:'viewOnAudit',
            success:function(data){
                console.log(data);
                if(data == 'encrypted')
                {//check user have the decrypt permisiion
                    if(user_permission_all.includes(substring))
                    {
                    $("#pdfrender").html('<a href="{{ url('documentAdd') }}?page={{Request::segment(1)}}"><button  id="add-btn" style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a><br/><div><div class="info-box"><span class="info-box-icon bg-aqua"><i class="fa fa-lock" aria-hidden="true"></i></span><div class="info-box-content"><span class="info-box-text" style="text-align: left !important;">This is a encrypted document.</span><span style="display: block;font-size: 15px;margin-top: 18px;text-align: left !important;"><a count="'+id+'" dcno="'+dcno+'" exprstatus="'+exprstatus+'" toplabel="'+toplabel+'" doc_name="'+dcname+'" doc_file_name="'+doc_file_name+'" style="cursor:pointer" id="decrypt_doc">Click here to decrypt the document</a></span></div><!-- /.info-box-content --></div></div>');
                    }
                    else
                    {
                        $("#pdfrender").html('<a href="{{ url('documentAdd') }}?page={{Request::segment(1)}}"><button  id="add-btn" style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a><br/><div><div class="info-box"><span class="info-box-icon bg-aqua"><i class="fa fa-lock" aria-hidden="true"></i></span><div class="info-box-content"><span class="info-box-text" style="text-align: left !important;">This is a encrypted document.</span><span style="display: block;font-size: 15px;margin-top: 18px;text-align: left !important;"><p>You have no permission to decrypt the document</p></span></div><!-- /.info-box-content --></div></div>');
                    }
                }
                else if(data == 'notexist')
                {
                    $("#pdfrender").html('<a href="{{ url('documentAdd') }}?page={{Request::segment(1)}}"><button  id="add-btn" style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a><br/><div>The requested document does not exist.</div>');
                }
                else if(data == 'empty')
                {
                    $("#pdfrender").html('<a href="{{ url('documentAdd') }}"><button  id="add-btn" style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a><br/><div>There are no documents in this folder.</div>');
                }
                else
                {console.log('not encrypted');
                    $('img').bind('contextmenu', function(e){
                        return false;
                    });
                }
            }
        });
        var user_permission = $('#user_permission').val();
        var doccnt = $("#doccnt").val();
        $("#seldocid").val(dcno);
        $("#filid").html(dcno);
        $("#top_lbl").html(toplabel);
        $("#selid").val(id);
        <?php Session::forget('sess_docview_id'); 
        Session::forget('sess_row_id'); ?>
        $("#sesdcno").val('');
        $("#sesrowno").val('');
        for (var i = 1; i <= doccnt; i++) {
            $("#box"+i+" .box.box-primary").css("background-color", "#ffffff");    
            $("#box"+i+" .box.box-danger").css("background-color", "#ffffff");    
            $("#box"+i+" .box.box-warning").css("background-color", "#ffffff");    
        };

        if(exprstatus==1){
            $("#box"+id+" .box.box-danger").css("background-color", "#f7dddc");
        }else if(exprstatus==2){
            $("#box"+id+" .box.box-warning").css("background-color", "#f7d9ad");
        }else{
            $("#box"+id+" .box.box-primary").css("background-color", "#e6faff");
        }
        // var path = $("#path"+id).val();
        // var ext=path.split('.').pop();//extension
        var myElem = document.getElementById('path'+id);
        if (myElem === null) {var path=null;}
        else{
            var path = $("#path"+id).val();
            var ext=path.split('.').pop();//extension
        }
        var url = $("#url"+id).val();
        if(ext=='tiff'||ext=='tif'||ext=='TIFF'||ext=='TIF'){   
            if(user_permission){
                var addDocument1 = '<a href="{{ url('documentAdd') }}"><button style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a>';
            }else{
                var addDocument1 = '';
            }

            $('#pdfrender').html('<a href="{{ url('documentManagementView') }}'+"?dcno="+url+"&id="+id+"&page=document"+'"><button style="margin-bottom:5px;" class="btn btn-primary">Open</button></a>'+addDocument1+'<br/><div id="output" style="height:560px;"></div>');
            	var xhr = new XMLHttpRequest();
	            xhr.responseType = 'arraybuffer';
	            xhr.open('GET',"{{ config('app.doc_url') }}"+path);
	            xhr.onload = function (e) {
	                var tiff = new Tiff({buffer: xhr.response});
	                var canvas = tiff.toCanvas();
	                if (canvas) {
	                  $('#output').empty().append(canvas);
	                }
	            };
	            xhr.send();
        }else if(ext=='doc'||ext=="docx"||ext=='xls'||ext=='xlsx'){
            if(user_permission){
                var addDocument2 = '<a href="{{ url('documentAdd') }}"><button style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a>';
            }else{
                var addDocument2 = '';
            }
          $("#pdfrender").html('<a href="{{ url('documentManagementView') }}'+"?dcno="+url+"&id="+id+"&page=document"+'"><button style="margin-bottom:5px;" class="btn btn-primary">Open</button></a>'+addDocument2+'<br/><div class="resizable"><iframe src="https://docs.google.com/gview?url={{ config('app.doc_url') }}'+path+'&embedded=true" width="auto" id="ifrm1" height="auto;" class="resizewrapperiframe"></iframe></div>');  
        }
        else if(ext=='dwg'){
            if(user_permission){
                var addDocument2 = '<a href="{{ url('documentAdd') }}"><button style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a>';
            }else{
                var addDocument2 = '';
            }
          $("#pdfrender").html('<a href="{{ url('documentManagementView') }}'+"?dcno="+url+"&id="+id+"&page=document"+'"><button style="margin-bottom:5px;" class="btn btn-primary">Open</button></a>'+addDocument2+'<br/><div class="resizable"><iframe src="cadView?file='+doc_file_name+'" width="auto" id="ifrm1" height="auto;" class="resizewrapperiframe"></iframe></div>');  
        }
        else if(ext=='gif'||ext=='jpg'||ext=='jpeg'||ext=='png'){ 
            if(user_permission){
                var addDocument3 = '<a href="{{ url('documentAdd') }}"><button style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a>';
            }else{
                var addDocument3 = '';
            }
            $("#pdfrender").html('<a href="{{ url('documentManagementView') }}'+"?dcno="+url+"&id="+id+"&page=document"+'"><button style="margin-bottom:5px;" class="btn btn-primary">Open</button></a>'+addDocument3+'<br/><img class="zoom" src="{{ config('app.doc_url') }}'+path+'">');
            $("#tpchkout").html('<a href="{{ url('checkoutDocument') }}'+"?id="+url+"&view=grid"+'" title="Check Out"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-edit fa-stack-1x fa-inverse"></i><div style="font-size:12px; padding-left:8px; color:#FFFFFF; width:100px; padding-top:25px;">Check Out</div></a>');
        }else if(ext=='zip' || ext == 'rar'){
            $("#pdfrender").html('<a href="{{ url('documentAdd') }}?page={{Request::segment(1)}}"><button  id="add-btn" style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a><br/><div><div class="info-box"><span class="info-box-icon bg-aqua"><i class="ion fa fa-file-archive-o"></i></span><div class="info-box-content"><span class="info-box-text" style="text-align: left !important;">This is a compressed document.</span><span style="display: block;font-size: 15px;margin-top: 18px;text-align: left !important;"><a href="{{ config('app.doc_url') }}'+path+'" download>Click here to download the document</a></span></div><!-- /.info-box-content --></div></div>');
        } 
        else if(ext=='mp4'||ext=="ogv"||ext=='webm'||ext=='flv'){
            if(ext=='mp4'){
                $("#pdfrender").html('<a href="{{ url('documentManagementView') }}'+"?dcno="+url+"&id="+id+"&page=document"+'"><button style="margin-bottom:5px;" class="btn btn-primary">Open</button></a><a href="{{ url('documentAdd') }}?page={{Request::segment(1)}}" ><button id="add-btn" style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a><br/><div class="resizable"><video id="player1" style="width:100%;" poster="" preload="none" controls playsinline webkit-playsinline><source src="{{ config('app.doc_url') }}'+path+'" type="video/mp4"></video></div>');  
            }else if(ext=='ogv'){
                $("#pdfrender").html('<a href="{{ url('documentManagementView') }}'+"?dcno="+url+"&id="+id+"&page=document"+'"><button style="margin-bottom:5px;" class="btn btn-primary">Open</button></a><a href="{{ url('documentAdd') }}?page={{Request::segment(1)}}" ><button id="add-btn" style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a><br/><div class="resizable"><video id="player1" style="width:100%;" poster="" preload="none" controls playsinline webkit-playsinline><source src="{{ config('app.doc_url') }}'+path+'" type="video/ogg"></video></div>');  
            }else if(ext=='webm'){
                $("#pdfrender").html('<a href="{{ url('documentManagementView') }}'+"?dcno="+url+"&id="+id+"&page=document"+'"><button style="margin-bottom:5px;" class="btn btn-primary">Open</button></a><a href="{{ url('documentAdd') }}?page={{Request::segment(1)}}" ><button id="add-btn" style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a><br/><div class="resizable"><video id="player1" style="width:100%;" poster="" preload="none" controls playsinline webkit-playsinline><source src="{{ config('app.doc_url') }}'+path+'" type="video/webm"></video></div>');  
            }else if(ext=='flv'){
                $("#pdfrender").html('<a href="{{ url('documentManagementView') }}'+"?dcno="+url+"&id="+id+"&page=document"+'"><button style="margin-bottom:5px;" class="btn btn-primary">Open</button></a><a href="{{ url('documentAdd') }}?page={{Request::segment(1)}}" ><button id="add-btn" style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a><br/><div class="resizable"><div id="player_8168" style="display:inline-block;"><a href="http://get.adobe.com/flashplayer/">You need to install the Flash plugin</a></div>');
                    var flashvars_8168 = {};
                    var params_8168 = {
                        quality: "high",
                        wmode: "transparent",
                        bgcolor: "#ffffff",
                        allowScriptAccess: "always",
                        allowFullScreen: "true",
                        flashvars: "fichier={{ config('app.doc_url') }}"+path
                    };
                    var attributes_8168 = {};
                    flashObject("http://flash.webestools.com/flv_player/v1_27.swf", "player_8168", "490", "300", "8", false, flashvars_8168, params_8168, attributes_8168);  
            }
        }else if(ext=='mp3'||ext=="wav"||ext=='ogg'){
            if(ext=='mp3'){
                $("#pdfrender").html('<a href="{{ url('documentManagementView') }}'+"?dcno="+url+"&id="+id+"&page=document"+'"><button style="margin-bottom:5px;" class="btn btn-primary">Open</button></a><a href="{{ url('documentAdd') }}?page={{Request::segment(1)}}" ><button id="add-btn" style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a><br/><div class="resizable"><audio id="player2" preload="none" controls style="width:100%;"><source src="{{ config('app.doc_url') }}'+path+'" type="audio/mp3"></audio></div>');
            }else if(ext=='wav'){
                $("#pdfrender").html('<a href="{{ url('documentManagementView') }}'+"?dcno="+url+"&id="+id+"&page=document"+'"><button style="margin-bottom:5px;" class="btn btn-primary">Open</button></a><a href="{{ url('documentAdd') }}?page={{Request::segment(1)}}" ><button id="add-btn" style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a><br/><div class="resizable"><audio id="player2" preload="none" controls style="width:100%;"><source src="{{ config('app.doc_url') }}'+path+'" type="audio/x-wav"></audio></div>');
            }else if(ext=='ogg'){
                $("#pdfrender").html('<a href="{{ url('documentManagementView') }}'+"?dcno="+url+"&id="+id+"&page=document"+'"><button style="margin-bottom:5px;" class="btn btn-primary">Open</button></a><a href="{{ url('documentAdd') }}?page={{Request::segment(1)}}" ><button id="add-btn" style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a><br/><div class="resizable"><audio id="player2" preload="none" controls style="width:100%;"><source src="{{ config('app.doc_url') }}'+path+'" type="audio/ogg"></audio></div>');
            }  
        }else{
            if(user_permission){
                var addDocument3 = '<a href="{{ url('documentAdd') }}"><button style="margin-bottom:5px;" class="btn btn-primary pull-right">Add Document <i class="fa fa-plus"></i></button></a>';
            }else{
                var addDocument3 = '';
            }
            $("#pdfrender").html('<a href="{{ url('documentManagementView') }}'+"?dcno="+url+"&id="+id+"&page=document"+'"><button style="margin-bottom:5px;" class="btn btn-primary">Open</button></a>'+addDocument3+'<br/><div class="resizable"><iframe src="webviewer?file={{ config('app.doc_url') }}'+path+'#toolbar=0" id="ifrm1" width="auto" height="auto;" class="resizewrapperiframe"></iframe></div>');
            $("#tpchkout").html('<a href="{{ url('checkoutDocument') }}'+"?id="+url+"&view=grid"+'" title="Check Out"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-edit fa-stack-1x fa-inverse"></i><div style="font-size:12px; padding-left:8px; color:#FFFFFF; width:100px; padding-top:25px;">Check Out</div></a>');
        }
    } 

    /*<!--To disable right click on image after load it-->*/ 
    $('body').bind('contextmenu', function(e) {
        return false;
    });  

    function duplication()
    {
        var val= $("#name_edi").val();
        var editVal= $("#edit_val").val();
        var oldVal= $("#oldVal").val();
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: 'post',
                url: '{{URL('documentGroupDuplication')}}',
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

    function del(id,docname)
    {
       $.ajax({
        type:'GET',
        url : base_url+'/getUserPermission',
        dataType:'JSON',
        success:function(result){  
            if (result.user_delete){ 

                swal({
                  title: "{{trans('language.confirm_delete_single')}}'" + docname + "' ?",
                  
                  type: "{{trans('language.Swal_warning')}}",
                  showCancelButton: true
                }).then(function (result) {
                    if(result){
                        // Success
                       var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                        $.ajax({
                            type: 'post',
                            url: '{{URL('documentDelete')}}',
                            dataType: 'json',
                            data: {_token: CSRF_TOKEN, id:id,docname:docname,view:'list'},
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
                    swal(
                    '{{trans('language.Swal_deleted')}}'
                    )
                });
            } else {
                swal('{{trans('documents.no_permission_delete')}}');
            }
        }
        });
    }      

    function Advancedser()
    {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: 'get',
                url: '{{URL('documentAdvanceSearch')}}',
               
                data: {_token: CSRF_TOKEN},
                timeout: 50000,
                beforeSend: function() {
                    $("#bs").show();
                },
                success: function(data, status){

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
    $('#docsrch').click(function(){
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var searhtext=$('#srchtxt').val()
        if(searhtext=="" || searhtext==null){
            swal("Please enter the keyword for search.");
        }else{
            $.ajax({
                type:'POST',
                url:'{{URL('documentSearch')}}',
                data:{_token:CSRF_TOKEN,q:searhtext},
                beforeSend: function() {
                    $("#bs").show();
                    $('.preloader').show();
                },
				success:function(data){
                    $('.preloader').hide();
					$("#datas").html(data);
					loadFile();
					var tot_doc=$('#doccnt').val();
                    $("#top_lbl").html($("#toplbl").val());
					if(tot_doc){
						$('#num-of-docs').text('Last '+tot_doc+ ' documents');
					}
					else{
						$('#num-of-docs').text('No documents found');
					}
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
    })
    $(function(){
    $('#togg-wrkspace').on('click', function(){
        if( $('#wrkspace-div').is(':visible') ) {
            $('#wrkspace-div').hide();
            $('#list-div').removeClass('col-md-3').addClass('col-md-3');
            $('#view-doc-div').removeClass('col-md-6').addClass('col-md-9');
        }
        else {
            $('#wrkspace-div').show();
            $('#wrkspace-div').removeAttr( 'style' );
            $('#wrkspace-div').removeClass('col-md-3').addClass('col-md-3');
            $('#list-div').removeClass('col-md-3').addClass('col-md-3');
            $('#view-doc-div').removeClass('col-md-9').addClass('col-md-6');    
        }
    });
});
$('body').on('click','#workflow',function(){
        var count = $(this).attr('count');
        var docid = $('#doc_id'+count+'').val();
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
//decrypt file
$('body').on('click','#decrypt_doc',function()
{
    var toplabel = $(this).attr('toplabel');
    var exprstatus = $(this).attr('exprstatus');
    var id = $(this).attr('count');
    var docid      = $(this).attr('dcno');
    var docname = $(this).attr('doc_name');
    var doc_file_name = $(this).attr('doc_file_name');
    var action = 'd';//decrypt
    if(docname=="")
    {
        docname="{{trans('documents.document')}}";
    }
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var ajaxURL_decrypt = 'documentDecrypt';
    swal({
          title: "{{trans('documents.confirm_decrypt_single')}}'" + docname + "' ?",
          
          type: "{{trans('language.Swal_warning')}}",
          showCancelButton: true
        }).then(function (result) {
        if(result)
        {
            $.ajax({
                type: 'post',
                url: ajaxURL_decrypt,
                data: {_token: CSRF_TOKEN,docid:docid,action:action,file:doc_file_name,docname:docname},
                timeout: 50000,
                success: function(data){     
                    console.log(data);
                    //return false;
                    if(data==1)
                    {
                        swal({
                        title: "{{trans('documents.document')}} '"+docname+"' {{trans('documents.success_decrypt')}}",
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ok'
                        }).then(function (result) {
                            if(result){
                                // Success
                                viewfile(id,docid,docname,doc_file_name,exprstatus,toplabel);
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
//encrypt file
$('body').on('click','#encrypt_doc',function()
{
    var docid      = $(this).attr('count');
    var docname = $(this).attr('doc_name');
    var doc_file_name = $(this).attr('doc_file_name');
    var action = 'c';//Encrypt
    if(docname=="")
    {
        docname="{{trans('documents.document')}}";
    }
    var view = "{{Session::get('view')}}";
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var ajaxURL_encrypt = 'documentEncrypt';
    swal({
          title: "{{trans('documents.confirm_encrypt_single')}}'" + docname + "' ?",
          type: "{{trans('language.Swal_warning')}}",
          showCancelButton: true
        }).then(function (result) {
        if(result)
        {
            $.ajax({
                type: 'post',
                url: ajaxURL_encrypt,
                data: {_token: CSRF_TOKEN,docid:docid,view:view,action:action,file:doc_file_name,docname:docname},
                timeout: 50000,
                success: function(data){     
                    // success
                    if(data==1)
                    {
                        swal({
                        title: "{{trans('documents.document')}} '"+docname+"' {{trans('documents.success_encrypt')}}",
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
//decrypt file
$('body').on('click','#decrypt_doc_data',function()
{
    var docid      = $(this).attr('count');
    var docname = $(this).attr('doc_name');
    var doc_file_name = $(this).attr('doc_file_name');
    var action = 'd';//Decrypt
    if(docname=="")
    {
        docname="{{trans('documents.document')}}";
    }
    var view = "{{Session::get('view')}}";
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var ajaxURL_decrypt = 'documentDecrypt';
    swal({
          title: "{{trans('documents.confirm_decrypt_single')}}'" + docname + "' ?",
          
          type: "{{trans('language.Swal_warning')}}",
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
                        title: "{{trans('documents.document')}} '"+docname+"' {{trans('documents.success_decrypt')}}",
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
<script src="js/tiff.min.js"></script>   
{!!Html::style('build/mediaelementplayer.css')!!}
{!!Html::script('build/mediaelement-and-player.js')!!}
@endsection