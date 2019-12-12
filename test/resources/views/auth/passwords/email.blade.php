<?php
include (public_path()."/storage/includes/lang1.en.php" );
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{$language['dms']}} | {{$language['pwd_reset_title']}}</title>
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
    {!! Html::style('css/custom-css.css') !!}

  
  </head>
  <body class="">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 background_image">
          <div class="container-left-half">
            <div class="row">
              <div class="col-xs-12 box_body_full"></div>
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
          <div class="container-right-half">
            <div class="row">
              <div class="col-xs-12 login_box_offset">
                <div class="login-box-body">
                  <div class="login-logo">
                    <b><?php echo Session::get('settings_company_name');?></b>
                  </div>
                 <form class="" role="form" method="POST" action="{{ url('/password/email') }}">
                    {{ csrf_field() }}
                  <div class="form-group has-feedback">
                   
        @if (session('status'))
          <div class="alert alert-success">
              {{$language['pwd_reset_success']}}
          </div>
        @endif
                  </div>
                  <p>{{$language['pwd_reset_validate']}}</p>

                  <div class="form-group has-feedback">
                  <input id="email" type="text" readonly="readonly" class="form-control custom_readonly" name="email" placeholder="{{$language['email_id']}}" value="{{ old('email') }}" onfocus="this.removeAttribute('readonly');">
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                  </div>
                  @if ($errors->has('email'))
              <p>{{ $errors->first('email') }}</p>
        @endif
                  
                  <div class="row">
                    <div class="col-xs-8">
                      <a href="{{ url('login')  }}">{{$language['back']}}</a>
                    </div>
                    <!-- /.col -->
                    <div class="col-xs-4">
                      {!!Form::submit($language['pwd_reset'], array('class' => 'btn login-btn-primary btn-block btn-flat','id'=>'login')) !!}
                    </div>
                    <!-- /.col -->
                  </div>
                  </form>
                </div>
                <!-- END-->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </body>
</html>
