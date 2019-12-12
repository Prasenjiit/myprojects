<?php
    $str= explode(",",Auth::user()->user_permission);
    include (public_path()."/storage/includes/lang1.en.php" );
    $user_permission=Auth::user()->user_permission;                       
?>
@extends('layouts.app')
@section('main_content')

{!! Html::script('js/parsley.min.js') !!}  
{!! Html::script('js/jquery.form.js') !!}   
{!! Html::style('css/font-awesome.min.css') !!} 
{!! Html::style('css/ionicons.min.css') !!} 
{!! Html::style('plugins/datatables_new/jquery.dataTables.min.css') !!}
{!! Html::script('js/jquery-ui.min.js') !!}
<style type="text/css">
a:active {
    color: black !important; 
}
/* styles during drag */
li.draggable-item.ui-sortable-helper {
  background-color: #e5e5e5;
  -webkit-box-shadow: 0 0 8px rgba(53,41,41, .8);
  -moz-box-shadow: 0 0 8px rgba(53,41,41, .8);
  box-shadow: 0 0 8px rgba(53,41,41, .8);
  transform: scale(1.015);
  z-index: 100;
}
li.draggable-item.ui-sortable-placeholder {
  background-color: #ddd;
  -moz-box-shadow:    inset 0 0 10px #000000;
   -webkit-box-shadow: inset 0 0 10px #000000;
   box-shadow:         inset 0 0 10px #000000;
}
    table.dataTable{
        border-collapse:collapse;
    }
    .expiresoon{
        border-left: 6px solid #9900ff;
    }
    .expired{
        border-left: 6px solid #cc0066;
    }
    .noexpire{
        border-left: 6px solid #996600; 
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
    /*.main-header .logo{
       height: 70px;
    }*/
    .topicn{
        z-index:1050; 
        position:relative;
        left:190px;
        top: -66px;
    }

    /*<--model style-->*/
    .modal-body-pop{
        overflow: auto;
        max-height: 550px;
    }
    .ScrollStyle
    {
        max-height: 570px;
        overflow-y: auto;
    }
    .box-modify {
        border-radius: 0px 0px 3px 3px;
        padding-bottom: 40px;
    }
    #exclude .drag_item
    {
        float: right;

    }
    #include .drag_item
    {
        float: left;
        margin-right: 10px;
    }
    .dataTables_processing
    {
        /*top: 64px !important;*/
        z-index: 1000 !important;
    }
</style>
<section class="content-header">
    <div class="col-sm-2">    
        <!-- heading of page -->    
        <strong>{{$language['documents']}}</strong>
        <a href="{{url('documents')}}" style="padding: 10px;">
            <i class="fa fa-th fa-lg" aria-hidden="true" title="Documents Grid View" id="grid-view-change"></i>
        </a>
    </div>
    <div class="col-sm-7" style="font-size:12px;">    
        {{ $language['color_legend'] }} <button type="button" style="margin-left: 10px; cursor: context-menu; padding: 1px 12px !important; " class="btn btn-warning-btn"></button>&nbsp;{{$language['soon_tobe_expired']}} <?php echo Session::get('settings_document_expiry')." days"; ?>
        <button type="button" style="margin-left: 10px; cursor: context-menu; padding: 1px 12px !important; " class="btn btn-danger-btn"></button>&nbsp;{{$language['expired_docs']}}
        <button type="button" style="margin-left: 10px; padding: 1px 12px !important; cursor: context-menu;" class="btn btn-success-btn"></button>&nbsp;{{$language['all_other_docs']}}
    </div>
    <div class="col-sm-3" style="font-size:12px;">
       <!-- breadcrump section -->
        <!-- <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> {{$language['home']}}</a></li>
            <li class="active">
            Documents List
            </li>
        </ol> -->
    <!-- end breadcrump-->            
    </div>
</section>
<section class="content content-sty" id="msg" style="display:none;"></section>
<section class="content content-sty" id="msg_add" style="display:none;"></section>
@if(Session::has('flash_message_edit'))
<section class="content content-sty" id="spl-wrn">        
    <div class="alert alert-sty {{ Session::get('alert-class', 'alert-info') }} ">{{ Session::get('flash_message_edit') }}</div>        
</section>
@endif

<!-- Error Msg -->
@if(Session::get('dglist') == '0')
<section class="content content-sty">        
    <div class="alert alert-warning" id="hide-div">
        <p style="text-align: center;"><b>There is no data found.</b></p>
    </div>       
</section>
@endif

<!--Checking view permission-->
@if(stristr($user_permission,'view'))   
    <section class="content" id="shw">

        <!--Model body for save criteria-->
        <div class="modal fade" id="addModal" data-backdrop="false" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="dGAddModal" >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-body modal-body-pop">
                        <div class="form-group">
                            Please Enter {{$language['criteria_name']}}<sapn class="share-doc-name"></sapn>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-12 control-label">{{$language['criteria_name']}}:<span style="color:red">*</span></label>
                            <div class="col-sm-12">
                            <input type="hidden" name="searchCriteriaId" id="searchCriteriaId">
                                {!! Form:: 
                                text('criteria_name','', 
                                array( 
                                'class'=> 'form-control', 
                                'id'=> 'criteria_name', 
                                'title'=> $language['criteria_name'], 
                                'placeholder'=> $language['criteria_name'],
                                'autofocus',                
                                )
                                ) 
                                !!}    
                                <span class="criteria_name_error" style="color:red"></span>            
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-5 control-label"></label>
                            <div class="col-sm-7" id="button-section" style="padding-top: 10px;">
                                {!!Form::submit($language['save'], array('class' => 'btn btn-primary sav_btn','id'=>'save-criteria')) !!} &nbsp;&nbsp;
                                    {!!Form::button($language['cancel'], array('class' => 'btn btn-primary btn-danger', 'id' => 'cn', 'data-dismiss' => 'modal')) !!}
                                </div>
                        </div><!-- /.col -->
                        {!!Form::close()!!}
                    </div>
                </div>
            </div>
        </div>
        <!--Model ends-->

        <div class="modal fade" id="userAddModal" data-backdrop="false" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="dGAddModal" >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-body modal-body-pop">
                        <div class="form-group">
                            {{$language['confirm_checkout_single']}}<sapn class="share-doc-name"></sapn>
                        </div>
                        <div class="form-group">
                            <label for="Comments" class="col-sm-12 control-label">{{$language['comments']}}:<span style="color:red">*</span></label>
                            <div class="col-sm-12">
                                {!! Form:: 
                                text('comments','', 
                                array( 
                                'class'=> 'form-control', 
                                'id'=> 'comments', 
                                'title'=> "'".$language['full name']."'".$language['length_others'], 
                                'placeholder'=> $language['comments'],
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
                                {!!Form::submit($language['save'], array('class' => 'btn btn-primary sav_btn','id'=>'comment_save')) !!} &nbsp;&nbsp;
                                    {!!Form::button($language['cancel'], array('class' => 'btn btn-primary btn-danger', 'id' => 'cn', 'data-dismiss' => 'modal')) !!}
                                </div>
                        </div><!-- /.col -->
                        {!!Form::close()!!}
                    </div>
                </div>
            </div>
        </div>

        <!--View more content-->
        <div class="modal fade" id="viewmoreModal" data-backdrop="true" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header" style="border-bottom-color: deepskyblue;">
                        <h4 class="modal-title" style="float:left; width:90%;">
                            {{$language['all documents']}}
                            <small>- View All Data</small>
                        </h4>

                        <a href="javascript:void(0);">
                            <button class="btn btn-primary btn-danger" id="cn" data-dismiss="modal" type="button">{{@$language['close']}}</button>
                        </a>
                    </div>

                    <div class="modal-body modal-body-pop" id="more">  
                       
                    </div><!-- /.modal-dialog -->

                </div><!-- /.modal -->
            </div>
        </div>
            
            {!! Form::open(array('url'=> array('documentsEditAll'), 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'uploadFileEditForm', 'id'=> 'uploadFileEditForm')) !!}
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-info">
                        <div class="box-header">
                            <!--Recent search list-->
                            @if(Session::get('search_list_exists'))
                            <div>
                                    <!-- /.box-header -->
                                    <div class="box-body no-padding col-sm-10">
                                        
                                        <h3 class="box-title">{{@$language['recent search']}}: </h3>

                                        <!--Making serach list as comma seperated-->
                                        @if(Session::get('keyword'))
                                            <span style="font-style: italic; font-weight:bold;">{{$language['keyword_search']}}:</span> {{Session::get('keyword')}}&nbsp
                                        @endif

                                        <!--Making serach list as comma seperated-->
                                        @if(Session::get('serach_doc_no'))
                                            <span style="font-style: italic; font-weight:bold;">{{$language['document no']}}:</span> {{Session::get('serach_doc_no')}}&nbsp
                                        @endif

                                        @if(Session::get('search_docname'))
                                           <span style="font-style: italic; font-weight:bold;">{{$language['document name']}}:</span>{{Session::get('search_docname')}}&nbsp
                                        @endif

                                        @if(Session::get('search_ownership'))
                                           <span style="font-style: italic; font-weight:bold;">{{$language['ownership']}}: </span><?php print_r(implode(',',Session::get('search_ownership')));?>&nbsp
                                        @endif

                                        @if(Session::get('search_created_by'))
                                           <span style="font-style: italic; font-weight:bold;">{{$language['created by']}}: </span><?php print_r(implode(',',Session::get('search_created_by')));?>&nbsp
                                        @endif

                                        @if(Session::get('search_updated_by'))
                                           <span style="font-style: italic; font-weight:bold;">{{$language['last modified by']}}: </span><?php print_r(implode(',',Session::get('search_updated_by')));?>&nbsp
                                        @endif

                                        @if(Session::get('search_departments'))
                                           <span style="font-style: italic; font-weight:bold;">@if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{$language['department']}} @endif: </span><?php print_r(implode(',',Session::get('search_departments')));?>&nbsp
                                        @endif

                                         @if(Session::get('search_document_type_name'))
                                            <input type="hidden" name="doctypeid" id="typeselect" value="{{Session::get('doctypeid')}}">
                                           <span style="font-style: italic; font-weight:bold;">{{$language['document type']}}:</span>{{Session::get('search_document_type_name')}}&nbsp
                                        @endif

                                        @if(Session::get('search_stack'))
                                           <span style="font-style: italic; font-weight:bold;">{{$language['stack']}}:</span><?php print_r(implode(',',Session::get('search_stack')));?>&nbsp
                                        @endif

                                        @if(Session::get('search_created_date_from'))
                                           <span style="font-style: italic; font-weight:bold;">{{$language['created date - from']}}:</span>{{Session::get('search_created_date_from')}}&nbsp
                                        @endif

                                        @if(Session::get('search_created_date_to'))
                                           <span style="font-style: italic; font-weight:bold;">{{$language['created date - to']}}:</span>{{Session::get('search_created_date_to')}}&nbsp
                                        @endif

                                        @if(Session::get('search_last_modified_from'))
                                           <span style="font-style: italic; font-weight:bold;">{{$language['last modified - from']}}:</span>{{Session::get('search_last_modified_from')}}&nbsp
                                        @endif

                                        @if(Session::get('search_last_modified_to'))
                                           <span style="font-style: italic; font-weight:bold;">{{$language['last modified - to']}}:</span>{{Session::get('search_last_modified_to')}}&nbsp
                                        @endif
                                                                  
                                    <!-- /.search-list -->

                                <div>
                                    <a href="{{ url('documentAdvanceSearch/edit') }}" class="btn btn-sm btn-primary" style="cursor:pointer;color: #3c8dbc;" title="{{$language['change_criteria']}}">{{$language['change_criteria']}} <i class="fa fa-arrows-h" aria-hidden="true"></i></a>
                                    
                                    @if(Session::get('search_criteria_id'))
                                        <!--Update criteris-->
                                        <a href="#" id="save-criteria" searchCriteriaId="{{Session::get('search_criteria_id')}}" class="btn btn-sm btn-primary" style="cursor:pointer;color: #3c8dbc;font-size: 12px;" title="{{$language['update_criteria']}}">{{$language['update_criteria']}} <i class="fa fa-pencil"></i></a>
                                    @else
                                        <!--Save criteris-->
                                        <a href="#" id="saveCriteria" class="btn btn-primary" style="cursor:pointer;color: #3c8dbc;font-size: 12px;" title="{{$language['save_criteria_name']}}">{{$language['save_criteria_name']}} <i class="fa fa-plus"></i></a> 
                                    @endif
                                    <!-- export -->
                                    <button type="button" id="export" class="btn btn-primary" data-toggle="modal" data-target="#modal-default" style="cursor:pointer;color: #3c8dbc;font-size: 12px;" title="{{$language['download']}}">{{$language['download']}} <i class="fa fa-fw fa-download"></i></button>
                                    <!-- export -->
                                </div>
                            </div>
                            </div><!--Recent search list ends-->
                            @endif
            
                            <!---Select documeny type-->
                             @if(Request::segment(1) == 'documentsList')
                                <div class="col-xs-2 hidden-xs">    
                                    <label class="control-label" id="labeldoc">Document Type :</label>
                                </div>
                               
                                <div class="col-xs-5">    
                                    <select class="form-control" id="typeselect" name="typeselect">
                                    <option value="0">Select a document type</option>
                                    @foreach($docType as $val)
                                    <option value="{{$val->document_type_id}}">{{$val->document_type_name}}</option>
                                    @endforeach
                                    </select>
                                </div>
                                <div class="col-xs-2">      
                                </div>          
                            @endif  

                        </div>
                        <input type="hidden" name="field1" value="<?php echo $settings_document_no; ?>" id="field1">
                        <input type="hidden" name="field2" value="<?php echo $settings_document_name; ?>" id="field2">
                        <div class="box-body">
                            <!--dataTable loading here-->
                                
    <table border="1" id="documentTypeDT" class="table table-bordered table-striped dataTable hover" width="100%">   
        <thead>
        </thead>
        <tbody>
        </tbody>              
    </table>
                            
                    </div><!-- /.box-body -->
                    </div><!-- /.box -->

                    <div class="form-group">
                        <label class="col-sm-5 control-label"></label>
                        <div class="col-sm-7">
                        
                        <!-- <span id="movetagdraft" value="Save&draft" name="Save&draft" class="btn btn-primary">Download</span> &nbsp;&nbsp; -->
                        <!-- bulk encrypt -->
                        @if (Session::get('module_activation_key6')==Session::get('tval'))
                        <span id="bulkencypt" value="Encrypt" name="bulkencypt" class="btn btn-primary">Encrypt</span> &nbsp;&nbsp;
                        @endif
                        
                        <!-- end bulk encrypt -->
                        @if(stristr($user_permission,'edit')) 
                        {!!Form::submit('Edit', array('class' => 'btn btn-primary','id'=>'Edit')) !!} &nbsp;&nbsp;
                        @endif
                        <input type="hidden" name="hidd_status" value="published">
                        @if(stristr($user_permission,"delete"))
                        <span id="deletetag" value="Delete" name="delete" class="btn btn-primary">{{$language['delete']}}</span> &nbsp;&nbsp;
                        @endif
                        
                        {!!Form::button($language['cancel'], array('class' => 'btn btn-primary btn-danger', 'id' => 'cnEdi','onclick'=> 'window.location="documentAdvanceSearch/edit"')) !!}
                        <input type="hidden" name="hidd_view" id="hidd_view" value="list">
                        <input type="hidden" name="hidd_count" id="hidd_count" value="0">
                        <input type="hidden" name="hidd_type" id="hidd_type" value="published">
                        </div>
                    </div>
                </div><!-- /.col -->
            </div><!-- /.row -->
         <!-- Adv Search -->
        <div class="modal fade" id="dTSearchModal" data-backdrop="false" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content"></div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <!-- export modal -->
        <div class="modal fade" id="modal-default" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">×</span></button>
                        <h4 class="modal-title">{{trans('documents.choose fields')}}</h4>
                    </div>
                    <div class="modal-body">
                    <p style="padding-left:15px; font-size:12px; color:#999;">{{trans('documents.adv_search_popup_help')}}</p>
                        <div class="container">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="box box-info box-solid">
                                        <div class="box-header">
                                            <h3 class="box-title">{{trans('documents.fields')}}</h3>
                                        </div>
                                        <div class="box-footer no-padding">
                                            <ul class="nav nav-stacked connected-sortable droppable-area1" id="exclude">
                                            @foreach($tcolumn as $value)
                                                <li class="draggable-item sort_li" id="{{$value['data']}}" value="{{$value['title']}}"><a style="cursor: move;">{{$value['title']}} <span class="badge bg-green drag_item" style="cursor: pointer;" title="Move"><i class="fa
                                                 fa-arrows-h"></i></span></a></li>
                                              @endforeach
                                              @foreach($tcolumn_dynamic as $value)
                                                <li class="draggable-item sort_li" id="{{$value['column_id']}}" column_id="{{$value['column_id']}}" value="{{$value['title']}}"><a style="cursor: move;">{{ucfirst($value['title'])}} <span class="badge bg-green drag_item" style="cursor: pointer;" title="Move"><i class="fa fa-arrows-h"></i></span></a></li>
                                            @endforeach
                                                                        
                                            </ul>
                                        </div>
                                  </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="box box-info box-solid">
                                        <div class="box-header">
                                            <h3 class="box-title">{{trans('documents.selected fields')}}</h3>
                                        </div>
                                        <div class="box-footer no-padding">
                                            <ul class="nav nav-stacked connected-sortable droppable-area2" id="include">
                                            @foreach($tcolumn_imp as $value)
                                                <li class="draggable-item sort_li" id="{{$value['data']}}" value="{{$value['title']}}"><a style="cursor: move;">{{$value['title']}} <span class="badge bg-green drag_item" style="cursor: pointer;" title="Move"><i class="fa fa-arrows-h"></i></span></a></li>
                                            @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="progress_section_form" style="display: none;margin-top: 10px;">
                    <div class="progress active">
                        <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="50" aria-valuemin="1" aria-valuemax="100" style="width: 1%" id="status_bar">
                          
                        </div>
                    </div>
                    </div>
                    <div class="modal-footer">
                        <label>Download As </label>
                        <!-- <input type="image" src="<?php //echo e(URL::to('/')); ?>/images/icons/pdf.png" alt="Submit" width="28" height="28" class="export_data" format="pdf" title="PDF">
                        <input type="image" src="<?php //echo e(URL::to('/')); ?>/images/icons/text.png" alt="Submit" width="28" height="28" class="export_data" format="csv" title="CSV"> -->
                        
                        <button type="button" class="btn btn-primary export_data" format="pdf" title="Download As PDF"><i class="fa fa-fw fa-file-pdf-o"></i></button>
                        <button type="button" class="btn btn-primary export_data" format="csv" title="Download As CSV"><i class="fa fa-fw fa-file-excel-o"></i></button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- end export modal -->
    </section>
@else
    <section class="content"><div class="alert alert-danger alert-sty">{{$language['dont_hav_permission']}}</div></section>
@endif

<!-- User edit Form -->
<div class="modal fade" id="dTEditModal" data-backdrop="false" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- User edit form end -->
<!-- addview_workflow -->
<div class="modal fade" id="addview_workflow" data-backdrop="true" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div id="content_wf"></div>
</div>
<!-- addview_workflow end -->
{!! Html::script('plugins/datatables_new/jquery.dataTables.min.js') !!}
<style type="text/css">
.no-sort{
        background-image: none !important;
        cursor: default !important;
        pointer-events: none !important;
    }
</style>
<script type="text/javascript">

var opendoc = '<?php echo Session::get("selected_doc_list");?>';
var ownership = [];
<?php 
$todaydate = date('Y-m-d');
if(Session::get('search_ownership'))
{
foreach (Session::get('search_ownership') as $value): ?>
ownership.push('<?php echo $value;?>');
<?php 
endforeach; 
}
?>

var created_by = [];
<?php 
if(Session::get('search_created_by'))
{
foreach (Session::get('search_created_by') as $value): ?>
created_by.push('<?php echo $value;?>');
<?php 
endforeach; 
}
?>

var updated_by = [];
<?php 
if(Session::get('search_updated_by'))
{
foreach (Session::get('search_updated_by') as $value): ?>
updated_by.push('<?php echo $value;?>');
<?php 
endforeach; 
}
?>

var departments = [];
<?php 
if(Session::get('departments'))
{
foreach (Session::get('departments') as $value): ?>
departments.push('<?php echo $value;?>');
<?php 
endforeach; 
}
?>

var stacks = [];
<?php 
if(Session::get('stackids'))
{
foreach (Session::get('stackids') as $value): ?>
stacks.push('<?php echo $value;?>');
<?php 
endforeach; 
}
?>

var doclabl = [];
<?php 
if(Session::get('document_column_name'))
{
foreach (Session::get('document_column_name') as $value): ?>
doclabl.push('<?php echo $value;?>');
<?php 
endforeach; 
}
?>

var doccol = [];
<?php 
if(Session::get('document_column_value'))
{
foreach (Session::get('document_column_value') as $value): ?>
doccol.push('<?php echo $value;?>');
<?php 
endforeach; 
}
?>

var docid = [];
<?php 
if(Session::get('document_type_column_id'))
{
foreach (Session::get('document_type_column_id') as $value): ?>
docid.push('<?php echo $value;?>');
<?php 
endforeach; 
}
?>

var doc_col_type = [];
<?php 
if(Session::get('document_type_column_type'))
{
foreach (Session::get('document_type_column_type') as $value): ?>
doc_col_type.push('<?php echo $value;?>');
<?php 
endforeach; 
}
?>
//console.log(data);
// ajax data table
var today = '{{$todaydate}}';
var selected = [];
var filter =   function()
{   
    var obj = $('#filter').val();
    return obj;
};

var rows_per_page = '{{Session('settings_rows_per_page')}}';
var lengthMenu = getLengthMenu();//Function in app.blade.php

var tcolumn=[];


tcolumn.push({"data":"actions","title":"<input type='checkbox' id='ckbCheckAll' data-orderable='false'></input> {{trans('language.actions')}}"});
tcolumn.push({"data":"department_id","title":"{{trans('language.department')}}","class":""});
tcolumn.push({"data":"stack_id","title":"{{trans('language.Stack')}}","class":""});
tcolumn.push({"data":"document_type_id","title":"{{trans('language.document type')}}"});
tcolumn.push({"data":"document_no","title":"{{trans('language.document no')}}","class":""});
tcolumn.push({"data":"document_name","title":"{{trans('language.document name')}}","class":""});
@php foreach($doctypeApp_selected as $key => $row){ 
$head_footr = (isset($heads[$row->document_type_id]))?$heads[$row->document_type_id]:array();

@endphp
@php 
if(count($head_footr)){
    foreach($head_footr as $value){ @endphp

        tcolumn.push({"data":"{{$value->document_type_column_id}}","title":"{{$value->document_type_column_name}}", "class":" no-sort"}); 

    @php } } 
@endphp
@php } @endphp
tcolumn.push({"data":"document_ownership","title":"{{$language['ownership']}}"});
tcolumn.push({"data":"created_at","title":"{{$language['created date']}}"});
tcolumn.push({"data":"updated_at","title":"{{trans('documents.last updated')}}","class":""});
tcolumn.push({"data":"document_expiry_date","title":"{{trans('documents.expir_date')}}","class":""});
tcolumn.push({"data":"document_status","title":"{{trans('language.status')}}","class":""});


var dtshow = function()
{
    var srchbtn = 1;
    if ( $.fn.DataTable.isDataTable('#documentTypeDT')) 
    {
      console.log("destroy DataTable");
      $('#documentTypeDT').DataTable().destroy();
    }

    $('#documentTypeDT').empty();
    var sort_order = "@php echo (isset($sort_order))?$sort_order:1; @endphp";
    var sort_direct = "@php echo (isset($sort_direct))?$sort_direct:'desc'; @endphp";
    //datatable
    var res_datatable =   $('#documentTypeDT').DataTable({ 
        "processing": true,
        "serverSide": true,
        "order": [[sort_order,sort_direct]],
        "lengthMenu": lengthMenu,
        "responsive": true,
        "destroy": true,
        "pageLength": rows_per_page,
        "displayStart": "@php echo (isset($displayStart))?$displayStart:0; @endphp",
        "colReorder": true,
        "scrollX": true,
        "scrollY": 350,
        "bFilter": true,
        "search": {"search": "@php echo (isset($search_text))?$search_text:''; @endphp"},
        "searching": true,
        "ajax": {
           "url": "@php echo url('documentsAdvSearch'); @endphp",
           "type": "POST",
            "headers": { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
           "data":function(d)
            {
d.filter = filter();
d.search_option = "@php echo @Session::get('search_option'); @endphp";
d.docno = "@php echo @Session::get('serach_doc_no'); @endphp";
d.docname = "@php echo @Session::get('search_docname'); @endphp";
d.ownership = ownership;
d.created_by = created_by;
d.updated_by = updated_by;
d.created_date_from = "@php echo @Session::get('search_created_date_from'); @endphp";
d.created_date_to = "@php echo @Session::get('search_created_date_to'); @endphp";
d.last_modified_from = "@php echo @Session::get('search_last_modified_from'); @endphp";
d.last_modified_to = "@php echo @Session::get('search_last_modified_to'); @endphp";
d.department = departments;
d.stacks = stacks;
d.doctypeid = "@php echo @Session::get('doctypeids'); @endphp";
d.keywrd_srchtxt = "@php echo @Session::get('search_keywrd_srchtxt'); @endphp";
d.search_criteria_id = "@php echo @Session::get('search_criteria_id'); @endphp";
d.coltypecnt = "@php echo @Session::get('coltypecnt'); @endphp";
d.doclabl = doclabl;
d.doccol = doccol;
d.docid = docid;
d.doc_col_type = doc_col_type;
            }
        },
        'fnDrawCallback': function (oSettings) {
        $('.dataTables_filter').each(function () {
            if(srchbtn ==1)
            {   srchbtn=0;
                $(this).append('<button type="button" id="docsrch" class="btn btn-default btn-xs srchbtn" style="height: 24px !important;margin-top: -5px;"margin-right: 17px;><span class="glyphicon glyphicon-search"></span></button>');
                
            }
        });
        },
        "rowCallback": function( row, data ) {
            //$("#colname").html(data['heads']);
            //console.log(opendoc);
            if(data['document_id']==opendoc)
            {
                $(row).addClass('row_selected');
            }
            if(data['document_encrypt_status']==1)
            {
                $(row).addClass('encrypted');
            }
            else if((data['document_expiry_date'] != null) && (data['document_expiry_date'] <= today))
            {
                $(row).addClass('expired');
            }
            else if((data['noofdays']!=0)&&(data['noofdays'] < doc_expire))
            {
                $(row).addClass('expiresoon');
            }
            else if(data['document_status']== 'Checkout')
            {
                $(row).addClass('checkouted');
            }
            else
            {
                $(row).addClass('noexpire');
            }

        },
        
        "iDisplayLength": 25,
        "columns": tcolumn,
        "columnDefs": [
            { "targets": 0, "orderable": false }
            ],
        "language": { processing: '<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span> ',"searchPlaceholder": "{{trans('language.data_tbl_search_placeholder')}}"  },                       
                           
    } );
    //disable auto search of datatable
    $("div.dataTables_filter input").unbind();

        return res_datatable;
    };

var datatable = dtshow();
//disable alert of datatable
$.fn.dataTable.ext.errMode = 'none';
//for highlight the row
    $('#documentTypeDT tbody').on('click', 'tr', function () {
        $("#documentTypeDT tbody tr").removeClass('row_selected');        
        $(this).addClass('row_selected');
    });
    $('#doctype').on('keyup', function(){
        table
        .column(1)
        .search(this.value)
        .draw();
    });       
    $('#docno').on('keyup', function(){
        table
        .column(2)
        .search(this.value)
        .draw();
    });       
    $('#docname').on('keyup', function(){
        table
        .column(3)
        .search(this.value)
        .draw();
    }); 

    $('#dept').on('keyup', function(){
        table
        .column(4)
        .search(this.value)
        .draw();
    });       
    $('#stacks').on('keyup', function(){
        table
        .column(5)
        .search(this.value)
        .draw();
    });
    //datatable search by button 
    $(document).on('click', '#docsrch', function ()
    {
        var value = $('.dataTables_filter input').val();
        console.log(value); // <-- the value
        datatable.search($(".dataTables_filter input").val()).draw();
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

            $("form").submit(function(){
                if(document.getElementById('hidd_count').value==0)
                    {
                        swal("{{$language['not select']}}");
                        return false;
                    }
    });     
</script>

    <script>
   /*<--check out model-->*/
    function myFunction(docname,docid,count) {
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
            // null
            $('.null_error').html('Please fill Check Out Comments.')
        }     
    });/*<--check out model-->*/

    $("#ckbCheckAll").click(function () {
        $(".checkBoxClass").prop('checked', $(this).prop('checked'));
    });
    var countChecked = function() {
    var n = $( ".checkBoxClass:checked" ).length;
    //swal(n);
    document.getElementById('hidd_count').value=n;
    };
    countChecked();
    $( "input[type=checkbox]" ).on( "click", countChecked );

        $("form").submit(function(){
            if(document.getElementById('hidd_count').value==0)
                {
                    swal("{{$language['not select']}}");
                    return false;
                }
        });
    $("#deletetag").click(function(){
    if(document.getElementById('hidd_count').value==0)
        {
            swal("{{$language['not select']}}");
            return false;
        }
    else{   

            swal({
                  title: "{{$language['confirm_delete_single']}} the documents?",
                  text: "{{$language['Swal_not_revert']}}",
                  type: "{{$language['Swal_warning']}}",
                  showCancelButton: true
                }).then(function (result) {
                    if(result){
                        // Success
                        var arr = $('input:checkbox.checkBoxClass').filter(':checked').map(function () {
                        return this.value;
                        }).get();
                        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                            $.ajax({
                                type: 'post',
                                url: '{{URL('deletePublished')}}',
                               
                                data: {_token: CSRF_TOKEN,selected:arr},
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
                    '{{$language['Swal_deleted']}}'
                    )
                });
        }   
    });
        $(function () {

            $('body').on('click','#moredet',function(){
                var count = $(this).attr('count');
                // Get data
                //var docid      = $('#doc_id'+count+'').val();
                var docid      = $(this).attr('count');
                var view = 'list';
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    type: 'get',
                    url: '{{URL::route('documentsMoreDetails')}}',
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
        });

        function del(id,docname)
        {

            swal({
                  title: "{{$language['confirm_delete_single']}}'" + docname + "' ?",
                  text: "{{$language['Swal_not_revert']}}",
                  type: "{{$language['Swal_warning']}}",
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
                                dtshow();
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
                    '{{$language['Swal_deleted']}}'
                    )
                });
    }   
</script>  
<script>
$( init );
function init() {
  $( "#exclude, #include" ).sortable({
      connectWith: ".connected-sortable",
      stack: '.connected-sortable ul'
    }).disableSelection();
}
    $(function ($) {
        //Ajax form
        var options = { 
            target:        '#msg_add',   // target element(s) to be updated with server response 
            beforeSubmit:  showRequest,  // pre-submit callback 
            success:       showResponse,  // post-submit callback     
            complete:      showStatus
        }; 
        // bind form using 'ajaxForm' 
        $('#documentTypeAddForm').ajaxForm(options);


        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        if($("#typeselect").val()==null){
            var value = 0;                 
        }else{
            var value = $("#typeselect").val();
        }
        if(value!=0){
            $.ajax({
                type: 'post',
                url: '{{URL('getDocumentIndexFields')}}',
                data: {_token: CSRF_TOKEN, doctypeid: value },
                timeout: 50000,
                success: function(data, status){
                    if(data) {
                        if(data['document_type_column_no']){
                            $("#docno_lbl").html(data['document_type_column_no']);                                    
                        }else{
                            var field1 = $("#field1").val();
                            $("#docno_lbl").html(field1);
                        }
                        if(data['document_type_column_name']){
                            $("#docname_lbl").html(data['document_type_column_name']);
                        }else{
                            var field2 = $("#field2").val();
                            $("#docname_lbl").html(field2);
                        }
                    }
                }
            });
        }else{
            var field1 = $("#field1").val();
            $("#docno_lbl").html(field1);
            var field2 = $("#field2").val();
            $("#docname_lbl").html(field2);
        }


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
                url: '{{URL('documentTypeDuplication')}}',
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

    
    $('#typeselect').change(function(){
        var selectet_type=$("#typeselect").val(); 
        if(selectet_type==0)
        {
            swal("Please choose a Document type.");
            return false;
        }
        $.ajax({
            url:'{{URL('selectType')}}',
            data:{type:selectet_type},
            type:"get",
            success:function(data,response)
            {
                $('#table_head').html(data);
            }
        });

    });
    function duplication()
    {
        var val= $("#name_edi").val();
        var editVal= $("#edit_val").val();
        var oldVal= $("#oldVal").val();
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            type: 'post',
            url: '{{URL('documentTypeDuplication')}}',
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
      $("#documentTypeAddForm :input").tooltip({
 
      // place tooltip on the right edge
      position: "center",
 
      // a little tweaking of the position
      offset: [-2, 10],
 
      // use the built-in fadeIn/fadeOut effect
      effect: "fade",
 
      // custom opacity setting
      opacity: 0.7
 
      }); 

    // Save criteria
    $(document).ready(function(){
        // Trigger model
        $('#saveCriteria').click(function(){

            // Get criteria name and id if exists
            var searchCriteriaId = $(this).attr('searchCriteriaId');
            var criteriaName     = $(this).attr('criteriaName');

            $('#addModal').modal('toggle');
            return false;
        });

        // Save
        $('body').on('click','#save-criteria',function(){
            
            var searchCriteriaId = $(this).attr('searchCriteriaId');

            if(searchCriteriaId){
                var criteria_name = '';
            }else{
                // Values when add
                var criteria_name = $('#criteria_name').val();
            }
                     
            if(criteria_name || searchCriteriaId){
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    type:'POST',
                    url:'{{URL('saveCriteria')}}',
                    data: {_token: CSRF_TOKEN, criteria_name: criteria_name, searchCriteriaId: searchCriteriaId},
                    success:function(response){
                        if(response == 0){
                            // if response '0' show error message(exists)
                            $('.criteria_name_error').html(criteria_name+' is already exists.Please try with new one.');
                            return false;
                        }else if(response == 1){
                            $('#addModal').modal('hide');
                            swal('Criteria successfully updated');
                        }else if(response == 2){
                            $('#addModal').modal('hide');
                            swal('Criteria successfully added');
                        }
                    }
                });
            }else{
                // Validation error
                $('.criteria_name_error').html('Criteria Name is required.');
                return false;
            }
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
                docname="{{$language['document']}}";
            }
            var view = "{{Session::get('view')}}";
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var ajaxURL_encrypt = 'documentEncrypt';
            swal({
                  title: "{{$language['confirm_encrypt_single']}}'" + docname + "' ?",
                  text: "{{$language['Swal_not_revert']}}",
                  type: "{{$language['Swal_warning']}}",
                  showCancelButton: true
                }).then(function (result) {
                if(result)
                {
                    $.ajax({
                        type: 'post',
                        url: ajaxURL_encrypt,
                        data: {_token: CSRF_TOKEN,docid:docid,view:view,action:action,file:doc_file_name},
                        timeout: 50000,
                        success: function(data){     
                            // success
                            if(data==1)
                            {
                                swal({
                                title: "{{$language['document']}} '"+docname+"' {{$language['success_encrypt']}}",
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
                        data: {_token: CSRF_TOKEN,docid:docid,view:view,action:action,file:doc_file_name},
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
        //encrypt selected
        $("#bulkencypt").click(function()
        {
            if(document.getElementById('hidd_count').value==0)
            {
                swal("{{$language['not select']}}");
                return false;
            }
            else
            {   
                swal({
                    title: "Do you want to encrypt the files?",
                    text: "{{$language['Swal_not_revert']}}",
                    type: "{{$language['Swal_warning']}}",
                    showCancelButton: true
                }).then(function (result) {
                if(result)
                {
                    // Success
                    var arr = $('input:checkbox.checkBoxClass').filter(':checked').map(function () {
                    return this.value;
                    }).get();
                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    var view = "{{Session::get('view')}}";
                    $.ajax({
                        type: 'post',
                        url: '{{URL('bulkEncrypt')}}',
                        data: {_token: CSRF_TOKEN,selected:arr,view:view},
                        timeout: 50000,
                        beforeSend: function() {
                            $("#bs").show();
                        },
                        success: function(data, status){
                            console.log(data);
                            swal({
                                  title: data,
                                  showCancelButton: false
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
                });
            }   
        });
        //addview workflow 
        $('body').on('click','#workflow',function()
        {
            var docid = $(this).attr('count');
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

        $(".drag_item").click(function() {
            if ($(this).closest("ul").attr("id") === "exclude"){
                $(this).closest("li").appendTo("#include");
            }
            else {
                $(this).closest("li").appendTo("#exclude");
            }
        });

        $('.export_data').click(function(e)
        {
            var $progressBar = $('.progress-bar');
            var $status = $('#status_bar');
            $(".progress_section_form").css("display", "block");
            setTimeout(function() {
            $progressBar.css('width', '5%');
            $status.text('5%');
            setTimeout(function() {
            $progressBar.css('width', '20%');
            $status.text('20%');
            setTimeout(function() {
            $progressBar.css('width', '40%');
            $status.text('40%');
            setTimeout(function() {
            $progressBar.css('width', '65%');
            $status.text('65%');
            setTimeout(function() {
            $progressBar.css('width', '97%');
            $status.text('97%');
            }, 20000); 
            }, 8000); 
            }, 4000); 
            }, 2000); 
            }, 1000);
            var format = $(this).attr('format');
            var data_items = new Array();
            var data_values = new Array();
            //var data = $('#include').sortable('serialize');
            $("#include li").each(function( index ) {
                var item = {'title':$( this ).attr('id')};
                data_items.push($( this ).attr('id'));
                data_values.push($( this ).attr('value'));

            });
            console.log(data_items.length);
            if(data_items.length>0){
            $.ajax({
                type:'post',
                url: '{{URL('documentsAdvSearch')}}',
                dataType: "json",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: 
                {

                    search_option : "@php echo @Session::get('search_option'); @endphp",
                    docno : "@php echo @Session::get('serach_doc_no'); @endphp",
                    docname : "@php echo @Session::get('search_docname'); @endphp",
                    ownership : ownership,
                    created_by : created_by,
                    updated_by : updated_by,
                    created_date_from : "@php echo @Session::get('search_created_date_from'); @endphp",
                    created_date_to : "@php echo @Session::get('search_created_date_to'); @endphp",
                    last_modified_from : "@php echo @Session::get('search_last_modified_from'); @endphp",
                    last_modified_to : "@php echo @Session::get('search_last_modified_to'); @endphp",
                    department : departments,
                    stacks : stacks,
                    doctypeid : "@php echo @Session::get('doctypeids'); @endphp",
                    keywrd_srchtxt : "@php echo @Session::get('search_keywrd_srchtxt'); @endphp",
                    search_criteria_id : "@php echo @Session::get('search_criteria_id'); @endphp",
                    coltypecnt : "@php echo @Session::get('coltypecnt'); @endphp",
                    doclabl : doclabl,
                    doccol : doccol,
                    docid : docid,
                    doc_col_type : doc_col_type,
                    action:'export',
                    data_items:data_items,
                    data_values:data_values,
                    data_format:format
                },
                success:function(response){
                    console.log(response.file); 
                    $(".progress_section_form").css("display", "none");
                    if(window.location.href = '{{URL('ExportSearch')}}'+'/'+response.file)
                    {
                        swal("{{trans('documents.search download success')}}");
                    }
                    else
                    {
                        swal("{{trans('documents.search download error')}}");
                    }
                }
            });
            }
            else
            {
                $(".progress_section_form").css("display", "none");
                swal("{{trans('documents.empty msg')}}");
                return false;
            }
        });
    });
</script>    

@endsection