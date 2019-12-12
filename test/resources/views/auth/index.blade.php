<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>DMS | Log in</title>
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

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]-->
        {!! Html::script('js/html5shiv.min.js') !!}  
        {!! Html::script('js/respond.min.js') !!}  
    <!--[endif]-->
  </head>
  <body class="hold-transition login-page">
    <div class="login-box">
      <div class="login-logo">
        <a href=" "><b>DMS</a>
      </div><!-- /.login-logo -->
      <div class="dms_login_error col-md-10" style="text-align:center;">
        @if(count($errors)> 0)        
          @foreach($errors->all() as $error)
            {!! $error !!}
          @endforeach
        
        @endif
      </div>
      <div class="col-md-4"></div>
      <div class="login-box-body col-md-4">
        <p class="login-box-msg">Sign in to start your session</p>
        {!! Form:: open(array('url'=>'postLogin', 'method'=>'patch')) !!}
          <div class="form-group has-feedback">
            {!! Form:: text('username', '', array('required', 'class' => 'form-control', 'id'=> 'username', 'placeholder'=> 'Username')) !!}
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
          {!! Form:: password('password', array('required', 'class'=> 'form-control', 'id'=> 'password', 'placeholder'=> 'Password')) !!} 
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>
          <div class="row">
            <div class="col-xs-8">
              <div class="checkbox icheck">
                <label>
                  <input type="checkbox" name="remember" id="remember"> Remember Me
                </label>
              </div>
            </div><!-- /.col -->
            <div class="col-xs-4">
            {!!Form::submit('Sign In', array('class' => 'btn btn-primary btn-block btn-flat')) !!}
            </div><!-- /.col -->
          </div>
        {!! Form:: close() !!}        
        <a href="{{ url('password/reset')  }}">I forgot my password</a><br>

        <!-- <a href="register.html" class="text-center">Register a new membership</a> -->
        

      </div><!-- /.login-box-body -->
      <div class="col-md-4"></div>
    </div><!-- /.login-box -->


    <!-- jQuery 2.1.4 -->
            {!! Html::script('plugins/jQuery/jQuery-2.1.4.min.js') !!}
            
            <!-- Bootstrap 3.3.5 -->
            {!! Html::script('bootstrap/js/bootstrap.min.js') !!}
            <!-- Morris.js charts -->
            {!! Html::script('plugins/iCheck/icheck.min.js') !!}
            <?php
            session_start();
            $_SESSION['SESS_path']='DMS ROOT';
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
