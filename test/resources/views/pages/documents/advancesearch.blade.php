<?php
include (public_path()."/storage/includes/lang1.en.php" );
$user_permission=Auth::user()->user_permission;
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
{!! Html::style('css/sweetalert2.min.css') !!} 

{!! Html::script('js/sweetalert2.min.js') !!}  
{!! Html::script('js/fastselect.standalone.js') !!}  
{!! Html::script('plugins/daterangepicker/daterangepicker.js') !!}

<!-- Content Wrapper. Contains page content -->
<style type="text/css">
    .selbox{
        width: 100%;
        height: 34px;
        background: #fff;
        border-color: #d2d6de;
    }
    .instructions{
        padding-left: 16px;
        margin-bottom: -12px;
        line-height: 16px;
    }
    .fstElement{
        width: 100%;
    }
    .fstControls{
        width: 100% !important;
    }
    a {
        color: #3c8dbc;
    }
    /*Responsive*/
    @media(max-width:1097px){
        #fa-icon{
            padding-top: 12% !important;
        }
    }
    @media(max-width:767px){
        #fa-icon {
            padding-left: 4% !important;
            padding-top: 0% !important;
        }
    }
    /*Responsive*/
    /* Style the tab */
.tab {
    overflow: hidden;
    border: 1px solid #ccc;
    background-color: #f1f1f1;
}

/* Style the buttons inside the tab */
.tab button {
    background-color: inherit;
    float: left;
    border: none;
    outline: none;
    cursor: pointer;
    padding: 10px 16px;
    transition: 0.3s;
    font-size: 15px;
}

/* Change background color of buttons on hover */
.tab button:hover {
    background-color: #ddd;
}

/* Create an active/current tablink class */
.tab button.active {
    background-color: #ccc;
}

/* Style the tab content */
.tabcontent {
    display: none;
    padding: 6px 12px;
    border: 1px solid #ccc;
    border-top: none;
    background-color: #fff;
}
.fstSingleMode.fstActive .fstResults {
    display: block;
    z-index: 10;
    margin-top: -1px;
    max-height: 100px;
}

</style>

<section class="content-header">
    <div class="col-sm-8">
        <span style="float:left;">
            <strong>
                {{$language['advance search']}}
            </strong>
        </span>
    </div>
    <div class="col-sm-4">
       <!--  <ol class="breadcrumb">
                <li><a href="<?php echo  url('/home');?>"><i class="fa fa-dashboard"></i> {{$language['home']}}</a></li>
                <li><a href="<?php echo  url('/documents');?>">{{$language['documents']}}</a></li>
                <li class="active">{{$language['advance search']}}</li>
        </ol>  -->  
    </div>
</section>

    <!--Warming message-->
    @if(session('status'))
        <div id="test" style="padding-left: 16px;padding-right: 15px;">
            <div class="alert alert-warning" id="hide-div">
                <p style="text-align: center;"><strong>Warning!</strong> {{ session('status') }}</p>
            </div>
        </div>
    @endif


<!--Checking view permission-->
@if(stristr($user_permission,'view')) 
<section class="content" id="shw">  
<div class="modal-body"> 


    <div class="tab">
        <button class="tablinks @if($name == 'document_search'){{'active'}}@endif" onclick="openTab(event, 'document_search')">Document</button>
        @if (Session::get('module_activation_key4')==Session::get('tval'))  
            <button class="tablinks @if($name == 'forms'){{'active'}}@endif" onclick="openTab(event, 'form_search')">Form</button>
        @endif
        <button class="tablinks" id="link_to_contentsearch">Content Search</button>
      
        <button class="tablinks" id="link_to_missingdocs">Download Mismatch Documents</button>
        <button class="tablinks" id="link_to_missingfiles">Download Mismatch Files</button>
        <button class="tablinks" id="link_to_missingdocs_list">List Duplicate Documents</button>
    </div>

    <div id="document_search" class="tabcontent" @if($name != 'forms'){{'style=display:block;'}}@endif>
        <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Document search</h3>
            </div>
            <div class="box-body">
                <div class="row">
                <!-- form start -->
                {!! Form::open(array('url'=> array('searchadv'), 'files'=>true, 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'documentAdvSearchForm', 'id'=> 'documentAdvSearchForm','data-parsley-validate'=> '','onsubmit' => 'return validateForm()')) !!}   

                <!-- Lift Side -->
                <div class="col-sm-6">
                    <div class="form-group">
                        <!--changed the style by Abhijith-->
                        <!-- <div class="col-sm-9">
                        	{!! Form::label($language['content_search'].':', '', array('class'=> 'control-label'))!!}
                            <input class="form-control" type="text" id="content_srchtxt" name="content_srchtxt" placeholder="{{$language['place_holder_content_search']}}">
                            <p style="font-size:12px; color:#999;">{{ $language['content_search_desc']}}</p>
                        </div>
                        <div class="col-sm-3">
                            {!! Form::label($language['options'].':', '', array('class'=> 'control-label'))!!}
                            <select class="selbox form-control" id="andor" name="searchformat">
                                <option value="or" selected>{{$language['OR']}}</option>
                                <option value="and">{{$language['AND']}}</option>
                                <option value="ex">{{$language['EXACT']}}</option>
                            </select>               
                        </div>

                        <div class='col-sm-12 instructions' style="color: green;">
                            <p><b>*{{$language['OR']}}</b> - {{$language['OR_content_note']}}</p>
                            <p><b>*{{$language['AND']}}</b> - {{$language['AND_content_note']}}</p>
                            <p><b>*{{$language['EXACT']}}</b> - {{$language['EXACT_content_note']}}</p>
                        </div>
                        </br> --> 

                        <div class="col-sm-12">
                            {!! Form::label($language['keyword_search'].':', '', array('class'=> 'control-label'))!!}
                            <input class="form-control" type="text" id="keywrd_srchtxt" name="keywrd_srchtxt" placeholder="{{$language['place_holder_keywrd_search']}}">
                        </div>

                       <input type="hidden" name="field1" value="<?php echo $settingsDetails[0]->settings_document_no; ?>" id="field1">
                       <input type="hidden" name="field2" value="<?php echo $settingsDetails[0]->settings_document_name; ?>" id="field2">
                        <?php if($serach_criteria):?>           
                            <div class="col-sm-11">
                                {!! Form::label($language['search_criteria'].':', '', array('class'=> 'control-label'))!!}
                                <select name="search_criteria_id" id="search_criteria" class="multipleSelect form-control">
                                    <option value="">Select {{$language['search_criteria']}}s</option>
                                    <?php
                                    foreach ($serach_criteria as $key => $cri) {
                                        ?>
                                        <option value="<?php echo $cri->search_criteria_id; ?>" <?php if(Session::get('search_criteria_id') == $cri->search_criteria_id):echo "selected";endif;?>><?php echo $cri->criteria_name;?></option>
                                        <?php
                                    }
                                    ?>
                                </select>    
                            </div>
                            <!--Delete saved criteria-->
                            <div class="col-sm-1" id="fa-icon" style="padding-top: 5%;padding-left: 1%;">
                                <a href="#" id="deleteSavedSearch" ><i class="fa fa-trash fa-lg" aria-hidden="true" title="Delete {{$language['search_criteria']}}"></i></a>
                            </div>   
                        <?php else:?>
                            <input type="hidden" value="" id="search_criteria">
                        <?php endif;?>

                        <div class="col-sm-12"> 
                            <label class="control-label">@if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{$language['department']}} @endif</label>
                            <select name="department[]" id="department" class="multipleSelect form-control" multiple>
                                <?php
                                foreach ($results as $key => $row) {
                                    ?>
                                    <option id="department_<?php echo $row->department_id;?>" value="<?php echo $row->department_id;?>" <?php if(Session::get('departments')):if(in_array($row->department_id,Session::get('departments'))):echo 'selected';endif;endif;?> ><?php echo $row->department_name;?></option>
                                    <?php
                                }
                                ?>
                            </select>   
                        </div>   

                        <div class="col-sm-12">
                        
                            {!! Form::label($language['document types'].':', '', array('class'=> 'control-label'))!!}
                            <select name="doctypeid" id="doctypeid" class="multipleSelect form-control">
                                <option value="0">Select Document Types</option>
                                <?php
                                foreach ($docType as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row->document_type_id; ?>" <?php if(Session::get('doctypeids') == $row->document_type_id):echo 'selected';endif;?>><?php echo $row->document_type_name;?></option>
                                    <?php
                                }
                                ?>
                            </select>          
                        </div>

                        <div class="col-sm-12">
                            {!! Form::label($settingsDetails[0]->settings_document_no.':','', array('class'=> 'control-label','id' => 'docno_lbl'))!!}
                            {!! Form:: 
                            text(
                            'docno', Session::get('serach_doc_no'), 
                            array( 
                            'class'                  => 'form-control', 
                            'id'                     => 'docno', 
                            'title'                  => $settingsDetails[0]->settings_document_no, 
                            'placeholder'            => $settingsDetails[0]->settings_document_no,
                            )
                            ) 
                            !!}      
                        </div>

                        <div class="col-sm-12">
                            {!! Form::label($settingsDetails[0]->settings_document_name.':', '', array('class'=> 'control-label','id' => 'docname_lbl'))!!}
                            {!! Form:: 
                            text(
                            'docname', Session::get('search_docname'), 
                            array( 
                            'class'                  => 'form-control', 
                            'id'                     => 'docname', 
                            'title'                  => $settingsDetails[0]->settings_document_name, 
                            'placeholder'            => $settingsDetails[0]->settings_document_name,
                            )
                            ) 
                            !!}
                        </div> 
                        <!--show content or html of doc type column when click on doctype-->
                        <div id="sublist">
                        @if(Session::get('search_document_type_name'))
                            <input id="doc_type_ids" type="hidden" value="<?php echo Session::get('doctypeids');?>">
                           
                        @endif
                       </div>

                    </div>
                </div> <!-- End Of Lfet Side -->

                <!-- Right Side -->
                <div class="col-sm-6">
                    <div class="form-group">              

                        <div class="col-sm-12">
                            {!! Form::label($language['stacks'].':', '', array('class'=> 'control-label'))!!}
                            <select name="stacks[]" id="stacks" class="multipleSelect form-control" multiple>
                            <!-- <option value="0">Select a Stack</option> -->
                                <?php
                                foreach ($stacks as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row->stack_id; ?>" <?php if(Session::get('stackids')):if(in_array($row->stack_id,Session::get('stackids'))):echo 'selected';endif;endif;?>><?php echo $row->stack_name;?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                         <div id="stackDiv"></div>
                        @if(Auth::user()->user_role != Session::get('user_role_private_user'))
                        <div class="col-sm-12">
                            {!! Form::label($language['ownership'].':', '', array('class'=> 'control-label'))!!}
                            <select name="ownership[]" id="ownership" class="multipleSelect form-control" multiple>
                                <?php
                                foreach ($users as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row->username; ?>" <?php if(Session::get('owner_ids')):if(in_array($row->id,Session::get('owner_ids'))):echo 'selected';endif;endif;?> ><?php echo ucfirst($row->username);?></option>

                                    <?php
                                }
                                ?>
                            </select> 
                        </div>
                        @endif
                        
                        

                       <div class="col-md-12">

                       <a href="javascript:void(0)" class="btn btn-primary show_hide_handle btn-xs pull-right" style="margin-top:5px;">More <i class="fa fa-angle-down"></i></a>

                        

                       
                    </div>
                        <div class="col-sm-12 collapse_group1">
                            {!! Form::label($language['created by'].':', '', array('class'=> 'control-label'))!!}
                            <select name="created_by[]" id="created_by" class="multipleSelect form-control" multiple>
                                <?php
                                foreach ($users as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row->username; ?>" <?php if(Session::get('created_by_owner_ids')):if(in_array($row->id,Session::get('created_by_owner_ids'))):echo 'selected';endif;endif;?> ><?php echo ucfirst($row->username);?></option>

                                    <?php
                                }
                                ?>
                            </select> 
                            
                        </div>

                        <div class="col-sm-12 collapse_group1">
                            {!! Form::label($language['last modified by'].':', '', array('class'=> 'control-label'))!!}
                            <select name="updated_by[]" id="updated_by" class="multipleSelect form-control" multiple>
                                <?php
                                foreach ($users as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row->username; ?>" <?php if(Session::get('updated_by_owner_ids')):if(in_array($row->id,Session::get('updated_by_owner_ids'))):echo 'selected';endif;endif;?> ><?php echo ucfirst($row->username);?></option>

                                    <?php
                                }
                                ?>
                            </select> 
                           
                        </div>

                        <div class="col-sm-12 collapse_group1">
                            {!! Form::label($language['created date - from'].':', '', array('class'=> 'control-label'))!!}
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" value="{{Session::get('search_created_date_from')}}" class="form-control active" id="created_date_from" name="created_date_from" placeholder="YYYY-MM-DD" title="Created Date - From" data-toggle="tooltip" data-original-title="">
                            </div>
                        </div>

                        <div class="col-sm-12 collapse_group1">
                            {!! Form::label($language['created date - to'].':', '', array('class'=> 'control-label'))!!}
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" value="{{Session::get('search_created_date_to')}}" class="form-control active" id="created_date_to" name="created_date_to" placeholder="YYYY-MM-DD" title="Created Date - To" data-toggle="tooltip" data-original-title="">
                            </div>
                        </div>

                        <div class="col-sm-12 collapse_group1">
                            {!! Form::label($language['last modified - from'].':', '', array('class'=> 'control-label'))!!}
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" value="{{Session::get('search_last_modified_from')}}" class="form-control active" id="last_modified_from" name="last_modified_from" placeholder="YYYY-MM-DD" title="Last Modified Date- From" data-toggle="tooltip" data-original-title="">
                            </div>
                        </div>

                        <div class="col-sm-12 collapse_group1">
                            {!! Form::label($language['last modified - to'].':', '', array('class'=> 'control-label'))!!}
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" value="{{Session::get('search_last_modified_to')}}" class="form-control active" id="last_modified_to" name="last_modified_to" placeholder="YYYY-MM-DD" title="Last Modified Date - To" data-toggle="tooltip" data-original-title="">
                            </div>
                        </div>

                        <!--Search options-->
                        <div class="col-sm-12"> 
                            {!! Form::label($language['search_option'].':', '', array('class'=> 'control-label'))!!}
                            <select name="search_option" id="search_option" class="multipleSelect form-control">
                                <option value="AND" @if(Session::get('search_option') == 'AND') selected @endif >AND</option>
                                <option value="OR" @if(Session::get('search_option') == 'OR') selected @endif> OR</option>
                            </select>                          
                        </div> 
                        <div class='instructions' style="color: green;">
                            <p><b>*{{$language['OR']}}</b> - {{$language['OR_search_option']}}</p>
                            <p><b>*{{$language['AND']}}</b> - {{$language['AND_search_option']}}</p>
                        </div> </br>   

                    </div>
                </div><!-- End Of Right Side -->
                <section class="content">
                    <div class="form-group">
                        <div class="col-sm-12" style="text-align:right;">
                            {!!Form::submit($language['search'], array('class' => 'btn btn-primary', 'id'=> 'saveEdi')) !!} &nbsp;&nbsp;

                                <!-- @if(Session::get('page_doc'))
                                    <a href="{{URL::route('documents')}}" class = "btn btn-primary btn-danger">{{$language['cancel']}}</a> &nbsp;&nbsp;
                                @elseif(Session::get('page_doc_view'))
                                   {{Session::forget('search_documentsIds')}}
                                    Distroy previous search list
                                    <a href="{{URL::route('documentsList')}}" class = "btn btn-primary btn-danger">{{$language['cancel']}}</a> &nbsp;&nbsp;
                               @endif -->
                                
                            <a href="javascript:void(0)" class="btn btn-primary" id="clr" title="{{$language['reset']}}">{{$language['reset']}}</a> &nbsp;&nbsp;

                            <a href="{{URL::route('documents')}}" class = "btn btn-primary btn-danger">{{$language['cancel']}}</a>&nbsp;&nbsp;

                        </div>
                    </div><!-- /.col -->
                </section>
                <div class="progress_section_document" style="display: none;margin-top: 10px;">
                    <div class="progress active">
                        <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="50" aria-valuemin="1" aria-valuemax="100" style="width: 1%" id="status_bar">
                          
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}        
            </div>
        </div>
    </div>




    <!-- /.box-body -->
</div>
<div id="form_search" class="tabcontent" @if($name == 'forms'){{'style=display:block;'}}@endif>
<div class="box box-primary">
    <div class="box-header with-border">
      <h3 class="box-title">Form search</h3>
    </div>
    <div class="box-body">
    <div class="row">
    <form role="form" id="form_search_tab" action="{{url('form_adv_search')}}" method="post" onsubmit="return validateFormSearch();" class="form-horizontal">
    {!! csrf_field() !!}
    <div class="col-sm-6">
        <div class="form-group">
            <?php /*<div class="col-sm-9">
                {!! Form::label($language['content_search'].':', '', array('class'=> 'control-label'))!!}
                <input class="form-control" type="text" id="content_srchtxt" name="content_srchtxt" placeholder="{{$language['place_holder_content_search']}}" value="{{Session::get('form_content_search')}}">
            </div>

            <div class="col-sm-3">
                {!! Form::label($language['options'].':', '', array('class'=> 'control-label'))!!}
                <select class="selbox form-control" id="andor" name="searchformat">
                    <option value="or" <?php if(Session::get('form_content_search_comb') == 'or'){ echo "selected";} ?> >{{$language['OR']}}</option>
                    <option value="and" <?php if(Session::get('form_content_search_comb') == 'and'){echo "selected";} ?>>{{$language['AND']}}</option>
                    <option value="ex" <?php if(Session::get('form_content_search_comb') == 'ex'){echo "selected";} ?>>{{$language['EXACT']}}</option>
                </select>               
            </div>

            <div class='instructions' style="color: green;">
                <p><b>*{{$language['OR']}}</b> - {{$language['OR_content_note']}}</p>
                <p><b>*{{$language['AND']}}</b> - {{$language['AND_content_note']}}</p>
                <p><b>*{{$language['EXACT']}}</b> - {{$language['EXACT_content_note']}}</p>
            </div> 
            <div class="col-sm-12">
             <label class="checkbox-inline"><input type="checkbox" id="attached" name="attached" <?php if(Session::get('form_content_search_attach') == 'on'){ echo "checked";} ?>><strong>Include attached documents in content search</strong></label>
            </div>
            </br>*/?>
            <div class="col-sm-12">
              <label>Form Name:</label>
              <select class="form-control multipleSelect" id="form_name"   name="form_name[]" multiple>
                        
                           @foreach($forms as $row)
                           <option value="{{$row->form_id}}" <?php if(Session::get('form_search_form_name')):if(in_array($row->form_id,Session::get('form_search_form_name'))):echo 'selected';endif;endif;?>>{{$row->form_name}}</option>
                           @endforeach  
                        </select>
            </div>
            <?php /*<div class="col-sm-12">
              <label>Assigned To:</label>
              <select class="form-control multipleSelect" id="assigned_to"   name="assigned_to">
                        <option value="">{{ $language['select_user'] }}</option>
                           @foreach($user as $row)
                           <option value="{{$row->id}}" <?php if(Session::get('form_search_assigned_to')):if($row->id == Session::get('form_search_assigned_to')):echo 'selected';endif;endif;?>>{{$row->user_full_name}}</option>
                           @endforeach  
                        </select>
            </div>*/ ?>
            <div class="col-sm-12">
              <label>Created By:</label>
              <select class="form-control multipleSelect" id="created_by"   name="created_by">
                        <option value="">{{ $language['select_user'] }}</option>
                           @foreach($user as $row)
                           <option value="{{$row->id}}" <?php if(Session::get('form_search_created_by')):if($row->id == Session::get('form_search_created_by')):echo 'selected';endif;endif;?>>{{$row->user_full_name}}</option>
                           @endforeach  
                        </select>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <div class="col-sm-12">
              <label>Workflow:</label>
              <select name="workflow_id" id="workflow_id" class="form-control multipleSelect">
                           <option value="0">{{ $language['select_workflow'] }}</option>
                           @foreach ($workflows as $wf_row)  
                           <option value="{{$wf_row->workflow_id}}" <?php if(Session::get('form_search_workflow_id')):if($wf_row->workflow_id == Session::get('form_search_workflow_id')):echo 'selected';endif;endif;?>>{{$wf_row->workflow_name}}</option>
                           @endforeach
                        </select>
            </div>
            <div class="col-sm-12">
              <label>Activity:</label>
              <select name="activity_id" id="activity_id" class="form-control multipleSelect">
                            <option value="0">{{$language["select_activity"]}}</option>
                            @foreach($activities as $row)
                            <option value="{{$row->activity_id}}" <?php if(Session::get('form_search_activity_id')):if($row->activity_id == Session::get('form_search_activity_id')):echo 'selected';endif;endif;?>>{{$row->activity_name}}</option>
                            @endforeach  
                        </select>
            </div>
            <div class="col-sm-12">
                <label>Submitted Date - From</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text"  class="form-control active" id="submitted_date_from" name="submitted_date_from" placeholder="YYYY-MM-DD" title="Submitted Date - From" data-toggle="tooltip" data-original-title="" value="{{Session::get('form_search_submitted_date_from')}}">
                </div>
            </div>
            <div class="col-sm-12">
                <label>Submitted Date - To</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text"  class="form-control active" id="submitted_date_to" name="submitted_date_to" placeholder="YYYY-MM-DD" title="Submitted Date - To" data-toggle="tooltip" data-original-title="" value="{{Session::get('form_search_submitted_date_to')}}">
                </div>
            </div>
            <!--Search options-->
            <div class="col-sm-12"> 
                {!! Form::label($language['search_option'].':', '', array('class'=> 'control-label'))!!}
                <select name="search_option" id="search_option" class="multipleSelect form-control">
                    <option value="AND" @if(Session::get('form_search_option') == 'AND') selected @endif >AND</option>
                    <option value="OR" @if(Session::get('form_search_option') == 'OR') selected @endif> OR</option>
                </select>                          
            </div> 
            <div class='instructions' style="color: green;">
                <p><b>*{{$language['OR']}}</b> - {{$language['OR_search_option']}}</p>
                <p><b>*{{$language['AND']}}</b> - {{$language['AND_search_option']}}</p>
            </div> </br>
        </div>
    </div>
    <section class="content">
        <div class="form-group">
            <div class="col-sm-12" style="text-align:right;">
                {!!Form::submit($language['search'], array('class' => 'btn btn-primary', 'id'=> 'search_form')) !!} &nbsp;&nbsp;

                <a href={{URL::route('documentAdvanceSearch')}} class="btn btn-primary" title="{{$language['reset']}}">{{$language['reset']}}</a> &nbsp;&nbsp;

                <a href="{{URL::route('documents')}}" class = "btn btn-primary btn-danger">{{$language['cancel']}}</a>
                
            </div>
        </div><!-- /.col -->
    </section>
    <div class="progress_section_form" style="display: none;margin-top: 10px;">
    <div class="progress active">
        <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="50" aria-valuemin="1" aria-valuemax="100" style="width: 1%" id="status_bar">
          
        </div>
    </div>
    </div>
  </form>
  </div>
</div>
    </div>
    <!-- /.box-body -->
</div>
</div>
</div>
</section>
@else
    <section class="content" style="min-height: 797px;"><div class="alert alert-danger alert-sty">{{$language['dont_hav_permission']}}</div></section>
@endif
<script>

$('#saveEdi').click(function()
{
    var $progressBar = $('.progress-bar');
    var $status = $('#status_bar');
    $(".progress_section_document").css("display", "block");
    setTimeout(function() {
        $progressBar.css('width', '10%');
        $status.text('10%');
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
                        $progressBar.css('width', '95%');
                        $status.text('95%');
                    }, 20000); 
                }, 15000); 
            }, 10000); 
        }, 5000); 
    }, 1000); 
});
$('#search_form').click(function(){
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
                }, 15000); 
            }, 10000); 
        }, 5000); 
    }, 1000); 
});
function openTab(evt, Name) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(Name).style.display = "block";
    evt.currentTarget.className += " active";
}
</script>
<script>

    $( document ).ready(function() {

        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        if($("#doctypeid").val()==null){
            var value = 0;                 
        }else{
            var value = $("#doctypeid").val();
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


        var show_date_text='More <i class="fa fa-angle-down"></i>';
        var hide_date_text='Less <i class="fa fa-angle-up"></i>';
        var date_open = true;
        //Hide and show date serch fileds
        var hide_show_date_search = function ()  
        {  
            
            var created_date_from = $("#created_date_from").val().length;
            var created_date_to = $("#created_date_to").val().length;
            var last_modified_from = $("#last_modified_from").val().length;
            var last_modified_to = $("#last_modified_to").val().length;
            if(created_date_from == 0 && created_date_to == 0 && last_modified_from == 0 && last_modified_to == 0)
            {
                date_open=false;
                $(".collapse_group1").slideUp();
                $(".show_hide_handle").html(show_date_text);
                
            }
            else
            {
                $(".show_hide_handle").html(hide_date_text);
            }          

        } 
        hide_show_date_search();
        $('.show_hide_handle').click(function(e)
        {
            if(date_open == false)
            {
                date_open=true;
                $(".collapse_group1").slideDown();  
                $(".show_hide_handle").html(hide_date_text);   
            }
            else if(date_open == true)
            {
                date_open=false;
                $(".collapse_group1").slideUp();  
                $(".show_hide_handle").html(show_date_text);   
            }            

        });


        // get previously saved criteria
        $('#search_criteria').change(function(){
            var id = $(this).val();
            var page   = '<?php echo Input::get('page');?>';
            if(page){
                var url = base_url+'/documentAdvanceSearch?page=documents&searchCriteriaId='+id+'';
            }else{
                var url = base_url+'/documentAdvanceSearch/edit?searchCriteriaId='+id+'';
            }
            // Reload page with new datas
            window.location.href = url;
        });

        // Delete saved Search
        $('#deleteSavedSearch').click(function(e){
            e.preventDefault();
            var search_criteria = $('#search_criteria').val();
            if(search_criteria){
                // Delete
                swal({
                      title: '{{$language['Swal_are_you_sure']}}',
                      text: "{{$language['Swal_not_revert']}}",
                      type: "{{$language['Swal_warning']}}",
                      showCancelButton: true
                    }).then(function (result) {
                        if(result){
                            $.ajax({
                                type:'GET',
                                url:base_url+'/deleteSavedSearch',
                                data:'search_criteria='+search_criteria,
                                success:function(result){
                                    /*<--success-->*/
                                    swal({
                                          title: "{{$language['success_delete']}}",
                                          type: "{{$language['Swal_warning']}}",
                                          showCancelButton: false
                                        }).then(function (result) {
                                            if(result){
                                                // reload
                                                window.location.href = base_url+'/documentAdvanceSearch?page=documents';
                                            }
                                        });
                                    /*<---->*/
                                }
                            });
                        }
                    });
            }else{
                // Null show message
                swal('Please select any saved search');
            }
        });
    
        // Get document sublist if exists when reload
        var doc_type_ids     = $('#doc_type_ids').val();
        var searchCriteriaId = '<?php echo Input::get('searchCriteriaId');?>';

        if(searchCriteriaId){
            dataValue = 'id='+doc_type_ids+'&criteriaId='+searchCriteriaId;
        }else{
            dataValue = 'id='+doc_type_ids;
        }
        if(doc_type_ids){        
            $.ajax({
                type:'GET',
                url: base_url+'/getDocumentTypeSublis',
                data:dataValue,
                success:function(response){
                   $('#sublist').html(response);
                }
            });
        }
        

        $('.multipleSelect').fastselect();
        var sess    =  <?php echo @$sess_doctypecol ?>
        function myFunction(){
            if ((sess == "")){
                $("#TextBoxesGroup").hide();
            }
        }  

        $("#cn").click(function(){
            $("#documentAdvSearchForm")[0].reset();
            $('#documentAdvSearchForm').parsley().reset();
        });

        // Onchange function to get the sublist of the document type
        $("#tagwrdcat").change(function(){
            // remove selected items
            $('.selecte_tag').attr('selected',false);

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

        // Reset click
        $("#clr").click(function(){ 
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var page   = '<?php echo Input::get('page');?>';
            if(page){
                    var url = base_url+'/documentAdvanceSearch?page=documents';
                }else{
                    var url = base_url+'/documentAdvanceSearch/edit';
                }

            $.ajax({
                type: 'get',
                url: '{{URL('clear')}}',
                data: {_token: CSRF_TOKEN},
                timeout: 50000,
                beforeSend: function() {
                    $("#bs").show(); 
                },
                success: function(data, status){

                    $('.fstControls').val('');
                    $("#TextBoxesGroup").hide();

                    // Reload
                    window.location.href = url;

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
                url: '{{URL('documentsSubListSrch')}}',
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

        // get the html doc type columnname by doctype when reload it for PREVIOUS DATA
        var d           = new Date();
        var currentYear = d.getFullYear();
        var newDate     = currentYear+10;
        var date        = '12/31/'+newDate;
        
        var doctypeid = $('#doctypeid').val();
        if(doctypeid){
            $.ajax({
                type:'GET',
                url : base_url+'/getDocTypeColumn',
                data:'ids='+doctypeid,
                success:function(result){
                    $('#doc-type-column').html(result);

                    $('input[name="daterange[]"]').daterangepicker({
                        singleDatePicker: true,
                        "drops": "up",
                        maxDate: moment(date),
                        showDropdowns: true
                    });
                    
                }
            });
        }
        $('#submitted_date_from').daterangepicker({
            singleDatePicker: true,
            "drops": "up",
            maxDate: moment(date),
            showDropdowns: true
        });
        $('#submitted_date_to').daterangepicker({
            singleDatePicker: true,
            "drops": "up",
            maxDate: moment(date),
            showDropdowns: true
        });
        $('#created_date_from').daterangepicker({
            singleDatePicker: true,
            "drops": "up",
            maxDate: moment(date),
            showDropdowns: true
        });
        $('#created_date_to').daterangepicker({
            singleDatePicker: true,
            "drops": "up",
            maxDate: moment(date),
            showDropdowns: true
        });
        $('#last_modified_from').daterangepicker({
            singleDatePicker: true,
            "drops": "up",
            maxDate: moment(date),
            showDropdowns: true
        });
        $('#last_modified_to').daterangepicker({
            singleDatePicker: true,
            "drops": "up",
             maxDate: moment(date),
            showDropdowns: true
        });

        

    });
    function validateFormSearch() {
        var texta = document.forms["form_search_tab"]["form_name"].value;
        var textb = document.forms["form_search_tab"]["assigned_to"].value;
        var textc = document.forms["form_search_tab"]["workflow_id"].value;
        var textd = document.forms["form_search_tab"]["activity_id"].value;
        var texte = document.forms["form_search_tab"]["submitted_date_from"].value;
        var textf = document.forms["form_search_tab"]["submitted_date_to"].value;
        var textg = document.forms["form_search_tab"]["created_by"].value;
        var textm = document.forms["form_search_tab"]["content_srchtxt"].value;
        if((texta=="") && (textb=="") && (textc=="0") && (textd=="0") && (texte=="") && (textf=="") && (textg=="") && (textm==""))
        {
            swal("Atleast one field must be required...");
            return false;
        }
    }
    function validateForm() {
        var texta = document.forms["documentAdvSearchForm"]["doctypeid"].value;
        var textb = document.forms["documentAdvSearchForm"]["docno"].value;
        var textc = document.forms["documentAdvSearchForm"]["docname"].value;
        var textd = document.forms["documentAdvSearchForm"]["department"].value;
        var texte = document.forms["documentAdvSearchForm"]["ownership"].value;
        var textf = document.forms["documentAdvSearchForm"]["created_date_from"].value;
        var textg = document.forms["documentAdvSearchForm"]["created_date_to"].value;
        var texth = document.forms["documentAdvSearchForm"]["last_modified_from"].value;
        var texti = document.forms["documentAdvSearchForm"]["last_modified_to"].value;
        var textj = document.forms["documentAdvSearchForm"]["stacks"].value;
        var textl = document.forms["documentAdvSearchForm"]["keywords"].value;
        var textm = document.forms["documentAdvSearchForm"]["content_srchtxt"].value;//content search
       
        if((texta=="") && (textb=="") && (textc=="") && (textd=="") && (texte=="") && (textf=="") && (textg=="") && (texth=="") && (texti=="") && (textj=="") && (textl=="") && (textm=="")){
            swal("Atleast one field must be required...");
            return false;
        }
    }  

    //stack columns code begins

    $("#stacks").on('change',function() {
        var stckid = $("#stacks").val();
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            type: 'post',
            url: '{{URL('loadStackonAdvanceSearch')}}',
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

    //link to content search
    $('#link_to_contentsearch').click(function(){
        var url_to_content = base_url+'/contentSearch';
        window.location.href = url_to_content;
    });

     $('#link_to_missingdocs').click(function(){
        var url_to_content = base_url+'/documentDuplicates';
        window.location.href = url_to_content;
    });

      $('#link_to_missingfiles').click(function(){
        var url_to_content = base_url+'/fileDuplicates';
        window.location.href = url_to_content;
    });

      $('#link_to_missingdocs_list').click(function(){
        var url_to_content = base_url+'/listDuplicates';
        window.location.href = url_to_content;
    });
</script>


@endsection

