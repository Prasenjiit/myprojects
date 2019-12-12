@extends('layouts.app')
@section('main_content')

{!! Html::style('css/font-awesome.min.css') !!} 
{!! Html::style('css/ionicons.min.css') !!} 
{!! Html::style('plugins/datatables_new/jquery.dataTables.min.css') !!}
{!! Html::style('plugins/datatables_new/colReorder.dataTables.min.css') !!}
<style type="text/css">
    .dataTables_scroll
{
    /*overflow:auto;*/
}
 .dataTables_processing {
        /*top: 64px !important;*/
        z-index: 1000 !important;
    }
</style>


<?php
    $sort_order =  Session::get('serach_order');
    $sort_direct =  Session::get('serach_direct');
    //set session for each view page import,checkout,list
    Session::put('view',Input::get('view'));
    include (public_path()."/storage/includes/lang1.en.php" );
    $user_permission=Auth::user()->user_permission;
    $current_view = Session::get('view');
    //current date
    $todaydate = date('Y-m-d'); // or your date as well
    $doc_expire = Session::get('settings_document_expiry');
  
    //set texts for appropriate pages
        switch(Session::get('view')) 
        {
            //import
            case trans('language.import_view'):
                $heading                = trans('documents.uploaded docs');
                $small                  = trans('documents.tempallview');
                $li_active              = trans('documents.import_data');
                $ajax_url_delete_all    = "deleteSelected";
                $ajax_url_blk_checkout  = "blkCheckout";
                $swal_title_delete_all  = trans('documents.confirm_delete_multiple');
                $ajax_url_publish       = 'publishSelected'; 

            break;
            //checkout
            case trans('language.checkout_view'):
                $heading                = trans('documents.documents');
                $small                  = trans('documents.documents').' '.trans('documents.check out').' '.trans('documents.list');
                $li_active              = trans('documents.documents').' '.trans('documents.check out');
                $ajax_url_delete_all    = "discardPublished";
                $ajax_url_blk_checkout  = "blkCheckout";
                $swal_title_delete_all  = trans('documents.confirm_discard_multiple');
                $ajax_url_publish       = 'publishChecked';

            break;
            //list
            case trans('documents.list_view'):
                $heading                = trans('documents.documents');
                $small                  = trans('documents.documents');
                $li_active              = trans('documents.documents').' '.trans('documents.list');
                $ajax_url_delete_all    = "deleteSelected";
                $ajax_url_blk_checkout  = "blkCheckout";
                $swal_title_delete_all  = trans('documents.confirm_delete_multiple');
                $ajax_url_publish       = '';

            break;
            //side bar document type
            case trans('documents.document_type_view'):
            //side bar department
            case trans('language.department_view'):
            //side bar stack
            case trans('language.stack_view'):
                $heading                = trans('documents.documents').' '.trans('documents.list');
                $small                  = trans('documents.documents');
                $li_active              = trans('documents.documents').' '.trans('documents.list');
                $ajax_url_delete_all    = "deleteSelected";
                $ajax_url_blk_checkout  = "blkCheckout";
                $swal_title_delete_all  = trans('documents.confirm_delete_multiple');
                $ajax_url_publish       = '';
            break;
            default:
                $heading                = trans('documents.documents').' '.trans('documents.list');
                $small                  = trans('documents.documents');
                $li_active              = trans('documents.documents').' '.trans('documents.list');
                $ajax_url_delete_all    = "deleteSelected";
                $ajax_url_blk_checkout  = "blkCheckout";
                $swal_title_delete_all  = trans('documents.confirm_delete_multiple');
                $ajax_url_publish       = '';

        }         
?>
<section class="content-header">
    <div class="col-sm-2" style="padding-right:0px;padding-left: 0px;">    
        <!-- heading of page -->  
        @if(Session::get('view')==trans('documents.import_view'))  
            <strong>{{trans('documents.import_data')}}</strong>
        @elseif(Session::get('view')==trans('documents.checkout_view'))
            <strong>{{trans('documents.checkout_list')}}</strong>
        @else
            <strong>{{trans('documents.all documents')}}</strong>
        @endif
        @if((Session::get('view') != trans('documents.import_view')) && (Session::get('view') != trans('documents.checkout_view'))) 
        <a href="{{url('documents')}}" style="padding: 10px;">
            <i class="fa fa-th fa-lg" aria-hidden="true" title="Documents Grid View" id="grid-view-change"></i>
        </a>
        @endif
        <!-- option to set default -->
        @if(Session::get('view') == trans('documents.list_view')) 
        <div style="width: 170px;padding-top: 5px;padding-bottom: 5px;">
            {{trans('documents.set_as_default')}}
            <div class="material-switch pull-right">
                <input id="someSwitchOptionSuccess" name="someSwitchOption001" type="checkbox" class="defaultview" title="{{trans('documents.set_as_default_title')}}">
                <label for="someSwitchOptionSuccess" class="label-success"></label>
            </div>
        </div>
        <!--end option to set default -->
        @endif
    </div>
    <div class="col-sm-10" style="font-size:12px;">
        @if(Session::get('view')!=trans('documents.import_view'))   
        <span class="pull-left">         
            {{ trans('documents.color_legend') }} 
            <button type="button" id="legend_button" class="btn btn-warning-btn"></button>&nbsp;{{trans('documents.soon_tobe_expired')}} <?php echo "< ".Session::get('settings_document_expiry')." days" ?>
            <button type="button" id="legend_button" class="btn btn-danger-btn"></button>&nbsp;{{trans('documents.expired_docs')}}
            <button type="button" id="legend_button" class="btn btn-encrypt-btn"></button>&nbsp;{{trans('documents.encrypted_docs')}}
            <button type="button" id="legend_button" class="btn btn-checkout-btn"></button>&nbsp;{{trans('documents.checkout_docs')}}
            <button type="button" id="legend_button" class="btn btn-success-btn"></button>&nbsp;{{trans('documents.all_other_docs')}}
            </span>
        @endif
    
        <!-- bulk import button only for import view -->
        @if(Session::get('view')==trans('documents.import_view'))
        <span class="pull-right"> 
            @if(stristr($user_permission,'import'))  
                <a class="btn btn-primary ibtn" href="documents?uploadFile=yes" title="{{trans('documents.upload files')}}" id="import">Import Documents</a> &nbsp;


                <!-- <a class="btn btn-primary ibtn" href="{{URL::route('importFile')}}" title="Bulk Import" id="import">{{$language['import_csv']}}</a> -->
            @endif
             <a class="btn btn-primary ibtn" href="{{URL::route('importFile')}}" title="Bulk Import" id="import">{{trans('documents.import_csv')}}</a>
             </span>
        @endif
       <!--  end bulk-->
    </div>
    <!-- <div class="col-sm-3" style="font-size:12px; float: right;"> -->
       <!-- breadcrump section -->
        <!-- <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> {{trans('language.home')}}</a></li>
            <li class="active">
            {{@$li_active}}
            </li>
        </ol> -->
    <!-- end breadcrump-->            
    <!-- </div> -->
</section>
<!-- session message shows space -->
@if(Session::has('data'))
    <section class="content content-sty" id="spl-wrn">        
            <div class="alert alert-sty alert-success">{{Session::get('data')}}</div>        
    </section>
@endif
 <!-- session msg end -->
<section class="content" id='shw'>
    <!-- check in comment add popup section -->
    <div class="modal fade" id="userAddModal" data-backdrop="false" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="dGAddModal" >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-group">
                        {{trans('language.confirm_checkout_single')}}<sapn class="share-doc-name"></sapn>
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
                            <input type="hidden" id="checkouttype" name="checkouttype">    
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
    <!-- end check in comment add -->
    <!--View more content-->
    <div class="modal fade" id="viewmoreModal" data-backdrop="true" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header" style="border-bottom-color: deepskyblue;">
                    <h4 class="modal-title" style="float:left; width:90%;">
                        {{trans('language.all documents')}}
                        <small>- {{trans('documents.view_all_data')}}</small>
                    </h4>

                    <a href="javascript:void(0);">
                        <button class="btn btn-primary btn-danger" id="cn" data-dismiss="modal" type="button">{{trans('language.close')}}</button>
                    </a>
                </div>

                <div class="modal-body" id="more">  
                   
                </div><!-- /.modal-dialog -->

            </div><!-- /.modal -->
        </div>
    </div>
    <!--Checking view permission-->
    @if(stristr($user_permission,'view'))   
    <!-- form start -->
    {!! Form::open(array('url'=> array('documentsEditAll'), 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'uploadFileEditForm', 'id'=> 'uploadFileEditForm')) !!}
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-info">
                <!-- box header -->
                <div class="box-header">
                    <!-- show/hide section -->
                    <!-- <div class="col-sm-1">
                    <input action="action" onclick="window.history.go(-1); return false;" type="button" value="{{trans('language.back')}}" title="{{trans('language.back')}}" class="btn btn-primary"/>
                    </div> -->
                    <div class="col-sm-2">
                        <div id="grpChkBox" class="test">
                           <div class="button-group">
                                    <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"><span>Show/Hide Columns</span> <span class="caret"></span></button>
                                    <ul class="dropdown-menu dropdown-menu-form" role="menu" >
                                        <li>
                                            <!-- inner menu: contains the actual data -->
                                            <ul class="menu" id="show_hide_button">
                                            
                                            </ul>
                                        </li>
                                    </ul>
                                </div>
                        </div>
                    </div>
                    <!-- document type filter -->
                    <div class="col-sm-2" style="padding-left:1px;">  
                        <select class="form-control" id="typeselect" name="typeselect" style="display:inline;">
                            @foreach($doctypeApp as $val)
                            <option value="{{$val->document_type_id}}" <?php if((Session::get('view') != 'stack') || (Session::get('view') != 'department')){ if(@$type_id == $val->document_type_id) { echo 'selected';}} ?>>{{$val->document_type_name}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="condition" id="condition" value="AND">
                    </div>
                    <!-- expiry section -->
                    @php $style = (Session::get('view') == trans('documents.import_view'))?'display:none;':''; @endphp
                    <div class="col-sm-3" style="{{ $style }}">
                        <label class="control-label" id="labeldoc2">{{trans('language.filter')}} :</label>
                        <select class="form-control" id="radio" name="radio" style="display:inline; width:70%;">
                            @php $selected = (@$serach_filter == trans('documents.radio_all'))?'selected="selected"':''; @endphp
                            <option value="{{trans('documents.radio_all')}}" {{ $selected }}>
                                  {{trans('documents.lnclude Expired Docs')}}
                            </option>
                            @php $selected = (@$serach_filter == trans('documents.radio_expired'))?'selected="selected"':''; @endphp
                            <option value="{{trans('documents.radio_expired')}}" {{ $selected }}>
                               {{trans('documents.Show Only Expired Docs')}}
                            </option>
                             @php $selected = (@$serach_filter == trans('documents.radio_exclude'))?'selected="selected"':''; @endphp
                            <option value="{{trans('documents.radio_exclude')}}" {{ $selected }}> 
                                {{trans('documents.Exclude Expired Docs')}}    
                            </option> 
                            @php $selected = (@$notification_expiry == '1' || @$serach_filter == trans('documents.radio_expire_soon'))?'selected="selected"':''; @endphp
                            <option value="{{trans('documents.radio_expire_soon')}}" {{ $selected }}> 
                                {{trans('documents.Expired Soon')}}
                            </option>
                            @php $selected = (@$notification_expiry == '2' || @$serach_filter == trans('documents.radio_assigned'))?'selected="selected"':''; @endphp
                            <option value="{{trans('documents.radio_assigned')}}" {{ $selected }}> 
                                {{trans('documents.Assigned docs')}}
                            </option> 
                            @php $selected = (@$serach_filter == trans('documents.radio_assigned_to'))?'selected="selected"':''; @endphp
                            <option value="{{trans('documents.radio_assigned_to')}}" {{ $selected }}> 
                                {{trans('documents.Assigned docs to')}}
                            </option> 
                           
                        </select>
                    </div>
                     <div class="col-sm-2" style="padding-right:0px;">  
                        <select class="form-control" id="search_column" name="search_column" >
                      </select> 
                    </div>    
                    <div class="col-sm-3" style="padding-left:1px;">
                     
                    <div class="input-group input-group-sm">
                    
                    <input type="text" class="form-control" id="search_text" name="search_text" value="@php echo (isset($search_text))?$search_text:''; @endphp">
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-info btn-flat" id="docsrch"><i class="fa fa-search"></i> {{trans('language.search')}} </button>
                    </span>
                  </div>
                    
                    </div>


                    <div class="col-sm-3">
                     
                        <div class="preloader col-sm-2" style="text-align: center; margin-top: 5px; display: none;" >
                            <i class="fa fa-spinner fa-pulse fa-fw fa-lg"></i>
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <!-- end expiry buttons -->
                </div>
                <input type="hidden" name="field1" value="<?php echo $settings_document_no; ?>" id="field1">
                <input type="hidden" name="field2" value="<?php echo $settings_document_name; ?>" id="field2">
                <!-- end box header -->
                <div class="box-body">
                    <table border="1" id="documentTypeDT" class="table table-bordered table-striped dataTable hover" width="100%"> 
                        <thead>
                        </thead>
                        <!-- <tfoot>
                            <tr>
                                <td class="0"></td>
                                <td class="1"><input type="text" autocomplete="off" id="doctype" placeholder="{{trans('language.search')}} {{trans('language.document type')}}" style="width:130px; font-size: 12px;"></td>
                                <td class="2"><input type="text" autocomplete="off" id="docno" placeholder="{{trans('language.search')}} {{trans('language.document no')}}" style="width:130px; font-size: 12px;"></td>
                                <td class="3"><input type="text" autocomplete="off" id="docname" placeholder="{{trans('language.search')}} {{trans('language.document name')}}" style="width:130px; font-size: 12px;"></td>
                                <td class="4"><input type="text" id="dept" placeholder="{{trans('language.search')}} @if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{trans('language.department')}} @endif" style="width:130px; font-size: 12px;"></td>
                                <td class="5"><input type="text" autocomplete="off" id="stack" placeholder="{{trans('language.search')}} {{trans('language.stack')}}" style="width:130px; font-size: 12px;"></td>
                                @if($current_view != trans('language.checkout_view'))
                                <td class="6"></td class="7"><td class="8"></td><td class="9">
                                    <input type="text" autocomplete="off" id="created_at_dtRange" placeholder="Date Range" style="width:80px; font-size: 12px;">

                                </td><td class="10">
                                    <input type="text" autocomplete="off" id="updated_at_dtRange" placeholder="Date Range" style="width:80px; font-size: 12px;">

                                </td>

                                <td class="11"></td><td class="12"></td>
                               @elseif($current_view == trans('language.checkout_view'))
                               <td class="6"></td class="7"><td class="8"></td>
                               <td class="9">
                                   <input type="text" autocomplete="off" id="created_at_dtRange" placeholder="Date Range" style="width:80px; font-size: 12px;">
                               </td>
                               <td class="10">
                                   <input type="text" autocomplete="off" id="updated_at_dtRange" placeholder="Date Range" style="width:80px; font-size: 12px;">

                               </td>
                               <td class="11">
                                    <input type="text" autocomplete="off" id="check_out_Date" placeholder="Date Range" style="width:80px; font-size: 12px;">
                               </td><td class="12"></td><td class="13"></td><td class="14"></td>
                               @endif
                            </tr>
                        </tfoot> -->   
                        <tbody>   
                        </tbody>                 
                    </table>
                </div><!-- /.box-body -->     
            </div><!-- /.box -->
            <!-- Buttons bottom section -->
            <!-- import section buttons -->
            @if(Session::get('view') == trans('documents.import_view'))
            <div class="form-group">
                <label class="col-sm-4 control-label processing_spinner text-right"></label>
                <div class="col-sm-8">
                     <span id="movetag" value="Save&publish" name="Save&publish" class="btn btn-primary checkin" data-type="publish" data-parent="0">{{trans('documents.check in and publish')}}</span> &nbsp;&nbsp;
                    <span id="movetagdraft" value="Save&draft" name="Save&draft" class="btn btn-primary checkin" data-type="draft" data-parent="0">{{trans('documents.check in as draft')}}</span> &nbsp;&nbsp;
                    @if(stristr($user_permission,'edit')) 
                    {!!Form::submit('Edit', array('class' => 'btn btn-primary','id'=>'Edit')) !!} &nbsp;&nbsp;
                    @endif
                     <input type="hidden" name="hidd_status" value="unpublished">
                    @if(stristr($user_permission,'delete')) 
                    <span id="deletetag" value="Delete" name="delete" class="btn btn-primary">{{trans('language.delete')}}</span> &nbsp;&nbsp;
                    @endif
                    {!!Form::button(trans('language.cancel'), array('class' => 'btn btn-primary btn-danger', 'id' => 'cnEdi','onclick'=> 'window.location="documents"')) !!}
                    <input type="hidden" name="hidd_count" id="hidd_count" value="0">
                    &nbsp;&nbsp;
                    
                </div>
            </div>
            <div class="progress_section" style="display: none;margin-top: 10px;">
            <div class="progress active">
                <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="50" aria-valuemin="1" aria-valuemax="100" style="width: 1%" id="status_bar">
                  
                </div>
            </div>
            </div>
            @endif
            <!-- end import buttons -->
            <!-- list button section -->
            @if(Session::get('view') == trans('documents.list_view') || Session::get('view') == trans('documents.document_type_view') || Session::get('view') == trans('language.stack_view') || Session::get('view') == trans('language.department_view'))
            <div class="form-group">
               <label class="col-sm-4 control-label processing_spinner text-right"></label>
               <div class="col-sm-7">
                    <!-- <span id="movetagdraft" value="Save&draft" name="Save&draft" class="btn btn-primary">Download</span> &nbsp;&nbsp; -->
                    @if (Session::get('module_activation_key1')==Session::get('tval'))  
                    <!-- bulk encrypt -->
                    <span id="bulkencypt" value="Encrypt" name="bulkencypt" class="btn btn-primary">Encrypt</span> &nbsp;&nbsp;
                    @endif
                    <!-- end bulk encrypt -->
                    @if(stristr($user_permission,'edit')) 
                    {!!Form::submit('Edit', array('class' => 'btn btn-primary','id'=>'Edit')) !!} &nbsp;&nbsp;
                    @endif
                    @if(stristr($user_permission,"checkout"))
                        <span id="blkcheckout" value="Checkout" name="checkout" class="btn btn-primary">{{trans('documents.checkout')}}</span> &nbsp;&nbsp;
                    @endif  
                    <input type="hidden" name="hidd_status" value="published">
                    @if(stristr($user_permission,"delete"))
                        <span id="deletetag" value="Delete" name="delete" class="btn btn-primary btn-danger">{{trans('language.delete')}}</span> &nbsp;&nbsp;
                    @endif
                                  
                    <a href="{{URL('documents')}}" id="cancel_window" value="Cancel" name="Cancel" class="btn btn-primary btn-danger">{{trans('language.cancel')}}</a>
                    <input type="hidden" name="hidd_view" id="hidd_view" value="list">
                    <input type="hidden" name="hidd_count" id="hidd_count" value="0">
                    <input type="hidden" name="hidd_type" id="hidd_type" value="published">
               </div>
            </div>
            <div class="progress_section" style="display: none;margin-top: 10px;">
            <div class="progress active">
                <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="50" aria-valuemin="1" aria-valuemax="100" style="width: 1%" id="status_bar">
                  
                </div>
            </div>
            </div>
            @endif
            <!-- end list buttons -->
            <!-- checkout button section -->
            @if(Session::get('view') == trans('language.checkout_view'))
            <div class="form-group">
                <label class="col-sm-4 control-label processing_spinner text-right"></label>
                <div class="col-sm-8">
                    <span id="movetag" value="Save&publish" name="Save&publish" class="btn btn-primary checkin" data-type="publish" data-parent="0">{{trans('documents.check in and publish')}}</span> &nbsp;&nbsp;
                    <!-- {!!Form::submit('Edit', array('class' => 'btn btn-primary','id'=>'Edit')) !!} &nbsp;&nbsp; -->
                    <span id="deletetag" value="{{trans('language.delete')}}" name="delete" class="btn btn-primary">{{trans('documents.discard check out')}}</span> &nbsp;&nbsp;
                    {!!Form::button(trans('language.cancel'), array('class' => 'btn btn-primary btn-danger', 'id' => 'cnEdi','onclick'=> 'window.location="documents"')) !!}
                    <input type="hidden" name="hidd_count" id="hidd_count" value="0">
                    <input type="hidden" name="hidd_type" id="hidd_type" value="checkout">
                </div>
            </div>
            @endif
            <!-- end checkout button -->
            <!-- end button section -->
        </div><!-- /.col -->
    </div><!-- /.row -->
    {!! Form::close() !!} 
    @else
        <section class="content" style="min-height: 597px;"><div class="alert alert-danger alert-sty">{{trans('language.dont_hav_permission')}}</div></section>
    @endif
    <div class="modal fade" id="addview_workflow" data-backdrop="true" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div id="content_wf"></div>
    </div>

     <div class="modal fade" id="duplicate_modal" data-backdrop="false" data-keyboard="false">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header" style="border-bottom-color: deepskyblue;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Duplicate Documents</h4>
              </div>          
              <div class="modal-body" id="dupicate_data_div">
                       
              </div>
              <div class="modal-footer">
                <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <label class="control-label processing_spinner_modal"></label> 
                   <button class="btn btn-primary checkin" type="button" id="replace" data-type ="" data-parent="1">{{Lang::get('language.replace')}}</button>
                   <button class="btn btn-danger checkin"  type="button" id="discard" data-type ="" data-parent="1">{{Lang::get('language.discard')}}</button>
                   <button class="btn btn-danger" data-dismiss="modal" type="button">{{Lang::get('language.close')}}</button>
                   <input type="hidden" name="dup_hidd_count" id="dup_hidd_count" value="0">
              </div>     
                </div>
                </div>
               </div> 
               
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
</section>
{!! Html::script('plugins/datatables_new/jquery.dataTables.min.js') !!}
{!! Html::script('plugins/datatables_new/dataTables.colReorder.min.js') !!}
{!! Html::script('plugins/datatables_new/jquery.columntoggle.js') !!}
{!! Html::script('plugins/datatables_new/dataTables.fixedHeader.min.js') !!}

<style type="text/css">
    /* Small devices (landscape phones, 576px and up) */
    @media screen and (min-width: 576px) {
        .box {
            /*height: 480px;*/
        } 
    }
    /* Medium devices (tablets, 768px and up) */
    @media screen and (min-width: 768px) {
        .box {
            /*height: 480px;*/
        } 
    }
    /* Large devices (desktops, 992px and up) */
    @media screen and (min-width: 992px) {
        .box {
            /*height: 480px;*/
        } 
    }
    @media screen and (min-width: 1200px) {
        .box {
            /*height: 500px;*/
        } 
    }
    @media screen and (min-width: 1400px) {
        .box {
            /*height: 550px;*/
        } 
    }
    @media screen and (min-width: 1600px) {
        .box {
            /*height: 600px;*/
        } 
    }
    @media screen and (min-width: 1900px) {
        .box {
            /*height: 1080px;*/
        } 
    }
    /*<--model style-->*/
    .modal-body{
        overflow: auto;
        max-height: 550px;
    }
    .radio{
        margin-top: -10px !important;
        margin-left: -15px;
        margin-right: -15px;
        margin-bottom: 0px;
    }
    #labeldoc1{
        margin-left: 0px;
    }
    #labeldoc2{
        margin-left: 0px;
    }
    @media(max-width:760px){
        #labeldoc1{
            margin-left: 0px;
        }
        #labeldoc2{
            margin-left: 0px;
        }
    }
    .dropdown-menu-form {
        padding: 5px 10px 0;
        max-height: 270px;
        overflow-y: scroll;
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
            padding: 45px 0px 0 !important;
            height: 50px;
        }
    }
    /*<--mobile view table-->*/
    @media(max-width:500px){
      
    }
    table.dataTable{
        border-collapse:collapse;
    }
    .encrypted{
        border-left: 6px solid #03826A;
    }
    .checkouted{
        border-left: 6px solid #17202A;
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
    /*.dataTables_wrapper .dataTables_filter{
        display: none;
    }*/
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
   /* .no-sort{
        background-image: none !important;
        cursor: default !important;
        pointer-events: none !important;
    }*/
</style>

<script type="text/javascript">
var opendoc = '<?php echo Session::get("selected_doc_list");?>';
var processing_spinner='<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>';
$(function() {

    // $('#keywrd_srchtxt').keypress(function(event){
    //     var keycode = (event.keyCode ? event.keyCode : event.which);
    //     if(keycode == '13'){
    //         alert('You pressed a "enter" key in textbox'); 
    //     }
    // });

    
    var $defaultview = '{{Auth::user()->user_documents_default_view}}';
    if($defaultview == 'list')
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
        var defaultview = 'list';
    }
    else
    {
        var defaultview = 'grid';
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
$(document).on("click","#searchindex",function(e) {
  var keywrd_srchtxt = $('#keywrd_srchtxt').val(); 
  if(!keywrd_srchtxt)
  {
    swal("Please enter the index");
        return false;
  }
  var doctype = $("#typeselect").val();
  var condtn = $("#condition").val();
  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
  //console.log(keywrd_srchtxt); 
  var newForm = $('<form>', {
        'action': "{{URL('documentsAdvSearch')}}",
        'method': 'post'
    }).appendTo('body').append($('<input>', {
        'name': 'search_option',
        'value': condtn,
        'type': 'hidden'
    })).appendTo('body').append($('<input>', {
        'name': 'doctypeid',
        'value': doctype,
        'type': 'hidden'
    })).appendTo('body').append($('<input>', {
        'name': 'keywrd_srchtxt',
        'value': keywrd_srchtxt,
        'type': 'hidden'
    })).append($('<input>', {
        'name': '_token',
        'value': CSRF_TOKEN,
        'type': 'hidden'
    }));
    newForm.submit();
    $('.preloader').show();
});

var base_url = window.location.origin;
var view = '{{Session::get('view')}}';
var ajaxURL_typeSelect = 'selectType';
var ajaxURL_deleteSelect = '{{@$ajax_url_delete_all}}';
var ajaxURL_blkCheckout = '{{@$ajax_url_blk_checkout}}';
var swalDelete_title = '{{@$swal_title_delete_all}}';
var ajaxURL_publishSelect = '{{@$ajax_url_publish}}';
var ajaxURL_moreDetails = 'documentsMoreDetails';
var ajaxURL_deleteSingle = 'documentDelete';
var today = '{{$todaydate}}';
var doc_expire = '{{$doc_expire}}';
var radio_selected = $('#radio').val();
var selected_type=$("#typeselect").val();
var view = "{{Session::get('view')}}";
var sel_item_id = "{{@$sel_item_id}}";
var docid = "{{@$doc_id}}";
var rows_per_page = "@php echo (isset($rows_per_page))?$rows_per_page:Session('settings_rows_per_page'); @endphp";
// Disappear message box 
$('#spl-wrn').delay(5000).slideUp('slow');

//bulk checkout selected
$("#blkcheckout").click(function()
{
    if(document.getElementById('hidd_count').value==0)
    {
        swal("{{trans('documents.not select')}}");
        return false;
    }
    else
    {     
        myFunction('','','','','1');    
        
    }   
});


//encrype selected
$("#bulkencypt").click(function()
{
    if(document.getElementById('hidd_count').value==0)
    {
        swal("{{trans('documents.not select')}}");
        return false;
    }
    else
    {   
        swal({
            title: "Do you want to encrypt the files?",
            text: "{{trans('language.Swal_not_revert')}}",
            type: "{{trans('language.Swal_warning')}}",
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


/*<--check out model-->*/
function myFunction(filename,docname,docid,count,type) 
{
    $('#userAddModal').modal('toggle');
    $('#userAddModal').modal('show');
    $('.share-doc-name').text(filename+' ?');
    $("#hidd-doc-id").val(docid);
    $('#hidd-doc-name').val(docname);
    $('#hidd-doc-count').val(count);
    $('#checkouttype').val(type);
}


//more details
$('body').on('click','#moredet',function()
{
    var docid      = $(this).attr('count');
    var view = "{{Session::get('view')}}";
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        type: 'get',
        url: ajaxURL_moreDetails,
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
          text: "{{trans('language.Swal_not_revert')}}",
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
$('body').on('click','#decrypt_doc',function()
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
          text: "{{trans('language.Swal_not_revert')}}",
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


//discard publish single document
function discard_published(id,docname)
{   
    swal({
      title: "{{trans('documents.confirm_discard_single')}}'" + docname + "' ?",
      text: "{{trans('language.Swal_not_revert')}}",
      type: "{{trans('language.Swal_warning')}}",
      showCancelButton: true
    }).then(function (result) {
        if(result){
            // Success

            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: 'post',
                url: '{{URL('discardDocumentPublished')}}',
               
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
        swal(
        "{{trans('documents.Swal_success')}}"
        )
    });
}
//discard draft single document
function discard_draft(id,docname)
{
    swal({
      title: "{{trans('documents.confirm_discard_single')}}'" + docname + "' ?",
      text: "{{trans('language.Swal_not_revert')}}",
      type: "{{trans('language.Swal_warning')}}",
      showCancelButton: true
    }).then(function (result) {
        if(result){
            // Success

            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: 'post',
                url: '{{URL('discardDocumentDraft')}}',
               
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
        swal(
        "{{trans('documents.Swal_success')}}"
        )
    });
} 
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
//Data table
$("document").ready(function() 
{   
    $(document).keypress(
            function(event){
            if (event.which == '13') {
              event.preventDefault();
            }
        });
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
                            $("#docno").attr("placeholder", data['document_type_column_no']);
                            $("#docno_lbl").html(data['document_type_column_no']);                                    
                        }else{
                            var field1 = $("#field1").val();
                            $("#docno_lbl").html(field1);
                            $("#docno").attr("placeholder", field1);
                        }
                        if(data['document_type_column_name']){
                            $("#docname").attr("placeholder", data['document_type_column_name']);
                            $("#docname_lbl").html(data['document_type_column_name']);
                        }else{
                            var field2 = $("#field2").val();
                            $("#docname_lbl").html(field2);
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
            $("#docname_lbl").html(field2);
            $("#docname").attr("placeholder", field2);
        }
    

    //Initilize date picker
    /*@ New Code added By Bibin John
    /* For Implementing date range filtering */

    var d           = new Date();
    var currentYear = d.getFullYear();
    var newDate     = currentYear+10;
    var date        = '12/31/'+newDate;

     // disable previous year
    var dd  = d.getDate();
    var mm = d.getMonth()+1; 
    if(dd<10) {
        dd  = '0'+dd
    } 

    if(mm<10) {
        mm = '0'+mm
    } 
    today = currentYear+'-'+mm+'-'+dd;
    /*Created At Sorting */
    $('#created_at_dtRange').daterangepicker({
            opens: 'left',
            "drops": "up",
            "showDropdowns": true,
            startDate: today,
            "buttonClasses": "btn btn-primary",
            "applyButtonClasses": "btn-primary ",
            "cancelClass": "btn-danger "
            //endDate: currentYear+'/'+mm+'/01'
    }, function(start, end, label) {
            //console.log("DT RANGE -> " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            var dtRange = start.format('YYYY-MM-DD')+'_'+end.format('YYYY-MM-DD');
            datatable
                .column(8)
                .search(dtRange)
                .draw();
    });
    /*Created At Sorting End */



    /*Updated At Sorting */
    $('#updated_at_dtRange').daterangepicker({
            opens: 'left',
            "drops": "up",
            "showDropdowns": true,
            startDate: today,
            "buttonClasses": "btn btn-primary",
            "applyButtonClasses": "btn-primary ",
            "cancelClass": "btn-danger "
            //endDate: currentYear+'/'+mm+'/01'
    }, function(start, end, label) {
            //console.log("DT RANGE -> " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            var dtRange = start.format('YYYY-MM-DD')+'_'+end.format('YYYY-MM-DD');
            datatable
                .column(9)
                .search(dtRange)
                .draw();
    });

    /*Updated At Sorting End */

     /*Check Out date :: Sorting */
    $('#check_out_Date').daterangepicker({
            opens: 'left',
            "drops": "up",
            "showDropdowns": true,
            startDate: today,
            "buttonClasses": "btn btn-primary",
            "applyButtonClasses": "btn-primary ",
            "cancelClass": "btn-danger "
            //endDate: currentYear+'/'+mm+'/01'
    }, function(start, end, label) {
            //console.log("DT RANGE -> " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            var dtRange = start.format('YYYY-MM-DD')+'_'+end.format('YYYY-MM-DD');
            datatable
                .column(10)
                .search(dtRange)
                .draw();
    });
    /*Ends Check Out date :: Sorting*/

    /*Date Range Code Ends :: Bibin John */


    var i ='<?php echo @$count_sub;?>';
    var lengthMenu = getLengthMenu();//Function in app.blade.php
    var typeselect =   function()
    {   
        var obj = $('#typeselect').val();
        return obj;
    };
    var filter =   function()
    {   
        var obj = $('#radio').val();
        return obj;
    };

     var search_text =   function()
    {   
        var obj = $('#search_text').val();
        return obj;
    };

    var search_column_data =   function()
    {   
        var column_type = $('#search_column').find(':selected').attr('data-column_type');
        var search_column_type =  (typeof column_type === "undefined")?"":column_type;
        var dynamic_col = $('#search_column').find(':selected').attr('data-dynamic_col');
        var search_dynamic_col =  (typeof dynamic_col === "undefined")?"0":dynamic_col;
        console.log("search_column_type = "+search_column_type);
        var obj = {'search_column':$('#search_column').val(),'search_column_type':search_column_type,'search_dynamic_col':search_dynamic_col};
        return obj;
    };

  
   var tcolumns=[];
   var ulbuttons=[];
   var duplcolumns=[];
   var doctype_options=[];
    @php foreach($doctypeApp as $key => $row){ 
        $head_footr = (isset($heads[$row->document_type_id]))?$heads[$row->document_type_id]:array();
        $loop=0;
        @endphp
        var tcolumn=[];
        var ulhtml='';
         var duplcolumn=[];
        var doctype_option='';
        var loop = 0;
        tcolumn.push({"data":"actions","title":"<input type='checkbox' id='ckbCheckAll' data-orderable='false'></input> {{trans('language.actions')}}","class":"{{ $loop }} "});
        ulhtml +='<li><input type="checkbox" name="{{ $loop }}" class="toogle_click" checked="checked" /><a> {{trans("language.actions")}}</a></li>';
        doctype_option +="<option value='search_all' data-column_type='' data-dynamic_col='0'>{{trans('language.search_all')}}</option>";    
        duplcolumn.push({"data":"actions","title":"<input type='checkbox' id='dupckbCheckAll'></input> {{trans('language.actions')}}"});
        
        var department_name ="@if(Session::get('settings_department_name')){{ Session::get('settings_department_name') }} @else {{trans('language.department')}} @endif";
        tcolumn.push({"data":"department_id","title":"@if(Session::get('settings_department_name')){{ Session::get('settings_department_name') }} @else {{trans('language.department')}} @endif","class":"{{ ++$loop }}"});
        ulhtml +='<li><input type="checkbox" name="{{ $loop }}" class="toogle_click" checked="checked" /><a> {{trans("language.department")}}</a></li>';
        doctype_option +="<option value='department' data-column_type='' data-dynamic_col='0'>@if(Session::get('settings_department_name')){{ Session::get('settings_department_name') }} @else {{trans('language.department')}} @endif</option>";
        duplcolumn.push({"data":"department_name","title":department_name});

        var stack_name = "{{trans('language.Stack')}}";
        tcolumn.push({"data":"stack_id","title":"{{trans('language.Stack')}}","class":"{{ ++$loop }}"});
        ulhtml +='<li><input type="checkbox" name="{{ $loop }}" class="toogle_click" checked="checked" /><a> {{trans("language.Stack")}}</a></li>';
       doctype_option +="<option value='stack' data-column_type='' data-dynamic_col='0'>{{trans('language.Stack')}}</option>";
       duplcolumn.push({"data":"stack_name","title":stack_name});

       doctype_option +="<option value='filename' data-column_type='' data-dynamic_col='0'>{{trans('language.file_name')}}</option>";

        // tcolumn.push({"data":"document_type_id","title":"{{trans('language.document type')}}"});
        var document_no_label ="{{$row->document_type_column_no}}";
        tcolumn.push({"data":"document_no","title":"{{$row->document_type_column_no}}","class":"{{ ++$loop }}"});
        ulhtml +='<li><input type="checkbox" name="{{ $loop }}" class="toogle_click" checked="checked" /><a> {{$row->document_type_column_no}}</a></li>';
        doctype_option +="<option value='document_no' data-column_type='' data-dynamic_col='0'>{{$row->document_type_column_no}}</option>";
        duplcolumn.push({"data":"document_no","title":document_no_label});

        var document_name_label ="{{$row->document_type_column_name}}";
        tcolumn.push({"data":"document_name","title":"{{$row->document_type_column_name}}","class":"{{ ++$loop }}"});
        ulhtml +='<li><input type="checkbox" name="{{ $loop }}" class="toogle_click" checked="checked" /><a> {{$row->document_type_column_name}}</a></li>';
        doctype_option +="<option value='document_name' data-column_type='' data-dynamic_col='0'>{{$row->document_type_column_name}}</option>"; 
        duplcolumn.push({"data":"document_name","title":document_name_label});    
        @php 
        if(count($head_footr)){
            foreach($head_footr as $value){ @endphp

                tcolumn.push({"data":"{{$value->document_type_column_id}}","title":"{{$value->document_type_column_name}}", "class":"{{ ++$loop }} no-sort"}); 
                ulhtml +='<li><input type="checkbox" name="{{ $loop }}" class="toogle_click" checked="checked" /><a> {{$value->document_type_column_name}}</a></li>'; 
                doctype_option +="<option value='{{$value->document_type_column_id}}' data-column_type='{{$value->document_type_column_type}}' data-dynamic_col='1'>{{$value->document_type_column_name}}</option>";  
                duplcolumn.push({"data":"{{$value->document_type_column_id}}","title":"{{$value->document_type_column_name}}"});      
            @php } } 
        @endphp
        
        // tcolumn.push({"data":"document_ownership","title":"{{$language['ownership']}}"});
        // tcolumn.push({"data":"document_path","title":"{{$language['document path']}}"});
       if(view == 'checkout')
        {  
        tcolumn.push({"data":"document_checkout_date","title":"{{trans('documents.check out date')}}","class":"{{ ++$loop }}"});
        ulhtml +='<li><input type="checkbox" name="{{ $loop }}" class="toogle_click" checked="checked" /><a> {{trans("documents.check out date")}}</a></li>';
        doctype_option +="<option value='check_out_date' data-column_type='' data-dynamic_col='0'>{{trans('documents.check out date')}}</option>"; 

        tcolumn.push({"data":"document_modified_by","title":"{{trans('documents.check out by')}}","class":"{{ ++$loop }}"});
        ulhtml +='<li><input type="checkbox" name="{{ $loop }}" class="toogle_click" checked="checked" /><a> {{trans("documents.check out by")}}</a></li>';
        doctype_option +="<option value='check_out_by' data-column_type='' data-dynamic_col='0'>{{trans('documents.check out by')}}</option>"; 
        }          
        // tcolumn.push({"data":"created_at","title":"{{$language['created date']}}"});
        tcolumn.push({"data":"updated_at","title":"{{trans('documents.last updated')}}","class":"{{ ++$loop }}"});
        ulhtml +='<li><input type="checkbox" name="{{ $loop }}" class="toogle_click" checked="checked" /><a> {{trans("documents.last updated")}}</a></li>';
        doctype_option +="<option value='last_updated' data-column_type='' data-dynamic_col='0'>{{trans('documents.last updated')}}</option>"; 

        tcolumn.push({"data":"document_expiry_date","title":"{{trans('documents.expir_date')}}","class":"{{ ++$loop }}"});
        ulhtml +='<li><input type="checkbox" name="{{ $loop }}" class="toogle_click" checked="checked" /><a> {{trans("documents.expir_date")}}</a></li>';
        doctype_option +="<option value='expir_date' data-column_type='' data-dynamic_col='0'>{{trans('documents.expir_date')}}</option>"; 

        tcolumn.push({"data":"document_status","title":"{{trans('language.status')}}","class":"{{ ++$loop }}"});
        ulhtml +='<li><input type="checkbox" name="{{ $loop }}" class="toogle_click" checked="checked"/><a> {{trans("language.status")}}</a></li>';
        doctype_option +="<option value='status' data-column_type='' data-dynamic_col='0'>{{trans('language.status')}}</option>"; 
        duplcolumn.push({"data":"document_name","title":"{{trans('language.status')}}"}); 

        tcolumns["{{$row->document_type_id}}"] = tcolumn;
        ulbuttons["{{$row->document_type_id}}"] = ulhtml;
        duplcolumns["{{$row->document_type_id}}"] = duplcolumn;
        doctype_options["{{$row->document_type_id}}"] = doctype_option;
    @php } @endphp

        /*var srchbtn = 1;*/
        var search_column_history = "@php echo (isset($search_column))?$search_column:''; @endphp"
        var dtshow = function(){
            if ( $.fn.DataTable.isDataTable('#documentTypeDT')) 
            {
              console.log("destroy DataTable");
              $('#documentTypeDT').DataTable().destroy();
            }

            $('#documentTypeDT').empty();
            if($("#typeselect").val()==null){
                var value = 0;                 
            }else{
                var value = $("#typeselect").val();
            }
            $('#show_hide_button').html(ulbuttons[value]);
            $('#search_column').html(doctype_options[value]);
            if(search_column_history !='')
            {
                $('#search_column').val(search_column_history);     
            }    
            var sort_order = "@php echo (isset($sort_order))?$sort_order:1; @endphp";
            var sort_direct = "@php echo (isset($sort_direct))?$sort_direct:'desc'; @endphp";
            
            var res_datatable = $('#documentTypeDT').DataTable( {
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
                /*"search": {"search": "@php echo (isset($search_text))?$search_text:''; @endphp"},*/
                "searching": false,
                "ajax": 
                {
                    "url": "@php echo url('selectType'); @endphp",
                    "type": "POST",
                    "headers": { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    "data":function(d)
                    {
                        //tcolumn.push({"data":"headsocument_status"});
                        d.type = typeselect();
                        d.filter = filter();
                        d.view = '{{Session::get('view')}}';
                        d.id = "{{@$sel_item_id}}";
                        d.docid = $("#hidd-doc-id").val();
                        d.search_text = search_text();
                        d.search_column_data = search_column_data();
                    }
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
                    else if((data['document_expiry_date']) && (data['document_expiry_date'] <= today))
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
                'fnDrawCallback': function (oSettings) {
                   /* $('.dataTables_filter').each(function () {
                        if(srchbtn ==1)
                        {   srchbtn=0;
                            $(this).append('<button type="button" id="docsrch" class="btn btn-default btn-xs srchbtn" style="height: 24px !important;margin-top: -5px;"margin-right: 17px;><span class="glyphicon glyphicon-search"></span></button>');
                            
                        }
                    });*/
                },
                "columns": tcolumns[value],            
                "columnDefs": [
                        //{ "orderable": false, "targets": 0 },
                        { "targets": 0, "orderable": false }
                        ],
                "language": { processing: '<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span> ',"searchPlaceholder": "{{trans('language.data_tbl_search_placeholder')}}"  },                                                        
            });
            //alert disable
            $.fn.dataTable.ext.errMode = 'none';

            //disable auto search of datatable
            $("div.dataTables_filter input").unbind();

            return res_datatable;
        };

        var datatable = dtshow();

        //high light the selected row with skin color
        $(document).on('click','#documentTypeDT tbody tr',function(){
        $("#documentTypeDT tbody tr").removeClass('row_selected');        
        $(this).addClass('row_selected');
        });

        $('#show_hide_button').on('click', '.toogle_click', function () 
        {
             console.log("aatr"+$(this).attr('name'));
             var column = datatable.column("." +$(this).attr('name'));

             column.visible( ! column.visible() );
        });
   
    //Radio button - filter by expired
    $('#radio').change(function() {
        datatable.ajax.reload();    
    });

    $('#typeselect').change(function(){
        datatable = dtshow();
    });
    
    $('#doctype').on('keyup', function(){
        datatable
        .column(1)
        .search(this.value)
        .draw();
    });
    $('#docno').on('keyup', function(){
        datatable
        .column(2)
        .search(this.value)
        .draw();
    });
    $('#docname').on('keyup', function(){
        datatable
        .column(3)
        .search(this.value)
        .draw();
    });
    $('#dept').on('keyup', function(){
        datatable
        .column(4)
        .search(this.value)
        .draw();
    });
    $('#stack').on('keyup', function(){
        datatable
        .column(5)
        .search(this.value)
        .draw();
    }); 
    // all check checkbox selection
    //$("#ckbCheckAll").click(function ()
    $(document).on('click', '#ckbCheckAll', function ()  
    {
        $(".checkBoxClass").prop('checked', $(this).prop('checked'));
        countChecked();
    });
        var countChecked = function() 
    {
        var n = $( ".checkBoxClass:checked" ).length;
        document.getElementById('hidd_count').value=n;

        var n = $( ".DupcheckBoxClass:checked" ).length;
        console.log("dn"+n);
        document.getElementById('dup_hidd_count').value=n;
    };
    //select each checkbox
    $(document).on('click', '.checkBoxClass', function () 
    {
        countChecked();
    });

     //duplicate Checkbox
    $(document).on('click', '.DupcheckBoxClass', function () 
    {
        countChecked();
    });

    $(document).on('click', '#dupckbCheckAll', function ()  
    {
        $(".DupcheckBoxClass").prop('checked', $(this).prop('checked'));
        countChecked();
    });

    //datatable search by button 
    $(document).on('click', '#docsrch', function ()
    {
        var value = $('#search_text').val();
        console.log(value); // <-- the value
        datatable.ajax.reload();    
    });
/*Delete single document*/
$(document).on('click', '.delDoc', function ()
{   
    var id = $(this).attr('data-docid');
    var docname = $(this).attr('data-docname');
    var filename = $(this).attr('data-filename');
    if(docname=="")
    {
        docname="{{trans('documents.document')}}";
    }
    swal({
          title: "{{trans('language.confirm_delete_single')}}'" + docname + "' ?",
          text: "{{trans('language.Swal_not_revert')}}",
          type: "{{trans('language.Swal_warning')}}",
          showCancelButton: true
        }).then(function (result) {
        if(result)
        {
            // Success
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var view = "{{Session::get('view')}}";
            $.ajax({
                type: 'post',
                url: ajaxURL_deleteSingle,
                data: {_token: CSRF_TOKEN, id:id,docname:filename,view:view},
                timeout: 50000,
                beforeSend: function() {
                    $("#bs").show();
                },
                success: function(data, status)
                {
                    // success
                    if(data==1)
                    {
                        
                        swal({
                        title: "{{trans('documents.document')}} '"+docname+"' {{trans('language.success_delete')}}",
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ok'
                        }).then(function (result) {
                            if(result){
                                // Success
                                /*window.location.reload();*/
                                datatable.ajax.reload( null,false);    
                            }
                        });
                    }
                    else
                    {
                        // data=0
                        swal("{{trans('documents.contact_admin')}}");
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
    });
});



//Delete selected
$("#deletetag").click(function()
{
    if(document.getElementById('hidd_count').value==0)
    {
        swal("{{trans('documents.not select')}}");
        return false;
    }
    else
    {   
        swal({
            title: swalDelete_title,
            text: "{{trans('language.Swal_not_revert')}}",
            type: "{{trans('language.Swal_warning')}}",
            showCancelButton: true
        }).then(function (result) {
        if(result)
        {
            $(".processing_spinner").html(processing_spinner);
            // Success
            var arr = $('input:checkbox.checkBoxClass').filter(':checked').map(function () {
            return this.value;
            }).get();
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var view = "{{Session::get('view')}}";
            $.ajax({
                type: 'post',
                url: ajaxURL_deleteSelect,
                data: {_token: CSRF_TOKEN,selected:arr,view:view},
                timeout: 50000,
                beforeSend: function() {
                    $("#bs").show();
                    show_bar();
                },
                success: function(data, status){
                    $("#ckbCheckAll").prop('checked', false);
                    /*window.location.reload();*/
                    datatable.ajax.reload( null,false);
                    $(".processing_spinner").html("");
                    swal(data)
                },
                error: function(jqXHR, textStatus, errorThrown){
                    $(".processing_spinner").html("");
                    console.log(jqXHR);    
                    console.log(textStatus);    
                    console.log(errorThrown);    

                },
                complete: function() {
                    $("#bs").hide();
                    hide_bar();
                }
            });
        }
        $('#hidd_count').val(0);
        });
    }   
});

//comment add when checkout
$('#comment_save').click(function()
{
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var docid=$("#hidd-doc-id").val();
    var docname=$('#hidd-doc-name').val();
    var comments=$('#comments').val();
    var count=$('#hidd-doc-count').val();
    var type = $('#checkouttype').val();

    if(comments)
    {
        $(".processing_spinner").html(processing_spinner);
        // success
        if(type=='1'){ //for multiple checkout
            // Success
            var arr = $('input:checkbox.checkBoxClass').filter(':checked').map(function () {
            return this.value;
            }).get();
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var view = "{{Session::get('view')}}";
            $.ajax({
                type: 'post',
                url: ajaxURL_blkCheckout,
                data: {_token: CSRF_TOKEN,selected:arr,view:view,comments:comments},
                timeout: 50000,
                beforeSend: function() {
                    $("#bs").show();
                },
                success: function(data, status){
                    $(".processing_spinner").html("");
                    // alert(data);
                    //window.location.reload();
                    $('#userAddModal').modal('hide');
                    //$('#status'+count).text('Checkout');
                    datatable.ajax.reload( null,false);
                    window.location.href = 'chkoutdownload';
                },
                error: function(jqXHR, textStatus, errorThrown){
                    $(".processing_spinner").html("");
                    console.log(jqXHR);    
                    console.log(textStatus);    
                    console.log(errorThrown);    
                },
                complete: function() {
                    $("#bs").hide();
                }
            });
        }else{
            $('.null_error').html('')
            $.ajax({
                type: 'post',
                url: "{{URL('commentAdd')}}",
                dataType: 'json',
                data: {_token:CSRF_TOKEN,hidd_doc_id:docid,hidd_doc_name:docname,comments:comments },
                beforeSend: function() 
                {
                    
                },
                success: function(data){
                    $(".processing_spinner").html("");
                    $('#userAddModal').modal('hide');
                    $('#status'+count).text('Checkout');
                    var download_file = (typeof data.download_file != 'undefined') ? data.download_file:'';
                    var file_name = (typeof data.file_name != 'undefined') ? data.file_name:'Download';
                    if(download_file)
                    {
                        //window.location.href = 'download';
                        var a = document.createElement('a');
                        a.href = download_file;
                        a.download = file_name;
                        document.body.append(a);
                        a.click();
                        a.remove();
                    }
                    datatable.ajax.reload( null,false);

                },
                error: function(jqXHR, textStatus, errorThrown){
                    $(".processing_spinner").html("");
                    console.log(jqXHR);    
                    console.log(textStatus);    
                    console.log(errorThrown);    
                },
                complete: function() {
                    // window.location.reload();
                }
            });
        }
    }
    else
    {
        // null
        $('.null_error').html("{{trans('documents.Fill Check Out Comments')}}");
    }     
});

//selected publish
$(".checkin").click(function()
{
    var document_type = $("#typeselect").val();
    var checkin_type = $(this).attr('data-type');
    var checkin_parent = $(this).attr('data-parent'); /*if 0 its coming from datatable,if 1 its coming from modal*/
    if(checkin_parent == 1) /*from duplicate modal*/
    {
        var chk = document.getElementById('dup_hidd_count').value;
        var checkin_action=$(this).attr('id');
    }
    else
    {
        var chk = document.getElementById('hidd_count').value;
        var checkin_action='';
    }
    
    if(chk==0)
    {
        swal("{{trans('documents.not select')}}");
        return false;
    }
    else
    { 
        
        swal({
              title: '{{trans('language.Swal_are_you_sure')}}',
              text: "{{trans('language.Swal_not_revert')}}",
              type: "{{trans('language.Swal_warning')}}",
              showCancelButton: true
            }).then(function (result) {
                if(result){
                    // Success
                    if(checkin_parent == 1) /*from duplicate modal*/
                    {
                            $(".processing_spinner_modal").html(processing_spinner);
                            var arr = $('input:checkbox.DupcheckBoxClass').filter(':checked').map(function () {
                                        return this.value;
                                        }).get();
                            
                    }
                    else
                    {
                            $(".processing_spinner").html(processing_spinner);
                            var arr = $('input:checkbox.checkBoxClass').filter(':checked').map(function () {
                                        return this.value;
                                        }).get();
                            
                    }
                    
                    var published_count = arr.length;
                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    //$(".preloader").css("display", "block");
                    //$(".progress_section").css("display", "block");
                    $.ajax({
                    type: 'post',
                    dataType:'json',
                    url: ajaxURL_publishSelect,
                    data: {_token: CSRF_TOKEN,selected:arr,checkin_type:checkin_type,checkin_parent:checkin_parent,checkin_action:checkin_action,doctype:document_type,response_format:'json'},
                    timeout: 50000,
                    beforeSend: function() {
                    show_bar();
                    },
                    success: function(data)
                    {
                        $(".processing_spinner").empty();
                        $(".processing_spinner_modal").empty();
                        $("#ckbCheckAll").prop('checked', false);
                        var duplicate_count = (typeof data.duplicate_count != 'undefined') ? data.duplicate_count:0;
                        if(duplicate_count != 0)
                        {
                            value = $("#typeselect").val();

                        var headers = duplcolumns[value];
                        var duplicate_list = data.duplicate_list;

                        var dupicate_data_div ='<div class="row">';
                            dupicate_data_div +='<div class="col-xs-12 col-sm-12 col-md-12">';
                            dupicate_data_div +='<div class="alert alert-sty alert-success alert_section">'+data.message+'</div>';
                            dupicate_data_div +='<table border="1" class="table table-bordered table-striped dataTable">';
                            dupicate_data_div +='<thead>';
                            dupicate_data_div +='<tr>';
                            $.each(headers, function(i, item) {
                                var nowrap=(i == 0)?'nowrap':'';
                                var style='style="text-align:left;"';
                                dupicate_data_div +='<th '+nowrap+' '+style+'>'+item.title+'</th>';
                            });
                            
                            dupicate_data_div +='</tr>';
                            dupicate_data_div +='</thead>';
                            dupicate_data_div +='<tbody>';
                            
                            $.each(duplicate_list, function(i1, item1) {
                                dupicate_data_div +='<tr>';
                                $.each(item1, function(i2, item2) {
                                dupicate_data_div +='<td>'+item2+'</td>';
                                });
                                dupicate_data_div +='</tr>';
                            });
                            
                            dupicate_data_div +='</tbody>';
                            dupicate_data_div +='</table>';
                            dupicate_data_div +='</div>';
                            dupicate_data_div +='</div>';
                            $('#dupicate_data_div').html(dupicate_data_div);
                            $('#duplicate_modal').modal().show();
                            hide_bar();
                            $('#replace').attr("data-type",checkin_type);
                            $('#discard').attr("data-type",checkin_type);
                            $('.alert_section').delay(10000).slideUp('slow');
                            datatable.ajax.reload( null,false);
                            countChecked();
                        }
                        else
                        {
                            $(".preloader").css("display", "none");
                            hide_bar();
                            swal(data.message);
                            datatable.ajax.reload( null,false);
                            $('#duplicate_modal').modal().hide();

                        }
                        
                        $('.progress-bar').attr('aria-valuenow',"98");
                        $('.progress-bar').attr('aria-valuemin',"0");
                        $('.progress-bar').attr('aria-valuemax',"100");
                        $('.progress-bar').css('width', '98%');
                        $('#status_bar').text('98%');
                        $(".progress_section").css("display", "block");
                    },
                    error: function(jqXHR, textStatus, errorThrown){
                        console.log(jqXHR);    
                        console.log(textStatus);    
                        console.log(errorThrown);    
                    },
                    complete: function() {
                        /*$("#bs").hide();*/
                        hide_bar();
                    },xhr: function()
                      {
                        var xhr = new window.XMLHttpRequest();
                        console.log("xhr"+xhr);
                        /*If not supported, do nothing*/
                        if (!xhr) {
                            return;
                        }
                        /*Upload progress*/
                        if(xhr.upload) {
                            xhr.upload.addEventListener('progress', this.progress, false);
                         }
                        /*Download progress*/
                        xhr.addEventListener('progress', this.progress, false);
                        return xhr;
                      },
                    progress: function(e) {
                        if(e.lengthComputable) 
                        {
                            
                            var pct = (e.loaded / e.total) * 100;
                            console.log("pct", pct);
                            /*$('#prog')
                                .progressbar('option', 'value', pct)
                                .children('.ui-progressbar-value')
                                .html(pct.toPrecision(3) + '%')
                                .css('display', 'block');*/
                        } else 
                        {
                            console.warn('Content Length not reported!');
                        }
                    }
                    });
                }
        });
    }    
});


});  
//for highlight the row
// $('#documentTypeDT tbody').on('click', 'tr', function () 
// {
//     $("#documentTypeDT tbody tr").removeClass('row_selected');        
//     $(this).addClass('row_selected');
// });
//hide and show column    
$(function () 
{
    /*var $chk = $("#grpChkBox input:checkbox");
    var $tbl = $(document);

    $chk.prop('checked', true);

    $chk.click(function () {
        var colToHide = $tbl.find("." + $(this).attr("name"));
        $(colToHide).toggle();
    });*/
});
//not close the dropdown window on each click
$('.dropdown-menu').on('click', function(e) 
{
  if($(this).hasClass('dropdown-menu-form')) {
      e.stopPropagation();
  }
});

//form submit
$("form").submit(function()
{
    if(document.getElementById('hidd_count').value==0)
        {
            swal("{{trans('documents.not select')}}");
            return false;
        }
}); 
/* show progress bar */
function show_bar()
{
    var $progressBar = $('.progress-bar');
    var $status = $('#status_bar');
    $(".progress_section").css("display", "block");
    setTimeout(function() {
        $progressBar.css('width', '25%');
        $status.text('25%');
        setTimeout(function() {
            $progressBar.css('width', '50%');
            $status.text('50%');
            setTimeout(function() {
                $progressBar.css('width', '75%');
                $status.text('75%');
                setTimeout(function() {
                    $progressBar.css('width', '98%');
                    $status.text('98%');
                    
                }, 6000); 
            }, 4000); 
        }, 2000); 
    }, 100); 
}
/* hide progress bar */
function hide_bar()
{
    $(".progress_section").css("display", "none");
}
</script>  
@endsection
