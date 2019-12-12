<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>File-Eazy</title>
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport"> -->
  <link rel="shortcut icon" href="<?php echo e(asset('images/icons/favicon.ico')); ?>" type="image/x-icon">
  <link rel="icon" href="<?php echo e(asset('images/icons/favicon.ico')); ?>" type="image/x-icon">
  <?php echo Html::style('dist/css/googlefont1.css'); ?>

  <!-- Bootstrap 3.3.5 -->
  <?php echo Html::style('bootstrap/css/bootstrap.min.css'); ?> 

  <!-- Font Awesome -->
  <?php echo Html::style('css/font-awesome.min.css'); ?>

  <!-- Ionicons -->
  <?php echo Html::style('css/ionicons.min.css'); ?>

  <!-- Theme style -->
  <?php echo Html::style('dist/css/AdminLTE.min.css'); ?> 
<!-- AdminLTE Skins. Choose a skin from the css/skins
  folder instead of downloading all of them to reduce the load. -->
  <?php echo Html::style('dist/css/skins/_all-skins.min.css'); ?> 
  <!-- iCheck -->
  <?php echo Html::style('plugins/iCheck/flat/blue.min.css'); ?> 
  <!-- Morris chart -->
  <?php echo Html::style('plugins/morris/morris.css'); ?> 
  <!-- jvectormap -->
  <?php echo Html::style('plugins/jvectormap/jquery-jvectormap-1.2.2.css'); ?> 
  <!-- Date Picker -->
  <?php echo Html::style('plugins/datepicker/datepicker3.css'); ?> 
  <!-- Daterange picker -->
  <?php echo Html::style('plugins/daterangepicker/daterangepicker-bs3.css'); ?> 
  <!-- bootstrap wysiHTML5 - text editor -->
  <?php echo Html::style('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?> 

  <?php echo Html::style('css/parsley.css'); ?>

  <?php echo Html::style('css/custom-css.css'); ?>

  <?php echo Html::style('css/global.css'); ?> <!--Tooltip css-->
  <style>
div#google_translate_element div.goog-te-gadget-simple {font-size: 0.15pt;}
div#google_translate_element div.goog-te-gadget-simple {background-color: transparent;}
div#google_translate_element div.goog-te-gadget-simple a.goog-te-menu-value span {color: white;}
div#google_translate_element div.goog-te-gadget-simple a.goog-te-menu-value span:hover {color: white;}
div#google_translate_element div.goog-te-gadget-simple {border: none;}
</style>

  <script type="text/javascript">
    // Initilizing language files that call in js
    var baseurl        = "<?php echo e(config('app.url')); ?>";
    var swalAreYouSure = "<?php echo e(@$language['Swal_are_you_sure']); ?>";
    var swalNotRevert  = "<?php echo e(@$language['Swal_not_revert']); ?>";
    var swalDeleted    = "<?php echo e(@$language['Swal_deleted']); ?>";
    var warning        = "<?php echo e(@$language['Swal_warning']); ?>";
    var confirm_delete_single = "<?php echo e(@$language['confirm_delete_single']); ?>";

    // For set length menu in datatable
    function getLengthMenu(){
      var rows_per_page = '<?php echo e(Session('settings_rows_per_page')); ?>';
      
      if(rows_per_page>100){
        var expression = true; 
      }else{
        var expression = rows_per_page;
      }

      switch(expression){
          case '10':
          case '20':
          case '30':
          case '40':
          case '50':
          case '60':
          case '70':
          case '80':
          case '90':
          case '100':
                  return [10,20,30,40,50,60,70,80,90,100];
                  break;
          case (rows_per_page>100):
                  return [10,20,30,40,50,60,70,80,90,100,rows_per_page];
                  break;
      }
    }

  </script>

<?php echo Html::script('plugins/jQuery/jQuery-2.1.4.min.js'); ?>


<!-- jQuery UI 1.11.4 -->
<!-- <?php echo Html::script('https://code.jquery.com/ui/1.11.4/jquery-ui.min.js'); ?> -->
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<!--<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>-->
<!-- Bootstrap 3.3.5 -->
<?php echo Html::script('bootstrap/js/bootstrap.min.js'); ?>

<!-- Morris.js charts -->
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"> -->
<?php echo Html::script('plugins/morris/morris.min.js'); ?>

<!-- Sparkline -->
<?php echo Html::script('plugins/sparkline/jquery.sparkline.min.js'); ?>

<!-- jvectormap -->
<?php echo Html::script('plugins/jvectormap/jquery-jvectormap-1.2.2.min.js'); ?>

<?php echo Html::script('plugins/jvectormap/jquery-jvectormap-world-mill-en.js'); ?>

<!-- jQuery Knob Chart -->
<?php echo Html::script('plugins/knob/jquery.knob.js'); ?>

<!-- daterangepicker -->
<?php echo Html::script('js/moment.min.js'); ?>

<?php echo Html::script('plugins/daterangepicker/daterangepicker.js'); ?>

<!-- datepicker -->
<?php echo Html::script('plugins/datepicker/bootstrap-datepicker.js'); ?>

<!-- Bootstrap WYSIHtml5 -->
<?php echo Html::script('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>

<!-- Slimscroll -->
<?php echo Html::script('plugins/slimScroll/jquery.slimscroll.min.js'); ?>

<!-- FastClick -->
<?php echo Html::script('plugins/fastclick/fastclick.min.js'); ?>

<!-- AdminLTE App -->
<?php if(Request::segment(1) != 'documentManagementView'): ?>
  <?php echo Html::script('dist/js/app.min.js'); ?>

<?php endif; ?>
<!-- AdminLTE dashboard demo (This is only for demo purposes)     
  <?php echo Html::script('dist/js/pages/dashboard.js'); ?>-->

  <!--Sweet alert-->
  <?php echo Html::style('css/sweetalert2.min.css'); ?> 
  <?php echo Html::script('js/sweetalert2.min.js'); ?> 

  <!-- AdminLTE for demo purposes 
  <?php echo Html::script('dist/js/demo.js'); ?>  -->

  <?php echo Html::style('css/component.css'); ?>

  <?php echo Html::script('js/modernizr.custom.js'); ?> 

 
    <style type="text/css">

    </style>

    <script type="text/javascript">
      /*<--Initilize base url-->*/
      var base_url_path  = "<?php echo e(config('app.url')); ?>";
      
      $(document).ready(function(){

        // Disable right click
        // $(document).bind("contextmenu",function(e){
        //   return false;
        // })

        //<!--Delete audits record-->
        $('body').on('click','#delete-audits',function(){

          var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
          var userName   = $(this).attr('user_name');
          var user_full_name   = $(this).attr('user_full_name');
          var delete_from_date = $(this).attr('delete_from_date');
          var delete_to_date  = $(this).attr('delete_to_date');
          var ownUser       = 'No';

          swal({
              title: '<?php echo e($language['plse_entr_ur_pswd']); ?>', 
              input: 'password',
              showCancelButton: true
            }).then((result) => {
                if(result){
                  deleteAuditsRecords(CSRF_TOKEN,userName,delete_from_date,delete_to_date,result,ownUser,user_full_name);               
                }else{
                  swal("<?php echo e($language['password_is_required']); ?>");
                }

            })
      }); 

      // Delete tbl_audits_delete_request when requested user get the notification
      $('body').on('click','#delete-audits-request',function(){
          var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
          var request_username   = $(this).attr('request_username');
              
            $.ajax({
              type:'POST',
              url:'<?php echo e(URL('deleteAuditsRequest')); ?>',
              data:{_token:CSRF_TOKEN,request_username:request_username},
              success:function(response){
                console.log("success");
              }
            });
        });
       //<!--Delete audits records ENd-->

      });  

      /*<--Common function-->*/
      function deleteAuditsRecords(CSRF_TOKEN,userName,delete_from_date,delete_to_date,result,ownUser,user_full_name){
        if(ownUser == 'Yes'){
          // If only one admin
          confirmButtonStatus = false;
        }else{
          // More than one admin
          confirmButtonStatus = true;
        }
        $.ajax({
              type:'POST',
              url:'<?php echo e(URL('deleteAudits')); ?>',
              data:{_token:CSRF_TOKEN,userName:userName,delete_from_date:delete_from_date,delete_to_date:delete_to_date,input_val:result,ownUser:ownUser,user_full_name:user_full_name},
              success:function(response){
                var time = "2000";
                if(response == '0'){
                  swal({text:'<?php echo e($language['wrong_password']); ?>',
                      showConfirmButton: confirmButtonStatus});
                }else if(response == '1'){
                  swal({text:'<?php echo e($language['approved_deleted_success']); ?>',
                      showConfirmButton: confirmButtonStatus});
                }else if(response == '2'){
                  swal({text:'<?php echo e($language['already_approved']); ?>',
                      showConfirmButton: confirmButtonStatus});
                }else if(response == '3'){
                  swal({text:'<?php echo e($language['deleted_audits']); ?>',
                      showConfirmButton: confirmButtonStatus});
                }else if(response == 'failed'){
                  swal('<?php echo e($language['msg_not_send']); ?>');
                  var time = "5000";
                }
                // Success,If only one admin reload it
                if(ownUser == 'Yes'){
                  setTimeout(function(){
                        window.location.reload();
                    },time);
                }
                
              }

        });
      }

    </script>

</head>

<?php
$value = Request::cookie('toggleStatus');
$menuvalue = Session::get('menuid');  

require_once (public_path()."/storage/includes/lang1.en.php");
$user_permission=Auth::user()->user_permission;     
?>
<?php if($value == 1): ?>
<body class="hold-transition <?php echo e(Auth::user()->user_skin); ?> sidebar-mini sidebar-collapse"> 
  <?php else: ?>
  <body class="hold-transition <?php echo e(Auth::user()->user_skin); ?> sidebar-mini"> 
    <?php endif; ?>
    <div class="wrapper">

<header class="main-header">
    <!-- Logo -->
    <a href="<?php echo e(url('/home')); ?>" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->

      <span class="logo-mini"><img src="<?php echo e(URL::to('/')); ?>/images/logo/<?php echo $records->settings_logo;?>" class="user-image" alt="FILEEAZY" style="width: 130px;height: 44px;margin-bottom: 3px;">
        </span>
        <!-- logo for regular state and mobile devices -->
        <div>
      <img src="<?php echo e(URL::to('/')); ?>/images/logo/<?php echo $records->settings_logo;?>" class="user-image hidden-xs hidden-sm" alt="FILEEAZY" style="width: 130px;height: 44px;margin-bottom: 3px;">
        </div>
    </a>

    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button" id="togg" onclick="saveToggle(0);">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <!-- Menu For Small Screens -->
        <div id="dl-menu" class="dl-menuwrapper" style="display: none;">
            <!-- <button class="dl-trigger"></button> -->
            <a href="#" class="sidebar-toggle dl-trigger">
                <span class="sr-only">Toggle navigation</span>
            </a>

            <ul class="dl-menu">
                <li>
                    <a href="#"><i class="fa fa-plus"></i>&nbsp;&nbsp; Add New</a>
                    <ul class="dl-submenu">
                        <li><a href="<?php echo e(url('/documentAdd')); ?>"><i class="fa fa-file-text-o"></i>&nbsp;&nbsp;<?php echo e($language['new doc']); ?></a></li>
                        <li><a href="<?php echo e(url('/documentAdd')); ?>"><i class="fa fa-database"></i>&nbsp;&nbsp;<?php echo e($language['new doc type']); ?></a></li>
                        <li><a href="<?php echo e(url('/stacks')); ?>"><i class="fa fa-stack-overflow"></i>&nbsp;&nbsp;<?php echo e($language['new stack']); ?></a></li>
                        <li><a href="<?php echo e(url('/departments')); ?>"><i class="fa fa-university"></i>&nbsp;&nbsp;<?php echo e($language['new dept']); ?></a></li>
                        <li><a href="<?php echo e(url('/tagWords')); ?>"><i class="fa fa-tags"></i>&nbsp;&nbsp;<?php echo e($language['add category']); ?></a></li>
                        <li><a href="<?php echo e(url('/allworkflow')); ?>"><i class="fa fa-map"></i>&nbsp;&nbsp;<?php echo e($language['add/view workflow']); ?></a></li>
                        <li><a href="<?php echo e(url('/forms')); ?>"><i class="fa fa-newspaper-o"></i>&nbsp;&nbsp;<?php echo e($language['add_form']); ?></a></li>
                        <li><a href="<?php echo e(url('/users')); ?>"><i class="fa fa-users"></i>&nbsp;&nbsp;<?php echo e($language['add_user']); ?></a></li>
                    </ul>
                </li>  
                <li><a href="<?php echo e(url('listview')); ?>?view=import"><i class="fa fa-download"></i>&nbsp;&nbsp;<?php echo e($language['import_data']); ?></a></li>
                <li><a href="<?php echo e(url('export')); ?>"><i class="fa fa-upload"></i>&nbsp;&nbsp;<?php echo e($language['export data']); ?></a></li>
                <li><a href="<?php echo e(url('listview')); ?>?view=checkout"><i class="fa fa-share"></i>&nbsp;&nbsp;<?php echo e($language['chkout list']); ?></a></li>
                <li><a href="<?php echo e(url('/documentAdvanceSearch')); ?>"><i class="fa fa-fw fa-search-plus"></i>&nbsp;&nbsp;<?php echo e($language['advance search']); ?></a></li>
                <li><a href="<?php echo e(url('/settings')); ?>"><i class="fa fa-cogs"></i>&nbsp;&nbsp;<?php echo e($language['settings']); ?></a></li>
            </ul>


        </div><!-- /dl-menuwrapper -->



        <!-- Menu For Large Screens -->
        <div class="topbar hidden-md hidden-sm hidden-xs">
            <?php if(stristr($user_permission,"add")): ?>
                <span class="fa-stack topsize" style="float:left; width:95px;">
                    <a href="<?php echo e(url('/documentAdd')); ?>">
                        <i class="fa fa-square fa-stack-2x" style="top:1px; col-sm-12; text-align:center; width:95px;"></i>
                        <i class="fa fa-file-text-o fa-stack-1x fa-inverse" style="text-align:center; width:95px;"></i>
                        <div id = "nav_icon" style="font-size:12px; color:#FFFFFF; width:95px; text-align:center; padding-top:25px;"><?php echo e($language['new doc']); ?></div>
                    </a>
                </span>
                
                <?php if(Auth::user()->user_role != Session::get('user_role_private_user')): ?>
                <span class="fa-stack topsize" style="float:left; width:115px;">
                    <a href="<?php echo e(url('/documentTypes')); ?>">
                        <i class="fa fa-square fa-stack-2x" style="top:1px; text-align:center; width:115px;"></i>
                        <i class="fa fa-database fa-stack-1x fa-inverse" style="text-align:center; width:115px;"></i>
                        <div id = "nav_icon" style="font-size:12px; color:#FFFFFF; width:115px; text-align:center; padding-top:25px;"><?php echo e($language['new doc type']); ?></div>
                    </a>
                </span>
           

                <span class="fa-stack topsize" style="float:left; width:100px;" >
                    <a href="<?php echo e(url('/stacks')); ?>">
                        <i class="fa fa-square fa-stack-2x" style="top:1px; text-align:center; width:100px;"></i>
                        <i class="fa fa-stack-overflow fa-stack-1x fa-inverse" style="text-align:center; width:100px;"></i>
                        <div id = "nav_icon" style="font-size:12px; color:#FFFFFF; width:100px; padding-top:25px; text-align:center;"><?php echo e($language['new stack']); ?></div>
                    </a>
                </span>
                <?php endif; ?>
            <?php endif; ?>
            <span class="fa-stack topsize" style="float:left; width:100px;">
                <a href="<?php echo e(url('/documentAdvanceSearch')); ?>">
                    <i class="fa fa-square fa-stack-2x" style="top:1px; width:100px; text-align:center;"></i>
                    <i class="fa fa-search-plus fa-stack-1x fa-inverse" style="text-align:center; width:100px;"></i>
                    <div id = "nav_icon" style="font-size:12px; color:#FFFFFF; width:100px; padding-top:25px; text-align:center;"><?php echo e($language['advance search']); ?></div>
                </a>
            </span>

            <?php if(Auth::user()->user_role == Session::get('user_role_super_admin')): ?>
            <span class="fa-stack topsize" style="float:left; width:70px;">
                <a href="<?php echo e(url('/settings')); ?>">
                    <i class="fa fa-square fa-stack-2x" style="text-align:center; top:1px; width:70px;"></i>
                    <i class="fa fa-cogs fa-stack-1x fa-inverse" style="text-align:center; width:70px;"></i>
                    <div id = "nav_icon" style="font-size:12px; color:#FFFFFF; width:100px; padding-top:25px; text-align:center; width:70px;"><?php echo e($language['settings']); ?></div>
                </a>
            </span>
            <?php endif; ?>

            <span class="fa-stack topsize" style="float:left; width:95px;">
                <a class="noHover">
                    
                    <i class="fa fa-stack-1x fa-inverse" style="text-align:center; width:95px;"><div id="google_translate_element"></div></i>
                    <div id = "nav_icon" style="font-size:12px; color:#FFFFFF; width:95px; text-align:center; padding-top:25px;">Translate</div>
                </a>
            </span>
            
        </div>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav pull-right">
                <!--Notification-->
                <?php $notCount = '0';?>             
                <li class="dropdown notifications-menu" id="li_top_notification">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-bell-o"></i>
                        <span class="label label-warning">0</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">Loading notification.....</li>
                    </ul>  
                </li>
                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="<?php echo e(URL::to('/')); ?>/images/logo/avatar.png" class="user-image" alt="User Image"> 
                        <span class="hidden-xs"><?php echo e(ucfirst(Auth::user()->user_full_name)); ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="<?php echo e(URL::to('/')); ?>/images/logo/<?php echo $records->settings_logo;?>" class="img-circle" alt="User Image"> 
                            <p>
                                <?php echo e(ucfirst(Auth::user()->user_full_name)); ?><br/>
                                <?php if(Auth::user()->user_role == Session::get('user_role_super_admin')): ?>
                                    <small>Super Admin</small>
                                <?php elseif(Auth::user()->user_role == Session::get('user_role_group_admin')): ?>
                                    <small>Group Admin</small>
                                <?php else: ?>
                                    <small>Regular User</small>
                                <?php endif; ?>
                            </p>
                            <?php if(Auth::user()->user_role == Session::get('user_role_super_admin')): ?>   
                                <p>
                                    <a href="<?php echo e(url('account')); ?>" class="btn btn-default btn-flat"><?php echo e(trans('language.account')); ?></a>
                                </p>
                            <?php endif; ?>
                        </li>

                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="<?php echo e(url('userEdit', Auth::user()->id )); ?>" class="btn btn-default btn-flat"><?php echo e(trans('language.profile')); ?></a>
                            </div>
                            <div class="pull-right">
                                <a href="<?php echo e(url('logout')); ?>" class="btn btn-default btn-flat"><?php echo e($language['sign out']); ?></a>
                            </div>
                        </li>
                    </ul>
                </li>
                <!-- Control Sidebar Toggle Button -->
                <li>
                    <a href="#" data-toggle="control-sidebar"><i class="fa fa-cog"></i></a>
                </li>
            </ul>
      </div>
    </nav>
  </header>

    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu" data-widget="tree">
                <!-- Sidebar user panel -->
            <ul class="sidebar-menu">
                <li class="header">MAIN NAVIGATION</li>
                <!-- Dashboard -->
                <?php if($menuvalue == 5): ?>
                    <li class="active"><a href="<?php echo e(url('/home')); ?>"><i class="fa fa-dashboard"></i> <span><?php echo e($language['dashboard']); ?></span></a></li>
                <?php else: ?>
                    <li><a href="<?php echo e(url('/home')); ?>"><i class="fa fa-dashboard"></i> <span><?php echo e($language['dashboard']); ?></span></a></li>
                <?php endif; ?>
                <?php if(Session::get('module_activation_key7')==1): ?> 
                  <!-- module section -->
                  <li class="<?php if($menuvalue == '-1'): ?> active <?php endif; ?>"><a href="<?php echo e(url('/apps')); ?>"><i class="fa  fa-clone"></i> <span>Home</span></a></li>  
                <?php endif; ?>

                <!-- All Documents -->
                <?php if($menuvalue == 1 || $menuvalue == 0): ?> 
                    <li class="active"><a <?php if(Auth::user()->user_documents_default_view == 'list'){?> href="<?php echo e(url('listview')); ?>?view=list" <?php } else {?> href="<?php echo e(url('/documents')); ?>" <?php }?> id="navigate_default"><i class="fa fa-file-text-o"></i> <span><?php echo e($language['all documents']); ?></span></a></li>
                <?php else: ?>
                    <li><a <?php if(Auth::user()->user_documents_default_view == 'list'){?> href="<?php echo e(url('listview')); ?>?view=list" <?php } else {?> href="<?php echo e(url('/documents')); ?>" <?php }?> id="navigate_default"><i class="fa fa-file-text-o"></i> <span><?php echo e($language['all documents']); ?></span></a></li>
                <?php endif; ?>

                <!-- Document Types -->
               
                    <li class="<?php if($menuvalue == 2):?> active <?php endif;?>">
             
                        <a href="<?php echo e(url('/documentTypes')); ?>" class="dms-dz-remove">
                            <i class="fa fa-database" aria-hidden="true"></i> 
                            <span><?php echo e($language['document types']); ?></span>
                            <span class="pull-right-container">
                               <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a> 
                        <ul class="treeview-menu">
                          <li class="<?php if(Request::segment(1) == 'documentTypes') echo 'light';?>"><a href="<?php echo e(url('/documentTypes')); ?>"><i class="fa fa-angle-right"></i><?php echo e($language['all document types']); ?></a></li>
                          <?php $__currentLoopData = $doctypeApp; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>                    
                          <li class="<?php if((Request::segment(2) == $val->document_type_id && Input::get('page') == 'documentType') || @$_GET['id'] == $val->document_type_id ) echo 'light';?>"><a href="<?php echo e(url('listview')); ?>?view=documentType&id=<?php echo e($val->document_type_id); ?>"><span style="position: relative;left: 7%;"><i style="position: relative;right: 4%;" class="fa fa-angle-right"></i> <?php echo e($val->document_type_name); ?></span></a></li>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </li>
                
                  
                        <li class="<?php if($menuvalue == 3):?> active <?php endif;?>">
                  
                          <a href="<?php echo e(url('/departments')); ?>" class="dms-dz-remove">
                              <i class="fa fa-university" aria-hidden="true"></i> 
                              <span><?php if(Session::get('settings_department_name')): ?> <?php echo e(Session::get('settings_department_name')); ?><?php else: ?> <?php echo e($language['departments']); ?> <?php endif; ?> </span>
                              <span class="pull-right-container">
                                 <i class="fa fa-angle-left pull-right"></i>
                              </span>
                          </a>
                          <ul class="treeview-menu">
                            <li class="<?php if(Request::segment(1) == 'departments') echo 'light';?>"><a href="<?php echo e(url('/departments')); ?>"><i class="fa fa-angle-right"></i><?php if(Session::get('settings_department_name_all')): ?> <?php echo e(Session::get('settings_department_name_all')); ?><?php else: ?> <?php echo e($language['all departments']); ?> <?php endif; ?><!-- <?php echo e($language['all departments']); ?> --></a></li>
                            <?php $__currentLoopData = $deptApp; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="<?php if((Request::segment(2) == $val->department_id && Input::get('page') == 'department') || @$_GET['id'] == $val->department_id ) echo 'light';?>"><a href="<?php echo e(url('listview')); ?>?view=department&id=<?php echo e($val->department_id); ?>"><span style="position: relative;left: 7%;"><i style="position: relative;right: 4%;" class="fa fa-angle-right"></i> <?php echo e($val->department_name); ?></span></a></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                          </ul>
                      </li>                  

                  
                    <li class="<?php if($menuvalue == 7):?> active <?php endif;?>">
                  
                          <a href="<?php echo e(url('/stacks')); ?>" class="dms-dz-remove">
                              <i class="fa fa-stack-overflow" aria-hidden="true"></i> 
                              <span><?php echo e($language['stacks']); ?></span>
                              <span class="pull-right-container">
                                 <i class="fa fa-angle-left pull-right"></i>
                              </span>
                          </a>
                          <ul class="treeview-menu">
                            <li class="<?php if(Request::segment(1) == 'stacks') echo 'light';?>"><a href="<?php echo e(url('/stacks')); ?>"><i class="fa fa-angle-right"></i><?php echo e($language['all stacks']); ?></a></li>

                            <?php $__currentLoopData = $stckApp; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="<?php if((Request::segment(2) == $val->stack_id && Input::get('page') == 'stack') || @$_GET['id'] == $val->stack_id ) echo 'light';?>"><a href="<?php echo e(url('listview')); ?>?view=stack&id=<?php echo e($val->stack_id); ?>"><span style="position: relative;left: 7%;"><i style="position: relative;right: 4%;" class="fa fa-angle-right"></i> <?php echo e($val->stack_name); ?></span></a></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                          </ul>
                      </li>
                  

                <?php if(Auth::user()->user_role != Session::get('user_role_regular_user') && (Auth::user()->user_role != Session::get('user_role_private_user'))): ?>
                    <?php if($menuvalue == 8): ?>
                        <li class="active"><a href="<?php echo e(url('/tagWords')); ?>"><i class="fa fa-tags" aria-hidden="true"></i><span><?php echo e($language['tag words']); ?></span></a></li>
                    <?php else: ?>
                        <li><a href="<?php echo e(url('/tagWords')); ?>"><i class="fa fa-tags" aria-hidden="true"></i><span><?php echo e($language['tag words']); ?></span></a></li> 
                    <?php endif; ?> 
                <?php endif; ?>
                
                <?php if(Session::get('module_activation_key4')==1): ?>  
                    <?php if(date("Y-m-d") > Session::get('module_expiry_date4')): ?> 
                    <?php else: ?>                    
                        <!-- workflows -->      
                        <?php if(stristr($user_permission,"workflow")): ?>
                            <li class="<?php if($menuvalue == 12):?> active <?php endif;?>">
                                <a href="<?php echo e(url('/allworkflow')); ?>" class="dms-dz-remove">
                                    <i class="fa fa-map" aria-hidden="true"></i> 
                                    <span><?php echo e($language['workflows']); ?></span>
                                    <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <?php $page = 'workflow' ?>
                                <ul class="treeview-menu">
                                    <li class="<?php if(Request::segment(1) == 'allworkflow') echo 'light';?>"><a href="<?php echo e(url('/allworkflow')); ?>"><i class="fa fa-angle-right"></i><?php echo e($language['all workflows']); ?></a></li>
                                    <?php  $workflowsapp = Session::get('workflowsapp');?>
                                    <?php $__currentLoopData = $workflowsapp; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $wf_id = (isset($workflow_id))?$workflow_id:''; ?>
                                    <li class="<?php if(($wf_id == $val->id && $page == 'workflow')) echo 'light';?>"><a href="<?php echo e(url('/view_workflow',$val->id)); ?>"><span style="position: relative;left: 7%;"><i style="position: relative;right: 4%;" class="fa fa-angle-right"></i> <?php echo e(ucfirst($val->workflow_name)); ?></span></a></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                        <!-- end workflows -->   
                        
                        <!-- forms -->              
                        <li class="<?php if($menuvalue == 14):?> active <?php endif;?>">
                            <a href="<?php echo e(url('/forms')); ?>" class="dms-dz-remove">
                                <i class="fa fa-newspaper-o" aria-hidden="true"></i> 
                                <span>Forms</span>
                                <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <!-- all forms menu -->
                                <li class="<?php if(Request::segment(1) == 'forms') echo 'light';?>"><a href="<?php echo e(url('/forms')); ?>"><i class="fa fa-angle-right"></i>All Forms</a></li>
                                <!-- my form menu -->
                               <!--  <li class="<?php if(Request::segment(1) == 'my_forms') echo 'light';?>"><a href="<?php echo e(url('/my_forms')); ?>"><i class="fa fa-angle-right"></i>My Forms</a></li> -->
                                <?php  $formapp = Session::get('formapp');?>
                                <?php $__currentLoopData = $formapp; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="<?php if((Request::segment(2) == $val->form_id && Request::segment(1) == 'form_details')) echo 'light';?>"><a href="<?php echo e(URL::route('form_details',$val->form_id)); ?>" id="viewform" form_id='<?php echo e($val->form_id); ?>' style="cursor: pointer;"><span style="position: relative;left: 7%;"><i style="position: relative;right: 4%;" class="fa fa-angle-right"></i> <?php echo e(ucfirst($val->form_name)); ?></span></a></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </li>
                        <!-- end forms -->  
              
                        <li class="<?php if($menuvalue == '13'): ?> active <?php endif; ?>">
                            <a href="<?php echo e(url('/activities')); ?>">
                                <i class="fa fa-cubes" aria-hidden="true"></i> 
                                <span><?php echo e($language['activities']); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php if(Session::get('module_activation_key1')==1): ?>  
                    <?php if(date("Y-m-d") > Session::get('module_expiry_date1')): ?> 
                    <?php else: ?>
                        <?php if(stristr($user_permission,'import')): ?>  
                     
                            <?php if($menuvalue == 16): ?>
                                <li class="active"><a href="<?php echo e(url('/listview')); ?>?view=import"><i class="fa fa-download"></i> <span><?php echo e($language['import_data']); ?></span></a></li>
                            <?php else: ?>
                                <li><a href="<?php echo e(url('/listview')); ?>?view=import"><i class="fa fa-download"></i> <span><?php echo e($language['import_data']); ?></span></a></li>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if(stristr($user_permission,'export')): ?>  
                            <?php if($menuvalue == 17): ?>
                                <li class="active"><a href="<?php echo e(url('export')); ?>"><i class="fa fa-upload"></i> <span><?php echo e($language['export data']); ?></span></a></li>
                            <?php else: ?>
                                <li><a href="<?php echo e(url('export')); ?>"><i class="fa fa-upload"></i> <span><?php echo e($language['export data']); ?></span></a></li>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
                <li class="<?php if($menuvalue == 22):?> active <?php endif;?>">
                    <a href="<?php echo e(url('dimentioncombinations')); ?>"><i class="fa fa-share-square-o"></i> <span>Dimension</span></a>
                </li>

                <li class="<?php if($menuvalue == 23):?> active <?php endif;?>">
                    <a href="<?php echo e(url('CBMaster')); ?>"><i class="fa fa-share-square-o"></i> <span>CB Transfer</span></a>
                </li>

                <li class="<?php if($menuvalue == 18):?> active <?php endif;?>">
                    <a href="<?php echo e(url('listview')); ?>?view=checkout"><i class="fa fa-share-square-o"></i> <span><?php echo e($language['chkout list']); ?></span></a>
                </li>
                <!-- forms -->              
                <li class="<?php if($menuvalue == 4):?> active <?php endif;?>">
                    <a href="" class="dms-dz-remove">
                        <i class="fa fa-ellipsis-h" aria-hidden="true"></i> 
                        <span>More</span>
                        <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                            <li class="<?php if(Request::segment(1) == 'users') echo 'light';?>" style="padding-bottom: 5px; padding-top: 5px;"><a href="<?php echo e(url('/users')); ?>"><span style="position: relative;left: 7%;"><i class="fa fa-users"></i> <span>&nbsp;&nbsp;<?php echo e($language['users']); ?></span></a></li>
                        <?php if(Session::get('module_activation_key5')==1): ?>  
                            <?php if(date("Y-m-d") > Session::get('module_expiry_date5')): ?> 
                            <?php else: ?>
                                <?php if(Auth::user()->user_role == Session::get('user_role_super_admin')): ?>
                                    <li class="<?php if(Request::segment(1) == 'audits') echo 'light';?>" style="padding-bottom: 5px; padding-top: 5px;"><a href="<?php echo e(url('/audits')); ?>"><span style="position: relative;left: 7%;"><i class="fa fa-history" aria-hidden="true"></i><span>&nbsp;&nbsp;<?php echo e($language['audits']); ?></span></a></li>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if(Auth::user()->user_role == Session::get('user_role_super_admin')): ?>
                                <li class="<?php if(Request::segment(1) == 'settings') echo 'light';?>" style="padding-bottom: 5px; padding-top: 5px;"><a href="<?php echo e(url('/settings')); ?>"><span style="position: relative;left: 7%;"><i class="fa fa-cogs" aria-hidden="true"></i> <span>&nbsp;&nbsp;<?php echo e($language['settings']); ?></span></a></li>

                                <li class="<?php if(Request::segment(1) == 'backup') echo 'light';?>" style="padding-bottom: 5px; padding-top: 5px;"><a href="<?php echo e(url('/backup')); ?>"><span style="position: relative;left: 7%;"><i class="fa fa-hdd-o" aria-hidden="true"></i> <span>&nbsp;&nbsp;<?php echo e(trans('backup_restore.bckprstr')); ?></span></a></li>
                        <?php endif; ?>

                        <li style="padding-bottom: 5px; padding-top: 5px;" class="<?php if(Request::segment(1) == 'faqs') echo 'light';?>"><a href="<?php echo e(url('/faqs')); ?>" ><span style="position: relative;left: 7%;"><i class="fa fa-question-circle" aria-hidden="true"></i> <span>&nbsp;&nbsp;<?php echo e($language['faqs']); ?></span></a></li>

                        <li style="padding-bottom: 5px; padding-top: 5px;"><a href="http://toptechinfo.net/dms/dmshelp/MyDocMan.html" target="_blank"><span style="position: relative;left: 7%;"><i class="fa fa-question" aria-hidden="true"></i><span>&nbsp;&nbsp;<?php echo e($language['help']); ?>&nbsp;&nbsp;<i class="fa fa-external-link" aria-hidden="true"></i></span></a></li>
                    </ul>
                </li>
                <!-- end forms --> 
                
                <li><a href="<?php echo e(url('logout')); ?>"><i class="fa fa-power-off"></i> <span><?php echo e($language['sign out']); ?></span></a></li>
            </ul>
        </section>
        <!-- /.sidebar -->
    </aside>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">        
  <!-- Main content -->
  <?php echo $__env->yieldContent('main_content'); ?>
  <!-- Main content end here -->
</div><!-- /.content-wrapper -->
<footer class="main-footer">
  <div class="pull-right hidden-xs">
    <b>Version</b> 1.0.0
</div>
<strong>Copyright &copy; 2014-2015 <a href="#">TopTech Informatics</a>.</strong> All rights reserved.
</footer>
<!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
      
      <li><!-- <a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a> --></li>
      <li><!-- <a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a> --></li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
      <!-- Home tab content -->
      <div class="tab-pane" id="control-sidebar-home-tab">
      </div>
      
    </div>
  </aside>
  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>

</div>
<!-- ./wrapper -->
<?php echo Html::script('js/parsley.min.js'); ?>  
      
<!-- developer script -->
<?php echo Html::script('js/devscript.js'); ?>


<!-- tags word master scripts-->
<?php echo Html::script('js/tagwords.js'); ?>     

<?php echo Html::script('js/jtip.js'); ?><!--Tooltip js-->
<?php echo Html::script('js/jquery.dlmenu.js'); ?>  

<?php echo Html::style('plugins/datatables_new/jquery.dataTables.min.css'); ?>

<?php echo Html::style('plugins/datatables_new/rowReorder.dataTables.min.css'); ?>

<?php echo Html::script('plugins/datatables_new/jquery.dataTables.min.js'); ?>

<?php echo Html::script('plugins/datatables_new/dataTables.rowReorder.min.js'); ?>

<?php echo Html::script('plugins/datatables_new/jquery.dataTables.rowReordering.js'); ?>

    
</body>
<script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
}
</script>

<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

<script type="text/javascript">


 
      // get baseurl
      var base_url = "<?php echo config('app.url');?>";
      //var base_url = '<?php //echo URL('');?>';

      $(document).ready(function(){

        $( '#dl-menu' ).dlmenu();
        
        //ajax call 
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
          type: 'post',
          url: '<?php echo e(URL::route('viewmenuToggle')); ?>',
          dataType: 'html',
          data: {_token: CSRF_TOKEN},
          timeout: 50000,
          success: function(data, status){     
          },
          error: function(jqXHR, textStatus, errorThrown){
            console.log(jqXHR);    
            console.log(textStatus);    
            console.log(errorThrown);    
        }
        });
      });

      function saveToggle(id){
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
          //ajax call 
          $.ajax({
            type: 'post',
            url: '<?php echo e(URL('menuToggle')); ?>',
            dataType: 'json',
            data: {_token: CSRF_TOKEN, id:id},
            timeout: 50000,
            beforeSend: function() {

            },
            success: function(data, status){ 

            },
            error: function(jqXHR, textStatus, errorThrown){
              console.log(jqXHR);    
              console.log(textStatus);    
              console.log(errorThrown);    
          },
          complete: function() {

          }
      });     
      }
      // Global setting module
      $(document).ready(function(){
        $('#btn-reset').click(function(){
          $('#globalSettingsAddForm')[0].reset();
      });

      // <--- Image size validation -->
      $("#globalSettingsAddForm").submit( function( e ) {
              var form = this;
              e.preventDefault(); 
              var fileInput = $(this).find("input[type=file]")[0],
              file = fileInput.files && fileInput.files[0];

              if( file ) {
                var img = new Image();
                img.src = window.URL.createObjectURL( file );

                img.onload = function() {
                  var width  = img.naturalWidth,
                  height = img.naturalHeight; 

                  if( width <= 300 && height <= 120 ) {
                    form.submit();
                }
                else {
                    $('.global_s_msg').removeClass('parsley-success');
                    // show error message
                    $('.img_class').css('display','block');
                    $('#settings_logo').val('');       
                }
            };

        }
            else { //No file was input 
              form.submit();
          } 
        });

      // hide error or success message
      setTimeout(function () {
            $("#hide-div").slideDown(1000);
        }, 200);
          setTimeout(function () {
            $('#hide-div').slideUp("slow");
        }, 5000);
          // hide warning message
          $('#settings_logo').click(function(){
            $('.img_class').css('display','none');
        });

          $(document).on("click",".read_notification",function(e) {
          e.preventDefault();
          var href_url=$(this).attr('href');
          var notid=$(this).attr('data-notid');
          var url = "<?php echo url('read_notification'); ?>?notification="+notid;
   
   $.ajax({
          type: "GET",
          url: url,
          dataType:'json',
          success: function(response)
          {
             window.location.href= href_url;
              
          }
        });

          });

          var url = "<?php echo url('load_notification'); ?>";
   
   $.ajax({
          type: "GET",
          url: url,
          dataType:'json',
          success: function(response)
          {
              if(response.status == 1)
              {
                 $("#li_top_notification").html(response.html); 
              }
              
          }
        });

      });
//change skin function
(function ($, AdminLTE) {
var current_skin = localStorage.getItem('skin');
if(current_skin == "" || current_skin == null)
{
  current_skin = '<?php echo Auth::user()->user_skin; ?>';
}
  "use strict";

  /**
   * List of all the available skins
   *
   * @type  Array
   */
  var my_skins = [
    "skin-blue",
    "skin-black",
    "skin-red",
    "skin-yellow",
    "skin-purple",
    "skin-green",
    "skin-blue-light",
    "skin-black-light",
    "skin-red-light",
    "skin-yellow-light",
    "skin-purple-light",
    "skin-green-light"
  ];

  //Create the new tab
  var tab_pane = $("<div />", {
    "id": "control-sidebar-theme-demo-options-tab",
    "class": "tab-pane active"
  });

  //Create the tab button
  var tab_button = $("<li />", {"class": "active"})
      .html("<a href='#control-sidebar-theme-demo-options-tab' data-toggle='tab'>"
      + "<i class='fa fa-wrench'></i>"
      + "</a>");

  //Add the tab button to the right sidebar tabs
  $("[href='#control-sidebar-home-tab']")
      .parent()
      .before(tab_button);

  //Create the menu
  var demo_settings = $("<div />");

  //Layout options
  
  var skins_list = $("<ul />", {"class": 'list-unstyled clearfix'});
  if(current_skin == 'skin-blue'){
    var skinactive ="skinactive";
  }
  else{
    var skinactive ="";
  }
  //Dark sidebar skins
  var skin_blue =
      $("<li />", {style: "float:left; width: 33.33333%; padding: 5px;"})
          .append("<a href='javascript:void(0);' data-skin='skin-blue' style='display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)' class='clearfix full-opacity-hover "+skinactive+"'>"
          + "<div><span style='display:block; width: 20%; float: left; height: 7px; background: #367fa9;'></span><span class='bg-light-blue' style='display:block; width: 80%; float: left; height: 7px;'></span></div>"
          + "<div><span style='display:block; width: 20%; float: left; height: 20px; background: #222d32;'></span><span style='display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;'></span></div>"
          + "</a>"
          + "<p class='text-center no-margin'>Default</p>");
  skins_list.append(skin_blue);
  if(current_skin == 'skin-black'){
    var skinactive ="skinactive";
  }
  else{
    var skinactive ="";
  }
  var skin_black =
      $("<li />", {style: "float:left; width: 33.33333%; padding: 5px;"})
          .append("<a href='javascript:void(0);' data-skin='skin-black' style='display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)' class='clearfix full-opacity-hover "+skinactive+"'>"
          + "<div style='box-shadow: 0 0 2px rgba(0,0,0,0.1)' class='clearfix'><span style='display:block; width: 20%; float: left; height: 7px; background: #fefefe;'></span><span style='display:block; width: 80%; float: left; height: 7px; background: #fefefe;'></span></div>"
          + "<div><span style='display:block; width: 20%; float: left; height: 20px; background: #222;'></span><span style='display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;'></span></div>"
          + "</a>"
          + "<p class='text-center no-margin'>Black</p>");
  skins_list.append(skin_black);
  if(current_skin == 'skin-purple'){
    var skinactive ="skinactive";
  }
  else{
    var skinactive ="";
  }
  var skin_purple =
      $("<li />", {style: "float:left; width: 33.33333%; padding: 5px;"})
          .append("<a href='javascript:void(0);' data-skin='skin-purple' style='display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)' class='clearfix full-opacity-hover "+skinactive+"'>"
          + "<div><span style='display:block; width: 20%; float: left; height: 7px;' class='bg-purple-active'></span><span class='bg-purple' style='display:block; width: 80%; float: left; height: 7px;'></span></div>"
          + "<div><span style='display:block; width: 20%; float: left; height: 20px; background: #222d32;'></span><span style='display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;'></span></div>"
          + "</a>"
          + "<p class='text-center no-margin'>Purple</p>");
  skins_list.append(skin_purple);
  if(current_skin == 'skin-green'){
    var skinactive ="skinactive";
  }
  else{
    var skinactive ="";
  }
  var skin_green =
      $("<li />", {style: "float:left; width: 33.33333%; padding: 5px;"})
          .append("<a href='javascript:void(0);' data-skin='skin-green' style='display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)' class='clearfix full-opacity-hover "+skinactive+"'>"
          + "<div><span style='display:block; width: 20%; float: left; height: 7px;' class='bg-green-active'></span><span class='bg-green' style='display:block; width: 80%; float: left; height: 7px;'></span></div>"
          + "<div><span style='display:block; width: 20%; float: left; height: 20px; background: #222d32;'></span><span style='display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;'></span></div>"
          + "</a>"
          + "<p class='text-center no-margin'>Green</p>");
  skins_list.append(skin_green);
  if(current_skin == 'skin-red'){
    var skinactive ="skinactive";
  }
  else{
    var skinactive ="";
  }
  var skin_red =
      $("<li />", {style: "float:left; width: 33.33333%; padding: 5px;"})
          .append("<a href='javascript:void(0);' data-skin='skin-red' style='display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)' class='clearfix full-opacity-hover "+skinactive+"'>"
          + "<div><span style='display:block; width: 20%; float: left; height: 7px;' class='bg-red-active'></span><span class='bg-red' style='display:block; width: 80%; float: left; height: 7px;'></span></div>"
          + "<div><span style='display:block; width: 20%; float: left; height: 20px; background: #222d32;'></span><span style='display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;'></span></div>"
          + "</a>"
          + "<p class='text-center no-margin'>Red</p>");
  skins_list.append(skin_red);
  if(current_skin == 'skin-yellow'){
    var skinactive ="skinactive";
  }
  else{
    var skinactive ="";
  }
  var skin_yellow =
      $("<li />", {style: "float:left; width: 33.33333%; padding: 5px;"})
          .append("<a href='javascript:void(0);' data-skin='skin-yellow' style='display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)' class='clearfix full-opacity-hover "+skinactive+"'>"
          + "<div><span style='display:block; width: 20%; float: left; height: 7px;' class='bg-yellow-active'></span><span class='bg-yellow' style='display:block; width: 80%; float: left; height: 7px;'></span></div>"
          + "<div><span style='display:block; width: 20%; float: left; height: 20px; background: #222d32;'></span><span style='display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;'></span></div>"
          + "</a>"
          + "<p class='text-center no-margin'>Yellow</p>");
  skins_list.append(skin_yellow);

  //Light sidebar skins
  if(current_skin == 'skin-blue-light'){
    var skinactive ="skinactive";
  }
  else{
    var skinactive ="";
  }
  var skin_blue_light =
      $("<li />", {style: "float:left; width: 33.33333%; padding: 5px;"})
          .append("<a href='javascript:void(0);' data-skin='skin-blue-light' style='display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)' class='clearfix full-opacity-hover "+skinactive+"'>"
          + "<div><span style='display:block; width: 20%; float: left; height: 7px; background: #367fa9;'></span><span class='bg-light-blue' style='display:block; width: 80%; float: left; height: 7px;'></span></div>"
          + "<div><span style='display:block; width: 20%; float: left; height: 20px; background: #f9fafc;'></span><span style='display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;'></span></div>"
          + "</a>"
          + "<p class='text-center no-margin' style='font-size: 12px'>Blue Light</p>");
  skins_list.append(skin_blue_light);
  if(current_skin == 'skin-black-light'){
    var skinactive ="skinactive";
  }
  else{
    var skinactive ="";
  }
  var skin_black_light =
      $("<li />", {style: "float:left; width: 33.33333%; padding: 5px;"})
          .append("<a href='javascript:void(0);' data-skin='skin-black-light' style='display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)' class='clearfix full-opacity-hover "+skinactive+"'>"
          + "<div style='box-shadow: 0 0 2px rgba(0,0,0,0.1)' class='clearfix'><span style='display:block; width: 20%; float: left; height: 7px; background: #fefefe;'></span><span style='display:block; width: 80%; float: left; height: 7px; background: #fefefe;'></span></div>"
          + "<div><span style='display:block; width: 20%; float: left; height: 20px; background: #f9fafc;'></span><span style='display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;'></span></div>"
          + "</a>"
          + "<p class='text-center no-margin' style='font-size: 12px'>Black Light</p>");
  skins_list.append(skin_black_light);
  if(current_skin == 'skin-purple-light'){
    var skinactive ="skinactive";
  }
  else{
    var skinactive ="";
  }
  var skin_purple_light =
      $("<li />", {style: "float:left; width: 33.33333%; padding: 5px;"})
          .append("<a href='javascript:void(0);' data-skin='skin-purple-light' style='display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)' class='clearfix full-opacity-hover "+skinactive+"'>"
          + "<div><span style='display:block; width: 20%; float: left; height: 7px;' class='bg-purple-active'></span><span class='bg-purple' style='display:block; width: 80%; float: left; height: 7px;'></span></div>"
          + "<div><span style='display:block; width: 20%; float: left; height: 20px; background: #f9fafc;'></span><span style='display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;'></span></div>"
          + "</a>"
          + "<p class='text-center no-margin' style='font-size: 12px'>Purple Light</p>");
  skins_list.append(skin_purple_light);
  if(current_skin == 'skin-green-light'){
    var skinactive ="skinactive";
  }
  else{
    var skinactive ="";
  }
  var skin_green_light =
      $("<li />", {style: "float:left; width: 33.33333%; padding: 5px;"})
          .append("<a href='javascript:void(0);' data-skin='skin-green-light' style='display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)' class='clearfix full-opacity-hover "+skinactive+"'>"
          + "<div><span style='display:block; width: 20%; float: left; height: 7px;' class='bg-green-active'></span><span class='bg-green' style='display:block; width: 80%; float: left; height: 7px;'></span></div>"
          + "<div><span style='display:block; width: 20%; float: left; height: 20px; background: #f9fafc;'></span><span style='display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;'></span></div>"
          + "</a>"
          + "<p class='text-center no-margin' style='font-size: 12px'>Green Light</p>");
  skins_list.append(skin_green_light);
  if(current_skin == 'skin-red-light'){
    var skinactive ="skinactive";
  }
  else{
    var skinactive ="";
  }
  var skin_red_light =
      $("<li />", {style: "float:left; width: 33.33333%; padding: 5px;"})
          .append("<a href='javascript:void(0);' data-skin='skin-red-light' style='display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)' class='clearfix full-opacity-hover "+skinactive+"'>"
          + "<div><span style='display:block; width: 20%; float: left; height: 7px;' class='bg-red-active'></span><span class='bg-red' style='display:block; width: 80%; float: left; height: 7px;'></span></div>"
          + "<div><span style='display:block; width: 20%; float: left; height: 20px; background: #f9fafc;'></span><span style='display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;'></span></div>"
          + "</a>"
          + "<p class='text-center no-margin' style='font-size: 12px'>Red Light</p>");
  skins_list.append(skin_red_light);
  if(current_skin == 'skin-yellow-light'){
    var skinactive ="skinactive";
  }
  else{
    var skinactive ="";
  }
  var skin_yellow_light =
      $("<li />", {style: "float:left; width: 33.33333%; padding: 5px;"})
          .append("<a href='javascript:void(0);' data-skin='skin-yellow-light' style='display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)' class='clearfix full-opacity-hover "+skinactive+"'>"
          + "<div><span style='display:block; width: 20%; float: left; height: 7px;' class='bg-yellow-active'></span><span class='bg-yellow' style='display:block; width: 80%; float: left; height: 7px;'></span></div>"
          + "<div><span style='display:block; width: 20%; float: left; height: 20px; background: #f9fafc;'></span><span style='display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;'></span></div>"
          + "</a>"
          + "<p class='text-center no-margin' style='font-size: 12px;'>Yellow Light</p>");
  skins_list.append(skin_yellow_light);

  demo_settings.append("<h4 class='control-sidebar-heading'>Skins</h4>");
  demo_settings.append(skins_list);

  tab_pane.append(demo_settings);
  $("#control-sidebar-home-tab").after(tab_pane);

  setup();


  /**
   * Replaces the old skin with the new skin
   * @param  String cls the new skin class
   * @returns  Boolean false to prevent link's default action
   */
  function change_skin(cls) {
    $.each(my_skins, function (i) {
      $("body").removeClass(my_skins[i]);
    });

    $("body").addClass(cls);
    store('skin', cls);
    return false;
  }

  /**
   * Store a new settings in the browser
   *
   * @param  String name Name of the setting
   * @param  String val Value of the setting
   * @returns  void
   */
  function store(name, val) {
    if (typeof (Storage) !== "undefined") {
      //alert(val);
      var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
      $.ajax({
        type:'POST',
        url:'<?php echo e(URL('changeSkin')); ?>',
        data:{_token:CSRF_TOKEN,name:name,val:val},
        success:function(response){
          localStorage.setItem(name, response);//store to local storage
        }
      });
    } else {
      window.alert('Please use a modern browser to properly view this template!');
    }
  }

  /**
   * Get a prestored setting
   *
   * @param  String name Name of of the setting
   * @returns  String The value of the setting | null
   */
  function get(name) {
    if (typeof (Storage) !== "undefined") {
      
      var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
      $.ajax({
        type:'post',
        url:'<?php echo e(URL('getSkin')); ?>',
        data:{_token:CSRF_TOKEN,name:name},
        success:function(response){
          return response;
        }
      });
    } else {
      window.alert('Please use a modern browser to properly view this template!');
    }
  }

  /**
   * Retrieve default settings and apply them to the template
   *
   * @returns  void
   */
  function setup() {
    var tmp = get('skin');
    if (tmp && $.inArray(tmp, my_skins))
      change_skin(tmp);

    //Add the change skin listener
    $("[data-skin]").on('click', function (e) {
      e.preventDefault();
      change_skin($(this).data('skin'));
      //active skin highlight
      $("[data-skin]").removeClass('skinactive');
      $(this).addClass('skinactive');
    });

    

    $("[data-controlsidebar]").on('click', function () {
      change_layout($(this).data('controlsidebar'));
      var slide = !AdminLTE.options.controlSidebarOptions.slide;
      AdminLTE.options.controlSidebarOptions.slide = slide;
      if (!slide)
        $('.control-sidebar').removeClass('control-sidebar-open');
    });

    $("[data-sidebarskin='toggle']").on('click', function () {
      var sidebar = $(".control-sidebar");
      if (sidebar.hasClass("control-sidebar-dark")) {
        sidebar.removeClass("control-sidebar-dark")
        sidebar.addClass("control-sidebar-light")
      } else {
        sidebar.removeClass("control-sidebar-light")
        sidebar.addClass("control-sidebar-dark")
      }
    });

  }
})(jQuery, $.AdminLTE);
</script>
<style type="text/css">
/*@media (max-width: 1236px) and (min-width: 900px) {
  .topbar{
    width: 68%;
    left: 41px;
  }  
}
@media(max-width:760px){
  .main-header{
    max-height: 160px;
  }
} 
@media (max-width: 1464px) and (min-width: 157px) { 
  .topbar{
    visibility: hidden;
  }  
}*/
.sidebar-menu ul li a {
    white-space: normal;
    word-wrap: break-word;
 }

 .noHover{
    background: rgba(0,0,0,.5);
}

</style>
</html>
<?php /**PATH F:\xampp\htdocs\nspl5.8\resources\views/layouts/app.blade.php ENDPATH**/ ?>