<?php
  include (public_path()."/storage/includes/lang1.en.php" );
  ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>FileEazy | Activate</title>
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
                
                <div class="form-group has-feedback">
                    <label id="errmsg"><?php echo Session::get('lic_mesage');?></label>
                </div>

                  <div class="form-group has-feedback">
                    <label>License Key</label>
                    <input type="text" name="serialkey" id="serialkey" placeholder="XXXXX-XXXXX-XXXXX-XXXXX-X" class="form-control">
                    <span
                      class="glyphicon glyphicon-lock form-control-feedback"></span>
                    <span><font color="red"></font></span>
                  </div>
                  <div class="row">
                    <!-- /.col -->
                    <div class="col-xs-4">
                        <input type="button" name="activate" value="Activate" class="btn login-btn-primary btn-block btn-flat" id="activate">
                        <input type="hidden" name="diskkey" id="diskkey" value="<?php echo Session::get('settings_volume_label'); ?>">
                        <a href="{{ url('login') }}" class="btn login-btn-primary btn-block btn-flat" id="login" style="display: none;">Login Now</a>
                    </div>
                    <!-- /.col -->
                  </div>
                  
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
?>
<script type="text/javascript">
     $(document).on("click","#activate",function(){
        var serialkey = $("#serialkey").val();
        var diskkey = $("#diskkey").val();
        var url = "<?php echo config('app.activate_url'); ?>"; 
        var value = {
            serialkey:serialkey,diskkey:diskkey,
            url:url,
            method : "online",
        };
        $("#errmsg").html('');
        $.ajax(
        {
            url: '{{URL('key_activate_req')}}',
            type: "get",
            data : value,
            success: function(data, textStatus, jqXHR)
            {   
                if((data=="mismatch")||(data=="error")||(data=="not found")){
                    $("#errmsg").html('License key you have entered is invalid.');
                }else{
                    $("#activate").css("display","none");
                    $("#login").css("display","block");
                    $("#errmsg").html('Validation success. Login Now');
                }
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
               alert("error");
               //$("#result").html("error");
            }
        });
    })
 
</script>
</body>
</html>