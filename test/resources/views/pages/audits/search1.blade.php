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
{!! Html::script('plugins/daterangepicker/daterangepicker.js') !!}

<!-- Content Wrapper. Contains page content -->
<style type="text/css">
    .fstElement{
        width: 100%;
    }

    @media(max-width:991px){
    .modal-body {
        position: relative;
        top: 31px;
        }
    }

</style>

    <section class="content-header">
        <h1>
            {{$language['audits']}}
            <small>- Search</small>
        </h1>
        {{$language['input_search']}}
        <!-- <ol class="breadcrumb">
                <li><a href="<?php echo  url('/home');?>"><i class="fa fa-dashboard"></i> {{$language['home']}}</a></li>
                <li class="active">{{$language['audits']}}</li>
            </ol>  -->           
    </section>

    <!--checking view permission-->
    @if(Auth::user()->user_role == '1')   
    <div class="modal-body"> 
        <!-- form start -->
        {!! Form::open(array('url'=> array('auditsAdvSearch'), 'files'=>true, 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'documentSrch', 'id'=> 'documentSrch','data-parsley-validate'=> '','onsubmit' => 'return validateForm()')) !!}            
        <div class="form-group">
            <div class="col-sm-6"> 
                {!! Form::label($language['sign in name'].':','', array('class'=> 'control-label'))!!} 

                <select name="username[]" id="username" class="multipleSelect form-control" multiple>
                    <?php foreach ($username as $key => $val) { ?>
                        <option value="<?php echo $val->audit_user_name; ?>" <?php if(session::get('username')):if(in_array($val->audit_user_name,explode(',',session::get('username')))): echo 'selected';endif;endif;?> ><?php echo $val->audit_user_name;?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-sm-6"> 
                {!! Form::label($language['item'].':', '', array('class'=> 'control-label'))!!}
                <select name="category[]" id="category" class="multipleSelect form-control" multiple>
                    <?php foreach ($category as $key => $val) { ?>
                        <option value="<?php echo $val->audit_owner; ?>" <?php if(Session::get('items')):if(in_array($val->audit_owner,explode(',',Session::get('items')))):echo 'selected';endif;endif;?> ><?php echo $val->audit_owner;?></option>
                    <?php } ?>
                </select>
            </div>       
        </div>
        
        <div class="form-group">
            <div class="col-sm-6">
                {!! Form::label($language['document no'].':','', array('class'=> 'control-label'))!!}
                <input type="text" class="form-control" name="docno" id="docno" value="{{Session::get('docno')}}" />
            </div>
            <div class="col-sm-6">
                {!! Form::label($language['document name'].':', '', array('class'=> 'control-label'))!!}
                <input type="text" class="form-control" name="docname" id="docname" value="{{Session::get('docname')}}" />
            </div>       
        </div>
        
        <div class="form-group">
            <div class="col-sm-6">  
                {!! Form::label($language['stacks'].':','', array('class'=> 'control-label'))!!}
                <select name="stacks[]" id="stacks" class="multipleSelect form-control" multiple>
                    <?php foreach ($stacks as $key => $val) { ?>
                        <option value="<?php echo $val->stack_id; ?>" @if(Session::get('stacks')) @if(in_array($val->stack_id,explode(',',Session::get('stacks')))) selected @endif @endif ><?php echo @$val->stack_name;?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-sm-6"> 
                {!! Form::label($language['department'].':', '', array('class'=> 'control-label'))!!}
                <select name="dept[]" id="dept" class="multipleSelect form-control" multiple>
                    <?php foreach ($depts as $key => $val) { ?>
                        <option value="<?php echo $val->department_id; ?>"  @if(Session::get('dept')) @if(in_array($val->department_id,explode(',',Session::get('dept')))) selected @endif @endif ><?php echo @$val->department_name;?></option>
                    <?php } ?>
                </select>
            </div>       
        </div>
        
        <div class="form-group">
        	<div class="col-sm-6"> 
                {!! Form::label($language['document type'].':', '', array('class'=> 'control-label'))!!}
                <select name="dctype[]" id="dctype" class="multipleSelect form-control" multiple>
                    <?php foreach ($doctypes as $key => $val) { ?>
                        <option value="<?php echo $val->document_type_id; ?>" @if(Session::get('document_type_ids')) @if(in_array($val->document_type_id,explode(',',Session::get('document_type_ids')))) selected @endif @endif ><?php echo @$val->document_type_name;?></option>
                    <?php } ?>
                </select>
            </div>    
            
            <div class="col-sm-6"> <?php echo Session::get('actions'); ?>
                {!! Form::label($language['action'].':','', array('class'=> 'control-label'))!!}
                <select name="actions[]" id="actions" class="multipleSelect form-control" multiple>
                    <?php foreach ($action as $key => $val) { ?>
                        <option value="<?php echo $val->audit_action_type; ?>" @if(Session::get('actions')) @if(in_array($val->audit_action_type,explode(',',Session::get('actions')))) selected @endif @endif ><?php echo $val->audit_action_type;?></option>
                    <?php } ?>
                </select>
            </div>
           
        </div>

       
       
        <div class="form-group">
            <div class="col-sm-6">
                {!! Form::label($language['date_from'].':', '', array('class'=> 'control-label'))!!}
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" value="{{Session::get('date_from')}}" class="form-control active" id="createddate_from" name="createddate_from" placeholder="YYYY-MM-DD" title="Created Date- From" data-toggle="tooltip" data-original-title="">
                </div>
            </div>
            <div class="col-sm-6">
                {!! Form::label($language['date_to'].':', '', array('class'=> 'control-label'))!!}
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" value="{{Session::get('date_to')}}" class="form-control active" id="createddate_to" name="createddate_to" placeholder="YYYY-MM-DD" title="Created Date - To" data-toggle="tooltip" data-original-title="">
                </div>
            </div>
        </div>

        <div class="form-group" style="margin-top:30px;margin-bottom:185px;">
            <div class="col-sm-12">
                <div class="text-center">
                    {!!Form::submit($language['search'], array('class' => 'btn btn-primary', 'id'=> 'saveEdi')) !!}&nbsp;&nbsp;
                    <a href="{{URL::route('auditsList')}}" class = "btn btn-primary">{{$language['show all']}}</a>&nbsp;&nbsp;
                </div>
            </div>
        </div><!-- /.col -->
        {!! Form::close() !!}        
    </div>
    @else
        <section class="content" style="min-height: 797px;"><div class="alert alert-danger alert-sty">{{$language['dont_hav_permission']}}</div></section>
    @endif

    

<script>
    $(document).ready(function() {
        var d           = new Date();
        var currentYear = d.getFullYear();
        var newDate     = currentYear+10;
        var date        = '12/31/'+newDate;
       
        $('.multipleSelect').fastselect();
        $('#createddate_from').daterangepicker({
            singleDatePicker: true,
            "drops": "up",
            maxDate: moment(date),
            showDropdowns: true
        });
        $('#createddate_to').daterangepicker({
            singleDatePicker: true,
            "drops": "up",
            maxDate: moment(date),
            showDropdowns: true
        });
    });

    function validateForm() {
        var texta = document.forms["documentSrch"]["username"].value;
        var textb = document.forms["documentSrch"]["category"].value;
        var textc = document.forms["documentSrch"]["actions"].value;
        var textd = document.forms["documentSrch"]["createddate_from"].value;
        var texte = document.forms["documentSrch"]["createddate_to"].value;
		
		var textf = document.forms["documentSrch"]["docno"].value;
		var textg = document.forms["documentSrch"]["docname"].value;		
		var texth = document.forms["documentSrch"]["stacks"].value;
		var texti = document.forms["documentSrch"]["dept"].value;
		var textj = document.forms["documentSrch"]["dctype"].value;
		
        if((texta=="") && (textb=="") && (textc=="") && (textd=="") && (texte=="") && (textf=="") && (textg=="") && (texth=="") && (texti=="") && (textj=="")){
            swal("{{$language['no entry msg']}}");
            return false;
        }
    }    

</script>
<style type="text/css">
    .form-group {
        margin-bottom: 0px;
    }
    .fstControls{
        width: 100% !important;
    }
</style>
@endsection

