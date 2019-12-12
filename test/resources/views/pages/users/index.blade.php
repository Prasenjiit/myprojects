<?php
include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')
@section('main_content')

{!! Html::script('js/parsley.min.js') !!}    
{!! Html::script('js/jquery.form.js') !!}   
{!! Html::style('css/font-awesome.min.css') !!} 
{!! Html::style('css/ionicons.min.css') !!} 
{!! Html::style('plugins/datatables_new/jquery.dataTables.min.css') !!}
    
<style type="text/css">
    /*This is for password rules*/
    .main-header {
        position: relative;
        z-index: 1;
    }
    div#userAddModal {
        z-index: 1;
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
    .nopadinglft{
        padding-left: 0px;
    }

</style>

<section class="content-header">
    <div class="col-sm-8">
        <span style="float:left;">
            <strong>{{trans('language.users')}}</strong> &nbsp; 
        </span> 
        <span style="float:left;">
            <?php
                $user_permission=Auth::user()->user_permission;
            ?>
            @if(Auth::user()->user_role != Session::get('user_role_regular_user'))
                 @if(stristr($user_permission,"add") && Auth::user()->user_role != Session::get('user_role_private_user'))
                    <a href="" data-toggle="modal" data-target="#userAddModal" id="dd"><button class="btn btn-block btn-info btn-flat newbtn">{{trans('language.add new')}} <i class="fa fa-user-plus"></i></button></a>
                @endif
            @endif
        </span>
    </div>

    <div class="col-sm-4">
        <!-- <ol class="breadcrumb">
            <li><a href="{{url('/home')}}"><i class="fa fa-dashboard"></i> {{trans('language.home')}}</a></li>
            <li class="active">{{trans('language.users')}}</li>
        </ol> -->
    </div>
</section>
<section class="content content-sty" id="msg" style="display:none;"></section>
<section class="content content-sty" id="msg_add" style="display:none;"></section>

<!--Flash message-->
@if(session('flash_msg'))
    <section class="content content-sty" id="spl-wrn">        
        <div class="alert alert-sty {{session('alert_msg') }}">{{session('flash_msg')}}</div>        
    </section>
@endif
<div class="preloader col-sm-12" style="text-align: center; margin-top: 50px; display: none;" id="bs">
      <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
      <span class="sr-only">Loading...</span>
</div> 
<section class="content" id="shw">
</section>

<!-- User add Form -->
<div class="modal fade" id="userAddModal" data-backdrop="false" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="dGAddModal" >

   <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    {{trans('language.users')}}
                    <small>- {{trans('language.add new')}}</small>
                </h4>
            </div>
            <div class="modal-body">
                <!-- form start -->
                {!! Form::open(array('url'=> array('userSave','0'), 'method'=> 'post', 'class'=> 'form-horizontal dms_form', 'name'=> 'userAddForm', 'id'=> 'userAddForm','data-parsley-validate'=> '')) !!}            

                <div class="form-group">
                    <label for="Full Name" class="col-sm-3 control-label">{{trans('language.full name')}}: <span class="compulsary">*</span></label>
                    <div class="col-sm-6">
                        {!! Form:: 
                        text('name','', 
                        array( 
                        'class'=> 'form-control', 
                        'id'=> 'name', 
                        'title'=> "'".trans('language.full name')."'".trans('language.length_others'), 
                        'placeholder'=> 'Full Name',
                        'required'=> '',
                        'autofocus',
                        'data-parsley-maxlength' => trans('language.max_length'),
                        'data-parsley-required-message' => trans('language.full_name_is_required')                  
                        )
                        ) 
                        !!}      
                        <span class="dms_error">{{$errors->first('name')}}</span>              
                    </div>
                </div>
                <div class="form-group">
                    <label for="Email" class="col-sm-3 control-label">{{ trans('language.email_id') }}: <span class="compulsary">*</span></label>
                    <div class="col-sm-6">
                        {!! Form:: 
                        text(
                        'email','', 
                        array( 
                        'class'                  => 'form-control', 
                        'id'                     => 'email', 
                        'title'                  => trans('language.mail_tooltip'), 
                        'placeholder'            => 'Email',
                        'required'               => '',
                        'data-parsley-maxlength' => trans('language.max_length_email'),
                        'data-parsley-required-message' => trans('language.email_id_required'),
                        'data-parsley-type'      => 'email'
                        )
                        ) 
                        !!}
                        <span class="dms_error">{{$errors->first('email_id')}}</span>
                        <span class="error-msg" id="show-err-msg"></span>
                    </div>

                </div>
                <div class="form-group">
                    <label for="Sign In Name" class="col-sm-3 control-label">{{trans('language.sign in name')}}: <span class="compulsary">*</span></label>
                    <div class="col-sm-6">
                        {!! Form:: 
                        text(
                        'username','', 
                        array( 
                        'class'                  => 'form-control', 
                        'id'                     => 'username', 
                        'title'                  => "'".trans('language.sign in name')."'".trans('language.length_username'), 
                        'placeholder'            => 'Sign In Name',
                        'required'               => '',
                        'data-parsley-maxlength' => trans('language.max_length_username'),
                        'data-parsley-required-message' => trans('language.sign_in_name_is_required'),
                        'data-parsley-minlength' => trans('language.min_length_username')
                        )
                        ) 
                        !!}                   
                        <div id="dp">
                            <span id="dp_wrn" style="display:none;">
                                <i class="fa fa-refresh fa-spin fa-1x fa-fw"></i>
                                <span class="">Please wait...</span>
                            </span>
                        </div>
                    </div>

                </div>
                <?php
                    $minleng  =  @$settings_password_length_from;
                    $maxleng  =  @$settings_password_length_to;

                ?>
                <div class="form-group">
                    <label for="Password" class="col-sm-3 control-label">{{trans('language.password')}}: <span class="compulsary">*</span></label>
                    <div class="col-sm-6">
                        {!! Form:: 
                        password(
                        'password', 
                        array( 
                        'class'                  => 'form-control password1', 
                        'this_val'               => '1',
                        'id'                     => 'password', 
                        'placeholder'            => 'Password',
                        'required'               => '',
                        'data-parsley-minlength' => $records->settings_password_length_from,
                        'data-parsley-maxlength' => $records->settings_password_length_to,
                        'data-parsley-required-message' => trans('language.password_is_required') )) 
                        !!}                    
                        <span class="dms_error">{{$errors->first('password')}}</span>
                        <!--password complexity errors-->
                        <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="space_error1"></li></ul>
                        <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="complexity_error_1alphabets"></li></ul>
                        <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="complexity_error_1numerics"></li></ul>
                        <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="complexity_error_1special_characters"></li></ul>
                        <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="complexity_error_1capital_and_small"></li></ul><!--password complexity errors end-->    
                        <!--password length error-->
                        <span class="password_length_error">
                          <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="password_length_from_error_1"></li></ul>
                          <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="password_length_to_error_1"></li></ul>
                        </span>
                    </div>
                    <!--Complexity message-->
                    <span class="formInfo" ><a href="{{url('getsecuritySettings')}}?id=null" class="jTip" id="one" name="Password must follow these rules:">?</a></span>
                </div>
                <div class="form-group">
                    <label for="Confirm Password" class="col-sm-3 control-label">Confirm {{trans('language.password')}}: <span class="compulsary">*</span></label>
                    <div class="col-sm-6">                      
                        {!! Form::
                        password(
                        'confirm_password', 
                        ['placeholder' => 'Password Confirm', 
                        'class' => 'form-control password2',
                        'this_val'               => '2', 
                        'id'=> 'confirm_password', 
                        'data-parsley-equalto'=> '#password', 
                        'required'=> '', 
                        'data-parsley-minlength' => $records->settings_password_length_from,
                        'data-parsley-maxlength' => $records->settings_password_length_to,
                        'data-parsley-required-message' => trans('language.confirm_psw_is_required')
                        ]) 
                        !!}                   
                        <span class="dms_error">{{$errors->first('confirm_password')}}</span>
                        <!--password complexity errors-->
                        <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="space_error2"></li></ul>
                        <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="complexity_error_2alphabets"></li></ul>
                        <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="complexity_error_2numerics"></li></ul>
                        <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="complexity_error_2special_characters"></li></ul>
                        <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="complexity_error_2capital_and_small"></li></ul><!--password complexity -->
                        <!--password length error-->
                        <span class="password_length_error">
                          <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="password_length_from_error_2"></li></ul>
                          <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="password_length_to_error_2"></li></ul>
                        </span>
                    </div>
                    <!--Complexity message-->
                    <span class="formInfo" ><a href="{{url('getsecuritySettings')}}?id=null" class="jTip" id="one" name="Password must follow these rules:">?</a></span>
                </div>
                 @if(Auth::user()->user_role == Session::get('user_role_super_admin'))
                    <div class="form-group">
                        <label for="User Type" class="col-sm-3 control-label">{{ trans('language.user_status') }} <span class="compulsary">*</span></label>
                        <div class="col-sm-6">
                            <div class="col-sm-4 nopadinglft">
                                <input type="radio" name="user_status" id="status_active" class="rd"  value="1" checked="checked"> {{ trans('language.active') }}
                            </div>
                            <div class="col-sm-4"> 
                                <input type="radio" name="user_status" id="status_inactive" class="rd"  value="0"> {{ trans('language.inactive') }}
                            </div>
                        </div>
                    </div>
                @endif

                
                <div class="form-group">
                    <label for="User Type" class="col-sm-3 control-label">{{trans('language.user type')}}: <span class="compulsary">*</span></label>
                    <div class="col-sm-8">
              
                    @if(Auth::user()->user_role == Session::get('user_role_super_admin'))
                        <div class="col-sm-4 nopadinglft">
                            {!! Form:: 
                            radio(
                            'u-type',
                            'super_admin', 
                            '' ,[
                            'required'=> 'true',
                            'data-parsley-required-message' => trans('language.user_type_is_required'),
                            'id' => 'super_admin',
                            'class' => 'rd']) 
                            !!}  
                            {{ trans('language.super admin') }}
                        </div>
                        <div class="col-sm-6">
                            {!! Form:: 
                            radio('u-type','group_admin', '' ,['id' => 'group_admin','class' => 'rd']) 
                            !!} 
                            {{ trans('language.group admin')}}
                        </div>
                        <div class="col-sm-4 nopadinglft">
                            {!! Form:: 
                            radio('u-type','regular_user', '' ,['id' => 'regular_user','class' => 'rd']) 
                            !!}  
                            {{ trans('language.regular user') }} 
                        </div>
                        <div class="col-sm-4">
                            {!! Form:: 
                            radio('u-type','viewonly_user', '' ,['id' => 'viewonly_user','class' => 'rd']) 
                            !!}  
                            {{ trans('language.viewonly_user') }} 
                        </div>
                        <div class="col-sm-4 nopadinglft">
                            {!! Form:: 
                            radio('u-type','private_user', '' ,['id' => 'private_user','class' => 'rd']) 
                            !!}  
                            {{trans('language.private_user')}} 
                        </div>
                    @elseif(Auth::user()->user_role == Session::get('user_role_group_admin'))
                        <div class="col-sm-4 nopadinglft">
                            {!! Form:: 
                            radio('u-type','group_admin', '' ,['id' => 'group_admin','class' => 'rd']) 
                            !!} 
                            {{trans('language.group admin')}}
                        </div>
                        <div class="col-sm-4">
                            {!! Form:: 
                            radio('u-type','regular_user', '' ,['id' => 'regular_user','class' => 'rd']) 
                            !!}  
                            {{ trans('language.regular user') }}
                        </div>
                        <div class="col-sm-4 nopadinglft">
                            {!! Form:: 
                            radio('u-type','viewonly_user', '' ,['id' => 'viewonly_user','class' => 'rd']) 
                            !!}  
                            {{ trans('language.viewonly_user') }} 
                        </div>
                    @endif
                    </div>
                </div>

                <div id="spl_div">
                    <div class="form-group">
                        <label for="Department" class="col-sm-3 control-label">@if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{trans('language.department')}} @endif: <span class="compulsary">*</span></label>
                        <div class="col-sm-8">
                        
                        <?php $incr = 1;
                        $j =1; ?>
                            @foreach ($departments as $key => $val)
                                <div class="col-sm-4 <?php if($j==1){ ?>nopadinglft<?php } ?>">
                                    <input type="checkbox" name="document_group[]" required="" data-parsley-required-message='@if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{trans('language.department')}} @endif {{trans('language.is_required')}}' data-parsley-mincheck = '1' value="<?php echo $val->department_id; ?>"> <?php if($incr ==3){ ?>
                                        {{ $val->department_name }} <br/>
                                    <?php }else{ ?>
                                        {{ $val->department_name }} &nbsp;&nbsp;
                                    <?php } ?>
                                    <?php $j++; if($j>3){ $j=1; } $incr ++; ?>
                                </div>
                            @endforeach
                                            
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="{{ trans('language.privileges') }}" class="col-sm-3 control-label">{{ trans('language.privileges') }}: <span class="compulsary">*</span></label>
                    <div class="col-sm-8">
                        <div class="col-sm-4 nopadinglft">
                            {!! Form::
                            checkbox('privileges[]',
                            'add',
                            null, 
                            array('required' => '', 
                            'data-parsley-required-message' => trans('language.user_access_privileges_is_required'), 
                            'id' => 'add','class'=>'privilege usr_permission'))
                            !!} {{trans('language.add')}}
                        </div>
                        <div class="col-sm-4">                    
                            {!! Form::checkbox('privileges[]', 'edit', null, array('id' => 'edit','class'=>'privilege usr_permission')) !!} {{trans('language.edit')}}
                        </div>
                        <div class="col-sm-4">
                            {!! Form::checkbox('privileges[]', 'view', null, array('id' => 'view','class'=>'privilege usr_permission')) !!} {{ trans('language.view')}}
                        </div>
                        <div class="col-sm-4 nopadinglft">
                            {!! Form::checkbox('privileges[]', 'delete', null, array('id' => 'delete','class'=>'privilege usr_permission')) !!} {{trans('language.delete')}}
                        </div>
                        <div class="col-sm-4">
                            {!! Form::checkbox('privileges[]', 'checkout', null, array('id' => 'checkout','class'=>'privilege usr_permission')) !!} {{ trans('language.checkinout')}}
                        </div>
                        <div class="col-sm-4">
                            {!! Form::checkbox('privileges[]', 'import', null, array('id' => 'import','class'=>'privilege usr_permission')) !!} {{ trans('language.import')}}
                        </div>
                        <div class="col-sm-4 nopadinglft">
                            {!! Form::checkbox('privileges[]', 'export', null, array('id' => 'export','class'=>'privilege usr_permission')) !!} {{trans('language.export')}}
                        </div>
                        <div class="col-sm-4">
                            {!! Form::checkbox('privileges[]', 'workflow', null, array('id' => 'workflow','class'=>'privilege usr_permission')) !!} {{trans('language.workflow')}}
                        </div>
                        <div class="col-sm-4">
                            {!! Form::checkbox('privileges[]', 'decrypt', null, array('id' => 'decrypt','class'=>'privilege usr_permission')) !!} {{ trans('language.decrypt')}}
                        </div>
                        <div class="col-sm-4 nopadinglft">
                            {!! Form::checkbox('s_all', 9, null, array('id' => 's_all')) !!} {{trans('language.select_all')}}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="{{trans('language.form_privileges')}}" class="col-sm-3 control-label">{{trans('language.form_privileges')}}:</label>
                    <div class="col-sm-8">
                        <div class="col-sm-4 nopadinglft">
                            {!! Form::
                            checkbox('privileges_frm[]',
                            'add',
                            null, 
                            array('id' => 'add_frm','class'=>'privilege frm_privileges'))
                            !!} {{trans('language.add')}}
                        </div>
                        <div class="col-sm-4">                        
                            {!! Form::checkbox('privileges_frm[]', 'edit', null, array('id' => 'edit_frm','class'=>'privilege frm_privileges')) !!} {{trans('language.edit')}}
                        </div>
                        <div class="col-sm-4">
                            {!! Form::checkbox('privileges_frm[]', 'view', null, array('id' => 'view_frm','class'=>'privilege frm_privileges')) !!} {{trans('language.view')}}
                        </div>
                        <div class="col-sm-4 nopadinglft">
                            {!! Form::checkbox('privileges_frm[]', 'delete', null, array('id' => 'delete_frm','class'=>'privilege frm_privileges')) !!} {{trans('language.delete')}}
                        </div>
                        <div class="col-sm-4">
                            {!! Form::checkbox('privileges_frm[]', 'export', null, array('id' => 'export_frm','class'=>'privilege frm_privileges')) !!} {{trans('language.export')}}
                        </div>
                        <div class="col-sm-4">
                            {!! Form::checkbox('s_all_frm', 5, null, array('id' => 's_all_frm')) !!} {{trans('language.select_all')}}
                        </div>                       
                    </div>
                </div>

                <div class="form-group">
                    <label for="{{trans('language.workflow_privileges')}}" class="col-sm-3 control-label">{{trans('language.workflow_privileges')}}: </label>
                    <div class="col-sm-8">
                        <div class="col-sm-4 nopadinglft">
                            {!! Form::
                            checkbox('privileges_wf[]',
                            'add',
                            null, 
                            array('id' => 'add_wf','class'=>'privilege wf_previlages'))
                            !!} {{trans('language.add')}}
                        </div>
                        <div class="col-sm-4">                       
                            {!! Form::checkbox('privileges_wf[]', 'edit', null, array('id' => 'edit_wf','class'=>'privilege wf_previlages')) !!} {{trans('language.edit')}}
                        </div>
                        <div class="col-sm-4">
                            {!! Form::checkbox('privileges_wf[]', 'view', null, array('id' => 'view_wf','class'=>'privilege wf_previlages')) !!} {{trans('language.view')}}
                        </div>
                        <div class="col-sm-4 nopadinglft">
                            {!! Form::checkbox('privileges_wf[]', 'delete', null, array('id' => 'delete_wf','class'=>'privilege wf_previlages')) !!} {{trans('language.delete')}}
                        </div>
                        <div class="col-sm-4">
                            {!! Form::checkbox('s_all_wf', 4, null, array('id' => 's_all_wf')) !!} {{trans('language.select_all')}}
                        </div>
                    </div>
                </div>
              
                
                <div class="form-group">
                    <label for="{{trans('language.privileges')}}" class="col-sm-3 control-label">{{Lang::get('language.email_notifications')}}: <span class="compulsary">*</span></label>
                    <div class="col-sm-8">
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="control-label">
                                <input type="checkbox" <?php if(@$emailNotif[0]->email_notification_activity_task_notifications == Session::get('activity_task_notifications')):echo "checked";endif;?> name="activity_task_notifications" class="minimal"> 
                                {{Lang::get('language.activity_task_notifications')}} </label>
                            </div> 
                            <div class="col-sm-12"> 
                                 <p style="padding-left:15px; font-size:12px; color:#999;">{{Lang::get('language.acivity_task_notifications_hlp')}}</p>
                            </div>
                        </div>
                        <div class="row">               
                            <div class="col-sm-12">
                                <label class="control-label">
                                <input type="checkbox" <?php if(@$emailNotif[0]->email_notification_form_notifications == Session::get('form_notifications')):echo "checked";endif;?> name="form_notifications" class="minimal">
                                {{Lang::get('language.form_notifications')}} </label>
                            </div>
                            <div class="col-sm-12">
                                 <p style="padding-left:15px; font-size:12px; color:#999;">{{Lang::get('language.form_notifications_hlp')}}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="control-label">
                                <input type="checkbox" <?php if(@$emailNotif[0]->email_notification_document_notifications  == Session::get('document_notifications')):echo "checked";endif;?> name="document_notifications" class="minimal">
                                {{Lang::get('language.document_notifications')}} </label>
                            </div>
                            <div class="col-sm-12">
                                <p style="padding-left:15px; font-size:12px; color:#999;">{{Lang::get('language.document_notifications_hlp')}}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="control-label">
                                <input type="checkbox" <?php if(@$emailNotif[0]->email_notification_signin_notifications == Session::get('signin_notifications')):echo "checked";endif;?> name="signin_notifications" class="minimal">
                                {{Lang::get('language.signin_notifications')}} </label>
                            </div>
                            <div class="col-sm-12"> 
                                <p style="padding-left:15px; font-size:12px; color:#999;">{{Lang::get('language.signin_notifications_hlp')}}</p>
                            </div>
                        </div>
                        <input type="hidden" name="stngs_activitytasknotif" value="<?php echo @$emailNotif[0]->email_notification_activity_task_notifications; ?>">
                        <input type="hidden" name="stngs_formnotif" value="<?php echo @$emailNotif[0]->email_notification_form_notifications; ?>">
                        <input type="hidden" name="stngs_documentnotif" value="<?php echo @$emailNotif[0]->email_notification_document_notifications; ?>">
                        <input type="hidden" name="stngs_singninnotif" value="<?php echo @$emailNotif[0]->email_notification_signin_notifications; ?>">

                        <div class="row">
                            <div class="col-sm-12">
                                <label class="control-label">
                                <input type="checkbox" checked="checked" value="1" name="system_prefer" class="minimal">
                                {{Lang::get('language.use_system_prefer')}} </label>
                            </div>                           
                        </div>
                    </div> 
                </div>
                
                
                <div class="form-group">
                    <label for="Sign In Expiry Date" class="col-sm-3 control-label">{{trans('language.expiry_date')}}: <span class="compulsary">*</span></label>
                    <div class="col-sm-6">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control" id="exp_date" name="exp_date" placeholder="YYYY-MM-DD">
                        </div><!-- /.input group -->
                    </div>
                    <span class="formInfo" ><a href="{{url('getexpiryMessage')}}" class="jTip" id="three" name="Expiry information">?</a></span>
                    {!! Form::checkbox('login_exp_chk',1, true, ['id'=> 'login_exp_chk']) !!} {{ trans('language.no expiry') }}
                    
                </div>
                <div class="form-group">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-6">
                        <p class="subnote">{{ trans('language.user_expiry_note') }}</p>
                    </div>
                </div>

                <div class="form-group">
                  <label for="" class="col-sm-3 control-label">Report To: </label>

                    <div class="col-sm-6">
                       <select class="form-control report_to" id="report_to"   name="report_to">
                                <option value="">{{ trans('language.select_user') }}</option>
                                   <?php
                                    foreach ($users as $key => $user) {
                                        ?>
                                        <option value="<?php echo $user->id; ?>" ><?php echo ucfirst(@$user->user_full_name);?> -{{@$user->user_role}}@if(@$user->departments[0]->department_name != "")- {{ ucfirst(@$user->departments[0]->department_name) }} @endif</option>
                                        <?php
                                    }
                                    ?> 
                                </select>
                          <p class="help">[SA] - Super Admin, [DA] - Department Admin, [RU] - Regular User, [PU] - Private User</p>
                         <!-- <p class="help">{{$language['form_help2']}}</p> -->
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-3 control-label"></label>
                    <div class="col-sm-6" style="text-align:right;">
                        {!!Form::submit(trans('language.save'), array('class' => 'btn btn-primary sav_btn')) !!} &nbsp;&nbsp;
                        <a href="{{URL::route('users')}}">
                            {!!Form::button(trans('language.close'), array('class' => 'btn btn-primary btn-danger', 'id' => 'cn', 'data-dismiss' => 'modal')) !!}
                        </a>
                    </div>
                </div><!-- /.col -->
                {!! Form::close() !!}
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- User add formend -->


<!-- User edit Form -->
<div class="modal fade" id="dTEditModal" data-backdrop="false" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- User edit form end -->
<script>
    $(function ($) {

        // Get language
        var expiry_lang = "{{ trans('language.sign_in_expiry_date_is_required') }}";

        //set focus on input
        //$("#name").focus();
        load();   

         
        //Ajax form
        var options = { 
            target:        '#msg_add',   // target element(s) to be updated with server response 
            beforeSubmit:  showRequest,  // pre-submit callback 
            success:       showResponse,  // post-submit callback     
            complete:      showStatus
        }; 
        // bind form using 'ajaxForm' 
        $('#userAddForm').ajaxForm(options);

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
      
        $('#exp_date').daterangepicker({
            singleDatePicker: true,
            "drops": "up",
            minDate: today,
            maxDate: moment(date),
            showDropdowns: true
        });

        window.ParsleyConfig = {
            errorsWrapper: '<div></div>',
            errorTemplate: '<div class="alert alert-danger parsley" role="alert"></div>'
        };

        $('#spl-wrn').delay(5000).slideUp('slow');

        //Expire checking
        if($("#login_exp_chk").prop('checked') == true){
            $("#exp_date").attr("disabled", true);
        } else {
            
        }   
        $('#login_exp_chk').click(function() {
            var ckb = $("#login_exp_chk").is(':checked');
            if(ckb == false) {
                $("#exp_date").attr("disabled", false);
                $('#exp_date').attr('required','required');
                $('#exp_date').attr('data-parsley-required-message',expiry_lang);
            } else {
                $("#exp_date").attr("disabled", true);
                $('#exp_date').attr('required',false);
                $('#exp_date').val('');
            }           
        }); 

        
        //Radion button checking forsuperadmin
        $('.rd').click(function() {
            var id= this.id;
            if(id == 'super_admin')
            {                  
                $('#add').prop('checked', true);
                $('#edit').prop('checked', true);
                $('#view').prop('checked', true);
                $('#delete').prop('checked', true);
                $('#checkout').prop('checked', true);
                $('#import').prop('checked', true);
                $('#export').prop('checked', true);
                $('#workflow').prop('checked', true);
                $('#decrypt').prop('checked', true);
                $('#s_all').prop('checked', true);

                //form section
                $('#add_frm').prop('checked', true);
                $('#edit_frm').prop('checked', true);
                $('#view_frm').prop('checked', true);
                $('#delete_frm').prop('checked', true);
                $('#export_frm').prop('checked', true);
                $('#s_all_frm').prop('checked', true);

                $("#add_frm").attr("disabled", true);
                $("#edit_frm").attr("disabled", true);
                $("#view_frm").attr("disabled", true);
                $("#delete_frm").attr("disabled", true);
                $('#export_frm').attr('disabled', true);
                $("#s_all_frm").attr("disabled", true);

                //workflow section
                $('#add_wf').prop('checked', true);
                $('#edit_wf').prop('checked', true);
                $('#view_wf').prop('checked', true);
                $('#delete_wf').prop('checked', true);
                $('#s_all_wf').prop('checked', true);

                $("#add_wf").attr("disabled", true);
                $("#edit_wf").attr("disabled", true);
                $("#view_wf").attr("disabled", true);
                $("#delete_wf").attr("disabled", true);
                $('#export_wf').attr('disabled', true);
                $("#s_all_wf").attr("disabled", true);

                $("#add").attr("disabled", true);
                $("#edit").attr("disabled", true);
                $("#view").attr("disabled", true);
                $("#delete").attr("disabled", true);
                $('#checkout').attr('disabled', true);
                $('#import').attr('disabled', true);
                $('#export').attr('disabled', true);
                $('#workflow').attr('disabled', true);
                $('#decrypt').attr('disabled', true);
                $("#s_all").attr("disabled", true);

                $("#spl_div").hide();
                //Hide validation for some fields
                $('#spl_div :checkbox:enabled').prop('checked', true);              
            }
            else if(id == 'group_admin') {  
                $('#add').prop('checked', true);
                $('#edit').prop('checked', true);
                $('#view').prop('checked', true);
                $('#delete').prop('checked', true);
                $('#checkout').prop('checked', true);
                $('#import').prop('checked', true);
                $('#export').prop('checked', true);
                $('#workflow').prop('checked', true);
                $('#decrypt').prop('checked', true);
                $('#s_all').prop('checked', true);

                $("#add").attr("disabled", true);
                $("#edit").attr("disabled", true);
                $("#view").attr("disabled", true);
                $("#delete").attr("disabled", true);
                $('#checkout').attr('disabled', true);
                $('#import').attr('disabled', true);
                $('#export').attr('disabled', true);
                $('#workflow').attr('disabled', true);
                $('#decrypt').attr('disabled', true);
                $("#s_all").attr("disabled", true);

                //form section
                $('#add_frm').prop('checked', true);
                $('#edit_frm').prop('checked', true);
                $('#view_frm').prop('checked', true);
                $('#delete_frm').prop('checked', true);
                $('#export_frm').prop('checked', true);
                $('#s_all_frm').prop('checked', true);

                $("#add_frm").attr("disabled", true);
                $("#edit_frm").attr("disabled", true);
                $("#view_frm").attr("disabled", true);
                $("#delete_frm").attr("disabled", true);
                $('#export_frm').attr('disabled', true);
                $("#s_all_frm").attr("disabled", true);

                //workflow section
                $('#add_wf').prop('checked', true);
                $('#edit_wf').prop('checked', true);
                $('#view_wf').prop('checked', true);
                $('#delete_wf').prop('checked', true);
                $('#s_all_wf').prop('checked', true);

                $("#add_wf").attr("disabled", true);
                $("#edit_wf").attr("disabled", true);
                $("#view_wf").attr("disabled", true);
                $("#delete_wf").attr("disabled", true);
                $("#s_all_wf").attr("disabled", true);                

                $("#spl_div").show();
                $('#spl_div :checkbox:enabled').prop('checked', false); 
            }else if(id == 'viewonly_user') {  
                $('#add').prop('checked', false);
                $('#edit').prop('checked', false);
                $('#view').prop('checked', true);
                $('#delete').prop('checked', false);
                $('#checkout').prop('checked', false);
                $('#import').prop('checked', false);
                $('#export').prop('checked', false);
                $('#workflow').prop('checked', false);
                $('#decrypt').prop('checked', false);
                $('#s_all').prop('checked', false);

                $("#add").attr("disabled", true);
                $("#edit").attr("disabled", true);
                $("#view").attr("disabled", true);
                $("#delete").attr("disabled", true);
                $('#checkout').attr('disabled', true);
                $('#import').attr('disabled', true);
                $('#export').attr('disabled', true);
                $('#workflow').attr('disabled', true);
                $('#decrypt').attr('disabled', true);
                $("#s_all").attr("disabled", true);

                //form section
                $('#add_frm').prop('checked', false);
                $('#edit_frm').prop('checked', false);
                $('#view_frm').prop('checked', true);
                $('#delete_frm').prop('checked', false);
                $('#export_frm').prop('checked', false);
                $('#s_all_frm').prop('checked', false);

                $("#add_frm").attr("disabled", true);
                $("#edit_frm").attr("disabled", true);
                $("#view_frm").attr("disabled", true);
                $("#delete_frm").attr("disabled", true);
                $('#export_frm').attr('disabled', true);
                $("#s_all_frm").attr("disabled", true);

                //workflow section
                $('#add_wf').prop('checked', false);
                $('#edit_wf').prop('checked', false);
                $('#view_wf').prop('checked', true);
                $('#delete_wf').prop('checked', false);
                $('#s_all_wf').prop('checked', false);

                $("#add_wf").attr("disabled", true);
                $("#edit_wf").attr("disabled", true);
                $("#view_wf").attr("disabled", true);
                $("#delete_wf").attr("disabled", true);
                $("#s_all_wf").attr("disabled", true);                

                $("#spl_div").show();
                $('#spl_div :checkbox:enabled').prop('checked', false); 
            }
            else {
                // Both private and regular user
                $('#add').prop('checked', false);
                $('#edit').prop('checked', false);
                $('#view').prop('checked', false);
                $('#delete').prop('checked', false);
                $('#checkout').prop('checked', false);
                $('#import').prop('checked', false);
                $('#export').prop('checked', false);
                $('#workflow').prop('checked', false);
                $('#decrypt').prop('checked', false);
                $('#s_all').prop('checked', false);

                $("#add").attr("disabled", false);
                $("#edit").attr("disabled", false);
                $("#view").attr("disabled", false);
                $("#delete").attr("disabled", false);
                $('#checkout').attr('disabled', false);
                $('#import').attr('disabled', false);
                $('#export').attr('disabled', false);
                $('#workflow').attr('disabled', false);
                $('#decrypt').attr('disabled', false);
                $("#s_all").attr("disabled", false);

                $('#add_frm').prop('checked', false);
                $('#edit_frm').prop('checked', false);
                $('#view_frm').prop('checked', false);
                $('#delete_frm').prop('checked', false);
                $('#export_frm').prop('checked', false);
                $('#s_all_frm').prop('checked', false);

                $("#add_frm").attr("disabled", false);
                $("#edit_frm").attr("disabled", false);
                $("#view_frm").attr("disabled", false);
                $("#delete_frm").attr("disabled", false);
                $('#export_frm').attr('disabled', false);
                $("#s_all_frm").attr("disabled", false);

                $('#add_wf').prop('checked', false);
                $('#edit_wf').prop('checked', false);
                $('#view_wf').prop('checked', false);
                $('#delete_wf').prop('checked', false);
                $('#s_all_wf').prop('checked', false);

                $("#add_wf").attr("disabled", false);
                $("#edit_wf").attr("disabled", false);
                $("#view_wf").attr("disabled", false);
                $("#delete_wf").attr("disabled", false);
                $("#s_all_wf").attr("disabled", false);

                $("#spl_div").show();
                $('#spl_div :checkbox:enabled').prop('checked', false); 
            }
        });
        //Select all
        $('#s_all').click(function() {
            var ckb = $("#s_all").is(':checked');
            if(ckb == false) {
                $('#add').prop('checked', false);
                $('#edit').prop('checked', false);
                $('#view').prop('checked', false);
                $('#delete').prop('checked', false);
                $('#checkout').prop('checked', false);
                $('#import').prop('checked', false);
                $('#export').prop('checked', false);
                $('#decrypt').prop('checked', false);
                $('#workflow').prop('checked', false);
            } else {
                $('#add').prop('checked', true);
                $('#edit').prop('checked', true);
                $('#view').prop('checked', true);
                $('#delete').prop('checked', true);
                $('#checkout').prop('checked', true);
                $('#import').prop('checked', true);
                $('#export').prop('checked', true);
                $('#workflow').prop('checked', true);
                $('#decrypt').prop('checked', true);
            }  
        });

        //Select all
        $('#s_all_frm').click(function() {
            var ckb = $("#s_all_frm").is(':checked');
            if(ckb == false) {
                $('#add_frm').prop('checked', false);
                $('#edit_frm').prop('checked', false);
                $('#view_frm').prop('checked', false);
                $('#delete_frm').prop('checked', false);
                $('#export_frm').prop('checked', false);
            } else {
                $('#add_frm').prop('checked', true);
                $('#edit_frm').prop('checked', true);
                $('#view_frm').prop('checked', true);
                $('#delete_frm').prop('checked', true);
                $('#export_frm').prop('checked', true);
            }  
        });

        //Select all
        $('#s_all_wf').click(function() {
            var ckb = $("#s_all_wf").is(':checked');
            if(ckb == false) {
                $('#add_wf').prop('checked', false);
                $('#edit_wf').prop('checked', false);
                $('#view_wf').prop('checked', false);
                $('#delete_wf').prop('checked', false);
            } else {
                $('#add_wf').prop('checked', true);
                $('#edit_wf').prop('checked', true);
                $('#view_wf').prop('checked', true);
                $('#delete_wf').prop('checked', true);
            }  
        });

        $('.usr_permission').click(function() {
          if($(this).is(':checked')==false) {
            $('#s_all').prop('checked', false);
          }
          var cnt = $('.usr_permission:checked').length;
          if(cnt==9){
            $('#s_all').prop('checked', true);
          }
        });
        $('.frm_privileges').click(function() {
          if($(this).is(':checked')==false) {
            $('#s_all_frm').prop('checked', false);
          }
          var cnt = $('.frm_privileges:checked').length;
          if(cnt==5){
            $('#s_all_frm').prop('checked', true);
          }
        });
        $('.wf_previlages').click(function() {
          if($(this).is(':checked')==false) {
            $('#s_all_wf').prop('checked', false);
          }
          var cnt = $('.wf_previlages:checked').length;
          if(cnt==4){
            $('#s_all_wf').prop('checked', true);
          }
        });

        //Add Modal reset
        $("#cn").click(function(){
            $("#userAddForm")[0].reset();
            $('#userAddForm').parsley().reset();
        });


        //Duplicate entry
        $("#username").change(function(){
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var name       = $("#username").val();
            $.ajax({
                type:'POST',
                url: '{{URL('userDuplication')}}',
                dataType:'json',
                data: {_token: CSRF_TOKEN, name: name },
                success:function(response){
                    if(response.status == 'true'){
                        // Data exists
                        $("#dp").html('<div class="parsley-errors-list filled" id="dp-inner">'+name+' {{Lang::get('language.already_db_msg')}} </div>');
                        $("#username").val('');
                    }else{
                        $("#dp").html('');
                    }
                }
            });
        });//Duplicate entry ends


    });

    function load()
    {
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            type: 'post',
            url: '{{URL::route('usersList')}}',
            dataType: 'html',
            data: {_token: CSRF_TOKEN},
            timeout: 50000,
            beforeSend: function() {
                $("#bs").show();
            },
            success: function(data, status){
                $("#shw").html(data);       
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

    // pre-submit callback 
    function showRequest(formData, jqForm, options) {
        $("#bs").show();
        return true; 
    } 

    // post-submit callback 
    function showResponse(responseText, statusText, xhr, $form)  {
        $('#cn').click();
        $("#userAddForm")[0].reset();
        //$("#msg_add").empty();
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

    function del(id,user)
    {

        swal({
              title: "{{trans('language.confirm_delete_single')}}'" + user + "' ?",
              text: "{{trans('language.Swal_not_revert')}}",
              type: "{{trans('language.Swal_warning')}}",
              showCancelButton: true
            }).then(function (result) {
                if(result){
                    // Success
                     
                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    $.ajax({
                        type: 'post',
                        url: '{{URL('userDelete')}}',
                        dataType: 'json',
                        data: {_token: CSRF_TOKEN, id:id},
                        timeout: 50000,
                        beforeSend: function() {
                            $("#bs").show();
                        },
                        success: function(data, status){ 
                            load();
                            //$("#msg").empty();
                            if(data != 'false'){
                                setTimeout(function () {
                                    $("#msg").html('<div class="alert alert-success alert-sty">'+ data +'</div>');
                                    $("#msg").slideDown(1000);
                                }, 200);
                                setTimeout(function () {
                                    $('#msg').slideUp("slow");
                                }, 5000);
                            }else{
                                setTimeout(function () {
                                    $("#msg").html('<div class="alert alert-warning alert-sty">You can not delete.This user is already logged in.</div>');
                                    $("#msg").slideDown(1000);
                                }, 200);
                                setTimeout(function () {
                                    $('#msg').slideUp("slow");
                                }, 5000);
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
    }
// select all desired input fields and attach tooltips to them
      $("#userAddForm :input").tooltip({
 
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