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

<style type="text/css">
    @media(max-width:335px){
        .breadcrumb{
            display: none;
        }
    }
</style>

<!-- Content Wrapper. Contains page content -->
<section class="content" id="shw">
    <section class="content-header">
        <strong>
            @if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{trans('language.departments')}} @endif
            <small>- {{trans('language.edit')}} @if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{trans('language.department')}} @endif: {{ $datas->department_name }}</small>
        </strong>
        </h1>
       <!--  <ol class="breadcrumb">
                <li><a href="<?php echo  url('/home');?>"><i class="fa fa-dashboard"></i> {{trans('language.home')}}</a></li>
                <li class="active">@if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{trans('language.departments')}} @endif</li>
            </ol>  -->           
    </section>

    <!-- Main content -->
    <section class="content">    
    <div class="modal-body">    
        <!-- form start -->
        {!! Form::open(array('url'=> array('departmentSave',$datas->department_id), 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'departmentAddForm', 'id'=> 'departmentAddForm','data-parsley-validate'=> '')) !!}        

        <div class="form-group">
            <label for="Department Name" class="col-sm-2 control-label">@if(Session::get('settings_dept_name')) {{ Session::get('settings_dept_name') }}@else {{trans('language.department name')}} @endif: <span class="compulsary">*</span></label>
            <div class="col-sm-5">
                <input type="text" class="form-control" id="name_edi" required="" data-parsley-required-message='@if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{trans('language.department')}} @endif {{trans('language.is_required')}}' data-parsley-trigger='change focusout' data-parsley-maxlength="{{ trans('language.max_length') }}" autofocus="autofocus" placeholder="@if(Session::get('settings_dept_name')){{ Session::get('settings_dept_name')}}@else{{trans('language.department name')}}@endif" title="@if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }} {{trans('language.length_others')}}@else {{trans('language.department')}} {{trans('language.length_others')}}@endif" name="name" onchange="duplication();" value="{{$datas->department_name}}">    
                <div id="dp_edi">
                <span id="dp_wrn_edi" style="display:none;">
                        <i class="fa fa-refresh fa-spin fa-1x fa-fw"></i>
                        <span class="">Please wait...</span>
                    </span>
                </div>              
            </div>
        </div>
        <div class="form-group">
        {!! Form::label(trans('language.description').': ', '', array('class'=> 'col-sm-2 control-label'))!!}
        <div class="col-sm-5">
            {!! Form:: 
            textarea(
            'description', $datas->department_description, 
            array( 
            'class'                  => 'form-control', 
            'id'                     => 'Description', 
            'title'                  => 'Document Type Description', 
            'placeholder'            => 'Document Type Description',
            'title'                  => trans('language.length_description'),
            'data-parsley-maxlength' => trans('language.max_length_description')
            )
            ) 
            !!}
            <span class="dms_error">{{$errors->first('description')}}</span>
        </div>
        </div>

        <input type="hidden" id="edit_val" name="edit_val" value="{{$datas->department_id}}">
        <input type="hidden" id="oldVal" name="oldVal" value="{{$datas->department_name}}">

        <div class="form-group">
        <label class="col-sm-2 control-label"></label>
        <div class="col-sm-5" style="text-align:right;">
            {!!Form::submit(trans('language.save'), array('class' => 'btn btn-primary', 'id'=> 'saveEdi')) !!} 
            <a href="{{URL::route('departments')}}" class = "btn btn-primary btn-danger">{{trans('language.cancel')}}</a>
        </div>
        </div>

       
        {!! Form::close() !!}  
    </section>
</section><!-- /.content -->  

<script type="text/javascript">
    function duplication(){
        var val= $("#name_edi").val();
        var editVal= $("#edit_val").val();
        var oldVal= $("#oldVal").val();
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            type: 'post',
            url: '{{URL('departmentDuplication')}}',
            dataType: 'json',
            data: {_token: CSRF_TOKEN, name: val, editId:editVal, oldVal:oldVal },
            timeout: 50000,
            beforeSend: function() {
                $("#dp_wrn_edi").show();
                //$("#saveEdi").attr("disabled", true);
            },
            success: function(data, status){

                if(data != 1)
                {
                    $("#dp_edi").html(data);
                    $("#saveEdi").attr("disabled", true);
                }
                else
                {
                    $("#dp").text('');
                    $("#dp-inner").text('');
                    $("#saveEdi").attr("disabled", false);                     
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                console.log(jqXHR);    
                console.log(textStatus);    
                console.log(errorThrown);    
            },
            complete: function() {
                $("#dp_wrn_edi").hide();
                //$("#saveEdi").attr("disabled", false);
            }
        });
    }
// select all desired input fields and attach tooltips to them
      $("#departmentAddForm :input").tooltip({
 
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

@endsection

