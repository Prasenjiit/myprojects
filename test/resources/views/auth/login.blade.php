<?php
  include (public_path()."/storage/includes/lang1.en.php" );
  ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>DMS | Log in</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="shortcut icon" href="{{ asset('images/icons/favicon.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('images/icons/favicon.ico') }}" type="image/x-icon">
    {!! Html::style('bootstrap/css/bootstrap.min.css') !!}
    <!-- Font Awesome -->
    {!! Html::style('dist/css/font-awesome.min.css') !!}
    <!-- Ionicons -->
    {!! Html::style('dist/css/ionicons.min.css') !!}
    <!-- Theme style -->
    {!! Html::style('dist/css/AdminLTE.min.css') !!}
    <!-- AdminLTE Skins. Choose a skin from the css/skins
      folder instead of downloading all of them to reduce the load. -->
    {!! Html::style('plugins/iCheck/square/blue.css') !!} {!! Html::style('css/custom-css.css') !!}
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]-->
    {!! Html::script('js/html5shiv.min.js') !!} {!! Html::script('js/respond.min.js') !!}
    <!-- <script src='https://www.google.com/recaptcha/api.js'></script> -->
    <!--[endif]-->
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
                  {!! Form:: open(array('url'=>'/login', 'method'=>'post','autocomplete' => 'off')) !!} @php setcookie("test_cookie", "test"); @endphp
                  <div class="form-group has-feedback">
                    @if(count($_COOKIE)==0)
                    <!--  <div class="alert alert-warning alert-dismissible">Your browser has cookies disabled. Make sure your cookies are enabled</div> -->
                    @endif @if(Session::has('flash_message_wanning'))
                    <div class="alert alert-warning alert-dismissible" style="font-size: 12px;">{{ Session::get('flash_message_wanning') }}</div>
                    @endif 
                  </div>

                  @if ($errors->has('username'))
                    <label>{{ $errors->first('username') }}</label>
                @endif
                  <div class="form-group has-feedback">
                    {!! Form:: text('username', '', array('class' => 'form-control custom_readonly', 'id'=> 'username', 'placeholder'=> 'Username','autofocus' => 'true','autocomplete'=>'off','readonly'=>'readonly','onfocus'=>"this.removeAttribute('readonly');")) !!}
                    <span
                      class="glyphicon glyphicon-user form-control-feedback"></span>
                    <span><font color="red"></font></span>
                  </div>

                  <input type="hidden" name="hidd_ip" id="hidd_ip">
                  <input type="hidden" name="hidd_location" id="hidd_location">
                  
@if ($errors->has('password'))
                    <label>{{ $errors->first('password') }}</label>
                @endif
                  <div class="form-group has-feedback">
                    {!! Form:: password('password', array( 'class'=> 'form-control custom_readonly','id'=> 'password', 'placeholder'=> 'Password','autocomplete'=>'off','readonly'=>'readonly','onfocus'=>"this.removeAttribute('readonly');")) !!}
                    <span
                      class="glyphicon glyphicon-lock form-control-feedback"></span>
                    <span><font color="red"></font></span>
                  </div>
                   
                  <div class="row">
                    <div class="col-xs-8">
                      <a href="{{ url('password/reset')  }}">I forgot my password ?</a>
                    </div>
                    <!-- /.col -->
                    <div class="col-xs-4">
                      {!!Form::submit('Sign In', array('class' => 'btn login-btn-primary btn-block btn-flat','id'=>'login')) !!}
                    </div>
                    <!-- /.col -->
                  </div>
                  {!! Form:: close() !!}
                </div>
                <!-- END-->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- jQuery 2.1.4 -->
    {!! Html::script('plugins/jQuery/jQuery-2.1.4.min.js') !!}
    <!-- Bootstrap 3.3.5 -->
    {!! Html::script('bootstrap/js/bootstrap.min.js') !!}
    <!-- Morris.js charts -->
    {!! Html::script('plugins/iCheck/icheck.min.js') !!}
    <!-- developer script -->
    {!! Html::script('js/devscript.js') !!}
    <?php
      session_start();
      Session::put('SESS_path','DMS ROOT');
      Session::put('SESS_parentIdd',1);
      ?>
    <script type="text/javascript">
      var base_url = '<?php echo URL('');?>';
    </script>
    <script>
      $(function () {
       
       
         // Checking usernames with previous one to remove captcha
         $('body').on('input','#username',function(){
             var username    = $(this).val();
             $.ajax({
                 type:'get',
                 url:base_url+'/checkUserLockStatus',
                 data:'username='+username,
                 success:function(response){
                     if(response == 'false'){
                         // show captcha
                         $('#captcha').css('display','block');
                         $('#need_captcha').html('<input type="hidden" value="yes" name="is_captcha_exists">');
                         $('#check_cap').val('yes');
                     }else{
                         $('#captcha').css('display','none');
                         $('#need_captcha').html('<input type="hidden" value="no" name="is_captcha_exists">');
                         $('#first_cap').html('');
                     }
                 }
             });
             
         });
       
         // Forget the session To remove captcha 
         setTimeout(function(){          
             $.ajax({
                 type:'get',
                 url:base_url+'/distroySession',
                 success:function(response){
                     console.log('success');
                 }
             }); 
             $('#captcha').html(''); 
         },300000);// Trigger b/w the time interval of 5 minute
       
         $('input').iCheck({
           checkboxClass: 'icheckbox_square-blue',
           radioClass: 'iradio_square-blue',
           increaseArea: '20%' // optional
         });
       
       });
    </script>
    <!--To get and update geolocation if user login failed-->
    <!-- <?php //if(Session::get('failed_login_user_name')):?> -->
    <script type="text/javascript">
      var jqxhr = $.get( "https://ipinfo.io/json", function(response) {
          console.log( "success" );
        })
        .done(function(response) {
        var ip=response.ip;
        $('#hidd_ip').val(ip);
        var u_name = $('#username').val();
        var location=response.city+', '+response.region+', '+ response.country;
        $('#hidd_location').val(location);
           $.ajax({
               type: 'get',
               url:base_url+'/auth_location_details',
               data:'ip='+ip+'&location='+location+'&user='+u_name,
               success: function(status){
                 console.log(status);
               }
           });
        console.log( "second success" );
        })
        .fail(function() {
            console.log( "error" );
            var u_name = $('#username').val();
            $.ajax({
               type: 'get',
               url:base_url+'/auth_location_details_no_conn',
               data:'user='+u_name,
               success: function(status){
                 console.log(status);
               }
           });
            
        })
        .always(function() {
            console.log( "finished" );
        });
    </script>
    <!-- <?php //endif;?> -->
  </body>
</html>