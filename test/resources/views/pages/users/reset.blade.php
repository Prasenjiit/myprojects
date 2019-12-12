<?php
include (public_path()."/storage/includes/lang1.en.php" );
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{trans('language.dms')}} | reset</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

       {!! Html::style('bootstrap/css/bootstrap.min.css') !!} 

        <!-- Font Awesome -->
        {!! Html::style('dist/css/font-awesome.min.css') !!} 
        <!-- Ionicons -->
        {!! Html::style('dist/css/ionicons.min.css') !!} 
        <!-- Theme style -->
        {!! Html::style('dist/css/AdminLTE.min.css') !!} 
        <!-- AdminLTE Skins. Choose a skin from the css/skins
        folder instead of downloading all of them to reduce the load. -->
        {!! Html::style('plugins/iCheck/square/blue.css') !!} 
        <!-- For formvalidations -->
        {!! Html::style('css/parsley.css') !!} 

        {!! Html::style('css/custom-css.css') !!}

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]-->
        {!! Html::script('js/html5shiv.min.js') !!}  
        {!! Html::script('js/respond.min.js') !!}  
    <!--[endif]-->

    <style type="text/css">
    .form-horizontal .form-group {
        margin-right: 0px !important;
        margin-left: 0px !important;
    }
</style>
  </head>

  <body class="hold-transition login-page">
   

    <div class="login-box">
    <div class="login-logo">
        <b><?php echo Session::get('settings_company_name');?></b>

        @if(Session::has('flash_message_wanning'))
        <section class="content content-sty" id="spl-wrn" style="width: 363px;">     
            <div class="alert alert-sty alert-error " style="font-size: 12px;">{{ Session::get('flash_message_wanning') }}</div>        
        </section>
        @endif
      </div> 

      

      <div class="dms_login_error">
        @if(count($errors)> 0)        
          @foreach($errors->all() as $error)
            {!! $error !!}
          @endforeach
        
        @endif
      </div>

      <div class="login-box-body">
        <p class="login-box-msg">{{ trans('language.reset_passwd_message') }}</p>
     
        {!! Form::open(array('url'=> array('resetSubmit'), 'method'=> 'post', 'class'=> 'form-horizontal dms_form', 'name'=> 'resetSubmit', 'id'=> 'resetSubmit','data-parsley-validate'=> '')) !!}            
        <div class="form-group has-feedback">
            {!! Form:: password('curpaswd', array( 'class'=> 'form-control','name'=>'current_password','required','data-parsley-required-message' =>"Current Password is required", 'placeholder'=> trans('language.curpassword'),'autofocus' => 'true')) !!} 
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>

        <div class="form-group has-feedback">
            <?php echo Form:: password('newpaswd', array( 'class'=> 'form-control password1','required','this_val'=>'1','data-parsley-minlength' => Session::get('settings_password_length_from'),
                        'data-parsley-required-message' =>"New Password is required",'data-parsley-maxlength' => Session::get('settings_password_length_to'),'is_auth'=>'true','id'=> 'password', 'placeholder'=> trans('language.newpassword'))) ;?>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            <!--password complexity error-->
            <span id="space_error1" style="font-size: 0.9em;color: red;"></span>
            <span id="complexity_error_1alphabets" style="font-size: 0.9em;color: red;"></span>
            <span id="complexity_error_1numerics" style="font-size: 0.9em;color: red;"></span>
            <span id="complexity_error_1special_characters" style="font-size: 0.9em;color: red;"></span>
            <span id="complexity_error_1capital_and_small" style="font-size: 0.9em;color: red;"></span>
            <!--password length error-->
            <span class="password_length_error">
              <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="password_length_from_error_1"></li></ul>
              <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="password_length_to_error_1"></li></ul>
            </span>
        </div>
        <div class="form-group has-feedback">
            <?php echo  Form:: password('confpaswd', array( 'class'=> 'form-control password2','is_auth'=>'true','data-parsley-minlength' => Session::get('settings_password_length_from'),
                        'data-parsley-equalto-message' => "Both Password and Confirm Password must be the same",'data-parsley-required-message' =>"Confirm Password is required",'data-parsley-maxlength' => Session::get('settings_password_length_to'),'required','name'=>'correct_password','data-parsley-equalto'=> '#password','this_val'=>'2', 'placeholder'=> trans('language.confpassword'))) ;?>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            <!--password complexity error-->
           <span id="space_error2" style="font-size: 0.9em;color: red;"></span>
           <span id="complexity_error_2alphabets" style="font-size: 0.9em;color: red;"></span>
           <span id="complexity_error_2numerics" style="font-size: 0.9em;color: red;"></span>
           <span id="complexity_error_2special_characters" style="font-size: 0.9em;color: red;"></span>
           <span id="complexity_error_2capital_and_small" style="font-size: 0.9em;color: red;"></span>
           <!--password length error-->
            <span class="password_length_error">
              <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="password_length_from_error_2"></li></ul>
              <ul class="parsley-errors-list filled" id="parsley-id-16"><li class="parsley-equalto" id="password_length_to_error_2"></li></ul>
            </span>
        </div>
        
        <input type="hidden" name="id" value="{{Session::get('auth_user_id')}}">
          <div class="row">
            <div class="col-xs-6">
            <a href="{{url('/login')}}" class="btn btn-block btn-flat cancel-btn">{{ trans('language.cancel') }}</a>
            </div><!-- /.col -->
            <div class="col-xs-6">
                {!!Form::submit(trans('language.reset_paswd'), array('class' => 'btn login-btn-primary btn-block btn-flat sav_btn')) !!}
            </div>
          </div>
        {!! Form:: close() !!}        
        

      </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->

    <script type="text/javascript">
        var base_url = '<?php echo URL('');?>';
    </script>
	<!-- jQuery 2.1.4 -->
    {!! Html::script('plugins/jQuery/jQuery-2.1.4.min.js') !!}
    
    <!-- Bootstrap 3.3.5 -->
    {!! Html::script('bootstrap/js/bootstrap.min.js') !!}
    <!-- Morris.js charts -->
    {!! Html::script('plugins/iCheck/icheck.min.js') !!}
    <!--From validation-->
    {!! Html::script('js/parsley.min.js') !!}  
    <!-- developer script -->
	{!! Html::script('js/devscript.js') !!}

    <?php
     session_start();
     Session::put('SESS_path','DMS ROOT');
     Session::put('SESS_parentIdd',1);
    ?>
    <script>
      $(function () {
        $('input').iCheck({
          checkboxClass: 'icheckbox_square-blue',
          radioClass: 'iradio_square-blue',
          increaseArea: '20%' // optional
        });
      });
    </script>
  </body>
</html>
