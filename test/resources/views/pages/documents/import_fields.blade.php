<?php
include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')
@section('main_content')

{!! Html::script('js/validation/jquery.validate.min.js') !!}
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
<style type="text/css">
.error{
    color: red;
}
</style>
<section class="content-header">
        <div class="col-sm-8">
            <strong>
                {{$language['import data']}} @if(Session::has('doctype_name')) - {{ Session::get('doctype_name') }} @endif</br>             
            </strong>
        </div>
        <div class="col-sm-4">
            <!-- <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> {{$language['home']}}</a></li>
                <li class="active">{{$language['import data']}}</li>
            </ol>  --> 
        </div> 
        <div class="col-sm-12">
            <p style="font-size:12px; color:#999;">- {{ $language['parse_msg']}}</p>       
        </div>  
    </section>
    @if(Session::has('warn'))
        <section class="content content-sty" id="spl-err-wrn">
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>        
            <div class="alert alert-sty {{ Session::get('alert-class', 'alert-success') }} ">{{ Session::get('warn') }}</div> 
        </div>       
        </section>
    @endif
    
<section class="content">  
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">CSV Import</div>
                <div class="panel-body">
                    <form class="form-horizontal" method="post" action="{{ URL::to('importExcel') }}" id="doc_import_form">
                        <div style="overflow-x: scroll;">
                            {{ csrf_field() }}
                            <div class="col-sm-12">
                                <table class="table">
                                    <?php 
                                    foreach ($csv_data as $row){ ?>
                                        <tr>
                                        <?php 
                                        foreach ($row as $key => $value){ ?>
                                            <td>{{ $value }}</td>
                                        <?php } ?>                                        
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <?php
                                        $i=1;                                        
                                        foreach ($csv_data[0] as $key => $value){ ?>
                                            <td nowrap="">
                                                <select name="header<?php echo $i; ?>" required="">
                                                    <option value="">Select Fields</option>
                                                    <option value="document_file_name">{{ $language['file Name']}}</option>
                                                    <?php foreach ($csv_header_fields as $val){ 
                                                        foreach($val as $v){ ?>
                                                        <option value="<?php echo $v; ?>"><?php echo $v; ?></option>
                                                    <?php } } ?>
                                                    <option value="dept_name"><?php if(session::get('settings_department_name')){ echo session::get('settings_department_name'); }else{ $language['department']; } ?></option>
                                                    <option value="stack_name">{{ $language['stack']}}</option>
                                                </select>                                                
                                            </td>
                                        <?php 
                                        $i++;
                                        } ?>                                        
                                    </tr>  
                                    <input type="hidden" name="headercnt" value="<?php echo $i-1; ?>">
                                </table>
                            </div>
                        </div><br/>
                         <button type="submit" id="importForm" class="btn btn-primary" style="float:right;">
                            Import Data
                        </button>
                        <a href="{{ route('importFile') }}" class="btn btn-primary" style="float: right; margin-right: 10px;">{{$language['back']}}</a>
                    </form>
                </div>
            </div>
            <div class="form-group" style="height: 20px;">
                <label class="col-sm-4"></label>
                <div class="col-sm-6">
                    <div class="preloader" style="display: none;" >
                        <i class="fa fa-spinner fa-pulse fa-fw fa-lg"></i>{{trans("apps.preloader_msg")}}
                        <span class="sr-only">{{trans("apps.preloader_msg")}}</span>
                    </div>    
                </div>
            </div>
        </div>   
    </div>
</section>
<script type="text/javascript">
$( document ).ready(function() 
    {
      $(".preloader").css("display", "none");
    });
$('#importForm').click(function()
{
    var validator = $("#doc_import_form").validate();
    if (validator.form()) 
    {
        $(".preloader").css("display", "block");
    }
});
</script>
@endsection