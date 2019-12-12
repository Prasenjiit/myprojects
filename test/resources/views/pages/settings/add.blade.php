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
        height: 20px;
    }
}

/*<--mobile view table-->*/
@media(max-width:500px){
    .box{
        overflow-x: auto;
    }
}

/*<--smtp and ftp form-->*/
.input-right{
    padding-left: 0px;
}
.input-left{
    padding-right: 0px;
}
.enable-smtp{
    padding-left: 0px;
}
.toptech-info{
    font-size:12px; 
    color:#999;
}

/*<--For mobile view SMTP-->*/
@media(max-width:767px){
   .input-right{
    padding-left: 15px !important;
    }
    .input-left{
        padding-right: 15px !important;
    } 
    .enable-smtp{
        padding-left: 15px !important;
    }
}

</style>

{!! Html::style('plugins/ionslider/ion.rangeSlider.css') !!}
{!! Html::style('plugins/ionslider/ion.rangeSlider.skinNice.css') !!}
{!! Html::style('plugins/bootstrap-slider/slider.css') !!}

{!! Html::script('plugins/daterangepicker/daterangepicker.js') !!}
<!-- bootstrap slider -->
{!! Html::script('plugins/ionslider/ion.rangeSlider.min.js') !!}
{!! Html::script('plugins/bootstrap-slider/bootstrap-slider.js') !!}

 <?php    $user_permission=Auth::user()->user_permission;
?>
    
<section class="content-header">
    <div class="col-sm-8">
        <span style="float:left;">
            <strong>{{trans('language.settings')}}</strong> &nbsp;
        </span>
    </div>
    <div class="col-sm-4">
        <!-- <ol class="breadcrumb">
            <li><a href="{{url('/home')}}"><i class="fa fa-dashboard"></i> {{trans('language.home')}}</a></li>
            <li class="active">{{trans('language.settings')}}</li>
        </ol> -->
    </div>
</section>
<!--Success flash message-->
@if (session('status'))
<section class="content content-sty" id="spl-wrn"> 
    <div class="alert alert-success" id="hide-div">
        <p style="text-align: center;"><strong>Success!</strong> {{ session('status') }}</p>
    </div>
</section>
@endif

<!--Checking view permission-->
@if(Auth::user()->user_role == Session::get('user_role_super_admin'))   
<!-- Main content -->
    <section class="content">
        <div class="row">
            {!! Form::open(array('url'=> array('settingsSave'), 'files'=>true, 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'globalSettingsAddForm', 'id'=> 'globalSettingsAddForm','data-parsley-validate'=> '')) !!} 
            <div class="col-md-6">
                <!-- company form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{trans('settings.company_settings')}}</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" title="Collapse"><i class="fa fa-minus"></i>
                            </button>
                        </div>

                    </div><!-- /.box-header -->  
                    <!--SA and GA-->
                    <div class="box-footer" style="">
                        <div class="modal-body">    
                            <!-- form start -->
                            
                            <div class="form-group">
                                <label for="Company Name" class="col-sm-12 control-label">{{trans('settings.company name')}}: <span class="compulsary">*</span></label>
                                <div class="col-sm-12">
                                    {!! Form::text('settings_company_name',@$records->settings_company_name,array('class' => 'form-control global_s_msg','id'=> 'comp_name','required'=>'','data-parsley-required-message' => 'Company Name is required','placeholder'=>trans('settings.company name'),'title'=>"'".trans('settings.company name')."'".trans('language.length_others'),'data-parsley-maxlength' => trans('language.max_length'))) !!}   
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="Address" class="col-sm-12 control-label">{{trans('language.address')}}: <span class="compulsary">*</span></label>
                                <div class="col-sm-12">
                                    {!! Form::textarea('settings_address',@$records->settings_address,array('class'=> 'form-control','placeholder'=>trans('language.address'),'required'=>'','data-parsley-required-message' => 'Address is required','title'=>trans('language.length_address'),'data-parsley-maxlength' => trans('language.max_length_description'))) !!}   
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="Email" class="col-sm-12 control-label">{{trans('language.email_id')}}: <span class="compulsary">*</span></label>
                                <div class="col-sm-12">
                                    {!! Form::email('settings_email',@$records->settings_email,array('class'=> 'form-control global_s_msg','id'=>'company-email','required'=>'','data-parsley-required-message' => 'Email is required','placeholder'=>'email@gmail.com','title'=>trans('language.mail_tooltip'))) !!}   
                                </div>
                            </div>
                            <input type="hidden" name="settings_document_no" id="settings_document_no" value="Document Number">
                            <input type="hidden" name="settings_document_name" id="settings_document_name" value="Document Name">
                                                               
                            <div class="form-group">
                                <label for="Logo" class="col-sm-12 control-label">{{trans('settings.logo')}}: <span class="compulsary">*</span></label>
                                <div class="col-sm-12">
                                @if(@$records->settings_logo)
                                    {!! Form::file('settings_logo',array('id'=>'settings_logo')) !!}
                                @else
                                    {!! Form::file('settings_logo',array( 'required'=> '','data-parsley-required-message' => 'Logo file is required','id'=>'settings_logo')) !!}
                                @endif
                                    <span class="dms_error">{{$errors->first('docdesc')}}</span>              
                                </div>
                                
                            </div>

                            <div class="form-group">
                                <div class="col-sm-12 control-label"></div>
                                <div class="col-sm-12">
                                <p><strong style="color:#f39c12">Warning!</strong> Logo size should less than 301 X 121 px</p>
                                </div>
                            </div>

                            <div class="form-group">
                            {!! Form::label('', '', array('class'=> 'col-sm-12 control-label'))!!}
                                @if(@$records->settings_logo)
                                <div class="col-sm-12">
                                    <img src="images/logo/<?php echo $records->settings_logo;?>" alt="Logo"> 
                                </div>
                                @endif
                            </div>


                        </div>
                    </div>
                </div>

                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ trans('settings.general_settings') }}</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" title="Collapse"><i class="fa fa-minus"></i>
                            </button>
                        </div>

                    </div><!-- /.box-header -->  
                    <!--SA and GA-->
                    <div class="box-footer" style="">
                        <div class="modal-body">    
                            <!-- form start -->
                            <div class="form-group">
                                <label for="{{ trans('settings.document_expiry') }}" class="col-sm-12 control-label">{{trans('settings.document_expiry') }}: </label>
                                <p style="padding-left:15px; font-size:12px; color:#999;">{{ trans('settings.document_expiry_desc')}}</p>
                                <div class="row margin">
                                   <div class="col-sm-12">
                                          <input id="settings_document_expiry" type="text" name="settings_document_expiry" value="">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-12 control-label">{{ trans('settings.rows_per_page') }}: </label>
                                <p style="padding-left:15px; font-size:12px; color:#999;">{{ trans('settings.rows_per_desc') }}</p>
                                <div class="row margin">
                                   <div class="col-sm-12">
                                          <input id="range_3" type="text" name="range_3" value="">
                        
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="{{trans('language.timezone')}}" class="col-sm-12 control-label">{{trans('language.timezone')}}: <span class="compulsary">*</span></label>
                                <div class="col-sm-12">
                                    <select class="form-control" name="settings_timezone" required='' data-parsley-required-message="{{trans('language.timezone')}} is required">
                                        <option value="">Select {{trans('language.timezone')}}</option>
                                        @foreach(DateTimeZone::listIdentifiers(DateTimeZone::ALL) as $val)
                                            <option value="{{$val}}" @if($records->settings_timezone == $val) selected @endif >{{$val}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="{{trans('language.timeformat')}}" class="col-sm-12 control-label">{{trans('language.timeformat')}}:  <span class="compulsary">*</span></label>
                                <p style="padding-left:15px; font-size:12px; margin-bottom:0px!important; color:#999; display: none;">What the notation means: d = day; y = year; m = month; H = hour; i = minute; s = second; A = am/pm</p>
                                <div class="col-sm-12">
                                    <select class="form-control" name="settings_datetimeformat" required='' data-parsley-required-message="{{trans('language.timeformat')}} is required">
                                        <option value="d-m-Y h:i A" <?php if($records->settings_datetimeformat=='d-m-Y h:i A') { echo 'selected'; } ?>><?php echo date('d-m-Y h:i A'); ?></option>
                                        <option value="d-m-Y" <?php if($records->settings_datetimeformat=='d-m-Y') { echo 'selected'; } ?>><?php echo date('d-m-Y'); ?></option>
                                        <option value="Y-m-d h:i A" <?php if($records->settings_datetimeformat=='Y-m-d h:i A') { echo 'selected'; } ?>><?php echo date('Y-m-d h:i A'); ?></option>
                                        <option value="Y-m-d" <?php if($records->settings_datetimeformat=='Y-m-d') { echo 'selected'; } ?>><?php echo date('Y-m-d'); ?></option>
                                        <option value="m-d-Y h:i A" <?php if($records->settings_datetimeformat=='m-d-Y h:i A') { echo 'selected'; } ?>><?php echo date('m-d-Y h:i A'); ?></option>
                                        <option value="m-d-Y" <?php if($records->settings_datetimeformat=='m-d-Y') { echo 'selected'; } ?>><?php echo date('m-d-Y'); ?></option>
                                        
                                        
                                    </select>

                                </div>
                                <p style="padding-left:15px; font-size:12px; color:#999;">{{ trans('settings.date_indx_colmn')}}</p>
                            </div>
                            <div class="form-group">
                                <label for="department" class="col-sm-12 control-label">{{trans('language.department_name')}}: </label>
                                <div class="col-sm-12">
                                    <input type="text" name="settings_deptname" class="form-control global_s_msg" title="{{ trans('language.department_name') }}" value="{{ @$records->settings_department_name }}">                                     
                                </div>
                            </div>


                        </div>
                    </div>
                </div>

                
                <!--SMTP settings-->
                <div class="box box-primary">

                    <div class="box-header with-border">
                        <h3 class="box-title">{{Lang::get('settings.smtp_settings')}}</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" title="Collapse"><i class="fa fa-minus"></i>
                            </button>
                        </div>
                        
                    </div><!-- /.box-header -->  

                    <!-- /.box-body -->
                    <div class="box-footer" style="">
                        <div class="nav-tabs-custom"> 
                            <!--For set active class if smtp table is empty(truncated)-->
                            @if(($otherData == null) && ($exchangeData == null) && ($yahooData == null) && ($gmailData == null) && ($outlookData == null) )
                                <?php $class = 'no'; ?>
                            @else
                                <?php $class = 'yes'; ?>
                            @endif
                            
                            <!-- Tabs within a box -->
                            <ul class="nav nav-tabs pull-right ui-sortable-handle">
                              <li <?php if(@$otherData[0]->smtp_details_active_account == 'active' || @$class == 'no'):?> class="active" <?php endif;?>><a href="#other" data-toggle="tab" class="smtp-btn">{{Lang::get('language.other')}}</a></li>
                              <li <?php if(@$exchangeData[0]->smtp_details_active_account == 'active'):?> class="active" <?php endif;?>><a href="#ms" data-toggle="tab" class="smtp-btn">{{Lang::get('settings.microsoft_exchange')}}</a></li>
                              <li <?php if(@$yahooData[0]->smtp_details_active_account == 'active'):?> class="active" <?php endif;?>><a href="#yahoo" data-toggle="tab" class="smtp-btn">{{Lang::get('settings.yahoo_mail')}}</a></li>
                              <li <?php if(@$gmailData[0]->smtp_details_active_account == 'active'):?> class="active" <?php endif;?>><a href="#gmail" data-toggle="tab" class="smtp-btn">{{Lang::get('settings.gmail')}}</a></li>
                              <li <?php if(@$outlookData[0]->smtp_details_active_account == 'active'):?> class="active" <?php endif;?>><a href="#outlook" data-toggle="tab" class="smtp-btn">{{Lang::get('settings.outlook.com')}}</a></li>
                              
                            </ul>
                            <!--Define email provider-->
                            <div class="tab-content no-padding">
                                <!-- Other -->
                                <div class="chart tab-pane <?php if(@$otherData[0]->smtp_details_active_account == 'active' || @$class == 'no'):?> active <?php endif;?>" id="other">
                                    <div class="modal-body">
                                        <span class="toptech-info">{{Lang::get('settings.mail_config_message')}}</span>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-6 input-left">
                                                    <label class="col-sm-12 control-label">{{Lang::get('language.from_name')}}: </label>
                                                    <div class="col-sm-12">
                                                        {!! Form::text('smtp_details_other_fromname',@$otherData[0]->smtp_details_fromname,array('class' => 'form-control','id'=>'other-fromname','placeholder'=>Lang::get('language.from_name') )) !!}   
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 input-right">
                                                    <label class="col-sm-12 control-label">{{Lang::get('language.from_add')}}: </label>
                                                    <div class="col-sm-12"> 
                                                        {!! Form::text('smtp_details_other_fromaddress',@$otherData[0]->smtp_details_fromaddress,array('class' => 'form-control','id'=>'other-fromaddress','placeholder'=>Lang::get('language.from_add') )) !!}  
                                                    </div>
                                                </div>  
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-6 input-left">
                                                    <label class="col-sm-12 control-label">{{Lang::get('settings.smtp_mail_server')}}: </label>
                                                    <div class="col-sm-12"> 
                                                        {!! Form::text('smtp_details_other_mailserver',@$otherData[0]->smtp_details_mailserver,array('class' => 'form-control','id'=>'other-mailserver','placeholder'=>Lang::get('settings.smtp_mail_server') )) !!}   
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 input-right">
                                                    <label class="col-sm-12 control-label">{{Lang::get('settings.smtp_port')}}: </label>
                                                    <div class="col-sm-12">
                                                        {!! Form::text('smtp_details_other_port',@$otherData[0]->smtp_details_port,array('class' => 'form-control','id'=>'other-port','placeholder'=>Lang::get('settings.smtp_port') )) !!}   
                                                    </div>
                                                </div>
                                            </div>
                                        </div>  

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-6 input-left">
                                                    
                                                    <div class="col-sm-12"> 
                                                        <input type="checkbox" id="smtp-other-authentication" <?php if(@$otherData[0]->smtp_details_user_authentication == 'on'):echo "checked";endif;?> name="smtp_details_other_user_authentication" class="minimal">  {{Lang::get('settings.smtp_auth')}}
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 input-right">
                                                    <label class="col-sm-7 ">{{Lang::get('settings.enable_smtp')}}: </label>
                                                    <div class="col-sm-5 enable-smtp">
                                                        <select name="smtp_details_other_tls_ssl" id="other_tls_ssl" class="form-control select2 select2-hidden-accessible" style="width: 100%;" tabindex="-1" aria-hidden="true">
                                                          <option value="none" @if(@$otherData[0]->smtp_details_tls_ssl == 'none') selected="selected" @endif>None</option>
                                                          <option @if(@$otherData[0]->smtp_details_tls_ssl == 'ssl') selected="selected" @endif value="ssl">SSL</option>
                                                          <option @if(@$otherData[0]->smtp_details_tls_ssl == 'tls') selected="selected" @endif value="tls">TLS</option>
                                                        </select>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group" id="auth-user" @if(@$otherData[0]->smtp_details_user_authentication != 'on')style="display:none" @endif>
                                            <div class="row">
                                                <div class="col-sm-6 input-left">
                                                    <label class="col-sm-12 control-label">{{Lang::get('settings.smtp_ftp_username')}}: </label>
                                                    <div class="col-sm-12">
                                                        <input class="form-control" id="otheruser-required"  placeholder="{{Lang::get('settings.smtp_ftp_username')}}" name="smtp_details_other_username" value="{{@$otherData[0]->smtp_details_username}}" type="text">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 input-right">
                                                    <label class="col-sm-12 control-label">{{Lang::get('settings.smtp_ftp_psw')}}: </label>
                                                    <div class="col-sm-12">  
                                                        <input type="password" placeholder="*******" id="otherpsw-required" name="smtp_details_other_password" class="form-control" autocomplete="new-password"> 
                                                        <!--Hidden-->
                                                        <input type="hidden" id="otherpsw-saved-psw" value="{{@$otherData[0]->smtp_details_password}}">
                                                    </div>
                                                </div>  
                                            </div>
                                        </div> 

                                        <div class="form-group">
                                            <div class="col-sm-12"> 
                                                {{Lang::get('language.allow_users')}}: 
                                                <input type="radio" name="smtp_details_active_account" value="other" <?php if(@$otherData[0]->smtp_details_active_account == 'active' || @$otherData[0]->smtp_details_active_account == ''):echo "checked";endif;?> />
                                            </div>  
                                        </div> 

                                        <!--send test mail-->
                                        <div class="form-group">
                                            <div class="col-sm-8">
                                                <span id="other-loading" class="loading"></span><!--Ajax response-->
                                            </div>    
                                            <div class="col-sm-4"> 
                                                <button type="button" serviceProvider='other' name="test_mail" class="btn btn-primary btn-xs pull-right send-test-email"><i class="fa fa-fw fa-check-square-o"></i> {{Lang::get('settings.send_test_email')}}
                                                </button>
                                            </div>
                                        </div>
                                        <!--//send test mail-->
                                    </div>
                                </div>  
                                <!-- MS -->
                                <div class="chart tab-pane <?php if(@$exchangeData[0]->smtp_details_active_account == 'active'):?> active <?php endif;?>" id="ms">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-6 input-left">
                                                    <label class="col-sm-12 control-label">{{Lang::get('settings.exchange_server')}}: </label>
                                                    <div class="col-sm-12"> 
                                                        {!! Form::text('smtp_details_exchange_server',@$exchangeData[0]->smtp_details_mailserver,array('class' => 'form-control','id'=>'ms-mailserver','placeholder'=>Lang::get('settings.exchange_server') )) !!}   
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 input-right">
                                                    <label class="col-sm-12 control-label">{{Lang::get('settings.exchange_port')}}: </label>
                                                    <div class="col-sm-12">
                                                        {!! Form::text('smtp_details_exchange_port',@$exchangeData[0]->smtp_details_port,array('class' => 'form-control','id'=>'ms-port','placeholder'=>Lang::get('settings.exchange_port') )) !!}   
                                                    </div>
                                                </div>
                                            </div>
                                        </div>  

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-6 input-left">
                                                    
                                                    <div class="col-sm-12"> 
                                                        <input type="checkbox" id="ms-authentication" <?php if(@$exchangeData[0]->smtp_details_user_authentication == 'on' || @$exchangeData[0]->smtp_details_user_authentication == ''):echo "checked";endif;?> name="smtp_details_exchange_user_authentication">  {{Lang::get('settings.smtp_auth')}}
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 input-right">
                                                    <label class="col-sm-7 ">{{Lang::get('settings.enable_smtp')}}: </label>
                                                    <div class="col-sm-5 enable-smtp">

                                                        <select name="smtp_details_exchange_tls_ssl" id="ms_tls_ssl" class="form-control select2 select2-hidden-accessible" style="width: 100%;" tabindex="-1" aria-hidden="true">
                                                           <option value="none" @if(@$exchangeData[0]->smtp_details_tls_ssl == 'none') selected="selected" @endif>None</option>
                                                          <option @if(@$exchangeData[0]->smtp_details_tls_ssl == 'ssl') selected="selected" @endif value="ssl">SSL</option>
                                                          <option @if(@$exchangeData[0]->smtp_details_tls_ssl == 'tls') selected="selected" @endif value="tls">TLS</option>
                                                        </select>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group" id="ms-auth-user">
                                            <div class="row">
                                                <div class="col-sm-6 input-left">
                                                    <label class="col-sm-12 control-label">{{Lang::get('settings.exchange_user')}}: </label>
                                                    <div class="col-sm-12">
                                                        <input class="form-control" id="msuser-required" placeholder="{{Lang::get('settings.exchange_user')}}" name="smtp_details_exchange_username" type="text" value="{{@$exchangeData[0]->smtp_details_username}}">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 input-right">
                                                    <label class="col-sm-12 control-label">{{Lang::get('settings.exchange_psw')}}: </label>
                                                    <div class="col-sm-12">  
                                                        <input type="password" placeholder="*******" id="mspsw-required" name="smtp_details_exchange_password" class="form-control" autocomplete="new-password" > 
                                                        <!--Hidden password-->
                                                        <input type="hidden" id="mspsw-saved-psw" value="{{@$exchangeData[0]->smtp_details_password}}">
                                                    </div>
                                                    
                                                </div>  
                                            </div>
                                        </div>

                                        <!--Allow account-->
                                        <div class="form-group">
                                            <div class="col-sm-12"> 
                                                {{Lang::get('settings.allow_users')}}: 
                                                <input type="radio" name="smtp_details_active_account" value="exchange" <?php if(@$exchangeData[0]->smtp_details_active_account == 'active'):echo "checked";endif;?>/>
                                            </div>  
                                        </div>  

                                        <!--send test mail-->
                                         <div class="form-group">
                                                <div class="col-sm-8">
                                                    <span id="ms-loading" class="loading"></span><!--Ajax response-->
                                                </div>
                                                <div class="col-sm-4">
                                                    
                                                    <button type="button" serviceProvider='ms' name="test_mail" class="btn btn-primary btn-xs pull-right send-test-email"><i class="fa fa-fw fa-check-square-o"></i> {{Lang::get('settings.send_test_email')}}
                                                    </button>
                                                </div>  
                                        </div>
                                        <!--//send test mail-->
                                    </div>
                                </div>
                                <!-- Yahoo gmail -->
                                <div class="chart tab-pane <?php if(@$yahooData[0]->smtp_details_active_account == 'active'):?> active <?php endif;?>" id="yahoo">
                                    <div class="modal-body">

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-6 input-left">
                                                    <label class="col-sm-12 control-label">{{Lang::get('settings.yahoo_server')}}: </label>
                                                    <div class="col-sm-12"> 
                                                        {!! Form::text('smtp_details_yahoo_server',@$yahooData[0]->smtp_details_mailserver,array('class' => 'form-control','id'=>'yahoo-mailserver','placeholder'=>Lang::get('settings.yahoo_server') )) !!}   
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 input-right">
                                                    <label class="col-sm-12 control-label">{{Lang::get('settings.yahoo_port')}}: </label>
                                                    <div class="col-sm-12">
                                                        {!! Form::text('smtp_details_yahoo_port',@$yahooData[0]->smtp_details_port,array('class' => 'form-control','id'=>'yahoo-port','placeholder'=>Lang::get('settings.yahoo_port') )) !!}   
                                                    </div>
                                                </div>
                                            </div>
                                        </div>  

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-6 input-left">
                                                    <label class="col-sm-12 control-label">{{Lang::get('settings.yahoo_mail_id')}}: </label>
                                                    <div class="col-sm-12">
                                                        {!! Form::text('smtp_details_yahoo_emailid',@$yahooData[0]->smtp_details_username,array('class' => 'form-control','id'=>'yahoouser-required','placeholder'=>Lang::get('settings.yahoo_mail_id') )) !!}   
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 input-right">
                                                    <label class="col-sm-12 control-label">{{Lang::get('settings.yahoo_psw')}}: </label>
                                                    <div class="col-sm-12">  
                                                        <input type="password" placeholder="*******" id='yahoopsw-required' name="smtp_details_yahoo_email_password" class="form-control" autocomplete="new-password"> 
                                                        <!--Hidden password-->
                                                        <input type="hidden" id="yahoopsw-saved-psw" value="{{@$yahooData[0]->smtp_details_password}}">
                                                    </div>
                                                    
                                                </div>  
                                            </div>
                                        </div> 
                                        <!--Allow account-->
                                        <div class="form-group">
                                            <div class="col-sm-12"> 
                                                {{Lang::get('settings.allow_users')}}: 
                                                <input type="radio" name="smtp_details_active_account" value="yahoo" <?php if(@$yahooData[0]->smtp_details_active_account == 'active'):echo "checked";endif;?>/>
                                            </div>  
                                        </div> 

                                        <!--send test mail-->
                                        <div class="form-group">
                                            
                                                <div class="col-sm-8">
                                                    <span id="yahoo-loading" class="loading"></span><!--Ajax response-->
                                                </div>
                                                    <div class="col-sm-4">
                                                        
                                                        <button type="button" serviceProvider='yahoo' name="test_mail" class="btn btn-primary btn-xs pull-right send-test-email"><i class="fa fa-fw fa-check-square-o"></i> {{Lang::get('settings.send_test_email')}}
                                                        </button>
                                                    </div>
                                            
                                        </div>
                                        <!--//send test mail-->
                                    </div>
                                </div>
                                <!-- Gmail -->
                                <div class="chart tab-pane <?php if(@$gmailData[0]->smtp_details_active_account == 'active'):?> active <?php endif;?>" id="gmail">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-6 input-left">
                                                    <label class="col-sm-12 control-label">{{Lang::get('settings.smtp_mail_server')}}: </label>
                                                    <div class="col-sm-12"> 
                                                        {!! Form::text('smtp_details_gmail_server',@$gmailData[0]->smtp_details_mailserver,array('class' => 'form-control','id'=>'gmail-mailserver','placeholder'=>Lang::get('settings.smtp_mail_server') )) !!}   
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 input-right">
                                                    <label class="col-sm-12 control-label">{{Lang::get('settings.smtp_port')}}: </label>
                                                    <div class="col-sm-12">
                                                        {!! Form::text('smtp_details_gmail_port',@$gmailData[0]->smtp_details_port,array('class' => 'form-control','id'=>'gmail-port','placeholder'=>Lang::get('settings.smtp_port') )) !!}   
                                                    </div>
                                                </div>
                                            </div>
                                        </div>  

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-6 input-left">
                                                    
                                                    <div class="col-sm-12"> 
                                                        <input type="checkbox" id="smtp-gmail-authentication" <?php if(@$gmailData[0]->smtp_details_user_authentication == 'on' || @$gmailData[0]->smtp_details_user_authentication == ''):echo "checked";endif;?> name="smtp_details_gmail_user_authentication">  {{Lang::get('settings.smtp_auth')}}
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 input-right">
                                                    <label class="col-sm-7 ">{{Lang::get('settings.enable_smtp')}}: </label>
                                                    <div class="col-sm-5 enable-smtp">
                                                        <select name="smtp_details_gmail_tls_ssl" id="gmail_tls_ssl" class="form-control select2 select2-hidden-accessible" style="width: 100%;" tabindex="-1" aria-hidden="true">
                                                          <option value="none" @if(@$gmailData[0]->smtp_details_tls_ssl == 'none') selected="selected" @endif>None</option>
                                                          <option @if(@$gmailData[0]->smtp_details_tls_ssl == 'ssl') selected="selected" @endif value="ssl">SSL</option>
                                                          <option @if(@$gmailData[0]->smtp_details_tls_ssl == 'tls') selected="selected" @endif value="tls">TLS</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group" id="gmail-auth-user">
                                            <div class="row">
                                                <div class="col-sm-6 input-left">
                                                    <label class="col-sm-12 control-label">{{Lang::get('language.email_address')}}: </label>
                                                    <div class="col-sm-12"> 
                                                        <input class="form-control" id="gmailuser-required" placeholder="{{Lang::get('language.email_address')}}" name="smtp_details_gmail_address" type="text" value="{{@$gmailData[0]->smtp_details_username}}"> 
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 input-right">
                                                    <label class="col-sm-12 control-label">{{Lang::get('settings.gmail_psw')}}:</label>
                                                    <div class="col-sm-12">  
                                                        <input type="password" id="gmailpsw-required" name="smtp_details_gmail_password" class="form-control" placeholder="*******" autocomplete="new-password">
                                                        <!--Hidden password-->
                                                        <input type="hidden" id="gmailpsw-saved-psw" value="{{@$gmailData[0]->smtp_details_password}}"> 
                                                    </div>
                                                    
                                                </div>  
                                            </div>
                                        </div> 

                                        <div class="form-group">
                                            <div class="col-sm-12"> 
                                                {{Lang::get('settings.allow_users')}}: 
                                                <input type="radio" name="smtp_details_active_account" value="gmail" <?php if(@$gmailData[0]->smtp_details_active_account == 'active'):echo "checked";endif;?>/>
                                            </div>  
                                        </div> 
                                        
                                        <!--send test mail-->
                                        <div class="form-group">
                                            
                                                <div class="col-sm-8">
                                                    <span id="gmail-loading" class="loading"></span><!--Ajax response-->
                                                </div>
                                                <div class="col-sm-4"> 
                                                    <button type="button" serviceProvider='gmail' name="test_mail" class="btn btn-primary btn-xs pull-right send-test-email"><i class="fa fa-fw fa-check-square-o"></i> {{Lang::get('settings.send_test_email')}}
                                                    </button>
                                                </div>
                            
                                        </div>
                                        <!--//send test mail-->
                                    </div>
                                </div>
                                <!--Outlook.com-->
                                <div class="chart tab-pane <?php if(@$outlookData[0]->smtp_details_active_account == 'active'):?> active <?php endif;?>" id="outlook">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-6 input-left">
                                                    <label class="col-sm-12 control-label">{{Lang::get('settings.smtp_mail_server')}}: </label>
                                                    <div class="col-sm-12"> 
                                                        {!! Form::text('smtp_details_outlook_server',@$outlookData[0]->smtp_details_mailserver,array('class' => 'form-control','id'=>'outlook-mailserver','placeholder'=>Lang::get('settings.smtp_mail_server') )) !!}   
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 input-right">
                                                    <label class="col-sm-12 control-label">{{Lang::get('settings.smtp_port')}}: </label>
                                                    <div class="col-sm-12">
                                                        {!! Form::text('smtp_details_outlook_port',@$outlookData[0]->smtp_details_port,array('class' => 'form-control','id'=>'outlook-port','placeholder'=>Lang::get('settings.smtp_port') )) !!}   
                                                    </div>
                                                </div>
                                            </div>
                                        </div>  

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-6 input-left">
                                                    
                                                    <div class="col-sm-12"> 
                                                        <input type="checkbox" id="outlook-authentication" <?php if(@$outlookData[0]->smtp_details_user_authentication == 'on' || @$outlookData[0]->smtp_details_user_authentication == ''):echo "checked";endif;?> name="smtp_details_outlook_user_authentication">  {{Lang::get('settings.smtp_auth')}}
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 input-right">
                                                    <label class="col-sm-7 ">{{Lang::get('settings.enable_smtp')}}: </label>
                                                    <div class="col-sm-5 enable-smtp">
                                                        <select name="smtp_details_outlook_tls_ssl" id="outlook_tls_ssl" class="form-control select2 select2-hidden-accessible" style="width: 100%;" tabindex="-1" aria-hidden="true">
                                                          <option value="none" @if(@$outlookData[0]->smtp_details_tls_ssl == 'none') selected="selected" @endif>None</option>
                                                          <option @if(@$outlookData[0]->smtp_details_tls_ssl == 'ssl') selected="selected" @endif value="ssl">SSL</option>
                                                          <option @if(@$outlookData[0]->smtp_details_tls_ssl == 'tls') selected="selected" @endif value="tls">TLS</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group" id="outlook-auth-user">
                                            <div class="row">
                                                <div class="col-sm-6 input-left">
                                                    <label class="col-sm-12 control-label">{{Lang::get('settings.outlook_user')}}: </label>
                                                    <div class="col-sm-12"> 
                                                        <input class="form-control" id="outlookuser-required" placeholder="{{Lang::get('settings.outlook_user')}}" name="smtp_details_outlook_address" type="text" value="{{@$outlookData[0]->smtp_details_username}}"> 
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 input-right">
                                                    <label class="col-sm-12 control-label">{{Lang::get('settings.outlook_psw')}}: </label>
                                                    <div class="col-sm-12">  
                                                        <input type="password" id="outlookpsw-required" name="smtp_details_outlook_password" class="form-control" placeholder="*******" autocomplete="new-password" > 
                                                        <!--Hidden password-->
                                                        <input type="hidden" id="outlookpsw-saved-psw" value="{{@$outlookData[0]->smtp_details_password}}"> 
                                                    </div>
                                                    
                                                </div>  
                                            </div>
                                        </div> 

                                        <div class="form-group">
                                            <div class="col-sm-12"> 
                                                {{Lang::get('settings.allow_users')}}: 
                                                <input type="radio" name="smtp_details_active_account" value="outlook" <?php if(@$outlookData[0]->smtp_details_active_account == 'active'):echo "checked";endif;?>/>
                                            </div>  
                                        </div> 

                                        <!--send test mail-->
                                        <div class="form-group">
                                            
                                                <div class="col-sm-8">
                                                    <span id="outlook-loading" class="loading"></span><!--Ajax response-->
                                                </div>
                                                <div class="col-sm-4">    
                                                    <button type="button" serviceProvider='outlook' name="test_mail" class="btn btn-primary btn-xs pull-right send-test-email"><i class="fa fa-fw fa-check-square-o"></i> {{Lang::get('settings.send_test_email')}}
                                                    </button>
                                                </div>
                                            
                                        </div>
                                        <!--//send test mail-->
                                    </div>
                                </div>
                               
                            </div><!--//End Tabs within a box -->
                        </div>
                    </div>
                </div><!--//SMTP settings end-->


            </div><!--// General form end-->

            <div class="col-md-6">                    
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ trans('settings.acc_lock_sett') }}</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" title="Collapse"><i class="fa fa-minus"></i>
                            </button>
                        </div>

                    </div><!-- /.box-header --> 
                    <!-- form start -->
                    <div class="box-footer" style="">
                        <div class="modal-body">    
                            <!-- form start -->
                            <div class="form-group">
                                <label for="" class="col-sm-12 control-label">{{ trans('settings.login_attempts') }}: </label>
                                <p style="padding-left:15px; font-size:12px; color:#999;">{{trans('settings.login_attempts_desc') }}</p>
                                <div class="row margin">
                                   <div class="col-sm-12">
                                          <input id="range_5" type="text" name="range_5" value="">
                        
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="" class="col-sm-12 control-label">{{ trans('settings.passwd_expiry') }}: </label>
                                <p style="padding-left:15px; font-size:12px; color:#999;">{{ trans('settings.passwd_expiry_desc') }}</p>
                                <div class="row margin">
                                   <div class="col-sm-12">
                                          <input id="range_1" type="text" name="range_1" value="">
                        
                                    </div>
                                </div>
                            </div>


                            <div class="form-group">
                                <label for="" class="col-sm-12 control-label">{{ trans('settings.passwd_complexity') }}: </label>
                                <p style="padding-left:15px; margin-bottom:0px!important; font-size:12px; color:#999;">{{ trans('settings.pswd_cmplx_desc')}}</p>
                                <p style="padding-left:15px; font-size:12px; color:#999;">{{ trans('settings.pswd_contain') }}</p>
                            </div>
                            <div class="row margin">

                                <!-- checkbox -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label style="font-weight: normal;">
                                          <input type="checkbox" id="alpha" <?php if($records->settings_alphabets == Session::get('settings_alphabets')):echo "checked";endif;?> name="settings_alphabets" class="minimal"> {{ trans('settings.alphabets') }}
                                        </label>  
                                    </div>               
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label style="font-weight: normal;">
                                          <input type="checkbox" id="scs" <?php if($records->settings_capital_and_small == Session::get('settings_capital_and_small')):echo "checked";endif;?> name="settings_capital_and_small" class="minimal"> {{ trans('settings.caps_and_small') }}
                                        </label>  
                                    </div>               
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label style="font-weight: normal;">
                                            <input type="checkbox" <?php if($records->settings_numerics == Session::get('settings_numerics')):echo "checked";endif;?> name="settings_numerics" class="minimal" > {{ trans('settings.nums') }}
                                        </label>                                                                   
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label style="font-weight: normal;">
                                              <input type="checkbox" <?php if($records->settings_special_characters == Session::get('settings_special_characters')):echo "checked";endif;?> name="settings_special_characters" class="minimal" > {{ trans('settings.spl_chars') }}&nbsp;<span style="padding-left:15px; font-size:12px; color:#999;">[ {{ trans('settings.splchar') }} ]</span>
                                        </label>                                                                 
                                    </div>
                                </div>                                
                            </div>

                            <div class="form-group">
                                <label for="" class="col-sm-12 control-label">{{ trans('settings.password_length') }}: </label>
                                <p style="padding-left:15px; font-size:12px; color:#999;">{{ trans('settings.pswd_length_desc') }}</p>
                                <div class="row margin">
                                   <div class="col-sm-12">
                                          <input id="range_2" type="text" name="password_length" >                    
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="Encryption pwd" class="col-sm-12 control-label">{{ trans('settings.File Encryption Key') }}: </label>
                                <p style="padding-left:15px; font-size:12px; margin-bottom:0px!important; color:#999;">{{ trans('settings.encrypt_help_text') }}</p>
                                <p style="padding-left:15px; font-size:12px; color:#999;">{{ trans('settings.encrypt_key_length') }}</p>
                                <div class="col-sm-12">
                                    <input type="password" placeholder="*******" autocomplete="new-password" name="doc_encrypt_password" class="form-control" id="doc_encrypt_password" data-parsley-minlength = "{{ trans('settings.min_length_password') }}">
                                    <input type="hidden" id="hidd_doc_encrypt_password" value="{{@$records->settings_encryption_pwd}}" name="hidd_doc_encrypt_password">    
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 


                <!--Email Notification settings-->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{Lang::get('settings.email_notifications')}}</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" title="Collapse"><i class="fa fa-minus"></i>
                            </button>
                        </div>

                    </div><!-- /.box-header -->  

                     <!-- /.box-body -->
                    <div class="box-footer" style="">
                        <div class="modal-body"> 

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="col-sm-12 control-label">
                                        <input type="checkbox" <?php if(@$emailNotif[0]->email_notification_activity_task_notifications == Session::get('activity_task_notifications')):echo "checked";endif;?> name="activity_task_notifications" class="minimal"> 
                                        {{Lang::get('settings.activity_task_notifications')}} </label>
                                    </div> 
                                    <div class="col-sm-12"> 
                                         <p style="padding-left:15px; font-size:12px; color:#999;">{{Lang::get('settings.acivity_task_notifications_hlp')}}</p>
                                    </div>
                                </div>
                                @if (Session::get('module_activation_key4')==Session::get('tval')) 
                                <div class="row">               
                                    <div class="col-sm-12">
                                        <label class="col-sm-12 control-label">
                                        <input type="checkbox" <?php if(@$emailNotif[0]->email_notification_form_notifications == Session::get('form_notifications')):echo "checked";endif;?> name="form_notifications" class="minimal">
                                        {{Lang::get('settings.form_notifications')}} </label>
                                    </div>
                                    <div class="col-sm-12">
                                         <p style="padding-left:15px; font-size:12px; color:#999;">{{Lang::get('settings.form_notifications_hlp')}}</p>
                                    </div>
                                </div>
                                @endif
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="col-sm-12 control-label">
                                        <input type="checkbox" <?php if(@$emailNotif[0]->email_notification_document_notifications  == Session::get('document_notifications')):echo "checked";endif;?> name="document_notifications" class="minimal">
                                        {{Lang::get('settings.document_notifications')}} </label>
                                    </div>
                                    <div class="col-sm-12">
                                        <p style="padding-left:15px; font-size:12px; color:#999;">{{Lang::get('settings.document_notifications_hlp')}}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="col-sm-12 control-label">
                                        <input type="checkbox" <?php if(@$emailNotif[0]->email_notification_signin_notifications == Session::get('signin_notifications')):echo "checked";endif;?> name="signin_notifications" class="minimal">
                                        {{Lang::get('settings.signin_notifications')}} </label>
                                    </div>
                                    <div class="col-sm-12"> 
                                        <p style="padding-left:15px; font-size:12px; color:#999;">{{Lang::get('settings.signin_notifications_hlp')}}</p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="col-sm-12 control-label">
                                        <input type="checkbox" <?php if(@$emailNotif[0]->email_notification_override_email_notifications_settings == Session::get('override_email_notifications_settings')):echo "checked";endif;?> name="override_email_notifications_settings" class="minimal">
                                        {{Lang::get('settings.override_email_notifications')}} </label>
                                    </div>
                                    <div class="col-sm-12"> 
                                        <p style="padding-left:15px; font-size:12px; color:#999;">{{Lang::get('settings.override_email_notifications_hlp')}}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="col-sm-12 control-label">
                                        <input type="radio" <?php if(@$emailNotif[0]->email_notification_overwrite_preferences == Session::get('apply_to_all_users')):echo "checked";endif;?> value="1" name="overwrite_preferences" class="minimal">
                                        {{Lang::get('settings.apply_all_users')}} &nbsp;&nbsp;
                                        <input type="radio" <?php if(@$emailNotif[0]->email_notification_overwrite_preferences == Session::get('apply_to_new_users')):echo "checked";endif;?> value="0" name="overwrite_preferences" class="minimal">
                                        {{Lang::get('settings.apply_new_users')}} </label>
                                    </div>
                                    <div class="col-sm-12"> 
                                        <p style="padding-left:15px; font-size:12px; color:#999;">{{Lang::get('settings.all_users_hlp')}}</p>
                                    </div>
                                    <div class="col-sm-12"> 
                                        <p style="padding-left:15px; font-size:12px; color:#999;">{{Lang::get('settings.new_users_hlp')}}</p>
                                    </div>
                                </div>
                            </div>  

                           
                        </div>
                    </div>
                </div><!--//FTP settings-->
                <!--FTP settings-->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{Lang::get('settings.ftp_settings')}}</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" title="Collapse"><i class="fa fa-minus"></i>
                            </button>
                        </div>

                    </div><!-- /.box-header -->  

                    <!-- /.box-body -->
                    <div class="box-footer" style="">
                        <div class="modal-body"> 

                            <div class="form-group">
                                <div class="row">
                                    <!-- <div class="col-sm-6 input-left">
                                        <label class="col-sm-12 control-label">{{Lang::get('language.smtp_ftp_name')}}: </label>
                                        <div class="col-sm-12"> 
                                            {!! Form::text('ftp_details_name',@$ftpData[0]->ftp_details_name,array('class' => 'form-control global_s_msg','placeholder'=>Lang::get('language.smtp_ftp_name') )) !!}   
                                        </div>
                                    </div> -->                                
                                    <div class="col-sm-6 input-left">
                                        <label class="col-sm-12 control-label">{{Lang::get('settings.ftp_host')}}: </label>
                                        <div class="col-sm-12">
                                            {!! Form::text('ftp_details_host',@$ftpData[0]->ftp_details_host,array('class' => 'form-control global_s_msg','id' => 'ftp_details_host','placeholder'=>Lang::get('settings.ftp_host') )) !!}   
                                        </div>
                                    </div>
                                    <div class="col-sm-6 input-right">
                                        <label class="col-sm-12 control-label">{{Lang::get('settings.smtp_ftp_port')}}: </label>
                                        <div class="col-sm-12">
                                            {!! Form::text('ftp_details_port',@$ftpData[0]->ftp_details_port,array('class' => 'form-control global_s_msg','id' => 'ftp_details_port','placeholder'=>Lang::get('settings.smtp_ftp_port') )) !!}   
                                        </div>
                                    </div>
                                </div>
                            </div>  

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-6 input-left">
                                        <label class="col-sm-12 control-label">{{Lang::get('settings.smtp_ftp_username')}}: </label>
                                        <div class="col-sm-12">
                                            {!! Form::text('ftp_details_username',@$ftpData[0]->ftp_details_username,array('class' => 'form-control global_s_msg','id' => 'ftp_details_username','placeholder'=>Lang::get('settings.smtp_ftp_username') )) !!}   
                                        </div>
                                    </div>
                                    <div class="col-sm-6 input-right">
                                        <label class="col-sm-12 control-label">{{Lang::get('settings.smtp_ftp_psw')}}: </label>
                                        <div class="col-sm-12">
                                            <input type="password" placeholder="*******" autocomplete="new-password" name="ftp_details_password" class="form-control" id="ftp_details_password"> 
                                        </div>
                                    </div>
                                </div>
                            </div> 

                            <div class="form-group">

                                    <div class="col-sm-12">
                                        <label class="control-label">
                                        <input type="radio" <?php if(@$records->settings_ftp == 1){ echo "checked"; } ?> value="1" name="ftp_upload" class="">
                                        {{Lang::get('settings.use_ftp_upload')}} &nbsp;&nbsp;
                                        <input type="radio" <?php if(@$records->settings_ftp == 0){ echo "checked"; } ?> value="0" name="ftp_upload" class="">
                                        {{Lang::get('settings.use_http_upload')}} 

                                    </label>

                                    </div>
                                    </div>
                            <div class="form-group">        
                                    <div class="col-sm-8 ftp_alert">
                                        &nbsp;
                                    </div>

                                    <div class="col-sm-4">
                                       <button type="button" class="btn btn-primary btn-xs pull-right test_ftp_connectivity">
                                                <i class="fa fa-fw fa-check-square-o"></i> {{Lang::get('settings.test_connectiivity')}}
                                            </button> 
                                    </div>
                            </div>
                        </div>
                    </div>
                </div><!--//FTP settings-->

            </div>

             <!--image size validation error message-->
            <div class="alert img_class" style="display:none;">
                <p style="text-align: center;color:red"><strong>Warning!</strong> <b>{{ trans('settings.img_size_msg') }}</b></p>
            </div> 
            <section class="content">
            <div class="form-group">
                <div class="col-sm-12" style="text-align:right;">
                    <input type="submit" value="<?php if($records): echo 'Update';else:echo 'Save';endif;?>" name="save" class="btn btn-primary">
                    <input type="button" value="Reset" name="cancel" class="btn btn-primary btn-danger" id ="btn-reset">
                </div>
            </div><!-- /.col -->

            </section>
        {!! Form::close() !!}     

    </div>  

</section>
@else
    <section class="content"><div class="alert alert-danger alert-sty">{{trans('language.dont_hav_permission')}}</div></section>
@endif

<style type="text/css">
    .control-label{
        text-align: left !important;
    }
</style>
    <script>
      $(function () {

        // SMTP settings
        // Other
        // Create input field when click on smtp authentication
        $('body').on('click','#smtp-other-authentication',function(){

            var ischecked = $(this).is(":checked");
            if(ischecked == true){
                $('#auth-user').css('display','block');
            }else{
                $('#auth-user').css('display','none');
                // Make empty the values
                $('#otheruser-required').val('');
                $('#otherpsw-required').val('');
            }
        });
        // Gmail
        // Create input field when click on gmail authentication
        $('#smtp-gmail-authentication').on('click',function(){
            var ischecked = $(this).is(":checked");
            if(ischecked == true){
                $('#gmail-auth-user').css('display','block');
            }else{
                $('#gmail-auth-user').css('display','none');
                // Make empty the values
                $('#gmailuser-required').val('');
                $('#gmailpsw-required').val('');
            }
        });  
        //MS Exchange
        // Create input field when click on ms authentication
        $('#ms-authentication').on('click',function(){
            var ischecked = $(this).is(":checked");
            if(ischecked == true){
                $('#ms-auth-user').css('display','block');
            }else{
                $('#ms-auth-user').css('display','none');
                $('#msuser-required').val('');
                $('#mspsw-required').val('');
            }
        });
        //Outlook
        // Create input field when click on ms authentication
        $('#outlook-authentication').on('click',function(){
            var ischecked = $(this).is(":checked");
            if(ischecked == true){
                $('#outlook-auth-user').css('display','block');
            }else{
                $('#outlook-auth-user').css('display','none');
                $('#outlookuser-required').val('');
                $('#outlookpsw-required').val('');
            }
        });

        // Send test email
        $(document).on('click','.send-test-email',function(){
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            
            // Get service provider
            var serviceProvider = $(this).attr('serviceProvider');
            var server          = $('#'+serviceProvider+'-mailserver').val();
            var port            = $('#'+serviceProvider+'-port').val();
            var username        = $('#'+serviceProvider+'user-required').val();
            var password        = $('#'+serviceProvider+'psw-required').val();
            var sslTls          = $('#'+serviceProvider+'_tls_ssl').val();
            
            if(password == '')
            {   
                var savedPassword    = $('#'+serviceProvider+'psw-saved-psw').val();
            }
            
            var companyEmailId  = $('#company-email').val();
            if(companyEmailId){
                //check it is email or not
                if (!ValidateEmail(companyEmailId)) {
                    $('#'+serviceProvider+'-loading').html('{{Lang::get('language.invalid_email')}}');
                    $('#'+serviceProvider+'-loading').css('color','red');
                } else {
                    $('#'+serviceProvider+'-loading').html('Sending..');
                    $('#'+serviceProvider+'-loading').css('color','#398439');
                    $.ajax({
                        type:'POST',
                        url : base_url+'/testEmail',
                        data:'_token='+CSRF_TOKEN+'&host='+server+'&port='+port+'&username='+username+'&password='+password+'&savedPsw='+savedPassword+'&emailId='+companyEmailId+'&sslTls='+sslTls,
                        dataType:'json',
                        success:function(response){
                            $('#'+serviceProvider+'-loading').html(response.message);
                            if(response.status == 1){
                                $('#'+serviceProvider+'-loading').css('color','#398439');
                            }else{
                                $('#'+serviceProvider+'-loading').css('color','red');
                            }

                        },
                    });
                }
            }else{
                $('#'+serviceProvider+'-loading').html('{{Lang::get('language.fill_email_id')}}');
                $('#'+serviceProvider+'-loading').css('color','red');
            }
            
        });

        //Gmail validation
        function ValidateEmail(email) {
            var expr = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
            return expr.test(email);
        };
        //<!--// SMTP settings-->


        $('#scs').change(function() {
            if($(this).prop("checked") == true){
                $('#alpha').prop('checked', true);
                $('#alpha').attr('disabled',true);
            }else if($(this).prop("checked") == false){
                $('#alpha').attr('disabled',false);
            }     
        });


        /* BOOTSTRAP SLIDER */
        $('.slider').slider();
        var settings_login_attempts = '<?php echo $records->settings_login_attempts;?>';
        var settings_pasword_expiry = '<?php echo $records->settings_pasword_expiry;?>';
        var settings_password_length_from = '<?php echo $records->settings_password_length_from;?>';
        var settings_password_length_to   = '<?php echo $records->settings_password_length_to;?>';
        var settings_rows_per_page        = '<?php echo $records->settings_rows_per_page;?>';
        var settings_document_expiry      = '<?php echo $records->settings_document_expiry;?>';
       
        $("#range_5").ionRangeSlider({
          min: 0,
          max: 10,
          from: +settings_login_attempts,
          type: 'single',
          step: 1,
          postfix: " Login Attempts",
          prettify: false,
          hasGrid: true,
          from_percent: 6
        });

        $("#range_1").ionRangeSlider({
          min: 0,
          max: 365,
          from: +settings_pasword_expiry,
          type: 'single',
          step: 5,
          postfix: " Days",
          prettify: false,
          hasGrid: true
        });

        // Document expire
        $("#settings_document_expiry").ionRangeSlider({
          min: 1,
          max: 180,
          from: +settings_document_expiry,
          type: 'single',
          step: 1,
          postfix: " Days",
          prettify: false,
          hasGrid: true
        });

        $("#range_2").ionRangeSlider({
          min: 5,
          max: 15,
          from: +settings_password_length_from,
          to: +settings_password_length_to,
          type: 'double',
          step: 1,
          postfix: " Chars",
          prettify: false,
          hasGrid: true
        });

        $("#range_3").ionRangeSlider({
          min: 10,
          max: 500,
          from: +settings_rows_per_page,
          type: 'single',
          step: 10,
          postfix: " per page",
          prettify: false,
          hasGrid: true
        });

        $(document).on("click",".test_ftp_connectivity",function(e) {
    test_ftp_connectivity();
   });

    var test_ftp_connectivity = function() 
   {
    $('.ftp_alert').css('color','#398439');
    $('.ftp_alert').html('Checking...'); 
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var post_data = {_token: CSRF_TOKEN,'ftp_details_host':$('#ftp_details_host').val(),'ftp_details_port':$('#ftp_details_port').val(),'ftp_details_username':$('#ftp_details_username').val(),'ftp_details_password':$('#ftp_details_password').val()}
    $.ajax({
            type: 'POST',
            url: "{{URL('test_ftp_connectivity')}}",
            dataType: 'json',
            data: post_data,
            timeout: 50000,
            success: function(data)
            {
                $('.ftp_alert').html(data.message);     
                if(data.status == 1)
                {
                  $('.ftp_alert').css('color','#398439');
                }
                else
                {
                   $('.ftp_alert').css('color','red'); 
                }



            }
        });
   };
       
      });
    </script>

<script type="text/javascript">
    $(document).ready(function(){
        $("#comp_name").focus();

    });
// select all desired input fields and attach tooltips to them
      $("#globalSettingsAddForm :input").tooltip({
 
      // place tooltip on the right edge
      position: "center right",
 
      // a little tweaking of the position
      offset: [-2, 10],
 
      // use the built-in fadeIn/fadeOut effect
      effect: "fade",
 
      // custom opacity setting
      opacity: 0.7
 
      });  
</script>
@endsection