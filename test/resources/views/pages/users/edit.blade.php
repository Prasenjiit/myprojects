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
    .notif_label{
        text-align: left !important;
    }
    @media(max-width:767px){
      .formInfo {
          margin-left: 13px !important;
      }
    }

    .s-btn{
          text-align: right;
    }
    .nopadinglft{
        padding-left: 0px;
    }

</style>
<section class="content" id="shw">
  <section class="content-header">
      <div class="row">
          <strong>
              {{$language['users']}}
              <small>- Edit User: {{ $userData->username }}</small>
          </strong>
          <a href="{{url('users')}}" class="btn btn-primary">Back</a>
      </div>
  </section>

  @if(Session::get('flash_message_wanning'))
      <section class="content content-sty" id="spl-wrn">     
        <div class="alert alert-sty {{ Session::get('alert-class', 'alert-info') }} ">{{ Session::get('flash_message_wanning') }}</div>        
      </section>
  @endif
  @if(Session::get('data'))
  <section class="content content-sty" id="spl-wrn">        
      <div class="alert alert-sty alert-success ">{{ Session::get('data') }}</div>        
  </section>
  @endif
  <!--Flash message-->
  @if(Session::get('flash_msg'))
      <section class="content content-sty" id="spl-wrn">        
          <div class="alert alert-sty {{session('alert_msg') }}">{{session('flash_msg')}}</div>        
      </section>
  @endif
  <section class="content content-sty" id="msg" style="display:none;"></section>
<!--Edit Own profile-->
@if($logged_in_userId == $userData->id)
<!-- Main content Without Notification Left And Right-->
<!-- Main content Left And Right-->
<section class="content">
@if(stristr($user_permission,'view'))
          {!! Form::open(array('url'=> array('userSave',$userData->id), 'method'=> 'post', 'class'=> 'form-horizontal dms_form', 'name'=> 'userEditForm', 'id'=> 'userEditForm','data-parsley-validate'=> '')) !!} 
    <div class="row">
        <div class="col-md-10">
            <!-- general form elements -->
          
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('language.edit_user') }}</h3> 
                </div><!-- /.box-header -->
                <!--Checking view permission-->
                 
                    <!--SA and GA-->
                    <div class="modal-body">    
                        <!-- form start -->
                          

                        <div class="form-group">
                                <label for="Full Name" class="col-sm-12 control-label notif_label">{{trans('language.full name')}}:<span class="compulsary">*</span></label>
                                <div class="col-sm-10">
                                    {!! Form:: text('name',$userData->user_full_name, 
                                        array( 
                                            'class'=> 'form-control', 
                                            'id'=> 'name', 
                                            'title'=> "'".trans('language.full name')."'".trans('language.length_others'), 
                                            'placeholder'=> 'Full Name',
                                            'required'=> '',
                                            'data-parsley-required-message' => trans('language.full_name_is_required'),
                                            'data-parsley-maxlength' => trans('language.max_length'),
                                            'data-parsley-trigger'          => 'change focusout',
                                            'autofocus'
                                    ))!!}                   
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Email" class="col-sm-12 control-label notif_label">{{trans('language.email_id')}}: <span class="compulsary">*</span></label>
                                <div class="col-sm-10">
                                  {!! Form:: 
                                          text(
                                          'email',$userData->email, 
                                          array( 
                                          'class'                  => 'form-control', 
                                          'id'                     => 'email', 
                                          'title'                  => trans('language.mail_tooltip'), 
                                          'placeholder'            => 'Email',
                                          'required'               => '',
                                          'data-parsley-required-message' => trans('language.email_id_required'),
                                          'data-parsley-maxlength' => trans('language.max_length'),
                                          'data-parsley-type'      => 'email'
                                        )
                                    ) 
                                  !!}
                                  <span class="dms_error">{{$errors->first('email')}}</span>
                                  <span class="error-msg" id="show-err-msg"></span>
                              </div>
                            </div>
                            <div class="form-group">
                                <label for="Sign In Name" class="col-sm-12 control-label notif_label">{{ trans('language.sign in name') }}: <span class="compulsary">*</span></label>
                                <div class="col-sm-10">
                                  {!! Form:: 
                                  text(
                                  'username',$userData->username, 
                                  array( 
                                  'class'                  => 'form-control', 
                                  'id'                     => 'username', 
                                  'title'                  => "'".trans('language.sign in name')."'".trans('language.length_username'), 
                                  'placeholder'            => 'Sign In Name',
                                  'disabled'               => true
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
                            if(isset($reportsTo->id)) {
                            ?>
                            <div class="form-group">
                                <label for="report_to" class="col-sm-12 control-label notif_label">Report to: </label>
                                <div class="col-sm-10">
                                  {!! Form:: 
                                  text(
                                  'report_to_user',$reportsTo->user_full_name.' '.$reportsTo->user_role, 
                                  array( 
                                  'class'                  => 'form-control', 
                                  'id'                     => 'report_to_user', 
                                  'placeholder'            => 'Report to',
                                  'disabled'               => true
                                  )
                                  ) 
                                  !!}
                                  <p class="help">{{ trans('language.users_with_roles') }}</p>
                                </div>

                            </div>
                            <input type="hidden" name="report_to" value="<?php echo $userData->report_to; ?>">
                            <?php } else { ?>
                            <input type="hidden" name="report_to" value="<?php echo $userData->report_to; ?>">
                            <?php } ?>
                            
                            <div class="form-group">
                            <label for="" class="col-sm-12 control-label">Workflow Delegate To: </label>

                              <div class="col-sm-10">
                                 <select class="form-control report_to" id="delegate_user"   name="delegate_user">
                                    <option value="">{{ trans('language.select_user') }}</option>
                                       <?php
                                        foreach ($users as $key => $user) {
                                            ?>
                                            <option value="<?php echo $user->id; ?>" <?php if($user->id==$userData->delegate_user) { echo 'selected'; } ?>><?php echo ucfirst(@$user->user_full_name);?> -{{@$user->user_role}}@if(@$user->departments[0]->department_name != "")- {{ ucfirst(@$user->departments[0]->department_name) }} @endif</option>
                                            <?php
                                        }
                                        ?> 
                                  </select>
                                  <span class="error-msg" id="delegate_user-err-msg"></span>
                                    <p class="help">{{ trans('language.users_with_roles') }}</p>
                                   <!-- <p class="help">{{$language['form_help2']}}</p> -->
                              </div>
                          </div>
                          <div class="form-group" id="delegate_date_div" <?php if(!isset($userData->delegate_user)) { echo "style='display:none;'"; } ?>>
                            <label for="Sign In Expiry Date" class="col-sm-12 control-label" id="delegateDate">Delegate From Date: <span class="compulsary">*</span></label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                        <input class="form-control active" id="delegate_period" name="delegate_period"  data-original-title="" title=""  data-parsley-required-message="Delegate date period is required" type="text"
                        value="<?php if(isset($userData->delegate_from_date) && isset($userData->delegate_to_date)) { echo $userData->delegate_from_date.'_'.$userData->delegate_to_date; } ?>"
                        <?php if(isset($userData->delegate_user)) { echo "required='required;'"; } ?>>
                                </div><!-- /.input group -->
                            </div>
                            <!--Show message-->
                            
                           
                        </div>
                           <!-- Change password-->
                            @if($logged_in_userId == $userData->id or $adminUserRole == 1)
                              <div class="form-group">
                                  <div class="col-sm-12"></div>
                                  <div class="col-sm-10">
                                    {!! Form::checkbox('pwd_edit',1, false, ['id'=> 'pwd_edit']) !!} Edit password
                                </div>
                              </div>
                            @endif

                            <div id="edpwd" style="display: none;">

                              @if($logged_in_userId == $userData->id)
                                <div class="form-group">
                                    <label for="{{ trans('language.curpassword') }}" class="col-sm-12 control-label notif_label">{{ trans('language.curpassword') }}: <span class="compulsary">*</span></label>
                                    <div class="col-sm-10">
                                      {!! Form::password('current_password',
                                       ['placeholder' => trans('language.curpassword'),
                                        'class' => 'form-control',
                                        'id'=> 'current_password',
                                        'data-parsley-required-message' => trans('language.current_pswd_is_required') ]) !!}                   
                                                
                                    </div>
                                </div>
                              @endif

                              <div id="ttips">
                                
                              </div>


                              <div class="form-group">
                                  <label for="Password" class="col-sm-12 control-label notif_label">{{ trans('language.password') }}: <span class="compulsary">*</span></label>
                                  <div class="col-sm-10">
                                      <?php echo Form:: 
                                          password('password', 
                                              array( 
                                                  'class'                  => 'form-control password1', 
                                                  'id'                     => 'password', 
                                                  'placeholder'            => ''.trans('language.password').'',
                                                  'this_val'                 => '1',
                                                  'data-parsley-minlength' => $records->settings_password_length_from,
                                                  'data-parsley-maxlength' => $records->settings_password_length_to,
                                                  'data-parsley-required-message' => trans('language.password_is_required')
                                              )
                                          ) 
                                    ;?>                    
                                    <span class="dms_error">{{$errors->first('password')}}</span>
                                    <!--password complexity errors-->
                                    <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="space_error1"></li></ul>
                                    <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="complexity_error_1alphabets"></li></ul>
                                    <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="complexity_error_1numerics"></li></ul>
                                    <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="complexity_error_1special_characters"></li></ul>
                                    <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="complexity_error_1capital_and_small"></li></ul>                                   
                                    <!--password length error-->
                                    <span class="password_length_error">
                                      <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="password_length_from_error_1"></li></ul>
                                      <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="password_length_to_error_1"></li></ul>
                                    </span>
                                  </div>      
                                  <!--Complexity message-->
                                  <span class="formInfo"><a href="{{url('getsecuritySettings')}}?id=<?php echo $userData->id;?>" class="jTip" id="one" name="Password must follow these rules:">?</a></span>
                              </div>

                                <div class="form-group">
                                    <label for="Confirm Password" class="col-sm-12 control-label notif_label">Confirm Password: <span class="compulsary">*</span></label>
                                    <div class="col-sm-10">
                                      {!! Form::password('confirm_password', ['placeholder' => 'Password Confirm', 'class' => 'form-control password2', 'this_val'=>'2' , 'id'=> 'confirm_password','data-parsley-equalto'=> '#password','data-parsley-required-message' => trans('language.confirm_psw_is_required'),'data-parsley-minlength' => $records->settings_password_length_from,'data-parsley-maxlength' => $records->settings_password_length_to]) !!}                   
                                          
                                    <span class="dms_error">{{$errors->first('password')}}</span>
                                    <!--password complexity errors-->
                                    <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="space_error2"></li></ul>
                                    <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="complexity_error_2alphabets"></li></ul>
                                    <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="complexity_error_2numerics"></li></ul>
                                    <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="complexity_error_2special_characters"></li></ul>
                                    <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="complexity_error_2capital_and_small"></li></ul><!--password complexity errors end-->   
                                    <!--password length error-->
                                    <span class="password_length_error">
                                      <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="password_length_from_error_2"></li></ul>
                                      <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="password_length_to_error_2"></li></ul>
                                    </span>
                                    </div>
                                  <!--Complexity message-->
                                  <span class="formInfo"><a href="{{url('getsecuritySettings')}}?id=<?php echo $userData->id;?>" class="jTip" id="one" name="Password must follow these rules:">?</a></span>
                                </div>
                              </div>
                            <div id="unlck">
                                <?php 
                                if(($userData->user_lock_status == Session::get('user_lock_status_Locked')) && ($userData->user_role == Session::get('user_role_super_admin'))){ ?>
                                    <div class="form-group">
                                        <label for="unlock account" class="col-sm-12 control-label notif_label"></label>
                                        <div class="col-sm-10">
                                            <a href="javascript:void(0);" onclick="unlock({{ $userData->id}},'{{$userData->username }}')"><li class="fa fa-lock" ></li> Unlock Account</a>            
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>   
                            <div id="bs" style="height:35px; display:none;">
                                <label for="preloader" class="col-sm-3 control-label notif_label"></label>
                                <div class="col-sm-4" style="padding-left:5px;">
                                    <i class="fa fa-spinner fa-pulse fa-fw"></i>
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                            <?php
                            if($userData->user_role == Session::get('user_role_group_admin') or $userData->user_role == Session::get('user_role_regular_user') ) {
                              ?>


                                @if(Auth::user()->user_role != Session::get('user_role_regular_user'))
                                <!--Department Admin-->
                                    <div class="form-group">
                                        <label for="User Type" class="col-sm-12 control-label notif_label">User Type: <span class="compulsary">*</span></label>
                                        <div class="col-sm-10">
                                            @if(Auth::user()->user_role == Session::get('user_role_super_admin'))
                                                <div class="col-sm-4">
                                                    <input type="radio" name="user_type" id="super_admin" class="rd"  value="1" <?php if($userData->user_role == Session::get('user_role_super_admin')) echo 'checked'; ?> >{{trans('language.super admin')}}
                                                </div>
                                                <div class="col-sm-4">
                                                    <input type="radio" name="user_type" id="group_admin" class="rd"  value="2" <?php if($userData->user_role == Session::get('user_role_group_admin')) echo 'checked'; ?> >{{trans('language.group admin')}}
                                                </div>
                                                <div class="col-sm-4">
                                                    <input type="radio" name="user_type" id="regular_user" class="rd"  value="3" <?php if($userData->user_role == Session::get('user_role_regular_user')) echo 'checked'; ?> >{{trans('language.regular user')}}
                                                    @elseif(Auth::user()->user_role == Session::get('user_role_group_admin'))
                                                    <input type="radio" name="user_type" id="group_admin" class="rd"  value="2" <?php if($userData->user_role == Session::get('user_role_group_admin')) echo 'checked'; ?> >{{trans('language.group admin')}}
                                               
                                                    <input type="radio" name="user_type" id="regular_user" class="rd"  value="3" <?php if($userData->user_role == Session::get('user_role_regular_user')) echo 'checked'; ?> >{{trans('language.regular user')}}  
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                

                                    <div id="spl_div_edi">
                                        <div class="form-group">
                                            <label for="Departments" class="col-sm-12 control-label notif_label">@if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{trans('language.departments')}} @endif: <span class="compulsary">*</span></label>
                                            <div class="col-sm-12"><p>
                                            <?php
                                              $stringSearch = substr_count($userData->department_id, ',');
                                              if($stringSearch > 0){
                                                $str= explode(",",$userData->department_id);
                                                foreach ($departments as $key => $val) { ?> 
                                                    <input type="checkbox" required data-parsley-required-message ="@if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{trans('language.department')}} @endif {{trans('language.is_required')}}"  name="document_group[]" data-parsley-mincheck= '1'  value="<?php echo $val->department_id; ?>" <?php if(in_array($val->department_id,$str)) echo 'checked';?> > <?php echo $val->department_name; ?>
                                                <?php } 

                                              } else {

                                                  foreach ($departments as $key => $val) { ?>
                                                    <input type="checkbox" required required data-parsley-required-message ="@if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{trans('language.department')}} @endif {{trans('language.is_required')}}" name="document_group[]" data-parsley-mincheck= '1'  value="<?php echo $val->department_id; ?>" <?php if($val->department_id == $userData->department_id) echo 'checked'; ?>> <?php echo $val->department_name; ?> &nbsp; 
                                                    <?php
                                                  }

                                              }       
                                              ?>
                                              </p>
                                          </div>
                                      </div>
                                  </div>

                                  <!--Prevelages Form :: User Access, Form Access, Workflow Access-->
                                  <input type="hidden" value="{{Request::segment(2)}}" id="userId">
                                   <input type="hidden" value="{{$userData->user_role}}" id="usrRol">
                                    <div class="form-group">
                                        <label for="{{trans('language.privileges')}}" class="col-sm-12 control-label notif_label">{{trans('language.privileges')}} : <span class="compulsary">*</span></label>
                                        <div class="col-sm-10">
                                        <?php
                                        if($userData->user_role == Session::get('user_role_group_admin')) {
                                        ?> 
                                            <input required="" data-parsley-required-message="{{trans('language.user_access_privileges_is_required')}}" type="checkbox" name="user_permission" class="usr_permission" id="eadd" data-parsley-mincheck= '1'  value="add" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?> > Add &nbsp;&nbsp;
                                            <input type="checkbox" name="user_permission[]" class="usr_permission" id="eedit" value="edit" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> {{trans('language.edit')}} &nbsp;&nbsp;
                                            <input type="checkbox" name="user_permission[]" class="usr_permission" id="eview" value="view" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> View &nbsp;&nbsp;
                                            <input type="checkbox" name="user_permission[]" class="usr_permission" id="edelete" value="delete" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> Delete &nbsp;&nbsp;
                                            <input type="checkbox" name="user_permission[]" class="usr_permission" id="echeckout" value="checkout" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> {{trans('language.checkinout')}} &nbsp;&nbsp;
                                            <input type="checkbox" name="user_permission[]" class="usr_permission" id="eimport" value="import" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> {{trans('language.import')}} &nbsp;&nbsp;
                                            <input type="checkbox" name="user_permission[]" class="usr_permission" id="eexport" value="export" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> {{trans('language.export')}} 
                                            &nbsp;&nbsp;
                                            <input type="checkbox" name="user_permission[]" class="usr_permission" id="eworkflow" value="workflow" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> Workflow
                                            &nbsp;&nbsp;
                                            <input type="checkbox" name="user_permission[]" class="usr_permission" id="edecrypt" value="decrypt" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> Decrypt
                                            &nbsp;&nbsp;
                                            <input type="checkbox" name="all" id="es_all" value="all" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> Select All 
                                                     
                                            <?php
                                        } else {
                                            $usrPrm= explode(",",$userData->user_permission);
                                            $cnt= count($usrPrm);
                                            if($cnt)
                                            {
                                          ?>
                                              <input required="" data-parsley-required-message="{{trans('language.user_access_privileges_is_required')}}" type="checkbox" name="user_permission[]" class="usr_permission" id="eadd" data-parsley-mincheck="1" value="add" <?php if(in_array('add', $usrPrm)) echo 'checked'; ?>> Add&nbsp;&nbsp;
                                              <input type="checkbox" name="user_permission[]" class="usr_permission" id="eedit" value="edit" <?php if(in_array('edit', $usrPrm)) echo 'checked'; ?> > {{trans('language.edit')}}&nbsp;&nbsp;
                                              <input type="checkbox" name="user_permission[]" class="usr_permission" id="eview" value="view" <?php if(in_array('view', $usrPrm)) echo 'checked'; ?> > View&nbsp;&nbsp;
                                              <input type="checkbox" name="user_permission[]" class="usr_permission" id="edelete" value="delete" <?php if(in_array('delete', $usrPrm)) echo 'checked'; ?> > Delete&nbsp;&nbsp;
                                              <input type="checkbox" name="user_permission[]" class="usr_permission" id="echeckout" value="checkout" <?php if(in_array('checkout', $usrPrm)) echo 'checked'; ?>> {{trans('language.checkinout')}}&nbsp;&nbsp;
                                              <input type="checkbox" name="user_permission[]" class="usr_permission" id="eimport" value="import" <?php if(in_array('import', $usrPrm)) echo 'checked'; ?>> {{trans('language.import')}}&nbsp;&nbsp;
                                              <input type="checkbox" name="user_permission[]" class="usr_permission" id="eexport" value="export" <?php if(in_array('export', $usrPrm)) echo 'checked'; ?>> {{trans('language.export')}}
                                              &nbsp;&nbsp;
                                              <input type="checkbox" name="user_permission[]" class="usr_permission" id="eworkflow" value="workflow" <?php if(in_array('workflow', $usrPrm)) echo 'checked'; ?>> {{trans('language.workflow')}}
                                              &nbsp;&nbsp;
                                              <input type="checkbox" name="user_permission[]" class="usr_permission" id="edecrypt" value="decrypt" <?php if(in_array('decrypt', $usrPrm)) echo 'checked'; ?>> {{trans('language.decrypt')}}
                                              &nbsp;&nbsp;
                                              <input type="checkbox" name="all" id="es_all" value="all" <?php if($cnt == 9) echo 'checked'; ?>> Select All     
                                                        
                                          <?php
                                          }
                                      }
                                      ?>
                                      </div>
                                  </div>
                                  <div class="form-group">
                                        <label for="{{trans('language.form_privileges')}}" class="col-sm-12 control-label notif_label">{{trans('language.form_privileges')}} : <span class="compulsary">*</span></label>
                                        <div class="col-sm-10">
                                        <?php
                                        if($userData->user_role == Session::get('user_role_group_admin')) {
                                        ?> 
                                        <div class="col-sm-4">
                                            <input required="" data-parsley-required-message="{{trans('language.user_access_privileges_is_required')}}" type="checkbox" name="privileges_frm[]" class="frm_privileges" id="eaddFrm" data-parsley-mincheck= '1'  value="add" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?> > Add &nbsp;&nbsp;
                                        </div>
                                        <div class="col-sm-4">
                                            <input type="checkbox" name="privileges_frm[]" class="frm_privileges" id="eeditFrm" value="edit" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> {{trans('language.edit')}} &nbsp;&nbsp;
                                        </div>
                                        <div class="col-sm-4">
                                            <input type="checkbox" name="privileges_frm[]" class="frm_privileges" id="eviewFrm" value="view" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> View &nbsp;&nbsp;
                                        </div>
                                        <div class="col-sm-4">
                                            <input type="checkbox" name="privileges_frm[]" class="frm_privileges" id="edeleteFrm" value="delete" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> Delete &nbsp;&nbsp;
                                        </div>
                                        <div class="col-sm-4">
                                            <input type="checkbox" name="privileges_frm[]" class="frm_privileges" id="eexportFrm" value="export" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> {{trans('language.export')}} 
                                        </div>
                                        <div class="col-sm-4">                                            
                                            <input type="checkbox" name="s_all_frm" id="s_all_frm" value="all" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> Select All 
                                        </div>
                                            <?php
                                        } else {
                                            $usrPrmFrm= explode(",",$userData->user_form_permission);
                                            $cnt= count($usrPrmFrm);
                                            if($cnt)
                                            {
                                          ?>
                                          <div class="col-sm-4">
                                              <input required="" data-parsley-required-message="{{trans('language.user_access_privileges_is_required')}}" type="checkbox" name="privileges_frm[]" class="frm_privileges" id="eaddFrm" data-parsley-mincheck="1" value="add" <?php if(in_array('add', $usrPrmFrm)) echo 'checked'; ?>> Add&nbsp;&nbsp;
                                            </div>
                                            <div class="col-sm-4">
                                              <input type="checkbox" name="privileges_frm[]" class="frm_privileges" id="eeditFrm" value="edit" <?php if(in_array('edit', $usrPrmFrm)) echo 'checked'; ?> > {{trans('language.edit')}}&nbsp;&nbsp;
                                            </div>
                                            <div class="col-sm-4">
                                              <input type="checkbox" name="privileges_frm[]" class="frm_privileges" id="eviewFrm" value="view" <?php if(in_array('view', $usrPrmFrm)) echo 'checked'; ?> > View&nbsp;&nbsp;
                                            </div>
                                            <div class="col-sm-4">
                                              <input type="checkbox" name="privileges_frm[]" class="frm_privileges" id="edeleteFrm" value="delete" <?php if(in_array('delete', $usrPrmFrm)) echo 'checked'; ?> > Delete&nbsp;&nbsp;
                                            </div> 
                                            <div class="col-sm-4">
                                              <input type="checkbox" name="privileges_frm[]" class="frm_privileges" id="eexportFrm" value="export" <?php if(in_array('export', $usrPrmFrm)) echo 'checked'; ?>> {{trans('language.export')}}
                                            </div>
                                            <div class="col-sm-4">
                                              <input type="checkbox" name="s_all_frm" id="s_all_frm" value="all" <?php if($cnt == 5) echo 'checked'; ?>> Select All     
                                            </div>
                                          <?php
                                          }
                                      }
                                      ?>
                                      </div>
                                  </div>

                                  <div class="form-group">
                                        <label for="{{trans('language.workflow_privileges')}}" class="col-sm-12 control-label notif_label">{{trans('language.workflow_privileges')}} : <span class="compulsary">*</span></label>
                                        <div class="col-sm-10">
                                        <?php
                                        if($userData->user_role == Session::get('user_role_group_admin')) {
                                        ?> 
                                            <input required="" data-parsley-required-message="{{trans('language.user_access_privileges_is_required')}}" type="checkbox" name="privileges_wf[]" class="wf_previlages" id="eaddWrkFlw" data-parsley-mincheck= '1'  value="add" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?> > Add &nbsp;&nbsp;
                                            <input type="checkbox" name="privileges_wf[]" class="wf_previlages" id="eeditWrkFlw" value="edit" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> {{trans('language.edit')}} &nbsp;&nbsp;
                                            <input type="checkbox" name="privileges_wf[]" class="wf_previlages" id="eviewWrkFlw" value="view" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> View &nbsp;&nbsp;
                                            <input type="checkbox" name="privileges_wf[]" class="wf_previlages" id="edeleteWrkFlw" value="delete" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> Delete &nbsp;&nbsp;
                                            
                                            
                                           
                                            
                                            <input type="checkbox" name="s_all_wf" id="s_all_wf" value="all" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> Select All 
                                                     
                                            <?php
                                        } else {
                                            $usrPrmWrkflw= explode(",",$userData->user_workflow_permission);
                                            $cnt= count($usrPrmWrkflw);
                                            if($cnt)
                                            {
                                          ?>
                                              <input required="" data-parsley-required-message="{{trans('language.user_access_privileges_is_required')}}" type="checkbox" name="privileges_wf[]" class="wf_previlages" id="eaddWrkFlw" data-parsley-mincheck="1" value="add" <?php if(in_array('add', $usrPrmWrkflw)) echo 'checked'; ?>> Add&nbsp;&nbsp;
                                              <input type="checkbox" name="privileges_wf[]" class="wf_previlages" id="eeditWrkFlw" value="edit" <?php if(in_array('edit', $usrPrmWrkflw)) echo 'checked'; ?> > {{trans('language.edit')}}&nbsp;&nbsp;
                                              <input type="checkbox" name="privileges_wf[]" class="wf_previlages" id="eviewWrkFlw" value="view" <?php if(in_array('view', $usrPrmWrkflw)) echo 'checked'; ?> > View&nbsp;&nbsp;
                                              <input type="checkbox" name="privileges_wf[]" class="wf_previlages" id="edeleteWrkFlw" value="delete" <?php if(in_array('delete', $usrPrmWrkflw)) echo 'checked'; ?> > Delete&nbsp;&nbsp;
                                             
                                              
                                             
                                              <input type="checkbox" name="s_all_wf" id="s_all_wf" value="all" <?php if($cnt == 4) echo 'checked'; ?>> Select All     
                                                        
                                          <?php
                                          }
                                      }
                                      ?>
                                      </div>
                                  </div>


                                  @if($userData->user_login_expiry)
                                    <div class="form-group">
                                      <label for="Sign In Expiry Date" class="col-sm-12 control-label notif_label">{{trans('language.expiry_date')}}: </label>
                                      <div class="col-sm-10">
                                          <div >
                                            @if($userData->user_login_expiry == '0000-00-00')
                                              No Expire
                                            @else
                                              {{$userData->user_login_expiry}}
                                            @endif
                                          </div>
                                    
                                      </div>
                                    </div>
                                  @endif
                                
                              @else
                              <!--Regular User-->
                                  <div class="form-group">
                                  <input type="hidden" name="user_type" value="3">
                                        <label for="User Type" class="col-sm-12 control-label notif_label">User Type: </label>
                                        <div class="col-sm-10">
                                              <input type="radio"  disabled id="regular_user" class="rd"  value="3" <?php if($userData->user_role == Session::get('user_role_regular_user')) echo 'checked'; ?> >{{trans('language.regular user')}}
                                      </div>
                                  </div>

                                   <div id="spl_div_edi">
                                      <div class="form-group">
                                          <label for="Departments" class="col-sm-12 control-label notif_label">@if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{trans('language.department')}} @endif: </label>
                                          <div class="col-sm-10"><p>
                                          <?php
                                            $stringSearch = substr_count($userData->department_id, ',');
                                            if($stringSearch > 0){
                                              $str= explode(",",$userData->department_id);
                                              foreach ($departments as $key => $val) { ?> 
                                                  <input type="checkbox"  disabled value="<?php echo $val->department_id; ?>" <?php if(in_array($val->department_id,$str)) echo 'checked';?> > <?php echo $val->department_name; ?>
                                              <?php } 

                                            } else {

                                                foreach ($departments as $key => $val) { ?>
                                                  <input type="checkbox"  disabled  value="<?php echo $val->department_id; ?>" <?php if($val->department_id == $userData->department_id) echo 'checked'; ?>> <?php echo $val->department_name; ?> &nbsp; 
                                                  <?php
                                                }

                                            }       
                                            ?>
                                            </p>
                                        </div>
                                    </div>
                                  </div>
                                  <div class="form-group">
                                        <label for="{{trans('language.privileges')}}" class="col-sm-12 control-label notif_label">{{trans('language.privileges')}} :</label>
                                        <div class="col-sm-10">
                                        <?php
                                            $usrPrm= explode(",",$userData->user_permission);
                                            $cnt= count($usrPrm);
                                            if($cnt)
                                            {
                                          ?>
                                              <input type="checkbox" disabled   <?php if(in_array('add', $usrPrm)) echo 'checked'; ?>> Add&nbsp;&nbsp;
                                              <input type="checkbox" disabled   <?php if(in_array('edit', $usrPrm)) echo 'checked'; ?> > {{trans('language.edit')}}&nbsp;&nbsp;
                                              <input type="checkbox" disabled   <?php if(in_array('view', $usrPrm)) echo 'checked'; ?> > View&nbsp;&nbsp;
                                              <input type="checkbox" disabled  <?php if(in_array('delete', $usrPrm)) echo 'checked'; ?> > Delete&nbsp;&nbsp;
                                              <input type="checkbox" disabled  <?php if(in_array('checkout', $usrPrm)) echo 'checked'; ?>> {{trans('language.checkinout')}}&nbsp;&nbsp;
                                              <input type="checkbox" disabled  <?php if(in_array('import', $usrPrm)) echo 'checked'; ?>> {{trans('language.import')}}&nbsp;&nbsp;
                                              <input type="checkbox" disabled  <?php if(in_array('export', $usrPrm)) echo 'checked'; ?>> {{trans('language.export')}}&nbsp;&nbsp;
                                              <input type="checkbox" disabled  <?php if(in_array('workflow', $usrPrm)) echo 'checked'; ?>> {{trans('language.workflow')}}              
                                          <?php
                                          }
                                      ?>
                                      </div>
                                  </div>

                                  @if($userData->user_login_expiry)
                                    <div class="form-group">
                                      <label for="Sign In Expiry Date" class="col-sm-12 control-label notif_label">{{trans('language.expiry_date')}}: </label>
                                      <div class="col-sm-10">
                                          <div style="margin-top: 3%;">
                                            @if($userData->user_login_expiry == '0000-00-00')
                                              No Expire
                                            @else
                                              {{$userData->user_login_expiry}}
                                            @endif
                                          </div>
                                    
                                      </div>
                                    </div>
                                  @endif
                              @endif
                            <?php
                            }        
                            ?> 

                       
                    </div>
                  
            </div>
</div>
<div class="col-md-10">
<div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Email Notifications : </h3> 
                </div><!-- /.box-header -->
                <!--Checking view permission-->
                <?php
                              $minleng  =  $settings_password_length_from;
                              $maxleng  =  $settings_password_length_to;
                            ?>
                  <div class="modal-body">
                  <?php //echo'<pre>'; print_r($userData);echo'</pre>';?>
                  <?php // echo'<pre>'; print_r(Session::all());echo'</pre>';?>
                            <div class="form-group">
                                <label for="{{trans('language.privileges')}}" class="col-sm-12 control-label">{{Lang::get('language.email_notifications')}}: <span class="compulsary">*</span></label>
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <label class="control-label">
                                            <input type="checkbox" <?php if($userData->user_activity_task_notifications == Session::get('user_activity_task_notifications')):echo "checked"; endif; ?> name="user_activity_task_notifications" class="minimal" <?php if(@$emailNotif[0]->email_notification_override_email_notifications_settings==0): ?> disabled <?php endif; ?> > 
                                            {{Lang::get('language.activity_task_notifications')}} </label>
                                        </div> 
                                        <div class="col-sm-12"> 
                                             <p style="padding-left:15px; font-size:12px; color:#999;">{{Lang::get('language.acivity_task_notifications_hlp')}}</p>
                                        </div>
                                    </div>
                                    <div class="row">               
                                        <div class="col-sm-12">
                                            <label class="control-label">
                                            <input type="checkbox" <?php if($userData->user_form_notifications == Session::get('user_form_notifications')):echo "checked";endif;?> name="user_form_notifications" class="minimal" <?php if(@$emailNotif[0]->email_notification_override_email_notifications_settings==0): ?> disabled <?php endif; ?>>
                                            {{Lang::get('language.form_notifications')}} </label>
                                        </div>
                                        <div class="col-sm-12">
                                             <p style="padding-left:15px; font-size:12px; color:#999;">{{Lang::get('language.form_notifications_hlp')}}</p>
                                        </div>
                                    </div>
                                    <div class="row"> 
                                        <div class="col-sm-12">
                                            <label class="control-label">
                                            <input type="checkbox" <?php if($userData->user_document_notifications  == Session::get('user_document_notifications')):echo "checked";endif;?> name="user_document_notifications" class="minimal" <?php if(@$emailNotif[0]->email_notification_override_email_notifications_settings==0): ?> disabled <?php endif; ?>>
                                            {{Lang::get('language.document_notifications')}} </label>
                                        </div>
                                        <div class="col-sm-12">
                                            <p style="padding-left:15px; font-size:12px; color:#999;">{{Lang::get('language.document_notifications_hlp')}}</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <label class="control-label">
                                            <input type="checkbox" <?php if($userData->user_signin_notifications == Session::get('user_signin_notifications')):echo "checked";endif;?> name="user_signin_notifications" class="minimal" <?php if(@$emailNotif[0]->email_notification_override_email_notifications_settings==0): ?> disabled <?php endif; ?>>
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
                                    <input type="hidden" name="stngs_ovrwritepref" value="<?php echo @$emailNotif[0]->email_notification_overwrite_preferences; ?>">                                    

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <label class="control-label">
                                            <input type="checkbox" value="1" name="system_prefer" class="minimal" <?php if(@$emailNotif[0]->email_notification_override_email_notifications_settings==0): ?> disabled <?php endif; if(@$emailNotif[0]->email_notification_overwrite_preferences==0): ?> disabled <?php endif;?>>
                                            {{Lang::get('language.use_system_prefer')}} </label>
                                        </div>                           
                                    </div>

                                </div> 
                            </div>


                            
                

               
</div>
            </div>






                            
             @else 
                      <div class="alert alert-danger alert-sty">{{trans('language.dont_hav_permission')}}</div>
                @endif
        </div> 

        <div class="col-md-12">
                
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('language.notification') }}</h3>
                </div><!-- /.box-header -->
                <!-- form start -->
      

                <div class="box-body">
                  <ul class="todo-list">
                                         
                      <!-- todo text -->
                      @if(Session::get('notification_expire_date'))
                        <?php
                        foreach(Session::get('notification_expire_date') as $val){
                          ?>
                            <li><span class="fa fa-star text-yellow"></span><a href="{{url('/listview?view=list&notification=1')}}"><span class="text"><?php echo $val;?></span></a></li>
                          <?php 
                        }
                        ?>
                        @else
                          {{ trans('language.no_notification') }}
                        @endif
                        <!--Password expiry-->
                        @if(Session::get('password_expire_date'))
                        <li>
                          <span class="fa fa-star text-yellow"></span><?php echo Session::get('password_expire_date');?>
                        </li>
                        @endif              
                  </ul>
                </div><!-- /.box-body -->
       
          
            </div>    
        </div> 
        {!! Form:: hidden('user_role', $userData->user_role ) !!} 
                            <div class="form-group">
                                <label class="col-sm-12 control-label notif_label"></label>
                                <div class="col-sm-10 s-btn">
                                    {!!Form::submit('Save', array('class' => 'btn btn-primary sav_btn')) !!} &nbsp;&nbsp;
                                    <a href="{{URL::route('users')}}" class = "btn btn-primary btn-danger">Cancel</a>
                                </div>
                            </div><!-- /.col -->
              {!! Form::close() !!}    
        </div>

        
    </div>            

@else
<!--Edit all profiles expect own-->
<!-- Without Notification -->
<div class="modal-body"> 
    <!-- form start -->
    {!! Form::open(array('url'=> array('userSave',$userData->id), 'method'=> 'post', 'class'=> 'form-horizontal dms_form', 'name'=> 'userEditForm', 'id'=> 'userEditForm','data-parsley-validate'=> '')) !!}            
        <div class="form-group">
            <label for="Full Name" class="col-sm-2 control-label">{{trans('language.full name')}}:<span class="compulsary">*</span></label>
            <div class="col-sm-8">
                {!! Form:: 
                    text('name',$userData->user_full_name, 
                        array( 
                            'class'=> 'form-control', 
                            'id'=> 'name', 
                            'title'=> "'".trans('language.full name')."'".trans('language.length_others'), 
                            'placeholder'=> 'Full Name',
                            'required'=> '',
                            'data-parsley-required-message' => trans('language.full_name_is_required'),
                            'data-parsley-maxlength' => trans('language.max_length'),
                            'data-parsley-trigger'          => 'change focusout',
                            'autofocus'
                        )
                    ) 
              !!}                   
            </div>
        </div>
        <div class="form-group">
            <label for="Email" class="col-sm-2 control-label">{{trans('language.email_id')}}: <span class="compulsary">*</span></label>
            <div class="col-sm-8">
                {!! Form:: 
                    text(
                        'email',$userData->email, 
                        array( 
                            'class'                  => 'form-control', 
                            'id'                     => 'email', 
                            'title'                  => trans('language.mail_tooltip'), 
                            'placeholder'            => 'Email',
                            'required'               => '',
                            'data-parsley-required-message' => trans('language.email_id_required'),
                            'data-parsley-maxlength' => trans('language.max_length'),
                            'data-parsley-type'      => 'email'
                        )
                    ) 
              !!}
              <span class="dms_error">{{$errors->first('email')}}</span>
              <span class="error-msg" id="show-err-msg"></span>
            </div>
        </div>

        <div class="form-group">
            <label for="Sign In Name" class="col-sm-2 control-label">{{ trans('language.sign in name') }}: <span class="compulsary">*</span></label>
            <div class="col-sm-8">
                {!! Form:: 
                    text(
                        'username',$userData->username, 
                        array( 
                            'class'                  => 'form-control', 
                            'id'                     => 'username', 
                            'title'                  => "'".trans('language.sign in name')."'".trans('language.length_username'), 
                            'placeholder'            => 'Sign In Name',
                            'disabled'               => true
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
        
        <!-- Change password-->
        @if($logged_in_userId == $userData->id or $adminUserRole == 1)
          <div class="form-group">
              <div class="col-sm-2"></div>
              <div class="col-sm-8">
                {!! Form::checkbox('pwd_edit',1, false, ['id'=> 'pwd_edit']) !!} Edit password
            </div>
          </div>
        @endif

        <div id="edpwd" style="display: none;">
            @if($logged_in_userId == $userData->id)
                <div class="form-group">
                    <label for="{{trans('language.curpassword')}}" class="col-sm-2 control-label">{{trans('language.curpassword')}}: <span class="compulsary">*</span></label>
                    <div class="col-sm-8">
                        {!! Form::password('current_password',
                        ['placeholder' => trans('language.curpassword'),
                        'class' => 'form-control',
                        'data-parsley-minlength' => trans('language.min_length_username'),
                        'data-parsley-maxlength' => trans('language.max_length_username'),
                        'id'=> 'current_password',
                        'data-parsley-required-message' => trans('language.current_pswd_is_required') ]) !!}              
                    </div>
                </div>
            @endif
            <div id="ttips">
            </div>

            <div class="form-group">
                <label for="Password" class="col-sm-2 control-label">{{ trans('language.password') }}: <span class="compulsary">*</span></label>
                <div class="col-sm-8">
                    <?php echo Form:: 
                    password('password', 
                        array( 
                            'class'                  => 'form-control password1', 
                            'id'                     => 'password', 
                            'placeholder'            => ''.trans('language.password').'',
                            'this_val'         => '1',
                            'data-parsley-minlength' => $records->settings_password_length_from,
                            'data-parsley-maxlength' => $records->settings_password_length_to,
                            'data-parsley-required-message'=> trans('language.password_is_required')
                        )
                    ) 
                    ;?>                    
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
                <span class="formInfo"><a href="{{url('getsecuritySettings')}}?id=<?php echo $userData->id;?>" class="jTip" id="one" name="Password must follow these rules:">?</a></span>
            </div>

            <div class="form-group">
                <label for="Confirm Password" class="col-sm-2 control-label">Confirm Password: <span class="compulsary">*</span></label>
                <div class="col-sm-8">
                    {!! Form::password('confirm_password', ['placeholder' => 'Password Confirm', 'class' => 'form-control password2', 'this_val'=>'2' , 'id'=> 'confirm_password','data-parsley-equalto'=> '#password','data-parsley-required-message' => trans('language.confirm_psw_is_required'),'data-parsley-minlength' => $records->settings_password_length_from,'data-parsley-maxlength' => $records->settings_password_length_to ]) !!}                   

                    <span class="dms_error">{{$errors->first('password')}}</span>
                        <!--password complexity errors-->
                        <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="space_error2"></li></ul>
                        <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="complexity_error_2alphabets"></li></ul>
                        <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="complexity_error_2numerics"></li></ul>
                        <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="complexity_error_2special_characters"></li></ul>
                        <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="complexity_error_2capital_and_small"></li></ul><!--password complexity errors end-->    
                        <!--password length error-->
                        <span class="password_length_error">
                        <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="password_length_from_error_2"></li></ul>
                        <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="password_length_to_error_2"></li></ul>
                    </span>
                </div>
                <!--Complexity message-->
                <span class="formInfo"><a href="{{url('getsecuritySettings')}}?id=<?php echo $userData->id;?>" class="jTip" id="one" name="Password must follow these rules:">?</a></span>
            </div>
        </div>

        <div id="unlck">
            <?php 
            if(($userData->user_lock_status == Session::get('user_lock_status_Locked')) && ($userData->user_role == Session::get('user_role_super_admin'))){ ?>
                <div class="form-group">
                    <label for="unlock account" class="col-sm-2 control-label"></label>
                    <div class="col-sm-8">
                        <a href="javascript:void(0);" onclick="unlock({{ $userData->id}},'{{$userData->username }}')"><li class="fa fa-lock" ></li> Unlock Account</a>            
                    </div>
                </div>
            <?php } ?>
        </div>  

        <div id="bs" style="height:35px; display:none;">
            <label for="preloader" class="col-sm-3 control-label"></label>
            <div class="col-sm-4" style="padding-left:5px;">
                <i class="fa fa-spinner fa-pulse fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>
        </div>

        <!-- User Status -->
        @if(( Auth::user()->user_role == Session::get('user_role_super_admin') ) || ( Auth::user()->user_role == Session::get('user_role_group_admin') ) )
            <div class="form-group">
                <label for="User Type" class="col-sm-2 control-label">{{ trans('language.user_status') }} <span class="compulsary">*</span></label>
                <div class="col-sm-8">
                    <div class="col-sm-4 nopadinglft">
                        <input type="radio" name="user_status" id="status_active" value="1" <?php if($userData->user_status == Session::get('user_status_Active')) echo 'checked'; ?>> {{ trans('language.active') }}
                    </div>
                    <div class="col-sm-4">  
                        <input type="radio" name="user_status" id="status_inactive" value="0" <?php if($userData->user_status == Session::get('user_status_Inactive')) echo 'checked'; ?>> {{ trans('language.inactive') }}
                    </div>
                </div>
            </div>
        @endif   

        <!-- User Role -->
        <?php 
        if($userData->user_role == Session::get('user_role_group_admin') or $userData->user_role == Session::get('user_role_regular_user') or $userData->user_role == Session::get('user_role_private_user') ) { ?>


            @if(Auth::user()->user_role != Session::get('user_role_regular_user') || Auth::user()->user_role != Session::get('user_role_private_user'))

                <!--This hidden values used for get and update user access privilege when click on private user-->
                <input type="hidden" value="{{Request::segment(2)}}" id="userId">
                <input type="hidden" value="{{$userData->user_role}}" id="usrRol">

                <div class="form-group">
                    <label for="User Type" class="col-sm-2 control-label">User Type: <span class="compulsary">*</span></label>
                    <div class="col-sm-8">
                        @if(Auth::user()->user_role == Session::get('user_role_super_admin'))
                            <div class="col-sm-4 nopadinglft">
                                <input type="radio" name="user_type" id="super_admin" class="rd"  value="1" <?php if($userData->user_role == Session::get('user_role_super_admin')) echo 'checked'; ?> > {{trans('language.super admin')}}
                            </div>
                            <div class="col-sm-4">   
                                <input type="radio" name="user_type" id="group_admin" class="rd"  value="2" <?php if($userData->user_role == Session::get('user_role_group_admin')) echo 'checked'; ?> > {{trans('language.group admin')}}
                            </div>
                            <div class="col-sm-4">
                                <input type="radio" name="user_type" id="regular_user" class="rd"  value="3" <?php if($userData->user_role == Session::get('user_role_regular_user')) echo 'checked'; ?> > {{trans('language.regular user')}} 
                            </div>
                            <div class="col-sm-4 nopadinglft">
                                <input type="radio" name="user_type" id="private_user"  class="rd"  value="4" <?php if($userData->user_role == Session::get('user_role_private_user')) echo 'checked'; ?> > {{trans('language.private_user')}}
                            </div>
                            <div class="col-sm-4">
                                <input type="radio" name="user_type" id="viewonly_user" class="rd"  value="3" <?php if($userData->user_view_only == '1') echo 'checked'; ?> > {{trans('language.viewonly_user')}} 
                        </div>

                        @elseif(Auth::user()->user_role == Session::get('user_role_group_admin')) 
                            <div class="col-sm-4 nopadinglft">
                                <input type="radio" name="user_type" id="group_admin" class="rd"  value="2" <?php if($userData->user_role == Session::get('user_role_group_admin')) echo 'checked'; ?> > {{trans('language.group admin')}}
                            </div>
                            
                            @if(Auth::user()->user_role != $userData->user_role)
                                <div class="col-sm-4">
                                    <input type="radio" name="user_type" id="regular_user" class="rd"  value="3" <?php if($userData->user_role == Session::get('user_role_regular_user')) echo 'checked'; ?> > {{trans('language.regular user')}} 
                                </div>
                                <div class="col-sm-4">
                                    <input type="radio" name="user_type" id="private_user"  class="rd"  value="4" <?php if($userData->user_role == Session::get('user_role_private_user')) echo 'checked'; ?> > {{trans('language.private_user')}}
                                </div>

                                <div class="col-sm-4 nopadinglft">
                                    <input type="radio" name="user_type" id="viewonly_user"  class="rd"  value="3" <?php if($userData->user_view_only == '0') echo 'checked'; ?> > {{trans('language.viewonly_user')}}
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            
                
                <div id="spl_div_edi">
                    <div class="form-group">
                        <label for="Departments" class="col-sm-2 control-label">@if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{trans('language.department')}} @endif: <span class="compulsary">*</span></label>
                        <div class="col-sm-8"><p>
                        <?php
                          $stringSearch = substr_count($userData->department_id, ',');
                          if($stringSearch > 0){
                            $str= explode(",",$userData->department_id);
                            $j =1; 
                            foreach ($departments as $key => $val) { ?> 
                                <div class="col-sm-4 <?php if($j==1){ ?>nopadinglft<?php } ?>">
                                    <input type="checkbox" required data-parsley-required-message ="@if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{trans('language.department')}} @endif {{trans('language.is_required')}}"  name="document_group[]" data-parsley-mincheck= '1'  value="<?php echo $val->department_id; ?>" <?php if(in_array($val->department_id,$str)) echo 'checked';?> > <?php echo $val->department_name; ?>
                                </div>
                            <?php $j++; if($j>3){ $j=1; } } 

                          } else {
                            $j =1; 
                              foreach ($departments as $key => $val) { ?>
                                <div class="col-sm-4 <?php if($j==1){ ?>nopadinglft<?php } ?>">
                                <input type="checkbox" required required data-parsley-required-message ="@if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{trans('language.department')}} @endif {{trans('language.is_required')}}" name="document_group[]" data-parsley-mincheck= '1'  value="<?php echo $val->department_id; ?>" <?php if($val->department_id == $userData->department_id) echo 'checked'; ?>> <?php echo $val->department_name; ?> </div>
                                <?php $j++; if($j>3){ $j=1; }
                              }

                          }       
                          ?>
                          </p>
                      </div>
                    </div>
                </div>
               

                <div class="form-group">
                    <label for="{{trans('language.privileges')}}" class="col-sm-2 control-label">{{trans('language.privileges')}} : <span class="compulsary">*</span></label>
                    <div class="col-sm-8">
                        <?php if($userData->user_role == Session::get('user_role_group_admin')) { ?>  
                            <div class="col-sm-4 nopadinglft">
                                <input required="" data-parsley-required-message="{{trans('language.user_access_privileges_is_required')}}" type="checkbox" name="user_permission" id="eadd" class="usr_permission" data-parsley-mincheck= '1' class="privilege" value="add" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?> > {{trans('language.add')}}
                            </div>
                            <div class="col-sm-4">                        
                                <input type="checkbox" name="user_permission[]" class="usr_permission" class="privilege" id="eedit" value="edit" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> {{trans('language.edit')}}
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" name="user_permission[]" class="usr_permission" class="privilege" id="eview" value="view" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> {{trans('language.view')}}
                            </div>
                            <div class="col-sm-4 nopadinglft">
                                <input type="checkbox" name="user_permission[]" class="usr_permission" class="privilege" id="edelete" value="delete" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> {{trans('language.delete')}} 
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" name="user_permission[]" class="usr_permission" class="privilege" id="echeckout" value="checkout" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> {{trans('language.checkinout')}}
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" name="user_permission[]" class="usr_permission" class="privilege" id="eimport" value="import" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> {{trans('language.import')}}
                            </div>
                            <div class="col-sm-4 nopadinglft">
                                <input type="checkbox" name="user_permission[]" class="usr_permission" class="privilege" id="eexport" value="export" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> {{trans('language.export')}}
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" name="user_permission[]" class="usr_permission" class="privilege" id="eworkflow" value="workflow" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> {{trans('language.workflow')}} 
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" name="user_permission[]" class="usr_permission" class="privilege" id="edecrypt" value="decrypt" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> {{trans('language.decrypt')}} 
                            </div>
                            <div class="col-sm-4 nopadinglft">
                                <input type="checkbox" name="all" id="es_all" value="all" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> {{trans('language.select_all')}}   
                            </div>               
                            <?php
                        } else {
                            $usrPrm= explode(",",$userData->user_permission);
                            $cnt= count($usrPrm);
                            if($cnt){ ?>  
                                <div class="col-sm-4 nopadinglft">
                                    <input required="" data-parsley-required-message="{{trans('language.user_access_privileges_is_required')}}" type="checkbox" name="user_permission[]" class="usr_permission" id="eadd" data-parsley-mincheck="1" value="add" <?php if(in_array('add', $usrPrm)) echo 'checked'; ?>> {{trans('language.add')}}
                                </div>
                                <div class="col-sm-4">
                                    <input type="checkbox" name="user_permission[]" class="usr_permission" id="eedit" value="edit" <?php if(in_array('edit', $usrPrm)) echo 'checked'; ?> > {{trans('language.edit')}}
                                </div>
                                <div class="col-sm-4">
                                    <input type="checkbox" name="user_permission[]" class="usr_permission" id="eview" value="view" <?php if(in_array('view', $usrPrm)) echo 'checked'; ?> > {{trans('language.view')}}
                                </div>
                                <div class="col-sm-4 nopadinglft">
                                    <input type="checkbox" name="user_permission[]" class="usr_permission" id="edelete" value="delete" <?php if(in_array('delete', $usrPrm)) echo 'checked'; ?> > {{trans('language.delete')}}
                                </div>
                                <div class="col-sm-4">
                                    <input type="checkbox" name="user_permission[]" class="usr_permission" id="echeckout" value="checkout" <?php if(in_array('checkout', $usrPrm)) echo 'checked'; ?>> {{trans('language.checkinout')}}
                                </div>
                                <div class="col-sm-4">
                                    <input type="checkbox" name="user_permission[]" class="usr_permission" id="eimport" value="import" <?php if(in_array('import', $usrPrm)) echo 'checked'; ?>> {{trans('language.import')}}
                                </div>
                                <div class="col-sm-4 nopadinglft">
                                    <input type="checkbox" name="user_permission[]" class="usr_permission" id="eexport" value="export" <?php if(in_array('export', $usrPrm)) echo 'checked'; ?>> {{trans('language.export')}}
                                </div>
                                <div class="col-sm-4">
                                    <input type="checkbox" name="user_permission[]" class="usr_permission" id="eworkflow" value="workflow" <?php if(in_array('workflow', $usrPrm)) echo 'checked'; ?>> {{trans('language.workflow')}}
                                </div>
                                <div class="col-sm-4">
                                    <input type="checkbox" name="user_permission[]" class="usr_permission" id="edecrypt" value="decrypt" <?php if(in_array('decrypt', $usrPrm)) echo 'checked'; ?>> {{trans('language.decrypt')}}
                                </div>
                                <div class="col-sm-4 nopadinglft">
                                    <input type="checkbox" name="all" id="es_all" value="all" <?php if($cnt == 9) echo 'checked'; ?>> {{trans('language.select_all')}}
                                </div>                  
                            <?php } 
                        } ?>
                  </div>
              </div>

              <!--Start Form & WorkFlow Access Privilages :: Bibin -->

              <!-- Start Form Access Privilages -->
              <div class="form-group">
                    <label for="{{trans('language.privileges')}}" class="col-sm-2 control-label">{{trans('language.form_privileges')}} : </label>
                    <div class="col-sm-8">
                    <?php
                    if($userData->user_role == Session::get('user_role_group_admin')) {
                    ?>  
                    <div class="col-sm-4 nopadinglft">
                        <input required="" data-parsley-required-message="{{trans('language.form_access_privileges_is_required')}}" type="checkbox" name="privileges_frm[]" class="frm_privileges" id="eaddFrm" data-parsley-mincheck= '1' class="privilege" value="add" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?> > {{trans('language.add')}} 
                    </div>
                    <div class="col-sm-4">                        
                        <input type="checkbox" name="privileges_frm[]" class="frm_privileges" class="privilege" id="eeditFrm" value="edit" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> {{trans('language.edit')}}
                    </div>
                    <div class="col-sm-4">
                        <input type="checkbox" name="privileges_frm[]" class="frm_privileges" class="privilege" id="eviewFrm" value="view" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> {{trans('language.view')}} 
                    </div>
                    <div class="col-sm-4 nopadinglft">
                        <input type="checkbox" name="privileges_frm[]" class="frm_privileges" class="privilege" id="edeleteFrm" value="delete" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> {{trans('language.delete')}} 
                    </div>
                    <div class="col-sm-4">
                        <input type="checkbox" name="privileges_frm[]" class="frm_privileges" class="privilege" id="eexportFrm" value="export" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> {{trans('language.export')}} 
                    </div>
                    <div class="col-sm-4">
                        <input type="checkbox" name="s_all_frm" id="s_all_frm" value="all" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> {{trans('language.select_all')}} 
                    </div>               
                        <?php
                    } else {
                        $usrPrmFrm= explode(",",$userData->user_form_permission);
                        $cnt= count($usrPrmFrm);
                        if($cnt)
                        {
                      ?>  
                      <div class="col-sm-4 nopadinglft">
                          <input type="checkbox" name="privileges_frm[]" class="frm_privileges" id="eaddFrm" data-parsley-mincheck="1" value="add" <?php if(in_array('add', $usrPrmFrm)) echo 'checked'; ?>> {{trans('language.add')}}
                        </div>
                       <div class="col-sm-4">
                          <input type="checkbox" name="privileges_frm[]" class="frm_privileges" id="eeditFrm" value="edit" <?php if(in_array('edit', $usrPrmFrm)) echo 'checked'; ?> > {{trans('language.edit')}}
                        </div>
                        <div class="col-sm-4">
                          <input type="checkbox" name="privileges_frm[]" class="frm_privileges" id="eviewFrm" value="view" <?php if(in_array('view', $usrPrmFrm)) echo 'checked'; ?> > {{trans('language.view')}}
                        </div>
                        <div class="col-sm-4 nopadinglft">
                          <input type="checkbox" name="privileges_frm[]" class="frm_privileges" id="edeleteFrm" value="delete" <?php if(in_array('delete', $usrPrmFrm)) echo 'checked'; ?> > {{trans('language.delete')}}
                        </div>
                        <div class="col-sm-4">  
                          <input type="checkbox" name="privileges_frm[]" class="frm_privileges" id="eexportFrm" value="export" <?php if(in_array('export', $usrPrmFrm)) echo 'checked'; ?>> {{trans('language.export')}}
                        </div>
                         <div class="col-sm-4"> 
                          <input type="checkbox" name="s_all_frm" id="s_all_frm" value="all" <?php if($cnt == 5) echo 'checked'; ?>> {{trans('language.select_all')}}   
                        </div>               
                      <?php
                      }
                  }
                  ?>
                  </div>
              </div>
              <!-- End Form Access Privilages -->

              <!-- Start Work Flow Access Privilages -->
              <div class="form-group">
                    <label for="{{trans('language.privileges')}}" class="col-sm-2 control-label">{{trans('language.workflow_privileges')}} : </label>
                    <div class="col-sm-8">
                    <?php
                    if($userData->user_role == Session::get('user_role_group_admin')) {
                    ?>  
                    <div class="col-sm-4 nopadinglft">
                        <input required="" data-parsley-required-message="{{trans('language.wf_access_privileges_is_required')}}" type="checkbox" name="privileges_wf[]" id="eaddWrkFlw" class="wf_previlages" data-parsley-mincheck= '1' class="privilege" value="add" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?> > {{trans('language.add')}} 
                    </div>
                    <div class="col-sm-4">
                        <input type="checkbox" name="privileges_wf[]" class="wf_previlages" class="privilege" id="eeditWrkFlw" value="edit" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> {{trans('language.edit')}} 
                    </div>
                    <div class="col-sm-4">
                        <input type="checkbox" name="privileges_wf[]" class="wf_previlages" class="privilege" id="eviewWrkFlw" value="view" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> {{trans('language.view')}}
                    </div>
                    <div class="col-sm-4 nopadinglft">
                        <input type="checkbox" name="privileges_wf[]" class="wf_previlages" class="privilege" id="edeleteWrkFlw" value="delete" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  required <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> {{trans('language.delete')}} 
                    </div>
                    <div class="col-sm-4">
                        <input type="checkbox" name="s_all_wf" id="s_all_wf" value="all" <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'checked'; ?>  <?php if($userData->user_role == Session::get('user_role_super_admin') or $userData->user_role == Session::get('user_role_group_admin') ) echo 'disabled'; ?>> {{trans('language.select_all')}} 
                    </div>                 
                        <?php
                    } else {
                        $usrPrmWf= explode(",",$userData->user_workflow_permission);
                        $cnt= count($usrPrmWf);
                        if($cnt)
                        {
                      ?>  
                      <div class="col-sm-4 nopadinglft">
                          <input type="checkbox" name="privileges_wf[]" id="eaddWrkFlw" class="wf_previlages" data-parsley-mincheck="1" value="add" <?php if(in_array('add', $usrPrmWf)) echo 'checked'; ?>> {{trans('language.add')}}
                        </div>
                        <div class="col-sm-4">
                          <input type="checkbox" name="privileges_wf[]" class="wf_previlages" id="eeditWrkFlw" value="edit" <?php if(in_array('edit', $usrPrmWf)) echo 'checked'; ?> > {{trans('language.edit')}}
                        </div>
                        <div class="col-sm-4">
                          <input type="checkbox" name="privileges_wf[]" class="wf_previlages" id="eviewWrkFlw" value="view" <?php if(in_array('view', $usrPrmWf)) echo 'checked'; ?> > {{trans('language.view')}}
                        </div>
                        <div class="col-sm-4 nopadinglft">
                          <input type="checkbox" name="privileges_wf[]" class="wf_previlages" id="edeleteWrkFlw" value="delete" <?php if(in_array('delete', $usrPrmWf)) echo 'checked'; ?> > {{trans('language.delete')}}
                        </div>
                        <div class="col-sm-4">
                          <input type="checkbox" name="s_all_wf" id="s_all_wf" value="all" <?php if($cnt == 4) echo 'checked'; ?>> {{trans('language.select_all')}}            
                        </div>
                      <?php
                      }
                  }
                  ?>
                  </div>
              </div>
              <!-- End Work Flow Access Privilages -->

              <!--End Form & WorkFlow Access Privilages -->

              <div class="form-group">
                                <label for="{{trans('language.privileges')}}" class="col-sm-2 control-label">{{Lang::get('language.email_notifications')}}: <span class="compulsary">*</span></label>
                                <div class="col-sm-9">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <label class="control-label">
                                            <input type="checkbox" <?php if($userData->user_activity_task_notifications == Session::get('user_activity_task_notifications')):echo "checked"; endif; ?> name="user_activity_task_notifications" class="minimal" <?php if(@$emailNotif[0]->email_notification_override_email_notifications_settings==0): ?> disabled <?php endif; if(@$emailNotif[0]->email_notification_overwrite_preferences==0): ?> disabled <?php endif; ?> > 
                                            {{Lang::get('language.activity_task_notifications')}} </label>
                                        </div> 
                                        <div class="col-sm-12"> 
                                             <p style="padding-left:15px; font-size:12px; color:#999;">{{Lang::get('language.acivity_task_notifications_hlp')}}</p>
                                        </div>
                                    </div>
                                    <div class="row">               
                                        <div class="col-sm-12">
                                            <label class="control-label">
                                            <input type="checkbox" <?php if($userData->user_form_notifications == Session::get('user_form_notifications')):echo "checked";endif;?> name="user_form_notifications" class="minimal" <?php if(@$emailNotif[0]->email_notification_override_email_notifications_settings==0): ?> disabled <?php endif; if(@$emailNotif[0]->email_notification_overwrite_preferences==0): ?> disabled <?php endif;?>>
                                            {{Lang::get('language.form_notifications')}} </label>
                                        </div>
                                        <div class="col-sm-12">
                                             <p style="padding-left:15px; font-size:12px; color:#999;">{{Lang::get('language.form_notifications_hlp')}}</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <label class="control-label">
                                            <input type="checkbox" <?php if($userData->user_document_notifications  == Session::get('user_document_notifications')):echo "checked";endif;?> name="user_document_notifications" class="minimal" <?php if(@$emailNotif[0]->email_notification_override_email_notifications_settings==0): ?> disabled <?php endif; if(@$emailNotif[0]->email_notification_overwrite_preferences==0): ?> disabled <?php endif;?>>
                                            {{Lang::get('language.document_notifications')}} </label>
                                        </div>
                                        <div class="col-sm-12">
                                            <p style="padding-left:15px; font-size:12px; color:#999;">{{Lang::get('language.document_notifications_hlp')}}</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <label class="control-label">
                                            <input type="checkbox" <?php if($userData->user_signin_notifications == Session::get('user_signin_notifications')):echo "checked";endif;?> name="user_signin_notifications" class="minimal" <?php if(@$emailNotif[0]->email_notification_override_email_notifications_settings==0): ?> disabled <?php endif; if(@$emailNotif[0]->email_notification_overwrite_preferences==0): ?> disabled <?php endif;?>>
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
                                    <input type="hidden" name="stngs_ovrwritepref" value="<?php echo @$emailNotif[0]->email_notification_overwrite_preferences; ?>">         

                                    <input type="hidden" name="viewonly" value="<?php echo $userData->user_view_only; ?>" id="viewonly">

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <label class="control-label">
                                            <input type="checkbox" value="1" name="system_prefer" class="minimal" <?php if(@$emailNotif[0]->email_notification_override_email_notifications_settings==0): ?> disabled <?php endif; if(@$emailNotif[0]->email_notification_overwrite_preferences==0): ?> disabled <?php endif;?>>
                                            {{Lang::get('language.use_system_prefer')}} </label>
                                        </div>                           
                                    </div>

                                </div> 
                            </div>
            

            
          @endif
        <?php
        }        
        ?> 

        <!--Only admin and group admin have the privilege to change user expiry -->
        @if( (Auth::user()->id != $userData->id) &&  ( (Auth::user()->user_role == Session::get('user_role_super_admin') ) || ( Auth::user()->user_role == Session::get('user_role_group_admin')) ) )
        
          <div class="form-group">
              <label for="Sign In Expiry Date" class="col-sm-2 control-label" id = "login_exp">Sign In Expiry Date: <span class="compulsary">*</span></label>
              <div class="col-sm-4">
                  <div class="input-group">
                      <div class="input-group-addon">
                          <i class="fa fa-calendar"></i>
                      </div>
                      <?php
                          $d=explode(" ",$userData->user_login_expiry);
                      ?>
                      <input type="text" class="form-control" id="exp_date_edi" name="exp_date_edi" value="<?php if($d[0] != '0000-00-00'){ echo $d[0]; }?>" <?php if($userData->user_login_expiry == NULL OR $d[0]== '0000-00-00') echo 'disabled'; ?>  >
                  </div><!-- /.input group -->
              </div>
              <!--Show message-->
              <!--It showing message in each space depending on its ids-->
              <span class="formInfo"><a href="{{url('getUserMessages')}}?which_message=date_setting_msg" class="jTip" id="three" name="Notice:">?</a></span>
              &nbsp;&nbsp;&nbsp;
              <input type="checkbox" name="login_exp_chk_edi" id="login_exp_chk_edi" value="1" <?php if($userData->user_login_expiry == NULL OR $d[0]== '0000-00-00'  ) echo 'checked'; ?>> No Expiry               
          </div>
         

          <div class="form-group">
              <div class="col-sm-2"></div>
              <div class="col-sm-6">
                  <p class="subnote">{{trans('language.user_expiry_note')}}</p>
              </div>
          </div>
        @endif
        <!--//Only admin and group admin have the privilege to change user expiry -->

        <div class="form-group">
          <label for="" class="col-sm-2 control-label">Report To: </label>

            <div class="col-sm-6">
               <select class="form-control report_to" id="report_to"   name="report_to">
                        <option value="">{{ trans('language.select_user') }}</option>
                           <?php
                            foreach ($users as $key => $user) {
                                ?>
                                <option value="<?php echo $user->id; ?>" <?php if($user->id==$userData->report_to) { echo 'selected'; } ?>><?php echo ucfirst(@$user->user_full_name);?> -{{@$user->user_role}}@if(@$user->departments[0]->department_name != "")- {{ ucfirst(@$user->departments[0]->department_name) }} @endif</option>
                                <?php
                            }
                            ?> 
                        </select>
                  <p class="help">{{trans('language.users_with_roles')}}</p>
                 <!-- <p class="help">{{$language['form_help2']}}</p> -->
            </div>
        </div>
        <div class="form-group">
          <label for="" class="col-sm-2 control-label">Workflow Delegate To: </label>

            <div class="col-sm-6">
               <select class="form-control " id="delegate_user"   name="delegate_user">
                        <option value="">{{ trans('language.select_user') }}</option>
                           <?php
                            foreach ($users as $key => $user) {
                                ?>
                                <option value="<?php echo $user->id; ?>" <?php if($user->id==$userData->delegate_user) { echo 'selected'; } ?>><?php echo ucfirst(@$user->user_full_name);?> -{{@$user->user_role}}@if(@$user->departments[0]->department_name != "")- {{ ucfirst(@$user->departments[0]->department_name) }} @endif</option>
                                <?php
                            }
                            ?> 
                        </select>
                  <span class="error-msg" id="delegate_user-err-msg"></span>
                  <p class="help">{{trans('language.users_with_roles')}}</p>
                 <!-- <p class="help">{{$language['form_help2']}}</p> -->
            </div>
        </div>
        <div class="form-group" id="delegate_date_div" <?php if(!isset($userData->delegate_user)) { echo "style='display:none;'"; } ?>>
          <label for="" class="col-sm-2 control-label">Delegate Period: </label>

            <div class="col-sm-6">
              <input class="form-control active" id="delegate_period" name="delegate_period"  data-original-title="" title=""  data-parsley-required-message="Delegate date period is required" type="text"
                        value="<?php if(isset($userData->delegate_from_date) && isset($userData->delegate_to_date)) { echo $userData->delegate_from_date.'_'.$userData->delegate_to_date; } ?>"
                        <?php if(isset($userData->delegate_user)) { echo "required='required;'"; } ?>>
                  
            </div>
        </div>

        {!! Form:: hidden('user_role', $userData->user_role ) !!} 
        <div class="form-group">
            <label class="col-sm-2 control-label"></label>
            <div class="col-sm-6 s-btn">
                {!!Form::submit('Save', array('class' => 'btn btn-primary sav_btn')) !!} &nbsp;&nbsp;
                <a href="{{URL::route('users')}}" class = "btn btn-primary btn-danger">Cancel</a>
            </div>
        </div><!-- /.col -->

    {!! Form::close() !!}

</div><!-- /.modal-dialog -->
@endif
</section>
</section>
<script type="text/javascript">
// $('#spl-wrn').empty();
// $('#msg').empty();
    $(document).ready(function(){
      
        setTimeout(function() {
        $('#spl-wrn').fadeOut('fast');
        }, 5000); // <-- time in milliseconds
        setTimeout(function() {
        $('#msg').fadeOut('fast');
        }, 5000); // <-- time in milliseconds

        var vou = $('#viewonly').val();
        if(vou==1){
            $('#eadd').prop('checked', false);
            $('#eedit').prop('checked', false);
            $('#eview').prop('checked', true);
            $('#edelete').prop('checked', false);
            $('#echeckout').prop('checked', false);
            $('#eimport').prop('checked', false);
            $('#eexport').prop('checked', false);
            $('#eworkflow').prop('checked', false);
            $('#edecrypt').prop('checked', false);
            $('#es_all').prop('checked', false);

            $("#eadd").attr("disabled", true);
            $("#eedit").attr("disabled", true);
            $("#eview").attr("disabled", true);
            $("#edelete").attr("disabled", true);
            $('#echeckout').attr('disabled', true);
            $('#eimport').attr('disabled', true);
            $('#eexport').attr('disabled', true);
            $('#eworkflow').attr('disabled', true);
            $('#edecrypt').attr('disabled', true);
            $("#es_all").attr("disabled", true);

            //Form Access Previleges  
            $('#eaddFrm').prop('checked', false);
            $('#eeditFrm').prop('checked', false);
            $('#eviewFrm').prop('checked', true);
            $('#edeleteFrm').prop('checked', false);
            $('#eexportFrm').prop('checked', false);
            $('#s_all_frm').prop('checked', false);

            $("#eaddFrm").attr("disabled", true);
            $("#eeditFrm").attr("disabled", true);
            $("#eviewFrm").attr("disabled", true);
            $("#edeleteFrm").attr("disabled", true);
            $("#eexportFrm").attr("disabled", true);
            $("#s_all_frm").attr("disabled", true);

            //Work Flow Access Previleges  
            $('#eaddWrkFlw').prop('checked', false);
            $('#eeditWrkFlw').prop('checked', false);
            $('#eviewWrkFlw').prop('checked', true);
            $('#edeleteWrkFlw').prop('checked', false);
            $('#s_all_wf').prop('checked', false);

            $("#eaddWrkFlw").attr("disabled", true);
            $("#eeditWrkFlw").attr("disabled", true);
            $("#eviewWrkFlw").attr("disabled", true);
            $("#edeleteWrkFlw").attr("disabled", true);
            $("#s_all_wf").attr("disabled", true);
        }
      
      $("#delegate_user").on('change',function() {
        var delegate_user = $(this).val();
        if(delegate_user=='') {
          $("#delegate_date_div").hide();
          $("#delegate_period").prop('required',false);
        }
        else {
          $.ajax({
                    type:'GET',
                    url:'{{URL('getUserAvailable')}}',
                    data:'delegate_user='+delegate_user,
                    dataType: 'json',
                    success:function(response){ console.log(response);
                      if(response.status==0) { 
                        $("#delegate_user-err-msg").html(response.msg);
                        $("#delegate_period").prop('disabled',true);
                        $("#delegate_period").val('');
                      }
                      else{
                        $("#delegate_user-err-msg").html('');
                        $("#delegate_period").prop('disabled',false);
                      }
                    },
        });
          
          $("#delegate_date_div").show();
          $("#delegate_period").prop('required',true);
        }
      });
      // Get language
      var expiry_lang = "{{trans('language.sign_in_expiry_date_is_required')}}";

        $('#pwd_edit').click(function() {
           $('#confirm_password').attr('required','');
            var ckb = $("#pwd_edit").is(':checked');
            if(ckb == false) {
                $("#edpwd").hide();
                $('#current_password').removeAttr('required');
                $('#password').removeAttr('required');
                $('#confirm_password').removeAttr('required');
            } else {

                $('#current_password').attr('required','');
                $('#password').attr('required');
                $('#confirm_password').attr('required');

                $("#edpwd").show();
                $("#password").prop("required", true);
                $("#confirm_password").prop("data-parsley-equalto", '#password');
            }
        });

        var d           = new Date();
        var currentYear = d.getFullYear();
        var newDate     = currentYear+10;
        var date        = '12/31/'+newDate;

        var dd = d.getDate();
        var mm = d.getMonth()+1; //January is 0!
        if(dd<10) {
            dd = '0'+dd
        } 

        if(mm<10) {
            mm = '0'+mm
        } 

        today = currentYear+'-'+mm+'-'+dd;
      
        $('#exp_date_edi').daterangepicker({
            singleDatePicker: true,
            "drops": "up",
            minDate: today,
            maxDate: moment(date),
            showDropdowns: true
        });

       

        $('#delegate_period').daterangepicker({
            opens: 'left',
            "drops": "up",
           // "showDropdowns": true,
           
            minDate: today,
            "buttonClasses": "btn btn-primary",
            "applyButtonClasses": "btn-primary ",
            "cancelClass": "btn-danger "
            //endDate: currentYear+'/'+mm+'/01'
        }, function(start, end, label) {
                //console.log("DT RANGE -> " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
                var dtRange = start.format('YYYY-MM-DD')+'_'+end.format('YYYY-MM-DD');
                $("#delegate_period").val(dtRange);
        });

   
        //$('[data-toggle="tooltip"]').tooltip(); 

        //Expire checking
      if($("#login_exp_chk_edi").prop('checked') == true){
          $("#exp_date_edi").attr("disabled", true);
      } else {
        
      } 

      $('#login_exp_chk_edi').click(function() { 
       
        var ckb = $("#login_exp_chk_edi").is(':checked');
        if(ckb == false) {
            $("#exp_date_edi").attr("disabled", false);
            $('#exp_date_edi').attr('required','required');
            $('#exp_date_edi').attr('data-parsley-required-message',expiry_lang);
        } else {
            $("#exp_date_edi").attr("disabled", true);
            $('#exp_date_edi').attr('required',false);
            $('#exp_date_edi').val('');
        }           
    });

      //Radion button checking forsuperadmin
        $('.rd').click(function() {
            var id= this.id;

            // Super admin
            if(id == 'super_admin'){   
                $("#viewonly").val('0');
                //User Access Previleges  
                $('#eadd').prop('checked', true);
                $('#eedit').prop('checked', true);
                $('#eview').prop('checked', true);
                $('#edelete').prop('checked', true);
                $('#echeckout').prop('checked', true);
                $('#eimport').prop('checked', true);
                $('#eexport').prop('checked', true);
                $('#eworkflow').prop('checked', true);
                $('#edecrypt').prop('checked', true);
                $('#es_all').prop('checked', true);

                $("#eadd").attr("disabled", true);
                $("#eedit").attr("disabled", true);
                $("#eview").attr("disabled", true);
                $("#edelete").attr("disabled", true);
                $('#echeckout').attr('disabled', true);
                $('#eimport').attr('disabled', true);
                $('#eexport').attr('disabled', true);
                $('#eworkflow').attr('disabled', true);
                $('#edecrypt').attr('disabled', true);
                $("#es_all").attr("disabled", true);

                //Form Access Previleges  
                $('#eaddFrm').prop('checked', true);
                $('#eeditFrm').prop('checked', true);
                $('#eviewFrm').prop('checked', true);
                $('#edeleteFrm').prop('checked', true);
                $('#eexportFrm').prop('checked', true);
                $('#s_all_frm').prop('checked', true);

                $("#eaddFrm").attr("disabled", true);
                $("#eeditFrm").attr("disabled", true);
                $("#eviewFrm").attr("disabled", true);
                $("#edeleteFrm").attr("disabled", true);
                $("#eexportFrm").attr("disabled", true);
                $("#s_all_frm").attr("disabled", true);

                //Work Flow Access Previleges  
                $('#eaddWrkFlw').prop('checked', true);
                $('#eeditWrkFlw').prop('checked', true);
                $('#eviewWrkFlw').prop('checked', true);
                $('#edeleteWrkFlw').prop('checked', true);
                $('#s_all_wf').prop('checked', true);

                $("#eaddWrkFlw").attr("disabled", true);
                $("#eeditWrkFlw").attr("disabled", true);
                $("#eviewWrkFlw").attr("disabled", true);
                $("#edeleteWrkFlw").attr("disabled", true);
                $("#s_all_wf").attr("disabled", true);

                $("#spl_div_edi").hide();  

            }else if(id == 'group_admin') {
                // Group admin
                $("#viewonly").val('0');
                //User Access Previleges  
                $('#eadd').prop('checked', true);
                $('#eedit').prop('checked', true);
                $('#eview').prop('checked', true);
                $('#edelete').prop('checked', true);
                $('#echeckout').prop('checked', true);
                $('#eimport').prop('checked', true);
                $('#eexport').prop('checked', true);
                $('#eworkflow').prop('checked', true);
                $('#edecrypt').prop('checked', true);
                $('#es_all').prop('checked', true);

                $("#eadd").attr("disabled", true);
                $("#eedit").attr("disabled", true);
                $("#eview").attr("disabled", true);
                $("#edelete").attr("disabled", true);
                $('#echeckout').attr('disabled', true);
                $('#eimport').attr('disabled', true);
                $('#eexport').attr('disabled', true);
                $('#eworkflow').attr('disabled', true);
                $('#edecrypt').attr('disabled', true);
                $("#es_all").attr("disabled", true);


                //Form Access Previleges  
                $('#eaddFrm').prop('checked', true);
                $('#eeditFrm').prop('checked', true);
                $('#eviewFrm').prop('checked', true);
                $('#edeleteFrm').prop('checked', true);
                $('#eexportFrm').prop('checked', true);
                $('#s_all_frm').prop('checked', true);

                $("#eaddFrm").attr("disabled", true);
                $("#eeditFrm").attr("disabled", true);
                $("#eviewFrm").attr("disabled", true);
                $("#edeleteFrm").attr("disabled", true);
                $("#eexportFrm").attr("disabled", true);
                $("#s_all_frm").attr("disabled", true);

                //Work Flow Access Previleges  
                $('#eaddWrkFlw').prop('checked', true);
                $('#eeditWrkFlw').prop('checked', true);
                $('#eviewWrkFlw').prop('checked', true);
                $('#edeleteWrkFlw').prop('checked', true);
                $('#s_all_wf').prop('checked', true);

                $("#eaddWrkFlw").attr("disabled", true);
                $("#eeditWrkFlw").attr("disabled", true);
                $("#eviewWrkFlw").attr("disabled", true);
                $("#edeleteWrkFlw").attr("disabled", true);
                $("#s_all_wf").attr("disabled", true);


                $("#spl_div_edi").show();
                /*$('#spl_div_edi :checkbox:enabled').prop('checked', false); */

            }else if(id == 'viewonly_user') {
                // Group admin
                $("#viewonly").val('1');
                //User Access Previleges  
                $('#eadd').prop('checked', false);
                $('#eedit').prop('checked', false);
                $('#eview').prop('checked', true);
                $('#edelete').prop('checked', false);
                $('#echeckout').prop('checked', false);
                $('#eimport').prop('checked', false);
                $('#eexport').prop('checked', false);
                $('#eworkflow').prop('checked', false);
                $('#edecrypt').prop('checked', false);
                $('#es_all').prop('checked', false);

                $("#eadd").attr("disabled", true);
                $("#eedit").attr("disabled", true);
                $("#eview").attr("disabled", true);
                $("#edelete").attr("disabled", true);
                $('#echeckout').attr('disabled', true);
                $('#eimport').attr('disabled', true);
                $('#eexport').attr('disabled', true);
                $('#eworkflow').attr('disabled', true);
                $('#edecrypt').attr('disabled', true);
                $("#es_all").attr("disabled", true);


                //Form Access Previleges  
                $('#eaddFrm').prop('checked', false);
                $('#eeditFrm').prop('checked', false);
                $('#eviewFrm').prop('checked', true);
                $('#edeleteFrm').prop('checked', false);
                $('#eexportFrm').prop('checked', false);
                $('#s_all_frm').prop('checked', false);

                $("#eaddFrm").attr("disabled", true);
                $("#eeditFrm").attr("disabled", true);
                $("#eviewFrm").attr("disabled", true);
                $("#edeleteFrm").attr("disabled", true);
                $("#eexportFrm").attr("disabled", true);
                $("#s_all_frm").attr("disabled", true);

                //Work Flow Access Previleges  
                $('#eaddWrkFlw').prop('checked', false);
                $('#eeditWrkFlw').prop('checked', false);
                $('#eviewWrkFlw').prop('checked', true);
                $('#edeleteWrkFlw').prop('checked', false);
                $('#s_all_wf').prop('checked', false);

                $("#eaddWrkFlw").attr("disabled", true);
                $("#eeditWrkFlw").attr("disabled", true);
                $("#eviewWrkFlw").attr("disabled", true);
                $("#edeleteWrkFlw").attr("disabled", true);
                $("#s_all_wf").attr("disabled", true);


                $("#spl_div_edi").show();
                /*$('#spl_div_edi :checkbox:enabled').prop('checked', false); */

            }else if( (id == 'private_user') || (id == 'regular_user')){
                $("#viewonly").val('0');
              //Either Regulare user or Private user
                  var userId = $('#userId').val();
                  var usrRol = $('#usrRol').val();

                 // Get privileges for select
                  $.ajax({
                    type:'GET',
                    url:'{{URL('getUserPermission')}}',
                    data:'editUserId='+userId,
                    success:function(response){
                      var data = jQuery.parseJSON(response);
                      // User Access prevelages
                      // Add priviledge
                      if(data.user_add){
                        $('#eadd').prop('checked', true);
                        $("#eadd").attr("disabled", false);
                      }else{
                        $('#eadd').prop('checked', false);
                        $("#eadd").attr("disabled", false);
                      }

                      // Edit priviledge
                      if(data.user_edit){
                        $('#eedit').prop('checked', true);
                        $("#eedit").attr("disabled", false);
                      }else{
                        $('#eedit').prop('checked', false);
                        $("#eedit").attr("disabled", false);
                      }

                      // View priviledge
                      if(data.user_view){
                        $('#eview').prop('checked', true);
                        $("#eview").attr("disabled", false);
                      }else{
                        $('#eview').prop('checked', false);
                        $("#eview").attr("disabled", false);
                      }

                      // Delete priviledge
                      if(data.user_delete){
                        $('#edelete').prop('checked', true);
                        $("#edelete").attr("disabled", false);
                      }else{
                        $('#edelete').prop('checked', false);
                        $("#edelete").attr("disabled", false);
                      }

                      // Checkout priviledge
                      if(data.user_download){
                        $('#echeckout').prop('checked', true);
                        $("#echeckout").attr("disabled", false);
                      }else{
                        $('#echeckout').prop('checked', false);
                        $("#echeckout").attr("disabled", false);
                      }

                      // Inport priviledge
                      if(data.user_import){
                        $('#eimport').prop('checked', true);
                        $("#eimport").attr("disabled", false);
                      }else{
                        $('#eimport').prop('checked', false);
                        $("#eimport").attr("disabled", false);
                      }

                      // Export priviledge
                      if(data.user_export){
                        $('#eexport').prop('checked', true);
                        $("#eexport").attr("disabled", false);
                      }else{
                        $('#eexport').prop('checked', false);
                        $("#eexport").attr("disabled", false);
                      }  

                      // workflow priviledge
                      if(data.user_workflow){
                        $('#eworkflow').prop('checked', true);
                        $("#eworkflow").attr("disabled", false);
                      }else{
                        $('#eworkflow').prop('checked', false);
                        $("#eworkflow").attr("disabled", false);
                      } 

                      // Form Access prevelages
                      // Form add
                      if(data.form_add) {
                        $('#eaddFrm').prop('checked', true);
                        $("#eaddFrm").attr("disabled", false);
                      }
                      else {
                        $('#eaddFrm').prop('checked', false);
                        $("#eaddFrm").attr("disabled", false);
                      }
                      // Form delete
                      if(data.form_delete) {
                        $('#edeleteFrm').prop('checked', true);
                        $("#edeleteFrm").attr("disabled", false);
                      }
                      else {
                        $('#edeleteFrm').prop('checked', false);
                        $("#edeleteFrm").attr("disabled", false);
                      }
                      // Form view
                      if(data.form_view) {
                        $('#eviewFrm').prop('checked', true);
                        $("#eviewFrm").attr("disabled", false);
                      }
                      else {
                        $('#eviewFrm').prop('checked', false);
                        $("#eviewFrm").attr("disabled", false);
                      }
                      // Form edit
                      if(data.form_edit) {
                        $('#eeditFrm').prop('checked', true);
                        $("#eeditFrm").attr("disabled", false);
                      }
                      else {
                        $('#eeditFrm').prop('checked', false);
                        $("#eeditFrm").attr("disabled", false);
                      }
                      // Form export
                      if(data.form_export) {
                        $('#eexportFrm').prop('checked', true);
                        $("#eexportFrm").attr("disabled", false);
                      }
                      else {
                        $('#eexportFrm').prop('checked', false);
                        $("#eexportFrm").attr("disabled", false);
                      }

                      // Work Flow Access prevelages
                      // Add Workflow
                      if(data.workflow_add) {
                        $('#eaddWrkFlw').prop('checked', true);
                        $("#eaddWrkFlw").attr("disabled", false);
                      }
                      else{
                        $('#eaddWrkFlw').prop('checked', false);
                        $("#eaddWrkFlw").attr("disabled", false);
                      }
                      // Edit Workflow
                      if(data.workflow_edit) {
                        $('#eeditWrkFlw').prop('checked', true);
                        $("#eeditWrkFlw").attr("disabled", false);
                      }
                      else{
                        $('#eeditWrkFlw').prop('checked', false);
                        $("#eeditWrkFlw").attr("disabled", false);
                      }
                      // View Workflow
                      if(data.workflow_view) {
                        $('#eviewWrkFlw').prop('checked', true);
                        $("#eviewWrkFlw").attr("disabled", false);
                      }
                      else{
                        $('#eviewWrkFlw').prop('checked', false);
                        $("#eviewWrkFlw").attr("disabled", false);
                      }
                      // Delete Workflow
                      if(data.workflow_delete) {
                        $('#edeleteWrkFlw').prop('checked', true);
                        $("#edeleteWrkFlw").attr("disabled", false);
                      }
                      else{
                        $('#edeleteWrkFlw').prop('checked', false);
                        $("#edeleteWrkFlw").attr("disabled", false);
                      }
                    }
                  
                  });
                  // Show department
                  $("#spl_div_edi").show();
            }
        });
        //Select all
        $('#es_all').click(function() {

            var ckb = $("#es_all").is(':checked');
            if(ckb == false) {
                $('#eadd').prop('checked', false);
                $('#eedit').prop('checked', false);
                $('#eview').prop('checked', false);
                $('#edelete').prop('checked', false);
                $('#echeckout').prop('checked', false);
                $('#eimport').prop('checked', false);
                $('#eexport').prop('checked', false);
                $('#eworkflow').prop('checked', false);
                $('#edecrypt').prop('checked', false);
            } else {
                $('#eadd').prop('checked', true);
                $('#eedit').prop('checked', true);
                $('#eview').prop('checked', true);
                $('#edelete').prop('checked', true);
                $('#echeckout').prop('checked', true);
                $('#eimport').prop('checked', true);
                $('#eexport').prop('checked', true);
                $('#eworkflow').prop('checked', true);
                $('#edecrypt').prop('checked', true);
            }   
            
        });
        $('#s_all_frm').click(function() {

            var ckb = $("#s_all_frm").is(':checked');
            if(ckb == false) {
                $('#eaddFrm').prop('checked', false);
                $('#eeditFrm').prop('checked', false);
                $('#eviewFrm').prop('checked', false);
                $('#edeleteFrm').prop('checked', false);
                $('#eexportFrm').prop('checked', false);
            } else {
                $('#eaddFrm').prop('checked', true);
                $('#eeditFrm').prop('checked', true);
                $('#eviewFrm').prop('checked', true);
                $('#edeleteFrm').prop('checked', true);
                $('#eexportFrm').prop('checked', true);
            }   
            
        });
        $('#s_all_wf').click(function() {

            var ckb = $("#s_all_wf").is(':checked');
            if(ckb == false) {
                $('#eaddWrkFlw').prop('checked', false);
                $('#eeditWrkFlw').prop('checked', false);
                $('#eviewWrkFlw').prop('checked', false);
                $('#edeleteWrkFlw').prop('checked', false);
            } else {
                $('#eaddWrkFlw').prop('checked', true);
                $('#eeditWrkFlw').prop('checked', true);
                $('#eviewWrkFlw').prop('checked', true);
                $('#edeleteWrkFlw').prop('checked', true);
            }   
            
        });

        $('.usr_permission').click(function() {
          if($(this).is(':checked')==false) {
            $('#es_all').prop('checked', false);
          }
          var cnt = $('.usr_permission:checked').length;
          if(cnt==9){
            $('#es_all').prop('checked', true);
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
    });
// select all desired input fields and attach tooltips to them
      $("#userEditForm :input").tooltip({
 
      // place tooltip on the right edge
      position: "center",
 
      // a little tweaking of the position
      offset: [-2, 10],
 
      // use the built-in fadeIn/fadeOut effect
      effect: "fade",
 
      // custom opacity setting
      opacity: 0.7
 
      }); 


      function unlock(id,username)
    {

        swal({
              title: "{{trans('language.confirm_unlock')}}'" + username + "' ?",
              text: "{{trans('language.Swal_not_revert')}}",
              type: "{{trans('language.Swal_warning')}}",
              showCancelButton: true
            }).then(function (result) {
                if(result){
                    // Success
                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    $.ajax({
                        type: 'post',
                        url: '{{URL('userUnlock')}}',
                        dataType: 'html',
                        data: {_token: CSRF_TOKEN, id:id,name:username},
                        timeout: 50000,
                        beforeSend: function() {
                            $("#bs").show();
                        },
                        success: function(data, status){ 
                            $("#bs").hide();
                            $("#unlck").slideUp("slow");
                            $("#msg").slideDown(1000);
                            $("#msg").html('<div class="alert alert-success alert-sty">'+ data +'</div>');
                        },
                        error: function(jqXHR, textStatus, errorThrown){
                            console.log(jqXHR);    
                            console.log(textStatus);    
                            console.log(errorThrown);    
                        },
                        complete: function() {                   
                           setTimeout(function () {
                                $('#msg').slideUp("slow");
                            }, 9000);
                        }
                    }); 
              }
          });
    }
</script>
@endsection