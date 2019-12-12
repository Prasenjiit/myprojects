<?php
include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')
@section('main_content')
{!! Html::script('js/jquery.form.js') !!}   
{!! Html::style('css/font-awesome.min.css') !!} 
{!! Html::style('css/ionicons.min.css') !!}
{!! Html::script('js/validation/jquery.validate.min.js') !!}
<style type="text/css">
.error{
    color: red;
}
</style>
<section class="content-header">
    <div class="col-sm-8">
        <strong>
            {{$language['import data']}} @if(Session::has('doctype_name')) - {{ Session::get('import_app_name') }} {{ Session::get('import_app_id') }}@endif</br>             
        </strong>
    </div>
    <div class="col-sm-4">
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> {{$language['home']}}</a></li>
            <li class="active">{{$language['import data']}}</li>
        </ol>  
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
                <div class="panel-heading">{{Lang::get('apps.csv_import')}}</div>
                <div class="panel-body">
                    <form class="form-horizontal" method="post" action="{{ URL::to('app_importExcel') }}" id="app_import_form">
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
                                            <td nowrap>
                                                <select name="header<?php echo $i; ?>" required="" >
                                                    <option value="">Select Fields</option>
                                                    
                                                    <?php foreach ($csv_header_fields as $val){ 
                                                        foreach($val as $v){ ?>
                                                        <option value="<?php echo $v; ?>"><?php echo $v; ?></option>
                                                    <?php } } ?>
                                                </select>                                                
                                            </td>
                                        <?php 
                                        $i++;
                                        } ?>                                        
                                    </tr>
                                </table> 
                            </div>    
                                    <div class="col-md-12">
                                        <p style="font-weight: 500;">{{Lang::get('apps.app_help_text')}}</p>
                                    </div>
                                    <div>
                                      <label for="inputEmail3" class="col-sm-3 control-label"><b>{{Lang::get('apps.unique_identifier')}} :</b></label>

                                      <div class="col-sm-4">
                                        <select name="unique_field" id="unique_field" class="form-control">
                                            <option value="">Select Fields</option>
                                            
                                            <?php foreach ($csv_header_fields as $val){ 
                                                foreach($val as $v){ ?>
                                                <option value="<?php echo $v; ?>"><?php echo $v; ?></option>
                                            <?php } } ?>
                                        </select>
                                      </div>
                                      <div class="col-sm-5"></div>
                                    </div>
                                    
                                    
                                    <div class="col-sm-12">
                                        <p style="font-size:12px; color:#999;">{{Lang::get('apps.example_import_text')}} 
                                        </p>
                                    </div>
                                
                                    <input type="hidden" name="headercnt" value="<?php echo $i-1; ?>">
                                
                        </div><br/>
                        <button type="submit" id="importForm" class="btn btn-primary" style="float:right;">
                            {{Lang::get('apps.import_data')}}
                        </button>
                        <a href="{{ route('importRecords') }}" class="btn btn-primary" style="float: right; margin-right: 10px;">{{$language['back']}}</a>
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
    var validator = $("#app_import_form").validate();
    if (validator.form()) 
    {
        $(".preloader").css("display", "block");
    }
});
</script>
@endsection