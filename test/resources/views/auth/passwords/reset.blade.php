<!DOCTYPE html>
<html>
  <head>

    <!--Include language file-->
    <?php include (public_path()."/storage/includes/lang1.en.php" );?>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>DMS | {{$language['reset_paswd']}}</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    {!! Html::style('bootstrap/css/bootstrap.min.css') !!} 

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

    </head>
        <body class="hold-transition login-page">

            <div class="container">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <div class="panel panel-default">
                            <div class="panel-heading">{{$language['reset_paswd']}}</div>

                            <div class="panel-body">
                                <form class="form-horizontal dms_form" role="form" method="POST" action="{{ url('/password/reset') }}" data-parsley-validate=''>
                                    {{ csrf_field() }}

                                    <input type="hidden" name="token" value="{{ $token }}">

                                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                        <label for="email" class="col-md-4 control-label">{{$language['email_address']}}</label>

                                        <div class="col-md-6">
                                            <input id="email" type="email" class="form-control" name="email" value="{{ $email or old('email') }}">

                                            @if ($errors->has('email'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('email') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                        <label for="password" class="col-md-4 control-label">{{$language['password']}}</label>

                                        <div class="col-md-6">
                                            <input id="password" type="password" class="form-control password1" this_val="1" is_auth="true" name="password" data-parsley-minlength = "<?php echo Session::get('settings_password_length_from');?>" data-parsley-maxlength = "<?php echo Session::get('settings_password_length_to');?>" >

                                            @if ($errors->has('password'))
                                                <span class="help-block">
                                                    <strong><!--{{ $errors->first('password') }}-->{{$language['password_is_required']}}</strong>
                                                </span>
                                            @endif
                                            <!--password complexity error 1-->
                                            <span id="space_error1" style="font-size: 0.9em;color: red;"></span>
                                            <span id="complexity_error_1alphabets" style="font-size: 0.9em;color: red;"></span>
                                            <span id="complexity_error_1numerics" style="font-size: 0.9em;color: red;"></span>
                                            <span id="complexity_error_1special_characters" style="font-size: 0.9em;color: red;"></span>
                                            <span id="complexity_error_1capital_and_small" style="font-size: 0.9em;color: red;"></span>
                                            <!--password length error-->
                                            <span class="password_length_error">
                                              <p id="password_length_from_error_1" style="font-size: 0.9em;color: red;"></p>
                                              <p id="password_length_to_error_1" style="font-size: 0.9em;color: red;"></p>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                        <label for="password-confirm" class="col-md-4 control-label">{{$language['confpassword']}}</label>
                                        <div class="col-md-6">
                                            <input id="password-confirm" type="password" class="form-control password2" this_val="2" is_auth="true" name="password_confirmation" data-parsley-minlength = "<?php echo Session::get('settings_password_length_from');?>" data-parsley-maxlength = "<?php echo Session::get('settings_password_length_to');?>" >

                                            @if ($errors->has('password_confirmation'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                                </span>
                                            @endif
                                            <!--password complexity error 2-->
                                            <span id="space_error2" style="font-size: 0.9em;color: red;"></span>
                                           <span id="complexity_error_2alphabets" style="font-size: 0.9em;color: red;"></span>
                                           <span id="complexity_error_2numerics" style="font-size: 0.9em;color: red;"></span>
                                           <span id="complexity_error_2special_characters" style="font-size: 0.9em;color: red;"></span>
                                           <span id="complexity_error_2capital_and_small" style="font-size: 0.9em;color: red;"></span>
                                            <!--password length error-->
                                            <span class="password_length_error">
                                              <p id="password_length_from_error_2" style="font-size: 0.9em;color: red;"></p>
                                              <p id="password_length_to_error_2" style="font-size: 0.9em;color: red;"></p>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-6 col-md-offset-4">
                                            <button type="submit" class="btn btn-primary sav_btn">
                                                {{$language['reset_paswd']}}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </body>
    </html>

    <script type="text/javascript">
        var base_url = '<?php echo URL('');?>';
    </script>
    <!-- jQuery 2.1.4 -->
    {!! Html::script('plugins/jQuery/jQuery-2.1.4.min.js') !!}
    <!--From validation-->
    {!! Html::script('js/parsley.min.js') !!}  
    <!-- developer script -->
    {!! Html::script('js/devscript.js') !!}

