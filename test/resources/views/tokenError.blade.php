<?php
include (public_path()."/storage/includes/lang1.en.php" );
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>DMS | 404 Error</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    {!! Html::style('bootstrap/css/bootstrap.min.css') !!} 
     <!-- Theme style -->
    {!! Html::style('dist/css/AdminLTE.min.css') !!} 
   
    <style type="text/css">
        p{
            color: white;
            font-size: 44px;
            margin-top: 1px;
            text-align: center;
        }
        .sub a {
            color: beige;
            background: #06afd8;
            text-decoration: none;
            padding: 5px;
            font-size: 13px;
            font-family: arial, serif;
            font-weight: bold;
        }
        .logo h1 {
            font-size: 200px;
            color: #8F8E8C;
            text-align: center;
            margin-bottom: 1px;
            text-shadow: 1px 1px 6px #fff;
        }
        .sub a:hover {
                    background-color: #1d7a90;
                } 
       
    </style>
    <!--[endif]-->
  </head>
    <body class="hold-transition login-page">
        <div class="login-box" style="width:auto; !important;">
            <div class="logo">
                <h1>404</h1>
                <p>OOPS! {{$language['token_error']}} Please Go Back.</p>

                <div class="sub">
                    <p><a href="{{url('home')}}">Go Back</a></p>
                </div>

            </div>   
        </div>

        <!-- jQuery 2.1.4 -->
        {!! Html::script('plugins/jQuery/jQuery-2.1.4.min.js') !!}
        {!! Html::script('bootstrap/js/bootstrap.min.js') !!}

  </body>
</html>
