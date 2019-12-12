<div class="row">
        <div class="col-xs-12">
            <?php
                include (public_path()."/storage/includes/lang1.en.php" );
                $user_permission=Auth::user()->user_permission;
            ?>
            <div class="box box-info">
                <div class="box-body">
                    <table border="1" id="example2" class="table table-bordered table-striped dataTable responsive hover" role="grid" aria-describedby="example1_info">
                        <thead>
                            <tr>
                                <th><?php echo e(trans('language.sign in name')); ?></th>
                                <th><?php echo e(trans('language.full name')); ?></th>
                                <th><?php echo e(trans('language.user type')); ?></th>
                                <th><?php if(Session::get('settings_department_name')): ?> <?php echo e(Session::get('settings_department_name')); ?><?php else: ?> <?php echo e(trans('language.departments')); ?> <?php endif; ?></th>
                                <th><?php echo e(trans('language.created date')); ?></th>
                                <th nowrap="nowrap"><?php echo e(trans('language.last sign in')); ?></th>
                                <th nowrap="nowrap"><?php echo e(trans('language.last_paswd_change')); ?></th>
                                <th><?php echo e(trans('language.status')); ?></th>
                                <th><?php echo e(trans('language.actions')); ?></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <td><input type="text" id="loginname" placeholder="Search Sign In Name"></td>
                                <td><input type="text" id="fullname" placeholder="Search Full Name"></td>
                                <td><select name="usertype" id="usertype">
                                <option value="" selected="selected">All</option>   
                                <option value="<?php echo e(trans('language.super admin')); ?>"><?php echo e(trans('language.super admin')); ?></option>   
                                <option value="<?php echo e(trans('language.group admin')); ?>"><?php echo e(trans('language.group admin')); ?></option>   
                                <option value="<?php echo e(trans('language.regular user')); ?>"><?php echo e(trans('language.regular user')); ?></option>   
                                </select></td>
                                <td><input type="text" id="depts" placeholder="Search Departments"></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>

                        <tbody>   
                            <?php $__currentLoopData = $usersList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <!-- According to the different users and permissions set variables-->
                            <?php $url=NULL; ?>
                            <?php if(Auth::user()->user_role == Session::get('user_role_group_admin') && $val->user_role == Session::get('user_role_group_admin') && Auth::user()->id != $val->id): ?>
                            <?php else: ?>
                            <?php if((Auth::user()->user_role == Session::get('user_role_regular_user') && Auth::user()->id != $val->id) || (Auth::user()->user_role == Session::get('user_role_private_user') && Auth::user()->id != $val->id)): ?>
                                <?php else: ?>
                                    <?php if(stristr($user_permission,"edit")): ?>
                                        <?php $url = url('userEdit', $val->id );?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                            <!-- //According to the different users and permissions set variables-->

                            <tr class="even" role="row">                                
                                <td>
                                <?php if(stristr($user_permission,'edit')): ?>
                                    
                                    <?php if(Auth::user()->user_role == Session::get('user_role_super_admin')  || Auth::user()->user_role == Session::get('user_role_group_admin')): ?>

                                        <?php if( (Auth::user()->user_role == Session::get('user_role_group_admin') && Auth::user()->id != $val->id) && ($val->user_role != Session::get('user_role_regular_user')) && ($val->user_role != Session::get('user_role_private_user')) ): ?> 
                                            <a title="Edit" style="color:#23527c">
                                        <?php else: ?>
                                            <a title="Edit" href="<?php echo e(url('userEdit', $val->id )); ?>">
                                        <?php endif; ?>

                                        <?php if(Auth::user()->id == $val->id): ?> 
                                            <b><?php echo e($val->username); ?></b>
                                        <?php else: ?>
                                        <?php echo e($val->username); ?>

                                        <?php endif; ?>
                                       </a>
                                    <?php else: ?>
                                        <?php if(Auth::user()->id == $val->id): ?>
                                            <a title="Edit" href="<?php echo e(url('userEdit', $val->id )); ?>">
                                           <b><?php echo e($val->username); ?></b>
                                        <?php else: ?>
                                            <?php echo e($val->username); ?>

                                           </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                
                                <?php else: ?>
                                    <?php if(Auth::user()->id == $val->id): ?>
                                       <b><?php echo e($val->username); ?></b>
                                    <?php else: ?>
                                    <?php echo e($val->username); ?>

                                    <?php endif; ?>
                                <?php endif; ?>
                                </td>
                                <td><?php echo e($val->user_full_name); ?></td>
                                <td>
                                    <?php if($val->user_role == Session::get('user_role_super_admin')): ?>
                                        <span class="label label-success"><b style="font-size: 12px;"><?php echo e(trans('language.super admin')); ?></b></span>
                                    <?php elseif($val->user_role == Session::get('user_role_group_admin')): ?>
                                        <span class="label label-danger"><b style="font-size: 12px;"><?php echo e(trans('language.group admin')); ?></b></span>          
                                    <?php elseif($val->user_role == Session::get('user_role_regular_user')): ?>
                                        <span class="label label-primary"><b style="font-size: 12px;"><?php echo e(trans('language.regular user')); ?></b></span> <?php if($val->user_view_only == '1'): ?> <span class="label btn-success"><b style="font-size: 12px;"><?php echo e(trans('language.viewonly')); ?> </b><?php endif; ?> </span>
                                    <?php elseif($val->user_role == Session::get('user_role_private_user')): ?>
                                        <span class="label label-warning"><b style="font-size: 12px;"><?php echo e(trans('language.private_user')); ?></b></span>
                                    <?php endif; ?>
                                </td>
                               
                                <td><!-- Department -->
                                <?php echo e($val->departments[0]->department_name); ?>

                                </td>
                                <?php /*<td><?php echo date('d-m-Y H:i:s a', strtotime($val->created_at)); ?></td>
                                <td><?php echo date('d-m-Y H:i:s a', strtotime($val->user_lastlogin_date)); ?></td>*/?>
                                <td nowrap="nowrap"><?php echo dtFormat($val->created_at); ?></td>
                                <td nowrap="nowrap"><?php echo dtFormat($val->user_lastlogin_date); ?></td>
                                <!-- Password Date -->
                                <td><?php if($val->password_date=="0000-00-00"){ echo"-"; }else{ echo dtFormat($val->password_date); }?></td>
                                <td style="text-align:center;">
                                <?php 
                                    //checking the password date is not 0 
                                    if($val->password_date!="0000-00-00"){
                                        $todate = date('Y-m-d');
                                        $paswdDate = $val->password_date;
                                        $startTimeStamp = strtotime($todate);
                                        $endTimeStamp = strtotime($paswdDate);

                                        $timeDiff = abs($endTimeStamp - $startTimeStamp);
                                        $numberDays = $timeDiff/86400;  // 86400 seconds in one day
                                        
                                        // and you might want to convert to integer
                                        $numberDays = intval($numberDays);
                                        ?>
                                        <!-- cheking user is lockedout -->
                                        <?php if($val->user_lock_status == 1): ?>
                                            <!-- cheking user role  -->
                                            <?php if((Auth::user()->user_role == Session::get('user_role_super_admin') )&&($val->id!=Auth::user()->user_role)): ?>
                                                <a title="<?php echo e(trans('language.locl_title')); ?>" href="javascript:void(0);" onclick="unlock(<?php echo e($val->id); ?>,'<?php echo e($val->username); ?>')">
                                                    <li class="fa fa-lock fa-lg" ></li>
                                                </a>
                                            <!-- cheking user is superadmin -->
                                            <?php else: ?>
                                                <a title="<?php echo e(trans('language.locl_title')); ?>" style="cursor:default">
                                                    <li class="fa fa-lock fa-lg" ></li>
                                                </a>
                                            <?php endif; ?>
                                            <!-- cheking user is not lockedout -->
                                        <?php else: ?>    
                                        	<!-- checking password is expired or not -->
                                        	<?php if($numberDays>$expry_no): ?>
                                                <a title="<?php echo e(trans('language.passwd_expired')); ?>" style="cursor:default">
                                                    <li class="fa fa-key fa-lg" ></li>
                                                </a>
                                            <?php else: ?>
                                            	<?php if($val->login_status == Session::get('login_status_Login')): ?>
                                                    <?php if((Auth::user()->user_role == Session::get('user_role_super_admin') )&&($val->id!=Auth::user()->user_role)): ?>
                                                        <a title="<?php echo e(trans('language.signout_title')); ?>" href="javascript:void(0);" onclick="logout(<?php echo e($val->id); ?>,'<?php echo e($val->username); ?>')">
                                                            <li class="fa fa-power-off fa-lg" ></li>
                                                        </a>
                                                    <?php else: ?>
                                                        <a title="<?php echo e(trans('language.signout_title')); ?>" style="cursor:default">
                                                            <li class="fa fa-power-off fa-lg" ></li>
                                                        </a>
                                                    <?php endif; ?>
                                                <?php else: ?>

                                                <?php if($val->user_status == Session::get('user_status_Inactive')): ?>
                                                    <a <?php if($url): ?> title="<?php echo e(trans('language.inactive_title')); ?>" href="<?php echo $url;?>" <?php else: ?> title="<?php echo e(trans('language.currently_inactive_title')); ?>"  <?php endif; ?>>
                                                        <span class="label label-danger"><b style="font-size: 12px;"><?php echo e(trans('language.inactive')); ?></b></span>
                                                    </a>
                                                <?php else: ?> 
                                                    <a  <?php if($url): ?> title="<?php echo e(trans('language.active_title')); ?>" href="<?php echo $url;?>" <?php else: ?> title="<?php echo e(trans('language.currently_active_title')); ?>" <?php endif; ?>>
                                                        <span class="label label-success"><b style="font-size: 12px;"><?php echo e(trans('language.active')); ?></b></span>
                                                    </a>                                                        
                                                <?php endif; ?>

                                                <?php endif; ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php
                                    }
                                ?>
                                        
                                </td>

                                <td width="5%">
                                    <?php if((Auth::user()->user_role == Session::get('user_role_super_admin') ) or (Auth::user()->user_role == Session::get('user_role_group_admin'))): ?>
                                        <a title="History" href="<?php echo e(url('userHistory', $val->username )); ?>">
                                            <li class="fa fa-history" ></li>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if(Auth::user()->user_role == Session::get('user_role_group_admin') && $val->user_role == Session::get('user_role_group_admin') && Auth::user()->id != $val->id): ?>
                                    <?php else: ?>
                                    <?php if((Auth::user()->user_role == Session::get('user_role_regular_user') && Auth::user()->id != $val->id) || (Auth::user()->user_role == Session::get('user_role_private_user') && Auth::user()->id != $val->id)): ?>
                                        <?php else: ?>
                                            <?php if(stristr($user_permission,"edit")): ?>
                                                &nbsp;
                                                <a title="Edit" href="<?php echo e(url('userEdit', $val->id )); ?>">
                                                    <li class="fa fa-pencil" ></li>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if(stristr($user_permission,"delete")): ?>
                                                <?php if(Auth::user()->id != $val->id && $val->user_role != Session::get('user_role_super_admin')): ?>
                                                    &nbsp;
                                                    <li title="Delete" class="fa fa-trash" onclick="del(<?php echo e($val->id); ?>, '<?php echo e($val->username); ?>')" style="color: red; cursor:pointer;"></li>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                     

                        </tbody>                    
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div><!-- /.col -->
    </div><!-- /.row -->
    <?php echo Html::script('plugins/jQuery/jQuery-2.1.4.min.js'); ?>

    <?php echo Html::script('plugins/datatables_new/jquery.dataTables.min.js'); ?>


<script>
    $(function () {
        
        var rows_per_page = '<?php echo e(Session('settings_rows_per_page')); ?>';
        var lengthMenu = getLengthMenu();//Function in app.blade.php
        var table = $('#example2').DataTable({
            "paging": true,
            "lengthMenu": lengthMenu,
            "searching": true,
            "ordering": true,
            //"info": true,
            //"autoWidth": false,
            "pageLength":rows_per_page,
            "order": [[ 0, "asc" ]],
            "scrollX": true,
            "scrollY":350,
            language: {
                searchPlaceholder: "<?php echo e(trans('language.data_tbl_search_placeholder')); ?>"
            },
            columnDefs: [ { orderable: false, targets: [7,8] } ]
        });
        //for highlight the row
    $('#example2 tbody').on('click', 'tr', function () {
        $("#example2 tbody tr").removeClass('row_selected');        
        $(this).addClass('row_selected');
    });
		$('#loginname').on('keyup', function(){
            table
            .column(0)
            .search(this.value)
            .draw();
        });  
		
		$('#fullname').on('keyup', function(){
            table
            .column(1)
            .search(this.value)
            .draw();
        }); 
		
		$('#usertype').on('change', function(){
            table
            .column(2)
            .search(this.value)
            .draw();
        }); 
		
		$('#depts').on('keyup', function(){
            table
            .column(3)
            .search(this.value)
            .draw();
        }); 


    });

  
    function logout(id,username)
    {   

        swal({
              title: "<?php echo e(trans('language.confirm_logout')); ?>'" + username + "' ?",
              text: "<?php echo e(trans('language.Swal_not_revert')); ?>",
              type: "<?php echo e(trans('language.Swal_warning')); ?>",
              showCancelButton: true
            }).then(function (result) {
                if(result){
                    // Success
                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    $.ajax({
                        type: 'post',
                        url: '<?php echo e(URL('userLogout')); ?>',
                        dataType: 'html',
                        data: {_token: CSRF_TOKEN, id:id,name:username},
                        timeout: 50000,
                        beforeSend: function() {
                            $("#bs").show();
                        },
                        success: function(data, status){ 
                            setTimeout(function () {
                                $("#msg").html('<div class="alert alert-success alert-sty">'+ data +'</div>');
                                $("#msg").slideDown(1000);
                            }, 200);
                            setTimeout(function () {
                                $('#msg').slideUp("slow");
                                window.location.reload();
                            }, 5000);

                        },
                        error: function(jqXHR, textStatus, errorThrown){
                            console.log(jqXHR);    
                            console.log(textStatus);    
                            console.log(errorThrown);    
                        },
                        complete: function() {
                           $("#bs").hide();
                        }
                    });      
              }
          });
    }


    function unlock(id,username)
    {   

         swal({
              title: "<?php echo e(trans('language.confirm_unlock')); ?>'" + username + "' ?",
              text: "<?php echo e(trans('language.Swal_not_revert')); ?>",
              type: "<?php echo e(trans('language.Swal_warning')); ?>",
              showCancelButton: true
            }).then(function (result) {
                if(result){
                    // Success
                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    $.ajax({
                        type: 'post',
                        url: '<?php echo e(URL('userUnlock')); ?>',
                        dataType: 'html',
                        data: {_token: CSRF_TOKEN, id:id,name:username},
                        timeout: 50000,
                        beforeSend: function() {
                            $("#bs").show();
                        },
                        success: function(data, status){ 
                            setTimeout(function () {
                                $("#msg").html('<div class="alert alert-success alert-sty">'+ data +'</div>');
                                $("#msg").slideDown(1000);
                            }, 200);
                            setTimeout(function () {
                                $('#msg').slideUp("slow");
                                window.location.reload();
                            }, 5000);

                        },
                        error: function(jqXHR, textStatus, errorThrown){
                            console.log(jqXHR);    
                            console.log(textStatus);    
                            console.log(errorThrown);    
                        },
                        complete: function() {
                           $("#bs").hide();
                        }
                    }); 

              }
          });
    }
    
</script>  <?php /**PATH F:\xampp\htdocs\nspl5.8\resources\views/pages/users/list.blade.php ENDPATH**/ ?>