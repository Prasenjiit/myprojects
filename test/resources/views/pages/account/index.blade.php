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
<?php $user_permission=Auth::user()->user_permission; ?>
    
<section class="content-header">
    <div class="col-sm-8">
        <span style="float:left;">
            <strong>{{trans('language.account')}}</strong> &nbsp;
        </span>
    </div>
    <div class="col-sm-4">
        <!-- <ol class="breadcrumb">
            <li><a href="{{url('/home')}}"><i class="fa fa-dashboard"></i> {{trans('language.home')}}</a></li>
            <li class="active">{{trans('language.account')}}</li>
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
            <div class="col-md-6">
                <!-- company form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{trans('language.account_details')}}</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" title="Collapse"><i class="fa fa-minus"></i>
                            </button>
                        </div>

                    </div><!-- /.box-header -->  
                    <!--SA and GA-->
                    <div class="box-footer" style="">
                        <div class="modal-body">    
                            <!-- form start -->
                            <ul class="list-group list-group-unbordered">
                                
                                <!-- <li class="list-group-item">
                                  {{trans('language.user_name')}} <span class="pull-right">@if(Session::get('settings_username')) {{ Session::get('settings_username') }}@else - @endif</span>
                                </li> -->
                                <li class="list-group-item">
                                  {{trans('language.installation_date')}} <span class="pull-right">@if(Session::get('settings_installation_date')) {{ Session::get('settings_installation_date') }}@else - @endif </span>
                                </li>
                                <li class="list-group-item">
                                  {{trans('language.no_of_user')}} <span class="pull-right">@if(Session::get('settings_no_of_users')) {{ Session::get('settings_no_of_users') }}@else - @endif</span>
                                </li>
                                <li class="list-group-item">
                                  {{trans('language.view_user_lic')}} <span class="pull-right">@if(Session::get('settings_view_only_users')) {{ Session::get('settings_view_only_users') }}@else - @endif</span>
                                </li>
                                <li class="list-group-item">
                                  {{trans('language.licence_key')}} <span class="pull-right">@if(Session::get('settings_license_key')) {{ Session::get('settings_license_key') }} <img src="images/checked.png" alt="active">@else - @endif</span>
                                </li>
                                <li class="list-group-item">
                                  {{trans('language.license_expiry_date')}} <span class="pull-right">@if(Session::get('settings_expiry_date')) {{ Session::get('settings_expiry_date') }}@else - @endif</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div><!--// General form end--> 

            <div class="col-md-6">
                <!-- company form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{trans('language.module_details')}}</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" title="Collapse"><i class="fa fa-minus"></i>
                            </button>
                        </div>

                    </div><!-- /.box-header -->  
                    <!--SA and GA-->
                    <div class="box-footer" style="">
                        <div class="modal-body">    
                            <!-- form start -->
                            <ul class="list-group list-group-unbordered">
                                <?php $totalrows = Session::get('totalcnt_av'); 
                                for($i=1;$i<=$totalrows;$i++){ ?>
                                    <li class="list-group-item"><?php echo Session::get('module_name_av'.$i); ?>
                                     <span class="pull-right"><?php if(Session::get('enbval_av'.$i)==Session::get('tval_av')){ ?><img src="images/checked.png" alt="active"><?php }else if(Session::get('enbval_av'.$i)==Session::get('fval_av')){ ?><img src="images/cancel.png" alt="inactive"><?php }else{ ?><img src="images/cancel.png" alt="inactive"> <?php } ?></span><br/> 
                                     <?php if(Session::get('enbval_av'.$i)==Session::get('tval_av')){ ?>
                                     {{trans('language.activated_on')}} <?php echo Session::get('module_activation_date_av'.$i); ?><span class="pull-right">{{trans('language.expired_on')}} <?php echo Session::get('module_expiry_date_av'.$i); ?></span>
                                 <?php } ?>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                    <?php if(Session::get('onpremise')==0){ ?>
                        <div class="box-footer" style="">
                            <div class="modal-body">  
                                <input type="hidden" name="lickey" id="lickey" value="{{ Session::get('settings_license_key') }}">
                                <input type="hidden" name="onpremise" id="onpremise" value="<?php echo Session::get('onpremise'); ?>">
                                <input type="hidden" name="diskkey" id="diskkey" value="<?php echo Session::get('settings_volume_label'); ?>">
                                <input type="button" name="Sync" value="Sync" id="syncmodule" class="btn btn-default btn-flat">
                                <div class="errmsg" id="errmsg"></div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div><!--// General form end--> 

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
<script type="text/javascript">
    $(document).ready(function(){
     //$(document).on("click","#activate",function(){
        $('#syncmodule').on('click', function(){
            var lickey = $("#lickey").val();
            var onpremise = $("#onpremise").val();
            var diskkey = $("#diskkey").val();

            if(lickey!=''&& onpremise==0){
                var modurl = "<?php echo config('app.module_update_url'); ?>"; 
                var value = {
                    lickey:lickey,diskkey:diskkey,
                    url:modurl,
                    method : "online",
                };
                $("#errmsg").html('');
                $.ajax(
                {
                    url: '{{URL('mod_update_req')}}',
                    type: "get",
                    data : value,
                    success: function(data, textStatus, jqXHR)
                    {   
                        if((data=="mismatch")||(data=="error")||(data=="not found")){
                            $("#errmsg").html('License key you have entered is invalid.');
                        }else{
                            //$("#activate").css("display","none");
                            //$("#login").css("display","block");
                            $("#errmsg").html('Modules updated successfully');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown)
                    {
                       //alert("error");
                       $("#result").html("error");
                    }
                });
            }else{
                $("#errmsg").html('License key/Premises status not valid.');
            }
        });
    });
 
</script>

@endsection